<?php
require_once 'config.php';
redirect_unauthorized('is_user', 'user_login.php'); 
$user_id = $_SESSION['user_id'];
$error = '';

// Check if cart is empty before proceeding
$check_cart_sql = "SELECT COUNT(*) FROM cart WHERE user_id = ?";
$check_stmt = mysqli_prepare($conn, $check_cart_sql);
mysqli_stmt_bind_param($check_stmt, 'i', $user_id);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_bind_result($check_stmt, $cart_count);
mysqli_stmt_fetch($check_stmt);
mysqli_stmt_close($check_stmt);

if ($cart_count == 0) {
    header("Location: user_cart.php");
    exit();
}

// Calculate total amount
$total_sql = "SELECT SUM(c.quantity * p.product_price) AS total FROM cart c JOIN products p ON c.product_id = p.product_id WHERE c.user_id = ?";
$total_stmt = mysqli_prepare($conn, $total_sql);
mysqli_stmt_bind_param($total_stmt, 'i', $user_id);
mysqli_stmt_execute($total_stmt);
$total_result = mysqli_stmt_get_result($total_stmt);
$total_row = mysqli_fetch_assoc($total_result);
$total_amount = $total_row['total'];

// Handle Order Placement
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_now'])) {
    // Requirement 10: Validations on forms (all fields are required via HTML, re-validate here)
    $name = clean_input($_POST['name']);
    $email = clean_input($_POST['email']);
    $phone = clean_input($_POST['number']);
    $address = clean_input($_POST['address']);
    $city = clean_input($_POST['city']);
    $state = clean_input($_POST['state']);
    $pin_code = clean_input($_POST['pin_code']);
    $payment_method = clean_input($_POST['payment_method']);

    if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($city) || empty($state) || empty($pin_code) || empty($payment_method)) {
        $error = "Please fill in all mandatory fields.";
    } else {
        // Start Transaction
        mysqli_begin_transaction($conn);
        $success = true;

        try {
            // 1. Insert into Orders Table
            $order_sql = "INSERT INTO orders (user_id, total_amount, name, email, phone, address, city, state, pin_code, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $order_stmt = mysqli_prepare($conn, $order_sql);
            mysqli_stmt_bind_param($order_stmt, 'idssssssss', $user_id, $total_amount, $name, $email, $phone, $address, $city, $state, $pin_code, $payment_method);
            if (!mysqli_stmt_execute($order_stmt)) { throw new Exception("Order insert failed."); }
            $order_id = mysqli_insert_id($conn);

            // 2. Move Cart Items to Order Items
            $items_sql = "SELECT product_id, quantity, product_price FROM cart c JOIN products p ON c.product_id = p.product_id WHERE user_id = ?";
            $items_stmt = mysqli_prepare($conn, $items_sql);
            mysqli_stmt_bind_param($items_stmt, 'i', $user_id);
            mysqli_stmt_execute($items_stmt);
            $items_result = mysqli_stmt_get_result($items_stmt);

            $order_item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $order_item_stmt = mysqli_prepare($conn, $order_item_sql);
            while ($item = mysqli_fetch_assoc($items_result)) {
                $price = $item['product_price'];
                mysqli_stmt_bind_param($order_item_stmt, 'iidd', $order_id, $item['product_id'], $item['quantity'], $price);
                if (!mysqli_stmt_execute($order_item_stmt)) { throw new Exception("Order item insert failed."); }
            }

            // 3. Clear the Cart
            $clear_cart_sql = "DELETE FROM cart WHERE user_id = ?";
            $clear_cart_stmt = mysqli_prepare($conn, $clear_cart_sql);
            mysqli_stmt_bind_param($clear_cart_stmt, 'i', $user_id);
            if (!mysqli_stmt_execute($clear_cart_stmt)) { throw new Exception("Clear cart failed."); }

            mysqli_commit($conn);

            // Redirect to a success page/popup (Image 15)
            header("Location: user_success.php?total=" . urlencode($total_amount) . "&order_id=" . $order_id);
            exit();

        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "Order processing error: " . $e->getMessage();
            $success = false;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User: Checkout</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .container { background: #e0e0e0; padding: 40px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 700px; text-align: center; }
        .header { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .nav-link { background-color: #4a69bd; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; margin: 5px; }
        .form-grid { display: flex; flex-wrap: wrap; justify-content: space-between; margin-top: 20px; }
        .form-grid input, .form-grid select { background: #e3f2fd; padding: 10px; border: 1px solid #ccc; border-radius: 8px; width: 100%; box-sizing: border-box; }
        .form-group { width: 48%; margin-bottom: 15px; }
        .label-style { background: #4a69bd; color: white; padding: 10px; border-radius: 8px; font-weight: bold; margin-bottom: 5px; display: block; }
        .order-btn { background: #4a69bd; color: white; padding: 15px 40px; border-radius: 8px; cursor: pointer; font-weight: bold; margin-top: 25px; border: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="user_cart.php" class="nav-link">Back to Cart</a>
            <h2 style="color: #4a69bd;">Checkout (Total: Rs. <?php echo number_format($total_amount, 2); ?>)</h2>
            <a href="logout.php" class="nav-link">LogOut</a>
        </div>
        
        <?php if ($error): ?><p style="color: red; font-weight: bold;"><?php echo $error; ?></p><?php endif; ?>

        <form method="POST" class="form-grid">
            <div class="form-group"><input type="text" name="name" placeholder="Name" required></div>
            <div class="form-group"><input type="text" name="number" placeholder="Number" required pattern="\d{10,15}" title="Enter a valid phone number."></div>
            <div class="form-group"><input type="email" name="email" placeholder="E-mail" required></div>
            <div class="form-group">
                <select name="payment_method" required>
                    <option value="">Payment Method</option>
                    <option value="Cash">Cash</option>
                    <option value="UPI">UPI</option>
                </select>
            </div>
            <div class="form-group"><input type="text" name="address" placeholder="Address" required></div>
            <div class="form-group"><input type="text" name="state" placeholder="State" required></div>
            <div class="form-group"><input type="text" name="city" placeholder="City" required></div>
            <div class="form-group"><input type="text" name="pin_code" placeholder="Pin Code" required pattern="\d{5,10}" title="Enter a valid pin code."></div>
            
            <button type="submit" name="order_now" class="order-btn" style="width: 100%;">Order Now</button>
        </form>
    </div>
</body>
</html>