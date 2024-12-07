<?php
include '../project/components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
    header('location:login.php');
}

$clientId = "ASu6mDOKgixuQEEUZ1KFF0_NwttgFWMWSM5__hMRMVXMClkS6Rt8wvBdprwPQzXpcC0Cj5GFbW9YP_9V";
$clientSecret = "EOagmsGh-9pnCvSPJ2pi1ONqUdLPr4oEfIOY26W6p_MsiVJ8rQ7ZZ74UQXePj4zgHGejnFezDmlHjGZW";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal Sandbox Payment</title>
    <link rel="stylesheet" href="/project/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .user-header {
            background-color: #007bff;
            padding: 15px;
            color: #fff;
            text-align: center;
        }

        .box-container {
            width: 100%;
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .box-container h1 {
            font-size: 24px;
            color: #007bff;
            margin-bottom: 20px;
        }

        .box-container p {
            font-size: 16px;
            margin-bottom: 20px;
            color: black;
        }

        #paypal-button-container {
            margin-top: 20px;
        }

        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .alert {
            padding: 10px;
            margin-top: 20px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            display: none;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
    </style>
    <!-- PayPal SDK: Replace YOUR_CLIENT_ID with your actual Client ID -->
    <script src="https://www.paypal.com/sdk/js?client-id=ASu6mDOKgixuQEEUZ1KFF0_NwttgFWMWSM5__hMRMVXMClkS6Rt8wvBdprwPQzXpcC0Cj5GFbW9YP_9V&currency=USD"></script>

</head>

<body>

    <?php include '../project/components/user_header.php'; ?>

    <section class="box-container">
        <h1>PayPal Sandbox Payment</h1>

        <!-- PayPal button container -->
        <div id="paypal-button-container"></div>

        <!-- Feedback message container -->
        <div id="payment-feedback" class="alert"></div>

        <script>
            // Render PayPal button
            paypal.Buttons({
                // Set up the transaction
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: '10.00' // Amount to charge
                            }
                        }]
                    });
                },

                // Finalize the transaction after the buyer approves the payment
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(details) {
                        const feedback = document.getElementById('payment-feedback');
                        feedback.className = 'alert alert-success'; // Success class
                        feedback.textContent = 'Transaction completed by ' + details.payer.name.given_name;
                        feedback.style.display = 'block';
                    });
                },

                // Handle errors
                onError: function(err) {
                    console.error('An error occurred during the transaction:', err);
                    const feedback = document.getElementById('payment-feedback');
                    feedback.className = 'alert'; // Default alert class
                    feedback.textContent = 'Something went wrong. Please try again.';
                    feedback.style.display = 'block';
                }
            }).render('#paypal-button-container');
        </script>
        
        <a href="my_bills.php" class="btn btn-primary btn-validate">Go Back</a>
    </section>

</body>

</html>
