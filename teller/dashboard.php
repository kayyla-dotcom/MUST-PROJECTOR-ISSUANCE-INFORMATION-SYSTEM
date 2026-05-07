<?php

//Shows the teller their stats and recent transactions.

$conn = mysqli_connect('localhost', 'root','' , 'projector_system');
require_once '../includes/auth.php';
requireLogin();
require_once '../db.php';

$pageTitle = "Dashboard";
$user = getLoggedInUser();

// Counts
$r = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_projectors WHERE status = 'available'");
$totalAvailable = mysqli_fetch_assoc($r)['total'];

$r = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_projectors WHERE status = 'issued'");
$totalIssued = mysqli_fetch_assoc($r)['total'];

$r = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_transactions WHERE status = 'overdue'");
$totalOverdue = mysqli_fetch_assoc($r)['total'];

// My transactions count
$myId = $user['id'];
$r = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_transactions WHERE issued_by = $myId");
$myTotal = mysqli_fetch_assoc($r)['total'];

// My recent transactions
$myTransactions = mysqli_query($conn,
    "SELECT t.id, t.date_issued, t.expected_return, t.status,
            p.model AS projector_model, p.serial_number,
            b.full_name AS borrower_name
     FROM tbl_transactions t
     JOIN tbl_projectors p ON t.projector_id = p.id
     JOIN tbl_borrowers  b ON t.borrower_id  = b.id
     WHERE t.issued_by = $myId
     ORDER BY t.created_at DESC
     LIMIT 8");

require_once '../includes/header.php';
?>

//STAT CARDS 
<div class="stats-row">
    <div class="stat-card">
        <div>
            <div class="stat-label">Available Now</div>
            <div class="stat-number"><?php echo $totalAvailable; ?></div>
        </div>
    </div>
    <div class="stat-card orange">
        <div>
            <div class="stat-label">Issued Out</div>
            <div class="stat-number"><?php echo $totalIssued; ?></div>
        </div>
    </div>
    <div class="stat-card red">
        <div>
            <div class="stat-label">Overdue</div>
            <div class="stat-number"><?php echo $totalOverdue; ?></div>
        </div>
    </div>
    <div class="stat-card blue">
        <div>
            <div class="stat-label">My Transactions</div>
            <div class="stat-number"><?php echo $myTotal; ?></div>
        </div>
    </div>
</div>

//QUICK ACTIONS 
<div class="action-row">
    <a href="issue.php"  class="btn btn-green">Issue Projector</a>
    <a href="return.php" class="btn btn-blue">Record Return</a>
</div>

//MY RECENT TRANSACTIONS 
<div class="card">
    <div class="card-header">
        <h3>My Recent Transactions</h3>
        <a href="my_transactions.php" class="btn btn-grey btn-small">View All</a>
    </div>
    <table>
        <thead>
            <tr><th>#</th><th>Projector</th><th>Borrower</th><th>Date Issued</th><th>Expected Return</th><th>Status</th></tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($myTransactions) == 0): ?>
            <tr><td colspan="6" class="text-center text-grey" style="padding:20px;">
                No transactions yet. <a href="issue.php">Issue your first projector →</a>
            </td></tr>
        <?php else: ?>
            <?php while ($t = mysqli_fetch_assoc($myTransactions)): ?>
            <tr>
                <td><?php echo $t['id']; ?></td>
                <td><?php echo $t['projector_model']; ?><br><small class="text-grey"><?php echo $t['serial_number']; ?></small></td>
                <td><?php echo $t['borrower_name']; ?></td>
                <td><?php echo $t['date_issued']; ?></td>
                <td><?php echo $t['expected_return']; ?></td>
                <td><span class="badge badge-<?php echo $t['status']; ?>"><?php echo $t['status']; ?></span></td>
            </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>



<?php require_once '../includes/footer.php'; ?>
