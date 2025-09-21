<footer>
    <div class="footer-info">
        <p><strong>🕒 Server Time:</strong> <?= date('Y-m-d H:i:s') ?></p>
        <p><strong>🧑 Current User:</strong> <?= htmlspecialchars($user['username'] ?? 'Guest') ?></p>
        <!-- If user show his coins -->
        <?php if (isset($user)): ?>
            <p><strong>💰 Coins:</strong> <?= htmlspecialchars(get_user_coins($user['id'])) ?></p>
        <?php endif; ?>
        
    </div>

    <div class="footer-nav">
        <form action="goto.php" method="get">
            <label for="footer-page">Go to:</label>
            <select name="page" id="footer-page" onchange="this.form.submit()">
                <option value="">Select a page</option>
                <option value="index.php">Home</option>
                <option value="me.php">Profile</option>
                <option value="shop.php">Shop</option>
                <option value="login.php">Login</option>
                <option value="signup.php">Sign Up</option>
                <option value="download.php">Download</option>
                <option value="games.php">Games</option>
                <option value="settings.php">Settings</option>
                <option value="status.php">Status</option>
                <option value="logout.php">Logout</option>
            </select>
            <noscript><input type="submit" value="Go"></noscript>
        </form>
    </div>

    <p class="footer-copy">© <?= date("Y") ?> PingVille | Connect, Play, Explore 🦥</p>
</footer>
