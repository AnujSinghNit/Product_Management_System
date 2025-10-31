<?php
require_once 'config.php';
redirect_unauthorized('is_admin', 'admin_login.php'); 

$action = $_GET['action'] ?? 'add';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vendor_id = clean_input($_POST['vendor_id']);
    $type = clean_input($_POST['membership_type']);
    $duration_months = 0;

    // Requirement 10: Validations on forms (all fields mandatory)
    if (empty($vendor_id) || empty($type)) {
        $error = "All fields are mandatory.";
    } else {
        // Calculate end date based on membership type (Requirement 1: 6 months or 1 year or 2 years)
        if ($type == '6 months') $duration_months = 6;
        elseif ($type == '1 year') $duration_months = 12;
        elseif ($type == '2 years') $duration_months = 24;

        if ($action == 'add') {
            // Check if vendor exists
            $check_sql = "SELECT vendor_id FROM vendors WHERE vendor_id = ?";
            $check_stmt = mysqli_prepare($conn, $check_sql);
            mysqli_stmt_bind_param($check_stmt, 'i', $vendor_id);
            mysqli_stmt_execute($check_stmt);
            if (mysqli_stmt_get_result($check_stmt)->num_rows == 0) {
                $error = "Vendor ID not found.";
            } else {
                $start_date = date('Y-m-d');
                $end_date = date('Y-m-d', strtotime("+$duration_months months"));

                $sql = "INSERT INTO membership (vendor_id, membership_type, start_date, end_date) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, 'isss', $vendor_id, $type, $start_date, $end_date);
                
                if (mysqli_stmt_execute($stmt)) {
                    $message = "Membership added for Vendor ID $vendor_id until $end_date.";
                } else {
                    $error = "Failed to add membership. Vendor might already have an active plan.";
                }
            }
        }
        // Update logic would be similar, checking existing membership and extending end_date
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo ($action == 'add') ? 'Add' : 'Update'; ?> Vendor Membership</title>
    <style>
        /* Styles */
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .container { background: #e0e0e0; padding: 40px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 500px; text-align: center; }
        .form-row { margin-bottom: 15px; display: flex; align-items: center; }
        label { background: #4a69bd; color: white; padding: 10px; border-radius: 8px; font-weight: bold; width: 120px; margin-right: 10px; text-align: left; }
        input[type="text"] { padding: 10px; border: 1px solid #ccc; border-radius: 8px; flex-grow: 1; background: #e3f2fd; }
        .radio-group { display: flex; justify-content: space-around; margin-top: 15px; padding: 10px; background: #ccc; border-radius: 8px; }
        .radio-group label { background: none; color: #333; width: auto; font-weight: normal; }
        .btn-submit { background: #4a69bd; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; margin-top: 15px; }
        .message { color: green; margin-bottom: 15px; font-weight: bold; }
        .error { color: #e74c3c; margin-bottom: 15px; font-weight: bold; }
        .nav-links a { background-color: #4a69bd; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; margin: 5px; }
    </style>
</head>
<body>
    <div class="nav-links" style="position: absolute; top: 20px; left: 50%; transform: translateX(-50%);">
        <a href="admin_dashboard.php">Home</a>
        <a href="admin_maintenance.php?type=vendor">Back to Maintenance</a>
        <a href="logout.php">LogOut</a>
    </div>

    <div class="container">
        <h2 style="color: #4a69bd;"><?php echo ($action == 'add') ? 'Add New' : 'Update Existing'; ?> Membership</h2>
        
        <?php if ($message): ?><div class="message"><?php echo $message; ?></div><?php endif; ?>
        <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>

        <form method="POST">
            <input type="hidden" name="action" value="<?php echo $action; ?>">
            <div class="form-row">
                <label>Vendor ID</label>
                <input type="text" name="vendor_id" placeholder="Enter Vendor ID" required>
            </div>
            
            <div class="radio-group">
                <label>
                    <input type="radio" name="membership_type" value="6 months" required <?php echo ($action == 'add' || $action == 'update') ? 'checked' : ''; ?>> 6 months (Default)
                </label>
                <label>
                    <input type="radio" name="membership_type" value="1 year" required> 1 year
                </label>
                <label>
                    <input type="radio" name="membership_type" value="2 years" required> 2 years
                </label>
            </div>
            
            <button type="submit" class="btn-submit"><?php echo ($action == 'add') ? 'Add Membership' : 'Update Membership'; ?></button>
        </form>
    </div>
</body>
</html>