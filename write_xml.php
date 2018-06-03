<?php
include('lib/database.php');
include('lib/main.php');

$dbName = __DIR__ . DIRECTORY_SEPARATOR . "msaccess/dbPIB.mdb";
if (!file_exists($dbName)) {
    die("Could not find database file.");
}

try {
	$dbA = new PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=$dbName; Uid=; Pwd=MumtazFarisHana;");
    $sqlGetHdr = "SELECT * FROM tblPibHdr";
    $resGetHdr = $dbA->query($sqlGetHdr);

	while ($rowHdr = $resGetHdr->fetch()) {
		isset($dataHdr);
		$xml = "<CDOCUMENT>
			<DOCTYPE></DOCTYPE>
			<EDINUMBER></EDINUMBER>";

		$xml .= "<HEADER>
				<CAR>" . $rowHdr['CAR']. "</CAR>
				<KDKPBC>" . $rowHdr['KDKPBC']. "</KDKPBC>
				<JNPIB>" . $rowHdr['JNPIB']. "</JNPIB>
				<JNIMP>" . $rowHdr['JNIMP']. "</JNIMP>
				<CRBYR>" . $rowHdr['CRBYR']. "</CRBYR>
				<DOKTUPKD>" . $rowHdr['DOKTUPKD']. "</DOKTUPKD>
				<DOKTUPNO>" . $rowHdr['DOKTUPNO']. "</DOKTUPNO>
				<DOKTUPTG>" . $rowHdr['DOKTUPTG']. "</DOKTUPTG>
				<POSNO>" . $rowHdr['POSNO']. "</POSNO>
				<POSSUB>" . $rowHdr['POSSUB']. "</POSSUB>
				<POSSUBSUB>" . $rowHdr['POSSUBSUB']. "</POSSUBSUB>
				<IMPID>" . $rowHdr['IMPID']. "</IMPID>
				<IMPNPWP>" . $rowHdr['IMPNPWP']. "</IMPNPWP>
				<IMPNAMA>" . $rowHdr['IMPNAMA']. "</IMPNAMA>
				<IMPALMT>" . $rowHdr['IMPALMT']. "</IMPALMT>
				<IMPSTATUS>" . $rowHdr['IMPSTATUS']. "</IMPSTATUS>
				<APIKD>" . $rowHdr['APIKD']. "</APIKD>
				<APINO>" . $rowHdr['APINO']. "</APINO>
				<PPJKID>" . $rowHdr['PPJKID']. "</PPJKID>
				<PPJKNPWP>" . $rowHdr['PPJKNPWP']. "</PPJKNPWP>
				<PPJKNAMA>" . $rowHdr['PPJKNAMA']. "</PPJKNAMA>
				<PPJKALMT>" . $rowHdr['PPJKALMT']. "</PPJKALMT>
				<PPJKNO>" . $rowHdr['PPJKNO']. "</PPJKNO>
				<PPJKTG>" . $rowHdr['PPJKTG']. "</PPJKTG>
				<INDID>" . $rowHdr['INDID']. "</INDID>
				<INDNPWP>" . $rowHdr['INDNPWP']. "</INDNPWP>
				<INDNAMA>" . $rowHdr['INDNAMA']. "</INDNAMA>
				<INDALMT>" . $rowHdr['INDALMT']. "</INDALMT>
				<PASOKNAMA>" . $rowHdr['PASOKNAMA']. "</PASOKNAMA>
				<PASOKALMT>" . $rowHdr['PASOKALMT']. "</PASOKALMT>
				<PASOKNEG>" . $rowHdr['PASOKNEG']. "</PASOKNEG>
				<PELBKR>" . $rowHdr['PELBKR']. "</PELBKR>
				<PELMUAT>" . $rowHdr['PELMUAT']. "</PELMUAT>
				<PELTRANSIT>" . $rowHdr['PELTRANSIT']. "</PELTRANSIT>
				<TMPTBN>" . $rowHdr['TMPTBN']. "</TMPTBN>
				<MODA>" . $rowHdr['MODA']. "</MODA>
				<ANGKUTNAMA>" . $rowHdr['ANGKUTNAMA']. "</ANGKUTNAMA>
				<ANGKUTNO>" . $rowHdr['ANGKUTNO']. "</ANGKUTNO>
				<ANGKUTFL>" . $rowHdr['ANGKUTFL']. "</ANGKUTFL>
				<TGTIBA>" . $rowHdr['TGTIBA']. "</TGTIBA>
				<KDVAL>" . $rowHdr['KDVAL']. "</KDVAL>
				<NDPBM>" . $rowHdr['NDPBM']. "</NDPBM>
				<NILINV>" . $rowHdr['NILINV']. "</NILINV>
				<FREIGHT>" . $rowHdr['FREIGHT']. "</FREIGHT>
				<BTAMBAHAN>" . $rowHdr['BTAMBAHAN']. "</BTAMBAHAN>
				<DISCOUNT>" . $rowHdr['DISCOUNT']. "</DISCOUNT>
				<KDASS>" . $rowHdr['KDASS']. "</KDASS>
				<ASURANSI>" . $rowHdr['ASURANSI']. "</ASURANSI>
				<KDHRG>" . $rowHdr['KDHRG']. "</KDHRG>
				<FOB>" . $rowHdr['FOB']. "</FOB>
				<CIF>" . $rowHdr['CIF']. "</CIF>
				<CIFRP>" . $rowHdr['CIFRP']. "</CIFRP>
				<BRUTO>" . $rowHdr['BRUTO']. "</BRUTO>
				<NETTO>" . $rowHdr['NETTO']. "</NETTO>
				<JMCONT>" . $rowHdr['JMCONT']. "</JMCONT>
				<JMBRG>" . $rowHdr['JMBRG']. "</JMBRG>
				<BILLNPWP>" . $rowHdr['BILLNPWP']. "</BILLNPWP>
				<BILLNAMA>" . $rowHdr['BILLNAMA']. "</BILLNAMA>
				<BILLALAMAT>" . $rowHdr['BILLALAMAT']. "</BILLALAMAT>
				<PERNYATAAN>" . $rowHdr['PERNYATAAN']. "</PERNYATAAN>
				<JNSTRANS>" . $rowHdr['JNSTRANS']. "</JNSTRANS>
				<VD>" . $rowHdr['VD']. "</VD>
				<NILVD>" . $rowHdr['NILVD']. "</NILVD>
				<NAMA_TTD>" . $rowHdr['NAMA_TTD']. "</NAMA_TTD>
				<KOTA_TTD>" . $rowHdr['KOTA_TTD']. "</KOTA_TTD>
				<TANGGAL_TTD>" . $rowHdr['TANGGAL_TTD']. "</TANGGAL_TTD>
			</HEADER>";

		$xml .="</CDOCUMENT>";
	}
}

catch (PDOException $e) {
    echo $e->getMessage();
}

$dom = new DOMDocument();
$dom->preserveWhiteSpace = true;
$dom->loadXML($xml);
$dom->save('sample.xml');
// $dom->formatOutput = TRUE;
echo $xml;
