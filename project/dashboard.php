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
   <title>dashboard</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <section class="dashboard">

      <h1 class="heading">dashboard</h1>

      <div class="box-container">

         <div class="box">
            <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ? LIMIT 1");
            $select_profile->execute([$user_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
            ?>
            <h3>welcome!</h3>
            <p><?= $fetch_profile['name']; ?></p>
            <a href="update.php" class="btn">update profile</a>
         </div>





         <div class="box">
            <?php
            // Count the number of payments for the user in the 'payments' table
            $count_payments = $conn->prepare("SELECT * FROM `payments` WHERE user_id = ?");
            $count_payments->execute([$user_id]);
            $total_payments = $count_payments->rowCount();
            ?>
            <h3>Please pay here</h3>
            <p>Payments</p>
            <a href="/project/index.php" class="btn">Pay House Rent</a>
         </div>


         <div class="box">
            <?php
            $count_owned = $conn->prepare("SELECT * FROM `owned` WHERE user_id = ?");
            $count_owned->execute([$user_id]);
            $total_owned = $count_owned->rowCount();
            ?>
            <h3>View your property</h3>
            <p>Your Unit</p>
            <a href="/project/owned_properties.php" class="btn">Manage Property</a>
         </div>

      </div>

   </section>
   <br><br><br><br><br><br><br><br><br><br><br>






















   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

   <?php include 'components/footer.php'; ?>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>

   <?php include 'components/message.php'; ?>

</body>

</html>