<?php
require_once 'config.php';
// Requirement 8: Admin can access maintenance, reports, and transactions.
// Flow Chart: ADMIN -> Maintanence Menu
redirect_unauthorized('is_admin', 'admin_login.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .container { background: #e0e0e0; padding: 50px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 600px; text-align: center; }
        .welcome { background-color: #f9f9f9; color: #4a69bd; padding: 15px; margin-bottom: 30px; border-radius: 5px; font-size: 20px; font-weight: bold; }
        .btn-group a { background-color: #4a69bd; color: white; padding: 20px 30px; margin: 20px; border-radius: 8px; text-decoration: none; display: inline-block; transition: background-color 0.3s; font-size: 18px; font-weight: bold; }
        .btn-group a:hover { background-color: #2d4373; }
        .nav-links { position: absolute; top: 20px; width: 95%; display: flex; justify-content: space-between; }
        .nav-links a { background-color: #4a69bd; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; }
    </style>
</head>
<body>
    <div class="nav-links">
        <a href="admin_dashboard.php">Home</a>
        <a href="logout.php">LogOut</a>
    </div>

    <div class="container">
        <div class="welcome">Welcome Admin</div>
        <div class="btn-group">
            <a href="admin_maintenance.php?type=user">Maintain User</a>
            <a href="admin_maintenance.php?type=vendor">Maintain Vendor</a>
        </div>
        <p style="margin-top: 30px; font-style: italic;">Access reports and transactions via Maintenance Menu (Requirement 8).</p>
    </div>
</body>
</html>