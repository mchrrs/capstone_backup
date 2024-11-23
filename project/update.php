<?php  
include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
   header('location:login.php');
}

$select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ? LIMIT 1");
$select_user->execute([$user_id]);
$fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

if(isset($_POST['submit'])){
   // Handle form submission
   // (Code for form handling, including validation, goes here as in your original code)
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Account</title>

   <!-- Font Awesome for Icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Tailwind CSS for Styling -->
   <script src="https://cdn.tailwindcss.com"></script>

   <!-- Custom CSS -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gray-50">

<?php include 'components/user_header.php'; ?>
<br><br><br><br><br><br><br><br>
<section class="max-w-5xl mx-auto p-12 bg-white rounded-lg shadow-lg mt-10">
   <form action="" method="post" class="space-y-8">
      <h3 class="text-4xl font-semibold text-center text-teal-600">Update Your Account</h3>

      <div class="space-y-6">
         <!-- Name Input -->
         <input type="text" name="name" maxlength="50" placeholder="<?= $fetch_user['name']; ?>" class="w-full px-6 py-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 text-lg">

         <!-- Email Input -->
         <input type="email" name="email" maxlength="50" placeholder="<?= $fetch_user['email']; ?>" class="w-full px-6 py-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 text-lg">

         <!-- Number Input -->
         <input type="tel" name="number" maxlength="11" placeholder="<?= $fetch_user['number']; ?>" class="w-full px-6 py-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 text-lg">

         <!-- Password Fields -->
         <input type="password" name="old_pass" maxlength="20" placeholder="Enter your old password" class="w-full px-6 py-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 text-lg">
         <input type="password" name="new_pass" maxlength="20" placeholder="Enter your new password" class="w-full px-6 py-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 text-lg">
         <input type="password" name="c_pass" maxlength="20" placeholder="Confirm your new password" class="w-full px-6 py-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 text-lg">
      </div>

      <!-- Submit Button -->
      <div class="flex justify-center">
         <input type="submit" value="Update Now" name="submit" class="px-8 py-4 bg-teal-600 text-white font-semibold rounded-lg shadow-lg hover:bg-teal-700 transition duration-300 ease-in-out text-xl">
      </div>
   </form>
</section>
<br><br><br><br><br><br><br><br><br>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<?php include 'components/footer.php'; ?>

<!-- Custom JS File -->
<script src="js/script.js"></script>

<?php include 'components/message.php'; ?>

</body>
</html>
