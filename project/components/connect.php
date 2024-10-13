<?php

$db_name = 'mysql:host=127.0.0.1;port=3307;dbname=home_db';
$db_user_name = 'root';
$db_user_pass = '';

try {
    // Create a new PDO instance and set error mode to exception
    $conn = new PDO($db_name, $db_user_name, $db_user_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Uncomment the line below for debugging purposes, to confirm the connection is working
    // echo "Connection successful";
} catch (PDOException $e) {
    // If there's an error, display a message and stop the script
    die("Connection failed: " . $e->getMessage());
}

function create_unique_id() {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 20; $i++) {
        $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

?>
