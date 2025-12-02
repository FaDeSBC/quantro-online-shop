<?php
session_start();
include "../db.php";
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$status_updated = false;
$update_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $update_sql = "UPDATE orders SET status = '$new_status' WHERE id = '$order_id'";
    if (mysqli_query($conn, $update_sql)) {
        $status_updated = true;
        $update_message = "Order status updated to $new_status successfully!";
    }
}

$sql = "SELECT o.id as order_id, o.user_id, o.total_amount, o.status, o.created_at,
        u.name as user_name, u.email as user_email,
        GROUP_CONCAT(p.name SEPARATOR ', ') as product_names,
        GROUP_CONCAT(oi.quantity SEPARATOR ', ') as quantities
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        GROUP BY o.id
        ORDER BY o.created_at DESC";

$result = mysqli_query($conn, $sql);

$total_orders_sql = "SELECT COUNT(*) as total FROM orders";
$total_orders_result = mysqli_query($conn, $total_orders_sql);
$total_orders = mysqli_fetch_assoc($total_orders_result)['total'];

$total_revenue_sql = "SELECT SUM(total_amount) as revenue FROM orders";
$total_revenue_result = mysqli_query($conn, $total_revenue_sql);
$total_revenue = mysqli_fetch_assoc($total_revenue_result)['revenue'] ?? 0;

$total_customers_sql = "SELECT COUNT(DISTINCT user_id) as customers FROM orders";
$total_customers_result = mysqli_query($conn, $total_customers_sql);
$total_customers = mysqli_fetch_assoc($total_customers_result)['customers'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Orders - Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
</head>

<body>

<header class="header">
    <a href="../index.php" class="logo">QUANTRO</a>

    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="displayproduct.php">Products</a></li>
            <li><a href="vieworders.php">Orders</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<div class="admin-container">
    <div class="admin-header">
        <div class="admin-title">
            <h1>Order Management</h1>
            <p style="color: var(--text-secondary); margin-top: 0.5rem;">View and manage all customer orders</p>
        </div>
        <div class="admin-actions">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Orders</div>
            <div class="stat-value"><?php echo $total_orders; ?></div>
        </div>

        <div class="stat-card" style="border-left-color: var(--success-color);">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value" style="color: var(--success-color);">
                $<?php echo number_format($total_revenue, 2); ?>
            </div>
        </div>

        <div class="stat-card" style="border-left-color: var(--secondary-color);">
            <div class="stat-label">Total Customers</div>
            <div class="stat-value" style="color: var(--secondary-color);">
                <?php echo $total_customers; ?>
            </div>
        </div>

        <div class="stat-card" style="border-left-color: var(--warning-color);">
            <div class="stat-label">Average Order Value</div>
            <div class="stat-value" style="color: var(--warning-color);">
                $<?php echo $total_orders > 0 ? number_format($total_revenue / $total_orders, 2) : '0.00'; ?>
            </div>
        </div>
    </div>

    <div class="table-container">
        <div class="table-header">
            <h2>All Orders</h2>
        </div>

        <?php if ($result && mysqli_num_rows($result) > 0) { ?>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Products</th>
                        <th>Amount</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td style="font-weight: 600; color: var(--primary-color);">
                                #<?php echo htmlspecialchars($order['order_id']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['user_email']); ?></td>
                            <td style="max-width: 250px;">
                                <?php 
                                $products = explode(', ', $order['product_names']);
                                $quantities = explode(', ', $order['quantities']);
                                $display = [];
                                for ($i = 0; $i < count($products); $i++) {
                                    $display[] = htmlspecialchars($products[$i]) . ' (x' . $quantities[$i] . ')';
                                }
                                echo implode(', ', $display);
                                ?>
                            </td>
                            <td style="font-weight: 600; color: var(--success-color);">
                                $<?php echo number_format($order['total_amount'], 2); ?>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                            <td>
                                <span class="order-status <?php 
                                    echo $order['status'] == 'Completed' ? 'status-completed' : 
                                        ($order['status'] == 'Pending' ? 'status-pending' : 'status-cancelled'); 
                                ?>">
                                    <?php echo htmlspecialchars($order['status']); ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <select name="status" class="status-select" style="padding: 0.5rem; border: 2px solid var(--border-color); border-radius: 6px; font-size: 0.85rem;">
                                        <option value="Pending" <?php echo $order['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Paid" <?php echo $order['status'] == 'Paid' ? 'selected' : ''; ?>>Paid</option>
                                        <option value="Completed" <?php echo $order['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="Cancelled" <?php echo $order['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem; margin-left: 0.5rem;">
                                        Update
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
                <h3>No orders found</h3>
                <p style="margin-top: 0.5rem;">Orders will appear here once customers start purchasing.</p>
            </div>
        <?php } ?>
    </div>
</div>

<footer class="footer">
    <p>&copy; <?php echo date('Y'); ?> Quantro Admin. All rights reserved.</p>
</footer>

<script src="../js/alert.js"></script>
<script>
    <?php if ($status_updated): ?>
        Alert.success('<?php echo addslashes($update_message); ?>');
    <?php endif; ?>
</script>

</body>
</html>