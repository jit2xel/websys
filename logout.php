<?php
session_start();

// Clear all session data
$_SESSION = [];

// Delete the session cookie (if any)
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destroy the session
session_destroy();

// Redirect back to login page
header('Location: login.php');
exit();
?>

<?php
session_start();

// 1. Wipe every session variable
$_SESSION = [];

// 2. Destroy the session cookie from the browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,          // expire in the past = delete it
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 3. Destroy the session on the server
session_destroy();

// 4. Redirect to login with a confirmation message
header("Location: login.php?logout=1");
exit();
?>