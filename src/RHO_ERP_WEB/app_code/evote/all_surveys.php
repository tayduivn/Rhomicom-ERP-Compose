<?php
$pageNo = isset($_POST['pageNo']) ? cleanInputData($_POST['pageNo']) : 1;
$lmtSze = isset($_POST['limitSze']) ? cleanInputData($_POST['limitSze']) : 10;
$sortBy = isset($_POST['sortBy']) ? cleanInputData($_POST['sortBy']) : "";
$curIdx = 0;
$orgID = $_SESSION['ORG_ID'];
$usrID = $_SESSION['USRID'];
$canAddSrvy = test_prmssns($dfltPrvldgs[6], $mdlNm);
$canEdtSrvy = test_prmssns($dfltPrvldgs[7], $mdlNm);
$canDelSrvy = test_prmssns($dfltPrvldgs[8], $mdlNm);

if (array_key_exists('lgn_num', get_defined_vars())) {
    if ($lgn_num > 0 && $canview === true) {
        if ($qstr == "DELETE") {
            if ($actyp == 1) {
                $pKeyID = isset($_POST['surveyID']) ? cleanInputData($_POST['surveyID']) : -1;
                $surveyNm = isset($_POST['surveyNm']) ? cleanInputData($_POST['surveyNm']) : "";
                echo deleteSurvey($pKeyID, $surveyNm);
            } else if ($actyp == 2) {
                $pKeyID = isset($_POST['pKeyID']) ? cleanInputData($_POST['pKeyID']) : -1;
                $qstnNm = isset($_POST['qstnNm']) ? cleanInputData($_POST['qstnNm']) : "";
                echo deleteSurveyQstns($pKeyID, $qstnNm);
            } else if ($actyp == 3) {
                $pKeyID = isset($_POST['pKeyID']) ? cleanInputData($_POST['pKeyID']) : -1;
                $ansNm = isset($_POST['ansNm']) ? cleanInputData($_POST['ansNm']) : "";
                echo deleteSurvyQstnAns($pKeyID, $ansNm);
            }
        } else if ($qstr == "UPDATE") {
            if ($actyp == 1) {
                //Surveys/Elections
                header("content-type:application/json");
                $orgid = $_SESSION['ORG_ID'];
                $inptSurveyID = isset($_POST['surveyID']) ? cleanInputData($_POST['surveyID']) : -1;
                $surveyName = isset($_POST['surveyName']) ? cleanInputData($_POST['surveyName']) : "";
                $surveyDesc = isset($_POST['surveyDesc']) ? cleanInputData($_POST['surveyDesc']) : "";
                $surveyInstructions = isset($_POST['surveyInstructions']) ? cleanInputData($_POST['surveyInstructions']) : "";
                $surveyType = isset($_POST['surveyType']) ? cleanInputData($_POST['surveyType']) : "";
                $sStartDate = isset($_POST['sStartDate']) ? cleanInputData($_POST['sStartDate']) : "";
                if ($sStartDate == "") {
                    $sStartDate = date("d-M-Y H:i:s");
                }
                $sEndDate = isset($_POST['sEndDate']) ? cleanInputData($_POST['sEndDate']) : "";
                if ($sEndDate == "") {
                    $sEndDate = "31-Dec-4000 23:59:59";
                }
                $includePrsnSetID = isset($_POST['includePrsnSetID']) ? cleanInputData($_POST['includePrsnSetID']) : -1;
                $excludePrsnSetID = isset($_POST['excludePrsnSetID']) ? cleanInputData($_POST['excludePrsnSetID']) : -1;

                $oldSurveyID = getGnrlRecID2("self.self_surveys", "srvy_name", "srvy_id", $surveyName);
                if ($sStartDate != "") {
                    $sStartDate = cnvrtDMYTmToYMDTm($sStartDate);
                }
                if ($sEndDate != "") {
                    $sEndDate = cnvrtDMYTmToYMDTm($sEndDate);
                }
                if ($surveyName != "" && ($oldSurveyID <= 0 || $oldSurveyID == $inptSurveyID)) {
                    if ($inptSurveyID <= 0) {
                        createSurvey($surveyName, $surveyDesc, $surveyInstructions, $sStartDate, $sEndDate, $includePrsnSetID, $excludePrsnSetID, $surveyType);
                        $inptSurveyID = getGnrlRecID2("self.self_surveys", "srvy_name", "srvy_id", $surveyName);
                    } else {
                        updateSurvey($inptSurveyID, $surveyName, $surveyDesc, $surveyInstructions, $sStartDate, $sEndDate, $includePrsnSetID, $excludePrsnSetID, $surveyType);
                    }
                    $arr_content['percent'] = 100;
                    $arr_content['srvyID'] = $inptSurveyID;
                    $arr_content['message'] = "<span style=\"color:green;\"><i class=\"fa fa-check\" aria-hidden=\"true\"></i></span>Survey/Election Successfully Saved!";
                    echo json_encode($arr_content);
                    exit();
                } else {
                    $arr_content['percent'] = 100;
                    $arr_content['srvyID'] = -1;
                    $arr_content['message'] = "<span style=\"color:red;\"><i class=\"fa fa-exclamation-circle\" aria-hidden=\"true\"></i>Either the New Survey/Election Name is in Use <br/>or Data Supplied is Incomplete!</span>";
                    echo json_encode($arr_content);
                    exit();
                }
            } else if ($actyp == 2) {
                //Surveys/Elections Questions
                header("content-type:application/json");
                $orgid = $_SESSION['ORG_ID'];
                $inptSurveyID = isset($_POST['surveyID']) ? cleanInputData($_POST['surveyID']) : -1;
                $inptSurveyQstnID = isset($_POST['surveyQstnID']) ? cleanInputData($_POST['surveyQstnID']) : -1;
                $sQuestionID = isset($_POST['sQuestionID']) ? cleanInputData($_POST['sQuestionID']) : -1;
                $questionDsplySQL = isset($_POST['questionDsplySQL']) ? cleanInputData($_POST['questionDsplySQL']) : "";
                $srvysInvldChoicesQry = isset($_POST['srvysInvldChoicesQry']) ? cleanInputData($_POST['srvysInvldChoicesQry']) : "";
                $srvysQstnInvldDsplyMsg = isset($_POST['srvysQstnInvldDsplyMsg']) ? cleanInputData($_POST['srvysQstnInvldDsplyMsg']) : "";
                $questionOrderNo = isset($_POST['questionOrderNo']) ? cleanInputData($_POST['questionOrderNo']) : "";
                $slctdSrvyQstnAns = isset($_POST['slctdSrvyQstnAns']) ? cleanInputData($_POST['slctdSrvyQstnAns']) : '';

                $oldSurveyQstnID = getSurveyQstnID($inptSurveyID, $sQuestionID);

                if ($questionDsplySQL != "" && ($oldSurveyQstnID <= 0 || $oldSurveyQstnID == $inptSurveyQstnID)) {
                    if ($inptSurveyQstnID <= 0) {
                        createSurveyQstns($inptSurveyID, $sQuestionID, $questionOrderNo, $questionDsplySQL, $srvysInvldChoicesQry, $srvysQstnInvldDsplyMsg);
                        $inptSurveyQstnID = getSurveyQstnID($inptSurveyID, $sQuestionID);
                    } else {
                        updateSurveyQstns($inptSurveyQstnID, $questionOrderNo, $questionDsplySQL, $srvysInvldChoicesQry, $srvysQstnInvldDsplyMsg);
                    }
                    $affctdAns = 0;
                    if (trim($slctdSrvyQstnAns, "|~") != "") {
                        //Save Question Answers
                        $variousRows = explode("|", trim($slctdSrvyQstnAns, "|"));
                        for ($z = 0; $z < count($variousRows); $z++) {
                            $crntRow = explode("~", $variousRows[$z]);
                            if (count($crntRow) == 5) {
                                $inptSrvyQstnAnsID = (int) (cleanInputData1($crntRow[0]));
                                $sqstnPssblAnsID = (int) (cleanInputData1($crntRow[1]));
                                $sqstnAnsOrderNo = (int) (cleanInputData1($crntRow[2]));
                                $sqstnDsplyCndtnSQL = cleanInputData1($crntRow[3]);
                                $isSqstnCorrectAns = cleanInputData1($crntRow[4]) == "YES" ? TRUE : FALSE;
                                $oldSrvyQstnAnsID = getSurveyQstnAnsID($inptSurveyID, $sQuestionID, $sqstnPssblAnsID);

                                if ($sqstnDsplyCndtnSQL != "" && ($oldSrvyQstnAnsID <= 0 || $oldSrvyQstnAnsID == $inptSrvyQstnAnsID)) {
                                    if ($inptSrvyQstnAnsID <= 0) {
                                        $affctdAns += createSurvyQstnAns($inptSurveyID, $sQuestionID, $sqstnPssblAnsID, $sqstnAnsOrderNo, $isSqstnCorrectAns, $sqstnDsplyCndtnSQL);
                                    } else {
                                        $affctdAns += updateSurvyQstnAns($inptSrvyQstnAnsID, $sqstnAnsOrderNo, $isSqstnCorrectAns, $sqstnDsplyCndtnSQL);
                                    }
                                }
                            }
                        }
                    }
                    $arr_content['percent'] = 100;
                    $arr_content['srvyQstnID'] = $inptSurveyQstnID;
                    $arr_content['message'] = "<span style=\"color:green;\"><i class=\"fa fa-check\" aria-hidden=\"true\"></i></span>Survey/Election Question Successfully Saved!<br/>" . $affctdAns . " Possible Answers Saved!";
                    echo json_encode($arr_content);
                    exit();
                } else {
                    $arr_content['percent'] = 100;
                    $arr_content['srvyQstnID'] = -1;
                    $arr_content['message'] = "<span style=\"color:red;\"><i class=\"fa fa-exclamation-circle\" aria-hidden=\"true\"></i>Either the New Survey/Election Question Exists <br/>or Data Supplied is Incomplete!</span>";
                    echo json_encode($arr_content);
                    exit();
                }
            } else if ($actyp == 3) {
                //Re-open Vote
                $inptAnsID = cleanInputData($_POST['pKeyID']);
                $inptVotrNm = cleanInputData($_POST['votrNm']);
                $status = cleanInputData($_POST['nwStatus']);
                echo reopenVote($inptAnsID, $inptVotrNm);
            }
        } else {
            $prsnid = $_SESSION['PRSN_ID'];
            if ($vwtyp == 0) {
                $sbmtdSrvyID = isset($_POST['sbmtdSrvyID']) ? $_POST['sbmtdSrvyID'] : -1;
                echo $cntent . "<li>
                    <span class = \"divider\"><i class=\"fa fa-angle-right\" aria-hidden=\"true\"></i></span>
                                    <span style=\"text-decoration:none;\">All Elections/Surveys</span>
				</li>
                               </ul>
                              </div>";
                $total = get_SurveysTtl($srchFor, $srchIn);
                if ($pageNo > ceil($total / $lmtSze)) {
                    $pageNo = 1;
                } else if ($pageNo < 1) {
                    $pageNo = ceil($total / $lmtSze);
                }

                $curIdx = $pageNo - 1;
                $result = get_Surveys($srchFor, $srchIn, $curIdx, $lmtSze);
                $cntr = 0;
                $colClassType1 = "col-lg-2";
                $colClassType2 = "col-lg-3";
                $colClassType3 = "col-lg-4";
                ?>
                <form id='allSrvysForm' action='' method='post' accept-charset='UTF-8'>
                    <div class="row rhoRowMargin">
                        <?php if ($canAddSrvy === true) { ?>
                            <div class="<?php echo $colClassType2; ?>" style="padding:0px 1px 0px 1px !important;"> 
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="getOneAllSrvyForm(-1, 6);" style="width:100% !important;">
                                        <img src="cmn_images/add1-64.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                        New Survey
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="saveSrvyForm();" style="width:100% !important;">
                                        <img src="cmn_images/FloppyDisk.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                        Save
                                    </button>
                                </div>
                            </div>
                            <?php
                        } else {
                            $colClassType1 = "col-lg-4";
                            $colClassType2 = "col-lg-4";
                            $colClassType3 = "col-lg-4";
                        }
                        ?>
                        <div class="<?php echo $colClassType2; ?>" style="padding:0px 15px 0px 15px !important;">
                            <div class="input-group">
                                <input class="form-control" id="allSrvysSrchFor" type = "text" placeholder="Search For" value="<?php echo trim(str_replace("%", " ", $srchFor)); ?>" onkeyup="enterKeyFuncAllSrvys(event, '', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>')">
                                <input id="allSrvysPageNo" type = "hidden" value="<?php echo $pageNo; ?>">
                                <label class="btn btn-primary btn-file input-group-addon" onclick="getAllSrvys('clear', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>')">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </label>
                                <label class="btn btn-primary btn-file input-group-addon" onclick="getAllSrvys('', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>')">
                                    <span class="glyphicon glyphicon-search"></span>
                                </label> 
                            </div>
                        </div>
                        <div class="<?php echo $colClassType3; ?>">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-filter"></span></span>
                                <select data-placeholder="Select..." class="form-control chosen-select" id="allSrvysSrchIn">
                                    <?php
                                    $valslctdArry = array("", "");
                                    $srchInsArrys = array("Name", "Instructions");

                                    for ($z = 0; $z < count($srchInsArrys); $z++) {
                                        if ($srchIn == $srchInsArrys[$z]) {
                                            $valslctdArry[$z] = "selected";
                                        }
                                        ?>
                                        <option value="<?php echo $srchInsArrys[$z]; ?>" <?php echo $valslctdArry[$z]; ?>><?php echo $srchInsArrys[$z]; ?></option>
                                    <?php } ?>
                                </select>
                                <span class="input-group-addon" style="max-width: 1px !important;padding:0px !important;width:1px !important;border:none !important;"></span>
                                <select data-placeholder="Select..." class="form-control chosen-select" id="allSrvysDsplySze" style="min-width:70px !important;">                            
                                    <?php
                                    $valslctdArry = array("", "", "", "", "", "", "", "");
                                    $dsplySzeArry = array(1, 5, 10, 15, 30, 50, 100, 500, 1000);
                                    for ($y = 0; $y < count($dsplySzeArry); $y++) {
                                        if ($lmtSze == $dsplySzeArry[$y]) {
                                            $valslctdArry[$y] = "selected";
                                        } else {
                                            $valslctdArry[$y] = "";
                                        }
                                        ?>
                                        <option value="<?php echo $dsplySzeArry[$y]; ?>" <?php echo $valslctdArry[$y]; ?>><?php echo $dsplySzeArry[$y]; ?></option>                            
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="<?php echo $colClassType1; ?>">
                            <nav aria-label="Page navigation">
                                <ul class="pagination" style="margin: 0px !important;">
                                    <li>
                                        <a class="rhopagination" href="javascript:getAllSrvys('previous', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>');" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="rhopagination" href="javascript:getAllSrvys('next', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>');" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>   
                </div>
                <div class="row" style="padding:0px 15px 0px 15px !important"> 
                    <div  class="col-md-3" style="padding:0px 1px 0px 1px !important">
                        <fieldset class="basic_person_fs">                                        
                            <table class="table table-striped table-bordered table-responsive" id="allSrvysTable" cellspacing="0" width="100%" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Name</th>                                        
                                        <?php if ($canDelSrvy === true) { ?>
                                            <th>&nbsp;</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = loc_db_fetch_array($result)) {
                                        if ($sbmtdSrvyID <= 0 && $cntr <= 0) {
                                            $sbmtdSrvyID = $row[0];
                                        }
                                        $cntr += 1;
                                        ?>
                                        <tr id="allSrvysRow_<?php echo $cntr; ?>" class="hand_cursor">                                    
                                            <td class="lovtd"><?php echo ($curIdx * $lmtSze) + ($cntr); ?></td>
                                            <td class="lovtd"><?php echo $row[1]; ?>
                                                <input type="hidden" class="form-control" aria-label="..." id="allSrvysRow<?php echo $cntr; ?>_SrvyID" value="<?php echo $row[0]; ?>">
                                                <input type="hidden" class="form-control" aria-label="..." id="allSrvysRow<?php echo $cntr; ?>_SrvyNm" value="<?php echo $row[1]; ?>">
                                            </td>
                                            <?php if ($canDelSrvy === true) { ?>
                                                <td class="lovtd">
                                                    <button type="button" class="btn btn-default" style="margin: 0px !important;padding:0px 3px 2px 4px !important;" onclick="delSrvy('allSrvysRow_<?php echo $cntr; ?>');" data-toggle="tooltip" data-placement="bottom" title="Delete Survey">
                                                        <img src="cmn_images/no.png" style="height:15px; width:auto; position: relative; vertical-align: middle;">
                                                    </button>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>                        
                        </fieldset>
                    </div>                        
                    <div  class="col-md-9" style="padding:0px 1px 0px 1px !important">
                        <fieldset class="basic_person_fs" style="padding-top:10px !important;">
                            <div class="rho-container-fluid" id="allSrvysDetailInfo">
                                <?php
                                if ($sbmtdSrvyID > 0) {
                                    $result1 = get_SurveysDet($sbmtdSrvyID);
                                    while ($row1 = loc_db_fetch_array($result1)) {
                                        ?>
                                        <div class="row phone-only-btn" style="margin: 0px 0px 10px 0px !important;">
                                            <div class="col-md-12" style="padding:0px 0px 0px 0px !important;">
                                                <button type="button" class="btn btn-default btn-sm phone-only-btn" onclick="openATab('#allSrvysDetailInfo', 'grp=19&typ=10&pg=2&vtyp=1&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>');">Survey Details</button>
                                                <button type="button" class="btn btn-default btn-sm phone-only-btn" onclick="openATab('#srvysQstnsPage', 'grp=19&typ=10&pg=2&vtyp=2&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>');">Survey Questions</button>
                                            </div>
                                        </div>
                                        <ul class="nav nav-tabs rho-hideable-tabs" style="margin-top:-5px !important;">
                                            <li class="active"><a data-toggle="tab" data-rhodata="&pg=2&vtyp=1&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>" href="#srvysDetPage" id="srvysDetPagetab">Survey Details</a></li>
                                            <li><a data-toggle="tabajxsrvy" data-rhodata="&pg=2&vtyp=2&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>" href="#srvysQstnsPage" id="srvysQstnsPagetab">Survey Questions</a></li>
                                        </ul>
                                        <div class="row">                  
                                            <div class="col-md-12">
                                                <div class="custDiv"> 
                                                    <div class="tab-content">
                                                        <div id="srvysDetPage" class="tab-pane fadein active" style="border:none !important;"> 

                                                            <div class="row">
                                                                <div  class="col-md-6" style="padding:0px 3px 0px 3px !important;">
                                                                    <fieldset class="basic_person_fs" style="padding:10px 3px 0px 3px !important;"> 
                                                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                                                            <label for="srvysSurveyName" class="control-label col-lg-4">Survey Name:</label>
                                                                            <div  class="col-lg-8">
                                                                                <?php if ($canEdtSrvy === true) { ?>
                                                                                    <input type="text" class="form-control" aria-label="..." id="srvysSurveyName" name="srvysSurveyName" value="<?php echo $row1[1]; ?>" style="width:100%;">
                                                                                    <input type="hidden" class="form-control" aria-label="..." id="srvysSurveyID" name="srvysSurveyID" value="<?php echo $row1[0]; ?>">
                                                                                <?php } else {
                                                                                    ?>
                                                                                    <span><?php echo $row1[1]; ?></span>
                                                                                    <?php
                                                                                }
                                                                                ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                                                            <label for="srvysSurveyDesc" class="control-label col-lg-4">Description:</label>
                                                                            <div  class="col-lg-8">
                                                                                <?php if ($canEdtSrvy === true) { ?>
                                                                                    <textarea class="form-control" aria-label="..." id="srvysSurveyDesc" name="srvysSurveyDesc" style="width:100%;" cols="3" rows="5"><?php echo $row1[2]; ?></textarea>
                                                                                <?php } else {
                                                                                    ?>
                                                                                    <span><?php echo $row1[2]; ?></span>
                                                                                    <?php
                                                                                }
                                                                                ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                                                            <label for="srvysSurveyType" class="control-label col-lg-6">Survey Type:</label>
                                                                            <div class="col-lg-6">
                                                                                <?php if ($canEdtSrvy === true) { ?>
                                                                                    <select data-placeholder="Select..." class="form-control chosen-select" id="srvysSurveyType">
                                                                                        <?php
                                                                                        $valslctdArry = array("", "", "", "");
                                                                                        $srchInsArrys = array("ELECTION", "SURVEY", "QUESTIONNAIRE", "OTHER");
                                                                                        for ($z = 0; $z < count($srchInsArrys); $z++) {
                                                                                            if ($row1[8] == $srchInsArrys[$z]) {
                                                                                                $valslctdArry[$z] = "selected";
                                                                                            }
                                                                                            ?>
                                                                                            <option value="<?php echo $srchInsArrys[$z]; ?>" <?php echo $valslctdArry[$z]; ?>><?php echo $srchInsArrys[$z]; ?></option>
                                                                                        <?php } ?>
                                                                                    </select>
                                                                                <?php } else {
                                                                                    ?>
                                                                                    <span><?php echo $row1[8]; ?></span>
                                                                                    <?php
                                                                                }
                                                                                ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                                                            <label for="srvysStartDate" class="control-label col-md-4">Start Date:</label>
                                                                            <div  class="col-md-8">
                                                                                <?php
                                                                                if ($canEdtSrvy === true) {
                                                                                    ?>
                                                                                    <div class="input-group date form_date_tme" data-date="" data-date-format="dd-M-yyyy hh:ii:ss" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd hh:ii:ss">
                                                                                        <input class="form-control" size="16" type="text" id="srvysStartDate" value="<?php echo cnvrtYMDTmToDMYTm($row1[4]); ?>" readonly="">
                                                                                        <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                                                                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                                                                    </div>
                                                                                <?php } else { ?>
                                                                                    <span><?php echo cnvrtYMDTmToDMYTm($row1[4]); ?></span>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                                                            <label for="srvysEndDate" class="control-label col-md-4">End Date:</label>
                                                                            <div  class="col-md-8">
                                                                                <?php
                                                                                if ($canEdtSrvy === true) {
                                                                                    ?>
                                                                                    <div class="input-group date form_date_tme" data-date="" data-date-format="dd-M-yyyy hh:ii:ss" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd hh:ii:ss">
                                                                                        <input class="form-control" size="16" type="text" id="srvysEndDate" value="<?php echo cnvrtYMDTmToDMYTm($row1[5]); ?>" readonly="">
                                                                                        <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                                                                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                                                                    </div>
                                                                                <?php } else { ?>
                                                                                    <span><?php echo cnvrtYMDTmToDMYTm($row1[5]); ?></span>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px!important;">
                                                                            <label for="srvysIncPrsnSetNm" class="control-label col-md-4">Eligible Person Set:</label>
                                                                            <div  class="col-lg-8">
                                                                                <div class="input-group">
                                                                                    <input type="text" class="form-control" aria-label="..." id="srvysIncPrsnSetNm" name="srvysIncPrsnSetNm" value="<?php echo $row1[7]; ?>" readonly="true">
                                                                                    <input type="hidden" class="form-control" aria-label="..." id="srvysIncPrsnSetID" name="srvysIncPrsnSetID" value="<?php echo $row1[6]; ?>">
                                                                                    <input type="hidden" class="form-control" aria-label="..." id="srvysOrgID" name="srvysOrgID" value="<?php echo $orgID; ?>">
                                                                                    <label class="btn btn-primary btn-file input-group-addon" onclick="getLovsPage('myLovModal', 'myLovModalTitle', 'myLovModalBody', 'Person Sets for Payments', 'srvysOrgID', '', '', 'radio', true, '<?php echo $row1[6]; ?>', 'srvysIncPrsnSetID', 'srvysIncPrsnSetNm', 'clear', 0, '');">
                                                                                        <span class="glyphicon glyphicon-th-list"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px!important;">
                                                                            <label for="srvysExcPrsnSetNm" class="control-label col-md-4">Set for Persons to Exclude:</label>
                                                                            <div  class="col-lg-8">
                                                                                <div class="input-group">
                                                                                    <input type="text" class="form-control" aria-label="..." id="srvysExcPrsnSetNm" name="srvysExcPrsnSetNm" value="<?php echo $row1[10]; ?>" readonly="true">
                                                                                    <input type="hidden" class="form-control" aria-label="..." id="srvysExcPrsnSetID" name="srvysExcPrsnSetID" value="<?php echo $row1[9]; ?>">
                                                                                    <label class="btn btn-primary btn-file input-group-addon" onclick="getLovsPage('myLovModal', 'myLovModalTitle', 'myLovModalBody', 'Person Sets for Payments', 'srvysOrgID', '', '', 'radio', true, '<?php echo $row[9]; ?>', 'srvysExcPrsnSetID', 'srvysExcPrsnSetNm', 'clear', 0, '');">
                                                                                        <span class="glyphicon glyphicon-th-list"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;"> 
                                                                            <label for="buttons" class="control-label col-md-4">Monitor Survey/Election:</label>
                                                                            <div  class="col-md-8">
                                                                                <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="getOneSrvyResults(<?php echo $row1[0]; ?>, 8);" style="width:100% !important;">
                                                                                    <img src="cmn_images/Flag_blue.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                                                                    Results
                                                                                </button>
                                                                                <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="getOneSrvyRspnsForm(<?php echo $row1[0]; ?>, 7);" style="width:100% !important;">
                                                                                    <img src="cmn_images/election.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                                                                    Responses
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </fieldset>
                                                                </div>
                                                                <div  class="col-md-6" style="padding:0px 3px 0px 3px !important;">
                                                                    <fieldset class = "basic_person_fs">
                                                                        <div class="row" style="padding:0px 15px 0px 15px !important;">
                                                                            <div id="srvyInstructions"></div> 
                                                                        </div>
                                                                    </fieldset>
                                                                    <input type="hidden" id="allSrvysInstructns" value="<?php echo urlencode($row1[3]); ?>">
                                                                </div>
                                                            </div> 
                                                        </div>
                                                        <div id="srvysQstnsPage" class="tab-pane fade" style="border:none !important;padding:1px !important;"></div>
                                                    </div>                        
                                                </div>                         
                                            </div>                
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                ?>
                                <span>No Results Found</span>
                                <?php
                            }
                            ?>
                        </fieldset>
                    </div>
                </div>
                </form>
                <?php
            } else if ($vwtyp == 1) {
                $sbmtdSrvyID = isset($_POST['sbmtdSrvyID']) ? $_POST['sbmtdSrvyID'] : -1;

                if ($sbmtdSrvyID > 0) {
                    $result1 = get_SurveysDet($sbmtdSrvyID);
                    while ($row1 = loc_db_fetch_array($result1)) {
                        ?>
                        <div class="row phone-only-btn" style="margin: 0px 0px 10px 0px !important;">
                            <div class="col-md-12" style="padding:0px 0px 0px 0px !important;">
                                <button type="button" class="btn btn-default btn-sm phone-only-btn" onclick="openATab('#allSrvysDetailInfo', 'grp=19&typ=10&pg=2&vtyp=1&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>');">Survey Details</button>
                                <button type="button" class="btn btn-default btn-sm phone-only-btn" onclick="openATab('#srvysQstnsPage', 'grp=19&typ=10&pg=2&vtyp=2&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>');">Survey Questions</button>
                            </div>
                        </div>
                        <ul class="nav nav-tabs rho-hideable-tabs" style="margin-top:-5px !important;">
                            <li class="active"><a data-toggle="tab" data-rhodata="&pg=2&vtyp=1&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>" href="#srvysDetPage" id="srvysDetPagetab">Survey Details</a></li>
                            <li><a data-toggle="tabajxsrvy" data-rhodata="&pg=2&vtyp=2&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>" href="#srvysQstnsPage" id="srvysQstnsPagetab">Survey Questions</a></li>
                        </ul>
                        <div class="row">                  
                            <div class="col-md-12">
                                <div class="custDiv"> 
                                    <div class="tab-content">
                                        <div id="srvysDetPage" class="tab-pane fadein active" style="border:none !important;"> 

                                            <div class="row">
                                                <div  class="col-md-6" style="padding:0px 3px 0px 3px !important;">
                                                    <fieldset class="basic_person_fs" style="padding:10px 3px 0px 3px !important;"> 
                                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                                            <label for="srvysSurveyName" class="control-label col-lg-4">Survey Name:</label>
                                                            <div  class="col-lg-8">
                                                                <?php if ($canEdtSrvy === true) { ?>
                                                                    <input type="text" class="form-control" aria-label="..." id="srvysSurveyName" name="srvysSurveyName" value="<?php echo $row1[1]; ?>" style="width:100%;">
                                                                    <input type="hidden" class="form-control" aria-label="..." id="srvysSurveyID" name="srvysSurveyID" value="<?php echo $row1[0]; ?>">
                                                                <?php } else {
                                                                    ?>
                                                                    <span><?php echo $row1[1]; ?></span>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                                            <label for="srvysSurveyDesc" class="control-label col-lg-4">Description:</label>
                                                            <div  class="col-lg-8">
                                                                <?php if ($canEdtSrvy === true) { ?>
                                                                    <textarea class="form-control" aria-label="..." id="srvysSurveyDesc" name="srvysSurveyDesc" style="width:100%;" cols="3" rows="5"><?php echo $row1[2]; ?></textarea>
                                                                <?php } else {
                                                                    ?>
                                                                    <span><?php echo $row1[2]; ?></span>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                                            <label for="srvysSurveyType" class="control-label col-lg-6">Survey Type:</label>
                                                            <div class="col-lg-6">
                                                                <?php if ($canEdtSrvy === true) { ?>
                                                                    <select data-placeholder="Select..." class="form-control chosen-select" id="srvysSurveyType">
                                                                        <?php
                                                                        $valslctdArry = array("", "", "", "");
                                                                        $srchInsArrys = array("ELECTION", "SURVEY", "QUESTIONNAIRE", "OTHER");
                                                                        for ($z = 0; $z < count($srchInsArrys); $z++) {
                                                                            if ($row1[8] == $srchInsArrys[$z]) {
                                                                                $valslctdArry[$z] = "selected";
                                                                            }
                                                                            ?>
                                                                            <option value="<?php echo $srchInsArrys[$z]; ?>" <?php echo $valslctdArry[$z]; ?>><?php echo $srchInsArrys[$z]; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                <?php } else {
                                                                    ?>
                                                                    <span><?php echo $row1[8]; ?></span>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                                            <label for="srvysStartDate" class="control-label col-md-4">Start Date:</label>
                                                            <div  class="col-md-8">
                                                                <?php
                                                                if ($canEdtSrvy === true) {
                                                                    ?>
                                                                    <div class="input-group date form_date_tme" data-date="" data-date-format="dd-M-yyyy hh:ii:ss" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd hh:ii:ss">
                                                                        <input class="form-control" size="16" type="text" id="srvysStartDate" value="<?php echo cnvrtYMDTmToDMYTm($row1[4]); ?>" readonly="">
                                                                        <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                                                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                                                    </div>
                                                                <?php } else { ?>
                                                                    <span><?php echo cnvrtYMDTmToDMYTm($row1[4]); ?></span>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                                            <label for="srvysEndDate" class="control-label col-md-4">End Date:</label>
                                                            <div  class="col-md-8">
                                                                <?php
                                                                if ($canEdtSrvy === true) {
                                                                    ?>
                                                                    <div class="input-group date form_date_tme" data-date="" data-date-format="dd-M-yyyy hh:ii:ss" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd hh:ii:ss">
                                                                        <input class="form-control" size="16" type="text" id="srvysEndDate" value="<?php echo cnvrtYMDTmToDMYTm($row1[5]); ?>" readonly="">
                                                                        <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                                                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                                                    </div>
                                                                <?php } else { ?>
                                                                    <span><?php echo cnvrtYMDTmToDMYTm($row1[5]); ?></span>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px!important;">
                                                            <label for="srvysIncPrsnSetNm" class="control-label col-md-4">Eligible Person Set:</label>
                                                            <div  class="col-lg-8">
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control" aria-label="..." id="srvysIncPrsnSetNm" name="srvysIncPrsnSetNm" value="<?php echo $row1[7]; ?>" readonly="true">
                                                                    <input type="hidden" class="form-control" aria-label="..." id="srvysIncPrsnSetID" name="srvysIncPrsnSetID" value="<?php echo $row1[6]; ?>">
                                                                    <input type="hidden" class="form-control" aria-label="..." id="srvysOrgID" name="srvysOrgID" value="<?php echo $orgID; ?>">
                                                                    <label class="btn btn-primary btn-file input-group-addon" onclick="getLovsPage('myLovModal', 'myLovModalTitle', 'myLovModalBody', 'Person Sets for Payments', 'srvysOrgID', '', '', 'radio', true, '<?php echo $row1[6]; ?>', 'srvysIncPrsnSetID', 'srvysIncPrsnSetNm', 'clear', 0, '', function () {
                                                                                                        reloadPrsnsInSet();
                                                                                                    });">
                                                                        <span class="glyphicon glyphicon-th-list"></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px!important;">
                                                            <label for="srvysExcPrsnSetNm" class="control-label col-md-4">Set for Persons to Exclude:</label>
                                                            <div  class="col-lg-8">
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control" aria-label="..." id="srvysExcPrsnSetNm" name="srvysExcPrsnSetNm" value="<?php echo $row1[10]; ?>" readonly="true">
                                                                    <input type="hidden" class="form-control" aria-label="..." id="srvysExcPrsnSetID" name="srvysExcPrsnSetID" value="<?php echo $row1[9]; ?>">
                                                                    <label class="btn btn-primary btn-file input-group-addon" onclick="getLovsPage('myLovModal', 'myLovModalTitle', 'myLovModalBody', 'Person Sets for Payments', 'srvysOrgID', '', '', 'radio', true, '<?php echo $row[9]; ?>', 'srvysExcPrsnSetID', 'srvysExcPrsnSetNm', 'clear', 0, '', function () {
                                                                                                        reloadPrsnsInSet();
                                                                                                    });">
                                                                        <span class="glyphicon glyphicon-th-list"></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;"> 
                                                            <label for="buttons" class="control-label col-md-4">Monitor Survey/Election:</label>
                                                            <div  class="col-md-8">
                                                                <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="getOneSrvyResults(<?php echo $row1[0]; ?>, 8);" style="width:100% !important;">
                                                                    <img src="cmn_images/Flag_blue.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                                                    Results
                                                                </button>
                                                                <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="getOneSrvyRspnsForm(<?php echo $row1[0]; ?>, 7);" style="width:100% !important;">
                                                                    <img src="cmn_images/election.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                                                    Responses
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div  class="col-md-6" style="padding:0px 3px 0px 3px !important;">
                                                    <fieldset class = "basic_person_fs">
                                                        <div class="row" style="padding:0px 15px 0px 15px !important;">
                                                            <div id="srvyInstructions"></div> 
                                                        </div>
                                                    </fieldset>
                                                    <input type="hidden" id="allSrvysInstructns" value="<?php echo urlencode($row1[3]); ?>">
                                                </div>
                                            </div> 
                                        </div>
                                        <div id="srvysQstnsPage" class="tab-pane fade" style="border:none !important;padding:1px !important;"></div>
                                    </div>                        
                                </div>                         
                            </div>                
                        </div>
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <span>No Results Found</span>
                    <?php
                }
            } else if ($vwtyp == 2) {
                $sbmtdSrvyID = isset($_POST['sbmtdSrvyID']) ? $_POST['sbmtdSrvyID'] : -1;
                $total = get_SrvyQuestionsTtl($srchFor, $sbmtdSrvyID);
                if ($pageNo > ceil($total / $lmtSze)) {
                    $pageNo = 1;
                } else if ($pageNo < 1) {
                    $pageNo = ceil($total / $lmtSze);
                }

                $curIdx = $pageNo - 1;
                $result2 = get_SrvyQuestions($srchFor, $curIdx, $lmtSze, $sbmtdSrvyID);
                $colClassType1 = "col-lg-2";
                $colClassType2 = "col-lg-3";
                $colClassType3 = "col-lg-4";
                $vwtyp = 2;
                ?>
                <div class="row">
                    <div class="<?php echo $colClassType2; ?>" style="padding:0px 1px 0px 1px !important;"> 
                        <div class="col-md-12">
                            <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="getOneSrvyQstnsForm(<?php echo $sbmtdSrvyID; ?>, -1, 5, 'New Survey/Election Question');" style="width:100% !important;">
                                <img src="cmn_images/add1-64.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                New Question
                            </button>
                        </div>
                    </div>
                    <div class="<?php echo $colClassType3; ?>" style="padding:0px 15px 0px 15px !important;">
                        <div class="input-group">
                            <input class="form-control" id="srvyQstnsSrchFor" type = "text" placeholder="Search For" value="<?php echo trim(str_replace("%", " ", $srchFor)); ?>" onkeyup="enterKeyFuncSrvyQstns(event, '', '#srvysQstnsPage', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>');">
                            <input id="srvyQstnsPageNo" type = "hidden" value="<?php echo $pageNo; ?>">
                            <label class="btn btn-primary btn-file input-group-addon" onclick="getSrvyQstns('clear', '#srvysQstnsPage', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>');">
                                <span class="glyphicon glyphicon-remove"></span>
                            </label>
                            <label class="btn btn-primary btn-file input-group-addon" onclick="getSrvyQstns('', '#srvysQstnsPage', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>');">
                                <span class="glyphicon glyphicon-search"></span>
                            </label> 
                        </div>
                    </div>
                    <div class="<?php echo $colClassType2; ?>">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-filter"></span></span>
                            <!--<select data-placeholder="Select..." class="form-control chosen-select" id="srvyQstnsSrchIn">
                            <?php
                            $valslctdArry = array("");
                            $srchInsArrys = array("Question");

                            for ($z = 0; $z < count($srchInsArrys); $z++) {
                                if ($srchIn == $srchInsArrys[$z]) {
                                    $valslctdArry[$z] = "selected";
                                }
                                ?>
                                                                                                                                                                                                                            <option value="<?php echo $srchInsArrys[$z]; ?>" <?php echo $valslctdArry[$z]; ?>><?php echo $srchInsArrys[$z]; ?></option>
                            <?php } ?>
                            </select>-->
                            <span class="input-group-addon" style="max-width: 1px !important;padding:0px !important;width:1px !important;border:none !important;"></span>
                            <select data-placeholder="Select..." class="form-control chosen-select" id="srvyQstnsDsplySze" style="min-width:70px !important;">                            
                                <?php
                                $valslctdArry = array("", "", "", "", "", "", "", "");
                                $dsplySzeArry = array(1, 5, 10, 15, 30, 50, 100, 500, 1000);
                                for ($y = 0; $y < count($dsplySzeArry); $y++) {
                                    if ($lmtSze == $dsplySzeArry[$y]) {
                                        $valslctdArry[$y] = "selected";
                                    } else {
                                        $valslctdArry[$y] = "";
                                    }
                                    ?>
                                    <option value="<?php echo $dsplySzeArry[$y]; ?>" <?php echo $valslctdArry[$y]; ?>><?php echo $dsplySzeArry[$y]; ?></option>                            
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="<?php echo $colClassType1; ?>">
                        <nav aria-label="Page navigation">
                            <ul class="pagination" style="margin: 0px !important;">
                                <li>
                                    <a class="rhopagination" href="javascript:getSrvyQstns('previous', '#srvysQstnsPage', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>');" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="rhopagination" href="javascript:getSrvyQstns('next', '#srvysQstnsPage', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>');" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="row"> 
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered table-responsive" id="srvyQstnsTable" cellspacing="0" width="100%" style="width:100%;min-width: 600px !important;">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Question</th>
                                    <!--<th>Question Display Condition</th>-->
                                    <th>Question ID</th>
                                    <th>Order No.</th>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $cntr = 0;
                                while ($row2 = loc_db_fetch_array($result2)) {
                                    $cntr += 1;
                                    ?>
                                    <tr id="srvyQstnsRow_<?php echo $cntr; ?>">                                    
                                        <td class="lovtd"><span><?php echo ($curIdx * $lmtSze) + ($cntr); ?></span></td>
                                        <td class="lovtd">                                                                   
                                            <span><?php echo $row2[1]; ?></span>
                                            <input type="hidden" class="form-control" aria-label="..." id="srvyQstnsRow<?php echo $cntr; ?>_SrvyQstnID" value="<?php echo $row2[0]; ?>" style="width:100% !important;">                                              
                                        </td>                                                
                                        <!--<td class="lovtd">
                                            <span><?php echo $row2[2]; ?></span>                                                       
                                        </td>-->                                               
                                        <td class="lovtd">
                                            <span><?php echo $row2[3]; ?></span>                                                       
                                        </td>                                               
                                        <td class="lovtd">
                                            <span><?php echo $row2[4]; ?></span>                                                       
                                        </td>
                                        <td class="lovtd">
                                            <button type="button" class="btn btn-default" style="margin: 0px !important;padding:0px 3px 2px 4px !important;" onclick="getOneSrvyQstnsForm(<?php echo $sbmtdSrvyID; ?>, <?php echo $row2[3]; ?>, 3, 'Survey/Election Question (ID:<?php echo $row2[3]; ?>)');" data-toggle="tooltip" data-placement="bottom" title="View Details">
                                                <img src="cmn_images/kghostview.png" style="height:15px; width:auto; position: relative; vertical-align: middle;">
                                            </button>
                                        </td>
                                        <td class="lovtd">
                                            <button type="button" class="btn btn-default" style="margin: 0px !important;padding:0px 3px 2px 4px !important;" onclick="delSrvyQstn('srvyQstnsRow_<?php echo $cntr; ?>');" data-toggle="tooltip" data-placement="bottom" title="Delete Question from Survey">
                                                <img src="cmn_images/no.png" style="height:15px; width:auto; position: relative; vertical-align: middle;">
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>                     
                </div> 
                <?php
            } else if ($vwtyp == 3) {
                $sbmtdSrvyID = isset($_POST['sbmtdSrvyID']) ? $_POST['sbmtdSrvyID'] : -1;
                $sbmtdQstnID = isset($_POST['sbmtdQstnID']) ? $_POST['sbmtdQstnID'] : -1;
                if ($sbmtdSrvyID > 0 && $sbmtdQstnID > 0) {
                    $result1 = get_SrvyQuestionsDet($sbmtdQstnID, $sbmtdSrvyID);
                    while ($row1 = loc_db_fetch_array($result1)) {
                        ?>
                        <form id='srvyQstnAnsForm' action='' method='post' accept-charset='UTF-8'>
                            <div class="row">
                                <fieldset class="basic_person_fs" style="padding:10px 3px 0px 3px !important;">
                                    <div  class="col-md-6" style="padding:0px 3px 0px 3px !important;"> 
                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                            <label for="srvysQstn" class="control-label col-lg-3">Question:</label>
                                            <div  class="col-lg-9 formtd">                                            
                                                <span><?php echo $row1[1]; ?></span>
                                                <input type="hidden" class="form-control" aria-label="..." id="srvysSrvyQstnID" name="srvysSrvyQstnID" value="<?php echo $row1[0]; ?>">
                                                <input type="hidden" class="form-control" aria-label="..." id="srvysQstnID" name="srvysQstnID" value="<?php echo $row1[3]; ?>">
                                                <input type="hidden" class="form-control" aria-label="..." id="srvyID" name="srvyID" value="<?php echo $sbmtdSrvyID; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                            <label for="srvysQstnOrder" class="control-label col-lg-3">Order No.:</label>
                                            <div  class="col-lg-3 formtd">
                                                <?php if ($canEdtSrvy === true) { ?>
                                                    <input type="number" min="1" max="9999" class="form-control" aria-label="..." id="srvysQstnOrder" name="srvysQstnOrder" value="<?php echo $row1[4]; ?>" style="width:100%;">
                                                <?php } else {
                                                    ?>
                                                    <span><?php echo $row1[4]; ?></span>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                            <label for="srvysQstnDsplyCndtn" class="control-label col-lg-3">Question Display Condition:</label>
                                            <div  class="col-lg-9 formtd">
                                                <?php if ($canEdtSrvy === true) { ?>
                                                    <textarea class="form-control" aria-label="..." id="srvysQstnDsplyCndtn" name="srvysQstnDsplyCndtn" style="width:100%;" cols="3" rows="5"><?php echo $row1[2]; ?></textarea>
                                                <?php } else {
                                                    ?>
                                                    <span><?php echo $row1[2]; ?></span>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>

                                    </div>
                                    <div  class="col-md-6" style="padding:0px 3px 0px 3px !important;">
                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                            <label for="srvysInvldChoicesQry" class="control-label col-lg-3">Choices Validity Query:</label>
                                            <div  class="col-lg-9 formtd">
                                                <?php if ($canEdtSrvy === true) { ?>
                                                    <textarea class="form-control" aria-label="..." id="srvysInvldChoicesQry" name="srvysInvldChoicesQry" style="width:100%;" cols="3" rows="4"><?php echo $row1[5]; ?></textarea>
                                                <?php } else {
                                                    ?>
                                                    <span><?php echo $row1[5]; ?></span>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                            <label for="srvysQstnInvldDsplyMsg" class="control-label col-lg-3">Invalid Choices Message:</label>
                                            <div class="col-lg-9 formtd">
                                                <?php if ($canEdtSrvy === true) { ?>
                                                    <textarea class="form-control" aria-label="..." id="srvysQstnInvldDsplyMsg" name="srvysQstnInvldDsplyMsg" style="width:100%;" cols="3" rows="3"><?php echo $row1[6]; ?></textarea>
                                                <?php } else {
                                                    ?>
                                                    <span><?php echo $row1[6]; ?></span>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            <?php
                            $pageNo = 1;
                            $lmtSze = 10;
                            $srchFor = "%";
                            $srchIn = "Name/Number";
                            $total = get_SrvyQstnAnsTtl($srchFor, $sbmtdQstnID, $sbmtdSrvyID);
                            if ($pageNo > ceil($total / $lmtSze)) {
                                $pageNo = 1;
                            } else if ($pageNo < 1) {
                                $pageNo = ceil($total / $lmtSze);
                            }

                            $curIdx = $pageNo - 1;
                            $result2 = get_SrvyQstnAns($srchFor, $curIdx, $lmtSze, $sbmtdQstnID, $sbmtdSrvyID);
                            $colClassType1 = "col-lg-2";
                            $colClassType2 = "col-lg-3";
                            $colClassType3 = "col-lg-4";
                            $vwtyp = 4;
                            ?>
                            <div class="row" id="srvyQstnAnsList" style="padding:0px 1px 0px 1px !important">
                                <div class="row">
                                    <?php
                                    if ($canEdtSrvy === true) {
                                        $nwRowHtml = urlencode("<tr id=\"srvyQstnAnsRow__WWW123WWW\">"
                                                . "<td class=\"lovtd\"><span class=\"normaltd\">New</span></td>"
                                                . "<td class=\"lovtd\">
                                              <div class=\"form-group form-group-sm\" style=\"width:100% !important;\">
                                                <div class=\"input-group\"  style=\"width:100%;\">
                                                    <input type=\"text\" class=\"form-control\" aria-label=\"...\" id=\"srvyQstnAnsRow_WWW123WWW_PsblAnsNm\" value=\"\" readonly=\"true\">
                                                    <input style=\"max-width:40px;width:40px;\" type=\"hidden\" class=\"form-control\" aria-label=\"...\" id=\"srvyQstnAnsRow_WWW123WWW_SrvyQstnAnsID\" value=\"-1\" readonly=\"true\">
                                                    <label class=\"btn btn-primary btn-file input-group-addon\" onclick=\"getLovsPage('myLovModal', 'myLovModalTitle', 'myLovModalBody', 'Question Possible Answers', 'srvysQstnID', '', '', 'radio', true, '', 'srvyQstnAnsRow_WWW123WWW_PssblAnsID', 'srvyQstnAnsRow_WWW123WWW_PsblAnsNm', 'clear', 0, '');\">
                                                        <span class=\"glyphicon glyphicon-th-list\"></span>
                                                    </label>
                                                </div>
                                              </div>
                                          </td>
                                          <td class=\"lovtd\">
                                              <div class=\"form-group form-group-sm\" style=\"width:100% !important;margin-bottom:0px !important;\">
                                                <input style=\"max-width:40px;width:40px;\" type=\"text\" class=\"form-control\" aria-label=\"...\" id=\"srvyQstnAnsRow_WWW123WWW_PssblAnsID\" value=\"-1\" readonly=\"true\">
                                              </div>
                                          </td>
                                          <td class=\"lovtd\">                                          
                                              <div class=\"form-group form-group-sm\" style=\"width:100% !important;margin-bottom:0px !important;\">
                                                    <input type=\"number\" min=\"0\" max=\"9999\" class=\"form-control\" aria-label=\"...\" id=\"srvyQstnAnsRow_WWW123WWW_OrderNo\" value=\"\" style=\"width:100% !important;\">
                                              </div>
                                          </td>
                                          <td class=\"lovtd\"> 
                                              <div class=\"form-group form-group-sm\" style=\"width:100% !important;margin-bottom:0px !important;\">
                                                <div class=\"form-check\" style=\"font-size: 12px !important;\">
                                                    <label class=\"form-check-label\">
                                                        <input type=\"checkbox\" class=\"form-check-input\" id=\"srvyQstnAnsRow_WWW123WWW_IsEnabled\" name=\"srvyQstnAnsRow_WWW123WWW_IsCrct\">
                                                    </label>
                                                </div>
                                              </div>
                                           </td>
                                           <td class=\"lovtd\">
                                              <div class=\"form-group form-group-sm\" style=\"width:100% !important;margin-bottom:0px !important;\">
                                                            <input type=\"text\" class=\"form-control\" aria-label=\"...\" id=\"srvyQstnAnsRow_WWW123WWW_DsplyCndtn\" value=\"\" style=\"width:100% !important;\">
                                              </div>
                                          </td>                                                                        
                                        <td class=\"lovtd\">
                                            <button type=\"button\" class=\"btn btn-default\" style=\"margin: 0px !important;padding:0px 3px 2px 4px !important;\" onclick=\"delSrvyQstnAns('srvyQstnAnsRow__WWW123WWW');\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Delete Possible Answer\">
                                                <img src=\"cmn_images/no.png\" style=\"height:15px; width:auto; position: relative; vertical-align: middle;\">
                                            </button>
                                        </td>
                                        </tr>");
                                        ?> 
                                        <div class="<?php echo $colClassType3; ?>" style="padding:0px 1px 0px 1px !important;"> 
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="insertNewRowBe4('srvyQstnAnsTable', 0, '<?php echo $nwRowHtml; ?>');" style="width:100% !important;">
                                                    <img src="cmn_images/add1-64.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                                    New Option
                                                </button>
                                                <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="saveSrvyQstnAnsForm('clear', '#srvyQstnAnsList', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>&sbmtdQstnID=<?php echo $sbmtdQstnID; ?>');" style="width:100% !important;">
                                                    <img src="cmn_images/FloppyDisk.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                                    Save
                                                </button>
                                            </div>
                                        </div>
                                        <?php
                                    } else {
                                        $colClassType1 = "col-lg-4";
                                        $colClassType2 = "col-lg-4";
                                        $colClassType3 = "col-lg-4";
                                    }
                                    ?>
                                    <div class="<?php echo $colClassType2; ?>" style="padding:0px 15px 0px 15px !important;">
                                        <div class="input-group">
                                            <input class="form-control" id="srvyQstnAnsSrchFor" type = "text" placeholder="Search For" value="<?php echo trim(str_replace("%", " ", $srchFor)); ?>" onkeyup="enterKeyFuncSrvyQstnAns(event, '', '#srvyQstnAnsList', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>&sbmtdQstnID=<?php echo $sbmtdQstnID; ?>');">
                                            <input id="srvyQstnAnsPageNo" type = "hidden" value="<?php echo $pageNo; ?>">
                                            <label class="btn btn-primary btn-file input-group-addon" onclick="getSrvyQstnAns('clear', '#srvyQstnAnsList', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>&sbmtdQstnID=<?php echo $sbmtdQstnID; ?>');">
                                                <span class="glyphicon glyphicon-remove"></span>
                                            </label>
                                            <label class="btn btn-primary btn-file input-group-addon" onclick="getSrvyQstnAns('', '#srvyQstnAnsList', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>&sbmtdQstnID=<?php echo $sbmtdQstnID; ?>');">
                                                <span class="glyphicon glyphicon-search"></span>
                                            </label> 
                                        </div>
                                    </div>
                                    <div class="<?php echo $colClassType2; ?>">
                                        <div class="input-group">
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-filter"></span></span>
                                            <!--<select data-placeholder="Select..." class="form-control chosen-select" id="srvyQstnAnsSrchIn">
                                            <?php
                                            $valslctdArry = array("");
                                            $srchInsArrys = array("Answer");

                                            for ($z = 0; $z < count($srchInsArrys); $z++) {
                                                if ($srchIn == $srchInsArrys[$z]) {
                                                    $valslctdArry[$z] = "selected";
                                                }
                                                ?>
                                                                                                                                                                                                                                    <option value="<?php echo $srchInsArrys[$z]; ?>" <?php echo $valslctdArry[$z]; ?>><?php echo $srchInsArrys[$z]; ?></option>
                                            <?php } ?>
                                            </select>-->
                                            <span class="input-group-addon" style="max-width: 1px !important;padding:0px !important;width:1px !important;border:none !important;"></span>
                                            <select data-placeholder="Select..." class="form-control chosen-select" id="srvyQstnAnsDsplySze" style="min-width:70px !important;">                            
                                                <?php
                                                $valslctdArry = array("", "", "", "", "", "", "", "");
                                                $dsplySzeArry = array(1, 5, 10, 15, 30, 50, 100, 500, 1000);
                                                for ($y = 0; $y < count($dsplySzeArry); $y++) {
                                                    if ($lmtSze == $dsplySzeArry[$y]) {
                                                        $valslctdArry[$y] = "selected";
                                                    } else {
                                                        $valslctdArry[$y] = "";
                                                    }
                                                    ?>
                                                    <option value="<?php echo $dsplySzeArry[$y]; ?>" <?php echo $valslctdArry[$y]; ?>><?php echo $dsplySzeArry[$y]; ?></option>                            
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="<?php echo $colClassType1; ?>">
                                        <nav aria-label="Page navigation">
                                            <ul class="pagination" style="margin: 0px !important;">
                                                <li>
                                                    <a class="rhopagination" href="javascript:getSrvyQstnAns('previous', '#srvyQstnAnsList', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>&sbmtdQstnID=<?php echo $sbmtdQstnID; ?>');" aria-label="Previous">
                                                        <span aria-hidden="true">&laquo;</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="rhopagination" href="javascript:getSrvyQstnAns('next', '#srvyQstnAnsList', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>&sbmtdQstnID=<?php echo $sbmtdQstnID; ?>');" aria-label="Next">
                                                        <span aria-hidden="true">&raquo;</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                                <div class="row"> 
                                    <div class="col-md-12">
                                        <table class="table table-striped table-bordered table-responsive" id="srvyQstnAnsTable" cellspacing="0" width="100%" style="width:100%;min-width: 600px !important;">
                                            <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Possible Answer</th>
                                                    <!--<th>Answer Hint Text</th>-->
                                                    <th>Ans. ID</th>
                                                    <th>Order No</th>
                                                    <th>Correct Ans?</th>
                                                    <th style="min-width:250px;">Display Criteria</th>
                                                    <th>&nbsp;</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $cntr = 0;
                                                while ($row2 = loc_db_fetch_array($result2)) {
                                                    $cntr += 1;
                                                    ?>
                                                    <tr id="srvyQstnAnsRow_<?php echo $cntr; ?>">                                    
                                                        <td class="lovtd"><span><?php echo ($curIdx * $lmtSze) + ($cntr); ?></span></td>
                                                        <td class="lovtd">                                                                   
                                                            <span><?php echo $row2[4]; ?></span>
                                                            <input type="hidden" class="form-control" aria-label="..." id="srvyQstnAnsRow<?php echo $cntr; ?>_SrvyQstnAnsID" value="<?php echo $row2[0]; ?>" style="width:100% !important;">                                              
                                                        </td>                                                
                                                        <!--<td class="lovtd">
                                                            <span><?php echo $row2[8]; ?></span>                                                       
                                                        </td>-->                                               
                                                        <td class="lovtd">
                                                            <span><?php echo $row2[3]; ?></span>  
                                                            <input type="hidden" class="form-control" aria-label="..." id="srvyQstnAnsRow<?php echo $cntr; ?>_PssblAnsID" value="<?php echo $row2[3]; ?>" style="width:100% !important;">
                                                        </td>  
                                                        <td class="lovtd">
                                                            <?php if ($canEdtSrvy === true) { ?>
                                                                <div class="form-group form-group-sm" style="width:100% !important;">
                                                                    <input type="number" min="1" max="9999" class="form-control" aria-label="..." id="srvyQstnAnsRow<?php echo $cntr; ?>_OrderNo" name="srvyQstnAnsRow<?php echo $cntr; ?>_OrderNo" value="<?php echo $row2[5]; ?>" style="width:100%;">
                                                                </div>
                                                            <?php } else { ?>
                                                                <span class="normaltd"><?php echo $row2[5]; ?></span>
                                                            <?php } ?>                                                         
                                                        </td>
                                                        <td class="lovtd">
                                                            <?php
                                                            $isChkd = "";
                                                            $isRdOnly = "disabled=\"true\"";
                                                            if ($canEdtSrvy === TRUE) {
                                                                $isRdOnly = "";
                                                            }
                                                            if ($row2[6] == "1") {
                                                                $isChkd = "checked=\"true\"";
                                                            }
                                                            ?>   
                                                            <div class="form-group form-group-sm" style="width:100% !important;margin-bottom:0px !important;">
                                                                <div class="form-check" style="font-size: 12px !important;">
                                                                    <label class="form-check-label">
                                                                        <input type="checkbox" class="form-check-input" id="srvyQstnAnsRow<?php echo $cntr; ?>_IsCrct" name="srvyQstnAnsRow<?php echo $cntr; ?>_IsCrct" <?php echo $isChkd . " " . $isRdOnly; ?> >
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </td>                                    
                                                        <td class="lovtd">                                                        
                                                            <?php if ($canEdtSrvy === true) { ?>
                                                                <div class="form-group form-group-sm" style="width:100% !important;">
                                                                    <input type="text" class="form-control" aria-label="..." id="srvyQstnAnsRow<?php echo $cntr; ?>_DsplyCndtn" name="srvyQstnAnsRow<?php echo $cntr; ?>_DsplyCndtn" value="<?php echo $row2[10]; ?>" style="width:100%;">
                                                                </div>
                                                            <?php } else { ?>
                                                                <span class="normaltd"><?php echo $row2[10]; ?></span>
                                                            <?php } ?>                                                       
                                                        </td>
                                                        <td class="lovtd">
                                                            <button type="button" class="btn btn-default" style="margin: 0px !important;padding:0px 3px 2px 4px !important;" onclick="delSrvyQstnAns('srvyQstnAnsRow_<?php echo $cntr; ?>');" data-toggle="tooltip" data-placement="bottom" title="Delete Question from Survey">
                                                                <img src="cmn_images/no.png" style="height:15px; width:auto; position: relative; vertical-align: middle;">
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>                     
                                </div> 
                            </div>
                        </form>
                        <?php
                    }
                } else {
                    ?>
                    <span>No Results Found</span>
                    <?php
                }
            } else if ($vwtyp == 4) {
                $sbmtdSrvyID = isset($_POST['sbmtdSrvyID']) ? $_POST['sbmtdSrvyID'] : -1;
                $sbmtdQstnID = isset($_POST['sbmtdQstnID']) ? $_POST['sbmtdQstnID'] : -1;

                $total = get_SrvyQstnAnsTtl($srchFor, $sbmtdQstnID, $sbmtdSrvyID);
                if ($pageNo > ceil($total / $lmtSze)) {
                    $pageNo = 1;
                } else if ($pageNo < 1) {
                    $pageNo = ceil($total / $lmtSze);
                }

                $curIdx = $pageNo - 1;
                $result2 = get_SrvyQstnAns($srchFor, $curIdx, $lmtSze, $sbmtdQstnID, $sbmtdSrvyID);
                $colClassType1 = "col-lg-2";
                $colClassType2 = "col-lg-3";
                $colClassType3 = "col-lg-4";
                $vwtyp = 4;
                ?>
                <div class="row">
                    <?php
                    if ($canEdtSrvy === true) {
                        $nwRowHtml = urlencode("<tr id=\"srvyQstnAnsRow__WWW123WWW\">"
                                . "<td class=\"lovtd\"><span class=\"normaltd\">New</span></td>"
                                . "<td class=\"lovtd\">
                                              <div class=\"form-group form-group-sm\" style=\"width:100% !important;\">
                                                <div class=\"input-group\"  style=\"width:100%;\">
                                                    <input type=\"text\" class=\"form-control\" aria-label=\"...\" id=\"srvyQstnAnsRow_WWW123WWW_PsblAnsNm\" value=\"\" readonly=\"true\">
                                                    <input style=\"max-width:40px;width:40px;\" type=\"hidden\" class=\"form-control\" aria-label=\"...\" id=\"srvyQstnAnsRow_WWW123WWW_SrvyQstnAnsID\" value=\"-1\" readonly=\"true\">
                                                    <label class=\"btn btn-primary btn-file input-group-addon\" onclick=\"getLovsPage('myLovModal', 'myLovModalTitle', 'myLovModalBody', 'Question Possible Answers', 'srvysQstnID', '', '', 'radio', true, '', 'srvyQstnAnsRow_WWW123WWW_PssblAnsID', 'srvyQstnAnsRow_WWW123WWW_PsblAnsNm', 'clear', 0, '');\">
                                                        <span class=\"glyphicon glyphicon-th-list\"></span>
                                                    </label>
                                                </div>
                                              </div>
                                          </td>
                                          <td class=\"lovtd\">
                                              <div class=\"form-group form-group-sm\" style=\"width:100% !important;margin-bottom:0px !important;\">
                                                <input style=\"max-width:40px;width:40px;\" type=\"text\" class=\"form-control\" aria-label=\"...\" id=\"srvyQstnAnsRow_WWW123WWW_PssblAnsID\" value=\"-1\" readonly=\"true\">
                                              </div>
                                          </td>
                                          <td class=\"lovtd\">                                          
                                              <div class=\"form-group form-group-sm\" style=\"width:100% !important;margin-bottom:0px !important;\">
                                                    <input type=\"number\" min=\"0\" max=\"9999\" class=\"form-control\" aria-label=\"...\" id=\"srvyQstnAnsRow_WWW123WWW_OrderNo\" value=\"\" style=\"width:100% !important;\">
                                              </div>
                                          </td>
                                          <td class=\"lovtd\"> 
                                              <div class=\"form-group form-group-sm\" style=\"width:100% !important;margin-bottom:0px !important;\">
                                                <div class=\"form-check\" style=\"font-size: 12px !important;\">
                                                    <label class=\"form-check-label\">
                                                        <input type=\"checkbox\" class=\"form-check-input\" id=\"srvyQstnAnsRow_WWW123WWW_IsEnabled\" name=\"srvyQstnAnsRow_WWW123WWW_IsCrct\">
                                                    </label>
                                                </div>
                                              </div>
                                           </td>
                                           <td class=\"lovtd\">
                                              <div class=\"form-group form-group-sm\" style=\"width:100% !important;margin-bottom:0px !important;\">
                                                            <input type=\"text\" class=\"form-control\" aria-label=\"...\" id=\"srvyQstnAnsRow_WWW123WWW_DsplyCndtn\" value=\"\" style=\"width:100% !important;\">
                                              </div>
                                          </td>                                                                        
                                        <td class=\"lovtd\">
                                            <button type=\"button\" class=\"btn btn-default\" style=\"margin: 0px !important;padding:0px 3px 2px 4px !important;\" onclick=\"delSrvyQstnAns('srvyQstnAnsRow__WWW123WWW');\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Delete Possible Answer\">
                                                <img src=\"cmn_images/no.png\" style=\"height:15px; width:auto; position: relative; vertical-align: middle;\">
                                            </button>
                                        </td>
                                        </tr>");
                        ?> 
                        <div class="<?php echo $colClassType3; ?>" style="padding:0px 1px 0px 1px !important;"> 
                            <div class="col-md-12">
                                <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="insertNewRowBe4('srvyQstnAnsTable', 0, '<?php echo $nwRowHtml; ?>');" style="width:100% !important;">
                                    <img src="cmn_images/add1-64.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                    New Option
                                </button>
                                <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="saveSrvyQstnAnsForm('clear', '#srvyQstnAnsList', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>&sbmtdQstnID=<?php echo $sbmtdQstnID; ?>');" style="width:100% !important;">
                                    <img src="cmn_images/FloppyDisk.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                    Save
                                </button>
                            </div>
                        </div>
                        <?php
                    } else {
                        $colClassType1 = "col-lg-4";
                        $colClassType2 = "col-lg-4";
                        $colClassType3 = "col-lg-4";
                    }
                    ?>
                    <div class="<?php echo $colClassType2; ?>" style="padding:0px 15px 0px 15px !important;">
                        <div class="input-group">
                            <input class="form-control" id="srvyQstnAnsSrchFor" type = "text" placeholder="Search For" value="<?php echo trim(str_replace("%", " ", $srchFor)); ?>" onkeyup="enterKeyFuncSrvyQstnAns(event, '', '#srvyQstnAnsList', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>&sbmtdQstnID=<?php echo $sbmtdQstnID; ?>');">
                            <input id="srvyQstnAnsPageNo" type = "hidden" value="<?php echo $pageNo; ?>">
                            <label class="btn btn-primary btn-file input-group-addon" onclick="getSrvyQstnAns('clear', '#srvyQstnAnsList', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>&sbmtdQstnID=<?php echo $sbmtdQstnID; ?>');">
                                <span class="glyphicon glyphicon-remove"></span>
                            </label>
                            <label class="btn btn-primary btn-file input-group-addon" onclick="getSrvyQstnAns('', '#srvyQstnAnsList', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>&sbmtdQstnID=<?php echo $sbmtdQstnID; ?>');">
                                <span class="glyphicon glyphicon-search"></span>
                            </label> 
                        </div>
                    </div>
                    <div class="<?php echo $colClassType2; ?>">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-filter"></span></span>
                            <!--<select data-placeholder="Select..." class="form-control chosen-select" id="srvyQstnAnsSrchIn">
                            <?php
                            $valslctdArry = array("");
                            $srchInsArrys = array("Answer");

                            for ($z = 0; $z < count($srchInsArrys); $z++) {
                                if ($srchIn == $srchInsArrys[$z]) {
                                    $valslctdArry[$z] = "selected";
                                }
                                ?>
                                                                                                                                                                                                                            <option value="<?php echo $srchInsArrys[$z]; ?>" <?php echo $valslctdArry[$z]; ?>><?php echo $srchInsArrys[$z]; ?></option>
                            <?php } ?>
                            </select>-->
                            <span class="input-group-addon" style="max-width: 1px !important;padding:0px !important;width:1px !important;border:none !important;"></span>
                            <select data-placeholder="Select..." class="form-control chosen-select" id="srvyQstnAnsDsplySze" style="min-width:70px !important;">                            
                                <?php
                                $valslctdArry = array("", "", "", "", "", "", "", "");
                                $dsplySzeArry = array(1, 5, 10, 15, 30, 50, 100, 500, 1000);
                                for ($y = 0; $y < count($dsplySzeArry); $y++) {
                                    if ($lmtSze == $dsplySzeArry[$y]) {
                                        $valslctdArry[$y] = "selected";
                                    } else {
                                        $valslctdArry[$y] = "";
                                    }
                                    ?>
                                    <option value="<?php echo $dsplySzeArry[$y]; ?>" <?php echo $valslctdArry[$y]; ?>><?php echo $dsplySzeArry[$y]; ?></option>                            
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="<?php echo $colClassType1; ?>">
                        <nav aria-label="Page navigation">
                            <ul class="pagination" style="margin: 0px !important;">
                                <li>
                                    <a class="rhopagination" href="javascript:getSrvyQstnAns('previous', '#srvyQstnAnsList', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>&sbmtdQstnID=<?php echo $sbmtdQstnID; ?>');" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="rhopagination" href="javascript:getSrvyQstnAns('next', '#srvyQstnAnsList', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>&sbmtdQstnID=<?php echo $sbmtdQstnID; ?>');" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="row"> 
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered table-responsive" id="srvyQstnAnsTable" cellspacing="0" width="100%" style="width:100%;min-width: 600px !important;">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Possible Answer</th>
                                    <!--<th>Answer Hint Text</th>-->
                                    <th>Ans. ID</th>
                                    <th>Order No</th>
                                    <th>Correct Ans?</th>
                                    <th style="min-width:250px;">Display Criteria</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $cntr = 0;
                                while ($row2 = loc_db_fetch_array($result2)) {
                                    $cntr += 1;
                                    ?>
                                    <tr id="srvyQstnAnsRow_<?php echo $cntr; ?>">                                    
                                        <td class="lovtd"><span><?php echo ($curIdx * $lmtSze) + ($cntr); ?></span></td>
                                        <td class="lovtd">                                                                   
                                            <span><?php echo $row2[4]; ?></span>
                                            <input type="hidden" class="form-control" aria-label="..." id="srvyQstnAnsRow<?php echo $cntr; ?>_SrvyQstnAnsID" value="<?php echo $row2[0]; ?>" style="width:100% !important;">                                              
                                        </td>                                                
                                        <!--<td class="lovtd">
                                            <span><?php echo $row2[8]; ?></span>                                                       
                                        </td>-->                                               
                                        <td class="lovtd">
                                            <span><?php echo $row2[3]; ?></span>     
                                            <input type="hidden" class="form-control" aria-label="..." id="srvyQstnAnsRow<?php echo $cntr; ?>_PssblAnsID" value="<?php echo $row2[3]; ?>" style="width:100% !important;">                                                    
                                        </td>  
                                        <td class="lovtd">
                                            <?php if ($canEdtSrvy === true) { ?>
                                                <div class="form-group form-group-sm" style="width:100% !important;">
                                                    <input type="number" min="1" max="9999" class="form-control" aria-label="..." id="srvyQstnAnsRow<?php echo $cntr; ?>_OrderNo" name="srvyQstnAnsRow<?php echo $cntr; ?>_OrderNo" value="<?php echo $row2[5]; ?>" style="width:100%;">
                                                </div>
                                            <?php } else { ?>
                                                <span class="normaltd"><?php echo $row2[5]; ?></span>
                                            <?php } ?>                                                         
                                        </td>
                                        <td class="lovtd">
                                            <?php
                                            $isChkd = "";
                                            $isRdOnly = "disabled=\"true\"";
                                            if ($canEdtSrvy === TRUE) {
                                                $isRdOnly = "";
                                            }
                                            if ($row2[6] == "1") {
                                                $isChkd = "checked=\"true\"";
                                            }
                                            ?>   
                                            <div class="form-group form-group-sm" style="width:100% !important;margin-bottom:0px !important;">
                                                <div class="form-check" style="font-size: 12px !important;">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" class="form-check-input" id="srvyQstnAnsRow<?php echo $cntr; ?>_IsCrct" name="srvyQstnAnsRow<?php echo $cntr; ?>_IsCrct" <?php echo $isChkd . " " . $isRdOnly; ?> >
                                                    </label>
                                                </div>
                                            </div>
                                        </td>                                    
                                        <td class="lovtd">                                                        
                                            <?php if ($canEdtSrvy === true) { ?>
                                                <div class="form-group form-group-sm" style="width:100% !important;">
                                                    <input type="text" class="form-control" aria-label="..." id="srvyQstnAnsRow<?php echo $cntr; ?>_DsplyCndtn" name="srvyQstnAnsRow<?php echo $cntr; ?>_DsplyCndtn" value="<?php echo $row2[10]; ?>" style="width:100%;">
                                                </div>
                                            <?php } else { ?>
                                                <span class="normaltd"><?php echo $row2[10]; ?></span>
                                            <?php } ?>                                                       
                                        </td>
                                        <td class="lovtd">
                                            <button type="button" class="btn btn-default" style="margin: 0px !important;padding:0px 3px 2px 4px !important;" onclick="delSrvyQstnAns('srvyQstnAnsRow_<?php echo $cntr; ?>');" data-toggle="tooltip" data-placement="bottom" title="Delete Question from Survey">
                                                <img src="cmn_images/no.png" style="height:15px; width:auto; position: relative; vertical-align: middle;">
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>                     
                </div> 
                <?php
            } else if ($vwtyp == 5) {
                //New Survey Question
                $sbmtdSrvyID = isset($_POST['sbmtdSrvyID']) ? $_POST['sbmtdSrvyID'] : -1;
                $sbmtdQstnID = -1;
                if ($sbmtdSrvyID > 0) {
                    ?>
                    <form id='srvyQstnAnsForm' action='' method='post' accept-charset='UTF-8'>
                        <div class="row">
                            <fieldset class="basic_person_fs" style="padding:10px 3px 0px 3px !important;">
                                <div  class="col-md-6" style="padding:0px 3px 0px 3px !important;"> 
                                    <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                        <label for="srvysQstn" class="control-label col-lg-3">Question:</label>
                                        <div  class="col-lg-9 formtd"> 
                                            <div class="input-group">
                                                <input type="text" class="form-control" aria-label="..." id="srvysQstn" name="srvysQstn" value="" readonly="true">                                                                                            
                                                <input type="hidden" class="form-control" aria-label="..." id="srvysSrvyQstnID" name="srvysSrvyQstnID" value="-1">
                                                <input type="hidden" class="form-control" aria-label="..." id="srvysQstnID" name="srvysQstnID" value="-1">
                                                <input type="hidden" class="form-control" aria-label="..." id="isNew123" name="isNew123" value="5">
                                                <input type="hidden" class="form-control" aria-label="..." id="srvyID" name="srvyID" value="<?php echo $sbmtdSrvyID; ?>">
                                                <label class="btn btn-primary btn-file input-group-addon" onclick="getLovsPage('myLovModal', 'myLovModalTitle', 'myLovModalBody', 'Questions Bank', '', '', '', 'radio', true, '', 'srvysQstnID', 'srvysQstn', 'clear', 0, '');">
                                                    <span class="glyphicon glyphicon-th-list"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                        <label for="srvysQstnOrder" class="control-label col-lg-3">Order No.:</label>
                                        <div  class="col-lg-3 formtd">
                                            <input type="number" min="1" max="9999" class="form-control" aria-label="..." id="srvysQstnOrder" name="srvysQstnOrder" value="" style="width:100%;">
                                        </div>
                                    </div>
                                    <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                        <label for="srvysQstnDsplyCndtn" class="control-label col-lg-3">Question Display Condition:</label>
                                        <div  class="col-lg-9 formtd">
                                            <textarea class="form-control" aria-label="..." id="srvysQstnDsplyCndtn" name="srvysQstnDsplyCndtn" style="width:100%;" cols="3" rows="5"></textarea>
                                        </div>
                                    </div>

                                </div>
                                <div  class="col-md-6" style="padding:0px 3px 0px 3px !important;">
                                    <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                        <label for="srvysInvldChoicesQry" class="control-label col-lg-3">Choices Validity Query:</label>
                                        <div  class="col-lg-9 formtd">
                                            <textarea class="form-control" aria-label="..." id="srvysInvldChoicesQry" name="srvysInvldChoicesQry" style="width:100%;" cols="3" rows="4"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                        <label for="srvysQstnInvldDsplyMsg" class="control-label col-lg-3">Invalid Choices Message:</label>
                                        <div class="col-lg-9 formtd">
                                            <textarea class="form-control" aria-label="..." id="srvysQstnInvldDsplyMsg" name="srvysQstnInvldDsplyMsg" style="width:100%;" cols="3" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <?php
                        $colClassType1 = "col-lg-2";
                        $colClassType2 = "col-lg-3";
                        $colClassType3 = "col-lg-4";
                        $vwtyp = 5;
                        ?>
                        <div class="row" id="srvyQstnAnsList" style="padding:0px 1px 0px 1px !important">
                            <div class="row">
                                <?php
                                $nwRowHtml = urlencode("<tr id=\"srvyQstnAnsRow__WWW123WWW\">"
                                        . "<td class=\"lovtd\"><span class=\"normaltd\">New</span></td>"
                                        . "<td class=\"lovtd\">
                                              <div class=\"form-group form-group-sm\" style=\"width:100% !important;\">
                                                <div class=\"input-group\"  style=\"width:100%;\">
                                                    <input type=\"text\" class=\"form-control\" aria-label=\"...\" id=\"srvyQstnAnsRow_WWW123WWW_PsblAnsNm\" value=\"\" readonly=\"true\">
                                                    <input style=\"max-width:40px;width:40px;\" type=\"hidden\" class=\"form-control\" aria-label=\"...\" id=\"srvyQstnAnsRow_WWW123WWW_SrvyQstnAnsID\" value=\"-1\" readonly=\"true\">
                                                    <label class=\"btn btn-primary btn-file input-group-addon\" onclick=\"getLovsPage('myLovModal', 'myLovModalTitle', 'myLovModalBody', 'Question Possible Answers', 'srvysQstnID', '', '', 'radio', true, '', 'srvyQstnAnsRow_WWW123WWW_PssblAnsID', 'srvyQstnAnsRow_WWW123WWW_PsblAnsNm', 'clear', 0, '');\">
                                                        <span class=\"glyphicon glyphicon-th-list\"></span>
                                                    </label>
                                                </div>
                                              </div>
                                          </td>
                                          <td class=\"lovtd\">
                                              <div class=\"form-group form-group-sm\" style=\"width:100% !important;margin-bottom:0px !important;\">
                                                <input style=\"max-width:40px;width:40px;\" type=\"text\" class=\"form-control\" aria-label=\"...\" id=\"srvyQstnAnsRow_WWW123WWW_PssblAnsID\" value=\"-1\" readonly=\"true\">
                                              </div>
                                          </td>
                                          <td class=\"lovtd\">                                          
                                              <div class=\"form-group form-group-sm\" style=\"width:100% !important;margin-bottom:0px !important;\">
                                                    <input type=\"number\" min=\"0\" max=\"9999\" class=\"form-control\" aria-label=\"...\" id=\"srvyQstnAnsRow_WWW123WWW_OrderNo\" value=\"\" style=\"width:100% !important;\">
                                              </div>
                                          </td>
                                          <td class=\"lovtd\"> 
                                              <div class=\"form-group form-group-sm\" style=\"width:100% !important;margin-bottom:0px !important;\">
                                                <div class=\"form-check\" style=\"font-size: 12px !important;\">
                                                    <label class=\"form-check-label\">
                                                        <input type=\"checkbox\" class=\"form-check-input\" id=\"srvyQstnAnsRow_WWW123WWW_IsEnabled\" name=\"srvyQstnAnsRow_WWW123WWW_IsCrct\">
                                                    </label>
                                                </div>
                                              </div>
                                           </td>
                                           <td class=\"lovtd\">
                                              <div class=\"form-group form-group-sm\" style=\"width:100% !important;margin-bottom:0px !important;\">
                                                            <input type=\"text\" class=\"form-control\" aria-label=\"...\" id=\"srvyQstnAnsRow_WWW123WWW_DsplyCndtn\" value=\"\" style=\"width:100% !important;\">
                                              </div>
                                          </td>                                                                        
                                        <td class=\"lovtd\">
                                            <button type=\"button\" class=\"btn btn-default\" style=\"margin: 0px !important;padding:0px 3px 2px 4px !important;\" onclick=\"delSrvyQstnAns('srvyQstnAnsRow__WWW123WWW');\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Delete Possible Answer\">
                                                <img src=\"cmn_images/no.png\" style=\"height:15px; width:auto; position: relative; vertical-align: middle;\">
                                            </button>
                                        </td>
                                        </tr>");
                                ?> 
                                <div class="<?php echo $colClassType3; ?>" style="padding:0px 1px 0px 1px !important;"> 
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="insertNewRowBe4('srvyQstnAnsTable', 0, '<?php echo $nwRowHtml; ?>');" style="width:100% !important;">
                                            <img src="cmn_images/add1-64.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                            New Option
                                        </button>
                                        <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="saveSrvyQstnAnsForm('clear', '#srvyQstnAnsList', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>&sbmtdQstnID=<?php echo $sbmtdQstnID; ?>');" style="width:100% !important;">
                                            <img src="cmn_images/FloppyDisk.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                            Save
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row"> 
                                <div class="col-md-12">
                                    <table class="table table-striped table-bordered table-responsive" id="srvyQstnAnsTable" cellspacing="0" width="100%" style="width:100%;min-width: 600px !important;">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Possible Answer</th>
                                                <!--<th>Answer Hint Text</th>-->
                                                <th>Ans. ID</th>
                                                <th>Order No</th>
                                                <th>Correct Ans?</th>
                                                <th style="min-width:250px;">Display Criteria</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $cntr = 1;
                                            ?>
                                            <tr id="srvyQstnAnsRow_<?php echo $cntr; ?>">                                    
                                                <td class="lovtd"><span><?php echo ($curIdx * $lmtSze) + ($cntr); ?></span></td>
                                                <td class="lovtd">                                                                   
                                                    <div class="form-group form-group-sm" style="width:100% !important;">
                                                        <div class="input-group"  style="width:100%;">
                                                            <input type="text" class="form-control" aria-label="..." id="srvyQstnAnsRow<?php echo $cntr; ?>_PsblAnsNm" value="" readonly="true">
                                                            <input style="max-width:40px;width:40px;" type="hidden" class="form-control" aria-label="..." id="srvyQstnAnsRow<?php echo $cntr; ?>_SrvyQstnAnsID" value="-1" readonly="true">
                                                            <label class="btn btn-primary btn-file input-group-addon" onclick="getLovsPage('myLovModal', 'myLovModalTitle', 'myLovModalBody', 'Question Possible Answers', 'srvysQstnID', '', '', 'radio', true, '', 'srvyQstnAnsRow<?php echo $cntr; ?>_PssblAnsID', 'srvyQstnAnsRow<?php echo $cntr; ?>_PsblAnsNm', 'clear', 0, '');">
                                                                <span class="glyphicon glyphicon-th-list"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </td>                                               
                                                <td class="lovtd">                                                    
                                                    <div class="form-group form-group-sm" style="width:100% !important;margin-bottom:0px !important;">  
                                                        <input style="max-width:40px;width:40px;" type="text" class="form-control" aria-label="..." id="srvyQstnAnsRow<?php echo $cntr; ?>_PssblAnsID" value="-1" style="width:100% !important;" readonly="true">
                                                    </div>                                                    
                                                </td>  
                                                <td class="lovtd">
                                                    <div class="form-group form-group-sm" style="width:100% !important;">
                                                        <input type="number" min="1" max="9999" class="form-control" aria-label="..." id="srvyQstnAnsRow<?php echo $cntr; ?>_OrderNo" name="srvyQstnAnsRow<?php echo $cntr; ?>_OrderNo" value="" style="width:100%;">
                                                    </div>                                                       
                                                </td>
                                                <td class="lovtd">
                                                    <?php
                                                    $isChkd = "";
                                                    $isRdOnly = "";
                                                    ?>   
                                                    <div class="form-group form-group-sm" style="width:100% !important;margin-bottom:0px !important;">
                                                        <div class="form-check" style="font-size: 12px !important;">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" class="form-check-input" id="srvyQstnAnsRow<?php echo $cntr; ?>_IsCrct" name="srvyQstnAnsRow<?php echo $cntr; ?>_IsCrct" <?php echo $isChkd . " " . $isRdOnly; ?> >
                                                            </label>
                                                        </div>
                                                    </div>
                                                </td>                                    
                                                <td class="lovtd">  
                                                    <div class="form-group form-group-sm" style="width:100% !important;">
                                                        <input type="text" class="form-control" aria-label="..." id="srvyQstnAnsRow<?php echo $cntr; ?>_DsplyCndtn" name="srvyQstnAnsRow<?php echo $cntr; ?>_DsplyCndtn" value="" style="width:100%;">
                                                    </div>
                                                </td>
                                                <td class="lovtd">
                                                    <button type="button" class="btn btn-default" style="margin: 0px !important;padding:0px 3px 2px 4px !important;" onclick="delSrvyQstnAns('srvyQstnAnsRow_<?php echo $cntr; ?>');" data-toggle="tooltip" data-placement="bottom" title="Delete Question from Survey">
                                                        <img src="cmn_images/no.png" style="height:15px; width:auto; position: relative; vertical-align: middle;">
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>                     
                            </div> 
                        </div>
                    </form>
                    <?php
                } else {
                    ?>
                    <span>No Results Found</span>
                    <?php
                }
            } else if ($vwtyp == 6) {
                ?>
                <div class="rho-container-fluid">                  
                    <div class="col-md-12" style="border:none;">
                        <div id="srvysDetPage" class="tab-pane fadein active" style="border:none !important;"> 
                            <div class="row">
                                <div  class="col-md-6" style="padding:0px 3px 0px 3px !important;">
                                    <fieldset class="basic_person_fs" style="padding:10px 3px 0px 3px !important;"> 
                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                            <label for="srvysSurveyName" class="control-label col-lg-4">Survey Name:</label>
                                            <div  class="col-lg-8">
                                                <input type="text" class="form-control" aria-label="..." id="srvysSurveyName" name="srvysSurveyName" value="" style="width:100%;">
                                                <input type="hidden" class="form-control" aria-label="..." id="srvysSurveyID" name="srvysSurveyID" value="-1">
                                            </div>
                                        </div>
                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                            <label for="srvysSurveyDesc" class="control-label col-lg-4">Description:</label>
                                            <div  class="col-lg-8">
                                                <textarea class="form-control" aria-label="..." id="srvysSurveyDesc" name="srvysSurveyDesc" style="width:100%;" cols="3" rows="5"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                            <label for="srvysSurveyType" class="control-label col-lg-6">Survey Type:</label>
                                            <div class="col-lg-6">
                                                <select data-placeholder="Select..." class="form-control chosen-select" id="srvysSurveyType">
                                                    <?php
                                                    $valslctdArry = array("", "", "", "");
                                                    $srchInsArrys = array("ELECTION", "SURVEY", "QUESTIONNAIRE", "OTHER");
                                                    for ($z = 0; $z < count($srchInsArrys); $z++) {
                                                        ?>
                                                        <option value="<?php echo $srchInsArrys[$z]; ?>" <?php echo $valslctdArry[$z]; ?>><?php echo $srchInsArrys[$z]; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                            <label for="srvysStartDate" class="control-label col-md-4">Start Date:</label>
                                            <div  class="col-md-8">
                                                <div class="input-group date form_date_tme" data-date="" data-date-format="dd-M-yyyy hh:ii:ss" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd hh:ii:ss">
                                                    <input class="form-control" size="16" type="text" id="srvysStartDate" value="" readonly="">
                                                    <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                            <label for="srvysEndDate" class="control-label col-md-4">End Date:</label>
                                            <div  class="col-md-8">
                                                <div class="input-group date form_date_tme" data-date="" data-date-format="dd-M-yyyy hh:ii:ss" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd hh:ii:ss">
                                                    <input class="form-control" size="16" type="text" id="srvysEndDate" value="" readonly="">
                                                    <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px!important;">
                                            <label for="srvysIncPrsnSetNm" class="control-label col-md-4">Eligible Person Set:</label>
                                            <div  class="col-lg-8">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" aria-label="..." id="srvysIncPrsnSetNm" name="srvysIncPrsnSetNm" value="" readonly="true">
                                                    <input type="hidden" class="form-control" aria-label="..." id="srvysIncPrsnSetID" name="srvysIncPrsnSetID" value="-1">
                                                    <input type="hidden" class="form-control" aria-label="..." id="srvysOrgID" name="srvysOrgID" value="<?php echo $orgID; ?>">
                                                    <label class="btn btn-primary btn-file input-group-addon" onclick="getLovsPage('myLovModal', 'myLovModalTitle', 'myLovModalBody', 'Person Sets for Payments', 'srvysOrgID', '', '', 'radio', true, '', 'srvysIncPrsnSetID', 'srvysIncPrsnSetNm', 'clear', 0, '');">
                                                        <span class="glyphicon glyphicon-th-list"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px!important;">
                                            <label for="srvysExcPrsnSetNm" class="control-label col-md-4">Set for Persons to Exclude:</label>
                                            <div  class="col-lg-8">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" aria-label="..." id="srvysExcPrsnSetNm" name="srvysExcPrsnSetNm" value="" readonly="true">
                                                    <input type="hidden" class="form-control" aria-label="..." id="srvysExcPrsnSetID" name="srvysExcPrsnSetID" value="-1">
                                                    <label class="btn btn-primary btn-file input-group-addon" onclick="getLovsPage('myLovModal', 'myLovModalTitle', 'myLovModalBody', 'Person Sets for Payments', 'srvysOrgID', '', '', 'radio', true, '', 'srvysExcPrsnSetID', 'srvysExcPrsnSetNm', 'clear', 0, '');">
                                                        <span class="glyphicon glyphicon-th-list"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                                <div  class="col-md-6" style="padding:0px 3px 0px 3px !important;">
                                    <fieldset class = "basic_person_fs">
                                        <div class="row" style="padding:0px 15px 0px 15px !important;">
                                            <div id="srvyInstructions"></div> 
                                        </div>
                                    </fieldset>
                                    <input type="hidden" id="allSrvysInstructns" value="">
                                </div>
                            </div> 
                        </div>                       
                    </div>                
                </div>
                <?php
            } else if ($vwtyp == 7) {
                //View Person's Answers Chosen

                $sbmtdSrvyID = isset($_POST['sbmtdSrvyID']) ? $_POST['sbmtdSrvyID'] : -1;

                $total = get_SrvyAnsChosenTtl($srchFor, $srchIn, $sbmtdSrvyID);
                if ($pageNo > ceil($total / $lmtSze)) {
                    $pageNo = 1;
                } else if ($pageNo < 1) {
                    $pageNo = ceil($total / $lmtSze);
                }

                $curIdx = $pageNo - 1;
                $result2 = get_SrvyAnsChosen($srchFor, $srchIn, $curIdx, $lmtSze, $sbmtdSrvyID);
                $colClassType1 = "col-lg-4";
                $colClassType2 = "col-lg-4";
                $colClassType3 = "col-lg-4";
                ?>
                <form id='srvyAnsChsnForm' action='' method='post' accept-charset='UTF-8'>
                    <div class="row rhoRowMargin">
                        <div class="<?php echo $colClassType2; ?>" style="padding:0px 15px 0px 15px !important;">
                            <div class="input-group">
                                <input class="form-control" id="srvyAnsChsnSrchFor" type = "text" placeholder="Search For" value="<?php echo trim(str_replace("%", " ", $srchFor)); ?>" onkeyup="enterKeyFuncSrvyAnsChsn(event, '', <?php echo $sbmtdSrvyID; ?>, 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>');">
                                <input id="srvyAnsChsnPageNo" type = "hidden" value="<?php echo $pageNo; ?>">
                                <label class="btn btn-primary btn-file input-group-addon" onclick="getSrvyAnsChsn('clear', <?php echo $sbmtdSrvyID; ?>, 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>');">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </label>
                                <label class="btn btn-primary btn-file input-group-addon" onclick="getSrvyAnsChsn('', <?php echo $sbmtdSrvyID; ?>, 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>');">
                                    <span class="glyphicon glyphicon-search"></span>
                                </label> 
                            </div>
                        </div>
                        <div class="<?php echo $colClassType2; ?>">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-filter"></span></span>
                                <select data-placeholder="Select..." class="form-control chosen-select" id="srvyAnsChsnSrchIn">
                                    <?php
                                    $valslctdArry = array("", "", "");
                                    $srchInsArrys = array("Person Name", "Answer", "Question");

                                    for ($z = 0; $z < count($srchInsArrys); $z++) {
                                        if ($srchIn == $srchInsArrys[$z]) {
                                            $valslctdArry[$z] = "selected";
                                        }
                                        ?>
                                        <option value="<?php echo $srchInsArrys[$z]; ?>" <?php echo $valslctdArry[$z]; ?>><?php echo $srchInsArrys[$z]; ?></option>
                                    <?php } ?>
                                </select>
                                <span class="input-group-addon" style="max-width: 1px !important;padding:0px !important;width:1px !important;border:none !important;"></span>
                                <select data-placeholder="Select..." class="form-control chosen-select" id="srvyAnsChsnDsplySze" style="min-width:70px !important;">                            
                                    <?php
                                    $valslctdArry = array("", "", "", "", "", "", "", "");
                                    $dsplySzeArry = array(1, 5, 10, 15, 30, 50, 100, 500, 1000);
                                    for ($y = 0; $y < count($dsplySzeArry); $y++) {
                                        if ($lmtSze == $dsplySzeArry[$y]) {
                                            $valslctdArry[$y] = "selected";
                                        } else {
                                            $valslctdArry[$y] = "";
                                        }
                                        ?>
                                        <option value="<?php echo $dsplySzeArry[$y]; ?>" <?php echo $valslctdArry[$y]; ?>><?php echo $dsplySzeArry[$y]; ?></option>                            
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="<?php echo $colClassType1; ?>">
                            <nav aria-label="Page navigation">
                                <ul class="pagination" style="margin: 0px !important;">
                                    <li>
                                        <a class="rhopagination" href="javascript:getSrvyAnsChsn('previous', <?php echo $sbmtdSrvyID; ?>, 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>');" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="rhopagination" href="javascript:getSrvyAnsChsn('next', <?php echo $sbmtdSrvyID; ?>, 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>&sbmtdSrvyID=<?php echo $sbmtdSrvyID; ?>');" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <div class="row"> 
                        <div class="col-md-12">
                            <table class="table table-striped table-bordered table-responsive" id="srvyAnsChsnTable" cellspacing="0" width="100%" style="width:100%;min-width: 600px !important;">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Name of Person / Voter</th>
                                        <th>Question Answered</th>
                                        <!--<th>Answer Chosen</th>-->
                                        <th>Submitted?</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cntr = 0;
                                    while ($row2 = loc_db_fetch_array($result2)) {
                                        $cntr += 1;
                                        ?>
                                        <tr id="srvyAnsChsnRow_<?php echo $cntr; ?>">                                    
                                            <td class="lovtd"><span><?php echo ($curIdx * $lmtSze) + ($cntr); ?></span></td>
                                            <td class="lovtd">                                                                   
                                                <span><?php echo $row2[4]; ?></span>
                                                <input type="hidden" class="form-control" aria-label="..." id="srvyAnsChsnRow<?php echo $cntr; ?>_PrsnAnsID" value="<?php echo $row2[0]; ?>" style="width:100% !important;"> 
                                                <input type="hidden" class="form-control" aria-label="..." id="srvyAnsChsnRow<?php echo $cntr; ?>_SrvyID" value="<?php echo $sbmtdSrvyID; ?>" style="width:100% !important;">                                             
                                            </td>                                                
                                            <td class="lovtd">
                                                <span><?php echo $row2[2]; ?></span>                                                       
                                            </td> 
                                            <td class="lovtd">
                                                <?php
                                                $isChkd = "";
                                                $isRdOnly = "disabled=\"true\"";
                                                if ($canEdtSrvy === TRUE) {
                                                    $isRdOnly = "";
                                                }
                                                if ($row2[7] == "1") {
                                                    $isChkd = "checked=\"true\"";
                                                }
                                                ?>   
                                                <div class="form-group form-group-sm" style="width:100% !important;margin-bottom:0px !important;">
                                                    <div class="form-check" style="font-size: 12px !important;">
                                                        <label class="form-check-label">
                                                            <input type="checkbox" class="form-check-input" id="srvyAnsChsnRow<?php echo $cntr; ?>_IsSbmtd" name="srvyAnsChsnRow<?php echo $cntr; ?>_IsSbmtd" <?php echo $isChkd . " " . $isRdOnly; ?> >
                                                        </label>
                                                    </div>
                                                </div>
                                            </td> 
                                            <td class="lovtd">
                                                <?php if ($row2[7] == "1") { ?>
                                                    <button type="button" class="btn btn-primary" style="margin: 0px !important;padding:0px 3px 2px 4px !important;" onclick="reopenSurvey('srvyAnsChsnRow_<?php echo $cntr; ?>', 'Re-Open');" data-toggle="tooltip" data-placement="bottom" title="Re-Open Vote">
                                                        <img src="cmn_images/unlock_enabled.gif" style="height:15px; width:auto; position: relative; vertical-align: middle;"> Re-Open Vote
                                                    </button>
                                                <?php } else { ?>
                                                    <span>&nbsp;</span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>                     
                    </div> 
                </form>
                <?php
            } else if ($vwtyp == 8) {
                //View Survey Results
                $srvyID = isset($_POST['sbmtdSrvyID']) ? $_POST['sbmtdSrvyID'] : -1;
                $rslt1 = get_SrvyDet($srvyID);
                $cntent = "<div style=\"padding:5px;\">Sorry this Survey/Electronic Voting has been Closed or is Yet to be Opened!";
                $numRws = loc_db_num_rows($rslt1);
                if ($numRws > 0) {
                    $row = loc_db_fetch_array($rslt1);
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
                }
                //Get Query to Display Election Results Rather if Permitted else
                $rslt2 = get_SurveyResults($srvyID);
                $colsCnt = loc_db_num_fields($rslt2);
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
                    $cntent .= "<td width=\"200px\" style=\"font-weight:bold;vertical-align:top;\" class=\"likeheader lovtd\">" . $labl1 . "</td>";
                    $cntent .= "<td width=\"250px\" class=\"lovtd\">" . $row1[6] . "</td>";
                    $cntent .= "<td width=\"50px\" class=\"lovtd\">$row1[8]</td>";
                    $cntent .= "<td width=\"50px\" class=\"lovtd\">" . $row1[9] . "%</td>";
                    $cntent .= "</tr>";
                    $i++;
                }
                if ($numRws > 0) {
                    $cntent .= "</tbody></table></div></div>";
                }
                echo $cntent;
            }
        }
    }
}    