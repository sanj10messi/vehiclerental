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
$isAdmin = isAdmin($token);

if (!$userId) {
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid token"
    ));
    die();
}



$sql = '';


if ($isAdmin) {
    $sql = "select * from categories";
} else {

    $sql = "select * from categories where is_deleted = 0";
}


$result = mysqli_query($CON, $sql);


$categories = [];

while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}

echo json_encode(array(
    "success" => true,
    "message" => "Categories fetched successfully",
    "categories" => $categories
));