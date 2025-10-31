<?php
require_once 'config.php';
redirect_unauthorized('is_vendor', 'vendor_login.php'); 
$vendor_id = $_SESSION['vendor_id'];
$vendor_name = $_SESSION['vendor_name'];

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $product_name = clean_input($_POST['product_name']);
    $product_price = clean_input($_POST['product_price']);
    $image_name = '';

    // Handle Image Upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $target_dir = "uploads/";
        $file_extension = pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION);
        $image_name = $vendor_id . '_' . time() . '.' . $file_extension;
        $target_file = $target_dir . $image_name;
        
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            // File uploaded successfully
        } else {
            $error = "Sorry, there was an error uploading your file.";
        }
    }

    if (empty($error)) {
        $sql = "INSERT INTO products (vendor_id, product_name, product_price, product_image) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'isds', $vendor_id, $product_name, $product_price, $image_name);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = "Product added successfully!";
        } else {
            $error = "Database error: Could not add product.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendor: Add New Item</title>
    <style>
        /* Styles */
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .container { background: #e0e0e0; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 90%; max-width: 1200px; }
        .header-nav { display: flex; justify-content: space-between; background: #4a69bd; padding: 10px; border-radius: 8px 8px 0 0; }
        .header-nav a { background-color: #6d9eeb; color: white; padding: 8px 15px; margin: 0 5px; border-radius: 5px; text-decoration: none; }
        .main-content { display: flex; margin-top: 10px; }
        .add-product-panel { background: #4a69bd; padding: 30px; border-radius: 8px; width: 35%; margin-right: 20px; }
        .form-row { margin-bottom: 20px; }
        .form-row input { width: 100%; padding: 12px; border: none; border-radius: 8px; margin-top: 5px; background: white; }
        .form-row input[type="file"] { padding: 10px 0; background: none; color: white; }
        .add-btn { background: #6d9eeb; color: white; padding: 12px 30px; border-radius: 8px; cursor: pointer; font-weight: bold; margin-top: 20px; }
        
        .product-list-panel { flex-grow: 1; background: #ccc; padding: 20px; border-radius: 8px; }
        .product-table { width: 100%; border-collapse: collapse; }
        .product-table th, .product-table td { border: 1px solid #999; padding: 10px; text-align: center; background: white; }
        .product-table th { background: #4a69bd; color: white; }
        .action-btn { background: orange; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; margin: 2px; }
        .delete-btn { background: red; }
        .update-btn { background: #008cba; }
        .product-image { max-width: 50px; max-height: 50px; }
        .message { color: green; margin-bottom: 15px; font-weight: bold; }
        .error { color: #e74c3c; margin-bottom: 15px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-nav">
            <span style="color: white; font-weight: bold;">Welcome '<?php echo htmlspecialchars($vendor_name); ?>'</span>
            <div>
                <a href="product_status.php">Product Status</a>
                <a href="request_item.php">Request Item</a>
                <a href="vendor_products.php">View Product</a>
                <a href="logout.php">Log Out</a>
            </div>
        </div>
        
        <?php if ($message): ?><div class="message"><?php echo $message; ?></div><?php endif; ?>
        <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>

        <div class="main-content">
            <div class="add-product-panel">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-row">
                        <input type="text" name="product_name" placeholder="Product Name" required>
                    </div>
                    <div class="form-row">
                        <input type="number" name="product_price" placeholder="Product Price" required step="0.01">
                    </div>
                    <div class="form-row">
                        <input type="file" name="product_image" required>
                    </div>
                    <button type="submit" name="add_product" class="add-btn">Add The Product</button>
                </form>
            </div>

            <div class="product-list-panel">
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Product Image</th>
                            <th>Product Name</th>
                            <th>Product Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $products_sql = "SELECT product_id, product_name, product_price, product_image FROM products WHERE vendor_id = ?";
                        $products_stmt = mysqli_prepare($conn, $products_sql);
                        mysqli_stmt_bind_param($products_stmt, 'i', $vendor_id);
                        mysqli_stmt_execute($products_stmt);
                        $products_result = mysqli_stmt_get_result($products_stmt);
                        
                        if (mysqli_num_rows($products_result) > 0):
                            while ($product = mysqli_fetch_assoc($products_result)):
                        ?>
                        <tr>
                            <td>
                                <img src="uploads/<?php echo htmlspecialchars($product['product_image']); ?>" alt="Image" class="product-image">
                            </td>
                            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                            <td>Rs/- <?php echo number_format($product['product_price'], 2); ?></td>
                            <td>
                                <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="action-btn update-btn">Update</a>
                                <a href="delete_product.php?id=<?php echo $product['product_id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                            </td>
                        </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <tr><td colspan="4">No products added yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>