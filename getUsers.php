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

$isAdmin = isAdmin($token);

if (!$isAdmin) {
    echo json_encode(array(
        "success" => false,
        "message" => "You are not authorized!"
    ));
    die();
}


$sql = "select user_id,email,fullname, role, address,contact,created_at,image_url, license_image from user";

$result = mysqli_query($CON, $sql);

$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

echo json_encode(array(
    "success" => true,
    "message" => "Users fetched successfully",
    "users" => $users
));