<?php
require_once 'config.php';
redirect_unauthorized('is_user', 'user_login.php'); 
$user_id = $_SESSION['user_id'];

// Fetch User Orders
$orders_sql = "SELECT order_id, name, email, address, order_status FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$orders_stmt = mysqli_prepare($conn, $orders_sql);
mysqli_stmt_bind_param($orders_stmt, 'i', $user_id);
mysqli_stmt_execute($orders_stmt);
$orders_result = mysqli_stmt_get_result($orders_stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User: Order Status</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; display: flex; justify-content: center; align-items: flex-start; min-height: 100vh; }
        .container { background: #e0e0e0; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 90%; max-width: 1000px; margin-top: 50px; }
        .header-nav { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .header-nav a { background-color: #4a69bd; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; margin: 5px; }
        .order-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .order-table th, .order-table td { padding: 10px; text-align: left; border: 1px solid #ccc; background: white; }
        .order-table th { background: #4a69bd; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-nav">
            <a href="user_dashboard.php">Home</a>
            <h2 style="color: #4a69bd; margin: 0;">User Order Status</h2>
            <a href="logout.php">LogOut</a>
        </div>

        <table class="order-table">
            <thead>
                <tr>
                    <th style="width: 5%;">Order ID</th>
                    <th>Name</th>
                    <th>E-mail</th>
                    <th>Address</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($orders_result) > 0): ?>
                    <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                    <tr>
                        <td><?php echo $order['order_id']; ?></td>
                        <td><?php echo htmlspecialchars($order['name']); ?></td>
                        <td><?php echo htmlspecialchars($order['email']); ?></td>
                        <td><?php echo htmlspecialchars($order['address']); ?></td>
                        <td style="font-weight: bold; color: <?php 
                            if ($order['order_status'] == 'Received') echo 'orange';
                            elseif ($order['order_status'] == 'Ready for Shipping') echo 'blue';
                            elseif ($order['order_status'] == 'Out For Delivery') echo 'green';
                            else echo 'black'; 
                        ?>;"><?php echo htmlspecialchars($order['order_status']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">You have no past orders.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>