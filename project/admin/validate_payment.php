<?php
include '../components/connect.php';

if (isset($_COOKIE['admin_id'])) {
    $admin_id = $_COOKIE['admin_id'];
} else {
    $admin_id = '';
    header('location:admin_login.php');
    exit;
}

$user_filter = ''; // Default value, will be updated by the filter

if (isset($_POST['user_filter'])) {
    $user_filter = $_POST['user_filter'];
}

try {
    // Fetch all users for the filter dropdown
    $user_sql = "SELECT id, name FROM users";
    $user_stmt = $conn->prepare($user_sql);
    $user_stmt->execute();
    $users = $user_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch receipts based on selected user filter
    if ($user_filter) {
        $sql = "SELECT 
                    r.id AS receipt_id, 
                    r.receipt_file, 
                    r.remarks, 
                    r.submitted_at, 
                    u.name AS user_name
                FROM receipts r
                JOIN users u ON r.user_id = u.id
                WHERE r.user_id = :user_filter";
    } else {
        $sql = "SELECT 
                    r.id AS receipt_id, 
                    r.receipt_file, 
                    r.remarks, 
                    r.submitted_at, 
                    u.name AS user_name
                FROM receipts r
                JOIN users u ON r.user_id = u.id";
    }

    $stmt = $conn->prepare($sql);

    if ($user_filter) {
        $stmt->bindParam(':user_filter', $user_filter, PDO::PARAM_STR);
    }

    $stmt->execute();
    $receipts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching receipts: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Payments</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>
    <?php include '../components/admin_header.php'; ?>
    <br><br><br><br>
    <!-- Filter Dropdown -->
    <section class="receipt-filter-container">
        <h1 class="heading">Filter Receipts by User</h1>
        <form method="POST" action="">
            <label for="user_filter">Select User:</label>
            <select name="user_filter" id="user_filter">
                <option value="">All Users</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?php echo $user['id']; ?>" <?php echo ($user['id'] == $user_filter) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($user['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Filter</button>
        </form>
    </section>
    <br><br><br>
    <section class="receipt-table-container">
        <h1 class="heading">All Payments</h1>

        <table class="receipt-table">
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>Receipt</th>
                    <th>Remarks</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($receipts)): ?>
                    <?php foreach ($receipts as $receipt): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($receipt['user_name']); ?></td>
                            <td><a href="javascript:void(0);" class="view-receipt" data-receipt="<?php echo htmlspecialchars($receipt['receipt_file']); ?>">View Receipt</a></td>
                            <td><?php echo $receipt['remarks'] ? htmlspecialchars($receipt['remarks']) : 'No remarks'; ?></td>
                            <td><?php echo date('F j, Y, g:i A', strtotime($receipt['submitted_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No receipts to display.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <!-- Popup Window -->
    <div class="overlay" id="overlay"></div>
    <div class="popup" id="popup">
        <button class="close-btn" onclick="closePopup()">×</button>
        <iframe id="popup-iframe" src=""></iframe>
    </div>

    <script>
        function previewReceipt(filePath) {
            const popup = document.getElementById('popup');
            const overlay = document.getElementById('overlay');
            const iframe = document.getElementById('popup-iframe');

            // Ensure the file path is correct
            let fullPath = filePath;

            // If the file path doesn't start with /, prepend the correct directory path
            if (!filePath.startsWith('http://') && !filePath.startsWith('https://') && !filePath.startsWith('/')) {
                fullPath = `/project/receipts/${filePath}`; // Update this if needed
            }

            // Check the file extension to determine how to display it
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

        // Attach the event listener for view receipt
        const receiptLinks = document.querySelectorAll('.view-receipt');
        receiptLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                const receiptFile = this.getAttribute('data-receipt');
                previewReceipt(receiptFile);
            });
        });
    </script>
</body>

</html>

<?php
$stmt = null;
$conn = null;
?>