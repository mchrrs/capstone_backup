<?php
include '../project/components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];  // Get user ID from cookie
} else {
    $user_id = '';
    header('location:login.php');  // Redirect to login if user is not logged in
}

// Fetch user's complaints from the database
$complaints_query = $conn->prepare("SELECT * FROM `complaints` WHERE user_id = ?");
$complaints_query->execute([$user_id]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Complaints</title>

    <!-- font awesome cdn link for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link -->
    <link rel="stylesheet" href="../project/css/style.css">
</head>

<body>
    <!-- Header -->
    <?php include '../project/components/user_header.php'; ?>

    <section class="complaints-section">
        <h1 class="heading">Your Complaints</h1>

        <?php if ($complaints_query->rowCount() > 0): ?>
            <!-- Table for displaying complaints -->
            <div class="complaint-details">
                <table class="complaints-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Submitted At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $count = 1;
                        while ($complaint = $complaints_query->fetch(PDO::FETCH_ASSOC)): 
                        ?>
                            <tr>
                                <td><?= $count++; ?></td>
                                <td><?= htmlspecialchars($complaint['complaint_type']); ?></td>
                                <td><?= htmlspecialchars($complaint['description']); ?></td>
                                <td>
                                    <span class="status-badge <?= strtolower($complaint['status']); ?>">
                                        <?= htmlspecialchars($complaint['status']); ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($complaint['submitted_at']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- Message when no complaints are found -->
            <p class="empty">You have not submitted any complaints yet!</p>
        <?php endif; ?>
    </section>

    <!-- custom js file link -->
    <script src="../project/js/script.js"></script>
</body>

</html>
