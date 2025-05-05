<?php

include "../utils/users.php"; // Include the utility functions

// Get the authenticated user
$user = get_auth_user($mode = LoginMode::BOTH); 

// Check if the user is authenticated
if ($user) {
    // Get the last position from the request body
    $data = get_post_from_json();
    $last_position = $data['last_position'] ?? null; // the format is x,y

    // Regex to validate the last position format
    $pattern = '/^(-?\d+(\.\d+)?),\s*(-?\d+(\.\d+)?)$/'; // Matches x,y or x,y with optional decimal points
    // Check if the last position matches the regex pattern
    if (!preg_match($pattern, $last_position)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid last position format.']);
        exit;
    }

    if ($last_position) {
        // Update the user's last position in the database
        set_player_position($user['id'], $last_position);
        echo json_encode(['status' => 'success', 'message' => 'Last position updated successfully.', 'last_position' => $last_position]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Last position is required.']);
    }
} else {
    // User is not authenticated, return an error message
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access. Please log in.'
    ]);
}