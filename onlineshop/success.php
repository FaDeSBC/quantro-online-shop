<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = mysqli_real_escape_string($conn, $_GET['order_id']);

$order_sql = "SELECT o.*, u.name as user_name, u.email, u.phone, u.address
              FROM orders o
              LEFT JOIN users u ON o.user_id = u.id
              WHERE o.id = '$order_id' AND o.user_id = '$user_id'";
$order_result = mysqli_query($conn, $order_sql);

if (!$order_result || mysqli_num_rows($order_result) == 0) {
    header("Location: index.php");
    exit();
}

$order = mysqli_fetch_assoc($order_result);

$items_sql = "SELECT oi.*, p.name, p.image, p.description
              FROM order_items oi
              LEFT JOIN products p ON oi.product_id = p.id
              WHERE oi.order_id = '$order_id'";
$items_result = mysqli_query($conn, $items_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Success - Quantro Shop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <style>
        .success-icon {
            width: 80px;
            height: 80px;
            background: var(--success-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 3rem;
            color: white;
        }
    </style>
</head>

<body>

<header class="header">
    <a href="index.php" class="logo">QUANTRO</a>

    <nav>
        <ul>
            <li><a href="index.php">Shop</a></li>
            <li><a href="myorders.php">My Orders</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<div class="orders-container">
    <div class="table-container" style="text-align: center; padding: 3rem;">
        <div class="success-icon">✓</div>
        <h1 style="color: var(--success-color); margin-bottom: 0.5rem;">Order Placed Successfully!</h1>
        <p style="color: var(--text-secondary); font-size: 1.1rem;">
            Thank you for your purchase, <?php echo htmlspecialchars($order['user_name']); ?>!
        </p>
        <p style="color: var(--text-secondary); margin-top: 1rem;">
            Order ID: <strong style="color: var(--primary-color);">#<?php echo htmlspecialchars($order_id); ?></strong>
        </p>
    </div>

    <div class="table-container" style="margin-top: 2rem;">
        <div class="table-header">
            <h2>Order Details</h2>
        </div>
        <div style="padding: 2rem;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                <div>
                    <h3 style="margin-bottom: 1rem; color: var(--text-primary);">Shipping Information</h3>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($order['user_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                </div>
                <div>
                    <h3 style="margin-bottom: 1rem; color: var(--text-primary);">Order Information</h3>
                    <p><strong>Order Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></p>
                    <p><strong>Payment Method:</strong> Cash on Delivery</p>
                    <p><strong>Status:</strong> <span class="order-status status-pending"><?php echo htmlspecialchars($order['status']); ?></span></p>
                    <p><strong>Total Amount:</strong> <span style="color: var(--success-color); font-weight: 600; font-size: 1.2rem;">$<?php echo number_format($order['total_amount'], 2); ?></span></p>
                </div>
            </div>
        </div>
    </div>

    <div class="table-container" style="margin-top: 2rem;">
        <div class="table-header">
            <h2>Order Items</h2>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = mysqli_fetch_assoc($items_result)) { ?>
                    <tr>
                        <td>
                            <div style="display: flex; gap: 1rem; align-items: center;">
                                <img src="image/<?php echo htmlspecialchars($item['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                <div>
                                    <div style="font-weight: 600;"><?php echo htmlspecialchars($item['name']); ?></div>
                                    <div style="font-size: 0.85rem; color: var(--text-secondary);">
                                        <?php echo htmlspecialchars(substr($item['description'], 0, 40)) . '...'; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td style="font-weight: 600; color: var(--success-color);">
                            $<?php echo number_format($item['subtotal'], 2); ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div style="text-align: center; margin-top: 2rem; padding: 2rem;">
        <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
            We'll send you a confirmation email shortly. You can track your order status in "My Orders" section.
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <a href="index.php" class="btn btn-primary" style="text-decoration: none;">
                Continue Shopping
            </a>
            <a href="myorders.php" class="btn btn-secondary" style="text-decoration: none;">
                View My Orders
            </a>
        </div>
    </div>
</div>

<footer class="footer">
    <p>&copy; <?php echo date('Y'); ?> Quantro. All rights reserved.</p>
</footer>

</body>
</html>