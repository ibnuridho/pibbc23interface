<?php
	
	function get_bc20_tax($car, $kode_trader = 1)
    {
    	// $arrDataDtl = DB::table('t_bc20dtl as DTL')
    	// 					->leftjoin('t_bc20fas as FAS', function($join){
    	// 						$join->on('DTL.KODE_TRADER', '=', 'FAS.KODE_TRADER');
    	// 						$join->on('DTL.CAR', '=', 'FAS.CAR');
    	// 						$join->on('DTL.SERIAL', '=', 'FAS.SERIAL');
    	// 					})
    	// 					->leftjoin('t_bc20trf as TRF', function($join){
    	// 						$join->on('DTL.KODE_TRADER', '=', 'TRF.KODE_TRADER');
    	// 						$join->on('DTL.CAR', '=', 'TRF.CAR');
    	// 						$join->on('DTL.NOHS', '=', 'TRF.NOHS');
    	// 						$join->on('DTL.SERITRP', '=', 'TRF.SERITRP');
    	// 					})
    	// 					->leftjoin('t_bc20dtldok as DTLDOK', function($join){
    	// 						$join->on('DTL.KODE_TRADER', '=', 'DTLDOK.KODE_TRADER');
    	// 						$join->on('DTL.CAR', '=', 'DTLDOK.CAR');
    	// 						$join->on('DTL.SERIAL', '=', 'DTLDOK.SERIAL');
    	// 					})
    	// 					->where('DTL.KODE_TRADER', '=', $kode_trader)
    	// 					->where('DTL.CAR', '=', $car)
    	// 					->select('*', DB::raw('GROUP_CONCAT(DTLDOK.KDFASDTL) as KDFASDTLDOK'))
    	// 					->groupBy('DTL.SERIAL')
    	// 					->get();


    	$sqlDtl = "SELECT *, GROUP_CONCAT(DTLDOK.KDFASDTL) as KDFASDTLDOK
			    	FROM T_BC20DTL AS DTL
			    	LEFT JOIN T_BC20FAS AS FAS ON DTL.KODE_TRADER = FAS.KODE_TRADER AND DTL.CAR = FAS.CAR AND DTL.SERIAL = FAS.SERIAL
			    	LEFT JOIN T_BC20TRF AS TRF ON DTL.KODE_TRADER = TRF.KODE_TRADER AND DTL.CAR = TRF.CAR AND DTL.NOHS = TRF.NOHS AND DTL.SERITRP = TRF.SERITRP
			    	LEFT JOIN T_BC20DTLDOK AS DTLDOK ON DTL.KODE_TRADER = DTLDOK.KODE_TRADER AND DTL.CAR = DTLDOK.CAR AND DTL.SERIAL = DTLDOK.SERIBRG
			    	WHERE DTL.CAR = '".$car."'
			    	GROUP BY DTL.SERIAL";
    	
    	$arrDataDtl = getResult($sqlDtl);

    	/*HITUNG DETAIL*/
    	$jmlDtl = count($arrDataDtl);

    	if($jmlDtl > 0)
    	{
	    	/*DECLARE VARIABLE DTL*/
			// $CAR 	= null;
			$SERI 		= null;
			$DCIF 		= null;
			$SATBMJM 	= null;
			$KDFASDTL 	= null;
			$KDFASDTLDOK= null;
			$SATCUKJM  	= null;

			/*DECLARE VARIABLE HDR*/
			$NDPBM 		= null;

			/*DECLARE VARIABLE TRF*/
			$KDTRPBM 	= null;
			$KDSATBM 	= null;
			$KDCUK 		= null;
			$KDTRPCUK 	= null;
			$KDSATCUK 	= null;
			$TRPBM		= null;
			$TRPCUK 	= null;
			$TRPPPN 	= null;
			$TRPPBM 	= null;
			$TRPPPH 	= null;

			/*DECLARE VARIABLE FAS*/
			$KDFASBM 	= null;
			$KDFASCUK 	= null;
			$KDFASPPN 	= null;
			$KDFASPPH 	= null;
			$KDFASPBM 	= null;
			$FASBM		= null;
			$FASCUK 	= null;
			$FASPPN 	= null;
			$FASPPH 	= null;
			$FASPBM 	= null;
			$DCIFRP		= null;

			/*DECLARE VARIABLE OTHER*/
			$BM			= null;
			$VAL		= null;
			$VAL_SPECT	= null;
			$TOTALCUK	= null;
			$TOTCUKPLTPITA	= null;
			$TOTCUKTDKDPGT	= null;

			/*VALUE BM*/
			$BMBAYAR	= null;			$BMBAYAR_A		= null;
			$BMDITGPEM 	= null;			$BMDITGPEM_A 	= null;
			$BMTANGGUH	= null;			$BMTANGGUH_A	= null;
			$BMBERKALA	= null;			$BMBERKALA_A	= null;
			$BMBEBAS	= null;			$BMBEBAS_A		= null;
			$BMTOTAL 	= null;

			/*VALUE BM TAMBAHAN*/
			$BMADBAYAR		= null;
			$BMADDITGPEM 	= null;		$BMADTANGGUH	= null;
			$BMADBERKALA	= null;		$BMADBEBAS		= null;

			$BMADBAYAR_A	= null;
			$BMADDITGPEM_A 	= null;		$BMADTANGGUH_A	= null;
			$BMADBERKALA_A	= null;		$BMADBEBAS_A	= null;

			#===================================================

			$BMTPBAYAR		= null;
			$BMTPDITGPEM 	= null;		$BMTPTANGGUH	= null;
			$BMTPBERKALA	= null;		$BMTPBEBAS		= null;

			$BMTPBAYAR_A	= null;
			$BMTPDITGPEM_A 	= null;		$BMTPTANGGUH_A	= null;
			$BMTPBERKALA_A	= null;		$BMTPBEBAS_A	= null;

			#===================================================

			$BMIMBAYAR		= null;
			$BMIMDITGPEM 	= null;		$BMIMTANGGUH	= null;
			$BMIMBERKALA	= null;		$BMIMBEBAS		= null;

			$BMIMBAYAR_A	= null;
			$BMIMDITGPEM_A 	= null;		$BMIMTANGGUH_A	= null;
			$BMIMBERKALA_A	= null;		$BMIMBEBAS_A	= null;

			#===================================================

			$BMPBBAYAR		= null;
			$BMPBDITGPEM 	= null;		$BMPBTANGGUH	= null;
			$BMPBBERKALA	= null;		$BMPBBEBAS		= null;

			$BMPBBAYAR_A	= null;
			$BMPBDITGPEM_A 	= null;		$BMPBTANGGUH_A	= null;
			$BMPBBERKALA_A	= null;		$BMPBBEBAS_A	= null;

			#===================================================

			$BMKITEBAYAR	= null;
			$BMKITEDITGPEM 	= null;		$BMKITETANGGUH	= null;
			$BMKITEBERKALA	= null;		$BMKITEBEBAS	= null;

			$BMKITEBAYAR_A	= null;
			$BMKITEDITGPEM_A= null;		$BMKITETANGGUH_A= null;
			$BMKITEBERKALA_A= null;		$BMKITEBEBAS_A	= null;

			#===================================================

			$BMADTOTAL		= null;		$BMIMTOTAL		= null;
			$BMTPTOTAL		= null;		$BMPBTOTAL		= null;
			$BMTTOTAL		= null;

			$BMADTOTAL_A	= null;		$BMIMTOTAL_A	= null;
			$BMTPTOTAL_A	= null;		$BMPBTOTAL_A	= null;
			$BMTTOTAL_A		= null;

			#===================================================

			/*VALUE CT*/
			$CTBAYAR		= null;		$CTBAYAR_A		= null;
			$CTPLTPITA		= null;		$CTPLTPITA_A	= null;
			$CTTDKDPGT		= null;		$CTTDKDPGT_A	= null;

			/*VALUE CMMAE*/
			$CMEBAYAR		= null;		$CMEBAYAR_A		= null;
			$CMEPLTPITA 	= null;		$CMEPLTPITA_A 	= null;
			$CMETDKDPGT 	= null;		$CMETDKDPGT_A 	= null;

			/*VALUE ETHIL ALKOHOL*/
			$CEABAYAR		= null;		$CEABAYAR_A		= null;
			$CEAPLTPITA 	= null;		$CEAPLTPITA_A 	= null;
			$CEATDKDPGT 	= null;		$CEATDKDPGT_A 	= null;

			/*PPN*/
			$PPNBAYAR		= null;
			$PPNDITGPEM 	= null;
			$PPNTANGGUH 	= null;
			$PPNBERKALA 	= null;
			$PPNBEBAS		= null;
			$PPNTDKPGT		= null;

			/*PPNBM*/
			$PBMBAYAR		= null;
			$PBMDITGPEM 	= null;
			$PBMTANGGUH 	= null;
			$PBMBERKALA 	= null;
			$PBMBEBAS		= null;
			$PBMTDKPGT		= null;

			/*PPH*/
			$PPHBAYAR		= null;
			$PPHDITGPEM 	= null;
			$PPHTANGGUH 	= null;
			$PPHBERKALA 	= null;
			$PPHBEBAS		= null;
			$PPHTDKPGT		= null;

			/*SET KURS*/
	    	$sql = "SELECT NDPBM FROM T_BC20HDR WHERE CAR = '".$car."'";
	    	$NDPBM = getResult($sql)[0]['NDPBM'];

	    	/*KOSONGKAN PUNGUTAN*/
	    	$sql = "SELECT COUNT(*) JMPGT FROM T_BC20PGT WHERE CAR = '".$car."'";
	    	$cekPgt = getResult($sql)[0]['JMPGT'];

	    	if($cekPgt > 0)
	    	{
	    		$sql = "DELETE FROM T_BC20PGT WHERE CAR = ".$car;
	    		delete($sql);
	    	}

	    	/*SET PUNGUTAN*/
	    	foreach ($arrDataDtl as $dataDtl)
	    	{
	    		$dataDtl = (object)$dataDtl;

				/*DETAIL*/
				$CAR 		= $dataDtl->CAR;				$SERI 		= $dataDtl->SERIAL; 
				$DCIF 		= $dataDtl->DCIF; 				$SATBMJM 	= $dataDtl->SATBMJM; 
				$KDFASDTL 	= $dataDtl->KDFASDTL; 			$SATCUKJM 	= $dataDtl->SATCUKJM;
				
				if($dataDtl->KDFASDTLDOK != null)
				{
					if(strpos($dataDtl->KDFASDTLDOK,',') != false)
					{
						$KDFASDTLDOK = explode(',',$dataDtl->KDFASDTLDOK);
					}
					else
					{
						$KDFASDTLDOK = array($dataDtl->KDFASDTLDOK);
					}
				}

				/*FASILITAS*/
				$KDFASBM 	= $dataDtl->KDFASBM; 			$KDFASCUK 	= $dataDtl->KDFASCUK; 
				$KDFASPPN 	= $dataDtl->KDFASPPN; 			$KDFASPPH 	= $dataDtl->KDFASPPH; 
				$KDFASPBM 	= $dataDtl->KDFASPBM; 			$FASBM 		= $dataDtl->FASBM; 
				$FASCUK 	= $dataDtl->FASCUK; 			$FASPPN 	= $dataDtl->FASPPN; 
				$FASPPH 	= $dataDtl->FASPPH; 			$FASPBM 	= $dataDtl->FASPBM;

				$KDFASBMAD 	= $dataDtl->KdFasBMAD;			$KDFASBMTP 	= $dataDtl->KdFasBMTP;
				$KDFASBMIM 	= $dataDtl->KdFasBMIM;			$KDFASBMPB 	= $dataDtl->KdFasBMPB;
				$FASBMAD 	= $dataDtl->FasBMAD;			$FASBMTP 	= $dataDtl->FasBMTP;
				$FASBMIM 	= $dataDtl->FasBMIM;			$FASBMPB 	= $dataDtl->FasBMPB;
				
				/*TARIF*/
				$KDTRPBM	= $dataDtl->KDTRPBM; 			$KDSATBM	= $dataDtl->KDSATBM;
				$KDCUK		= $dataDtl->KDCUK; 				$KDTRPCUK	= $dataDtl->KDTRPCUK; 
				$KDSATCUK	= $dataDtl->KDSATCUK; 			$TRPBM		= $dataDtl->TRPBM; 
				$TRPCUK		= $dataDtl->TRPCUK; 			$TRPPPN		= $dataDtl->TRPPPN; 
				$TRPPBM		= $dataDtl->TRPPBM; 			$TRPPPH		= $dataDtl->TRPPPH;

				$KDTRPBMAD 	= $dataDtl->KdTrpBmAD;			$TRPBMAD 	= $dataDtl->TrpBmAD;
				$KDTRPBMTP 	= $dataDtl->KdTrpBmTP;			$TRPBMTP 	= $dataDtl->TrpBmTP;
				$KDTRPBMIM 	= $dataDtl->KdTrpBmIM;			$TRPBMIM 	= $dataDtl->TrpBmIM;
				$KDTRPBMPB 	= $dataDtl->KdTrpBmPB;			$TRPBMPB 	= $dataDtl->TrpBmPB;

	    		/*START RESET VARIABLE*/
				/*HEADER*/
				$DCIFRP	= ROUND($DCIF, 2) * $NDPBM;

			    $BM	   		= 0;		    $BMBEBAS_A		= 0;		    
			    $BMAD 		= 0;			$BMADBEBAS_A	= 0;
			    $BMTP 		= 0;			$BMTPBEBAS_A 	= 0;
			    $BMIM 		= 0;			$BMIMBEBAS_A	= 0;
			    $BMPB 		= 0;			$BMPBBEBAS_A	= 0;


			    /*VALUE BM*/
				$BMBAYAR_A		= 0;
				$BMDITGPEM_A 	= 0;
				$BMTANGGUH_A	= 0;
				$BMBERKALA_A	= 0;
				$BMBEBAS_A		= 0;

				/*VALUE BM TAMBAHAN*/
				$BMADBAYAR_A	= 0;
				$BMADDITGPEM_A 	= 0;		$BMADTANGGUH_A	= 0;
				$BMADBERKALA_A	= 0;		$BMADBEBAS_A	= 0;

				#===================================================
				$BMTPBAYAR_A	= 0;
				$BMTPDITGPEM_A 	= 0;		$BMTPTANGGUH_A	= 0;
				$BMTPBERKALA_A	= 0;		$BMTPBEBAS_A	= 0;

				#===================================================
				$BMIMBAYAR_A	= 0;
				$BMIMDITGPEM_A 	= 0;		$BMIMTANGGUH_A	= 0;
				$BMIMBERKALA_A	= 0;		$BMIMBEBAS_A	= 0;

				#===================================================
				$BMPBBAYAR_A	= 0;
				$BMPBDITGPEM_A 	= 0;		$BMPBTANGGUH_A	= 0;
				$BMPBBERKALA_A	= 0;		$BMPBBEBAS_A	= 0;

				#===================================================
				$BMKITEBAYAR_A	= 0;
				$BMKITEDITGPEM_A= 0;		$BMKITETANGGUH_A= 0;
				$BMKITEBERKALA_A= 0;		$BMKITEBEBAS_A	= 0;

				#===================================================
				$BMADTOTAL_A	= 0;		$BMIMTOTAL_A	= 0;
				$BMTPTOTAL_A	= 0;		$BMPBTOTAL_A	= 0;
				$BMTTOTAL_A		= 0;

				#===================================================

				/*VALUE CT*/
				$CTBAYAR_A		= 0;
				$CTPLTPITA_A	= 0;
				$CTTDKDPGT_A	= 0;

				/*VALUE CMMAE*/
				$CMEBAYAR_A		= 0;
				$CMEPLTPITA_A 	= 0;
				$CMETDKDPGT_A 	= 0;

				/*VALUE ETHIL ALKOHOL*/
				$CEABAYAR_A		= 0;
				$CEAPLTPITA_A 	= 0;
				$CEATDKDPGT_A 	= 0;

				/*END RESET VARIABLE*/

				/*JENIS TARIF BM*/
				($KDTRPBM 	== '1') ? $BM 	= $DCIFRP * $TRPBM 	 / 100 : $BM   = $SATBMJM * $TRPBM;

				/*JENIS TARIF BMT*/
				($KDTRPBMAD == '1') ? $BMAD = $DCIFRP * $TRPBMAD / 100 : $BMAD   = $SATBMJM * $TRPBMAD;
				($KDTRPBMTP == '1') ? $BMTP = $DCIFRP * $TRPBMTP / 100 : $BMTP   = $SATBMJM * $TRPBMTP;
				($KDTRPBMIM == '1') ? $BMIM = $DCIFRP * $TRPBMIM / 100 : $BMIM   = $SATBMJM * $TRPBMIM;
				($KDTRPBMPB == '1') ? $BMPB = $DCIFRP * $TRPBMPB / 100 : $BMPB   = $SATBMJM * $TRPBMPB;

				/*BM*/
				$VAL = ($BM * $FASBM / 100);
				if($KDFASBM == '1')
				{
					$BMDITGPEM = $BMDITGPEM + ROUND($VAL,2);
					$BMDITGPEM_A = ROUND($VAL,2);
				}
				elseif($KDFASBM == '2')
				{
					$BMTANGGUH = $BMTANGGUH + ROUND($VAL,2);
					$BMTANGGUH_A = ROUND($VAL,2);
				}
				elseif($KDFASBM == '3')
				{
					$BMBERKALA = $BMBERKALA + ROUND($VAL,2);
					$BMBERKALA_A = ROUND($VAL,2);
				}
				elseif($KDFASBM == '4')
				{
					$BMBEBAS   = $BMBEBAS   + ROUND($VAL,2);
					$BMBEBAS_A = ROUND($VAL,2);
				}

				if($KDFASDTLDOK != null && in_array('40', $KDFASDTLDOK))
				{
					$BMKITEBAYAR = $BMKITEBAYAR + ROUND(($BM - $VAL),2);
					$BMKITEBAYAR_A = ROUND(($BM - $VAL),2);
				}
				else
				{
					$BMBAYAR = $BMBAYAR + ROUND(($BM - $VAL),2);
					$BMBAYAR_A = ROUND(($BM - $VAL),2);
				}

				/*BM TAMBAHAN*/ #==============================
				/*BMAD*/
				$VAL = ($BMAD * $FASBMAD / 100);
				if($KDFASBMAD == '1')
				{
					$BMADDITGPEM = $BMADDITGPEM + ROUND($VAL,2);
					$BMADDITGPEM_A = ROUND($VAL,2);
				}
				elseif($KDFASBMAD == '2')
				{
					$BMADTANGGUH = $BMADTANGGUH + ROUND($VAL,2);
					$BMADTANGGUH_A = ROUND($VAL,2);
				}
				elseif($KDFASBMAD == '3')
				{
					$BMADBERKALA = $BMADBERKALA + ROUND($VAL,2);
					$BMADBERKALA_A = ROUND($VAL,2);
				}
				elseif($KDFASBMAD == '4')
				{
					$BMADBEBAS   = $BMADBEBAS   + ROUND($VAL,2);
					$BMADBEBAS_A = ROUND($VAL,2);
				}

				$BMADBAYAR = $BMADBAYAR + ROUND(($BMAD - $VAL),2);
				$BMADBAYAR_A = ROUND(($BMAD - $VAL),2);

				/*BMTP*/
				$VAL = ($BMTP * $FASBMTP / 100);
				if($KDFASBMTP == '1')
				{
					$BMTPDITGPEM = $BMTPDITGPEM + ROUND($VAL,2);
					$BMTPDITGPEM_A = ROUND($VAL,2);
				}
				elseif($KDFASBMTP == '2')
				{
					$BMTPTANGGUH = $BMTPTANGGUH + ROUND($VAL,2);
					$BMTPTANGGUH_A = ROUND($VAL,2);
				}
				elseif($KDFASBMTP == '3')
				{
					$BMTPBERKALA = $BMTPBERKALA + ROUND($VAL,2);
					$BMTPBERKALA_A = ROUND($VAL,2);
				}
				elseif($KDFASBMTP == '4')
				{
					$BMTPBEBAS   = $BMTPBEBAS   + ROUND($VAL,2);
					$BMTPBEBAS_A = ROUND($VAL,2);
				}

				$BMTPBAYAR = $BMTPBAYAR + ROUND(($BMTP - $VAL),2);
				$BMTPBAYAR_A = ROUND(($BMTP - $VAL),2);

				/*BMIM*/
				$VAL = ($BMIM * $FASBMIM / 100);
				if($KDFASBMIM == '1')
				{
					$BMIMDITGPEM = $BMIMDITGPEM + ROUND($VAL,2);
					$BMIMDITGPEM_A = ROUND($VAL,2);
				}
				elseif($KDFASBMIM == '2')
				{
					$BMIMTANGGUH = $BMIMTANGGUH + ROUND($VAL,2);
					$BMIMTANGGUH_A = ROUND($VAL,2);
				}
				elseif($KDFASBMIM == '3')
				{
					$BMIMBERKALA = $BMIMBERKALA + ROUND($VAL,2);
					$BMIMBERKALA_A = ROUND($VAL,2);
				}
				elseif($KDFASBMIM == '4')
				{
					$BMIMBEBAS   = $BMIMBEBAS   + ROUND($VAL,2);
					$BMIMBEBAS_A = ROUND($VAL,2);
				}

				$BMIMBAYAR = $BMIMBAYAR + ROUND(($BMIM - $VAL),2);
				$BMIMBAYAR_A = ROUND(($BMIM - $VAL),2);

				/*BMPB*/
				$VAL = ($BMPB * $FASBMPB / 100);
				if($KDFASBMPB == '1')
				{
					$BMPBDITGPEM = $BMPBDITGPEM + ROUND($VAL,2);
					$BMPBDITGPEM_A = ROUND($VAL,2);
				}
				elseif($KDFASBMPB == '2')
				{
					$BMPBTANGGUH = $BMPBTANGGUH + ROUND($VAL,2);
					$BMPBTANGGUH_A = ROUND($VAL,2);
				}
				elseif($KDFASBMPB == '3')
				{
					$BMPBBERKALA = $BMPBBERKALA + ROUND($VAL,2);
					$BMPBBERKALA_A = ROUND($VAL,2);
				}
				elseif($KDFASBMPB == '4')
				{
					$BMPBBEBAS   = $BMPBBEBAS   + ROUND($VAL,2);
					$BMPBBEBAS_A = ROUND($VAL,2);
				}

				$BMPBBAYAR = $BMPBBAYAR + ROUND(($BMPB - $VAL),2);
				$BMPBBAYAR_A = ROUND(($BMPB - $VAL),2);

				/*CUKAI*/
				if($KDTRPCUK == '1')
				{
					$VAL 		= $DCIFRP * ($TRPCUK / 100) * ($FASCUK / 100);
					$VAL_SPECT	= $DCIFRP * ($TRPCUK / 100);
				}
				else
				{
					$VAL 		= $TRPCUK * $SATCUKJM * ($FASCUK / 100);
					$VAL_SPECT	= $TRPCUK * $SATCUKJM;
				}

				if($KDCUK == '1')
				{
	            	if($KDFASCUK == '5')
	            	{
	            		$CEAPLTPITA	= $CEAPLTPITA + ROUND($VAL,2);
	            		$CEAPLTPITA_A	= ROUND($VAL,2);
	            	}
					if($KDFASCUK == '6')
					{
						$CEATDKDPGT	= $CEATDKDPGT + ROUND($VAL,2);
						$CEATDKDPGT_A	= ROUND($VAL,2);
					}
	             	$CEABAYAR = $CEABAYAR + ROUND($VAL_SPECT - $VAL,2);
	             	$CEABAYAR_A = ROUND($VAL_SPECT - $VAL,2);
				}
				elseif($KDCUK == '2')
				{
					if($KDFASCUK == '5')
					{
						$CMEPLTPITA	= $CMEPLTPITA + ROUND($VAL,2);
						$CMEPLTPITA_A	= ROUND($VAL,2);
					}
					if($KDFASCUK == '6')
					{
						$CMETDKDPGT	= $CMETDKDPGT + ROUND($VAL,2);
						$CMETDKDPGT_A	= ROUND($VAL,2);
					}
		            $CMEBAYAR = $CMEBAYAR + ROUND($VAL_SPECT - $VAL,2);
		            $CMEBAYAR_A = ROUND($VAL_SPECT - $VAL,2);
				}
				elseif($KDCUK == '3')
				{
					if($KDFASCUK == '5')
					{
						$CTPLTPITA	= $CTPLTPITA + ROUND($VAL,2);
						$CTPLTPITA_A	= ROUND($VAL,2);
					}
					if($KDFASCUK == '6')
					{
						$CTTDKDPGT	= $CTTDKDPGT + ROUND($VAL,2);
						$CTTDKDPGT_A	= ROUND($VAL,2);
					}
					$CTBAYAR = $CTBAYAR + ROUND($VAL_SPECT - $VAL,2);
					$CTBAYAR_A = ROUND($VAL_SPECT - $VAL,2);
				}
				/*END CUKAI*/

				// BMTOTAL PER DETAIL
				$BMTOTAL 	= $BMBAYAR_A + $BMKITEBAYAR_A + $BMDITGPEM_A + $BMTANGGUH_A + $BMBERKALA_A;
				$BMADTOTAL 	= $BMADBAYAR_A + $BMADDITGPEM_A + $BMADTANGGUH_A + $BMADBERKALA_A;
				$BMTPTOTAL 	= $BMTPBAYAR_A + $BMTPDITGPEM_A + $BMTPTANGGUH_A + $BMTPBERKALA_A;
				$BMIMTOTAL 	= $BMIMBAYAR_A + $BMIMDITGPEM_A + $BMIMTANGGUH_A + $BMIMBERKALA_A;
				$BMPBTOTAL 	= $BMPBBAYAR_A + $BMPBDITGPEM_A + $BMPBTANGGUH_A + $BMPBBERKALA_A;
				$BMTTOTAL 	= $BMADTOTAL + $BMTPTOTAL + $BMIMTOTAL + $BMPBTOTAL;

				$CEATOTAL	= $CEABAYAR_A + $CEAPLTPITA_A;
				$CMETOTAL	= $CMEBAYAR_A + $CMEPLTPITA_A;
				$CTTOTAL	= $CTBAYAR_A + $CTPLTPITA_A;

				$CUKAITOTAL = $CEATOTAL + $CMETOTAL + $CTTOTAL;
				// END OF BMTOTAL

				/*PPN*/
				$VAL = ($DCIFRP + $BMTOTAL + $BMTTOTAL + $CUKAITOTAL) * ($TRPPPN / 100) * ($FASPPN / 100);
			    if($KDFASPPN == '1') { $PPNDITGPEM = $PPNDITGPEM + ROUND($VAL,2); }
		     	if($KDFASPPN == '2') { $PPNTANGGUH = $PPNTANGGUH + ROUND($VAL,2); }
		     	if($KDFASPPN == '3') { $PPNBERKALA = $PPNBERKALA + ROUND($VAL,2); }
		     	if($KDFASPPN == '4') { $PPNBEBAS   = $PPNBEBAS + ROUND($VAL,2); }
		     	if($KDFASPPN == '6') { $PPNTDKPGT  = $PPNTDKPGT + ROUND($VAL,2); }
			    $PPNBAYAR = $PPNBAYAR + ROUND((($DCIFRP + $BMTOTAL + $BMTTOTAL + $CUKAITOTAL) * ($TRPPPN / 100)) - $VAL,2);
			    
			    /*PPNBM*/
			    $VAL = ($DCIFRP + $BMTOTAL + $BMTTOTAL + $CUKAITOTAL) * ($TRPPBM / 100) * ($FASPBM / 100);
			    if($KDFASPBM == '1') { $PBMDITGPEM = $PBMDITGPEM + ROUND($VAL,2); }
		     	if($KDFASPBM == '2') { $PBMTANGGUH = $PBMTANGGUH + ROUND($VAL,2); }
		     	if($KDFASPBM == '3') { $PBMBERKALA = $PBMBERKALA + ROUND($VAL,2); }
		     	if($KDFASPBM == '4') { $PBMBEBAS   = $PBMBEBAS + ROUND($VAL,2); }
		     	if($KDFASPBM == '6') { $PBMTDKPGT  = $PBMTDKPGT + ROUND($VAL,2); }
			    $PBMBAYAR = $PBMBAYAR + ROUND((($DCIFRP + $BMTOTAL + $BMTTOTAL + $CUKAITOTAL) * ($TRPPBM / 100)) - $VAL,2);
			    
			    /*PPH*/
			    $VAL = ($DCIFRP + $BMTOTAL + $BMTTOTAL + $CUKAITOTAL) * ($TRPPPH / 100) * ($FASPPH / 100);
			    if($KDFASPPH == '1') { $PPHDITGPEM = $PPHDITGPEM + ROUND($VAL,2); }
			    if($KDFASPPH == '2') { $PPHTANGGUH = $PPHTANGGUH + ROUND($VAL,2); }
			    if($KDFASPPH == '3') { $PPHBERKALA = $PPHBERKALA + ROUND($VAL,2); }
			    if($KDFASPPH == '4') { $PPHBEBAS   = $PPHBEBAS   + ROUND($VAL,2); }
			    if($KDFASPPH == '6') { $PPHTDKPGT  = $PPHTDKPGT   + ROUND($VAL,2); }
			    $PPHBAYAR = $PPHBAYAR + ROUND((($DCIFRP + $BMTOTAL + $BMTTOTAL + $CUKAITOTAL) * ($TRPPPH / 100)) - $VAL,2);

	    	}
	    	/*END FOREACH*/

	    	/*BULATKAN KE RIBUAN*/
	    	$BMBAYAR 		= ceiling($BMBAYAR,1000);
	    	$BMDITGPEM 		= ceiling($BMDITGPEM,1000);				$BMTANGGUH 		= ceiling($BMTANGGUH,1000);
	    	$BMBERKALA 		= ceiling($BMBERKALA,1000);				$BMBEBAS 		= ceiling($BMBEBAS,1000);
	    	
	    	$BMKITEBAYAR 	= ceiling($BMKITEBAYAR,1000);
	    	$BMKITEDITGPEM 	= ceiling($BMKITEDITGPEM,1000);			$BMKITETANGGUH 	= ceiling($BMKITETANGGUH,1000);
	    	$BMKITEBERKALA 	= ceiling($BMKITEBERKALA,1000);			$BMKITEBEBAS 	= ceiling($BMKITEBEBAS,1000);

	    	$BMADBAYAR 		= ceiling($BMADBAYAR,1000);
	    	$BMADDITGPEM 	= ceiling($BMADDITGPEM,1000);			$BMADTANGGUH 	= ceiling($BMADTANGGUH,1000);
	    	$BMADBERKALA 	= ceiling($BMADBERKALA,1000);			$BMADBEBAS 		= ceiling($BMADBEBAS,1000);

	    	$BMTPBAYAR 		= ceiling($BMTPBAYAR,1000);
	    	$BMTPDITGPEM 	= ceiling($BMTPDITGPEM,1000);			$BMTPTANGGUH 	= ceiling($BMTPTANGGUH,1000);
	    	$BMTPBERKALA 	= ceiling($BMTPBERKALA,1000);			$BMTPBEBAS 		= ceiling($BMTPBEBAS,1000);

	    	$BMIMBAYAR 		= ceiling($BMIMBAYAR,1000);
	    	$BMIMDITGPEM 	= ceiling($BMIMDITGPEM,1000);			$BMIMTANGGUH 	= ceiling($BMIMTANGGUH,1000);
	    	$BMIMBERKALA 	= ceiling($BMIMBERKALA,1000);			$BMIMBEBAS 		= ceiling($BMIMBEBAS,1000);

	    	$BMPBBAYAR 		= ceiling($BMPBBAYAR,1000);
	    	$BMPBDITGPEM 	= ceiling($BMPBDITGPEM,1000);			$BMPBTANGGUH 	= ceiling($BMPBTANGGUH,1000);
	    	$BMPBBERKALA 	= ceiling($BMPBBERKALA,1000);			$BMPBBEBAS 		= ceiling($BMPBBEBAS,1000);

			#========================================================
			$BMTBAYAR 		= $BMADBAYAR + $BMTPBAYAR + $BMIMBAYAR + $BMPBBAYAR;
			$BMTDITGPEM 	= $BMADDITGPEM + $BMTPDITGPEM + $BMIMDITGPEM + $BMPBDITGPEM;
			$BMTTANGGUH 	= $BMADTANGGUH + $BMTPTANGGUH + $BMIMTANGGUH + $BMPBTANGGUH;
			$BMTBERKALA 	= $BMADBERKALA + $BMTPBERKALA + $BMIMBERKALA + $BMPBBERKALA;
			$BMTBEBAS 		= $BMADBEBAS + $BMTPBEBAS + $BMIMBEBAS + $BMPBBEBAS;

			$BMTBAYAR 		= ceiling($BMTBAYAR,1000);
	    	$BMTDITGPEM 	= ceiling($BMTDITGPEM,1000);			$BMTTANGGUH 	= ceiling($BMTTANGGUH,1000);
	    	$BMTBERKALA 	= ceiling($BMTBERKALA,1000);			$BMTBEBAS 		= ceiling($BMTBEBAS,1000);
			#========================================================

	    	$PPNBAYAR 		= ceiling($PPNBAYAR,1000);				$PPNTDKPGT	 	= ceiling($PPNTDKPGT,1000);
	    	$PPNDITGPEM 	= ceiling($PPNDITGPEM,1000);			$PPNTANGGUH 	= ceiling($PPNTANGGUH,1000);
	    	$PPNBERKALA 	= ceiling($PPNBERKALA,1000);			$PPNBEBAS 		= ceiling($PPNBEBAS,1000);

	    	$PBMBAYAR 		= ceiling($PBMBAYAR,1000);				$PBMTDKPGT	 	= ceiling($PBMTDKPGT,1000);
	    	$PBMDITGPEM 	= ceiling($PBMDITGPEM,1000);			$PBMTANGGUH 	= ceiling($PBMTANGGUH,1000);
	    	$PBMBERKALA 	= ceiling($PBMBERKALA,1000);			$PBMBEBAS 		= ceiling($PBMBEBAS,1000);

	    	$PPHBAYAR 		= ceiling($PPHBAYAR,1000);				$PPHTDKPGT	 	= ceiling($PPHTDKPGT,1000);
	    	$PPHDITGPEM 	= ceiling($PPHDITGPEM,1000);			$PPHTANGGUH 	= ceiling($PPHTANGGUH,1000);
	    	$PPHBERKALA 	= ceiling($PPHBERKALA,1000);			$PPHBEBAS 		= ceiling($PPHBEBAS,1000);

	    	$CEABAYAR		= ceiling($CEABAYAR,1000);
	    	$CEAPLTPITA		= ceiling($CEAPLTPITA,1000);
	    	$CEATDKDPGT		= ceiling($CEATDKDPGT,1000);

	    	$CMEBAYAR		= ceiling($CMEBAYAR,1000);
	    	$CMEPLTPITA		= ceiling($CMEPLTPITA,1000);
	    	$CMETDKDPGT		= ceiling($CMETDKDPGT,1000);

	    	$CTBAYAR		= ceiling($CTBAYAR,1000);
	    	$CTPLTPITA		= ceiling($CTPLTPITA,1000);
	    	$CTTDKDPGT		= ceiling($CTTDKDPGT,1000);

			#========================================================
			$TOTALCUK		= $CEABAYAR + $CMEBAYAR + $CTBAYAR;
			$TOTCUKPLTPITA 	= $CEAPLTPITA + $CMEPLTPITA + $CTPLTPITA;
			$TOTCUKTDKDPGT 	= $CEATDKDPGT + $CMETDKDPGT + $CTTDKDPGT;

	    	$TOTALCUK		= ceiling($TOTALCUK,1000);
	    	$TOTCUKPLTPITA 	= ceiling($TOTCUKPLTPITA,1000);
	    	$TOTCUKTDKDPGT 	= ceiling($TOTCUKTDKDPGT,1000);
	    	#========================================================

	    	// $pungutan  = "<table style='font-family:Helvetica;font-size:12px' width='100%' border='1' bordercolor='white' bgcolor='#f7f7f7' cellpadding='3'>";
	    	// $pungutan .= "<tr>";
	    	// $pungutan .= "<td width='20%'></td><td width='13.33%'>DIBAYAR</td><td width='13.33%'>DITGPEM</td><td width='13.33%'>DITUNDA</td><td width='13.33%'>TDK DIPUNGUT</td><td width='13.33%'>DIBEBASKAN</td><td width='13.33%'>DILUNASI</td>";
	    	// $pungutan .= "</tr>";
	    	// $pungutan .= "<tr>";
	    	// $pungutan .= "<td>BM</td><td align='right'>".number_format($BMBAYAR)."</td><td align='right'>".number_format($BMDITGPEM)."</td><td align='right'>".number_format($BMTANGGUH)."</td><td align='right'></td><td align='right'>".number_format($BMBEBAS)."</td><td align='right'></td>";
	    	// $pungutan .= "</tr>";
	    	// $pungutan .= "<tr>";
	    	// $pungutan .= "<td>BMKITE</td><td align='right'>".number_format($BMKITEBAYAR)."</td><td align='right'>".number_format($BMKITEDITGPEM)."</td><td align='right'>".number_format($BMKITETANGGUH)."</td><td align='right'></td><td align='right'>".number_format($BMKITEBEBAS)."</td><td align='right'></td>";
	    	// $pungutan .= "</tr>";
	    	// $pungutan .= "<tr>";
	    	// $pungutan .= "<td>BMAD</td><td align='right'>".$BMADBAYAR."</td><td align='right'>".$BMADDITGPEM."</td><td align='right'>".$BMADTANGGUH."</td><td align='right'></td><td align='right'>".$BMADBEBAS."</td><td align='right'></td>";
	    	// $pungutan .= "</tr>";
	    	// $pungutan .= "<tr'>";
	    	// $pungutan .= "<td>BMTP</td><td align='right'>".$BMTPBAYAR."</td><td align='right'>".$BMTPDITGPEM."</td><td align='right'>".$BMTPTANGGUH."</td><td align='right'></td><td align='right'>".$BMTPBEBAS."</td><td align='right'></td>";
	    	// $pungutan .= "</tr>";
	    	// $pungutan .= "<tr>";
	    	// $pungutan .= "<td>BMIM</td><td align='right'>".$BMIMBAYAR."</td><td align='right'>".$BMIMDITGPEM."</td><td align='right'>".$BMIMTANGGUH."</td><td align='right'></td><td align='right'>".$BMIMBEBAS."</td><td align='right'></td>";
	    	// $pungutan .= "</tr>";
	    	// $pungutan .= "<tr>";
	    	// $pungutan .= "<td>BMPB</td><td align='right'>".$BMPBBAYAR."</td><td align='right'>".$BMPBDITGPEM."</td><td align='right'>".$BMPBTANGGUH."</td><td align='right'></td><td align='right'>".$BMPBBEBAS."</td><td align='right'></td>";
	    	// $pungutan .= "</tr>";
	    	// $pungutan .= "<tr bgcolor='#f0f0f0'>";
	    	// $pungutan .= "<td>BMT</td><td align='right'>".$BMTBAYAR."</td><td align='right'>".$BMTDITGPEM."</td><td align='right'>".$BMTTANGGUH."</td><td align='right'></td><td align='right'>".$BMTBEBAS."</td><td align='right'></td>";
	    	// $pungutan .= "</tr>";
	    	// $pungutan .= "<tr>";
	    	// $pungutan .= "<td>CUKAI EA</td><td align='right'>".$CEABAYAR."</td><td align='right'></td><td align='right'></td><td align='right'>".$CEATDKDPGT."</td><td align='right'></td><td align='right'>".$CEAPLTPITA."</td>";
	    	// $pungutan .= "</tr>";
	    	// $pungutan .= "<tr>";
	    	// $pungutan .= "<td>CUKAI MMEA</td><td align='right'>".$CMEBAYAR."</td><td align='right'></td><td align='right'></td><td align='right'>".$CMETDKDPGT."</td><td align='right'></td><td align='right'>".$CMEPLTPITA."</td>";
	    	// $pungutan .= "</tr>";
	    	// $pungutan .= "<tr>";
	    	// $pungutan .= "<td>CUKAI TMB</td><td align='right'>".$CTBAYAR."</td><td align='right'></td><td align='right'></td><td align='right'>".$CTTDKDPGT."</td><td align='right'></td><td align='right'>".$CTPLTPITA."</td>";
	    	// $pungutan .= "</tr>";
	    	// $pungutan .= "<tr bgcolor='#f0f0f0'>";
	    	// $pungutan .= "<td>TOTAL CUKAI</td><td align='right'>".$TOTALCUK."</td><td align='right'></td><td align='right'></td><td align='right'>".$TOTCUKTDKDPGT."</td><td align='right'></td><td align='right'>".$TOTCUKPLTPITA."</td>";
	    	// $pungutan .= "</tr>";
	    	// $pungutan .= "<tr>";
	    	// $pungutan .= "<td>PPN</td><td align='right'>".$PPNBAYAR."</td><td align='right'>".$PPNDITGPEM."</td><td align='right'>".$PPNTANGGUH."</td><td align='right'></td><td align='right'>".$PPNBEBAS."</td><td align='right'></td>";
	    	// $pungutan .= "</tr>";
	    	// $pungutan .= "<tr>";
	    	// $pungutan .= "<td>PPNBM</td><td align='right'>".$PBMBAYAR."</td><td align='right'>".$PBMDITGPEM."</td><td align='right'>".$PBMTANGGUH."</td><td align='right'></td><td align='right'>".$PBMBEBAS."</td><td align='right'></td>";
	    	// $pungutan .= "</tr>";
	    	// $pungutan .= "<tr>";
	    	// $pungutan .= "<td>PPH</td><td align='right'>".$PPHBAYAR."</td><td align='right'>".$PPHDITGPEM."</td><td align='right'>".$PPHTANGGUH."</td><td align='right'></td><td align='right'>".$PPHBEBAS."</td><td align='right'></td>";
	    	// $pungutan .= "</tr>";
	    	// $pungutan .= "</table>";

	    	// print_r($pungutan);exit();


	    	/*INSERT INTO DB*/
	    	/*INSERT BM*/
		    if($BMBAYAR			> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '1','KDFASIL' => '0','NILBEBAN' => $BMBAYAR]); }
		    if($BMDITGPEM		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '1','KDFASIL' => '1','NILBEBAN' => $BMDITGPEM]); }
		    if($BMTANGGUH		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '1','KDFASIL' => '2','NILBEBAN' => $BMTANGGUH]); }
		    if($BMBEBAS			> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '1','KDFASIL' => '4','NILBEBAN' => $BMBEBAS]); }

		    /*INSERT BMKITE*/
		    if($BMKITEBAYAR		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '8','KDFASIL' => '0','NILBEBAN' => $BMKITEBAYAR]); }
		    if($BMKITEDITGPEM	> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '8','KDFASIL' => '1','NILBEBAN' => $BMKITEDITGPEM]); }
		    if($BMKITETANGGUH	> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '8','KDFASIL' => '2','NILBEBAN' => $BMKITETANGGUH]); }
		    if($BMKITEBEBAS		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '8','KDFASIL' => '4','NILBEBAN' => $BMKITEBEBAS]); }

		    /*INSERT BMAD*/
		    if($BMADBAYAR		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '9','KDFASIL' => '0','NILBEBAN' => $BMADBAYAR]); }
		    if($BMADDITGPEM		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '9','KDFASIL' => '1','NILBEBAN' => $BMADDITGPEM]); }
		    if($BMADTANGGUH		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '9','KDFASIL' => '2','NILBEBAN' => $BMADTANGGUH]); }
		    if($BMADBERKALA		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '9','KDFASIL' => '3','NILBEBAN' => $BMADBERKALA]); }
		    if($BMADBEBAS		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '9','KDFASIL' => '4','NILBEBAN' => $BMADBEBAS]); }

		    /*INSERT BMTP*/
		    if($BMTPBAYAR		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '10','KDFASIL' => '0','NILBEBAN' => $BMTPBAYAR]); }
		    if($BMTPDITGPEM		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '10','KDFASIL' => '1','NILBEBAN' => $BMTPDITGPEM]); }
		    if($BMTPTANGGUH		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '10','KDFASIL' => '2','NILBEBAN' => $BMTPTANGGUH]); }
		    if($BMTPBERKALA		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '10','KDFASIL' => '3','NILBEBAN' => $BMTPBERKALA]); }
		    if($BMTPBEBAS		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '10','KDFASIL' => '4','NILBEBAN' => $BMTPBEBAS]); }

		    /*INSERT BMIM*/
		    if($BMIMBAYAR		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '11','KDFASIL' => '0','NILBEBAN' => $BMIMBAYAR]); }
		    if($BMIMDITGPEM		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '11','KDFASIL' => '1','NILBEBAN' => $BMIMDITGPEM]); }
		    if($BMIMTANGGUH		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '11','KDFASIL' => '2','NILBEBAN' => $BMIMTANGGUH]); }
		    if($BMIMBERKALA		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '11','KDFASIL' => '3','NILBEBAN' => $BMIMBERKALA]); }
		    if($BMIMBEBAS		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '11','KDFASIL' => '4','NILBEBAN' => $BMIMBEBAS]); }

		    /*INSERT BMPB*/
		    if($BMPBBAYAR		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '12','KDFASIL' => '0','NILBEBAN' => $BMPBBAYAR]); }
		    if($BMPBDITGPEM		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '12','KDFASIL' => '1','NILBEBAN' => $BMPBDITGPEM]); }
		    if($BMPBTANGGUH		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '12','KDFASIL' => '2','NILBEBAN' => $BMPBTANGGUH]); }
		    if($BMPBBERKALA		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '12','KDFASIL' => '3','NILBEBAN' => $BMPBBERKALA]); }
		    if($BMPBBEBAS		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '12','KDFASIL' => '4','NILBEBAN' => $BMPBBEBAS]); }

		    /*INSERT CEA*/
		    if($CEABAYAR		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '7','KDFASIL' => '0','NILBEBAN' => $CEABAYAR]); }
		    if($CEAPLTPITA		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '7','KDFASIL' => '5','NILBEBAN' => $CEAPLTPITA]); }
		    if($CEATDKDPGT		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '7','KDFASIL' => '6','NILBEBAN' => $CEATDKDPGT]); }

		    /*INSERT CME*/
		    if($CMEBAYAR		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '6','KDFASIL' => '0','NILBEBAN' => $CMEBAYAR]); }
		    if($CMEPLTPITA		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '6','KDFASIL' => '5','NILBEBAN' => $CMEPLTPITA]); }
		    if($CMETDKDPGT		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '6','KDFASIL' => '6','NILBEBAN' => $CMETDKDPGT]); }

		    /*INSERT CEA*/
		    if($CTBAYAR			> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '5','KDFASIL' => '0','NILBEBAN' => $CTBAYAR]); }
		    if($CTPLTPITA		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '5','KDFASIL' => '5','NILBEBAN' => $CTPLTPITA]); }
		    if($CTTDKDPGT		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '5','KDFASIL' => '6','NILBEBAN' => $CTTDKDPGT]); }

		    /*INSERT PPN*/
		    if($PPNBAYAR		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '2','KDFASIL' => '0','NILBEBAN' => $PPNBAYAR]); }
		    if($PPNDITGPEM		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '2','KDFASIL' => '1','NILBEBAN' => $PPNDITGPEM]); }
		    if($PPNTANGGUH		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '2','KDFASIL' => '2','NILBEBAN' => $PPNTANGGUH]); }
		    if($PPNBEBAS		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '2','KDFASIL' => '4','NILBEBAN' => $PPNBEBAS]); }
		    if($PPNTDKPGT		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '2','KDFASIL' => '6','NILBEBAN' => $PPNTDKPGT]); }

		    /*INSERT PPNBM*/
		    if($PBMBAYAR		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '3','KDFASIL' => '0','NILBEBAN' => $PBMBAYAR]); }
		    if($PBMDITGPEM		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '3','KDFASIL' => '1','NILBEBAN' => $PBMDITGPEM]); }
		    if($PBMTANGGUH		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '3','KDFASIL' => '2','NILBEBAN' => $PBMTANGGUH]); }
		    if($PBMBEBAS		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '3','KDFASIL' => '4','NILBEBAN' => $PBMBEBAS]); }
		    if($PBMTDKPGT		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '3','KDFASIL' => '6','NILBEBAN' => $PBMTDKPGT]); }

		    /*INSERT PPH*/
		    if($PPHBAYAR		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '4','KDFASIL' => '0','NILBEBAN' => $PPHBAYAR]); }
		    if($PPHDITGPEM		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '4','KDFASIL' => '1','NILBEBAN' => $PPHDITGPEM]); }
		    if($PPHTANGGUH		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '4','KDFASIL' => '2','NILBEBAN' => $PPHTANGGUH]); }
		    if($PPHBEBAS		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '4','KDFASIL' => '4','NILBEBAN' => $PPHBEBAS]); }
		    if($PPHTDKPGT		> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '4','KDFASIL' => '6','NILBEBAN' => $PPHTDKPGT]); }

	    	return "success";
	    }
	    else
	    {
	    	return "failed";
	    }
    }

	function get_bc23_tax($car, $kode_trader = 1)
    {
    	// $arrDataDtl = DB::table('t_bc23dtl as DTL')
    	// 					->join('t_bc23fas as FAS', function($join){
    	// 						$join->on('DTL.KODE_TRADER', '=', 'FAS.KODE_TRADER');
    	// 						$join->on('DTL.CAR', '=', 'FAS.CAR');
    	// 						$join->on('DTL.SERIAL', '=', 'FAS.SERIAL');
    	// 					})
    	// 					->leftjoin('t_bc23trf as TRF', function($join){
    	// 						$join->on('DTL.KODE_TRADER', '=', 'TRF.KODE_TRADER');
    	// 						$join->on('DTL.CAR', '=', 'TRF.CAR');
    	// 						$join->on('DTL.NOHS', '=', 'TRF.NOHS');
    	// 						$join->on('DTL.SERITRP', '=', 'TRF.SERITRP');
    	// 					})
    	// 					->where('DTL.KODE_TRADER', '=', $kode_trader)
    	// 					->where('DTL.CAR', '=', $car)
    	// 					->get();

    	$sqlDtl = "SELECT *, GROUP_CONCAT(DTLDOK.KDFASDTL) as KDFASDTLDOK
			    	FROM T_BC23DTL AS DTL
			    	LEFT JOIN T_BC23FAS AS FAS ON DTL.KODE_TRADER = FAS.KODE_TRADER AND DTL.CAR = FAS.CAR AND DTL.SERIAL = FAS.SERIAL
			    	LEFT JOIN T_BC23TRF AS TRF ON DTL.KODE_TRADER = TRF.KODE_TRADER AND DTL.CAR = TRF.CAR AND DTL.NOHS = TRF.NOHS AND DTL.SERITRP = TRF.SERITRP
			    	WHERE DTL.CAR = '".$car."'
			    	GROUP BY DTL.SERIAL";
    	
    	$arrDataDtl = getResult($sqlDtl);

    	/*HITUNG DETAIL*/
    	$jmlDtl = count($arrDataDtl);

    	if($jmlDtl > 0)
    	{
	    	/*DECLARE VARIABLE DTL*/
	    	// $CAR 			= null;
	    	$SERI 			= null;
			$DCIF 			= null;
			$SATBMJM 		= null;
			$KDFASDTL 		= null;
			$SATCUKJM  		= null;

			/*DECLARE VARIABLE HDR*/
			$NDPBM 			= null;

			/*DECLARE VARIABLE TRF*/
			$KDTRPBM 		= null;
			$KDSATBM 		= null;
			$KDCUK 			= null;
			$KDTRPCUK 		= null;
			$KDSATCUK 		= null;
			$TRPBM 			= null;
			$TRPCUK 		= null;
			$TRPPPN 		= null;
			$TRPPBM 		= null;
			$TRPPPH 		= null;

			/*DECLARE VARIABLE FAS*/
			$KDFASBM 		= null;
			$KDFASCUK 		= null;
			$KDFASPPN 		= null;
			$KDFASPPH 		= null;
			$KDFASPBM 		= null;
			$FASBM 			= null;
			$FASCUK 		= null;
			$FASPPN 		= null;
			$FASPPH 		= null;
			$FASPBM 		= null;
			$DCIFRP 		= null;

			/*DECLARE VARIABLE OTHER*/
			$BM 			= null;
			$VAL 			= null;
			$VAL_SPECT 		= null;
			$TOTALCUK 		= null;
			$BMBEBAS_A 		= null;
			$CKBEBAS_A 		= null;
			$TOTCUKPLTPITA	= null;
			$TOTCUKTDKDPGT	= null;

			/*VALUE BM*/		
			$BMBAYAR		= null;
			$BMDITGPEM		= null;
			$BMTANGGUH		= null;
			$BMBERKALA		= null;
			$BMBEBAS		= null;

			/*VALUE CT*/
			$CTBAYAR		= null;
			$CTDITGPEM		= null;
			$CTTANGGUH		= null;
			$CTBERKALA		= null;
			$CTBEBAS		= null;

			/*VALUE CMMAE*/
			$CMEBAYAR		= null;
			$CMEDITGPEM 	= null;
			$CMETANGGUH 	= null;
			$CMEBERKALA 	= null;
			$CMEBEBAS 		= null;

			/*VALUE ETHIL ALKOHOL*/
			$CEABAYAR		= null;
			$CEADITGPEM 	= null;
			$CEATANGGUH 	= null;
			$CEABERKALA 	= null;
			$CEABEBAS	 	= null;

			/*PPN*/	
			$PPNBAYAR		= null;
			$PPNDITGPEM 	= null;
			$PPNTANGGUH 	= null;
			$PPNBERKALA 	= null;
			$PPNBEBAS		= null;

			/*PPNBM*/	
			$PBMBAYAR		= null;
			$PBMDITGPEM 	= null;
			$PBMTANGGUH 	= null;
			$PBMBERKALA 	= null;
			$PBMBEBAS		= null;

			/*PPH*/	
			$PPHBAYAR		= null;
			$PPHDITGPEM 	= null;
			$PPHTANGGUH 	= null;
			$PPHBERKALA 	= null;
			$PPHBEBAS		= null;

			/*PNBP*/
			$PNBPBAYAR		= null;
			$PNBPDITGPEM	= null;
			$PNBPTANGGUH	= null;
			$PNBPBERKALA	= null;
			$PNBPBEBAS		= null;



			/*SET KURS*/
	    	$sql = "SELECT NDPBM FROM T_BC23HDR WHERE CAR = '".$car."'";
	    	$NDPBM = getResult($sql)[0]['NDPBM'];


	    	/*KOSONGKAN PUNGUTAN*/
	    	$sql = "SELECT COUNT(*) JMPGT FROM T_BC20PGT WHERE CAR = '".$car."'";
	    	$cekPgt = getResult($sql)[0]['JMPGT'];

	    	if($cekPgt > 0)
	    	{
	    		$sql = "DELETE FROM T_BC20PGT WHERE CAR = ".$car;
	    		delete($sql);
	    	}

	    	if($cekPgt > 0)
	    	{	    		
	    		$dataPNBP = "SELECT * FROM T_BC23PGT WHERE CAR = ".$car." AND KDBEBAN = 8";
	    		$dataPNBP = getResult($sql)[0];

				foreach($dataPNBP as $data)
		    	{
		    		$data = (object)$data;
		    		if($data->KDFASIL == '0') { $PNBPBAYAR		= $data->NILBEBAN; }
					if($data->KDFASIL == '1') { $PNBPDITGPEM	= $data->NILBEBAN; }
					if($data->KDFASIL == '2') { $PNBPTANGGUH	= $data->NILBEBAN; }
					if($data->KDFASIL == '3') { $PNBPBERKALA	= $data->NILBEBAN; }
					if($data->KDFASIL == '4') { $PNBPBEBAS		= $data->NILBEBAN; }
		    	}
				
				$sql = "DELETE FROM T_BC23PGT WHERE CAR = ".$car;
	    		delete($sql);
	    	}

			/*SET PUNGUTAN*/
	    	foreach ($arrDataDtl as $dataDtl)
	    	{
	    		$dataDtl = (object)$dataDtl;

				/*DETAIL*/
				$CAR 		= $dataDtl->CAR;				$SERI 		= $dataDtl->SERIAL; 
				$DCIF 		= $dataDtl->DCIF; 				$SATBMJM 	= $dataDtl->SATBMJM; 
				$KDFASDTL 	= $dataDtl->KDFASDTL; 			$SATCUKJM 	= $dataDtl->SATCUKJM;

				/*FASILITAS*/
				$KDFASBM 	= $dataDtl->KDFASBM; 			$KDFASCUK 	= $dataDtl->KDFASCUK; 
				$KDFASPPN 	= $dataDtl->KDFASPPN; 			$KDFASPPH 	= $dataDtl->KDFASPPH; 
				$KDFASPBM 	= $dataDtl->KDFASPBM; 			$FASBM 		= $dataDtl->FASBM; 
				$FASCUK 	= $dataDtl->FASCUK; 			$FASPPN 	= $dataDtl->FASPPN; 
				$FASPPH 	= $dataDtl->FASPPH; 			$FASPBM 	= $dataDtl->FASPBM;
				
				/*TARIF*/
				$KDTRPBM	= $dataDtl->KDTRPBM; 			$KDSATBM	= $dataDtl->KDSATBM;
				$KDCUK		= $dataDtl->KDCUK; 				$KDTRPCUK	= $dataDtl->KDTRPCUK; 
				$KDSATCUK	= $dataDtl->KDSATCUK; 			$TRPBM		= $dataDtl->TRPBM; 
				$TRPCUK		= $dataDtl->TRPCUK; 			$TRPPPN		= $dataDtl->TRPPPN; 
				$TRPPBM		= $dataDtl->TRPPBM; 			$TRPPPH		= $dataDtl->TRPPPH;

				/*START RESET VARIABLE*/
				/*HEADER*/
				$DCIFRP	= ROUND($DCIF, 2) * $NDPBM;

			    $BM	   			= 0;
			    $BMBAYAR_A 		= 0;
			    $BMDITGPEM_A 	= 0;
			    $BMTANGGUH_A 	= 0;
			    $BMBERKALA_A 	= 0;
			    $BMBEBAS_A		= 0;

			    $CTDITGPEM_A	= 0;
				$CTTANGGUH_A	= 0;
				$CTBERKALA_A	= 0;
				$CTBEBAS_A		= 0;
				$CTBAYAR_A		= 0;
				$CMEDITGPEM_A	= 0;
				$CMETANGGUH_A	= 0;
				$CMEBERKALA_A	= 0;
				$CMEBEBAS_A		= 0;
				$CMEBAYAR_A		= 0;
				$CEADITGPEM_A	= 0;
				$CEATANGGUH_A	= 0;
				$CEABERKALA_A	= 0;
				$CEABEBAS_A		= 0;
				$CEABAYAR_A		= 0;
			    $CUKAIBAYAR 	= 0;
			    $TOTALCUK 		= 0;
			    $CKBEBAS_A 		= 0;
			    // return $DCIFRP;
				/*END RESET VARIABLE*/

				/*JENIS TARIF BM*/
				($KDTRPBM 	== '1') ? $BM 	= $DCIFRP * $TRPBM 	 / 100 : $BM   = $SATBMJM * $TRPBM;

				/*BM*/
				// DCIF * TRPBM
				$VAL = ($BM * $FASBM / 100);
				if($KDFASBM == '1')
				{
					$BMDITGPEM 	 = $BMDITGPEM + ROUND($VAL,2);
					$BMDITGPEM_A = ROUND($VAL,2);
				}
				elseif($KDFASBM == '2')
				{
					$BMTANGGUH 	 = $BMTANGGUH + ROUND($VAL,2);
					$BMTANGGUH_A = ROUND($VAL,2);
				}
				elseif($KDFASBM == '3')
				{
					$BMBERKALA 	 = $BMBERKALA + ROUND($VAL,2);
					$BMBERKALA_A = ROUND($VAL,2);
				}
				elseif($KDFASBM == '4')
				{
					$BMBEBAS   = $BMBEBAS   + ROUND($VAL,2);
					$BMBEBAS_A = ROUND($VAL,2);
				}

				$BMBAYAR   = $BMBAYAR + ROUND(($BM - $VAL),2);
				$BMBAYAR_A = ROUND(($BM - $VAL),2);
		
				$BMTOTAL = $BMBAYAR_A + $BMTANGGUH_A + $BMBERKALA_A;

				/*CUKAI*/
				// (DCIF + BMBAYAR) * TRPCUK
				if($KDTRPCUK == '1')
				{
					$VAL 		= ($DCIFRP + $BMTOTAL) * ($TRPCUK / 100) * ($FASCUK / 100);
					$VAL_SPECT	= ($DCIFRP + $BMTOTAL) * ($TRPCUK / 100);
				}
				else
				{
					$VAL 		= $TRPCUK * $SATCUKJM * ($FASCUK / 100);
					$VAL_SPECT	= $TRPCUK * $SATCUKJM;
				}

				if($KDCUK == '1')
				{
					if($KDFASCUK == '1') 
					{ 
						$CTDITGPEM		= $CTDITGPEM + ROUND($VAL,2);
						$CTDITGPEM_A	= ROUND($VAL,2);
					}
					if($KDFASCUK == '2') 
					{ 
						$CTTANGGUH		= $CTTANGGUH + ROUND($VAL,2);
						$CTTANGGUH_A	= ROUND($VAL,2);
					}
					if($KDFASCUK == '3') 
					{ 
						$CTBERKALA		= $CTBERKALA + ROUND($VAL,2);
						$CTBERKALA_A	= ROUND($VAL,2);
					}
					if($KDFASCUK == '4') 
					{ 
						$CTBEBAS		= $CTBEBAS + ROUND($VAL,2);
						$CTBEBAS_A		= ROUND($VAL,2);
					}

					$CTBAYAR 	= $CTBAYAR + ROUND($VAL_SPECT - $VAL,2);
					$CTBAYAR_A 	= ROUND($VAL_SPECT - $VAL,2);
				}
				elseif($KDCUK == '2')
				{
					if($KDFASCUK == '1') 
					{ 
						$CMEDITGPEM		= $CMEDITGPEM + ROUND($VAL,2);
						$CMEDITGPEM_A	= ROUND($VAL,2);
					}
					if($KDFASCUK == '2') 
					{ 
						$CMETANGGUH		= $CMETANGGUH + ROUND($VAL,2);
						$CMETANGGUH_A	= ROUND($VAL,2);
					}
					if($KDFASCUK == '3') 
					{ 
						$CMEBERKALA		= $CMEBERKALA + ROUND($VAL,2);
						$CMEBERKALA_A	= ROUND($VAL,2);
					}
					if($KDFASCUK == '4') 
					{ 
						$CMEBEBAS		= $CMEBEBAS + ROUND($VAL,2);
						$CMEBEBAS_A		= ROUND($VAL,2);
					}
		            
		            $CMEBAYAR 	= $CMEBAYAR + ROUND($VAL_SPECT - $VAL,2);
		            $CMEBAYAR_A = ROUND($VAL_SPECT - $VAL,2);
				}
				elseif($KDCUK == '3')
				{
	            	if($KDFASCUK == '1') 
	            	{ 
	            		$CEADITGPEM		= $CEADITGPEM + ROUND($VAL,2);
	            		$CEADITGPEM_A	= ROUND($VAL,2);
	            	}
					if($KDFASCUK == '2') 
					{ 
						$CEATANGGUH		= $CEATANGGUH + ROUND($VAL,2);
						$CEATANGGUH_A	= ROUND($VAL,2);
					}
					if($KDFASCUK == '3') 
					{ 
						$CEABERKALA		= $CEABERKALA + ROUND($VAL,2);
						$CEABERKALA_A	= ROUND($VAL,2);
					}
					if($KDFASCUK == '4') 
					{ 
						$CEABEBAS		= $CEABEBAS + ROUND($VAL,2);
						$CEABEBAS_A		= ROUND($VAL,2);
					}
	             	
	             	$CEABAYAR 	= $CEABAYAR + ROUND($VAL_SPECT - $VAL,2);
	             	$CEABAYAR_A = ROUND($VAL_SPECT - $VAL,2);
				}
				/*END CUKAI*/

				$CUKAIBAYAR = $CTBAYAR_A + $CMEBAYAR_A + $CEABAYAR_A;
				// $CUKAIBAYAR_A[] = $CTBAYAR_A + $CMEBAYAR_A + $CEABAYAR_A;
				// $CTBAYAR_B[] = $CTBAYAR_A;

				/*PPN*/
				// (DCIF + BM + CUKAI) * TRPPPN
				$VAL = ($DCIFRP + $BMTOTAL + $CUKAIBAYAR) * ($TRPPPN / 100) * ($FASPPN / 100);
			    if($KDFASPPN == '1') { $PPNDITGPEM = $PPNDITGPEM + ROUND($VAL,2); }
		     	if($KDFASPPN == '2') { $PPNTANGGUH = $PPNTANGGUH + ROUND($VAL,2); }
		     	if($KDFASPPN == '3') { $PPNBERKALA = $PPNBERKALA + ROUND($VAL,2); }
		     	if($KDFASPPN == '4') { $PPNBEBAS   = $PPNBEBAS + ROUND($VAL,2); }
			    // $PPNBAYAR_A[] = ROUND((($DCIFRP + $BMBAYAR_A + $CUKAIBAYAR) * ($TRPPPN / 100)) - $VAL,2);
			    $PPNBAYAR = $PPNBAYAR + ROUND((($DCIFRP + $BMTOTAL + $CUKAIBAYAR) * ($TRPPPN / 100)) - $VAL,2);
			    
			    /*PPNBM*/
			    // (DCIF + BM + CUKAI) * TRPPPNBM
			    $VAL = ($DCIFRP + $BMTOTAL + $CUKAIBAYAR) * ($TRPPBM / 100) * ($FASPBM / 100);
			    if($KDFASPBM == '1') { $PBMDITGPEM = $PBMDITGPEM + ROUND($VAL,2); }
		     	if($KDFASPBM == '2') { $PBMTANGGUH = $PBMTANGGUH + ROUND($VAL,2); }
		     	if($KDFASPBM == '3') { $PBMBERKALA = $PBMBERKALA + ROUND($VAL,2); }
		     	if($KDFASPBM == '4') { $PBMBEBAS   = $PBMBEBAS + ROUND($VAL,2); }
			    $PBMBAYAR = $PBMBAYAR + ROUND((($DCIFRP + $BMTOTAL + $CUKAIBAYAR) * ($TRPPBM / 100)) - $VAL,2);
			    
			    /*PPH*/
			    // (DCIF + BM + CUKAI) * TRPPPH
			    $VAL = ($DCIFRP + $BMTOTAL + $CUKAIBAYAR) * ($TRPPPH / 100) * ($FASPPH / 100);
			    // dd($VAL);
			    if($KDFASPPH == '1') { $PPHDITGPEM = $PPHDITGPEM + ROUND($VAL,2); }
			    if($KDFASPPH == '2') { $PPHTANGGUH = $PPHTANGGUH + ROUND($VAL,2); }
			    if($KDFASPPH == '3') { $PPHBERKALA = $PPHBERKALA + ROUND($VAL,2); }
			    if($KDFASPPH == '4') { $PPHBEBAS   = $PPHBEBAS   + ROUND($VAL,2); }
			    $PPHBAYAR = $PPHBAYAR + ROUND((($DCIFRP + $BMTOTAL + $CUKAIBAYAR) * ($TRPPPH / 100)) - $VAL,2);

	    	}

	    	/*END FOREACH*/
			    // dd($CUKAIBAYAR_A, $CTBAYAR_B);

	    	/*BULATKAN KE RIBUAN*/
	    	$BMBAYAR 		= ceiling($BMBAYAR,1000);
	    	$BMDITGPEM 		= ceiling($BMDITGPEM,1000);				$BMTANGGUH 		= ceiling($BMTANGGUH,1000);
	    	$BMBERKALA 		= ceiling($BMBERKALA,1000);				$BMBEBAS 		= ceiling($BMBEBAS,1000);

			#========================================================

	    	$PPNBAYAR 		= ceiling($PPNBAYAR,1000);
	    	$PPNDITGPEM 	= ceiling($PPNDITGPEM,1000);			$PPNTANGGUH 	= ceiling($PPNTANGGUH,1000);
	    	$PPNBERKALA 	= ceiling($PPNBERKALA,1000);			$PPNBEBAS 		= ceiling($PPNBEBAS,1000);

	    	$PPHBAYAR 		= ceiling($PPHBAYAR,1000);
	    	$PPHDITGPEM 	= ceiling($PPHDITGPEM,1000);			$PPHTANGGUH 	= ceiling($PPHTANGGUH,1000);
	    	$PPHBERKALA 	= ceiling($PPHBERKALA,1000);			$PPHBEBAS 		= ceiling($PPHBEBAS,1000);

	    	$CEABAYAR		= ceiling($CEABAYAR,1000);
	    	$CEADITGPEM		= ceiling($CEADITGPEM,1000);
	    	$CEATANGGUH		= ceiling($CEATANGGUH,1000);
	    	$CEABERKALA		= ceiling($CEABERKALA,1000);
	    	$CEABEBAS		= ceiling($CEABEBAS,1000);

	    	$CMEBAYAR		= ceiling($CMEBAYAR,1000);
	    	$CMEDITGPEM		= ceiling($CMEDITGPEM,1000);
	    	$CMETANGGUH		= ceiling($CMETANGGUH,1000);
	    	$CMEBERKALA		= ceiling($CMEBERKALA,1000);
	    	$CMEBEBAS		= ceiling($CMEBEBAS,1000);

	    	$CTBAYAR		= ceiling($CTBAYAR,1000);
	    	$CTDITGPEM		= ceiling($CTDITGPEM,1000);
	    	$CTTANGGUH		= ceiling($CTTANGGUH,1000);
	    	$CTBERKALA		= ceiling($CTBERKALA,1000);
	    	$CTBEBAS		= ceiling($CTBEBAS,1000);

			#========================================================
			$TOTALCUK		= $CEABAYAR + $CMEBAYAR + $CTBAYAR;
			$TOTCUKDITGPEM 	= $CEADITGPEM + $CMEDITGPEM + $CTDITGPEM;
			$TOTCUKTANGGUH 	= $CEATANGGUH + $CMETANGGUH + $CTTANGGUH;
			$TOTCUKBERKALA 	= $CEABERKALA + $CMEBERKALA + $CTBERKALA;
			$TOTCUKBEBAS 	= $CEABEBAS + $CMEBEBAS + $CTBEBAS;

	    	$TOTALCUK		= ceiling($TOTALCUK,1000);
	    	$TOTCUKDITGPEM 	= ceiling($TOTCUKDITGPEM,1000);
	    	$TOTCUKTANGGUH 	= ceiling($TOTCUKTANGGUH,1000);
	    	$TOTCUKBERKALA 	= ceiling($TOTCUKBERKALA,1000);
	    	$TOTCUKBEBAS 	= ceiling($TOTCUKBEBAS,1000);
	    	#========================================================

			// return array($PPHBAYAR, $PPNBAYAR, $TOTCUKTDKDPGT);

	    	/*INSERT INTO DB*/
	    	/*INSERT BM*/
		    if($BMBAYAR			> 0){ insertRefernce('T_BC20PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '1','KDFASIL' => '0','NILBEBAN' => $BMBAYAR]); }
		    if($BMBAYAR			> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '1','KDFASIL' => '0','NILBEBAN' => $BMBAYAR]); }
		    if($BMDITGPEM		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '1','KDFASIL' => '1','NILBEBAN' => $BMDITGPEM]); }
		    if($BMTANGGUH		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '1','KDFASIL' => '2','NILBEBAN' => $BMTANGGUH]); }
		    if($BMBERKALA		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '1','KDFASIL' => '3','NILBEBAN' => $BMBERKALA]); }
		    if($BMBEBAS			> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '1','KDFASIL' => '4','NILBEBAN' => $BMBEBAS]); }

		    /*INSERT CEA*/
		    if($CEABAYAR		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '7','KDFASIL' => '0','NILBEBAN' => $CEABAYAR]); }
		    if($CEADITGPEM		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '7','KDFASIL' => '1','NILBEBAN' => $CEADITGPEM]); }
		    if($CEATANGGUH		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '7','KDFASIL' => '2','NILBEBAN' => $CEATANGGUH]); }
		    if($CEABERKALA		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '7','KDFASIL' => '3','NILBEBAN' => $CEABERKALA]); }
		    if($CEABEBAS		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '7','KDFASIL' => '4','NILBEBAN' => $CEABEBAS]); }

		    /*INSERT CME*/
		    if($CMEBAYAR		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '6','KDFASIL' => '0','NILBEBAN' => $CMEBAYAR]); }
		    if($CMEDITGPEM		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '6','KDFASIL' => '1','NILBEBAN' => $CMEDITGPEM]); }
		    if($CMETANGGUH		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '6','KDFASIL' => '2','NILBEBAN' => $CMETANGGUH]); }
		    if($CMEBERKALA		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '6','KDFASIL' => '3','NILBEBAN' => $CMEBERKALA]); }
		    if($CMEBEBAS		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '6','KDFASIL' => '4','NILBEBAN' => $CMEBEBAS]); }

		    /*INSERT CEA*/
		    if($CTBAYAR			> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '5','KDFASIL' => '0','NILBEBAN' => $CTBAYAR]); }
		    if($CTDITGPEM		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '5','KDFASIL' => '1','NILBEBAN' => $CTDITGPEM]); }
		    if($CTTANGGUH		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '5','KDFASIL' => '2','NILBEBAN' => $CTTANGGUH]); }
		    if($CTBERKALA		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '5','KDFASIL' => '3','NILBEBAN' => $CTBERKALA]); }
		    if($CTBEBAS			> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '5','KDFASIL' => '4','NILBEBAN' => $CTBEBAS]); }

		    /*INSERT PPN*/
		    if($PPNBAYAR		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '2','KDFASIL' => '0','NILBEBAN' => $PPNBAYAR]); }
		    if($PPNDITGPEM		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '2','KDFASIL' => '1','NILBEBAN' => $PPNDITGPEM]); }
		    if($PPNTANGGUH		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '2','KDFASIL' => '2','NILBEBAN' => $PPNTANGGUH]); }
		    if($PPNBERKALA		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '2','KDFASIL' => '3','NILBEBAN' => $PPNBERKALA]); }
		    if($PPNBEBAS		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '2','KDFASIL' => '4','NILBEBAN' => $PPNBEBAS]); }

		    /*INSERT PPNBM*/
		    if($PBMBAYAR		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '3','KDFASIL' => '0','NILBEBAN' => $PBMBAYAR]); }
		    if($PBMDITGPEM		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '3','KDFASIL' => '1','NILBEBAN' => $PBMDITGPEM]); }
		    if($PBMTANGGUH		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '3','KDFASIL' => '2','NILBEBAN' => $PBMTANGGUH]); }
		    if($PBMBERKALA		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '3','KDFASIL' => '3','NILBEBAN' => $PBMBERKALA]); }
		    if($PBMBEBAS		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '3','KDFASIL' => '4','NILBEBAN' => $PBMBEBAS]); }

		    /*INSERT PPH*/
		    if($PPHBAYAR		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '4','KDFASIL' => '0','NILBEBAN' => $PPHBAYAR]); }
		    if($PPHDITGPEM		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '4','KDFASIL' => '1','NILBEBAN' => $PPHDITGPEM]); }
		    if($PPHTANGGUH		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '4','KDFASIL' => '2','NILBEBAN' => $PPHTANGGUH]); }
		    if($PPHBERKALA		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '4','KDFASIL' => '3','NILBEBAN' => $PPHBERKALA]); }
		    if($PPHBEBAS		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '4','KDFASIL' => '4','NILBEBAN' => $PPHBEBAS]); }

		     /*INSERT PNBP*/
		    if($PNBPBAYAR		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '8','KDFASIL' => '0','NILBEBAN' => $PNBPBAYAR]); }
		    if($PNBPDITGPEM		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '8','KDFASIL' => '1','NILBEBAN' => $PNBPDITGPEM]); }
		    if($PNBPTANGGUH		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '8','KDFASIL' => '2','NILBEBAN' => $PNBPTANGGUH]); }
		    if($PNBPBERKALA		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '8','KDFASIL' => '3','NILBEBAN' => $PNBPBERKALA]); }
		    if($PNBPBEBAS		> 0){ insertRefernce('T_BC23PGT', ['KODE_TRADER' => $kode_trader,'CAR' => $car,'KDBEBAN' => '8','KDFASIL' => '4','NILBEBAN' => $PNBPBEBAS]); }

	    	return "success";
	    }
	    else
	    {
	    	return "failed";
	    }
	    
    }