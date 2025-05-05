<?php

include "../utils/users.php"; // Include the utility functions


$user = get_auth_user($mode=LoginMode::BOTH); // Get the authenticated user


if ($user) {
    // User is authenticated, return user data
    echo json_encode([
        'status' => 'success',
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'avatar' => 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($user['email']))) . '?d=identicon',
            'last_position' => $user['last_position'], // Assuming you have a last_position field in the user data
            // Add any other user data you want to return
        ]
    ]);
} else {
    // User is not authenticated, return an error message
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access. Please log in.'
    ]);
}