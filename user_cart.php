<?php
require_once 'config.php';
redirect_unauthorized('is_user', 'user_login.php'); 
$user_id = $_SESSION['user_id'];
$message = '';

// Handle cart updates (Remove, Delete All, Quantity Update)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['remove_item'])) {
        $product_id = clean_input($_POST['product_id']);
        $sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $user_id, $product_id);
        mysqli_stmt_execute($stmt);
        $message = "Item removed.";
    } elseif (isset($_POST['delete_all'])) {
        $sql = "DELETE FROM cart WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        mysqli_stmt_execute($stmt);
        $message = "Cart cleared.";
    } elseif (isset($_POST['update_quantity'])) {
        $product_id = clean_input($_POST['product_id']);
        $quantity = clean_input($_POST['quantity']);
        if ($quantity > 0) {
            $sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'iii', $quantity, $user_id, $product_id);
            mysqli_stmt_execute($stmt);
            $message = "Quantity updated.";
        } else {
             // If quantity is 0, treat it as a remove action
             $sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
             $stmt = mysqli_prepare($conn, $sql);
             mysqli_stmt_bind_param($stmt, 'ii', $user_id, $product_id);
             mysqli_stmt_execute($stmt);
             $message = "Item removed.";
        }
    }
}

// Fetch Cart Items
$cart_sql = "SELECT c.product_id, c.quantity, p.product_name, p.product_price, p.product_image 
             FROM cart c JOIN products p ON c.product_id = p.product_id 
             WHERE c.user_id = ?";
$cart_stmt = mysqli_prepare($conn, $cart_sql);
mysqli_stmt_bind_param($cart_stmt, 'i', $user_id);
mysqli_stmt_execute($cart_stmt);
$cart_result = mysqli_stmt_get_result($cart_stmt);
$grand_total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User: Shopping Cart</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .container { background: #e0e0e0; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 90%; max-width: 1200px; text-align: center; }
        .header-nav { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .header-nav a { background-color: #4a69bd; color: white; padding: 8px 15px; margin: 0 5px; border-radius: 5px; text-decoration: none; }
        .cart-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .cart-table th, .cart-table td { padding: 10px; text-align: center; border: 1px solid #ccc; }
        .cart-table th { background: #4a69bd; color: white; }
        .cart-table td { background: white; }
        .cart-table .item-image { width: 50px; height: 50px; object-fit: cover; }
        .total-row { background: #4a69bd; color: white; font-weight: bold; }
        .checkout-btn { background: orange; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-top: 20px; display: inline-block; cursor: pointer; border: none;}
        .delete-all-btn { background: red; color: white; padding: 8px 15px; border-radius: 5px; border: none; cursor: pointer; margin-left: 10px; }
        .remove-btn { background: red; color: white; padding: 5px 10px; border-radius: 5px; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-nav">
            <a href="user_dashboard.php" class="nav-link">Home</a>
            <div>
                <a href="vendor_products.php" class="nav-link">View Product</a>
                <a href="request_item.php" class="nav-link">Request Item</a>
                <a href="product_status.php" class="nav-link">Product Status</a>
                <a href="logout.php" class="nav-link">LogOut</a>
            </div>
        </div>
        <h2 style="color: #4a69bd;">Shopping Cart</h2>
        
        <?php if ($message): ?><p style="color: green; font-weight: bold;"><?php echo $message; ?></p><?php endif; ?>

        <table class="cart-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($cart_result) > 0): ?>
                    <?php while ($item = mysqli_fetch_assoc($cart_result)): 
                        $item_total = $item['product_price'] * $item['quantity'];
                        $grand_total += $item_total;
                    ?>
                    <tr>
                        <td><img src="uploads/<?php echo htmlspecialchars($item['product_image']); ?>" alt="Image" class="item-image"></td>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td>Rs/- <?php echo number_format($item['product_price'], 2); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                <select name="quantity" onchange="this.form.submit()">
                                    <?php for ($i = 1; $i <= 10; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo ($item['quantity'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <input type="hidden" name="update_quantity">
                            </form>
                        </td>
                        <td>Rs/- <?php echo number_format($item_total, 2); ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                <button type="submit" name="remove_item" class="remove-btn">Remove</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6">Your cart is empty.</td></tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4">Grand Total</td>
                    <td>Rs/- <?php echo number_format($grand_total, 2); ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <button type="submit" name="delete_all" class="delete-all-btn">Delete All</button>
                        </form>
                    </td>
                </tr>
            </tfoot>
        </table>

        <?php if ($grand_total > 0): ?>
            <a href="user_checkout.php" class="checkout-btn">Proceed to CheckOut</a>
        <?php endif; ?>
    </div>
</body>
</html>