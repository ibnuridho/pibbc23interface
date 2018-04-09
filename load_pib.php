<?php
include('lib/database.php');
include('lib/main.php');

$dbName = __DIR__ . DIRECTORY_SEPARATOR . "msaccess/dbPIB.mdb";
if (!file_exists($dbName)) {
    die("Could not find database file.");
}

try {
	$dbA = new PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=$dbName; Uid=; Pwd=MumtazFarisHana;");

	// HEADER
	$sqlHdr = "SELECT * FROM t_bc20hdr WHERE KODE_TRADER = 1 AND CAR IS NOT NULL";
	$dataHdr = $db->query($sqlHdr);
	while($rowHdr = $dataHdr->fetch_assoc()){
		// $arrHdr[] = $rowHdr;
		die($rowHdr["CAR"]);
		$sqlMA_Hdr = "INSERT INTO tblPibHdr ()";

		// DETAIL
	}
	// print_r($arrHdr); die();

}
catch (PDOException $e) {
    echo $e->getMessage();
}