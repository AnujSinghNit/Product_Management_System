<?php
require_once 'config.php';
redirect_unauthorized('is_user', 'user_login.php'); 
// Get vendor category from URL parameter (simulating the dropdown menu on the dashboard)
$category = clean_input($_GET['cat'] ?? 'Florist'); 

$vendors_sql = "SELECT vendor_id, name FROM vendors WHERE category = ?";
$stmt = mysqli_prepare($conn, $vendors_sql);
mysqli_stmt_bind_param($stmt, 's', $category);
mysqli_stmt_execute($stmt);
$vendors_result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User: Browse Vendors</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .container { background: #e0e0e0; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 90%; max-width: 1000px; text-align: center; }
        .header { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .nav-link { background-color: #4a69bd; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; margin: 5px; }
        .category-bar { background: #6d9eeb; color: white; padding: 15px; margin-bottom: 30px; border-radius: 5px; font-size: 18px; font-weight: bold; }
        .vendor-grid { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; }
        .vendor-card { background: #4a69bd; color: white; padding: 20px; border-radius: 15px; width: 200px; }
        .vendor-card h3 { margin: 0 0 10px 0; }
        .shop-btn { background: white; color: #4a69bd; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; display: block; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="user_dashboard.php" class="nav-link">Home</a>
            <a href="logout.php" class="nav-link">LogOut</a>
        </div>
        <div class="category-bar">Vendor - <?php echo htmlspecialchars($category); ?></div>
        
        <div class="vendor-grid">
            <?php if (mysqli_num_rows($vendors_result) > 0): ?>
                <?php while ($vendor = mysqli_fetch_assoc($vendors_result)): ?>
                    <div class="vendor-card">
                        <h3><?php echo htmlspecialchars($vendor['name']); ?></h3>
                        <p>Contact Details</p>
                        <a href="user_products.php?vendor_id=<?php echo $vendor['vendor_id']; ?>" class="shop-btn">Shop Item</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No vendors found in this category.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>