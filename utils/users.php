<?php

include $_SERVER['DOCUMENT_ROOT'] . "../db.php"; // Include the database connection file

session_start(); // Start the session

function get_user_by_username($username)
{
    return db_query("SELECT * FROM users WHERE username = ?", [$username]);
}
function get_user_by_email($email)
{
    return db_query("SELECT * FROM users WHERE email = ?", [$email]);
}

function get_user_by_id($user_id)
{
    return db_query("SELECT * FROM users WHERE id = ?", [$user_id]);
}

function create_user($username, $email, $password)
{
    // Check if the username already exists
    $existing_user = get_user_by_username($username);
    if ($existing_user) {
        return ['status' => 'error', 'message' => 'Username already exists.'];
    }

    // Check if the email already exists
    $existing_email = get_user_by_email($email);
    if ($existing_email) {
        return ['status' => 'error', 'message' => 'Email already exists.'];
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Create a new user in the database
    $user_id = db_query("INSERT INTO users (username, email, password) VALUES (?, ?, ?)", [$username, $email, $hashed_password]);

    if ($user_id) {
        add_coins($user_id, 100); // Add initial coins to the user
        return ['status' => 'success', 'user' => $username];
    } else {
        return ['status' => 'error', 'message' => 'Failed to create user. Please try again.'];
    }
}

function generate_auth_token($user_id, $length = 32)
{
    // At a time only one token is generated for a user until it is expired or revoked
    // Check if the user already has a token and it has not expired
    $existing_token = db_query("SELECT * FROM auth_tokens WHERE user_id = ? AND expires_at > ?", [$user_id, date('Y-m-d H:i:s', time())]);
    if ($existing_token) {
        return $existing_token['token'];
    }

    // Generate a new token
    $token = bin2hex(random_bytes($length / 2)); // Generate a random token
    $expires_at = time() + (60 * 60 * 24); // Token expires in 24 hours
    $expires_at = date('Y-m-d H:i:s', $expires_at); // Format the expiration time

    // Store the token in the database
    db_query("INSERT INTO auth_tokens (user_id, token, expires_at) VALUES (?, ?, ?)", [$user_id, $token, $expires_at]);

    return $token; // Return the generated token


}

function has_claimed_daily_bonus($user_id) {
    $user = get_user_by_id($user_id);
    if (!$user || empty($user['last_daily_bonus'])) {
        return false; // Never claimed
    }
    return ($user['last_daily_bonus'] == date('Y-m-d'));
}

function claim_daily_bonus($user_id, $amount = 10) {
    if (has_claimed_daily_bonus($user_id)) {
        return ['status' => 'error', 'message' => 'Daily bonus already claimed today.'];
    }

    // Add coins
    add_coins($user_id, $amount);

    // Update last_daily_bonus date
    db_query("UPDATE users SET last_daily_bonus = ? WHERE id = ?", [date('Y-m-d'), $user_id]);

    return ['status' => 'success', 'message' => "You received {$amount} coins as daily bonus!"];
}





function authorize_user($token)
{
    $time = time();
    $time = date('Y-m-d H:i:s', $time); // Format the current time
    // Check if the token is valid and not expired
    $result = db_query("SELECT * FROM auth_tokens WHERE token = ? AND expires_at > ?", [$token, $time]);
    if ($result) {
        return $result['user_id'];
    } else {
        return false;
    }
}

enum LoginMode
{
    case SESSION;
    case API;
    case BOTH;
}

function get_post_from_json()
{
    // Get the raw POST data from the request body
    $json = file_get_contents('php://input');
    // Decode the JSON data into an associative array
    $data = json_decode($json, true);
    return $data;
}

function get_all_users()
{
    return db_query("SELECT * FROM users", [], false);
}

function get_all_tokens()
{
    return db_query("SELECT * FROM auth_tokens", [], false);
}

function get_auth_user($mode = LoginMode::SESSION)
{
    if ($mode === LoginMode::API) {
        // Get the token from the request headers or query parameters
        $token = $_POST['token'] ?? $_GET['token'] ?? get_post_from_json()['token'] ?? null;
        if ($token) {
            // Authorize the user using the token
            $user_id = authorize_user($token);
            if ($user_id) {
                return get_user_by_id($user_id);
            }
        }
    } else if ($mode === LoginMode::SESSION) {
        // Check if the user is logged in via session
        if (isset($_SESSION['user_id'])) {
            return get_user_by_id($_SESSION['user_id']);
        }
    } else if ($mode === LoginMode::BOTH) {
        // Check if the user is logged in via session
        if (isset($_SESSION['user_id'])) {
            return get_user_by_id($_SESSION['user_id']);
        }
        // Get the token from the request headers or query parameters
        $token = $_POST['token'] ?? $_GET['token'] ?? get_post_from_json()['token'] ?? null;
        if ($token) {
            // Authorize the user using the token
            $user_id = authorize_user($token);
            if ($user_id) {
                return get_user_by_id($user_id);
            }
        }
    }
    return null; // No authenticated user found
}


function login_user($username, $password, $mode = LoginMode::SESSION)
{
    // Check if the user exists
    $user = get_user_by_username($username);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id']; // Store user ID in session

        // Claim daily bonus if not claimed yet
        $daily_bonus_result = null;
        if (!has_claimed_daily_bonus($user['id'])) {
            $daily_bonus_result = claim_daily_bonus($user['id']);
        }

        if ($mode === LoginMode::API) {
            $token = generate_auth_token($user['id']);
            $response = ['status' => 'success', 'token' => $token];
        } else {
            $response = ['status' => 'success', 'user' => $username];
        }

        // Optionally include daily bonus message
        if ($daily_bonus_result && $daily_bonus_result['status'] === 'success') {
            $response['daily_bonus_message'] = $daily_bonus_result['message'];
        }

        return $response;
    } else {
        return ['status' => 'error', 'message' => 'Invalid username or password.'];
    }
}

function logout_user($mode = LoginMode::SESSION)
{
    if ($mode === LoginMode::API) {
        // Invalidate the token in the database
        $token = $_POST['token'] ?? $_GET['token'] ?? null;
        if ($token) {
            db_query("DELETE FROM auth_tokens WHERE token = ?", [$token]);
        }
    } else {
        // Destroy the session
        session_start();
        session_destroy();
    }
    return ['status' => 'success', 'message' => 'Logged out successfully.'];
}


function get_user_tokens($user_id)
{
    return db_query("SELECT * FROM auth_tokens WHERE user_id = ?", [$user_id], false);
}

function revoke_token($user_id, $token)
{
    // Revoke the token by deleting it from the database
    db_query("DELETE FROM auth_tokens WHERE user_id = ? AND token = ?", [$user_id, $token]);
    return ['status' => 'success', 'message' => 'Token revoked successfully.'];
}


function update_user_settings($user_id, $username, $email)
{
    // Update the user's settings in the database
    db_query("UPDATE users SET username = ?, email = ? WHERE id = ?", [$username, $email, $user_id]);
    return ['status' => 'success', 'message' => 'Settings updated successfully.'];
}

function set_player_position($user_id, $position)
{
    // Update the player's position in the database
    db_query("UPDATE users SET last_position = ? WHERE id = ?", [$position, $user_id]);
    return ['status' => 'success', 'message' => 'Position updated successfully.'];
}
function get_player_position($user_id)
{
    // Get the player's position from the database
    return db_query("SELECT last_position FROM users WHERE id = ?", [$user_id]);
}

function is_admin($user_id)
{
    // Check if the user is an admin
    $user = get_user_by_id($user_id);
    return $user['is_admin'] ? true : false;
}



// Get the current coin balance of a user
function get_user_coins($user_id)
{
    $result = db_query("SELECT coins FROM user_currency WHERE user_id = ?", [$user_id]);
    if ($result && isset($result['coins'])) {
        return (int)$result['coins'];
    }
    // If no record found, assume 0 coins
    return 0;
}

// Add coins to a user's balance
function add_coins($user_id, $amount)
{
    if ($amount <= 0) return false; // Prevent negative or zero additions

    $current = get_user_coins($user_id);

    if ($current === 0) {
        // No record yet, insert new
        db_query("INSERT INTO user_currency (user_id, coins) VALUES (?, ?)", [$user_id, $amount]);
    } else {
        // Update existing record
        db_query("UPDATE user_currency SET coins = coins + ? WHERE user_id = ?", [$amount, $user_id]);
    }
    return true;
}

// Subtract coins from a user's balance (only if enough coins available)
function subtract_coins($user_id, $amount)
{
    if ($amount <= 0) return false; // Prevent negative or zero subtraction

    $current = get_user_coins($user_id);
    if ($current < $amount) {
        // Not enough coins
        return false;
    }
    db_query("UPDATE user_currency SET coins = coins - ? WHERE user_id = ?", [$amount, $user_id]);
    return true;
}

// Check if the user has at least $amount coins
function has_enough_coins($user_id, $amount)
{
    $current = get_user_coins($user_id);
    return ($current >= $amount);
}
