<?php
/*
When the user clicks Logout, they come here.
We clear the session and send them to login.
*/

session_start();
session_unset();    // Clear all session data
session_destroy();  // Delete the session completely

header("Location: login.php");
exit();
?>
