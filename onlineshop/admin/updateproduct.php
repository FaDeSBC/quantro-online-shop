<?php
session_start();
include "../db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SESSION['user_role'] != "admin") {
    echo "Go for user dashboard";
    exit();
}

$sql1 = "SELECT * FROM categories";
$result1 = mysqli_query($conn, $sql1);

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    $sql2 = "SELECT * FROM products WHERE id = '$product_id'";
    $result2 = mysqli_query($conn, $sql2);
    $row2 = mysqli_fetch_assoc($result2);
} else {
    echo "No product selected!";
    exit();
}

if (isset($_POST['submit'])) {

    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category_name = $_POST['category_name'];

    $sql3 = "UPDATE products SET 
        name='$name',
        description='$description',
        price='$price',
        stock='$stock',
        category_name='$category_name'
        WHERE id = '$product_id'";

    $result3 = mysqli_query($conn, $sql3);

    if (!$result3) {
        echo "Error!: {$conn->error}";
        exit();
    }

    if (!empty($_FILES['image']['name'])) {

        $image = $_FILES['image']['name'];
        $temp_location = $_FILES['image']['tmp_name'];
        $upload_location = "../image/" . $image;

        $sql4 = "UPDATE products SET image='$image' WHERE id='$product_id'";
        $result4 = mysqli_query($conn, $sql4);

        if ($result4) {
            move_uploaded_file($temp_location, $upload_location);
        } else {
            echo "Image Error!: {$conn->error}";
            exit();
        }
    }

    header("Location: displayproduct.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --navy-primary: #1e3a8a;
            --navy-secondary: #1e40af;
            --navy-dark: #0f172a;
            --navy-light: #3b82f6;
            --white: #ffffff;
            --off-white: #f8fafc;
            --gray-light: #e2e8f0;
            --gray-medium: #94a3b8;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px rgba(30, 58, 138, 0.1);
            --shadow-lg: 0 10px 25px rgba(30, 58, 138, 0.15);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, var(--off-white) 0%, #e0e7ff 100%);
            color: var(--navy-dark);
            overflow-x: hidden;
            min-height: 100vh;
        }

        .dashboard_sidebar {
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(180deg, var(--navy-primary) 0%, var(--navy-dark) 100%);
            height: 100vh;
            width: 260px;
            padding: 2rem 0;
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            overflow-y: auto;
        }

        .dashboard_sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--navy-light), var(--white));
        }

        .sidebar-header {
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1.5rem;
        }

        .sidebar-header h2 {
            color: var(--white);
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .sidebar-header p {
            color: var(--gray-medium);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .dashboard_sidebar ul {
            list-style: none;
            padding: 0;
        }

        .dashboard_sidebar ul li {
            margin: 0.5rem 1rem;
        }

        .dashboard_sidebar ul li a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--white);
            padding: 0.875rem 1.25rem;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .dashboard_sidebar ul li a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--white);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .dashboard_sidebar ul li a:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(4px);
            color: var(--white);
        }

        .dashboard_sidebar ul li a:hover::before {
            transform: scaleY(1);
        }

        .dashboard_main {
            margin-left: 260px;
            padding: 2.5rem;
            min-height: 100vh;
        }

        .page-header {
            background: var(--white);
            padding: 2rem;
            border-radius: 16px;
            box-shadow: var(--shadow-md);
            margin-bottom: 2rem;
            border-left: 4px solid var(--navy-primary);
        }

        .page-header h1 {
            color: var(--navy-primary);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: var(--gray-medium);
            font-size: 1rem;
        }

        .form-container {
            background: var(--white);
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: var(--shadow-md);
            max-width: 800px;
        }

        .form-group {
            margin-bottom: 1.75rem;
        }

        .form-group label {
            display: block;
            color: var(--navy-dark);
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.625rem;
            letter-spacing: 0.3px;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.875rem 1.125rem;
            border: 2px solid var(--gray-light);
            border-radius: 10px;
            font-size: 1rem;
            font-family: inherit;
            color: var(--navy-dark);
            background: var(--off-white);
            transition: all 0.3s ease;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="number"]:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--navy-primary);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
            line-height: 1.6;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            left: -9999px;
        }

        .file-input-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: var(--off-white);
            border: 2px dashed var(--gray-light);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: var(--gray-medium);
            font-weight: 500;
        }

        .file-input-label:hover {
            border-color: var(--navy-primary);
            background: rgba(30, 58, 138, 0.05);
            color: var(--navy-primary);
        }

        .image-preview {
            margin-top: 1rem;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            max-width: 300px;
        }

        .image-preview img {
            width: 100%;
            height: auto;
            display: block;
            border: 3px solid var(--gray-light);
            border-radius: 12px;
        }

        .category-info {
            background: linear-gradient(135deg, var(--navy-primary), var(--navy-secondary));
            color: var(--white);
            padding: 1rem 1.25rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            font-weight: 500;
            box-shadow: var(--shadow-sm);
        }

        .category-info h3 {
            font-size: 0.95rem;
            font-weight: 600;
            margin: 0;
        }

        .button {
            background: linear-gradient(135deg, var(--navy-primary), var(--navy-secondary));
            color: var(--white);
            border: none;
            cursor: pointer;
            padding: 1rem 2.5rem;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-md);
            letter-spacing: 0.5px;
            margin-top: 1.5rem;
            display: inline-block;
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 58, 138, 0.3);
            background: linear-gradient(135deg, var(--navy-secondary), var(--navy-light));
        }

        .button:active {
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .dashboard_sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .dashboard_main {
                margin-left: 0;
                padding: 1.5rem;
            }

            .form-container {
                padding: 1.5rem;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }
        }

        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--off-white);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--navy-primary);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--navy-secondary);
        }
    </style>
</head>
<body>
    <div class="dashboard_sidebar">
        <div class="sidebar-header">
            <h2>Admin Panel</h2>
            <p>Product Management</p>
        </div>
        <ul>
            <li><a href="addproduct.php">➕ Add Product</a></li>
            <li><a href="displayproduct.php">📦 View Products</a></li>
            <li><a href="../logout.php">🚪 Logout</a></li>
        </ul>
    </div>
    
    <div class="dashboard_main">
        <div class="page-header">
            <h1>Update Product</h1>
            <p>Modify product details and update inventory information</p>
        </div>

        <div class="form-container">
            <form action="updateproduct.php?product_id=<?php echo $product_id; ?>" method="post" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($row2['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Product Description</label>
                    <textarea id="description" name="description" required><?php echo htmlspecialchars($row2['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="price">Price ($)</label>
                    <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($row2['price']); ?>" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="stock">Stock Quantity</label>
                    <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($row2['stock']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Current Product Image</label>
                    <div class="image-preview">
                        <img src="../image/<?php echo htmlspecialchars($row2['image']); ?>" alt="Product Image">
                    </div>
                </div>

                <div class="form-group">
                    <label for="image">Update Product Image (Optional)</label>
                    <div class="file-input-wrapper">
                        <input type="file" id="image" name="image" accept="image/*">
                        <label for="image" class="file-input-label">
                            📁 Choose New Image
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="category-info">
                        <h3>Current Category: <?php echo htmlspecialchars($row2['category_name']); ?></h3>
                    </div>
                    <label for="category_name">Update Category</label>
                    <select id="category_name" name="category_name" required>
                        <?php while ($row = mysqli_fetch_assoc($result1)) { ?>
                            <option value="<?php echo htmlspecialchars($row['name']); ?>" 
                                <?php echo ($row['name'] == $row2['category_name']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <button class="button" type="submit" name="submit">
                    ✓ Update Product
                </button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('image').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                const label = document.querySelector('.file-input-label');
                label.textContent = '✓ ' + fileName;
                label.style.color = 'var(--navy-primary)';
                label.style.borderColor = 'var(--navy-primary)';
            }
        });
    </script>
</body>
</html>