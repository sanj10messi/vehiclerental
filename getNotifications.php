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


$sql = "select * from notifications where user_id = $userId order by created_at desc";

$result = mysqli_query($CON, $sql);


$notifications = mysqli_fetch_all($result, MYSQLI_ASSOC);


echo json_encode(array(
    "success" => true,
    "message" => "Notifications fetched successfully",
    "notifications" => $notifications
));