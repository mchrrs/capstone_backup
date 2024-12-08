<?php
include '../components/connect.php'; // This includes the PDO connection file

// Check if admin is logged in by checking the admin_id cookie
if (isset($_COOKIE['admin_id'])) {
    $admin_id = $_COOKIE['admin_id'];
} else {
    $admin_id = '';
    header('location:login.php');
    exit;
}

// Fetch all conversations for the logged-in admin, including user_name from the users table
$query = "SELECT c.*, u.name AS user_name 
          FROM conversations c 
          LEFT JOIN users u ON c.user_id = u.id
          WHERE c.admin_id = :admin_id";
$stmt = $conn->prepare($query);
$stmt->execute(['admin_id' => $admin_id]);
$conversations = $stmt->fetchAll();

// Search users functionality
$search_query = '';
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
    // Fetch users who match the search query but exclude users who are already part of a conversation
    $user_query = "
        SELECT * 
        FROM users 
        WHERE name LIKE :search_query 
        AND id NOT IN (SELECT user_id FROM conversations WHERE admin_id = :admin_id)";
    $stmt = $conn->prepare($user_query);
    $stmt->execute([
        'search_query' => "%$search_query%", 
        'admin_id' => $admin_id
    ]);
    $users = $stmt->fetchAll();
} else {
    $users = [];
}

// Start conversation functionality
if (isset($_POST['start_conversation'])) {
    $user_id = $_POST['user_id'];
    $conversation_id = create_unique_id();
    // Insert the new conversation
    $query = "INSERT INTO conversations (conversation_id, admin_id, user_id) 
              VALUES (:conversation_id, :admin_id, :user_id)";
    $stmt = $conn->prepare($query);
    $stmt->execute([
        'conversation_id' => $conversation_id,
        'admin_id' => $admin_id,
        'user_id' => $user_id
    ]);
    
    // Redirect to the conversation page
    header("Location: view_conversation_admin.php?conversation_id=$conversation_id");
    exit;
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Messaging</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>
    <?php include '../components/admin_header.php'; ?>
    <br><br><br><br>

    <section class="messaging-box">
        <h1 class="heading">Messages</h1>

        <!-- Search for Users -->
        <form method="POST">
            <input type="text" name="search" placeholder="Search for users..." value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit">Search</button>
        </form>

        <!-- Display Search Results -->
        <?php if ($users): ?>
            <div class="user-list">
                <h3>Select a user to start a conversation:</h3>
                <?php foreach ($users as $user): ?>
                    <div class="user">
                        <p><?php echo htmlspecialchars($user['name']); ?></p>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="start_conversation">Start a Conversation</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>User already exist.</p>
        <?php endif; ?>

        <!-- Display Existing Conversations -->
        <div class="conversations">
            <h3>Existing Conversations:</h3>
            <?php if ($conversations): ?>
                <?php foreach ($conversations as $conversation): ?>
                    <div class="conversation">
                        <h4><?php echo htmlspecialchars($conversation['user_name']); ?></h4>
                        <!-- Add a link to view messages in this conversation -->
                        <a href="view_conversation_admin.php?conversation_id=<?php echo htmlspecialchars($conversation['conversation_id']); ?>">View Conversation</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No conversations yet.</p>
            <?php endif; ?>
        </div>
    </section>

</body>

</html>
