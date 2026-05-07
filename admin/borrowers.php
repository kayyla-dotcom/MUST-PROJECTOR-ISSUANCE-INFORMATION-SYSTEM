<?php
$conn = mysqli_connect('localhost', 'root','' , 'projector_system');
require_once '../includes/auth.php';
requireAdmin();
require_once '../db.php';

$pageTitle = "Manage Borrowers";
$message   = '';
$msgType   = '';

/* ---- DELETE ---- */
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    // Check if borrower has transactions - cannot delete if they do
    $check = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_transactions WHERE borrower_id = $id");
    $row   = mysqli_fetch_assoc($check);
    if ($row['total'] > 0) {
        $message = 'Cannot delete - this borrower has existing transactions.';
        $msgType = 'error';
    } else {
        mysqli_query($conn, "DELETE FROM tbl_borrowers WHERE id = $id");
        $message = 'Borrower deleted.';
        $msgType = 'success';
    }
}

/* ---- ADD OR EDIT ---- */
if (isset($_POST['save'])) {
    $fullName   = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $department = mysqli_real_escape_string($conn, trim($_POST['department']));
    $contact    = mysqli_real_escape_string($conn, trim($_POST['contact']));
    $email      = mysqli_real_escape_string($conn, trim($_POST['email']));
    $editId     = (int)$_POST['edit_id'];

    if ($fullName == '') {
        $message = 'Full name is required.';
        $msgType = 'error';
    } else {
        if ($editId > 0) {
            $sql = "UPDATE tbl_borrowers SET full_name='$fullName', department='$department',
                    contact='$contact', email='$email' WHERE id=$editId";
            mysqli_query($conn, $sql);
            $message = 'Borrower updated.';
            $msgType = 'success';
        } else {
            $sql = "INSERT INTO tbl_borrowers (full_name, department, contact, email)
                    VALUES ('$fullName', '$department', '$contact', '$email')";
            mysqli_query($conn, $sql);
            $message = 'Borrower added.';
            $msgType = 'success';
        }
    }
}

/* ---- LOAD FOR EDITING ---- */
$editing = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id      = (int)$_GET['id'];
    $result  = mysqli_query($conn, "SELECT * FROM borrowers WHERE id = $id");
    $editing = mysqli_fetch_assoc($result);
}

/* ---- GET ALL BORROWERS ---- */
$allBorrowers = mysqli_query($conn, "SELECT * FROM tbl_borrowers ORDER BY full_name ASC");

require_once '../includes/header.php';
?>

<?php if ($message != ''): ?>
    <!-- auto-hide class makes this disappear after 4 seconds via main.js -->
    <div class="alert alert-<?php echo $msgType; ?> auto-hide"><?php echo $message; ?></div>
<?php endif; ?>

<!-- FORM -->
<div class="card">
    <div class="card-header">
        <?php if ($editing): ?>
            <h3>Edit Borrower</h3>
            <a href="borrowers.php" class="btn btn-grey btn-small">Cancel</a>
        <?php else: ?>
            <h3>Add Borrower</h3>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <form method="POST" action="borrowers.php">
            <input type="hidden" name="edit_id" value="<?php echo $editing ? $editing['id'] : 0; ?>">

            <div class="form-row">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="full_name" required
                           placeholder="e.g. Dr. John Banda"
                           value="<?php echo $editing ? $editing['full_name'] : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Department</label>
                    <input type="text" name="department"
                           placeholder="e.g. Computer Science" required
                           value="<?php echo $editing ? $editing['department'] : ''; ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="tel" name="contact"
                           placeholder="e.g. 0881234567" pattern="[0-9]+" title="Phone numbers only" required
                           value="<?php echo $editing ? $editing['contact'] : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email"
                           placeholder="borrower@school.edu" required
                           value="<?php echo $editing ? $editing['email'] : ''; ?>">
                </div>
            </div>

            <button type="submit" name="save" class="btn btn-green">
                <?php echo $editing ? 'Save Changes' : 'Add Borrower'; ?>
            </button>
        </form>
    </div>
</div>

<!-- TABLE -->
<div class="card">
    <div class="card-header"><h3>All Borrowers</h3></div>
    <table>
        <thead>
            <tr><th>#</th><th>Full Name</th><th>Department</th><th>Contact</th><th>Email</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php while ($b = mysqli_fetch_assoc($allBorrowers)): ?>
            <tr>
                <td><?php echo $b['id']; ?></td>
                <td><strong><?php echo $b['full_name']; ?></strong></td>
                <td><?php echo $b['department']; ?></td>
                <td><?php echo $b['contact']; ?></td>
                <td><?php echo $b['email']; ?></td>
                <td>
                    <a href="borrowers.php?action=edit&id=<?php echo $b['id']; ?>" class="btn btn-gold btn-small">Edit</a>
                    <a href="borrowers.php?action=delete&id=<?php echo $b['id']; ?>" class="btn btn-red btn-small confirm-delete">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>



<?php require_once '../includes/footer.php'; ?>
