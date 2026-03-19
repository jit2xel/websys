<?php
session_start();

// Guard: only admins can access this page
$isAdmin = !empty($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
if (!$isAdmin) {
    header('Location: login.php');
    exit();
}

require 'db.php';

$adminUsername = $_SESSION['username'] ?? 'admin';

$users = [];
$res = $conn->query('SELECT id, email FROM users ORDER BY id DESC');
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $users[] = $row;
    }
}

$adminId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
        }
        nav {
            background: #111827;
            color: #fff;
            padding: 0 2rem;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        nav .brand { font-size: 18px; font-weight: 800; letter-spacing: 0.01em; }
        nav .user-info { font-size: 13px; display: flex; align-items: center; gap: 16px; }
        nav .logout-btn {
            background: rgba(255,255,255,0.12);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s;
        }
        nav .logout-btn:hover { background: rgba(255,255,255,0.22); }

        .main { max-width: 980px; margin: 2rem auto; padding: 0 1rem; }

        .welcome-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            padding: 1.5rem 2rem;
            margin-bottom: 1.25rem;
        }
        .welcome-card h2 { font-size: 20px; color: #0f172a; margin-bottom: 6px; }
        .welcome-card p { font-size: 14px; color: #4b5563; }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        }
        th, td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            text-align: left;
            font-size: 14px;
        }
        th { background: #f8fafc; color: #334155; font-weight: 700; }
        tr:last-child td { border-bottom: none; }

        .actions { text-align: right; }
        .btn {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-danger { background: #ef4444; color: #fff; }
        .btn-danger:hover { background: #dc2626; }
        .btn-disabled { background: #94a3b8; color: #fff; cursor: not-allowed; opacity: 0.8; }
        .hint { margin-top: 10px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
<nav>
    <div class="brand">Admin</div>
    <div class="user-info">
        <span>Signed in as <strong><?php echo htmlspecialchars($adminUsername); ?></strong></span>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</nav>

<div class="main">
    <div class="welcome-card">
        <h2>Manage Users</h2>
        <p>Delete any user account. (Admin cannot delete the admin account.)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 90px;">User ID</th>
                <th>Email</th>
                <th style="width: 200px;" class="actions">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($users) === 0): ?>
            <tr>
                <td colspan="3" style="color:#6b7280;">No users found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($users as $u): ?>
                <?php $uid = (int)$u['id']; ?>
                <tr>
                    <td>#<?php echo $uid; ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td class="actions">
                        <?php if ($uid === $adminId): ?>
                            <button type="button" class="btn btn-danger btn-disabled" disabled>Delete</button>
                        <?php else: ?>
                            <form method="POST" action="admin_delete_user.php" onsubmit="return confirm('Delete user #<?php echo $uid; ?>?');" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $uid; ?>">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="hint">
        Make sure your `users` table has columns: `id`, `email`, `password`.
    </div>
</div>
</body>
</html>

