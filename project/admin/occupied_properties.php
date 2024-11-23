<?php
include '../components/connect.php';

// Handle property deletion
if (isset($_POST['delete_property'])) {
    $property_id = filter_var($_POST['property_id'], FILTER_SANITIZE_STRING);

    try {
        // Delete the property from the `occupied_properties` table
        $delete_property = $conn->prepare("DELETE FROM `occupied_properties` WHERE id = ?");
        if ($delete_property->execute([$property_id])) {
            // Update the status in the `property` table
            $update_property = $conn->prepare("UPDATE `property` SET status = 'Available' WHERE id = ?");
            $update_property->execute([$property_id]);

            $success_msg = "Property deleted successfully, and status updated.";
        } else {
            $error_msg = "Failed to delete the property.";
        }
    } catch (PDOException $e) {
        $error_msg = "An error occurred: " . $e->getMessage();
    }
}

// Fetch occupied properties with tenant details and property status
$select_properties = $conn->prepare("
    SELECT 
        op.id, op.property_name, op.name, op.number, op.email, op.occupants, op.contract, op.status
    FROM `occupied_properties` op
");
$select_properties->execute();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Occupied Properties</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="/project/css/admin_style.css">
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
    <?php include '../components/admin_header.php'; ?>

    <section class="occupied-properties">
        <h1 class="heading">Occupied Properties</h1>

        <?php if (isset($success_msg)): ?>
            <p class="success"><?= htmlspecialchars($success_msg); ?></p>
        <?php elseif (isset($error_msg)): ?>
            <p class="error"><?= htmlspecialchars($error_msg); ?></p>
        <?php endif; ?>

        <?php if ($select_properties->rowCount() > 0): ?>
            <div class="table-container">
                <table class="occupied-properties-table">
                    <thead>
                        <tr>
                            <th>Property Name</th>
                            <th>Tenant Name</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Occupants</th>
                            <th>Contract</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($property = $select_properties->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?= htmlspecialchars($property['property_name']); ?></td>
                                <td><?= htmlspecialchars($property['name']); ?></td>
                                <td><?= htmlspecialchars($property['number']); ?></td>
                                <td><?= htmlspecialchars($property['email']); ?></td>
                                <td><?= htmlspecialchars($property['occupants']); ?></td>
                                <td>
                                    <?php if (!empty($property['contract'])): ?>
                                        <button
                                            class="btn preview-btn"
                                            onclick="previewContract('<?= htmlspecialchars('admin/uploaded_contracts/' . $property['contract']); ?>')">
                                            Preview Contract
                                        </button>
                                    <?php else: ?>
                                        <span>No Contract</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($property['status']); ?></td>
                                <td>
                                    <form action="" method="POST" style="display:inline;">
                                        <input type="hidden" name="property_id" value="<?= htmlspecialchars($property['id']); ?>">
                                        <button type="submit" name="delete_property" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this property?');">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="empty">No occupied properties found.</p>
        <?php endif; ?>
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
</body>

</html>