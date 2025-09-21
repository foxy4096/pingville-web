<?php
include "utils/auth.php";
include_once "db.php";

// Check if user is authenticated
$user = get_auth_user(LoginMode::BOTH);


// Helper: Format status badge
function format_status($status)
{
    $class = $status ? 'online' : 'offline';
    $text = $status ? 'Online' : 'Offline';
    return "<span class='status-badge $class'>$text</span>";
}

$current_datetime = date("Y-m-d H:i:s", time()); // UTC time

// Check DB connection and stats
$db_status = false;
$users_count = 0;
$active_users_count = 0;
$db_info = [];
$db_error = '';

try {
    $test_query = db_query("SELECT 1");
    $db_status = ($test_query !== false);

    if ($db_status) {
        $users_query = db_query("SELECT COUNT(*) as count FROM users");
        $users_count = $users_query['count'];

        $active_users_query = db_query("SELECT COUNT(DISTINCT user_id) AS active_users_count
                                        FROM auth_tokens
                                        WHERE created_at > NOW() - INTERVAL 24 HOUR;");
        $active_users_count = $active_users_query['active_users_count'];
        $db_info = db_query("SELECT @@version as version, database() as db_name");
    }
} catch (Exception $e) {
    $db_error = $e->getMessage();
}

// Function to check if game server is reachable
function check_server($host, $port, $timeout = 3)
{
    $connection = @fsockopen($host, $port, $errno, $errstr, $timeout);
    if (is_resource($connection)) {
        fclose($connection);
        return true;
    }
    return false;
}

// Server info
$server_host = $_ENV['GAME_SERVER_HOST'] ?? '127.0.0.1';
$server_listening_port = $_ENV['GAME_SERVER_LISTENING_PORT'] ?? 9050;
$server_actual_port = $_ENV['GAME_SERVER_ACTUAL_PORT'] ?? 9000;
$game_server_status = check_server($server_host, $server_listening_port, 3);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>System Status - PingVille</title>
    <link rel="stylesheet" href="assets/css/status_page.css">
    <style>
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            color: white;
        }

        .online {
            background-color: #28a745;
        }

        .offline {
            background-color: #dc3545;
        }

        

        .admin-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <header>
        <h1>PingVille System Status 🦥</h1>
        <p>Current information about our game services</p>
        <?php if ($user): ?>
            <p>Hello, <?= htmlspecialchars($user['username']) ?>!
                <a href="me.php" class="button">Profile</a>
                <a href="logout.php" class="button">Logout</a>
            </p>
        <?php endif; ?>
    </header>

    <section>
        <p>Last updated: <?= $current_datetime ?> UTC</p>

        <div class="status-card">
            <h3>Database Status <?= format_status($db_status) ?></h3>
            <?php if ($db_status): ?>
                <table class="status-table">
                    <tr>
                        <th>Database Name</th>
                        <td><?= htmlspecialchars($db_info['db_name']) ?></td>
                    </tr>
                    <tr>
                        <th>Total Users</th>
                        <td><?= $users_count ?></td>
                    </tr>
                    <tr>
                        <th>Active Users (24h)</th>
                        <td><?= $active_users_count ?></td>
                    </tr>
                    <tr>
                        <th>Database Server</th>
                        <td><?= htmlspecialchars($db_info['version']) ?></td>
                    </tr>
                </table>
            <?php else: ?>
                <p class="error">Database connection failed: <?= htmlspecialchars($db_error ?: "Could not establish connection") ?></p>
            <?php endif; ?>
        </div>

        <div class="status-card">
            <h3>Game Server Status <?= format_status($game_server_status) ?></h3>
            <?php if ($game_server_status): ?>
                <table class="status-table">
                    <tr>
                        <th>Server Host</th>
                        <td><?= htmlspecialchars($server_host) ?></td>
                    </tr>
                    <tr>
                        <th>Server Port</th>
                        <td><?= htmlspecialchars($server_listening_port) ?>
                            [Actual Port: <?= htmlspecialchars($server_actual_port) ?>]</td>
                    </tr>
                    </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>Accepting connections</td>
                    </tr>
                    <tr>
                        <th>Online Players</th>
                        <td id="online-players">Checking...</td>
                    </tr>
                </table>
            <?php else: ?>
                <p class="error">Game server is currently offline.</p>
            <?php endif; ?>
        </div>

        <div class="status-card">
            <h3>System Information</h3>
            <table class="status-table">
                <tr>
                    <th>Web Server</th>
                    <td><?= htmlspecialchars($_SERVER['SERVER_SOFTWARE']) ?></td>
                </tr>
                <tr>
                    <th>PHP Version</th>
                    <td><?= phpversion() ?></td>
                </tr>
                <tr>
                    <th>Current Time (UTC)</th>
                    <td><?= $current_datetime ?></td>
                </tr>
                <tr>
                    <th>Server Load</th>
                    <td>
                        <?php
                        $cache_file = 'cache/cpu_load.txt';
                        $cache_lifetime = 60; // Cache for 1 minute

                        // Check if the cache file exists, if not, create it with a default value
                        if (!file_exists($cache_file)) {
                            // Create the cache file with a default value (e.g., "0")
                            file_put_contents($cache_file, "0");
                        }

                        // Check if cache file is available and not expired
                        if (time() - filemtime($cache_file) < $cache_lifetime) {
                            // Read from the cache
                            $load = file_get_contents($cache_file);
                        } else {
                            // Run the command only if cache is expired
                            if (strtoupper(PHP_OS) === 'WINNT') {
                                $load = shell_exec('wmic cpu get loadpercentage');
                                $load = trim($load);
                            } else {
                                if (function_exists('sys_getloadavg')) {
                                    $load = sys_getloadavg();
                                    $load = implode(", ", array_map(fn($v) => number_format($v, 2), array_slice($load, 0, 3)));
                                } else {
                                    $load = "Not available";
                                }
                            }

                            // Cache the new result
                            file_put_contents($cache_file, $load);
                        }

                        echo "CPU Load: " . $load;
                        ?>
                    </td>
                </tr>

            </table>
        </div>

        <?php if ($user && !empty($user['is_admin'])): ?>
            <div class="status-card">
                <h3>Admin Actions</h3>
                <div class="admin-actions">
                    <a href="admin/restart-server.php" class="button" onclick="return confirm('Restart game server?')">Restart Game Server</a>
                    <a href="admin/maintenance-mode.php" class="button">Toggle Maintenance Mode</a>
                    <a href="admin/logs.php" class="button">View Server Logs</a>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <?php include "templates/footer.php"; ?>

    <?php if ($game_server_status): ?>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                fetch('api/server_status.php')
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('online-players').textContent = data.online_players ?? "Unknown";
                    })
                    .catch(err => {
                        console.error('Error:', err);
                        document.getElementById('online-players').textContent = "Error";
                    });
            });
        </script>
    <?php endif; ?>
</body>

</html>