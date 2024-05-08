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


if (isset(
    $_POST['vehicle_id'],
    $_POST['rating']
)) {

    $vehicle_id = $_POST['vehicle_id'];
    $rating = $_POST['rating'];


    $sql = "select * from ratings where vehicle_id = $vehicle_id AND user_id = $userId";

    $result = mysqli_query($CON, $sql);


    $rating_id = null;

    if (mysqli_num_rows($result) > 0) {
        $ratingData = mysqli_fetch_assoc($result);
        $rating_id = $ratingData['rating_id'];
    }

    $sql = '';

    if ($rating_id != null) {
        $sql = "UPDATE ratings SET rating = $rating WHERE rating_id = $rating_id";
    } else {
        $sql = "INSERT INTO ratings (user_id, vehicle_id, rating) VALUES ($userId, $vehicle_id, $rating)";
    }

    $result = mysqli_query($CON, $sql);


    if ($result) {
        echo json_encode(array(
            "success" => true,
            "message" => "Rating added successfully"
        ));


        $sql = "UPDATE vehicles SET rating = (SELECT AVG(rating) FROM ratings WHERE vehicle_id = $vehicle_id) WHERE vehicle_id = $vehicle_id";
        $result = mysqli_query($CON, $sql);
        die();
    }

    echo json_encode(array(
        "success" => false,
        "message" => "Failed to add rating"
    ));
} else {
    echo json_encode(array(
        "success" => false,
        "message" => "vehicle_id and rating are required"
    ));
    die();
}