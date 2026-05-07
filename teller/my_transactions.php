<?php
//Shows only the transactions this teller created.
$conn = mysqli_connect('localhost', 'root','' , 'projector_system');
require_once '../includes/auth.php';
requireLogin();
require_once '../db.php';

$pageTitle = "My Transactions";
$user = getLoggedInUser();
$myId = $user['id'];

// Auto-mark overdue
mysqli_query($conn, "UPDATE tbl_transactions SET status='overdue'
                     WHERE status='issued' AND expected_return < CURDATE()");

// Get my transactions
$myTransactions = mysqli_query($conn,
    "SELECT t.id, t.date_issued, t.expected_return, t.date_returned, t.status, t.purpose,
            p.model AS projector_model, p.serial_number,
            b.full_name AS borrower_name, b.department
     FROM tbl_transactions t
     JOIN tbl_projectors p ON t.projector_id = p.id
     JOIN tbl_borrowers  b ON t.borrower_id  = b.id
     WHERE t.issued_by = $myId
     ORDER BY t.date_issued DESC");

require_once '../includes/header.php';
?>

<div class="card">
    <div class="card-header"><h3>My Transactions (<?php echo mysqli_num_rows($myTransactions); ?>)</h3></div>
    <table>
        <thead>
            <tr><th>#</th><th>Projector</th><th>Borrower</th><th>Purpose</th><th>Date Issued</th><th>Expected Return</th><th>Returned On</th><th>Status</th></tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($myTransactions) == 0): ?>
            <tr><td colspan="8" class="text-center text-grey" style="padding:20px;">
                No transactions yet. <a href="issue.php">Issue your first projector →</a>
            </td></tr>
        <?php else: ?>
            <?php while ($t = mysqli_fetch_assoc($myTransactions)): ?>
            <tr>
                <td><?php echo $t['id']; ?></td>
                <td><?php echo $t['projector_model']; ?><br><small class="text-grey"><?php echo $t['serial_number']; ?></small></td>
                <td><?php echo $t['borrower_name']; ?><br><small class="text-grey"><?php echo $t['department']; ?></small></td>
                <td><?php echo $t['purpose'] ? $t['purpose'] : '—'; ?></td>
                <td><?php echo $t['date_issued']; ?></td>
                <td><?php echo $t['expected_return']; ?></td>
                <td><?php echo $t['date_returned'] ? $t['date_returned'] : '—'; ?></td>
                <td><span class="badge badge-<?php echo $t['status']; ?>"><?php echo $t['status']; ?></span></td>
            </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
