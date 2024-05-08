<?php

include "helpers/connection.php";
include "helpers/authHelper.php";


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


if (isset($_POST['old_password'], $_POST['new_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    $sql = "SELECT password FROM user WHERE user_id = $userId";
    $result = mysqli_query($CON,$sql);

    if(!$result){
        echo json_encode(array(
            "success" => false,
            "message" => "Failed to fetch users data",
            
        ));
        die();
    }

    $user = mysqli_fetch_assoc($result);

    $hashed_password = $user['password'];

    $is_correct = password_verify($old_password, $hashed_password);

    if (!$is_correct) {
        echo json_encode(array(
            "success" => false,
            "message" => "Old password doesn't match",
        ));
        die();
    }

    $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

    $sql = "UPDATE user SET password = '$hashed_new_password' WHERE user_id = $userId";

    $result = mysqli_query($CON, $sql);

    if (!$result) {
        echo json_encode(array(
            "success" => false,
            "message" => "Failed to update password",
            
        ));
        die();
    }

    echo json_encode(array(
        "success" => true,
        "message" => "Password changed successfully"
    ));


    
} else{
    echo json_encode(array(
        "success" => false,
        "message" => "All fields are required"
    ));
    die();
}