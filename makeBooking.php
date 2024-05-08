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

if (isset(
    $_POST['vehicle_id'],
    $_POST['start_date'],
    $_POST['end_date'],
)) {

    $vehicleId = $_POST['vehicle_id'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    $sql = "select * from bookings where vehicle_id = $vehicleId and ((start_date between '$startDate' and '$endDate') or (end_date between '$startDate' and '$endDate')) and status = 'Success'";

    $result = mysqli_query($CON, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo json_encode(array(
            "success" => false,
            "message" => "Vehicle is already booked for given dates"
        ));
        die();
    }

    $sql = "select * from vehicles where vehicle_id = $vehicleId";

    $result = mysqli_query($CON, $sql);

    if (mysqli_num_rows($result) == 0) {
        echo json_encode(array(
            "success" => false,
            "message" => "Vehicle not found"
        ));
    }

    $vehicle = mysqli_fetch_assoc($result);

    $vehicle_per_day_price = $vehicle['per_day_price'];
    $number_of_days = ceil((strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24)) + 1;
    $total = $vehicle_per_day_price * $number_of_days;

    $sql = "insert into bookings (vehicle_id, start_date, end_date, total, booked_by) values('$vehicleId', '$startDate', '$endDate', '$total', '$userId')";

    $result = mysqli_query($CON, $sql);

    if (!$result) {
        echo json_encode(array(
            "success" => false,
            "message" => "Failed to make booking"
        ));
        die();
    }

    $booking_id = mysqli_insert_id($CON);

     $isNotificationSent =    sendNotification(
        "New Booking Made",
        "New booking has been made from $startDate to $endDate",
        $vehicle['added_by']
    );

    if (!$isNotificationSent) {
        echo json_encode(array(
            "success" => false,
            "message" => "Failed to send notification"
        ));
        die();
    }


    echo json_encode(array(
        "success" => true,
        "message" => "Booking made successfully",
        "booking_id" => $booking_id,
        "total" => $total*100
    ));
} else {
    echo json_encode(array(
        "success" => false,
        "message" => "vechicle_id, start_date, end_date are required"
    ));
}