<?php

include "../utils/users.php"; // Include the utility functions
include "../utils/shop_helpers.php";

$user = get_auth_user($mode=LoginMode::BOTH); // Get the authenticated user
$coins = get_user_coins($user['id']); // Get the user's coins
$owned_cosmetics_array = get_user_cosmetics($user['id']); // Get the user's owned cosmetics
$owned_cosmetics_id = array_column($owned_cosmetics_array, 'cosmetic_id'); // Get the IDs of owned cosmetics

$owned_cosmetics = get_cosmetic_by_ids($owned_cosmetics_id); // Get the details of owned cosmetics

if ($user) {
    // User is authenticated, return user data
    echo json_encode([
        'status' => 'success',
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'is_admin' => $user['is_admin'] ? true : false,
            'avatar' => 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($user['email']))) . '?d=identicon',
            'last_position' => $user['last_position'], // Assuming you have a last_position field in the user data
            // Add any other user data you want to return
            'coins' => $coins,
            'owned_cosmetics' => $owned_cosmetics
        ]
    ]);
} else {
    // User is not authenticated, return an error message
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access. Please log in.'
    ]);
}