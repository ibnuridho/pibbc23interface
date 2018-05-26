<?php
error_reporting(E_ALL & ~E_NOTICE);
libxml_use_internal_errors(true);

function insertRefernce($table, $arrData, $allField = false, $set_last_id = ""){
    global $db;

    $arrData = array_change_key_case($arrData,CASE_UPPER);
    $arrData = array_map('trim', $arrData);
    $arrData = array_map('strtoupper', $arrData);
    $sqlTBL = 'SHOW FIELDS FROM ' . $table;
    $datTBL = $db->query($sqlTBL);
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
                        $data[$aTBL['FIELD']] = floatval($data[$aTBL['FIELD']]);
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
    $datCek = $db->query($sqlCek);
    if ($datCek->num_rows == 0) {
        $columns = implode(", ",array_keys($data));
        $values  = implode(", ", array_values($data));

        $sqlIns = "INSERT INTO $table ($columns) VALUES ($values)";
        $exec = $db->query($sqlIns);
        if($set_last_id != "") $last_id = $db->insert_id;
        
    } else {
        // $data = array_diff($data, $whrTBL);
        foreach($data as $key => $value) $dataUpd[] = "$key=$value";
        $dataUpd = implode(", ", $dataUpd);

        $sqlUpd = "UPDATE $table SET $dataUpd WHERE " . $dataWhere;
        // return $sqlUpd;
        $exec = $db->query($sqlUpd);
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