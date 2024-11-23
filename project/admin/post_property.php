<?php

include '../components/connect.php';

// Initialize message arrays
$warning_msg = [];
$success_msg = [];

if (isset($_COOKIE['admin_id'])) {
   $user_id = $_COOKIE['admin_id'];
} else {
   $user_id = '';
   header('location:login.php');
}

if (isset($_POST['post'])) {

   $id = create_unique_id();
   $property_name = $_POST['property_name'];
   $property_name = filter_var($property_name, FILTER_SANITIZE_STRING);
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);
   $address = $_POST['address'];
   $address = filter_var($address, FILTER_SANITIZE_STRING);
   $offer = $_POST['offer'];
   $offer = filter_var($offer, FILTER_SANITIZE_STRING);
   $type = $_POST['type'];
   $type = filter_var($type, FILTER_SANITIZE_STRING);
   $status = $_POST['status'];
   $status = filter_var($status, FILTER_SANITIZE_STRING);
   $furnished = $_POST['furnished'];
   $furnished = filter_var($furnished, FILTER_SANITIZE_STRING);
   $bedroom = $_POST['bedroom'];
   $bedroom = filter_var($bedroom, FILTER_SANITIZE_STRING);
   $bathroom = $_POST['bathroom'];
   $bathroom = filter_var($bathroom, FILTER_SANITIZE_STRING);
   $carpet = $_POST['carpet'];
   $carpet = filter_var($carpet, FILTER_SANITIZE_STRING);
   $age = $_POST['age'];
   $age = filter_var($age, FILTER_SANITIZE_STRING);
   $total_floors = $_POST['total_floors'];
   $total_floors = filter_var($total_floors, FILTER_SANITIZE_STRING);
   $room_floor = $_POST['room_floor'];
   $room_floor = filter_var($room_floor, FILTER_SANITIZE_STRING);
   $description = $_POST['description'];
   $description = filter_var($description, FILTER_SANITIZE_STRING);

   // Handle image uploads and file sizes
   $image_fields = ['image_01', 'image_02', 'image_03', 'image_04', 'image_05'];
   $uploaded_images = [];
   foreach ($image_fields as $image_field) {
      if (!empty($_FILES[$image_field]['name'])) {
         $image_name = $_FILES[$image_field]['name'];
         $image_name = filter_var($image_name, FILTER_SANITIZE_STRING);
         $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
         $rename_image = create_unique_id() . '.' . $image_ext;
         $image_tmp_name = $_FILES[$image_field]['tmp_name'];
         $image_size = $_FILES[$image_field]['size'];
         $image_folder = 'uploaded_files/' . $rename_image;

         if ($image_size > 2000000) {
            $warning_msg[] = "{$image_field} size is too large!";
         } else {
            move_uploaded_file($image_tmp_name, $image_folder);
            $uploaded_images[$image_field] = $rename_image;
         }
      } else {
         $uploaded_images[$image_field] = '';
      }
   }

   // If the image 01 is valid, proceed with posting the property
   if (empty($warning_msg)) {
      $insert_property = $conn->prepare("INSERT INTO `property`(id, user_id, property_name, address, price, type, offer, status, furnished, bedroom, bathroom, carpet, age, total_floors, room_floor, image_01, image_02, image_03, image_04, image_05, description) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
      $insert_property->execute([
         $id,
         $user_id,
         $property_name,
         $address,
         $price,
         $type,
         $offer,
         $status,
         $furnished,
         $bedroom,
         $bathroom,
         $carpet,
         $age,
         $total_floors,
         $room_floor,
         $uploaded_images['image_01'],
         $uploaded_images['image_02'],
         $uploaded_images['image_03'],
         $uploaded_images['image_04'],
         $uploaded_images['image_05'],
         $description
      ]);

      $success_msg[] = 'Property posted successfully!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Post Property</title>

   <!-- font awesome cdn link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

   <?php include '../components/admin_header.php'; ?>

   <section class="property-form">

      <form action="" method="POST" enctype="multipart/form-data">
         <h3>Property Details</h3>

         <div class="box">
            <p>Property Name <span>*</span></p>
            <input type="text" name="property_name" required maxlength="50" placeholder="Enter property name" class="input">
         </div>

         <div class="flex">
            <div class="box">
               <p>Property Price <span>*</span></p>
               <input type="number" name="price" required min="0" max="9999999999" maxlength="10" placeholder="Enter property price" class="input">
            </div>

            <div class="box">
               <p>Property Address <span>*</span></p>
               <input type="text" name="address" required maxlength="100" placeholder="Enter property full address" class="input">
            </div>
         </div>

         <div class="flex">
            <div class="box">
               <p>Offer Type <span>*</span></p>
               <select name="offer" required class="input">
                  <option value="">Select an Option</option>
                  <option value="Sale">Sale</option>
                  <option value="Resale">Resale</option>
                  <option value="Rent">Rent</option>
               </select>
            </div>

            <div class="box">
               <p>Property Type <span>*</span></p>
               <select name="type" required class="input">
                  <option value="">Select an Option</option>
                  <option value="House">House</option>
                  <option value="Shop">Shop</option>
               </select>
            </div>

            <div class="box">
               <p>Property Status <span>*</span></p>
               <select name="status" required class="input">
                  <option value="">Select an Option</option>
                  <option value="Occupied">Occupied</option>
                  <option value="Available">Available</option>
               </select>
            </div>
         </div>

         <div class="flex">
            <div class="box">
               <p>Furnished Status <span>*</span></p>
               <select name="furnished" required class="input">
               <option value="">Select an Option</option>
                  <option value="furnished">Furnished</option>
                  <option value="semi-furnished">Semi-furnished</option>
                  <option value="unfurnished">Unfurnished</option>
               </select>
            </div>

            <div class="box">
               <p>How many Bedrooms <span>*</span></p>
               <select name="bedroom" required class="input">
                  <option value="0">0 Bedroom</option>
                  <option value="1" selected>1 Bedroom</option>
                  <option value="2">2 Bedrooms</option>
                  <option value="3">3 Bedrooms</option>
                  <option value="4">4 Bedrooms</option>
                  <option value="5">5 Bedrooms</option>
                  <option value="6">6 Bedrooms</option>
                  <option value="7">7 Bedrooms</option>
                  <option value="8">8 Bedrooms</option>
                  <option value="9">9 Bedrooms</option>
               </select>
            </div>

            <div class="box">
               <p>How many Bathrooms <span>*</span></p>
               <select name="bathroom" required class="input">
                  <option value="1">1 Bathroom</option>
                  <option value="2">2 Bathrooms</option>
                  <option value="3">3 Bathrooms</option>
                  <option value="4">4 Bathrooms</option>
                  <option value="5">5 Bathrooms</option>
                  <option value="6">6 Bathrooms</option>
                  <option value="7">7 Bathrooms</option>
                  <option value="8">8 Bathrooms</option>
                  <option value="9">9 Bathrooms</option>
               </select>
            </div>
         </div>

         <div class="flex">
            <div class="box">
               <p>Carpet Area (sq.ft) <span>*</span></p>
               <input type="number" name="carpet" required min="1" max="999999" maxlength="10" placeholder="Enter carpet area" class="input">
            </div>

            <div class="box">
               <p>Property Age <span>*</span></p>
               <input type="number" name="age" required min="0" max="99" maxlength="2" placeholder="Enter property age" class="input">
            </div>

            <div class="box">
               <p>Total Floors <span>*</span></p>
               <input type="number" name="total_floors" required min="0" max="99" maxlength="2" placeholder="Enter total floors" class="input">
            </div>

            <div class="box">
               <p>Floor Number <span>*</span></p>
               <input type="number" name="room_floor" required min="0" max="99" maxlength="2" placeholder="Enter property floor number" class="input">
            </div>
         </div>

         <div class="box">
            <p>Property Description <span>*</span></p>
            <textarea name="description" required class="input" maxlength="1000" cols="30" rows="6" placeholder="Describe the property..."></textarea>
         </div>

         <div class="box">
            <p>Image 01 <span>*</span></p>
            <input type="file" name="image_01" class="input" accept="image/*" required>
         </div>

         <div class="box">
            <p>Image 02</p>
            <input type="file" name="image_02" class="input" accept="image/*">
         </div>

         <div class="box">
            <p>Image 03 </p>
            <input type="file" name="image_03" class="input" accept="image/*">
         </div>

         <div class="box">
            <p>Image 04</p>
            <input type="file" name="image_04" class="input" accept="image/*">
         </div>
         <div class="box">
            <p>Image 05</p>
            <input type="file" name="image_05" class="input" accept="image/*">
         </div>
         <input type="submit" value="post property" class="btn" name="post">


      </form>


   </section>

   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

   <!-- custom js file link -->
   <script src="../js/admin_script.js"></script>

   <?php include '../components/message.php'; ?>

</body>

</html>