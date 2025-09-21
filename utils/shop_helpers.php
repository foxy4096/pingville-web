<?php


function get_all_cosmetics()
{
    return db_query("SELECT * FROM cosmetics", [], false);
}

function get_user_cosmetics($user_id)
{
    return db_query("SELECT * FROM user_cosmetics WHERE user_id = ?", [$user_id], false);
}

function user_owns_cosmetic($user_id, $cosmetic_id) {
    $row = db_query("SELECT id FROM user_cosmetics WHERE user_id = ? AND cosmetic_id = ?", [$user_id, $cosmetic_id]);
    return $row ? true : false;
}

function can_afford_cosmetic($user_id, $cosmetic_id)
{
    $user = get_user_by_id($user_id);
    $cosmetic = db_query("SELECT price FROM cosmetics WHERE id = ?", [$cosmetic_id]);
    if ($user && $cosmetic) {
        return $user['coins'] >= $cosmetic['price'];
    }
    return false;
}



// Buy a cosmetic
function buy_cosmetic($user_id, $cosmetic_id) {
    if (user_owns_cosmetic($user_id, $cosmetic_id)) {
        return ['status' => 'error', 'message' => 'You already own this cosmetic.'];
    }

    $cosmetic = db_query("SELECT * FROM cosmetics WHERE id = ?", [$cosmetic_id]);
    if (!$cosmetic) {
        return ['status' => 'error', 'message' => 'Cosmetic not found.'];
    }

    $user_coins = get_user_coins($user_id);
    if ($user_coins < $cosmetic['price']) {
        return ['status' => 'error', 'message' => 'Not enough coins.'];
    }

    // Deduct coins
    subtract_coins($user_id, $cosmetic['price']);

    // Grant cosmetic
    db_query("INSERT INTO user_cosmetics (user_id, cosmetic_id) VALUES (?, ?)", [$user_id, $cosmetic_id]);

    return ['status' => 'success', 'message' => 'Cosmetic purchased successfully!'];
}

function get_cosmetic_by_ids($cosmetic_ids = []){
    if (empty($cosmetic_ids)) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($cosmetic_ids), '?'));
    $query = "SELECT * FROM cosmetics WHERE id IN ($placeholders)";
    return db_query($query, $cosmetic_ids, false);
}