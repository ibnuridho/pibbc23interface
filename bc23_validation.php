<?php

	include 'appreff.php';

    validate();
	
    // =========== Validation Data =========== //
	function validationData()
	{
		// $sql = "SELECT * FROM T_BC20HDR WHERE STATUS = '000' ORDER BY CREATED_TIME ASC LIMIT 1";
		$sql = "SELECT * FROM T_BC23HDR WHERE CAR = '00000000000320151125000021'";
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

        $sql = "SELECT A.*, C.*, A.SERIAL, B.KDFASBM, B.FASBM, B.KDFASCUK, B.FASCUK, B.KDFASPPN, B.FASPPN, B.KDFASPBM, B.FASPBM, B.KDFASPPH, B.FASPPH FROM T_BC23DTL A LEFT JOIN T_BC23FAS B ON B.KODE_TRADER=A.KODE_TRADER AND B.CAR=A.CAR AND B.SERIAL=A.SERIAL LEFT JOIN T_BC23TRF C ON C.KODE_TRADER=A.KODE_TRADER AND C.CAR=A.CAR AND C.NOHS=A.NOHS AND C.SERITRP=A.SERITRP 
                WHERE A.CAR = '" . $car . "' AND A.KODE_TRADER = '" . $kode_trader . "' AND A.SERIAL = '" . $serial . "'";
        
        $data['DETIL'] = getResult($sql)[0];
        return $data;
	}
    // =========== End Validation Data =========== //

	function validate()
	{
		$request = (object)validationData();
		// print_r($request->HEADER);exit();

        $errors = [];

        //avoid jumlah barang < 0
        if (((int) $request->HEADER["JMBRG"]) < 0)
            $errors[] = "Jumlah Barang Tidak Boleh Negatif";

        //jika kode API isi status harus diisi
        if ($request->HEADER['APIKD'] == '' || $request->HEADER['APINO'] == '')
        {
            $request->HEADER['APIKD'] = '';
            $request->HEADER['APINO'] = '';
        }

        //Cek Pengisian harga
        switch ($request->HEADER["KDHRG"]) {
            case "1": //CIF
                $request->HEADER["ASURANSI"] = 0;
                $request->HEADER["FREIGHT"] = 0;
                $request->HEADER["FOB"] = 0;
            break;
            case "2": //CNF
                $request->HEADER["FREIGHT"] = 0;
                $request->HEADER["FOB"] = $request->HEADER["NILINV"] + $request->HEADER["BTAMBAHAN"] - $request->HEADER["DISKON"];
                if ($request->HEADER["ASURANSI"] == 0 && $request->HEADER["KDASS"]=="1")
                    $errors[] = "Asuransi Luar Negeri, harus diisi";
            break;
            case "3": //FOB
                $request->HEADER["FOB"] = $request->HEADER["NILINV"] + $request->HEADER["BTAMBAHAN"] + $request->HEADER["DISKON"];
                if ($request->HEADER["FREIGHT"] == "0")
                    $errors[] = "Kode Harga FOB, Freight harus diisi";
                if ($request->HEADER["ASURANSI"] == "0" && $request->HEADER["KDASS"] == "1")
                    $errors[] = "Kode Harga FOB, dan bayar asuransi di LN, nilai asuransi harus diisi";
            break;
            default :
                $errors[] = "Kode harga salah";
            break;
        }
        if ($request->HEADER["NILINV"] < 0 || $request->HEADER["ASURANSI"] < 0 || $request->HEADER["FREIGHT"] < 0 || $request->HEADER["CIF"] < 0)
            $errors[] = "Unsur harga tidak boleh kurang dari 0";

        //Validasi Berat
        if ($request->HEADER["BRUTO"] < $request->HEADER["NETTO"])
            $errors[] = "Bruto tidak boleh lebih kecil dari netto";
        if ($request->HEADER["BRUTO"] <= 0)
            $errors[] = "Berat Bruto Harus diisi";
        if ($request->HEADER["NETTO"] <= 0)
            $errors[] = "Berat Netto Harus diisi"; 

        //cek pengisian dokumen
        $querydok = "SELECT GROUP_CONCAT(DOKKD separator '|') as ADOK FROM t_bc23dok WHERE CAR = '" . $request->HEADER['CAR'] . "' AND KODE_TRADER = " . $request->HEADER['KODE_TRADER'];
        $strdok = getResult($querydok)[0]['ADOK'];

        $dok['INVOICE'] = findArr2Str($strdok, array('380'));
        $dok['BL'] = findArr2Str($strdok, array('704', '705'));
        $dok['AWB'] = findArr2Str($strdok, array('740', '741'));
        $dok['SKEP'] = findArr2Str($strdok, array('912'));
        $dok['BC30'] = findArr2Str($strdok, array('816'));
        $dok['BPBP'] = findArr2Str($strdok, array('449'));
        $dok['IzinPNBPKala'] = findArr2Str($strdok, array('820'));

        //Invoice
        if (!$dok["INVOICE"])
            $errors[] = "Dokumen Invoice belum diisi";
        //BL
        if (!$dok["BL"] && $request->HEADER["MODA"] == "1")
            $errors[] = "Dokumen BL tidak ada [Angkutan Laut]";
        //AWB
        if (!$dok["AWB"] && $request->HEADER["MODA"] == "4")
            $errors[] = "Dokumen AWB tidak ada [Angkutan Udara]";
        //BC30
        if (!$dok["BC30"] && $request->HEADER["JNSBARANG"] == "7")
            $errors[] = "Barang Reimpor harus mencantumkann dokumen PEB (Kode Dok: 816)";
        //SKEP
        if (!$dok["SKEP"] && trim($request->HEADER["TUJUAN"])== "1" && strstr("|04|05|06|", $request->HEADER["JNSBARANG"]))
            $errors[] = "Jenis Barang Tidak Berhubungan langsung (04, 05, 06) harus ada Skep Penangguhan (Kode 912)";

        //JNSBARANG
        $query = "SELECT GROUP_CONCAT(DISTINCT A.JNSBARANGDTL ORDER BY JNSBARANGDTL ASC SEPARATOR '|') as 'JNSBARANG' 
                FROM t_bc23dtl A  
                WHERE A.CAR = '" . $request->HEADER['CAR'] . "' AND A.KODE_TRADER = " . $request->HEADER['KODE_TRADER'];
        $jnsBarang = getResult($query)[0]['JNSBARANG'];

        if($jnsBarang)
            $jnsBarang = $jnsBarang;
        else
            $jnsBarang = null;
        
        if (!$dok["SKEP"] && strstr('|04|05|06|', $jnsBarang))
            $errors[] = "Di Detil Jenis Barang Tidak Berhubungan langsung (04, 05, 06) harus ada Skep Penangguhan (Kode 912)";

        //KEMASAN
        $querykms = "SELECT COUNT(CAR) as jml FROM t_bc23kms WHERE CAR = '" . $request->HEADER['CAR'] . "' AND KODE_TRADER = " . $request->HEADER['KODE_TRADER'];
        $jmlkms = getResult($querykms)[0]['jml'];

        if ($jmlkms <= 0)
            $errors[] = "Belum mengisi data kemasan";

        //GUDANG
        // if ($request->HEADER['TMPTBN'] != '')
        // {
        //     $queryGdg = "SELECT COUNT(KDKPBC) as jmlKPBC 
        //                 FROM m_gudang 
        //                 WHERE KDKPBC ='" . $request->HEADER['KDKPBC'] . "' AND KDGDG ='" . $request->HEADER['TMPTBN'] . "'";
        //     $jmlGdg = $this->appRef->geturaian($queryGdg, "jmlKPBC");
        //     if ($jmlGdg <= 0)
        //         $errors[] = "Kode gudang " . $request->HEADER['TMPTBN'] . " tidak ada di kantor " . $request->HEADER['KDKPBC'];
        // }

        //TUJUAN-TUJUAN PENGIRIMAN
        if ($request->HEADER["TUJUAN"] == "1" && $request->HEADER["TUJUANKIRIM"] == "01" )
            $errors[] = "Tujuan ke Kawasan Berikat, bukan untuk tujuan ditimbun";
        if (($request->HEADER["TUJUAN"] == "5" || $request->HEADER["TUJUAN"] == "6")  && $request->HEADER["TUJUANKIRIM"] != "09" )
            $errors[] = "Jika tujuan ke TLB/KDUB, tujuan kirim hanya Lainnya";
        if (($request->HEADER["TUJUAN"] == "2" || $request->HEADER["TUJUAN"] == "3" || $request->HEADER["TUJUAN"] == "4")  && ($request->HEADER["TUJUANKIRIM"] != "01" && $request->HEADER["TUJUANKIRIM"] != "09"  ))
            $errors[] = "Jika tujuan ke GB, TPPB atau TBB, tujuan kirim hanya untuk Ditimbun atau Lainnya";

        //Cek Header vs Details Invoice
        $dnilinvsql = "SELECT SUM(a.DNILINV) DNILINV FROM t_bc23dtl a WHERE a.CAR = '" . $request->HEADER['CAR'] . "' AND KODE_TRADER = " . $request->HEADER['KODE_TRADER'];
        $dnilinv = getResult($dnilinvsql)[0]['DNILINV'];
        if ($request->HEADER['NILINV'] != (int) $dnilinv) {
            if (abs($request->HEADER['NILINV'] - $dnilinv) > 1) {
                $errors[] = "Harga Header = " . $request->HEADER["NILINV"] . ", Harga Detil = " . (int) $dnilinv;
            }
        }

        //Get Pungutan
        // $gettax = $this->repo->gettax($request->HEADER['CAR']);
        // if(!$gettax)
        //     $errors[] = "Proses penghitungan pungutan gagal";

        //Validasi Detil
        $resultvalverb = "";
        $detilsql = "SELECT SERIAL FROM T_BC23DTL WHERE CAR = '" . $request->HEADER['CAR'] . "' AND KODE_TRADER = " . $request->HEADER['KODE_TRADER'];
        $detildata = getResult($detilsql);

        for($i = 0; $i < count($detildata); $i++)
        {
            $serialverb = $detildata[$i]['SERIAL'];
            $failedserial = validateDtl($request->HEADER['CAR'], $request->HEADER['KODE_TRADER'], $serialverb);
            if($failedserial != "")
                $errors[] = $failedserial;
        }

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

        //Cek Jumlah Satuan
        if ($request->DETIL['JMLSAT'] <= 0)
            $errors[] = "Jumlah Satuan harus diisi";

        //Cek jika kode jenis Tarif kosong
        // if ($request->DETIL["KDTRPBM"] == "")
        //     $errors[] = "Kode jenis tarif BM (Advalorum/Spesifik) harus diisi";
        if ($request->DETIL["KDTRPBM"] == "2")
        {
            if ((int) $request->DETIL["TRPBM"] == 0 || $request->DETIL["KDSATBM"] == "" || (int) $request->DETIL["SATBMJM"] == 0)
                $errors[] = "Tarif Spesifik, Besar tarif dan kode satuan tarif harus diisi";
        }

        //Cek Fasilitas
        if ($request->DETIL['KDFASBM'] != '' && $request->DETIL['FASBM'] <= 0)
            $errors[] = "Prosentase fasilitas BM belum diisi atau hapus fasilitas";

        if ($request->DETIL['KDFASPPN'] != '' && $request->DETIL['FASPPN'] <= 0)
            $errors[] = "Prosentase fasilitas PPN belum diisi atau hapus fasilitas";

        if ($request->DETIL['KDFASPBM'] != '' && $request->DETIL['FASPBM'] <= 0)
            $errors[] = "Prosentase fasilitas PPnBM belum diisi atau hapus fasilitas";

        if ($request->DETIL['KDFASPPH'] != '' && $request->DETIL['FASPPH'] <= 0)
            $errors[] = "Prosentase fasilitas PPh belum diisi atau hapus fasilitas";

        //Cek Jenis Barang
        $sql = "SELECT a.TUJUAN FROM t_bc23hdr a WHERE a.CAR = '" . $request->DETIL['CAR'] . "' AND a.KODE_TRADER = " . $kode_trader;
        $hdrtujuan =  getResult($sql)[0]['TUJUAN'];

        if($request->DETIL['JNSBARANGDTL'])
            $request->DETIL['JNSBARANGDTL'] = $request->DETIL['JNSBARANGDTL'];
        else
            $request->DETIL['JNSBARANGDTL'] = null;
        
        if ($hdrtujuan == '2' && (strpos('|05|06|07|09|', $request->DETIL['JNSBARANGDTL']) < 1))
            $errors[] = "Tujuan ke GB, jenis barang hanya 5, 6, 7 atau 9";
        if ((strpos('|3|4|5|6|', $hdrtujuan) > 0) && $request->DETIL['JNSBARANGDTL'] != '9')
            $errors[] = "Tujuan ke TPPB, TBB, TLB, KDUB, jenis barang hanya untuk 'Barang Lainnya (9)";
        
        $result = false;
        if(count($errors) > 0)
        	$result = array($serial => $errors);
        
        return $result;

        // return count($errors) > 0 ? $errors : false;
    }