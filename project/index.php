<?php

include '../project/components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
    header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay with PayMongo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="/project/css/style.css">

    <style>
        .payment-form {
            width: 100%;
            display: flex;
            justify-content: center;
            /* Center the content inside this section */
        }

        .payment-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .input-field {
            margin-bottom: 15px;
            width: 100%;
        }

        .input-field input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .submit-btn {
            background-color: #007bff;
            color: white;
            padding: 12px;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .submit-btn:hover {
            background-color: #0056b3;
        }

        .payment-link {
            margin-top: 20px;
            color: green;
            font-size: 18px;
        }
    </style>
</head>

<body>
    <?php include '../project/components/user_header.php'; ?>

    <section class="payment-form">
        <br><br><br>
        <div class="payment-container">
            <h2>Pay with PayMongo</h2>
            <form action="process_payment.php" method="POST">
                <div class="input-field">
                    <label for="amount">Amount (PHP):</label>
                    <input type="number" name="amount" id="amount" required placeholder="Enter amount">
                </div>
                <div class="input-field">
                    <label for="email">Email Address:</label>
                    <input type="email" name="email" id="email" required placeholder="Enter your email">
                </div>
                <button type="submit" class="submit-btn">Proceed to Payment</button>
            </form>
        </div>
        <br><br><br>
    </section>
    <?php include '../project/components/footer.php'; ?>
</body>

</html>