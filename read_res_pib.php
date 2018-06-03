<?php
include('lib/database.php');
include('lib/main.php');

$dbName = __DIR__ . DIRECTORY_SEPARATOR . "msaccess/dbPIB.mdb";
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

	    $sqlResConR = "SELECT * FROM tblPibConR WHERE CAR = '" . $row['CAR'] . "'";
	    $resConR = $dbA->query($sqlResConR);

		while ($rowConR = $resConR->fetch()) {
			isset($dataConR);
			$dataConR = [
				'CAR' => $rowConR['CAR'], 'RESKD' => $rowConR['RESKD'], 'CONTNO' => $rowConR['CONTNO'], 'CONTUKUR' => $rowConR['CONTUKUR'], 'CONTTIPE' => $rowConR['CONTTIPE'],
			];

			$insertData[] = insertRefernce('t_bc20conr', $dataConR);
	    	set_logs('RESPIBCONR', $rowConR['CAR'], 'Load Response', 0);
		}

		$sqlResBill = "SELECT * FROM tblPibResBill WHERE CAR = '" . $row['CAR'] . "'";
	    $resBill = $dbA->query($sqlResBill);

		while ($rowResBill = $resBill->fetch()) {
			isset($dataResBill);
			$dataResBill = [
				'CAR' => $rowResBill['CAR'], 'KODEBILL' => $rowResBill['KODEBILL'], 'ResTg' => $rowResBill['ResTg'], 'ResWk' => $rowResBill['ResWk'], 'Seri' => $rowResBill['Seri'], 'Akun' => $rowResBill['Akun'], 'NPWP' => $rowResBill['NPWP'], 'Nilai' => $rowResBill['Nilai'],
			];

			$insertData[] = insertRefernce('tblPibResBill', $dataResBill);
	    	set_logs('RESPIBCONR', $rowResBill['CAR'], 'Load Response', 0);
		}

		$sqlResNpbl = "SELECT * FROM t_bc20ResNPBL WHERE CAR = '" . $row['CAR'] . "'";
	    $resNpbl = $dbA->query($sqlResNpbl);

		while ($rowResNpbl = $resNpbl->fetch()) {
			isset($dataResNpbl);
			$dataResNpbl = [
				'CAR' => $rowResNpbl['CAR'], 'ResKd' => $rowResNpbl['ResKd'], 'ResTg' => $rowResNpbl['ResTg'], 'ResWk' => $rowResNpbl['ResWk'], 'Serial' => $rowResNpbl['Serial'], 'BrgUrai' => $rowResNpbl['BrgUrai'], 'Ketentuan' => $rowResNpbl['Ketentuan'], 'Pemberitahuan' => $rowResNpbl['Pemberitahuan'], 'Penetapan' => $rowResNpbl['Penetapan'],
			];

			$insertData[] = insertRefernce('t_bc20ResNPBL', $dataResNpbl);
	    	set_logs('RESPIBCONR', $rowResNpbl['CAR'], 'Load Response', 0);
		}

		$sqlResNpd = "SELECT * FROM t_bc20ResNPD WHERE CAR = '" . $row['CAR'] . "'";
	    $resNpd = $dbA->query($sqlResNpd);

		while ($rowResNpd = $resNpd->fetch()) {
			isset($dataResNpd);
			$dataResNpd = [
				'CAR' => $rowResNpd['CAR'], 'ResTg' => $rowResNpd['ResTg'], 'ResWk' => $rowResNpd['ResWk'], 'Seri' => $rowResNpd['Seri'], 'UrDok' => $rowResNpd['UrDok'], 'Nilai' => $rowResNpd['Nilai'],
			];

			$insertData[] = insertRefernce('t_bc20ResNPD', $dataResNpd);
	    	set_logs('RESPIBCONR', $rowResNpd['CAR'], 'Load Response', 0);
		}

	}
	
}
catch (PDOException $e) {
    echo $e->getMessage();
} 