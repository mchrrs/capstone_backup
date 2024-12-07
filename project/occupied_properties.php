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
        /* Styling for the entire properties section */
        .occupied-properties {
            padding: 40px;
            background-color: #f8f8f8;
        }

        .occupied-properties .heading {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 30px;
            color: #333;
            text-align: center;
        }

        /* Container for the properties cards */
        .box-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .box {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease-in-out;
        }

        .box:hover {
            transform: translateY(-10px);
        }

        /* Property Details Section */
        .property-details h3 {
            font-size: 1.8rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .property-details p {
            font-size: 1.5rem;
            color: #555;
            margin-bottom: 12px;
        }

        .property-details i {
            margin-right: 10px;
        }

        /* Contract Details Section */
        .contract-details h4 {
            font-weight: bold;
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: #333;
        }

        .contract-details .btn {
            padding: 20px 30px; /* Increased padding for larger button */
            background-color: #008c8c;
            color: white;
            border: none;
            border-radius: 8px; /* Rounded corners for smooth look */
            cursor: pointer;
            text-decoration: none;
            font-size: 1.4rem; /* Larger font size */
            font-weight: bold; /* Make text bold */
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: inline-block; /* Ensure button behaves as a block element */
            text-align: center;
        }

        .contract-details .btn:hover {
            background-color: #006666;
            transform: scale(1.05);
        }

        /* Actions */
        .actions .btn {
            padding: 20px 30px; /* Increased padding for larger button */
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px; /* Rounded corners */
            cursor: pointer;
            text-decoration: none;
            font-size: 1.4rem; /* Increased font size */
            font-weight: bold; /* Bold text */
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .actions .btn:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }

        /* Popup Styling */
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            width: 90%;
            max-width: 1000px;
            height: 90%;
            background: #fff;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            overflow: hidden;
            text-align: center;
        }

        .popup iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .popup img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: auto;
        }

        .popup .close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #f44336;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            cursor: pointer;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
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
                            <a href="/project/messages_index.php" class="btn">Message Admin</a>
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
