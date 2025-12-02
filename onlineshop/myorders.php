<?php
session_start();
include "db.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';

$sql = "SELECT o.id as order_id, o.total_amount, o.status, o.created_at,
        GROUP_CONCAT(p.name SEPARATOR ', ') as product_names,
        GROUP_CONCAT(oi.quantity SEPARATOR ', ') as quantities
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE o.user_id = '$user_id'
        GROUP BY o.id
        ORDER BY o.created_at DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders - Quantro Shop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
</head>

<body>

<header class="header">
    <a href="index.php" class="logo">QUANTRO</a>

    <nav>
        <ul>
            <li><a href="index.php">Shop</a></li>
            <li><a href="cart.php">Cart</a></li>
            <li><a href="myorders.php">My Orders</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<div class="orders-container">
    <div class="orders-header">
        <h1>My Orders</h1>
        <p>Welcome back, <?php echo htmlspecialchars($user_name); ?>! Here are your recent orders.</p>
    </div>

    <?php
    if ($result && mysqli_num_rows($result) > 0) {
        ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Products</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td style="font-weight: 600; color: var(--primary-color);">
                                #<?php echo htmlspecialchars($order['order_id']); ?>
                            </td>
                            <td>
                                <?php 
                                $products = explode(', ', $order['product_names']);
                                $quantities = explode(', ', $order['quantities']);
                                for ($i = 0; $i < count($products); $i++) {
                                    echo htmlspecialchars($products[$i]) . ' (x' . $quantities[$i] . ')';
                                    if ($i < count($products) - 1) echo '<br>';
                                }
                                ?>
                            </td>
                            <td style="font-weight: 600; color: var(--success-color);">
                                $<?php echo number_format($order['total_amount'], 2); ?>
                            </td>
                            <td>
                                <span class="order-status <?php 
                                    echo $order['status'] == 'Completed' ? 'status-completed' : 
                                        ($order['status'] == 'Pending' ? 'status-pending' : 'status-cancelled'); 
                                ?>">
                                    <?php echo htmlspecialchars($order['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                            <td>
                                <a href="order_details.php?order_id=<?php echo $order['order_id']; ?>" 
                                   class="btn btn-primary" 
                                   style="padding: 0.5rem 1rem; font-size: 0.85rem; text-decoration: none;">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php
    } else {
        ?>
        <div class="no-orders">
            <h2>No orders yet</h2>
            <p style="color: var(--text-secondary); margin-top: 0.5rem;">Start shopping to see your orders here!</p>
            <a href="index.php">Browse Products</a>
        </div>
        <?php
    }
    ?>
</div>

<footer class="footer">
    <p>&copy; <?php echo date('Y'); ?> Quantro. All rights reserved.</p>
</footer>

</body>
</html>