<?php

include "../utils/users.php"; // Include the utility functions


header('Content-Type: application/json'); // Set the content type to JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? null;
    $password = $data['password'] ?? null;

    if (empty($username) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Username and password are required.']);
        exit;
    } 

    // Check if the user exists
    $user = get_user_by_username($username);

    if ($user && password_verify($password, $user['password'])) {
        // Generate an auth token for the user
        $token = generate_auth_token($user['id']);
        $_SESSION['user_id'] = $user['id']; // Store user ID in session

        echo json_encode(['status' => 'success', 'token' => $token]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid username or password.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}