<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/project/images/home-button.png" type="image/png">

    <!-- Tailwind CSS CDN Link -->
    <script src="https://cdn.tailwindcss.com"></script>

    <title>Dashboard Header</title>

</head>

<body class="bg-gray-100">

    <!-- Header Section Starts -->
    <header class="bg-teal-600 text-white">
        <nav class="flex items-center justify-between p-4 max-w-screen-xl mx-auto">

            <!-- Logo Section with Bigger Font -->
            <a href="dashboard.php" class="text-3xl font-semibold flex items-center space-x-2">
                <i class="fas fa-house"></i>
                <span>AMS</span>
            </a>

            <!-- Navigation Section (Desktop & Mobile) -->
            <div class="flex items-center space-x-6">
                <!-- Home Button: Increased Font Size & Padding -->
                <a href="#" class="text-xl px-6 py-3 rounded-lg hover:bg-teal-700 transition duration-300 hover:text-teal-200">Home</a>

                <!-- Account Link with Larger Font and Padding -->
                <div class="relative group">
                    <a href="#" class="text-xl px-6 py-3 rounded-lg flex items-center hover:bg-teal-700 transition duration-300 hover:text-teal-200">
                        Account
                        <i class="fas fa-angle-down ml-2"></i>
                    </a>
                    <!-- Dropdown Menu (Only Visible on Hover) -->
                    <ul class="absolute left-0 hidden mt-2 space-y-2 bg-teal-600 text-white group-hover:block w-48 p-3 rounded-lg shadow-md">
                        <?php if ($user_id != '') { ?>
                            <li><a href="dashboard.php" class="block px-4 py-2 hover:bg-teal-700 rounded-lg text-lg">Dashboard</a></li>
                            <li><a href="/project/components/user_logout.php" onclick="return confirm('Logout from this website?');" class="block px-4 py-2 hover:bg-teal-700 rounded-lg text-lg">Logout</a></li>
                        <?php } else { ?>
                            <li><a href="login.php" class="block px-4 py-2 hover:bg-teal-700 rounded-lg text-lg">Login now</a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>

            <!-- Mobile Hamburger Menu (Hidden on Desktop) -->
            <div class="md:hidden flex items-center">
                <button id="mobile-menu-toggle" class="text-white">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>

        </nav>

        <!-- Mobile Navigation Menu (Hidden by default) -->
        <div id="mobile-menu" class="md:hidden hidden bg-teal-600 text-white p-4 space-y-4">
            <ul>
                <li><a href="#" class="block px-4 py-2 hover:bg-teal-700 rounded-lg text-lg">Home</a></li>
                <li class="relative group">
                    <a href="#" class="block px-4 py-2 hover:bg-teal-700 rounded-lg flex items-center text-lg">
                        Account
                        <i class="fas fa-angle-down ml-2"></i>
                    </a>
                    <ul class="absolute left-0 hidden mt-2 space-y-2 bg-teal-600 text-white group-hover:block w-48 p-3 rounded-lg shadow-md">
                        <?php if ($user_id != '') { ?>
                            <li><a href="dashboard.php" class="block px-4 py-2 hover:bg-teal-700 rounded-lg text-lg">Dashboard</a></li>
                            <li><a href="/project/components/user_logout.php" onclick="return confirm('Logout from this website?');" class="block px-4 py-2 hover:bg-teal-700 rounded-lg text-lg">Logout</a></li>
                        <?php } else { ?>
                            <li><a href="login.php" class="block px-4 py-2 hover:bg-teal-700 rounded-lg text-lg">Login now</a></li>
                        <?php } ?>
                    </ul>
                </li>
            </ul>
        </div>

    </header>
    <!-- Header Section Ends -->

</body>

</html>
