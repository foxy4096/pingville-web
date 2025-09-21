<?php
// check_admin.php
include "../utils/users.php"; // Include the utility functions


$user = get_auth_user($mode=LoginMode::BOTH); // Get the authenticated user

header('Content-Type: application/json'); // Set the content type to JSON

// Check if the user is an admin
if ($user && $user['is_admin']) {
    // User is an admin, return success response
    echo json_encode(['admin' => true]);
} else {
    // User is not an admin, return error response
    echo json_encode(['admin' => false]);
}