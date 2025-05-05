<?php

include "utils/auth.php";

if (!$user) {
    // If no user is authenticated, redirect to the login page
    header("Location: login.php?next=me.php&error=You must be logged in to view this page.");
    exit;
}
$tokens = get_user_tokens($user['id']); // Get the user's tokens
$user['tokens'] = $tokens; // Add tokens to the user array


$success = $_GET['success'] ?? null; // Get success message from URL
$error = $_GET['error'] ?? null; // Get error message from URL

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>
        <?php echo $user['username']; ?>'s Profile - PingVille
    </title>
</head>

<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
    </header>

    <!-- Messages -->
    <?php if ($success): ?>
        <div class="success-message">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>


    <main>
        <div class="box">

            <h2>Your Profile</h2>
            <!-- Avatar from gravatar -->
            <img src="https://www.gravatar.com/avatar/<?php echo md5(strtolower(trim($user['email']))); ?>?d=identicon" alt="Avatar" class="avatar">
            <p>User ID: <?php echo htmlspecialchars($user['id']); ?></p>
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p>Last Position: <?php echo htmlspecialchars($user['last_position']); ?></p>
        </div>
        <!-- Add more user information as needed -->
        <h2>Your Tokens</h2>
        <!-- Display the tokens in tables -->

        <table>
            <thead>
                <tr>
                    <th>Token</th>
                    <th>Expires At</th>
                    <th>Is Expired?</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($user['tokens'])): ?>
                    <tr>
                        <td colspan="3">No tokens available.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($user['tokens'] as $token): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($token['token']); ?></td>
                        <td><?php echo htmlspecialchars($token['expires_at']); ?></td>
                        <!-- Do some processing to check if the token has expired -->
                        <td><?php echo (strtotime($token['expires_at']) < time()) ? 'Yes' : 'No'; ?></td>
                        <td>
                            <form action="revoke_token.php" method="POST" style="text-align: center;">
                                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token['token']); ?>">
                                <button type="submit">Revoke</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <?php include "templates/footer.php"; // Include the footer 
    ?>

</body>

</html>