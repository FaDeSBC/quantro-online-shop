<?php 
session_start();
include "../db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: ../index.php");
    exit();
}

if($_SESSION['user_role'] != "admin"){
    echo "Go for user dashboard";
    exit();
}

$deleted = false;
$error = false;

if(isset($_GET['product_id'])){
    $product_id = $_GET['product_id'];
    $sql = "delete from products where id = '$product_id'";
    $result = mysqli_query($conn, $sql);
    
    if(!$result){
        $error = $conn->error;
    } else {
        $deleted = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Product - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles.css">
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
            --success-green: #10b981;
            --success-bg: #d1fae5;
            --success-border: #6ee7b7;
            --error-red: #ef4444;
            --error-bg: #fee2e2;
            --error-border: #fca5a5;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px rgba(30, 58, 138, 0.1);
            --shadow-lg: 0 10px 25px rgba(30, 58, 138, 0.15);
            --shadow-xl: 0 20px 40px rgba(30, 58, 138, 0.2);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, var(--off-white) 0%, #e0e7ff 100%);
            color: var(--navy-dark);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .container {
            background: var(--white);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: var(--shadow-xl);
            max-width: 600px;
            width: 100%;
            text-align: center;
            animation: slideUp 0.6s ease-out;
            position: relative;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--navy-primary), var(--navy-light), var(--navy-primary));
            border-radius: 20px 20px 0 0;
        }

        .icon-wrapper {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            animation: scaleIn 0.5s ease-out 0.2s both;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .icon-wrapper.success {
            background: var(--success-bg);
            color: var(--success-green);
            border: 3px solid var(--success-border);
        }

        .icon-wrapper.error {
            background: var(--error-bg);
            color: var(--error-red);
            border: 3px solid var(--error-border);
        }

        h1 {
            color: var(--navy-primary);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            animation: fadeIn 0.6s ease-out 0.3s both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .message {
            color: var(--gray-medium);
            font-size: 1.125rem;
            margin-bottom: 2rem;
            line-height: 1.6;
            animation: fadeIn 0.6s ease-out 0.4s both;
        }

        .message.success-text {
            color: #065f46;
        }

        .message.error-text {
            color: #991b1b;
            background: var(--error-bg);
            padding: 1rem;
            border-radius: 10px;
            border: 2px solid var(--error-border);
        }

        .button-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeIn 0.6s ease-out 0.5s both;
        }

        .button {
            padding: 1rem 2rem;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-md);
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            border: none;
        }

        .button-primary {
            background: linear-gradient(135deg, var(--navy-primary), var(--navy-secondary));
            color: var(--white);
        }

        .button-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 58, 138, 0.3);
            background: linear-gradient(135deg, var(--navy-secondary), var(--navy-light));
        }

        .button-secondary {
            background: var(--off-white);
            color: var(--navy-primary);
            border: 2px solid var(--gray-light);
        }

        .button-secondary:hover {
            background: var(--white);
            border-color: var(--navy-primary);
            transform: translateY(-2px);
        }

        .button:active {
            transform: translateY(0);
        }


        @media (max-width: 550px) {
            .container {
                padding: 2rem 1.5rem;
            }

            h1 {
                font-size: 1.5rem;
            }

            .message {
                font-size: 1rem;
            }

            .button-group {
                flex-direction: column;
            }

            .button {
                width: 100%;
                justify-content: center;
            }
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: var(--white);
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if($deleted): ?>
            <div class="icon-wrapper success">
                ✓
            </div>
            <h1>Product Deleted Successfully!</h1>
            <p class="message success-text">
                The product has been permanently removed from your inventory.
            </p>
        <?php elseif($error): ?>
            <div class="icon-wrapper error">
                ✗
            </div>
            <h1>Deletion Failed</h1>
            <p class="message error-text">
                <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
            </p>
        <?php else: ?>
            <div class="icon-wrapper error">
                ⚠
            </div>
            <h1>No Product Selected</h1>
            <p class="message">
                Please select a product to delete from the product list.
            </p>
        <?php endif; ?>

        <div class="button-group">
            <a href="displayproduct.php" class="button button-primary">
                ← Back to Products
            </a>
            <a href="addproduct.php" class="button button-secondary">
                + Add New Product
            </a>
        </div>
    </div>

    <script src="../js/alert.js"></script>
    <script>
        <?php if($deleted): ?>
            Alert.success('Product deleted successfully!');
        <?php elseif($error): ?>
            Alert.error('Failed to delete product: <?php echo addslashes($error); ?>');
        <?php endif; ?>
    </script>
</body>
</html>