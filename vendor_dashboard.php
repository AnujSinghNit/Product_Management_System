<?php
require_once 'config.php';
redirect_unauthorized('is_vendor', 'vendor_login.php'); 
$vendor_name = $_SESSION['vendor_name'] ?? 'Vendor';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendor Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #4a69bd; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .container { background: #3b5998; padding: 50px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.5); width: 700px; text-align: center; }
        .welcome { background-color: white; color: #3b5998; padding: 15px; margin-bottom: 30px; border-radius: 5px; font-size: 20px; font-weight: bold; }
        .btn-group a { background-color: #6d9eeb; color: white; padding: 15px 30px; margin: 10px; border-radius: 8px; text-decoration: none; display: inline-block; transition: background-color 0.3s; font-size: 16px; font-weight: bold; }
        .btn-group a:hover { background-color: #4a69bd; }
        .chart-link { position: absolute; top: 20px; left: 20px; background: #6d9eeb; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; }
        .logout-link { position: absolute; top: 20px; right: 20px; background: #6d9eeb; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; }
    </style>
</head>
<body>
    <a href="flowchart.php" class="chart-link">CHART</a>
    <a href="logout.php" class="logout-link">LogOut</a>
    
    <div class="container">
        <div class="welcome">Welcome <?php echo htmlspecialchars($vendor_name); ?></div>
        <div class="btn-group">
            <a href="vendor_products.php">Your Item</a>
            <a href="add_product.php">Add New Item</a>
            <a href="vendor_transactions.php">Transaction</a>
        </div>
    </div>
</body>
</html>