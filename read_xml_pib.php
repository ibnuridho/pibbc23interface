<?php
include('lib/database.php');
include('lib/main.php');

// READ XML
$pib_xml_dir = __DIR__ . DIRECTORY_SEPARATOR . "FROM" . DIRECTORY_SEPARATOR .date('Ymd');
$filter = "/*.xml";
$xmlFiles  = array_slice(glob($pib_xml_dir . $filter, GLOB_BRACE),0,1000);
// print_r($xmlFiles); die();

foreach($xmlFiles as $file){
	isset($dataHeader);
	isset($dataDoc);
	isset($dataItem);
	isset($dataCon);
	isset($dataItemDoc);
	isset($dataItemSpec);
	isset($dataItemVD);
	isset($dataItemFac);
	isset($dataKms);
	isset($dataPgt);
	isset($dataTrf);
	isset($insertData);

	$xmlFilePath   	= realpath($file);
	$xmlContent		= file_get_contents($xmlFilePath);
	$xmlArr		 	= simplexml_load_string($xmlContent);
	// print_r($xmlArr->DOCTYPE); die();
	if($xmlArr->DOCTYPE == "C5") {
		foreach ($xmlArr->HEADER as $xmlHeader) {
			$car = (string)($xmlHeader->CAR);
			//Car kosong, data baru
			if($car=="") $car = date("Ymdhis");
			$dataHeader = [
				'KODE_TRADER' => $kode_trader = ($xmlHeader->KODE_TRADER) ? (string)$xmlHeader->KODE_TRADER : "0",
				'CAR'	=> $car,
				'KDKPBC'	=> ($xmlHeader->KDKPBC) ? (string)$xmlHeader->KDKPBC : "-",
				'JNPIB'	=> ($xmlHeader->JNPIB) ? (string)$xmlHeader->JNPIB : "-",
				'JNIMP'	=> ($xmlHeader->JNIMP) ? (string)$xmlHeader->JNIMP : "-",
				'CRBYR'	=> ($xmlHeader->CRBYR) ? (string)$xmlHeader->CRBYR : "-",
				'DOKTUPKD'	=> ($xmlHeader->DOKTUPKD) ? (string)$xmlHeader->DOKTUPKD : "-",
				'DOKTUPNO'	=> ($xmlHeader->DOKTUPNO) ? (string)$xmlHeader->DOKTUPNO : "-",
				'DOKTUPTG'	=> ($xmlHeader->DOKTUPTG) ? (string)$xmlHeader->DOKTUPTG : "-",
				'POSNO'	=> ($xmlHeader->POSNO) ? (string)$xmlHeader->POSNO : "-",
				'POSSUB'	=> ($xmlHeader->POSSUB) ? (string)$xmlHeader->POSSUB : "-",
				'POSSUBSUB'	=> ($xmlHeader->POSSUBSUB) ? (string)$xmlHeader->POSSUBSUB : "-",
				'IMPID'	=> ($xmlHeader->IMPID) ? (string)$xmlHeader->IMPID : "-",
				'IMPNPWP'	=> ($xmlHeader->IMPNPWP) ? (string)$xmlHeader->IMPNPWP : "-",
				'IMPNAMA'	=> ($xmlHeader->IMPNAMA) ? (string)$xmlHeader->IMPNAMA : "-",
				'IMPALMT'	=> ($xmlHeader->IMPALMT) ? (string)$xmlHeader->IMPALMT : "-",
				'IMPSTATUS'	=> ($xmlHeader->IMPSTATUS) ? (string)$xmlHeader->IMPSTATUS : "-",
				'APIKD'	=> ($xmlHeader->APIKD) ? (string)$xmlHeader->APIKD : "-",
				'APINO'	=> ($xmlHeader->APINO) ? (string)$xmlHeader->APINO : "-",
				'PPJKID'	=> ($xmlHeader->PPJKID) ? (string)$xmlHeader->PPJKID : "-",
				'PPJKNPWP'	=> ($xmlHeader->PPJKNPWP) ? (string)$xmlHeader->PPJKNPWP : "-",
				'PPJKNAMA'	=> ($xmlHeader->PPJKNAMA) ? (string)$xmlHeader->PPJKNAMA : "-",
				'PPJKALMT'	=> ($xmlHeader->PPJKALMT) ? (string)$xmlHeader->PPJKALMT : "-",
				'PPJKNO'	=> ($xmlHeader->PPJKNO) ? (string)$xmlHeader->PPJKNO : "-",
				'PPJKTG'	=> ($xmlHeader->PPJKTG) ? (string)$xmlHeader->PPJKTG : "-",
				'INDID'	=> ($xmlHeader->INDID) ? (string)$xmlHeader->INDID : "-",
				'INDNPWP'	=> ($xmlHeader->INDNPWP) ? (string)$xmlHeader->INDNPWP : "-",
				'INDNAMA'	=> ($xmlHeader->INDNAMA) ? (string)$xmlHeader->INDNAMA : "-",
				'INDALMT'	=> ($xmlHeader->INDALMT) ? (string)$xmlHeader->INDALMT : "-",
				'PASOKNAMA'	=> ($xmlHeader->PASOKNAMA) ? (string)$xmlHeader->PASOKNAMA : "-",
				'PASOKALMT'	=> ($xmlHeader->PASOKALMT) ? (string)$xmlHeader->PASOKALMT : "-",
				'PASOKNEG'	=> ($xmlHeader->PASOKNEG) ? (string)$xmlHeader->PASOKNEG : "-",
				'PELBKR'	=> ($xmlHeader->PELBKR) ? (string)$xmlHeader->PELBKR : "-",
				'PELMUAT'	=> ($xmlHeader->PELMUAT) ? (string)$xmlHeader->PELMUAT : "-",
				'PELTRANSIT'	=> ($xmlHeader->PELTRANSIT) ? (string)$xmlHeader->PELTRANSIT : "-",
				'TMPTBN'	=> ($xmlHeader->TMPTBN) ? (string)$xmlHeader->TMPTBN : "-",
				'MODA'	=> ($xmlHeader->MODA) ? (string)$xmlHeader->MODA : "-",
				'ANGKUTNAMA'	=> ($xmlHeader->ANGKUTNAMA) ? (string)$xmlHeader->ANGKUTNAMA : "-",
				'ANGKUTNO'	=> ($xmlHeader->ANGKUTNO) ? (string)$xmlHeader->ANGKUTNO : "-",
				'ANGKUTFL'	=> ($xmlHeader->ANGKUTFL) ? (string)$xmlHeader->ANGKUTFL : "-",
				'TGTIBA'	=> ($xmlHeader->TGTIBA) ? (string)$xmlHeader->TGTIBA : "-",
				'KDVAL'	=> ($xmlHeader->KDVAL) ? (string)$xmlHeader->KDVAL : "-",
				'NDPBM'	=> ($xmlHeader->NDPBM) ? (string)$xmlHeader->NDPBM : "-",
				'NILINV'	=> ($xmlHeader->NILINV) ? (string)$xmlHeader->NILINV : "-",
				'FREIGHT'	=> ($xmlHeader->FREIGHT) ? (string)$xmlHeader->FREIGHT : "-",
				'BTAMBAHAN'	=> ($xmlHeader->BTAMBAHAN) ? (string)$xmlHeader->BTAMBAHAN : "-",
				'DISCOUNT'	=> ($xmlHeader->DISCOUNT) ? (string)$xmlHeader->DISCOUNT : "-",
				'KDASS'	=> ($xmlHeader->KDASS) ? (string)$xmlHeader->KDASS : "-",
				'ASURANSI'	=> ($xmlHeader->ASURANSI) ? (string)$xmlHeader->ASURANSI : "-",
				'KDHRG'	=> ($xmlHeader->KDHRG) ? (string)$xmlHeader->KDHRG : "-",
				'FOB'	=> ($xmlHeader->FOB) ? (string)$xmlHeader->FOB : "-",
				'CIF'	=> ($xmlHeader->CIF) ? (string)$xmlHeader->CIF : "-",
				'CIFRP'	=> ($xmlHeader->CIFRP) ? (string)$xmlHeader->CIFRP : "-",
				'BRUTO'	=> ($xmlHeader->BRUTO) ? (string)$xmlHeader->BRUTO : "-",
				'NETTO'	=> ($xmlHeader->NETTO) ? (string)$xmlHeader->NETTO : "-",
				'JMCONT'	=> ($xmlHeader->JMCONT) ? (string)$xmlHeader->JMCONT : "-",
				'JMBRG'	=> ($xmlHeader->JMBRG) ? (string)$xmlHeader->JMBRG : "-",
				'BILLNPWP'	=> ($xmlHeader->BILLNPWP) ? (string)$xmlHeader->BILLNPWP : "-",
				'BILLNAMA'	=> ($xmlHeader->BILLNAMA) ? (string)$xmlHeader->BILLNAMA : "-",
				'BILLALAMAT'	=> ($xmlHeader->BILLALAMAT) ? (string)$xmlHeader->BILLALAMAT : "-",
				'PERNYATAAN'	=> ($xmlHeader->PERNYATAAN) ? (string)$xmlHeader->PERNYATAAN : "-",
				'JNSTRANS'	=> ($xmlHeader->JNSTRANS) ? (string)$xmlHeader->JNSTRANS : "-",
				'VD'	=> ($xmlHeader->VD) ? (string)$xmlHeader->VD : "-",
				'NILVD'	=> ($xmlHeader->NILVD) ? (string)$xmlHeader->NILVD : "-",
				'NAMA_TTD'	=> ($xmlHeader->NAMA_TTD) ? (string)$xmlHeader->NAMA_TTD : "-",
				'KOTA_TTD'	=> ($xmlHeader->KOTA_TTD) ? (string)$xmlHeader->KOTA_TTD : "-",
				'TANGGAL_TTD'	=> ($xmlHeader->TANGGAL_TTD) ? (string)$xmlHeader->TANGGAL_TTD : "-",
			];	
			// print_r($dataHeader); die();
			$insertData[] = insertRefernce('t_bc20hdr', $dataHeader);
		}

		foreach ($xmlArr->HEADER->PACKAGES as $xmlKmss) {
			foreach ($xmlKmss->PACKAGE as $xmlKms) {
				$dataKms = [
					'KODE_TRADER' => $kode_trader,
					'CAR'	=> $car,
					'JNKEMAS' => ($xmlKms->JNKEMAS) ? (string)$xmlKms->JNKEMAS : "",
					'JMKEMAS' => ($xmlKms->JMKEMAS) ? (string)$xmlKms->JMKEMAS : "",
					'MERKKEMAS' => ($xmlKms->MERKKEMAS) ? (string)$xmlKms->MERKKEMAS : "",
				];
				$insertData[] = insertRefernce('t_bc20kms', $dataKms);
			}
		}

		foreach ($xmlArr->HEADER->CHARGES as $xmlPgts) {
			foreach ($xmlPgts->CHARGE as $xmlPgt) {
				$dataPgt = [
					'KODE_TRADER' => $kode_trader,
					'CAR'	=> $car,
					'KDBEBAN' => ($xmlPgt->KDBEBAN) ? (string)$xmlPgt->KDBEBAN : "",
					'KDFASIL' => ($xmlPgt->KDFASIL) ? (string)$xmlPgt->KDFASIL : "",
					'NILBEBAN' => ($xmlPgt->NILBEBAN) ? (string)$xmlPgt->NILBEBAN : "",
				];
				$insertData[] = insertRefernce('t_bc20pgt', $dataPgt);
			}
		}
		// die();

		foreach ($xmlArr->DETAIL->DOCUMENTS as $xmlDocs) {
			foreach ($xmlDocs->DOCUMENT as $xmlDoc) {
				$dataDoc = [
					'KODE_TRADER' => $kode_trader,
					'CAR'	=> $car,
					'SERIDOK' => ($xmlDoc->SERIDOK) ? (string)$xmlDoc->SERIDOK : "",
					'DOKKD' => ($xmlDoc->DOKKD) ? (string)$xmlDoc->DOKKD : "",
					'DOKNO' => ($xmlDoc->DOKNO) ? (string)$xmlDoc->DOKNO : "",
					'DOKTG' => ($xmlDoc->DOKTG) ? (string)$xmlDoc->DOKTG : "",
					'KDGROUPDOK' => ($xmlDoc->KDGROUPDOK) ? (string)$xmlDoc->KDGROUPDOK : "",
				];
				$insertData[] = insertRefernce('t_bc20dok', $dataDoc);
			}
		}

		foreach ($xmlArr->DETAIL->ITEMS as $xmlItems) {
			foreach ($xmlItems->ITEM as $xmlItem) {
				$dataItem = [
					'KODE_TRADER' => $kode_trader,
					'CAR'	=> $car,
					'SERIAL' => ($xmlItem->SERIAL) ? (string)$xmlItem->SERIAL : "",
					'NOHS' => ($xmlItem->NOHS) ? (string)$xmlItem->NOHS : "",
					'SERITRP' => ($xmlItem->SERITRP) ? (string)$xmlItem->SERITRP : "",
					'BRGURAI' => ($xmlItem->BRGURAI) ? (string)$xmlItem->BRGURAI : "",
					'MERK' => ($xmlItem->MERK) ? (string)$xmlItem->MERK : "",
					'TIPE' => ($xmlItem->TIPE) ? (string)$xmlItem->TIPE : "",
					'SPFLAIN' => ($xmlItem->SPFLAIN) ? (string)$xmlItem->SPFLAIN : "",
					'BRGASAL' => ($xmlItem->BRGASAL) ? (string)$xmlItem->BRGASAL : "",
					'DNILINV' => ($xmlItem->DNILINV) ? (string)$xmlItem->DNILINV : "",
					'DCIF' => ($xmlItem->DCIF) ? (string)$xmlItem->DCIF : "",
					'KDSAT' => ($xmlItem->KDSAT) ? (string)$xmlItem->KDSAT : "",
					'JMLSAT' => ($xmlItem->JMLSAT) ? (string)$xmlItem->JMLSAT : "",
					'KEMASJN' => ($xmlItem->KEMASJN) ? (string)$xmlItem->KEMASJN : "",
					'KEMASJM' => ($xmlItem->KEMASJM) ? (string)$xmlItem->KEMASJM : "",
					'SATBMJM' => ($xmlItem->SATBMJM) ? (string)$xmlItem->SATBMJM : "",
					'SATCUKJM' => ($xmlItem->SATCUKJM) ? (string)$xmlItem->SATCUKJM : "",
					'NETTODTL' => ($xmlItem->NETTODTL) ? (string)$xmlItem->NETTODTL : "",
					'FLBARANGBARU' => ($xmlItem->FLBARANGBARU) ? (string)$xmlItem->FLBARANGBARU : "",
					'FLLARTAS' => ($xmlItem->FLLARTAS) ? (string)$xmlItem->FLLARTAS : "",
					'SPEKTARIF' => ($xmlItem->SPEKTARIF) ? (string)$xmlItem->SPEKTARIF : "",
					'DNILCUK' => ($xmlItem->DNILCUK) ? (string)$xmlItem->DNILCUK : "",
					'JMPC' => ($xmlItem->JMPC) ? (string)$xmlItem->JMPC : "",
					'SALDOAWALPC' => ($xmlItem->SALDOAWALPC) ? (string)$xmlItem->SALDOAWALPC : "",
					'SALDOAKHIRPC' => ($xmlItem->SALDOAKHIRPC) ? (string)$xmlItem->SALDOAKHIRPC : "",
				];
				$insertData[] = insertRefernce('t_bc20dtl', $dataItem);
			}
		}

		foreach ($xmlArr->DETAIL->CONTAINERS as $xmlCons) {
			foreach ($xmlCons->CONTAINER as $xmlCon) {
				$dataCon = [
					'KODE_TRADER' => $kode_trader,
					'CAR'	=> $car,
					'CONTNO' => ($xmlCon->CONTNO) ? (string)$xmlCon->CONTNO : "",
					'CONTUKUR' => ($xmlCon->CONTUKUR) ? (string)$xmlCon->CONTUKUR : "",
					'CONTTIPE' => ($xmlCon->CONTTIPE) ? (string)$xmlCon->CONTTIPE : "",
					'NOSEAL' => ($xmlCon->NOSEAL) ? (string)$xmlCon->NOSEAL : "",
				];
				$insertData[] = insertRefernce('t_bc20con', $dataCon);
			}
		}

		foreach ($xmlArr->DETAIL->ITEMDOCUMENTS as $xmlItemDocs) {
			foreach ($xmlItemDocs->ITEMDOCUMENT as $xmlItemDoc) {
				$dataItemDoc = [
					'KODE_TRADER' => $kode_trader,
					'CAR'	=> $car,
					'SERIBRG' => ($xmlItemDoc->SERIBRG) ? (string)$xmlItemDoc->SERIBRG : "",
					'KDFASDTL' => ($xmlItemDoc->KDFASDTL) ? (string)$xmlItemDoc->KDFASDTL : "",
					'SERIDOK' => ($xmlItemDoc->SERIDOK) ? (string)$xmlItemDoc->SERIDOK : "",
					'DOKKD' => ($xmlItemDoc->DOKKD) ? (string)$xmlItemDoc->DOKKD : "",
					'DOKNO' => ($xmlItemDoc->DOKNO) ? (string)$xmlItemDoc->DOKNO : "",
					'DOKTG' => ($xmlItemDoc->DOKTG) ? (string)$xmlItemDoc->DOKTG : "",
					'KDGROUPDOK' => ($xmlItemDoc->KDGROUPDOK) ? (string)$xmlItemDoc->KDGROUPDOK : "",
				];
			}
			$insertData[] = insertRefernce('t_bc20dtldok', $dataItemDoc);
		}

		foreach ($xmlArr->DETAIL->ITEMSPECIALSPEC as $xmlItemSpecs) {
			foreach ($xmlItemSpecs->ITEMSPEC as $xmlItemSpec) {
				$dataItemSpec = [
					'KODE_TRADER' => $kode_trader,
					'CAR'	=> $car,
					'SERIBRG' => ($xmlItemSpec->SERIBRG) ? (string)$xmlItemSpec->SERIBRG : "",
					'CAS1' => ($xmlItemSpec->CAS1) ? (string)$xmlItemSpec->CAS1 : "",
					'CAS2' => ($xmlItemSpec->CAS2) ? (string)$xmlItemSpec->CAS2 : "",
				];
				$insertData[] = insertRefernce('t_bc20dtlspekkhusus', $dataItemSpec);
			}
		}

		foreach ($xmlArr->DETAIL->ITEMVD as $xmlItemVDs) {
			foreach ($xmlItemVDs->VD as $xmlItemVD) {
				$dataItemVD = [
					'KODE_TRADER' => $kode_trader,
					'CAR'	=> $car,
					'SERIBRG' => ($xmlItemVD->SERIBRG) ? (string)$xmlItemVD->SERIBRG : "",
					'JENIS' => ($xmlItemVD->JENIS) ? (string)$xmlItemVD->JENIS : "",
					'NILAI' => ($xmlItemVD->NILAI) ? (string)$xmlItemVD->NILAI : "",
					'TGJATUHTEMPO' => ($xmlItemVD->TGJATUHTEMPO) ? (string)$xmlItemVD->TGJATUHTEMPO : "",
				];
				$insertData[] = insertRefernce('t_bc20dtlvd', $dataItemVD);
			}
		}

		foreach ($xmlArr->DETAIL->ITEMFACILITY as $xmlItemFacs) {
			foreach ($xmlItemFacs->FACILITY as $xmlItemFac) {
				$dataFac = [
					'KODE_TRADER' => $kode_trader,
					'CAR'	=> $car,
					'SERIBRG' => ($xmlItemFac->SERIBRG) ? (string)$xmlItemFac->SERIBRG : "",
					'KDFASBM' => ($xmlItemFac->KDFASBM) ? (string)$xmlItemFac->KDFASBM : "",
					'FASBM' => ($xmlItemFac->FASBM) ? (string)$xmlItemFac->FASBM : "",
					'KDFASCUK' => ($xmlItemFac->KDFASCUK) ? (string)$xmlItemFac->KDFASCUK : "",
					'FASCUK' => ($xmlItemFac->FASCUK) ? (string)$xmlItemFac->FASCUK : "",
					'KDFASPPN' => ($xmlItemFac->KDFASPPN) ? (string)$xmlItemFac->KDFASPPN : "",
					'FASPPN' => ($xmlItemFac->FASPPN) ? (string)$xmlItemFac->FASPPN : "",
					'KDFASPPH' => ($xmlItemFac->KDFASPPH) ? (string)$xmlItemFac->KDFASPPH : "",
					'FASPPH' => ($xmlItemFac->FASPPH) ? (string)$xmlItemFac->FASPPH : "",
					'KDFASPBM' => ($xmlItemFac->KDFASPBM) ? (string)$xmlItemFac->KDFASPBM : "",
					'FASPBM' => ($xmlItemFac->FASPBM) ? (string)$xmlItemFac->FASPBM : "",
					'KdFasBMAD' => ($xmlItemFac->KdFasBMAD) ? (string)$xmlItemFac->KdFasBMAD : "",
					'FasBMAD' => ($xmlItemFac->FasBMAD) ? (string)$xmlItemFac->FasBMAD : "",
					'BMADS' => ($xmlItemFac->BMADS) ? (string)$xmlItemFac->BMADS : "",
					'KdFasBMTP' => ($xmlItemFac->KdFasBMTP) ? (string)$xmlItemFac->KdFasBMTP : "",
					'FasBMTP' => ($xmlItemFac->FasBMTP) ? (string)$xmlItemFac->FasBMTP : "",
					'BMTPS' => ($xmlItemFac->BMTPS) ? (string)$xmlItemFac->BMTPS : "",
					'KdFasBMIM' => ($xmlItemFac->KdFasBMIM) ? (string)$xmlItemFac->KdFasBMIM : "",
					'FasBMIM' => ($xmlItemFac->FasBMIM) ? (string)$xmlItemFac->FasBMIM : "",
					'BMIMS' => ($xmlItemFac->BMIMS) ? (string)$xmlItemFac->BMIMS : "",
					'KdFasBMPB' => ($xmlItemFac->KdFasBMPB) ? (string)$xmlItemFac->KdFasBMPB : "",
					'FasBMPB' => ($xmlItemFac->FasBMPB) ? (string)$xmlItemFac->FasBMPB : "",
					'BMPBS' => ($xmlItemFac->BMPBS) ? (string)$xmlItemFac->BMPBS : "",
				];
				$insertData[] = insertRefernce('t_bc20fas', $dataFac);
			}
		}

		foreach ($xmlArr->DETAIL->ITEMTARIFS as $xmlTrfs) {
			foreach ($xmlTrfs->TARIF as $xmlTrf) {
				$dataTrf = [
					'KODE_TRADER' => $kode_trader,
					'CAR'	=> $car,
					'NOHS' => ($xmlTrf->NOHS) ? (string)$xmlTrf->NOHS : "",
					'SERITRP' => ($xmlTrf->SERITRP) ? (string)$xmlTrf->SERITRP : "",
					'KDTRPBM' => ($xmlTrf->KDTRPBM) ? (string)$xmlTrf->KDTRPBM : "",
					'KDSATBM' => ($xmlTrf->KDSATBM) ? (string)$xmlTrf->KDSATBM : "",
					'TRPBM' => ($xmlTrf->TRPBM) ? (string)$xmlTrf->TRPBM : "",
					'KDCUK' => ($xmlTrf->KDCUK) ? (string)$xmlTrf->KDCUK : "",
					'KDTRPCUK' => ($xmlTrf->KDTRPCUK) ? (string)$xmlTrf->KDTRPCUK : "",
					'KDSATCUK' => ($xmlTrf->KDSATCUK) ? (string)$xmlTrf->KDSATCUK : "",
					'TRPCUK' => ($xmlTrf->TRPCUK) ? (string)$xmlTrf->TRPCUK : "",
					'TRPPPN' => ($xmlTrf->TRPPPN) ? (string)$xmlTrf->TRPPPN : "",
					'TRPPBM' => ($xmlTrf->TRPPBM) ? (string)$xmlTrf->TRPPBM : "",
					'TRPPPH' => ($xmlTrf->TRPPPH) ? (string)$xmlTrf->TRPPPH : "",
					'KdTrpBmAD' => ($xmlTrf->KdTrpBmAD) ? (string)$xmlTrf->KdTrpBmAD : "",
					'TrpBmAD' => ($xmlTrf->TrpBmAD) ? (string)$xmlTrf->TrpBmAD : "",
					'KdTrpBmTP' => ($xmlTrf->KdTrpBmTP) ? (string)$xmlTrf->KdTrpBmTP : "",
					'TrpBmTP' => ($xmlTrf->TrpBmTP) ? (string)$xmlTrf->TrpBmTP : "",
					'KdTrpBmIM' => ($xmlTrf->KdTrpBmIM) ? (string)$xmlTrf->KdTrpBmIM : "",
					'TrpBmIM' => ($xmlTrf->TrpBmIM) ? (string)$xmlTrf->TrpBmIM : "",
					'KdTrpBmPB' => ($xmlTrf->KdTrpBmPB) ? (string)$xmlTrf->KdTrpBmPB : "",
					'TrpBmPB' => ($xmlTrf->TrpBmPB) ? (string)$xmlTrf->TrpBmPB : "",
					'KDCUKSUB' => ($xmlTrf->KDCUKSUB) ? (string)$xmlTrf->KDCUKSUB : "",
					'HJECuk' => ($xmlTrf->HJECuk) ? (string)$xmlTrf->HJECuk : "",
					'KdKmsCuk' => ($xmlTrf->KdKmsCuk) ? (string)$xmlTrf->KdKmsCuk : "",
					'IsiPerKmsCuk' => ($xmlTrf->IsiPerKmsCuk) ? (string)$xmlTrf->IsiPerKmsCuk : "",
				];
				$insertData[] = insertRefernce('t_bc20trf', $dataTrf);
			}
		}

		print_r(json_encode($insertData));

	}else{
		
	}
}
// READ XML

$db->close();