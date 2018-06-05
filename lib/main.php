<?php
error_reporting(E_ALL & ~E_NOTICE);
libxml_use_internal_errors(true);

function insertRefernce($table, $arrData, $set_db = "LOCAL", $allField = false, $set_last_id = ""){
    global $db;
    global $db_tpb;

    $arrData = array_change_key_case($arrData,CASE_UPPER);
    $arrData = array_map('trim', $arrData);
    $arrData = array_map('strtoupper', $arrData);
    $sqlTBL = 'SHOW FIELDS FROM ' . $table;

    switch ($set_db) {
        case 'TPB': $datTBL = $db_tpb->query($sqlTBL); break;
        default: $datTBL = $db->query($sqlTBL); break;
    }

    while($row = $datTBL->fetch_assoc()){
        $arrTBL[] = $row;
    }
    // return $arrTBL;
    $whrTBL = [];
    $data   = [];
    foreach ($arrTBL as $aTBL) {
        $aTBL = array_change_key_case($aTBL, CASE_UPPER);
        $aTBL = array_map('strtoupper' , $aTBL);
        $trueData = false;
        if ($aTBL['EXTRA'] != 'AUTO_INCREMENT') {
            if($allField){
                $data[$aTBL['FIELD']] = "'" . str_replace("'", "\'", $arrData[$aTBL['FIELD']]) . "'";
                $trueData = true;
            }else{
                if(isset($arrData[$aTBL['FIELD']])){
                    $data[$aTBL['FIELD']] = "'" . str_replace("'", "\'", $arrData[$aTBL['FIELD']]) . "'";
                    // return $data[$aTBL['FIELD']];
                    $trueData = true;
                }
            }
            if($trueData){
                $type = explode('(', $aTBL['TYPE']);
                switch ($type[0]){
                    case'DATE':
                        $strDate = $arrData[$aTBL['FIELD']];
                        if(strlen($strDate) == 8){
                            $date = [ substr($strDate, 0, 4), substr($strDate, 4, 2), substr($strDate, 6, 2) ];
                        }else{
                            $date = explode('-', $strDate);
                        }
                        if(checkdate($date[1], $date[2], $date[0])){
                            $data[$aTBL['FIELD']] = "'" . $date[0] . '-' . $date[1] . '-' . $date[2] . "'";
                        }else{
                            $data[$aTBL['FIELD']] = "'" . '0000-00-00' . "'";
                        }
                        
                        break;
                    case 'DATETIME':
                        list($date_, $time_) = explode(' ', $arrData[$aTBL['FIELD']]);
                        $date = explode('-', $date_);
                        if(checkdate($date[1], $date[0], $date[2])){
                            $data[$aTBL['FIELD']] = "'" . $date[2] . '-' . $date[1] . '-' . $date[0] . ' ' . $time_ . "'";
                        }else{
                            // $data[$aTBL['FIELD']] = 'NULL';
                            $data[$aTBL['FIELD']] = "'" . '0000-00-00' . "'";
                        }
                        break;
                    case'DOUBLE':
                        // $data[$aTBL['FIELD']] = floatval($data[$aTBL['FIELD']]);
                        break;
                    case'INT':
                        if($arrData[$aTBL['FIELD']] == ''){
                            $data[$aTBL['FIELD']] = 0;
                        }else{
                            $data[$aTBL['FIELD']] = str_replace(',', '', $arrData[$aTBL['FIELD']]);
                        }
                            
                        break;
                }
            }
        }
        #find key
        if ($aTBL['KEY'] == 'PRI') {
            $whrTBL[$aTBL['FIELD']] = $arrData[$aTBL['FIELD']];
            if($trueData){
                $whrTBL[$aTBL['FIELD']] = $data[$aTBL['FIELD']];
            }
        }

    }

    foreach($whrTBL as $key => $value) $dataWhere[] = "$key = $value";
    $dataWhere = implode(" AND ", $dataWhere);
    $last_id = 0;

    $sqlCek = "SELECT * FROM $table WHERE " . $dataWhere;
    switch ($set_db) {
        case 'TPB': $datCek = $db_tpb->query($sqlCek); break;
        default: $datCek = $db->query($sqlCek); break;
    }
    if ($datCek->num_rows == 0) {
        $columns = implode(", ",array_keys($data));
        $values  = implode(", ", array_values($data));

        $sqlIns = "INSERT INTO $table ($columns) VALUES ($values)";
        switch ($set_db) {
            case 'TPB': 
                $exec = $db_tpb->query($sqlIns);
                if($set_last_id != "") $last_id = $db_tpb->insert_id;
                break;
            default: 
                $exec = $db->query($sqlIns);
                if($set_last_id != "") $last_id = $db->insert_id;
                break;
        }   
        
    } else {
        // $data = array_diff($data, $whrTBL);
        foreach($data as $key => $value) $dataUpd[] = "$key=$value";
        $dataUpd = implode(", ", $dataUpd);

        $sqlUpd = "UPDATE $table SET $dataUpd WHERE " . $dataWhere;
        // return $sqlUpd;
        switch ($set_db) {
            case 'TPB': $exec = $db_tpb->query($sqlUpd); break;
            default: $exec = $db->query($sqlUpd); break;
        }
        $last_id = "";
    }
    
    if($exec){
        return [$table => true, "last_id" => $last_id];
    }else{
        return [$table => false, "last_id" => $last_id];
    }
}

function set_logs($jns_dok, $car, $log_desc, $log_by){
    $data = [
        'JNS_DOK' => $jns_dok, 
        'CAR' => $car, 
        'LOG_DESC' => $log_desc, 
        'LOG_BY' => $log_by
    ];

    $insertData = insertRefernce('t_dok_log', $data);
}

function fixLen($str, $len, $chr = ' ', $alg = STR_PAD_RIGHT) {
    $hasil = str_pad(substr($str, 0, $len), $len, $chr, $alg);
    return $hasil;
}

function getResult($sql)
{
    global $db;
    
    $result = $db->query($sql);
    $data = [];

    if($result->num_rows > 0)
    {
        while ($row = $result->fetch_assoc())
        {
            $data[] = $row;
        }   
    }
    
    return $data;
}

function delete($sql)
{
    global $db;
    
    $result = $db->query($sql);
    return $result;
}

function findArr2Str($str,$arr){
    $hasil = false;
    foreach($arr as $a){
        if(strstr($str,$a)){
            $hasil = true;
            break;
        }
    }
    return $hasil;
}

function getdata($type, $keyword)
{
    global $db;
    
    switch ($type) {
        case 'pelabuhan':
            $sql = "SELECT URAIAN FROM TM_PELABUHAN WHERE KODE = '".$keyword."'";
            break;
        default:
            # code...
            break;
    }

    $result = $db->query($sql);

    $data = [];

    if($result->num_rows > 0)
    {
        while ($row = $result->fetch_assoc())
        {
            $data[] = $row;
        }   
    }
    
    return $data;
}

function sqlUpdate($table, $data, $whrTBL)
{
    global $db;

    foreach($whrTBL as $key => $value) $dataWhere[] = "$key = $value";
    $dataWhere = implode(" AND ", $dataWhere);
    
    foreach($data as $key => $value) $dataUpd[] = "$key=$value";
    $dataUpd = implode(", ", $dataUpd);

    $sqlUpd = "UPDATE $table SET $dataUpd WHERE " . $dataWhere;
    $exec = $db->query($sqlUpd);
    
    if($exec){
        return [$table => true];
    }else{
        return [$table => false];
    }
}

function ceiling($number, $significance = 1)
{
    return ( is_numeric($number) && is_numeric($significance) ) ? (ceil($number/$significance)*$significance) : false;
}

function strtotimecustom($strdate) {
    switch($strdate)
    {
        case '':
        case '0000-00-00':
        case '00-00-0000':
            return "-";
        break;
        default:
            return date('d-m-Y', strtotime($strdate));
        break;
    }
}

function trimstr($strpotong, $panjang) {
    $hsl = '';
    $strpotong = trim($strpotong);
    $len = strlen($strpotong);
    $str = 0;
    while ($str < $len) {
        $hsl[] = substr($strpotong, $str, $panjang);
        $str += $panjang;
    }
    return $hsl;
}

function formatNPWP($npwp) {
    $strlen = strlen($npwp);
    if ($strlen == 15) {
        $npwpnya = substr($npwp, 0, 2) . "." . substr($npwp, 2, 3) . "." . substr($npwp, 5, 3) . "." . substr($npwp, 8, 1) . "-" . substr($npwp, 9, 3) . "." . substr($npwp, 12, 3);
    } else if ($strlen == 12) {
        $npwpnya = substr($npwp, 0, 2) . "." . substr($npwp, 2, 3) . "." . substr($npwp, 5, 3) . "." . substr($npwp, 8, 1) . "-" . substr($npwp, 9, 3);
    } else {
        $npwpnya = $npwp;
    }
    return $npwpnya;
}

function setnocont($nocont) {
    if($nocont!=''){
        $rtn = substr($nocont, 0, 4) . '-' . substr($nocont, 4, 11);
    }else{
        $rtn = '';
    }
    return $rtn;
}

function formaths($hs) {
    if(strlen($hs) > 8){
        $formaths = substr($hs, 0, 4) . '.' . substr($hs, 4, 2) . '.' . substr($hs, 6, 2) . '.' . substr($hs, 8, 2);
    } else {
        $formaths = substr($hs, 0, 4) . '.' . substr($hs, 4, 2) . '.' . substr($hs, 6, 2);
    }
    return $formaths;
}

function strip($strstrip) {
    if (trim($strstrip) != 0) {
        $hasile = $strstrip . '%';
    } else {
        $hasile = ' - ';
    }
    return $hasile;
}

function showdok($jnDok, $arrDok) {//'380|365|861'
    $hasil = array();
    if(!is_array($arrDok)) return $hasil;
    foreach ($arrDok as $a) {
        if (strstr($jnDok, $a['DOKKD'])) {
            $hasil['NO'][] = $a['DOKNO'];
            $hasil['TG'][] = $a['DOKTG'];
        }
    }
    return $hasil;
}

function formatdokarr($arr) {
    $hasil = '';
    foreach ($arr as $a) {
        $hasil[$a['DOKKD']]['NO'][] = $a['DOKNO'];
        $hasil[$a['DOKKD']]['TG'][] = $a['DOKTG'];
    }
    return $hasil;
}

function getfas($kodefas) {
    switch ($kodefas) {
        case 1:
            $kdfaspbm_nama = 'DTP';
            break;
        case 2:
            $kdfaspbm_nama = 'DTG';
            break;
        case 3:
            $kdfaspbm_nama = 'BKL';
            break;
        case 4:
            $kdfaspbm_nama = 'BBS';
            break;
    }
    return $kdfaspbm_nama;
}

function terbilang($bilangan) {
    $angka = array('0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0');
    $kata = array('', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan');
    $tingkat = array('', 'ribu', 'juta', 'milyar', 'triliun');
    $panjang_bilangan = strlen($bilangan);

    if ($panjang_bilangan > 15) {
        $kalimat = "Diluar Batas";
        return $kalimat;
    }

    /* mengambil angka-angka yang ada dalam bilangan, dimasukkan ke dalam array */
    for ($i = 1; $i <= $panjang_bilangan; $i++) {
        $angka[$i] = substr($bilangan, -($i), 1);
    }

    $i = 1;
    $j = 0;
    $kalimat = "";

    /* mulai proses iterasi terhadap array angka */
    while ($i <= $panjang_bilangan) {
        $subkalimat = "";
        $kata1 = "";
        $kata2 = "";
        $kata3 = "";

        /* untuk ratusan */
        if ($angka[$i + 2] != "0") {
            if ($angka[$i + 2] == "1") {
                $kata1 = "seratus";
            } else {
                $kata1 = $kata[$angka[$i + 2]] . " ratus";
            }
        }

        /* untuk puluhan atau belasan */
        if ($angka[$i + 1] != "0") {
            if ($angka[$i + 1] == "1") {
                if ($angka[$i] == "0") {
                    $kata2 = "sepuluh";
                } elseif ($angka[$i] == "1") {
                    $kata2 = "sebelas";
                } else {
                    $kata2 = $kata[$angka[$i]] . " belas";
                }
            } else {
                $kata2 = $kata[$angka[$i + 1]] . " puluh";
            }
        }

        /* untuk satuan */
        if ($angka[$i] != "0") {
            if ($angka[$i + 1] != "1") {
                $kata3 = $kata[$angka[$i]];
            }
        }

        /* pengujian angka apakah tidak nol semua, lalu ditambahkan tingkat */
        if (($angka[$i] != "0") OR ( $angka[$i + 1] != "0") OR ( $angka[$i + 2] != "0")) {
            $subkalimat = "$kata1 $kata2 $kata3 " . $tingkat[$j] . " ";
        }

        /* gabungkan variabel sub kalimat (untuk satu blok 3 angka) ke variabel kalimat */
        $kalimat = $subkalimat . $kalimat;
        $i = $i + 3;
        $j = $j + 1;
    }
    /* mengganti satu ribu jadi seribu jika diperlukan */
    if (($angka[5] == "0") AND ( $angka[6] == "0")) {
        $kalimat = str_replace("satu ribu", "seribu", $kalimat);
    }
    return trim($kalimat);
}

function formattglmysql($tglMysql){
    $arr = explode('-', $tglMysql);
    return $arr[2].' '.$this->bulan[(int)$arr[1]].' '.$arr[0];
}

function formatcar($CAR){
    //$arr = explode('-', $tglMysql);
    if(strlen($CAR) < 1) return "";
    return substr($CAR, 0,6).'-'.substr($CAR, 6,6).'-'.substr($CAR, 12,8).'-'.substr($CAR, 20,6);
}

function showmsg($issuccess = true, $callbackurl = '', $message, $callbackurl2 = '', $containercallback2 = '', $containercallback1 = '')
{
    if($callbackurl2 != '')
        $returnmsg = "MSG|".($issuccess ? "OK" : "ER")."|".$callbackurl."|".$message."|".$callbackurl2."|".$containercallback2."|".$containercallback1;
    else
        $returnmsg = "MSG|".($issuccess ? "OK" : "ER")."|".$callbackurl."|".$message;
    return $returnmsg;
}

function FormatRupiah($angka,$decimal){
    $rupiah=number_format($angka,$decimal,'.',',');     
    return $rupiah;
}

function tanggal_periode($tglmin, $tglmax)
{       
    $bulan = array (1 =>   'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember'
            );
     $tmin       = explode('-', $tglmin);
     $tmax       = explode('-', $tglmax);
    
     if($tglmin==$tglmax){
        $tgl_periode ='periode ' .$tmin[2] . ' ' . $bulan[ (int)$tmin[1] ] . ' ' . $tmin[0];
     }
     elseif($tmin[0]==$tmax[0]){
        $tgl_periode ='periode ' .$tmin[2] . ' ' . $bulan[ (int)$tmin[1] ] . ' S/D '.$tmax[2] . ' ' . $bulan[ (int)$tmax[1] ] . ' ' . $tmax[0];
     }
     else{
         $tgl_periode ='periode ' .$tmin[2] . ' ' . $bulan[ (int)$tmin[1] ] . ' ' . $tmin[0]. ' S/D '.$tmax[2] . ' ' . $bulan[ (int)$tmax[1] ] . ' ' . $tmax[0];
     }
    
    return $tgl_periode;
}

function dd($data)
{
    print_r($data);
    exit();
}