<?php

include "utils/users.php"; // Include the user functions

$user = get_auth_user(); // Get the authenticated user

if (!$user) {
    // If the user is not authenticated, redirect to the login page
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // If the form is submitted, revoke the token
    $token = $_POST['token'] ?? null;
    if ($token) {
        $result = revoke_token($user['id'], $token); // Revoke the token for the user
        if ($result) {
            // If the token is revoked successfully, redirect to the profile page with a success message
            header("Location: me.php?success=Token revoked successfully.");
            exit;
        } else {
            // If there was an error revoking the token, show an error message
            header("Location: me.php?error=Failed to revoke token.");
            exit;
        }
    } else {
        header("Location: me.php?error=Invalid token.");
        exit;
    }
} else {
    // If the request method is not POST, show an error message
    header("Location: me.php?error=Invalid request method.");
    exit;
}
