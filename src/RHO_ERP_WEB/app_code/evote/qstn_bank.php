<?php
$pageNo = isset($_POST['pageNo']) ? cleanInputData($_POST['pageNo']) : 1;
$lmtSze = isset($_POST['limitSze']) ? cleanInputData($_POST['limitSze']) : 10;
$sortBy = isset($_POST['sortBy']) ? cleanInputData($_POST['sortBy']) : "";
$curIdx = 0;
$orgID = $_SESSION['ORG_ID'];
$usrID = $_SESSION['USRID'];
$canAddQstn = test_prmssns($dfltPrvldgs[9], $mdlNm);
$canEdtQstn = test_prmssns($dfltPrvldgs[10], $mdlNm);
$canDelQstn = test_prmssns($dfltPrvldgs[11], $mdlNm);

if (array_key_exists('lgn_num', get_defined_vars())) {
    if ($lgn_num > 0 && $canview === true) {
        if ($qstr == "DELETE") {
            if ($actyp == 1) {
                $pKeyID = isset($_POST['pKeyID']) ? cleanInputData($_POST['pKeyID']) : -1;
                $qstnNm = isset($_POST['qstnNm']) ? cleanInputData($_POST['qstnNm']) : "";
                echo deleteQuestion($pKeyID, $qstnNm);
            } else if ($actyp == 2) {
                $pKeyID = isset($_POST['pKeyID']) ? cleanInputData($_POST['pKeyID']) : -1;
                $ansNm = isset($_POST['ansNm']) ? cleanInputData($_POST['ansNm']) : "";
                echo deleteQuestionAns($pKeyID, $ansNm);
            }
        } else if ($qstr == "UPDATE") {
            if ($actyp == 1) {
                //Questions
                header("content-type:application/json");
                //categoryCombo
                $orgid = $_SESSION['ORG_ID'];
                $inptQuestionHdrID = isset($_POST['qstnID']) ? cleanInputData($_POST['qstnID']) : -1;
                $questionDesc = isset($_POST['qstnDesc']) ? cleanInputData($_POST['qstnDesc']) : "";
                $questionAnsTyp = isset($_POST['qstnAnsType']) ? cleanInputData($_POST['qstnAnsType']) : "";
                $qCategory = isset($_POST['qstnCtgry']) ? cleanInputData($_POST['qstnCtgry']) : "";
                $allwdNumSlctns = isset($_POST['qstnAllwdAns']) ? cleanInputData($_POST['qstnAllwdAns']) : "";
                $qImageLoc = isset($_POST['qImageLoc']) ? cleanInputData($_POST['qImageLoc']) : "";
                $qstnHint = isset($_POST['qstnHint']) ? cleanInputData($_POST['qstnHint']) : "";
                $oldQuestionHdrID = getGnrlRecID2("self.self_question_bank", "qstn_desc", "qstn_id", $questionDesc);

                if ($questionDesc != "" && ($oldQuestionHdrID <= 0 || $oldQuestionHdrID == $inptQuestionHdrID)) {
                    if ($inptQuestionHdrID <= 0) {
                        createQuestion($questionDesc, $questionAnsTyp, $qImageLoc, $allwdNumSlctns, $qCategory, $qstnHint);
                        $inptQuestionHdrID = getGnrlRecID2("self.self_question_bank", "qstn_desc", "qstn_id", $questionDesc);
                    } else {
                        updateQuestion($inptQuestionHdrID, $questionDesc, $questionAnsTyp, $qImageLoc, $allwdNumSlctns, $qCategory, $qstnHint);
                    }
                    $arr_content['percent'] = 100;
                    $arr_content['qstnID'] = $inptQuestionHdrID;
                    $arr_content['message'] = "<span style=\"color:green;\"><i class=\"fa fa-check\" aria-hidden=\"true\"></i></span>Question Successfully Saved!";
                    echo json_encode($arr_content);
                    exit();
                } else {
                    $arr_content['percent'] = 100;
                    $arr_content['qstnID'] = -1;
                    $arr_content['message'] = "<span style=\"color:red;\"><i class=\"fa fa-exclamation-circle\" aria-hidden=\"true\"></i>Either the New Question Name is in Use <br/>or Data Supplied is Incomplete!</span>";
                    echo json_encode($arr_content);
                    exit();
                }
            } else if ($actyp == 2) {
                //Question Possible Answers
                header("content-type:application/json");
                $orgid = $_SESSION['ORG_ID'];
                $inptQuestionHdrID = isset($_POST['ansQstnID']) ? cleanInputData($_POST['ansQstnID']) : -1;
                $inptPssblAnsID = isset($_POST['ansPssblID']) ? cleanInputData($_POST['ansPssblID']) : -1;
                $answerDesc = isset($_POST['answrDesc']) ? cleanInputData($_POST['answrDesc']) : "";
                $answerHintText = isset($_POST['ansHintTxt']) ? cleanInputData($_POST['ansHintTxt']) : "";
                $aImageLoc = isset($_POST['ansImgLoc']) ? cleanInputData($_POST['ansImgLoc']) : "";
                $answerHint = isset($_POST['answrHint']) ? cleanInputData($_POST['answrHint']) : "";
                $isCorrectAns = ((isset($_POST['ans_IsCrct']) ? cleanInputData($_POST['ans_IsCrct']) : "NO") == "YES") ? "1" : "0";
                $ansOrderNo = isset($_POST['ansSortNo']) ? cleanInputData($_POST['ansSortNo']) : 1;
                $isAnsEnabled = ((isset($_POST['ans_IsEnbld']) ? cleanInputData($_POST['ans_IsEnbld']) : "NO") == "YES") ? "1" : "0";

                $oldPssblAnsID = getQstnAnsID($inptQuestionHdrID, $answerDesc);

                if ($answerDesc != "" && ($oldPssblAnsID <= 0 || $oldPssblAnsID == $inptPssblAnsID)) {
                    if ($inptPssblAnsID <= 0) {
                        createQuestionAns($inptQuestionHdrID, $answerDesc, $ansOrderNo, $isCorrectAns, $aImageLoc, $answerHintText, $answerHint, $isAnsEnabled);
                        $inptPssblAnsID = getQstnAnsID($inptQuestionHdrID, $answerDesc);
                    } else {
                        updateQuestionAns($inptPssblAnsID, $answerDesc, $ansOrderNo, $isCorrectAns, $aImageLoc, $answerHintText, $answerHint, $isAnsEnabled);
                    }
                    $arr_content['percent'] = 100;
                    $arr_content['ansPssblID'] = $inptPssblAnsID;
                    $arr_content['message'] = "<span style=\"color:green;\"><i class=\"fa fa-check\" aria-hidden=\"true\"></i></span>Question Successfully Saved!";
                    echo json_encode($arr_content);
                    exit();
                } else {
                    $arr_content['percent'] = 100;
                    $arr_content['ansPssblID'] = -1;
                    $arr_content['message'] = "<span style=\"color:red;\"><i class=\"fa fa-exclamation-circle\" aria-hidden=\"true\"></i>Either the New Question Name is in Use <br/>or Data Supplied is Incomplete!</span>";
                    echo json_encode($arr_content);
                    exit();
                }
            }
        } else {
            $prsnid = $_SESSION['PRSN_ID'];
            if ($vwtyp == 0) {
                $sbmtdQstnID = isset($_POST['sbmtdQstnID']) ? $_POST['sbmtdQstnID'] : -1;
                echo $cntent . "<li>
                                    <span class=\"divider\"><i class=\"fa fa-angle-right\" aria-hidden=\"true\"></i></span>
                                    <span style=\"text-decoration:none;\">Questions Bank</span>
				</li>
                               </ul>
                              </div>";

                $total = get_QuestionsTtl($srchFor, $srchIn);
                if ($pageNo > ceil($total / $lmtSze)) {
                    $pageNo = 1;
                } else if ($pageNo < 1) {
                    $pageNo = ceil($total / $lmtSze);
                }

                $curIdx = $pageNo - 1;
                $result = get_Questions($srchFor, $srchIn, $curIdx, $lmtSze);
                $prntArray = array();
                $cntr = 0;
                $colClassType1 = "col-lg-2";
                $colClassType2 = "col-lg-3";
                $colClassType3 = "col-lg-4";
                ?>
                <form id='allQstnsForm' action='' method='post' accept-charset='UTF-8'>
                    <div class="row rhoRowMargin">
                        <?php if ($canAddQstn === true) { ?>
                            <div class="<?php echo $colClassType2; ?>" style="padding:0px 1px 0px 1px !important;"> 
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="getOneQstnForm(-1, 3);" style="width:100% !important;">
                                        <img src="cmn_images/add1-64.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                        New Question
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
                                <input class="form-control" id="allQstnsSrchFor" type = "text" placeholder="Search For" value="<?php echo trim(str_replace("%", " ", $srchFor)); ?>" onkeyup="enterKeyFuncAllQstns(event, '', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>')">
                                <input id="allQstnsPageNo" type = "hidden" value="<?php echo $pageNo; ?>">
                                <label class="btn btn-primary btn-file input-group-addon" onclick="getAllQstns('clear', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>')">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </label>
                                <label class="btn btn-primary btn-file input-group-addon" onclick="getAllQstns('', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>')">
                                    <span class="glyphicon glyphicon-search"></span>
                                </label> 
                            </div>
                        </div>
                        <div class="<?php echo $colClassType3; ?>">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-filter"></span></span>
                                <select data-placeholder="Select..." class="form-control chosen-select" id="allQstnsSrchIn">
                                    <?php
                                    $valslctdArry = array("", "", "");
                                    $srchInsArrys = array("Question", "Category", "Hint");

                                    for ($z = 0; $z < count($srchInsArrys); $z++) {
                                        if ($srchIn == $srchInsArrys[$z]) {
                                            $valslctdArry[$z] = "selected";
                                        }
                                        ?>
                                        <option value="<?php echo $srchInsArrys[$z]; ?>" <?php echo $valslctdArry[$z]; ?>><?php echo $srchInsArrys[$z]; ?></option>
                                    <?php } ?>
                                </select>
                                <span class="input-group-addon" style="max-width: 1px !important;padding:0px !important;width:1px !important;border:none !important;"></span>
                                <select data-placeholder="Select..." class="form-control chosen-select" id="allQstnsDsplySze" style="min-width:70px !important;">                            
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
                                        <a class="rhopagination" href="javascript:getAllQstns('previous', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>');" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="rhopagination" href="javascript:getAllQstns('next', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>');" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>   
                </div>
                <div class="row" style="padding:0px 15px 0px 15px !important"> 
                    <div class="col-md-7" style="padding:0px 1px 0px 1px !important">
                        <fieldset class="basic_person_fs">                                        
                            <table class="table table-striped table-bordered table-responsive" id="allQstnsTable" cellspacing="0" width="100%" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Question</th>   
                                        <th>Answer Type</th>   
                                        <th>Question Category</th> 
                                        <th>&nbsp;</th>                                       
                                        <?php if ($canDelQstn === true) { ?>
                                            <th>&nbsp;</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = loc_db_fetch_array($result)) {
                                        if ($sbmtdQstnID <= 0 && $cntr <= 0) {
                                            $sbmtdQstnID = $row[0];
                                        }
                                        $cntr += 1;
                                        ?>
                                        <tr id="allQstnsRow_<?php echo $cntr; ?>" class="hand_cursor">                                    
                                            <td class="lovtd"><?php echo ($curIdx * $lmtSze) + ($cntr); ?></td>
                                            <td class="lovtd"><?php echo $row[1]; ?>
                                                <input type="hidden" class="form-control" aria-label="..." id="allQstnsRow<?php echo $cntr; ?>_QstnID" value="<?php echo $row[0]; ?>">
                                            </td>
                                            <td class="lovtd"><?php echo $row[2]; ?></td>
                                            <td class="lovtd"><?php echo $row[5]; ?></td>
                                            <td class="lovtd">
                                                <button type="button" class="btn btn-default" style="margin: 0px !important;padding:0px 3px 2px 4px !important;" onclick="getOneQstnForm(<?php echo $row[0]; ?>, 2);" data-toggle="tooltip" data-placement="bottom" title="View Details">
                                                    <img src="cmn_images/kghostview.png" style="height:15px; width:auto; position: relative; vertical-align: middle;">
                                                </button>
                                            </td>
                                            <?php if ($canDelQstn === true) { ?>
                                                <td class="lovtd">
                                                    <button type="button" class="btn btn-default" style="margin: 0px !important;padding:0px 3px 2px 4px !important;" onclick="delQstn('allQstnsRow_<?php echo $cntr; ?>');" data-toggle="tooltip" data-placement="bottom" title="Delete Question">
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
                    <div  class="col-md-5" style="padding:0px 1px 0px 1px !important">
                        <fieldset class="basic_person_fs" style="padding-top:5px !important;">
                            <div class="" id="allQstnsDetailInfo">
                                <?php
                                if ($sbmtdQstnID > 0) {
                                    ?>
                                    <div class="row" style="padding:0px 15px 0px 15px !important">
                                        <div class="col-md-12" style="padding:0px 1px 0px 1px !important;"> 
                                            <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="getOneAnsForm(-1,<?php echo $sbmtdQstnID; ?>, 5);" style="width:100% !important;">
                                                <img src="cmn_images/add1-64.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                                New Possible Answer
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row" style="padding:0px 15px 0px 15px !important">                  
                                        <div class="col-md-12" style="padding:0px 1px 0px 1px !important">
                                            <table class="table table-striped table-bordered table-responsive" id="srvyQstnsAnsTable" cellspacing="0" width="100%" style="width:100%;min-width: 300px !important;">
                                                <thead>
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Possible Answer</th>
                                                        <th>Correct Answer?</th>
                                                        <th>Enabled?</th>
                                                        <th>&nbsp;</th>
                                                        <th>&nbsp;</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $curIdx=0;
                                                    $cntr = 0;
                                                    $result2 = get_Answers(0, 10000000, $sbmtdQstnID);
                                                    while ($row2 = loc_db_fetch_array($result2)) {
                                                        $cntr += 1;
                                                        ?>
                                                        <tr id="srvyQstnsAnsRow_<?php echo $cntr; ?>">                                    
                                                            <td class="lovtd"><span><?php echo ($curIdx * $lmtSze) + ($cntr); ?></span></td>
                                                            <td class="lovtd">                                                                   
                                                                <span><?php echo $row2[1]; ?></span>
                                                                <input type="hidden" class="form-control" aria-label="..." id="srvyQstnsAnsRow<?php echo $cntr; ?>_PsblAnsID" value="<?php echo $row2[0]; ?>" style="width:100% !important;">                                              
                                                            </td>                                             

                                                            <td class="lovtd">
                                                                <?php
                                                                $isChkd = "";
                                                                $isRdOnly = "disabled=\"true\"";
                                                                if ($row2[2] == "1") {
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
                                                                <?php
                                                                $isChkd = "";
                                                                $isRdOnly = "disabled=\"true\"";
                                                                if ($row2[7] == "1") {
                                                                    $isChkd = "checked=\"true\"";
                                                                }
                                                                ?>   
                                                                <div class="form-group form-group-sm" style="width:100% !important;margin-bottom:0px !important;">
                                                                    <div class="form-check" style="font-size: 12px !important;">
                                                                        <label class="form-check-label">
                                                                            <input type="checkbox" class="form-check-input" id="srvyQstnAnsRow<?php echo $cntr; ?>_IsEnbld" name="srvyQstnAnsRow<?php echo $cntr; ?>_IsEnbld" <?php echo $isChkd . " " . $isRdOnly; ?> >
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </td> 
                                                            <td class="lovtd">
                                                                <button type="button" class="btn btn-default" style="margin: 0px !important;padding:0px 3px 2px 4px !important;" onclick="getOneAnsForm(<?php echo $row2[0]; ?>,<?php echo $sbmtdQstnID; ?>, 4);" data-toggle="tooltip" data-placement="bottom" title="View Details">
                                                                    <img src="cmn_images/kghostview.png" style="height:15px; width:auto; position: relative; vertical-align: middle;">
                                                                </button>
                                                            </td>
                                                            <td class="lovtd">
                                                                <button type="button" class="btn btn-default" style="margin: 0px !important;padding:0px 3px 2px 4px !important;" onclick="delAns('srvyQstnsAnsRow_<?php echo $cntr; ?>');" data-toggle="tooltip" data-placement="bottom" title="Delete Answer from Question">
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
                                <?php
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
                $sbmtdQstnID = isset($_POST['sbmtdQstnID']) ? $_POST['sbmtdQstnID'] : -1;
                if ($sbmtdQstnID > 0) {
                    ?>
                    <div class="row" style="padding:0px 15px 0px 15px !important">
                        <div class="col-md-12" style="padding:0px 1px 0px 1px !important;"> 
                            <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="getOneAnsForm(-1,<?php echo $sbmtdQstnID; ?>, 5);" style="width:100% !important;">
                                <img src="cmn_images/add1-64.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                New Possible Answer
                            </button>
                        </div>
                    </div>
                    <div class="row" style="padding:0px 15px 0px 15px !important">                  
                        <div class="col-md-12" style="padding:0px 1px 0px 1px !important">
                            <table class="table table-striped table-bordered table-responsive" id="srvyQstnsAnsTable" cellspacing="0" width="100%" style="width:100%;min-width: 300px !important;">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Possible Answer</th>
                                        <th>Correct Answer?</th>
                                        <th>Enabled?</th>
                                        <th>&nbsp;</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cntr = 0;

                                    $result2 = get_Answers(0, 10000000, $sbmtdQstnID);
                                    while ($row2 = loc_db_fetch_array($result2)) {
                                        $cntr += 1;
                                        ?>
                                        <tr id="srvyQstnsAnsRow_<?php echo $cntr; ?>">                                    
                                            <td class="lovtd"><span><?php echo ($curIdx * $lmtSze) + ($cntr); ?></span></td>
                                            <td class="lovtd">                                                                   
                                                <span><?php echo $row2[1]; ?></span>
                                                <input type="hidden" class="form-control" aria-label="..." id="srvyQstnsAnsRow<?php echo $cntr; ?>_PsblAnsID" value="<?php echo $row2[0]; ?>" style="width:100% !important;">                                              
                                            </td>                                             

                                            <td class="lovtd">
                                                <?php
                                                $isChkd = "";
                                                $isRdOnly = "disabled=\"true\"";
                                                if ($row2[2] == "1") {
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
                                                <?php
                                                $isChkd = "";
                                                $isRdOnly = "disabled=\"true\"";
                                                if ($row2[7] == "1") {
                                                    $isChkd = "checked=\"true\"";
                                                }
                                                ?>   
                                                <div class="form-group form-group-sm" style="width:100% !important;margin-bottom:0px !important;">
                                                    <div class="form-check" style="font-size: 12px !important;">
                                                        <label class="form-check-label">
                                                            <input type="checkbox" class="form-check-input" id="srvyQstnAnsRow<?php echo $cntr; ?>_IsEnbld" name="srvyQstnAnsRow<?php echo $cntr; ?>_IsEnbld" <?php echo $isChkd . " " . $isRdOnly; ?> >
                                                        </label>
                                                    </div>
                                                </div>
                                            </td> 
                                            <td class="lovtd">
                                                <button type="button" class="btn btn-default" style="margin: 0px !important;padding:0px 3px 2px 4px !important;" onclick="getOneAnsForm(<?php echo $row2[0]; ?>,<?php echo $sbmtdQstnID; ?>, 4)" data-toggle="tooltip" data-placement="bottom" title="View Details">
                                                    <img src="cmn_images/kghostview.png" style="height:15px; width:auto; position: relative; vertical-align: middle;">
                                                </button>
                                            </td>
                                            <td class="lovtd">
                                                <button type="button" class="btn btn-default" style="margin: 0px !important;padding:0px 3px 2px 4px !important;" onclick="delAns('srvyQstnsAnsRow_<?php echo $cntr; ?>');" data-toggle="tooltip" data-placement="bottom" title="Delete Answer from Question">
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
                    <?php
                } else {
                    ?>
                    <span>No Results Found</span>
                    <?php
                }
            } else if ($vwtyp == 2) {
                //Questions Detail
                $sbmtdQstnID = isset($_POST['sbmtdQstnID']) ? $_POST['sbmtdQstnID'] : -1;
                $result = get_OneQuestion($sbmtdQstnID);
                while ($row = loc_db_fetch_array($result)) {
                    ?>
                    <form method="post" class="form-horizontal" id="qstnsForm">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <label for="qstnDesc" class="control-label">Question (*):</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <textarea name="qstnDesc" id="qstnDesc" required autofocus rows="7" class="form-control"><?php echo $row[1]; ?></textarea>
                                        <input type="hidden" id="qstnID" name="qstnID" class="form-control" value="<?php echo $row[0]; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <label for="qstnAnsType" class="control-label">Answer Type:</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <select name="qstnAnsType" id="qstnAnsType" class="form-control">
                                            <optgroup label="Answer Types">
                                                <?php
                                                $valslctdArry = array("", "", "", "");
                                                $srchInsArrys = array("Single Answer (Radio)", "Multiple-Select Answers (Checkbox)",
                                                    "Single-Text Answer (Textfield)", "Multiple-Text Answer (Textfield)");
                                                for ($z = 0; $z < count($srchInsArrys); $z++) {
                                                    if ($row[2] == $srchInsArrys[$z]) {
                                                        $valslctdArry[$z] = "selected";
                                                    }
                                                    ?>
                                                    <option value="<?php echo $srchInsArrys[$z]; ?>" <?php echo $valslctdArry[$z]; ?>><?php echo $srchInsArrys[$z]; ?></option>
                                                <?php } ?>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <label for="qstnCtgry" class="control-label">Category: </label>
                                    </div>
                                    <div class="col-sm-8">
                                        <select name="qstnCtgry" id="qstnCtgry" class="form-control">
                                            <optgroup label="Question Categories">
                                                <?php
                                                $titleRslt = get_QstnCtgries();
                                                while ($titleRow = loc_db_fetch_array($titleRslt)) {
                                                    $selectedTxt = "";
                                                    if ($titleRow[1] == $row[5]) {
                                                        $selectedTxt = "selected";
                                                    }
                                                    ?>
                                                    <option value="<?php echo $titleRow[1]; ?>" <?php echo $selectedTxt; ?>><?php echo $titleRow[1]; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <label for="qstnAllwdAns" class="control-label">Allowed No. of Ans:</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="number" name="qstnAllwdAns" id="qstnAllwdAns" class="form-control" value="<?php echo $row[4]; ?>">
                                    </div>
                                </div>
                                <div class="form-group" style="margin-bottom:5px !important;">
                                    <div class="col-sm-4">
                                        <label for="qstnImgLoc" class="control-label">Image Location:</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" name="qstnImgLoc" id="qstnImgLoc" class="form-control" value="<?php echo $row[3]; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <label for="qstnActions" class="control-label">Actions:</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <button class="btn btn-primary" type="button" onclick="saveQstnForm();">SAVE</button>
                                        <button class="btn btn-default" type="button" onclick="$('#myFormsModalLg').modal('hide');">CLOSE</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label for="qstnHint" class="control-label">Question Hint (Further Explanations):</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12" id="qstnHint"></div> 
                                    <input type="hidden" name="allQstnHint" id="allQstnHint" class="form-control" value="<?php echo urlencode($row[6]); ?>">
                                </div> 
                            </div>
                        </div>
                    </form>
                    <?php
                }
            } else if ($vwtyp == 3) {
                //New Question
                $sbmtdQstnID = isset($_POST['sbmtdQstnID']) ? $_POST['sbmtdQstnID'] : -1;
                ?>
                <form method="post" class="form-horizontal" id="qstnsForm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label for="qstnDesc" class="control-label">Question (*):</label>
                                </div>
                                <div class="col-sm-8">
                                    <textarea name="qstnDesc" id="qstnDesc" required autofocus rows="7" class="form-control"></textarea>
                                    <input type="hidden" id="qstnID" name="qstnID" class="form-control" value="-1">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label for="qstnAnsType" class="control-label">Answer Type:</label>
                                </div>
                                <div class="col-sm-8">
                                    <select name="qstnAnsType" id="qstnAnsType" class="form-control">
                                        <optgroup label="Answer Types">
                                            <?php
                                            $valslctdArry = array("", "", "", "");
                                            $srchInsArrys = array("Single Answer (Radio)", "Multiple-Select Answers (Checkbox)",
                                                "Single-Text Answer (Textfield)", "Multiple-Text Answer (Textfield)");
                                            for ($z = 0; $z < count($srchInsArrys); $z++) {
                                                ?>
                                                <option value="<?php echo $srchInsArrys[$z]; ?>" <?php echo $valslctdArry[$z]; ?>><?php echo $srchInsArrys[$z]; ?></option>
                                            <?php } ?>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label for="qstnCtgry" class="control-label">Category: </label>
                                </div>
                                <div class="col-sm-8">
                                    <select name="qstnCtgry" id="qstnCtgry" class="form-control">
                                        <optgroup label="Question Categories">
                                            <?php
                                            $titleRslt = get_QstnCtgries();
                                            while ($titleRow = loc_db_fetch_array($titleRslt)) {
                                                $selectedTxt = "";
                                                ?>
                                                <option value="<?php echo $titleRow[1]; ?>" <?php echo $selectedTxt; ?>><?php echo $titleRow[1]; ?></option>
                                                <?php
                                            }
                                            ?>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label for="qstnAllwdAns" class="control-label">Allowed No. of Ans:</label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="number" name="qstnAllwdAns" id="qstnAllwdAns" class="form-control" value="">
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:5px !important;">
                                <div class="col-sm-4">
                                    <label for="qstnImgLoc" class="control-label">Image Location:</label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="text" name="qstnImgLoc" id="qstnImgLoc" class="form-control" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label for="qstnActions" class="control-label">Actions:</label>
                                </div>
                                <div class="col-sm-8">
                                    <button class="btn btn-primary" type="button" onclick="saveQstnForm();">SAVE</button>
                                    <button class="btn btn-default" type="button" onclick="$('#myFormsModalLg').modal('hide');">CLOSE</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label for="answrHint" class="control-label">Question Hint (Further Explanations):</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12" id="qstnHint"></div> 
                                <input type="hidden" name="allQstnHint" id="allQstnHint" class="form-control" value="">
                            </div>
                        </div>
                    </div>
                </form>
                <?php
            } else if ($vwtyp == 4) {
                //Answer Detail
                $sbmtdAnsID = isset($_POST['sbmtdAnsID']) ? $_POST['sbmtdAnsID'] : -1;
                $result = get_AnswerDetail($sbmtdAnsID);
                while ($row = loc_db_fetch_array($result)) {
                    ?>
                    <form method="post" class="form-horizontal" id="answrForm">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <label for="answrDesc" class="control-label">Possible Answer (*):</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <textarea rows="7" id="answrDesc" name="answrDesc" required autofocus class="form-control"><?php echo $row[1]; ?></textarea>
                                        <input type="hidden" id="ansPssblID" name="ansPssblID" class="form-control" value="<?php echo $row[0]; ?>">
                                        <input type="hidden" id="ansQstnID" name="ansQstnID" class="form-control" value="<?php echo $row[8]; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <label for="ansSortNo" class="control-label">Answer Sort No.:</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="number" id="ansSortNo" name="ansSortNo" class="form-control" value="<?php echo $row[2]; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?php
                                    $chkdYes = "";
                                    $chkdNo = "checked=\"true\"";
                                    if ($row[3] == "YES") {
                                        $chkdNo = "";
                                        $chkdYes = "checked=\"true\"";
                                    }
                                    ?>
                                    <div class="col-sm-4">
                                        <label for="ans_IsCrct" class="control-label">Is Correct Answer?:</label>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="radio">
                                            <label class="control-label">
                                                <input type="radio" name="ans_IsCrct" value="YES" <?php echo $chkdYes; ?>>YES</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="radio">
                                            <label class="control-label">
                                                <input type="radio" name="ans_IsCrct" value="NO" <?php echo $chkdNo; ?>>NO</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?php
                                    $chkdYes = "";
                                    $chkdNo = "checked=\"\"";
                                    if ($row[7] == "1") {
                                        $chkdNo = "";
                                        $chkdYes = "checked=\"\"";
                                    }
                                    ?>
                                    <div class="col-sm-4">
                                        <label for="ans_IsEnbld" class="control-label">Is Enabled?:</label>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="radio">
                                            <label class="control-label">
                                                <input type="radio" name="ans_IsEnbld" value="YES" <?php echo $chkdYes; ?>>YES</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="radio">
                                            <label class="control-label">
                                                <input type="radio" name="ans_IsEnbld" value="NO" <?php echo $chkdNo; ?>>NO</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <label for="ansImgLoc" class="control-label">Image Location:</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ansImgLoc" name="ansImgLoc" class="form-control"  value="<?php echo $row[4]; ?>">
                                    </div>
                                </div>
                                <div class="form-group" style="margin-bottom:5px !important;">
                                    <div class="col-sm-4">
                                        <label for="ansHintTxt" class="control-label">Hint Link Text:</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" id="ansHintTxt" name="ansHintTxt" class="form-control" value="<?php echo $row[5]; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <label for="ansActions" class="control-label">Actions:</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <button class="btn btn-primary" type="button" onclick="saveAnsForm();">SAVE</button>
                                        <button class="btn btn-default" type="button" onclick="$('#myFormsModalLg').modal('hide');">CLOSE</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div></div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label for="answrHint" class="control-label">Answer Hint (Further Explanations):</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12" id="answrHint"></div> 
                                    <input type="hidden" name="allAnswrHint" id="allAnswrHint" class="form-control" value="<?php echo urlencode($row[6]); ?>">
                                </div>
                            </div>
                        </div>
                    </form>
                    <?php
                }
            } else if ($vwtyp == 5) {
                //New Answer
                $sbmtdAnsID = isset($_POST['sbmtdAnsID']) ? $_POST['sbmtdAnsID'] : -1;
                $sbmtdQstnID = isset($_POST['sbmtdQstnID']) ? $_POST['sbmtdQstnID'] : -1;
                ?>
                <form method="post" class="form-horizontal" id="answrForm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label for="answrDesc" class="control-label">Possible Answer (*):</label>
                                </div>
                                <div class="col-sm-8">
                                    <textarea rows="7" id="answrDesc" name="answrDesc" required autofocus class="form-control"></textarea>
                                    <input type="hidden" id="ansPssblID" name="ansPssblID" class="form-control" value="-1"/>
                                    <input type="hidden" id="ansQstnID" name="ansQstnID" class="form-control" value="<?php echo $sbmtdQstnID; ?>"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label for="ansSortNo" class="control-label">Answer Sort No.:</label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="number" id="ansSortNo" name="ansSortNo" class="form-control" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $chkdYes = "";
                                $chkdNo = "checked=\"\"";
                                ?>
                                <div class="col-sm-4">
                                    <label for="ans_IsCrct" class="control-label">Is Correct Answer?:</label>
                                </div>
                                <div class="col-md-4">
                                    <div class="radio">
                                        <label class="control-label">
                                            <input type="radio" name="ans_IsCrct" value="YES" <?php echo $chkdYes; ?>>YES</label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="radio">
                                        <label class="control-label">
                                            <input type="radio" name="ans_IsCrct" value="NO" <?php echo $chkdYes; ?>>NO</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $chkdYes = "";
                                $chkdNo = "checked=\"\"";
                                ?>
                                <div class="col-sm-4">
                                    <label for="ans_IsEnbld" class="control-label">Is Enabled?:</label>
                                </div>
                                <div class="col-md-4">
                                    <div class="radio">
                                        <label class="control-label">
                                            <input type="radio" name="ans_IsEnbld" value="YES" <?php echo $chkdYes; ?>>YES</label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="radio">
                                        <label class="control-label">
                                            <input type="radio" name="ans_IsEnbld" value="NO" <?php echo $chkdYes; ?>>NO</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label for="ansImgLoc" class="control-label">Image Location:</label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="text" id="ansImgLoc" name="ansImgLoc" class="form-control"  value="">
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom:5px !important;">
                                <div class="col-sm-4">
                                    <label for="ansHintTxt" class="control-label">Hint Link Text:</label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="text" id="ansHintTxt" name="ansHintTxt" class="form-control" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label for="ansActions" class="control-label">Actions:</label>
                                </div>
                                <div class="col-sm-8">
                                    <button class="btn btn-primary" type="button" onclick="saveAnsForm();">SAVE</button>
                                    <button class="btn btn-default" type="button" onclick="$('#myFormsModalLg').modal('hide');">CLOSE</button>
                                </div>
                            </div>
                            <div class="row">
                                <div></div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label for="answrHint" class="control-label">Answer Hint (Further Explanations):</label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12" id="answrHint"></div> 
                                <input type="hidden" name="allAnswrHint" id="allAnswrHint" class="form-control" value="">
                            </div>
                        </div>
                    </div>
                </form>
                <?php
            }
        }
    }
}    
