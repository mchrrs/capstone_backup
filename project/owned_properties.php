<?php

include '../project/components/connect.php';

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
   <title>Owned Properties</title>

   <!-- font awesome cdn link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link -->
   <link rel="stylesheet" href="/project/css/style.css">

</head>

<body>

   <!-- header section starts -->
   <?php include '../project/components/user_header.php'; ?>
   <!-- header section ends -->

   <!-- owned properties section starts -->

   <section class="grid">

      <h1 class="heading">Your Owned Properties</h1>

      <div class="box-container">

         <?php
         $select_properties = $conn->prepare("SELECT * FROM `owned_properties` WHERE user_id = ?");
         $select_properties->execute([$user_id]);

         if ($select_properties->rowCount() > 0) {
            while ($fetch_property = $select_properties->fetch(PDO::FETCH_ASSOC)) {
         ?>
               <div class="box">
                  <p>Property Name: <span><?= $fetch_property['property_name']; ?></span></p>
                  <p>Location: <span><?= $fetch_property['location']; ?></span></p>
                  <p>Size: <span><?= $fetch_property['size']; ?> sq. ft.</span></p>
                  <p>Price: <span>$<?= number_format($fetch_property['price'], 2); ?></span></p>
                  <p>Status: <span><?= $fetch_property['status']; ?></span></p>

                  <!-- Buttons for additional actions -->
                  <div class="actions">
                     <a href="messages.php?property_id=<?= $fetch_property['id']; ?>" class="btn">Messages</a>
                     <a href="complaints.php?property_id=<?= $fetch_property['id']; ?>" class="btn">Complaints</a>
                     <a href="transactions.php?property_id=<?= $fetch_property['id']; ?>" class="btn">Payments</a>
                  </div>
               </div>
         <?php
            }
         } else {
            echo '<p class="empty">You have no owned properties!</p>';
         }
         ?>

      </div>

   </section>

   <!-- owned properties section ends -->

   <!-- custom js file link -->
   <script src="../js/user_script.js"></script>


   <?php include '../project/components/message.php'; ?>

</body>

</html>