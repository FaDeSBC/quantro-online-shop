<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$result = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Quantro Admin</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>

<header class="header">
    <a href="../index.php" class="logo">QUANTRO</a>

    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="admin_users.php" class="active">Users</a></li>
            <li><a href="displayproduct.php">Products</a></li>
            <li><a href="vieworders.php">Orders</a></li>
            <li><a href="addproduct.php">Add Product</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<div class="admin-container">
    <div class="admin-header">
        <div class="admin-title">
            <h1>User Management</h1>
            <p style="color: var(--text-secondary); margin-top: 0.5rem;">
                Manage all users and administrators
            </p>
        </div>
        <div class="admin-actions">
            <a href="add_user.php" class="btn btn-primary">Add New User</a>
        </div>
    </div>

    <div class="table-container">
        <div class="table-header">
            <h2>All Users</h2>
            <p style="color: var(--text-secondary); margin: 0;">
                Total: <?php echo mysqli_num_rows($result); ?> users
            </p>
        </div>

        <?php if ($result && mysqli_num_rows($result) > 0) { ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td style="font-weight: 600; color: var(--primary-color);">
                            #<?= $row['id'] ?>
                        </td>
                        <td style="font-weight: 600;">
                            <?= htmlspecialchars($row['name']) ?>
                        </td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td style="max-width: 200px;">
                            <?= htmlspecialchars(substr($row['address'], 0, 50)) . (strlen($row['address']) > 50 ? '...' : '') ?>
                        </td>
                        <td>
                            <span class="order-status <?= $row['role'] === 'admin' ? 'status-completed' : 'status-pending' ?>">
                                <?= ucfirst($row['role']) ?>
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                <a href="edit_user.php?id=<?= $row['id'] ?>" 
                                   class="btn btn-primary" 
                                   style="padding: 0.5rem 1rem; font-size: 0.85rem; text-decoration: none;">
                                    Edit
                                </a>
                                <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                <a href="delete_user.php?id=<?= $row['id'] ?>" 
                                   class="btn btn-danger" 
                                   style="padding: 0.5rem 1rem; font-size: 0.85rem; text-decoration: none;"
                                   onclick="return confirm('Are you sure you want to delete this user?')">
                                    Delete
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
                <h3>No users found</h3>
                <p style="margin-top: 0.5rem;">Add your first user to get started.</p>
                <a href="add_user.php" class="btn btn-primary" style="margin-top: 1rem; display: inline-block; text-decoration: none;">
                    Add User
                </a>
            </div>
        <?php } ?>
    </div>
</div>

<footer class="footer">
    <p>&copy; <?php echo date('Y'); ?> Quantro Admin. All rights reserved.</p>
</footer>

</body>
</html>