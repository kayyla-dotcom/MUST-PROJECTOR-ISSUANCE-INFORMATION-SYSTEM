<?php
$conn = mysqli_connect('localhost', 'root','' , 'projector_system');
require_once '../includes/auth.php';
requireAdmin();
require_once '../db.php';

$pageTitle = "Manage Projectors";
$message   = '';
$msgType   = '';


if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {

  
    $id = (int)$_GET['id'];

    // Do not delete if projector is currently issued
    $check  = mysqli_query($conn, "SELECT status FROM tbl_projectors WHERE id = $id");
    $proj   = mysqli_fetch_assoc($check);

    if ($proj['status'] == 'issued') {
        $message = 'Cannot delete - this projector is currently issued out.';
        $msgType = 'error';
    } else {
        mysqli_query($conn, "DELETE FROM tbl_projectors WHERE id = $id");
        $message = 'Projector deleted.';
        $msgType = 'success';
    }
}

if (isset($_POST['save'])) {

    // Get form values
    $serial    = mysqli_real_escape_string($conn, trim($_POST['serial_number']));
    $brand     = mysqli_real_escape_string($conn, trim($_POST['brand']));
    $model     = mysqli_real_escape_string($conn, trim($_POST['model']));
    $status    = mysqli_real_escape_string($conn, $_POST['status']);
    $condition = mysqli_real_escape_string($conn, trim($_POST['condition_note']));
    $editId    = (int)$_POST['edit_id']; // 0 = new, >0 = editing existing

    // Check required fields
    if ($serial == '' || $brand == '' || $model == '') {
        $message = 'Serial number, brand and model are required.';
        $msgType = 'error';

    } else {

        if ($editId > 0) {
            // UPDATE existing projector
            $sql = "UPDATE tbl_projectors SET
                        serial_number  = '$serial',
                        brand          = '$brand',
                        model          = '$model',
                        status         = '$status',
                        condition_note = '$condition'
                    WHERE id = $editId";
            mysqli_query($conn, $sql);
            $message = 'Projector updated successfully.';
            $msgType = 'success';

        } else {
            // INSERT new projector
            $sql = "INSERT INTO tbl_projectors (serial_number, brand, model, status, condition_note)
                    VALUES ('$serial', '$brand', '$model', '$status', '$condition')";
            mysqli_query($conn, $sql);
            $message = 'New projector added successfully.';
            $msgType = 'success';
        }
    }
}


$editing = null; 

if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id     = (int)$_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM tbl_projectors WHERE id = $id");
    $editing = mysqli_fetch_assoc($result);
}


$allProjectors = mysqli_query($conn, "SELECT * FROM tbl_projectors ORDER BY id ASC");

require_once '../includes/header.php';
?>

<!-- Flash message -->
<?php if ($message != ''): ?>
    <div class="alert alert-<?php echo $msgType; ?> auto-hide">
        <?php echo $message; ?>
    </div>
<?php endif; ?>


<!-- ADD / EDIT FORM -->
<div class="card">
    <div class="card-header">
        <?php if ($editing): ?>
            <h3>Edit Projector</h3>
            <a href="projectors.php" class="btn btn-grey btn-small">Cancel</a>
        <?php else: ?>
            <h3>Add New Projector</h3>
        <?php endif; ?>
    </div>
    <div class="card-body">

        <form method="POST" action="projectors.php">

            <input type="hidden" name="edit_id"
                   value="<?php echo $editing ? $editing['id'] : 0; ?>">

            <div class="form-row">
                <div class="form-group">
                    <label>Serial Number *</label>
                    <input type="text" name="serial_number"
                           placeholder="e.g. PRJ-021"
                           value="<?php echo $editing ? $editing['serial_number'] : ''; ?>"
                           required>
                </div>
                <div class="form-group">
                    <label>Brand *</label>
                    <input type="text" name="brand"
                           placeholder="e.g. Epson"
                           value="<?php echo $editing ? $editing['brand'] : ''; ?>"
                           required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Model *</label>
                    <input type="text" name="model"
                           placeholder="e.g. EB-X41"
                           value="<?php echo $editing ? $editing['model'] : ''; ?>"
                           required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="available"
                            <?php if ($editing && $editing['status'] == 'available') echo 'selected'; ?>>
                            Available
                        </option>
                        <option value="issued"
                            <?php if ($editing && $editing['status'] == 'issued') echo 'selected'; ?>>
                            Issued
                        </option>
                        <option value="maintenance"
                            <?php if ($editing && $editing['status'] == 'maintenance') echo 'selected'; ?>>
                            Maintenance
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Condition Notes</label>
                <input type="text" name="condition_note"
                       placeholder="e.g. Good, Minor scratch on lens..."
                       value="<?php echo $editing ? $editing['condition_note'] : 'Good'; ?>">
            </div>

            <button type="submit" name="save" class="btn btn-green">
                <?php echo $editing ? 'Save Changes' : 'Add Projector'; ?>
            </button>

        </form>
    </div>
</div>


<!-- PROJECTORS TABLE -->
<div class="card">
    <div class="card-header">
        <h3>All Projectors</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Serial No.</th>
                <th>Brand</th>
                <th>Model</th>
                <th>Status</th>
                <th>Condition</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>

        <?php while ($p = mysqli_fetch_assoc($allProjectors)): ?>
            <tr>
                <td><?php echo $p['id']; ?></td>
                <td><strong><?php echo $p['serial_number']; ?></strong></td>
                <td><?php echo $p['brand']; ?></td>
                <td><?php echo $p['model']; ?></td>
                <td>
                    <span class="badge badge-<?php echo $p['status']; ?>">
                        <?php echo $p['status']; ?>
                    </span>
                </td>
                <td><?php echo $p['condition_note']; ?></td>
                <td>
                    <a href="projectors.php?action=edit&id=<?php echo $p['id']; ?>"
                       class="btn btn-gold btn-small">Edit</a>

                    <a href="projectors.php?action=delete&id=<?php echo $p['id']; ?>"
                       class="btn btn-red btn-small confirm-delete">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>

        </tbody>
    </table>
</div>



<?php require_once '../includes/footer.php'; ?>
