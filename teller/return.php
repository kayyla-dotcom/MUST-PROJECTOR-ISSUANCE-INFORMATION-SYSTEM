<?php
/*
Shows all projectors currently out (issued/overdue).
Teller clicks "Mark Returned" to close the transaction.
*/
$conn = mysqli_connect('localhost', 'root','' , 'projector_system');
require_once '../includes/auth.php';
requireLogin();
require_once '../db.php';

$pageTitle = "Record Return";
$message   = '';
$msgType   = '';

//HANDLE RETURN SUBMISSION 
if (isset($_POST['return_id'])) {

    $txId  = (int)$_POST['return_id'];
    $notes = mysqli_real_escape_string($conn, trim($_POST['notes']));
    $today = date('Y-m-d');

    // Get the transaction to find which projector to free
    $result = mysqli_query($conn, "SELECT * FROM tbl_transactions WHERE id = $txId");
    $tx     = mysqli_fetch_assoc($result);

    if ($tx && ($tx['status'] == 'issued' || $tx['status'] == 'overdue')) {

        // Mark transaction as returned
        mysqli_query($conn, "UPDATE tbl_transactions SET status='returned',
                             date_returned='$today', notes='$notes'
                             WHERE id=$txId");

        // Free up the projector - set back to available
        $projId = $tx['projector_id'];
        mysqli_query($conn, "UPDATE tbl_projectors SET status='available' WHERE id=$projId");

        $message = 'Return recorded! The projector is now available again.';
        $msgType = 'success';

    } else {
        $message = 'Transaction not found or already returned.';
        $msgType = 'error';
    }
}

// Get all currently active transactions
$activeTransactions = mysqli_query($conn,
    "SELECT t.id, t.date_issued, t.expected_return, t.status, t.purpose,
            p.model AS projector_model, p.serial_number, p.brand,
            b.full_name AS borrower_name, b.department, b.contact
     FROM tbl_transactions t
     JOIN tbl_projectors p ON t.projector_id = p.id
     JOIN tbl_borrowers  b ON t.borrower_id  = b.id
     WHERE t.status IN ('issued', 'overdue')
     ORDER BY t.expected_return ASC");

require_once '../includes/header.php';
?>

<?php if ($message != ''): ?>
    <div class="alert alert-<?php echo $msgType; ?> auto-hide"><?php echo $message; ?></div>
<?php endif; ?>

<?php if (mysqli_num_rows($activeTransactions) == 0): ?>
    <div class="alert alert-info">All projectors have been returned. Nothing is out right now.</div>
<?php else: ?>

<div class="card">
    <div class="card-header">
        <h3>Projectors Currently Out (<?php echo mysqli_num_rows($activeTransactions); ?>)</h3>
    </div>
    <table>
        <thead>
            <tr><th>#</th><th>Projector</th><th>Borrower</th><th>Date Issued</th><th>Expected Return</th><th>Status</th><th>Action</th></tr>
        </thead>
        <tbody>
        <?php while ($t = mysqli_fetch_assoc($activeTransactions)): ?>
            <?php
            $isOverdue = ($t['status'] == 'overdue');
            $daysLate  = 0;
            if ($isOverdue) {
                $daysLate = (int)((time() - strtotime($t['expected_return'])) / 86400);
            }
            ?>
            <tr <?php if ($isOverdue) echo 'style="background:#fff5f5"'; ?>>
                <td><?php echo $t['id']; ?></td>
                <td>
                    <strong><?php echo $t['projector_model']; ?></strong><br>
                    <small class="text-grey"><?php echo $t['brand']; ?> · <?php echo $t['serial_number']; ?></small>
                </td>
                <td>
                    <?php echo $t['borrower_name']; ?><br>
                    <small class="text-grey"><?php echo $t['department']; ?></small><br>
                    <small><?php echo $t['contact']; ?></small>
                </td>
                <td><?php echo $t['date_issued']; ?></td>
                <td>
                    <?php echo $t['expected_return']; ?>
                    <?php if ($isOverdue): ?>
                        <br><strong class="text-red"><?php echo $daysLate; ?> day(s) overdue</strong>
                    <?php endif; ?>
                </td>
                <td><span class="badge badge-<?php echo $t['status']; ?>"><?php echo $t['status']; ?></span></td>
                <td>
                    <form method="POST" action="return.php">
                        <input type="hidden" name="return_id" value="<?php echo $t['id']; ?>">
                        <input type="text" name="notes" placeholder="Notes (optional)"
                               style="width:140px; padding:4px 8px; font-size:12px; margin-bottom:5px;
                                      border:1px solid #a7f3d0; border-radius:4px;"><br>
                        <button type="submit" class="btn btn-green btn-small"
                                onclick="return confirm('Mark this projector as returned?')">
                            Mark Returned
                        </button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
