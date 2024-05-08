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

$isVendor = isVendor($token);

if ($isVendor) {
    echo json_encode(array(
        "success" => false,
        "message" => "You are not authorized!"
    ));
    die();
}


if (isset(
    $_POST['email'],
    $_POST['password'],
    $_POST['fullname']
)) {

    $email = $_POST['email'];
    $password = $_POST['password'];
    $fullName = $_POST['fullname'];


    $role = "user";

    if (isset($_POST['role'])) {
        $role = $_POST['role'];
    }

    $sql = "select * from user where email ='$email'";

    $result = mysqli_query($CON, $sql);

    $count = mysqli_num_rows($result);

    if ($count > 0) {
        echo json_encode(array(
            "success" => false,
            "message" => "Email already exists"
        ));
        die();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "insert into user(email, password, fullname,role) values('$email', '$hashed_password', '$fullName','$role')";

    $result = mysqli_query($CON, $sql);

    if (!$result) {
        echo json_encode(array(
            "success" => false,
            "message" => "Registration failed, please try again later"
        ));
        die();
    }

    echo json_encode(array(
        "success" => true,
        "message" => "User registered successfully"
    ));
} else {
    echo json_encode(array(
        "success" => false,
        "message" => "Email, password and full name are required"
    ));
}
// dddd