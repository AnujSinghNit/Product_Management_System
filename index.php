<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Management System - INDEX</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; display: flex; justify-content: center; align-items: center; min-height: 100vh; flex-direction: column; }
        .box { background: #e0e0e0; padding: 40px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); text-align: center; }
        h1 { color: #3b5998; margin-bottom: 30px; }
        .btn-group a { background-color: #3b5998; color: white; padding: 12px 20px; margin: 10px; border-radius: 8px; text-decoration: none; display: inline-block; transition: background-color 0.3s; }
        .btn-group a:hover { background-color: #2d4373; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Event Management System</h1>
        <p style="margin-bottom: 20px; font-weight: bold;">Select Your Role to Login or Sign Up:</p>
        <div class="btn-group">
            <a href="admin_login.php">Admin Login</a>
            <a href="vendor_login.php">Vendor Login</a>
            <a href="user_login.php">User Login</a>
            <a href="vendor_signup.php">Vendor Sign Up</a>
            <a href="user_signup.php">User Sign Up</a>
        </div>
    </div>
</body>
</html>