<style>
    body {
        font-family: monospace;
        background: #1e1e1e;
        color: #d4d4d4;
        padding: 20px;
    }

    pre {
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th,
    td {
        padding: 10px;
        border: 1px solid #444;
        text-align: left;
    }

    th {
        background-color: #333;
        color: #fff;
    }

    tr:nth-child(even) {
        background-color: #222;
    }


    h1,
    h2 {
        color: #fff;
    }

    .error-message {
        color: red;
    }

    .success-message {
        color: green;
    }

    .info-message {
        color: #00f;
    }

    .avatar {
        border-radius: 50%;
        width: 50px;
        height: 50px;
    }

    .button {
        background-color: #007bff;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        text-decoration: none;
    }
</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Information - PingVille</title>
</head>

<?php
include "utils/auth.php";
include "utils/shop_helpers.php"; // Include the shop helpers

$user = get_auth_user($mode = LoginMode::BOTH); // Get the authenticated user

if (!$user || !$user['is_admin']) {
    // User is not authenticated, return an error message
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access. Please log in.'
    ]);
    exit;
}

// Show some debug information

echo "<h1>Debug Information</h1>";
echo "<h2>User Information</h2>";
// Show user information in a preformatted block in table
echo "<pre>";
echo "User ID: " . $user['id'] . "\n";
echo "Username: " . $user['username'] . "\n";
echo "Email: " . $user['email'] . "\n";
echo "Is Admin: " . ($user['is_admin'] ? 'Yes' : 'No') . "\n";
echo "Last Position: " . $user['last_position'] . "\n";
echo "Avatar: https://www.gravatar.com/avatar/" . md5(strtolower(trim($user['email']))) . "?d=identicon\n";
echo "</pre>";
echo "<h2>Shop Information</h2>";
// Get the list of all the users
$users = get_all_users();
// Show the users in a table
echo "<table>";
echo "<thead>";
echo "<tr>";
echo "<th>User ID</th>";
echo "<th>Username</th>";
echo "<th>Email</th>";
echo "<th>Is Admin</th>";
echo "<th>Last Position</th>";
echo "<th>Coins</th>";
echo "<th>Owned Cosmetics</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
foreach ($users as $user) {
    // Get the user's coins
    $coins = get_user_coins($user['id']);
    // Get the user's owned cosmetics
    $owned_cosmetics_array = get_user_cosmetics($user['id']);
    $owned_cosmetics_id = array_column($owned_cosmetics_array, 'cosmetic_id');
    $owned_cosmetics = get_cosmetic_by_ids($owned_cosmetics_id);
    echo "<tr>";
    echo "<td>" . htmlspecialchars($user['id']) . "</td>";
    echo "<td>" . htmlspecialchars($user['username']) . "</td>";
    echo "<td>" . htmlspecialchars($user['email']) . "</td>";
    echo "<td>" . ($user['is_admin'] ? 'Yes' : 'No') . "</td>";
    echo "<td>" . htmlspecialchars($user['last_position']) . "</td>";
    echo "<td>" . htmlspecialchars($coins) . "</td>";
    echo "<td>";
    foreach ($owned_cosmetics as $cosmetic) {
        echo htmlspecialchars($cosmetic['name']) . " (ID: " . htmlspecialchars($cosmetic['id']) . ")<br>";
    }
    echo "</td>";
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";

// Show the list of all the cosmetics
$cosmetics = get_all_cosmetics();

// Show the cosmetics
echo "<h2>Cosmetics Information</h2>";
// Show the cosmetics in a table
echo "<table>";
echo "<thead>";
echo "<tr>";
echo "<th>Cosmetic ID</th>";
echo "<th>Name</th>";
echo "<th>Price</th>";
echo "<th>Rarity</th>";
echo "<th>Category</th>";
echo "<th>Image</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
foreach ($cosmetics as $cosmetic) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($cosmetic['id']) . "</td>";
    echo "<td>" . htmlspecialchars($cosmetic['name']) . "</td>";
    echo "<td>" . htmlspecialchars($cosmetic['price']) . "</td>";
    echo "<td>" . htmlspecialchars($cosmetic['rarity']) . "</td>";
    echo "<td>" . htmlspecialchars($cosmetic['category']) . "</td>";
    echo "<td>";
    if ($cosmetic['image_url']) {
        echo "<img src='" . htmlspecialchars($cosmetic['image_url']) . "' alt='image' width='50'>";
    } else {
        echo "No Image";
    }
    echo "</td>";

    echo "</tr>";
}
echo "</tbody>";
echo "</table>";

// Show some server information
echo "<h2>Server Information</h2>";
echo "<pre>";
echo "Server Name: " . $_SERVER['SERVER_NAME'] . "\n";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "Server Protocol: " . $_SERVER['SERVER_PROTOCOL'] . "\n";
echo "Server Port: " . $_SERVER['SERVER_PORT'] . "\n";
echo "Server IP: " . $_SERVER['SERVER_ADDR'] . "\n";
echo "Server Port: " . $_SERVER['SERVER_PORT'] . "\n";
echo "Server Admin: " . $_SERVER['SERVER_ADMIN'] . "\n";
echo "Server Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Server Script Name: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "Server Request Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "Server Request Time: " . $_SERVER['REQUEST_TIME'] . "\n";
echo "Server Request Time Float: " . $_SERVER['REQUEST_TIME_FLOAT'] . "\n";
echo "Server Query String: " . $_SERVER['QUERY_STRING'] . "\n";
echo "Server Remote Address: " . $_SERVER['REMOTE_ADDR'] . "\n";
echo "Server Remote Port: " . $_SERVER['REMOTE_PORT'] . "\n";
echo "Server Request URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Server Request Headers: " . json_encode(getallheaders()) . "\n";
echo "</pre>";

include "templates/footer.php"; // Include the footer

exit;