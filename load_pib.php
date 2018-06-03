<?php
include('lib/database.php');
include('lib/main.php');

// $dbName = __DIR__ . DIRECTORY_SEPARATOR . "msaccess/dbPIB.mdb";
$dbName = "C:/BeaCukai/PIB6/dbPIB.mdb";
if (!file_exists($dbName)) {
    die("Could not find PIB Database file.");
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
			$sqlMA_Hdr = "INSERT INTO tblPibHdr (Car, KdKpbc, JnPib, JkWaktu, CrByr, DokTupKd, DokTupNo, DokTupTg, PosNo, PosSub, PosSubSub, ImpId, ImpNpwp, ImpNama, ImpAlmt, ImpStatus, ApiKd, ApiNo, PpjkId, PpjkNpwp, PpjkNama, PpjkAlmt, PpjkNo, PpjkTg, IndId, IndNpwp, IndNama, IndAlmt, PasokNama, PasokAlmt, PasokNeg, PelBkr, PelMuat, PelTransit, TmpTbn, Moda, AngkutNama, AngkutNo, AngkutFl, TgTiba, KdVal, Ndpbm, NilInv, Freight, BTambahan, Diskon, KdAss, Asuransi, KdHrg, Fob, Cif, Bruto, Netto, JmCont, JmBrg, Status, VersiModul)
			VALUES ( '". $mdb_Car . "', '". $rowHdr["KDKPBC"] . "', '". $rowHdr["JNPIB"] . "', 0, '". $rowHdr["CRBYR"] . "', '". $rowHdr["DOKTUPKD"] . "', '". $rowHdr["DOKTUPNO"] . "', '". $rowHdr["DOKTUPTG"] . "', '". $rowHdr["POSNO"] . "', '". $rowHdr["POSSUB"] . "', '". $rowHdr["POSSUBSUB"] . "', '". $rowHdr["IMPID"] . "', '". $rowHdr["IMPNPWP"] . "', '". $rowHdr["IMPNAMA"] . "', '". $rowHdr["IMPALMT"] . "', '". $rowHdr["IMPSTATUS"] . "', '". $rowHdr["APIKD"] . "', '". $rowHdr["APINO"] . "', '". $rowHdr["PPJKID"] . "', '". $rowHdr["PPJKNPWP"] . "', '". $rowHdr["PPJKNAMA"] . "', '". $rowHdr["PPJKALMT"] . "', '". $rowHdr["PPJKNO"] . "', '". $rowHdr["PPJKTG"] . "', '". $rowHdr["INDID"] . "', '". $rowHdr["INDNPWP"] . "', '". $rowHdr["INDNAMA"] . "', '". $rowHdr["INDALMT"] . "', '". $rowHdr["PASOKNAMA"] . "', '". $rowHdr["PASOKALMT"] . "', '". $rowHdr["PASOKNEG"] . "', '". $rowHdr["PELBKR"] . "', '". $rowHdr["PELMUAT"] . "', '". $rowHdr["PELTRANSIT"] . "', '". $rowHdr["TMPTBN"] . "', '". $rowHdr["MODA"] . "', '". $rowHdr["ANGKUTNAMA"] . "', '". $rowHdr["ANGKUTNO"] . "', '". $rowHdr["ANGKUTFL"] . "', '". $rowHdr["TGTIBA"] . "', '". $rowHdr["KDVAL"] . "', ". $rowHdr["NDPBM"] . ", ". $rowHdr["NILINV"] . ", ". $rowHdr["FREIGHT"] . ", ". $rowHdr["BTAMBAHAN"] . ", ". $rowHdr["DISCOUNT"] . ", '". $rowHdr["KDASS"] . "', '". $rowHdr["ASURANSI"] . "', '". $rowHdr["KDHRG"] . "', ". $rowHdr["FOB"] . ", ". $rowHdr["CIF"] . ", ". $rowHdr["BRUTO"] . ", ". $rowHdr["NETTO"] . ", ". $rowHdr["JMCONT"] . ", ". $rowHdr["JMBRG"] . ", '010', 607);";
			// die($sqlMA_Hdr);
			$ins_hdr_p = $dbA->query($sqlMA_Hdr);
			// $ins_hdr_p = $dbA->prepare($sqlMA_Hdr);
			// $ins_hdr = $ins_hdr_p->execute();
			$result_cek = $dbA->query("SELECT count(1) AS adaHdr FROM tblPibHdr WHERE CAR = '$mdb_Car'");
			$ins_hdr = $result_cek->fetch();
			
			$status_ins = array();

			if($ins_hdr["adaHdr"]==1){
				$status_ins['car'] = $mdb_Car;
				$status_ins['t_bc20hdr'] = "1";
				//Update Nomor
				$new_number = (int)$last_number["Nomor"] + 1;
				$sqlMA_UpdNum = "UPDATE tblNomor SET Nomor = " . $new_number;
				$upd_nmr_p = $dbA->query($sqlMA_UpdNum);

				//Insert Detail
				$sqlDtl = "SELECT * FROM t_bc20dtl WHERE KODE_TRADER = 1 AND CAR = '$CAR'";
				$dataDtl = $db->query($sqlDtl);

				if($dataDtl->num_rows > 0){
					while($rowDtl = $dataDtl->fetch_assoc()){

						$sqlMA_Dtl = "INSERT INTO tblPibDtl (CAR, Serial, NoHs, SeriTrp, BrgUrai, Merk, Tipe, SpfLain, BrgAsal, DnilInv, DCif, KdSat, JmlSat, KemasJn, KemasJm, SatBmJm, SatCukJm, NettoDtl, FlBarangBaru, FlLartas, SpekTarif, DNilCuk, JmPC, SaldoAwalPC, SaldoAkhirPC) 
						VALUES ('" . $mdb_Car . "', " . $rowDtl["SERIAL"] . ", '" . $rowDtl["NOHS"] . "', " . $rowDtl["SERITRP"] . ", '" . $rowDtl["BRGURAI"] . "', '" . $rowDtl["MERK"] . "', '" . $rowDtl["TIPE"] . "', '" . $rowDtl["SPFLAIN"] . "', '" . $rowDtl["BRGASAL"] . "', " . $rowDtl["DNILINV"] . ", " . $rowDtl["DCIF"] . ", '" . $rowDtl["KDSAT"] . "', " . $rowDtl["JMLSAT"] . ", '" . $rowDtl["KEMASJN"] . "', " . $rowDtl["KEMASJM"] . ", " . $rowDtl["SATBMJM"] . ", " . $rowDtl["SATCUKJM"] . ", " . $rowDtl["NETTODTL"] . ", '" . $rowDtl["FLBARANGBARU"] . "', '" . $rowDtl["FLLARTAS"] . "', '" . $rowDtl["SPEKTARIF"] . "', " . $rowDtl["DNILCUK"] . ", " . $rowDtl["JMPC"] . ", " . $rowDtl["SALDOAWALPC"] . ", " . $rowDtl["SALDOAKHIRPC"] . " ); ";

					}

					$ins_dtl_p = $dbA->query($sqlMA_Dtl);
					$status_ins['t_bc20dtl'] = "1";

				}else{
					$status_ins['t_bc20dtl'] = "0";
				}

				//Insert Dokumen
				$sqlDok = "SELECT * FROM t_bc20dok WHERE KODE_TRADER = 1 AND CAR = '$CAR'";
				$dataDok = $db->query($sqlDok);

				if($dataDok->num_rows > 0){
					while($rowDok = $dataDok->fetch_assoc()){

						$sqlMA_Dok = "INSERT INTO tblPibDok (CAR, DokKd, DokNo, DokTg, DokInst, NoUrut, KdGroupDok) 
						VALUES ('" . $mdb_Car . "', '" . $rowDok["DOKKD"] . "', '" . $rowDok["DOKNO"] . "', '" . $rowDok["DOKTG"] . "', '" . $rowDok["DOKINST"] . "', " . $rowDok["SERIDOK"] . ", '" . $rowDok["KDGROUPDOK"] . "'); ";

					}

					$ins_dok_p = $dbA->query($sqlMA_Dok);
					$status_ins['t_bc20dok'] = "1";

				}else{
					$status_ins['t_bc20dok'] = "0";
				}

				//Insert Container
				$sqlCon = "SELECT * FROM t_bc20Con WHERE KODE_TRADER = 1 AND CAR = '$CAR'";
				$dataCon = $db->query($sqlCon);

				if($dataCon->num_rows > 0){
					while($rowCon = $dataCon->fetch_assoc()){

						$sqlMA_Con = "INSERT INTO tblPibCon (CAR, ContNo, ContUkur, ContTipe) 
						VALUES ('" . $mdb_Car . "', '" . $rowCon["CONTNO"] . "', '" . $rowCon["CONTUKUR"] . "', '" . $rowCon["CONTTIPE"] . "'); ";

					}

					$ins_con_p = $dbA->query($sqlMA_Con);
					$status_ins['t_bc20Con'] = "1";

				}else{
					$status_ins['t_bc20Con'] = "0";
				}

				//Insert Detail Dokumen
				$sqlDtlDok = "SELECT * FROM t_bc20dtldok WHERE KODE_TRADER = 1 AND CAR = '$CAR'";
				$dataDtlDok = $db->query($sqlDtlDok);

				if($dataDtlDok->num_rows > 0){ 
					while($rowDtlDok = $dataDtlDok->fetch_assoc()){

						$sqlMA_DtlDok = "INSERT INTO tblPibDtlDok (Car, Serial, KdFasDtl, NoUrut, DokKd, DokNo, DokTg, KdGroupDok) 
						VALUES ('" . $mdb_Car . "', " . $rowDtlDok["SERIBRG"] . ", '" . $rowDtlDok["KDFASDTL"] . "', " . $rowDtlDok["SERIDOK"] . ", '" . $rowDtlDok["DOKKD"] . "', '" . $rowDtlDok["DOKNO"] . "', '" . $rowDtlDok["DOKTG"] . "', '" . $rowDtlDok["KDGROUPDOK"] . "') ";

					}

					$ins_dtldok_p = $dbA->query($sqlMA_DtlDok);
					$status_ins['t_bc20dtldok'] = "1";

				}else{
					$status_ins['t_bc20dtldok'] = "0";
				}

				//Insert Fasilistas
				$sqlFas = "SELECT * FROM t_bc20fas WHERE KODE_TRADER = 1 AND CAR = '$CAR'";
				$dataFas = $db->query($sqlFas);

				if($dataFas->num_rows > 0){
					while($rowFas = $dataFas->fetch_assoc()){

						$sqlMA_Fas = "INSERT INTO tblPibFas (Car, Serial, KdFasBM, FasBM, KdFasCuk, FasCuk, KdFasPpn, FasPpn, KdFasPph, FasPph, KdFasPbm, FasPbm, KdFasBMAD, FasBMAD, BMADS, KdFasBMTP, FasBMTP, BMTPS, KdFasBMIM, FasBMIM, BMIMS, KdFasBMPB, FasBMPB, BMPBS) 
						VALUES ('". $mdb_Car ."', ". $rowFas["SERIAL"] .", '". $rowFas["KDFASBM"] ."', ". $rowFas["FASBM"] .", '". $rowFas["KDFASCUK"] ."', ". $rowFas["FASCUK"] .", '". $rowFas["KDFASPPN"] ."', ". $rowFas["FASPPN"] .", '". $rowFas["KDFASPPH"] ."', ". $rowFas["FASPPH"] .", '". $rowFas["KDFASPBM"] ."', ". $rowFas["FASPBM"] .", '". $rowFas["KDFASBMAD"] ."', ". $rowFas["FASBMAD"] .", '". $rowFas["BMADS"] ."', '". $rowFas["KDFASBMTP"] ."', ". $rowFas["FASBMTP"] .", '". $rowFas["BMTPS"] ."', '". $rowFas["KDFASBMIM"] ."', ". $rowFas["FASBMIM"] .", '". $rowFas["BMIMS"] ."', '". $rowFas["KDFASBMPB"] ."', ". $rowFas["FASBMPB"] .", '". $rowFas["BMPBS"] ."') ";

					}

					$ins_fas_p = $dbA->query($sqlMA_Fas);
					$status_ins['t_bc20fas'] = "1";

				}else{
					$status_ins['t_bc20fas'] = "0";
				}

				//Insert Kemasan
				$sqlKms = "SELECT * FROM t_bc20kms WHERE KODE_TRADER = 1 AND CAR = '$CAR'";
				$dataKms = $db->query($sqlKms);

				if($dataKms->num_rows > 0){
					while($rowKms = $dataKms->fetch_assoc()){

						$sqlMA_Kms = "INSERT INTO tblPibKms (CAR, JnKemas, JmKemas, merkkemas) 
						VALUES ('" . $mdb_Car . "', '" . $rowKms["JNKEMAS"] . "', " . $rowKms["JMKEMAS"] . ", '" . $rowKms["MERKKEMAS"] . "') ";

					}

					$ins_kms_p = $dbA->query($sqlMA_Kms);
					$status_ins['t_bc20kms'] = "1";

				}else{
					$status_ins['t_bc20kms'] = "0";
				}

				//Insert Pungutan
				$sqlPgt = "SELECT * FROM t_bc20pgt WHERE KODE_TRADER = 1 AND CAR = '$CAR'";
				$dataPgt = $db->query($sqlPgt);

				if($dataPgt->num_rows > 0){
					while($rowPgt = $dataPgt->fetch_assoc()){

						$sqlMA_Pgt = "INSERT INTO tblPibPgt (CAR, KdBeban, KdFasil, NilBeban) 
						VALUES ('" . $mdb_Car . "', '" . $rowPgt["KDBEBAN"] . "', '" . $rowPgt["KDFASIL"] . "', " . $rowPgt["NILBEBAN"] . ") ";

					}

					$ins_pgt_p = $dbA->query($sqlMA_Pgt);
					$status_ins['t_bc20pgt'] = "1";

				}else{
					$status_ins['t_bc20pgt'] = "0";
				}

				//Insert Tarif
				$sqlTrf = "SELECT * FROM t_bc20trf WHERE KODE_TRADER = 1 AND CAR = '$CAR'";
				$dataTrf = $db->query($sqlTrf);

				if($dataTrf->num_rows > 0){
					while($rowTrf = $dataTrf->fetch_assoc()){

						$sqlMA_Trf = "INSERT INTO tblPibTrf (CAR, NOHS, SERITRP, KDTRPBM, KDSATBM, TRPBM, KDCUK, KDTRPCUK, KDSATCUK, TRPCUK, TRPPPN, TRPPBM, TRPPPH, KdTrpBmAD, TrpBmAD, KdTrpBmTP, TrpBmTP, KdTrpBmIM, TrpBmIM, KdTrpBmPB, TrpBmPB, KDCUKSUB, HJECuk, KdKmsCuk, IsiPerKmsCuk) 
						VALUES ('" . $mdb_Car . "', '" . $rowTrf["NOHS"] . "', " . $rowTrf["SERITRP"] . ", '" . $rowTrf["KDTRPBM"] . "', '" . $rowTrf["KDSATBM"] . "', " . $rowTrf["TRPBM"] . ", '" . $rowTrf["KDCUK"] . "', '" . $rowTrf["KDTRPCUK"] . "', '" . $rowTrf["KDSATCUK"] . "', " . $rowTrf["TRPCUK"] . ", " . $rowTrf["TRPPPN"] . ", " . $rowTrf["TRPPBM"] . ", " . $rowTrf["TRPPPH"] . ", '" . $rowTrf["KdTrpBmAD"] . "', " . $rowTrf["TrpBmAD"] . ", '" . $rowTrf["KdTrpBmTP"] . "', " . $rowTrf["TrpBmTP"] . ", '" . $rowTrf["KdTrpBmIM"] . "', " . $rowTrf["TrpBmIM"] . ", '" . $rowTrf["KdTrpBmPB"] . "', " . $rowTrf["TrpBmPB"] . ", '" . $rowTrf["KDCUKSUB"] . "', " . $rowTrf["HJECuk"] . ", '" . $rowTrf["KdKmsCuk"] . "', " . $rowTrf["IsiPerKmsCuk"] . ") ";

					}

					$ins_trf_p = $dbA->query($sqlMA_Trf);
					$status_ins['t_bc20trf'] = "1";

				}else{
					$status_ins['t_bc20trf'] = "0";
				}

				// //Insert Respon
				// $sqlRes = "SELECT * FROM t_bc20res WHERE KODE_TRADER = 1 AND CAR = '$CAR'";
				// $dataRes = $db->query($sqlRes);

				// if($dataRes->num_rows > 0){
				// 	while($rowRes = $dataRes->fetch_assoc()){

				// 		$sqlMA_Res = "INSERT INTO tblPibRes (CAR, RESKD, RESTG, RESWK, DOKRESNO, DOKRESTG, KPBC, PIBNO, PIBTG, KDGUDANG, PEJABAT1, NIP1, JABATAN1, PEJABAT2, NIP2, JATUHTEMPO, KOMTG, KOMWK, DESKRIPSI, DIBACA, JmKemas, NoKemas, NPWPImp, NamaImp, AlamatImp, IDPPJK, NamaPPJK, AlamatPPJK, KodeBill, TanggalBill, TanggalJtTempo, TanggalAju, TotalBayar, Terbilang) 
				// 		VALUES ('" . $mdb_Car . "', '" . $rowRes["RESKD"] . "', '" . $rowRes["RESTG"] . "', '" . $rowRes["RESWK"] . "', '" . $rowRes["DOKRESNO"] . "', '" . $rowRes["DOKRESTG"] . "', '" . $rowRes["KPBC"] . "', '" . $rowRes["PIBNO"] . "', '" . $rowRes["PIBTG"] . "', '" . $rowRes["KDGUDANG"] . "', '" . $rowRes["PEJABAT1"] . "', '" . $rowRes["NIP1"] . "', '" . $rowRes["JABATAN1"] . "', '" . $rowRes["PEJABAT2"] . "', '" . $rowRes["NIP2"] . "', '" . $rowRes["JATUHTEMPO"] . "', '" . $rowRes["KOMTG"] . "', '" . $rowRes["KOMWK"] . "', '" . $rowRes["DESKRIPSI"] . "', '" . $rowRes["DIBACA"] . "', " . $rowRes["JmKemas"] . ", '" . $rowRes["NoKemas"] . "', '" . $rowRes["NPWPImp"] . "', '" . $rowRes["NamaImp"] . "', '" . $rowRes["AlamatImp"] . "', '" . $rowRes["IDPPJK"] . "', '" . $rowRes["NamaPPJK"] . "', '" . $rowRes["AlamatPPJK"] . "', '" . $rowRes["KodeBill"] . "', '" . $rowRes["TanggalBill"] . "', '" . $rowRes["TanggalJtTempo"] . "', '" . $rowRes["TanggalAju"] . "', '" . $rowRes["TotalBayar"] . "', '" . $rowRes["Terbilang"] . "')";

				// 	}

				// 	$status_ins['t_bc20res'] = "1";

				// }else{
				// 	$status_ins['t_bc20res'] = "0";
				// }

				// //Insert Respon Billing
				// $sqlResBill = "SELECT * FROM t_bc20resbill WHERE KODE_TRADER = 1 AND CAR = '$CAR'";
				// $dataResBill = $db->query($sqlResBill);

				// if($dataResBill->num_rows > 0){
				// 	while($rowResBill = $dataResBill->fetch_assoc()){

				// 		$sqlMA_Res = "INSERT INTO tblPIBResBill (Car, ResTg, ResWk, Akun, NPWP, Nilai) 
				// 		VALUES ('" . $mdb_Car . "', '" . $rowResBill["ResTg"] . "', '" . $rowResBill["ResWk"] . "', '" . $rowResBill["Akun"] . "', '" . $rowResBill["NPWP"] . "', '" . $rowResBill["Nilai"] . "')";

				// 	}

				// 	$status_ins['t_bc20resbill'] = "1";

				// }else{
				// 	$status_ins['t_bc20resbill'] = "0";
				// }

				// //Insert Respon NPBL
				// $sqlResNPBL = "SELECT * FROM t_bc20resnpbl WHERE KODE_TRADER = 1 AND CAR = '$CAR'";
				// $dataResNPBL = $db->query($sqlResNPBL);

				// if($dataResNPBL->num_rows > 0){
				// 	while($rowResNPBL = $dataResNPBL->fetch_assoc()){

				// 		$sqlMA_ResNPBL = "INSERT INTO tblPIBResNPBL (Car, ResKd, ResTg, ResWk, Serial, BrgUrai, Ketentuan, Pemberitahuan, Penetapan) 
				// 		VALUES ('" . $mdb_Car . "', '" . $rowResNPBL["ResKd"] . "', '" . $rowResNPBL["ResTg"] . "', '" . $rowResNPBL["ResWk"] . "', '" . $rowResNPBL["Serial"] . "', '" . $rowResNPBL["BrgUrai"] . "', '" . $rowResNPBL["Ketentuan"] . "', '" . $rowResNPBL["Pemberitahuan"] . "', '" . $rowResNPBL["Penetapan"] . "')";

				// 	}

				// 	$status_ins['t_bc20resnpbl'] = "1";

				// }else{
				// 	$status_ins['t_bc20resnpbl'] = "0";
				// }

				// //Insert Respon NPD
				// $sqlResNPD = "SELECT * FROM t_bc20resnpd WHERE KODE_TRADER = 1 AND CAR = '$CAR'";
				// $dataResNPD = $db->query($sqlResNPD);
				
				// if($dataResNPD->num_rows > 0){		
				// 	while($rowResNPD = $dataResNPD->fetch_assoc()){

				// 		$sqlMA_ResNPD = "INSERT INTO tblPIBResNPD (Car, ResTg, ResWk, Seri, UrDok, Nilai) 
				// 		VALUES ('" . $mdb_Car . "', '" . $rowResNPD["ResTg"] . "', '" . $rowResNPD["ResWk"] . "', '" . $rowResNPD["Seri"] . "', '" . $rowResNPD["UrDok"] . "', '" . $rowResNPD["Nilai"] . "')";

				// 	}

				// 	$status_ins['t_bc20resnpd'] = "1";

				// }else{
				// 	$status_ins['t_bc20resnpd'] = "0";
				// }

			}else{
				$status_ins['t_bc20hdr'] = "0";
			}			
			
			$t_bc20log = [
		        'CAR' => $mdb_Car, 
		        'ACTION_NAME' => "LOAD PIB TO MDB",
		        'DESCRIPTION' => implode("|", $status_ins), 
		        // 'DESCRIPTION' => http_build_query($status_ins), 
		        'WK_REKAM' => date("d-m-Y h:i:s")
		    ];

		    $insert_bc20log = insertRefernce('t_bc20log', $t_bc20log);

		}else{
			
		}

	}

	print_r($status_ins); die();

}
catch (PDOException $e) {
    echo $e->getMessage();
}