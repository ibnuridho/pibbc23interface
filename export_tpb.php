<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

require('./lib/database.php');
require('./lib/main.php');
require('./lib/phpspreadsheet/vendor/autoload.php');


$spreadsheet = new Spreadsheet();

drawExcel($spreadsheet);

// Set active sheet index to the first sheet, so Excel opens this as the first sheet

$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a clientâ€™s web browser (Xlsx)

$filename = 'BACKUP_BC23_'.date('YmdHis');
// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
// header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
// header('Cache-Control: max-age=0');

// // If you're serving to IE 9, then the following may be needed

// header('Cache-Control: max-age=1');

// // If you're serving to IE over SSL, then the following may be needed

// header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
// header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
// header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
// header('Pragma: public'); // HTTP/1.0
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('test.xlsx');
exit;

function drawExcel($spreadsheet)
{
	// HEADER
	$data = getHdr();
	$awl = 1;
	$spreadsheet->setActiveSheetIndex(0)
		->setCellValue('A'.$awl, 'NOMOR AJU')
		->setCellValue('B'.$awl, 'KPPBC')
		->setCellValue('C'.$awl, 'PERUSAHAAN')
		->setCellValue('D'.$awl, 'PEMASOK')
		->setCellValue('E'.$awl, 'STATUS')
		->setCellValue('F'.$awl, 'KODE DOKUMEN PABEAN')
		->setCellValue('G'.$awl, 'NPPJK')
		->setCellValue('H'.$awl, 'ALAMAT PEMASOK')
		->setCellValue('I'.$awl, 'ALAMAT PEMILIK')
		->setCellValue('J'.$awl, 'ALAMAT PENERIMA BARANG')
		->setCellValue('K'.$awl, 'ALAMAT PENGIRIM')
		->setCellValue('L'.$awl, 'ALAMAT PENGUSAHA')
		->setCellValue('M'.$awl, 'ALAMAT PPJK')
		->setCellValue('N'.$awl, 'API PEMILIK')
		->setCellValue('O'.$awl, 'API PENERIMA')
		->setCellValue('P'.$awl, 'API PENGUSAHA')
		->setCellValue('Q'.$awl, 'ASAL DATA')
		->setCellValue('R'.$awl, 'ASURANSI')
		->setCellValue('S'.$awl, 'BIAYA TAMBAHAN')
		->setCellValue('T'.$awl, 'BRUTO')
		->setCellValue('U'.$awl, 'CIF')
		->setCellValue('V'.$awl, 'CIF RUPIAH')
		->setCellValue('W'.$awl, 'DISKON')
		->setCellValue('X'.$awl, 'FLAG PEMILIK')
		->setCellValue('Y'.$awl, 'URL DOKUMEN PABEAN')
		->setCellValue('Z'.$awl, 'FOB')
		->setCellValue('AA'.$awl, 'FREIGHT')
		->setCellValue('AB'.$awl, 'HARGA BARANG LDP')
		->setCellValue('AC'.$awl, 'HARGA INVOICE')
		->setCellValue('AD'.$awl, 'HARGA PENYERAHAN')
		->setCellValue('AE'.$awl, 'HARGA TOTAL')
		->setCellValue('AF'.$awl, 'ID MODUL')
		->setCellValue('AG'.$awl, 'ID PEMASOK')
		->setCellValue('AH'.$awl, 'ID PEMILIK')
		->setCellValue('AI'.$awl, 'ID PENERIMA BARANG')
		->setCellValue('AJ'.$awl, 'ID PENGIRIM')
		->setCellValue('AK'.$awl, 'ID PENGUSAHA')
		->setCellValue('AL'.$awl, 'ID PPJK')
		->setCellValue('AM'.$awl, 'JABATAN TTD')
		->setCellValue('AN'.$awl, 'JUMLAH BARANG')
		->setCellValue('AO'.$awl, 'JUMLAH KEMASAN')
		->setCellValue('AP'.$awl, 'JUMLAH KONTAINER')
		->setCellValue('AQ'.$awl, 'KESESUAIAN DOKUMEN')
		->setCellValue('AR'.$awl, 'KETERANGAN')
		->setCellValue('AS'.$awl, 'KODE ASAL BARANG')
		->setCellValue('AT'.$awl, 'KODE ASURANSI')
		->setCellValue('AU'.$awl, 'KODE BENDERA')
		->setCellValue('AV'.$awl, 'KODE CARA ANGKUT')
		->setCellValue('AW'.$awl, 'KODE CARA BAYAR')
		->setCellValue('AX'.$awl, 'KODE DAERAH ASAL')
		->setCellValue('AY'.$awl, 'KODE FASILITAS')
		->setCellValue('AZ'.$awl, 'KODE FTZ')
		->setCellValue('BA'.$awl, 'KODE HARGA')
		->setCellValue('BB'.$awl, 'KODE ID PEMASOK')
		->setCellValue('BC'.$awl, 'KODE ID PEMILIK')
		->setCellValue('BD'.$awl, 'KODE ID PENERIMA BARANG')
		->setCellValue('BE'.$awl, 'KODE ID PENGIRIM')
		->setCellValue('BF'.$awl, 'KODE ID PENGUSAHA')
		->setCellValue('BG'.$awl, 'KODE ID PPJK')
		->setCellValue('BH'.$awl, 'KODE JENIS API')
		->setCellValue('BI'.$awl, 'KODE JENIS API PEMILIK')
		->setCellValue('BJ'.$awl, 'KODE JENIS API PENERIMA')
		->setCellValue('BK'.$awl, 'KODE JENIS API PENGUSAHA')
		->setCellValue('BL'.$awl, 'KODE JENIS BARANG')
		->setCellValue('BM'.$awl, 'KODE JENIS BC25')
		->setCellValue('BN'.$awl, 'KODE JENIS NILAI')
		->setCellValue('BO'.$awl, 'KODE JENIS PEMASUKAN01')
		->setCellValue('BP'.$awl, 'KODE JENIS PEMASUKAN 02')
		->setCellValue('BQ'.$awl, 'KODE JENIS TPB')
		->setCellValue('BR'.$awl, 'KODE KANTOR BONGKAR')
		->setCellValue('BS'.$awl, 'KODE KANTOR TUJUAN')
		->setCellValue('BT'.$awl, 'KODE LOKASI BAYAR')
		->setCellValue('BU'.$awl, 'KODE NEGARA PEMASOK')
		->setCellValue('BV'.$awl, 'KODE NEGARA PENGIRIM')
		->setCellValue('BW'.$awl, 'KODE NEGARA PEMILIK')
		->setCellValue('BX'.$awl, 'KODE NEGARA TUJUAN')
		->setCellValue('BY'.$awl, 'KODE PEL BONGKAR')
		->setCellValue('BZ'.$awl, 'KODE PEL MUAT')
		->setCellValue('CA'.$awl, 'KODE PEL TRANSIT')
		->setCellValue('CB'.$awl, 'KODE PEMBAYAR')
		->setCellValue('CC'.$awl, 'KODE STATUS PENGUSAHA')
		->setCellValue('CD'.$awl, 'STATUS PERBAIKAN')
		->setCellValue('CE'.$awl, 'KODE TPS')
		->setCellValue('CF'.$awl, 'KODE TUJUAN PEMASUKAN')
		->setCellValue('CG'.$awl, 'KODE TUJUAN PENGIRIMAN')
		->setCellValue('CH'.$awl, 'KODE TUJUAN TPB')
		->setCellValue('CI'.$awl, 'KODE TUTUP PU')
		->setCellValue('CJ'.$awl, 'KODE VALUTA')
		->setCellValue('CK'.$awl, 'KOTA TTD')
		->setCellValue('CL'.$awl, 'NAMA PEMILIK')
		->setCellValue('CM'.$awl, 'NAMA PENERIMA BARANG')
		->setCellValue('CN'.$awl, 'NAMA PENGANGKUT')
		->setCellValue('CO'.$awl, 'NAMA PENGIRIM')
		->setCellValue('CP'.$awl, 'NAMA PPJK')
		->setCellValue('CQ'.$awl, 'NAMA TTD')
		->setCellValue('CR'.$awl, 'NDPBM')
		->setCellValue('CS'.$awl, 'NETTO')
		->setCellValue('CT'.$awl, 'NILAI INCOTERM')
		->setCellValue('CU'.$awl, 'NIPER PENERIMA')
		->setCellValue('CV'.$awl, 'NOMOR API')
		->setCellValue('CW'.$awl, 'NOMOR BC11')
		->setCellValue('CX'.$awl, 'NOMOR BILLING')
		->setCellValue('CY'.$awl, 'NOMOR DAFTAR')
		->setCellValue('CZ'.$awl, 'NOMOR IJIN BPK PEMASOK')
		->setCellValue('DA'.$awl, 'NOMOR IJIN BPK PENGUSAHA')
		->setCellValue('DB'.$awl, 'NOMOR IJIN TPB')
		->setCellValue('DC'.$awl, 'NOMOR IJIN TPB PENERIMA')
		->setCellValue('DD'.$awl, 'NOMOR VOYV FLIGHT')
		->setCellValue('DE'.$awl, 'NPWP BILLING')
		->setCellValue('DF'.$awl, 'POS BC11')
		->setCellValue('DG'.$awl, 'SERI')
		->setCellValue('DH'.$awl, 'SUBPOS BC11')
		->setCellValue('DI'.$awl, 'SUB SUBPOS BC11')
		->setCellValue('DJ'.$awl, 'TANGGAL BC11')
		->setCellValue('DK'.$awl, 'TANGGAL BERANGKAT')
		->setCellValue('DL'.$awl, 'TANGGAL BILLING')
		->setCellValue('DM'.$awl, 'TANGGAL DAFTAR')
		->setCellValue('DN'.$awl, 'TANGGAL IJIN BPK PEMASOK')
		->setCellValue('DO'.$awl, 'TANGGAL IJIN BPK PENGUSAHA')
		->setCellValue('DP'.$awl, 'TANGGAL IJIN TPB')
		->setCellValue('DQ'.$awl, 'TANGGAL NPPPJK')
		->setCellValue('DR'.$awl, 'TANGGAL TIBA')
		->setCellValue('DS'.$awl, 'TANGGAL TTD')
		->setCellValue('DT'.$awl, 'TANGGAL JATUH TEMPO')
		->setCellValue('DU'.$awl, 'TOTAL BAYAR')
		->setCellValue('DV'.$awl, 'TOTAL BEBAS')
		->setCellValue('DW'.$awl, 'TOTAL DILUNASI')
		->setCellValue('DX'.$awl, 'TOTAL JAMIN')
		->setCellValue('DY'.$awl, 'TOTAL SUDAH DILUNASI')
		->setCellValue('DZ'.$awl, 'TOTAL TANGGUH')
		->setCellValue('EA'.$awl, 'TOTAL TANGGUNG')
		->setCellValue('EB'.$awl, 'TOTAL TIDAK DIPUNGUT')
		->setCellValue('EC'.$awl, 'URL DOKUMEN PABEAN')
		->setCellValue('ED'.$awl, 'VERSI MODUL')
		->setCellValue('EE'.$awl, 'VOLUME')
		->setCellValue('EF'.$awl, 'WAKTU BONGKAR')
		->setCellValue('EG'.$awl, 'WAKTU STUFFING')
		->setCellValue('EH'.$awl, 'NOMOR POLISI')
		;

	$totData = count($data);
    for ($i = 0; $i < $totData; $i++)
    {
        $pos = ($awl+1+$i);

        $spreadsheet->getActiveSheet()
        	->setCellValue('A'.$pos, $data[$i]['CAR'])
			->setCellValue('B'.$pos, $data[$i]['KDKPBC'])
			->setCellValue('C'.$pos, $data[$i]['USAHANAMA'])
			->setCellValue('D'.$pos, $data[$i]['PASOKNAMA'])
			->setCellValue('E'.$pos, $data[$i]['STATUS'])
			->setCellValue('F'.$pos, $data[$i]['KDDOKPAB'])
			->setCellValue('G'.$pos, $data[$i]['PPJKNPWP'])
			->setCellValue('H'.$pos, $data[$i]['PASOKALMT'])
			->setCellValue('I'.$pos, $data[$i]['INDALMT'])
			// ->setCellValue('J'.$pos, $data[$i]['PENERIMAALMT'])
			// ->setCellValue('K'.$pos, $data[$i]['PENGIRIMALMT'])
			->setCellValue('L'.$pos, $data[$i]['USAHAALMT'])
			->setCellValue('M'.$pos, $data[$i]['PPJKALMT'])
			->setCellValue('N'.$pos, $data[$i]['INDAPINO'])
			// ->setCellValue('O'.$pos, $data[$i]['PENERIMAAPINO'])
			->setCellValue('P'.$pos, $data[$i]['APINO'])
			->setCellValue('Q'.$pos, $data[$i]['ASALDATA'])
			->setCellValue('R'.$pos, $data[$i]['ASURANSI'])
			->setCellValue('S'.$pos, $data[$i]['BTAMBAHAN'])
			->setCellValue('T'.$pos, $data[$i]['BRUTO'])
			->setCellValue('U'.$pos, $data[$i]['CIF'])
			->setCellValue('V'.$pos, $data[$i]['CIFRP'])
			->setCellValue('W'.$pos, $data[$i]['DISKON'])
			// ->setCellValue('X'.$pos, $data[$i]['FLAGPEMILIK'])
			// ->setCellValue('Y'.$pos, $data[$i]['URLDOKPAB'])
			->setCellValue('Z'.$pos, $data[$i]['FOB'])

			->setCellValue('AA'.$pos, $data[$i]['FREIGHT'])
			// ->setCellValue('AB'.$pos, $data[$i]['HARGA BARANG LDP'])
			->setCellValue('AC'.$pos, $data[$i]['NILINV'])
			// ->setCellValue('AD'.$pos, $data[$i]['HARGA PENYERAHAN'])
			// ->setCellValue('AE'.$pos, $data[$i]['HARGA TOTAL'])
			// ->setCellValue('AF'.$pos, $data[$i]['ID MODUL'])
			// ->setCellValue('AG'.$pos, $data[$i]['ID PEMASOK'])
			->setCellValue('AH'.$pos, $data[$i]['INDNPWP'])
			// ->setCellValue('AI'.$pos, $data[$i]['ID PENERIMA BARANG'])
			// ->setCellValue('AJ'.$pos, $data[$i]['ID PENGIRIM'])
			->setCellValue('AK'.$pos, $data[$i]['USAHANPWP'])
			->setCellValue('AL'.$pos, $data[$i]['PPJKNPWP'])
			->setCellValue('AM'.$pos, $data[$i]['JABATANTTD'])
			->setCellValue('AN'.$pos, $data[$i]['JMBRG'])
			// ->setCellValue('AO'.$pos, $data[$i]['JUMLAH KEMASAN']) //==================
			->setCellValue('AP'.$pos, $data[$i]['JMCONT'])
			// ->setCellValue('AQ'.$pos, $data[$i]['KESESUAIAN DOKUMEN'])
			// ->setCellValue('AR'.$pos, $data[$i]['KETERANGAN'])
			// ->setCellValue('AS'.$pos, $data[$i]['KODE ASAL BARANG'])
			->setCellValue('AT'.$pos, $data[$i]['KDASS'])
			->setCellValue('AU'.$pos, $data[$i]['ANGKUTFL']) //==================
			->setCellValue('AV'.$pos, $data[$i]['MODA'])
			// ->setCellValue('AW'.$pos, $data[$i]['KODE CARA BAYAR'])
			// ->setCellValue('AX'.$pos, $data[$i]['KODE DAERAH ASAL'])
			// ->setCellValue('AY'.$pos, $data[$i]['KODE FASILITAS'])
			// ->setCellValue('AZ'.$pos, $data[$i]['KODE FTZ'])
			->setCellValue('BA'.$pos, $data[$i]['KDHRG'])
			// ->setCellValue('BB'.$pos, $data[$i]['KODE ID PEMASOK'])
			->setCellValue('BC'.$pos, $data[$i]['INDID'])
			// ->setCellValue('BD'.$pos, $data[$i]['KODE ID PENERIMA BARANG'])
			// ->setCellValue('BE'.$pos, $data[$i]['KODE ID PENGIRIM'])
			->setCellValue('BF'.$pos, $data[$i]['USAHAID'])
			->setCellValue('BG'.$pos, $data[$i]['PPJKID'])
			// ->setCellValue('BH'.$pos, $data[$i]['KODE JENIS API'])
			->setCellValue('BI'.$pos, $data[$i]['INDAPIKD'])
			// ->setCellValue('BJ'.$pos, $data[$i]['KODE JENIS API PENERIMA'])
			->setCellValue('BK'.$pos, $data[$i]['APIKD'])
			// ->setCellValue('BL'.$pos, $data[$i]['KODE JENIS BARANG'])
			// ->setCellValue('BM'.$pos, $data[$i]['KODE JENIS BC25'])
			// ->setCellValue('BN'.$pos, $data[$i]['KODE JENIS NILAI'])
			// ->setCellValue('BO'.$pos, $data[$i]['KODE JENIS PEMASUKAN01'])
			// ->setCellValue('BP'.$pos, $data[$i]['KODE JENIS PEMASUKAN 02'])
			// ->setCellValue('BQ'.$pos, $data[$i]['KODE JENIS TPB'])
			->setCellValue('BR'.$pos, $data[$i]['KDKPBCBONGKAR'])
			// ->setCellValue('BS'.$pos, $data[$i]['KODE KANTOR TUJUAN'])
			// ->setCellValue('BT'.$pos, $data[$i]['KODE LOKASI BAYAR'])
			->setCellValue('BU'.$pos, $data[$i]['PASOKNEG'])
			// ->setCellValue('BV'.$pos, $data[$i]['KODE NEGARA PENGIRIM'])
			// ->setCellValue('BW'.$pos, $data[$i]['KODE NEGARA PEMILIK'])
			// ->setCellValue('BX'.$pos, $data[$i]['KODE NEGARA TUJUAN'])
			->setCellValue('BY'.$pos, $data[$i]['PELBKR'])
			->setCellValue('BZ'.$pos, $data[$i]['PELMUAT'])
			->setCellValue('CA'.$pos, $data[$i]['PELTRANSIT'])
			// ->setCellValue('CB'.$pos, $data[$i]['KODE PEMBAYAR'])
			// ->setCellValue('CC'.$pos, $data[$i]['KODE STATUS PENGUSAHA'])
			// ->setCellValue('CD'.$pos, $data[$i]['STATUS PERBAIKAN']) //==================
			->setCellValue('CE'.$pos, $data[$i]['TMPTBN'])
			// ->setCellValue('CF'.$pos, $data[$i]['KODE TUJUAN PEMASUKAN'])
			// ->setCellValue('CG'.$pos, $data[$i]['KODE TUJUAN PENGIRIMAN'])
			->setCellValue('CH'.$pos, $data[$i]['TUJUAN'])
			// ->setCellValue('CI'.$pos, $data[$i]['KODE TUTUP PU'])
			->setCellValue('CJ'.$pos, $data[$i]['KDVAL'])
			->setCellValue('CK'.$pos, $data[$i]['KOTA_TTD'])
			->setCellValue('CL'.$pos, $data[$i]['INDNAMA'])
			// ->setCellValue('CM'.$pos, $data[$i]['NAMA PENERIMA BARANG'])
			->setCellValue('CN'.$pos, $data[$i]['ANGKUTNAMA'])
			// ->setCellValue('CO'.$pos, $data[$i]['NAMA PENGIRIM'])
			->setCellValue('CP'.$pos, $data[$i]['PPJKNAMA'])
			->setCellValue('CQ'.$pos, $data[$i]['NAMA_TTD'])
			->setCellValue('CR'.$pos, $data[$i]['NDPBM'])
			->setCellValue('CS'.$pos, $data[$i]['NETTO'])
			// ->setCellValue('CT'.$pos, $data[$i]['NILAI INCOTERM'])
			// ->setCellValue('CU'.$pos, $data[$i]['NIPER PENERIMA'])
			->setCellValue('CV'.$pos, $data[$i]['APINO'])
			->setCellValue('CW'.$pos, $data[$i]['DOKTUPNO'])
			// ->setCellValue('CX'.$pos, $data[$i]['NOMOR BILLING'])
			->setCellValue('CY'.$pos, $data[$i]['BC23NO'])
			// ->setCellValue('CZ'.$pos, $data[$i]['NOMOR IJIN BPK PEMASOK'])
			// ->setCellValue('DA'.$pos, $data[$i]['NOMOR IJIN BPK PENGUSAHA'])
			->setCellValue('DB'.$pos, $data[$i]['REGISTRASI'])
			// ->setCellValue('DC'.$pos, $data[$i]['NOMOR IJIN TPB PENERIMA'])
			->setCellValue('DD'.$pos, $data[$i]['ANGKUTNO'])
			// ->setCellValue('DE'.$pos, $data[$i]['NPWP BILLING'])
			->setCellValue('DF'.$pos, $data[$i]['POSNO'])
			->setCellValue('DG'.$pos, $data[$i]['0'])
			->setCellValue('DH'.$pos, $data[$i]['POSSUB'])
			->setCellValue('DI'.$pos, $data[$i]['POSSUBSUB'])
			->setCellValue('DJ'.$pos, $data[$i]['DOKTUPTG'])
			// ->setCellValue('DK'.$pos, $data[$i]['TANGGAL BERANGKAT'])
			// ->setCellValue('DL'.$pos, $data[$i]['TANGGAL BILLING'])
			->setCellValue('DM'.$pos, $data[$i]['BC23TG'])
			// ->setCellValue('DN'.$pos, $data[$i]['TANGGAL IJIN BPK PEMASOK'])
			// ->setCellValue('DO'.$pos, $data[$i]['TANGGAL IJIN BPK PENGUSAHA'])
			// ->setCellValue('DP'.$pos, $data[$i]['TANGGAL IJIN TPB'])
			// ->setCellValue('DQ'.$pos, $data[$i]['TANGGAL NPPPJK'])
			// ->setCellValue('DR'.$pos, $data[$i]['TANGGAL TIBA'])
			->setCellValue('DS'.$pos, $data[$i]['TANGGAL_TTD'])
			// ->setCellValue('DT'.$pos, $data[$i]['TANGGAL JATUH TEMPO'])
			// ->setCellValue('DU'.$pos, $data[$i]['TOTAL BAYAR'])
			// ->setCellValue('DV'.$pos, $data[$i]['TOTAL BEBAS'])
			// ->setCellValue('DW'.$pos, $data[$i]['TOTAL DILUNASI'])
			// ->setCellValue('DX'.$pos, $data[$i]['TOTAL JAMIN'])
			// ->setCellValue('DY'.$pos, $data[$i]['TOTAL SUDAH DILUNASI'])
			// ->setCellValue('DZ'.$pos, $data[$i]['TOTAL TANGGUH'])
			// ->setCellValue('EA'.$pos, $data[$i]['TOTAL TANGGUNG'])
			// ->setCellValue('EB'.$pos, $data[$i]['TOTAL TIDAK DIPUNGUT'])
			// ->setCellValue('EC'.$pos, $data[$i]['URL DOKUMEN PABEAN'])
			->setCellValue('ED'.$pos, $data[$i]['3.1.6'])
			// ->setCellValue('EE'.$pos, $data[$i]['VOLUME'])
			// ->setCellValue('EF'.$pos, $data[$i]['WAKTU BONGKAR'])
			// ->setCellValue('EG'.$pos, $data[$i]['WAKTU STUFFING'])
			// ->setCellValue('EH'.$pos, $data[$i]['NOMOR POLISI'])
			;

		$car[] = $data[$i]['CAR'];
    }

    $spreadsheet->getActiveSheet()->setTitle('Header');
    unset($data);
    
    $car = "'".implode("','", $car)."'";


    // DETAIL
	$data = getBrg($car);
	$awl = 1;

	$spreadsheet->createSheet();
	$spreadsheet->setActiveSheetIndex(1)
		->setCellValue('A'.$awl, 'NOMOR AJU')
		->setCellValue('B'.$awl, 'SERI BARANG')
		->setCellValue('C'.$awl, 'ASURANSI')
		->setCellValue('D'.$awl, 'CIF')
		->setCellValue('E'.$awl, 'CIF RUPIAH')
		->setCellValue('F'.$awl, 'DISKON')
		->setCellValue('G'.$awl, 'FLAG KENDARAAN')
		->setCellValue('H'.$awl, 'FOB')
		->setCellValue('I'.$awl, 'FREIGHT')
		->setCellValue('J'.$awl, 'BARANG BARANG LDP')
		->setCellValue('K'.$awl, 'HARGA INVOICE')
		->setCellValue('L'.$awl, 'HARGA PENYERAHAN')
		->setCellValue('M'.$awl, 'HARGA SATUAN')
		->setCellValue('N'.$awl, 'JENIS KENDARAAN')
		->setCellValue('O'.$awl, 'JUMLAH BAHAN BAKU')
		->setCellValue('P'.$awl, 'JUMLAH KEMASAN')
		->setCellValue('Q'.$awl, 'JUMLAH SATUAN')
		->setCellValue('R'.$awl, 'KAPASITAS SILINDER')
		->setCellValue('S'.$awl, 'KATEGORI BARANG')
		->setCellValue('T'.$awl, 'KODE_ASAL BARANG')
		->setCellValue('U'.$awl, 'KODE BARANG')
		->setCellValue('V'.$awl, 'KODE FASILITAS')
		->setCellValue('W'.$awl, 'KODE GUNA')
		->setCellValue('X'.$awl, 'KODE JENIS NILAI')
		->setCellValue('Y'.$awl, 'KODE KEMASAN')
		->setCellValue('Z'.$awl, 'KODE LEBIH DARI 4 TAHUN')
		->setCellValue('AA'.$awl, 'KODE NEGARA ASAL')
		->setCellValue('AB'.$awl, 'KODE SATUAN')
		->setCellValue('AC'.$awl, 'KODE SKEMA TARIF')
		->setCellValue('AD'.$awl, 'KODE STATUS')
		->setCellValue('AE'.$awl, 'KONDISI BARANG')
		->setCellValue('AF'.$awl, 'MERK')
		->setCellValue('AG'.$awl, 'NETTO')
		->setCellValue('AH'.$awl, 'NILAI INCOTERM')
		->setCellValue('AI'.$awl, 'NILAI PABEAN')
		->setCellValue('AJ'.$awl, 'NOMOR MESIN')
		->setCellValue('AK'.$awl, 'POS TARIF')
		->setCellValue('AL'.$awl, 'SERI POS TARIF')
		->setCellValue('AM'.$awl, 'SPESIFIKASI LAIN')
		->setCellValue('AN'.$awl, 'TAHUN PEMBUATAN')
		->setCellValue('AO'.$awl, 'TIPE')
		->setCellValue('AP'.$awl, 'UKURAN')
		->setCellValue('AQ'.$awl, 'URAIAN')
		->setCellValue('AR'.$awl, 'VOLUME')
		->setCellValue('AS'.$awl, 'SERI IJIN')
		;

	$totData = count($data);
    for ($i = 0; $i < $totData; $i++)
    {
        $pos = ($awl+1+$i);

        $spreadsheet->getActiveSheet()
        	->setCellValue('A'.$pos, $data[$i]['CAR'])
			->setCellValue('B'.$pos, $data[$i]['SERIAL'])
			->setCellValue('C'.$pos, $data[$i]['ASURANSI'])
			->setCellValue('D'.$pos, $data[$i]['DCIF'])
			->setCellValue('E'.$pos, $data[$i]['DCIFRP'])
			->setCellValue('F'.$pos, $data[$i]['DISKON'])
			// ->setCellValue('G'.$pos, $data[$i]['FLAG KENDARAAN'])
			->setCellValue('H'.$pos, $data[$i]['FOB'])
			->setCellValue('I'.$pos, $data[$i]['FREIGHT'])
			// ->setCellValue('J'.$pos, $data[$i]['BARANG BARANG LDP'])
			->setCellValue('K'.$pos, $data[$i]['DNILINV'])
			// ->setCellValue('L'.$pos, $data[$i]['HARGA PENYERAHAN'])
			->setCellValue('M'.$pos, $data[$i]['HRGSAT'])
			// ->setCellValue('N'.$pos, $data[$i]['JENIS KENDARAAN'])
			// ->setCellValue('O'.$pos, $data[$i]['JUMLAH BAHAN BAKU'])
			->setCellValue('P'.$pos, $data[$i]['KEMASJM'])
			->setCellValue('Q'.$pos, $data[$i]['JMLSAT'])
			// ->setCellValue('R'.$pos, $data[$i]['KAPASITAS SILINDER'])
			// ===->setCellValue('S'.$pos, $data[$i]['KATEGORI BARANG'])
			// ->setCellValue('T'.$pos, $data[$i]['KODE_ASAL BARANG'])
			->setCellValue('U'.$pos, $data[$i]['KDBRG'])
			->setCellValue('V'.$pos, $data[$i]['KDFASDTL'])
			// ->setCellValue('W'.$pos, $data[$i]['KODE GUNA'])
			// ->setCellValue('X'.$pos, $data[$i]['KODE JENIS NILAI'])
			->setCellValue('Y'.$pos, $data[$i]['KEMASJN'])
			// ->setCellValue('Z'.$pos, $data[$i]['KODE LEBIH DARI 4 TAHUN'])
			->setCellValue('AA'.$pos, $data[$i]['BRGASAL'])
			->setCellValue('AB'.$pos, $data[$i]['KDSAT'])
			->setCellValue('AC'.$pos, $data[$i]['KDSKEMATARIF'])
			->setCellValue('AD'.$pos, $data[$i]['STATUS'])
			// ->setCellValue('AE'.$pos, $data[$i]['KONDISI BARANG'])
			->setCellValue('AF'.$pos, $data[$i]['MERK'])
			->setCellValue('AG'.$pos, $data[$i]['NETTO'])
			// ->setCellValue('AH'.$pos, $data[$i]['NILAI INCOTERM'])
			// ->setCellValue('AI'.$pos, $data[$i]['NILAI PABEAN'])
			// ->setCellValue('AJ'.$pos, $data[$i]['NOMOR MESIN'])
			->setCellValue('AK'.$pos, $data[$i]['NOHS'])
			->setCellValue('AL'.$pos, $data[$i]['SERITRP'])
			->setCellValue('AM'.$pos, $data[$i]['SPFLAIN'])
			// ->setCellValue('AN'.$pos, $data[$i]['TAHUN PEMBUATAN'])
			->setCellValue('AO'.$pos, $data[$i]['TIPE'])
			// ->setCellValue('AP'.$pos, $data[$i]['UKURAN'])
			->setCellValue('AQ'.$pos, $data[$i]['BRGURAI'])
			// ->setCellValue('AR'.$pos, $data[$i]['VOLUME'])
			// ->setCellValue('AS'.$pos, $data[$i]['SERI IJIN'])
			;
    }

    $spreadsheet->getActiveSheet()->setTitle('Barang');
    unset($data);


    // TARIF
	$data = getTrf($car);
	$awl = 1;

	$spreadsheet->createSheet();
	$spreadsheet->setActiveSheetIndex(2)
		->setCellValue('A'.$awl, 'NOMOR AJU')
		->setCellValue('B'.$awl, 'SERI BARANG')
		->setCellValue('C'.$awl, 'JENIS TARIF')
		->setCellValue('D'.$awl, 'JUMLAH SATUAN')
		->setCellValue('E'.$awl, 'KODE FASILITAS')
		->setCellValue('F'.$awl, 'KODE KOMODITI CUKAI')
		->setCellValue('G'.$awl, 'TARIF KODE SATUAN')
		->setCellValue('H'.$awl, 'TARIF KODE TARIF')
		->setCellValue('I'.$awl, 'TARIF NILAI BAYAR')
		->setCellValue('J'.$awl, 'TARIF NILAI FASILITAS')
		->setCellValue('K'.$awl, 'TARIF NILAI SUDAH DILUNASI')
		->setCellValue('L'.$awl, 'TARIF')
		->setCellValue('M'.$awl, 'TARIF FASILITAS')
		;

	$totData = count($data);
    for ($i = 0; $i < $totData; $i++)
    {
        $pos = ($awl+1+$i);

        $spreadsheet->getActiveSheet()
   //      	->setCellValue('A'.$pos, $data[$i]['CAR'])
			// ->setCellValue('B'.$pos, $data[$i]['SERIBRG'])
			// ->setCellValue('C'.$pos, $data[$i]['JENIS TARIF'])
			// ->setCellValue('D'.$pos, $data[$i]['JUMLAH SATUAN'])
			// ->setCellValue('E'.$pos, $data[$i]['KODE FASILITAS'])
			// ->setCellValue('F'.$pos, $data[$i]['KODE KOMODITI CUKAI'])
			// ->setCellValue('G'.$pos, $data[$i]['TARIF KODE SATUAN'])
			// ->setCellValue('H'.$pos, $data[$i]['TARIF KODE TARIF'])
			// ->setCellValue('I'.$pos, $data[$i]['TARIF NILAI BAYAR'])
			// ->setCellValue('J'.$pos, $data[$i]['TARIF NILAI FASILITAS'])
			// ->setCellValue('K'.$pos, $data[$i]['TARIF NILAI SUDAH DILUNASI'])
			// ->setCellValue('L'.$pos, $data[$i]['TARIF'])
			// ->setCellValue('M'.$pos, $data[$i]['TARIF FASILITAS'])
			;
    }

    $spreadsheet->getActiveSheet()->setTitle('BarangTarif');
    unset($data);


    // DTLDOK
	$data = getDtlDok($car);
	$awl = 1;

	$spreadsheet->createSheet();
	$spreadsheet->setActiveSheetIndex(3)
		->setCellValue('A'.$awl, 'NOMOR AJU')
		->setCellValue('B'.$awl, 'SERI BARANG')
		->setCellValue('C'.$awl, 'SERI DOKUMEN')
		;

	$totData = count($data);
    for ($i = 0; $i < $totData; $i++)
    {
        $pos = ($awl+1+$i);

        $spreadsheet->getActiveSheet()
        	->setCellValue('A'.$pos, $data[$i]['CAR'])
			->setCellValue('B'.$pos, $data[$i]['SERIBRG'])
			->setCellValue('C'.$pos, $data[$i]['SERIDOK'])
			;
    }

    $spreadsheet->getActiveSheet()->setTitle('BarangDokumen');
    unset($data);


    // DOKUMEN
	$data = getDok($car);
	$awl = 1;

	$spreadsheet->createSheet();
	$spreadsheet->setActiveSheetIndex(4)	
		->setCellValue('A'.$awl, 'NOMOR AJU')
		->setCellValue('B'.$awl, 'SERI DOKUMEN')
		->setCellValue('C'.$awl, 'FLAG URL DOKUMEN')
		->setCellValue('D'.$awl, 'KODE JENIS DOKUMEN')
		->setCellValue('E'.$awl, 'NOMOR DOKUMEN')
		->setCellValue('F'.$awl, 'TANGGAL DOKUMEN')
		->setCellValue('G'.$awl, 'TIPE DOKUMEN')
		->setCellValue('H'.$awl, 'URL DOKUMEN')
		;

	$totData = count($data);
    for ($i = 0; $i < $totData; $i++)
    {
        $pos = ($awl+1+$i);

        $spreadsheet->getActiveSheet()
        	->setCellValue('A'.$pos, $data[$i]['CAR'])
			->setCellValue('B'.$pos, $data[$i]['SERIDOK'])
			// ->setCellValue('C'.$pos, $data[$i]['FLAG URL DOKUMEN'])
			->setCellValue('D'.$pos, $data[$i]['DOKKD'])
			->setCellValue('E'.$pos, $data[$i]['DOKNO'])
			->setCellValue('F'.$pos, $data[$i]['DOKTG'])
			//======= ->setCellValue('G'.$pos, $data[$i]['TIPE DOKUMEN'])
			// ->setCellValue('H'.$pos, $data[$i]['URL DOKUMEN'])
			;
    }

    $spreadsheet->getActiveSheet()->setTitle('Dokumen');
    unset($data);


    // KEMASAN
	$data = getKms($car);
	$awl = 1;

	$spreadsheet->createSheet();
	$spreadsheet->setActiveSheetIndex(5)
		->setCellValue('A'.$awl, 'NOMOR AJU')
		->setCellValue('B'.$awl, 'SERI KEMASAN')
		->setCellValue('C'.$awl, 'JUMLAH KEMASAN')
		->setCellValue('D'.$awl, 'KESESUAIAN DOKUMEN')
		->setCellValue('E'.$awl, 'KETERANGAN')
		->setCellValue('F'.$awl, 'KODE JENIS KEMASAN')
		->setCellValue('G'.$awl, 'MEREK KEMASAN')
		->setCellValue('H'.$awl, 'NIP GATE IN')
		->setCellValue('I'.$awl, 'NIP GATE OUT')
		->setCellValue('J'.$awl, 'NOMOR POLISI')
		->setCellValue('K'.$awl, 'NOMOR SEGEL')
		->setCellValue('L'.$awl, 'WAKTU GATE IN')
		->setCellValue('M'.$awl, 'WAKTU GATE OUT')
		;

	$totData = count($data);
    for ($i = 0; $i < $totData; $i++)
    {
        $pos = ($awl+1+$i);

        $spreadsheet->getActiveSheet()
        	->setCellValue('A'.$pos, $data[$i]['CAR'])
			// ->setCellValue('B'.$pos, $data[$i]['SERI KEMASAN'])
			->setCellValue('C'.$pos, $data[$i]['JMKEMAS'])
			// ->setCellValue('D'.$pos, $data[$i]['KESESUAIAN DOKUMEN'])
			// ->setCellValue('E'.$pos, $data[$i]['KETERANGAN'])
			->setCellValue('F'.$pos, $data[$i]['JNKEMAS'])
			->setCellValue('G'.$pos, $data[$i]['MERKKEMAS'])
			// ->setCellValue('H'.$pos, $data[$i]['NIP GATE IN'])
			// ->setCellValue('I'.$pos, $data[$i]['NIP GATE OUT'])
			// ->setCellValue('J'.$pos, $data[$i]['NOMOR POLISI'])
			// ->setCellValue('K'.$pos, $data[$i]['NOMOR SEGEL'])
			// ->setCellValue('L'.$pos, $data[$i]['WAKTU GATE IN'])
			// ->setCellValue('M'.$pos, $data[$i]['WAKTU GATE OUT'])
			;
    }

    $spreadsheet->getActiveSheet()->setTitle('Kemasan');
    unset($data);


    // KONTAINER
	$data = getCon($car);
	$awl = 1;

	$spreadsheet->createSheet();
	$spreadsheet->setActiveSheetIndex(6)
		->setCellValue('A'.$awl, 'NOMOR AJU')
		->setCellValue('B'.$awl, 'SERI KONTAINER')
		->setCellValue('C'.$awl, 'KESESUAIAN DOKUMEN')
		->setCellValue('D'.$awl, 'KETERANGAN')
		->setCellValue('E'.$awl, 'KODE STUFFING')
		->setCellValue('F'.$awl, 'KODE TIPE KONTAINER')
		->setCellValue('G'.$awl, 'KODE UKURAN KONTAINER')
		->setCellValue('H'.$awl, 'FLAG GATE IN')
		->setCellValue('I'.$awl, 'FLAG GATE OUT')
		->setCellValue('J'.$awl, 'NOMOR POLISI')
		->setCellValue('K'.$awl, 'NOMOR KONTAINER')
		->setCellValue('L'.$awl, 'NOMOR SEGEL')
		->setCellValue('M'.$awl, 'WAKTU GATE IN')
		->setCellValue('N'.$awl, 'WAKTU GATE OUT')
		;

	$totData = count($data);
    for ($i = 0; $i < $totData; $i++)
    {
        $pos = ($awl+1+$i);

        $spreadsheet->getActiveSheet()
        	->setCellValue('A'.$pos, $data[$i]['CAR'])
			// ->setCellValue('B'.$pos, 'SERI KONTAINER')
			// ->setCellValue('C'.$pos, 'KESESUAIAN DOKUMEN')
			// ->setCellValue('D'.$pos, 'KETERANGAN')
			// ->setCellValue('E'.$pos, 'KODE STUFFING')
			->setCellValue('F'.$pos, $data[$i]['CONTTIPE'])
			->setCellValue('G'.$pos, $data[$i]['CONTUKUR'])
			// ->setCellValue('H'.$pos, 'FLAG GATE IN')
			// ->setCellValue('I'.$pos, 'FLAG GATE OUT')
			// ->setCellValue('J'.$pos, 'NOMOR POLISI')
			->setCellValue('K'.$pos, $data[$i]['CONTNO'])
			// ->setCellValue('L'.$pos, 'NOMOR SEGEL')
			// ->setCellValue('M'.$pos, 'WAKTU GATE IN')
			// ->setCellValue('N'.$pos, 'WAKTU GATE OUT')
			;
    }

    $spreadsheet->getActiveSheet()->setTitle('Kontainer');
    unset($data);


    // RESPON
	$data = getRes($car);
	$awl = 1;

	$spreadsheet->createSheet();
	$spreadsheet->setActiveSheetIndex(7)
		->setCellValue('A'.$awl, 'NOMOR AJU')
		->setCellValue('B'.$awl, 'KODE RESPON')
		->setCellValue('C'.$awl, 'NOMOR RESPON')
		->setCellValue('D'.$awl, 'TANGGAL RESPON')
		->setCellValue('E'.$awl, 'WAKTU RESPON')
		->setCellValue('F'.$awl, 'BYTE STRAM PDF')
		;

	$totData = count($data);
    for ($i = 0; $i < $totData; $i++)
    {
        $pos = ($awl+1+$i);

        $spreadsheet->getActiveSheet()
        	->setCellValue('A'.$pos, $data[$i]['CAR'])
			->setCellValue('B'.$pos, $data[$i]['KODE_RESPON'])
			->setCellValue('C'.$pos, $data[$i]['NOMOR_RESPON'])
			->setCellValue('D'.$pos, $data[$i]['TANGGAL_RESPON'])
			->setCellValue('E'.$pos, $data[$i]['WAKTU_RESPON'])
			->setCellValue('F'.$pos, $data[$i]['BYTE_STREM_PDF'])
			;
    }

    $spreadsheet->getActiveSheet()->setTitle('Respon');
    unset($data);


    // STATUS
	$data = getRes($car);
	$awl = 1;

	$spreadsheet->createSheet();
	$spreadsheet->setActiveSheetIndex(8)
		->setCellValue('A'.$awl, 'NOMOR AJU')
		->setCellValue('B'.$awl, 'KODE RESPON')
		->setCellValue('C'.$awl, 'NOMOR RESPON')
		;

	$totData = count($data);
    for ($i = 0; $i < $totData; $i++)
    {
        $pos = ($awl+1+$i);

        $spreadsheet->getActiveSheet()
        	->setCellValue('A'.$pos, $data[$i]['CAR'])
			->setCellValue('B'.$pos, $data[$i]['KODE_RESPON'])
			->setCellValue('C'.$pos, $data[$i]['NOMOR_RESPON'])
			;
    }

    $spreadsheet->getActiveSheet()->setTitle('Status');
    unset($data);


    // PUNGUTAN
	$data = getPgt($car);
	$awl = 1;

	$spreadsheet->createSheet();
	$spreadsheet->setActiveSheetIndex(9)
		->setCellValue('A'.$awl, 'NOMOR AJU')
		->setCellValue('B'.$awl, 'JENIS TARIF')
		->setCellValue('C'.$awl, 'KODE FASILITAS')
		->setCellValue('D'.$awl, 'NILAI PUNGUTAN')
		;

	$totData = count($data);
    for ($i = 0; $i < $totData; $i++)
    {
        $pos = ($awl+1+$i);

        $spreadsheet->getActiveSheet()
        	->setCellValue('A'.$pos, $data[$i]['CAR'])
			->setCellValue('B'.$pos, $data[$i]['JENIS_TARIF'])
			->setCellValue('C'.$pos, $data[$i]['KDFASIL'])
			->setCellValue('D'.$pos, $data[$i]['NILBEBAN'])
			;
    }

    $spreadsheet->getActiveSheet()->setTitle('Pungutan');
    unset($data);
}

function getHdr($select='')
{
	$sql = "SELECT ".(($select) ? $select : '*')." FROM T_BC23HDR";
	$data = getResult($sql);

	return $data;
}

function getBrg($car)
{
	$sql = "SELECT * FROM T_BC23DTL WHERE CAR IN (".$car.") ORDER BY CAR, SERIAL ASC";
	$data = getResult($sql);

	return $data;
}

function getTrf($car)
{
	$sql = "SELECT * FROM T_BC23TRF WHERE CAR IN (".$car.") ORDER BY CAR, SERIBRG ASC";
	$data = getResult($sql);

	return $data;
}

function getDtlDok($car)
{
	$sql = "SELECT * FROM T_BC23DTLDOK WHERE CAR IN (".$car.") ORDER BY CAR, SERIBRG, SERIDOK ASC";
	$data = getResult($sql);

	return $data;
}

function getDok($car)
{
	$sql = "SELECT * FROM T_BC23DOK WHERE CAR IN (".$car.") ORDER BY CAR, SERIDOK ASC";
	$data = getResult($sql);

	return $data;
}

function getKms($car)
{
	$sql = "SELECT * FROM T_BC23KMS WHERE CAR IN (".$car.") ORDER BY CAR ASC";
	$data = getResult($sql);

	return $data;
}

function getCon($car)
{
	$sql = "SELECT * FROM T_BC23CON WHERE CAR IN (".$car.") ORDER BY CAR ASC";
	$data = getResult($sql);

	return $data;
}

function getRes($car)
{
	$sql = "SELECT * FROM T_BC23CON WHERE CAR IN (".$car.") ORDER BY CAR ASC";
	$data = getResult($sql);

	return $data;
}

function getPgt($car)
{
	$sql = "SELECT * FROM T_BC23PGT WHERE CAR IN (".$car.") ORDER BY CAR ASC";
	$data = getResult($sql);

	return $data;
}