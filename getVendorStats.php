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

$isVendor = isVendor($token);

if (!$isVendor) {
    echo json_encode(array(
        "success" => false,
        "message" => "You are not authorized!"
    ));
    die();
}

$totalUsers = 0;
$totalVehicles = 0;
$totalIncome = 0;
$totalMonthlyIncome = 0;

$sql = "SELECT COUNT(DISTINCT user.user_id) AS total_users 
        FROM bookings 
        JOIN payments ON bookings.booking_id = payments.booking_id 
        JOIN vehicles ON bookings.vehicle_id = vehicles.vehicle_id 
        JOIN user ON user.user_id = payments.payment_by 
        WHERE user.role = 'user' 
        AND vehicles.added_by = $userId";
$result = mysqli_query($CON, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalUsers = $row['total_users'];
}

$sql = "SELECT COUNT(*) AS total_vehicles 
        FROM vehicles 
        WHERE added_by = $userId";
$result = mysqli_query($CON, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalVehicles = $row['total_vehicles'];
}

// $sql = "SELECT SUM(amount) AS total_income FROM payments WHERE payment_by = $userId";
$sql = "SELECT SUM(amount) AS total_income
FROM payments
JOIN bookings ON payments.booking_id = bookings.booking_id
JOIN vehicles ON bookings.vehicle_id = vehicles.vehicle_id
WHERE vehicles.added_by = $userId
";



$result = mysqli_query($CON, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalIncome = $row['total_income'];
}

$month = date("m");
$year = date("Y");

if (isset($_POST['month'], $_POST['year'])) {
    $month = $_POST['month'];
    $year = $_POST['year'];
}

$sql = "SELECT SUM(amount) AS total_monthly_income 
        FROM payments 
        JOIN bookings ON payments.booking_id = bookings.booking_id
        JOIN vehicles ON bookings.vehicle_id = vehicles.vehicle_id
        WHERE vehicles.added_by = $userId 
        AND MONTH(payments.created_at) = $month 
        AND YEAR(payments.created_at) = $year";



$result = mysqli_query($CON, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalMonthlyIncome = $row['total_monthly_income'];
}

echo json_encode(array(
    "success" => true,
    "message" => "Stats retrieved successfully",
    "stats" => array(
        "total_users" => $totalUsers,
        "total_vehicles" => $totalVehicles,
        "total_income" => $totalIncome ?? "0",
        "total_monthly_income" => $totalMonthlyIncome ?? "0",
    )
));
