<?php
require_once 'config.php';
redirect_unauthorized('is_user', 'user_login.php'); 
$user_name = $_SESSION['user_name'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .container { background: #e0e0e0; padding: 50px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 700px; text-align: center; }
        .welcome { background-color: #4a69bd; color: white; padding: 15px; margin-bottom: 30px; border-radius: 5px; font-size: 20px; font-weight: bold; }
        .btn-group a { background-color: #4a69bd; color: white; padding: 20px 30px; margin: 15px; border-radius: 8px; text-decoration: none; display: inline-block; transition: background-color 0.3s; font-size: 16px; font-weight: bold; }
        .btn-group a:hover { background-color: #2d4373; }
        .chart-link { position: absolute; top: 20px; left: 20px; background: #4a69bd; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; }
        .logout-btn { position: absolute; bottom: 50px; right: 50%; transform: translateX(50%); background-color: #4a69bd; color: white; padding: 15px 40px; border-radius: 8px; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <a href="flowchart.php" class="chart-link">CHART</a>
    <div class="container">
        <div class="welcome">WELCOME USER: <?php echo htmlspecialchars($user_name); ?></div>
        <div class="btn-group">
            <a href="user_vendors.php">Vendor</a>
            <a href="user_cart.php">Cart</a>
            <a href="guest_list.php">Guest List</a>
            <a href="user_orders.php">Order Status</a>
        </div>
        <a href="logout.php" class="logout-btn">LogOut</a>
    </div>
</body>
</html>