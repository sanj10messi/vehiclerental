<?php
include "../helpers/connection.php";

if(isset(
  $_POST['email'],
  $_POST['password'],
)){

  $email = $_POST['email'];
  $password = $_POST['password'];

  $sql = "SELECT * from user where email = '$email'";

  $result= mysqli_query($CON, $sql);

  $count= mysqli_num_rows($result);

  if ($count == 0){
    echo json_encode(array(
      "success" => false,
      "message" => "user not found "
    ));
    die();
  } 

  $user = mysqli_fetch_assoc($result);

  $hashed_password = $user['password'];

  $is_correct = password_verify($password, $hashed_password );
    if (!$is_correct){
      echo json_encode(array(
      "success" => false,
      "message" => "Please enter your valid password"
    ));
    die();
  }

  $token = bin2hex(random_bytes(16));

  $user_id = $user['user_id'];


  $sql = "insert into access_tokens(token, user_id) values('$token','$user_id')";

  $result = mysqli_query($CON, $sql);

  if (!$result){
    echo json_encode(array(
      "success" => false,
      "message" => "fail to login"
    ));
    die();

  }
  $role = $user['role'];


    echo json_encode(array(
        "success" => true,
        "message" => "login successfully",
        "token" => $token,
        "role" => $role,
        "userId" => $user_id,
        )
    );


}else{
  echo json_encode(array(
    "success" => false,
    "message" => "all field required"
  ));
}