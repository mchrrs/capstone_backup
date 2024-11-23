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
    <style>
        /* Popup styling */
        /* Popup styling */
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            width: 80%;
            max-width: 800px;
            height: 80%;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            overflow: hidden;
            text-align: center;
            /* Center align the content */
        }

        .popup iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .popup img {
            max-width: 100%;
            /* Ensure the image scales to the popup width */
            max-height: 100%;
            /* Ensure the image scales to the popup height */
            width: auto;
            /* Maintain aspect ratio */
            height: auto;
            /* Maintain aspect ratio */
            margin: auto;
            /* Center the image */
            display: block;
        }

        .popup .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #f44336;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            text-align: center;
            line-height: 30px;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
    </style>
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
                                <p>
                                    <button
                                        class="btn"
                                        onclick="previewContract('<?= htmlspecialchars('admin/uploaded_contracts/' . $fetch_property['contract']); ?>')">
                                        Preview Contract
                                    </button>
                                </p>
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

    <!-- Popup Window -->
    <div class="overlay" id="overlay"></div>
    <div class="popup" id="popup">
        <button class="close-btn" onclick="closePopup()">×</button>
        <iframe id="popup-iframe" src=""></iframe>
    </div>

    <script>
        // Function to preview contract
        function previewContract(filePath) {
            const popup = document.getElementById('popup');
            const overlay = document.getElementById('overlay');
            const iframe = document.getElementById('popup-iframe');

            // Prepend base directory if required
            const fullPath = filePath.startsWith('/') || filePath.startsWith('http') ?
                filePath :
                `/project/${filePath}`;

            // Handle file preview
            const ext = fullPath.split('.').pop().toLowerCase();
            if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
                iframe.style.display = 'none';
                const img = document.createElement('img');
                img.src = fullPath;
                popup.appendChild(img);
            } else {
                iframe.src = fullPath;
                iframe.style.display = 'block';
            }

            overlay.style.display = 'block';
            popup.style.display = 'block';
        }

        // Function to close popup
        function closePopup() {
            const popup = document.getElementById('popup');
            const overlay = document.getElementById('overlay');
            popup.style.display = 'none';
            overlay.style.display = 'none';

            // Remove dynamically added image
            const img = popup.querySelector('img');
            if (img) img.remove();
        }
    </script>

    <?php include '../project/components/message.php'; ?>

</body>

</html>