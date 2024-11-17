<?php
include '../components/connect.php'; // Database connection

// Check if admin is logged in
if (!isset($_COOKIE['admin_id'])) {
    header('location:login.php');
    exit;
}

if (isset($_POST['submit'])) {
    $id = uniqid(); // Generate a unique ID

    // Sanitize input fields
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $warning_msg[] = 'Invalid email address!';
    }

    // Password validation
    $password = $_POST['password'];
    $c_pass = $_POST['c_pass'];

    // Check if password and confirm password match
    if ($password !== $c_pass) {
        $warning_msg[] = 'Passwords do not match!';
    }

    if (empty($warning_msg)) {
        // Hash password
        $hashed_pass = password_hash($password, PASSWORD_DEFAULT);

        // Check if the username or email already exists
        $select_users = $conn->prepare("SELECT * FROM users WHERE name = ? OR email = ?");
        $select_users->execute([$name, $email]);

        if ($select_users->rowCount() > 0) {
            $warning_msg[] = 'Username or Email already taken!';
        } else {
            // Insert user into the database
            $insert_user = $conn->prepare("INSERT INTO users(id, name, number, email, password) VALUES(?, ?, ?, ?, ?)");
            $insert_user->execute([$id, $name, $number, $email, $hashed_pass]);

            $success_msg[] = 'Registered successfully!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="form-container">
    <form action="" method="POST">
        <h3>Register New User</h3>
        <input type="text" name="name" placeholder="Enter username" maxlength="50" class="box" required>
        <input type="text" name="number" placeholder="Enter phone number" maxlength="11" class="box" required>
        <input type="email" name="email" placeholder="Enter email address" class="box" required>
        <input type="password" name="password" placeholder="Enter password" maxlength="50" class="box" required>
        <input type="password" name="c_pass" placeholder="Confirm password" maxlength="50" class="box" required>
        <input type="submit" value="Register Now" name="submit" class="btn">
    </form>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="../js/admin_script.js"></script>

<?php include '../components/message.php'; ?>

</body>
</html>
