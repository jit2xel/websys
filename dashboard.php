<?php
session_start();

// Guard — if no session, kick back to login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
$user_id  = htmlspecialchars($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
        }
        /* Top navbar */
        nav {
            background: #4f46e5;
            color: #fff;
            padding: 0 2rem;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        nav .brand { font-size: 18px; font-weight: 700; }
        nav .user-info { font-size: 13px; display: flex; align-items: center; gap: 16px; }
        nav .user-info span { opacity: 0.85; }
        nav .logout-btn {
            background: rgba(255,255,255,0.18);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s;
        }
        nav .logout-btn:hover { background: rgba(255,255,255,0.28); }

        /* Main content */
        .main { max-width: 860px; margin: 2rem auto; padding: 0 1rem; }

        .welcome-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            padding: 1.5rem 2rem;
            margin-bottom: 1.5rem;
        }
        .welcome-card h2 { font-size: 20px; color: #1a1a1a; margin-bottom: 6px; }
        .welcome-card p  { font-size: 14px; color: #666; }

        /* Stat grid */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
            margin-bottom: 1.5rem;
        }
        .stat {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            padding: 1.25rem;
        }
        .stat .label { font-size: 12px; color: #888; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; }
        .stat .value { font-size: 26px; font-weight: 700; color: #4f46e5; }

        /* Session info box */
        .info-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            padding: 1.5rem 2rem;
        }
        .info-card h3 { font-size: 15px; font-weight: 600; color: #1a1a1a; margin-bottom: 14px; }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 13px;
        }
        .info-row:last-child { border-bottom: none; }
        .info-row .key { color: #888; }
        .info-row .val { color: #222; font-weight: 500; }
    </style>
</head>
<body>

<nav>
    <div class="brand">MyApp</div>
    <div class="user-info">
        <span><?php echo $username; ?></span>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</nav>

<div class="main">

    <div class="welcome-card">
        <h2>Welcome back!</h2>
        <p>You are logged in as <strong><?php echo $username; ?></strong>. Here's your dashboard.</p>
    </div>

    <div class="stats">
        <div class="stat">
            <div class="label">User ID</div>
            <div class="value">#<?php echo $user_id; ?></div>
        </div>
        <div class="stat">
            <div class="label">Status</div>
            <div class="value" style="color:#16a34a; font-size:18px;">Active</div>
        </div>
        <div class="stat">
            <div class="label">Session</div>
            <div class="value" style="color:#d97706; font-size:18px;">Live</div>
        </div>
    </div>

    <div class="info-card">
        <h3>Session Details</h3>
        <div class="info-row">
            <span class="key">Email</span>
            <span class="val"><?php echo $username; ?></span>
        </div>
        <div class="info-row">
            <span class="key">User ID</span>
            <span class="val"><?php echo $user_id; ?></span>
        </div>
        <div class="info-row">
            <span class="key">Session ID</span>
            <span class="val"><?php echo session_id(); ?></span>
        </div>
        <div class="info-row">
            <span class="key">Login time</span>
            <span class="val"><?php echo date("F j, Y  g:i A"); ?></span>
        </div>
    </div>

</div>
</body>
</html>