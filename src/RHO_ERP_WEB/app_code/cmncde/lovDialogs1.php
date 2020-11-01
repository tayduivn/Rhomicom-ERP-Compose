<?php

require 'admin_funcs.php';

$isAjx = (int) $_POST['ajx'];
if ($isAjx !== 1) {
    header('location: ../../index.php');
}
$qryStr = cleanInputData($_POST['q']);
$usrID = $_SESSION['USRID'];

function lovsForm($error) {
    $srchFor = $_POST['searchfor'];
    $srchIn = $_POST['searchin'];
    $lmtSze = $_POST['dsplySze'];
    $navPos = $_POST['nv'];
    $curIdx = $_POST['idx'];
    $chkOrRadio = $_POST['chkOrRadio'];
    $mustSelSth = $_POST['mustSelSth'];
    $lov_res_action_nm = $_POST['lov_res_action_nm'];
    $selvals = $_POST['selvals'];
    $srcTyp = $_POST['srcTyp'];
    $showValOrDesc = $_POST['showValOrDesc'];
    $lovNm = $_POST['lovNm'];
    $criteriaID = $_POST['criteriaID'];
    $criteriaID2 = $_POST['criteriaID2'];
    $criteriaID3 = $_POST['criteriaID3'];
    $valueElmntID = $_POST['valElmntID'];
    $descElemntID = $_POST['descElmntID'];
    $lovID = getLovID($lovNm);
    $sqlMsg = "";
    $isDynmc = "";
    $selvals = "|" . $selvals;

    if ($srchFor == "" ||
            isset($srchFor) == FALSE) {
        $srchFor = "%";
    }
    $result = getLovValues($srchFor, $srchIn, $curIdx, $lmtSze, $sqlMsg, $lovID, $isDynmc, $criteriaID, $criteriaID2, $criteriaID3);
    $recCnt = loc_db_num_rows($result);
    $startIdx = 0;
    $endIdx = 0;
    if ($recCnt > 0) {
        $startIdx = ($curIdx * $lmtSze) + 1;
        $endIdx = $startIdx + $recCnt - 1;
    }
    $output = "<!-- Form Code Start -->
<div id='rho_form'>
<form id='lovForm' action='' method='post' accept-charset='UTF-8'>
<fieldset >
<legend>$lovNm</legend>
<input type='hidden' name='submitted' id='submitted' value='1'/>";
    $srchInArry = array("Value", "Description");
    $output.=getLovNavBar($srchInArry, $srchIn, $recCnt, $navPos, $lmtSze, $startIdx, $endIdx, $srchFor, "getLovsPage1", "nav_lovs", $lovNm, "mydialog2", $criteriaID, $criteriaID2, $criteriaID3, $chkOrRadio, $mustSelSth, $lov_res_action_nm, $selvals, $valueElmntID, $descElemntID, $srcTyp);
//onclick=\"getLovsPage('nav_lovs', 'first', 'Organisations', 'mydialog','-1','','');\"
    $output .= createLovRecsTbl($result, $startIdx, $chkOrRadio, 0, $selvals);
    $output.="<div class='buttonBox'>
    <a name='lov_submit' id='lov_submit' class=\"button\" onclick=\"loopChecksRadios1('lovForm', '$valueElmntID', '$descElemntID', '$srcTyp', '$showValOrDesc');\">Submit</a>   
</div>
</fieldset>
</form>";
    echo $output;
}

$error = "";

if ($usrID > 0) {
    if ($qryStr === "load_selection") {
        
    } else {
        lovsForm($error);
    }
} else {
    //echo "Please Login First!"; 
    restricted();
}
?>
