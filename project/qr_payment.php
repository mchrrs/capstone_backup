<p?php

    include 'components/connect.php' ;

    if(isset($_COOKIE['user_id'])){
    $user_id=$_COOKIE['user_id'];
    }else{
    $user_id='' ;
    }

    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Payment</title>

        <!-- font awesome cdn link  -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

        <!-- custom css file link  -->
        <link rel="stylesheet" href="/project/css/style.css">





    </head>

    <body>

        <style>
            /* Center the payment section */

            .payment {
                display: flex;
                justify-content: center;
                align-items: center;
                width: 100%;
                height: 100%;
            }

            .payment-container {
                background-color: #fff;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
                max-width: 400px;
                text-align: center;
            }

            .payment-container .heading {
                font-size: 24px;
                margin-bottom: 15px;
                color: #333;
            }

            .payment-container p {
                font-size: 16px;
                color: #666;
                margin-bottom: 20px;
            }

            .qr-code img {
                max-width: 100%;
                height: auto;
                border: 1px solid #ddd;
                border-radius: 10px;
                padding: 10px;
            }
        </style>


        <section class="payment">
            <div class="payment-container">

                <h1 class="heading">Pay with QR Code</h1>
                <p> Scan the QR code below using your mobile banking app or payment to proceed with the payment. </p>

                <div class="qr-code">
                    <img src="/project/images/qr_payment.png">
                </div>

                <!-- Add the form here -->
                <form action="/project/owned_property.php" method="POST" style="margin-top: 20px;">
                    <input type="hidden" name="property_id" value="<?= $property_id; ?>"> <!-- Assuming $property_id is set before this -->
                    <button type="submit" name="mark_owned" class="btn" style="padding: 10px 20px; background-color: #33a2ff; color: white; border: none; border-radius: 5px; cursor: pointer;">
                        Mark as Owned
                    </button>
                </form>
            </div>
        </section>






        <?php include 'components/footer.php'; ?>

        <!-- custom js file link  -->
        <script src="js/script.js"></script>

    </body>

    </html>