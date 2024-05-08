<?php

include("./helpers/connection.php");
include("./helpers/authHelper.php");


if (isset($_POST['code'], $_POST['email'], $_POST['new_password'])) {
    $email = $_POST['email'];
    $code = $_POST['code'];
    $new_password = $_POST['new_password'];

    $sql = "select * from user where email = '$email' and code = $code";
    $result = mysqli_query($CON, $sql);

    if (mysqli_num_rows($result) > 0) {

        $hash_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "update user set password = '$hash_password' where email = '$email'";
        $result = mysqli_query($CON, $sql);

        if ($result) {
            echo json_encode(array(
                "success" => true,
                "message" => "Password reset successful"
            ));
            die();
        } else {
            echo json_encode(array(
                "success" => false,
                "message" => "Failed to reset password"
            ));
            die();
        }
    } else {
        echo json_encode(array(
            "success" => false,
            "message" => "Invalid code"
        ));
        die();
    }
}