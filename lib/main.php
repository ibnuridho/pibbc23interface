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

function sqlUpdate($table, $data, $dataWhere)
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