<?php include "utils/auth.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>
        Sign Up - PingVille
    </title>
</head>

<body>
    <header>
        <h1>Sign Up for PingVille</h1>
        <p>Join the cozy sloth multiplayer world!</p>
    </header>

    <main>
        <form action="signup.php" method="POST" class="signup-form">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Sign Up</button>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            $result = create_user($username, $email, $password);

            if ($result['status'] === 'success') {
                echo "<p class='success-message'>User created successfully! Welcome, " . htmlspecialchars($result['user']) . "!</p>";
            } else {
                echo "<p class='error-message'>" . htmlspecialchars($result['message']) . "</p>";
            }
        }
        ?>
    </main>

    <?php include "templates/footer.php"; // Include the footer ?>

</body>


</html>