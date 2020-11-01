<?php
$canAddWkfHrchy = test_prmssns($dfltPrvldgs[10], $mdlNm);
$canEdtWkfHrchy = test_prmssns($dfltPrvldgs[11], $mdlNm);
$canDelWkfHrchy = test_prmssns($dfltPrvldgs[12], $mdlNm);

$pageNo = isset($_POST['pageNo']) ? cleanInputData($_POST['pageNo']) : 1;
$lmtSze = isset($_POST['limitSze']) ? cleanInputData($_POST['limitSze']) : 10;
$sortBy = isset($_POST['sortBy']) ? cleanInputData($_POST['sortBy']) : "";

if (array_key_exists('lgn_num', get_defined_vars())) {
    if ($lgn_num > 0 && $canview === true) {
        if ($qstr == "DELETE") {
            if ($actyp == 1) {
                $pKeyID = isset($_POST['pKeyID']) ? cleanInputData($_POST['pKeyID']) : -1;
                $hrchyNm = isset($_POST['hrchyNm']) ? cleanInputData($_POST['hrchyNm']) : "";
                if ($pKeyID > 0) {
                    echo deleteWkfHrchy($pKeyID, $hrchyNm);
                }
            } else if ($actyp == 2) {
                $pKeyID = isset($_POST['pKeyID']) ? cleanInputData($_POST['pKeyID']) : -1;
                $apprvNm = isset($_POST['apprvNm']) ? cleanInputData($_POST['apprvNm']) : "";
                if ($pKeyID > 0) {
                    echo deleteWkfMnlHrchy($pKeyID, $apprvNm);
                }
            }
        } else if ($qstr == "UPDATE") {
            if ($actyp == 1) {
                //Save Workflow Hierarchy
                header("content-type:application/json");
                $orgid = $_SESSION['ORG_ID'];
                $wkfHrchyID = isset($_POST['wkfHrchyID']) ? (int) cleanInputData($_POST['wkfHrchyID']) : -1;
                $wkfHrchyNm = isset($_POST['wkfHrchyNm']) ? cleanInputData($_POST['wkfHrchyNm']) : "";
                $wkfHrchyDesc = isset($_POST['wkfHrchyDesc']) ? cleanInputData($_POST['wkfHrchyDesc']) : "";
                $wkfHrchyIsEnbld = isset($_POST['wkfHrchyIsEnbld']) ? cleanInputData($_POST['wkfHrchyIsEnbld']) : "";
                $wkfHrchyType = isset($_POST['wkfHrchyType']) ? cleanInputData($_POST['wkfHrchyType']) : '';
                $wkfHrchySQL = isset($_POST['wkfHrchySQL']) ? cleanInputData($_POST['wkfHrchySQL']) : '';
                $slctdHrchyLvls = isset($_POST['slctdHrchyLvls']) ? cleanInputData($_POST['slctdHrchyLvls']) : '';

                $isenbld = ($wkfHrchyIsEnbld == "YES") ? "1" : "0";
                $oldHrchyID = getGnrlRecID2("wkf.wkf_hierarchy_hdr", "hierarchy_name", "hierarchy_id", $wkfHrchyNm);
                if ($wkfHrchyNm != "" && $wkfHrchyType != "" && ($oldHrchyID <= 0 || $oldHrchyID == $wkfHrchyID)) {
                    if ($wkfHrchyID <= 0) {
                        createWkfHrchy($wkfHrchyNm, $wkfHrchyDesc, $wkfHrchyType, $wkfHrchySQL, $isenbld);
                        $wkfHrchyID = getGnrlRecID2("wkf.wkf_hierarchy_hdr", "hierarchy_name", "hierarchy_id", $wkfHrchyNm);
                    } else {
                        updateWkfHrchy($wkfHrchyID, $wkfHrchyNm, $wkfHrchyDesc, $wkfHrchyType, $wkfHrchySQL, $isenbld);
                    }
                    $affctdLvls = 0;
                    if ($wkfHrchyID > 0) {
                        if (trim($slctdHrchyLvls, "|~") != "") {
                            //Save Report Parameters
                            $variousRows = explode("|", trim($slctdHrchyLvls, "|"));
                            for ($z = 0; $z < count($variousRows); $z++) {
                                $crntRow = explode("~", $variousRows[$z]);
                                if (count($crntRow) == 6) {
                                    $mnlHrchyID = (int) (cleanInputData1($crntRow[0]));
                                    $apprvrID = cleanInputData1($crntRow[1]);
                                    $apprvrNm = cleanInputData1($crntRow[2]);
                                    $aprvrTyp = cleanInputData1($crntRow[3]);
                                    $apprvrLevel = (int) cleanInputData1($crntRow[4]);
                                    $isEnbld = cleanInputData1($crntRow[5]);
                                    $isEnbld1 = ($isEnbld == "YES") ? "1" : "0";
                                    $apprvrPrsnID = -1;
                                    $apprvrPosID = -1;
                                    $appvrGrpID = -1;
                                    if ($aprvrTyp == "Position Holder") {
                                        $apprvrPosID = $apprvrID;
                                    } else if ($aprvrTyp == "Group") {
                                        $appvrGrpID = $apprvrID;
                                    } else {
                                        $apprvrID = getPersonID($apprvrID);
                                        $apprvrPrsnID = $apprvrID;
                                    }
                                    $oldHrchyDetID = get_MnlHrchyDetID($wkfHrchyID, $apprvrID, $apprvrLevel);
                                    if ($apprvrNm != "") {
                                        if ($oldHrchyDetID <= 0 && $mnlHrchyID <= 0) {
                                            $affctdLvls += createWkfMnlHrchy($wkfHrchyID, $apprvrPrsnID, $apprvrLevel, $isEnbld1, $apprvrPosID, $appvrGrpID, $aprvrTyp);
                                        } else if ($mnlHrchyID > 0) {
                                            $affctdLvls += updateWkfMnlHrchy($mnlHrchyID, $apprvrPrsnID, $apprvrLevel, $isEnbld1, $apprvrPosID, $appvrGrpID, $aprvrTyp);
                                        } else if ($oldHrchyDetID > 0) {
                                            $affctdLvls += updateWkfMnlHrchy($oldHrchyDetID, $apprvrPrsnID, $apprvrLevel, $isEnbld1, $apprvrPosID, $appvrGrpID, $aprvrTyp);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $arr_content['percent'] = 100;
                    $arr_content['wkfHrchyID'] = $wkfHrchyID;
                    $arr_content['message'] = "<span style=\"color:green;\"><i class=\"fa fa-check\" aria-hidden=\"true\"></i></span>Workflow Hierarchy Successfully Saved!<br/>" . $affctdLvls . " Hierarchy Levels Saved!";
                    echo json_encode($arr_content);
                    exit();
                } else {
                    $arr_content['percent'] = 100;
                    $arr_content['wkfHrchyID'] = -1;
                    $arr_content['message'] = "<span style=\"color:red;\"><i class=\"fa fa-exclamation-circle\" aria-hidden=\"true\"></i>Either the New Workflow Hierarchy Exists <br/>or Data Supplied is Incomplete!</span>";
                    echo json_encode($arr_content);
                    exit();
                }
            } else if ($actyp == 2) {
                
            }
        } else {
            if ($vwtyp == 0) {
                $pkID = isset($_POST['sbmtdHrchyID']) ? $_POST['sbmtdHrchyID'] : -1;
                echo $cntent . "<li onclick=\"openATab('#allmodules', 'grp=$group&typ=$type');\">
						<span style=\"text-decoration:none;\">Workflow Manager Menu</span>
				</li>
                                <li>
                                    <span class=\"divider\"><i class=\"fa fa-angle-right\" aria-hidden=\"true\"></i></span>
                                    <span style=\"text-decoration:none;\">Workflow Hierarchies</span>
				</li>
                               </ul>
                              </div>";
                $total = get_WkfHrchyTtl($srchFor, $srchIn);
                if ($pageNo > ceil($total / $lmtSze)) {
                    $pageNo = 1;
                } else if ($pageNo < 1) {
                    $pageNo = ceil($total / $lmtSze);
                }

                $curIdx = $pageNo - 1;
                $result = get_WkfHrchyTblr($srchFor, $srchIn, $curIdx, $lmtSze);
                $cntr = 0;
                $colClassType1 = "col-lg-2";
                $colClassType2 = "col-lg-3";
                $colClassType3 = "col-lg-4";
                ?>
                <form id='allWkfHrchysForm' action='' method='post' accept-charset='UTF-8'>
                    <div class="row rhoRowMargin">
                        <?php
                        if ($canAddWkfHrchy === true) {
                            ?> 
                            <div class="<?php echo $colClassType3; ?>" style="padding:0px 1px 0px 1px !important;"> 
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="getOneWkfHrchyForm(-1, 2);" style="width:100% !important;">
                                        <img src="cmn_images/add1-64.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                        New Hierarchy
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="saveWkfHrchyForm();" style="width:100% !important;">
                                        <img src="cmn_images/FloppyDisk.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                        Save
                                    </button>
                                </div>
                            </div>
                            <?php
                        } else {
                            $colClassType1 = "col-lg-2";
                            $colClassType2 = "col-lg-5";
                        }
                        ?>
                        <div class="<?php echo $colClassType2; ?>" style="padding:0px 15px 0px 15px !important;">
                            <div class="input-group">
                                <input class="form-control" id="allWkfHrchysSrchFor" type = "text" placeholder="Search For" value="<?php echo trim(str_replace("%", " ", $srchFor)); ?>" onkeyup="enterKeyFuncWkfHrchys(event, '', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>')">
                                <input id="allWkfHrchysPageNo" type = "hidden" value="<?php echo $pageNo; ?>">
                                <label class="btn btn-primary btn-file input-group-addon" onclick="getAllWkfHrchys('clear', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>')">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </label>
                                <label class="btn btn-primary btn-file input-group-addon" onclick="getAllWkfHrchys('', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>')">
                                    <span class="glyphicon glyphicon-search"></span>
                                </label> 
                            </div>
                        </div>
                        <div class="<?php echo $colClassType2; ?>">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-filter"></span></span>
                                <select data-placeholder="Select..." class="form-control chosen-select" id="allWkfHrchysSrchIn">
                                    <?php
                                    $valslctdArry = array("", "");
                                    $srchInsArrys = array("Name", "Description");

                                    for ($z = 0; $z < count($srchInsArrys); $z++) {
                                        if ($srchIn == $srchInsArrys[$z]) {
                                            $valslctdArry[$z] = "selected";
                                        }
                                        ?>
                                        <option value="<?php echo $srchInsArrys[$z]; ?>" <?php echo $valslctdArry[$z]; ?>><?php echo $srchInsArrys[$z]; ?></option>
                                    <?php } ?>
                                </select>
                                <span class="input-group-addon" style="max-width: 1px !important;padding:0px !important;width:1px !important;border:none !important;"></span>
                                <select data-placeholder="Select..." class="form-control chosen-select" id="allWkfHrchysDsplySze" style="min-width:70px !important;">                            
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
                                        <a class="rhopagination" href="javascript:getAllWkfHrchys('previous', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>');" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:getAllWkfHrchys('next', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>');" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <div class="row"  style="padding:1px 15px 1px 15px !important;"><hr style="margin:1px 0px 3px 0px;"></div>
                    <div class="row" style="padding:0px 15px 0px 15px !important"> 
                        <div  class="col-lg-3" style="padding:0px 1px 0px 1px !important">
                            <fieldset class="basic_person_fs">                                        
                                <table class="table table-striped table-bordered table-responsive" id="allWkfHrchysTable" cellspacing="0" width="100%" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Workflow Hierarchy Name</th>
                                            <?php
                                            if ($canDelWkfHrchy === true) {
                                                ?>
                                                <th>&nbsp;</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($row = loc_db_fetch_array($result)) {
                                            if ($pkID <= 0 && $cntr <= 0) {
                                                $pkID = $row[0];
                                            }
                                            $cntr += 1;
                                            ?>
                                            <tr id="allWkfHrchysRow_<?php echo $cntr; ?>" class="hand_cursor">                                    
                                                <td class="lovtd"><?php echo ($curIdx * $lmtSze) + ($cntr); ?></td>
                                                <td class="lovtd"><?php echo $row[1]; ?><input type="hidden" class="form-control" aria-label="..." id="allWkfHrchysRow<?php echo $cntr; ?>_HrchyID" value="<?php echo $row[0]; ?>"></td>                                                
                                                <?php
                                                if ($canDelWkfHrchy === true) {
                                                    ?>
                                                    <td class="lovtd">
                                                        <button type="button" class="btn btn-default" style="margin: 0px !important;padding:0px 3px 2px 4px !important;" onclick="delWkfHrchy('allWkfHrchysRow_<?php echo $cntr; ?>');" data-toggle="tooltip" data-placement="bottom" title="Delete Workflow Hierarchy">
                                                            <img src="cmn_images/delete.png" style="height:15px; width:auto; position: relative; vertical-align: middle;">
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
                        <div  class="col-lg-9" style="padding:0px 1px 0px 1px !important">
                            <fieldset class="basic_person_fs" style="padding-top:10px !important;">
                                <div class="container-fluid" id="wkfHrchysDetailInfo">
                                    <?php
                                    if ($pkID > 0) {
                                        $result1 = get_WkfHrchyDet($pkID);
                                        $sbmtdHrchyID = $pkID;
                                        while ($row1 = loc_db_fetch_array($result1)) {
                                            ?>
                                            <div class="row">
                                                <div  class="col-md-6" style="padding:0px 3px 0px 3px !important;">
                                                    <fieldset class="basic_person_fs" style="padding:10px 3px 0px 3px !important;"> 
                                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                                            <label for="wkfHrchyNm" class="control-label col-lg-4">Hierarchy Name:</label>
                                                            <div  class="col-lg-8">
                                                                <?php if ($canEdtWkfHrchy === true) { ?>
                                                                    <input type="text" class="form-control" aria-label="..." id="wkfHrchyNm" name="wkfHrchyNm" value="<?php echo $row1[1]; ?>" style="width:100%;">
                                                                    <input type="hidden" class="form-control" aria-label="..." id="wkfHrchyID" name="wkfHrchyID" value="<?php echo $row1[0]; ?>">
                                                                <?php } else {
                                                                    ?>
                                                                    <span><?php echo $row1[1]; ?></span>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                                            <label for="wkfHrchyDesc" class="control-label col-lg-4">Description:</label>
                                                            <div  class="col-lg-8">
                                                                <?php if ($canEdtWkfHrchy === true) { ?>
                                                                    <textarea class="form-control" aria-label="..." id="wkfHrchyDesc" name="wkfHrchyDesc" style="width:100%;" cols="5" rows="3"><?php echo $row1[2]; ?></textarea>
                                                                <?php } else {
                                                                    ?>
                                                                    <span><?php echo $row1[2]; ?></span>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                                            <label for="wkfHrchyIsEnbld" class="control-label col-lg-6">Enabled?:</label>
                                                            <div class="col-lg-6">
                                                                <?php
                                                                $chkdYes = "";
                                                                $chkdNo = "checked=\"\"";
                                                                if ($row1[3] == "Yes") {
                                                                    $chkdNo = "";
                                                                    $chkdYes = "checked=\"\"";
                                                                }
                                                                ?>
                                                                <?php if ($canEdtWkfHrchy === true) { ?>
                                                                    <label class="radio-inline"><input type="radio" name="wkfHrchyIsEnbld" value="YES" <?php echo $chkdYes; ?>>YES</label>
                                                                    <label class="radio-inline"><input type="radio" name="wkfHrchyIsEnbld" value="NO" <?php echo $chkdNo; ?>>NO</label>
                                                                <?php } else {
                                                                    ?>
                                                                    <span><?php echo ($row1[3] == "Yes" ? "YES" : "NO"); ?></span>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div  class="col-md-6" style="padding:0px 3px 0px 3px !important;">
                                                    <fieldset class = "basic_person_fs">
                                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                                            <label for="wkfHrchyType" class="control-label col-lg-4">Hierarchy Type:</label>
                                                            <div class="col-lg-8">
                                                                <?php if ($canEdtWkfHrchy === true) { ?>
                                                                    <select data-placeholder="Select..." class="form-control chosen-select" id="wkfHrchyType">
                                                                        <?php
                                                                        $valslctdArry = array("", "");
                                                                        $srchInsArrys = array("Manual", "SQL");
                                                                        for ($z = 0; $z < count($srchInsArrys); $z++) {
                                                                            if ($row1[4] == $srchInsArrys[$z]) {
                                                                                $valslctdArry[$z] = "selected";
                                                                            }
                                                                            ?>
                                                                            <option value="<?php echo $srchInsArrys[$z]; ?>" <?php echo $valslctdArry[$z]; ?>><?php echo $srchInsArrys[$z]; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                <?php } else {
                                                                    ?>
                                                                    <span><?php echo $row1[4]; ?></span>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                                            <label for="wkfHrchySQL" class="control-label col-lg-2">SQL Query:</label>
                                                            <div  class="col-lg-10">
                                                                <?php if ($canEdtWkfHrchy === true) { ?>
                                                                    <textarea class="form-control" aria-label="..." id="wkfHrchySQL" name="wkfHrchySQL" style="width:100%;" cols="5" rows="4"><?php echo $row1[5]; ?></textarea>
                                                                <?php } else {
                                                                    ?>
                                                                    <span><?php echo $row1[5]; ?></span>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>                                            
                                            <div class="row">
                                                <div class="col-md-12" style="padding:0px 3px 0px 3px !important;">
                                                    <fieldset class="basic_person_fs"> 
                                                        <?php
                                                        if ($canEdtWkfHrchy === true) {
                                                            $nwRowHtml = "<tr id=\"wkfHrchyLvlsRow__WWW123WWW\">"
                                                                    . "<td class=\"lovtd\"><span class=\"\">New</span></td>"
                                                                    . "<td class=\"lovtd\">
                                                                            <div class=\"form-group form-group-sm col-md-12\" style=\"padding:0px 3px 0px 3px !important;\">
                                                                                <select data-placeholder=\"Select...\" class=\"form-control chosen-select\" id=\"wkfHrchyLvlsRow_WWW123WWW_AprvrTyp\">";
                                                            $valslctdArry = array("", "", "");
                                                            $srchInsArrys = array("Group", "Position Holder", "Individual");
                                                            for ($z = 0; $z < count($srchInsArrys); $z++) {
                                                                $nwRowHtml .= "<option value=\"$srchInsArrys[$z]\" $valslctdArry[$z]>$srchInsArrys[$z]</option>";
                                                            }
                                                            $nwRowHtml .= "</select>
                                                                            </div>
                                                                        </td>
                                                                        <td class=\"lovtd\">  
                                                                            <div class=\"input-group\" style=\"width:100% !important;\">
                                                                                    <input type=\"text\" class=\"form-control\" aria-label=\"...\" id=\"wkfHrchyLvlsRow_WWW123WWW_ApprvrNm\" value=\"\" style=\"width:100% !important;\">
                                                                                    <input type=\"hidden\" class=\"form-control\" aria-label=\"...\" id=\"wkfHrchyLvlsRow_WWW123WWW_ApprvrID\" value=\"-1\">
                                                                                    <input type=\"hidden\" class=\"form-control\" aria-label=\"...\" id=\"wkfHrchyLvlsRow_WWW123WWW_MnlHrchyID\" value=\"-1\">
                                                                                    <label class=\"btn btn-primary btn-file input-group-addon\" onclick=\"getHrchyLovsPage('myLovModal', 'myLovModalTitle', 'myLovModalBody', '', '', '', '', 'radio', true, '', 'wkfHrchyLvlsRow_WWW123WWW_ApprvrID', 'wkfHrchyLvlsRow_WWW123WWW_ApprvrNm', 'clear', 0, '','wkfHrchyLvlsRow_WWW123WWW_AprvrTyp');\">
                                                                                        <span class=\"glyphicon glyphicon-th-list\"></span>
                                                                                    </label></div>
                                                                        </td>
                                                                        <td class=\"lovtd\">
                                                                            <div class=\"form-group form-group-sm\" style=\"width:100% !important;\">
                                                                                    <input type=\"number\" min=\"0\" max=\"9999\" class=\"form-control\" aria-label=\"...\" id=\"wkfHrchyLvlsRow_WWW123WWW_Level\" name=\"wkfHrchyLvlsRow_WWW123WWW_Level\" value=\"\" style=\"width:100%;\">
                                                                            </div>                                                    
                                                                        </td>
                                                                       <td class=\"lovtd\"> 
                                                                           <div class=\"form-group form-group-sm \">
                                                                                         <div class=\"form-check\" style=\"font-size: 12px !important;\">
                                                                                             <label class=\"form-check-label\">
                                                                                                 <input type=\"checkbox\" class=\"form-check-input\" id=\"wkfHrchyLvlsRow_WWW123WWW_Enabled\" name=\"wkfHrchyLvlsRow_WWW123WWW_Enabled\">
                                                                                             </label>
                                                                                         </div>
                                                                           </div>
                                                                        </td>                                                                        
                                                                        <td class=\"lovtd\">
                                                                            <button type=\"button\" class=\"btn btn-default\" style=\"margin: 0px !important;padding:0px 3px 2px 4px !important;\" onclick=\"delWkfHrchyLvl('wkfHrchyLvlsRow__WWW123WWW');\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Delete Hierarchy Level\">
                                                                                <img src=\"cmn_images/delete.png\" style=\"height:15px; width:auto; position: relative; vertical-align: middle;\">
                                                                            </button>
                                                                        </td>
                                                                     </tr>";
                                                            $nwRowHtml = urlencode($nwRowHtml);
                                                            ?>
                                                            <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="insertNewRowBe4('wkfHrchyLvlsTable', 0, '<?php echo $nwRowHtml; ?>');">
                                                                <img src="cmn_images/add1-64.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                                                Add Workflow Hierarchy Level
                                                            </button>
                                                        <?php } ?>
                                                        <table class="table table-striped table-bordered table-responsive" id="wkfHrchyLvlsTable" cellspacing="0" width="100%" style="width:100%;min-width: 600px;">
                                                            <thead>
                                                                <tr>
                                                                    <th>No.</th>
                                                                    <th>Approver Type</th>
                                                                    <th style="min-width: 150px;">Approver Name</th>
                                                                    <th>Hierarchy Level</th>
                                                                    <th>Enabled?</th> 
                                                                    <?php
                                                                    if ($canDelWkfHrchy === true) {
                                                                        ?>
                                                                        <th>&nbsp;</th>
                                                                    <?php } ?>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $result2 = get_HrchyCntntMnl($pkID);
                                                                $cntr = 0;
                                                                while ($row2 = loc_db_fetch_array($result2)) {
                                                                    $cntr += 1;
                                                                    ?>
                                                                    <tr id="wkfHrchyLvlsRow_<?php echo $cntr; ?>" class="hand_cursor">                                    
                                                                        <td class="lovtd"><span class=""><?php echo ($curIdx * $lmtSze) + ($cntr); ?></span></td>
                                                                        <td class="lovtd">
                                                                            <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                                                                <?php if ($canEdtWkfHrchy === true) { ?>
                                                                                    <select data-placeholder="Select..." class="form-control chosen-select" id="wkfHrchyLvlsRow<?php echo $cntr; ?>_AprvrTyp">
                                                                                        <?php
                                                                                        $valslctdArry = array("", "", "");
                                                                                        $srchInsArrys = array("Group", "Position Holder", "Individual");
                                                                                        for ($z = 0; $z < count($srchInsArrys); $z++) {
                                                                                            if ($row2[1] == $srchInsArrys[$z]) {
                                                                                                $valslctdArry[$z] = "selected";
                                                                                            }
                                                                                            ?>
                                                                                            <option value="<?php echo $srchInsArrys[$z]; ?>" <?php echo $valslctdArry[$z]; ?>><?php echo $srchInsArrys[$z]; ?></option>
                                                                                        <?php } ?>
                                                                                    </select>
                                                                                <?php } else {
                                                                                    ?>
                                                                                    <span><?php echo $row2[1]; ?></span>
                                                                                    <?php
                                                                                }
                                                                                ?>
                                                                            </div>
                                                                        </td>
                                                                        <td class="lovtd">
                                                                            <?php if ($canEdtWkfHrchy === true) { ?>
                                                                                <div class="input-group" style="width:100% !important;">
                                                                                    <input type="text" class="form-control" aria-label="..." id="wkfHrchyLvlsRow<?php echo $cntr; ?>_ApprvrNm" value="<?php echo $row2[2]; ?>" style="width:100% !important;">
                                                                                    <input type="hidden" class="form-control" aria-label="..." id="wkfHrchyLvlsRow<?php echo $cntr; ?>_ApprvrID" value="<?php echo (($row2[1] == "Individual") ? getPersonLocID($row2[3]) : $row2[3]); ?>">
                                                                                    <input type="hidden" class="form-control" aria-label="..." id="wkfHrchyLvlsRow<?php echo $cntr; ?>_MnlHrchyID" value="<?php echo $row2[0]; ?>">
                                                                                    <label class="btn btn-primary btn-file input-group-addon" onclick="getHrchyLovsPage('myLovModal', 'myLovModalTitle', 'myLovModalBody', '', '', '', '', 'radio', true, '<?php echo $row2[3]; ?>', 'wkfHrchyLvlsRow<?php echo $cntr; ?>_ApprvrID', 'wkfHrchyLvlsRow<?php echo $cntr; ?>_ApprvrNm', 'clear', 0, '', 'wkfHrchyLvlsRow<?php echo $cntr; ?>_AprvrTyp');">
                                                                                        <span class="glyphicon glyphicon-th-list"></span>
                                                                                    </label>
                                                                                </div>
                                                                            <?php } else { ?>
                                                                                <span class=""><?php echo $row2[2]; ?></span>
                                                                            <?php } ?>                                                         
                                                                        </td>
                                                                        <td class="lovtd">
                                                                            <?php if ($canEdtWkfHrchy === true) { ?>
                                                                                <div class="form-group form-group-sm" style="width:100% !important;">
                                                                                    <input type="number" min="1" max="9999" class="form-control" aria-label="..." id="wkfHrchyLvlsRow<?php echo $cntr; ?>_Level" name="wkfHrchyLvlsRow<?php echo $cntr; ?>_Level" value="<?php echo $row2[4]; ?>" style="width:100%;">
                                                                                </div>
                                                                            <?php } else { ?>
                                                                                <span class=""><?php echo $row2[4]; ?></span>
                                                                            <?php } ?>                                                         
                                                                        </td>
                                                                        <td class="lovtd">
                                                                            <?php
                                                                            $isChkd = "";
                                                                            if ($row2[5] == "1") {
                                                                                $isChkd = "checked=\"true\"";
                                                                            }
                                                                            if ($canEdtWkfHrchy === true) {
                                                                                ?>
                                                                                <div class="form-group form-group-sm ">
                                                                                    <div class="form-check" style="font-size: 12px !important;">
                                                                                        <label class="form-check-label">
                                                                                            <input type="checkbox" class="form-check-input" id="wkfHrchyLvlsRow<?php echo $cntr; ?>_Enabled" name="wkfHrchyLvlsRow<?php echo $cntr; ?>_Enabled" <?php echo $isChkd ?>>
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            <?php } else { ?>
                                                                                <span class=""><?php echo $row2[5]; ?></span>
                                                                            <?php } ?>                                                         
                                                                        </td>
                                                                        <?php
                                                                        if ($canDelWkfHrchy === true) {
                                                                            ?>
                                                                            <td class="lovtd">
                                                                                <button type="button" class="btn btn-default" style="margin: 0px !important;padding:0px 3px 2px 4px !important;" onclick="delWkfHrchyLvl('wkfHrchyLvlsRow_<?php echo $cntr; ?>');" data-toggle="tooltip" data-placement="bottom" title="Delete Hierarchy Level">
                                                                                    <img src="cmn_images/delete.png" style="height:15px; width:auto; position: relative; vertical-align: middle;">
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
                                            </div>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <span>No Results Found</span>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </form>
                <?php
            } else if ($vwtyp == 1) {
                //lov detail on tr click
                $curIdx = 0;
                $pkID = isset($_POST['sbmtdHrchyID']) ? $_POST['sbmtdHrchyID'] : -1;
                if ($pkID > 0) {
                    $result1 = get_WkfHrchyDet($pkID);
                    $sbmtdHrchyID = $pkID;
                    while ($row1 = loc_db_fetch_array($result1)) {
                        ?>
                        <div class="row">
                            <div  class="col-md-6" style="padding:0px 3px 0px 3px !important;">
                                <fieldset class="basic_person_fs" style="padding:10px 3px 0px 3px !important;"> 
                                    <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                        <label for="wkfHrchyNm" class="control-label col-lg-4">Hierarchy Name:</label>
                                        <div  class="col-lg-8">
                                            <?php if ($canEdtWkfHrchy === true) { ?>
                                                <input type="text" class="form-control" aria-label="..." id="wkfHrchyNm" name="wkfHrchyNm" value="<?php echo $row1[1]; ?>" style="width:100%;">
                                                <input type="hidden" class="form-control" aria-label="..." id="wkfHrchyID" name="wkfHrchyID" value="<?php echo $row1[0]; ?>">
                                            <?php } else {
                                                ?>
                                                <span><?php echo $row1[1]; ?></span>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                        <label for="wkfHrchyDesc" class="control-label col-lg-4">Description:</label>
                                        <div  class="col-lg-8">
                                            <?php if ($canEdtWkfHrchy === true) { ?>
                                                <textarea class="form-control" aria-label="..." id="wkfHrchyDesc" name="wkfHrchyDesc" style="width:100%;" cols="5" rows="3"><?php echo $row1[2]; ?></textarea>
                                            <?php } else {
                                                ?>
                                                <span><?php echo $row1[2]; ?></span>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                        <label for="wkfHrchyIsEnbld" class="control-label col-lg-6">Enabled?:</label>
                                        <div class="col-lg-6">
                                            <?php
                                            $chkdYes = "";
                                            $chkdNo = "checked=\"\"";
                                            if ($row1[3] == "Yes") {
                                                $chkdNo = "";
                                                $chkdYes = "checked=\"\"";
                                            }
                                            ?>
                                            <?php if ($canEdtWkfHrchy === true) { ?>
                                                <label class="radio-inline"><input type="radio" name="wkfHrchyIsEnbld" value="YES" <?php echo $chkdYes; ?>>YES</label>
                                                <label class="radio-inline"><input type="radio" name="wkfHrchyIsEnbld" value="NO" <?php echo $chkdNo; ?>>NO</label>
                                            <?php } else {
                                                ?>
                                                <span><?php echo ($row1[3] == "Yes" ? "YES" : "NO"); ?></span>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            <div  class="col-md-6" style="padding:0px 3px 0px 3px !important;">
                                <fieldset class = "basic_person_fs">
                                    <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                        <label for="wkfHrchyType" class="control-label col-lg-4">Hierarchy Type:</label>
                                        <div class="col-lg-8">
                                            <?php if ($canEdtWkfHrchy === true) { ?>
                                                <select data-placeholder="Select..." class="form-control chosen-select" id="wkfHrchyType">
                                                    <?php
                                                    $valslctdArry = array("", "");
                                                    $srchInsArrys = array("Manual", "SQL");
                                                    for ($z = 0; $z < count($srchInsArrys); $z++) {
                                                        if ($row1[4] == $srchInsArrys[$z]) {
                                                            $valslctdArry[$z] = "selected";
                                                        }
                                                        ?>
                                                        <option value="<?php echo $srchInsArrys[$z]; ?>" <?php echo $valslctdArry[$z]; ?>><?php echo $srchInsArrys[$z]; ?></option>
                                                    <?php } ?>
                                                </select>
                                            <?php } else {
                                                ?>
                                                <span><?php echo $row1[4]; ?></span>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                        <label for="wkfHrchySQL" class="control-label col-lg-2">SQL Query:</label>
                                        <div  class="col-lg-10">
                                            <?php if ($canEdtWkfHrchy === true) { ?>
                                                <textarea class="form-control" aria-label="..." id="wkfHrchySQL" name="wkfHrchySQL" style="width:100%;" cols="5" rows="4"><?php echo $row1[5]; ?></textarea>
                                            <?php } else {
                                                ?>
                                                <span><?php echo $row1[5]; ?></span>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>                                            
                        <div class="row">
                            <div class="col-md-12" style="padding:0px 3px 0px 3px !important;">
                                <fieldset class="basic_person_fs"> 
                                    <?php
                                    if ($canEdtWkfHrchy === true) {
                                        $nwRowHtml = "<tr id=\"wkfHrchyLvlsRow__WWW123WWW\">"
                                                . "<td class=\"lovtd\"><span class=\"\">New</span></td>"
                                                . "<td class=\"lovtd\">
                                                                            <div class=\"form-group form-group-sm col-md-12\" style=\"padding:0px 3px 0px 3px !important;\">
                                                                                <select data-placeholder=\"Select...\" class=\"form-control chosen-select\" id=\"wkfHrchyLvlsRow_WWW123WWW_AprvrTyp\">";
                                        $valslctdArry = array("", "", "");
                                        $srchInsArrys = array("Group", "Position Holder", "Individual");
                                        for ($z = 0; $z < count($srchInsArrys); $z++) {
                                            $nwRowHtml .= "<option value=\"$srchInsArrys[$z]\" $valslctdArry[$z]>$srchInsArrys[$z]</option>";
                                        }
                                        $nwRowHtml .= "</select>
                                                                            </div>
                                                                        </td>
                                                                        <td class=\"lovtd\">  
                                                                            <div class=\"input-group\" style=\"width:100% !important;\">
                                                                                    <input type=\"text\" class=\"form-control\" aria-label=\"...\" id=\"wkfHrchyLvlsRow_WWW123WWW_ApprvrNm\" value=\"\" style=\"width:100% !important;\" readonly=\"true\">
                                                                                    <input type=\"hidden\" class=\"form-control\" aria-label=\"...\" id=\"wkfHrchyLvlsRow_WWW123WWW_ApprvrID\" value=\"-1\">
                                                                                    <input type=\"hidden\" class=\"form-control\" aria-label=\"...\" id=\"wkfHrchyLvlsRow_WWW123WWW_MnlHrchyID\" value=\"-1\">
                                                                                    <label class=\"btn btn-primary btn-file input-group-addon\" onclick=\"getHrchyLovsPage('myLovModal', 'myLovModalTitle', 'myLovModalBody', '', '', '', '', 'radio', true, '', 'wkfHrchyLvlsRow_WWW123WWW_ApprvrID', 'wkfHrchyLvlsRow_WWW123WWW_ApprvrNm', 'clear', 0, '','wkfHrchyLvlsRow_WWW123WWW_AprvrTyp');\">
                                                                                        <span class=\"glyphicon glyphicon-th-list\"></span>
                                                                                    </label>
                                                                            </div>
                                                                        </td>
                                                                        <td class=\"lovtd\">
                                                                            <div class=\"form-group form-group-sm\" style=\"width:100% !important;\">
                                                                                    <input type=\"number\" min=\"0\" max=\"9999\" class=\"form-control\" aria-label=\"...\" id=\"wkfHrchyLvlsRow_WWW123WWW_Level\" name=\"wkfHrchyLvlsRow_WWW123WWW_Level\" value=\"\" style=\"width:100%;\">
                                                                            </div>                                                    
                                                                        </td>
                                                                       <td class=\"lovtd\"> 
                                                                           <div class=\"form-group form-group-sm \">
                                                                                         <div class=\"form-check\" style=\"font-size: 12px !important;\">
                                                                                             <label class=\"form-check-label\">
                                                                                                 <input type=\"checkbox\" class=\"form-check-input\" id=\"wkfHrchyLvlsRow_WWW123WWW_Enabled\" name=\"wkfHrchyLvlsRow_WWW123WWW_Enabled\">
                                                                                             </label>
                                                                                         </div>
                                                                           </div>
                                                                        </td>                                                                        
                                                                        <td class=\"lovtd\">
                                                                            <button type=\"button\" class=\"btn btn-default\" style=\"margin: 0px !important;padding:0px 3px 2px 4px !important;\" onclick=\"delWkfHrchyLvl('wkfHrchyLvlsRow__WWW123WWW');\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Delete Hierarchy Level\">
                                                                                <img src=\"cmn_images/delete.png\" style=\"height:15px; width:auto; position: relative; vertical-align: middle;\">
                                                                            </button>
                                                                        </td>
                                                                     </tr>";
                                        $nwRowHtml = urlencode($nwRowHtml);
                                        ?>
                                        <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="insertNewRowBe4('wkfHrchyLvlsTable', 0, '<?php echo $nwRowHtml; ?>');">
                                            <img src="cmn_images/add1-64.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                            Add Workflow Hierarchy Level
                                        </button>
                                    <?php } ?>
                                    <table class="table table-striped table-bordered table-responsive" id="wkfHrchyLvlsTable" cellspacing="0" width="100%" style="width:100%;min-width: 600px;">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Approver Type</th>
                                                <th style="min-width: 150px;">Approver Name</th>
                                                <th>Hierarchy Level</th>
                                                <th>Enabled?</th> 
                                                <?php
                                                if ($canDelWkfHrchy === true) {
                                                    ?>
                                                    <th>&nbsp;</th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $result2 = get_HrchyCntntMnl($pkID);
                                            $cntr = 0;
                                            while ($row2 = loc_db_fetch_array($result2)) {
                                                $cntr += 1;
                                                ?>
                                                <tr id="wkfHrchyLvlsRow_<?php echo $cntr; ?>" class="hand_cursor">                                    
                                                    <td class="lovtd"><span class=""><?php echo ($curIdx * $lmtSze) + ($cntr); ?></span></td>
                                                    <td class="lovtd">
                                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                                            <?php if ($canEdtWkfHrchy === true) { ?>
                                                                <select data-placeholder="Select..." class="form-control chosen-select" id="wkfHrchyLvlsRow<?php echo $cntr; ?>_AprvrTyp">
                                                                    <?php
                                                                    $valslctdArry = array("", "", "");
                                                                    $srchInsArrys = array("Group", "Position Holder", "Individual");
                                                                    for ($z = 0; $z < count($srchInsArrys); $z++) {
                                                                        if ($row2[1] == $srchInsArrys[$z]) {
                                                                            $valslctdArry[$z] = "selected";
                                                                        }
                                                                        ?>
                                                                        <option value="<?php echo $srchInsArrys[$z]; ?>" <?php echo $valslctdArry[$z]; ?>><?php echo $srchInsArrys[$z]; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            <?php } else {
                                                                ?>
                                                                <span><?php echo $row2[1]; ?></span>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                    </td>
                                                    <td class="lovtd">
                                                        <?php if ($canEdtWkfHrchy === true) { ?>
                                                            <div class="input-group" style="width:100% !important;">
                                                                <input type="text" class="form-control" aria-label="..." id="wkfHrchyLvlsRow<?php echo $cntr; ?>_ApprvrNm" value="<?php echo $row2[2]; ?>" style="width:100% !important;" readonly="true">
                                                                <input type="hidden" class="form-control" aria-label="..." id="wkfHrchyLvlsRow<?php echo $cntr; ?>_ApprvrID" value="<?php echo (($row2[1] == "Individual") ? getPersonLocID($row2[3]) : $row2[3]); ?>">
                                                                <input type="hidden" class="form-control" aria-label="..." id="wkfHrchyLvlsRow<?php echo $cntr; ?>_MnlHrchyID" value="<?php echo $row2[0]; ?>">
                                                                <label class="btn btn-primary btn-file input-group-addon" onclick="getHrchyLovsPage('myLovModal', 'myLovModalTitle', 'myLovModalBody', '', '', '', '', 'radio', true, '<?php echo $row2[3]; ?>', 'wkfHrchyLvlsRow<?php echo $cntr; ?>_ApprvrID', 'wkfHrchyLvlsRow<?php echo $cntr; ?>_ApprvrNm', 'clear', 0, '', 'wkfHrchyLvlsRow<?php echo $cntr; ?>_AprvrTyp');">
                                                                    <span class="glyphicon glyphicon-th-list"></span>
                                                                </label>
                                                            </div>
                                                        <?php } else { ?>
                                                            <span class=""><?php echo $row2[2]; ?></span>
                                                        <?php } ?>                                                         
                                                    </td>
                                                    <td class="lovtd">
                                                        <?php if ($canEdtWkfHrchy === true) { ?>
                                                            <div class="form-group form-group-sm" style="width:100% !important;">
                                                                <input type="number" min="1" max="9999" class="form-control" aria-label="..." id="wkfHrchyLvlsRow<?php echo $cntr; ?>_Level" name="wkfHrchyLvlsRow<?php echo $cntr; ?>_Level" value="<?php echo $row2[4]; ?>" style="width:100%;">
                                                            </div>
                                                        <?php } else { ?>
                                                            <span class=""><?php echo $row2[4]; ?></span>
                                                        <?php } ?>                                                         
                                                    </td>
                                                    <td class="lovtd">
                                                        <?php
                                                        $isChkd = "";
                                                        if ($row2[5] == "1") {
                                                            $isChkd = "checked=\"true\"";
                                                        }
                                                        if ($canEdtWkfHrchy === true) {
                                                            ?>
                                                            <div class="form-group form-group-sm ">
                                                                <div class="form-check" style="font-size: 12px !important;">
                                                                    <label class="form-check-label">
                                                                        <input type="checkbox" class="form-check-input" id="wkfHrchyLvlsRow<?php echo $cntr; ?>_Enabled" name="wkfHrchyLvlsRow<?php echo $cntr; ?>_Enabled" <?php echo $isChkd ?>>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        <?php } else { ?>
                                                            <span class=""><?php echo $row2[5]; ?></span>
                                                        <?php } ?>                                                         
                                                    </td>
                                                    <?php
                                                    if ($canDelWkfHrchy === true) {
                                                        ?>
                                                        <td class="lovtd">
                                                            <button type="button" class="btn btn-default" style="margin: 0px !important;padding:0px 3px 2px 4px !important;" onclick="delWkfHrchyLvl('wkfHrchyLvlsRow_<?php echo $cntr; ?>');" data-toggle="tooltip" data-placement="bottom" title="Delete Hierarchy Level">
                                                                <img src="cmn_images/delete.png" style="height:15px; width:auto; position: relative; vertical-align: middle;">
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
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <span>No Results Found</span>
                    <?php
                }
            } else if ($vwtyp == 2) {
                //New Org Form
                $curIdx = 0;
                $pkID = -1;
                if ($canAddWkfHrchy === true) {
                    
                } else {
                    exit();
                }
                ?>                
                <div class="row">
                    <div  class="col-md-6" style="padding:0px 3px 0px 3px !important;">
                        <fieldset class="basic_person_fs" style="padding:10px 3px 0px 3px !important;"> 
                            <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                <label for="wkfHrchyNm" class="control-label col-lg-4">Hierarchy Name:</label>
                                <div  class="col-lg-8">
                                    <input type="text" class="form-control" aria-label="..." id="wkfHrchyNm" name="wkfHrchyNm" value="" style="width:100%;">
                                    <input type="hidden" class="form-control" aria-label="..." id="wkfHrchyID" name="wkfHrchyID" value="">
                                    <input type="hidden" class="form-control" aria-label="..." id="isNew123" name="isNew123" value="5">
                                </div>
                            </div>
                            <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                <label for="wkfHrchyDesc" class="control-label col-lg-4">Description:</label>
                                <div  class="col-lg-8">
                                    <textarea class="form-control" aria-label="..." id="wkfHrchyDesc" name="wkfHrchyDesc" style="width:100%;" cols="5" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                <label for="wkfHrchyIsEnbld" class="control-label col-lg-6">Enabled?:</label>
                                <div class="col-lg-6">
                                    <?php
                                    $chkdYes = "";
                                    $chkdNo = "checked=\"\"";
                                    ?>                                   
                                    <label class="radio-inline"><input type="radio" name="wkfHrchyIsEnbld" value="YES" <?php echo $chkdYes; ?>>YES</label>
                                    <label class="radio-inline"><input type="radio" name="wkfHrchyIsEnbld" value="NO" <?php echo $chkdNo; ?>>NO</label>  
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div  class="col-md-6" style="padding:0px 3px 0px 3px !important;">
                        <fieldset class = "basic_person_fs">
                            <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                <label for="wkfHrchyType" class="control-label col-lg-4">Hierarchy Type:</label>
                                <div class="col-lg-8">
                                    <select data-placeholder="Select..." class="form-control chosen-select" id="wkfHrchyType">
                                        <?php
                                        $valslctdArry = array("", "");
                                        $srchInsArrys = array("Manual", "SQL");
                                        for ($z = 0; $z < count($srchInsArrys); $z++) {
                                            ?>
                                            <option value="<?php echo $srchInsArrys[$z]; ?>" <?php echo $valslctdArry[$z]; ?>><?php echo $srchInsArrys[$z]; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                <label for="wkfHrchySQL" class="control-label col-lg-2">SQL Query:</label>
                                <div  class="col-lg-10">
                                    <textarea class="form-control" aria-label="..." id="wkfHrchySQL" name="wkfHrchySQL" style="width:100%;" cols="5" rows="4"></textarea>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>                                            
                <div class="row">
                    <div class="col-md-12" style="padding:0px 3px 0px 3px !important;">
                        <fieldset class="basic_person_fs"> 
                            <?php
                            if ($canEdtWkfHrchy === true) {
                                $nwRowHtml = "<tr id=\"wkfHrchyLvlsRow__WWW123WWW\">"
                                        . "<td class=\"lovtd\"><span class=\"\">New</span></td>"
                                        . "<td class=\"lovtd\">
                                                                            <div class=\"form-group form-group-sm col-md-12\" style=\"padding:0px 3px 0px 3px !important;\">
                                                                                <select data-placeholder=\"Select...\" class=\"form-control chosen-select\" id=\"wkfHrchyLvlsRow_WWW123WWW_AprvrTyp\">";
                                $valslctdArry = array("", "", "");
                                $srchInsArrys = array("Group", "Position Holder", "Individual");
                                for ($z = 0; $z < count($srchInsArrys); $z++) {
                                    $nwRowHtml .= "<option value=\"$srchInsArrys[$z]\" $valslctdArry[$z]>$srchInsArrys[$z]</option>";
                                }
                                $nwRowHtml .= "</select>
                                                                            </div>
                                                                        </td>
                                                                        <td class=\"lovtd\">  
                                                                            <div class=\"input-group\" style=\"width:100% !important;\">
                                                                                    <input type=\"text\" class=\"form-control\" aria-label=\"...\" id=\"wkfHrchyLvlsRow_WWW123WWW_ApprvrNm\" value=\"\" style=\"width:100% !important;\">
                                                                                    <input type=\"hidden\" class=\"form-control\" aria-label=\"...\" id=\"wkfHrchyLvlsRow_WWW123WWW_ApprvrID\" value=\"-1\">
                                                                                    <input type=\"hidden\" class=\"form-control\" aria-label=\"...\" id=\"wkfHrchyLvlsRow_WWW123WWW_MnlHrchyID\" value=\"-1\">
                                                                                    <label class=\"btn btn-primary btn-file input-group-addon\" onclick=\"getHrchyLovsPage('myLovModal', 'myLovModalTitle', 'myLovModalBody', '', '', '', '', 'radio', true, '', 'wkfHrchyLvlsRow_WWW123WWW_ApprvrID', 'wkfHrchyLvlsRow_WWW123WWW_ApprvrNm', 'clear', 0, '','wkfHrchyLvlsRow_WWW123WWW_AprvrTyp');\">
                                                                                        <span class=\"glyphicon glyphicon-th-list\"></span>
                                                                                    </label>
                                                                            </div>
                                                                        </td>
                                                                        <td class=\"lovtd\">
                                                                            <div class=\"form-group form-group-sm\" style=\"width:100% !important;\">
                                                                                    <input type=\"number\" min=\"0\" max=\"9999\" class=\"form-control\" aria-label=\"...\" id=\"wkfHrchyLvlsRow_WWW123WWW_Level\" name=\"wkfHrchyLvlsRow_WWW123WWW_Level\" value=\"\" style=\"width:100%;\">
                                                                            </div>                                                    
                                                                        </td>
                                                                       <td class=\"lovtd\"> 
                                                                           <div class=\"form-group form-group-sm \">
                                                                                         <div class=\"form-check\" style=\"font-size: 12px !important;\">
                                                                                             <label class=\"form-check-label\">
                                                                                                 <input type=\"checkbox\" class=\"form-check-input\" id=\"wkfHrchyLvlsRow_WWW123WWW_Enabled\" name=\"wkfHrchyLvlsRow_WWW123WWW_Enabled\">
                                                                                             </label>
                                                                                         </div>
                                                                           </div>
                                                                        </td>
                                                                        <td class=\"lovtd\">
                                                                            <button type=\"button\" class=\"btn btn-default\" style=\"margin: 0px !important;padding:0px 3px 2px 4px !important;\" onclick=\"delWkfHrchyLvl('wkfHrchyLvlsRow__WWW123WWW');\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Delete Hierarchy Level\">
                                                                                <img src=\"cmn_images/delete.png\" style=\"height:15px; width:auto; position: relative; vertical-align: middle;\">
                                                                            </button>
                                                                        </td>
                                                                     </tr>";
                                $nwRowHtml = urlencode($nwRowHtml);
                                ?>
                                <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="insertNewRowBe4('wkfHrchyLvlsTable', 0, '<?php echo $nwRowHtml; ?>');">
                                    <img src="cmn_images/add1-64.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                    Add Workflow Hierarchy Level
                                </button>
                            <?php } ?>
                            <table class="table table-striped table-bordered table-responsive" id="wkfHrchyLvlsTable" cellspacing="0" width="100%" style="width:100%;min-width: 600px;">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Approver Type</th>
                                        <th style="min-width: 150px;">Approver Name</th>
                                        <th>Hierarchy Level</th>
                                        <th>Enabled?</th> 
                                        <?php
                                        if ($canDelWkfHrchy === true) {
                                            ?>
                                            <th>&nbsp;</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cntr = 0;
                                    $cntr += 1;
                                    ?>
                                    <tr id="wkfHrchyLvlsRow_<?php echo $cntr; ?>" class="hand_cursor">                                    
                                        <td class="lovtd"><span class=""><?php echo ($curIdx * $lmtSze) + ($cntr); ?></span></td>
                                        <td class="lovtd">
                                            <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">

                                                <select data-placeholder="Select..." class="form-control chosen-select" id="wkfHrchyLvlsRow<?php echo $cntr; ?>_AprvrTyp">
                                                    <?php
                                                    $valslctdArry = array("", "", "");
                                                    $srchInsArrys = array("Group", "Position Holder", "Individual");
                                                    for ($z = 0; $z < count($srchInsArrys); $z++) {
                                                        ?>
                                                        <option value="<?php echo $srchInsArrys[$z]; ?>" <?php echo $valslctdArry[$z]; ?>><?php echo $srchInsArrys[$z]; ?></option>
                                                    <?php } ?>
                                                </select>

                                            </div>
                                        </td>
                                        <td class="lovtd">
                                            <div class="input-group" style="width:100% !important;">
                                                <input type="text" class="form-control" aria-label="..." id="wkfHrchyLvlsRow<?php echo $cntr; ?>_ApprvrNm" value="" style="width:100% !important;">
                                                <input type="hidden" class="form-control" aria-label="..." id="wkfHrchyLvlsRow<?php echo $cntr; ?>_ApprvrID" value="-1">
                                                <input type="hidden" class="form-control" aria-label="..." id="wkfHrchyLvlsRow<?php echo $cntr; ?>_MnlHrchyID" value="-1">
                                                <label class="btn btn-primary btn-file input-group-addon" onclick="getHrchyLovsPage('myLovModal', 'myLovModalTitle', 'myLovModalBody', '', '', '', '', 'radio', true, '', 'wkfHrchyLvlsRow<?php echo $cntr; ?>_ApprvrID', 'wkfHrchyLvlsRow<?php echo $cntr; ?>_ApprvrNm', 'clear', 0, '', 'wkfHrchyLvlsRow<?php echo $cntr; ?>_AprvrTyp');">
                                                    <span class="glyphicon glyphicon-th-list"></span>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="lovtd">
                                            <div class="form-group form-group-sm" style="width:100% !important;">
                                                <input type="number" min="0" max="9999" class="form-control" aria-label="..." id="wkfHrchyLvlsRow<?php echo $cntr; ?>_Level" name="wkfHrchyLvlsRow<?php echo $cntr; ?>_Level" value="" style="width:100%;">
                                            </div>                                                        
                                        </td>
                                        <td class="lovtd">
                                            <?php
                                            $isChkd = "";
                                            ?>                                                
                                            <div class="form-group form-group-sm ">
                                                <div class="form-check" style="font-size: 12px !important;">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" class="form-check-input" id="wkfHrchyLvlsRow<?php echo $cntr; ?>_Enabled" name="wkfHrchyLvlsRow<?php echo $cntr; ?>_Enabled" <?php echo $isChkd ?>>
                                                    </label>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="lovtd">
                                            <button type="button" class="btn btn-default" style="margin: 0px !important;padding:0px 3px 2px 4px !important;" onclick="delWkfHrchyLvl('wkfHrchyLvlsRow_<?php echo $cntr; ?>');" data-toggle="tooltip" data-placement="bottom" title="Delete Hierarchy Level">
                                                <img src="cmn_images/delete.png" style="height:15px; width:auto; position: relative; vertical-align: middle;">
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </fieldset>
                    </div>
                </div>
                <?php
            } else if ($vwtyp == 3) {
                
            } else if ($vwtyp == 4) {
                
            }
        }
    }
}
    