<?php

include "utils/auth.php";

if (!$user) {
    // If no user is authenticated, redirect to the login page
    header("Location: login.php?next=me.php&error=You must be logged in to view this page.");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        Settings - PingVille
    </title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header>
        <h1>Settings</h1>
        <p>Manage your account settings and preferences.</p>
    </header>

    <main>
        <form action="settings.php" method="POST" class="settings-form">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <button type="submit">Save Changes</button>
        </form>

        <?php

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? $user['username'];
            $email = $_POST['email'] ?? $user['email'];

            $result = update_user_settings($user['id'], $username, $email);

            if ($result['status'] === 'success') {
                echo "<p class='success-message'>Settings updated successfully!</p>";
            } else {
                echo "<p class='error-message'>" . htmlspecialchars($result['message']) . "</p>";
            }
        }
        ?>
    </main>

    <?php include "templates/footer.php"; // Include the footer 
    ?>
</body>

</html>