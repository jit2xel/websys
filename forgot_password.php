<?php
session_start();

// If already logged in, go straight to dashboard
if (isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'db.php';

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($email === '' || $password === '' || $confirm === '') {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif (!hash_equals($password, $confirm)) {
        $error = 'Passwords do not match.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // If the email exists, update its password.
        // We return a generic success message either way to avoid user enumeration.
        $stmt = $conn->prepare('UPDATE users SET password = ? WHERE email = ?');
        if (!$stmt) {
            $error = 'Password reset temporarily unavailable.';
        } else {
            $stmt->bind_param('ss', $hash, $email);
            $stmt->execute();
            $stmt->close();
            $success = 'If that email exists, the password has been updated. You can now sign in.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .card { background: #fff; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 12px rgba(0,0,0,0.1); width: 100%; max-width: 440px; }
        h2 { margin-bottom: 1.5rem; font-size: 22px; color: #1a1a1a; }
        label { display: block; font-size: 13px; color: #555; margin-bottom: 4px; margin-top: 14px; }
        input[type=email], input[type=password] { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; outline: none; }
        input:focus { border-color: #4f46e5; }
        .btn { width: 100%; margin-top: 20px; padding: 11px; background: #4f46e5; color: #fff; font-size: 15px; font-weight: 600; border: none; border-radius: 6px; cursor: pointer; }
        .btn:hover { background: #4338ca; }
        .error { margin-top: 12px; padding: 10px 12px; background: #fee2e2; color: #b91c1c; border-radius: 6px; font-size: 13px; }
        .success { margin-top: 12px; padding: 10px 12px; background: #dcfce7; color: #166534; border-radius: 6px; font-size: 13px; }
        .sub { margin-top: 14px; text-align: center; font-size: 13px; color: #555; }
        .sub a { color:#4f46e5; text-decoration:none; font-weight:600; }
    </style>
</head>
<body>
<div class="card">
    <h2>Reset Password</h2>
    <form method="POST" action="forgot_password.php">
        <label>Email</label>
        <input type="email" name="email" placeholder="you@example.com"
               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>

        <label>New Password</label>
        <input type="password" name="password" placeholder="••••••••" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" placeholder="••••••••" required>

        <button type="submit" class="btn">Update Password</button>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
    </form>

    <div class="sub">
        Remembered your password? <a href="login.php">Sign in</a>
    </div>
</div>
</body>
</html>

