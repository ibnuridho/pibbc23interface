<?php
include('lib/database.php');
include('lib/main.php');

// $dbName = __DIR__ . DIRECTORY_SEPARATOR . "msaccess/dbPIB.mdb";
$dbName = "C:/BeaCukai/PIB6/dbPIB.mdb";
if (!file_exists($dbName)) {
    die("Could not find database file.");
}

try {
	$dbA = new PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=$dbName; Uid=; Pwd=MumtazFarisHana;");

	// HEADER
	$sqlHdr = "SELECT * FROM t_bc20hdr WHERE KODE_TRADER = 1 AND CAR IS NOT NULL LIMIT 1";
	$dataHdr = $db->query($sqlHdr);
	while($rowHdr = $dataHdr->fetch_assoc()){
		// $arrHdr[] = $rowHdr;
		$CAR = $rowHdr["CAR"];
		$sqlMA_Cek = "SELECT count(1) AS adacar FROM tblPibHdr WHERE CAR = '$CAR'";
		$result = $dbA->query($sqlMA_Cek);
		$adacar = $result->fetch();

		if($adacar["adacar"]==0){
			$sqlMA_GwtNum = "SELECT Nomor FROM tblNomor";
			$result1 = $dbA->query($sqlMA_GwtNum);
			$last_number = $result1->fetch();

			//Set CAR
			$mdb_Car = '010700000397' . date("Ymd") . fixLen($last_number["Nomor"], 6, "0", STR_PAD_LEFT);
			
			//Insert Detail
			$sqlMA_Hdr = "INSERT INTO tblPibHdr (Car, KdKpbc, JnPib, JkWaktu, CrByr, DokTupKd, DokTupNo, DokTupTg, PosNo, PosSub, PosSubSub, ImpId, ImpNpwp, ImpNama, ImpAlmt, ImpStatus, ApiKd, ApiNo, PpjkId, PpjkNpwp, PpjkNama, PpjkAlmt, PpjkNo, PpjkTg, IndId, IndNpwp, IndNama, IndAlmt, PasokNama, PasokAlmt, PasokNeg, PelBkr, PelMuat, PelTransit, TmpTbn, Moda, AngkutNama, AngkutNo, AngkutFl, TgTiba, KdVal, Ndpbm, NilInv, Freight, BTambahan, Diskon, KdAss, Asuransi, KdHrg, Fob, Cif, Bruto, Netto, JmCont, JmBrg, Status, Snrf, KdFas, Lengkap, VersiModul)
			VALUES ( '". $mdb_Car . "', '". $rowHdr["KDKPBC"] . "', '". $rowHdr["JNPIB"] . "', 0, '". $rowHdr["CRBYR"] . "', '". $rowHdr["DOKTUPKD"] . "', '". $rowHdr["DOKTUPNO"] . "', '". $rowHdr["DOKTUPTG"] . "', '". $rowHdr["POSNO"] . "', '". $rowHdr["POSSUB"] . "', '". $rowHdr["POSSUBSUB"] . "', '". $rowHdr["IMPID"] . "', '". $rowHdr["IMPNPWP"] . "', '". $rowHdr["IMPNAMA"] . "', '". $rowHdr["IMPALMT"] . "', '". $rowHdr["IMPSTATUS"] . "', '". $rowHdr["APIKD"] . "', '". $rowHdr["APINO"] . "', '". $rowHdr["PPJKID"] . "', '". $rowHdr["PPJKNPWP"] . "', '". $rowHdr["PPJKNAMA"] . "', '". $rowHdr["PPJKALMT"] . "', '". $rowHdr["PPJKNO"] . "', '". $rowHdr["PPJKTG"] . "', '". $rowHdr["INDID"] . "', '". $rowHdr["INDNPWP"] . "', '". $rowHdr["INDNAMA"] . "', '". $rowHdr["INDALMT"] . "', '". $rowHdr["PASOKNAMA"] . "', '". $rowHdr["PASOKALMT"] . "', '". $rowHdr["PASOKNEG"] . "', '". $rowHdr["PELBKR"] . "', '". $rowHdr["PELMUAT"] . "', '". $rowHdr["PELTRANSIT"] . "', '". $rowHdr["TMPTBN"] . "', '". $rowHdr["MODA"] . "', '". $rowHdr["ANGKUTNAMA"] . "', '". $rowHdr["ANGKUTNO"] . "', '". $rowHdr["ANGKUTFL"] . "', '". $rowHdr["TGTIBA"] . "', '". $rowHdr["KDVAL"] . "', ". $rowHdr["NDPBM"] . ", ". $rowHdr["NILINV"] . ", ". $rowHdr["FREIGHT"] . ", ". $rowHdr["BTAMBAHAN"] . ", ". $rowHdr["DISKON"] . ", '". $rowHdr["KDASS"] . "', '". $rowHdr["ASURANSI"] . "', '". $rowHdr["KDHRG"] . "', ". $rowHdr["FOB"] . ", ". $rowHdr["CIF"] . ", ". $rowHdr["BRUTO"] . ", ". $rowHdr["NETTO"] . ", ". $rowHdr["JMCONT"] . ", ". $rowHdr["JMBRG"] . ", '". $rowHdr["STATUS"] . "', '". $rowHdr["SNRF"] . "', '". $rowHdr["KDFAS"] . "', '". $rowHdr["LENGKAP"] . "', 607);";

			// $ins_hdr_p = $dbA->prepare($sqlMA_Hdr);
			// $ins_hdr = $ins_hdr_p->execute();
			$ins_hdr = true;

			if($ins_hdr){
				//Update Nomor
				$new_number = (int)$last_number["Nomor"] + 1;
				$sqlMA_UpdNum = "UPDATE tblNomor SET Nomor = " . $new_number;
				// $upd_nmr_p = $dbA->prepare($sqlMA_UpdNum);
				// $upd_nmr = $upd_nmr_p->execute();

				//Insert Detail
				$sqlDtl = "SELECT * FROM t_bc20dtl WHERE KODE_TRADER = 1 AND CAR = '$CAR'";
				$dataDtl = $db->query($sqlDtl);
				while($rowDtl = $dataDtl->fetch_assoc()){

					$sqlMA_Dtl = "INSERT INTO tblPibDtl (CAR, Serial, NoHs, SeriTrp, BrgUrai, Merk, Tipe, SpfLain, BrgAsal, DnilInv, DCif, KdSat, JmlSat, KemasJn, KemasJm, SatBmJm, SatCukJm, NettoDtl, FlBarangBaru, FlLartas, SpekTarif, DNilCuk, JmPC, SaldoAwalPC, SaldoAkhirPC) 
					VALUES ('" . $mdb_Car . "', " . $rowDtl["SERIAL"] . ", '" . $rowDtl["NOHS"] . "', " . $rowDtl["SERITRP"] . ", '" . $rowDtl["BRGURAI"] . "', '" . $rowDtl["MERK"] . "', '" . $rowDtl["TIPE"] . "', '" . $rowDtl["SPFLAIN"] . "', '" . $rowDtl["BRGASAL"] . "', " . $rowDtl["DNILINV"] . ", " . $rowDtl["DCIF"] . ", '" . $rowDtl["KDSAT"] . "', " . $rowDtl["JMLSAT"] . ", '" . $rowDtl["KEMASJN"] . "', " . $rowDtl["KEMASJM"] . ", " . $rowDtl["SATBMJM"] . ", " . $rowDtl["SATCUKJM"] . ", " . $rowDtl["NETTODTL"] . ", '" . $rowDtl["FLBARANGBARU"] . "', '" . $rowDtl["FLLARTAS"] . "', '" . $rowDtl["SPEKTARIF"] . "', " . $rowDtl["DNILCUK"] . ", " . $rowDtl["JMPC"] . ", " . $rowDtl["SALDOAWALPC"] . ", " . $rowDtl["SALDOAKHIRPC"] . " ); ";

				}

				//Insert Dokumen
				$sqlDok = "SELECT * FROM t_bc20dok WHERE KODE_TRADER = 1 AND CAR = '$CAR'";
				$dataDok = $db->query($sqlDok);
				while($rowDok = $dataDok->fetch_assoc()){

					$sqlMA_Dok = "INSERT INTO tblPibDok (CAR, DokKd, DokNo, DokTg, DokInst, NoUrut, KdGroupDok) 
					VALUES ('" . $mdb_Car . "', '" . $rowDok["DOKKD"] . "', '" . $rowDok["DOKNO"] . "', '" . $rowDok["DOKTG"] . "', '" . $rowDok["DOKINST"] . "', " . $rowDok["SERIDOK"] . ", '" . $rowDok["KDGROUPDOK"] . "'); ";

				}

				//Insert Container
				$sqlCon = "SELECT * FROM t_bc20Con WHERE KODE_TRADER = 1 AND CAR = '$CAR'";
				$dataCon = $db->query($sqlCon);
				while($rowCon = $dataCon->fetch_assoc()){

					$sqlMA_Con = "INSERT INTO tblPibCon (CAR, ContNo, ContUkur, ContTipe) 
					VALUES ('" . $mdb_Car . "', '" . $rowCon["CONTNO"] . "', '" . $rowCon["CONTUKUR"] . "', '" . $rowCon["CONTTIPE"] . "'); ";

				}

				//Insert Detail Dokumen
				$sqlDtlDok = "SELECT * FROM t_bc20dtldok WHERE KODE_TRADER = 1 AND CAR = '$CAR'";
				$dataDtlDok = $db->query($sqlDtlDok);
				while($rowDtlDok = $dataDtlDok->fetch_assoc()){

					$sqlMA_DtlDok = "INSERT INTO tblPibDtlDok (Car, Serial, KdFasDtl, NoUrut, DokKd, DokNo, DokTg, KdGroupDok) 
					VALUES ('" . $mdb_Car . "', " . $rowDtlDok["SERIBRG"] . ", '" . $rowDtlDok["KDFASDTL"] . "', " . $rowDtlDok["SERIDOK"] . ", '" . $rowDtlDok["DOKKD"] . "', '" . $rowDtlDok["DOKNO"] . "', '" . $rowDtlDok["DOKTG"] . "', '" . $rowDtlDok["KDGROUPDOK"] . "') ";

				}

				//Insert Detail Fasilistas
				$sqlFas = "SELECT * FROM t_bc20fas WHERE KODE_TRADER = 1 AND CAR = '$CAR'";
				$dataFas = $db->query($sqlFas);
				while($rowFas = $dataFas->fetch_assoc()){

					$sqlMA_Fas = "INSERT INTO tblPibFas (Car, Serial, KdFasBM, FasBM, KdFasCuk, FasCuk, KdFasPpn, FasPpn, KdFasPph, FasPph, KdFasPbm, FasPbm, KdFasBMAD, FasBMAD, BMADS, KdFasBMTP, FasBMTP, BMTPS, KdFasBMIM, FasBMIM, BMIMS, KdFasBMPB, FasBMPB, BMPBS) 
					VALUES ('". $rowFas["CAR"] ."', ". $rowFas["SERIBRG"] .", '". $rowFas["KDFASBM"] ."', ". $rowFas["FASBM"] .", '". $rowFas["KDFASCUK"] ."', ". $rowFas["FASCUK"] .", '". $rowFas["KDFASPPN"] ."', ". $rowFas["FASPPN"] .", '". $rowFas["KDFASPPH"] ."', ". $rowFas["FASPPH"] .", '". $rowFas["KDFASPBM"] ."', ". $rowFas["FASPBM"] .", '". $rowFas["KDFASBMAD"] ."', ". $rowFas["FASBMAD"] .", '". $rowFas["BMADS"] ."', '". $rowFas["KDFASBMTP"] ."', ". $rowFas["FASBMTP"] .", '". $rowFas["BMTPS"] ."', '". $rowFas["KDFASBMIM"] ."', ". $rowFas["FASBMIM"] .", '". $rowFas["BMIMS"] ."', '". $rowFas["KDFASBMPB"] ."', ". $rowFas["FASBMPB"] .", '". $rowFas["BMPBS"] ."') ";

					print_r($sqlMA_Fas); die();
				}

			}			
			
			
		}else{
			
		}
		print_r($last_number["Nomor"]); die();


		// DETAIL
	}
	print_r($adacar["adacar"]); die();

}
catch (PDOException $e) {
    echo $e->getMessage();
}