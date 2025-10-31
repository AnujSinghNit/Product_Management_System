<?php
require_once 'config.php';
// Requirement 12: Maintanence Menu (Admin access only).
redirect_unauthorized('is_admin', 'admin_login.php'); 

$type = $_GET['type'] ?? 'user'; // Defaults to user maintenance screen
$title = ($type == 'vendor') ? 'Maintain Vendor' : 'Maintain User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
    <style>
        /* Shared Styles */
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .container { background: #e0e0e0; padding: 40px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 600px; text-align: center; }
        .nav-links { position: absolute; top: 20px; width: 95%; display: flex; justify-content: space-between; }
        .nav-links a { background-color: #4a69bd; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; }
        .maintenance-block { background: #ccc; padding: 30px; border-radius: 10px; margin-top: 20px; }
        .menu-group { margin-bottom: 20px; display: flex; align-items: center; justify-content: center; }
        .menu-label { background: #f9f9f9; color: #4a69bd; padding: 10px 20px; border-radius: 8px; font-weight: bold; width: 180px; margin-right: 15px; }
        .action-buttons a { background-color: #4a69bd; color: white; padding: 10px 15px; margin: 5px; border-radius: 8px; text-decoration: none; display: inline-block; }
        .action-buttons a:hover { background-color: #2d4373; }
        .chart-link { position: absolute; top: 20px; left: 20px; background: #4a69bd; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; }
    </style>
</head>
<body>
    <div class="nav-links">
        <a href="admin_dashboard.php">Home</a>
        <a href="logout.php">LogOut</a>
    </div>

    <div class="container">
        <div class="maintenance-block">
            <h2 style="color: #4a69bd; margin-bottom: 30px;"><?php echo $title; ?> Menu</h2>

            <?php if ($type == 'vendor'): ?>
            <div class="menu-group">
                <div class="menu-label">Membership</div>
                <div class="action-buttons">
                    <a href="membership_management.php?action=add">Add</a>
                    <a href="membership_management.php?action=update">Update</a>
                </div>
            </div>
            <?php endif; ?>

            <div class="menu-group">
                <div class="menu-label"><?php echo ($type == 'vendor') ? 'Vendor Management' : 'User Management'; ?></div>
                <div class="action-buttons">
                    <a href="<?php echo ($type == 'vendor') ? 'vendor_management.php?action=add' : 'user_management.php?action=add'; ?>">Add</a>
                    <a href="<?php echo ($type == 'vendor') ? 'vendor_management.php?action=update' : 'user_management.php?action=update'; ?>">Update</a>
                </div>
            </div>
            
            <p style="margin-top: 30px; color: #555;">(Reports and Transactions access included here per Requirement 8, but require separate views.)</p>
        </div>
    </div>
    <a href="flowchart.php" class="chart-link">CHART</a>
</body>
</html>