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


$sortByPrice = false;

$sql = '';

if (
    isset($_POST['sortByPrice']) &&
    $_POST['sortByPrice'] === 'true'
) {
    $sql = "select vehicles.*,categories.*,email,fullname,address,contact from vehicles join categories on categories.category_id=vehicles.category_id
    join user on user.user_id=vehicles.added_by order by vehicles.per_day_price asc";
} else {
    $sql = "select vehicles.*,categories.*,email,fullname,address,contact from vehicles join categories on categories.category_id=vehicles.category_id
join user on user.user_id=vehicles.added_by";
}

$result = mysqli_query($CON, $sql);


$vehicles = [];

while ($row = mysqli_fetch_assoc($result)) {
    $vehicles[] = $row;
}

echo json_encode(array(
    "success" => true,
    "message" => "Vehicles fetched successfully",
    "vehicles" => $vehicles
));