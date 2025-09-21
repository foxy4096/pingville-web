<?php
include "utils/auth.php";
include "utils/shop_helpers.php";

if (!$user) {
    header("Location: login.php?next=shop.php&error=You must be logged in.");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cosmetic_id'])) {
    $cosmetic_id = (int) $_POST['cosmetic_id'];
    $result = buy_cosmetic($user['id'], $cosmetic_id);

    if ($result['status'] === 'success') {
        header("Location: shop.php?success=" . urlencode($result['message']));
        exit;
    } else {
        header("Location: shop.php?error=" . urlencode($result['message']));
        exit;
    }
}

$cosmetics = get_all_cosmetics();
$user_owned = get_user_cosmetics($user['id']);
$owned_ids = array_column($user_owned, 'cosmetic_id');

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Shop - PingVille</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header>
        <h1>Welcome to the Cosmetic Shop!</h1>
        <p>Explore and buy cosmetics for your character.</p>
        <p><span style="font-size: 1.2em; color: #fff; display: inline-flex; margin-top: 10px; align-items: center; gap: 5px;">
                <img src="/assets/art/coin.png" alt="coin" width="30"><strong>Coins:</strong> <?php echo get_user_coins($user['id']); ?>
            </span>
        </p>
    </header>

    <?php if ($success): ?>
        <div>
        <p class="success-message">
            <?php echo htmlspecialchars($success); ?>
        </p>        
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div>
            <p class="error-message">

                <?php echo htmlspecialchars($error); ?>
            </p>
        </div>
    <?php endif; ?>

    <?php if (empty($cosmetics)): ?>
        <p>No cosmetics available in the shop yet.</p>
    <?php else: ?>
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Rarity</th>
                    <th>Category</th>
                    <th>Image</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cosmetics as $cosmetic): ?>
                    <tr>
                        <td>
                            <?php if ($cosmetic['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($cosmetic['image_url']); ?>" alt="image" width="50">
                            <?php else: ?>
                                No Image
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($cosmetic['name']); ?></td>
                        <td><?php echo (int)$cosmetic['price']; ?> coins</td>
                        <td><?php echo htmlspecialchars($cosmetic['rarity']); ?></td>
                        <td><?php echo htmlspecialchars($cosmetic['category']); ?></td>
                        <td>
                            <?php if (in_array($cosmetic['id'], $owned_ids)): ?>
                                Owned
                            <?php else: ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="cosmetic_id" value="<?php echo $cosmetic['id']; ?>">
                                    <button type="submit">Buy</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <?php include "templates/footer.php"; ?>

</body>

</html>