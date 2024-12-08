<?php
include '../components/connect.php';

$alert_message = null; // Variable to store SweetAlert message

if (!isset($_GET['get_id']) || empty($_GET['get_id'])) {
    header('Location: listings.php');
    exit;
}

$property_id = filter_var($_GET['get_id'], FILTER_SANITIZE_STRING);

$select_users = $conn->prepare("SELECT id, name, number FROM `users`");
$select_users->execute();
$users = $select_users->fetchAll(PDO::FETCH_ASSOC);

$select_property = $conn->prepare("SELECT * FROM `property` WHERE id = ?");
$select_property->execute([$property_id]);
$property = $select_property->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    header('Location: listings.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tenant_id = $_POST['tenant_id'];
    $occupants = $_POST['occupants'];
    $status = $_POST['status'];
    $contract_image = $_FILES['contract_image'];

    if ($contract_image['error'] == 0) {
        $contract_image_name = $contract_image['name'];
        $contract_image_tmp = $contract_image['tmp_name'];
        $contract_image_ext = pathinfo($contract_image_name, PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];

        if (in_array(strtolower($contract_image_ext), $allowed_extensions)) {
            $contract_image_new_name = uniqid('', true) . '.' . $contract_image_ext;
            move_uploaded_file($contract_image_tmp, 'uploaded_contracts/' . $contract_image_new_name);
        } else {
            $alert_message = 'Invalid file type. Only JPG, JPEG, PNG, and PDF files are allowed.';
        }
    } else {
        $alert_message = 'Please upload a valid contract image.';
    }

    if ($alert_message === null) {
        $select_tenant = $conn->prepare("SELECT name, number, email FROM `users` WHERE id = ?");
        $select_tenant->execute([$tenant_id]);
        $tenant = $select_tenant->fetch(PDO::FETCH_ASSOC);

        if ($tenant && $occupants && isset($contract_image_new_name) && $status) {
            $update_property = $conn->prepare("UPDATE `property` SET status = 'occupied' WHERE id = ?");
            $update_property->execute([$property_id]);

            $insert_occupied = $conn->prepare("
                INSERT INTO `occupied_properties` (property_name, name, occupants, contract, email, number, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $insert_occupied->execute([
                $property['property_name'],
                $tenant['name'],
                $occupants,
                $contract_image_new_name,
                $tenant['email'],
                $tenant['number'],
                $status
            ]);

            // Only show success message and redirect after insert
            $alert_message = 'Insert Successful!';
            header("Location: occupied_properties.php");
            exit;
        } else {
            $alert_message = 'Please fill in all fields and upload a valid contract image.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Property</title>
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <!-- Include SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include '../components/admin_header.php'; ?>
    <section class="set-property-form">
        <h1 class="heading">Set Property for Tenants</h1>

        <div class="property-details">
            <form action="" method="POST" enctype="multipart/form-data">
                <h3><?= htmlspecialchars($property['property_name']); ?></h3>
                <p><strong>Location:</strong> <?= htmlspecialchars($property['address']); ?></p>
                <p><strong>Price:</strong> <?= htmlspecialchars($property['price']); ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($property['status']); ?></p>
                <br><br><br>
                <label for="tenant_id">Select Tenant:</label>
                <select name="tenant_id" id="tenant_id" required>
                    <option value="" disabled selected>Select a tenant</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id']; ?>"><?= htmlspecialchars($user['name']); ?> - <?= htmlspecialchars($user['number']); ?></option>
                    <?php endforeach; ?>
                </select>
                <br><br><br>
                <label for="occupants">Number of Occupants:</label>
                <input type="number" name="occupants" required min="1" max="99">
                <br><br><br>
                <label for="contract_image">Upload Contract (Image or PDF):</label>
                <input type="file" name="contract_image" accept="image/*, .pdf" required>
                <br><br><br>
                <label for="status">Status:</label>
                <input type="text" name="status" placeholder="e.g., Active, Terminated" required>

                <input type="submit" value="Assign Property" class="btn">
            </form>
        </div>
    </section>

    <!-- SweetAlert script -->
    <script>
        <?php if ($alert_message !== null): ?>
            Swal.fire({
                title: '<?= $alert_message ?>',
                icon: '<?= strpos($alert_message, 'Invalid') !== false || strpos($alert_message, 'Please') !== false ? 'error' : 'success' ?>',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>
    </script>
</body>

</html>
