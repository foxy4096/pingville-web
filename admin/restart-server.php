<style>
        body { font-family: monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; }
        pre { white-space: pre-wrap; word-wrap: break-word; }
    </style>
<?php
include "../utils/auth.php";
$user = get_auth_user(LoginMode::BOTH);

// Only allow admins
if (!$user || empty($user['is_admin'])) {
    http_response_code(403);
    exit("Forbidden: Admins only.");
}

// Print the current running processes on windows machine

function get_running_processes() {
    // Execute the command to get the list of running processes
    $output = shell_exec('tasklist');
    return $output;
}

echo "<h3>Running Processes</h3>";

// Get the list of running processes
$processes = get_running_processes();
// Display the processes in a preformatted block
echo "<pre>$processes</pre>";