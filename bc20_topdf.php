<?php

require('./lib/database.php');
require('./lib/main.php');
require('./lib/fpdf/fpdf.php');

$pdf = new FPDF();


// $sql = "SELECT CAR FROM T_BC20HDR LIMIT 1";
$sql = "SELECT CAR FROM T_BC20HDR WHERE CAR = '00000012345620170820123458'";
$cars = getResult($sql);

foreach($cars as $car)
{
	$pdf->AddPage();

	drawpib($pdf, $car['CAR']);
	
	$pdf->Output();
}

exit();

function gethdr($car)
{
	$whrTbl = "a.CAR = '" . $car ."'";
    $whrOn = "CAR = '" . $car ."'";
    
    $sql = "SELECT *, 
                a.APINO AS APINO,
                (SELECT URAIAN FROM TM_KPBC WHERE KODE = a.KDKPBC) as URKDKPBC ,
                (SELECT URAIAN FROM TM_KODE_ID WHERE KODE = a.IMPID) as URIMPID,
                (SELECT URAIAN FROM TM_JENIS_API WHERE KODE = a.APIKD) as URAPIKD,
                (SELECT URAIAN FROM TM_KODE_ID WHERE KODE = a.INDID) as URINDID,
                (SELECT URAIAN FROM TM_JENIS_DOKUMEN WHERE KODE = a.DOKTUPKD) as URDOKTUPKD,
                DATE_FORMAT(a.DOKTUPTG,'%d-%m-%Y') as DOKTUPTG,
                DATE_FORMAT(a.PPJKTG,'%d-%m-%Y') as PPJKTG,
                (SELECT URAIAN FROM TM_TAX_FACILITY WHERE KODE = a.KDFAS) as URKDFAS,
                CASE
                    WHEN INSTR('06|54|55|56|57|58', KDFAS)
                    THEN 
                        CASE 
                            WHEN (SELECT COUNT(*) FROM T_BC20DOK WHERE DOKKD = '861' AND ".$whrOn.") = 1
                            THEN (SELECT CONCAT(URAIAN,'|',DOKNO,'|',DOKTG)
                                FROM T_BC20DOK DOK 
                                LEFT JOIN TM_JENIS_DOKUMEN URDOK ON URDOK.KODE = DOK.DOKKD
                                WHERE DOKKD = '861' AND ".$whrOn.")
                        END
                    END AS DOKSKEP,
                (SELECT URAIAN FROM TM_GUDANG WHERE KDKPBC = a.KDKPBC AND KODE = a.TMPTBN) as URTMPTBN,
                (SELECT URAIAN FROM TM_MODA WHERE KODE = a.MODA) as URMODA,
                (SELECT URAIAN FROM TM_NEGARA WHERE KODE = a.ANGKUTFL) as URANGKUTFL,
                (SELECT URAIAN FROM TM_CURRENCY WHERE KODE = a.KDVAL) as URKDVAL,
                DATE_FORMAT(a.TGTIBA,'%d-%m-%Y') as TGTIBA,
                (SELECT URAIAN FROM TM_INCOTERM WHERE KODE = a.KDHRG) as URKDHRG,
                (SELECT URAIAN FROM TM_PELABUHAN WHERE KODE = a.PELTRANSIT) as URPELTRANSIT,
                (SELECT URAIAN FROM TM_PELABUHAN WHERE KODE = a.PELMUAT) as URPELMUAT,
                (SELECT URAIAN FROM TM_PELABUHAN WHERE KODE = a.PELBKR) as URPELBKR,
                DATE_FORMAT(a.TANGGAL_TTD,'%d-%m-%Y') as TANGGAL_TTD,
                DATE_FORMAT(CONCAT(SUBSTRING(a.CAR,13,8)), '%d-%m-%Y') as TANGGAL_TTD_2,
                a.IMPSTATUS AS IMPSTATUS
            FROM T_BC20HDR a 
            WHERE ".$whrTbl;
    $datahdr = getResult($sql)[0];

    return $datahdr;
}

function getdok($car) {
    $sql = "SELECT DOKKD, DOKNO, DATE_FORMAT(DOKTG,'%d-%m-%Y') AS DOKTG,
                   B.URAIAN AS 'URDOKKD'
            FROM   T_BC20DOK A
            LEFT JOIN TM_JENIS_DOKUMEN B ON B.KODE = A.DOKKD
            WHERE  CAR = '" . $car . "'";
    $datadok = getResult($sql);
    
    return $datadok;
}

function getcon($car) {
    $sql = "SELECT CONTNO, CONCAT(CONTUKUR,' Feet') as CONTUKUR,CONCAT(CONTTIPE,'CL') as CONTTIPE
            FROM t_bc20con 
            WHERE CAR = '" . $car . "'";
    $datacon = getResult($sql);

    return $datacon;
}

function getkms($car) {
    $sql = "SELECT JNKEMAS,JMKEMAS,MERKKEMAS,(SELECT URAIAN FROM TM_PACKAGE WHERE KODE  = JNKEMAS) as URJNKEMAS
            FROM t_bc20kms 
            WHERE CAR = '" . $car . "'";
    $datakms = getResult($sql);

    return $datakms;
}

function getdtl($car) {
    $sql = "SELECT *,
                dtldok.KDFASDTL AS KDFASDTL,
                GROUP_CONCAT(CONCAT((SELECT URAIAN FROM TM_TAX_FACILITY WHERE KODE = dtldok.KDFASDTL),'(',dtldok.SERIDOK,')') SEPARATOR ', ') AS URKDFASDTL,
                dtl.FLLARTAS AS FLLARTAS,
                GROUP_CONCAT(
                 CASE
                  WHEN dtldok.KDFASDTL = 'Y' THEN CONCAT((SELECT URAIAN FROM TM_JENIS_DOKUMEN WHERE KODE = dtldok.DOKKD),'(',dtldok.SERIDOK,')')
                 END 
                 SEPARATOR ', '
                ) AS URLARTAS,
                (SELECT URAIAN FROM TM_NEGARA WHERE KODE = BRGASAL) AS URBRGASAL,
                (SELECT URAIAN FROM TM_SATUAN WHERE KODE = KDSAT) AS URKDSAT,
                (SELECT URAIAN FROM TM_PACKAGE WHERE KODE = KEMASJN) AS URKEMASJN,
                dtlvd.Jenis AS JNSTRANS,
                dtlvd.Nilai AS NILVD
            FROM t_bc20dtl dtl
            LEFT JOIN t_bc20fas fas ON dtl.KODE_TRADER = fas.KODE_TRADER AND  
                                  dtl.CAR         = fas.CAR AND 
                                  dtl.SERIAL      = fas.SERIAL
            LEFT JOIN t_bc20trf trf ON dtl.KODE_TRADER = trf.KODE_TRADER AND
                                  dtl.CAR         = trf.CAR AND
                                  dtl.NOHS      = trf.NOHS AND
                                  dtl.SERITRP      = trf.SERITRP
            LEFT JOIN t_bc20dtldok dtldok ON dtl.KODE_TRADER = dtldok.KODE_TRADER AND  
                                  dtl.CAR         = dtldok.CAR AND 
                                  dtl.SERIAL      = dtldok.SERIBRG
            LEFT JOIN t_bc20dtlvd dtlvd ON dtl.KODE_TRADER = dtlvd.KODE_TRADER AND
                                  dtl.CAR         = dtlvd.CAR AND 
                                  dtl.SERIAL      = dtlvd.SERIBRG
            WHERE dtl.CAR = '" . $car . "' 
            GROUP BY dtl.SERIAL
            ORDER BY dtl.SERIAL ASC";
    $datadtl = getResult($sql);

    return $datadtl;
}

function getCukai($car) {
    $sql = "SELECT *,
                    MERK AS 'MEREK',
                    C.URAIAN AS 'URKOMODITI',
                    D.URAIAN AS 'URJNSTARIF',
                    E.URAIAN AS 'URSUBKOMODITI',
                    B.TRPCUK AS 'BESARTRF',
                    DNilCuk AS 'NILAITRF',
                    HJECuk AS 'HRGJUAL',
                    F.URAIAN AS 'URKEMASAN',
                    JmPC AS 'PITACUKAI',
                    SaldoAwalPC AS 'SALDOAWALPC',
                    SaldoAkhirPC AS 'SALDOAKHIRPC'
            FROM t_bc20dtl A
            left join t_bc20trf B on A.CAR = B.CAR and A.NOHS = B.NOHS and A.SERITRP = B.SERITRP
			left join tm_cukai_komodity C on B.KDCUK = C.KODE
			left join tm_kode_tarif D on B.KDTRPCUK = D.KODE
			left join tm_cukai_sub_komodity E on B.KDCUKSUB = E.KODE
			left join TM_PACKAGE F on KdKmsCuk = F.KODE
            WHERE A.CAR = '" . $car . "' ORDER BY A.SERIAL ASC";
    $datacuk = getResult($sql);

    return $datacuk;
}

function getpgt($car) {
    $sql = "SELECT KDBEBAN,
    				KDFASIL,
                    NILBEBAN 
              FROM  t_bc20pgt 
              WHERE t_bc20pgt.CAR='" . $car . "'";
    $arrPGT = getResult($sql);
    foreach ($arrPGT as $row) {
        $data['data_pgt'][$row['KDBEBAN']][$row['KDFASIL']] += $row['NILBEBAN'];
        $data['data_pgt']['TOTAL'][$row['KDFASIL']] += $row['NILBEBAN'];
    }
    $datapgt = $data;

    return $datapgt;
}

function drawpib($pdf, $car)
{
	$datahdr = gethdr($car);
	$datadok = getdok($car);
	$datacon = getcon($car);
	$datakms = getkms($car);
	$datadtl = getdtl($car);
	$datapgt = getpgt($car);
	$datacuk = getCukai($car);

	$pdf->SetAutoPageBreak(0, 0);
    $pdf->AliasNbPages('{totalPages}');
    $pdf->SetY(5.5);
    $pdf->SetFont('times', 'B', '12');
    $pdf->cell(190, 4, 'PEMBERITAHUAN IMPOR BARANG (PIB)', 0, 0, 'C', 0);        
    $pdf->SetFont('times', '', '8');
    $pdf->text(193, 9, 'BC 2.0');

    $pdf->Rect(7.5, 10, 194, 20, 1, 'F');
    $pdf->Rect(130, 10, 14, 4, 1, 'F');
    $pdf->Rect(52.8, 18, 4, 11, 1, 'F');
    $pdf->Rect(52.8, 21.7, 4, 3.4, 1, 'F');

    $pdf->Ln();
    $pdf->cell(45, 5, 'Kantor Pabean', 0, 0, 'L', 0);
    $pdf->cell(2, 5, ':', 0, 0, 'L', 0);
    $pdf->cell(75, 5, $datahdr['URKDKPBC'], 0, 0, 'L', 0); //var
    $pdf->cell(10, 5, $datahdr['KDKPBC'], 0, 0, 'L', 0); //var
    $pdf->cell(30, 5, '', 0, 0, 'L', 0);
    $pdf->cell(2, 5, 'Halaman '.$pdf->PageNo().' dari '.'{totalPages}', 0, 0, 'L', 0); //var

    $pdf->Ln(3.5);
    $pdf->cell(45, 6, 'Nomor Pengajuan', 0, 0, 'L', 0);
    $pdf->cell(2, 6, ':', 0, 0, 'L', 0);
    $pdf->cell(75, 6, formatcar($datahdr['CAR']), 0, 0, 'L', 0); //var
    $pdf->cell(27, 6, 'Tanggal Pengajuan :', 0, 0, 'L', 0);
    $pdf->cell(30, 6, strtotimecustom(($datahdr['PIBTG'])), 0, 0, 'L', 0); // tanggal pengajuan

    $pdf->Ln(4);
    $pdf->cell(43, 6, 'A. Jenis PIB', 0, 0, 'L', 0);
    $pdf->cell(4, 6, $datahdr['JNPIB'], 0, 0, 'L', 0); //var
    $pdf->cell(25, 6, '1. Biasa;', 0, 0, 'L', 0);
    $pdf->cell(25, 6, '2. Berkala.', 0, 0, 'L', 0);

    $pdf->Ln(3.5);
    $pdf->cell(43, 6, 'B. Jenis Impor', 0, 0, 'L', 0);
    $pdf->cell(4, 6, $datahdr['JNIMP'], 0, 0, 'L', 0); //var
    $pdf->cell(25, 6, '1. Untuk Dipakai;', 0, 0, 'L', 0); //var
    $pdf->cell(20, 6, '2. Sementara;', 0, 0, 'L', 0);
    $pdf->cell(30, 6, '5. Pelayanan Segera;', 0, 0, 'L', 0);
    $pdf->cell(30, 6, '9. Gabungan 1 & 2.', 0, 0, 'L', 0);

    $pdf->Ln(3.5);
    $pdf->cell(43, 6, 'C. Cara Pembayaran', 0, 0, 'L', 0);
    $pdf->cell(4, 6, $datahdr['CRBYR'], 0, 0, 'L', 0); //var
    $pdf->cell(25, 6, '1. Biasa/Tunai;', 0, 0, 'L', 0); //var
    $pdf->cell(20, 6, '2. Berkala;', 0, 0, 'L', 0);
    $pdf->cell(30, 6, '3. Dengan Jaminan;', 0, 0, 'L', 0);
    $pdf->cell(30, 6, '9. Lainnya.', 0, 0, 'L', 0);

    $pdf->Ln(5);
    $pdf->cell(43, 6, 'D. DATA PEMBERITAHUAN', 0, 0, 'L', 0);
    
    $pdf->Ln(4);
    $pdf->cell(43, 6, 'PENGIRIM', 0, 0, 'L', 0);
    $pdf->cell(42, 6, '', 0, 0, 'L', 0);
    $pdf->cell(8, 6, $datahdr['PASOKNEG'], 0, 0, 'L', 0); //var
    $pdf->cell(2, 6, '', 0, 0, 'L', 0);
    $pdf->cell(54, 6, 'G. No. & Tgl. Pendaftaran :', 0, 0, 'L', 0);
    $pdf->cell(20, 6, $datahdr['PIBNO'], 0, 0, 'C', 0); //var
    $pdf->cell(2.5, 6, '', 0, 0, 'C', 0);
    $pdf->cell(20, 6, strtotimecustom(($datahdr['PIBTG'])), 0, 0, 'C', 0); //var
    
    $pdf->Ln(3.5);
    $pdf->cell(43, 5, '1. Nama, Alamat:', 0, 0, 'L', 0);
    $pdf->cell(52, 8, '', 0, 0, 'L', 0);
    $pdf->cell(40, 8, ' 9. Cara Pengangkutan :', 0, 0, 'L', 0);
    $pdf->cell(41, 8, $datahdr['URMODA'], 0, 0, 'C', 0); //var
    $pdf->cell(15, 8, $datahdr['MODA'], 0, 0, 'C', 0); //var

    $pdf->Ln(4);
    $pdf->cell(3, 5, '', 0, 0, 'L', 0);
    $pdf->cell(43, 3, $datahdr['PASOKNAMA'], 0, 0, 'L', 0); //var
    $pdf->cell(49, 8, '', 0, 0, 'L', 0);
    $pdf->cell(81.5, 8, '10. Nama Sarana Pengangkut & No. Voy/Flight dan Bendera :', 0, 0, 'L', 0);
    $pdf->cell(15, 8, $datahdr['ANGKUTFL'], 0, 0, 'C', 0);

    $pdf->Ln(3.5);
    $pdf->cell(3, 5, '', 0, 0, 'L', 0);
    $pdf->MultiCell(90, 3, $datahdr['PASOKALMT'], 0, 'L', 0); //var
    $pdf->setXY(105,45);
    $pdf->cell(40, 8, $datahdr['ANGKUTNAMA'].' '.$datahdr['ANGKUTNO'].' '.$datahdr['URANGKUTFL'], 0, 0, 'L', 0); //var

    $pdf->Ln(4);
    $pdf->cell(43, 6, 'PENJUAL', 0, 0, 'L', 0);
    $pdf->cell(40, 6, '', 0, 0, 'L', 0);
    $pdf->cell(10, 6, $datahdr['PENJNEG'], 0, 0, 'C', 0); //var
    $pdf->cell(2, 6, '', 0, 0, 'L', 0);
    $pdf->cell(30, 10, '11. Perkiraan Tgl. Tiba :', 0, 0, 'L', 0);
    $pdf->cell(30, 10, strtotimecustom($datahdr['TGTIBA']), 0, 0, 'L', 0); //var

    $pdf->Ln(5);
    $pdf->cell(43, 5, '1a. Nama, Alamat:', 0, 0, 'L', 0);

    $pdf->Ln(4);
    $pdf->cell(3, 5, '', 0, 0, 'L', 0);
    $pdf->cell(43, 5, $datahdr['PENJNAMA'], 0, 0, 'L', 0); //var
    $pdf->cell(49, 5, '', 0, 0, 'L', 0);
    $pdf->cell(30, 1, '12. Pelabuhan Muat', 0, 0, 'L', 0);
    $pdf->cell(2, 1, ':', 0, 0, 'L', 0);
    $pdf->cell(49, 1, $datahdr['URPELMUAT'], 0, 0, 'L', 0); //var
    $pdf->cell(16, 1, $datahdr['PELMUAT'], 0, 0, 'C', 0); //var

    $pdf->Ln(4);
    $pdf->cell(3, 5, '', 0, 0, 'L', 0);
    $pdf->MultiCell(90, 3, $datahdr['PENJALMT'], 0, 'L', 0); //var
    $pdf->setXY(105,62);
    $pdf->cell(30, 1, '13. Pelabuhan Transit', 0, 0, 'L', 0);
    $pdf->cell(2, 1, ':', 0, 0, 'L', 0);
    $pdf->cell(49, 1, $datahdr['URPELTRANSIT'], 0, 0, 'L', 0); //var
    $pdf->cell(16, 1, $datahdr['PELTRANSIT'], 0, 0, 'C', 0); //var

    $pdf->Ln(4);
    $pdf->cell(95, 5, '', 0, 0, 'L', 0);
    $pdf->cell(30, 1, '14. Pelabuhan Tujuan', 0, 0, 'L', 0);
    $pdf->cell(2, 1, ':', 0, 0, 'L', 0);
    $pdf->cell(49, 1, $datahdr['URPELBKR'], 0, 0, 'L', 0); //var
    $pdf->cell(16, 1, $datahdr['PELBKR'], 0, 0, 'C', 0); //var

    for ($x=0; $x < count($datadok); $x++){
        if($datadok[$x]['DOKKD'] == '380'){
            $noinvoice = $datadok[$x]['DOKNO'];
            $invtg = strtotimecustom($datadok[$x]['DOKTG']);
        }
        if($datadok[$x]['DOKKD'] == '705' || $datadok[$x]['DOKKD'] == '740'){
            $nohouse = $datadok[$x]['DOKNO'];
            $housetg = strtotimecustom($datadok[$x]['DOKTG']);
            if($datadok[$x]['DOKKD'] == '705'){
                $pdf->Line(123, 78.6, 129, 78.6);
                $pdf->Line(123, 79.0, 129, 79.0);
                $pdf->Line(124, 83.3, 130, 83.3);
                $pdf->Line(124, 83.7, 130, 83.7);
            }
            if($datadok[$x]['DOKKD'] == '740'){
                $pdf->Line(119, 78.6, 122, 78.6);
                $pdf->Line(119, 79.0, 122, 79.0);
                $pdf->Line(119, 83.3, 123, 83.3);
                $pdf->Line(119, 83.7, 123, 83.7);
            }
        }
        if($datadok[$x]['DOKKD'] == '704' || $datadok[$x]['DOKKD'] == '741'){
            $nomaster = $datadok[$x]['DOKNO'];
            $mastertg = strtotimecustom($datadok[$x]['DOKTG']);
            if($datadok[$x]['DOKKD'] == '704'){
                $pdf->Line(123, 78.6, 129, 78.6);
                $pdf->Line(123, 79.0, 129, 79.0);
                $pdf->Line(124, 83.3, 130, 83.3);
                $pdf->Line(124, 83.7, 130, 83.7);
            }
            if($datadok[$x]['DOKKD'] == '741'){
                $pdf->Line(119, 78.6, 122, 78.6);
                $pdf->Line(119, 79.0, 122, 79.0);
                $pdf->Line(119, 83.3, 123, 83.3);
                $pdf->Line(119, 83.7, 123, 83.7);
            }
        }
        if($datadok[$x]['DOKKD'] == '465'){
            $notrans = $datadok[$x]['DOKNO'];
            $tgtrans = strtotimecustom($datadok[$x]['DOKTG']);
        }
    }

    $pdf->Ln(4);
    $pdf->cell(43, 3, 'IMPORTIR', 0, 0, 'L', 0);
    $pdf->cell(52, 3, '', 0, 0, 'L', 0);
    $pdf->cell(30, 3, '15. Invoice', 0, 0, 'L', 0);
    $pdf->cell(2, 3, ':', 0, 0, 'L', 0);
    $pdf->cell(30, 3, 'No. '.$noinvoice, 0, 0, 'L', 0); //No. Invoice
    $pdf->cell(10, 3, '', 0, 0, 'L', 0);
    $pdf->cell(30, 3, 'Tgl. '.$invtg, 0, 0, 'L', 0); //Tgl. Invoice

    $pdf->Ln(4);
    $pdf->cell(40, 3, '2. Identitas : ('.$datahdr['URIMPID'].')', 0, 0, 'L', 0); //var
    $pdf->cell(22, 3, formatNPWP($datahdr['IMPNPWP']), 0, 0, 'L', 0); //var
    $pdf->cell(33, 3, '', 0, 0, 'L', 0);
    $pdf->cell(20, 3, '16. Transaksi', 0, 0, 'L', 0);
    $pdf->cell(10, 3, $datahdr['JNSTRANS'], 0, 0, 'C', 0); //Transaksi
    $pdf->cell(2, 3, ':', 0, 0, 'L', 0);
    $pdf->cell(30, 3, 'No. '.$notrans, 0, 0, 'L', 0); //No. Transaksi
    $pdf->cell(10, 3, '', 0, 0, 'L', 0);
    $pdf->cell(30, 3, 'Tgl. '.$tgtrans, 0, 0, 'L', 0); //Tgl. Transaksi

    $pdf->Ln(3.5);
    $pdf->cell(40, 3, '3. Nama, Alamat : '.$datahdr['IMPNAMA'], 0, 0, 'L', 0);
    $pdf->cell(55, 3, '', 0, 0, 'L', 0);
    $pdf->cell(30, 3, '17. House-BL/AWB', 0, 0, 'L', 0);
    $pdf->cell(2, 3, ':', 0, 0, 'L', 0);
    $pdf->cell(30, 3, 'No. '.$nohouse, 0, 0, 'L', 0); //No. House BL
    $pdf->cell(10, 3, '', 0, 0, 'L', 0);
    $pdf->cell(30, 3, 'Tgl. '.$housetg, 0, 0, 'L', 0); //Tgl. House BL

    $pdf->Ln(3.5);
    $pdf->cell(3, 3, '', 0, 0, 'L', 0);
    $pdf->MultiCell(90, 3, $datahdr['IMPALMT'], 0, 'L', 0); //var
    $pdf->setXY(105,82);
    $pdf->cell(30, 3, '      Master-BL/AWB', 0, 0, 'L', 0);
    $pdf->cell(2, 3, ':', 0, 0, 'L', 0);
    $pdf->cell(30, 3, 'No. '.$nomaster, 0, 0, 'L', 0); //No. Master BL
    $pdf->cell(10, 3, '', 0, 0, 'L', 0);
    $pdf->cell(30, 3, 'Tgl. '.$mastertg, 0, 0, 'L', 0); //Tgl. Master BL
    
    $doktup = '';
    if($datahdr['DOKTUPKD'] == '1'){
        $doktup = 'BC 1.1';
    } else if($datahdr['DOKTUPKD'] == '2'){
        $doktup = 'BC 1.2';
    } else if($datahdr['DOKTUPKD'] == '3'){
        $doktup = 'BC 2.3';
    } else {
        $doktup = 'Dok. Lainnya';
    }

    $pdf->Ln(4);
    $pdf->cell(3, 3, '', 0, 0, 'L', 0);
    $pdf->cell(40, 3, '', 0, 0, 'L', 0); 
    $pdf->cell(52, 3, '', 0, 0, 'L', 0);
    $pdf->cell(30, 3, '18. '.$doktup, 0, 0, 'L', 0); // xvar
    $pdf->cell(2, 3, ':', 0, 0, 'L', 0);
    $pdf->cell(30, 3, 'No. '.$datahdr['DOKTUPNO'], 0, 0, 'L', 0); //No. BC11
    $pdf->cell(10, 3, '', 0, 0, 'L', 0);
    $pdf->cell(30, 3, 'Tgl. '.$datahdr['DOKTUPTG'], 0, 0, 'L', 0); //Tgl. Bc11

    $pdf->Ln(4);
    $pdf->cell(15, 3, '4. Status :', 0, 0, 'L', 0);
    
    $urimpstatus = '';
    if($datahdr['IMPSTATUS'] == "A"){
        $urimpstatus = 'AEO';
    } else if ($datahdr['IMPSTATUS'] == "M"){
        $urimpstatus = 'MITA';
    } else if ($datahdr['IMPSTATUS'] == "L"){
        $urimpstatus = 'Lainnya';
    } else {
        $urimpstatus = '';
    }

    $pdf->cell(20, 3, $urimpstatus, 0, 0, 'L', 0); //var
    $pdf->cell(15, 3, '5. '.$datahdr['URAPIKD'].' :', 0, 0, 'L', 0);
    $pdf->cell(15, 3, $datahdr['APINO'], 0, 0, 'L', 0); //var
    $pdf->cell(62, 3, '', 0, 0, 'L', 0);
    $pdf->cell(30, 3, 'Pos : '.$datahdr['POSNO'], 0, 0, 'L', 0); //var
    $pdf->cell(10, 3, '', 0, 0, 'L', 0);
    $pdf->cell(30, 3, 'Sub : '.$datahdr['POSSUB'].'.'.$datahdr['POSSUBSUB'], 0, 0, 'L', 0); //var

    $pdf->Ln(5);
    $pdf->cell(40, 3, 'PEMILIK BARANG', 0, 0, 'L', 0);
    $pdf->cell(55, 3, '', 0, 0, 'L', 0);
    $pdf->cell(81.5, 3, '19. Pemenuhan Persyaratan/Fasilitas Impor :', 0, 0, 'L', 0);
    $pdf->cell(15, 2.5, $datahdr['KDFAS'], 0, 0, 'C', 0);

    $pdf->Ln(3.5);
    $pdf->cell(40, 3, '2a. Identitas : ('.$datahdr['URINDID'].')', 0, 0, 'L', 0); //var
    $pdf->cell(60, 3, formatNPWP($datahdr['INDNPWP']), 0, 0, 'L', 0); //var
    $pdf->cell(30, 2, $datahdr['URKDFAS'], 0, 0, 'L', 0);

    $pdf->Ln(3.5);
    $pdf->cell(100, 2, '3a. Nama, Alamat : '.$datahdr['INDNAMA'], 0, 0, 'L', 0);
    // $pdf->cell(60, 3, '', 0, 0, 'L', 0);

    if($datahdr['KDFAS'])
    {
        if(strpos('|06|54|55|56|57|58|', $datahdr['KDFAS']))
        {
            $DOKSKEP = $datahdr['DOKSKEP'];
            if($DOKSKEP)
            {
                $DOKSKEP = explode('|', $DOKSKEP);
                $SKEP = $DOKSKEP[0];
                $SKEPNO = $DOKSKEP[1];
                $SKEPTG = $DOKSKEP[2];

                $pdf->cell(68, 3, $SKEP, 0, 0, 'L', 0);
                $pdf->cell(20, 3, 'Tgl. '. date("d-m-Y",strtotime($SKEPTG)), 0, 0, 'L', 0);
            }
            else
            {
                $pdf->cell(68, 3, '==lihat lampiran==', 0, 0, 'L', 0);
                $pdf->cell(20, 3, 'Tgl. ', 0, 0, 'L', 0);
            }

        }
        else
        {
            $pdf->cell(68, 3, '==lihat lampiran==', 0, 0, 'L', 0);
            $pdf->cell(20, 3, 'Tgl. '. date("d-m-Y",strtotime($SKEPTG)), 0, 0, 'L', 0);
        }
    }


    $pdf->Ln(3.5);
    $pdf->MultiCell(90, 3, $datahdr['INDALMT'], 0, 'L', 0); //var
    $pdf->setXY(110,105);
    $pdf->cell(68, 3, $SKEPNO, 0, 0, 'L', 0);        
    $pdf->setXY(105,112);
    $pdf->cell(80, 3, '20. Tempat Penimbunan :', 0, 0, 'L', 0);
    $pdf->cell(18, 2, $datahdr['TMPTBN'], 0, 0, 'C', 0); //var

    $pdf->Ln(4);
    $pdf->cell(3, 3, '', 0, 0, 'L', 0);
    $pdf->cell(40, 3, '', 0, 0, 'L', 0); //var
    $pdf->cell(57, 3, '', 0, 0, 'L', 0);
    $pdf->cell(30, 3, $datahdr['URTMPTBN'], 0, 0, 'L', 0); //var

    $pdf->Ln(5);
    $pdf->cell(43, 3, 'PPJK', 0, 0, 'L', 0);
    $pdf->cell(52, 3, '', 0, 0, 'L', 0);
    $pdf->cell(35, 3, '21. Valuta :', 0, 0, 'L', 0);
    $pdf->cell(12, 2, $datahdr['KDVAL'], 0, 0, 'C', 0); //var
    $pdf->cell(2, 3, '', 0, 0, 'C', 0); 
    $pdf->cell(10, 3, '22. NDPBM :', 0, 0, 'L', 0);

    $pdf->Ln(4);
    $pdf->cell(20, 3, '6. NPWP ', 0, 0, 'L', 0);
    $pdf->cell(2, 3, ':', 0, 0, 'L', 0);
    $pdf->cell(22, 3, formatNPWP($datahdr['PPJKNPWP']), 0, 0, 'L', 0); //var
    $pdf->cell(84, 3, '', 0, 0, 'L', 0);
    $pdf->cell(15, 3, $datahdr['URKDVAL'], 0, 0, 'R', 0); //var
    $pdf->cell(33, 3, '', 0, 0, 'L', 0);
    $pdf->cell(15, 3, number_format($datahdr['NDPBM'], 4, '.', ','), 0, 0, 'R', 0); //var

    $pdf->Ln(4);
    $pdf->cell(40, 3, '7. Nama, Alamat : '.$datahdr['PPJKNAMA'], 0, 0, 'L', 0);
    $pdf->cell(55, 3, '', 0, 0, 'L', 0);
    $pdf->cell(28, 3, '23. Nilai : '.$datahdr['URKDHRG'], 0, 0, 'L', 0); //var
    if($datahdr['KDHRG'] == "1" || $datahdr['KDHRG'] == "4" || $datahdr['KDHRG'] == "6" || $datahdr['KDHRG'] == "8"){
        $nilaia = $datahdr['CIF'];
    } else {
        $nilaia = $datahdr['FOB'];
    }
    $pdf->cell(20, 3, number_format($nilaia, 2, '.', ','), 0, 0, 'R', 0); //var
    $pdf->cell(1, 3, '', 0, 0, 'L', 0);
    $pdf->cell(33, 3, '26. Nilai Pabean :', 0, 0, 'L', 0); //var
    if($datahdr['VD'] == "1"){
        $pdf->cell(15, 3, 'VD', 0, 0, 'C', 0); //var    
    }
    $pdf->Ln(4);
    $pdf->cell(3, 3, '', 0, 0, 'L', 0);
    $pdf->cell(40, 3, substr($datahdr['PPJKALMT'], 0, 57), 0, 0, 'L', 0);
    $pdf->cell(52, 3, '', 0, 0, 'L', 0);
    $pdf->cell(28, 3, '24. Asuransi LN/DN :' , 0, 0, 'L', 0); //var 
    // if(($datahdr['ASURANSI'] != "0.0") || $datahdr['ASURANSI'] != "0"){
        if($datahdr['KDASS'] == '1'){
            $pdf->Line(125, 134.2, 129, 134.2);
            $pdf->Line(125, 134.7, 129, 134.7);
        } else if($datahdr['KDASS'] == '2'){
            $pdf->Line(121, 134.2, 124, 134.2);
            $pdf->Line(121, 134.7, 124, 134.7); 
        }        
    // }
    // else
    // {
            // $pdf->Line(121, 134.2, 124, 134.2);
            // $pdf->Line(121, 134.7, 124, 134.7);
            // $pdf->Line(125, 134.2, 129, 134.2);
            // $pdf->Line(125, 134.7, 129, 134.7);
    // }
    
    $pdf->cell(20, 3, number_format($datahdr['ASURANSI'], 2, '.', ','), 0, 0, 'R', 0); //var
    $pdf->cell(1, 3, '', 0, 0, 'L', 0);
    $pdf->cell(32, 3, '', 0, 0, 'L', 0); //var
    $pdf->cell(15, 3, number_format($datahdr['CIF'], 2, '.', ','), 0, 0, 'R', 0); //var

    $pdf->Ln(4);
    $pdf->cell(40, 3, '8. NP-PPJK :', 0, 0, 'L', 0);
    $pdf->cell(25, 3, $datahdr['PPJKNO'], 0, 0, 'L', 0);
    $pdf->cell(15, 3, strtotimecustom($datahdr['PPJKTG']), 0, 0, 'L', 0);
    $pdf->cell(15, 3, '', 0, 0, 'L', 0);
    $pdf->cell(28, 3, '25. Freight :', 0, 0, 'L', 0); //var
    $pdf->cell(20, 3, number_format($datahdr['FREIGHT'], 2, '.', ','), 0, 0, 'R', 0); //var
    $pdf->cell(1, 3, '', 0, 0, 'L', 0);
    $pdf->cell(32, 3, 'Rp.', 0, 0, 'L', 0); //var
    
    // if($datahdr['KDHRG'] == "1" || $datahdr['KDHRG'] == "4" || $datahdr['KDHRG'] == "6" || $datahdr['KDHRG'] == "8"){
    //     $nilaib = ($datahdr['NILINV']*$datahdr['NDPBM']);
    // } else if($datahdr['KDHRG'] == "3") {
    //     $nilaib = ($datahdr['CIF']*$datahdr['NDPBM']);
    // } else {
    //     $nilaib = ($datahdr['NILINV']*$datahdr['NDPBM']);
    // }
        $nilaib = ($datahdr['CIF']*$datahdr['NDPBM']);
    
    $pdf->cell(15, 3, number_format($nilaib, 2, '.', ',').'', 0, 0, 'R', 0); //var

    $pdf->Ln(5);
    $pdf->cell(26, 3, '27. Nomor, Ukuran, dan Tipe Peti Kemas:', 0, 0, 'L', 0);
    $pdf->cell(52, 3, '', 0, 0, 'L', 0);
    $pdf->cell(44, 3, '28. Jumlah, Jenis dan Merek:', 0, 0, 'L', 0);
    $pdf->cell(35, 2, '29. Berat Kotor (kg):', 0, 0, 'L', 0);
    $pdf->cell(10, 3, '30. Berat Bersih (kg):', 0, 0, 'L', 0);

    $pdf->Ln(4);
    $pdf->cell(4, 3, '', 0, 0, 'L', 0);
    $pdf->cell(26, 3, '', 0, 0, 'L', 0); //var
    $pdf->cell(53, 3, '', 0, 0, 'L', 0);
    $pdf->cell(43, 3, '', 0, 0, 'L', 0); //var
    $pdf->cell(30, 2, number_format($datahdr['BRUTO'], 4, '.', ','), 0, 0, 'R', 0); //var
    $pdf->cell(5, 2, '', 0, 0, 'R', 0);
    $pdf->cell(30, 3, number_format($datahdr['NETTO'], 4, '.', ','), 0, 0, 'R', 0); //var

    $pdf->Ln(1);
    $pdf->cell(4, 3, '', 0, 0, 'L', 0);
    if(count($datacon) > 8){
    		$pdf->setXY(20,150);
            $pdf->cell(26, 3, '== Lihat Lembar Lampiran ==', 0, 0, 'L', 0); //var
    } else {
    		$pdf->setX(10);
    		$pindah = array(1,3,5);
    		for ($i=0; $i < count($datacon) ; $i++) { 
                $pdf->cell(30, 2, substr($datacon[$i]['CONTNO'], 0, 4).'-'.substr($datacon[$i]['CONTNO'], 4, 11).' '.$datacon[$i]['CONTUKUR'].' '.$datacon[$i]['CONTTIPE'], 0, 0, 'L', 0);
        		$pdf->cell(10, 2, '', 0, 0, 'L', 0);
        	
        		if(in_array($i, $pindah)){
        			$pdf->Ln(3);
        		}
    		}
    }

    $pdf->setXY(78,146);
    $pdf->cell(10, 3, '', 0, 0, 'L', 0);
    if(count($datakms) > 1){
            $pdf->MultiCell(39, 3, '== Lihat Lembar Lampiran ==', 0, 'L', 0);    
    } else {
            $pdf->MultiCell(39, 3, $datakms[0]['JMKEMAS'].' '.ucfirst(strtolower($datakms[0]['URJNKEMAS'])).' / '.$datakms[0]['MERKKEMAS'], 0, 'L', 0);    
    }

    $pdf->setXY(136,150);
    $pdf->Ln(4);
    $pdf->cell(4, 3, '', 0, 0, 'L', 0);
    $pdf->cell(26, 3, '', 0, 0, 'L', 0); //var
    $pdf->cell(53, 3, '', 0, 0, 'L', 0);
    $pdf->cell(43, 3, '', 0, 0, 'L', 0); //var

    $pdf->Ln(7);
    $pdf->cell(8, 3, '31.', 0, 0, 'L', 0);
    $pdf->cell(59, 3, '32. - Pos Tarif/HS', 0, 0, 'L', 0);
    $pdf->cell(34, 3, '33. - Keterangan', 0, 0, 'L', 0);
    $pdf->cell(30, 2, '34. - Tarif & Fasilitas', 0, 0, 'L', 0);
    $pdf->cell(30, 2, '35. - Jumlah &', 0, 0, 'L', 0);
    $pdf->cell(30, 3, '36. - Nilai Pabean', 0, 0, 'L', 0);

    $pdf->Ln(3);
    $pdf->cell(9, 3, 'No.', 0, 0, 'L', 0);
    $pdf->cell(59, 3, '     - Uraian Jenis Barang, Merk, Tipe, Spesifikasi', 0, 0, 'L', 0);
    $pdf->cell(34, 3, '     - Fasilitas & No. Urut', 0, 0, 'L', 0);
    $pdf->cell(30, 2, '     - BM - PPN -BMT', 0, 0, 'L', 0);
    $pdf->cell(30, 2, '     - Jenis Satuan,', 0, 0, 'L', 0);
    $pdf->cell(30, 3, '     - Jenis', 0, 0, 'L', 0);

    $pdf->Ln(3);
    $pdf->cell(9, 3, '', 0, 0, 'L', 0);
    $pdf->cell(59, 3, '       Wajib', 0, 0, 'L', 0);
    $pdf->cell(34, 3, '     - Persyaratan & No. Urut', 0, 0, 'L', 0);
    $pdf->cell(30, 2, '     - PPnBM - Cukai', 0, 0, 'L', 0);
    $pdf->cell(30, 2, '     - Berat Bersih (kg)', 0, 0, 'L', 0);
    $pdf->cell(30, 3, '     - Nilai yang', 0, 0, 'L', 0);

    $pdf->Ln(3);
    $pdf->cell(9, 3, '', 0, 0, 'L', 0);
    $pdf->cell(59, 3, '     - Negara Asal Barang', 0, 0, 'L', 0);
    $pdf->cell(34, 3, '', 0, 0, 'L', 0);
    $pdf->cell(30, 2, '     - PPh', 0, 0, 'L', 0);
    $pdf->cell(30, 2, '     - Jml/Jns Kemasan', 0, 0, 'L', 0);
    $pdf->cell(30, 3, '       ditambahkan', 0, 0, 'L', 0);

    $pdf->Ln(3);
    $pdf->cell(9, 3, '', 0, 0, 'L', 0);
    $pdf->cell(59, 3, '', 0, 0, 'L', 0);
    $pdf->cell(34, 3, '', 0, 0, 'L', 0);
    $pdf->cell(30, 2, '', 0, 0, 'L', 0);
    $pdf->cell(30, 2, '', 0, 0, 'L', 0);
    $pdf->cell(30, 3, '     - Jatuh Tempo', 0, 0, 'L', 0);

    $pdf->Ln(10);

    if(count($datadtl) > 1){
        $pdf->cell(196, 3, '=== Lihat Lembar  Lampiran ===', 0, 0, 'C', 0);            
    }else{
        $pdf->setXY(10, 178);
        
        $pdf->SetFont('times', '', '6');
        $pdf->cell(8, 3, '1.', 0, 0, 'L', 0);          
        $pdf->MultiCell(60, 3, 
            formaths($datadtl[0]['NOHS'])."\n".
            $datadtl[0]['BRGURAI']."\n".$datadtl[0]['MERK']." ".$datadtl[0]['TIPE']." ".$datadtl[0]['SPFLAIN']."\n".
            (($datadtl[0]['FlBarangBaru'] == 1) ? 'BARANG BARU' : 'BARANG BUKAN BARU')."\n".
            ucfirst(strtolower($datadtl[0]['URBRGASAL'])).' ('.$datadtl[0]['BRGASAL'].')', 0, 'L', 0);

        // Fasilitas
        $pdf->setXY(78, 178);
        // $pdf->SetFont('times', '', '8');
        if ($datadtl[0]['KDFASDTL'] != ''){
            $pdf->MultiCell(34, 3, '- '.$datadtl[0]['URKDFASDTL'], 0, 'L', 0); //var
        } else {
            $pdf->MultiCell(34, 3, '- '.'Tanpa Fasilitas', 0, 'L', 0); //var
        }

        // Larts
    	$pdf->setXY(78, 184);
        if ($datadtl[0]['FLLARTAS'] == 'Y'){
            $pdf->MultiCell(34, 3, '- '.$datadtl[0]['URLARTAS'], 0, 'L', 0); // LAMPIRAN LARTAS
        } else {
            $pdf->MultiCell(34, 3, '- '.'Bukan Lartas', 0, 'L', 0); //var
        }

        // BM
        $FASBM = ($datadtl[0]['FASBM'] > 0) ? getfas($datadtl[0]['KDFASBM']) . ' : ' . ($datadtl[0]['FASBM']) . '%' : ' ';
        $BM = strip($datadtl[0]['TRPBM']).' '.$FASBM;
        $FASBMI = ($datadtl[0]['FasBMIM'] > 0) ? getfas($datadtl[0]['KdFasBMIM']) . ' : ' . ($datadtl[0]['FasBMIM'] ) . '%' : ' ';
        $BMI = strip($datadtl[0]['TrpBmIM']).' '.$FASBMI;
        $FASBMAD = ($datadtl[0]['FasBMAD'] > 0) ? getfas($datadtl[0]['KdFasBMAD']) . ' : ' . ($datadtl[0]['FasBMAD'] ) . '%' : ' ';
        $BMAD = strip($datadtl[0]['TrpBmAD']).' '.$FASBMAD;
        $FASBMP = ($datadtl[0]['FasBMPB'] > 0) ? getfas($datadtl[0]['KdFasBMPB']) . ' : ' . ($datadtl[0]['FasBMPB'] ) . '%' : ' ';
        $BMP = strip($datadtl[0]['TrpBmPB']).' '.$FASBMP;
        $FASBMTP = ($datadtl[0]['FasBMTP'] > 0) ? getfas($datadtl[0]['KdFasBMTP']) . ' : ' . ($datadtl[0]['FasBMTP'] ) . '%' : ' ';
        $BMTP = strip($datadtl[0]['TrpBmTP']).' '.$FASBMTP;
        $FasCukai = ($datadtl[0]['FASCUK'] > 0) ? getfas($datadtl[0]['KDFASCUK']) . ' : ' . ($datadtl[0]['FASCUK']) . '%' : ' ';
        $Cukai = strip($datadtl[0]['TRPCUK']).' '.$FasCukai;
        $FASPPN = ($datadtl[0]['FASPPN'] > 0) ? getfas($datadtl[0]['KDFASPPN']) . ' : ' . ($datadtl[0]['FASPPN']) . '%' : ' ';
        $PPN = strip($datadtl[0]['TRPPPN']).' '.$FASPPN;
        $FASPPNBM = ($datadtl[0]['FASPBM'] > 0) ? getfas($datadtl[0]['KDFASPBM']) . ' : ' . ($datadtl[0]['FASPBM']) . '%' : ' ';
        $PPNBM = strip($datadtl[0]['TRPPBM']).' '.$FASPPNBM;
        $FASPPH = ($datadtl[0]['FASPPH'] > 0) ? getfas($datadtl[0]['KDFASPPH']) . ' : ' . ($datadtl[0]['FASPPH']) . '%' : ' ';
        $PPH = strip($datadtl[0]['TRPPPH']).' '.$FASPPH;

    	$pdf->setXY(112, 177);
    	$pdf->SetFont('times', '', '6');
    	$pdf->cell(29, 3, 'BM : '.$BM, 0, 0, 'L', 0);
    	$pdf->setXY(112, 179);
    	$pdf->cell(29, 3, 'BMI : '.$BMI, 0, 0, 'L', 0);
    	$pdf->setXY(112, 181);
    	$pdf->cell(29, 3, 'BMAD : '.$BMAD, 0, 0, 'L', 0);
    	$pdf->setXY(112, 183);
    	$pdf->cell(29, 3, 'BMP : '.$BMP, 0, 0, 'L', 0);
    	$pdf->setXY(112, 185);
    	$pdf->cell(29, 3, 'BMTP : '.$BMTP, 0, 0, 'L', 0);
    	$pdf->setXY(112, 187);
    	$pdf->cell(29, 3, 'Cukai : '.$Cukai, 0, 0, 'L', 0);
    	$pdf->setXY(112, 189);
    	$pdf->cell(29, 3, 'PPN : '.$PPN, 0, 0, 'L', 0);
    	$pdf->setXY(112, 191);
    	$pdf->cell(29, 3, 'PPnBM : '.$PPNBM, 0, 0, 'L', 0);
    	$pdf->setXY(112, 193);
    	$pdf->cell(29, 3, 'PPh : '.$PPH, 0, 0, 'L', 0);

    	// Satuan dll
    	$pdf->setXY(142, 177);
    	$pdf->MultiCell(29, 3, number_format($datadtl[0]['JMLSAT'], 4, '.', ',').' '.ucfirst(strtolower($datadtl[0]['URKDSAT'])).' ('.$datadtl[0]['KDSAT'].")\n".number_format($datadtl[0]['NETTODTL'], 4, '.', ',').' Kg'."\n".$datadtl[0]['KEMASJM'].' '.ucfirst(strtolower($datadtl[0]['URKEMASJN'])).' ( '.$datadtl[0]['KEMASJN'].' )', 0, 'L', 0);

    	// Nilai Pabean Dll
    	$pdf->setXY(172, 177);
        $pdf->MultiCell(29, 3, number_format($datadtl[0]['DCIF'], 2, '.', ',')."\n".$datadtl[0]['JNSTRANS']."\n".number_format($datadtl[0]['NILVD'], 2, '.', ','), 0, 'R', 0);
    }

    $pdf->SetFont('times', '', '8');
    $pdf->setXY(136,183);
    $pdf->SetMargins(7.5,0);
    $pdf->Ln(14);
    $pdf->cell(30, 4, 'Jenis Pungutan', 1, 0, 'C', 0);
    $pdf->cell(26, 4, 'Dibayar', 1, 0, 'C', 0);
    $pdf->cell(35, 4, 'Ditanggung Pemerintah', 1, 0, 'C', 0);
    $pdf->cell(26, 4, 'Ditunda', 1, 0, 'C', 0);
    $pdf->cell(25, 4, 'Tidak Dipungut', 1, 0, 'C', 0);
    $pdf->cell(26, 4, 'Dibebaskan', 1, 0, 'C', 0);
    $pdf->cell(26, 4, 'Telah Dilunasi', 1, 0, 'C', 0);

    $pdf->Ln();
    $pdf->cell(10, 4, ' 37.', 1, 0, 'L', 0);
    $pdf->cell(20, 4, 'BM', 1, 0, 'L', 0);
    $pdf->cell(26, 4, number_format((float)$datapgt['data_pgt'][1][0] + (float)$datapgt['data_pgt'][1][3]), 1, 0, 'R', 0); //var
    $pdf->cell(35, 4, number_format($datapgt['data_pgt'][1][1]), 1, 0, 'R', 0); //var
    $pdf->cell(26, 4, number_format($datapgt['data_pgt'][1][2]), 1, 0, 'R', 0); //var
    $pdf->cell(25, 4, number_format($datapgt['data_pgt'][1][6]), 1, 0, 'R', 0); //var
    $pdf->cell(26, 4, number_format($datapgt['data_pgt'][1][4]), 1, 0, 'R', 0); //var
    $pdf->cell(26, 4, number_format($datapgt['data_pgt'][1][5]), 1, 0, 'R', 0); //var

    $pdf->Ln();
    $pdf->cell(10, 4, ' 38.', 1, 0, 'L', 0);
    $pdf->cell(20, 4, 'BM KITE', 1, 0, 'L', 0);
    $pdf->cell(26, 4, number_format((float)$datapgt['data_pgt'][8][0] + (float)$datapgt['data_pgt'][8][3]), 1, 0, 'R', 0); //var
    $pdf->cell(35, 4, number_format($datapgt['data_pgt'][8][1]), 1, 0, 'R', 0); //var
    $pdf->cell(26, 4, number_format($datapgt['data_pgt'][8][2]), 1, 0, 'R', 0); //var
    $pdf->cell(25, 4, number_format($datapgt['data_pgt'][8][6]), 1, 0, 'R', 0); //var
    $pdf->cell(26, 4, number_format($datapgt['data_pgt'][8][4]), 1, 0, 'R', 0); //var
    $pdf->cell(26, 4, number_format($datapgt['data_pgt'][8][5]), 1, 0, 'R', 0); //var

    $pdf->Ln();
    $pdf->cell(10, 4, ' 39.', 1, 0, 'L', 0);
    $pdf->cell(20, 4, 'BMT', 1, 0, 'L', 0);
    $pdf->cell(26, 4, number_format((float)$datapgt['data_pgt'][9][0] + (float)$datapgt['data_pgt'][10][0] + (float)$datapgt['data_pgt'][11][0] + (float)$datapgt['data_pgt'][12][0] + (float)$datapgt['data_pgt'][9][3] + (float)$datapgt['data_pgt'][10][3] + (float)$datapgt['data_pgt'][11][3] + (float)$datapgt['data_pgt'][12][3]), 1, 0, 'R', 0); //var
    $pdf->cell(35, 4, number_format((float)$datapgt['data_pgt'][9][1] + (float)$datapgt['data_pgt'][10][1] + (float)$datapgt['data_pgt'][11][1] + (float)$datapgt['data_pgt'][12][1] ), 1, 0, 'R', 0); //var
    $pdf->cell(26, 4, number_format((float)$datapgt['data_pgt'][9][2] + (float)$datapgt['data_pgt'][10][2] + (float)$datapgt['data_pgt'][11][2] + (float)$datapgt['data_pgt'][12][2] ), 1, 0, 'R', 0); //var
    $pdf->cell(25, 4, number_format((float)$datapgt['data_pgt'][9][6] + (float)$datapgt['data_pgt'][10][6] + (float)$datapgt['data_pgt'][11][6] + (float)$datapgt['data_pgt'][12][6] ), 1, 0, 'R', 0); //var
    $pdf->cell(26, 4, number_format((float)$datapgt['data_pgt'][9][4] + (float)$datapgt['data_pgt'][10][4] + (float)$datapgt['data_pgt'][11][4] + (float)$datapgt['data_pgt'][12][4] ), 1, 0, 'R', 0); //var
    $pdf->cell(26, 4, number_format((float)$datapgt['data_pgt'][9][5] + (float)$datapgt['data_pgt'][10][5] + (float)$datapgt['data_pgt'][11][5] + (float)$datapgt['data_pgt'][12][5] ), 1, 0, 'R', 0); //var

    $pdf->Ln();
    $pdf->cell(10, 4, ' 40.', 1, 0, 'L', 0);
    $pdf->cell(20, 4, 'Cukai', 1, 0, 'L', 0);
    $pdf->cell(26, 4, number_format((float)$datapgt['data_pgt'][5][0] + (float)$datapgt['data_pgt'][6][0] + (float)$datapgt['data_pgt'][7][0] + (float)$datapgt['data_pgt'][5][3] + (float)$datapgt['data_pgt'][6][3] + (float)$datapgt['data_pgt'][7][3]), 1, 0, 'R', 0); //var
    $pdf->cell(35, 4, number_format((float)$datapgt['data_pgt'][5][1] + (float)$datapgt['data_pgt'][6][1] + (float)$datapgt['data_pgt'][7][1]), 1, 0, 'R', 0); //var
    $pdf->cell(26, 4, number_format((float)$datapgt['data_pgt'][5][2] + (float)$datapgt['data_pgt'][6][2] + (float)$datapgt['data_pgt'][7][2]), 1, 0, 'R', 0); //var
    $pdf->cell(25, 4, number_format((float)$datapgt['data_pgt'][5][6] + (float)$datapgt['data_pgt'][6][6] + (float)$datapgt['data_pgt'][7][6]), 1, 0, 'R', 0); //var
    $pdf->cell(26, 4, number_format((float)$datapgt['data_pgt'][5][4] + (float)$datapgt['data_pgt'][6][4] + (float)$datapgt['data_pgt'][7][4]), 1, 0, 'R', 0); //var
    $pdf->cell(26, 4, number_format((float)$datapgt['data_pgt'][5][5] + (float)$datapgt['data_pgt'][6][5] + (float)$datapgt['data_pgt'][7][5]), 1, 0, 'R', 0); //var

    $pdf->Ln();
    $pdf->cell(10, 4, ' 41.', 1, 0, 'L', 0);
    $pdf->cell(20, 4, 'PPN', 1, 0, 'L', 0);
    $pdf->cell(26, 4, number_format((float)$datapgt['data_pgt'][2][0] + (float)$datapgt['data_pgt'][2][3]), 1, 0, 'R', 0); //var
    $pdf->cell(35, 4, number_format($datapgt['data_pgt'][2][1]), 1, 0, 'R', 0); //var
    $pdf->cell(26, 4, number_format($datapgt['data_pgt'][2][2]), 1, 0, 'R', 0); //var
    $pdf->cell(25, 4, number_format($datapgt['data_pgt'][2][6]), 1, 0, 'R', 0); //var
    $pdf->cell(26, 4, number_format($datapgt['data_pgt'][2][4]), 1, 0, 'R', 0); //var
    $pdf->cell(26, 4, number_format($datapgt['data_pgt'][2][5]), 1, 0, 'R', 0); //var

    $pdf->Ln();
    $pdf->cell(10, 4, ' 42.', 1, 0, 'L', 0);
    $pdf->cell(20, 4, 'PPnBM', 1, 0, 'L', 0);
    $pdf->cell(26, 4, number_format((float)$datapgt['data_pgt'][3][0] + (float)$datapgt['data_pgt'][3][3]), 1, 0, 'R', 0); //var
    $pdf->cell(35, 4, number_format($datapgt['data_pgt'][3][1]), 1, 0, 'R', 0); //var
    $pdf->cell(26, 4, number_format($datapgt['data_pgt'][3][2]), 1, 0, 'R', 0); //var
    $pdf->cell(25, 4, number_format($datapgt['data_pgt'][3][6]), 1, 0, 'R', 0); //var
    $pdf->cell(26, 4, number_format($datapgt['data_pgt'][3][4]), 1, 0, 'R', 0); //var
    $pdf->cell(26, 4, number_format($datapgt['data_pgt'][3][5]), 1, 0, 'R', 0); //var

    $pdf->Ln();
    $pdf->cell(10, 4, ' 43.', 1, 0, 'L', 0);
    $pdf->cell(20, 4, 'PPh', 1, 0, 'L', 0);
    $pdf->cell(26, 4, number_format((float)$datapgt['data_pgt'][4][0] + (float)$datapgt['data_pgt'][4][3]), 1, 0, 'R', 0); //var
    $pdf->cell(35, 4, number_format($datapgt['data_pgt'][4][1]), 1, 0, 'R', 0); //var
    $pdf->cell(26, 4, number_format($datapgt['data_pgt'][4][2]), 1, 0, 'R', 0); //var
    $pdf->cell(25, 4, number_format($datapgt['data_pgt'][4][6]), 1, 0, 'R', 0); //var
    $pdf->cell(26, 4, number_format($datapgt['data_pgt'][4][4]), 1, 0, 'R', 0); //var
    $pdf->cell(26, 4, number_format($datapgt['data_pgt'][4][5]), 1, 0, 'R', 0); //var
    
    $pdf->Ln();
    $pdf->cell(10, 4, ' 44.', 1, 0, 'L', 0);
    $pdf->cell(20, 4, 'TOTAL', 1, 0, 'L', 0);
    $pdf->cell(26, 4, number_format((float)$datapgt['data_pgt']['TOTAL'][0] + (float)$datapgt['data_pgt']['TOTAL'][3]), 1, 0, 'R', 0); //var
    $pdf->cell(35, 4, number_format($datapgt['data_pgt']['TOTAL'][1]), 1, 0, 'R', 0); //var
    $pdf->cell(26, 4, number_format($datapgt['data_pgt']['TOTAL'][2]), 1, 0, 'R', 0); //var
    $pdf->cell(25, 4, number_format($datapgt['data_pgt']['TOTAL'][6]), 1, 0, 'R', 0); //var
    $pdf->cell(26, 4, number_format($datapgt['data_pgt']['TOTAL'][4]), 1, 0, 'R', 0); //var
    $pdf->cell(26, 4, number_format($datapgt['data_pgt']['TOTAL'][5]), 1, 0, 'R', 0); //var

    $pdf->Ln(4);
    $pdf->cell(2, 4, '', 0, 0, 'L', 0);
    $pdf->cell(113, 4, 'F. Dengan ini saya menyatakan :', 0, 0, 'L', 0);
    $pdf->cell(79, 4, 'E. UNTUK PEMBAYARAN DAN JAMINAN', 0, 0, 'L', 0);

    $pdf->Ln(4);
    $pdf->cell(115, 4, '', 0, 0, 'L', 0);
    $pdf->cell(25, 4, 'a. Pembayaran', 0, 0, 'L', 0);
    $pdf->cell(4, 4, '', 1, 0, 'C', 0); //var
    $pdf->cell(10, 4, ' 1. Bank;    2. Pos;    3. Kantor Pabean.', 0, 0, 'L', 0);

    $pdf->Ln(4);
    $pdf->cell(115, 4, '', 0, 0, 'L', 0);
    $pdf->cell(25, 4, 'b. Jaminan', 0, 0, 'L', 0);
    $pdf->cell(4, 4, '', 1, 0, 'C', 0); //var
    $pdf->cell(10, 4, ' 1. Tunai;                  2. Bank Garansi;', 0, 0, 'L', 0);

    $pdf->Ln(4);
    $pdf->cell(144, 4, '', 0, 0, 'L', 0);
    $pdf->cell(10, 4, ' 3. Customs Bond;    4. Lainnya.', 0, 0, 'L', 0);

    $pdf->Ln(4);
    $pdf->cell(115, 4, '', 0, 0, 'L', 0);
    $pdf->cell(10, 4, '', 1, 0, 'L', 0);
    $pdf->cell(44, 4, 'N o m o r', 1, 0, 'C', 0); //var
    $pdf->cell(25, 4, 'Tanggal', 1, 0, 'C', 0);

    $pdf->Ln(4);
    $pdf->cell(115, 4, '', 0, 0, 'L', 0);
    $pdf->cell(10, 4, ' a.', 1, 0, 'L', 0);
    $pdf->cell(44, 4, '', 1, 0, 'C', 0); //var
    $pdf->cell(25, 4, '', 1, 0, 'C', 0); //var

    $pdf->Ln(4);
    $pdf->cell(115, 4, '', 0, 0, 'L', 0);
    $pdf->cell(10, 4, ' b.', 1, 0, 'L', 0);
    $pdf->cell(44, 4, '', 1, 0, 'C', 0); //var
    $pdf->cell(25, 4, '', 1, 0, 'C', 0); //var

    $pdf->text(14, 239, 'a. Bertanggung jawab atas kebenaran hal-hal yang diberitahukan dalam dokumen ini dan');
    $pdf->text(14, 243, '    keabsahan dokumen pelengkap pabean yang menjadi dasar pembuatan dokumen ini; dan');
    $pdf->text(14, 249, 'b. sanggup menyiapkan dan menyerahkan barang impor untuk diperiksa, serta menyaksikan');
    $pdf->text(14, 253, '    pemeriksaan fisik. Dalam hal saya tidak memenuhi ketentuan ini dalam jangka waktu');
    $pdf->text(14, 257, '    yang ditetapkan maka saya menguasakan kepada pengusaha Tempat Penimbunan');
    $pdf->text(14, 261, '    Sementara tempat pemeriksaan atas risiko dan biaya saya.');

    $pdf->Ln(6);
    // $pdf->cell(115, 4, ucfirst(strtolower($datahdr['KOTA_TTD_2'])).', '.strtotimecustom($datahdr['TANGGAL_TTD']), 0, 0, 'C', 0); //var
    $pdf->cell(115, 4, ucfirst(strtolower($datahdr['KOTA_TTD_2'])).', '.date('d-m-Y'), 0, 0, 'C', 0); //var
    $pdf->Ln(4);

    $jntrd = 'IMPORTIR';
    $pdf->cell(115, 4, $jntrd, 0, 0, 'C', 0);
    $pdf->Ln(7);
    $pdf->cell(115, 4, $datahdr['NAMA_TTD'], 0, 0, 'C', 0);

    $pdf->Ln(5);
    $pdf->cell(50, 4, 'Perdirjen BC No. PER-20/BC/2016', 0, 0, 'L', 0);
    $pdf->cell(94, 4, 'Rangkap ke-1/2/3/4 untuk Importir/Kantor Pabean/BPS/BI', 0, 0, 'C', 0);
    $pdf->SetFont('times', 'I', '8');
    $pdf->cell(32, 4, 'Tgl. Cetak '.date('d-m-y'), 0, 0, 'R', 0);
    $pdf->SetFont('times', '', '8');
    $pdf->cell(18, 4, 'Ver. 6.0.7', 0, 0, 'R', 0);
    

    // Kotak
    $pdf->Rect(7.5, 34, 97, 16, 1, 'F'); //Pengirim
    $pdf->Rect(104.5, 34, 97, 5, 1, 'F'); // G. No & Tgl Pendaftaran
    $pdf->Rect(89.5, 34, 15, 4, 1, 'F'); // Negara Pengirim
    $pdf->Rect(159, 34, 20, 4, 1, 'F'); // 1. No Pendaftaran
    $pdf->Rect(181.5, 34, 20, 4, 1, 'F'); // Tgl Pendaftaran
    $pdf->Rect(104.5, 39, 97, 4, 1, 'F'); // 9. Cara Pengangkutan
    $pdf->Rect(186.5, 39, 15, 8, 1, 'F'); 
    $pdf->Rect(104.5, 43, 97, 9, 1, 'F'); // 10. Sarana Pengangkut
    $pdf->Rect(7.5, 50, 97, 19, 1, 'F'); //Penjual
    $pdf->Rect(89.5, 50, 15, 4, 1, 'F'); // Negara Penjual
    $pdf->Rect(104.5, 52, 97, 4, 1, 'F'); // 11. Perkiraan tgl tiba
    $pdf->Rect(104.5, 56, 97, 13, 1, 'F'); // 12. Pelabuhan
    $pdf->Rect(186.5, 56, 15, 13, 1, 'F'); 
    $pdf->Rect(186.5, 60.5, 15, 4, 1, 'F'); 
    $pdf->Rect(126, 73.5, 8, 3.5, 1, 'F'); 
    $pdf->Rect(7.5, 69, 97, 25, 1, 'F'); //Importir
    $pdf->Rect(104.5, 69, 97, 25, 1, 'F'); //Invoice
    $pdf->Rect(7.5, 94, 97, 26, 1, 'F'); //Pemilik Barang
    $pdf->Rect(104.5, 94, 97, 17, 1, 'F'); //Pemenuhan Fasilitas
    $pdf->Rect(186.5, 94, 15, 4, 1, 'F'); 
    $pdf->Rect(104.5, 111, 97, 9, 1, 'F'); //Tempat Penimbunan
    $pdf->Rect(186.5, 111, 15, 4, 1, 'F'); 
    $pdf->Rect(7.5, 120, 97, 21, 1, 'F'); //PPJK
    $pdf->Rect(104.5, 120, 48.5, 21, 1, 'F'); //Valuta
    $pdf->Rect(138, 120, 15, 4, 1, 'F'); 
    $pdf->Rect(153, 120, 48.5, 21, 1, 'F'); //NDPBM
    $pdf->Rect(186.5, 128.5, 15, 4, 1, 'F'); 
    $pdf->Rect(104.5, 120, 97, 8.5, 1, 'F');
    $pdf->Rect(7.5, 141, 80, 19, 1, 'F'); //27.
    $pdf->Rect(87.5, 141, 44, 19, 1, 'F'); //28.
    $pdf->Rect(131.5, 141, 35, 19, 1, 'F'); //29.
    $pdf->Rect(166.5, 141, 35, 19, 1, 'F'); //30.
    $pdf->Rect(7.5, 160, 10, 17, 1, 'F'); //31.
    $pdf->Rect(17.5, 160, 60, 17, 1, 'F'); //32.
    $pdf->Rect(77.5, 160, 34, 17, 1, 'F'); //33.
    $pdf->Rect(111.5, 160, 30, 17, 1, 'F'); //34.
    $pdf->Rect(141.5, 160, 30, 17, 1, 'F'); //35.
    $pdf->Rect(171.5, 160, 30, 17, 1, 'F'); //36.
    $pdf->Rect(7.5, 177, 10, 20, 1, 'F'); //31. -
    $pdf->Rect(17.5, 177, 60, 20, 1, 'F'); //32. -
    $pdf->Rect(77.5, 177, 34, 20, 1, 'F'); //33. -
    $pdf->Rect(111.5, 177, 30, 20, 1, 'F'); //34. -
    $pdf->Rect(141.5, 177, 30, 20, 1, 'F'); //35. -
    $pdf->Rect(171.5, 177, 30, 20, 1, 'F'); //36. -
    $pdf->Rect(7.5, 233, 115, 45, 1, 'F'); //F. 
    $pdf->Rect(122.5, 233, 79, 45, 1, 'F'); //E.

    if(count($datadtl) > 1){
        $lampiran['detil'] = '1';
    }

    if(count($datadok) != '0' && $datadok != ""){
        $lampiran['dokumen'] = '1';
    }

    if(count($datakms) > 1 && $datakms != ""){
        $lampiran['kemasan'] = '1';
    }

    if(count($datacon) > 0 && $datacon != ""){
        $lampiran['kontainer'] = '1';
    }

    for ($x=0; $x<count($datacuk); $x++){
        if($datacuk[$x]['NILAITRF'] > 0){
            $lampiran['cukai'] = '1';    
        }
    }

    $idPrint = 0;
    
    if ($lampiran['detil'] == '1') {
        headerlampiranpib($pdf, $datahdr, 'barang', $idPrint);
        lampiranbarangpib($pdf, $datahdr, $datadtl, $idPrint);
    }

    
    if ($lampiran['dokumen'] == '1') {
        headerlampiranpib($pdf, $datahdr, 'dokumen', $idPrint);
        lampirandokumenpib($pdf, $datahdr, $datadok, $idPrint);
    }

    if ($lampiran['kemasan'] == '1') {
        headerlampiranpib($pdf, $datahdr, 'kemasan', $idPrint);
        lampirankemasanpib($pdf, $datahdr, $datakms, $idPrint);
    }
    
    if ($lampiran['kontainer'] == '1') {
        headerlampiranpib($pdf, $datahdr, 'kontainer', $idPrint);
        lampirankontainerpib($pdf, $datahdr, $datacon, $idPrint);
    }

    if ($lampiran['cukai'] == '1') {
        headerlampiranpib($pdf, $datahdr, 'cukai', $idPrint);
        lampirancukaipib($pdf, $datahdr, $datacuk, $idPrint);
    }

}

function headerlampiranpib($pdf, $datahdr, $tipe, $idPrint) {
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(0, 0);
    $pdf->SetY(5.5);
    
    switch ($tipe) {
        case 'barang':
            $pdf->SetFont('times', 'B', '12');
            $pdf->cell(190, 4, 'LEMBAR LANJUTAN', 0, 0, 'C', 0);        

            $pdf->Ln();
            $pdf->cell(190, 4, 'PEMBERITAHUAN IMPOR BARANG (PIB)', 0, 0, 'C', 0);        
            $pdf->SetFont('times', '', '8');
            $pdf->text(193, 13, 'BC 2.0');
            break;
        case 'dokumen':
            $pdf->SetFont('times', 'B', '12');
            $pdf->cell(190, 4, 'LEMBAR LAMPIRAN DOKUMEN', 0, 0, 'C', 0);        

            $pdf->Ln();
            $pdf->cell(190, 4, 'PEMBERITAHUAN IMPOR BARANG (PIB)', 0, 0, 'C', 0);        
            $pdf->SetFont('times', '', '8');
            $pdf->text(193, 13, 'BC 2.0');
            break;
        case 'kemasan':
            $pdf->SetFont('times', 'B', '12');
            $pdf->cell(190, 4, 'LEMBAR LAMPIRAN KEMASAN', 0, 0, 'C', 0);        

            $pdf->Ln();
            $pdf->cell(190, 4, 'PEMBERITAHUAN IMPOR BARANG (PIB)', 0, 0, 'C', 0);        
            $pdf->SetFont('times', '', '8');
            $pdf->text(193, 13, 'BC 2.0');
            break;
        case 'kontainer':
            $pdf->SetFont('times', 'B', '12');
            $pdf->cell(190, 4, 'LEMBAR LAMPIRAN KONTAINER', 0, 0, 'C', 0);        

            $pdf->Ln();
            $pdf->cell(190, 4, 'PEMBERITAHUAN IMPOR BARANG (PIB)', 0, 0, 'C', 0);        
            $pdf->SetFont('times', '', '8');
            $pdf->text(193, 13, 'BC 2.0');
            break;
        case 'cukai':
            $pdf->SetFont('times', 'B', '12');
            $pdf->cell(190, 4, 'LEMBAR LANJUTAN PELUNASAN CUKAI', 0, 0, 'C', 0);        

            $pdf->Ln();
            $pdf->cell(190, 4, 'PEMBERITAHUAN IMPOR BARANG (PIB)', 0, 0, 'C', 0);        
            $pdf->SetFont('times', '', '8');
            $pdf->text(193, 13, 'BC 2.0');
            break;
        
        default:
            # code...
            break;
    }

    if($tipe == 'kemasan'){
        $pdf->Rect(7.5, 16.5, 194, 20, 1, 'F');
    } else {
        $pdf->Rect(7.5, 16.5, 194, 14, 1, 'F');    
    }
    

    $pdf->Ln(7);
    $pdf->cell(45, 5, 'Kantor Pabean', 0, 0, 'L', 0);
    $pdf->cell(2, 5, ':', 0, 0, 'L', 0);
    $pdf->cell(75, 5, $datahdr['URKDKPBC'], 0, 0, 'L', 0); //var
    $pdf->cell(15, 4, $datahdr['KDKPBC'], 1, 0, 'C', 0);
    $pdf->cell(30, 5, '', 0, 0, 'L', 0);
    $pdf->cell(2, 5, 'Halaman '.$pdf->PageNo().' dari '.'{totalPages}', 0, 0, 'L', 0); //var

    $pdf->Ln(3.5);
    $pdf->cell(45, 6, 'Nomor Pengajuan', 0, 0, 'L', 0);
    $pdf->cell(2, 6, ':', 0, 0, 'L', 0);
    $pdf->cell(75, 6, formatcar($datahdr['CAR']), 0, 0, 'L', 0); //var
    if($tipe != 'kemasan'){
        $pdf->cell(27, 6, 'Tanggal Pengajuan :', 0, 0, 'L', 0);
        $pdf->cell(30, 6, strtotimecustom(($datahdr['PIBTG'])), 0, 0, 'L', 0); //Tgl. Pengajuan
    }
    
    $pdf->Ln(4);
    $pdf->cell(45, 6, 'Nomor Pendaftaran', 0, 0, 'L', 0);
    $pdf->cell(2, 6, ':', 0, 0, 'L', 0);
    $pdf->cell(75, 6, $datahdr['PIBNO'], 0, 0, 'L', 0); //var
    if($tipe != 'kemasan'){
        $pdf->cell(27, 6, 'Tanggal Pendaftaran :', 0, 0, 'L', 0);
        $pdf->cell(30, 6, strtotimecustom(($datahdr['PIBTG'])), 0, 0, 'L', 0); //var
    }

    switch ($tipe) {
        case 'barang':
            $pdf->Ln(8);

            $pdf->cell(2, 3, '', 0, 0, 'L', 0);
            $pdf->cell(8.5, 3, '31.', 0, 0, 'L', 0);
            $pdf->cell(59, 3, '32. - Pos Tarif/HS', 0, 0, 'L', 0);
            $pdf->cell(34, 3, '33. - Keterangan', 0, 0, 'L', 0);
            $pdf->cell(33, 2, '34. - Tarif & Fasilitas', 0, 0, 'L', 0);
            $pdf->cell(29, 2, '35. - Jumlah &', 0, 0, 'L', 0);
            $pdf->cell(28, 3, '36. - Nilai Pabean', 0, 0, 'L', 0);

            $pdf->Ln(3);
            $pdf->cell(2, 3, '', 0, 0, 'L', 0);
            $pdf->cell(9, 3, 'No.', 0, 0, 'L', 0);
            $pdf->cell(59, 3, '     - Uraian Jenis Barang, Merk, Tipe, Spesifikasi', 0, 0, 'L', 0);
            $pdf->cell(34, 3, '     - Fasilitas & No. Urut', 0, 0, 'L', 0);
            $pdf->cell(33, 2, '     ', 0, 0, 'L', 0);
            $pdf->cell(29, 2, '     - Jenis Satuan,', 0, 0, 'L', 0);
            $pdf->cell(28, 3, '     - Jenis', 0, 0, 'L', 0);

            $pdf->Ln(3);
            $pdf->cell(2, 3, '', 0, 0, 'L', 0);
            $pdf->cell(9, 3, '', 0, 0, 'L', 0);
            $pdf->cell(59, 3, '       Wajib', 0, 0, 'L', 0);
            $pdf->cell(34, 3, '     - Persyaratan & No. Urut', 0, 0, 'L', 0);
            $pdf->cell(33, 2, '     ', 0, 0, 'L', 0);
            $pdf->cell(29, 2, '     - Berat Bersih (kg)', 0, 0, 'L', 0);
            $pdf->cell(28, 3, '     - Nilai yang', 0, 0, 'L', 0);

            $pdf->Ln(3);
            $pdf->cell(2, 3, '', 0, 0, 'L', 0);
            $pdf->cell(9, 3, '', 0, 0, 'L', 0);
            $pdf->cell(59, 3, '     - Negara Asal Barang', 0, 0, 'L', 0);
            $pdf->cell(34, 3, '', 0, 0, 'L', 0);
            $pdf->cell(33, 2, '     ', 0, 0, 'L', 0);
            $pdf->cell(29, 2, '     - Jml/Jns Kemasan', 0, 0, 'L', 0);
            $pdf->cell(28, 3, '       ditambahkan', 0, 0, 'L', 0);

            $pdf->Ln(3);
            $pdf->cell(2, 3, '', 0, 0, 'L', 0);
            $pdf->cell(9, 3, '', 0, 0, 'L', 0);
            $pdf->cell(59, 3, '', 0, 0, 'L', 0);
            $pdf->cell(34, 3, '', 0, 0, 'L', 0);
            $pdf->cell(33, 2, '', 0, 0, 'L', 0);
            $pdf->cell(29, 2, '', 0, 0, 'L', 0);
            $pdf->cell(28, 3, '     - Jatuh Tempo', 0, 0, 'L', 0);

            $pdf->Rect(7.5, 30.5, 10, 17, 1, 'F'); //31.
            $pdf->Rect(17.5, 30.5, 60, 17, 1, 'F'); //32.
            $pdf->Rect(77.5, 30.5, 34, 17, 1, 'F'); //33.
            $pdf->Rect(111.5, 30.5, 33, 17, 1, 'F'); //34.
            $pdf->Rect(144.5, 30.5, 29, 17, 1, 'F'); //35.
            $pdf->Rect(173.5, 30.5, 28, 17, 1, 'F'); //36.
            footerlampiran($pdf, $datahdr);
            break;
        case 'dokumen':
            $pdf->Ln(6.5);
            $pdf->cell(10, 4, 'No.', 1, 0, 'C', 0);
            $pdf->cell(20, 4, 'Kode Dokumen', 1, 0, 'C', 0);
            $pdf->cell(60, 4, 'Nama Dokumen', 1, 0, 'C', 0);
            $pdf->cell(80, 4, 'Nomor dan Tanggal Dokumen', 1, 0, 'C', 0);
            $pdf->cell(24, 4, 'Dilampirkan', 1, 0, 'C', 0);
            footerlampiran($pdf, $datahdr);
            break;
        case 'kemasan':
            $pdf->Ln(8);
            $pdf->cell(30, 4, 'Jumlah', 0, 0, 'R', 0);
            $pdf->cell(10, 4, '', 0, 0, 'R', 0);
            $pdf->cell(70, 4, 'Jenis Kemasan', 0, 0, 'L', 0);
            $pdf->cell(104, 4, 'Merk Kemasan', 0, 0, 'L', 0);
            footerlampiran($pdf, $datahdr);
            break;
        case 'kontainer':
            $pdf->Ln(6.5);
            $pdf->cell(14, 4, 'No. Urut', 1, 0, 'C', 0);
            $pdf->cell(43, 4, 'Nomor Kontainer', 1, 0, 'C', 0);
            $pdf->cell(19, 4, 'Ukuran', 1, 0, 'C', 0);
            $pdf->cell(21, 4, 'Tipe', 1, 0, 'C', 0);
            $pdf->cell(14, 4, 'No. Urut', 1, 0, 'C', 0);
            $pdf->cell(43, 4, 'Nomor Kontainer', 1, 0, 'C', 0);
            $pdf->cell(19, 4, 'Ukuran', 1, 0, 'C', 0);
            $pdf->cell(21, 4, 'Tipe', 1, 0, 'C', 0);
            footerlampiran($pdf, $datahdr);
            break;
        case 'cukai':
            $pdf->Ln(8);

            $pdf->cell(2, 3, '', 0, 0, 'L', 0);
            $pdf->cell(8.5, 3, '31.', 0, 0, 'L', 0);
            $pdf->cell(59, 3, '32a. Spesifikasi Wajib BKC', 0, 0, 'L', 0);
            $pdf->cell(34, 3, '34a. Pungutan Cukai dan', 0, 0, 'L', 0);
            $pdf->cell(33, 2, '35a. Penjualan Eceran', 0, 0, 'L', 0);
            $pdf->cell(57, 2, '35b. Pita Cukai', 0, 0, 'L', 0);

            $pdf->Ln(3);
            $pdf->cell(2, 3, '', 0, 0, 'L', 0);
            $pdf->cell(9, 3, 'No.', 0, 0, 'L', 0);
            $pdf->cell(59, 3, '     - Komoditi BKC', 0, 0, 'L', 0);
            $pdf->cell(34, 3, '     - Jenis Tarif Cukai', 0, 0, 'L', 0);
            $pdf->cell(33, 2, '     - Harga Jual Eceran', 0, 0, 'L', 0);
            $pdf->cell(57, 2, '     - Saldo Awal,', 0, 0, 'L', 0);

            $pdf->Ln(3);
            $pdf->cell(2, 3, '', 0, 0, 'L', 0);
            $pdf->cell(9, 3, '', 0, 0, 'L', 0);
            $pdf->cell(59, 3, '     - Subkomoditi BKC', 0, 0, 'L', 0);
            $pdf->cell(34, 3, '     - Besar Tarif Cukai', 0, 0, 'L', 0);
            $pdf->cell(33, 2, '     - Kemasan Penjualan', 0, 0, 'L', 0);
            $pdf->cell(57, 2, '     - Jumlah Dilekatkan', 0, 0, 'L', 0);

            $pdf->Ln(3);
            $pdf->cell(2, 3, '', 0, 0, 'L', 0);
            $pdf->cell(9, 3, '', 0, 0, 'L', 0);
            $pdf->cell(59, 3, '     - Merek BKC', 0, 0, 'L', 0);
            $pdf->cell(34, 3, '     - Nilai Cukai', 0, 0, 'L', 0);
            $pdf->cell(33, 2, '       Eceran', 0, 0, 'L', 0);
            $pdf->cell(57, 2, '     - Saldo Akhir', 0, 0, 'L', 0);

            $pdf->Ln(3);
            $pdf->cell(2, 3, '', 0, 0, 'L', 0);
            $pdf->cell(9, 3, '', 0, 0, 'L', 0);
            $pdf->cell(59, 3, '', 0, 0, 'L', 0);
            $pdf->cell(34, 3, '', 0, 0, 'L', 0);
            $pdf->cell(33, 2, '     - Isi Per Kemasan', 0, 0, 'L', 0);
            $pdf->cell(57, 2, '', 0, 0, 'L', 0);

            $pdf->Rect(7.5, 30.5, 10, 17, 1, 'F'); //31.
            $pdf->Rect(17.5, 30.5, 60, 17, 1, 'F'); //32.
            $pdf->Rect(77.5, 30.5, 34, 17, 1, 'F'); //33.
            $pdf->Rect(111.5, 30.5, 33, 17, 1, 'F'); //34.
            $pdf->Rect(144.5, 30.5, 57, 17, 1, 'F'); //35.
            footerlampiran($pdf, $datahdr);
            break;
        default:
            $pdf->cell(194, 4, '', 1, 0, 'C', 0);
            break;
    }

}

function lampiranbarangpib($pdf, $datahdr, $datadtl, $idPrint) {
    $pdf->setY(50);
    $idPrint = $idPrint;
    $Yawl = $pdf->getY();
    for($x=$idPrint; $x < count($datadtl); $x++)
    {
        ($datadtl[$x]['FlBarangBaru'] == 1) ? $datadtl[$x]['FlBarangBaru'] = "BARANG BARU" : $datadtl[$x]['FlBarangBaru'] = "BARANG BUKAN BARU";
        $h = [];
        $pdf->setY($Yawl);
        $pdf->cell(10, 3, ($x+1), 0, 0, 'C', 0);
        $h[] = $pdf->getY();
        //HS
        $pdf->MultiCell(60, 3, formaths($datadtl[$x]['NOHS'])."\n".$datadtl[$x]['BRGURAI']."\n".$datadtl[$x]['MERK']."\n".$datadtl[$x]['TIPE']."\n".$datadtl[$x]['SPFLAIN']."\n".$datadtl[$x]['FlBarangBaru']."\n".ucfirst(strtolower($datadtl[$x]['URBRGASAL'])).' ('.$datadtl[$x]['BRGASAL'].')', 0, 'L', 0);
        $h[] = $pdf->getY();
        //Fasilitas dan lartas
        $pdf->setY($Yawl);
        $pdf->setX($pdf->getX() + 70);
        if ($datadtl[$x]['KDFASDTL'] != ''){
            $pdf->MultiCell(34, 3, '- '.$datadtl[$x]['URKDFASDTL'], 0, 'L', 0); //var
        } else {
            $pdf->MultiCell(34, 3, '- '.'Tanpa Fasilitas', 0, 'L', 0); //var
        }
        //Lartas
        $pdf->setX($pdf->getX() + 70);
        if ($datadtl[$x]['FLLARTAS'] == 'Y'){
            $pdf->MultiCell(34, 3, '- '.$datadtl[$x]['URLARTAS'], 0, 'L', 0); // LAMPIRAN LARTAS
        } else {
            $pdf->MultiCell(34, 3, '- '.'Bukan Lartas', 0, 'L', 0); //var
        }
        $h[] = $pdf->getY();

        //BM
        $FASBM = ($datadtl[$x]['FASBM'] > 0) ? getfas($datadtl[$x]['KDFASBM']) . ' : ' . ($datadtl[$x]['FASBM']) . '%' : ' ';
        $BM = strip($datadtl[$x]['TRPBM']).' '.$FASBM;
        $FASBMI = ($datadtl[$x]['FasBMIM'] > 0) ? getfas($datadtl[$x]['KdFasBMIM']) . ' : ' . ($datadtl[$x]['FasBMIM'] ) . '%' : ' ';
        $BMI = strip($datadtl[$x]['TrpBmIM']).' '.$FASBMI;
        $FASBMAD = ($datadtl[$x]['FasBMAD'] > 0) ? getfas($datadtl[$x]['KdFasBMAD']) . ' : ' . ($datadtl[$x]['FasBMAD'] ) . '%' : ' ';
        $BMAD = strip($datadtl[$x]['TrpBmAD']).' '.$FASBMAD;
        $FASBMP = ($datadtl[$x]['FasBMPB'] > 0) ? getfas($datadtl[$x]['KdFasBMPB']) . ' : ' . ($datadtl[$x]['FasBMPB'] ) . '%' : ' ';
        $BMP = strip($datadtl[$x]['TrpBmPB']).' '.$FASBMP;
        $FASBMTP = ($datadtl[$x]['FasBMTP'] > 0) ? getfas($datadtl[$x]['KdFasBMTP']) . ' : ' . ($datadtl[$x]['FasBMTP'] ) . '%' : ' ';
        $BMTP = strip($datadtl[$x]['TrpBmTP']).' '.$FASBMTP;
        $FasCukai = ($datadtl[$x]['FASCUK'] > 0) ? getfas($datadtl[$x]['KDFASCUK']) . ' : ' . ($datadtl[$x]['FASCUK']) . '%' : ' ';
        $Cukai = strip($datadtl[$x]['TRPCUK']).' '.$FasCukai;
        $FASPPN = ($datadtl[$x]['FASPPN'] > 0) ? getfas($datadtl[$x]['KDFASPPN']) . ' : ' . ($datadtl[$x]['FASPPN']) . '%' : ' ';
        $PPN = strip($datadtl[$x]['TRPPPN']).' '.$FASPPN;
        $FASPPNBM = ($datadtl[$x]['FASPBM'] > 0) ? getfas($datadtl[$x]['KDFASPBM']) . ' : ' . ($datadtl[$x]['FASPBM']) . '%' : ' ';
        $PPNBM = strip($datadtl[$x]['TRPPBM']).' '.$FASPPNBM;
        $FASPPH = ($datadtl[$x]['FASPPH'] > 0) ? getfas($datadtl[$x]['KDFASPPH']) . ' : ' . ($datadtl[$x]['FASPPH']) . '%' : ' ';
        $PPH = strip($datadtl[$x]['TRPPPH']).' '.$FASPPH;

        $pdf->setY($Yawl);
        $pdf->setX($pdf->getX() + 104);
        $pdf->MultiCell(33, 3,'BM : '.$BM."\nBMI : ".$BMI."\nBMAD : ".$BMAD."\nBMP : ".$BMP."\nBMTP : ".$BMTP."\nCukai : ".$Cukai."\nPPN : ".$PPN."\nPPnBM : ".$PPNBM."\nPPh : ".$PPH, 0, 'L', 0); // BM
        $h[] = $pdf->getY();

        $pdf->setY($Yawl);
        $pdf->setX($pdf->getX() + 137);
        $pdf->MultiCell(29, 3, number_format($datadtl[$x]['JMLSAT'], 4, '.', ',').' '.ucfirst(strtolower($datadtl[$x]['URKDSAT'])).' ('.$datadtl[$x]['KDSAT'].")\n".number_format($datadtl[$x]['NETTODTL'], 4, '.', ',').' Kg'."\n".$datadtl[$x]['KEMASJM'].' '.ucfirst(strtolower($datadtl[$x]['URKEMASJN'])).' ( '.$datadtl[$x]['KEMASJN'].' )', 0, 'L', 0); //jumlah
        $h[] = $pdf->getY();

        $pdf->setY($Yawl);
        $pdf->setX($pdf->getX() + 166);
        $pdf->MultiCell(28, 3, number_format($datadtl[$x]['DCIF'], 2, '.', ',')."\n".$datadtl[$x]['JNSTRANS']."\n".number_format($datadtl[$x]['NILVD'], 2, '.', ','), 0, 'R', 0); // belum lengkap
        $h[] = $pdf->getY();

        $Yawl = max($h) + 5;
        if ($Yawl > 250){
            $x++;
            break;
        }    
    }

    $pdf->Rect(7.5, 47.5, 10, 222, 1, 'F'); //31.
    $pdf->Rect(17.5, 47.5, 60, 222, 1, 'F'); //32.
    $pdf->Rect(77.5, 47.5, 34, 222, 1, 'F'); //33.
    $pdf->Rect(111.5, 47.5, 33, 222, 1, 'F'); //34.
    $pdf->Rect(144.5, 47.5, 29, 222, 1, 'F'); //35.
    $pdf->Rect(173.5, 47.5, 28, 222, 1, 'F'); //36.
    //dd($x);
    if ($x < count($datadtl)) {
        headerlampiranpib($pdf, $datahdr, 'barang', $x);
        lampiranbarangpib($pdf, $datahdr, $datadtl, $x);
    }
}

function lampirandokumenpib($pdf, $datahdr, $datadok, $idPrint){
    $pdf->setY(37);
    $idPrint = $idPrint;
    $Yawl = $pdf->getY();
    for($x=$idPrint; $x < count($datadok); $x++){
        $h = [];
        $pdf->setY($Yawl);
        $pdf->cell(10, 3, ($x+1), 0, 0, 'C', 0);
        $h[] = $pdf->getY();

        $pdf->MultiCell(20, 3, $datadok[$x]['DOKKD'], 0, 'C', 0);
        $h[] = $pdf->getY();

        $pdf->setY($Yawl);
        $pdf->setX($pdf->getX() + 30);
        $pdf->MultiCell(60, 3, $datadok[$x]['URDOKKD'], 0, 'L', 0); // BM
        $h[] = $pdf->getY();

        $pdf->setY($Yawl);
        $pdf->setX($pdf->getX() + 95);
        $pdf->MultiCell(30, 3, $datadok[$x]['DOKNO'], 0, 'L', 0); // BM
        $h[] = $pdf->getY();

        $pdf->setY($Yawl);
        $pdf->setX($pdf->getX() + 120);
        $pdf->MultiCell(45, 3, $datadok[$x]['DOKTG'], 0, 'R', 0); // BM
        $h[] = $pdf->getY();

        $pdf->setY($Yawl);
        $pdf->setX($pdf->getX() + 170);
        $pdf->MultiCell(24, 3, 'Ya / Tidak', 0, 'C', 0); // BM
        $h[] = $pdf->getY();

        $Yawl = max($h) + 3;
        if ($Yawl > 245){
            $x++;
            break;
        }

    }

    $pdf->Rect(7.5, 34.5, 10, 225, 1, 'F'); //31.
    $pdf->Rect(17.5, 34.5, 20, 225, 1, 'F'); //32.
    $pdf->Rect(37.5, 34.5, 60, 225, 1, 'F'); //33.
    $pdf->Rect(97.5, 34.5, 80, 225, 1, 'F'); //34.
    $pdf->Rect(177.5, 34.5, 24, 225, 1, 'F'); //35.
    
    if ($x < count($datadok)) {
        headerlampiranpib($pdf, $datahdr, 'dokumen', $x);
        lampirandokumenpib($pdf, $datahdr, $datadok, $x);
    }
}

function lampirankemasanpib($pdf, $datahdr, $datakms, $idPrint){
    $pdf->setY(37);
    $idPrint = $idPrint;
    $Yawl = $pdf->getY();
    for($x=$idPrint; $x < count($datakms); $x++){
        $h = [];
        $pdf->setY($Yawl);
        $pdf->cell(30, 3, $datakms[$x]['JMKEMAS'], 0, 0, 'R', 0);
        $pdf->cell(10, 3, '', 0, 0, 'R', 0);
        $h[] = $pdf->getY();

        $pdf->MultiCell(70, 3, $datakms[$x]['JNKEMAS'].'      /  '.ucfirst(strtolower($datakms[$x]['URJNKEMAS'])), 0, 'L', 0);
        $h[] = $pdf->getY();

        $pdf->setY($Yawl);
        $pdf->setX($pdf->getX() + 110);
        $pdf->MultiCell(80, 3, $datakms[$x]['MERKKEMAS'], 0, 'L', 0); // BM
        $h[] = $pdf->getY();

        $Yawl = max($h) + 3;
        if ($Yawl > 245){
            $x++;
            break;
        }

    }

    $pdf->Rect(7.5, 25.5, 194, 225, 1, 'F'); //31.
    //$pdf->Rect(7.5, 34.5, 20, 225, 1, 'F'); //31.
    //$pdf->Rect(27.5, 34.5, 70, 225, 1, 'F'); //32.
    //$pdf->Rect(97.5, 34.5, 104, 225, 1, 'F'); //33.
    
    if ($x < count($datakms)) {
        headerlampiranpib($pdf, $datahdr, 'kemasan', $x);
        lampirankemasanpib($pdf, $datahdr, $datakms, $x);
    }
}

function lampirankontainerpib($pdf, $datahdr, $datacon, $idPrint){
    $pdf->setY(37);
    $idPrint = $idPrint;
    $Yawl = $pdf->getY();
    for($x=$idPrint; $x < count($datacon); $x++){
        $h = [];
        $pdf->setY($Yawl);
        $pdf->cell(14, 3, ($x+1), 0, 0, 'C', 0);
        $h[] = $pdf->getY();

        $pdf->MultiCell(43, 3, setnocont($datacon[$x]['CONTNO']), 0, 'L', 0);
        $h[] = $pdf->getY();

        $pdf->setY($Yawl);
        $pdf->setX($pdf->getX() + 57);
        $pdf->MultiCell(19, 3, $datacon[$x]['CONTUKUR'], 0, 'L', 0); // BM
        $h[] = $pdf->getY();

        $pdf->setY($Yawl);
        $pdf->setX($pdf->getX() + 76);
        $pdf->MultiCell(21, 3, $datacon[$x]['CONTTIPE'], 0, 'L', 0); // BM
        $h[] = $pdf->getY();
    
        $x++;

        $pdf->setY($Yawl);
        $pdf->setX($pdf->getX() + 97);
        $pdf->MultiCell(14, 3, ($datacon[$x]['CONTNO']) ? ($x+1) : '', 0, 'C', 0); // BM
        $h[] = $pdf->getY();

        $pdf->setY($Yawl);
        $pdf->setX($pdf->getX() + 111);
        $pdf->MultiCell(43, 3, setnocont($datacon[$x]['CONTNO']), 0, 'L', 0); // BM
        $h[] = $pdf->getY();

        $pdf->setY($Yawl);
        $pdf->setX($pdf->getX() + 154);
        $pdf->MultiCell(19, 3, $datacon[$x]['CONTUKUR'], 0, 'L', 0); // BM
        $h[] = $pdf->getY();

        $pdf->setY($Yawl);
        $pdf->setX($pdf->getX() + 173);
        $pdf->MultiCell(21, 3, $datacon[$x]['CONTTIPE'], 0, 'L', 0); // BM
        $h[] = $pdf->getY();
        
        $Yawl = max($h) + 3;
        if ($Yawl > 245){
            $x++;
            break;
        }

    }

    $pdf->Rect(7.5, 34.5, 14, 225, 1, 'F'); 
    $pdf->Rect(21.5, 34.5, 43, 225, 1, 'F'); 
    $pdf->Rect(64.5, 34.5, 19, 225, 1, 'F'); 
    $pdf->Rect(83.5, 34.5, 21, 225, 1, 'F'); 
    $pdf->Rect(104.5, 34.5, 14, 225, 1, 'F'); 
    $pdf->Rect(118.5, 34.5, 43, 225, 1, 'F'); 
    $pdf->Rect(161.5, 34.5, 19, 225, 1, 'F'); 
    $pdf->Rect(180.5, 34.5, 21, 225, 1, 'F'); 
    
    if ($x < count($datacon)) {
        headerlampiranpib($pdf, $datahdr, 'kontainer', $x);
        lampirankontainerpib($pdf, $datahdr, $datacon, $x);
    }
}

function lampirancukaipib($pdf, $datahdr, $datacuk, $idPrint) {
    $pdf->setY(50);
    $idPrint = $idPrint;
    $Yawl = $pdf->getY();
    $nomor = 1;
    for($x=$idPrint; $x < count($datacuk); $x++){
        if($datacuk[$x]['NILAITRF'] > 0){
            $h = [];
            $pdf->setY($Yawl);
            $pdf->cell(10, 3, ($nomor), 0, 0, 'C', 0);
            $h[] = $pdf->getY();
            //HS
            $pdf->MultiCell(60, 3, $datacuk[$x]['URKOMODITI']."\n".$datacuk[$x]['URSUBKOMODITI']."\n".$datacuk[$x]['MEREK'], 0, 'L', 0);
            $h[] = $pdf->getY();
            //Fasilitas dan lartas
            $pdf->setY($Yawl);
            $pdf->setX($pdf->getX() + 70);
            $pdf->MultiCell(34, 3, $datacuk[$x]['URJNSTARIF']."\n".number_format($datacuk[$x]['BESARTRF'], 4, '.', ',')."\n".number_format($datacuk[$x]['NILAITRF'], 4, '.', ','), 0, 'L', 0); //var
            $h[] = $pdf->getY();

            $pdf->setY($Yawl);
            $pdf->setX($pdf->getX() + 104);
            $pdf->MultiCell(33, 3, number_format($datacuk[$x]['HRGJUAL'], 0, '.', ',')."\n".$datacuk[$x]['URKEMASAN']."\n".number_format($datacuk[$x]['IsiPerKmsCuk'], 0, '.', ','), 0, 'L', 0); // BM
            $h[] = $pdf->getY();
            $pdf->setY($Yawl);
            $pdf->setX($pdf->getX() + 137);
            $pdf->MultiCell(57, 3, number_format($datacuk[$x]['SALDOAWALPC'], 0, '.', ',')."\n".number_format($datacuk[$x]['PITACUKAI'], 0, '.', ',')."\n".number_format($datacuk[$x]['SALDOAKHIRPC'], 0, '.', ','), 0, 'L', 0); //jumlah
            $h[] = $pdf->getY();
            $Yawl = max($h) + 5;
            if ($Yawl > 250){
                $x++;
                break;
            }
            $nomor++;
        }
    }

    $pdf->Rect(7.5, 47.5, 10, 222, 1, 'F'); //31.
    $pdf->Rect(17.5, 47.5, 60, 222, 1, 'F'); //32.
    $pdf->Rect(77.5, 47.5, 34, 222, 1, 'F'); //33.
    $pdf->Rect(111.5, 47.5, 33, 222, 1, 'F'); //34.
    $pdf->Rect(144.5, 47.5, 57, 222, 1, 'F'); //35.
    
    if ($x < count($datacuk)) {
        headerlampiranpib($pdf, $datahdr, 'cukai', $x);
        lampirancukaipib($pdf, $datahdr, $datacuk, $x);
    }
}

function footerlampiran($pdf, $datahdr) {
    $pdf->setXY(150, 270);
    $pdf->SetFont('times', '', '8');
    $imp = 'IMPORTIR';
    $pdf->multicell(50, 4, ucfirst(strtolower($datahdr['KOTA_TTD'])) . ', ' . date('d-m-Y') . "\n" . $imp . "\n\n\n" . $datahdr['NAMA_TTD'], 0, 'C');
    $pdf->SetFont('times', 'I', '8');
    $pdf->multicell(99.6, 3, "Tgl.Cetak " . date('d-m-Y'), 0, 'L');
    $pdf->SetFont('times', '', '8');
}