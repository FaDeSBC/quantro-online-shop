<?php
session_start();
include "../db.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != "admin") {
    header("Location: ../dashboard.php");
    exit();
}

$total_products_sql = "SELECT COUNT(*) as total FROM products";
$total_products_result = mysqli_query($conn, $total_products_sql);
$total_products = mysqli_fetch_assoc($total_products_result)['total'];

$total_orders_sql = "SELECT COUNT(*) as total FROM orders";
$total_orders_result = mysqli_query($conn, $total_orders_sql);
$total_orders = mysqli_fetch_assoc($total_orders_result)['total'];

$total_revenue_sql = "SELECT SUM(total_amount) as revenue FROM orders";
$total_revenue_result = mysqli_query($conn, $total_revenue_sql);
$total_revenue = mysqli_fetch_assoc($total_revenue_result)['revenue'] ?? 0;

$total_users_sql = "SELECT COUNT(*) as total FROM users WHERE role != 'admin'";
$total_users_result = mysqli_query($conn, $total_users_sql);
$total_users = mysqli_fetch_assoc($total_users_result)['total'];

$recent_orders_sql = "SELECT o.id, o.created_at, o.total_amount, 
                             u.name as user_name, 
                             GROUP_CONCAT(p.name SEPARATOR ', ') as product_names
                      FROM orders o 
                      LEFT JOIN users u ON o.user_id = u.id 
                      LEFT JOIN order_items oi ON o.id = oi.order_id
                      LEFT JOIN products p ON oi.product_id = p.id 
                      GROUP BY o.id
                      ORDER BY o.id DESC LIMIT 5";
$recent_orders_result = mysqli_query($conn, $recent_orders_sql);

$products_sql = "SELECT * FROM products ORDER BY id DESC LIMIT 10";
$products_result = mysqli_query($conn, $products_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Quantro</title>
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
            <li><a href="addproduct.php">Add Product</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<div class="admin-container">
    <div class="admin-header">
        <div class="admin-title">
            <h1>Admin Dashboard</h1>
            <p style="color: var(--text-secondary); margin-top: 0.5rem;">
                Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
            </p>
        </div>
        <div class="admin-actions">
            <a href="addproduct.php" class="btn btn-primary">Add New Product</a>
            <a href="../index.php" class="btn btn-secondary">View Store</a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Products</div>
            <div class="stat-value"><?php echo $total_products; ?></div>
        </div>

        <div class="stat-card" style="border-left-color: var(--success-color);">
            <div class="stat-label">Total Orders</div>
            <div class="stat-value" style="color: var(--success-color);">
                <?php echo $total_orders; ?>
            </div>
        </div>

        <div class="stat-card" style="border-left-color: var(--secondary-color);">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value" style="color: var(--secondary-color);">
                $<?php echo number_format($total_revenue, 2); ?>
            </div>
        </div>

        <div class="stat-card" style="border-left-color: var(--warning-color);">
            <div class="stat-label">Total Users</div>
            <div class="stat-value" style="color: var(--warning-color);">
                <?php echo $total_users; ?>
            </div>
        </div>
    </div>

    <div class="table-container" style="margin-bottom: 2rem;">
        <div class="table-header">
            <h2>Products Overview</h2>
            <a href="displayproduct.php" class="btn btn-primary" style="text-decoration: none;">View All Products</a>
        </div>

        <?php if ($products_result && mysqli_num_rows($products_result) > 0) { ?>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($product = mysqli_fetch_assoc($products_result)) { ?>
                        <tr>
                            <td>
                                <img src="../image/<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                            </td>
                            <td style="font-weight: 600; color: var(--text-primary);">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                            <td style="font-weight: 600; color: var(--success-color);">
                                $<?php echo number_format($product['price'], 2); ?>
                            </td>
                            <td>
                                <span class="order-status <?php echo $product['stock'] > 10 ? 'status-completed' : ($product['stock'] > 0 ? 'status-pending' : 'status-cancelled'); ?>">
                                    <?php echo htmlspecialchars($product['stock']); ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                    <a href="updateproduct.php?product_id=<?php echo $product['id']; ?>" 
                                       class="btn btn-primary" 
                                       style="padding: 0.5rem 1rem; font-size: 0.85rem; text-decoration: none;">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
                <h3>No products yet</h3>
                <p style="margin-top: 0.5rem;">Add your first product to get started.</p>
                <a href="addproduct.php" class="btn btn-primary" style="margin-top: 1rem; display: inline-block; text-decoration: none;">
                    Add Product
                </a>
            </div>
        <?php } ?>
    </div>

    <div class="table-container">
        <div class="table-header">
            <h2>Recent Orders</h2>
            <a href="vieworders.php" class="btn btn-primary" style="text-decoration: none;">View All Orders</a>
        </div>

        <?php if ($recent_orders_result && mysqli_num_rows($recent_orders_result) > 0) { ?>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Products</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = mysqli_fetch_assoc($recent_orders_result)) { ?>
                        <tr>
                            <td style="font-weight: 600; color: var(--primary-color);">
                                #<?php echo htmlspecialchars($order['id']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                            <td style="max-width: 200px;">
                                <?php echo htmlspecialchars(substr($order['product_names'], 0, 50)) . (strlen($order['product_names']) > 50 ? '...' : ''); ?>
                            </td>
                            <td style="font-weight: 600; color: var(--success-color);">
                                $<?php echo number_format($order['total_amount'], 2); ?>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
                <h3>No orders yet</h3>
                <p style="margin-top: 0.5rem;">Orders will appear here once customers start purchasing.</p>
            </div>
        <?php } ?>
    </div>
</div>

<footer class="footer">
    <p>&copy; <?php echo date('Y'); ?> Quantro Admin. All rights reserved.</p>
</footer>

</body>
</html>