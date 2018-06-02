<?php
include('lib/database.php');
include('lib/main.php');

// HEADER
$sql_Intf_Hdr = "SELECT * FROM t_bc23hdr WHERE `STATUS` = '0'";
$data_Intf_Hdr = $db->query($sql_Intf_Hdr);
while($row_Intf_Hdr = $data_Intf_Hdr->fetch_assoc()){
	$CAR = $row_Intf_Hdr["CAR"];
	isset($data_Tpb_Hdr);

	// CEK HEADER ON TPB
	$sql_Tpb_Cek = "SELECT COUNT(*) adaCar FROM tpb_header WHERE NOMOR_AJU = '$CAR'";
    $data_Tpb_Cek = $db_tpb->query($sql_Tpb_Cek)->fetch_assoc();

    if ($data_Tpb_Cek["adaCar"] == 0) {
    	// INSERT HEADER ON TPB
    	$data_Tpb_Hdr = [
			"KODE_BENDERA" => $row_Intf_Hdr["ANGKUTFL"],
			"NAMA_PENGANGKUT" => $row_Intf_Hdr["ANGKUTNAMA"],
			"NOMOR_VOY_FLIGHT" => $row_Intf_Hdr["ANGKUTNO"],
			"KODE_JENIS_API" => $row_Intf_Hdr["APIKD"],
			"NOMOR_API" => $row_Intf_Hdr["APINO"],
			"ASURANSI" => $row_Intf_Hdr["ASURANSI"],
			"BRUTO" => $row_Intf_Hdr["BRUTO"],
			"BIAYA_TAMBAHAN" => $row_Intf_Hdr["BTAMBAHAN"],
			"NOMOR_AJU" => $row_Intf_Hdr["CAR"],
			"CIF" => $row_Intf_Hdr["CIF"],
			"CIF_RUPIAH" => $row_Intf_Hdr["CIFRP"],
			"DISKON" => $row_Intf_Hdr["DISKON"],
			"KODE_TUTUP_PU" => $row_Intf_Hdr["DOKTUPNO"],
			"FOB" => $row_Intf_Hdr["FOB"],
			"FREIGHT" => $row_Intf_Hdr["FREIGHT"],
			"ALAMAT_PEMILIK" => $row_Intf_Hdr["INDALMT"],
			"KODE_JENIS_API_PEMILIK" => $row_Intf_Hdr["INDAPIKD"],
			"API_PEMILIK" => $row_Intf_Hdr["INDAPINO"],
			"ID_PEMILIK" => $row_Intf_Hdr["INDID"],
			"NAMA_PEMILIK" => $row_Intf_Hdr["INDNAMA"],
			"KODE_ID_PEMILIK" => $row_Intf_Hdr["INDNPWP"],
			"JABATAN_TTD" => $row_Intf_Hdr["JABATANTTD"],
			"JUMLAH_BARANG" => $row_Intf_Hdr["JMBRG"],
			"JUMLAH_KONTAINER" => $row_Intf_Hdr["JMCONT"],
			"KODE_ASURANSI" => $row_Intf_Hdr["KDASS"],
			"KODE_FASILITAS" => $row_Intf_Hdr["KDFAS"],
			"KODE_HARGA" => $row_Intf_Hdr["KDHRG"],
			"KODE_KANTOR" => $row_Intf_Hdr["KDKPBC"],
			"KODE_KANTOR_BONGKAR" => $row_Intf_Hdr["KDKPBCBONGKAR"],
			"KODE_JENIS_TPB" => $row_Intf_Hdr["KDTPB"],
			"KODE_VALUTA" => $row_Intf_Hdr["KDVAL"],
			"KOTA_TTD" => $row_Intf_Hdr["KOTA_TTD"],
			"KODE_CARA_ANGKUT" => $row_Intf_Hdr["MODA"],
			"NAMA_TTD" => $row_Intf_Hdr["NAMA_TTD"],
			"NDPBM" => $row_Intf_Hdr["NDPBM"],
			"NETTO" => $row_Intf_Hdr["NETTO"],
			"HARGA_INVOICE" => $row_Intf_Hdr["NILINV"],
			"ALAMAT_PEMASOK" => $row_Intf_Hdr["PASOKALMT"],
			"NAMA_PEMASOK" => $row_Intf_Hdr["PASOKNAMA"],
			"KODE_NEGARA_PEMASOK" => $row_Intf_Hdr["PASOKNEG"],
			"KODE_PEL_BONGKAR" => $row_Intf_Hdr["PELBKR"],
			"KODE_PEL_MUAT" => $row_Intf_Hdr["PELMUAT"],
			"KODE_PEL_TRANSIT" => $row_Intf_Hdr["PELTRANSIT"],
			"POS_BC11" => $row_Intf_Hdr["POSNO"],
			"SUBPOS_BC11" => $row_Intf_Hdr["POSSUB"],
			"ALAMAT_PPJK" => $row_Intf_Hdr["PPJKALMT"],
			"ID_PPJK" => $row_Intf_Hdr["PPJKID"],
			"NAMA_PPJK" => $row_Intf_Hdr["PPJKNAMA"],
			"NPPPJK" => $row_Intf_Hdr["PPJKNO"],
			"TANGGAL_NPPPJK" => $row_Intf_Hdr["PPJKTG"],
			"NOMOR_IJIN_TPB" => $row_Intf_Hdr["REGISTRASI"],
			"TANGGAL_TTD" => $row_Intf_Hdr["TANGGAL_TTD"],
			"KODE_TUJUAN_TPB" => $row_Intf_Hdr["TUJUAN"],
			"ALAMAT_PENGUSAHA" => $row_Intf_Hdr["USAHAALMT"],
			"ID_PENGUSAHA" => $row_Intf_Hdr["USAHAID"],
			"NAMA_PENGUSAHA" => $row_Intf_Hdr["USAHANAMA"],
			"KODE_ID_PENGUSAHA" => $row_Intf_Hdr["USAHANPWP"],
			"KODE_STATUS_PENGUSAHA" => $row_Intf_Hdr["USAHASTATUS"],
    	];

    	$insertDataHdr[] = insertRefernce('tpb_header', $data_Tpb_Hdr, "TPB", false, "Y");
		$tpb_hdr_id = $insertDataHdr[0]["last_id"];

    	// INSERT DETAIL ON TPB
		$sql_Intf_Dtl = "SELECT * FROM t_bc23dtl WHERE CAR = '$CAR'";
		$data_Intf_Dtl = $db->query($sql_Intf_Dtl);
		while($row_Intf_Dtl = $data_Intf_Dtl->fetch_assoc()){
			$data_Tpb_Dtl = [
				"ASURANSI" => $row_Intf_Dtl["ASURANSI"],
				"KODE_NEGARA_ASAL" => $row_Intf_Dtl["BRGASAL"],
				"URAIAN" => $row_Intf_Dtl["BRGURAI"],
				"CIF" => $row_Intf_Dtl["DCIF"],
				"CIF_RUPIAH" => $row_Intf_Dtl["DCIFRP"],
				"DISKON" => $row_Intf_Dtl["DISKON"],
				"HARGA_INVOICE" => $row_Intf_Dtl["DNILINV"],
				"FOB" => $row_Intf_Dtl["FOB"],
				"FREIGHT" => $row_Intf_Dtl["FREIGHT"],
				"HARGA_SATUAN" => $row_Intf_Dtl["HRGSAT"],
				"JUMLAH_SATUAN" => $row_Intf_Dtl["JMLSAT"],
				"KODE_BARANG" => $row_Intf_Dtl["KDBRG"],
				"KODE_FASILITAS_DOKUMEN" => $row_Intf_Dtl["KDFASDTL"],
				"KODE_SATUAN" => $row_Intf_Dtl["KDSAT"],
				"KODE_KEMASAN" => $row_Intf_Dtl["KDSKEMATARIF"],
				"JUMLAH_KEMASAN" => $row_Intf_Dtl["KEMASJM"],
				"JENIS_KENDARAAN" => $row_Intf_Dtl["KEMASJN"],
				"MERK" => $row_Intf_Dtl["MERK"],
				"NETTO" => $row_Intf_Dtl["NETTODTL"],
				"SERI_BARANG" => $row_Intf_Dtl["SERIAL"],
				"SERI_POS_TARIF" => $row_Intf_Dtl["SERITRP"],
				"SPESIFIKASI_LAIN" => $row_Intf_Dtl["SPFLAIN"],
				"TIPE" => $row_Intf_Dtl["TIPE"],
				"ID_HEADER" => $tpb_hdr_id,
			];
    		
    		$insertDataDtl = insertRefernce('tpb_barang', $data_Tpb_Dtl, "TPB", false, "Y");
    		$insertDataHdr[] = $insertDataDtl;
			$tpb_dtl_id = $insertDataDtl["last_id"];

    		
    		// INSERT DETAIL DOCUMENT ON TPB
			$sql_Intf_DtlDok = "SELECT * FROM t_bc23dtldok WHERE CAR = '$CAR' AND SERIBRG = $row_Intf_Dtl['SERIAL'] ";
			$data_Intf_DtlDok = $db->query($sql_Intf_DtlDok);
			while($row_Intf_DtlDok = $data_Intf_DtlDok->fetch_assoc()){

				$data_Tpb_DtlDok = [
					"SERI_DOKUMEN" => $row_Intf_DtlDok["SERIBRG"],
					"ID_BARANG" => $tpb_dtl_id,
					"ID_HEADER" => $tpb_hdr_id,
				];
	    		$insertDataHdr[] = insertRefernce('tpb_barang_dokumen', $data_Tpb_DtlDok, "TPB");
			}


			// INSERT DETIL TARIF ON TPB
			$sql_Intf_DtlTrf = "SELECT * FROM t_bc23dtlTrf WHERE CAR = '$CAR' AND SERIBRG = $row_Intf_Dtl['SERIAL'] ";
			$data_Intf_DtlTrf = $db->query($sql_Intf_DtlTrf);
			while($row_Intf_DtlTrf = $data_Intf_DtlTrf->fetch_assoc()){

				// INSERT BM
				$data_Tpb_DtlTrf_BM = [
					"JENIS_TARIF" => 'BM',
					"KODE_TARIF" => $row_Intf_DtlTrf["KDTRPBM"],
					"KODE_SATUAN" => $row_Intf_DtlTrf["KDSATBM"],
					"TARIF" => $row_Intf_DtlTrf["TRPBM"],
					"KODE_FASILITAS" => $row_Intf_DtlTrf["KDFASBM"],
					"TARIF_FASILITAS" => $row_Intf_DtlTrf["FASBM"],
					"ID_BARANG" => $tpb_dtl_id,
					"ID_HEADER" => $tpb_hdr_id,
				];
	    		$insertDataHdr[] = insertRefernce('tpb_barang_tarif', $data_Tpb_DtlTrf_BM, "TPB");

	    		// INSERT PPN
	    		$data_Tpb_DtlTrf_PPN = [
					"JENIS_TARIF" => 'PPN',
					"TARIF" => $row_Intf_DtlTrf["TRPPPN"],
					"KODE_FASILITAS" => $row_Intf_DtlTrf["KDFASPPN"],
					"TARIF_FASILITAS" => $row_Intf_DtlTrf["FASPPN"],
					"ID_BARANG" => $tpb_dtl_id,
					"ID_HEADER" => $tpb_hdr_id,
				];
	    		$insertDataHdr[] = insertRefernce('tpb_barang_tarif', $data_Tpb_DtlTrf_PPN, "TPB");

	    		// INSERT PPH
	    		$data_Tpb_DtlTrf_PPH = [
					"JENIS_TARIF" => 'PPH',
					"TARIF" => $row_Intf_DtlTrf["TRPPPH"],
					"KODE_FASILITAS" => $row_Intf_DtlTrf["KDFASPPH"],
					"TARIF_FASILITAS" => $row_Intf_DtlTrf["FASPPH"],
					"ID_BARANG" => $tpb_dtl_id,
					"ID_HEADER" => $tpb_hdr_id,
				];
	    		$insertDataHdr[] = insertRefernce('tpb_barang_tarif', $data_Tpb_DtlTrf_PPH, "TPB");
			}

		}



		// INSERT CONTAINER ON TPB
		$sql_Intf_Con = "SELECT * FROM t_bc23con WHERE CAR = '$CAR'";
		$data_Intf_Con = $db->query($sql_Intf_Con);
		while($row_Intf_Con = $data_Intf_Con->fetch_assoc()){
			$data_Tpb_Con = [
				"NOMOR_KONTAINER" => $row_Intf_Con["CONTNO"],
				"KODE_TIPE_KONTAINER" => $row_Intf_Con["CONTTIPE"],
				"KODE_UKURAN_KONTAINER" => $row_Intf_Con["CONTUKUR"],
				"KETERANGAN" => $row_Intf_Con["KETERANGAN"],
				"ID_HEADER" => $tpb_hdr_id,
			];
    		$insertDataHdr[] = insertRefernce('tpb_kontainer', $data_Tpb_Con, "TPB");
		}



    	// INSERT DOCUMENT ON TPB
		$sql_Intf_Dok = "SELECT * FROM t_bc23dok WHERE CAR = '$CAR'";
		$data_Intf_Dok = $db->query($sql_Intf_Dok);
		while($row_Intf_Dok = $data_Intf_Dok->fetch_assoc()){
			$data_Tpb_Dok = [
				"KODE_JENIS_DOKUMEN" => $row_Intf_Dok["DOKKD"],
				"NOMOR_DOKUMEN" => $row_Intf_Dok["DOKNO"],
				"TANGGAL_DOKUMEN" => $row_Intf_Dok["DOKTG"],
				"SERI_DOKUMEN" => $row_Intf_Dok["SERIDOK"],
				"ID_HEADER" => $tpb_hdr_id,
			];
    		$insertDataHdr[] = insertRefernce('tpb_dokumen', $data_Tpb_Dok, "TPB");
		}


    	// INSERT KEMASAN ON TPB
		$sql_Intf_Kms = "SELECT * FROM t_bc23kms WHERE CAR = '$CAR'";
		$data_Intf_Kms = $db->query($sql_Intf_Kms);
		while($row_Intf_Kms = $data_Intf_Kms->fetch_assoc()){
			$data_Tpb_Kms = [
				"KODE_JENIS_KEMASAN" => $row_Intf_Kms["JNKEMAS"],
				"JUMLAH_KEMASAN" => $row_Intf_Kms["JMKEMAS"],
				"MERK_KEMASAN" => $row_Intf_Kms["DOKTMERKKEMAS"],
				"ID_HEADER" => $tpb_hdr_id,
			];
    		$insertDataHdr[] = insertRefernce('tpb_kemasan', $data_Tpb_Kms, "TPB");
		}


		// INSERT PUNGUTAN ON TPB
		// $sql_Intf_Pgt = "SELECT * FROM t_bc23pgt WHERE CAR = '$CAR'";
		// $data_Intf_Pgt = $db->query($sql_Intf_Pgt);
		// while($row_Intf_Pgt = $data_Intf_Pgt->fetch_assoc()){
		// 	$data_Tpb_Pgt = [
		// 		"JENIS_TARIF" => $row_Intf_Pgt["KDBEBAN"],
		// 		"KODE_FASILITAS" => $row_Intf_Pgt["KDFASIL"],
		// 		"NILAI_PUNGUTAN" => $row_Intf_Pgt["NILBEBAN"],
		// 		"ID_HEADER" => $tpb_hdr_id,
		// 	];
  //   		$insertDataHdr[] = insertRefernce('tpb_pungutan', $data_Tpb_Pgt, "TPB");
		// }

    }
}