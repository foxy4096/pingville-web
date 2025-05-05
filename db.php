<?php

include "_secrets.php";

$db = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}


$GLOBALS['conn'] = $db; // Replace it with your own `mysqli_connect` :)

function db_query($sql, $params = array(), $single = true)
{
    $stmt = mysqli_prepare($GLOBALS['conn'], $sql);

    if (!$stmt) {
        // Handle SQL query preparation error
        return null;
    }

    // Determine parameter types dynamically based on the values passed
    $types = '';
    foreach ($params as $param) {
        if (is_int($param)) {
            $types .= 'i'; // Integer
        } elseif (is_float($param)) {
            $types .= 'd'; // Double
        } elseif (is_string($param)) {
            $types .= 's'; // String
        } else {
            $types .= 'b'; // Blob
        }
    }

    // Bind parameters with dynamically determined types to the prepared statement
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    // Execute the prepared statement
    $success = mysqli_stmt_execute($stmt);

    if (!$success) {
        // Handle SQL query execution error
        return null;
    }

    $result = mysqli_stmt_get_result($stmt);
    $affected_rows = mysqli_stmt_affected_rows($stmt);

    if ($result === false) {
        if ($affected_rows > 0){
            return mysqli_stmt_insert_id($stmt);
        }
        // Handle result retrieval error
        return null;
    }

    if ($single) {
        $row = mysqli_fetch_assoc($result);
    } else {
        $row = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    mysqli_stmt_close($stmt);

    return $row;
}