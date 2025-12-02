<?php
session_start();
include "db.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$sql = "SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.price, p.stock
        FROM cart c
        LEFT JOIN products p ON c.product_id = p.id
        WHERE c.user_id = '$user_id'";
$result = mysqli_query($conn, $sql);

$cart_items = [];
$total = 0;
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $row['item_total'] = $row['price'] * $row['quantity'];
        $total += $row['item_total'];
        $cart_items[] = $row;
    }
}
if (count($cart_items) == 0) {
    header("Location: cart.php");
    exit();
}
$order_success = false;
$order_id = '';
$order_total = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    mysqli_begin_transaction($conn);
    try {
        $order_sql = "INSERT INTO orders (user_id, total_amount, status) VALUES ('$user_id', '$total', 'Pending')";
        if (mysqli_query($conn, $order_sql)) {
            $order_id = mysqli_insert_id($conn);
            foreach ($cart_items as $item) {
                $product_id = $item['product_id'];
                $quantity = $item['quantity'];
                $subtotal = $item['item_total'];
                $order_item_sql = "INSERT INTO order_items (order_id, product_id, quantity, subtotal) 
                                   VALUES ('$order_id', '$product_id', '$quantity', '$subtotal')";
                mysqli_query($conn, $order_item_sql);
                $update_stock_sql = "UPDATE products SET stock = stock - $quantity WHERE id = '$product_id'";
                mysqli_query($conn, $update_stock_sql);
            }
            $payment_sql = "INSERT INTO payments (order_id, payment_method, status) VALUES ('$order_id', 'cashon', 'Pending')";
            mysqli_query($conn, $payment_sql);
            $clear_cart_sql = "DELETE FROM cart WHERE user_id = '$user_id'";
            mysqli_query($conn, $clear_cart_sql);
            mysqli_commit($conn);
            $order_success = true;
            $order_total = number_format($total, 2);
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error_message = "Order placement failed. Please try again.";
    }
}

$user_sql = "SELECT * FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_sql);
$user_info = mysqli_fetch_assoc($user_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Quantro Shop</title>
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
        <h1>Checkout</h1>
        <p>Review your order and complete purchase</p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
        <div class="table-container">
            <div class="table-header">
                <h2>Shipping Information</h2>
            </div>
            <div style="padding: 2rem;">
                <div style="margin-bottom: 1rem;">
                    <strong>Name:</strong> <?php echo htmlspecialchars($user_info['name']); ?>
                </div>
                <div style="margin-bottom: 1rem;">
                    <strong>Email:</strong> <?php echo htmlspecialchars($user_info['email']); ?>
                </div>
                <div style="margin-bottom: 1rem;">
                    <strong>Phone:</strong> <?php echo htmlspecialchars($user_info['phone']); ?>
                </div>
                <div>
                    <strong>Address:</strong> <?php echo htmlspecialchars($user_info['address']); ?>
                </div>
            </div>
        </div>
        <div class="table-container">
            <div class="table-header">
                <h2>Payment Method</h2>
            </div>
            <div style="padding: 2rem;">
                <div style="padding: 1rem; background: var(--light-bg); border-radius: 8px; border: 2px solid var(--primary-color);">
                    <strong>Cash on Delivery</strong>
                    <p style="color: var(--text-secondary); margin-top: 0.5rem; font-size: 0.9rem;">
                        Pay when you receive your order
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="table-container">
        <div class="table-header">
            <h2>Order Summary</h2>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item) { ?>
                    <tr>
                        <td style="font-weight: 600;"><?php echo htmlspecialchars($item['name']); ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td style="font-weight: 600; color: var(--success-color);">
                            $<?php echo number_format($item['item_total'], 2); ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        
        <div style="padding: 2rem; border-top: 2px solid var(--border-color);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2 style="font-size: 1.5rem;">
                    Total: <span style="color: var(--success-color);">$<?php echo number_format($total, 2); ?></span>
                </h2>
            </div>
            
            <form action="checkout.php" method="POST" id="checkoutForm" style="display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="cart.php" class="btn btn-secondary" style="text-decoration: none;">
                    Back to Cart
                </a>
                <button type="submit" name="place_order" class="btn btn-primary">
                    Place Order
                </button>
            </form>
        </div>
    </div>
</div>

<footer class="footer">
    <p>&copy; <?php echo date('Y'); ?> Quantro. All rights reserved.</p>
</footer>

<script src="js/alert.js"></script>
<script>
    <?php if ($order_success): ?>
        showOrderConfirmation('<?php echo $order_id; ?>', '<?php echo $order_total; ?>');
    <?php elseif (isset($error_message)): ?>
        Alert.error('<?php echo addslashes($error_message); ?>');
    <?php endif; ?>
</script>

</body>
</html>