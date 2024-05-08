<?php 
include "./helpers/connection.php";
include "./helpers/authHelper.php";

if(!isset($_POST['token'])){
    echo json_encode(array(
        "success" => false,
        "message" => "Token is required"
    ));
    die();
}

$token = $_POST['token'];

$userId = getUserId($token);

if(!$userId){
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid token"
    ));
    die();
}

// Check if all required fields are provided
if(isset($_POST['vehicle_id'], $_POST['category_id'], $_POST['title'], $_POST['description'], $_POST['per_day_price'], $_POST['no_of_seats'])){
    $vehicleId = $_POST['vehicle_id'];
    $category_id = $_POST['category_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $per_day_price = $_POST['per_day_price'];
    $no_of_seat = $_POST['no_of_seats'];

    // Check if an image is uploaded
    if(isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $image = $_FILES['image'];

        $image_size = $image['size'];

        if($image_size > 5 * 1024 * 1024){
            echo json_encode(array(
                "success" => false,
                "message" => "Image size should be less than 5 MB"
            ));
            die();
        }

        // Validate image format
        $allowed = ["jpg", "jpeg", "png", "webp"];
        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
        if (!in_array($ext, $allowed)){
            echo json_encode(array(
                "success" => false,
                "message" => "Invalid image format"
            ));
            die();
        }

        $new_name = uniqid() . "." . $ext;
        $temp_location = $image['tmp_name'];
        $new_location = "./images/" . $new_name;
        $image_path = "images/" . $new_name;

        // Move uploaded image to the destination folder
        if(!move_uploaded_file($temp_location, $new_location)){
            echo json_encode(array(
                "success" => false,
                "message" => "Failed to upload image"
            ));
            die();
        }
    } else {
        // If no new image is provided, retrieve the current image path from the database
        $sql = "SELECT image FROM vehicles WHERE vehicle_id = '$vehicleId'";
        $result = mysqli_query($CON, $sql);
        $row = mysqli_fetch_assoc($result);
        $image_path = $row['image'];
    }

    // Update the vehicle information in the database
    $sql = "UPDATE vehicles 
            SET category_id = '$category_id', 
                title = '$title', 
                description = '$description', 
                per_day_price = '$per_day_price', 
                no_of_seats = '$no_of_seat',
                image = '$image_path' 
            WHERE vehicle_id = '$vehicleId' AND added_by = '$userId'";

    $result = mysqli_query($CON, $sql);

    if($result){
        echo json_encode(array(
            "success"=> true,
            "message" => "Vehicle information updated successfully"
        ));
    } else {
        echo json_encode(array(
            "success"=> false,
            "message" => "Failed to update vehicle information"
        ));
    }
} else {
    echo json_encode(array(
        "success"=> false,
        "message" => "All fields are required"
    ));
}

