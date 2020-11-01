<?php
$canAddWkfAp = test_prmssns($dfltPrvldgs[7], $mdlNm);
$canEdtWkfAp = test_prmssns($dfltPrvldgs[8], $mdlNm);
$canDelWkfAp = test_prmssns($dfltPrvldgs[9], $mdlNm);

$pageNo = isset($_POST['pageNo']) ? cleanInputData($_POST['pageNo']) : 1;
$lmtSze = isset($_POST['limitSze']) ? cleanInputData($_POST['limitSze']) : 10;
$sortBy = isset($_POST['sortBy']) ? cleanInputData($_POST['sortBy']) : "";

if (array_key_exists('lgn_num', get_defined_vars())) {
    if ($lgn_num > 0 && $canview === true) {
        if ($qstr == 'DELETE') {
            if ($actyp == 1) {
                $pKeyID = isset($_POST['pKeyID']) ? cleanInputData($_POST['pKeyID']) : -1;
                $appNm = isset($_POST['appNm']) ? cleanInputData($_POST['appNm']) : "";
                if ($pKeyID > 0) {
                    echo deleteWkfApp($pKeyID, $appNm);
                }
            } else if ($actyp == 2) {
                $pKeyID = isset($_POST['pKeyID']) ? cleanInputData($_POST['pKeyID']) : -1;
                $actnNm = isset($_POST['actnNm']) ? cleanInputData($_POST['actnNm']) : "";
                if ($pKeyID > 0) {
                    echo deleteWkfAppAction($pKeyID, $actnNm);
                }
            }
        } else if ($qstr == "UPDATE") {
            if ($actyp == 1) {
                //Save Workflow App
                header("content-type:application/json");
                $orgid = $_SESSION['ORG_ID'];
                $wkfDetAppID = isset($_POST['wkfDetAppID']) ? (int) cleanInputData($_POST['wkfDetAppID']) : -1;
                $wkfDetAppNm = isset($_POST['wkfDetAppNm']) ? cleanInputData($_POST['wkfDetAppNm']) : "";
                $wkfDetSrcMdl = isset($_POST['wkfDetSrcMdl']) ? cleanInputData($_POST['wkfDetSrcMdl']) : "";
                $wkfDetSrcMdlID = isset($_POST['wkfDetSrcMdlID']) ? cleanInputData($_POST['wkfDetSrcMdlID']) : -1;
                $wkfDetAppDesc = isset($_POST['wkfDetAppDesc']) ? cleanInputData($_POST['wkfDetAppDesc']) : '';
                $slctdAppActns = isset($_POST['slctdAppActns']) ? cleanInputData($_POST['slctdAppActns']) : '';
                $oldAppID = getGnrlRecID2("wkf.wkf_apps", "app_name", "app_id", $wkfDetAppNm);
                if ($wkfDetAppNm != "" && $wkfDetSrcMdl != "" && ($oldAppID <= 0 || $oldAppID == $wkfDetAppID)) {
                    if ($wkfDetAppID <= 0) {
                        createWkfApp($wkfDetAppNm, $wkfDetSrcMdl, $wkfDetAppDesc);
                        $wkfDetAppID = getGnrlRecID2("wkf.wkf_apps", "app_name", "app_id", $wkfDetAppNm);
                    } else {
                        updateWkfApp($wkfDetAppID, $wkfDetAppNm, $wkfDetSrcMdl, $wkfDetAppDesc);
                    }
                    $affctdAppActns = 0;
                    if ($wkfDetAppID > 0) {
                        if (trim($slctdAppActns, "|~") != "") {
                            //Save Report Parameters
                            $variousRows = explode("|", trim($slctdAppActns, "|"));
                            for ($z = 0; $z < count($variousRows); $z++) {
                                $crntRow = explode("~", $variousRows[$z]);
                                if (count($crntRow) == 7) {
                                    $inptActionID = (int) (cleanInputData1($crntRow[0]));
                                    $actionNm = cleanInputData1($crntRow[1]);
                                    $actionDesc = cleanInputData1($crntRow[2]);
                                    $actionSQL = cleanInputData1($crntRow[3]);
                                    $actionUrl = cleanInputData1($crntRow[4]);
                                    $actionDsplyTyp = cleanInputData1($crntRow[5]);
                                    $isdiag = ($actionDsplyTyp == "Dialog") ? '1' : '0';
                                    $admnOnly = cleanInputData1($crntRow[6]);
                                    $isAdmnOnly = cnvrtBoolToBitStr(($admnOnly == "YES") ? true : false);

                                    $oldAppActionID = getGnrlRecIDExtr("wkf.wkf_apps_actions", "action_performed_nm", "app_id", "action_sql_id", $actionNm, $wkfDetAppID);

                                    if ($actionNm != "") {
                                        if ($oldAppActionID <= 0) {
                                            $affctdAppActns += createWkfAppAction($actionNm, $actionSQL, $wkfDetAppID, "", $actionUrl, $isdiag, $actionDesc, $isAdmnOnly);
                                        } else {
                                            $affctdAppActns += updateWkfAppAction($oldAppActionID, $actionNm, $actionSQL, $wkfDetAppID, "", $actionUrl, $isdiag, $actionDesc, $isAdmnOnly);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $arr_content['percent'] = 100;
                    $arr_content['wkfDetAppID'] = $wkfDetAppID;
                    $arr_content['message'] = "<span style=\"color:green;\"><i class=\"fa fa-check\" aria-hidden=\"true\"></i></span>Workflow App Successfully Saved!<br/>" . $affctdAppActns . " App Actions Saved!";
                    echo json_encode($arr_content);
                    exit();
                } else {
                    $arr_content['percent'] = 100;
                    $arr_content['wkfDetAppID'] = -1;
                    $arr_content['message'] = "<span style=\"color:red;\"><i class=\"fa fa-exclamation-circle\" aria-hidden=\"true\"></i>Either the New Workflow App Exists <br/>or Data Supplied is Incomplete!</span>";
                    echo json_encode($arr_content);
                    exit();
                }
            } else if ($actyp == 2) {
                
            }
        } else {
            if ($vwtyp == 0) {
                $pkID = isset($_POST['sbmtdAppID']) ? $_POST['sbmtdAppID'] : -1;
                echo $cntent . "<li onclick=\"openATab('#allmodules', 'grp=$group&typ=$type');\">
						<span style=\"text-decoration:none;\">Workflow Manager Menu</span>
				</li>
                                <li>
                                    <span class=\"divider\"><i class=\"fa fa-angle-right\" aria-hidden=\"true\"></i></span>
                                    <span style=\"text-decoration:none;\">Workflow Apps</span>
				</li>
                               </ul>
                              </div>";
                $total = get_WkfAppsTtl($srchFor, $srchIn);
                if ($pageNo > ceil($total / $lmtSze)) {
                    $pageNo = 1;
                } else if ($pageNo < 1) {
                    $pageNo = ceil($total / $lmtSze);
                }

                $curIdx = $pageNo - 1;
                $result = get_WkfAppsTblr($srchFor, $srchIn, $curIdx, $lmtSze);
                $cntr = 0;
                $colClassType1 = "col-lg-2";
                $colClassType2 = "col-lg-3";
                $colClassType3 = "col-lg-4";
                ?>
                <form id='allWkfAppsForm' action='' method='post' accept-charset='UTF-8'>
                    <div class="row rhoRowMargin">
                        <?php
                        if ($canAddWkfAp === true) {
                            ?> 
                            <div class="<?php echo $colClassType3; ?>" style="padding:0px 1px 0px 1px !important;"> 
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="getOneWkfAppForm(-1, 2);" style="width:100% !important;">
                                        <img src="cmn_images/add1-64.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                        New Workflow App
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="saveWkfAppForm();" style="width:100% !important;">
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
                                <input class="form-control" id="allWkfAppsSrchFor" type = "text" placeholder="Search For" value="<?php echo trim(str_replace("%", " ", $srchFor)); ?>" onkeyup="enterKeyFuncWkfApps(event, '', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>')">
                                <input id="allWkfAppsPageNo" type = "hidden" value="<?php echo $pageNo; ?>">
                                <label class="btn btn-primary btn-file input-group-addon" onclick="getAllWkfApps('clear', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>')">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </label>
                                <label class="btn btn-primary btn-file input-group-addon" onclick="getAllWkfApps('', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>')">
                                    <span class="glyphicon glyphicon-search"></span>
                                </label> 
                            </div>
                        </div>
                        <div class="<?php echo $colClassType2; ?>">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-filter"></span></span>
                                <select data-placeholder="Select..." class="form-control chosen-select" id="allWkfAppsSrchIn">
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
                                <select data-placeholder="Select..." class="form-control chosen-select" id="allWkfAppsDsplySze" style="min-width:70px !important;">                            
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
                                        <a class="rhopagination" href="javascript:getAllWkfApps('previous', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>');" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="rhopagination" href="javascript:getAllWkfApps('next', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=<?php echo $vwtyp; ?>');" aria-label="Next">
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
                                <table class="table table-striped table-bordered table-responsive" id="allWkfAppsTable" cellspacing="0" width="100%" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Workflow App Name</th>
                                            <?php
                                            if ($canDelWkfAp === true) {
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
                                            <tr id="allWkfAppsRow_<?php echo $cntr; ?>" class="hand_cursor">                                    
                                                <td class="lovtd"><?php echo ($curIdx * $lmtSze) + ($cntr); ?></td>
                                                <td class="lovtd"><?php echo $row[1]; ?><input type="hidden" class="form-control" aria-label="..." id="allWkfAppsRow<?php echo $cntr; ?>_AppID" value="<?php echo $row[0]; ?>"></td>                                                
                                                <?php
                                                if ($canDelWkfAp === true) {
                                                    ?>
                                                    <td class="lovtd">
                                                        <button type="button" class="btn btn-default" style="margin: 0px !important;padding:0px 3px 2px 4px !important;" onclick="delWkfApp('allWkfAppsRow_<?php echo $cntr; ?>');" data-toggle="tooltip" data-placement="bottom" title="Delete Workflow App">
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
                                <div class="container-fluid" id="wkfAppsDetailInfo">
                                    <?php
                                    if ($pkID > 0) {
                                        $result1 = get_WkfAppsDet($pkID);
                                        $sbmtdAppID = $pkID;
                                        while ($row1 = loc_db_fetch_array($result1)) {
                                            ?>
                                            <div class="row">
                                                <div  class="col-md-6" style="padding:0px 3px 0px 3px !important;">
                                                    <fieldset class="basic_person_fs" style="padding:10px 3px 0px 3px !important;"> 
                                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                                            <label for="wkfDetAppNm" class="control-label col-lg-4">Application Name:</label>
                                                            <div  class="col-lg-8">
                                                                <?php if ($canEdtWkfAp === true) { ?>
                                                                    <input type="text" class="form-control" aria-label="..." id="wkfDetAppNm" name="wkfDetAppNm" value="<?php echo $row1[1]; ?>" style="width:100%;">
                                                                    <input type="hidden" class="form-control" aria-label="..." id="wkfDetAppID" name="wkfDetAppID" value="<?php echo $row1[0]; ?>">
                                                                <?php } else {
                                                                    ?>
                                                                    <span><?php echo $row1[1]; ?></span>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div  class="col-md-6" style="padding:0px 3px 0px 3px !important;">
                                                    <fieldset class = "basic_person_fs">
                                                        <div class="form-group form-group-sm">
                                                            <label for="wkfDetSrcMdl" class="control-label col-md-4">Source Module:</label>
                                                            <div  class="col-md-8">
                                                                <?php if ($canEdtWkfAp === true) { ?>
                                                                    <div class="input-group">
                                                                        <input type="text" class="form-control" aria-label="..." id="wkfDetSrcMdl" value="<?php echo $row1[2]; ?>">
                                                                        <input type="hidden" id="wkfDetSrcMdlID" value="-1">
                                                                        <label class="btn btn-primary btn-file input-group-addon" onclick="getLovsPage('myLovModal', 'myLovModalTitle', 'myLovModalBody', 'System Modules', '', '', '', 'radio', true, '<?php echo $row1[2]; ?>', 'wkfDetSrcMdlID', 'wkfDetSrcMdl', 'clear', 1, '');">
                                                                            <span class="glyphicon glyphicon-th-list"></span>
                                                                        </label>
                                                                    </div>
                                                                <?php } else {
                                                                    ?>
                                                                    <span><?php echo $row1[2]; ?></span>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div  class="col-md-12" style="padding:0px 3px 0px 3px !important;">
                                                    <fieldset class="basic_person_fs" style="padding:10px 3px 0px 3px !important;">                                                         
                                                        <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                                            <label for="wkfDetAppDesc" class="control-label col-lg-2">Description:</label>
                                                            <div  class="col-lg-10">
                                                                <?php if ($canEdtWkfAp === true) { ?>
                                                                    <textarea class="form-control" aria-label="..." id="wkfDetAppDesc" name="wkfDetAppDesc" style="width:100%;" cols="9" rows="2"><?php echo $row1[3]; ?></textarea>
                                                                <?php } else {
                                                                    ?>
                                                                    <span><?php echo $row1[3]; ?></span>
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
                                                        if ($canEdtWkfAp === true) {
                                                            $nwRowHtml = "<tr id=\"wkfAppActnsRow__WWW123WWW\">"
                                                                    . "<td class=\"lovtd\"><span class=\"\">New</span></td>"
                                                                    . "<td class=\"lovtd\">                                            
                                              <div class=\"form-group form-group-sm\" style=\"width:100% !important;\">
                                                            <input type=\"text\" class=\"form-control\" aria-label=\"...\" id=\"wkfAppActnsRow_WWW123WWW_ActnNm\" value=\"\" style=\"width:100% !important;\">
                                                            <input type=\"hidden\" class=\"form-control\" aria-label=\"...\" id=\"wkfAppActnsRow_WWW123WWW_ActnID\" value=\"\">
                                              </div>
                                          </td>
                                           <td class=\"lovtd\">
                                                            <div class=\"form-group form-group-sm\" style=\"width:100% !important;\">
                                                                <textarea class=\"form-control\" aria-label=\"...\" id=\"wkfAppActnsRow_WWW123WWW_ActnDesc\" name=\"wkfAppActnsRow_WWW123WWW_ActnDesc\" style=\"width:100%;\" cols=\"7\" rows=\"2\"></textarea>
                                                            </div>                                                       
                                                    </td>
                                           <td class=\"lovtd\">
                                                            <div class=\"form-group form-group-sm\" style=\"width:100% !important;\">
                                                                <textarea class=\"form-control\" aria-label=\"...\" id=\"wkfAppActnsRow_WWW123WWW_ActnSQL\" name=\"wkfAppActnsRow_WWW123WWW_ActnSQL\" style=\"width:100%;\" cols=\"7\" rows=\"2\"></textarea>
                                                            </div>                                                       
                                                    </td>
                                           <td class=\"lovtd\">
                                                            <div class=\"form-group form-group-sm\" style=\"width:100% !important;\">
                                                                <textarea class=\"form-control\" aria-label=\"...\" id=\"wkfAppActnsRow_WWW123WWW_ActnURL\" name=\"wkfAppActnsRow_WWW123WWW_ActnURL\" style=\"width:100%;\" cols=\"7\" rows=\"2\"></textarea>
                                                            </div>                                                       
                                                    </td>
                                          <td class=\"lovtd\">
							<select data-placeholder=\"Select...\" class=\"form-control chosen-select\" id=\"wkfAppActnsRow_WWW123WWW_DsplyTyp\" name=\"wkfAppActnsRow_WWW123WWW_DsplyTyp\">";

                                                            $valslctdArry = array("", "");
                                                            $srchInsArrys = array("Dialog", "Direct");
                                                            for ($z = 0; $z < count($srchInsArrys); $z++) {
                                                                $nwRowHtml .= "<option value=\"$srchInsArrys[$z]\" $valslctdArry[$z]>$srchInsArrys[$z]</option>";
                                                            }
                                                            $nwRowHtml .= "</select>
                                                    </td>
                                          <td class=\"lovtd\"> 
                                              <div class=\"form-group form-group-sm \">
                                                <div class=\"form-check\" style=\"font-size: 12px !important;\">
                                                    <label class=\"form-check-label\">
                                                        <input type=\"checkbox\" class=\"form-check-input\" id=\"wkfAppActnsRow_WWW123WWW_AdmnOnly\" name=\"wkfAppActnsRow_WWW123WWW_AdmnOnly\">
                                                    </label>
                                                </div>
                                              </div>
                                           </td>
                                           <td class=\"lovtd\">
                                                <button type=\"button\" class=\"btn btn-default\" style=\"margin: 0px !important;padding:0px 3px 2px 4px !important;\" onclick=\"delWkfAppAction('wkfAppActnsRow__WWW123WWW');\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Delete Group Member\">
                                                    <img src=\"cmn_images/delete.png\" style=\"height:15px; width:auto; position: relative; vertical-align: middle;\">
                                                </button>
                                           </td>
                                        </tr>";
                                                            $nwRowHtml = urlencode($nwRowHtml);
                                                            ?>
                                                            <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="insertNewRowBe4('wkfAppActnsTable', 0, '<?php echo $nwRowHtml; ?>');">
                                                                <img src="cmn_images/add1-64.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                                                Add Workflow App Action
                                                            </button>
                                                        <?php } ?>
                                                        <table class="table table-striped table-bordered table-responsive" id="wkfAppActnsTable" cellspacing="0" width="100%" style="width:100%;min-width: 800px;">
                                                            <thead>
                                                                <tr>
                                                                    <th>No.</th>
                                                                    <th>Name of Action Performed</th>
                                                                    <th style="min-width: 150px;width:160px;">Description</th>
                                                                    <th style="min-width: 150px;width:160px;">SQL Statement</th>
                                                                    <th style="min-width: 150px;width:160px;">Action Url</th>
                                                                    <th>Web Display Type</th>
                                                                    <th>For Admins Only?</th> 
                                                                    <?php
                                                                    if ($canDelWkfAp === true) {
                                                                        ?>
                                                                        <th>&nbsp;</th>
                                                                    <?php } ?>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $result2 = get_WkfAppsActns($pkID);
                                                                $cntr = 0;
                                                                while ($row2 = loc_db_fetch_array($result2)) {
                                                                    $cntr += 1;
                                                                    ?>
                                                                    <tr id="wkfAppActnsRow_<?php echo $cntr; ?>" class="hand_cursor">                                    
                                                                        <td class="lovtd"><span class=""><?php echo ($curIdx * $lmtSze) + ($cntr); ?></span></td>
                                                                        <td class="lovtd">
                                                                            <?php if ($canEdtWkfAp === true) { ?>
                                                                                <div class="form-group form-group-sm" style="width:100% !important;">
                                                                                    <input type="text" class="form-control" aria-label="..." id="wkfAppActnsRow<?php echo $cntr; ?>_ActnNm" value="<?php echo $row2[1]; ?>" style="width:100% !important;">
                                                                                    <input type="hidden" class="form-control" aria-label="..." id="wkfAppActnsRow<?php echo $cntr; ?>_ActnID" value="<?php echo $row2[0]; ?>">
                                                                                </div>
                                                                            <?php } else { ?>
                                                                                <span class=""><?php echo $row2[1]; ?></span>
                                                                            <?php } ?>                                                         
                                                                        </td>
                                                                        <td class="lovtd">
                                                                            <?php if ($canEdtWkfAp === true) { ?>
                                                                                <div class="form-group form-group-sm" style="width:100% !important;">
                                                                                    <textarea class="form-control" aria-label="..." id="wkfAppActnsRow<?php echo $cntr; ?>_ActnDesc" name="wkfAppActnsRow<?php echo $cntr; ?>_ActnDesc" style="width:100%;" cols="7" rows="2"><?php echo $row2[2]; ?></textarea>
                                                                                </div>
                                                                            <?php } else { ?>
                                                                                <span class=""><?php echo $row2[2]; ?></span>
                                                                            <?php } ?>                                                         
                                                                        </td>
                                                                        <td class="lovtd">
                                                                            <?php if ($canEdtWkfAp === true) { ?>
                                                                                <div class="form-group form-group-sm" style="width:100% !important;">
                                                                                    <textarea class="form-control" aria-label="..." id="wkfAppActnsRow<?php echo $cntr; ?>_ActnSQL" name="wkfAppActnsRow<?php echo $cntr; ?>_ActnSQL" style="width:100%;" cols="7" rows="2"><?php echo $row2[3]; ?></textarea>
                                                                                </div>
                                                                            <?php } else { ?>
                                                                                <span class=""><?php echo $row2[3]; ?></span>
                                                                            <?php } ?>                                                         
                                                                        </td>
                                                                        <td class="lovtd">
                                                                            <?php if ($canEdtWkfAp === true) { ?>
                                                                                <div class="form-group form-group-sm" style="width:100% !important;">
                                                                                    <textarea class="form-control" aria-label="..." id="wkfAppActnsRow<?php echo $cntr; ?>_ActnURL" name="wkfAppActnsRow<?php echo $cntr; ?>_ActnURL" style="width:100%;" cols="7" rows="2"><?php echo $row2[10]; ?></textarea>
                                                                                </div>
                                                                            <?php } else { ?>
                                                                                <span class=""><?php echo $row2[10]; ?></span>
                                                                            <?php } ?>                                                         
                                                                        </td>
                                                                        <td class="lovtd">
                                                                            <?php if ($canEdtWkfAp === true) { ?>
                                                                                <select data-placeholder="Select..." class="form-control chosen-select" id="wkfAppActnsRow<?php echo $cntr; ?>_DsplyTyp" name="wkfAppActnsRow<?php echo $cntr; ?>_DsplyTyp">
                                                                                    <?php
                                                                                    $valslctdArry = array("", "");
                                                                                    $srchInsArrys = array("Dialog", "Direct");
                                                                                    for ($z = 0; $z < count($srchInsArrys); $z++) {
                                                                                        if ($row2[11] == $srchInsArrys[$z]) {
                                                                                            $valslctdArry[$z] = "selected";
                                                                                        }
                                                                                        ?>
                                                                                        <option value="<?php echo $srchInsArrys[$z]; ?>" <?php echo $valslctdArry[$z]; ?>><?php echo $srchInsArrys[$z]; ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            <?php } else {
                                                                                ?>
                                                                                <span><?php echo $row2[11]; ?></span>
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <td class="lovtd">
                                                                            <?php
                                                                            $isChkd = "";
                                                                            if ($row2[12] == "Yes") {
                                                                                $isChkd = "checked=\"true\"";
                                                                            }
                                                                            if ($canEdtWkfAp === true) {
                                                                                ?>
                                                                                <div class="form-group form-group-sm ">
                                                                                    <div class="form-check" style="font-size: 12px !important;">
                                                                                        <label class="form-check-label">
                                                                                            <input type="checkbox" class="form-check-input" id="wkfAppActnsRow<?php echo $cntr; ?>_AdmnOnly" name="wkfAppActnsRow<?php echo $cntr; ?>_AdmnOnly" <?php echo $isChkd ?>>
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            <?php } else { ?>
                                                                                <span class=""><?php echo $row2[12]; ?></span>
                                                                            <?php } ?>                                                         
                                                                        </td>
                                                                        <?php
                                                                        if ($canDelWkfAp === true) {
                                                                            ?>
                                                                            <td class="lovtd">
                                                                                <button type="button" class="btn btn-default" style="margin: 0px !important;padding:0px 3px 2px 4px !important;" onclick="delWkfAppAction('wkfAppActnsRow_<?php echo $cntr; ?>');" data-toggle="tooltip" data-placement="bottom" title="Delete App Action">
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
                $pkID = isset($_POST['sbmtdAppID']) ? $_POST['sbmtdAppID'] : -1;
                if ($pkID > 0) {
                    $result1 = get_WkfAppsDet($pkID);
                    $sbmtdAppID = $pkID;
                    while ($row1 = loc_db_fetch_array($result1)) {
                        ?>
                        <div class="row">
                            <div  class="col-md-6" style="padding:0px 3px 0px 3px !important;">
                                <fieldset class="basic_person_fs" style="padding:10px 3px 0px 3px !important;"> 
                                    <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                        <label for="wkfDetAppNm" class="control-label col-lg-4">Application Name:</label>
                                        <div  class="col-lg-8">
                                            <?php if ($canEdtWkfAp === true) { ?>
                                                <input type="text" class="form-control" aria-label="..." id="wkfDetAppNm" name="wkfDetAppNm" value="<?php echo $row1[1]; ?>" style="width:100%;">
                                                <input type="hidden" class="form-control" aria-label="..." id="wkfDetAppID" name="wkfDetAppID" value="<?php echo $row1[0]; ?>">
                                            <?php } else {
                                                ?>
                                                <span><?php echo $row1[1]; ?></span>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            <div  class="col-md-6" style="padding:0px 3px 0px 3px !important;">
                                <fieldset class = "basic_person_fs">
                                    <div class="form-group form-group-sm">
                                        <label for="wkfDetSrcMdl" class="control-label col-md-4">Source Module:</label>
                                        <div  class="col-md-8">
                                            <?php if ($canEdtWkfAp === true) { ?>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" aria-label="..." id="wkfDetSrcMdl" value="<?php echo $row1[2]; ?>">
                                                    <input type="hidden" id="wkfDetSrcMdlID" value="-1">
                                                    <label class="btn btn-primary btn-file input-group-addon" onclick="getLovsPage('myLovModal', 'myLovModalTitle', 'myLovModalBody', 'System Modules', '', '', '', 'radio', true, '<?php echo $row1[2]; ?>', 'wkfDetSrcMdlID', 'wkfDetSrcMdl', 'clear', 1, '');">
                                                        <span class="glyphicon glyphicon-th-list"></span>
                                                    </label>
                                                </div>
                                            <?php } else {
                                                ?>
                                                <span><?php echo $row1[2]; ?></span>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="row">
                            <div  class="col-md-12" style="padding:0px 3px 0px 3px !important;">
                                <fieldset class="basic_person_fs" style="padding:10px 3px 0px 3px !important;">                                                         
                                    <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                        <label for="wkfDetAppDesc" class="control-label col-lg-2">Description:</label>
                                        <div  class="col-lg-10">
                                            <?php if ($canEdtWkfAp === true) { ?>
                                                <textarea class="form-control" aria-label="..." id="wkfDetAppDesc" name="wkfDetAppDesc" style="width:100%;" cols="9" rows="2"><?php echo $row1[3]; ?></textarea>
                                            <?php } else {
                                                ?>
                                                <span><?php echo $row1[3]; ?></span>
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
                                    if ($canEdtWkfAp === true) {
                                        $nwRowHtml = "<tr id=\"wkfAppActnsRow__WWW123WWW\">"
                                                . "<td class=\"lovtd\"><span class=\"\">New</span></td>"
                                                . "<td class=\"lovtd\">                                            
                                              <div class=\"form-group form-group-sm\" style=\"width:100% !important;\">
                                                            <input type=\"text\" class=\"form-control\" aria-label=\"...\" id=\"wkfAppActnsRow_WWW123WWW_ActnNm\" value=\"\" style=\"width:100% !important;\">
                                                            <input type=\"hidden\" class=\"form-control\" aria-label=\"...\" id=\"wkfAppActnsRow_WWW123WWW_ActnID\" value=\"\">
                                              </div>
                                          </td>
                                           <td class=\"lovtd\">
                                                            <div class=\"form-group form-group-sm\" style=\"width:100% !important;\">
                                                                <textarea class=\"form-control\" aria-label=\"...\" id=\"wkfAppActnsRow_WWW123WWW_ActnDesc\" name=\"wkfAppActnsRow_WWW123WWW_ActnDesc\" style=\"width:100%;\" cols=\"7\" rows=\"2\"></textarea>
                                                            </div>                                                       
                                                    </td>
                                           <td class=\"lovtd\">
                                                            <div class=\"form-group form-group-sm\" style=\"width:100% !important;\">
                                                                <textarea class=\"form-control\" aria-label=\"...\" id=\"wkfAppActnsRow_WWW123WWW_ActnSQL\" name=\"wkfAppActnsRow_WWW123WWW_ActnSQL\" style=\"width:100%;\" cols=\"7\" rows=\"2\"></textarea>
                                                            </div>                                                       
                                                    </td>
                                           <td class=\"lovtd\">
                                                            <div class=\"form-group form-group-sm\" style=\"width:100% !important;\">
                                                                <textarea class=\"form-control\" aria-label=\"...\" id=\"wkfAppActnsRow_WWW123WWW_ActnURL\" name=\"wkfAppActnsRow_WWW123WWW_ActnURL\" style=\"width:100%;\" cols=\"7\" rows=\"2\"></textarea>
                                                            </div>                                                       
                                                    </td>
                                          <td class=\"lovtd\">
							<select data-placeholder=\"Select...\" class=\"form-control chosen-select\" id=\"wkfAppActnsRow_WWW123WWW_DsplyTyp\" name=\"wkfAppActnsRow_WWW123WWW_DsplyTyp\">";

                                        $valslctdArry = array("", "");
                                        $srchInsArrys = array("Dialog", "Direct");
                                        for ($z = 0; $z < count($srchInsArrys); $z++) {
                                            $nwRowHtml .= "<option value=\"$srchInsArrys[$z]\" $valslctdArry[$z]>$srchInsArrys[$z]</option>";
                                        }
                                        $nwRowHtml .= "</select>
                                                    </td>
                                          <td class=\"lovtd\"> 
                                              <div class=\"form-group form-group-sm \">
                                                <div class=\"form-check\" style=\"font-size: 12px !important;\">
                                                    <label class=\"form-check-label\">
                                                        <input type=\"checkbox\" class=\"form-check-input\" id=\"wkfAppActnsRow_WWW123WWW_AdmnOnly\" name=\"wkfAppActnsRow_WWW123WWW_AdmnOnly\">
                                                    </label>
                                                </div>
                                              </div>
                                           </td>
                                           <td class=\"lovtd\">
                                                <button type=\"button\" class=\"btn btn-default\" style=\"margin: 0px !important;padding:0px 3px 2px 4px !important;\" onclick=\"delWkfAppAction('wkfAppActnsRow__WWW123WWW');\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Delete Group Member\">
                                                    <img src=\"cmn_images/delete.png\" style=\"height:15px; width:auto; position: relative; vertical-align: middle;\">
                                                </button>
                                           </td>
                                        </tr>";
                                        $nwRowHtml = urlencode($nwRowHtml);
                                        ?>
                                        <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="insertNewRowBe4('wkfAppActnsTable', 0, '<?php echo $nwRowHtml; ?>');">
                                            <img src="cmn_images/add1-64.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                            Add Workflow App Action
                                        </button>
                                    <?php } ?>
                                    <table class="table table-striped table-bordered table-responsive" id="wkfAppActnsTable" cellspacing="0" width="100%" style="width:100%;min-width: 800px;">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Name of Action Performed</th>
                                                <th style="min-width: 150px;width:160px;">Description</th>
                                                <th style="min-width: 150px;width:160px;">SQL Statement</th>
                                                <th style="min-width: 150px;width:160px;">Action Url</th>
                                                <th>Web Display Type</th>
                                                <th>For Admins Only?</th> 
                                                <?php
                                                if ($canDelWkfAp === true) {
                                                    ?>
                                                    <th>&nbsp;</th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $result2 = get_WkfAppsActns($pkID);
                                            $cntr = 0;
                                            while ($row2 = loc_db_fetch_array($result2)) {
                                                $cntr += 1;
                                                ?>
                                                <tr id="wkfAppActnsRow_<?php echo $cntr; ?>" class="hand_cursor">                                    
                                                    <td class="lovtd"><span class=""><?php echo ($curIdx * $lmtSze) + ($cntr); ?></span></td>
                                                    <td class="lovtd">
                                                        <?php if ($canEdtWkfAp === true) { ?>
                                                            <div class="form-group form-group-sm" style="width:100% !important;">
                                                                <input type="text" class="form-control" aria-label="..." id="wkfAppActnsRow<?php echo $cntr; ?>_ActnNm" value="<?php echo $row2[1]; ?>" style="width:100% !important;">
                                                                <input type="hidden" class="form-control" aria-label="..." id="wkfAppActnsRow<?php echo $cntr; ?>_ActnID" value="<?php echo $row2[0]; ?>">
                                                            </div>
                                                        <?php } else { ?>
                                                            <span class=""><?php echo $row2[1]; ?></span>
                                                        <?php } ?>                                                         
                                                    </td>
                                                    <td class="lovtd">
                                                        <?php if ($canEdtWkfAp === true) { ?>
                                                            <div class="form-group form-group-sm" style="width:100% !important;">
                                                                <textarea class="form-control" aria-label="..." id="wkfAppActnsRow<?php echo $cntr; ?>_ActnDesc" name="wkfAppActnsRow<?php echo $cntr; ?>_ActnDesc" style="width:100%;" cols="7" rows="2"><?php echo $row2[2]; ?></textarea>
                                                            </div>
                                                        <?php } else { ?>
                                                            <span class=""><?php echo $row2[2]; ?></span>
                                                        <?php } ?>                                                         
                                                    </td>
                                                    <td class="lovtd">
                                                        <?php if ($canEdtWkfAp === true) { ?>
                                                            <div class="form-group form-group-sm" style="width:100% !important;">
                                                                <textarea class="form-control" aria-label="..." id="wkfAppActnsRow<?php echo $cntr; ?>_ActnSQL" name="wkfAppActnsRow<?php echo $cntr; ?>_ActnSQL" style="width:100%;" cols="7" rows="2"><?php echo $row2[3]; ?></textarea>
                                                            </div>
                                                        <?php } else { ?>
                                                            <span class=""><?php echo $row2[3]; ?></span>
                                                        <?php } ?>                                                         
                                                    </td>
                                                    <td class="lovtd">
                                                        <?php if ($canEdtWkfAp === true) { ?>
                                                            <div class="form-group form-group-sm" style="width:100% !important;">
                                                                <textarea class="form-control" aria-label="..." id="wkfAppActnsRow<?php echo $cntr; ?>_ActnURL" name="wkfAppActnsRow<?php echo $cntr; ?>_ActnURL" style="width:100%;" cols="7" rows="2"><?php echo $row2[10]; ?></textarea>
                                                            </div>
                                                        <?php } else { ?>
                                                            <span class=""><?php echo $row2[10]; ?></span>
                                                        <?php } ?>                                                         
                                                    </td>
                                                    <td class="lovtd">
                                                        <?php if ($canEdtWkfAp === true) { ?>
                                                            <select data-placeholder="Select..." class="form-control chosen-select" id="wkfAppActnsRow<?php echo $cntr; ?>_DsplyTyp" name="wkfAppActnsRow<?php echo $cntr; ?>_DsplyTyp">
                                                                <?php
                                                                $valslctdArry = array("", "");
                                                                $srchInsArrys = array("Dialog", "Direct");
                                                                for ($z = 0; $z < count($srchInsArrys); $z++) {
                                                                    if ($row2[11] == $srchInsArrys[$z]) {
                                                                        $valslctdArry[$z] = "selected";
                                                                    }
                                                                    ?>
                                                                    <option value="<?php echo $srchInsArrys[$z]; ?>" <?php echo $valslctdArry[$z]; ?>><?php echo $srchInsArrys[$z]; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        <?php } else {
                                                            ?>
                                                            <span><?php echo $row2[11]; ?></span>
                                                            <?php
                                                        }
                                                        ?>
                                                    </td>
                                                    <td class="lovtd">
                                                        <?php
                                                        $isChkd = "";
                                                        if ($row2[12] == "Yes") {
                                                            $isChkd = "checked=\"true\"";
                                                        }
                                                        if ($canEdtWkfAp === true) {
                                                            ?>
                                                            <div class="form-group form-group-sm ">
                                                                <div class="form-check" style="font-size: 12px !important;">
                                                                    <label class="form-check-label">
                                                                        <input type="checkbox" class="form-check-input" id="wkfAppActnsRow<?php echo $cntr; ?>_AdmnOnly" name="wkfAppActnsRow<?php echo $cntr; ?>_AdmnOnly" <?php echo $isChkd ?>>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        <?php } else { ?>
                                                            <span class=""><?php echo $row2[12]; ?></span>
                                                        <?php } ?>                                                         
                                                    </td>
                                                    <?php
                                                    if ($canDelWkfAp === true) {
                                                        ?>
                                                        <td class="lovtd">
                                                            <button type="button" class="btn btn-default" style="margin: 0px !important;padding:0px 3px 2px 4px !important;" onclick="delWkfAppAction('wkfAppActnsRow_<?php echo $cntr; ?>');" data-toggle="tooltip" data-placement="bottom" title="Delete App Action">
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
                //New Workflow App Form
                $curIdx = 0;
                $pkID = -1;
                if ($canAddWkfAp === true) {
                    
                } else {
                    exit();
                }
                ?>
                <div class="row">
                    <div  class="col-md-6" style="padding:0px 3px 0px 3px !important;">
                        <fieldset class="basic_person_fs" style="padding:10px 3px 0px 3px !important;"> 
                            <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                <label for="wkfDetAppNm" class="control-label col-lg-4">Application Name:</label>
                                <div  class="col-lg-8">
                                    <input type="text" class="form-control" aria-label="..." id="wkfDetAppNm" name="wkfDetAppNm" value="" style="width:100%;">
                                    <input type="hidden" class="form-control" aria-label="..." id="wkfDetAppID" name="wkfDetAppID" value="-1">
                                    <input type="hidden" class="form-control" aria-label="..." id="isNew123" name="isNew123" value="5">
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div  class="col-md-6" style="padding:0px 3px 0px 3px !important;">
                        <fieldset class = "basic_person_fs">
                            <div class="form-group form-group-sm">
                                <label for="wkfDetSrcMdl" class="control-label col-md-4">Source Module:</label>
                                <div  class="col-md-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" aria-label="..." id="wkfDetSrcMdl" value="">
                                        <input type="hidden" id="wkfDetSrcMdlID" value="-1">
                                        <label class="btn btn-primary btn-file input-group-addon" onclick="getLovsPage('myLovModal', 'myLovModalTitle', 'myLovModalBody', 'System Modules', '', '', '', 'radio', true, '', 'wkfDetSrcMdlID', 'wkfDetSrcMdl', 'clear', 1, '');">
                                            <span class="glyphicon glyphicon-th-list"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <div class="row">
                    <div  class="col-md-12" style="padding:0px 3px 0px 3px !important;">
                        <fieldset class="basic_person_fs" style="padding:10px 3px 0px 3px !important;">                                                         
                            <div class="form-group form-group-sm col-md-12" style="padding:0px 3px 0px 3px !important;">
                                <label for="wkfDetAppDesc" class="control-label col-lg-2">Description:</label>
                                <div  class="col-lg-10">
                                    <textarea class="form-control" aria-label="..." id="wkfDetAppDesc" name="wkfDetAppDesc" style="width:100%;" cols="9" rows="2"></textarea>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="padding:0px 3px 0px 3px !important;">
                        <fieldset class="basic_person_fs"> 
                            <?php
                            $nwRowHtml = "<tr id=\"wkfAppActnsRow__WWW123WWW\">"
                                    . "<td class=\"lovtd\"><span class=\"\">New</span></td>"
                                    . "<td class=\"lovtd\">                                            
                                              <div class=\"form-group form-group-sm\" style=\"width:100% !important;\">
                                                            <input type=\"text\" class=\"form-control\" aria-label=\"...\" id=\"wkfAppActnsRow_WWW123WWW_ActnNm\" value=\"\" style=\"width:100% !important;\">
                                                            <input type=\"hidden\" class=\"form-control\" aria-label=\"...\" id=\"wkfAppActnsRow_WWW123WWW_ActnID\" value=\"\">
                                              </div>
                                          </td>
                                           <td class=\"lovtd\">
                                                            <div class=\"form-group form-group-sm\" style=\"width:100% !important;\">
                                                                <textarea class=\"form-control\" aria-label=\"...\" id=\"wkfAppActnsRow_WWW123WWW_ActnDesc\" name=\"wkfAppActnsRow_WWW123WWW_ActnDesc\" style=\"width:100%;\" cols=\"7\" rows=\"2\"></textarea>
                                                            </div>                                                       
                                                    </td>
                                           <td class=\"lovtd\">
                                                            <div class=\"form-group form-group-sm\" style=\"width:100% !important;\">
                                                                <textarea class=\"form-control\" aria-label=\"...\" id=\"wkfAppActnsRow_WWW123WWW_ActnSQL\" name=\"wkfAppActnsRow_WWW123WWW_ActnSQL\" style=\"width:100%;\" cols=\"7\" rows=\"2\"></textarea>
                                                            </div>                                                       
                                                    </td>
                                           <td class=\"lovtd\">
                                                            <div class=\"form-group form-group-sm\" style=\"width:100% !important;\">
                                                                <textarea class=\"form-control\" aria-label=\"...\" id=\"wkfAppActnsRow_WWW123WWW_ActnURL\" name=\"wkfAppActnsRow_WWW123WWW_ActnURL\" style=\"width:100%;\" cols=\"7\" rows=\"2\"></textarea>
                                                            </div>                                                       
                                                    </td>
                                          <td class=\"lovtd\">
							<select data-placeholder=\"Select...\" class=\"form-control chosen-select\" id=\"wkfAppActnsRow_WWW123WWW_DsplyTyp\" name=\"wkfAppActnsRow_WWW123WWW_DsplyTyp\">";
                            $valslctdArry = array("", "");
                            $srchInsArrys = array("Dialog", "Direct");
                            for ($z = 0; $z < count($srchInsArrys); $z++) {
                                $nwRowHtml .= "<option value=\"$srchInsArrys[$z]\" $valslctdArry[$z]>$srchInsArrys[$z]</option>";
                            }
                            $nwRowHtml .= "</select>
                                                    </td>
                                          <td class=\"lovtd\"> 
                                              <div class=\"form-group form-group-sm \">
                                                <div class=\"form-check\" style=\"font-size: 12px !important;\">
                                                    <label class=\"form-check-label\">
                                                        <input type=\"checkbox\" class=\"form-check-input\" id=\"wkfAppActnsRow_WWW123WWW_AdmnOnly\" name=\"wkfAppActnsRow_WWW123WWW_AdmnOnly\">
                                                    </label>
                                                </div>
                                              </div>
                                           </td>
                                           <td class=\"lovtd\">
                                                <button type=\"button\" class=\"btn btn-default\" style=\"margin: 0px !important;padding:0px 3px 2px 4px !important;\" onclick=\"delWkfAppAction('wkfAppActnsRow__WWW123WWW');\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"Delete Group Member\">
                                                    <img src=\"cmn_images/delete.png\" style=\"height:15px; width:auto; position: relative; vertical-align: middle;\">
                                                </button>
                                           </td>
                                        </tr>";
                            $nwRowHtml = urlencode($nwRowHtml);
                            ?>
                            <button type="button" class="btn btn-default" style="margin-bottom: 5px;" onclick="insertNewRowBe4('wkfAppActnsTable', 0, '<?php echo $nwRowHtml; ?>');">
                                <img src="cmn_images/add1-64.png" style="left: 0.5%; padding-right: 5px; height:20px; width:auto; position: relative; vertical-align: middle;">
                                Add Workflow App Action
                            </button>
                            <table class="table table-striped table-bordered table-responsive" id="wkfAppActnsTable" cellspacing="0" width="100%" style="width:100%;min-width: 800px;">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Name of Action Performed</th>
                                        <th style="min-width: 150px;width:160px;">Description</th>
                                        <th style="min-width: 150px;width:160px;">SQL Statement</th>
                                        <th style="min-width: 150px;width:160px;">Action Url</th>
                                        <th>Web Display Type</th>
                                        <th>For Admins Only?</th> 
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cntr = 1;
                                    ?>
                                    <tr id="wkfAppActnsRow_<?php echo $cntr; ?>" class="hand_cursor">                                    
                                        <td class="lovtd"><span class="">New</span></td>
                                        <td class="lovtd">
                                            <div class="form-group form-group-sm" style="width:100% !important;">
                                                <input type="text" class="form-control" aria-label="..." id="wkfAppActnsRow<?php echo $cntr; ?>_ActnNm" value="" style="width:100% !important;">
                                                <input type="hidden" class="form-control" aria-label="..." id="wkfAppActnsRow<?php echo $cntr; ?>_ActnID" value="">
                                            </div>
                                        </td>
                                        <td class="lovtd">
                                            <div class="form-group form-group-sm" style="width:100% !important;">
                                                <textarea class="form-control" aria-label="..." id="wkfAppActnsRow<?php echo $cntr; ?>_ActnDesc" name="wkfAppActnsRow<?php echo $cntr; ?>_ActnDesc" style="width:100%;" cols="7" rows="2"></textarea>
                                            </div>
                                        </td>
                                        <td class="lovtd">
                                            <div class="form-group form-group-sm" style="width:100% !important;">
                                                <textarea class="form-control" aria-label="..." id="wkfAppActnsRow<?php echo $cntr; ?>_ActnSQL" name="wkfAppActnsRow<?php echo $cntr; ?>_ActnSQL" style="width:100%;" cols="7" rows="2"></textarea>
                                            </div>
                                        </td>
                                        <td class="lovtd">
                                            <div class="form-group form-group-sm" style="width:100% !important;">
                                                <textarea class="form-control" aria-label="..." id="wkfAppActnsRow<?php echo $cntr; ?>_ActnURL" name="wkfAppActnsRow<?php echo $cntr; ?>_ActnURL" style="width:100%;" cols="7" rows="2"></textarea>
                                            </div>
                                        </td>
                                        <td class="lovtd">
                                            <select data-placeholder="Select..." class="form-control chosen-select" id="wkfAppActnsRow<?php echo $cntr; ?>_DsplyTyp" name="wkfAppActnsRow<?php echo $cntr; ?>_DsplyTyp">
                                                <?php
                                                $valslctdArry = array("", "");
                                                $srchInsArrys = array("Dialog", "Direct");
                                                for ($z = 0; $z < count($srchInsArrys); $z++) {
                                                    ?>
                                                    <option value="<?php echo $srchInsArrys[$z]; ?>" <?php echo $valslctdArry[$z]; ?>><?php echo $srchInsArrys[$z]; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td class="lovtd">
                                            <?php
                                            $isChkd = "";
                                            ?>
                                            <div class="form-group form-group-sm ">
                                                <div class="form-check" style="font-size: 12px !important;">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" class="form-check-input" id="wkfAppActnsRow<?php echo $cntr; ?>_AdmnOnly" name="wkfAppActnsRow<?php echo $cntr; ?>_AdmnOnly" <?php echo $isChkd ?>>
                                                    </label>
                                                </div>
                                            </div>                                                       
                                        </td>
                                        <td class="lovtd">
                                            <button type="button" class="btn btn-default" style="margin: 0px !important;padding:0px 3px 2px 4px !important;" onclick="delWkfAppAction('wkfAppActnsRow_<?php echo $cntr; ?>');" data-toggle="tooltip" data-placement="bottom" title="Delete App Action">
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
