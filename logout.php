<?php

include "utils/users.php"; // Include the utility functions

logout_user(); // Call the logout function

header('Location: ../index.php'); // Redirect to the home page
exit; // Optional: exit the script to ensure no further code is executed