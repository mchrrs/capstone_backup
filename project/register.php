<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

if(isset($_POST['submit'])){

   $id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING); 
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING); 
   $c_pass = sha1($_POST['c_pass']);
   $c_pass = filter_var($c_pass, FILTER_SANITIZE_STRING);   

   $select_users = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $select_users->execute([$email]);

   if($select_users->rowCount() > 0){
      $warning_msg[] = 'email already taken!';
   }else{
      if($pass != $c_pass){
         $warning_msg[] = 'Password not matched!';
      }else{
         $insert_user = $conn->prepare("INSERT INTO `users`(id, name, number, email, password) VALUES(?,?,?,?,?)");
         $insert_user->execute([$id, $name, $number, $email, $c_pass]);
         
         if($insert_user){
            $verify_users = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ? LIMIT 1");
            $verify_users->execute([$email, $pass]);
            $row = $verify_users->fetch(PDO::FETCH_ASSOC);
         
            if($verify_users->rowCount() > 0){
               setcookie('user_id', $row['id'], time() + 60*60*24*30, '/');
               header('location:home.php');
            }else{
               $error_msg[] = 'something went wrong!';
            }
         }

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
   <title>Register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<!-- register section starts  -->

<section class="form-container">

<section class="form-container">

<form action="" method="post" class="register-form">
   <h3>Create Your Account</h3>

   <div class="form-group">
      <label for="name">Full Name</label>
      <input type="text" name="name" id="name" required maxlength="50" 
      pattern="[A-Za-z\s]+" 
      title="Name should only contain letters and spaces." 
      placeholder="John Doe" class="box">
   </div>

   <div class="form-group">
      <label for="email">Email Address</label>
      <input type="email" name="email" id="email" required maxlength="50" placeholder="example@example.com" class="box">
   </div>

   <div class="form-group">
      <label for="number">Phone Number</label>
      <input type="tel" name="number" id="number" required pattern="[0-9]{10,11}" title="Please enter a valid 10-11 digit phone number" placeholder="09123456789" class="box">
   </div>

   <div class="form-group">
      <label for="password">Password</label>
      <input type="password" name="pass" id="password" required maxlength="20" placeholder="Enter a secure password" class="box">
   </div>

   <div class="form-group">
      <label for="c_pass">Confirm Password</label>
      <input type="password" name="c_pass" id="c_pass" required maxlength="20" placeholder="Re-enter your password" class="box">
   </div>

   <p>Already have an account? <a href="login.php">Login here</a></p>

   <input type="submit" value="Register Now" name="submit" class="btn">
</form>

</section>


</section>

<!-- register section ends -->










<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<?php include 'components/message.php'; ?>

</body>
</html>