<?php
header('Content-Type: application/json');
include_once "../db.php";

$host = $_ENV['GAME_SERVER_HOST'] ?? '127.0.0.1';
$port = 9050;
$timeout = 3;
$server_status = false;

$fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
$response = '';
if ($fp) {
    stream_set_timeout($fp, $timeout);
    $response = fgets($fp, 128);
    fclose($fp);
    if ($response !== false) {
        $server_status = true;
    }
}

if (preg_match('/players=(\d+)/', $response, $matches)) {
    $online_players = (int)$matches[1];
} else {
    $online_players = 0;
}

echo json_encode([
    'status' => $server_status ? 'online' : 'offline',
    'online_players' => $online_players,
    'timestamp' => date('Y-m-d H:i:s')
]);