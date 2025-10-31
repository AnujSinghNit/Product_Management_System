<?php
require_once 'config.php';
redirect_unauthorized('is_user', 'user_login.php'); 

$vendor_id = $_GET['vendor_id'] ?? 0;
$vendor_name = 'Unknown Vendor';

// Fetch Vendor Name
$vendor_sql = "SELECT name FROM vendors WHERE vendor_id = ?";
$stmt = mysqli_prepare($conn, $vendor_sql);
mysqli_stmt_bind_param($stmt, 'i', $vendor_id);
mysqli_stmt_execute($stmt);
$vendor_result = mysqli_stmt_get_result($stmt);
if ($vendor_row = mysqli_fetch_assoc($vendor_result)) {
    $vendor_name = $vendor_row['name'];
}

// Fetch Products
$products_sql = "SELECT product_id, product_name, product_price, product_image FROM products WHERE vendor_id = ? AND status = 'Active'";
$products_stmt = mysqli_prepare($conn, $products_sql);
mysqli_stmt_bind_param($products_stmt, 'i', $vendor_id);
mysqli_stmt_execute($products_stmt);
$products_result = mysqli_stmt_get_result($products_stmt);

// Handle Add to Cart Action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = clean_input($_POST['product_id']);
    $quantity = 1; // Default quantity

    // Check if item is already in cart, if so, update quantity, otherwise insert
    $cart_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE quantity = quantity + 1";
    $cart_stmt = mysqli_prepare($conn, $cart_sql);
    mysqli_stmt_bind_param($cart_stmt, 'iii', $user_id, $product_id, $quantity);
    
    if (mysqli_stmt_execute($cart_stmt)) {
        header("Location: user_cart.php"); // Redirect to cart after adding
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User: Products from <?php echo htmlspecialchars($vendor_name); ?></title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .container { background: #e0e0e0; padding: 40px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 90%; max-width: 1000px; text-align: center; }
        .header { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .nav-link { background-color: #4a69bd; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; margin: 5px; }
        .vendor-name-bar { background: #6d9eeb; color: white; padding: 15px; margin-bottom: 30px; border-radius: 5px; font-size: 20px; font-weight: bold; }
        .product-grid { display: flex; flex-wrap: wrap; justify-content: center; gap: 30px; }
        .product-card { background: #4a69bd; color: white; padding: 20px; border-radius: 15px; width: 200px; }
        .product-card h3 { margin: 5px 0; }
        .product-card p { font-size: 1.2em; font-weight: bold; margin: 5px 0 15px 0; }
        .add-to-cart-btn { background: white; color: #4a69bd; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; cursor: pointer; border: none; }
        .product-image { max-width: 100%; height: auto; border-radius: 5px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="user_dashboard.php" class="nav-link">Home</a>
            <a href="logout.php" class="nav-link">LogOut</a>
        </div>
        <div class="vendor-name-bar">Vendor Name: <?php echo htmlspecialchars($vendor_name); ?></div>
        
        <div class="product-grid">
            <?php if (mysqli_num_rows($products_result) > 0): ?>
                <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
                    <div class="product-card">
                        <img src="uploads/<?php echo htmlspecialchars($product['product_image']); ?>" alt="Product Image" class="product-image">
                        <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        <p>Price: Rs. <?php echo number_format($product['product_price'], 2); ?></p>
                        <form method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                            <button type="submit" name="add_to_cart" class="add-to-cart-btn">Add to Cart</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>This vendor has no active products.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>