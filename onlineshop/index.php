<?php
session_start();
include "db.php";
$search_query = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
    $sql = "SELECT * FROM products WHERE name LIKE '%$search_query%' OR description LIKE '%$search_query%' OR category_name LIKE '%$search_query%'";
} else {
    $sql = "SELECT * FROM products";
}
$result = mysqli_query($conn, $sql);
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $cart_count_sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = '$user_id'";
    $cart_count_result = mysqli_query($conn, $cart_count_sql);
    if ($cart_count_result) {
        $cart_count_row = mysqli_fetch_assoc($cart_count_result);
        $cart_count = $cart_count_row['total'] ?? 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quantro Shop - Modern Online Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
</head>

<body>

<header class="header">
    <a href="index.php" class="logo">QUANTRO</a>

    <div class="search-container">
        <form action="index.php" method="GET" style="display: flex; gap: 0.5rem; align-items: center;">
            <input type="text" 
                   name="search" 
                   placeholder="Search products..." 
                   value="<?php echo htmlspecialchars($search_query); ?>"
                   style="padding: 0.75rem 1rem; border: 2px solid var(--border-color); border-radius: 8px; font-size: 1rem; width: 300px; transition: var(--transition);">
            <button type="submit" 
                    style="padding: 0.75rem 1.5rem; background: var(--primary-color); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: var(--transition);">
                Search
            </button>
            <?php if (!empty($search_query)) { ?>
                <a href="index.php" 
                   style="padding: 0.75rem 1rem; background: var(--secondary-color); color: white; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    Clear
                </a>
            <?php } ?>
        </form>
    </div>

    <nav>
        <ul>
            <?php if (!isset($_SESSION['user_id'])) { ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Sign Up</a></li>
            <?php } ?>

            <?php if (isset($_SESSION['user_id'])) { ?>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') { ?>
                    <li><a href="admin/dashboard.php">Admin Dashboard</a></li>
                <?php } else { ?>
                    <li><a href="cart.php">Cart (<?php echo $cart_count; ?>)</a></li>
                    <li><a href="myorders.php">My Orders</a></li>
                <?php } ?>
                <li><a href="logout.php">Logout</a></li>
            <?php } ?>
        </ul>
    </nav>
</header>

<main class="main">

<?php
if (!empty($search_query)) {
    echo '<div style="padding: 1rem 2rem; background: var(--light-bg); border-radius: 8px; margin-bottom: 2rem;">';
    echo '<p style="color: var(--text-secondary);">Search results for: <strong style="color: var(--primary-color);">' . htmlspecialchars($search_query) . '</strong></p>';
    echo '</div>';
}

if ($result && mysqli_num_rows($result) > 0) {
    while ($row_product = mysqli_fetch_assoc($result)) { ?>
        <div class="product">
            <img src="image/<?php echo htmlspecialchars($row_product['image']); ?>" alt="<?php echo htmlspecialchars($row_product['name']); ?>">
            <h2><?php echo htmlspecialchars($row_product['name']); ?></h2>
            <p><?php echo htmlspecialchars($row_product['description']); ?></p>
            <p class="stock">Stock: <?php echo htmlspecialchars($row_product['stock']); ?></p>
            <p class="productprice">$<?php echo number_format($row_product['price'], 2); ?></p>
            
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] != 'admin') { ?>
                <form action="cart.php" method="POST" class="add-to-cart-form" onsubmit="return handleAddToCart(event, '<?php echo htmlspecialchars($row_product['name']); ?>');">
                    <input type="hidden" name="product_id" value="<?php echo $row_product['id']; ?>">
                    <input type="hidden" name="action" value="add">
                    <button type="submit" class="add-to-cart-btn">
                        <span class="cart-icon">🛒</span>
                        <span class="btn-text">Add to Cart</span>
                    </button>
                </form>
            <?php } else if (!isset($_SESSION['user_id'])) { ?>
                <a href="login.php" class="login-to-purchase">Login to Purchase</a>
            <?php } ?>
        </div>
<?php }
} else {
    echo '<div style="text-align: center; padding: 3rem; color: var(--text-secondary);">';
    echo '<h2>No products found</h2>';
    if (!empty($search_query)) {
        echo '<p>Try searching with different keywords</p>';
    }
    echo '</div>';
}
?>

</main>

<footer class="footer">
    <p>&copy; <?php echo date('Y'); ?> Quantro. All rights reserved.</p>
</footer>

<script src="js/alert.js"></script>
<script>
    function handleAddToCart(event, productName) {
        event.preventDefault();
        const form = event.target;
        
        Alert.info('Adding to cart...', 1000);
        
        setTimeout(() => {
            form.submit();
        }, 500);
        
        return false;
    }
</script>

</body>
</html>