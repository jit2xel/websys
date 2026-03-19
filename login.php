<?php
session_start();

// Configure the single admin account here (email must match exactly).
// You can change this value to your admin's email.
$ADMIN_EMAIL = 'admin@admin.com'; 

// If already logged in, go straight to dashboard
if (isset($_SESSION['username'])) {
    // Re-check admin status from the session email (prevents stale `is_admin` values).
    $isAdmin = (strtolower(trim($_SESSION['username'])) === strtolower(trim($ADMIN_EMAIL)));
    $_SESSION['is_admin'] = $isAdmin;
    header('Location: ' . ($isAdmin ? 'admin_dashboard.php' : 'dashboard.php'));
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'db.php'; // uses $conn from db.php

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $conn->prepare('SELECT id, email, password FROM users WHERE email = ? LIMIT 1');

        if ($stmt) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->bind_result($id, $db_email, $db_password);
            $stmt->fetch();
            $stmt->close();

            // Supports both hashed (recommended) and legacy plain-text passwords
            $validHashed = is_string($db_password) && password_verify($password, $db_password);
            $validPlain  = !$validHashed && hash_equals((string)$db_password, (string)$password);
            $valid = $validHashed || $validPlain;

            if ($id && $valid) {
                session_regenerate_id(true);
                $_SESSION['user_id']  = $id;
                $_SESSION['username'] = $db_email;

                $isAdmin = (strtolower(trim($db_email)) === strtolower(trim($ADMIN_EMAIL)));
                $_SESSION['is_admin'] = $isAdmin;

                header('Location: ' . ($isAdmin ? 'admin_dashboard.php' : 'dashboard.php'));
                exit();
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
            $error = 'Login temporarily unavailable.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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
    </style>
</head>
<body>
<div class="card">
    <h2>Login</h2>
    <form method="POST" action="login.php">
        <label>Email</label>
        <input type="email" name="email" placeholder="you@example.com"
               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
        <label>Password</label>
        <input type="password" name="password" placeholder="••••••••" required>
        <button type="submit" class="btn">Sign In</button>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
    </form>
    <div style="margin-top:10px; text-align:center; font-size:13px;">
        <a href="forgot_password.php" style="color:#4f46e5; text-decoration:none; font-weight:600;">Forgot password?</a>
    </div>
    <div style="margin-top:14px; text-align:center; font-size:13px; color:#555;">
        Don’t have an account?
        <a href="register.php" style="color:#4f46e5; text-decoration:none; font-weight:600;">Create one</a>
    </div>
</div>
</body>
</html>