<?php
/*

  includes/auth.php - Login Protection


This file protects pages from people who are not logged in.
  requireLogin(); ........for pages any logged in user can see
  requireAdmin();  ........ for pages only admins can see
*/

// Start the session 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


/* 
   Checks if someone is logged in
*/
  
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        // Not logged in - go back to login page
        header("Location: ../index.php");
        exit();
    }
}


/* 
   Checks if the logged in user is an admin.
 */
function requireAdmin() {
    requireLogin(); // First check they are logged in
    if ($_SESSION['role'] != 'admin') {
        // Logged in but not admin - go to teller area
        header("Location: ../teller/dashboard.php");
        exit();
    }
}


/* 
   Returns the current user's info as an array.
 */
function getLoggedInUser() {
    $user = array();
    $user['id']         = isset($_SESSION['user_id'])    ? $_SESSION['user_id']    : 0;
    $user['first_name'] = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : '';
    $user['username']   = isset($_SESSION['username'])   ? $_SESSION['username']   : '';
    $user['role']       = isset($_SESSION['role'])       ? $_SESSION['role']       : '';
    return $user;
}
?>
