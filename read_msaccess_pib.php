<?php
error_reporting(ALL);
$dbName = $_SERVER["DOCUMENT_ROOT"] . "/pibbc23interface/msaccess/dbPIB.mdb";
if (!file_exists($dbName)) {
    die("Could not find database file.");
}
// die($dbName);
try {
	$db = new PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=$dbName; Uid=; Pwd=MumtazFarisHana;");
    // $pdo = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb)};Dbq=$dbName;Uid=;Pwd=MumtazFarisHana";);
}
catch (PDOException $e) {
    echo $e->getMessage();
} 
// $connA = new COM('ADODB.Connection') or exit('Cannot start ADO.'); #connect to ms access
// $connA->Open("Provider=Microsoft.Jet.OLEDB.4.0; Data Source=$dbName"); #connect to ms access