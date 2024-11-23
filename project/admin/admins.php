<?php
include '../components/connect.php';

if (isset($_COOKIE['admin_id'])) {
   $admin_id = $_COOKIE['admin_id'];
} else {
   $admin_id = '';
   header('location:login.php');
}

if (isset($_POST['delete'])) {
   $delete_id = $_POST['delete_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_delete = $conn->prepare("SELECT * FROM `admins` WHERE id = ?");
   $verify_delete->execute([$delete_id]);

   if ($verify_delete->rowCount() > 0) {
      $delete_admin = $conn->prepare("DELETE FROM `admins` WHERE id = ?");
      $delete_admin->execute([$delete_id]);
      $success_msg[] = 'Admin deleted!';
   } else {
      $warning_msg[] = 'Admin deleted already!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admins</title>

   <!-- Font Awesome CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <!-- Custom CSS -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

   <!-- Header section -->
   <?php include '../components/admin_header.php'; ?>

   <!-- Admins Section -->
   <section class="grid">
      <h1 class="heading">Admins</h1>

      <!-- Search form -->
      <form action="" method="POST" class="search-form">
         <input type="text" name="search_box" placeholder="Search admins..." maxlength="100" required>
         <button type="submit" class="fas fa-search" name="search_btn"></button>
      </form>

      <!-- Admins Table -->
      <div class="box-container">
         <?php
         if (isset($_POST['search_box']) or isset($_POST['search_btn'])) {
            $search_box = $_POST['search_box'];
            $search_box = filter_var($search_box, FILTER_SANITIZE_STRING);
            $select_admins = $conn->prepare("SELECT * FROM `admins` WHERE name LIKE '%{$search_box}%'");
            $select_admins->execute();
         } else {
            $select_admins = $conn->prepare("SELECT * FROM `admins`");
            $select_admins->execute();
         }
         if ($select_admins->rowCount() > 0) {
         ?>
         <table class="admins-table">
            <thead>
               <tr>
                  <th>Admin Name</th>
                  <th>Actions</th>
               </tr>
            </thead>
            <tbody>
               <?php while ($fetch_admins = $select_admins->fetch(PDO::FETCH_ASSOC)) { ?>
                  <tr>
                     <td><?= htmlspecialchars($fetch_admins['name']); ?></td>
                     <td>
                        <?php if ($fetch_admins['id'] == $admin_id) { ?>
                           <a href="update.php" class="option-btn">Update Account</a>
                           <a href="register.php" class="btn">Register New</a>
                        <?php } else { ?>
                           <form action="" method="POST" style="display:inline;">
                              <input type="hidden" name="delete_id" value="<?= $fetch_admins['id']; ?>">
                              <input type="submit" value="Delete" onclick="return confirm('Delete this admin?');" name="delete" class="delete-btn">
                           </form>
                        <?php } ?>
                     </td>
                  </tr>
               <?php } ?>
            </tbody>
         </table>
         <?php
         } elseif (isset($_POST['search_box']) or isset($_POST['search_btn'])) {
            echo '<p class="empty">No results found!</p>';
         } else {
            ?>
            <p class="empty">No admins added yet!</p>
            <div class="box" style="text-align: center;">
               <p>Create a new admin</p>
               <a href="register.php" class="btn">Register Now</a>
            </div>
         <?php } ?>
      </div>
   </section>

   <!-- Custom JS -->
   <script src="../js/admin_script.js"></script>
</body>
</html>
