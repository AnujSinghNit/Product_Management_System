<?php
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = clean_input($_POST['user_id']); // Can be email/user_id as per design
    $password = clean_input($_POST['password']);
    
    $sql = "SELECT vendor_id, name, password FROM vendors WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) { 
            $_SESSION['vendor_id'] = $row['vendor_id'];
            $_SESSION['vendor_name'] = $row['name'];
            header("Location: vendor_dashboard.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Vendor not found! Sign up first.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendor Login</title>
    <style>/* ... (Copy styles from admin_login.php) ... */
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .container { background: #e0e0e0; padding: 40px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 400px; text-align: center; }
        .header { background-color: #4a69bd; color: white; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .form-group { margin-bottom: 15px; display: flex; align-items: center; }
        label { background: #4a69bd; color: white; padding: 10px; border-radius: 8px; font-weight: bold; width: 100px; margin-right: 10px; }
        input { padding: 10px; border: 1px solid #ccc; border-radius: 8px; flex-grow: 1; background: #e3f2fd; }
        .btn-group { margin-top: 20px; display: flex; justify-content: space-around; }
        .btn { padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; width: 100px; }
        .btn-cancel { background: gray; color: white; }
        .btn-login { background: #4a69bd; color: white; }
        .error { color: #e74c3c; margin-bottom: 15px; font-weight: bold; }
        .chart-link { position: absolute; top: 10px; left: 10px; background: #4a69bd; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; }
        .back-link { position: absolute; top: 10px; right: 10px; background: #4a69bd; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; }
    </style>
</head>
<body>
    <a href="flowchart.php" class="chart-link">CHART</a>
    <a href="index.php" class="back-link">BACK</a>
    <div class="container">
        <div class="header">Event Management System</div>
        <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>User Id</label>
                <input type="text" name="user_id" placeholder="Vendor" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Vendor" required> 
            </div>
            <div class="btn-group">
                <a href="index.php" class="btn btn-cancel">Cancel</a>
                <button type="submit" class="btn btn-login">Login</button>
            </div>
        </form>
    </div>
</body>
</html>