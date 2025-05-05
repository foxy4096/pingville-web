<?php include "utils/auth.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PingVille - Cozy Sloth Multiplayer World</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header>
        <h1>Welcome to PingVille! 🦥</h1>
        <p>A cozy online world filled with mini-games, sloth friends, and fun adventures!</p>
        <?php if ($user): ?>
            <p>Hello, <?= $user['username'] ?>!
                <a href="me.php" class="button">Profile</a>
                <a href="logout.php" class="button">Logout</a>
            </p>
        <?php endif; ?>
    </header>

    <section class="cta">
        <?php if ($user): ?>
            <h2>Ready to play?</h2>
            <p>Join your friends in PingVille and start your adventure!</p>
            <a href="games.php" class="button">Play Now</a>
        <?php else: ?>
        <a href="login.php" class="button">Login</a>
        <a href="signup.php" class="button">Sign Up</a>
        <?php endif; ?>
    </section>

    <section class="preview">
        <h2>Explore & Play!</h2>
        <p>Join the PingVille community and dive into exciting mini-games.</p>
        <!-- <img src="sloth-game-preview.png" alt="Preview of PingVille"> -->
        <p>Preview of PingVille game</p>
    </section>

    <section class="download">
        <h2>Download the PingVille launcher</h2>
        <p>Get the latest version of our launcher to start playing!</p>
        <a href="download.php" class="button">Download Now</a>
    </section>
    <?php include "templates/footer.php"; // Include the footer 
    ?>
    <script src="script.js"></script>
</body>

</html>