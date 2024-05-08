<?php
include "./helpers/connection.php";
include "./helpers/authHelper.php";


if (!isset($_POST['token'])) {
    echo json_encode(array(
        "success" => false,
        "message" => "Token is required"
    ));
    die();
}

$token = $_POST['token'];

$userId = getUserId($token);

if (!$userId) {
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid token"
    ));
    die();
}


$sql = "select fullname,email,contact,address,role, image_url, created_at, license_image from user where user_id=$userId";

$result = mysqli_query($CON, $sql);

if (!$result) {
    echo json_encode(array(
        "success" => false,
        "message" => "Failed to fetch user data"
    ));
    die();
}


$user = mysqli_fetch_assoc($result);

echo json_encode(array(
    "success" => true,
    "message" => "User fetched successfully",
    "user" => $user
));