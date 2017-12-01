<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project2";
$db = new mysqli($servername, $username, $password, $dbname);

if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}
?>