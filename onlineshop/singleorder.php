<?php

include 'db.php';
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $role = "user";

    $sql = "insert into users (name, 
    email, password, phone, address, role) 
    values ('$name', '$email', 
    '$password', '$phone', '$address', 
    '$role')";
    $result = mysqli_query($conn,$sql);
    if(!$result){
        echo "Error!: {$conn->error}";
    }
    else {
        echo "Registration Successful!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Create Account</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
            padding: 20px;
        }

        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            top: -250px;
            right: -250px;
            animation: float 25s infinite ease-in-out;
        }

        body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(30, 58, 138, 0.2) 0%, transparent 70%);
            border-radius: 50%;
            bottom: -200px;
            left: -200px;
            animation: float 20s infinite ease-in-out reverse;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            33% {
                transform: translate(80px, -80px) scale(1.1);
            }
            66% {
                transform: translate(-60px, 80px) scale(0.95);
            }
        }

        .container {
            display: flex;
            max-width: 1100px;
            width: 100%;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            z-index: 10;
            animation: slideIn 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .brand-section {
            flex: 1;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .brand-section::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            top: -100px;
            left: -100px;
            animation: pulse 8s infinite ease-in-out;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 0.5;
            }
            50% {
                transform: scale(1.2);
                opacity: 0.8;
            }
        }

        .brand-content {
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .brand-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 40px;
            animation: fadeInScale 0.6s ease-out 0.2s both;
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .brand-section h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 16px;
            line-height: 1.2;
            animation: fadeInUp 0.6s ease-out 0.3s both;
        }

        .brand-section p {
            font-size: 16px;
            opacity: 0.9;
            line-height: 1.6;
            max-width: 320px;
            animation: fadeInUp 0.6s ease-out 0.4s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .register-section {
            flex: 1;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-height: 90vh;
            overflow-y: auto;
        }

        .register-header {
            margin-bottom: 30px;
            animation: fadeIn 0.6s ease-out 0.2s both;
        }

        .register-header h2 {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .register-header p {
            color: #64748b;
            font-size: 15px;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .message {
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
            animation: slideDown 0.4s ease-out;
        }

        .message.success {
            background: #d1fae5;
            color: #065f46;
            border: 2px solid #10b981;
        }

        .message.error {
            background: #fee2e2;
            color: #991b1b;
            border: 2px solid #ef4444;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .register-form {
            animation: fadeIn 0.6s ease-out 0.3s both;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #334155;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper input,
        .input-wrapper textarea {
            width: 100%;
            padding: 14px 16px;
            font-size: 15px;
            font-family: inherit;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            outline: none;
            background: #ffffff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: #1e293b;
        }

        .input-wrapper textarea {
            min-height: 100px;
            resize: vertical;
            line-height: 1.6;
        }

        .input-wrapper input::placeholder,
        .input-wrapper textarea::placeholder {
            color: #94a3b8;
        }

        .input-wrapper input:focus,
        .input-wrapper textarea:focus {
            border-color: #3b82f6;
            background: #f8fafc;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 12px;
            padding: 16px;
            font-size: 16px;
            font-weight: 600;
            font-family: inherit;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.3);
            margin-top: 10px;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 58, 138, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .login-link {
            text-align: center;
            font-size: 14px;
            color: #64748b;
            margin-top: 24px;
        }

        .login-link a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .login-link a:hover {
            color: #1e3a8a;
        }

        @media (max-width: 968px) {
            .brand-section {
                display: none;
            }
        }

        @media (max-width: 640px) {
            .container {
                border-radius: 16px;
            }

            .register-section {
                padding: 40px 30px;
            }

            .register-header h2 {
                font-size: 24px;
            }

            .form-group {
                margin-bottom: 16px;
            }
        }


    </style>
</head>

<body>
    <div class="container">
        <div class="brand-section">
            <div class="brand-content">
                <div class="brand-icon">
                    ✨
                </div>
                <h1>Join Our Community</h1>
                <p>Create an account to unlock exclusive features and start your journey with us today</p>
            </div>
        </div>

        <div class="register-section">
            <div class="register-header">
                <h2>Create Account</h2>
                <p>Fill in your details to get started</p>
            </div>

            <?php if(isset($_POST['submit'])): ?>
                <?php if($result): ?>
                    <div class="message success">
                        ✓ Registration Successful! You can now <a href="login.php" style="color: #065f46; text-decoration: underline;">login here</a>.
                    </div>
                <?php else: ?>
                    <div class="message error">
                        ✗ Error: <?php echo htmlspecialchars($conn->error); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <form action="register.php" method="post" class="register-form">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <div class="input-wrapper">
                        <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" placeholder="Create a password" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <div class="input-wrapper">
                        <input type="text" id="phone" name="phone" placeholder="Enter your phone number" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <div class="input-wrapper">
                        <textarea id="address" name="address" placeholder="Enter your address" required></textarea>
                    </div>
                </div>

                <button type="submit" name="submit" class="submit-btn">
                    Create Account
                </button>

                <div class="login-link">
                    Already have an account? <a href="login.php">Login here</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>