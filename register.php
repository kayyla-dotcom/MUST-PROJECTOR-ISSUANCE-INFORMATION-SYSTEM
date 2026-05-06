<?php
session_start();
require_once 'db.php';
$conn = mysqli_connect('localhost', 'root','' , 'projector_system');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username         = trim($_POST['username']         ?? '');
    $email            = trim($_POST['email']            ?? '');
    $password         =      $_POST['password']         ?? '';
    $confirm_password =      $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
    }

    if (strlen($password) < 8) {
        echo "<script>alert('Password must be at least 8 characters.'); window.history.back();</script>";
        exit();
    }

    $check = mysqli_prepare($conn, "SELECT id FROM tbl_users WHERE username = ? OR email = ?");
    mysqli_stmt_bind_param($check, 'ss', $username, $email);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        echo "<script>alert('Username or email already taken!'); window.history.back();</script>";
        mysqli_stmt_close($check);
        exit();
    }
    mysqli_stmt_close($check);

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // status = 'pending' added so admin must approve before user can login
    $stmt = mysqli_prepare($conn, "INSERT INTO tbl_users (username, email, password, role, status) VALUES (?, ?, ?, 'user', 'pending')");
    mysqli_stmt_bind_param($stmt, 'sss', $username, $email, $hashed_password);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        // updated message to inform user about admin approval
        echo "<script>alert('Registration submitted! Please wait for admin approval before logging in.'); window.location.href='login.php';</script>";
    } else {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        echo "<script>alert('Registration failed. Try again.'); window.history.back();</script>";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="reg-container">
        <div class="form-container">
            <p class="create">Create Account</p>
            <form action="" method="POST">
                <div class="input-group">

                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter username" required>

                    <br><br>

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter email" required>

                    <br><br>

                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter password" required>

                    <br><br>

                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>

                    <br><br>

                    <button type="submit">Register</button>

                </div>
            </form>
        </div>
    </div>
</body>
</html>