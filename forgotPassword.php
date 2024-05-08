<?php

include("./helpers/connection.php");
include("./helpers/authHelper.php");

use PHPMailer\PHPMailer\PHPMailer;

require 'vendor/autoload.php';

if (isset($_POST['email'])) {
    $email = $_POST['email'];

    $sql = "select * from user where email = '$email'";
    $result = mysqli_query($CON, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $user_id = $user['user_id'];
        $fullName = $user['fullname'];

        //6 digit random number
        $otp = rand(111111, 999999);

        $sql = "update user set code = $otp where user_id = $user_id";
        $result = mysqli_query($CON, $sql);

        if (!$result) {
            echo json_encode(array(
                "success" => false,
                "message" => "Failed to send code"
            ));
            die();
        }

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'sanjaymessisharma@gmail.com';
        $mail->Password = 'oqyr wczm joww oxss';
        $mail->SMTPSecure = 'ssl';
        ///driver
        $mail->Port = 465;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->Subject = 'MeroCar Password Reset';
        $mail->Body    = "Your password reset code for MeroCar $otp";

        $mail->setFrom('sanjaymessisharma@gmail.com', 'MeroCar');        // Set sender of the mail          // Add a recipient
        $mail->addAddress("$email", "$fullName");  // Add a recipient
        // Add a recipient

        if (!$mail->send()) {
            echo json_encode(array(
                "success" => false,
                "message" => "Failed to send code $mail->ErrorInfo"
            ));
            die();
        } else {
            echo json_encode(array(
                "success" => true,
                "message" => "Password reset link sent to your email"
            ));
        }
    } else {
        echo json_encode(array(
            "success" => false,
            "message" => "User not found"
        ));
    }
} else {
    echo json_encode(array(
        "success" => false,
        "message" => "Email is required"
    ));
    die();
}