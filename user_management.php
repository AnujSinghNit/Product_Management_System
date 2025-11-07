<?php
require_once 'config.php';
// Ensure only Admin can access this page
redirect_unauthorized('is_admin', 'admin_login.php');

// --- FETCH USER LIST ---
$users_sql = "
    SELECT 
        user_id, 
        name, 
        email, 
        created_at
    FROM users
    ORDER BY user_id DESC
";
$users_result = mysqli_query($conn, $users_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin: User Management</title>
    <style>
        /* Styles adapted from vendor_management.php */
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; display: flex; justify-content: center; align-items: flex-start; min-height: 100vh; }
        .container { background: #e0e0e0; padding: 40px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 90%; max-width: 1000px; margin-top: 50px; }
        .nav-links { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .nav-links a { background-color: #4a69bd; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; margin: 0 5px; }
        .user-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .user-table th, .user-table td { border: 1px solid #999; padding: 10px; text-align: left; background: white; }
        .user-table th { background: #4a69bd; color: white; }
        .chart-link { position: absolute; top: 20px; left: 20px; background: #4a69bd; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; }
    </style>
</head>
<body>
    <a href="flowchart.php" class="chart-link">CHART</a>
    <div class="container">
        <h2 style="color: #4a69bd; text-align: center;">User Management</h2>

        <div class="nav-links">
            <a href="admin_maintenance.php?type=user">Back to Maintenance Menu</a>
            <a href="logout.php">LogOut</a>
        </div>
        
        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Date Registered</th>
                    <th>Actions (View Orders)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($users_result) > 0): ?>
                    <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                        <tr>
                            <td><?php echo $user['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                            <td>
                                <a href="admin_view_orders.php?user_id=<?php echo $user['user_id']; ?>" style="background: green; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none;">View Orders</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align: center;">No users registered in the system.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
