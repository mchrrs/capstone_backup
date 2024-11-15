<?php

include '../components/connect.php';

if(isset($_COOKIE['admin_id'])){
   $admin_id = $_COOKIE['admin_id'];
}else{
   $admin_id = '';
   header('location:login.php');
}

if(isset($_POST['delete'])){

   $delete_id = $_POST['delete_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_delete = $conn->prepare("SELECT * FROM `complaints` WHERE id = ?");
   $verify_delete->execute([$delete_id]);

   if($verify_delete->rowCount() > 0){
      $delete_complaint = $conn->prepare("DELETE FROM `complaints` WHERE id = ?");
      $delete_complaint->execute([$delete_id]);
      $success_msg[] = 'Complaint deleted!';
   }else{
      $warning_msg[] = 'Complaint already deleted!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Complaints</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include '../components/admin_header.php'; ?>
<!-- header section ends -->

<!-- complaints section starts  -->

<section class="grid">

   <h1 class="heading">complaints</h1>

   <form action="" method="POST" class="search-form">
      <input type="text" name="search_box" placeholder="search complaints..." maxlength="100" required>
      <button type="submit" class="fas fa-search" name="search_btn"></button>
   </form>

   <div class="box-container">

   <?php
      if(isset($_POST['search_box']) OR isset($_POST['search_btn'])){
         $search_box = $_POST['search_box'];
         $search_box = filter_var($search_box, FILTER_SANITIZE_STRING);
         $select_complaints = $conn->prepare("SELECT * FROM `complaints` WHERE complaint_text LIKE '%{$search_box}%'");
         $select_complaints->execute();
      }else{
         $select_complaints = $conn->prepare("SELECT * FROM `complaints`");
         $select_complaints->execute();
      }
      if($select_complaints->rowCount() > 0){
         while($fetch_complaints = $select_complaints->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p>user_id : <span><?= $fetch_complaints['user_id']; ?></span></p>
      <p>property_id : <span><?= $fetch_complaints['property_id']; ?></span></p>
      <p>complaint : <span><?= $fetch_complaints['complaint_text']; ?></span></p>
      <p>status : <span><?= $fetch_complaints['status']; ?></span></p>
      <form action="" method="POST">
         <input type="hidden" name="delete_id" value="<?= $fetch_complaints['id']; ?>">
         <input type="submit" value="delete complaint" onclick="return confirm('delete this complaint?');" name="delete" class="delete-btn">
      </form>
   </div>
   <?php
      }
   }elseif(isset($_POST['search_box']) OR isset($_POST['search_btn'])){
      echo '<p class="empty">results not found!</p>';
   }else{
      echo '<p class="empty">no complaints yet!</p>';
   }
   ?>

   </div>

</section>

<!-- complaints section ends -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

<?php include '../components/message.php'; ?>

</body>
</html>
