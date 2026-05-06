<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password =      $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo "<script>alert('Please fill in all fields.'); window.history.back();</script>";
        exit();
    }

    // add 'status' 
    $stmt = mysqli_prepare($conn, "SELECT id, username, password, role, status FROM tbl_users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user   = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($user && password_verify($password, $user['password'])) {

        // blocks pending accounts
        if ($user['status'] === 'pending') {
            mysqli_close($conn);
            echo "<script>alert('Your account is awaiting admin approval.'); window.history.back();</script>";
            exit();
        }

        // blocks inactive/rejected accounts
        if ($user['status'] === 'inactive') {
            mysqli_close($conn);
            echo "<script>alert('Your account has been deactivated. Contact the admin.'); window.history.back();</script>";
            exit();
        }

        // active account should start session
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];
        mysqli_close($conn);

        // redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: teller/dashboard.php");
        }
        exit();

    } else {
        mysqli_close($conn);
        echo "<script>alert('Invalid email or password.'); window.history.back();</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> MUST Projector Issuance Information System | Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h3>Login</h3>
            <form action="" method="POST">
            <div class="input-group">
                
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter email" required><br><br>
                
                <label for="password">Password</label>
                <div class="password-box">
                    <input type="password" id="password" name="password" placeholder="Enter password" required>
                </div>

                <br>

                <p class="link">Do not have an account? <a href="register.php">Register</a></p>

                <button type="submit">Login</button>
            </div>
            </form>
        </div>
    </div>

    <script>
    </script>
</body>
</html>