<?php
session_start();
include "db.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$action_message = '';
$action_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action == 'add' && isset($_POST['product_id'])) {
            $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
            $check_sql = "SELECT * FROM cart WHERE user_id = '$user_id' AND product_id = '$product_id'";
            $check_result = mysqli_query($conn, $check_sql);
            
            if (mysqli_num_rows($check_result) > 0) {
                $update_sql = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = '$user_id' AND product_id = '$product_id'";
                mysqli_query($conn, $update_sql);
            } else {
                $price_sql = "SELECT price FROM products WHERE id = '$product_id'";
                $price_result = mysqli_query($conn, $price_sql);
                $price_row = mysqli_fetch_assoc($price_result);
                $price = $price_row['price'];
            
                $insert_sql = "INSERT INTO cart (user_id, product_id, quantity, subtotal) VALUES ('$user_id', '$product_id', 1, '$price')";
                mysqli_query($conn, $insert_sql);
            }
            
            $action_message = 'Item added to cart successfully!';
            $action_type = 'success';
        }
        
        if ($action == 'update' && isset($_POST['cart_id']) && isset($_POST['quantity'])) {
            $cart_id = mysqli_real_escape_string($conn, $_POST['cart_id']);
            $quantity = max(1, intval($_POST['quantity']));
            
            $update_sql = "UPDATE cart SET quantity = '$quantity' WHERE id = '$cart_id' AND user_id = '$user_id'";
            mysqli_query($conn, $update_sql);
            
            $action_message = 'Cart updated successfully!';
            $action_type = 'success';
        }
        
        if ($action == 'remove' && isset($_POST['cart_id'])) {
            $cart_id = mysqli_real_escape_string($conn, $_POST['cart_id']);
            $delete_sql = "DELETE FROM cart WHERE id = '$cart_id' AND user_id = '$user_id'";
            mysqli_query($conn, $delete_sql);
            
            $action_message = 'Item removed from cart!';
            $action_type = 'info';
        }
    }
}
$sql = "SELECT c.id as cart_id, c.quantity, c.subtotal, p.id as product_id, p.name, p.description, p.price, p.image, p.stock
        FROM cart c
        LEFT JOIN products p ON c.product_id = p.id
        WHERE c.user_id = '$user_id'";
$result = mysqli_query($conn, $sql);
$total = 0;
$cart_items = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $row['item_total'] = $row['price'] * $row['quantity'];
        $total += $row['item_total'];
        $cart_items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart - Quantro Shop</title>
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
        <h1>Shopping Cart</h1>
        <p>Review your items before checkout</p>
    </div>

    <?php if (count($cart_items) > 0) { ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item) { ?>
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
                            <td style="font-weight: 600;">$<?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <form action="cart.php" method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                    <input type="number" 
                                           name="quantity" 
                                           value="<?php echo $item['quantity']; ?>" 
                                           min="1" 
                                           max="<?php echo $item['stock']; ?>"
                                           style="width: 60px; padding: 0.5rem; border: 2px solid var(--border-color); border-radius: 4px;">
                                    <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                                        Update
                                    </button>
                                </form>
                            </td>
                            <td style="font-weight: 600; color: var(--success-color);">
                                $<?php echo number_format($item['item_total'], 2); ?>
                            </td>
                            <td>
                                <form action="cart.php" method="POST" onsubmit="return confirmDelete('item', this);">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                    <button type="submit" 
                                            class="btn btn-secondary" 
                                            style="padding: 0.5rem 1rem; font-size: 0.85rem; background: var(--danger-color); border-color: var(--danger-color);">
                                        Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            
            <div style="padding: 2rem; border-top: 2px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="font-size: 1.5rem; color: var(--text-primary);">
                        Total: <span style="color: var(--success-color);">$<?php echo number_format($total, 2); ?></span>
                    </h2>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <a href="index.php" class="btn btn-secondary" style="text-decoration: none;">
                        Continue Shopping
                    </a>
                    <a href="checkout.php" class="btn btn-primary" style="text-decoration: none;">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="no-orders">
            <h2>Your cart is empty</h2>
            <p style="color: var(--text-secondary); margin-top: 0.5rem;">Add some products to get started!</p>
            <a href="index.php">Browse Products</a>
        </div>
    <?php } ?>
</div>

<footer class="footer">
    <p>&copy; <?php echo date('Y'); ?> Quantro. All rights reserved.</p>
</footer>

<script src="js/alert.js"></script>
<script>
    <?php if (!empty($action_message)): ?>
        Alert.<?php echo $action_type; ?>('<?php echo addslashes($action_message); ?>');
    <?php endif; ?>

    function confirmDelete(itemType, form) {
        Alert.show(
            `Are you sure you want to remove this ${itemType} from cart?`,
            'warning',
            0,
            [
                {
                    text: 'Cancel',
                    primary: false,
                    callback: () => {}
                },
                {
                    text: 'OK',
                    primary: true,
                    callback: () => {
                        form.submit();
                    }
                }
            ]
        );
        return false;
    }
</script>

</body>
</html>