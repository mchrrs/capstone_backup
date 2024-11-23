<?php
include '../components/connect.php'; // Database connection

// Ensure the admin is logged in
if (!isset($_COOKIE['admin_id'])) {
   header('location:login.php');
   exit;
}

// Handle user deletion
if (isset($_POST['delete'])) {
   $delete_id = $_POST['delete_id']; // Get the user ID to delete
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING); // Sanitize the ID

   // Verify if the user exists
   $verify_delete = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
   $verify_delete->execute([$delete_id]);

   if ($verify_delete->rowCount() > 0) {
      // Delete the user
      $delete_user = $conn->prepare("DELETE FROM `users` WHERE id = ?");
      $delete_user->execute([$delete_id]);

      // Show success message
      $success_msg[] = 'User deleted successfully!';
   } else {
      // Show error message if user doesn't exist
      $error_msg[] = 'User not found!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Users</title>

   <!-- Font Awesome CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <!-- Custom CSS -->
   <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>

   <!-- Header Section -->
   <?php include '../components/admin_header.php'; ?>

   <!-- Users Section -->
   <section class="users-table-section">
      <h1 class="heading">Users</h1>

      <!-- Search form -->
      <form action="" method="POST" class="search-form">
         <input type="text" name="search_box" placeholder="Search users..." maxlength="100" required>
         <button type="submit" class="fas fa-search" name="search_btn"></button>
      </form>

      <?php
      // Determine if a search was made
      if (isset($_POST['search_box']) || isset($_POST['search_btn'])) {
         $search_box = filter_var($_POST['search_box'], FILTER_SANITIZE_STRING);
         $select_users = $conn->prepare("SELECT * FROM `users` WHERE name LIKE ?");
         $select_users->execute(["%$search_box%"]);
      } else {
         $select_users = $conn->prepare("SELECT * FROM `users`");
         $select_users->execute();
      }

      if ($select_users->rowCount() > 0): ?>
         <!-- Table to display users -->
         <div class="table-container">
            <table class="users-table">
               <thead>
                  <tr>
                     <th>ID</th>
                     <th>Name</th>
                     <th>Email</th>
                     <th>Phone</th>
                     <th>Actions</th>
                  </tr>
               </thead>
               <tbody>
                  <?php while ($fetch_users = $select_users->fetch(PDO::FETCH_ASSOC)): ?>
                     <tr>
                        <td><?= htmlspecialchars($fetch_users['id']); ?></td>
                        <td><?= htmlspecialchars($fetch_users['name']); ?></td>
                        <td><?= htmlspecialchars($fetch_users['email']); ?></td>
                        <td><?= htmlspecialchars($fetch_users['number']); ?></td>
                        <td>
                           <!-- Delete Form -->
                           <form action="" method="POST" style="display:inline;">
                              <input type="hidden" name="delete_id" value="<?= $fetch_users['id']; ?>">
                              <button type="submit" name="delete" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this user?');">
                                 Delete
                              </button>
                           </form>
                        </td>
                     </tr>
                  <?php endwhile; ?>
               </tbody>
            </table>
         </div>
      <?php else: ?>
         <p class="empty">No users found!</p>
      <?php endif; ?>

      <!-- Register new user link -->
      <div class="box-container">
         <a href="register_user.php" class="btn">Register a New User</a>
      </div>
   </section>

   <!-- Include SweetAlert and Custom JS -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
   <script src="../js/admin_script.js"></script>

   <!-- Display Success/Warning/Error Messages -->
   <?php include '../components/message.php'; ?>

</body>

</html>
