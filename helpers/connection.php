<?php

$host = "localhost";
$user = "root";
$password = "";
$db = "vehicle_rental";

$CON = mysqli_connect($host , $user, $password, $db);

if (!$CON){
  echo "Connection fail";
}