<?php
// webhook.php

// Get the raw POST data
$payload = file_get_contents('php://input');

// Decode the JSON data
$data = json_decode($payload, true);

// Process the payment status
if (isset($data['data']['attributes']['status'])) {
    if ($data['data']['attributes']['status'] === 'paid') {
        // Handle successful payment (e.g., mark order as paid)
        echo 'Payment Successful!';
    } else {
        // Handle failed payment
        echo 'Payment Failed.';
    }
} else {
    // Handle invalid or missing status
    echo 'Invalid data received.';
}
?>

