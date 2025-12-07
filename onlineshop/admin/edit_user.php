<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = intval($_GET['id']);
$user = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();

if (!$user) {
    header("Location: admin_users.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $phone   = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $role    = mysqli_real_escape_string($conn, $_POST['role']);

    // Check if email already exists for other users
    $check_email = $conn->query("SELECT id FROM users WHERE email='$email' AND id != $id");
    if ($check_email->num_rows > 0) {
        $error = "Email already exists!";
    } else {
        $sql = "UPDATE users 
                SET name='$name', email='$email', phone='$phone', address='$address', role='$role'
                WHERE id=$id";

        if ($conn->query($sql)) {
            $success = "User updated successfully!";
            // Refresh user data
            $user = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Quantro Admin</title>
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
            <h1>Edit User</h1>
            <p style="color: var(--text-secondary); margin-top: 0.5rem;">
                Update user information and permissions
            </p>
        </div>
        <div class="admin-actions">
            <a href="admin_users.php" class="btn btn-secondary">Back to Users</a>
        </div>
    </div>

    <div class="form-container" style="max-width: 600px; margin: 0 auto;">
        <?php if ($error): ?>
            <div style="background: #fee; border: 1px solid #fcc; color: #c33; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div style="background: #efe; border: 1px solid #cfc; color: #3c3; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">
                    User ID
                </label>
                <input type="text" 
                       value="#<?= $user['id'] ?>" 
                       disabled
                       style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; background: #f5f5f5;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">
                    Full Name *
                </label>
                <input type="text" 
                       name="name" 
                       value="<?= htmlspecialchars($user['name']) ?>" 
                       required
                       style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">
                    Email *
                </label>
                <input type="email" 
                       name="email" 
                       value="<?= htmlspecialchars($user['email']) ?>" 
                       required
                       style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">
                    Phone *
                </label>
                <input type="text" 
                       name="phone" 
                       value="<?= htmlspecialchars($user['phone']) ?>" 
                       required
                       style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">
                    Address *
                </label>
                <textarea name="address" 
                          required
                          rows="3"
                          style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; resize: vertical;"><?= htmlspecialchars($user['address']) ?></textarea>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">
                    Role *
                </label>
                <select name="role" 
                        required
                        style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;">
                    <option value="user" <?= $user['role']=="user"?'selected':'' ?>>User (Customer)</option>
                    <option value="admin" <?= $user['role']=="admin"?'selected':'' ?>>Admin</option>
                </select>
                <small style="color: var(--text-secondary); display: block; margin-top: 0.5rem;">
                    Admin users have full access to the admin panel
                </small>
            </div>

            <?php if ($user['id'] == $_SESSION['user_id']): ?>
                <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <strong>Note:</strong> You are editing your own account. Be careful when changing your role.
                </div>
            <?php endif; ?>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    Update User
                </button>
                <a href="admin_users.php" class="btn btn-secondary" style="flex: 1; text-align: center; text-decoration: none;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<footer class="footer">
    <p>&copy; <?php echo date('Y'); ?> Quantro Admin. All rights reserved.</p>
</footer>

</body>
</html>