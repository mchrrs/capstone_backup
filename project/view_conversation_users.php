<?php
include 'components/connect.php';

// Check if user is logged in
if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    header('location:login.php');
    exit;
}

// Fetch the conversation ID from the URL
if (isset($_GET['conversation_id'])) {
    $conversation_id = $_GET['conversation_id'];
} else {
    header('location:user_messaging.php'); // or wherever your user messaging page is
    exit;
}

// Fetch the conversation details for this conversation_id
$query = "SELECT * FROM conversations WHERE conversation_id = :conversation_id AND user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->execute(['conversation_id' => $conversation_id, 'user_id' => $user_id]);
$conversation = $stmt->fetch();

// Check if the conversation exists and belongs to the user
if (!$conversation) {
    echo "Conversation not found or you do not have access to it.";
    exit;
}

// Fetch all messages for this conversation
$query = "SELECT m.*, a.name AS sender_name 
          FROM messages m 
          LEFT JOIN admins a ON m.sender_id = a.id
          WHERE m.conversation_id = :conversation_id 
          ORDER BY m.created_at ASC";
$stmt = $conn->prepare($query);
$stmt->execute(['conversation_id' => $conversation_id]);
$messages = $stmt->fetchAll();

// Handle sending a new message
if (isset($_POST['send_message'])) {
    $message = $_POST['message'];

    // User sends the message
    $sender_id = $user_id;

    $query = "INSERT INTO messages (conversation_id, sender_id, message) 
              VALUES (:conversation_id, :sender_id, :message)";
    $stmt = $conn->prepare($query);
    $stmt->execute([
        'conversation_id' => $conversation_id,
        'sender_id' => $sender_id,
        'message' => $message
    ]);

    // Reload the page to show the new message
    header("Location: view_conversation_users.php?conversation_id=$conversation_id");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversation with Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'components/user_header.php'; ?>
    <br><br><br><br>

    <section class="chat-box">
        <h1 class="heading">Conversation with Admin</h1>
        <div class="user-chat-box">
            <?php if ($messages): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="message <?php echo ($message['sender_id'] == $user_id) ? 'sent' : 'received'; ?>">
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
