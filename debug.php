<?php

session_start(); // Start the session

echo json_encode($_SESSION); // Output the session data as JSON