<?php require_once 'config.php'; redirect_unauthorized('is_user', 'user_login.php'); ?>
<!DOCTYPE html>
<html>
<head><title>User: Guest List Management</title></head>
<body>
    <h1 style="text-align: center;">User: Guest List Management</h1>
    <p style="text-align: center;">This page allows the user to Add/Update/Delete guest entries (Flow Chart: USER -> Guest List).</p>
    <a href="user_dashboard.php">Go to Dashboard</a>
</body>
</html>