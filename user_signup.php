<?php
require_once 'config.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = clean_input($_POST['name']);
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are mandatory.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'sss', $name, $email, $hashed_password);

        if (mysqli_stmt_execute($stmt)) {
            $message = "User registration successful! Please log in.";
        } else {
            $error = "Registration failed. Email may already be in use.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Sign Up</title>
    <style>
        /* Styles to match the image design */
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .container { background: #e0e0e0; padding: 40px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 600px; text-align: center; }
        .header { background-color: #4a69bd; color: white; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .form-row { margin-bottom: 15px; display: flex; align-items: center; }
        label { background: #4a69bd; color: white; padding: 10px; border-radius: 8px; font-weight: bold; width: 150px; margin-right: 10px; text-align: left; }
        input { padding: 10px; border: 1px solid #ccc; border-radius: 8px; flex-grow: 1; background: #e3f2fd; }
        .btn-signup { background: #4a69bd; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; margin-top: 15px; }
        .message { color: green; margin-bottom: 15px; font-weight: bold; }
        .error { color: #e74c3c; margin-bottom: 15px; font-weight: bold; }
        .chart-link { position: absolute; top: 10px; left: 10px; background: #4a69bd; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; }
    </style>
</head>
<body>
    <a href="flowchart.php" class="chart-link">CHART</a>
    <div class="container">
        <div class="header">Event Management System</div>
        
        <?php if ($message): ?><div class="message"><?php echo $message; ?></div><?php endif; ?>
        <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>

        <form method="POST">
            <div class="form-row">
                <label>Name</label>
                <input type="text" name="name" placeholder="User" required>
            </div>
            <div class="form-row">
                <label>Email</label>
                <input type="email" name="email" placeholder="User" required>
            </div>
            <div class="form-row">
                <label>Password</label>
                <input type="password" name="password" placeholder="User" required>
            </div>
            <button type="submit" class="btn-signup">Sign Up</button>
        </form>
        <a href="index.php" style="display: block; margin-top: 15px; color: #4a69bd;">Go to Login</a>
    </div>
</body>
</html>