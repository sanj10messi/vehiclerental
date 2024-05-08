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


if (isset($_POST['fullname'], $_POST['contact'], $_POST['address'])) {

    $full_name = $_POST['fullname'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $imageUrl = null;

    if (isset($_FILES['image_url'])) {

        $image = $_FILES['image_url'];

        $image_size = $image['size'];

        if ($image_size > 5 * 1024 * 1024) {
            echo json_encode(array(
                "success" => false,
                "message" => "Image size should be less than 5 MB"
            ));
            die();
        }

        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);

        $allowed = ["jpg", "jpeg", "png", "webp"];

        if (!in_array($ext, $allowed)) {
            echo json_encode(array(
                "success" => false,
                "message" => "Invalid image format"
            ));
        }

        $new_name = uniqid() . "." . $ext;
        $temp_location = $image['tmp_name'];
        $new_location = "./images/" . $new_name;
        $image_url = "images/" . $new_name;


        if (!move_uploaded_file($temp_location, $new_location)) {
            echo json_encode(array(
                "success" => false,
                "message" => "Failed to upload image"
            ));
        }

        $imageUrl = $image_url;
    }

    $sql = '';

    if ($imageUrl == null) {
        $sql = "UPDATE user SET fullname = '$full_name', contact = '$contact', address = '$address' WHERE user_id = $userId";
    } else {
        $sql = "UPDATE user SET fullname = '$full_name', image_url='$imageUrl', contact = '$contact', address = '$address' WHERE user_id = $userId";
    }

    $result = mysqli_query($CON, $sql);


    if ($result) {
        echo json_encode(array(
            "success" => true,
            "message" => "Profile updated successfully"
        ));

        die();
    } else {
        echo json_encode(array(
            "success" => false,
            "message" => "Profile updated failed"
        ));

        die();
    }
} else {
    echo json_encode(array(
        "success" => false,
        "message" => "full_name,contact, image and address is required",
    ));
    die();
}