<p?php  

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
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
   

<section class="payment">
    <div class="payment-container">
        
        <h1 class="heading">Pay with QR Code</h1>
        <p> Scan the QR code below using your mobile banking app or payment to proceed with the payment. </p>

        <div class="qr-code">
            <img src="/project/images/qr_payment.png">
        </div>
        
    </div>
</section>





<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>