<?php
include '../components/connect.php';

if (isset($_COOKIE['admin_id'])) {
   $admin_id = $_COOKIE['admin_id'];
} else {
   $admin_id = '';
   header('location:login.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>

   <!-- Font Awesome CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>
   <style>
      /* Dashboard Section */
      .dashboard {
         padding: 20px;
         background-color: #f4f7fc;
      }

      /* Box Container Styling */
      .box-container {
         display: grid;
         grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
         gap: 20px;
         margin-top: 30px;
      }

      /* Common Box Styling */
      .box {
         background-color: #ffffff;
         border-radius: 8px;
         padding: 20px;
         box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
         text-align: center;
         transition: transform 0.3s ease-in-out;
      }

      .box:hover {
         transform: scale(1.05);
      }

      /* Heading inside boxes */
      .box h3 {
         font-size: 2.5rem;
         color: #333;
         margin-bottom: 10px;
      }

      /* Paragraph inside boxes */
      .box p {
         color: #555;
         font-size: 1.2rem;
         margin-bottom: 20px;
      }

      /* Action buttons */
      .btn {
         display: inline-block;
         padding: 10px 20px;
         background-color: #33a2ff;
         color: #ffffff;
         text-decoration: none;
         border-radius: 5px;
         transition: background-color 0.3s ease;
      }

      .btn:hover {
         background-color: #0277bd;
      }

      /* Specific Styling for each Box */
      .profile-box {
         background-color: #e0f7fa;
      }

      .property-box {
         background-color: #c8e6c9;
      }

      .user-box {
         background-color: #fff3e0;
      }

      .admin-box {
         background-color: #ffebee;
      }

      .complaint-box {
         background-color: #f3e5f5;
      }

      .occupied-box {
         background-color: #fffde7;
      }

      /* Add responsiveness */
      @media (max-width: 768px) {
         .box-container {
            grid-template-columns: 1fr 1fr;
         }
      }

      @media (max-width: 480px) {
         .box-container {
            grid-template-columns: 1fr;
         }
      }
   </style>

   <!-- Header section starts -->
   <?php include '../components/admin_header.php'; ?>
   <!-- Header section ends -->
<br><br><br>
   <!-- Dashboard section starts -->
   <section class="dashboard">

      <h1 class="heading">Admin Dashboard</h1>

      <div class="box-container">

         <!-- Profile Section -->
         <div class="box profile-box">
            <?php
            $select_profile = $conn->prepare("SELECT * FROM `admins` WHERE id = ? LIMIT 1");
            $select_profile->execute([$admin_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
            ?>
            <h3>Welcome, <?= htmlspecialchars($fetch_profile['name']); ?>!</h3>
            <p>Admin</p>
            <a href="update.php" class="btn">Update Profile</a>
         </div>

         <!-- Property Listings Section -->
         <div class="box property-box">
            <?php
            $select_listings = $conn->prepare("SELECT * FROM `property`");
            $select_listings->execute();
            $count_listings = $select_listings->rowCount();
            ?>
            <h3><?= $count_listings; ?></h3>
            <p>Properties Posted</p>
            <a href="listings.php" class="btn">View Listings</a>
         </div>

         <!-- Users Section -->
         <div class="box user-box">
            <?php
            $select_users = $conn->prepare("SELECT * FROM `users`");
            $select_users->execute();
            $count_users = $select_users->rowCount();
            ?>
            <h3><?= $count_users; ?></h3>
            <p>Total Users</p>
            <a href="users.php" class="btn">View Users</a>
         </div>

         <!-- Admins Section -->
         <div class="box admin-box">
            <?php
            $select_admins = $conn->prepare("SELECT * FROM `admins`");
            $select_admins->execute();
            $count_admins = $select_admins->rowCount();
            ?>
            <h3><?= $count_admins; ?></h3>
            <p>Total Admins</p>
            <a href="admins.php" class="btn">View Admins</a>
         </div>

         <!-- Complaints Section -->
         <div class="box complaint-box">
            <?php
            $select_complaints = $conn->prepare("SELECT * FROM `complaints`");
            $select_complaints->execute();
            $count_complaints = $select_complaints->rowCount();
            ?>
            <h3><?= $count_complaints; ?></h3>
            <p>Complaints</p>
            <a href="complaints.php" class="btn">View Complaints</a>
         </div>

         <!-- Occupied Properties Section -->
         <div class="box occupied-box">
            <?php
            $select_occupied_properties = $conn->prepare("SELECT * FROM `occupied_properties`");
            $select_occupied_properties->execute();
            $count_occupied_properties = $select_occupied_properties->rowCount();
            ?>
            <h3><?= $count_occupied_properties; ?></h3>
            <p>Occupied Properties</p>
            <a href="occupied_properties.php" class="btn">View Occupied Units</a>
         </div>

      </div>

   </section>
   <!-- Dashboard section ends -->

   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

   <!-- Custom JS file link -->
   <script src="../js/admin_script.js"></script>

   <?php include '../components/message.php'; ?>

</body>
</html>