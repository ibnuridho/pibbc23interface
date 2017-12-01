<?php
include('lib/database.php');
include('lib/main.php');

// READ XML
$bc23_xml_dir = $_SERVER['DOCUMENT_ROOT']."/pibbc23interface/xml/bc23/".date('Ymd');
$filter = "/*.xml";
$xmlFiles  = array_slice(glob($bc23_xml_dir . $filter, GLOB_BRACE),0,1000);

foreach($xmlFiles as $file){
	isset($dataHeader);
	isset($dataItem);
	isset($dataDoc);
	isset($dataItemDoc);
	isset($dataCon);
	isset($dataKms);
	isset($dataPgt);
	isset($dataTrf);
	isset($insertData);

	$xmlFilePath   	= realpath($file);
	$xmlContent		= file_get_contents($xmlFilePath);
	$xmlArr		 	= simplexml_load_string($xmlContent);
	
	foreach ($xmlArr->HEADER as $xmlHeader) {
		$dataHeader = [
			'KODE_TRADER' => $kode_trader = ($xmlHeader->KODE_TRADER) ? (string)$xmlHeader->KODE_TRADER : "0",
			'CAR' => $car = ($xmlHeader->CAR) ? (string)$xmlHeader->CAR : "-",
			'KDKPBC' => ($xmlHeader->KDKPBC) ? (string)$xmlHeader->KDKPBC : "",
			'TUJUAN' => ($xmlHeader->TUJUAN) ? (string)$xmlHeader->TUJUAN : "",
			'PASOKNAMA' => ($xmlHeader->PASOKNAMA) ? (string)$xmlHeader->PASOKNAMA : "",
			'PASOKALMT' => ($xmlHeader->PASOKALMT) ? (string)$xmlHeader->PASOKALMT : "",
			'PASOKNEG' => ($xmlHeader->PASOKNEG) ? (string)$xmlHeader->PASOKNEG : "",
			'USAHAID' => ($xmlHeader->USAHAID) ? (string)$xmlHeader->USAHAID : "",
			'USAHANPWP' => ($xmlHeader->USAHANPWP) ? (string)$xmlHeader->USAHANPWP : "",
			'KDTPB' => ($xmlHeader->KDTPB) ? (string)$xmlHeader->KDTPB : "",
			'REGISTRASI' => ($xmlHeader->REGISTRASI) ? (string)$xmlHeader->REGISTRASI : "",
			'USAHANAMA' => ($xmlHeader->USAHANAMA) ? (string)$xmlHeader->USAHANAMA : "",
			'USAHAALMT' => ($xmlHeader->USAHAALMT) ? (string)$xmlHeader->USAHAALMT : "",
			'USAHASTATUS' => ($xmlHeader->USAHASTATUS) ? (string)$xmlHeader->USAHASTATUS : "",
			'APIKD' => ($xmlHeader->APIKD) ? (string)$xmlHeader->APIKD : "",
			'APINO' => ($xmlHeader->APINO) ? (string)$xmlHeader->APINO : "",
			'PPJKID' => ($xmlHeader->PPJKID) ? (string)$xmlHeader->PPJKID : "",
			'PPJKNPWP' => ($xmlHeader->PPJKNPWP) ? (string)$xmlHeader->PPJKNPWP : "",
			'PPJKNAMA' => ($xmlHeader->PPJKNAMA) ? (string)$xmlHeader->PPJKNAMA : "",
			'PPJKALMT' => ($xmlHeader->PPJKALMT) ? (string)$xmlHeader->PPJKALMT : "",
			'PPJKNO' => ($xmlHeader->PPJKNO) ? (string)$xmlHeader->PPJKNO : "",
			'PPJKTG' => ($xmlHeader->PPJKTG) ? (string)$xmlHeader->PPJKTG : "",
			'INDID' => ($xmlHeader->INDID) ? (string)$xmlHeader->INDID : "",
			'INDNPWP' => ($xmlHeader->INDNPWP) ? (string)$xmlHeader->INDNPWP : "",
			'INDNAMA' => ($xmlHeader->INDNAMA) ? (string)$xmlHeader->INDNAMA : "",
			'INDALMT' => ($xmlHeader->INDALMT) ? (string)$xmlHeader->INDALMT : "",
			'INDAPIKD' => ($xmlHeader->INDAPIKD) ? (string)$xmlHeader->INDAPIKD : "",
			'INDAPINO' => ($xmlHeader->INDAPINO) ? (string)$xmlHeader->INDAPINO : "",
			'MODA' => ($xmlHeader->MODA) ? (string)$xmlHeader->MODA : "",
			'ANGKUTNAMA' => ($xmlHeader->ANGKUTNAMA) ? (string)$xmlHeader->ANGKUTNAMA : "",
			'ANGKUTNO' => ($xmlHeader->ANGKUTNO) ? (string)$xmlHeader->ANGKUTNO : "",
			'ANGKUTFL' => ($xmlHeader->ANGKUTFL) ? (string)$xmlHeader->ANGKUTFL : "",
			'PELBKR' => ($xmlHeader->PELBKR) ? (string)$xmlHeader->PELBKR : "",
			'PELMUAT' => ($xmlHeader->PELMUAT) ? (string)$xmlHeader->PELMUAT : "",
			'PELTRANSIT' => ($xmlHeader->PELTRANSIT) ? (string)$xmlHeader->PELTRANSIT : "",
			'BC23NO' => ($xmlHeader->BC23NO) ? (string)$xmlHeader->BC23NO : "",
			'BC23TG' => ($xmlHeader->BC23TG) ? (string)$xmlHeader->BC23TG : "",
			'KDKPBCBONGKAR' => ($xmlHeader->KDKPBCBONGKAR) ? (string)$xmlHeader->KDKPBCBONGKAR : "",
			'KDKPBCAWAS' => ($xmlHeader->KDKPBCAWAS) ? (string)$xmlHeader->KDKPBCAWAS : "",
			'DOKTUPKD' => ($xmlHeader->DOKTUPKD) ? (string)$xmlHeader->DOKTUPKD : "",
			'DOKTUPNO' => ($xmlHeader->DOKTUPNO) ? (string)$xmlHeader->DOKTUPNO : "",
			'DOKTUPTG' => ($xmlHeader->DOKTUPTG) ? (string)$xmlHeader->DOKTUPTG : "",
			'POSNO' => ($xmlHeader->POSNO) ? (string)$xmlHeader->POSNO : "",
			'POSSUB' => ($xmlHeader->POSSUB) ? (string)$xmlHeader->POSSUB : "",
			'POSSUBSUB' => ($xmlHeader->POSSUBSUB) ? (string)$xmlHeader->POSSUBSUB : "",
			'TMPTBN' => ($xmlHeader->TMPTBN) ? (string)$xmlHeader->TMPTBN : "",
			'KDVAL' => ($xmlHeader->KDVAL) ? (string)$xmlHeader->KDVAL : "",
			'NDPBM' => ($xmlHeader->NDPBM) ? (string)$xmlHeader->NDPBM : "",
			'NILINV' => ($xmlHeader->NILINV) ? (string)$xmlHeader->NILINV : "",
			'FREIGHT' => ($xmlHeader->FREIGHT) ? (string)$xmlHeader->FREIGHT : "",
			'BTAMBAHAN' => ($xmlHeader->BTAMBAHAN) ? (string)$xmlHeader->BTAMBAHAN : "",
			'DISKON' => ($xmlHeader->DISKON) ? (string)$xmlHeader->DISKON : "",
			'KDASS' => ($xmlHeader->KDASS) ? (string)$xmlHeader->KDASS : "",
			'ASURANSI' => ($xmlHeader->ASURANSI) ? (string)$xmlHeader->ASURANSI : "",
			'KDHRG' => ($xmlHeader->KDHRG) ? (string)$xmlHeader->KDHRG : "",
			'FOB' => ($xmlHeader->FOB) ? (string)$xmlHeader->FOB : "",
			'CIF' => ($xmlHeader->CIF) ? (string)$xmlHeader->CIF : "",
			'CIFRP' => ($xmlHeader->CIFRP) ? (string)$xmlHeader->CIFRP : "",
			'BRUTO' => ($xmlHeader->BRUTO) ? (string)$xmlHeader->BRUTO : "",
			'NETTO' => ($xmlHeader->NETTO) ? (string)$xmlHeader->NETTO : "",
			'JMCONT' => ($xmlHeader->JMCONT) ? (string)$xmlHeader->JMCONT : "",
			'JMBRG' => ($xmlHeader->JMBRG) ? (string)$xmlHeader->JMBRG : "",
			'TGLTTD' => ($xmlHeader->TGLTTD) ? (string)$xmlHeader->TGLTTD : "",
			'KOTATTD' => ($xmlHeader->KOTATTD) ? (string)$xmlHeader->KOTATTD : "",
			'NAMATTD' => ($xmlHeader->NAMATTD) ? (string)$xmlHeader->NAMATTD : "",
			'CONTAKPERSON' => ($xmlHeader->CONTAKPERSON) ? (string)$xmlHeader->CONTAKPERSON : "",
			'NOPHONE' => ($xmlHeader->NOPHONE) ? (string)$xmlHeader->NOPHONE : "",
			'NOFAX' => ($xmlHeader->NOFAX) ? (string)$xmlHeader->NOFAX : "",
			'EMAIL' => ($xmlHeader->EMAIL) ? (string)$xmlHeader->EMAIL : "",
			'KDFAS' => ($xmlHeader->KDFAS) ? (string)$xmlHeader->KDFAS : "",
			'NOSKEPFAS' => ($xmlHeader->NOSKEPFAS) ? (string)$xmlHeader->NOSKEPFAS : "",
			'TGLSKEPFAS' => ($xmlHeader->TGLSKEPFAS) ? (string)$xmlHeader->TGLSKEPFAS : "",
		];	
		$insertData[] = insertRefernce('t_bc23hdr', $dataHeader);
	}

	foreach ($xmlArr->DETAIL->DOCUMENTS as $xmlDocs) {
		foreach ($xmlDocs->DOCUMENT as $xmlDoc) {
			$dataDoc = [
				'KODE_TRADER' => $kode_trader,
				'CAR'	=> $car,
				'DOKKD' => ($xmlDoc->DOKKD) ? (string)$xmlDoc->DOKKD : "",
				'DOKNO' => ($xmlDoc->DOKNO) ? (string)$xmlDoc->DOKNO : "",
				'DOKTG' => ($xmlDoc->DOKTG) ? (string)$xmlDoc->DOKTG : "",
				'SERIDOK' => ($xmlDoc->SERIDOK) ? (string)$xmlDoc->SERIDOK : "",
			];
			$insertData[] = insertRefernce('t_bc23dok', $dataDoc);
		}
	}

	foreach ($xmlArr->DETAIL->ITEMS as $xmlItems) {
		foreach ($xmlItems->ITEM as $xmlItem) {
			$dataItem = [
				'KODE_TRADER' => $kode_trader,
				'CAR'	=> $car,
				'SERIAL' => ($xmlItem->SERIAL) ? (string)$xmlItem->SERIAL : "",
				'JNSBARANGDTL' => ($xmlItem->JNSBARANGDTL) ? (string)$xmlItem->JNSBARANGDTL : "",
				'NOHS' => ($xmlItem->NOHS) ? (string)$xmlItem->NOHS : "",
				'BRGURAI' => ($xmlItem->BRGURAI) ? (string)$xmlItem->BRGURAI : "",
				'MERK' => ($xmlItem->MERK) ? (string)$xmlItem->MERK : "",
				'TIPE' => ($xmlItem->TIPE) ? (string)$xmlItem->TIPE : "",
				'UKURAN' => ($xmlItem->UKURAN) ? (string)$xmlItem->UKURAN : "",
				'SPFLAIN' => ($xmlItem->SPFLAIN) ? (string)$xmlItem->SPFLAIN : "",
				'KDBRG' => ($xmlItem->KDBRG) ? (string)$xmlItem->KDBRG : "",
				'KEMASJN' => ($xmlItem->KEMASJN) ? (string)$xmlItem->KEMASJN : "",
				'KEMASJM' => ($xmlItem->KEMASJM) ? (string)$xmlItem->KEMASJM : "",
				'KDFASDTL' => ($xmlItem->KDFASDTL) ? (string)$xmlItem->KDFASDTL : "",
				'BRGASAL' => ($xmlItem->BRGASAL) ? (string)$xmlItem->BRGASAL : "",
				'DNILINV' => ($xmlItem->DNILINV) ? (string)$xmlItem->DNILINV : "",
				'DCIF' => ($xmlItem->DCIF) ? (string)$xmlItem->DCIF : "",
				'KDSAT' => ($xmlItem->KDSAT) ? (string)$xmlItem->KDSAT : "",
				'JMLSAT' => ($xmlItem->JMLSAT) ? (string)$xmlItem->JMLSAT : "",
				'NETTODTL' => ($xmlItem->NETTODTL) ? (string)$xmlItem->NETTODTL : "",
				'HRGSAT' => ($xmlItem->HRGSAT) ? (string)$xmlItem->HRGSAT : "",
				'KDSKEMATARIF' => ($xmlItem->KDSKEMATARIF) ? (string)$xmlItem->KDSKEMATARIF : "",
				'STATUS' => ($xmlItem->STATUS) ? (string)$xmlItem->STATUS : "",
			];
			$insertData[] = insertRefernce('t_bc23dtl', $dataItem);
		}
	}

	foreach ($xmlArr->DETAIL->ITEMDOCUMENTS as $xmlItemDocs) {
		foreach ($xmlItemDocs->ITEMDOCUMENT as $xmlItemDoc) {
			$dataItemDoc = [
				'KODE_TRADER' => $kode_trader,
				'CAR'	=> $car,
				'SERIBRG' => ($xmlItemDoc->SERIBRG) ? (string)$xmlItemDoc->SERIBRG : "",
				'SERIDOK' => ($xmlItemDoc->SERIDOK) ? (string)$xmlItemDoc->SERIDOK : "",
			];
		}
		$insertData[] = insertRefernce('t_bc23dtldok', $dataItemDoc);
	}

	foreach ($xmlArr->DETAIL->CONTAINERS as $xmlCons) {
		foreach ($xmlCons->CONTAINER as $xmlCon) {
			$dataCon = [
				'KODE_TRADER' => $kode_trader,
				'CAR'	=> $car,
				'CONTNO' => ($xmlCon->CONTNO) ? (string)$xmlCon->CONTNO : "",
				'CONTUKUR' => ($xmlCon->CONTUKUR) ? (string)$xmlCon->CONTUKUR : "",
				'CONTTIPE' => ($xmlCon->CONTTIPE) ? (string)$xmlCon->CONTTIPE : "",
				'KETERANGAN' => ($xmlCon->KETERANGAN) ? (string)$xmlCon->KETERANGAN : "",
			];
			$insertData[] = insertRefernce('t_bc23con', $dataCon);
		}
	}

	foreach ($xmlArr->DETAIL->PACKAGES as $xmlKmss) {
		foreach ($xmlKmss->PACKAGE as $xmlKms) {
			$dataKms = [
				'KODE_TRADER' => $kode_trader,
				'CAR'	=> $car,
				'JNKEMAS' => ($xmlKms->JNKEMAS) ? (string)$xmlKms->JNKEMAS : "",
				'JMKEMAS' => ($xmlKms->JMKEMAS) ? (string)$xmlKms->JMKEMAS : "",
				'MERKKEMAS' => ($xmlKms->MERKKEMAS) ? (string)$xmlKms->MERKKEMAS : "",
			];
			$insertData[] = insertRefernce('t_bc23kms', $dataKms);
		}
	}

	foreach ($xmlArr->DETAIL->CHARGES as $xmlPgts) {
		foreach ($xmlPgts->CHARGE as $xmlPgt) {
			$dataPgt = [
				'KODE_TRADER' => $kode_trader,
				'CAR'	=> $car,
				'KDBEBAN' => ($xmlPgt->KDBEBAN) ? (string)$xmlPgt->KDBEBAN : "",
				'KDFASIL' => ($xmlPgt->KDFASIL) ? (string)$xmlPgt->KDFASIL : "",
				'NILBEBAN' => ($xmlPgt->NILBEBAN) ? (string)$xmlPgt->NILBEBAN : "",
			];
			$insertData[] = insertRefernce('t_bc23pgt', $dataPgt);
		}
	}

	foreach ($xmlArr->DETAIL->ITEMTARIFS as $xmlTrfs) {
		foreach ($xmlTrfs->TARIF as $xmlTrf) {
			$dataTrf = [
				'KODE_TRADER' => $kode_trader,
				'CAR'	=> $car,
				'SERIBRG' =>($xmlTrf->SERIBRG) ? (string)$xmlTrf->SERIBRG : "",
				'KDTRPBM' =>($xmlTrf->KDTRPBM) ? (string)$xmlTrf->KDTRPBM : "",
				'KDSATBM' =>($xmlTrf->KDSATBM) ? (string)$xmlTrf->KDSATBM : "",
				'TRPBM' =>($xmlTrf->TRPBM) ? (string)$xmlTrf->TRPBM : "",
				'TRPPPN' =>($xmlTrf->TRPPPN) ? (string)$xmlTrf->TRPPPN : "",
				'TRPPBM' =>($xmlTrf->TRPPBM) ? (string)$xmlTrf->TRPPBM : "",
				'TRPPPH' =>($xmlTrf->TRPPPH) ? (string)$xmlTrf->TRPPPH : "",
				'KDTRPBMAD' =>($xmlTrf->KDTRPBMAD) ? (string)$xmlTrf->KDTRPBMAD : "",
				'TRBMAD' =>($xmlTrf->TRBMAD) ? (string)$xmlTrf->TRBMAD : "",
				'FLBMADS' =>($xmlTrf->FLBMADS) ? (string)$xmlTrf->FLBMADS : "",
				'KDTRPBMTP' =>($xmlTrf->KDTRPBMTP) ? (string)$xmlTrf->KDTRPBMTP : "",
				'TRPBMTP' =>($xmlTrf->TRPBMTP) ? (string)$xmlTrf->TRPBMTP : "",
				'FLBMTPS' =>($xmlTrf->FLBMTPS) ? (string)$xmlTrf->FLBMTPS : "",
				'KDTPRBMIM' =>($xmlTrf->KDTPRBMIM) ? (string)$xmlTrf->KDTPRBMIM : "",
				'TRPBMIM' =>($xmlTrf->TRPBMIM) ? (string)$xmlTrf->TRPBMIM : "",
				'FLBIMPS' =>($xmlTrf->FLBIMPS) ? (string)$xmlTrf->FLBIMPS : "",
				'KDTRPBMPB' =>($xmlTrf->KDTRPBMPB) ? (string)$xmlTrf->KDTRPBMPB : "",
				'TRPBMPB' =>($xmlTrf->TRPBMPB) ? (string)$xmlTrf->TRPBMPB : "",
				'FLBMPBS' =>($xmlTrf->FLBMPBS) ? (string)$xmlTrf->FLBMPBS : "",
				'KDFASBM' =>($xmlTrf->KDFASBM) ? (string)$xmlTrf->KDFASBM : "",
				'KDFASPPN' =>($xmlTrf->KDFASPPN) ? (string)$xmlTrf->KDFASPPN : "",
				'KDFASPPH' =>($xmlTrf->KDFASPPH) ? (string)$xmlTrf->KDFASPPH : "",
				'KDPASPPNBM' =>($xmlTrf->KDPASPPNBM) ? (string)$xmlTrf->KDPASPPNBM : "",
				'FASBM' =>($xmlTrf->FASBM) ? (string)$xmlTrf->FASBM : "",
				'FASPPN' =>($xmlTrf->FASPPN) ? (string)$xmlTrf->FASPPN : "",
				'FASPPH' =>($xmlTrf->FASPPH) ? (string)$xmlTrf->FASPPH : "",
				'FASPPNBM' =>($xmlTrf->FASPPNBM) ? (string)$xmlTrf->FASPPNBM : "",
				'JMLSAT' =>($xmlTrf->JMLSAT) ? (string)$xmlTrf->JMLSAT : "",
				'KDCUK' =>($xmlTrf->KDCUK) ? (string)$xmlTrf->KDCUK : "",
				'KDTRPCUK' =>($xmlTrf->KDTRPCUK) ? (string)$xmlTrf->KDTRPCUK : "",
				'KDSATCUK' =>($xmlTrf->KDSATCUK) ? (string)$xmlTrf->KDSATCUK : "",
				'TRPCUK' =>($xmlTrf->TRPCUK) ? (string)$xmlTrf->TRPCUK : "",
				'KDFASCUK' =>($xmlTrf->KDFASCUK) ? (string)$xmlTrf->KDFASCUK : "",
				'FASCUK' =>($xmlTrf->FASCUK) ? (string)$xmlTrf->FASCUK : "",
			];
			$insertData[] = insertRefernce('t_bc23trf', $dataTrf);
		}
	}

	print_r($insertData);
}

$db->close();