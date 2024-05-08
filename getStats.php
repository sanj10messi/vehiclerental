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




$totalUsers = 0;
$totalVendors = 0;
$totalVehicles = 0;
$totalIncome = 0;
$totalMonthlyIncome = 0;


$sql = "select count(*) as total_users from user where role = 'user'";

$result = mysqli_query($CON, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalUsers = $row['total_users'];
}

$sql = "select count(*) as total_vendors from user where role = 'vendor'";

$result = mysqli_query($CON, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalVendors = $row['total_vendors'];
}


$sql = "select count(*) as total_vehicles from vehicles";

$result = mysqli_query($CON, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalVehicles = $row['total_vehicles'];
}


$sql = "select sum(amount) as total_income from payments";

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

$sql = "select sum(amount) as total_monthly_income from payments where MONTH(created_at) = $month and YEAR(created_at) = $year";

$result = mysqli_query($CON, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalMonthlyIncome = $row['total_monthly_income'];
}

//top 5 categories with total income

$sql = "
select categories.category_id,category,sum(amount) as total_income from payments
join bookings on bookings.booking_id = payments.booking_id
join vehicles on vehicles.vehicle_id = bookings.vehicle_id
join categories on categories.category_id = vehicles.category_id
group by categories.category_id
order by total_income desc
limit 5
";

$result = mysqli_query($CON, $sql);





if (!$result) {
    echo json_encode(array(
        "success" => false,
        "message" => "Error retrieving stats",
        "error" => mysqli_error($CON)
    ));
    die();
}

$top_categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

$remainingAmount = $totalIncome;
$remainingPercentage = 100;


foreach ($top_categories as $key => $user) {
    $top_categories[$key]['percentage'] = round((($user['total_income'] / $totalIncome) * 100), 2);
    $remainingPercentage -= $top_categories[$key]['percentage'];
    $remainingAmount -= $top_categories[$key]['total_income'];
}

$top_categories[] = array(
    "category_id" => 0,
    "category" => "Others",
    "total_income" => $remainingAmount,
    "percentage" => abs(round($remainingPercentage))
);

echo json_encode(array(
    "success" => true,
    "message" => "Stats retrieved successfully",
    "stats" => array(
        "total_users" => $totalUsers,
        "total_vendors" => $totalVendors,
        "total_vehicles" => $totalVehicles,
        "total_income" => $totalIncome,
        "total_monthly_income" => $totalMonthlyIncome ?? "0",
        "top_categories" => $top_categories,
    )

));
