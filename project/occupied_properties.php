<?php
include '../project/components/connect.php';

// Check if the user is logged in through the cookie
if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
    header('location:login.php');
    exit; // Prevent further code execution if not logged in
}

// Fetch the properties and contract for the logged-in user
$select_properties = $conn->prepare("
    SELECT * FROM `occupied_properties`
    WHERE `name` = (SELECT `name` FROM `users` WHERE `id` = ? LIMIT 1)"); // Using user's name to match property
$select_properties->execute([$user_id]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owned Properties</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="/project/css/style.css">
</head>

<body>

    <!-- Header section -->
    <?php include '../project/components/user_header.php'; ?>

    <!-- Owned properties section -->
    <section class="occupied-properties">
        <h1 class="heading">Your Owned Properties</h1>

        <div class="box-container">

            <?php
            // Check if there are any properties fetched
            if ($select_properties->rowCount() > 0) {
                // Loop through each fetched property and display it
                while ($fetch_property = $select_properties->fetch(PDO::FETCH_ASSOC)) {
            ?>
                    <div class="box">

                        <!-- Property Details Section -->
                        <div class="property-details">
                            <h3><?= htmlspecialchars($fetch_property['property_name']); ?></h3>
                            <br>
                            <p><i class="fa-solid fa-user"></i><?= htmlspecialchars($fetch_property['name']); ?></p>
                            <p><i class="fas fa-phone-alt"></i> <?= htmlspecialchars($fetch_property['number']); ?></p>
                            <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($fetch_property['email']); ?></p>
                            <p><i class="fas fa-calendar-check"></i> Status: <?= htmlspecialchars($fetch_property['status']); ?></p>
                        </div>
<br><br>
                        <!-- Contract Details Section -->
                        <div class="contract-details">
                            <?php if (!empty($fetch_property['contract'])) { ?>
                                <h4>Contract Details</h4>
                                <p><a href="<?= htmlspecialchars($fetch_property['contract']); ?>" class="btn" target="_blank">View Contract</a></p>
                            <?php } else { ?>
                                <p>No contract available for this property.</p>
                            <?php } ?>
                        </div>

                        <!-- Action Buttons -->
                        <div class="actions">
                            <a href="messages.php?property_id=<?= $fetch_property['id']; ?>" class="btn">Message Admins</a>
                            <a href="complaints.php?property_id=<?= $fetch_property['id']; ?>" class="btn">Submit a Complaint</a>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<p class="empty">You have no owned properties!</p>';
            }
            ?>

        </div>

    </section>

    <script src="../js/user_script.js"></script>

    <?php include '../project/components/message.php'; ?>

</body>

</html>
