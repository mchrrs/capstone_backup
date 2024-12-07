<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
   $user_id = $_COOKIE['user_id'];
} else {
   $user_id = '';
   header('location:login.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>User Dashboard</title>

   <!-- Font Awesome CDN Link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS File Link -->
   <link rel="stylesheet" href="css/style.css">
   <style>
      /* Basic Layout Styles */
      body {
         font-family: 'Arial', sans-serif;
         margin: 0;
         padding: 0;
         background-color: #f5f5f5;
      }

      .dashboard {
         width: 90%;
         max-width: 1200px;
         margin: 20px auto;
         padding: 20px;
         background-color: #fff;
         box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
         border-radius: 8px;
      }

      .heading {
         text-align: center;
         margin-bottom: 30px;
         color: #333;
         font-size: 24px;
      }

      .box-container {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
         gap: 20px;
      }

      .box {
         background-color: #e0f7fa;
         padding: 20px;
         border-radius: 8px;
         text-align: center;
         box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
         transition: transform 0.3s ease;
      }

      .box:hover {
         transform: translateY(-5px);
      }

      .box h3 {
         font-size: 36px;
         color: #00796b;
         margin-bottom: 10px;
      }

      .box p {
         font-size: 18px;
         color: #555;
         margin-bottom: 15px;
      }

      .btn {
         background-color: #004d40;
         color: white;
         padding: 10px 20px;
         border-radius: 5px;
         text-decoration: none;
         font-size: 16px;
         transition: background-color 0.3s ease;
      }

      .btn:hover {
         background-color: rgb(218, 44, 50);
      }
   </style>
</head>

<body>

   <?php include 'components/user_header.php'; ?>
   <br><br><br><br><br>
   <section class="dashboard">
      <h1 class="heading">Welcome to Your Dashboard</h1>

      <div class="box-container">
         <!-- Profile Box -->
         <div class="box">
            <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ? LIMIT 1");
            $select_profile->execute([$user_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
            ?>
            <h3>Welcome!</h3>
            <p><?= $fetch_profile['email']; ?></p>
            <a href="update.php" class="btn">Update Profile</a>
         </div>

         <!-- Payments Box -->
         <div class="box">
            <?php
            $count_bills = $conn->prepare("SELECT * FROM `bills` WHERE user_id = ?");
            $count_bills->execute([$user_id]);
            $total_bills = $count_bills->rowCount();
            ?>
            <h3><?= $total_bills; ?></h3>
            <p>My Bills</p>
            <a href="/project/my_bills.php" class="btn">Pay Rent</a>
         </div>

         <!-- Owned Property Box -->
         <div class="box">
            <?php
            $count_owned = $conn->prepare("SELECT * FROM `occupied_properties` WHERE email = ?");
            $count_owned->execute([$fetch_profile['email']]);
            $total_owned = $count_owned->rowCount();
            ?>
            <h3><?= $total_owned; ?></h3>
            <p>Owned Properties</p>
            <a href="/project/occupied_properties.php" class="btn">Access Your Units</a>
         </div>

         <!-- Tickets Box -->
         <div class="box">
            <?php
            // Query to count the complaints by the user's ID
            $count_complaints = $conn->prepare("SELECT * FROM `complaints` WHERE user_id = ?");
            $count_complaints->execute([$fetch_profile['id']]);
            $total_complaints = $count_complaints->rowCount();
            ?>
            <!-- Display the total complaints count -->
            <h3><?= $total_complaints; ?></h3>
            <p>Your Complaints</p>
            <a href="/project/view_complaints.php" class="btn">Manage your tickets</a>
         </div>

      </div>

   </section>
   <br><br><br><br><br><br><br><br><br><br><br><br><br>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

   <?php include 'components/footer.php'; ?>

   <!-- Custom JS File Link -->
   <script src="js/script.js"></script>

   <?php include 'components/message.php'; ?>

</body>

</html>