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
}



if (isset($_POST['category_id'], $_POST['is_delete'])) {

    $categoryId = $_POST['category_id'];
    $isDelete = $_POST['is_delete'];

    $sql = "update categories set is_deleted = $isDelete where category_id = $categoryId";


    $result = mysqli_query($CON, $sql);


    if ($result) {
        echo json_encode(array(
            "success" => true,
            "message" => "Category status updated successfully"
        ));
    } else {
        echo json_encode(array(
            "success" => false,
            "message" => "Failed to update category status"
        ));
    }
} else {

    echo json_encode(array(
        "success" => false,
        "message" => "category_id, is_delete are required"
    ));
}