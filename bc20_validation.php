<?php
    
    include('lib/database.php');
    include('lib/main.php');
    
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

	function validationDataDtl($car, $kode_trader, $serial)
	{
		$returndata = [];
        $sql = "SELECT  *, a.SERIAL "
                . " FROM    t_bc20dtl a
                    Left Join t_bc20fas b ON a.KODE_TRADER = b.KODE_TRADER AND a.CAR = b.CAR AND a.SERIAL = b.SERIAL
                    Left Join t_bc20trf c ON a.KODE_TRADER = c.KODE_TRADER AND a.CAR = c.CAR AND a.NOHS = c.NOHS AND a.SERITRP = c.SERITRP "
                . " WHERE A.CAR = '" . $car . "' AND (A.KODE_TRADER = '" . $kode_trader . "') AND A.SERIAL = '" . $serial . "'";
        
        $data['DETIL'] = getResult($sql)[0];
        return $data;
	}
    // =========== End Validation Data =========== //

	function validate()
	{
		$request = (object)validationData();
		// print_r($request->HEADER['CAR']);exit();

		//status importir maksimal 2
        if (strlen($request->HEADER["IMPSTATUS"]) > 2)
            $errors[] = "Status importir maksimal = 2";

        //avoid jumlah barang < 0
        if (((int) $request->HEADER["JMBRG"]) < 0)
            $errors[] = "Jumlah Barang Tidak Boleh Negatif";

        //jika kode API isi status harus diisi
        if ($request->HEADER['APIKD'] == '' || $request->HEADER['APINO'] == '') {
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
        if (!$dok['COO'] && strstr('|06|54|55|56|57|58|', $request->HEADER['KDFAS']))
            $errors[] = "Ada fasilitas ' . {$request->HEADER['KDFAS']} . ' tetapi belum mengisi Dokumen SKA/COO [kode 861]";
        //SKEP
        if (!$dok['SKEP'] && trim($request->HEADER['KDFAS']) != '' && !strstr('06|54|55|56|57|58', $request->HEADER['KDFAS']))
            $errors[] = "Ada fasilitas ' . {$request->HEADER['KDFAS']} . ' tetapi belum mengisi Dokumen Skep [kode 814, 815, 851, 853, 911, 912, 913, 993, 998]";

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

        //Cek User
        // if (!strpos("|020|030|040|080|082|084|086|087|088|090|100|102|105|110|112|114|116|120|150|", $request->HEADER['STATUS']))
        // {
        //     if ($tipe_trader == '1')
        //     { //Importir
        //         $request->HEADER['IMPID'] = $strtrader['KODE_ID'];
        //         $request->HEADER['IMPNPWP'] = $strtrader['ID'];
        //         $request->HEADER['IMPNAMA'] = $strtrader['NAMA'];
        //         $request->HEADER['IMPALMT'] = $strtrader['ALAMAT'];

        //         $request->HEADER['PPJKID'] = '';
        //         $request->HEADER['PPJKNPWP'] = '';
        //         $request->HEADER['PPJKNAMA'] = '';
        //         $request->HEADER['PPJKALMT'] = '';
        //         $request->HEADER['PPJKNO'] = '';
        //         $request->HEADER['PPJKTG'] = NULL;
        //     }
        //     elseif ($tipe_trader == '4')
        //     {
        //         $request->HEADER['PPJKID'] = $strtrader['KODE_ID'];
        //         $request->HEADER['PPJKNPWP'] = $strtrader['ID'];
        //         $request->HEADER['PPJKNAMA'] = $strtrader['NAMA'];
        //         $request->HEADER['PPJKALMT'] = $strtrader['ALAMAT'];
        //         $request->HEADER['PPJKNO'] = $strtrader['NO_PPJK'];
        //         $request->HEADER['PPJKTG'] = $strtrader['TANGGAL_PPJK'];
        //     }
        //     else
        //     {
        //         $errors[] = "Tipe user yang anda gunakan tidak sesuai dengan dokumen yang anda entry";
        //     }
        // }

        //validasi Fasilitas Detil
        $fasdtlsql = "SELECT COUNT(1) CNTFASDTL FROM t_bc20dtldok WHERE CAR = '" . $request->HEADER['CAR'] . "' AND KODE_TRADER = " . $request->HEADER['KODE_TRADER'];
        $cntfasdtl = getResult($fasdtlsql)[0]['CNTFASDTL'];
        if (strlen($request->HEADER['KDFAS']) > 0 && $cntfasdtl < 1)
            $errors[] = "Jenis fasilitas di Detil belum diisi";

        $dnilinvsql = "SELECT SUM(a.DNILINV) DNILINV FROM t_bc20dtl a WHERE a.CAR = '" . $request->HEADER['CAR'] . "' AND KODE_TRADER = " . $request->HEADER['KODE_TRADER'];
        $dnilinv = getResult($dnilinvsql)[0]['DNILINV'];
        if (($request->HEADER["NILINV"] + ($request->HEADER["NILVD"] > 0 ? $request->HEADER["NILVD"] : 0)) != (int) $dnilinv) {
            // if (abs($request->HEADER['NILINV'] - $dnilinv) > 1) {
                $errors[] = "Harga Header = " . ($request->HEADER["NILINV"] + ($request->HEADER["NILVD"] > 0 ? $request->HEADER["NILVD"] : 0)) . ", Harga Detil = " . (int) $dnilinv;
            // }
        }

        //Get Pungutan
        // $gettax = $this->repo->gettax($request->HEADER['CAR']);
        // if(!$gettax)
        //     $errors[] = "Proses penghitungan pungutan gagal";
        
        //pernyataan
        if ($request->HEADER["PERNYATAAN"] != "1")
            $errors[] = "Anda belum menyetujui kolom pernyataan";

        //Validasi Detil
        $resultvalverb = "";
        $detilsql = "SELECT SERIAL FROM T_BC20DTL WHERE CAR = '" . $request->HEADER['CAR'] . "' AND KODE_TRADER = " . $request->HEADER['KODE_TRADER'];
        $detildata = getResult($detilsql);
        
        for($i = 0; $i < count($detildata); $i++)
        {
            $serialverb = $detildata[$i]['SERIAL'];
            $failedserial = validateDtl($request->HEADER['CAR'], $request->HEADER['KODE_TRADER'], $serialverb);
            if($failedserial)
            	$errors[] = $failedserial;
            // if($failedserial != "")
            //     $resultvalverb .= "," . $failedserial;
        }

        // $detilerrors = explode(',', substr($resultvalverb, 1));
        // foreach ($detilerrors as $detilerror)
        // {
        //     $detilerror = rtrim(ltrim($detilerror));
        //     if($detilerror != '')
        //         $errors[] = "Pengisian Detil ke-" . $detilerror . " belum benar";
        // }

        // return count($errors) > 0 ? $errors : false;

        $string = "<b>Validasi Header</b><br>"; 
        foreach ($errors as $value)
        {
        	if(!is_array($value))
        	{
        		$string .= "- " . $value . "<br>";
        	}
        }

        $string .= "<b>Validasi Detil</b><br>"; 
        foreach ($errors as $value)
        {
        	if(is_array($value))
        	{
        		if(count($value) > 0)
        		{
	        		foreach($value as $key => $val)
	        		{
	        			$string .= "Seri ke - " . $key . "<br>";
	        			foreach($val as $v)
	        			{
	        				$string .= "- " . $v . "<br>";
	        			}
	        		}
        		}
        	}
        }

        print_r($string);
	}

	function validateDtl($car, $kode_trader, $serial)
    {
        $request = (object)validationDataDtl($car, $kode_trader, $serial);
        $errors = [];

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
        if ($request->DETIL['KDFASBM'] != '' && $jmlfas < 1)
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