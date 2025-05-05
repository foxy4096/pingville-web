<?php
include "utils/auth.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        Games - PingVille
    </title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header>
        <h1>Games in PingVille</h1>
        <p>Explore the mini-games available in PingVille!</p>
    </header>

    <main>
        <section class="games-list">
            <h2>Available Games</h2>
            <ul>
                <li><a href="game1.php">Game 1: Sloth Racing</a></li>
                <li><a href="game2.php">Game 2: Sloth Fishing</a></li>
                <li><a href="game3.php">Game 3: Sloth Cooking</a></li>
                <!-- Add more games as needed -->
            </ul>
        </section>

        <section class="game-info">
            <h2>Game Information</h2>
            <p>Select a game from the list to view more information.</p>
        </section>

        <?php if ($user): ?>
            <section class="play-now">
                <h2>Ready to play?</h2>
                <p>Join your friends in PingVille and start your adventure!</p>
                <a href="games.php" class="button">Play Now</a>
            </section>
        <?php else: ?>
            <section class="login-prompt">
                <p>Please log in to access the games.</p>
                <a href="login.php?next=games.php" class="button">Login</a>
                <a href="signup.php" class="button">Sign Up</a>
            </section>
        <?php endif; ?>
    </main>

    <?php include "templates/footer.php"; // Include the footer 
    ?>
</body>

</html>