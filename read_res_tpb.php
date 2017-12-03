<?php
include('lib/database.php');
include('lib/main.php');

//CONN TO TPBDB
$servertpb = "localhost";
$usertpb = "root";
$passtpb = "";
$dbname = "tpbdb";
$dbtpb = new mysqli($servertpb, $usertpb, $passtpb, $dbname);

if($dbtpb->connect_errno > 0){
    die('Unable to connect to database [' . $dbtpb->connect_error . ']');
}
//CONN TO TPBDB
try {
	$sqlGet = "SELECT * FROM tpb_respon";
	$dataGet = $dbtpb->query($sqlGet);

	while ($row = $dataGet->fetch_assoc()) {
		isset($data);
		$data = [
			'BYTE_STREM_PDF' => $row['BYTE_STREM_PDF'],
			'FLAG_BACA' => $row['FLAG_BACA'],
			'KODE_RESPON' => $row['KODE_RESPON'],
			'NOMOR_AJU' => $row['NOMOR_AJU'],
			'NOMOR_RESPON' => $row['NOMOR_RESPON'],
			'TANGGAL_RESPON' => $row['TANGGAL_RESPON'],
			'WAKTU_RESPON' => $row['WAKTU_RESPON'],
			'ID_HEADER' => $row['ID_HEADER'],
		];

		$insertData[] = insertRefernce('t_bc23res', $data);
		set_logs('RESTPB', $row['NOMOR_AJU'], 'Load Response', 1);
	}
}
catch (PDOException $e) {
    echo $e->getMessage();
}