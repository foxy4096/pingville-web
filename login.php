<?php include "utils/auth.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>
        Login - PingVille
    </title>
</head>

<body>
    <header>
        <h1>Login to PingVille</h1>
        <p>Welcome back, sloth friend!</p>
    </header>

    <main>
        <form action="login.php" method="POST" class="login-form">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required autocomplete="username">

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">
            <button type="submit">Login</button>
            <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
        </form>
        <?php if (isset($_GET['error'])): ?>
            <p class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
        <p class="info-message">If you encounter any issues, please contact support.</p>
        <?php

        if ($user) {
            // If user is already logged in, redirect to profile page
            header("Location: me.php");
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $username = $_POST['username'];
            $password = $_POST['password'];

            $result = login_user($username, $password);

            if ($result['status'] === 'success') {
                echo "<p class='success-message'>Login successful! Welcome back, " . htmlspecialchars($result['user']) . "!</p>";
                // Show go to home page button
                echo "<a href='index.php' class='button'>Go to Home</a>";

                // Optionally, you can redirect to the home page after a few seconds via JavaScript
                echo "<script>setTimeout(function() { window.location.href = 'index.php'; }, 2000);</script>";
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