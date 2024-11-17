<?php
include '../components/connect.php';

// Check if the admin is logged in
if (isset($_COOKIE['admin_id'])) {
    $admin_id = $_COOKIE['admin_id'];
} else {
    $admin_id = '';
    header('location:login.php');
    exit;
}

// Check if user ID is passed via URL
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ? LIMIT 1");
    $select_user->execute([$user_id]);

    if ($select_user->rowCount() > 0) {
        $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
    } else {
        header('location:users.php');
        exit;
    }
} else {
    header('location:users.php');
    exit;
}

// Fetch the total number of payments the user has made
$count_payments = $conn->prepare("SELECT * FROM `payments` WHERE user_id = ?");
$count_payments->execute([$user_id]);
$total_payments = $count_payments->rowCount();

// Initialize message arrays if they don't exist
if (!isset($success_msg)) {
   $success_msg = [];
}
if (!isset($warning_msg)) {
   $warning_msg = [];
}
if (!isset($info_msg)) {
   $info_msg = [];
}
if (!isset($error_msg)) {
   $error_msg = [];
}

// Show success messages
if (!empty($success_msg) && is_array($success_msg)) {
   foreach ($success_msg as $msg) {
      echo '<script>swal("' . $msg . '", "", "success");</script>';
   }
}

// Show warning messages
if (!empty($warning_msg) && is_array($warning_msg)) {
   foreach ($warning_msg as $msg) {
      echo '<script>swal("' . $msg . '", "", "warning");</script>';
   }
}

// Show info messages
if (!empty($info_msg) && is_array($info_msg)) {
   foreach ($info_msg as $msg) {
      echo '<script>swal("' . $msg . '", "", "info");</script>';
   }
}

// Show error messages
if (!empty($error_msg) && is_array($error_msg)) {
   foreach ($error_msg as $msg) {
      echo '<script>swal("' . $msg . '", "", "error");</script>';
   }
}

// Update user details if form is submitted
if (isset($_POST['submit'])) {
    // Sanitize and update name
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    if (!empty($name)) {
        // Update user's name
        $update_name = $conn->prepare("UPDATE `users` SET name = ? WHERE id = ?");
        $update_name->execute([$name, $user_id]);
        $success_msg[] = 'Username updated!';
    }

    // Sanitize and update email
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $update_email = $conn->prepare("UPDATE `users` SET email = ? WHERE id = ?");
        $update_email->execute([$email, $user_id]);
        $success_msg[] = 'Email updated!';
    } else {
        $warning_msg[] = 'Invalid email format!';
    }

    // Sanitize and update phone number
    $number = $_POST['number'];
    $number = filter_var($number, FILTER_SANITIZE_STRING);
    if (!empty($number) && preg_match('/^\d{10}$/', $number)) {
        $update_number = $conn->prepare("UPDATE `users` SET number = ? WHERE id = ?");
        $update_number->execute([$number, $user_id]);
        $success_msg[] = 'Phone number updated!';
    } else {
        $warning_msg[] = 'Invalid phone number format!';
    }

    // Password change section
    if (!empty($_POST['old_pass']) || !empty($_POST['new_pass']) || !empty($_POST['c_pass'])) {
        $prev_pass = $fetch_user['password'];  // The existing hashed password from DB
        $old_pass = $_POST['old_pass'];
        $new_pass = $_POST['new_pass'];
        $c_pass = $_POST['c_pass'];

        // Verify the old password entered by the user using password_verify
        if (!password_verify($old_pass, $prev_pass)) {
            $warning_msg[] = 'Old password does not match!';
        } elseif ($new_pass !== $c_pass) {
            $warning_msg[] = 'New password does not match with confirmation!';
        } else {
            // Hash the new password before updating it
            $hashed_new_pass = password_hash($new_pass, PASSWORD_DEFAULT);

            // Update password in the database
            $update_password = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
            $update_password->execute([$hashed_new_pass, $user_id]);
            $success_msg[] = 'Password updated!';
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
    <title>Update User</title>

    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>

    <!-- Header Section -->
    <?php include '../components/admin_header.php'; ?>
    <!-- End Header Section -->

    <!-- Update User Form -->
    <section class="form-container">

        <form action="" method="POST">
            <h3>Update User Profile</h3>

            <input type="text" name="name" placeholder="Name" value="<?= htmlspecialchars($fetch_user['name']); ?>" class="box" required>
            <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($fetch_user['email']); ?>" class="box" required>
            <input type="text" name="number" placeholder="Phone Number" value="<?= htmlspecialchars($fetch_user['number']); ?>" class="box" required>

            <!-- Password change fields -->
            <input type="password" name="old_pass" placeholder="Enter Old Password" class="box">
            <input type="password" name="new_pass" placeholder="Enter New Password" class="box">
            <input type="password" name="c_pass" placeholder="Confirm New Password" class="box">

            <input type="submit" value="Update Now" name="submit" class="btn">
        </form>

    </section>
    <!-- End Update Section -->

    <!-- Include SweetAlert for notifications -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <!-- Custom JS File link -->
    <script src="../js/admin_script.js"></script>

    <?php include '../components/message.php'; ?>

</body>

</html>
