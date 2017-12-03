<?php
error_reporting(ALL);
include('lib/database.php');
include('lib/main.php');

$dbName = $_SERVER["DOCUMENT_ROOT"] . "/pibbc23interface/msaccess/dbPIB.mdb";
if (!file_exists($dbName)) {
    die("Could not find database file.");
}

try {
	$dbA = new PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=$dbName; Uid=; Pwd=MumtazFarisHana;");
    $sqlRes = "SELECT * FROM tblPibRes";
    $result = $dbA->query($sqlRes);

	while ($row = $result->fetch()) {
		isset($data);
		$data = [
			'CAR' => $row['CAR'], 'RESKD' => $row['RESKD'], 'RESTG' => $row['RESTG'], 'RESWK' => $row['RESWK'], 'DOKRESNO' => $row['DOKRESNO'], 'DOKRESTG' => $row['DOKRESTG'], 'KPBC' => $row['KPBC'], 'PIBNO' => $row['PIBNO'], 'PIBTG' => $row['PIBTG'], 'KDGUDANG' => $row['KDGUDANG'], 'PEJABAT1' => $row['PEJABAT1'], 'Nip1' => $row['Nip1'], 'JABATAN1' => $row['JABATAN1'], 'PEJABAT2' => $row['PEJABAT2'], 'Nip2' => $row['Nip2'], 'JATUHTEMPO' => $row['JATUHTEMPO'], 'KOMTG' => $row['KOMTG'], 'KOMWK' => $row['KOMWK'], 'DesKripsi' => $row['DesKripsi'], 'dibaca' => $row['dibaca'], 'JmKemas' => $row['JmKemas'], 'NoKemas' => $row['NoKemas'], 'NPWPImp' => $row['NPWPImp'], 'NamaImp' => $row['NamaImp'], 'AlamatImp' => $row['AlamatImp'], 'IDPPJK' => $row['IDPPJK'], 'NamaPPJK' => $row['NamaPPJK'], 'AlamatPPJK' => $row['AlamatPPJK'], 'KodeBill' => $row['KodeBill'], 'TanggalBill' => $row['TanggalBill'], 'TanggalJtTempo' => $row['TanggalJtTempo'], 'TanggalAju' => $row['TanggalAju'], 'TotalBayar' => $row['TotalBayar'], 'Terbilang' => $row['Terbilang'],
		];
		
	    $insertData[] = insertRefernce('t_bc20res', $data);
	    set_logs('RESPIB', $row['CAR'], 'Load Response', 0);
	}
	
	print_r($insertData);

}
catch (PDOException $e) {
    echo $e->getMessage();
} 