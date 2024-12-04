<?php
include '../project/components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
    header('location:login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $complaint_type = $_POST['complaint_type'] ?? '';
    $description = $_POST['description'] ?? '';

    if (!empty($complaint_type) && !empty($description)) {
        $stmt = $conn->prepare("INSERT INTO complaints (user_id, complaint_type, description) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $complaint_type, $description]);
        $message = "Complaint submitted successfully!";
    } else {
        $message = "Please fill in all fields!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit an Issue</title>
    <link rel="stylesheet" href="/project/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>

<body>
<?php include '../project/components/user_header.php'; ?>

<section class="complaint-details">
    <h1>Submit an Issue</h1>
    <?php if (!empty($message)) { echo "<p>$message</p>"; } ?>
    <form action="" method="POST">
        <label for="complaint_type">Complaint Type:</label>
        <select name="complaint_type" id="complaint_type" required>
            <option value="" disabled selected>Select a type</option>
            <option value="Maintenance">Maintenance</option>
            <option value="Noise">Noise</option>
            <option value="Cleanliness">Cleanliness</option>
            <option value="Safety">Safety</option>
            <option value="Utility">Utility</option>
            <option value="Payment">Payment</option>
            <option value="General">General</option>
        </select>

        <label for="description">Description:</label>
        <textarea name="description" id="description" rows="5" placeholder="Provide a detailed description of your issue..." required></textarea>

        <button type="submit">Submit Complaint</button>
    </form>
</section>


</body>

</html>
