<?php
// process_payment.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture the amount and email entered by the user
    $amount = $_POST['amount'];  // Amount entered by the user
    $email = $_POST['email'];    // User's email address

    // PayMongo API URL to create a payment intent
    $paymongo_api_url = 'https://api.paymongo.com/v1/payment_intents';

    // Your PayMongo Test Secret Key (replace with your actual test key)
    $secret_key = 'sk_test_X8WnexKFwziaFLgtWdV9hRHm';  // Replace with your PayMongo test key

    // Convert the amount to cents (PayMongo requires the amount in cents)
    $amount_in_cents = $amount * 100;

    // Prepare the data for the payment intent
    $data = [
        'data' => [
            'attributes' => [
                'amount' => $amount_in_cents,  // Amount in cents
                'currency' => 'PHP',  // Currency in PHP
                'description' => 'Apartment Rent Payment',  // Description for the payment
                'email' => $email,  // Customer email
                'payment_method' => [
                    'type' => 'gcash',  // Payment method type (e.g., GCash)
                ],
                'payment_method_allowed' => ['gcash'],  // Specify allowed payment methods (e.g., GCash)
            ]
        ]
    ];

    // Base64 encode the secret key for Basic Authentication
    $auth_value = base64_encode($secret_key);  // No colon after key

    // Initialize cURL to create the payment intent
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $paymongo_api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . $auth_value,  // Use Basic Authentication with the base64 encoded key
        'Content-Type: application/json',
    ]);

    // Execute the cURL request and capture the response
    $response = curl_exec($ch);
    curl_close($ch);

    // Decode the response JSON
    $response_data = json_decode($response, true);

    // Debug: print the full response for troubleshooting
    echo '<pre>';
    print_r($response_data);  // Print the full response to debug
    echo '</pre>';

    // Check if the payment intent was successfully created
    if (isset($response_data['data']['id'])) {
        // Normally, you would get the client_key here:
        // $client_key = $response_data['data']['attributes']['client_key'];

        // Instead of dynamically generating the link, use your test link directly
        $payment_link = "https://pm.link/org-NDmGxzLuoB7svkhjwX1wCet4/test/8VHzMdP";

        // Display the payment link (this link will allow the user to proceed to PayMongo's payment page)
        echo '
        <div class="payment-container">
            <h2>Your Payment Link</h2>
            <p>Click below to proceed to payment:</p>
            <div class="button-container">
                <a href="' . $payment_link . '" target="_blank" class="payment-btn">Click here to proceed to payment</a>
            </div>
        </div>';
    } else {
        // Display error messages if payment intent creation failed
        if (isset($response_data['errors'])) {
            foreach ($response_data['errors'] as $error) {
                echo 'Error: ' . $error['detail'] . '<br>';
            }
        } else {
            echo 'Payment creation failed. Please try again later.';
        }
    }
}
?>
