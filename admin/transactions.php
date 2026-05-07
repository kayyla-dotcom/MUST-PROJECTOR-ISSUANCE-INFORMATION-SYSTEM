<?php
/*
admin/transactions.php - View All Transactions
Admin sees every transaction. Can filter by status.
Also auto-marks overdue transactions.
*/
$conn = mysqli_connect('localhost', 'root','' , 'projector_system');
require_once '../includes/auth.php';
requireAdmin();
require_once '../db.php';

$pageTitle = "All Transactions";

// Auto-mark overdue: if still 'issued' but past due date, change to 'overdue'
mysqli_query($conn, "UPDATE tbl_transactions SET status = 'overdue'
                     WHERE status = 'issued' AND expected_return < CURDATE()");

// Filter by status from URL e.g. ?status=overdue
$filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Build query based on filter
if ($filter == 'all') {
    $sql = "SELECT t.*, p.model AS projector_model, p.serial_number,
                   b.full_name AS borrower_name, b.department,
                   u.username AS issued_by
            FROM tbl_transactions t
            JOIN tbl_projectors p ON t.projector_id = p.id
            JOIN tbl_borrowers  b ON t.borrower_id  = b.id
            JOIN tbl_users      u ON t.issued_by    = u.id
            ORDER BY t.date_issued DESC";
} else {
    $safeFilter = mysqli_real_escape_string($conn, $filter);
    $sql = "SELECT t.*, p.model AS projector_model, p.serial_number,
                   b.full_name AS borrower_name, b.department,
                   u.username AS issued_by
            FROM tbl_transactions t
            JOIN tbl_projectors p ON t.projector_id = p.id
            JOIN tbl_borrowers  b ON t.borrower_id  = b.id
            JOIN tbl_users      u ON t.issued_by    = u.id
            WHERE t.status = '$safeFilter'
            ORDER BY t.date_issued DESC";
}

$transactions = mysqli_query($conn, $sql);

require_once '../includes/header.php';
?>

<!-- Filter buttons -->
<div class="action-row">
    <a href="transactions.php?status=all"      class="btn <?php echo $filter == 'all'      ? 'btn-green' : 'btn-grey'; ?>">All</a>
    <a href="transactions.php?status=issued"   class="btn <?php echo $filter == 'issued'   ? 'btn-green' : 'btn-grey'; ?>">Issued</a>
    <a href="transactions.php?status=returned" class="btn <?php echo $filter == 'returned' ? 'btn-green' : 'btn-grey'; ?>">Returned</a>
    <a href="transactions.php?status=overdue"  class="btn <?php echo $filter == 'overdue'  ? 'btn-red'   : 'btn-grey'; ?>">Overdue</a>
    <a href="../reports/all_transactions.php"  class="btn btn-gold" style="margin-left:auto;">Export PDF</a>
</div>

<div class="card">
    <div class="card-header">
        <h3>Transactions - <?php echo ucfirst($filter); ?></h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th><th>Projector</th><th>Borrower</th><th>Department</th>
                <th>Issued By</th><th>Date Issued</th><th>Expected Return</th>
                <th>Returned On</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($transactions) == 0): ?>
            <tr><td colspan="9" class="text-center text-grey" style="padding:20px;">No transactions found.</td></tr>
        <?php else: ?>
            <?php while ($t = mysqli_fetch_assoc($transactions)): ?>
            <tr>
                <td><?php echo $t['id']; ?></td>
                <td>
                    <strong><?php echo $t['projector_model']; ?></strong><br>
                    <small class="text-grey"><?php echo $t['serial_number']; ?></small>
                </td>
                <td><?php echo $t['borrower_name']; ?></td>
                <td><?php echo $t['department']; ?></td>
                <td><?php echo $t['issued_by']; ?></td>
                <td><?php echo $t['date_issued']; ?></td>
                <td>
                    <?php echo $t['expected_return']; ?>
                    <?php if ($t['status'] == 'overdue'): ?>
                        <br><small class="text-red">
                            <?php
                            // Calculate days overdue
                            $due  = strtotime($t['expected_return']);
                            $today = time();
                            $days  = (int)(($today - $due) / 86400);
                            echo $days . ' days late';
                            ?>
                        </small>
                    <?php endif; ?>
                </td>
                <td><?php echo $t['date_returned'] ? $t['date_returned'] : '—'; ?></td>
                <td><span class="badge badge-<?php echo $t['status']; ?>"><?php echo $t['status']; ?></span></td>
            </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>


<?php require_once '../includes/footer.php'; ?>
