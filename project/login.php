<?php
include 'components/connect.php';  // Include the database connection

// Check if the user is already logged in (optional, depending on your system)
if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';  // User is not logged in
}

if (isset($_POST['submit'])) {
    // Sanitize and retrieve the input fields
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_EMAIL); // Sanitize email input
    $password = $_POST['pass']; // Raw password input from user

    // Query to check if the email exists in the users table
    $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? LIMIT 1");
    $select_user->execute([$email]);
    $row = $select_user->fetch(PDO::FETCH_ASSOC);

    if ($select_user->rowCount() > 0) {
        // Validate password using password_verify
        if (password_verify($password, $row['password'])) {
            // Password is correct
            setcookie('user_id', $row['id'], time() + 60*60*24*30, '/');  // Cookie set for 30 days
            header('location:dashboard.php');  // Redirect to the user dashboard page
            exit;
        } else {
            // Incorrect password
            $warning_msg[] = 'Incorrect password!';
        }
    } else {
        // User not found
        $warning_msg[] = 'User not found!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="form-container">
    <form action="" method="POST">
        <h3>Welcome back!</h3>
        <input type="email" name="email" required maxlength="50" placeholder="Enter your email" class="box">
        <input type="password" name="pass" required maxlength="20" placeholder="Enter your password" class="box">
        <input type="submit" value="Login Now" name="submit" class="btn">
    </form>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<br><br><br><br><br><br><br>
<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

<?php
// Include the message.php file to show success or warning messages
include 'components/message.php';
?>

</body>
</html>
