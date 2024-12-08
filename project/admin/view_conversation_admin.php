<?php
include '../components/connect.php';

// Check if admin is logged in
if (isset($_COOKIE['admin_id'])) {
    $admin_id = $_COOKIE['admin_id'];
} else {
    header('location:login.php');
    exit;
}

// Fetch the conversation ID from the URL
if (isset($_GET['conversation_id'])) {
    $conversation_id = $_GET['conversation_id'];
} else {
    header('location:admin_messaging.php'); // or wherever your admin messaging page is
    exit;
}

// Fetch the conversation details for this conversation_id
$query = "SELECT * FROM conversations WHERE conversation_id = :conversation_id AND admin_id = :admin_id";
$stmt = $conn->prepare($query);
$stmt->execute(['conversation_id' => $conversation_id, 'admin_id' => $admin_id]);
$conversation = $stmt->fetch();

// Check if the conversation exists and belongs to the admin
if (!$conversation) {
    echo "Conversation not found or you do not have access to it.";
    exit;
}

// Fetch all messages for this conversation
$query = "SELECT m.*, u.name AS sender_name 
          FROM messages m 
          LEFT JOIN users u ON m.sender_id = u.id
          WHERE m.conversation_id = :conversation_id 
          ORDER BY m.created_at ASC";
$stmt = $conn->prepare($query);
$stmt->execute(['conversation_id' => $conversation_id]);
$messages = $stmt->fetchAll();

// Handle sending a new message
if (isset($_POST['send_message'])) {
    $message = $_POST['message'];

    // Admin sends the message
    $sender_id = $admin_id;

    $query = "INSERT INTO messages (conversation_id, sender_id, message) 
              VALUES (:conversation_id, :sender_id, :message)";
    $stmt = $conn->prepare($query);
    $stmt->execute([
        'conversation_id' => $conversation_id,
        'sender_id' => $sender_id,
        'message' => $message
    ]);

    // Reload the page to show the new message
    header("Location: view_conversation_admin.php?conversation_id=$conversation_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversation with User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
    
</head>
<body style="background-color: dimgrey;">
    <?php include '../components/admin_header.php'; ?>
<br><br><br><br>
    <section class="chat-box">
        <div class="admin-chat-box">
            <?php if ($messages): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="message <?php echo ($message['sender_id'] == $admin_id) ? 'sent' : 'received'; ?>">
                        <p><strong><?php echo htmlspecialchars($message['sender_name']); ?>:</strong> <?php echo htmlspecialchars($message['message']); ?></p>
                        <span class="timestamp"><?php echo date('H:i', strtotime($message['created_at'])); ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No messages yet. Be the first to send a message!</p>
            <?php endif; ?>
        </div>

        <!-- Message Input Form -->
        <form method="POST" class="send-message-form">
            <textarea name="message" placeholder="Type your message..." required></textarea>
            <button type="submit" name="send_message">Send</button>
        </form>
    </section>

</body>
</html>
