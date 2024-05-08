<?php
include "../helpers/connection.php";

if(isset(
  $_POST['email'],
  $_POST['password'],
  $_POST['fullname'],
  $_POST['contact'],
  $_FILES['license_image'],

)){

   $image =$_FILES['license_image'];

  $image_size = $image['size'];

  if($image_size > 5 * 1024 * 1024){
    echo json_encode(array(
    "success" => false,
    "message" => "Image size should be less than 5 mb "));
    die();
  }
    $ext = pathinfo($image['name'], PATHINFO_EXTENSION);

    $allowed =["jpg", "jpeg", "png", "webp"];

    if (!in_array($ext, $allowed)){
      echo json_encode(array(
          "success" => false,
          "message" => "Invalid image format"
        ));

    }

    $new_name = uniqid()."." . $ext;
    $temp_location =$image['tmp_name'];
    $new_location = "../images/" . $new_name;
    $image = "images/" .$new_name;

    if(!move_uploaded_file($temp_location, $new_location)){
      echo json_encode(array(
            "success" => false,
            "message" => "fail to upload image"
      ));
    }

  $email = $_POST['email'];
  $password = $_POST['password'];
  $fullname = $_POST['fullname'];
  $contact =$_POST['contact'];

  $sql = "SELECT * from user where email = '$email'";

  $result= mysqli_query($CON, $sql);

  $count= mysqli_num_rows($result);

  if ($count >0){
    echo json_encode(array(
      "success" => false,
      "message" => "email already exists"
    ));
    die();
  } 

  $hashed_password= password_hash($password, PASSWORD_DEFAULT);

  $sql = "insert into user(email, password, fullname, contact, license_image) values('$email', '$hashed_password', '$fullname', '$contact','$image')";

  $result = mysqli_query($CON, $sql);

  if (!$result){
    echo json_encode(array(
      "success" => false,
      "message" => "Registration fail please try again"
    ));
    die();

  }
  else{
    echo json_encode(array(
        "success" => false,
        "message" => "user register successfully"
        )
    );
  }


}else{
  echo json_encode(array(
    "success" => false,
    "message" => "all field required"
  ));
}