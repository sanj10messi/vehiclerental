<?php

include "./helpers/connection.php";

function sendNotification($title, $body, $user_id)
{
    global $CON;
    $sql = "insert into notifications (title,description,user_id) values ('$title','$body',$user_id)";
    $result = mysqli_query($CON, $sql);
    if ($result) {
        return true;
    } else {
        return false;
    }
}