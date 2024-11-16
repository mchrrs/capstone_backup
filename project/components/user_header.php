<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="icon" href="/project/images/home-button.png" type="image/png">

   <!-- header section starts  -->

<header class="header">

<nav class="navbar nav-1">
   <section class="flex">
      <a href="home.php" class="logo"><i class="fas fa-house"></i>MyHome</a>
   </section>
</nav>

<nav class="navbar nav-2">
   <section class="flex">
      <div id="menu-btn" class="fas fa-bars"></div>

      <div class="menu">
         <ul>
            <li><a href="listings.php">Properties</i></a></li>
            <li><a href="search.php">Filter search</a></li>
            
            <li><a href="#">Help<i class="fas fa-angle-down"></i></a>
               <ul>
                  <li><a href="about.php">About us</a></i></li>
                  <li><a href="contact.php">Contact us</a></i></li>
                  <li><a href="contact.php#faq">FAQ</a></i></li>
               </ul>
            </li>
         </ul>
      </div>
      <ul> 
         <li><a href="saved.php">Saved <i class="far fa-heart"></i></a></li>
         <li><a href="#">Account <i class="fas fa-angle-down"></i></a>
            <ul>
               <?php if ($user_id != '') { ?>
                  <!-- Show these items if the user is logged in -->
                  <li><a href="dashboard.php">Dashboard</a></li>
                  <li><a href="/project/components/user_logout.php" onclick="return confirm('Logout from this website?');">Logout</a></li>
               <?php } else { ?>
                  <!-- Do NOT show Login and Register if the user is logged in -->
                  <li><a href="login.php">Login now</a></li>
                  <li><a href="register.php">Register new</a></li>
               <?php } ?>
            </ul>
         </li>
      </ul>
   </section>
</nav>

</header>

<!-- header section ends -->
</head>
<body>
   
</body>
</html>