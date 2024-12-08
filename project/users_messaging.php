<?php
include 'components/connect.php'; // This includes the PDO connection file

// Check if user is logged in by checking the user_id cookie
if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
    header('location:login.php');
    exit;
}

// Fetch all conversations for the logged-in user, including the admin_name for easy display
$query = "SELECT c.*, a.name AS admin_name 
          FROM conversations c 
          LEFT JOIN admins a ON c.admin_id = a.id 
          WHERE c.user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$conversations = $stmt->fetchAll();

// Fetch list of admins for the user to select
$admin_query = "SELECT * FROM admins";
$admin_stmt = $conn->prepare($admin_query);
$admin_stmt->execute();
$admins = $admin_stmt->fetchAll();

// Start a conversation functionality
if (isset($_POST['start_conversation'])) {
    $admin_id = $_POST['admin_id'];
    $conversation_id = create_unique_id();  // Function to create a unique ID for the conversation

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
    header("Location: view_conversation_users.php?conversation_id=$conversation_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Messaging</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include 'components/user_header.php'; ?>
    <br><br><br><br>
    <section class="messaging-box">
        <h1 class="heading">Messages</h1>
        <!-- Start a New Conversation -->
        <div class="start-conversation">
            <h3>Select an Admin to Message:</h3>
            <form method="POST">
                <select name="admin_id" required>
                    <option value="">-- Select Admin --</option>
                    <?php foreach ($admins as $admin): ?>
                        <option value="<?php echo $admin['id']; ?>"><?php echo htmlspecialchars($admin['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="start_conversation">Start a Conversation</button>
            </form>
        </div>
        <!-- Display Existing Conversations -->
        <div class="conversations">
            <?php if ($conversations): ?>
                <?php foreach ($conversations as $conversation): ?>
                    <div class="conversation">
                        <h3><?php echo htmlspecialchars($conversation['admin_name']); ?></h3>
                        <!-- Link to view the full conversation -->
                        <a href="view_conversation_users.php?conversation_id=<?php echo htmlspecialchars($conversation['conversation_id']); ?>">View Conversation</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No conversations yet. Feel free to start a conversation with an admin!</p>
            <?php endif; ?>
        </div>

        
    </section>
</body>

</html>
