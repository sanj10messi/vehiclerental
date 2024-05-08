<?php 
include "./helpers/connection.php";
include "./helpers/authHelper.php";

if(!isset($_POST['token'],)){
  echo json_encode(array(
      "success" => false,
      "message" => "Token is required "
  ));
  die();
}

$token = $_POST['token'];

$userId = getUserId($token);

if(!$userId){
  echo json_encode(array(
    "success" => false,
    "message" => "invalid token"
  ));
  die();
}

if(isset(
  $_POST['category_id'],
  $_POST['title'],
  $_POST['description'],
  $_POST['per_day_price'],
  $_FILES['image'],
  $_POST['no_of_seats'],
)){
  $image =$_FILES['image'];

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
    $new_location = "./images/" . $new_name;
    $image = "images/" .$new_name;

    if(!move_uploaded_file($temp_location, $new_location)){
      echo json_encode(array(
            "success" => false,
            "message" => "fail to upload image"
      ));
    }

    $category_id = $_POST['category_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $per_day_price = $_POST['per_day_price'];
    $no_of_seat = $_POST['no_of_seats'];
    


$sql = "insert into vehicles (category_id, title, description, per_day_price, image, no_of_seats, added_by) values('$category_id','$title','$description','$per_day_price', '$image','$no_of_seat','$userId')" ;
$result = mysqli_query($CON,$sql);
if($result){
  echo json_encode(array(
    "success"=> true,
    "message" =>"success"
  ));

}

  }else {
  echo json_encode(array(
    "success"=> false,
    "message" =>"all field required"
  ));
}

