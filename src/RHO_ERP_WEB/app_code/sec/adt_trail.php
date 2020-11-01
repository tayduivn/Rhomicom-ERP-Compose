<?php
$pageNo = isset($_POST['pageNo']) ? cleanInputData($_POST['pageNo']) : 1;
$lmtSze = isset($_POST['limitSze']) ? cleanInputData($_POST['limitSze']) : 10;
$sortBy = isset($_POST['sortBy']) ? cleanInputData($_POST['sortBy']) : "Role Name";

if (array_key_exists('lgn_num', get_defined_vars())) {
    if ($lgn_num > 0 && $canview === true) {
        if ($qstr == "DELETE") {
            if ($actyp == 1) {
                
            }
        } else if ($qstr == "UPDATE") {
            if ($actyp == 1) {
                
            } else if ($actyp == 2) {
                
            }
        } else {
            if ($vwtyp == 0) {
                $pkID = isset($_POST['sbmtdMdlID']) ? $_POST['sbmtdMdlID'] : -1;
                echo $cntent . "<li>
						<span class=\"divider\"><i class=\"fa fa-angle-right\" aria-hidden=\"true\"></i></span>
                                                <span style=\"text-decoration:none;\">Audit Trail Information</span>
					</li>
                                       </ul>
                                     </div>";
                ?>
                <form id='adtTrailsForm' action='' method='post' accept-charset='UTF-8'>
                    <div class="row rhoRowMargin">
                        <div class="col-md-3" style="padding:0px 15px 0px 15px !important;">
                            <select class="form-control" id="allMdlsToSlct" style="width:100%;">
                                <?php
                                $titleRslt = get_AllMdls("%", "Module Name", 0, 1000000, "Module Name");
                                $cntr = 0;
                                while ($titleRow = loc_db_fetch_array($titleRslt)) {
                                    $selectedTxt = "";
                                    if ($pkID <= 0 && $cntr == 0) {
                                        $pkID = $titleRow[0];
                                        $selectedTxt = "selected";
                                    } else if ($pkID == $titleRow[0]) {
                                        $selectedTxt = "selected";
                                    }
                                    $cntr++;
                                    ?>
                                    <option value="<?php echo $titleRow[0]; ?>" <?php echo $selectedTxt; ?>><?php echo $titleRow[1]; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3" style="padding:0px 15px 0px 15px !important;">
                            <div class="input-group">
                                <input class="form-control" id="adtTrailsSrchFor" name="adtTrailsSrchFor" type = "text" placeholder="Search For" value="<?php echo trim(str_replace("%", " ", $srchFor)); ?>" onkeyup="enterKeyFuncAdtTrails(event, '', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=0')">
                                <input id="adtTrailsPageNo" name="adtTrailsPageNo" type = "hidden" value="<?php echo $pageNo; ?>">
                                <label class="btn btn-primary btn-file input-group-addon" onclick="getAdtTrails('clear', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=0')">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </label>
                                <label class="btn btn-primary btn-file input-group-addon" onclick="getAdtTrails('', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=0')">
                                    <span class="glyphicon glyphicon-search"></span>
                                </label> 
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-filter"></span></span>
                                <select data-placeholder="Select..." class="form-control chosen-select" id="adtTrailsSrchIn">
                                    <?php
                                    $valslctdArry = array("", "", "", "", "", "");
                                    $srchInsArrys = array("Login Number", "User Name", "Action Details", "Action Type", "Date/Time", "Machine Used");

                                    for ($z = 0; $z < count($srchInsArrys); $z++) {
                                        if ($srchIn == $srchInsArrys[$z]) {
                                            $valslctdArry[$z] = "selected";
                                        }
                                        ?>
                                        <option value="<?php echo $srchInsArrys[$z]; ?>" <?php echo $valslctdArry[$z]; ?>><?php echo $srchInsArrys[$z]; ?></option>
                                    <?php } ?>
                                </select>
                                <span class="input-group-addon" style="max-width: 1px !important;padding:0px !important;width:1px !important;border:none !important;"></span>
                                <select data-placeholder="Select..." class="form-control chosen-select" id="adtTrailsDsplySze" name="adtTrailsDsplySze" style="min-width:70px !important;">                            
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
                        <!--<div class="col-md-2">
                            <div class="input-group">                        
                                <span class="input-group-addon"><span class="glyphicon glyphicon-sort-by-attributes"></span></span>
                                <select data-placeholder="Select..." class="form-control chosen-select" id="adtTrailsSortBy" name="adtTrailsSortBy">
                        <?php
                        $valslctdArry = array("", "", "");
                        $srchInsArrys = array("Table ID");
                        for ($z = 0; $z < count($srchInsArrys); $z++) {
                            if ($sortBy == $srchInsArrys[$z]) {
                                $valslctdArry[$z] = "selected";
                            }
                            ?>
                                                                            <option value="<?php echo $srchInsArrys[$z]; ?>" <?php echo $valslctdArry[$z]; ?>><?php echo $srchInsArrys[$z]; ?></option>
                        <?php } ?>
                                </select> 
                            </div>
                        </div>-->
                        <div class="col-md-2">
                            <nav aria-label="Page navigation">
                                <ul class="pagination" style="margin: 0px !important;">
                                    <li>
                                        <a class="rhopagination" href="javascript:getAdtTrails('previous', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=0');" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="rhopagination" href="javascript:getAdtTrails('next', '#allmodules', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=0');" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <div class="row"> 
                        <div  class="col-md-12">
                            <table class="table table-striped table-bordered table-responsive" id="adtTrailsTable" cellspacing="0" width="100%" style="width:100%;min-width: 800px;">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>User Name</th>
                                        <th>Action Type</th>
                                        <th>Action Details</th>
                                        <th>Action Time</th>
                                        <th>Machine Used</th>
                                        <th>Login Number</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $total = get_MdlsAdtTrlsTtls($srchFor, $srchIn, $pkID);
                                    if ($pageNo > ceil($total / $lmtSze)) {
                                        $pageNo = 1;
                                    } else if ($pageNo < 1) {
                                        $pageNo = ceil($total / $lmtSze);
                                    }

                                    $curIdx = $pageNo - 1;
                                    $result = get_MdlsAdtTrls($srchFor, $srchIn, $curIdx, $lmtSze, $pkID);
                                    $cntr = 0;
                                    while ($row = loc_db_fetch_array($result)) {
                                        $cntr += 1;
                                        ?>
                                        <tr id="adtTrailsEdtRow_<?php echo $cntr; ?>">                                    
                                            <td class="lovtd"><?php echo ($curIdx * $lmtSze) + ($cntr); ?></td>
                                            <td class="lovtd">
                                                <span><?php echo $row[0]; ?></span>                                                         
                                            </td>
                                            <td class="lovtd">
                                                <span><?php echo $row[1]; ?></span>                                                         
                                            </td>
                                            <?php if (strpos($row[4], "Browser") === FALSE) { ?>
                                                <td class="lovtd">
                                                    <span><?php echo $row[2]; ?></span>                                                        
                                                </td>
                                            <?php } else { ?>
                                                <td class="lovtd">
                                                    <button type="button" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="bottom" title="View Details" onclick="getOneUsrLgnDet('<?php echo $row[6]; ?>', 'grp=<?php echo $group; ?>&typ=<?php echo $type; ?>&pg=<?php echo $pgNo; ?>&vtyp=1&sbmtdRowID=<?php echo $row[7]; ?>');" style="padding:2px !important;" style="padding:2px !important;">
                                                        <img src="cmn_images/kghostview.png" style="height:20px; width:auto; position: relative; vertical-align: middle;"><?php echo $row[2] == "" ? "View Details" : $row[2]; ?>
                                                    </button>
                                                </td>
                                            <?php } ?>
                                            <td class="lovtd">
                                                <span><?php echo $row[3]; ?></span>                                                       
                                            </td>
                                            <td class="lovtd">
                                                <span><?php echo $row[4]; ?></span>                                                       
                                            </td>
                                            <td class="lovtd">
                                                <span><?php echo $row[6]; ?></span>                                                       
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
            } else if ($vwtyp == 1) {
                $sbmtdRowID = isset($_POST['sbmtdRowID']) ? cleanInputData($_POST['sbmtdRowID']) : -1;
                $sbmtdLgnNum = isset($_POST['sbmtdLgnNum']) ? cleanInputData($_POST['sbmtdLgnNum']) : -1;
                $file = $ftp_base_db_fldr . "/bin/log_files/adt_trail/$sbmtdRowID" . "_" . "$sbmtdLgnNum" . ".rho";
                if (file_exists($file)) {
                    $text = file_get_contents($file);
                    echo str_replace(PHP_EOL, "<br/>", $text);
                } else {
                    echo $file."_File not Found!";
                }
            } else if ($vwtyp == 2) {
                
            } else if ($vwtyp == 3) {
                
            } else if ($vwtyp == 4) {
                
            }
        }
    }
}