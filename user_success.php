<?php
require_once 'config.php';
redirect_unauthorized('is_user', 'user_login.php'); 
$total_amount = $_GET['total'] ?? '0.00';
$order_id = $_GET['order_id'] ?? 'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Success</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .popup { background: #e0e0e0; padding: 40px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 400px; text-align: center; }
        .header-title { background-color: #4a69bd; color: white; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; }
        .thank-you { font-size: 24px; color: green; margin-bottom: 15px; }
        .total-box { background: #6d9eeb; color: white; padding: 15px; border-radius: 8px; font-size: 18px; font-weight: bold; margin-bottom: 20px; }
        .continue-btn { background: #4a69bd; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="popup">
        <div class="header-title">PopUp</div>
        <div class="thank-you">THANK YOU!</div>
        <div class="total-box">Total Amount: Rs. <?php echo number_format($total_amount, 2); ?></div>
        <p>Your Order #<?php echo htmlspecialchars($order_id); ?> has been placed successfully.</p>
        <a href="user_dashboard.php" class="continue-btn">Continue Shopping</a>
    </div>
</body>
</html>