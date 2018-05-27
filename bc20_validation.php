<?php
    
    include('lib/database.php');
    include('lib/main.php');
    include('tax_calculation.php');
    
	validate();
	
    // =========== Validation Data =========== //
	function validationData()
	{
		// $sql = "SELECT * FROM T_BC20HDR WHERE STATUS = '000' ORDER BY CREATED_TIME ASC LIMIT 1";
		$sql = "SELECT * FROM T_BC20HDR WHERE CAR = '00000012345620170820123458'";
		// $sql = "SELECT H.*, COUNT(D.SERIAL) JM FROM T_BC20HDR H 
		// 		LEFT JOIN T_BC20DTL D ON H.CAR = D.CAR
		// 		WHERE H.STATUS = '000' AND H.KODE_TRADER = '2' 
		// 		GROUP BY H.CAR
		// 		HAVING JM = 2
		// 		LIMIT 2";

		$data['HEADER'] = getResult($sql)[0];
		return $data;
	}

	function validationDataDtl($car, $serial)
	{
        $sql = "SELECT  *, a.SERIAL
                    FROM t_bc20dtl a
                    Left Join t_bc20fas b ON a.KODE_TRADER = b.KODE_TRADER AND a.CAR = b.CAR AND a.SERIAL = b.SERIAL
                    Left Join t_bc20trf c ON a.KODE_TRADER = c.KODE_TRADER AND a.CAR = c.CAR AND a.NOHS = c.NOHS AND a.SERITRP = c.SERITRP
                    WHERE A.CAR = '" . $car . "' AND A.SERIAL = '" . $serial . "'";
        
        $data['DETIL'] = getResult($sql)[0];
        return $data;
	}
    // =========== End Validation Data =========== //

	function validate()
    {
        $errors = [];
        $request = (object)validationData();
        // print_r($request->HEADER);exit();

        $result = validateHeaderMandatory($request);
        if($result)
            $errors = $result;

        //status importir maksimal 2
        if (strlen($request->HEADER["IMPSTATUS"]) > 2)
            $errors[] = "Status importir maksimal = 2";

        //avoid jumlah barang < 0
        if (((int) $request->HEADER["JMBRG"]) < 0)
            $errors[] = "Jumlah Barang Tidak Boleh Negatif";

        //jika kode API isi status harus diisi
        if ($request->HEADER['APIKD'] == '' || $request->HEADER['APINO'] == '')
        {
            $request->HEADER['APIKD'] = '';
            $request->HEADER['APINO'] = '';
        }
        if ($request->HEADER['APIKD'] != '' && $request->HEADER['IMPSTATUS'] == '')
            $errors[] = "Status Importir harus diisi";

        //jika impor sementara maka jangkawaktu harus diisi
        if ($request->HEADER['JNIMP'] == '2' && (int) $request->HEADER['JKWAKTU'] <= 0)
            $errors[] = "Jangka waktu impor sementara harus diisi";

        //cek jenis impor & cara bayar
        if ($request->HEADER["JNIMP"] == 2 && $request->HEADER["CRBYR"] != 3)
            $errors[] = "Impor Sementara, cara pembayaran : [3 - Dengan jaminan]";
        
        //validasi berat
        if ($request->HEADER['BRUTO'] < $request->HEADER['NETTO'])
            $errors[] = "Bruto tidak boleh lebih kecil dari netto";

        //unsur Harga
        if ($request->HEADER['NILINV'] < 0 || $request->HEADER['ASURANSI'] < 0 || $request->HEADER['FREIGHT'] < 0 || $request->HEADER['DISCOUNT'] < 0)
            $errors[] = "Unsur harga tidak boleh kurang dari 0";

        //validasi VD
        $dnilvdsql = "SELECT SUM(a.Nilai) DNILVD FROM t_bc20dtlvd a WHERE a.CAR = '" . $request->HEADER['CAR'] . "' AND KODE_TRADER = " . $request->HEADER['KODE_TRADER'];
        $dnilvd = getResult($dnilvdsql)[0]['DNILVD'];
        
        if ($request->HEADER['VD'] != "1" && ($dnilvd > 0 || $request->HEADER['NILVD'] > 0))
        {
            $errors[] = "Jenis transaksi Vd di Header belum diisi";
        }
        if ($request->HEADER['NILVD'] != (int) $dnilvd) {
            if (abs($request->HEADER['NILVD'] - $dnilvd) > 1) {
                $errors[] = "Total VD Header = " . $request->HEADER["NILVD"] . ", detil = " . (int) $dnilvd;
            }
        }

        // cek pengisian dokumen
        $querydok = "SELECT GROUP_CONCAT(DOKKD separator '|') as ADOK FROM t_bc20dok WHERE CAR = '" . $request->HEADER['CAR'] . "' AND KODE_TRADER = " . $request->HEADER['KODE_TRADER'];
        $strdok = getResult($querydok)[0]['ADOK'];

        $dok['INVOICE'] = findArr2Str($strdok, array('380'));
        $dok['BL'] = findArr2Str($strdok, array('704', '705'));
        $dok['AWB'] = findArr2Str($strdok, array('740', '741'));
        $dok['SKEP'] = findArr2Str($strdok, array('814', '815', '851', '853', '911', '912', '913', '993', '998'));
        $dok['BPJ'] = findArr2Str($strdok, array('994'));
        $dok['STTJ'] = findArr2Str($strdok, array('997'));
        $dok['COO'] = findArr2Str($strdok, array('861'));
        $dok['NPWP'] = findArr2Str($strdok, array('450'));

        if($request->HEADER['KDFAS'])
            $request->HEADER['KDFAS'] = $request->HEADER['KDFAS'];
        else
            $request->HEADER['KDFAS'] = null;

        //Invoice
        if (!$dok['INVOICE'])
            $errors[] = "Dokumen Invoice belum diisi";
        //BL
        if (!$dok['BL'] && $request->HEADER['MODA'] == '1')
            $errors[] = "Dokumen BL tidak ada [Angkutan Laut]";
        //AWB
        if (!$dok['AWB'] && $request->HEADER['MODA'] == '4')
            $errors[] = "Dokumen AWB tidak ada [Angkutan Udara]";
        //COO
        if (!$dok['COO'] && strstr('06|54|55|56|57|58', $request->HEADER['KDFAS']))
            $errors[] = "Ada fasilitas ' . {$request->HEADER['KDFAS']} . ' tetapi belum mengisi Dokumen SKA/COO [kode 861]";
        //SKEP
        if (!$dok['SKEP'] && trim($request->HEADER['KDFAS']) != '' && !strstr('06|54|55|56|57|58|70', $request->HEADER['KDFAS']))
            $errors[] = "Ada fasilitas ' . {$request->HEADER['KDFAS']} . ' tetapi belum mengisi Dokumen Skep [kode 814, 815, 851, 853, 911, 912, 913, 993, 998]";
        
        //NPWP
        if($request->HEADER['IMPID'] == '0' && strlen(str_replace('.', '', str_replace('-', '', $request->HEADER['IMPNPWP']))) != 12)
            $errors[] = "Pengisian Identitas + NPWP Importir salah [12 digit]";

        if($request->HEADER['IMPID'] == '1' && strlen(str_replace('.', '', str_replace('-', '', $request->HEADER['IMPNPWP']))) != 11)
            $errors[] = "Pengisian Identitas + NPWP Importir salah [11 digit]";

        if($request->HEADER['IMPID'] == '5' && strlen(str_replace('.', '', str_replace('-', '', $request->HEADER['IMPNPWP']))) != 15)
            $errors[] = "Pengisian Identitas + NPWP Importir salah [15 digit]";
        
        if (!$dok['NPWP'] && strstr('|70|', $request->HEADER['KDFAS']))
            $errors[] = "Ada Pemilik Barang tetapi belum mengisi NPWP [kode 450]";

        //cek isian kemasan
        $querykms = "SELECT COUNT(CAR) as jml FROM t_bc20kms WHERE CAR = '" . $request->HEADER['CAR'] . "' AND KODE_TRADER = " . $request->HEADER['KODE_TRADER'];
        $jmlkms = getResult($querykms)[0]['jml'];
        if ($jmlkms <= 0)
            $errors[] = "Data kemasan masih kosong";

        //Kode gudang harus sesuai dengan kantor KPBC yang dipilih
        if ($request->HEADER['TMPTBN'] != '')
        {
            $querygdg = "SELECT COUNT(KDKPBC) as jml FROM m_gudang WHERE KDKPBC ='" . $request->HEADER['KDKPBC'] . "' AND KDGDG ='" . $request->HEADER['TMPTBN'] . "'";
            $jmlgdg = getResult($querygdg)[0]['jml'];
            if ($jmlgdg <= 0 && $request->HEADER['TMPTBN'] != '-')
                $errors[] = "Kode gudang " . $request->HEADER['TMPTBN'] . " tidak ada di kantor " . $request->HEADER['KDKPBC'];
        }
        //validasi Fasilitas Header Detil
        $fasdtlsql = "SELECT COUNT(1) CNTFASDTL FROM t_bc20dtldok WHERE CAR = '" . $request->HEADER['CAR'] . "' AND KODE_TRADER = " . $request->HEADER['KODE_TRADER'];
        $cntfasdtl = getResult($fasdtlsql)[0]['CNTFASDTL'];
        //validasi Fasilitas Header
        if (strlen($request->HEADER['KDFAS']) < 1 && $cntfasdtl > 0)
            $errors[] = "Jenis fasilitas di Header belum diisi";
        //validasi Fasilitas Detil
        if (strlen($request->HEADER['KDFAS']) > 0 && $cntfasdtl < 1)
            $errors[] = "Jenis fasilitas di Detil belum diisi";

        $dnilinvsql = "SELECT SUM(a.DNILINV) DNILINV FROM t_bc20dtl a WHERE a.CAR = '" . $request->HEADER['CAR'] . "' AND KODE_TRADER = " . $request->HEADER['KODE_TRADER'];
        $dnilinv = getResult($dnilinvsql)[0]['DNILINV'];
        if (round(($request->HEADER["NILINV"] + ($request->HEADER["NILVD"] > 0 ? $request->HEADER["NILVD"] : 0)), 2) != (float) $dnilinv) {
            // if (abs($request->HEADER['NILINV'] - $dnilinv) > 1) {
                $errors[] = "Harga Header = " . ($request->HEADER["NILINV"] + ($request->HEADER["NILVD"] > 0 ? $request->HEADER["NILVD"] : 0)) . ", Harga Detil = " . (float) $dnilinv;
            // }
        }

        //Get Pungutan
        $gettax = get_bc20_tax($request->HEADER['CAR']);
        if($gettax == 'failed')
            $errors[] = "Proses penghitungan pungutan gagal";
        
        //pernyataan
        if ($request->HEADER["PERNYATAAN"] != "1")
            $errors[] = "Anda belum menyetujui kolom pernyataan";

        //Validasi Detil
        $detilsql = "SELECT SERIAL FROM T_BC20DTL WHERE CAR = '" . $request->HEADER['CAR'] . "'";
        $detildata = getResult($detilsql);
        
        for($i = 0; $i < count($detildata); $i++)
        {
            $serialverb = $detildata[$i]['SERIAL'];
            $failedserial = validateDtl($request->HEADER['CAR'], $serialverb);
            if($failedserial)
                $errors[] = $failedserial;
        }

        if(count($errors) > 0)
        {
            $dataUp = ['STATUS' => 'INV'];
            $where = ['CAR' => $request->HEADER['CAR']];
            sqlUpdate('T_BC20HDR', $dataUp, $where);

            $string = "Validasi Header\n"; 
            foreach ($errors as $value)
            {
                if(!is_array($value))
                {
                    $string .= "- " . $value . "\n";
                }
            }

            $string .= "Validasi Detil\n"; 
            foreach ($errors as $value)
            {
                if(is_array($value))
                {
                    if(count($value) > 0)
                    {
                        foreach($value as $key => $val)
                        {
                            $string .= "Seri ke - " . $key . "\n";
                            foreach($val as $v)
                            {
                                $string .= "- " . $v . "\n";
                            }
                        }
                    }
                }
            }
        }
        else
        {
            $dataUp = ['STATUS' => '010'];
            $where = ['CAR' => $request->HEADER['CAR']];
            sqlUpdate('T_BC20HDR', $dataUp, $where);
            $string = 'Valid';
        }


        $dataHslVal = [
            'KODE_TRADER' =>  '1',
            'CAR' =>  $request->HEADER['CAR'],
            'VALIDASI' =>  $string,
        ];
        insertRefernce('T_BC20HASILVAL', $dataHslVal);

        $dataLogVal = [
            'CAR' =>  $request->HEADER['CAR'],
            'ACTION_NAME' =>  'VALIDATION',
            'DESCRIPTION' =>  $string,
            'USERNAME' =>  0,
            'CREATED_BY' =>  0,
        ];
        
        insertRefernce('T_BC20LOG', $dataLogVal);
        print_r($string);
    }

	function validateDtl($car, $serial)
    {
        $errors = [];
        $request = (object)validationDataDtl($car, $serial);

        $result = validateDetailMandatory($request);
        if($result)
            $errors = $result;

        //Cek Panjang HS
        if (!(strlen($request->DETIL['NOHS']) == 8) && !(strlen($request->DETIL['NOHS']) == 10))
            $errors[] = "Pengisian HS salah";

        //Cek jika kode jenis Tarif kosong
        if ($request->DETIL['KDTRPBM'] == '' && $request->DETIL['FASBM'] > 0) {
            $errors[] = "Kode jenis tarif BM (Advalorum/Spesifik) harus diisi";
        }
        if ($request->DETIL['KDTRPBM'] == '2') {
            if ((int) $request->DETIL['TRPBM'] == 0 || $request->DETIL['KDSATBM'] == '' || (int) $request->DETIL['SATBMJM'] == 0) {
                $errors[] = "Tarif Spesifik, Besar tarif dan kode satuan tarif harus diisi";
            }
        }

        //Cek Jumlah Satuan
        if ($request->DETIL['JMLSAT'] <= 0)
            $errors[] = "Jumlah Satuan harus diisi";

        //Cek jika kode jenis Fasilitas ada tapi Data Fasilitas kosong
        $queryfas = "SELECT COUNT(1) AS jml FROM t_bc20dtldok WHERE LENGTH(KdFasDtl) > 0 AND KdFasDtl != 'Y' AND Serial = '" . $request->DETIL['SERIAL'] . "' AND CAR = '" . $request->DETIL['CAR'] . "' AND KODE_TRADER = " . $request->DETIL['KODE_TRADER'];
        $jmlfas = getResult($queryfas)[0]['jml'];
        if (trim($request->DETIL['KDFASBM']," ") != '' && $jmlfas < 1)
        {
            $errors[] = "Jenis fasilitas belum diisi";
        }

        //Cek jika Fasilitas belum ada dokumennya
        $queryfasnodok = "SELECT COUNT(1) AS jml FROM t_bc20dtldok WHERE LENGTH(KdFasDtl) > 0 AND KdFasDtl != 'Y' AND LENGTH(LTRIM(RTRIM(IFNULL(DokKd, '')))) < 1 AND Serial = '" . $request->DETIL['SERIAL'] . "' AND CAR = '" . $request->DETIL['CAR'] . "' AND KODE_TRADER = " . $request->DETIL['KODE_TRADER'];
        $jmlfasnodok = getResult($queryfasnodok)[0]['jml'];
        if ($jmlfasnodok > 0)
        {
            $errors[] = "Ada fasilitas belum mengisi dokumennya";
        }

        // Cek kategori barang
        $queryjnsbrg = "SELECT a.FlBarangBaru AS barang_baru FROM t_bc20dtl a WHERE a.CAR = '" . $request->DETIL['CAR'] . "' AND a.SERIAL = '" . $request->DETIL['SERIAL'] . "' AND a.KODE_TRADER = " . $request->DETIL['KODE_TRADER'];
        $jnsbrg =  getResult($queryjnsbrg)[0]['barang_baru'];
        if ($jnsbrg < 1)
            $errors[] = "Jenis Barang pada detil belum di isi";

        //validasi vd
        $queryvd = "SELECT a.VD AS vd, COUNT(1) AS jml FROM t_bc20hdr a, t_bc20dtlvd b WHERE a.CAR = b.CAR AND a.KODE_TRADER = b.KODE_TRADER AND a.CAR = '" . $request->DETIL['CAR'] . "' AND b.SERIAL = '" . $request->DETIL['SERIAL'] . "' AND a.KODE_TRADER = " . $request->DETIL['KODE_TRADER'];
        // $isvd = getResult($queryvd)[0]['vd'];
        $jmlvd =  getResult($queryvd)[0]['jml'];
        // if ($isvd == '1' && $jmlvd < 1)
        if ($jmlvd < 1)
            $errors[] = "Jenis transaksi pada detil belum di isi";

        //validasi lartas
        $querylartas = "SELECT count(1) AS jml FROM t_bc20dtldok a WHERE a.CAR = '" . $request->DETIL['CAR'] . "' AND a.SERIAL = '" . $request->DETIL['SERIAL'] . "' AND a.KdFasDtl = 'Y' AND a.KODE_TRADER = " . $request->DETIL['KODE_TRADER'];
        $jmldoklartas =  getResult($querylartas)[0]['jml'];
        if ($request->DETIL['FlLartas'] == 'Y' && $jmldoklartas < 1)
            $errors[] = "Anda mengisi Flag Lartas, namun belum mengisi Dokumen Ijin Lartas";
        
        $result = false;
        if(count($errors) > 0)
            $result = array($serial => $errors);
        
        return $result;

        // return count($errors) > 0 ? $errors : false;
    }

    function validateHeaderMandatory($request)
    {
        $errors = [];
        if(!$request->HEADER['KDKPBC'])
            $errors[] = 'Kode Kantor BC Harus Diisi';
        if(!$request->HEADER['PELBKR'])
            $errors[] = 'Pelabuhan Bongkar Harus Diisi';
        if(!$request->HEADER['JNPIB'])
            $errors[] = 'Jenis PIB Harus Diisi';
        if(!$request->HEADER['JNIMP'])
            $errors[] = 'Jenis Impor Harus Diisi';
        if(!$request->HEADER['IMPID'])
            $errors[] = 'Jenis Identitas Importir Harus Diisi';
        if(!$request->HEADER['IMPNPWP'])
            $errors[] = 'Nomor Identitas Importir Harus Diisi';
        if(!$request->HEADER['IMPNAMA'])
            $errors[] = 'Nama Importir Harus Diisi';
        if(!$request->HEADER['IMPALMT'])
            $errors[] = 'Alamat Importir Harus Diisi';
        if(!$request->HEADER['PPJKID'])
            $errors[] = 'Jenis Identitas PPJK Harus Diisi';
        if(!$request->HEADER['PPJKNPWP'])
            $errors[] = 'Nomor Identitas PPJK Harus Diisi';
        if(!$request->HEADER['PPJKNAMA'])
            $errors[] = 'Nama PPJK Harus Diisi';
        if(!$request->HEADER['PPJKALMT'])
            $errors[] = 'Alamat PPJK Harus Diisi';
        if(!$request->HEADER['PASOKNAMA'])
            $errors[] = 'Nama Pemasok Harus Diisi';
        if(!$request->HEADER['PASOKALMT'])
            $errors[] = 'Alamat Pemasok Harus Diisi';
        if(!$request->HEADER['PASOKNEG'])
            $errors[] = 'Negara Pemasok Harus Diisi';
        if(!$request->HEADER['PELMUAT'])
            $errors[] = 'Pelabuhan Muat Harus Diisi';
        if(!$request->HEADER['MODA'])
            $errors[] = 'Alat Transportasi Harus Diisi';
        if(!$request->HEADER['ANGKUTNAMA'])
            $errors[] = 'Nama Alat Angkut Harus Diisi';
        if(!$request->HEADER['ANGKUTNO'])
            $errors[] = 'Nomor Voy/Flight Harus Diisi';
        if(!$request->HEADER['TGTIBA'])
            $errors[] = 'Tanggal Tiba Harus Diisi';
        if(!$request->HEADER['KDVAL'])
            $errors[] = 'Kode Valuta Harus Diisi';
        if(!$request->HEADER['NDPBM'])
            $errors[] = 'NDPBM Harus Diisi';
        if(!$request->HEADER['KDHRG'])
            $errors[] = 'Kode Harga Harus Diisi';
        if(!$request->HEADER['BRUTO'])
            $errors[] = 'Berat Bruto Harus Diisi';
        if(!$request->HEADER['NETTO'])
            $errors[] = 'Berat Netto Harus Diisi';
        if(!$request->HEADER['JMBRG'])
            $errors[] = 'Jumlah Barang Harus Diisi';

        return $errors;
    }

    function validateDetailMandatory($request)
    {
        $errors = [];
        if(!$request->DETIL['SERIAL'])
            $errors[] = 'Seri barang Harus Diisi';
        if(!$request->DETIL['NOHS'])
            $errors[] = 'Nomor HS Harus Diisi';
        if(!$request->DETIL['BRGURAI'])
            $errors[] = 'Uraian Barang Harus Diisi';
        if(!$request->DETIL['BRGASAL'])
            $errors[] = 'Negara Asal Barang Harus Diisi';
        if(!$request->DETIL['KDSAT'])
            $errors[] = 'Kode Satuan Harus Diisi';
        if(!$request->DETIL['JMLSAT'])
            $errors[] = 'Jumlah Satuan Harus Diisi';
        if(!$request->DETIL['KEMASJN'])
            $errors[] = 'Jenis Pengemas Harus Diisi';
        if(!$request->DETIL['CRBYR'])
            $errors[] = 'Cara Pembayaran Harus Diisi';
        if(!$request->DETIL['CIF'])
            $errors[] = 'Harga CIF Harus Diisi';

        return $errors;
    }