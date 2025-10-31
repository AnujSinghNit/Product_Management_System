<?php
require_once 'config.php';
redirect_unauthorized('is_vendor', 'vendor_login.php'); 
$vendor_id = $_SESSION['vendor_id'];

$message = '';
$error = '';

// Handle Status Update (POST from modal/popup)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_order_status'])) {
    $order_id = clean_input($_POST['order_id']);
    $new_status = clean_input($_POST['new_status']);
    
    // Validate status against ENUM in DB
    if (in_array($new_status, ['Received', 'Ready for Shipping', 'Out For Delivery'])) {
        $sql = "UPDATE orders o JOIN order_items oi ON o.order_id = oi.order_id 
                JOIN products p ON oi.product_id = p.product_id
                SET o.order_status = ? 
                WHERE o.order_id = ? AND p.vendor_id = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'sii', $new_status, $order_id, $vendor_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = "Order #$order_id status updated to $new_status.";
        } else {
            $error = "Failed to update order status.";
        }
    } else {
        $error = "Invalid status selected.";
    }
}

// Fetch Orders related to this Vendor
$orders_sql = "SELECT DISTINCT o.order_id, o.name, o.email, o.address, o.order_status 
               FROM orders o 
               JOIN order_items oi ON o.order_id = oi.order_id 
               JOIN products p ON oi.product_id = p.product_id 
               WHERE p.vendor_id = ? 
               ORDER BY o.order_date DESC";
$orders_stmt = mysqli_prepare($conn, $orders_sql);
mysqli_stmt_bind_param($orders_stmt, 'i', $vendor_id);
mysqli_stmt_execute($orders_stmt);
$orders_result = mysqli_stmt_get_result($orders_stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendor: Product Status</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; display: flex; justify-content: center; align-items: flex-start; min-height: 100vh; }
        .container { background: #e0e0e0; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 90%; max-width: 1200px; margin-top: 50px; }
        .header-nav { display: flex; justify-content: space-between; background: #4a69bd; padding: 10px; color: white; border-radius: 8px 8px 0 0; }
        .header-nav a { background-color: #6d9eeb; color: white; padding: 8px 15px; margin: 0 5px; border-radius: 5px; text-decoration: none; }
        .order-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .order-table th, .order-table td { padding: 10px; text-align: left; border: 1px solid #ccc; background: white; }
        .order-table th { background: #4a69bd; color: white; }
        .update-btn { background: orange; color: white; padding: 5px 10px; border-radius: 5px; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-nav">
            <a href="vendor_dashboard.php">Home</a>
            <h2 style="margin: 0;">Product Status</h2>
            <a href="logout.php">LogOut</a>
        </div>
        
        <?php if ($message): ?><p style="color: green; font-weight: bold;"><?php echo $message; ?></p><?php endif; ?>
        <?php if ($error): ?><p style="color: red; font-weight: bold;"><?php echo $error; ?></p><?php endif; ?>

        <table class="order-table">
            <thead>
                <tr>
                    <th style="width: 5%;">Order ID</th>
                    <th>Name</th>
                    <th>E-Mail</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th style="width: 15%;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($orders_result) > 0): ?>
                    <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                    <tr>
                        <td><?php echo $order['order_id']; ?></td>
                        <td><?php echo htmlspecialchars($order['name']); ?></td>
                        <td><?php echo htmlspecialchars($order['email']); ?></td>
                        <td><?php echo htmlspecialchars($order['address']); ?></td>
                        <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                        <td>
                            <button class="update-btn" onclick="openUpdateModal(<?php echo $order['order_id']; ?>, '<?php echo $order['order_status']; ?>')">Update</button>
                            <button class="update-btn" style="background: red;">Delete</button> 
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6">No orders found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div id="statusModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center;">
        <div style="background: #e0e0e0; padding: 30px; border-radius: 10px; width: 400px; text-align: center;">
            <h3 style="color: #4a69bd;">Update Order Status</h3>
            <form method="POST">
                <input type="hidden" name="order_id" id="modalOrderId">
                <div style="background: #4a69bd; padding: 30px; border-radius: 15px; margin: 20px 0;">
                    <label style="display: block; color: white; text-align: left; margin-bottom: 15px;">
                        <input type="radio" name="new_status" value="Received" required> Recieved
                    </label>
                    <label style="display: block; color: white; text-align: left; margin-bottom: 15px;">
                        <input type="radio" name="new_status" value="Ready for Shipping" required> Ready for Shipping
                    </label>
                    <label style="display: block; color: white; text-align: left;">
                        <input type="radio" name="new_status" value="Out For Delivery" required> Out For Delivery
                    </label>
                </div>
                <button type="submit" name="update_order_status" class="update-btn" style="background: #4a69bd; width: 100%;">Update</button>
                <button type="button" onclick="document.getElementById('statusModal').style.display='none'" style="background: gray; color: white; padding: 10px; border: none; border-radius: 5px; margin-top: 10px; width: 100%;">Cancel</button>
            </form>
        </div>
    </div>
    
    <script>
        function openUpdateModal(orderId, currentStatus) {
            document.getElementById('modalOrderId').value = orderId;
            // Set the current status radio button as checked (if matched)
            const radios = document.getElementsByName('new_status');
            radios.forEach(radio => {
                radio.checked = (radio.value === currentStatus);
            });
            document.getElementById('statusModal').style.display = 'flex';
        }
    </script>
</body>
</html>