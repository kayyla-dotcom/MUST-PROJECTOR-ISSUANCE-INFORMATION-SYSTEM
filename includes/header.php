<?php

$user = getLoggedInUser();
$role = $user['role'];

// Decide what name to show in the navbar
if ($user['first_name'] != '') {
    $displayName = $user['first_name'];
} else {
    $displayName = $user['username'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?>MUST PROJECTOR ISSUANCE SYSTEM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="topbar">

    <!-- System name on the left -->
    <div class="topbar-brand">
        <strong>MUST PROJECTOR ISSUANCE SYSTEM</strong>
    </div>

    <!-- Navigation links in the middle -->
    <nav class="topbar-nav">

        <?php if ($role == 'admin'): ?>
        <!-- ADMIN links -->
        <a href="../admin/dashboard.php">Dashboard</a>
        <a href="../admin/projectors.php">Projectors</a>
        <a href="../admin/borrowers.php">Borrowers</a>
        <a href="../admin/transactions.php">Transactions</a>
        <a href="../admin/users.php">Staff</a>

        <!-- Reports dropdown -->
        <div class="dropdown">
            <a href="#">Reports ▾</a>
            <div class="dropdown-menu">
                <a href="../reports/all_transactions.php">All Transactions PDF</a>
                <a href="../reports/projector_status.php">Projector Status PDF</a>
                <a href="../reports/overdue.php">Overdue Report PDF</a>
            </div>
        </div>

        <?php else: ?>
        <!-- TELLER links -->
        <a href="../teller/dashboard.php">Dashboard</a>
        <a href="../teller/issue.php">Issue Projector</a>
        <a href="../teller/return.php">Record Return</a>
        <a href="../teller/my_transactions.php">My Transactions</a>
        <?php endif; ?>

    </nav>

    <!-- User info and logout on the right -->
    <div class="topbar-right">
        <?php if ($role == 'admin'): ?>
            <span class="role-pill admin-pill">Admin</span>
        <?php else: ?>
            <span class="role-pill teller-pill">Teller</span>
        <?php endif; ?>
        <span class="username-text"><?php echo $displayName; ?></span>
        <a href="../logout.php" class="logout-btn">Logout</a>
    </div>

</div>

<!--MAIN CONTENT AREA -->
<div class="main-content">
    <div class="page-title-bar">
        <h2><?php echo $pageTitle; ?></h2>
    </div>
