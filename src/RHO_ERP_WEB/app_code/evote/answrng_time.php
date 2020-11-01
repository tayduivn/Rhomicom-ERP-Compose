<?php

$pageNo = isset($_POST['pageNo']) ? cleanInputData($_POST['pageNo']) : 1;
$lmtSze = isset($_POST['limitSze']) ? cleanInputData($_POST['limitSze']) : 10;
$sortBy = isset($_POST['sortBy']) ? cleanInputData($_POST['sortBy']) : "";
$curIdx = 0;
$orgID = $_SESSION['ORG_ID'];
$usrID = $_SESSION['USRID'];

if (array_key_exists('lgn_num', get_defined_vars())) {
    if ($lgn_num > 0 && $canview === true) {
        if ($pgNo == 5) {
            /* Insert/Update Provided Answer */
            /*
             * if Multiple Answers Allowed, Delete all Previous annswers and insert new ones
             * else check if prsn_ansr_id exists and update else insert
             */
            $dateStr = getDB_Date_time();
            $srvyID = isset($_POST['sbmtdSrvyID']) ? cleanInputData($_POST['sbmtdSrvyID']) : -1;
            $questionID = isset($_POST['questionID']) ? cleanInputData($_POST['questionID']) : 0;
            $answerIDs = isset($_POST['answer']) ? cleanInputData($_POST['answer']) : "";
            $answerTxts = isset($_POST['answerTxt']) ? cleanInputData($_POST['answerTxt']) : "";
            $actionNm = isset($_POST['actionNm']) ? cleanInputData($_POST['actionNm']) : "";
            $nxQstnIdx = isset($_POST['nxQstnIdx']) ? cleanInputData($_POST['nxQstnIdx']) : 0;
            $invldAsnMsg = "";
            $srvyNm = get_SrvyNm($srvyID);
            $shdSbmt = isset($_POST['shdSubmit']) ? cleanInputData($_POST['shdSubmit']) : 0;
            $breadCrm = $cntent . "<li onclick=\"openATab('#allmodules', 'grp=19&typ=10&pg=1');\">
                                    <span class=\"divider\"><i class=\"fa fa-angle-right\" aria-hidden=\"true\"></i></span>
                                    <span style=\"text-decoration:none;\">Available Elections/Surveys</span>
				</li>
                                <li>
                                    <span class=\"divider\"><i class=\"fa fa-angle-right\" aria-hidden=\"true\"></i></span>
                                    <span style=\"text-decoration:none;\">$srvyNm</span>
				</li>
                               </ul>
                            </div>";
            if ($questionID > 0) {
                $qstnAnsTyp = get_QstnAnsTyp($questionID);
                $qstnCtgry = get_QstnCtgry($questionID);
                $qstnCtgryTtl = get_QstnCtgryTtl($qstnCtgry, $srvyID);

                if ($qstnCtgryTtl >= 2 || $qstnAnsTyp = "Multiple-Select Answers (Checkbox)") {
                    $delSQL = "DELETE FROM self.self_person_question_answers "
                            . "WHERE prsn_id=" . $prsnid . " and (qstn_id=" . $questionID .
                            " or qstn_id IN (SELECT qstn_id from self.self_question_bank where category_clsfctn = '" . loc_db_escape_string($qstnCtgry) . "')) and srvy_id=" . $srvyID;
                    execUpdtInsSQL($delSQL);
                    $arryAnsIDs = explode("|", trim($answerIDs, "|"));
                    $arryAnsTxts = explode("|", trim($answerTxts, "|"));
                    for ($i = 0; $i < count($arryAnsIDs); $i++) {
                        if ($arryAnsTxts[$i] != "") {
                            $insSQL = "INSERT INTO self.self_person_question_answers(
            qstn_id, prsn_id, psbl_ansr_id, ansr_desc, created_by, 
            creation_date, last_update_by, last_update_date, srvy_id)
    VALUES ($questionID, $prsnid, $arryAnsIDs[$i], '$arryAnsTxts[$i]', $usrID, '$dateStr', 
            $usrID, '$dateStr', $srvyID)";
                            execUpdtInsSQL($insSQL);
                        }
                    }
                } else {
                    $arryAnsIDs = explode("|", trim($answerIDs, "|"));
                    $arryAnsTxts = explode("|", trim($answerTxts, "|"));
                    for ($i = 0; $i < count($arryAnsIDs); $i++) {
                        $prsnAnsID = getPrsnsAnswerIDSlctd($questionID, $prsnid, $srvyID);
                        if ($prsnAnsID > 0) {
                            $updtSQL = "UPDATE self.self_person_question_answers
   SET qstn_id=$questionID, prsn_id=$prsnid, psbl_ansr_id=$arryAnsIDs[$i], ansr_desc='$arryAnsTxts[$i]', 
       last_update_by=$usrID, last_update_date='$dateStr' WHERE prsn_ansr_id=$prsnAnsID";
                            execUpdtInsSQL($updtSQL);
                        } else if ($arryAnsTxts[$i] != "") {
                            $insSQL = "INSERT INTO self.self_person_question_answers(
            qstn_id, prsn_id, psbl_ansr_id, ansr_desc, created_by, 
            creation_date, last_update_by, last_update_date, srvy_id)
    VALUES ($questionID, $prsnid, $arryAnsIDs[$i], '$arryAnsTxts[$i]', $usrID, '$dateStr', 
            $usrID, '$dateStr', $srvyID)";
                            execUpdtInsSQL($insSQL);
                        }
                    }
                }
                if ($actionNm == "NEXT") {
                    $qstnAnsVldtySQL = get_QstnAnsVldtnSQL($questionID, $srvyID);
                    $qstnAnsVldtyTxt = get_QstnAnsVldtnTxt($questionID, $srvyID);
                    if ($qstnAnsVldtySQL != "" && $qstnAnsVldtyTxt != "") {
                        $qstnAnsVldtySQL = str_replace("{:p_qstn_id}", $questionID, str_replace("{:p_srvy_id}", $srvyID, str_replace("{:p_prsn_id}", $prsnid, $qstnAnsVldtySQL)));
                        $vldtyrslt = executeSQLNoParams($qstnAnsVldtySQL);
                        while ($rowvld = loc_db_fetch_array($vldtyrslt)) {
                            $vldtycntr = $rowvld[0];
                            if ($vldtycntr <= 0) {
                                /* Is Not Valid */
                                $nxQstnIdx = ($_SESSION['PRV_OFFST']) + 1;
                                $invldAsnMsg = "<span style=\"font-weight:bold;font-style:italic;color:red;font-family:georgia,times;font-size:16px;\">" . $qstnAnsVldtyTxt . "</span>";
                            }
                        }
                    }
                }
            } else if ($shdSbmt == 1) {
                $updtSQL = "UPDATE self.self_person_question_answers
   SET is_submitted='1', 
       last_update_by=$usrID, last_update_date='$dateStr' WHERE prsn_id=$prsnid and srvy_id = $srvyID";
                $afctd = execUpdtInsSQL($updtSQL);
                echo "<p>Successfully Finalised $afctd Selected Answers!</p>";
            }

            if (hasSrvyBeenClosed($srvyID) > 0) {
                $rslt1 = get_SrvyDet($srvyID);
                $cntent = "<div style=\"padding:5px;\">Sorry this Survey/Electronic Voting has been Closed or is Yet to be Opened!";
                while ($row = loc_db_fetch_array($rslt1)) {
                    //Get Query to Display Election Results Rather if Permitted else
                    $rslt2 = get_SurveyResults($srvyID);
                    $colsCnt = loc_db_num_fields($rslt2);
                    $cntent = "<div class=\"container-fluid\" style=\"padding:10px 20px 10px 20px;\">
                <div class=\"row\">
                <table class=\"table table-striped table-bordered table-responsive gridtable\" id=\"srvyResultsTable\" cellspacing=\"0\" width=\"100%\" style=\"width:100%;\">        
            <caption style=\"text-align:center;\">PROVISIONAL RESULTS OF " . ($row[1]) . " (Submitted Electronic Votes Only)</caption>";
                    $cntent .= "<thead><tr>";
                    $cntent .= "<th width=\"200px\" style=\"font-weight:bold;\">ELECTION ITEM OR QUESTION</th>";
                    $cntent .= "<th width=\"250px\" style=\"font-weight:bold;\">POSSIBLE OPTIONS</th>";
                    $cntent .= "<th width=\"50px\" style=\"font-weight:bold;\">NUMBER OF VOTES</th>";
                    $cntent .= "<th width=\"50px\" style=\"font-weight:bold;\">PERCENTAGE OF VOTES</th>";
                    $cntent .= "</tr></thead>";
                    $cntent .= "<tbody>";
                    $i = 0;
                    $labl = "";
                    $labl1 = "";
                    while ($row1 = loc_db_fetch_array($rslt2)) {
                        $cntent .= "<tr>";
                        $labl1 = "";
                        if ($i == 0) {
                            $labl = "(" . $row1[1] . ") " . $row1[2];
                            $labl1 = $labl;
                        } else if ($labl != "(" . $row1 [1] . ") " . $row1 [2]) {
                            $labl = "(" . $row1[1] . ") " . $row1[2];
                            $labl1 = $labl;
                        } else {
                            $labl1 = "";
                        }
                        $cntent .= "<td width=\"200px\" style=\"font-weight:bold;vertical-align:top;\" class=\"likeheader\">" . $labl1 . "</td>";
                        $cntent .= "<td width=\"250px\">" . $row1[6] . "</td>";
                        $cntent .= "<td width=\"50px\">$row1[8]</td>";
                        $cntent .= "<td width=\"50px\">" . $row1[9] . "%</td>";
                        $cntent .= "</tr>";
                        $i++;
                    }
                    $No = 5;
                    $buttons = "<div class=\"row\"><div style=\"float:right !important;vertical-align:middle;\">
        <button type=\"button\" class=\"btn btn-default btn-lg btn-block\" onclick=\"openATab('#allmodules', 'grp=$group&typ=$type&pg=1&vtyp=0&sbmtdSrvyID=$srvyID');\" style=\"vertical-align:middle;\">
            <img src=\"cmn_images/Back_Button.png\" style=\"margin:1px auto;padding-right:1em; height:30px; width:auto; position: relative; vertical-align: middle;float:left;\">
            <span style=\"vertical-align:middle;\">GO BACK&nbsp;&nbsp;&nbsp;</span>
        </button>
            </div></div>";
                    $cntent .= "</tbody></table></div>" . $buttons . "</div>";
                }
            } else if (getPrsnsUnSbmttdAnswerTtl($prsnid, $srvyID) <= 0 && getPrsnsAnswersTtl($prsnid, $srvyID) > 0) {
                $cntent = "<div class=\"container-fluid\" style=\"padding:10px 20px 10px 20px;\"><div class=\"row\" style=\"margin-bottom:10px;font-family:georgia, times;font-style:italic;\">You have submitted this Survey/Electronic Voting!<br/>Please wait for the Election to be Over to View the Results!<br/>Thank you!<br/><br/>Should you have any genuine concerns please do not hesitate to contact the Electoral Commissioner for further Clarifications!</div>";
                $rslt1 = get_SrvyDet($srvyID);
                while ($row = loc_db_fetch_array($rslt1)) {
                    //Get Query to Display Election Results Rather if Permitted else
                    $rslt2 = get_SurveyRslts($srvyID, $prsnid);
                    $colsCnt = loc_db_num_fields($rslt2);
                    $cntent .= "<div class=\"row\">
        <table class=\"table table-striped table-bordered table-responsive gridtable\" id=\"srvyResultsTable\" cellspacing=\"0\" width=\"100%\" style=\"width:100%;\">        
            <caption>SUMMARY OF YOUR VOTES/OPTIONS CHOSEN (" . ($row[1]) . ")</caption>";
                    $cntent .= "<thead><tr>";
                    $cntent .= "<th width=\"200px\" style=\"font-weight:bold;\">ELECTION ITEM OR QUESTION</th>";
                    $cntent .= "<th width=\"250px\" style=\"font-weight:bold;\">POSSIBLE OPTIONS</th>";
                    $cntent .= "<th width=\"50px\" style=\"font-weight:bold;\">NUMBER OF VOTES</th>";
                    //$cntent.= "<th width=\"50px\" style=\"font-weight:bold;\">PERCENTAGE OF VOTES</th>";
                    $cntent .= "</tr></thead>";
                    $cntent .= "<tbody>";
                    $i = 0;
                    $labl = "";
                    $labl1 = "";
                    while ($row1 = loc_db_fetch_array($rslt2)) {
                        $cntent .= "<tr>";
                        $labl1 = "";
                        if ($i == 0) {
                            $labl = "(" . $row1[1] . ") " . $row1[2];
                            $labl1 = $labl;
                        } else if ($labl != "(" . $row1 [1] . ") " . $row1 [2]) {
                            $labl = "(" . $row1[1] . ") " . $row1[2];
                            $labl1 = $labl;
                        } else {
                            $labl1 = "";
                        }
                        $cntent .= "<td width=\"200px\" style=\"font-weight:bold;vertical-align:top;\" class=\"likeheader\">" . $labl1 . "</td>";
                        $cntent .= "<td width=\"250px\">" . $row1[6] . "</td>";
                        $cntent .= "<td width=\"50px\">$row1[8]</td>";
                        $cntent .= "</tr>";
                        $i++;
                    }
                    $No = 5;
                    $buttons = "<div class=\"row\">
                <div style=\"float:right !important;vertical-align:middle;\">
                    <button type=\"button\" class=\"btn btn-default btn-lg btn-block\" onclick=\"openATab('#allmodules', 'grp=$group&typ=$type&pg=1&vtyp=0&sbmtdSrvyID=$srvyID');\" style=\"vertical-align:middle;\">
                        <img src=\"cmn_images/Back_Button.png\" style=\"margin:1px auto;padding-right:1em; height:30px; width:auto; position: relative; vertical-align: middle;float:left;\">
                        <span style=\"vertical-align:middle;\">GO BACK&nbsp;&nbsp;&nbsp;</span>
                    </button>
                </div>
            </div>";
                    $cntent .= "</tbody></table></div>" . $buttons . "</div>";
                }
            } else {
                $ttlQstns = get_SrvyQstnsTtl($srvyID);
                $cntent = "";
                if ($nxQstnIdx == 0) {
                    $cntent = "<div class=\"container-fluid\" style=\"padding:10px 20px 10px 20px;\"><div class=\"row\">Sorry this Survey/Electronic Voting has been Closed or is Yet to be Opened!</div></div>";
                    $rslt = get_ActvSrvyDet($srvyID);
                    if (loc_db_num_rows($rslt) <= 0) {
                        
                    } else {
                        while ($row = loc_db_fetch_array($rslt)) {
                            $cntent = "<div class=\"container-fluid\" style=\"padding:10px 20px 10px 20px;\"><div class=\"row\">"
                                    . "<p style=\"margin-bottom:10px;\"><span style=\"font-weight:bold;font-size:24px;color:blue;text-shadow: 0px 1px #ccccff;\">" . $row[1] . "</span></p>";
                            $cntent .= "<p style=\"margin-bottom:10px;\"><span style=\"font-weight:bold;font-style:italic;font-family:Times;font-size:16px;color:green;text-shadow: 0px 1px #ccffcc;padding-bottom:10px;\">" . $row[2] . "</span></p>";
                            $cntent .= "<p style=\"font-weight:bold;font-style:italic;font-family:Times;font-size:14px;color:#000000;margin:10px;\">" . $row[3] . "</p></div>";
                        }
                        $No = 5;
                        $buttons = "<div class=\"row\">
                    <div style=\"float:left !important;vertical-align:middle;\">
        <button type=\"button\" class=\"btn btn-default btn-lg btn-block\" onclick=\"openATab('#allmodules', 'grp=$group&typ=$type&pg=1&vtyp=0&sbmtdSrvyID=$srvyID&nxQstnIdx=0');\" style=\"vertical-align:middle;\">
            <img src=\"cmn_images/Back_Button.png\" style=\"margin:1px auto;padding-right:1em; height:30px; width:auto; position: relative; vertical-align: middle;float:left;\">
            <span style=\"vertical-align:middle;\">BACK&nbsp;&nbsp;&nbsp;</span>
        </button>
            </div>";
                        $buttons .= "<div style=\"float:right !important;vertical-align:middle;\">
        <button type=\"button\" class=\"btn btn-default btn-lg btn-block\" onclick=\"openATab('#allmodules', 'grp=$group&typ=$type&pg=$No&vtyp=0&sbmtdSrvyID=$srvyID&nxQstnIdx=" . ($nxQstnIdx + 1) . "');\" style=\"vertical-align:middle;\">
            <span style=\"vertical-align:middle;\">NEXT&nbsp;&nbsp;&nbsp;</span>
            <img src=\"cmn_images/Forward.png\" style=\"margin:1px auto;padding-left:1em; height:30px; width:auto; position: relative; vertical-align: middle;float:right;\">
        </button>
            </div></div>";
                        $cntent .= "" . $buttons . "</div>";
                        $_SESSION ['PRV_OFFST'] = 0;
                    }
                } else if ($nxQstnIdx > 0 && $nxQstnIdx <= $ttlQstns) {
                    $offst = $nxQstnIdx - 1;
                    $rslt = get_SrvyNxtQstn($srvyID, $offst);
                    $cntent .= "<div class=\"container-fluid\" style=\"padding:10px 20px 10px 20px;\"><div class=\"row\" style=\"margin-bottom:15px;\">"
                            . "<p style=\"margin-bottom:10px;\"><span style=\"font-weight:bold;font-size:24px;color:blue;text-shadow: 0px 1px #ccccff;\">" . $srvyNm . "</span></p>";
                    if (loc_db_num_rows($rslt) <= 0) {
                        $cntent .= "<form><p>Congratulations! You have successfully completed all Sections of this Survey/Election!<br/>"
                                . "Please click on FINISH to end your Voting Session<br/>"
                                . "NB: Once you click on FINISH you cannot review your Options Selected!<br/>"
                                . "<span style=\"color:red;font-weight:bold;font-style:italic;\">Also only FULLY SUBMITTED and FINALISED ballots will be counted!</span></p><input type=\"hidden\" name=\"shdSubmit\" value=\"1\"></form></div>";
                        $No = 5;
                        $buttons = "<div class=\"row\" style=\"margin-bottom:15px;\">
                    <div style=\"float:left !important;vertical-align:middle;\">
        <button type=\"button\" class=\"btn btn-default btn-lg btn-block\" onclick=\"openATab('#allmodules', 'grp=$group&typ=$type&pg=$No&vtyp=0&sbmtdSrvyID=$srvyID&nxQstnIdx=" . ($_SESSION['PRV_OFFST'] + 1) . "');\" style=\"vertical-align:middle;\">
            <img src=\"cmn_images/Backward.png\" style=\"margin:1px auto;padding-right:1em; height:30px; width:auto; position: relative; vertical-align: middle;float:left;\">
            <span style=\"vertical-align:middle;\">PREVIOUS&nbsp;&nbsp;&nbsp;</span>
        </button>
            </div>";
                        $buttons .= "<div style=\"float:right !important;vertical-align:middle;\">
        <button type=\"button\" class=\"btn btn-default btn-lg btn-block\" onclick=\"validateQstnAns('#allmodules', $No, '$srvyNm', $srvyID, 0, 'FINISH');\" style=\"vertical-align:middle;\">
            <img src=\"cmn_images/approve1.png\" style=\"margin:1px auto;padding-right:1em; height:30px; width:auto; position: relative; vertical-align: middle;float:left;\">
            <span style=\"vertical-align:middle;\">FINISH&nbsp;&nbsp;&nbsp;</span>
        </button>
        </div></div>";
                        $cntent .= "" . $buttons . "</div>";
                        $nxQstnIdx = $ttlQstns;
                        $rslt1 = get_SrvyDet($srvyID);
                        while ($row = loc_db_fetch_array($rslt1)) {
                            //Get Query to Display Election Results Rather if Permitted else
                            $rslt2 = get_SurveyRslts($srvyID, $prsnid);
                            $colsCnt = loc_db_num_fields($rslt2);
                            $cntent .= "<div class=\"row\"><div class=\"col-md-12\">
        <table class=\"table table-striped table-bordered table-responsive gridtable\" id=\"srvyResultsTable\" cellspacing=\"0\" width=\"100%\" style=\"width:100%;\">        
            <caption>SUMMARY OF YOUR VOTES/OPTIONS CHOSEN (" . ($row[1]) . ")</caption>";
                            $cntent .= "<thead><tr>";
                            $cntent .= "<th width=\"200px\" style=\"font-weight:bold;\">ELECTION ITEM OR QUESTION</th>";
                            $cntent .= "<th width=\"250px\" style=\"font-weight:bold;\">POSSIBLE OPTIONS</th>";
                            $cntent .= "<th width=\"50px\" style=\"font-weight:bold;\">NUMBER OF VOTES</th>";
                            $cntent .= "</tr></thead>";
                            $cntent .= "<tbody>";
                            $i = 0;
                            $labl = "";
                            $labl1 = "";
                            while ($row1 = loc_db_fetch_array($rslt2)) {
                                $cntent .= "<tr>";
                                $labl1 = "";
                                if ($i == 0) {
                                    $labl = "(" . $row1[1] . ") " . $row1[2];
                                    $labl1 = $labl;
                                } else if ($labl != "(" . $row1 [1] . ") " . $row1 [2]) {
                                    $labl = "(" . $row1[1] . ") " . $row1[2];
                                    $labl1 = $labl;
                                } else {
                                    $labl1 = "";
                                }
                                $cntent .= "<td width=\"200px\" style=\"font-weight:bold;vertical-align:top;\" class=\"likeheader\">" . $labl1 . "</td>";
                                $cntent .= "<td width=\"250px\">" . $row1[6] . "</td>";
                                $cntent .= "<td width=\"50px\">$row1[8]</td>";
                                //$cntent.= "<td width=\"50px\">" . $row1[9] . "%</td>";
                                $cntent .= "</tr>";
                                $i++;
                            }
                            $cntent .= "</tbody></table></div></div></div>";
                        }
                    } else {
                        $_SESSION['PRV_OFFST'] = $offst;
                        while ($row = loc_db_fetch_array($rslt)) {
                            $hntTxt = "<button type=\"button\" class=\"btn btn-primary srvyInfoButton\" onclick=\"validateQstnAns('',7,'$srvyNm',$row[0],0,'QUESTION');\" value=\"Further Info!\"><img src=\"cmn_images/alert_info.gif\" width=\"18\" height=\"18\" style=\"height:18px !important;\"/> Further Info!</button>";
                            $cntent .= "<p style=\"margin-bottom:10px;\"><span style=\"font-weight:bold;font-style:italic;font-family:Times;font-size:16px;color:green;text-shadow: 0px 1px #ccffcc;\">" . $row[7] . "</span></p>";
                            $cntent .= "<form action=\"index.php\" method=\"post\">"
                                    . "<div><p>(" . $row[1] . ") " . $row[3] . "&nbsp;&nbsp;" . $hntTxt . "</p></div><div><p>"
                                    . "<input type=\"hidden\" name=\"grp\" value=\"19\">"
                                    . "<input type=\"hidden\" name=\"typ\" value=\"10\">"
                                    . "<input type=\"hidden\" name=\"vwtyp\" value=\"0\">"
                                    . "<input type=\"hidden\" name=\"pg\" value=\"5\">"
                                    . "<input type=\"hidden\" name=\"questionID\" value=\"$row[0]\">"
                                    . "<input type=\"hidden\" name=\"sbmtdSrvyID\" value=\"$srvyID\"><table><tbody>";
                            $rslt1 = get_QstnPssblAnswers($row[0], $srvyID);
                            $ansTagType = "";
                            $checked = "";
                            if ($invldAsnMsg != "") {
                                $ansTagType .= "<tr><td>" . $invldAsnMsg . "</td></tr>";
                            }
                            if ($row[4] == "Single Answer (Radio)") {
                                while ($row1 = loc_db_fetch_array($rslt1)) {
                                    $checked = "";
                                    $ansTagType .= "<tr>";
                                    if (getPrsnChosenAnswerID($row[0], $prsnid, $srvyID, $row1[0]) > 0) {
                                        $checked = "checked=\"checked\"";
                                    }
                                    $hntTxt = "<button type=\"button\" class=\"btn btn-primary srvyInfoButton\" onclick=\"validateQstnAns('',7,'$srvyNm',$row1[0],0,'ANSWER');\" value=\"$row[5]\"><img src=\"cmn_images/alert_info.gif\" width=\"18\" height=\"18\" style=\"height:18px !important;\"/>$row[5]</button>";
                                    $ansTagType .= "<td><input type=\"radio\" name=\"answer\" value=\"$row1[0]\" $checked> $row1[1]</td><td>&nbsp;</td><td>$hntTxt</td>";
                                    $ansTagType .= "</tr>";
                                }
                            } else if ($row[4] == "Multiple-Select Answers (Checkbox)") {
                                while ($row1 = loc_db_fetch_array($rslt1)) {
                                    $checked = "";
                                    $ansTagType .= "<tr>";
                                    if (getPrsnChosenAnswerID($row[0], $prsnid, $srvyID, $row1 [0]) > 0) {
                                        $checked = "checked=\"checked\"";
                                    }
                                    $hntTxt = "<button type=\"button\" class=\"btn btn-primary srvyInfoButton\" onclick=\"validateQstnAns('',7,'$srvyNm',$row1[0],0,'ANSWER');\"  value=\"$row[5]\"><img src=\"cmn_images/alert_info.gif\" width=\"18\" height=\"18\" style=\"height:18px !important;\"/>$row[5]</button>";
                                    $ansTagType .= "<td><input type=\"checkbox\" name=\"answer\" value=\"$row1[0]\" $checked> $row1[1]</td><td>&nbsp;</td><td>$hntTxt</td>";
                                    $ansTagType .= "</tr>";
                                }
                            } else if ($row[4] == "Single-Text Answer (Textfield)") {
                                $prvAnswer = getPrsnChosenAnswer($row[0], $prsnid, $srvyID, -1);
                                $ansTagType = "<input type=\"text\" name=\"answer\" value=\"$prvAnswer\"><br>";
                            } else {
                                $prvAnswer = getPrsnChosenAnswer($row[0], $prsnid, $srvyID, -1);
                                $ansTagType = "<textarea rows=\"4\" cols=\"50\" name=\"answer\">$prvAnswer</textarea><br>";
                            }
                            $No = 5;
                            $srvyNm = get_SrvyNm($srvyID);
                            $buttons = "<div class=\"row\">
                    <div style=\"float:left !important;vertical-align:middle;\">
        <button type=\"button\" class=\"btn btn-default btn-lg btn-block\" onclick=\"openATab('#allmodules', 'grp=$group&typ=$type&pg=$No&vtyp=0&sbmtdSrvyID=$srvyID&nxQstnIdx=" . ($_SESSION['PRV_OFFST']) . "');\" style=\"vertical-align:middle;\">
            <img src=\"cmn_images/Backward.png\" style=\"margin:1px auto;padding-right:1em; height:30px; width:auto; position: relative; vertical-align: middle;float:left;\">
            <span style=\"vertical-align:middle;\">PREVIOUS&nbsp;&nbsp;&nbsp;</span>
        </button>
            </div>";
                            $buttons .= "<div style=\"float:right !important;vertical-align:middle;\">
        <button type=\"button\" class=\"btn btn-default btn-lg btn-block\" onclick=\"validateQstnAns('#allmodules', $No, '$srvyNm', $srvyID, " . ($nxQstnIdx + 1) . ", 'NEXT');\" style=\"vertical-align:middle;\">
            <span style=\"vertical-align:middle;\">NEXT&nbsp;&nbsp;&nbsp;</span>
            <img src=\"cmn_images/Forward.png\" style=\"margin:1px auto;padding-left:1em; height:30px; width:auto; position: relative; vertical-align: middle;float:right;\">
        </button>
            </div></div>";
                            $cntent .= $ansTagType . "</tbody></table></p></div></form>" . "</div>" . $buttons . "</div>";
                        }
                    }
                } else if ($nxQstnIdx >= $ttlQstns + 1) {
                    $cntent .= "<div class=\"container-fluid\" style=\"padding:10px 20px 10px 20px;\"><div class=\"row\">"
                            . "<p style=\"margin-bottom:10px;\"><span style=\"font-weight:bold;font-size:24px;color:blue;text-shadow: 0px 1px #ccccff;\">" . $srvyNm . "</span></p>";
                    $cntent .= "<form><p>Congratulations! You have successfully completed all Sections of this Survey/Election!<br/>"
                            . "Please click on FINISH to end your Voting Session<br/>"
                            . "NB: Once you click on FINISH you cannot review your Options Selected!</p><input type=\"hidden\" name=\"shdSubmit\" value=\"1\"></form>";
                    $No = 5;
                    $nxQstnIdx = $ttlQstns;
                    $buttons = "<div class=\"row\">
                    <div style=\"float:left !important;vertical-align:middle;\">
        <button type=\"button\" class=\"btn btn-default btn-lg btn-block\" onclick=\"openATab('#allmodules', 'grp=$group&typ=$type&pg=$No&vtyp=0&sbmtdSrvyID=$srvyID&nxQstnIdx=" . ($_SESSION['PRV_OFFST'] + 1) . "');\" style=\"vertical-align:middle;\">
            <img src=\"cmn_images/Backward.png\" style=\"margin:1px auto;padding-right:1em; height:30px; width:auto; position: relative; vertical-align: middle;float:left;\">
            <span style=\"vertical-align:middle;\">PREVIOUS&nbsp;&nbsp;&nbsp;</span>
        </button>
            </div>";
                    $buttons .= "<div style=\"float:right !important;vertical-align:middle;\">
        <button type=\"button\" class=\"btn btn-default btn-lg btn-block\" onclick=\"validateQstnAns('#allmodules', $No, '$srvyNm', $srvyID, 0, 'FINISH');\" style=\"vertical-align:middle;\">
            <img src=\"cmn_images/approve1.png\" style=\"margin:1px auto;padding-right:1em; height:30px; width:auto; position: relative; vertical-align: middle;float:left;\">
            <span style=\"vertical-align:middle;\">FINISH&nbsp;&nbsp;&nbsp;</span>
        </button>
            </div></div>";
                    $cntent .= "</div>" . $buttons . "</div>";
                }
            }
            echo $breadCrm . $cntent;
        } else if ($pgNo == 6) {
            $inptPrsnAnsID = isset($_POST['prsnAnsID']) ? cleanInputData($_POST['prsnAnsID']) : -1;

            $updtSQL1 = "UPDATE self.self_person_question_answers SET is_submitted='0' WHERE prsn_ansr_id=" . $inptPrsnAnsID;
            $afctd1 = execUpdtInsSQL($updtSQL1);
            echo "<p>Successfully Re-Opened $afctd1 Selected Submitted Votes!</p>";
        } else if ($pgNo == 7) {
            $actionNm = isset($_POST['actionNm']) ? cleanInputData($_POST['actionNm']) : '';
            $pkeyID = isset($_POST['pkeyID']) ? cleanInputData($_POST['pkeyID']) : 0;
            if ($actionNm == "ANSWER") {
                echo "" . get_AnsHint($pkeyID) . "";
            } else {
                echo "" . get_QstnHint($pkeyID) . "";
            }
        } else if ($pgNo == 8) {
            if ($vwtyp == 0) {
                //Vote Validations 1
            }
        }
    }
}
