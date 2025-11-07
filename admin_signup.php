<!-- <?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
require_once 'config.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_signup'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $sql_check = "SELECT * FROM admins WHERE email = ?";
        $stmt_check = mysqli_prepare($conn, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "s", $email);
        mysqli_stmt_execute($stmt_check);
        $result = mysqli_stmt_get_result($stmt_check);

        if (mysqli_num_rows($result) > 0) {
            $error = "Email already registered.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert admin data into database
            $sql = "INSERT INTO admins (name, email, password) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed_password);

            if (mysqli_stmt_execute($stmt)) {
                $message = "Admin registered successfully! You can now log in.";
            } else {
                $error = "Database error: Could not register admin.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Signup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef2f3;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            width: 350px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            color: green;
        }
        .error {
            text-align: center;
            color: red;
        }
    </style>
</head>
<body>

    <form method="POST" action="">
        <h2>Admin Signup</h2>

        <?php if ($message): ?>
            <p class="message"><?= $message ?></p>
        <?php elseif ($error): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>

        <button type="submit" name="admin_signup">Sign Up</button>
    </form>

</body>
</html> -->

<?php
require_once 'config.php';

$message = '';
$error = '';

// Check if an Admin is already logged in, and redirect them.
if (is_admin() || is_vendor() || is_user()) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['admin_signup'])) {
    $user_id = clean_input($_POST['user_id']);
    $password = clean_input($_POST['password']);
    $confirm_password = clean_input($_POST['confirm_password']);
    
    // Requirement 10: Validations on forms
    if (empty($user_id) || empty($password) || empty($confirm_password)) {
        $error = "All fields are mandatory.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) { // Simple length check
        $error = "Password must be at least 6 characters long.";
    } else {
        // Check if user_id already exists
        $check_sql = "SELECT admin_id FROM admin WHERE user_id = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, 's', $user_id);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $error = "User ID already taken. Please choose another.";
        } else {
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new admin user
            // NOTE: You must ensure an 'admin' table exists in your DB with at least 'user_id' and 'password' columns.
            $sql = "INSERT INTO admin (user_id, password) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'ss', $user_id, $hashed_password);
            
            if (mysqli_stmt_execute($stmt)) {
                $message = "Admin account created successfully! You can now log in.";
                // Clear inputs after successful registration
                $_POST = array(); 
            } else {
                $error = "Database error: Could not create account.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Sign Up</title>
    <style>
        /* Styles matching admin_login.php */
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .container { background: #e0e0e0; padding: 40px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 400px; text-align: center; }
        .header { background-color: #4a69bd; color: white; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .form-group { margin-bottom: 15px; display: flex; align-items: center; }
        label { background: #4a69bd; color: white; padding: 10px; border-radius: 8px; font-weight: bold; width: 100px; margin-right: 10px; }
        input { padding: 10px; border: 1px solid #ccc; border-radius: 8px; flex-grow: 1; background: #e3f2fd; }
        .btn-group { margin-top: 20px; display: flex; justify-content: space-around; }
        .btn { padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; width: 100px; text-decoration: none; display: inline-block; }
        .btn-cancel { background: gray; color: white; }
        .btn-signup { background: #4a69bd; color: white; }
        .message { color: green; margin-bottom: 15px; font-weight: bold; }
        .error { color: #e74c3c; margin-bottom: 15px; font-weight: bold; }
        .chart-link { position: absolute; top: 10px; left: 10px; background: #4a69bd; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; }
    </style>
</head>
<body>
    <a href="flowchart.php" class="chart-link">CHART</a>
    <div class="container">
        <div class="header">Admin Sign Up</div>
        <?php if ($message): ?><div class="message"><?php echo $message; ?></div><?php endif; ?>
        <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>User Id</label>
                <input type="text" name="user_id" placeholder="Choose a User ID" required value="<?php echo htmlspecialchars($_POST['user_id'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter Password" required>
            </div>
            <div class="form-group">
                <label>Confirm</label>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            </div>
            <div class="btn-group">
                <a href="index.php" class="btn btn-cancel">Cancel</a>
                <button type="submit" name="admin_signup" class="btn btn-signup">Sign Up</button>
            </div>
            <p style="margin-top: 15px;"><a href="admin_login.php">Already have an account? Login here.</a></p>
        </form>
    </div>
</body>
</html>
