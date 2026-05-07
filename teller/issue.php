<?php

/*Teller selects a projector and a borrower,
fills in the dates, and submits the form.
This records the transaction and marks the
projector as issued.
*/
$conn = mysqli_connect('localhost', 'root','' , 'projector_system');
require_once '../includes/auth.php';
requireLogin();
require_once '../db.php';

$pageTitle = "Issue Projector";
$message   = '';
$msgType   = '';

//HANDLE FORM SUBMISSION 
if (isset($_POST['issue'])) {

    $projectorId    = (int)$_POST['projector_id'];
    $borrowerId     = (int)$_POST['borrower_id'];
    $purpose        = mysqli_real_escape_string($conn, trim($_POST['purpose']));
    $dateIssued     = mysqli_real_escape_string($conn, $_POST['date_issued']);
    $expectedReturn = mysqli_real_escape_string($conn, $_POST['expected_return']);
    $issuedBy       = $_SESSION['user_id'];


    // Validate
    if ($projectorId == 0 || $borrowerId == 0 || $expectedReturn == '') {
        $message = 'Please select a projector, a borrower and set the return date.';
        $msgType = 'error';
    } elseif ($expectedReturn <= $dateIssued) {
        $message = 'Return date must be after the issue date.';
        $msgType = 'error';
    } else {

        // Double check projector is still available
        $check = mysqli_query($conn, "SELECT status FROM tbl_projectors WHERE id = $projectorId");
        $proj  = mysqli_fetch_assoc($check);

        if ($proj['status'] != 'available') {
            $message = 'This projector is no longer available. Please choose another.';
            $msgType = 'error';
        } else {

            // Save the transaction
            $sql = "INSERT INTO tbl_transactions (projector_id, borrower_id, issued_by, purpose, date_issued, expected_return)
                    VALUES ($projectorId, $borrowerId, $issuedBy, '$purpose', '$dateIssued', '$expectedReturn')";
            mysqli_query($conn, $sql);

            // Mark the projector as issued
            mysqli_query($conn, "UPDATE tbl_projectors SET status = 'issued' WHERE id = $projectorId");

            $message = 'Projector issued successfully!';
            $msgType = 'success';
        }
    }
}


// Get available projectors for the dropdown
$availableProjectors = mysqli_query($conn,
    "SELECT * FROM tbl_projectors WHERE status = 'available' ORDER BY serial_number ASC");

// Get all borrowers for the dropdown
$allBorrowers = mysqli_query($conn, "SELECT * FROM tbl_borrowers ORDER BY full_name ASC");

require_once '../includes/header.php';
?>

<?php if ($message != ''): ?>
    <div class="alert alert-<?php echo $msgType; ?> auto-hide"><?php echo $message; ?></div>
<?php endif; ?>

<?php if (mysqli_num_rows($availableProjectors) == 0): ?>
    <div class="alert alert-warning">
        No projectors are available right now. All are issued out or in maintenance.
    </div>
<?php else: ?>

<div class="card">
    <div class="card-header"><h3> Issue a Projector</h3></div>
    <div class="card-body">
        <form method="POST" action="issue.php">

            <div class="form-group">
                <label>Select Projector * (<?php echo mysqli_num_rows($availableProjectors); ?> available)</label>
                <select name="projector_id" required>
                    <option value="">-- Choose a projector --</option>
                    <?php while ($p = mysqli_fetch_assoc($availableProjectors)): ?>
                        <option value="<?php echo $p['id']; ?>">
                            <?php echo $p['serial_number']; ?> -
                            <?php echo $p['brand']; ?>
                            <?php echo $p['model']; ?>
                            (<?php echo $p['condition_note']; ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Select Borrower *</label>
                <select name="borrower_id" required>
                    <option value="">-- Choose a borrower --</option>
                    <?php while ($b = mysqli_fetch_assoc($allBorrowers)): ?>
                        <option value="<?php echo $b['id']; ?>">
                            <?php echo $b['full_name']; ?>
                            <?php if ($b['department'] != '') echo ' - ' . $b['department']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Purpose / Reason</label>
                <textarea name="purpose" placeholder="e.g. Lecture on Database Systems..."></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Date Issued *</label>
                    <input type="date" name="date_issued"
                           value="<?php echo date('Y-m-d'); ?>"
                           max="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label>Expected Return Date *</label>
                    <input type="date" name="expected_return"
                           min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                </div>
            </div>

            <button type="submit" name="issue" class="btn btn-green">Issue Projector</button>
            <a href="dashboard.php" class="btn btn-grey">Cancel</a>

        </form>
    </div>
</div>

<div class="alert alert-info">
    If a borrower is not in the list, ask an admin to add them first.
</div>

<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
                    