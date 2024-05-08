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
    $_POST['booking_id'],
    $_POST['other_details'],
    $_POST['amount'],

)) {

    $bookingId = $_POST['booking_id'];
    $otherDetails = $_POST['other_details'];
    $amount = $_POST['amount'];


    $sql = "select * from bookings where booking_id ='$bookingId'";


    $result = mysqli_query($CON, $sql);

    if (mysqli_num_rows($result) == 0) {
        echo json_encode(array(
            "success" => false,
            "message" => "Booking not found"
        ));
        die();
    }

    $booking = mysqli_fetch_assoc($result);
    $status = $booking['status'];

    if ($status != "Pending") {
        echo json_encode(array(
            "success" => false,
            "message" => "Booking already completed"
        ));
        die();
    }

    $sql = "insert into payments (booking_id, other_details, amount, payment_by) values ('$bookingId', '$otherDetails', '$amount','$userId')";
    $result = mysqli_query($CON, $sql);

    if (!$result) {
        echo json_encode(array(
            "success" => false,
            "message" => "Failed to save payment"
        ));
        die();
    }

    $sql = "update bookings set status = 'Success' where booking_id = '$bookingId'";

    $result = mysqli_query($CON, $sql);

    if (!$result) {
        echo json_encode(array(
            "success" => false,
            "message" => "Failed to update booking"
        ));
        die();
    }

    echo json_encode(array(
        "success" => true,
        "message" => "Payment saved successfully"
    ));
} else {
    echo json_encode(array(
        "success" => false,
        "message" => "booking_id, other_details, amount are required"
    ));
}