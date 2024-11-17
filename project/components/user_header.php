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
      <a href="dashboard.php" class="logo"><i class="fas fa-house"></i>AMS</a>
   </section>
</nav>

<nav class="navbar nav-2">
   <section class="flex">

      <ul>
         <li><a href="#">Account <i class="fas fa-angle-down"></i></a>
            <ul>
               <?php if ($user_id != '') { ?>
                  <!-- Show these items if the user is logged in -->
                  <li><a href="dashboard.php">Dashboard</a></li>
                  <li><a href="/project/components/user_logout.php" onclick="return confirm('Logout from this website?');">Logout</a></li>
               <?php } else { ?>
                  <!-- Do NOT show Login and Register if the user is logged in -->
                  <li><a href="login.php">Login now</a></li>
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