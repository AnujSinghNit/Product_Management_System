<?php require_once 'config.php'; redirect_unauthorized('is_admin', 'admin_login.php'); ?>
<!DOCTYPE html>
<html>
<head><title>Admin Management</title></head>
<body>
    <h1 style="text-align: center;">Admin Management Page</h1>
    <p style="text-align: center;">This page is for Admin to fully manage user/vendor accounts.</p>
    <a href="admin_dashboard.php">Go to Dashboard</a>
</body>
</html>