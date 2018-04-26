<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pibbc23portal";
$db = new mysqli($servername, $username, $password, $dbname);

if($db->connect_errno > 0){
    die('Unable to connect to interface database [' . $db->connect_error . ']');
}

$servername_tpb = "localhost";
$username_tpb = "root";
$password_tpb = "";
$dbname_tpb = "tpbdb";
$db_tpb = new mysqli($servername_tpb, $username_tpb, $password_tpb, $dbname_tpb);

if($db_tpb->connect_errno > 0){
    die('Unable to connect to TPB database [' . $db_tpb->connect_error . ']');
}
?>