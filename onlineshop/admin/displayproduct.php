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

$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);

if (!$result) {
    $error_message = "Error: " . mysqli_error($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products - Admin Dashboard</title>
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
            <li><a href="addproduct.php">Add Product</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<div class="admin-container">
    <div class="admin-header">
        <div class="admin-title">
            <h1>Product Management</h1>
            <p style="color: var(--text-secondary); margin-top: 0.5rem;">View and manage all products</p>
        </div>
        <div class="admin-actions">
            <a href="addproduct.php" class="btn btn-primary">Add New Product</a>
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <?php if (isset($error_message)) { ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php } ?>

    <div class="table-container">
        <div class="table-header">
            <h2>All Products</h2>
        </div>

        <?php if ($result && mysqli_num_rows($result) > 0) { ?>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td>
                                <img src="../image/<?php echo htmlspecialchars($row['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($row['name']); ?>"
                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                            </td>
                            <td style="font-weight: 600; color: var(--text-primary);">
                                <?php echo htmlspecialchars($row['name']); ?>
                            </td>
                            <td style="max-width: 200px;">
                                <?php echo htmlspecialchars(substr($row['description'], 0, 60)) . '...'; ?>
                            </td>
                            <td style="font-weight: 600; color: var(--success-color);">
                                $<?php echo number_format($row['price'], 2); ?>
                            </td>
                            <td>
                                <span class="order-status <?php echo $row['stock'] > 10 ? 'status-completed' : ($row['stock'] > 0 ? 'status-pending' : 'status-cancelled'); ?>">
                                    <?php echo htmlspecialchars($row['stock']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                    <a href="updateproduct.php?product_id=<?php echo $row['id']; ?>" 
                                       class="btn btn-primary" 
                                       style="padding: 0.5rem 1rem; font-size: 0.85rem; text-decoration: none;">
                                        Update
                                    </a>
                                    <a href="#" 
                                       class="btn btn-secondary delete-product-btn" 
                                       data-product-id="<?php echo $row['id']; ?>"
                                       data-product-name="<?php echo htmlspecialchars($row['name']); ?>"
                                       style="padding: 0.5rem 1rem; font-size: 0.85rem; text-decoration: none; background: var(--danger-color); border-color: var(--danger-color);">
                                        Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div style="padding: 3rem; text-align: center; color: var(--text-secondary);">
                <h3>No products found</h3>
                <p style="margin-top: 0.5rem;">Add your first product to get started.</p>
                <a href="addproduct.php" class="btn btn-primary" style="margin-top: 1rem; display: inline-block; text-decoration: none;">
                    Add Product
                </a>
            </div>
        <?php } ?>
    </div>
</div>

<footer class="footer">
    <p>&copy; <?php echo date('Y'); ?> Quantro Admin. All rights reserved.</p>
</footer>

<script src="../js/alert.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-product-btn');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.dataset.productId;
                const productName = this.dataset.productName;
                
                Alert.show(
                    `Are you sure you want to delete <strong>${productName}</strong>?<br>This action cannot be undone.`,
                    'warning',
                    0,
                    [
                        {
                            text: 'Cancel',
                            primary: false,
                            callback: () => {}
                        },
                        {
                            text: 'Delete',
                            primary: true,
                            callback: () => {
                                window.location.href = `deleteproduct.php?product_id=${productId}`;
                            }
                        }
                    ]
                );
            });
        });
    });
</script>

</body>
</html>