<?php

$page = $_GET['page'] ?? 'index.php'; // Get the page from the query string, default to index.php

header("Location: $page"); // Redirect to the specified page
exit; // Exit the script to prevent further execution