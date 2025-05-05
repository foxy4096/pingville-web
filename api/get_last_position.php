<?php

include "../utils/users.php"; // Include the utility functions

// Get the authenticated user
$user = get_auth_user($mode = LoginMode::BOTH); 

// Check if the user is authenticated
if ($user) {
    $last_position = get_player_position($user['id']); // Get the user's last position from the database

    if ($last_position) {
        echo json_encode(['status' => 'success', 'last_position' => $last_position]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Last position not found.']);
    }
}