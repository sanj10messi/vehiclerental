<?php

include "./helpers/connection.php";
include "./helpers/authHelper.php";

if($_SERVER['REQUEST_METHOD'] !== 'POST') {

}


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
$isVendor = isVendor($token);

if (!$userId) {
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid token"
    ));
    die();
}


$sql = '';


if ($isAdmin) {
    $sql = "select bookings.*,vehicles.*,email,fullname,address,contact from bookings join user on user.user_id = bookings.booked_by join vehicles on vehicles.vehicle_id = bookings.vehicle_id order by created_at desc";
} else if($isVendor) {

    $sql = "SELECT bookings.*, vehicles.*, user.email, user.fullname, user.address, user.contact 
            FROM bookings 
            JOIN user ON user.user_id = bookings.booked_by 
            JOIN vehicles ON vehicles.vehicle_id = bookings.vehicle_id 
            WHERE vehicles.added_by = $userId 
            ORDER BY created_at DESC";

} else {

    $sql = "select bookings.*,vehicles.*,email,fullname,address,contact
     from bookings
     join user on user.user_id = bookings.booked_by
     join vehicles on vehicles.vehicle_id = bookings.vehicle_id
     where bookings.booked_by = $userId
     order by created_at desc";
}


$result = mysqli_query($CON, $sql);


if (!$result) {
    echo json_encode(array(
        "success" => false,
        "message" => "Error fetching bookings"
    ));
    die();
}


$bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);


echo json_encode(array(
    "success" => true,
    "message" => "Bookings fetched successfully",
    "bookings" => $bookings
));