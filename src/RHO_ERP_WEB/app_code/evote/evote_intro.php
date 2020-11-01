<?php

$menuItems = array("Available Elections", "All Elections", "Questions Bank", "Person Sets");
$menuImages = array("election.png", "world_48.png", "chcklst4.png", "person.png", "approve1.png");

$mdlNm = "e-Voting";
$ModuleName = $mdlNm;

$dfltPrvldgs = array("View e-Voting",
    /* 1 */ "View All Surveys/Elections", "View Questions Bank", "View Person Sets",
    /* 4 */ "View Record History", "View SQL",
    /* 6 */ "Add Surveys/Elections", "Edit Surveys/Elections", "Delete Surveys/Elections",
    /* 9 */ "Add Questions Bank", "Edit Questions Bank", "Delete Questions Bank");

$canview = test_prmssns($dfltPrvldgs[0], $mdlNm) || test_prmssns("View Elections", "Self Service");

$vwtyp = "0";
$qstr = "";
$dsply = "";
$actyp = "";
$srchFor = "";
$srchIn = "Name";
$PKeyID = -1;
$sortBy = "ID ASC";
if (isset($_POST['PKeyID'])) {
    $PKeyID = cleanInputData($_POST['PKeyID']);
}
if (isset($_POST['searchfor'])) {
    $srchFor = cleanInputData($_POST['searchfor']);
}
if (isset($_POST['searchin'])) {
    $srchIn = cleanInputData($_POST['searchin']);
}
if (isset($_POST['q'])) {
    $qstr = cleanInputData($_POST['q']);
}
if (isset($_POST['vtyp'])) {
    $vwtyp = cleanInputData($_POST['vtyp']);
}
if (isset($_POST['actyp'])) {
    $actyp = cleanInputData($_POST['actyp']);
}
if (isset($_POST['sortBy'])) {
    $sortBy = cleanInputData($_POST['sortBy']);
}
if (strpos($srchFor, "%") === FALSE) {
    $srchFor = " " . $srchFor . " ";
    $srchFor = str_replace(" ", "%", $srchFor);
}

$cntent = "<div>
				<ul class=\"breadcrumb\" style=\"$breadCrmbBckclr\">
					<li onclick=\"openATab('#home', 'grp=40&typ=1');\">
                                                <i class=\"fa fa-home\" aria-hidden=\"true\"></i>
						<span style=\"text-decoration:none;\">Home</span>
                                                <span class=\"divider\"><i class=\"fa fa-angle-right\" aria-hidden=\"true\"></i></span>
					</li>
					<li onclick=\"openATab('#allmodules', 'grp=40&typ=5');\">
						<span style=\"text-decoration:none;\">All Modules&nbsp;</span><span class=\"divider\"><i class=\"fa fa-angle-right\" aria-hidden=\"true\"></i></span>
					</li>";

if ($lgn_num > 0 && $canview === true) {
    $cntent .= "
					<li onclick=\"openATab('#allmodules', 'grp=$group&typ=$type');\">
						<span style=\"text-decoration:none;\">e-Voting Menu</span>
					</li>";
    if ($pgNo == 0) {
        $cntent .= "
                                       </ul>
                                     </div>" . "<div style=\"font-family: Tahoma, Arial, sans-serif;font-size: 1.3em;
                    padding:10px 15px 15px 20px;border:1px solid #ccc;\">                    
      <div style=\"padding:5px 30px 5px 10px;margin-bottom:2px;\">
                    <span style=\"font-family: georgia, times;font-size: 12px;font-style:italic;
                    font-weight:normal;\">This is where Elections/Polls/Surveys in the Institution are Conducted and Managed. The module has the ff areas:</span>
                    </div>
      <p>";
        $grpcntr = 0;
        for ($i = 0; $i < count($menuItems); $i++) {
            $No = $i + 1;
            if ($i == 0) {
                
            } else if ($i == 1 && test_prmssns($dfltPrvldgs[1], $mdlNm) == FALSE) {
                continue;
            } else if ($i == 2 && test_prmssns($dfltPrvldgs[2], $mdlNm) == FALSE) {
                continue;
            } else if ($i == 3 && test_prmssns($dfltPrvldgs[3], $mdlNm) == FALSE) {
                continue;
            }
            if ($grpcntr == 0) {
                $cntent .= "<div class=\"row\">";
            }
            if ($i == 3) {
                $cntent .= "<div class=\"col-md-3 colmd3special2\">
        <button type=\"button\" class=\"btn btn-default btn-lg btn-block modulesButton\" onclick=\"openATab('#allmodules', 'grp=7&typ=1&pg=6&vtyp=0&srcMdl=eVote');\">
            <img src=\"cmn_images/$menuImages[$i]\" style=\"margin:5px; padding-right: 1em; height:58px; width:auto; position: relative; vertical-align: middle;float:left;\">
            <span class=\"wordwrap2\">" . ($menuItems[$i]) . "</span>
        </button>
            </div>";
            } else {
                $cntent .= "<div class=\"col-md-3 colmd3special2\">
        <button type=\"button\" class=\"btn btn-default btn-lg btn-block modulesButton\" onclick=\"openATab('#allmodules', 'grp=$group&typ=$type&pg=$No&vtyp=0');\">
            <img src=\"cmn_images/$menuImages[$i]\" style=\"margin:5px; padding-right: 1em; height:58px; width:auto; position: relative; vertical-align: middle;float:left;\">
            <span class=\"wordwrap2\">" . ($menuItems[$i]) . "</span>
        </button>
            </div>";
            }

            if ($grpcntr == 3) {
                $cntent .= "</div>";
                $grpcntr = 0;
            } else {
                $grpcntr = $grpcntr + 1;
            }
        }

        $cntent .= "
      </p>
    </div>";
        echo $cntent;
    } else if ($pgNo == 1) {
        //Get My Linked Elections/Surveys
        require 'avlbl_srvys.php';
    } else if ($pgNo == 2) {
        //Get Surveys
        require 'all_surveys.php';
    } else if ($pgNo == 3) {
        //Get Question Bank
        require 'qstn_bank.php';
    } else if ($pgNo == 4) {
        //Get Person Sets
    } else if ($pgNo == 5 || $pgNo == 6 || $pgNo == 7 || $pgNo == 8) {
        //Question and Answer Times
        require 'answrng_time.php';
    } else {
        restricted();
    }
} else {
    restricted();
}

/*
 * Create/Update/Delete Person Set/Person Set Det
 * Create/Update/Delete Question Bank/Answers
 * Create/Update/Delete Elections/Question Sets
 */

function deleteSurveyQstns($hdrid, $qstnNm = "") {
    $insSQL = "DELETE FROM self.self_srvy_qustns WHERE srvy_qstn_id = " . $hdrid;
    $affctd = execUpdtInsSQL($insSQL, "Question:" . $qstnNm);

    if ($affctd > 0) {
        $dsply = "Successfully Deleted the ff Records-";
        $dsply .= "<br/>$affctd Survey/Election Question(s)!";
        return "<p style = \"text-align:left; color:#32CD32;font-weight:bold;font-style:italic;\">$dsply</p>";
    } else {
        $dsply = "No Record Deleted!";
        return "<p style = \"text-align:left; color:red;font-weight:bold;font-style:italic;\">$dsply</p>";
    }
}

function createSurveyQstns($srvyID, $qstnID, $qstnOrder, $qstnDsplCondtn, $qstnInvldChcsQry, $qstnInvldChcsMsg) {
    global $usrID;
    $dateStr = getDB_Date_time();
    $insSQL = "INSERT INTO self.self_srvy_qustns(
            srvy_id, qstn_id, qstn_order, created_by, creation_date, 
            last_update_by, last_update_date, qstn_dsply_condtn_sql, qstn_ans_valdtn_sql, 
            invalid_ans_set_msg) VALUES (" . $srvyID .
            "," . $qstnID .
            "," . $qstnOrder .
            ", " . $usrID . ", '" . $dateStr .
            "', " . $usrID . ", '" . $dateStr .
            "', '" . loc_db_escape_string($qstnDsplCondtn) .
            "', '" . loc_db_escape_string($qstnInvldChcsQry) .
            "', '" . loc_db_escape_string($qstnInvldChcsMsg) .
            "')";
    execUpdtInsSQL($insSQL);
}

function updateSurveyQstns($srvyQstnID, $qstnOrder, $qstnDsplCondtn, $qstnInvldChcsQry, $qstnInvldChcsMsg) {
    global $usrID;
    $dateStr = getDB_Date_time();

    $insSQL = "UPDATE self.self_srvy_qustns " .
            "SET qstn_dsply_condtn_sql='" . loc_db_escape_string($qstnDsplCondtn) .
            "', qstn_ans_valdtn_sql='" . loc_db_escape_string($qstnInvldChcsQry) .
            "', invalid_ans_set_msg='" . loc_db_escape_string($qstnInvldChcsMsg) .
            "', qstn_order = " . $qstnOrder .
            ", last_update_by=" . $usrID .
            ", last_update_date='" . $dateStr .
            "' WHERE srvy_qstn_id = " . $srvyQstnID;
    execUpdtInsSQL($insSQL);
}

function deleteSurvey($hdrid, $surveyNm = "") {
    $insSQL = "DELETE FROM self.self_surveys WHERE srvy_id = " . $hdrid;
    $affctd = execUpdtInsSQL($insSQL, "Survey Name:" . $surveyNm);
    $insSQL1 = "DELETE FROM self.self_srvy_qustns WHERE srvy_id = " . $hdrid;
    $affctd1 = execUpdtInsSQL($insSQL1, "Survey Name:" . $surveyNm);
    $insSQL2 = "DELETE FROM self.self_srvy_qustns_ans WHERE srvy_id = " . $hdrid;
    $affctd2 = execUpdtInsSQL($insSQL2, "Survey Name:" . $surveyNm);

    if ($affctd > 0) {
        $dsply = "Successfully Deleted the ff Records-";
        $dsply .= "<br/>$affctd Survey/Election(s)!";
        $dsply .= "<br/>$affctd1 Survey/Election Question(s)!";
        $dsply .= "<br/>$affctd2 Survey/Election Question Answer(s)!";
        return "<p style = \"text-align:left; color:#32CD32;font-weight:bold;font-style:italic;\">$dsply</p>";
    } else {
        $dsply = "No Record Deleted!";
        return "<p style = \"text-align:left; color:red;font-weight:bold;font-style:italic;\">$dsply</p>";
    }
}

function createSurvey($srvyNm, $srvyDesc, $srvyInstrctns, $srvyStartDte, $srvyEndDte, $inclPrsnSetID, $exclPrsnSetID, $srvyType) {
    global $usrID;
    $dateStr = getDB_Date_time();
    $insSQL = "INSERT INTO self.self_surveys(
            srvy_name, srvy_desc, srvy_instrctns, created_by, creation_date, 
            last_update_by, last_update_date, srvy_start_date, srvy_end_date, 
            eligible_prsn_set_id, survey_type, exclsn_lst_prsn_set_id) " .
            "VALUES ('" . loc_db_escape_string($srvyNm) .
            "', '" . loc_db_escape_string($srvyDesc) .
            "', '" . loc_db_escape_string($srvyInstrctns) .
            "', " . $usrID . ", '" . $dateStr .
            "', " . $usrID . ", '" . $dateStr .
            "', '" . loc_db_escape_string($srvyStartDte) .
            "', '" . loc_db_escape_string($srvyEndDte) .
            "', " . $inclPrsnSetID .
            ", '" . loc_db_escape_string($srvyType) .
            "', " . $exclPrsnSetID .
            ")";
    execUpdtInsSQL($insSQL);
}

function updateSurvey($srvyID, $srvyNm, $srvyDesc, $srvyInstrctns, $srvyStartDte, $srvyEndDte, $inclPrsnSetID, $exclPrsnSetID, $srvyType) {
    global $usrID;
    $dateStr = getDB_Date_time();

    $insSQL = "UPDATE self.self_surveys " .
            "SET srvy_name='" . loc_db_escape_string($srvyNm) .
            "', srvy_desc='" . loc_db_escape_string($srvyDesc) .
            "', srvy_instrctns = '" . loc_db_escape_string($srvyInstrctns) .
            "', eligible_prsn_set_id = " . $inclPrsnSetID .
            ", exclsn_lst_prsn_set_id = " . $exclPrsnSetID .
            ", srvy_start_date = '" . loc_db_escape_string($srvyStartDte) .
            "', srvy_end_date = '" . loc_db_escape_string($srvyEndDte) .
            "', survey_type = '" . loc_db_escape_string($srvyType) .
            "', last_update_by=" . $usrID .
            ", last_update_date='" . $dateStr .
            "' WHERE srvy_id = " . $srvyID;
    execUpdtInsSQL($insSQL);
}

function deleteQuestionAns($pssblAnsId, $ansNm = "") {
    $insSQL = "DELETE FROM self.self_question_possible_answers WHERE psbl_ansr_id = " . $pssblAnsId;
    $affctd = execUpdtInsSQL($insSQL, $ansNm = "");

    if ($affctd > 0) {
        $dsply = "Successfully Deleted the ff Records-";
        $dsply .= "<br/>$affctd Possible Answer(s)!";
        return "<p style = \"text-align:left; color:#32CD32;font-weight:bold;font-style:italic;\">$dsply</p>";
    } else {
        $dsply = "No Record Deleted!";
        return "<p style = \"text-align:left; color:red;font-weight:bold;font-style:italic;\">$dsply</p>";
    }
}

function createQuestionAns($qstnID, $ansdesc, $ansOrdrNo, $isCorrectAns, $ansImgLoc, $ansHintTxt, $ansHint, $isAnsEnabled) {
    global $usrID;
    $dateStr = getDB_Date_time();
    $insSQL = "INSERT INTO self.self_question_possible_answers(
            qstn_id, psbl_ansr_desc, psbl_ansr_order_no, correct_ansr, 
            created_by, creation_date, last_update_by, last_update_date, 
            answer_img_loc, answer_hint_link_txt, answer_hint, is_enabled) " .
            "VALUES (" . $qstnID . ", '" . loc_db_escape_string($ansdesc) .
            "', " . $ansOrdrNo . ",  '" . loc_db_escape_string($isCorrectAns) .
            "', " . $usrID . ", '" . $dateStr .
            "', " . $usrID . ", '" . $dateStr .
            "', '" . loc_db_escape_string($ansImgLoc) .
            "', '" . loc_db_escape_string($ansHintTxt) .
            "', '" . loc_db_escape_string($ansHint) . "','" . loc_db_escape_string($isAnsEnabled) . "')";

    execUpdtInsSQL($insSQL);
}

function updateQuestionAns($pssblAnsID, $ansdesc, $ansOrdrNo, $isCorrectAns, $ansImgLoc, $ansHintTxt, $ansHint, $isAnsEnabled) {
    global $usrID;
    $dateStr = getDB_Date_time();
    $insSQL = "UPDATE self.self_question_possible_answers " .
            "SET psbl_ansr_desc='" . loc_db_escape_string($ansdesc) .
            "', correct_ansr='" . loc_db_escape_string($isCorrectAns) .
            "', answer_img_loc = '" . loc_db_escape_string($ansImgLoc) .
            "', psbl_ansr_order_no = " . $ansOrdrNo .
            ", answer_hint_link_txt = '" . loc_db_escape_string($ansHintTxt) .
            "', answer_hint = '" . loc_db_escape_string($ansHint) .
            "', last_update_by=" . $usrID .
            ", last_update_date='" . $dateStr .
            "', is_enabled='" . loc_db_escape_string($isAnsEnabled) .
            "' WHERE psbl_ansr_id = " . $pssblAnsID;

    execUpdtInsSQL($insSQL);
}

function deleteSurvyQstnAns($srvQstnAnsId, $ansNm = "") {
    $insSQL = "DELETE FROM self.self_srvy_qustns_ans WHERE srvy_qstn_ans_id = " . $srvQstnAnsId;
    $affctd = execUpdtInsSQL($insSQL, "Answer:" . $ansNm);

    if ($affctd > 0) {
        $dsply = "Successfully Deleted the ff Records-";
        $dsply .= "<br/>$affctd Survey Question Answer(s)!";
        return "<p style = \"text-align:left; color:#32CD32;font-weight:bold;font-style:italic;\">$dsply</p>";
    } else {
        $dsply = "No Record Deleted!";
        return "<p style = \"text-align:left; color:red;font-weight:bold;font-style:italic;\">$dsply</p>";
    }
}

function createSurvyQstnAns($srvyID, $qstnID, $ansID, $ansOrdrNo, $isCorrectAns, $ansDsplyCondition) {
    global $usrID;
    $dateStr = getDB_Date_time();
    $insSQL = "INSERT INTO self.self_srvy_qustns_ans(
            srvy_id, qstn_id, pssbl_ans_id, ans_order, correct_ansr, 
            created_by, creation_date, last_update_by, last_update_date, 
            ans_dsply_condtn_sql) " .
            "VALUES (" . $srvyID . ", " . $qstnID . ", " . $ansID . ", " . $ansOrdrNo .
            ", '" . loc_db_escape_string($isCorrectAns) .
            "', " . $usrID . ", '" . $dateStr .
            "', " . $usrID . ", '" . $dateStr .
            "', '" . loc_db_escape_string($ansDsplyCondition) . "')";
    return execUpdtInsSQL($insSQL);
}

function updateSurvyQstnAns($srvyQstnAnsID, $ansOrdrNo, $isCorrectAns, $ansDsplyCondition) {
    global $usrID;
    $dateStr = getDB_Date_time();
    $insSQL = "UPDATE self.self_srvy_qustns_ans " .
            "SET ans_dsply_condtn_sql='" . loc_db_escape_string($ansDsplyCondition) .
            "', correct_ansr='" . loc_db_escape_string($isCorrectAns) .
            "', ans_order = " . $ansOrdrNo .
            ", last_update_by=" . $usrID .
            ", last_update_date='" . $dateStr .
            "' WHERE srvy_qstn_ans_id = " . $srvyQstnAnsID;
    return execUpdtInsSQL($insSQL);
}

function deleteQuestion($hdrid, $qstnNm = "") {
    $insSQL = "DELETE FROM self.self_question_bank WHERE qstn_id = " . $hdrid;
    $affctd = execUpdtInsSQL($insSQL, "Question:" . $qstnNm);
    $insSQL1 = "DELETE FROM self.self_question_possible_answers WHERE qstn_id = " . $hdrid;
    $affctd1 = execUpdtInsSQL($insSQL1, "Question:" . $qstnNm);

    if ($affctd > 0) {
        $dsply = "Successfully Deleted the ff Records-";
        $dsply .= "<br/>$affctd Question(s)!";
        $dsply .= "<br/>$affctd1 Answer(s)!";
        return "<p style = \"text-align:left; color:#32CD32;font-weight:bold;font-style:italic;\">$dsply</p>";
    } else {
        $dsply = "No Record Deleted!";
        return "<p style = \"text-align:left; color:red;font-weight:bold;font-style:italic;\">

    $dsply</p>";
    }
}

function createQuestion($qstndesc, $qstnAnsType, $imgLoc, $allwdAns, $qstnClsfctn, $qstnHint) {
    global $usrID;
    $dateStr = getDB_Date_time();
    $insSQL = "INSERT INTO self.self_question_bank(
            qstn_desc, qstn_ansr_type, created_by, creation_date, 
            last_update_by, last_update_date, qstn_img_loc, allwd_num_slctd_ans, 
            category_clsfctn, question_hint) " .
            "VALUES ('" . loc_db_escape_string($qstndesc) .
            "', '" . loc_db_escape_string($qstnAnsType) .
            "', " . $usrID . ", '" . $dateStr .
            "', " . $usrID . ", '" . $dateStr .
            "', '" . loc_db_escape_string($imgLoc) .
            "', " . $allwdAns .
            ", '" . loc_db_escape_string($qstnClsfctn) .
            "', '" . loc_db_escape_string($qstnHint) . "')";

    execUpdtInsSQL($insSQL);
}

function updateQuestion($qstnID, $qstndesc, $qstnAnsType, $imgLoc, $allwdAns, $qstnClsfctn, $qstnHint) {
    global $usrID;
    $dateStr = getDB_Date_time();
    $insSQL = "UPDATE self.self_question_bank " .
            "SET qstn_desc='" . loc_db_escape_string($qstndesc) .
            "', qstn_ansr_type='" . loc_db_escape_string($qstnAnsType) .
            "', qstn_img_loc = '" . loc_db_escape_string($imgLoc) .
            "', allwd_num_slctd_ans = " . $allwdAns .
            ", category_clsfctn = '" . loc_db_escape_string($qstnClsfctn) .
            "', question_hint = '" . loc_db_escape_string($qstnHint) .
            "', last_update_by=" . $usrID .
            ", last_update_date='" . $dateStr .
            "' WHERE qstn_id = " . $qstnID;
    execUpdtInsSQL($insSQL);
}

function get_QstnCtgries() {
    $sqlStr = "Select row_number() OVER (ORDER BY tbl1.category) AS \"No.  \", 
tbl1.category AS \"Question Category  \"
 FROM (select distinct category_clsfctn category 
from self.self_question_bank where category_clsfctn != '') tbl1 
order by 2";

    $result = executeSQLNoParams($sqlStr);
    return $result;
}

function getQstnAnsID($qstnID, $pssblAns) {
    $strSql = "Select a.psbl_ansr_id "
            . "FROM self.self_question_possible_answers a where((a.qstn_id  = " .
            $qstnID . ") and (a.psbl_ansr_desc = '" . loc_db_escape_string($pssblAns) . "'))";
    $result = executeSQLNoParams($strSql);
    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return -1;
}

function getSurveyQstnID($surveyID, $qstnID) {
    $strSql = "Select a.srvy_qstn_id "
            . "FROM self.self_srvy_qustns a where((a.qstn_id  = " .
            $qstnID . ") and (a.srvy_id = " . $surveyID . "))";
    $result = executeSQLNoParams($strSql);
    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return -1;
}

function getSurveyQstnAnsID($surveyID, $qstnID, $pssblAnsID) {
    $strSql = "Select a.srvy_qstn_ans_id "
            . "FROM self.self_srvy_qustns_ans a where((a.qstn_id  = " .
            $qstnID . ") and (a.srvy_id = " . $surveyID . ") and (a.pssbl_ans_id = " . $pssblAnsID . "))";
    $result = executeSQLNoParams($strSql);
    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return -1;
}

function get_Surveys($searchFor, $searchIn, $offset, $limit_size) {
    $wherecls = "";
    if ($searchIn === "Name") {
        $wherecls = "(a.srvy_name ilike '" .
                loc_db_escape_string($searchFor) . "' or a.srvy_desc ilike '" .
                loc_db_escape_string($searchFor) . "') and ";
    } else if ($searchIn === "Instructions") {
        $wherecls = "(a.srvy_instrctns ilike '" .
                loc_db_escape_string($searchFor) . "') and ";
    } else {
        $wherecls = "(a.survey_type ilike '" .
                loc_db_escape_string($searchFor) . "') and ";
    }
    $strSql = "SELECT a.srvy_id, a.srvy_name, a.srvy_desc, a.srvy_instrctns, a.srvy_start_date, "
            . "a.srvy_end_date, a.eligible_prsn_set_id, COALESCE(b.prsn_set_hdr_name,''), "
            . "a.survey_type, a.exclsn_lst_prsn_set_id, COALESCE(c.prsn_set_hdr_name,'') " .
            "FROM self.self_surveys a "
            . "LEFT OUTER JOIN pay.pay_prsn_sets_hdr b ON (a.eligible_prsn_set_id = b.prsn_set_hdr_id) "
            . "LEFT OUTER JOIN pay.pay_prsn_sets_hdr c ON (a.exclsn_lst_prsn_set_id = c.prsn_set_hdr_id) "
            . "WHERE " . $wherecls .
            "(1=1) ORDER BY a.srvy_id DESC LIMIT " . $limit_size .
            " OFFSET " . abs($offset * $limit_size);
    //echo $strSql;
    $result = executeSQLNoParams($strSql);

    return $result;
}

function get_SurveysDet($srvyID) {
    $strSql = "SELECT a.srvy_id, a.srvy_name, a.srvy_desc, a.srvy_instrctns, a.srvy_start_date, "
            . "a.srvy_end_date, a.eligible_prsn_set_id, COALESCE(b.prsn_set_hdr_name,''), "
            . "a.survey_type, a.exclsn_lst_prsn_set_id, COALESCE(c.prsn_set_hdr_name,'') " .
            "FROM self.self_surveys a "
            . "LEFT OUTER JOIN pay.pay_prsn_sets_hdr b ON (a.eligible_prsn_set_id = b.prsn_set_hdr_id) "
            . "LEFT OUTER JOIN pay.pay_prsn_sets_hdr c ON (a.exclsn_lst_prsn_set_id = c.prsn_set_hdr_id) "
            . "WHERE a.srvy_id=" . $srvyID . "";
    //echo $strSql;
    $result = executeSQLNoParams($strSql);

    return $result;
}

function get_QuestionDetail($qstn_id) {
    $strSql = "SELECT 
        qstn_id \"Question ID\", 
        qstn_desc \"Question\", 
        qstn_ansr_type \"Answer Type\", 
       qstn_img_loc \"Image Location\", 
        allwd_num_slctd_ans \"Max. No. of Selected Answers\", 
       category_clsfctn \"Question Category\", 
       question_hint \"Question Hint/Further Info.\" 
       FROM self.self_question_bank a " .
            "WHERE (qstn_id = $qstn_id)";
    $result = executeSQLNoParams($strSql);

    return $result;
}

function get_AnswerDetail($ans_id) {
    $strSql = "SELECT 
        psbl_ansr_id \"Possible Answer ID\", 
        psbl_ansr_desc \"Answer\", 
        psbl_ansr_order_no \"Answer Order No.\", 
        (CASE WHEN correct_ansr='1' THEN 'YES' ELSE 'NO' END) \"Is Correct Answer?\",
        answer_img_loc \"Image Location\", 
        answer_hint_link_txt \"Hint Link Text\", 
        answer_hint \"Answer Hint/Further Info.\" ,
        is_enabled, qstn_id  
       FROM self.self_question_possible_answers a " .
            "WHERE (psbl_ansr_id = $ans_id)";
    $result = executeSQLNoParams($strSql);
    return $result;
}

function get_SrvyQstnAnsDetail($srvyQstnID) {
    $strSql = "SELECT b.srvy_qstn_ans_id \"Survey Question Answer ID\", 
        b.srvy_id \"Survey ID\", 
        a.qstn_id \"Question ID\", 
        a.psbl_ansr_id \"Possible Answer ID\", 
        a.psbl_ansr_desc \"Answer \", 
        b.ans_order \"Answer Sort No. \", 
        (CASE WHEN b.correct_ansr='1' THEN 'YES' ELSE 'NO' END) \"Is Correct Answer?\", 
        a.answer_img_loc \"Answer Image Location \", 
        a.answer_hint_link_txt \"Answer Hint Link Text \", 
        a.answer_hint \"Answer Hint\", 
        b.ans_dsply_condtn_sql \"Answer Display Condition \", 
        (CASE WHEN a.is_enabled='1' THEN 'YES' ELSE 'NO' END) \"Is Enabled?\" 
        FROM self.self_question_possible_answers a, self.self_srvy_qustns_ans b  
        WHERE b.srvy_qstn_ans_id = " . $srvyQstnID .
            " and a.qstn_id = b.qstn_id and a.psbl_ansr_id = b.pssbl_ans_id";
    $result = executeSQLNoParams($strSql);

    return $result;
}

function get_SurveyDetail($srvy_id) {
    $strSql = "SELECT 
        a.srvy_id \"Survey/Election ID\", 
        a.srvy_name \"Survey/Election Name\", 
        a.srvy_desc \"Survey/Election Description\", 
        a.srvy_instrctns \"Survey/Election Instructions\",
        to_char(to_timestamp(a.srvy_start_date,'YYYY-MM-DD HH24:MI:SS'),'DD-Mon-YYYY HH24:MI:SS') \"Start Date \", 
        to_char(to_timestamp(a.srvy_end_date,'YYYY-MM-DD HH24:MI:SS'),'DD-Mon-YYYY HH24:MI:SS') \"End Date \", 
        a.survey_type \"Survey/Election Type\",
      b.prsn_set_hdr_name \"Eligible Persons' Set \",
      c.prsn_set_hdr_name \"Exclusion List Person Set \"
       FROM self.self_surveys a 
       LEFT OUTER JOIN pay.pay_prsn_sets_hdr b ON (a.eligible_prsn_set_id=b.prsn_set_hdr_id)
       LEFT OUTER JOIN pay.pay_prsn_sets_hdr c ON (a.exclsn_lst_prsn_set_id=c.prsn_set_hdr_id)  " .
            "WHERE (srvy_id = $srvy_id)";
    $result = executeSQLNoParams($strSql);

    return $result;
}

function get_SurveyQstnsDetail($srvy_qstn_id) {
    $strSql = "SELECT 
        a.srvy_id \"Survey/Election ID \", 
        a.srvy_qstn_id \"Primary Key\",
        a.qstn_id \"Question ID \", 
        b.qstn_desc \"Question \", 
        a.qstn_order \"Question Order No. \", 
        a.qstn_dsply_condtn_sql \"Question Display Condition \"
       FROM self.self_srvy_qustns a 
       LEFT OUTER JOIN self.self_question_bank b ON (a.qstn_id=b.qstn_id) " .
            "WHERE (srvy_qstn_id = $srvy_qstn_id)";
    $result = executeSQLNoParams($strSql);

    return $result;
}

function get_SurveysTtl($searchFor, $searchIn) {
    $wherecls = "";
    if ($searchIn === "Name") {
        $wherecls = "(a.srvy_name ilike '" .
                loc_db_escape_string($searchFor) . "' or a.srvy_desc ilike '" .
                loc_db_escape_string($searchFor) . "') and ";
    } else if ($searchIn === "Instructions") {
        $wherecls = "(a.srvy_instrctns ilike '" .
                loc_db_escape_string($searchFor) . "') and ";
    } else {
        $wherecls = "(a.survey_type ilike '" .
                loc_db_escape_string($searchFor) . "') and ";
    }
    $strSql = "SELECT count(1) from self.self_surveys a WHERE " . $wherecls .
            "(1=1)";
    $result = executeSQLNoParams($strSql);
    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return 0;
}

function get_SrvyQuestions($searchFor, $offset, $limit_size, $srvyID) {
    $strSql = "SELECT a.srvy_qstn_id, b.qstn_desc, a.qstn_dsply_condtn_sql, a.qstn_id, a.qstn_order " .
            "FROM self.self_srvy_qustns a, self.self_question_bank b "
            . "WHERE a.qstn_id = b.qstn_id and a.srvy_id= " . $srvyID
            . " and (b.qstn_desc ilike '" .
            loc_db_escape_string($searchFor) . "' or a.qstn_dsply_condtn_sql ilike '" .
            loc_db_escape_string($searchFor) . "') ORDER BY a.qstn_order LIMIT " . $limit_size .
            " OFFSET " . abs($offset * $limit_size);
    $result = executeSQLNoParams($strSql);

    return $result;
}

function get_SrvyQuestionsDet($qstnID, $srvyID) {
    $strSql = "SELECT a.srvy_qstn_id, b.qstn_desc, a.qstn_dsply_condtn_sql, a.qstn_id, a.qstn_order, "
            . "a.qstn_ans_valdtn_sql, a.invalid_ans_set_msg " .
            "FROM self.self_srvy_qustns a, self.self_question_bank b "
            . "WHERE a.qstn_id = b.qstn_id and a.qstn_id = $qstnID and a.srvy_id = " . $srvyID
            . "";
    $result = executeSQLNoParams($strSql);
    return $result;
}

function get_SrvyQuestionsTtl($searchFor, $srvyID) {
    $strSql = "SELECT count(1) " .
            "FROM self.self_srvy_qustns a, self.self_question_bank b "
            . "WHERE a.qstn_id = b.qstn_id and a.srvy_id= " . $srvyID
            . " and (b.qstn_desc ilike '" .
            loc_db_escape_string($searchFor) . "' or a.qstn_dsply_condtn_sql ilike '" .
            loc_db_escape_string($searchFor) . "')";
    $result = executeSQLNoParams($strSql);
    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return 0;
}

function get_SrvyAnsChosen($searchFor, $searchIn, $offset, $limit_size, $srvyID) {
    $whrcls = "";
    if ($searchIn == "Answer") {
        $whrcls = " and (trim(a.ansr_desc) ilike '" . loc_db_escape_string($searchFor) . "')";
    } else if ($searchIn == "Question") {
        $whrcls = " and (trim(b.qstn_desc) ilike '" . loc_db_escape_string($searchFor) . "')";
    } else if ($searchIn == "Person Name") {
        $whrcls = " and ((prs.get_prsn_name(a.prsn_id) || ' (' || prs.get_prsn_loc_id(a.prsn_id) || ')') ilike '" . loc_db_escape_string($searchFor) . "')";
    }
    $strSql = "SELECT a.prsn_ansr_id, a.qstn_id, b.qstn_desc, a.prsn_id, 
        prs.get_prsn_name(a.prsn_id) || ' (' || prs.get_prsn_loc_id(a.prsn_id) || ')',
        a.psbl_ansr_id, trim(a.ansr_desc), a.is_submitted
  FROM self.self_person_question_answers a, 
  self.self_question_bank b 
  where a.qstn_id = b.qstn_id and a.srvy_id = " . $srvyID . $whrcls .
            " ORDER BY a.prsn_ansr_id ASC LIMIT " . $limit_size .
            " OFFSET " . abs($offset * $limit_size);
    $result = executeSQLNoParams($strSql);
    return $result;
}

function get_SrvyAnsChosenTtl($searchFor, $searchIn, $srvyID) {
    $whrcls = "";
    if ($searchIn == "Answer") {
        $whrcls = " and (trim(a.ansr_desc) ilike '" . loc_db_escape_string($searchFor) . "')";
    } else if ($searchIn == "Question") {
        $whrcls = " and (trim(b.qstn_desc) ilike '" . loc_db_escape_string($searchFor) . "')";
    } else if ($searchIn == "Person Name") {
        $whrcls = " and ((prs.get_prsn_name(a.prsn_id) || ' (' || prs.get_prsn_loc_id(a.prsn_id) || ')') ilike '" . loc_db_escape_string($searchFor) . "')";
    }
    $strSql = "SELECT count(1) 
  FROM self.self_person_question_answers a, 
  self.self_question_bank b 
  where a.qstn_id = b.qstn_id and a.srvy_id = " . $srvyID . $whrcls;

    $result = executeSQLNoParams($strSql);
    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return 0;
}

function get_Questions($searchFor, $searchIn, $offset, $limit_size) {
    $wherecls = "";
    if ($searchIn === "Question") {
        $wherecls = "(a.qstn_desc ilike '" .
                loc_db_escape_string($searchFor) . "' or a.question_hint ilike '" .
                loc_db_escape_string($searchFor) . "') and ";
    } else if ($searchIn === "Category") {
        $wherecls = "(a.category_clsfctn ilike '" .
                loc_db_escape_string($searchFor) . "') and ";
    } else {
        $wherecls = "(a.question_hint ilike '" .
                loc_db_escape_string($searchFor) . "') and ";
    }

    $strSql = "SELECT a.qstn_id, a.qstn_desc, a.qstn_ansr_type, a.qstn_img_loc, a.allwd_num_slctd_ans, a.category_clsfctn, a.question_hint " .
            "FROM self.self_question_bank a WHERE " . $wherecls .
            "(1=1) "
            . "ORDER BY a.qstn_id DESC LIMIT " . $limit_size .
            " OFFSET " . abs($offset * $limit_size);
    //echo $strSql;
    $result = executeSQLNoParams($strSql);

    return $result;
}

function get_QuestionsTtl($searchFor, $searchIn) {
    $wherecls = "";
    if ($searchIn === "Question") {
        $wherecls = "(a.qstn_desc ilike '" .
                loc_db_escape_string($searchFor) . "' or a.question_hint ilike '" .
                loc_db_escape_string($searchFor) . "') and ";
    } else if ($searchIn === "Category") {
        $wherecls = "(a.category_clsfctn ilike '" .
                loc_db_escape_string($searchFor) . "') and ";
    } else {
        $wherecls = "(a.question_hint ilike '" .
                loc_db_escape_string($searchFor) . "') and ";
    }
    $strSql = "SELECT count(1) from self.self_question_bank a WHERE " . $wherecls .
            "(1=1)";
    $result = executeSQLNoParams($strSql);
    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return 0;
}

function get_OneQuestion($pkID) {
    $strSql = "SELECT a.qstn_id, a.qstn_desc, a.qstn_ansr_type, "
            . "a.qstn_img_loc, a.allwd_num_slctd_ans, a.category_clsfctn, "
            . "a.question_hint " .
            "FROM self.self_question_bank a WHERE a.qstn_id = " . $pkID;

    $result = executeSQLNoParams($strSql);

    return $result;
}

function get_Answers($offset, $limit_size, $questionID) {
    $strSql = "SELECT a.psbl_ansr_id, a.psbl_ansr_desc, a.correct_ansr, "
            . "a.answer_img_loc, a.answer_hint_link_txt, a.answer_hint, "
            . "a.psbl_ansr_order_no, a.is_enabled " .
            "FROM self.self_question_possible_answers a WHERE a.qstn_id= " . $questionID
            . " ORDER BY a.psbl_ansr_order_no LIMIT " . $limit_size .
            " OFFSET " . abs($offset * $limit_size);
    $result = executeSQLNoParams($strSql);

    return $result;
}

function get_AnswersTtl($questionID) {
    $strSql = "SELECT count(1) " .
            "FROM self.self_question_possible_answers a "
            . "WHERE a.qstn_id= " . $questionID
            . "";
    $result = executeSQLNoParams($strSql);
    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return 0;
}

function get_MyActvSrvysTblr($prsnID) {
    $strSql = "SELECT srvy_id, srvy_name, srvy_desc, srvy_instrctns, srvy_start_date, srvy_end_date, 
       eligible_prsn_set_id, survey_type 
  FROM self.self_surveys y
  WHERE (survey_type IN ('ELECTION', 'SURVEY')) 
  and pay.does_prsn_exist_in_prs_st(y.eligible_prsn_set_id, $prsnID)>=1 "
            . " and pay.does_prsn_exist_in_prs_st(y.exclsn_lst_prsn_set_id, $prsnID)<=0"
            . " ORDER BY srvy_start_date DESC LIMIT 20 OFFSET 0";
    //echo $strSql;
    /* now() between to_timestamp(srvy_start_date, 'YYYY-MM-DD HH24:MI:SS') 
      and to_timestamp(srvy_end_date, 'YYYY-MM-DD HH24:MI:SS') and */
    $result = executeSQLNoParams($strSql);

    return $result;
}

function get_ActvSrvyDet($srvyID) {
    $strSql = "SELECT srvy_id, srvy_name, srvy_desc, 
        srvy_instrctns, srvy_start_date, srvy_end_date, 
       eligible_prsn_set_id, survey_type 
  FROM self.self_surveys y
  WHERE (now() between to_timestamp(srvy_start_date, 'YYYY-MM-DD HH24:MI:SS') 
      and to_timestamp(srvy_end_date, 'YYYY-MM-DD HH24:MI:SS') and 
      y.srvy_id = $srvyID)";
    $result = executeSQLNoParams($strSql);

    return $result;
}

function hasSrvyBeenClosed($srvyID) {
    $strSql = "SELECT srvy_id 
  FROM self.self_surveys y
  WHERE (now() > to_timestamp(srvy_end_date, 'YYYY-MM-DD HH24:MI:SS') and 
      y.srvy_id = $srvyID)";
    $result = executeSQLNoParams($strSql);
    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return -1;
}

function get_SrvyDet($srvyID) {
    $strSql = "SELECT srvy_id, srvy_name, srvy_desc, 
        srvy_instrctns, srvy_start_date, srvy_end_date, 
       eligible_prsn_set_id, survey_type 
  FROM self.self_surveys y
  WHERE (y.srvy_id = $srvyID

            )";
    $result = executeSQLNoParams($strSql);

    return $result;
}

function get_SrvyNm($srvyID) {
    $strSql = "SELECT srvy_name 
  FROM self.self_surveys y
  WHERE (y.srvy_id = $srvyID)";
    $result = executeSQLNoParams($strSql);


    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return "";
}

function get_SrvyQstnsTtl($pkeyID) {
    $strSql = "SELECT count(1) FROM self.self_srvy_qustns 
        WHERE srvy_id = " . $pkeyID;
    $result = executeSQLNoParams($strSql);
    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return 0;
}

function get_SrvyNxtQstn($pkeyID, $offset) {
    global $prsnid;

    $strSql = "SELECT a.qstn_id, a.qstn_order, a.qstn_dsply_condtn_sql,
        b.qstn_desc, b.qstn_ansr_type, b.qstn_img_loc, b.allwd_num_slctd_ans, 
        b.category_clsfctn, b.question_hint  
        FROM self.self_srvy_qustns a, self.self_question_bank b
        WHERE a.qstn_id = b.qstn_id and a.srvy_id = " . $pkeyID .
            " and self.does_qstn_cndtn_exst(a.srvy_qstn_id, $prsnid)>=1 "
            . "ORDER BY a.qstn_order, a.qstn_id LIMIT 1 OFFSET $offset

            ";
    $result = executeSQLNoParams($strSql);
    return $result;
}

function get_QstnCtgry($questionID) {
    $strSql = "SELECT b.category_clsfctn   
        FROM self.self_question_bank b
        WHERE b.qstn_id = " . $questionID;
    $result = executeSQLNoParams($strSql);

    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return "";
}

function get_QstnCtgryTtl($questionCtgry, $srvyID) {
    $strSql = "SELECT count(1)   
        FROM self.self_question_bank b, self.self_srvy_qustns c 
        WHERE b.qstn_id = c.qstn_id and b.category_clsfctn = 
        '" . loc_db_escape_string($questionCtgry) . "' and c.srvy_id =" . $srvyID;
    $result = executeSQLNoParams($strSql);
    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return 0;
}

function get_QstnAnsTyp($questionID) {
    $strSql = "SELECT b.qstn_ansr_type   
        FROM self.self_question_bank b
        WHERE b.qstn_id = " . $questionID;
    $result = executeSQLNoParams($strSql);


    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return "";
}

function get_QstnAnsVldtnSQL($questionID, $srvy_id) {
    $strSql = "SELECT b.qstn_ans_valdtn_sql   
        FROM self.self_srvy_qustns b
        WHERE b.qstn_id = " . $questionID . " and b.srvy_id=" . $srvy_id;
    $result = executeSQLNoParams($strSql);
    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return "";
}

function get_QstnAnsVldtnTxt($questionID, $srvy_id) {
    $strSql = "SELECT b.invalid_ans_set_msg   
        FROM self.self_srvy_qustns b
        WHERE b.qstn_id = " . $questionID . " and b.srvy_id=" . $srvy_id;
    $result = executeSQLNoParams($strSql);
    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return "";
}

function get_QstnHint($questionID) {
    $strSql = "SELECT b.question_hint   
        FROM self.self_question_bank b
        WHERE b.qstn_id = " . $questionID;
    $result = executeSQLNoParams($strSql);


    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return "";
}

function get_AnsHint($ansID) {
    $strSql = "SELECT b.answer_hint   
        FROM self.self_question_possible_answers b
        WHERE b.psbl_ansr_id = " . $ansID;

    $result = executeSQLNoParams($strSql);
    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return "";
}

function get_SrvyQstnAns($searchFor, $offset, $limit_size, $pkeyID, $srvyID) {
    $strSql = "SELECT b.srvy_qstn_ans_id, b.srvy_id, a.qstn_id, a.psbl_ansr_id, a.psbl_ansr_desc, b.ans_order, b.correct_ansr, 
        a.answer_img_loc, a.answer_hint_link_txt, a.answer_hint, b.ans_dsply_condtn_sql, a.is_enabled  
        FROM self.self_question_possible_answers a, self.self_srvy_qustns_ans b  
        WHERE a.qstn_id = " . $pkeyID .
            " and a.qstn_id = b.qstn_id and a.psbl_ansr_id = b.pssbl_ans_id and b.srvy_id= " . $srvyID .
            " and a.psbl_ansr_desc ilike '" .
            loc_db_escape_string($searchFor) . "' ORDER BY a.psbl_ansr_order_no, a.psbl_ansr_id LIMIT " . $limit_size .
            " OFFSET " . abs($offset * $limit_size);
    $result = executeSQLNoParams($strSql);
    return $result;
}

function get_SrvyQstnAnsTtl($searchFor, $pkeyID, $srvyID) {
    $strSql = "SELECT count(1)   
        FROM self.self_question_possible_answers a, 
        self.self_srvy_qustns_ans b  
        WHERE a.qstn_id = " . $pkeyID .
            " and a.qstn_id = b.qstn_id and a.psbl_ansr_id = b.pssbl_ans_id "
            . "and b.srvy_id= " . $srvyID . " and a.psbl_ansr_desc ilike '" .
            loc_db_escape_string($searchFor) . "'";
    $result = executeSQLNoParams($strSql);
    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return 0;
}

function get_QstnPssblAnswers($pkeyID, $srvyID) {
    $strSql = "SELECT a.psbl_ansr_id, a.psbl_ansr_desc, b.ans_order, b.correct_ansr, 
        a.answer_img_loc, a.answer_hint_link_txt, a.answer_hint, b.ans_dsply_condtn_sql  
        FROM self.self_question_possible_answers a, self.self_srvy_qustns_ans b  
        WHERE a.qstn_id = " . $pkeyID .
            " and a.qstn_id = b.qstn_id and a.psbl_ansr_id = b.pssbl_ans_id and b.srvy_id= " . $srvyID .
            " ORDER BY a.psbl_ansr_order_no, a.psbl_ansr_id";

    $result = executeSQLNoParams($strSql);

    return $result;
}

function getPrsnChosenAnswerID($qstnID, $prsnID, $srvyID, $answerID) {
    $strSql = "SELECT a.prsn_ansr_id 
        FROM self.self_person_question_answers a 
        WHERE a.qstn_id = " . $qstnID .
            " and a.prsn_id = $prsnID "
            . "and a.psbl_ansr_id = $answerID "
            . "and a.srvy_id = $srvyID";
    $result = executeSQLNoParams($strSql);


    while ($row = loc_db_fetch_array($result)) {
        return $row [0]

        ;
    } return -1;
}

function getPrsnChosenAnswer($qstnID, $prsnID, $srvyID, $answerID) {
    $strSql = "SELECT a.ansr_desc 
        FROM self.self_person_question_answers a 
        WHERE a.qstn_id = " . $qstnID .
            " and a.prsn_id = $prsnID "
            . "and a.psbl_ansr_id = $answerID "
            . "and a.srvy_id = $srvyID";
    $result = executeSQLNoParams($strSql);


    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return "";
}

function getPrsnsAnswerIDSlctd($qstnID, $prsnID, $srvyID) {
    $strSql = "SELECT a.prsn_ansr_id 
        FROM self.self_person_question_answers a 
        WHERE a.qstn_id = " . $qstnID .
            " and a.prsn_id = $prsnID "
            . "and a.srvy_id = $srvyID";
    $result = executeSQLNoParams($strSql);


    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return "";
}

function getPrsnsUnSbmttdAnswerTtl($prsnID, $srvyID) {
    $strSql = "SELECT count(1) 
        FROM self.self_person_question_answers a 
        WHERE a.prsn_id = $prsnID "
            . "and a.srvy_id = $srvyID"
            . " and is_submitted != '1'";
    $result = executeSQLNoParams($strSql);
    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return 0;
}

function getPrsnsAnswersTtl($prsnID, $srvyID) {
    $strSql = "SELECT count(1) 
        FROM self.self_person_question_answers a 
        WHERE a.prsn_id = $prsnID "
            . "and a.srvy_id = $srvyID";
    $result = executeSQLNoParams($strSql);
    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return 0;
}

function get_SurveyResults($pkeyID) {
    $strSql = "SELECT a.qstn_id, a.qstn_order,
        b.qstn_desc, b.qstn_img_loc, c.psbl_ansr_id,
       d.ans_order, 
        c.psbl_ansr_desc, c.answer_img_loc, 
        count(y.prsn_ansr_id),
        round((round(count(y.prsn_ansr_id),2)/round((COALESCE(NULLIF((Select count(1) from 
        self.self_person_question_answers z where z.qstn_id = a.qstn_id and z.srvy_id = a.srvy_id and z.is_submitted='1'),0),1)),2))*100.00,2),
        a.srvy_id
        FROM self.self_srvy_qustns a 
        LEFT OUTER JOIN self.self_question_bank b ON (a.qstn_id = b.qstn_id)
        LEFT OUTER JOIN self.self_srvy_qustns_ans d ON (d.qstn_id = a.qstn_id and a.srvy_id = d.srvy_id)
        LEFT OUTER JOIN self.self_question_possible_answers c ON (c.qstn_id = b.qstn_id and d.pssbl_ans_id = c.psbl_ansr_id) 
        LEFT OUTER JOIN self.self_person_question_answers y ON (y.qstn_id = a.qstn_id and y.srvy_id = a.srvy_id 
        and y.psbl_ansr_id = c.psbl_ansr_id and y.is_submitted='1') 
        WHERE a.srvy_id = " . $pkeyID .
            " GROUP BY 2,1,3,4,6,5,7,8,11" .
            " ORDER BY a.qstn_order, a.qstn_id, d.ans_order, c.psbl_ansr_id";
    //echo $strSql;
    $result = executeSQLNoParams($strSql);
    return $result;
}

function get_SurveyRslts($pkeyID, $prsnID) {
    $strSql = "SELECT a.qstn_id, a.qstn_order,
        b.qstn_desc, b.qstn_img_loc, c.psbl_ansr_id,
       d.ans_order, 
        c.psbl_ansr_desc, c.answer_img_loc, 
        count(y.prsn_ansr_id),
        round((round(count(y.prsn_ansr_id),2)/round((COALESCE(NULLIF((Select count(1) from 
        self.self_person_question_answers z where z.qstn_id = a.qstn_id and z.srvy_id = a.srvy_id),0),1)),2))*100.00,2),
        a.srvy_id
        FROM self.self_srvy_qustns a 
        LEFT OUTER JOIN self.self_question_bank b ON (a.qstn_id = b.qstn_id)
        LEFT OUTER JOIN self.self_srvy_qustns_ans d ON (d.qstn_id = a.qstn_id and a.srvy_id = d.srvy_id)
        LEFT OUTER JOIN self.self_question_possible_answers c ON (c.qstn_id = b.qstn_id and d.pssbl_ans_id = c.psbl_ansr_id) 
        LEFT OUTER JOIN self.self_person_question_answers y ON (y.qstn_id = a.qstn_id and y.srvy_id = a.srvy_id 
        and y.psbl_ansr_id = c.psbl_ansr_id) 
        WHERE a.srvy_id = " . $pkeyID .
            " and y.prsn_id = $prsnID GROUP BY 2,1,3,4,6,5,7,8,11" .
            " ORDER BY a.qstn_order, a.qstn_id, d.ans_order, c.psbl_ansr_id";
    $result = executeSQLNoParams($strSql);
    return $result;
    // and y.is_submitted='1'
    // and z.is_submitted='1'
}

function doesPrsStHvPrs($hdrID, $prsnID) {
    $strSql = "Select a.prsn_set_det_id FROM pay.pay_prsn_sets_det a where((a.prsn_set_hdr_id = " .
            $hdrID . ") and (a.person_id = " . $prsnID . "))";
    $result = executeSQLNoParams($strSql);
    if (loc_db_num_rows($result) > 0) {
        return true;
    }

    return false;
}

function getPrsStSQL($prsStID) {
    $strSql = "select sql_query from pay.pay_prsn_sets_hdr where prsn_set_hdr_id = " .
            $prsStID . "";
    $result = executeSQLNoParams($strSql);
    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return "";
}

function reopenVote($prsnAnsID, $inptPrsNm = "") {
    global $usrID;
    $dateStr = getDB_Date_time();
    $insSQL = "UPDATE self.self_person_question_answers SET is_submitted = '0', last_update_by=" . $usrID .
            ", last_update_date='" . $dateStr .
            "' WHERE (prsn_ansr_id = " . $prsnAnsID . ")";
    $affctd = execUpdtInsSQL($insSQL, "Person Name:" . $inptPrsNm);
    if ($affctd > 0) {
        $dsply = "Successfully Updated the ff Records-";
        $dsply .= "<br/>$affctd Survey Vote!";
        return "<p style = \"text-align:left; color:#32CD32;font-weight:bold;font-style:italic;\">$dsply</p>";
    } else {
        $dsply = "No Record Updated!";
        return "<p style = \"text-align:left; color:red;font-weight:bold;font-style:italic;\">$dsply</p>";
    }
}

?>
