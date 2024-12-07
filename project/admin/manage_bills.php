<?php
include '../components/connect.php';

// Check if admin is logged in
if (!isset($_COOKIE['admin_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}

$admin_id = $_COOKIE['admin_id'];

// Fetch users (tenants) for dropdown selection
$select_users = $conn->prepare("SELECT id, name, number FROM users");
$select_users->execute();
$users = $select_users->fetchAll(PDO::FETCH_ASSOC);

// Fetch all bills with tenant details
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$tenant_id_filter = isset($_GET['tenant_id']) ? $_GET['tenant_id'] : '';  // Filter for selected tenant

$query = "SELECT bills.id, users.name, users.number, bills.house_rent, bills.water_bill, bills.electricity_bill, bills.due_date, bills.status
          FROM bills 
          JOIN users ON bills.user_id = users.id";

// Apply filters for tenant selection
if ($tenant_id_filter) {
    $query .= " WHERE bills.user_id = :tenant_id";  // Filter by selected tenant
    if ($status_filter) {
        $query .= " AND bills.status = :status";
    }
} elseif ($status_filter) {
    $query .= " WHERE bills.status = :status";
}

$query .= " ORDER BY bills.due_date ASC";

$select_bills = $conn->prepare($query);

// Bind the parameters for status and tenant filter
if ($tenant_id_filter) {
    if ($status_filter) {
        $select_bills->execute([':tenant_id' => $tenant_id_filter, ':status' => $status_filter]);
    } else {
        $select_bills->execute([':tenant_id' => $tenant_id_filter]);
    }
} else {
    if ($status_filter) {
        $select_bills->execute([':status' => $status_filter]);
    } else {
        $select_bills->execute();
    }
}

$bills = $select_bills->fetchAll(PDO::FETCH_ASSOC);

// Handle status update
if (isset($_POST['action']) && isset($_POST['bill_id'])) {
    $bill_id = $_POST['bill_id'];
    $action = $_POST['action'];
    $new_status = ($action === 'approve') ? 'paid' : 'pending'; // Keep "pending" for rejected bills

    $update_status = $conn->prepare("UPDATE bills SET status = :status WHERE id = :id");
    $update_status->execute([':status' => $new_status, ':id' => $bill_id]);

    // Optionally, notify the user (e.g., by updating a notifications table or sending an email)
    $message = "Bill status updated successfully!";
    $message_type = "success";
}


// Handle form submission for adding a bill
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_bill'])) {
    $tenant_id = $_POST['tenant_id'];
    $house_rent = filter_var($_POST['house_rent'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $water_bill = filter_var($_POST['water_bill'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $electricity_bill = filter_var($_POST['electricity_bill'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $due_date = $_POST['due_date'];

    if (empty($tenant_id) || empty($due_date)) {
        $message = "Please select a tenant and due date.";
        $message_type = "error";
    } elseif ($house_rent <= 0 || $water_bill <= 0 || $electricity_bill <= 0) {
        $message = "All bill amounts must be greater than zero.";
        $message_type = "error";
    } else {
        try {
            $query = "INSERT INTO bills (user_id, house_rent, water_bill, electricity_bill, due_date, status) 
                      VALUES (:user_id, :house_rent, :water_bill, :electricity_bill, :due_date, 'pending')";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':user_id' => $tenant_id,
                ':house_rent' => $house_rent,
                ':water_bill' => $water_bill,
                ':electricity_bill' => $electricity_bill,
                ':due_date' => $due_date
            ]);

            $message = "Bill added successfully!";
            $message_type = "success";
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
            $message_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bills</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include '../components/admin_header.php'; ?>
    <section class="table">
        <h2 class="heading">Bill Payment Status</h2>
        <!-- Dropdown for selecting a specific tenant -->
        <form method="GET" class="tenant-filter">
            <label for="tenant_id">Select Tenant:</label>
            <select name="tenant_id" id="tenant_id" onchange="this.form.submit()">
                <option value="">All Tenants</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id']; ?>" <?= $tenant_id_filter == $user['id'] ? 'selected' : ''; ?>><?= htmlspecialchars($user['name']); ?> (<?= htmlspecialchars($user['number']); ?>)</option>
                <?php endforeach; ?>
            </select>
        </form>

        <!-- Filter by status -->
        <form method="GET" class="status-filter">
            <label for="status">Filter by Status:</label>
            <select name="status" id="status" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="paid" <?= $status_filter === 'paid' ? 'selected' : '' ?>>Paid</option>
                <option value="overdue" <?= $status_filter === 'overdue' ? 'selected' : '' ?>>Overdue</option>
            </select>
        </form>

        <div class="bill-table-wrapper">
            <table class="bill-table">
                <thead>
                    <tr>
                        <th>Tenant Name</th>
                        <th>Tenant Number</th>
                        <th>House Rent</th>
                        <th>Water Bill</th>
                        <th>Electricity Bill</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($bills) > 0): ?>
                        <?php foreach ($bills as $bill): ?>
                            <tr>
                                <td><?= htmlspecialchars($bill['name']); ?></td>
                                <td><?= htmlspecialchars($bill['number']); ?></td>
                                <td>₱<?= number_format($bill['house_rent'], 2); ?></td>
                                <td>₱<?= number_format($bill['water_bill'], 2); ?></td>
                                <td>₱<?= number_format($bill['electricity_bill'], 2); ?></td>
                                <td><?= htmlspecialchars($bill['due_date']); ?></td>
                                <td><span class="status <?= strtolower($bill['status']); ?>"><?= ucfirst($bill['status']); ?></span></td>
                                <td>
                                    <?php if ($bill['status'] === 'pending'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="bill_id" value="<?= $bill['id']; ?>">
                                            <button type="submit" name="action" value="approve" class="btn btn-success">Approve</button>
                                            <button type="submit" name="action" value="reject" class="btn btn-danger">Reject</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="no-action">No actions available</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">No bills available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <a href="validate_payment.php" class="btn btn-primary btn-validate">Go to Validate Payments</a>
    </section>

    <br><br><br>
    <section class="add-bills">
        <h1 class="heading">Manage Bills</h1>
        <br><br>
        <form class="add-bills-form" action="" method="POST">
            <div class="form-group">
                <label for="tenant_id">Select Tenant:</label>
                <select name="tenant_id" id="tenant_id" required>
                    <option value="" disabled selected>Select a tenant</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id']; ?>"><?= htmlspecialchars($user['name']); ?> (<?= htmlspecialchars($user['number']); ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="house_rent">House Rent:</label>
                <input type="number" name="house_rent" min="0" required>
            </div>

            <div class="form-group">
                <label for="water_bill">Water Bill:</label>
                <input type="number" name="water_bill" min="0" required>
            </div>

            <div class="form-group">
                <label for="electricity_bill">Electricity Bill:</label>
                <input type="number" name="electricity_bill" min="0" required>
            </div>

            <div class="form-group">
                <label for="due_date">Due Date:</label>
                <input type="date" name="due_date" required>
            </div>

            <button type="submit" name="add_bill">Add Bill</button>
        </form>
    </section>

    <?php if (isset($message)): ?>
        <script>
            Swal.fire({
                icon: '<?= $message_type; ?>',
                title: '<?= htmlspecialchars($message); ?>',
                showConfirmButton: true,
            });
        </script>
    <?php endif; ?>

    <?php include '../components/message.php'; ?>
</body>

</html>