<?php
// PayPal API credentials
$clientId = "ASu6mDOKgixuQEEUZ1KFF0_NwttgFWMWSM5__hMRMVXMClkS6Rt8wvBdprwPQzXpcC0Cj5GFbW9YP_9V";
$clientSecret = "EOagmsGh-9pnCvSPJ2pi1ONqUdLPr4oEfIOY26W6p_MsiVJ8rQ7ZZ74UQXePj4zgHGejnFezDmlHjGZW";

// Get the POST data
$input = json_decode(file_get_contents('php://input'), true);
$orderId = $input['orderId'];

// Get an access token from PayPal
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v1/oauth2/token");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Accept: application/json",
    "Accept-Language: en_US"
]);
curl_setopt($ch, CURLOPT_USERPWD, "$clientId:$clientSecret");
curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$token = json_decode($response, true)['access_token'];

// Capture the order
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v2/checkout/orders/$orderId/capture");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $token"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Return the response to the client
echo $response;
