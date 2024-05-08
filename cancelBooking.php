<?php

include "./helpers/connection.php";
include "./helpers/authHelper.php";
include "./helpers/notifyHelper.php";


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


if (isset($_POST['booking_id'])) {


    $bookingId = $_POST['booking_id'];

    $sql = "select * from bookings where booking_id = $bookingId";

    $result = mysqli_query($CON, $sql);


    if (mysqli_num_rows($result) == 0) {

        echo json_encode(array(
            "success" => false,
            "message" => "Booking not found"
        ));
        die();
    }


    $row = mysqli_fetch_assoc($result);


    if ($row['booked_by'] != $userId) {

        echo json_encode(array(
            "success" => false,
            "message" => "You are not authorized to cancel this booking"
        ));
        die();
    }

    if ($row['status'] == "Cancelled") {

        echo json_encode(array(
            "success" => false,
            "message" => "Booking already cancelled"
        ));
        die();
    }

    if ($row['status'] == "Success") {
        echo json_encode(array(
            "success" => false,
            "message" => "Payment already made, contact admin to cancel booking"
        ));
        die();
    }



    $sql = "update bookings set status = 'Cancelled' where booking_id = $bookingId";

    $result = mysqli_query($CON, $sql);


    if (!$result) {

        echo json_encode(array(
            "success" => false,
            "message" => "Failed to cancel booking"
        ));
        die();
    }

    echo json_encode(array(
        "success" => true,
        "message" => "Booking cancelled successfully"
    ));
} else {

    echo json_encode(array(
        "success" => false,
        "message" => "Booking id is required"
    ));
}