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

$product_added = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_path = "../image/" . $image_name;
        
        if (move_uploaded_file($image_tmp, $image_path)) {
            $sql = "INSERT INTO products (name, description, price, stock, category_name, image) 
                    VALUES ('$name', '$description', '$price', '$stock', '$category', '$image_name')";
            
            if (mysqli_query($conn, $sql)) {
                $product_added = true;
            } else {
                $error_message = "Error: " . mysqli_error($conn);
            }
        } else {
            $error_message = "Failed to upload image.";
        }
    } else {
        $error_message = "Please select an image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product - Admin Dashboard</title>
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
            <h1>Add New Product</h1>
            <p style="color: var(--text-secondary); margin-top: 0.5rem;">Add a new product to your inventory</p>
        </div>
        <div class="admin-actions">
            <a href="displayproduct.php" class="btn btn-secondary">View All Products</a>
        </div>
    </div>

    <div class="table-container">
        <div class="table-header">
            <h2>Product Information</h2>
        </div>
        
        <form action="addproduct.php" method="POST" enctype="multipart/form-data" style="padding: 2rem;">
            <div style="display: grid; gap: 1.5rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-primary);">Product Name</label>
                    <input type="text" name="name" required 
                           style="width: 100%; padding: 0.75rem; border: 2px solid var(--border-color); border-radius: 8px; font-size: 1rem;">
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-primary);">Description</label>
                    <textarea name="description" required rows="4"
                              style="width: 100%; padding: 0.75rem; border: 2px solid var(--border-color); border-radius: 8px; font-size: 1rem; resize: vertical;"></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-primary);">Price ($)</label>
                        <input type="number" name="price" step="0.01" min="0" required 
                               style="width: 100%; padding: 0.75rem; border: 2px solid var(--border-color); border-radius: 8px; font-size: 1rem;">
                    </div>

                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-primary);">Stock Quantity</label>
                        <input type="number" name="stock" min="0" required 
                               style="width: 100%; padding: 0.75rem; border: 2px solid var(--border-color); border-radius: 8px; font-size: 1rem;">
                    </div>
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-primary);">Category</label>
                    <input type="text" name="category" required 
                           style="width: 100%; padding: 0.75rem; border: 2px solid var(--border-color); border-radius: 8px; font-size: 1rem;">
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-primary);">Product Image</label>
                    <input type="file" name="image" accept="image/*" required 
                           style="width: 100%; padding: 0.75rem; border: 2px solid var(--border-color); border-radius: 8px; font-size: 1rem;">
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1rem;">
                    <a href="displayproduct.php" class="btn btn-secondary" style="text-decoration: none;">
                        Cancel
                    </a>
                    <button type="submit" name="add_product" class="btn btn-primary">
                        Add Product
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<footer class="footer">
    <p>&copy; <?php echo date('Y'); ?> Quantro Admin. All rights reserved.</p>
</footer>

<script src="../js/alert.js"></script>
<script>
    <?php if ($product_added): ?>
        Alert.success('Product added successfully!', 2000);
        setTimeout(() => {
            window.location.href = 'displayproduct.php';
        }, 2000);
    <?php elseif (!empty($error_message)): ?>
        Alert.error('<?php echo addslashes($error_message); ?>');
    <?php endif; ?>
</script>

</body>
</html>