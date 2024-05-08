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
$isAdmin = isAdmin($token);
$isVendor = isVendor($token);

if (!$userId) {
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid token"
    ));
    die();
}


$sql = '';

if ($isAdmin) {
     $sql = "SELECT payments.*,bookings.*,vehicles.*, user.email, user.fullname, user.address, user.contact 
            FROM payments 
            JOIN bookings ON bookings.booking_id = payments.booking_id 
            JOIN vehicles ON vehicles.vehicle_id = bookings.vehicle_id 
            JOIN user ON user.user_id = payments.payment_by 
            ORDER BY payments.created_at DESC";

    // $sql = "select payments.*,email, fullname, address, contact from payments join user on user.user_id = payments.payment_by order by created_at desc";
} else if ($isVendor) {
        //    $sql = "SELECT payments.*, user.email, user.fullname, user.address, user.contact 
        //     FROM payments 
        //     JOIN bookings ON bookings.vehicle_id = payments.vehicle_id 
        //     JOIN vehicles ON vehicles.vehicle_id = bookings.vehicle_id 
        //     JOIN user ON user.user_id = payments.payment_by 
        //     WHERE vehicles.added_by = $userId 
        //     ORDER BY payments.created_at DESC";

        $sql = "SELECT payments.*,bookings.*,vehicles.*, user.email, user.fullname, user.address, user.contact 
            FROM payments 
            JOIN bookings ON bookings.booking_id = payments.booking_id 
            JOIN vehicles ON vehicles.vehicle_id = bookings.vehicle_id 
            JOIN user ON user.user_id = payments.payment_by 
            WHERE vehicles.added_by = $userId 
            ORDER BY payments.created_at DESC";
}else {
        $sql = "SELECT payments.*,bookings.*,vehicles.*, user.email, user.fullname, user.address, user.contact 
            FROM payments 
            JOIN bookings ON bookings.booking_id = payments.booking_id 
            JOIN vehicles ON vehicles.vehicle_id = bookings.vehicle_id 
            JOIN user ON user.user_id = payments.payment_by 
            WHERE payment_by = $userId 
            ORDER BY payments.created_at DESC";

}





// if ($isAdmin) {
//     $sql = "select payments.*,email, fullname, address, contact from payments join user on user.user_id = payments.payment_by order by created_at desc";
// } else if ($isVendor) {
//         //    $sql = "SELECT payments.*, user.email, user.fullname, user.address, user.contact 
//         //     FROM payments 
//         //     JOIN bookings ON bookings.vehicle_id = payments.vehicle_id 
//         //     JOIN vehicles ON vehicles.vehicle_id = bookings.vehicle_id 
//         //     JOIN user ON user.user_id = payments.payment_by 
//         //     WHERE vehicles.added_by = $userId 
//         //     ORDER BY payments.created_at DESC";

//         $sql = "SELECT payments.*,bookings.*, user.email, user.fullname, user.address, user.contact 
//             FROM payments 
//             JOIN bookings ON bookings.booking_id = payments.booking_id 
//             JOIN vehicles ON vehicles.vehicle_id = bookings.vehicle_id 
//             JOIN user ON user.user_id = payments.payment_by 
//             WHERE vehicles.added_by = $userId 
//             ORDER BY payments.created_at DESC";
// }else {
//         $sql = "select payments.*,email, fullname, address, contact from payments join user on user.user_id = payments.payment_by where payment_by = $userId order by created_at desc";

 
// }




$result = mysqli_query($CON, $sql);


if (!$result) {
    echo json_encode(array(
        "success" => false,
        "message" => "Error fetching payments"
    ));
    die();
}


$payments = mysqli_fetch_all($result, MYSQLI_ASSOC);


echo json_encode(array(
    "success" => true,
    "message" => "Payments fetched successfully",
    "payments" => $payments
));