<?php

	include('lib/database.php');
    include('lib/main.php');

    validate();
	
    // =========== Validation Data =========== //
	function validationData()
	{
		// $sql = "SELECT * FROM T_BC23HDR WHERE STATUS = '000' ORDER BY CREATED_TIME ASC LIMIT 1";
		$sql = "SELECT * FROM T_BC23HDR WHERE CAR = '00000012345620170820123456'";
		// $sql = "SELECT H.*, COUNT(D.SERIAL) JM FROM T_BC23HDR H 
		// 		LEFT JOIN T_BC23DTL D ON H.CAR = D.CAR
		// 		WHERE H.STATUS = '000' AND H.KODE_TRADER = '2' 
		// 		GROUP BY H.CAR
		// 		HAVING JM = 2
		// 		LIMIT 2";

		$data['HEADER'] = getResult($sql)[0];
		return $data;
	}

	function validationDataDtl($car, $serial)
	{
        $sql = "SELECT A.*, C.*, A.SERIAL, B.KDFASBM, B.FASBM, B.KDFASCUK, B.FASCUK, B.KDFASPPN, B.FASPPN, B.KDFASPBM, B.FASPBM, B.KDFASPPH, B.FASPPH 
                    FROM T_BC23DTL A 
                    LEFT JOIN T_BC23FAS B ON B.KODE_TRADER=A.KODE_TRADER AND B.CAR=A.CAR AND B.SERIAL=A.SERIAL 
                    LEFT JOIN T_BC23TRF C ON C.KODE_TRADER=A.KODE_TRADER AND C.CAR=A.CAR AND C.NOHS=A.NOHS AND C.SERITRP=A.SERITRP 
                    WHERE A.CAR = '" . $car . "' AND A.SERIAL = '" . $serial . "'";

                    print_r($sql);exit();
        
        $data['DETIL'] = getResult($sql)[0];
        return $data;
	}
    // =========== End Validation Data =========== //

	function validate()
	{
		$request = (object)validationData();
		// print_r($request->HEADER);exit();

        $errors = [];

        //pelabuhan
        $PELMUAT = getdata('pelabuhan', $request->HEADER["PELMUAT"]);
        if(!$PELMUAT)
            $errors[] = "Pelabuhan Muat Tidak Valid";
        $PELTRANSIT = getdata('pelabuhan', $request->HEADER["PELTRANSIT"]);
        if(!$PELMUAT)
            $errors[] = "Pelabuhan Transit Tidak Valid";
        $PELBKR = getdata('pelabuhan', $request->HEADER["PELBKR"]);
        if(!$PELMUAT)
            $errors[] = "Pelabuhan Bongkar Tidak Valid";

        // cek pengisian dokumen
        $querydok = "SELECT GROUP_CONCAT(DOKKD separator '|') as ADOK FROM t_bc23dok WHERE CAR = '" . $request->HEADER['CAR'] . "' AND KODE_TRADER = " . $request->HEADER['KODE_TRADER'];
        $strdok = getResult($querydok)[0]['ADOK'];

        $dok['INVOICE'] = findArr2Str($strdok, array('380'));
        $dok['BL'] = findArr2Str($strdok, array('704', '705'));
        $dok['AWB'] = findArr2Str($strdok, array('740', '741'));
        
        //Invoice
        if (!$dok['INVOICE'])
            $errors[] = "Dokumen Invoice belum diisi";
        //BL
        if (!$dok['BL'] && $request->HEADER['MODA'] == '1')
            $errors[] = "Terdapat data yang tidak sesuai antara transportasi yang digunakan dengan dokumenn B/L";
        //AWB
        if (!$dok['AWB'] && $request->HEADER['MODA'] == '4')
            $errors[] = "Terdapat data yang tidak sesuai antara transportasi yang digunakan dengan dokumenn AWB";

        //Validasi Berat
        if ($request->HEADER["BRUTO"] == 0 || $request->HEADER["NETTO"] == 0)
            $errors[] = "Bruto dan Netto tidak boleh bernilai 0";
        if ($request->HEADER["BRUTO"] < $request->HEADER["NETTO"])
            $errors[] = "Bruto < Netto";

        $NETTODTLSQL = "SELECT SUM(a.NETTODTL) NETTODTL FROM t_bc23dtl a WHERE a.CAR = '" . $request->HEADER['CAR'] . "'";
        $NETTODTL = getResult($NETTODTLSQL)[0]['NETTODTL'];
        if (round(($request->HEADER["NETTO"])) != round((float) $NETTODTL)) {
            $errors[] = "Netto di Header = " . $request->HEADER["NETTO"] . ", Netto di Barang = " . (float) $NETTODTL;
        }

        // Get Pungutan
        $gettax = get_bc23_tax($request->HEADER['CAR']);
        if($gettax == 'failed')
            $errors[] = "Proses penghitungan pungutan gagal";

        //Validasi Detil
        $resultvalverb = "";
        $detilsql = "SELECT SERIAL FROM T_BC23DTL WHERE CAR = '" . $request->HEADER['CAR'] . "'";
        $detildata = getResult($detilsql);
        
        for($i = 0; $i < count($detildata); $i++)
        {
            $serialverb = $detildata[$i]['SERIAL'];
            $failedserial = validateDtl($request->HEADER['CAR'], $request->HEADER['KODE_TRADER'], $serialverb);
            if($failedserial)
                $errors[] = $failedserial;
        }

        if(count($errors) > 0)
        {
            $dataUp = ['STATUS' => 'INV'];
            $where = ['CAR' => $request->HEADER['CAR']];
            sqlUpdate('T_BC23HDR', $dataUp, $where);

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
            sqlUpdate('T_BC23HDR', $dataUp, $where);
            $string = 'Valid';
        }


        $dataHslVal = [
            'KODE_TRADER' =>  '1',
            'CAR' =>  $request->HEADER['CAR'],
            'VALIDASI' =>  $string,
        ];
        insertRefernce('T_BC23HASILVAL', $dataHslVal);

        $dataLogVal = [
            'CAR' =>  $request->HEADER['CAR'],
            'ACTION_NAME' =>  'VALIDATION',
            'DESCRIPTION' =>  $string,
            'USERNAME' =>  0,
            'CREATED_BY' =>  0,
        ];
        
        insertRefernce('T_BC23LOG', $dataLogVal);
        print_r($string);
	}

	function validateDtl($car, $serial)
    {
        $request = (object)validationDataDtl($car, $serial);
        $errors = [];

        print_r($request);exit();

        //Cek jika kode jenis Tarif kosong
        if ($request->DETIL["KDTRPBM"] == "1" && (int) $request->DETIL["KDTRPBM"] == 0)
            $errors[] = "Pengisian BM Advalorum belum lengkap";
        
        if ($request->DETIL["KDTRPBM"] == "2")
        {
            if ((int) $request->DETIL["TRPBM"] == 0 || $request->DETIL["KDSATBM"] == "" || (int) $request->DETIL["SATBMJM"] == 0)
                $errors[] = "Pengisian BM Spesifik belum lengkap";
        }

        //Cek Skema Tarif belum ada dokumennya
        $sql = "SELECT (*) JML
                        FROM T_BC23DTLDOK DTLDOK
                        LEFT JOIN T_BC23DTL DTL ON DTL.CAR = DTLDOK.CAR AND DTL.SERIAL = DTLDOK.SERIBRG
                        LEFT JOIN T_BC23DOK DOK ON DOK.SERIDOK = DTLDOK.SERIDOK
                        WHERE DTLDOK.CAR = '".$request->DETIL['CAR']."' AND DTL.SERIAL = '".$request->DETIL['SERIAL']."' AND DOK.DOKKD = '861'";
        $jmlfasnodok = getResult($sql)[0]['jml'];
        if ($jmlfasnodok > 0)
        {
            $errors[] = "Wajib mencantumkan dokumen COO";
        }
        
        $result = false;
        if(count($errors) > 0)
        	$result = array($serial => $errors);
        
        return $result;
    }

    function validateHeaderMandatory($request)
    {
        $errors = [];
        if($request->HEADER['TUJUAN'])
            $errors[] = 'Tujuan TPB Harus Diisi';
        if($request->HEADER['PASOKNAMA'])
            $errors[] = 'Nama Pemasok Harus Diisi';
        if($request->HEADER['PASOKALMT'])
            $errors[] = 'Alamat Pemasok Harus Diisi';
        if($request->HEADER['PASOKNEG'])
            $errors[] = 'Negara Pemasok Harus Diisi';
        if($request->HEADER['USAHAID'])
            $errors[] = 'Kode Id Importir Harus Diisi';
        if($request->HEADER['USAHANPWP'])
            $errors[] = 'Id Importir Harus Diisi';
        if($request->HEADER['USAHANAMA'])
            $errors[] = 'Nama Importir Harus Diisi';
        if($request->HEADER['USAHAALMT'])
            $errors[] = 'Alamat Importir Harus Diisi';
        if($request->HEADER['REGISTRASI'])
            $errors[] = 'Nomor Ijin Importir Harus Diisi';
        if($request->HEADER['APIKD'])
            $errors[] = 'Kode API Importir Harus Diisi';
        if($request->HEADER['APINO'])
            $errors[] = 'API Importir Harus Diisi';
        if($request->HEADER['INDID'])
            $errors[] = 'Kode Id Pemilik Harus Diisi';
        if($request->HEADER['INDNPWP'])
            $errors[] = 'Id Pemilik Harus Diisi';
        if($request->HEADER['INDNAMA'])
            $errors[] = 'Nama Pemilik Harus Diisi';
        if($request->HEADER['INDALMT'])
            $errors[] = 'Alamat Pemilik Harus Diisi';
        if($request->HEADER['INDAPIKD'])
            $errors[] = 'Kode Jenis API Pemilik Harus Diisi';
        if($request->HEADER['INDAPINO'])
            $errors[] = 'API Pemilik Harus Diisi';
        if($request->HEADER['KDKPBCBONGKAR'])
            $errors[] = 'Kode Kantor Bongkar Harus Diisi';
        if($request->HEADER['KDKPBCAWAS'])
            $errors[] = 'Kode Kantor Pengawas Harus Diisi';
        if($request->HEADER['DOKTUPNO'])
            $errors[] = 'Nomor Dokumen Penutup PU Harus Diisi';
        if($request->HEADER['DOKTUPTG'])
            $errors[] = 'Tanggal Dokumen Penutup PU Harus Diisi';
        if($request->HEADER['POSNO'])
            $errors[] = 'Pos BC11 Harus Diisi';
        if($request->HEADER['POSSUB'])
            $errors[] = 'Sub Pos BC11 Harus Diisi';
        if($request->HEADER['POSSUBSUB'])
            $errors[] = 'Sub SubPos BC11 Harus Diisi';
        if($request->HEADER['TMPTBN'])
            $errors[] = 'Tempat Penimbunan Harus Diisi';
        if($request->HEADER['KDHRG'])
            $errors[] = 'Kode Harga Harus Diisi';
        if($request->HEADER['KDVAL'])
            $errors[] = 'Kode Valuta Harus Diisi';
        if($request->HEADER['NDPBM'])
            $errors[] = 'NDPBM tidak boleh 0';
        if($request->HEADER['MODA'])
            $errors[] = 'Alat Transportasi Harus Diisi';
        if($request->HEADER['ANGKUTNAMA'])
            $errors[] = 'Nama Alat Angkut Harus Diisi';
        if($request->HEADER['ANGKUTNO'])
            $errors[] = 'Nomor Voy/Flight Harus Diisi';
        if($request->HEADER['PELMUAT'])
            $errors[] = 'Pelabuhan Muat Harus Diisi';
        if($request->HEADER['PELBKR'])
            $errors[] = 'Pelabuhan Bongkar Harus Diisi';
        if($request->HEADER['KOTA_TTD'])
            $errors[] = 'Kota Penandatangan Harus Diisi';
        if($request->HEADER['NAMA_TTD'])
            $errors[] = 'Nama Penandatangan Harus Diisi';
        if($request->HEADER['JABATANTTD'])
            $errors[] = 'Jabatan Penandatangan Harus Diisi';

        return $errors;
    }

    function validateDetailMandatory($request)
    {
        $errors = [];
        if($request->DETIL['NOHS'])
            $errors[] = 'Nomor HS Harus Diisi';
        if($request->DETIL[''])
            $errors[] = 'Kategori Barang Harus Diisi';
        if($request->DETIL[''])
            $errors[] = 'Uraian Barang Harus Diisi';
        if($request->DETIL[''])
            $errors[] = 'Kode Barang Harus Diisi';
        if($request->DETIL[''])
            $errors[] = 'Kode Satuan Harus Diisi';
        if($request->DETIL[''])
            $errors[] = 'Jumlah Pengemas Harus Diisi';
        if($request->DETIL[''])
            $errors[] = 'Jenis Pengemas Harus Diisi';
        if($request->DETIL[''])
            $errors[] = 'Negara Asal Barang Harus Diisi';

        return $errors;
    }