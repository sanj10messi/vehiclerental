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


if (isset(
    $_POST['category'],
)) {

    $category = $_POST['category'];

    $sql = "select * from categories where category='$category'";

    $result = mysqli_query($CON, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo json_encode(array(
            "success" => false,
            "message" => "Category already exists"
        ));
        die();
    }

    $sql = "insert into categories(category) values('$category')";

    $result = mysqli_query($CON, $sql);

    if (!$result) {
        echo json_encode(array(
            "success" => false,
            "message" => "Failed to add category"
        ));
        die();
    }

    echo json_encode(array(
        "success" => true,
        "message" => "Category added successfully"
    ));
} else {
    echo json_encode(array(
        "success" => false,
        "message" => "Category  is required"
    ));
    die();
}