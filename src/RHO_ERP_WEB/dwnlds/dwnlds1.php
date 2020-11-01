<?php

require '../app_code/cmncde/globals.php';
require '../app_code/cmncde/admin_funcs.php';
//require '../app_code/epay/epay_intro.php';

//$dwnldfile = $_GET['q'];

/*$filename = $_GET['q'];
$filename = $_GET['q'];
$filename = $_GET['q'];
$filename = $_GET['q'];
$filename = $_GET['q'];
$filename = $_GET['q'];

$error=false;
// required for IE, otherwise Content-disposition is ignored
if (ini_get('zlib.output_compression')) {
    ini_set('zlib.output_compression', 'Off');
}

// addition by Jorg Weske
$file_extension = strtolower(substr(strrchr($filename, "."), 1));

if ($filename == "") {
    $error = true;
    //exit($error);
} elseif (!file_exists($filename)) {
    $error = true;
    //exit($error);
}

if($error==false)
{
switch ($file_extension) {
    case "pdf": $ctype = "application/pdf";
        break;
    case "exe": $ctype = "application/octet-stream";
        break;
    case "zip": $ctype = "application/zip";
        break;
    case "doc": 
    case "docx": $ctype = "application/msword";
        break;
    case "xls": 
    case "xlsx": $ctype = "application/vnd.ms-excel";
        break;
    case "ppt":
    case "pptx": $ctype = "application/vnd.ms-powerpoint";
        break;
    case "gif": $ctype = "image/gif";
        break;
    case "png": $ctype = "image/png";
        break;
    case "ico": $ctype = "image/ico";
        break;
    case "jpeg":
    case "jpg": $ctype = "image/jpg";
        break;
    default: $ctype = "application/octet-stream";
        // header('Content-Type: application/force-download');
        //binary
}
header("Pragma: public"); // required
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private", false); // required for certain browsers 
header('Content-Description: File Transfer');
header("Content-Type: $ctype");
// change, added quotes to allow spaces in filenames, by Rajkumar Singh
header("Content-Disposition: attachment; filename=\"" . basename($filename) . "\";");
header("Content-Transfer-Encoding: binary");
header("Content-Length: " . filesize($filename));
readfile("$filename");
exit();
}
else
{
    echo "File not Available!";
}*/


  // Original PHP code by Chirp Internet: www.chirp.com.au
  // Please acknowledge use of this code by including this header.

  /*$data = array(
    array("firstname" => "Mary", "lastname" => "Johnson", "age" => 25),
    array("firstname" => "Amanda", "lastname" => "Miller", "age" => 18),
    array("firstname" => "James", "lastname" => "Brown", "age" => 31),
    array("firstname" => "Patricia", "lastname" => "Williams", "age" => 7),
    array("firstname" => "Michael", "lastname" => "Davis", "age" => 43),
    array("firstname" => "Sarah", "lastname" => "Miller", "age" => 24),
    array("firstname" => "Patrick", "lastname" => "Miller", "age" => 27)
  );

  function cleanData(&$str)
  {
    if($str == 't') $str = 'TRUE';
    if($str == 'f') $str = 'FALSE';
    if(preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
      $str = "'$str";
    }
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
  }

  // filename for download
  $filename = "website_data_" . date('Ymd') . ".csv";

  header("Content-Disposition: attachment; filename=\"$filename\"");
  header("Content-Type: text/csv");

  $out = fopen("php://output", 'w');

  $flag = false;
  foreach($data as $row) {
    if(!$flag) {
      // display field/column names as first row
      fputcsv($out, array_keys($row), ',', '"');
      $flag = true;
    }
    array_walk($row, 'cleanData');
    fputcsv($out, array_values($row), ',', '"');
  }

  fclose($out);
  exit;*/

$vwtyp = "0";
$qstr = "";
$dsply = "";
$actyp = "";
$srctyp = "";
$hdrid = "";
$result = "";
$count = 0;

if (isset($_GET['q'])) {
    $qstr = cleanInputData($_GET['q']);
}
if (isset($_GET['actyp'])) {
    $actyp = cleanInputData($_GET['actyp']);
}
if (isset($_GET['hdrid'])) {
    $hdrid = cleanInputData($_GET['hdrid']);
}
if (isset($_GET['recno'])) {
    $count = cleanInputData($_GET['recno']);
}

if($count == 1){
    
  $data = array(
    array("customer_name" => "Kwesi Watson", "mobile_number" => "0299258974", "outstanding_balance" => 25000),
    array("customer_name" => "Delight Ametefe", "mobile_number" => "0599875421", "outstanding_balance" => 85000.52),
  );

  // filename for download
  $filename = "template_" . date('Ymd') . ".csv";

  header("Content-Disposition: attachment; filename=\"$filename\"");
  header("Content-Type: text/csv");

  $out = fopen("php://output", 'w');

  $flag = false;
  foreach($data as $row) {
    if(!$flag) {
      // display field/column names as first row
      fputcsv($out, array_keys($row), ',', '"');
      $flag = true;
    }
    array_walk($row, 'cleanData');
    fputcsv($out, array_values($row), ',', '"');
  }

  fclose($out);
  exit;    
    
} else if($count == 2){
    $count = 100;
}

  // Original PHP code by Chirp Internet: www.chirp.com.au
  // Please acknowledge use of this code by including this header.

  if ($actyp == 1){    
      $result = get_Psb81_Mintier_Trns_Export($hdrid, $count);
  } else if ($actyp == 2){
      $result = get_Psb82_Medtier_Trns_Export($hdrid, $count);                    
  } else if ($actyp == 3){
      $result = get_Psb83_Ehntier_Trns_Export($hdrid, $count);                    
  }

  /*function cleanData(&$str)
  {
    if($str == 't') $str = 'TRUE';
    if($str == 'f') $str = 'FALSE';
    if(preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
      $str = "'$str";
    }
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
  }*/

  // filename for download
  $filename = "website_data_" . date('Ymd') . ".csv";

  header("Content-Disposition: attachment; filename=\"$filename\"");
  header("Content-Type: text/csv");

  $out = fopen("php://output", 'w');

  $flag = false;
  //$result = pg_query("SELECT * FROM table ORDER BY field") or die('Query failed!');
  
  while ($row = pg_fetch_assoc($result)) {//loc_db_fetch_array
    if(!$flag) {
      // display field/column names as first row
      fputcsv($out, array_keys($row), ',', '"');
      $flag = true;
    }
    array_walk($row, 'cleanData');
    fputcsv($out, array_values($row), ',', '"');
  }
  
  fclose($out);
  exit;
  
  function get_Psb81_Mintier_Trns_Export($p_hdrid, $count) {
    //global $usrID;
    $hdrid = -1;
    
    if ($p_hdrid > 0){
        $hdrid = $p_hdrid;
    }  

    $sqlStr = "SELECT customer_name, mobile_number, outstanding_balance 
       FROM epay.epay_psb8_mintier_trns WHERE psb_hdr_id = ".$hdrid. " ORDER BY outstanding_balance desc limit ".$count;

    $result = executeSQLNoParams($sqlStr);
    return $result;
}
  
  function get_Psb82_Medtier_Trns_Export($p_hdrid, $count) {
    //global $usrID;
    $hdrid = -1;
    
    if ($p_hdrid > 0){
        $hdrid = $p_hdrid;
    }  

    $sqlStr = "SELECT customer_name, mobile_number, outstanding_balance 
       FROM epay.epay_psb8_medtier_trns WHERE psb_hdr_id = ".$hdrid. " ORDER BY outstanding_balance desc limit ".$count;

    $result = executeSQLNoParams($sqlStr);
    return $result;
}
  
  function get_Psb83_Ehntier_Trns_Export($p_hdrid, $count) {
    //global $usrID;
    $hdrid = -1;
    
    if ($p_hdrid > 0){
        $hdrid = $p_hdrid;
    }  

    $sqlStr = "SELECT customer_name, mobile_number, outstanding_balance 
       FROM epay.epay_psb8_ehntier_trns WHERE psb_hdr_id = ".$hdrid. " ORDER BY outstanding_balance desc limit ".$count;

    $result = executeSQLNoParams($sqlStr);
    return $result;
}

  function cleanData(&$str)
  {
    if($str == 't') $str = 'TRUE';
    if($str == 'f') $str = 'FALSE';
    if(preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
      $str = "'$str";
    }
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
  }

?>