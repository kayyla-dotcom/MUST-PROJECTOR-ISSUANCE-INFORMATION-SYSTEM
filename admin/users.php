<?php
//Admin can add, edit, delete, approve and reject accounts.

$conn = mysqli_connect('localhost', 'root','' , 'projector_system');
require_once '../includes/auth.php';
requireAdmin();
require_once '../db.php';

$pageTitle = "Staff Accounts";
$message   = '';
$msgType   = '';

//APPROVE 
if (isset($_GET['action']) && $_GET['action'] == 'approve' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "UPDATE tbl_users SET status = 'active' WHERE id = $id");
    $message = 'Account approved successfully.';
    $msgType = 'success';
}

//REJECT 
if (isset($_GET['action']) && $_GET['action'] == 'reject' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "UPDATE tbl_users SET status = 'inactive' WHERE id = $id");
    $message = 'Account rejected.';
    $msgType = 'error';
}

//DELETE 
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id == $_SESSION['user_id']) {
        $message = 'You cannot delete your own account.';
        $msgType = 'error';
    } else {
        mysqli_query($conn, "DELETE FROM tbl_users WHERE id = $id");
        $message = 'Account deleted.';
        $msgType = 'success';
    }
}

// ADD OR EDIT 
if (isset($_POST['save'])) {
    $username  = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email     = mysqli_real_escape_string($conn, trim($_POST['email']));
    $role      = mysqli_real_escape_string($conn, $_POST['role']);
    $password  = $_POST['password'];
    $editId    = (int)$_POST['edit_id'];

    if ($username == '' || $email == '') {
        $message = 'Username and email are required.';
        $msgType = 'error';
    } else {
        if ($editId > 0) {
            if ($password != '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $hash = mysqli_real_escape_string($conn, $hash);
                $sql  = "UPDATE tbl_users SET username='$username', email='$email', role='$role', password='$hash'
                         WHERE id=$editId";
            } else {
                $sql = "UPDATE tbl_users SET username='$username', email='$email', role='$role'
                        WHERE id=$editId";
            }
            mysqli_query($conn, $sql);
            $message = 'Account updated.';
            $msgType = 'success';
        } else {
            if ($password == '') {
                $message = 'Password is required for new accounts.';
                $msgType = 'error';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $hash = mysqli_real_escape_string($conn, $hash);
                // Accounts created by admin are active immediately
                $sql  = "INSERT INTO tbl_users (username, email, password, role, status)
                         VALUES ('$username', '$email', '$hash', '$role', 'active')";
                mysqli_query($conn, $sql);
                $message = "Account created. Username: $username";
                $msgType = 'success';
            }
        }
    }
}

//LOAD FOR EDITING
$editing = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id      = (int)$_GET['id'];
    $result  = mysqli_query($conn, "SELECT * FROM tbl_users WHERE id = $id");
    $editing = mysqli_fetch_assoc($result);
}

// Fetch pending users for approval panel
$pendingResult = mysqli_query($conn, "SELECT * FROM tbl_users WHERE status = 'pending' ORDER BY id DESC");
$pendingCount  = mysqli_num_rows($pendingResult);

// Only show active/inactive staff in the main table (not pending)
$allUsers = mysqli_query($conn, "SELECT * FROM tbl_users WHERE status != 'pending' ORDER BY role DESC, username ASC");

require_once '../includes/header.php';
?>

<?php if ($message != ''): ?>
    <div class="alert alert-<?php echo $msgType; ?> auto-hide"><?php echo $message; ?></div>
<?php endif; ?>


     //PENDING APPROVALS PANEL
<div class="card" style="margin-bottom:25px;">
    <div class="card-header">
        <h3>Pending Approvals
            <?php if ($pendingCount > 0): ?>
                <span style="background:#e74c3c; color:#fff; border-radius:50%;
                             padding:2px 9px; font-size:13px; margin-left:6px;">
                    <?php echo $pendingCount; ?>
                </span>
            <?php endif; ?>
        </h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($pendingCount == 0): ?>
            <tr>
                <td colspan="5" class="text-center text-grey" style="padding:20px;">
                    No pending accounts.
                </td>
            </tr>
        <?php else: ?>
            <?php while ($u = mysqli_fetch_assoc($pendingResult)): ?>
            <tr>
                <td><?php echo $u['id']; ?></td>
                <td><?php echo htmlspecialchars($u['username']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td><span class="badge badge-<?php echo $u['role']; ?>"><?php echo $u['role']; ?></span></td>
                <td>
                    <a href="users.php?action=approve&id=<?php echo $u['id']; ?>"
                       class="btn btn-green btn-small"
                       onclick="return confirm('Approve this account?')">
                        Approve
                    </a>
                    <a href="users.php?action=reject&id=<?php echo $u['id']; ?>"
                       class="btn btn-red btn-small"
                       onclick="return confirm('Reject this account?')">
                        Reject
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>


<div class="card">
    <div class="card-header"><h3>All Staff</h3></div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($u = mysqli_fetch_assoc($allUsers)): ?>
            <tr>
                <td><?php echo $u['id']; ?></td>
                <td><?php echo htmlspecialchars($u['username']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td><span class="badge badge-<?php echo $u['role']; ?>"><?php echo $u['role']; ?></span></td>
                <td>
                    //Show active/inactive status badge 
                    <span class="badge badge-<?php echo $u['status']; ?>">
                        <?php echo ucfirst($u['status']); ?>
                    </span>
                </td>
                <td>
                    <a href="users.php?action=edit&id=<?php echo $u['id']; ?>" class="btn btn-gold btn-small">Edit</a>
                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                        <a href="users.php?action=delete&id=<?php echo $u['id']; ?>"
                           class="btn btn-red btn-small confirm-delete">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>


<?php require_once '../includes/footer.php'; ?>