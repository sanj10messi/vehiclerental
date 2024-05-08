<?php
include "./helpers/connection.php";
include "./helpers/authHelper.php";

if(!isset($_POST['token'])) {
  echo json_encode(array(
    "success" => false,
    "message" => "Token is required"
  ));
  die();
}

$token = $_POST['token'];

$userId = getUserId($token);

if(!$userId) {
  echo json_encode(array(
    "success" => false,
    "message" => "Invalid token"
  ));
  die();
}

if (isset(
  $_POST['start_date'],
  $_POST['end_date'],

)) {
  $startDate = $_POST['start_date'];
  $endDate = $_POST['end_date'];

  $sql = "SELECT * FROM vehicles 
          WHERE vehicle_id NOT IN (
              SELECT vehicle_id FROM bookings 
              WHERE ('$startDate' BETWEEN start_date AND end_date OR '$endDate' BETWEEN start_date AND end_date)
              AND status = 'Success' 
          )"; 

  $result = mysqli_query($CON, $sql);

  if (mysqli_num_rows($result) == 0) {
    echo json_encode(array(
      "success" => false,
      "message" => "No vehicles available for given dates"
    ));
    die();
  }

  $vehicles = array();
  while ($row = mysqli_fetch_assoc($result)) {
    $vehicles[] = $row;
  }

  echo json_encode(array(
    "success" => true,
    "vehicles" => $vehicles
  ));
} else {
  echo json_encode(array(
    "success" => false,
    "message" => "Start date and end date are required"
  ));
}

