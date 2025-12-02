<?php
session_start();
include "db.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') {
    header("Location: admin/dashboard.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';
$total_orders_sql = "SELECT COUNT(*) as total FROM single_order WHERE usere_id = '$user_id'";
$total_orders_result = mysqli_query($conn, $total_orders_sql);
$total_orders = mysqli_fetch_assoc($total_orders_result)['total'];
$total_spent_sql = "SELECT SUM(total_amount) as spent FROM single_order WHERE usere_id = '$user_id'";
$total_spent_result = mysqli_query($conn, $total_spent_sql);
$total_spent = mysqli_fetch_assoc($total_spent_result)['spent'] ?? 0;
$recent_orders_sql = "SELECT so.id, so.order_date, so.total_amount, p.name as product_name, p.image 
                      FROM single_order so 
                      LEFT JOIN products p ON so.product_id = p.id 
                      WHERE so.usere_id = '$user_id' 
                      ORDER BY so.order_date DESC LIMIT 3";
$recent_orders_result = mysqli_query($conn, $recent_orders_sql);
$user_info_sql = "SELECT * FROM users WHERE id = '$user_id'";
$user_info_result = mysqli_query($conn, $user_info_sql);
$user_info = mysqli_fetch_assoc($user_info_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Dashboard - Quantro Shop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <style>
        .welcome-banner {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 3rem 2rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            text-align: center;
            box-shadow: var(--shadow-lg);
        }

        .welcome-banner h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .welcome-banner p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .dashboard-card {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .dashboard-card h3 {
            font-size: 1.25rem;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem;
            background: var(--card-bg);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            text-decoration: none;
            color: var(--text-primary);
            font-weight: 600;
            transition: var(--transition);
        }

        .action-btn:hover {
            border-color: var(--primary-color);
            background: rgba(59, 130, 246, 0.05);
            transform: translateY(-2px);
        }

        .recent-order-item {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            background: var(--light-bg);
            border-radius: 12px;
            margin-bottom: 1rem;
            transition: var(--transition);
        }

        .recent-order-item:hover {
            background: var(--border-color);
        }

        .recent-order-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        .recent-order-info {
            flex: 1;
        }

        .recent-order-info h4 {
            margin-bottom: 0.25rem;
            color: var(--text-primary);
        }

        .recent-order-info p {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.5rem;
            background: var(--light-bg);
            border-radius: 12px;
        }

        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: 700;
        }

        .user-details h4 {
            margin-bottom: 0.25rem;
            color: var(--text-primary);
        }

        .user-details p {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }
    </style>
</head>

<body>

<header class="header">
    <a href="index.php" class="logo">QUANTRO</a>

    <nav>
        <ul>
            <li><a href="index.php">Shop</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="myorders.php">My Orders</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<div class="orders-container">
    <div class="welcome-banner">
        <h1>Welcome back, <?php echo htmlspecialchars($user_name); ?>! 👋</h1>
        <p>Here's what's happening with your account today</p>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-card">
            <h3>Total Orders</h3>
            <div class="stat-value" style="color: var(--primary-color);"><?php echo $total_orders; ?></div>
            <p style="color: var(--text-secondary); margin-top: 0.5rem;">All time purchases</p>
        </div>

        <div class="dashboard-card">
            <h3>Total Spent</h3>
            <div class="stat-value" style="color: var(--success-color);">$<?php echo number_format($total_spent, 2); ?></div>
            <p style="color: var(--text-secondary); margin-top: 0.5rem;">Lifetime spending</p>
        </div>

        <div class="dashboard-card">
            <h3>Account Status</h3>
            <div style="margin-top: 1rem;">
                <span class="order-status status-completed">Active</span>
            </div>
            <p style="color: var(--text-secondary); margin-top: 0.5rem;">Member since <?php echo date('M Y', strtotime($user_info['created_at'] ?? 'now')); ?></p>
        </div>
    </div>

    <div class="dashboard-card" style="margin-bottom: 2rem;">
        <h3>Profile Information</h3>
        <div class="user-profile">
            <div class="user-avatar">
                <?php echo strtoupper(substr($user_name, 0, 1)); ?>
            </div>
            <div class="user-details">
                <h4><?php echo htmlspecialchars($user_info['name']); ?></h4>
                <p><?php echo htmlspecialchars($user_info['email']); ?></p>
                <p style="margin-top: 0.5rem;">
                    <span class="order-status status-pending" style="font-size: 0.8rem;">
                        <?php echo ucfirst($user_info['role']); ?>
                    </span>
                </p>
            </div>
        </div>
    </div>

    <div class="dashboard-card">
        <h3>Recent Orders</h3>
        <?php if ($recent_orders_result && mysqli_num_rows($recent_orders_result) > 0) { ?>
            <div style="margin-top: 1rem;">
                <?php while ($order = mysqli_fetch_assoc($recent_orders_result)) { ?>
                    <div class="recent-order-item">
                        <?php if ($order['image']) { ?>
                            <img src="image/<?php echo htmlspecialchars($order['image']); ?>" alt="Product">
                        <?php } ?>
                        <div class="recent-order-info">
                            <h4><?php echo htmlspecialchars($order['product_name']); ?></h4>
                            <p>Order #<?php echo htmlspecialchars($order['id']); ?> • <?php echo date('M j, Y', strtotime($order['order_date'])); ?></p>
                            <p style="font-weight: 600; color: var(--success-color); margin-top: 0.25rem;">
                                $<?php echo number_format($order['total_amount'], 2); ?>
                            </p>
                        </div>
                        <span class="order-status status-completed">Completed</span>
                    </div>
                <?php } ?>
            </div>
            <div style="margin-top: 1rem; text-align: center;">
                <a href="myorders.php" class="btn btn-primary" style="text-decoration: none; display: inline-block;">
                    View All Orders
                </a>
            </div>
        <?php } else { ?>
            <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                <p>No orders yet</p>
                <a href="index.php" class="btn btn-primary" style="margin-top: 1rem; display: inline-block; text-decoration: none;">
                    Start Shopping
                </a>
            </div>
        <?php } ?>
    </div>

    <div class="dashboard-card">
        <h3>Quick Actions</h3>
        <div class="quick-actions">
            <a href="index.php" class="action-btn">
                🛍️ Browse Products
            </a>
            <a href="myorders.php" class="action-btn">
                📦 My Orders
            </a>
            <a href="index.php" class="action-btn">
                🔍 Search Products
            </a>
            <a href="logout.php" class="action-btn">
                🚪 Logout
            </a>
        </div>
    </div>
</div>

<footer class="footer">
    <p>&copy; <?php echo date('Y'); ?> Quantro. All rights reserved.</p>
</footer>

</body>
</html>