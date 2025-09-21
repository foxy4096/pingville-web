<?php
include "../utils/auth.php";
$user = get_auth_user(LoginMode::BOTH);

// Only allow admins
if (!$user || empty($user['is_admin'])) {
    http_response_code(403);
    exit("Forbidden: Admins only.");
}

// Path to your server log file (adjust this!)
$log_path = "C:/pingville_logs/server.log";

if (!file_exists($log_path)) {
    exit("Log file not found.");
}

// Display last 100 lines (optional)
$lines = file($log_path);
$last_lines = array_slice($lines, -100);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Server Logs</title>
    <style>
        body { font-family: monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; }
        pre { white-space: pre-wrap; word-wrap: break-word; }
    </style>
</head>
<body>
    <h1>Server Logs</h1>
    <pre><?= htmlspecialchars(implode("", $last_lines)) ?></pre>
</body>
</html>
