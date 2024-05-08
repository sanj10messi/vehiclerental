<?php

include "./helpers/connection.php";
function getUserId($token){

  $sql = "select user_id from access_tokens where token = '$token' ";

  global $CON;

  $result = mysqli_query($CON, $sql);

  if (!$result){
    return null;
  }

  $user = mysqli_fetch_assoc($result);
  return $user['user_id'];

}

function isAdmin($token){
  $userId = getUserId($token);

  if (!$userId){
    return false;
  }

  global $CON;
  $sql ="select role from user where user_id = '$userId'";
  $result = mysqli_query($CON, $sql);

  if (!$result){
    return false;
  }
  $user = mysqli_fetch_assoc($result);
  $userRole = $user['role'];

  return $userRole =="admin";
}


function isVendor($token){
  $userId = getUserId($token);

  if (!$userId){
    return false;
  }

  global $CON;
  $sql ="SELECT role FROM user WHERE user_id = '$userId'";
  $result = mysqli_query($CON, $sql);

  if (!$result){
    return false;
  }
  
  $user = mysqli_fetch_assoc($result);
  $userRole = $user['role'];


  return $userRole == "vendor";
}
