<?php
require_once 'config.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = clean_input($_POST['name']);
    $email = clean_input($_POST['email']);
    $password = $_POST['password']; // Raw password
    $category = clean_input($_POST['category']);

    if (empty($name) || empty($email) || empty($password) || empty($category)) {
        $error = "All fields are mandatory.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO vendors (name, email, password, category) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssss', $name, $email, $hashed_password, $category);

        if (mysqli_stmt_execute($stmt)) {
            $message = "Vendor registration successful! Please log in.";
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
    <title>Vendor Sign Up</title>
    <style>
        /* Styles to match the image design */
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .container { background: #e0e0e0; padding: 40px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 600px; text-align: center; }
        .header { background-color: #4a69bd; color: white; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .form-row { margin-bottom: 15px; display: flex; align-items: center; }
        label { background: #4a69bd; color: white; padding: 10px; border-radius: 8px; font-weight: bold; width: 150px; margin-right: 10px; text-align: left; }
        input, select { padding: 10px; border: 1px solid #ccc; border-radius: 8px; flex-grow: 1; background: #e3f2fd; }
        .btn-signup { background: #4a69bd; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; margin-top: 15px; }
        .message { color: green; margin-bottom: 15px; font-weight: bold; }
        .error { color: #e74c3c; margin-bottom: 15px; font-weight: bold; }
        .chart-link { position: absolute; top: 10px; left: 10px; background: #4a69bd; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; }
        .category-list { position: absolute; right: 10px; top: 200px; background: white; border: 1px solid #4a69bd; padding: 10px; border-radius: 5px; }
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
                <input type="text" name="name" placeholder="Vendor" required>
            </div>
            <div class="form-row">
                <label>Email</label>
                <input type="email" name="email" placeholder="Vendor" required>
            </div>
            <div class="form-row">
                <label>Password</label>
                <input type="password" name="password" placeholder="Vendor" required>
            </div>
            <div class="form-row">
                <label>Category</label>
                <select name="category" required>
                    <option value="">Drop Down</option>
                    <option value="Catering">Catering</option>
                    <option value="Florist">Florist</option>
                    <option value="Decoration">Decoration</option>
                    <option value="Lighting">Lighting</option>
                </select>
            </div>
            <button type="submit" class="btn-signup">Sign Up</button>
        </form>
    </div>
    <div class="category-list">
        * Catering<br>
        * Florist<br>
        * Decoration<br>
        * Lighting
    </div>
</body>
</html>