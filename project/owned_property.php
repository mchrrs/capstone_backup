<?php
include 'components/connect.php';

// Start the session (if not started already)
session_start();

// Check if the user is logged in and get their ID
$user_id = $_SESSION['user_id'] ?? ''; // Use null coalescing operator for cleaner code

// Handle 'send' action
if (isset($_POST['send'])) {
    $property_id = filter_input(INPUT_POST, 'property_id', FILTER_SANITIZE_STRING); // Use filter_input for better sanitization

    if (!empty($user_id)) {
        // Check if the property is already marked as owned
        $check_owned = $conn->prepare("SELECT * FROM `owned` WHERE user_id = ? AND property_id = ?");
        $check_owned->execute([$user_id, $property_id]);

        if ($check_owned->rowCount() == 0) {
            // Insert into the 'owned' table if not owned yet
            $insert_owned = $conn->prepare("INSERT INTO `owned` (user_id, property_id) VALUES (?, ?)");
            $insert_owned->execute([$user_id, $property_id]);

            $success_msg[] = 'Property marked as owned successfully!';
        } else {
            $warning_msg[] = 'You have already marked this property as owned.';
        }
    } else {
        header('location:login.php');
        exit(); // Ensure no further code is executed after the redirect
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owned Properties</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <?php include 'components/user_header.php'; ?>

    <section class="owned-properties">

        <h1 class="heading">Owned Properties</h1>

        <div class="box-container">
            <?php
            $total_images = 0;
            $select_properties = $conn->prepare("SELECT * FROM `property` ORDER BY date DESC");
            $select_properties->execute();
            if ($select_properties->rowCount() > 0) {
                while ($fetch_property = $select_properties->fetch(PDO::FETCH_ASSOC)) {

                    $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
                    $select_user->execute([$fetch_property['user_id']]);
                    $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

                    if (!empty($fetch_property['image_02'])) {
                        $image_coutn_02 = 1;
                    } else {
                        $image_coutn_02 = 0;
                    }
                    if (!empty($fetch_property['image_03'])) {
                        $image_coutn_03 = 1;
                    } else {
                        $image_coutn_03 = 0;
                    }
                    if (!empty($fetch_property['image_04'])) {
                        $image_coutn_04 = 1;
                    } else {
                        $image_coutn_04 = 0;
                    }
                    if (!empty($fetch_property['image_05'])) {
                        $image_coutn_05 = 1;
                    } else {
                        $image_coutn_05 = 0;
                    }

                    $total_images = (1 + $image_coutn_02 + $image_coutn_03 + $image_coutn_04 + $image_coutn_05);

                    $select_saved = $conn->prepare("SELECT * FROM `saved` WHERE property_id = ? and user_id = ?");
                    $select_saved->execute([$fetch_property['id'], $user_id]);

            ?>
                    <form action="" method="POST">
                        <div class="box">
                            <input type="hidden" name="property_id" value="<?= $fetch_property['id']; ?>">
                            <?php
                            if ($select_saved->rowCount() > 0) {
                            ?>
                                <button type="submit" name="save" class="save"><i class="fas fa-heart"></i><span>saved</span></button>
                            <?php
                            } else {
                            ?>
                                <button type="submit" name="save" class="save"><i class="far fa-heart"></i><span>save</span></button>
                            <?php
                            }
                            ?>
                            <div class="thumb">
                                <p class="total-images"><i class="far fa-image"></i><span><?= $total_images; ?></span></p>

                                <img src="/project/admin/uploaded_files/<?= $fetch_property['image_01']; ?>" alt="">
                            </div>


                        </div>
                        <div class="box">
                            <div class="price"><i class="fa-solid fa-peso-sign"></i><span><?= $fetch_property['price']; ?></span></div>
                            <h3 class="name"><?= $fetch_property['property_name']; ?></h3>
                            <p class="location"><i class="fas fa-map-marker-alt"></i><span><?= $fetch_property['address']; ?></span></p>
                            <div class="flex">
                                <p><i class="fas fa-house"></i><span><?= $fetch_property['type']; ?></span></p>
                                <p><i class="fas fa-tag"></i><span><?= $fetch_property['offer']; ?></span></p>
                                <p><i class="fas fa-bed"></i><span><?= $fetch_property['bhk']; ?> BHK</span></p>
                                <p><i class="fas fa-trowel"></i><span><?= $fetch_property['status']; ?></span></p>
                                <p><i class="fas fa-couch"></i><span><?= $fetch_property['furnished']; ?></span></p>
                                <p><i class="fas fa-maximize"></i><span><?= $fetch_property['carpet']; ?> sqft</span></p>
                            </div>
                            <div class="flex-btn">
                                <a href="view_property.php?get_id=<?= $fetch_property['id']; ?>" class="btn">view property</a>
                                <input type="submit" value="Mark as Owned" name="mark_owned" class="btn">
                            </div>


                        </div>
                    </form>
            <?php
                }
            } else {
                echo '<p class="empty">no properties added yet! <a href="listings.php" style="margin-top:1.5rem;" class="btn">discover more</a></p>';
            }
            ?>

        </div>

    </section>

    <?php include 'components/footer.php'; ?>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

    <?php include 'components/message.php'; ?>

</body>

</html>