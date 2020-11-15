<?php

function getAccntName($accntid) {
    $sqlStr = "select accnt_name from accb.accb_chart_of_accnts where accnt_id = $accntid";
    //echo "</br> GetAccName New ".$sqlStr;
    $result = executeSQLNoParams($sqlStr);
    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return "";
}

function getAccntId($tblNm, $srchcol, $rtrnCol, $recid) {
    $sqlStr = "select COALESCE(" . $rtrnCol . ",-1) from " . $tblNm . " where " . $srchcol . " = " . $recid;
    $result = executeSQLNoParams($sqlStr);
    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
}

function getAccntNum($accntid) {
    $sqlStr = "select accnt_num from accb.accb_chart_of_accnts where accnt_id = $accntid";
    $result = executeSQLNoParams($sqlStr);
    //echo "</br>".$sqlStr;
    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return "";
}


function createPaymntLine($pymtTyp, $amnt, $curBals, $payRmrk, $srcDocTyp, $srcDocID, $dateStr, $dateRcvd) {
    $dateStr = getDB_Date_time();
    global $usrID;
    global $orgID;

    $insSQL = "INSERT INTO scm.scm_payments(" .
            "pymnt_type, amount_paid, custmrs_balance, pymnt_remark, " .
            "src_doc_typ, src_doc_id, created_by, creation_date, last_update_by, " .
            "last_update_date, date_rcvd) " .
            "VALUES ('" . loc_db_escape_string($pymtTyp) . "',$amnt,$curBals, '" . loc_db_escape_string($payRmrk) .
            "','" . loc_db_escape_string($srcDocTyp) .
            "',$srcDocID,$usrID, '" . $dateStr . "',$usrID, '" . $dateStr . "', '" . $dateRcvd . "')";
    execUpdtInsSQL($insSQL);
}

function createTodaysGLBatch($orgid, $batchnm, $batchdesc, $batchsource) {
    $dateStr = getDB_Date_time();
    global $usrID;
    global $orgID;

    $insSQL = "INSERT INTO accb.accb_trnsctn_batches(" .
            "batch_name, batch_description, created_by, creation_date, " .
            "org_id, batch_status, last_update_by, last_update_date, batch_source) " .
            "VALUES ('" . loc_db_escape_string($batchnm) . "', '" . loc_db_escape_string($batchdesc) .
            "',$usrID, '" . $dateStr . "',$orgid, '0', $usrID, 
              '" . $dateStr . "','" . loc_db_escape_string($batchsource) . "')";
    execUpdtInsSQL($insSQL);
}

function createPymntGLLine($accntid, $trnsdesc, $dbtamnt, $trnsdte, $crncyid, $batchid, $crdtamnt, $netamnt, $srcids, $dateStr) {
    $dateStr = getDB_Date_time();
    global $usrID;
    global $orgID;

    if (accntid <= 0) {
        return;
    }
    $insSQL = "INSERT INTO accb.accb_trnsctn_details(" .
            "accnt_id, transaction_desc, dbt_amount, trnsctn_date, " .
            "func_cur_id, created_by, creation_date, batch_id, crdt_amount, " .
            "last_update_by, last_update_date, net_amount, trns_status, source_trns_ids) " .
            "VALUES ($accntid, '" . loc_db_escape_string($trnsdesc) . "', $dbtamnt, 
               '" . loc_db_escape_string($trnsdte) . "',$crncyid, $usrID, 
                '" . $dateStr . "',$batchid,$crdtamnt,$usrID, 
               '" . $dateStr . "',$netamnt, '0', '" . $srcids . "')";
    execUpdtInsSQL($insSQL);
}

function createPymntGLIntFcLn($accntid, $trnsdesc, $dbtamnt, $trnsdte, $crncyid, $crdtamnt, $netamnt, $srcDocTyp, $srcDocID, $srcDocLnID, $dateStr) {
    $dateStr = getDB_Date_time();
    global $usrID;
    global $orgID;

    if ($accntid <= 0) {
        return;
    }
    $insSQL = "INSERT INTO scm.scm_gl_interface(" .
            "accnt_id, transaction_desc, dbt_amount, trnsctn_date, " .
            "func_cur_id, created_by, creation_date, crdt_amount, last_update_by, " .
            "last_update_date, net_amount, gl_batch_id, src_doc_typ, src_doc_id, " .
            "src_doc_line_id) " .
            "VALUES ($accntid, '" . loc_db_escape_string($trnsdesc) . "',$dbtamnt, 
               '" . loc_db_escape_string($trnsdte) . "',$crncyid,$usrID, '" . $dateStr . "',$crdtamnt,$usrID, 
                   '" . $dateStr . "',$netamnt, -1,'" . loc_db_escape_string($srcDocTyp) . "',$srcDocID, $srcDocLnID)";
    execUpdtInsSQL($insSQL);
}

function getPurchOdrID($parPONo) {
    global $orgID;
    $sqlStr = "SELECT prchs_doc_hdr_id from scm.scm_prchs_docs_hdr
          WHERE purchase_doc_num = $parPONo AND org_id = $orgID";
    $result = executeSQLNoParams($sqlStr);
    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return 0;
}

function getPurchOdrNo($parPOID) {
    $sqlStr = "select purchase_doc_num from scm.scm_prchs_docs_hdr 
         where prchs_doc_hdr_id = $parPOID";
    $result = executeSQLNoParams($sqlStr);
    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return 0;
}

function dispDBTimeLong($parDTime) {
    if ($parDTime == "") {
        return "";
    } else {
        return to_char(to_timestamp($parDTime, 'YYYY-MM-DD HH24:MI:SS'), 'DD-Mon-YYYY HH24:MI:SS');
    }
}

function getGnrlRecIDilike($tblNm, $srchcol, $rtrnCol, $recname, $orgid) {
    $sqlStr = "select " . $rtrnCol . " from " . $tblNm . " where lower(" . $srchcol . ") ilike lower('" .
            loc_db_escape_string($recname) . "') and org_id = " . $orgid;
    //echo "<p>".$sqlStr ."</p>";
    $result = executeSQLNoParams($sqlStr);
    $ftchrslt = "'";
    $ftchrslt1 = "'";
    while ($row = loc_db_fetch_array($result)) {
        $ftchrslt1.=$row[0] . "','";
    }
    if ($ftchrslt1 != "'") {
        $finrst = $ftchrslt1 . $ftchrslt;
        return str_ireplace(",''", "", $finrst);
    }
    return -1;
}

function getSalesDocLnID($itmID, $storeID, $srcDocID) {
    $sqlStr = "select y.invc_det_ln_id " .
            "from scm.scm_sales_invc_det y " .
            "where y.itm_id=  $itmID and y.store_id= $storeID 
            and y.invc_hdr_id=" . $srcDocID . " ";
    $result = executeSQLNoParams($sqlStr);
    if (loc_db_num_rows($result) > 0) {
        $row = loc_db_fetch_array($result);
        return $row[0];
    }
    return -1;
}

function getSalesSmmryItmID($smmryType, $codeBhnd, $srcDocID, $srcDocTyp) {
    $sqlStr = "select y.smmry_id " .
            "from scm.scm_doc_amnt_smmrys y " .
            "where y.smmry_type= '" . $smmryType . "' and y.code_id_behind= " . $codeBhnd .
            " and y.src_doc_type='" . $srcDocTyp .
            "' and y.src_doc_hdr_id=" . $srcDocID . " ";
    $result = executeSQLNoParams($sqlStr);
    if (loc_db_num_rows($result) > 0) {
        $row = loc_db_fetch_array($result);
        return $row[0];
    }
    return -1;
}

function get_One_SalesDcDt1($dochdrID) {
    $strSql = "SELECT a.invc_hdr_id, a.invc_number, " .
            "a.invc_type, a.src_doc_hdr_id, a.invc_date, " .
            "a.customer_id, a.customer_site_id, a.comments_desc, a.payment_terms, " .
            "a.approval_status, a.next_aproval_action, " .
            "a.created_by " .
            "FROM scm.scm_sales_invc_hdr a " .
            "WHERE(a.invc_hdr_id = " . $dochdrID .
            ")";

    $result = executeSQLNoParams($strSql);
    return $result;
}

function get_One_SalesDcDt($dochdrID) {
    global $orgID;
    $sqlStr = "SELECT a.invc_hdr_id mt, 
        CASE WHEN a.invc_type = 'Sales Order' THEN 'Sales Invoice'
        WHEN a.invc_type = 'Pro-Forma Invoice' THEN 'Sales Order'
        WHEN a.invc_type = 'Internal Item Request' THEN 'Item Issue-Unbilled'
        WHEN a.invc_type = 'Sales Invoice' THEN 'Sales Return'
        WHEN a.invc_type = 'Item Issue-Unbilled' THEN 'Sales Return'
        END \"document type\", 
        (SELECT invc_number FROM scm.scm_sales_invc_hdr WHERE invc_hdr_id = a.src_doc_hdr_id) \"source doc. no\",
        a.comments_desc \"description\", a.invc_number \"doc. number\", 
        (SELECT cust_sup_name FROM scm.scm_cstmr_suplr WHERE cust_sup_id = a.customer_id) \"customer name\",
        to_char(to_timestamp(a.invc_date,'YYYY-MM-DD HH24:MI:SS'),'DD-Mon-YYYY') \"doc. date\" ,  
       (SELECT site_name FROM scm.scm_cstmr_suplr_sites WHERE cust_supplier_id = a.customer_site_id) \"customer site\",
       a.payment_terms \"payment terms\", 
       (SELECT user_name from sec.sec_users WHERE user_id = a.created_by) \"created by\",
       a.approval_status \"approval status\", a.next_aproval_action \"next action\", a.customer_id mt, a.customer_site_id mt
       FROM scm.scm_sales_invc_hdr a 
        WHERE a.invc_hdr_id = $dochdrID";
    //echo $sqlStr;
    $result = executeSQLNoParams($sqlStr);
    return $result;
}

function getSalesDocBscAmnt($dochdrID, $docTyp) {
    $strSql = "select SUM(CASE WHEN (smmry_type='2Tax' or smmry_type='4Extra Charge') THEN -1*y.smmry_amnt ELSE y.smmry_amnt END) amnt " .
            "from scm.scm_doc_amnt_smmrys y " .
            "where y.src_doc_hdr_id=" . $dochdrID .
            " and y.src_doc_type='" . $docTyp . "' and y.smmry_type != '1Initial Amount' " .
            " and y.smmry_type != '6Total Payments Received' and y.smmry_type != " .
            "'7Change/Balance' and smmry_type!='3Discount'";
    $result = executeSQLNoParams($strSql);
    $rs = 0;

    if (loc_db_num_rows($result) > 0) {
        $row = loc_db_fetch_array($result);
        $rs = $row[0];
    }
    return $rs;
}

function getSalesDocCodesAmnt($codeID, $unitAmnt, $qnty) {
    $codeSQL = getGnrlRecNm("scm.scm_tax_codes", "code_id", "sql_formular", $codeID);
    $codeSQL = str_replace("{:unit_price}", $unitAmnt, str_replace("{:qty}", $qnty, $codeSQL));
    if ($codeSQL != "") {
        $result = executeSQLNoParams($codeSQL);
        $rs1 = 0;

        if (loc_db_num_rows($result) > 0) {
            $row = loc_db_fetch_array($result);
            $rs1 = $row[0];
        }
        return $rs1 * $qnty;
    } else {
        return 0.00;
    }
}

function getSalesDocGrndAmnt($dochdrID) {
    $strSql = "select COALESCE(SUM(y.doc_qty*unit_selling_price),0) amnt " .
            "from scm.scm_sales_invc_det y " .
            "where y.invc_hdr_id=" . $dochdrID . " ";
    //echo "</br>".$strSql;
    $result = executeSQLNoParams($strSql);
    $rs = 0;

    if (loc_db_num_rows($result) > 0) {
        $row = loc_db_fetch_array($result);
        $rs = (float) $row[0];
    }
    return $rs;
}

function getSalesDocRcvdPymnts($dochdrID, $docType) {
    $strSql = "select COALESCE(SUM(y.amount_paid),0) amnt " .
            "from scm.scm_payments y " .
            "where y.src_doc_id=" . $dochdrID . " and y.src_doc_typ = '" . loc_db_escape_string($docType) . "'";
    $result = executeSQLNoParams($strSql);
    $rs = 0;

    if (loc_db_num_rows($result) > 0) {
        $row = loc_db_fetch_array($result);
        $rs = $row[0];
    }
    return $rs;
}

function get_One_AvlblSrcLnQty($srcLnID) {
    $strSql = "SELECT (a.doc_qty - a.qty_trnsctd_in_dest_doc) avlbl_qty " .
            "FROM scm.scm_sales_invc_det a " .
            "WHERE(a.invc_det_ln_id = " . $srcLnID .
            ") ORDER BY a.invc_det_ln_id";
    $result = executeSQLNoParams($strSql);
    $rs = 0;
    if (loc_db_num_rows($result) > 0) {
        $row = loc_db_fetch_array($result);
        $rs = $row[0];
    }
    return $rs;
}

function get_One_LnTrnsctdQty($dochdrID, $srcLnID) {
    $strSql = "SELECT SUM(a.doc_qty) trnsctd_qty " .
            "FROM scm.scm_sales_invc_det a " .
            "WHERE(a.invc_hdr_id IN(select b.invc_hdr_id " .
            "from scm.scm_sales_invc_hdr b where b.src_doc_hdr_id = " . $dochdrID .
            " and b.src_doc_hdr_id>0) and a.invc_det_ln_id = "
            . $srcLnID . ")";
    $result = executeSQLNoParams($strSql);
    $rs = 0;
    if (loc_db_num_rows($result) > 0) {
        $row = loc_db_fetch_array($result);
        $rs = $row[0];
    }
    return $rs;
}

function get_One_SalesDcLines($dochdrID) {
    global $recDt_SQL;
    $strSql = "SELECT a.invc_det_ln_id, a.itm_id, " .
            "a.doc_qty, a.unit_selling_price, (a.doc_qty * a.unit_selling_price) amnt, " .
            "a.store_id, a.crncy_id, (a.doc_qty - a.qty_trnsctd_in_dest_doc) avlbl_qty, " .
            "a.src_line_id, a.tax_code_id, a.dscnt_code_id, a.chrg_code_id, a.rtrn_reason, a.consgmnt_ids " .
            "FROM scm.scm_sales_invc_det a " .
            "WHERE(a.invc_hdr_id = " . $dochdrID .
            " and a.invc_hdr_id >0) ORDER BY a.invc_det_ln_id";
    $result = executeSQLNoParams($strSql);
    $recDt_SQL = $strSql;
    return $result;
}

function get_One_DfltAcnt($orgID) {
    $strSql = "SELECT row_id, itm_inv_asst_acnt_id, cost_of_goods_acnt_id, expense_acnt_id, " .
            "prchs_rtrns_acnt_id, rvnu_acnt_id, sales_rtrns_acnt_id, sales_cash_acnt_id, " .
            "sales_check_acnt_id, sales_rcvbl_acnt_id, rcpt_cash_acnt_id, " .
            "rcpt_lblty_acnt_id " .
            "FROM scm.scm_dflt_accnts a " .
            "WHERE(a.org_id = $orgID)";

    $result = executeSQLNoParams($strSql);
    return $result;
}

function get_DfltRcvblAcnt($orgID) {
    $strSql = "SELECT sales_rcvbl_acnt_id " .
            "FROM scm.scm_dflt_accnts a " .
            "WHERE(a.org_id = $orgID)";

    $result = executeSQLNoParams($strSql);
    if (loc_db_num_rows($result) > 0) {
        $row = loc_db_fetch_array($result);
        return $row[0];
    }
    return -1;
}

function get_DfltInvAcnt($orgID) {
    $strSql = "SELECT itm_inv_asst_acnt_id " .
            "FROM scm.scm_dflt_accnts a " .
            "WHERE(a.org_id = $orgID)";

    $result = executeSQLNoParams($strSql);
    if (loc_db_num_rows($result) > 0) {
        $row = loc_db_fetch_array($result);
        return $row[0];
    }
    return -1;
}

function get_DfltCSGAcnt($orgID) {
    $strSql = "SELECT cost_of_goods_acnt_id " .
            "FROM scm.scm_dflt_accnts a " .
            "WHERE(a.org_id = $orgID)";

    $result = executeSQLNoParams($strSql);
    if (loc_db_num_rows($result) > 0) {
        $row = loc_db_fetch_array($result);
        return $row[0];
    }
    return -1;
}

function get_DfltExpnsAcnt($orgID) {
    $strSql = "SELECT expense_acnt_id " .
            "FROM scm.scm_dflt_accnts a " .
            "WHERE(a.org_id = $orgID)";

    $result = executeSQLNoParams($strSql);
    if (loc_db_num_rows($result) > 0) {
        $row = loc_db_fetch_array($result);
        return $row[0];
    }
    return -1;
}

function get_DfltRvnuAcnt($orgID) {
    $strSql = "SELECT rvnu_acnt_id " .
            "FROM scm.scm_dflt_accnts a " .
            "WHERE(a.org_id = $orgID)";

    $result = executeSQLNoParams($strSql);
    if (loc_db_num_rows($result) > 0) {
        $row = loc_db_fetch_array($result);
        return $row[0];
    }
    return -1;
}

function get_DfltSRAcnt($orgID) {
    $strSql = "SELECT sales_rtrns_acnt_id " .
            "FROM scm.scm_dflt_accnts a " .
            "WHERE(a.org_id = $orgID)";

    $result = executeSQLNoParams($strSql);
    if (loc_db_num_rows($result) > 0) {
        $row = loc_db_fetch_array($result);
        return $row[0];
    }
    return -1;
}

function get_DfltCashAcnt($orgID) {
    $strSql = "SELECT sales_cash_acnt_id " .
            "FROM scm.scm_dflt_accnts a " .
            "WHERE(a.org_id = $orgID)";

    $result = executeSQLNoParams($strSql);
    if (loc_db_num_rows($result) > 0) {
        $row = loc_db_fetch_array($result);
        return $row[0];
    }
    return -1;
}

function get_DfltCheckAcnt($orgID) {
    $strSql = "SELECT sales_check_acnt_id " .
            "FROM scm.scm_dflt_accnts a " .
            "WHERE(a.org_id = $orgID)";

    $result = executeSQLNoParams($strSql);
    if (loc_db_num_rows($result) > 0) {
        $row = loc_db_fetch_array($result);
        return $row[0];
    }
    return -1;
}


function updateSmmryItm($smmryID, $smmryTyp, $amnt, $autoCalc, $smmryNm) {
    //Global.mnFrm.cmCde.Extra_Adt_Trl_Info = "";
    global $usrID;
    if ($smmryTyp == "3Discount") {
        $amnt = -1 * abs($amnt);
    }
    $dateStr = getDB_Date_time();
    $updtSQL = "UPDATE scm.scm_doc_amnt_smmrys SET " .
            "smmry_amnt = " . $amnt .
            ", last_update_by = " . $usrID . ", " .
            "auto_calc = '" . cnvrtBoolToBitStr($autoCalc) .
            "', last_update_date = '" . $dateStr .
            "', smmry_name='" . loc_db_escape_string($smmryNm) . "' WHERE (smmry_id = " . $smmryID . ")";
    execUpdtInsSQL($updtSQL);
}

function deleteSmmryItm($docID, $docType, $smmryTyp) {
    //Global.mnFrm.cmCde.Extra_Adt_Trl_Info = "";
    $delSQL = "DELETE FROM scm.scm_doc_amnt_smmrys WHERE src_doc_hdr_id = " .
            $docID . " and src_doc_type = '" . $docType . "' and smmry_type = '" . $smmryTyp . "'";
    execUpdtInsSQL($delSQL);
}

function loadInvRqrmnts() {
    global $dfltPrvldgs;
    $DefaultPrvldgs = $dfltPrvldgs;

    $subGrpNames = "";
    $mainTableNames = "";
    $keyColumnNames = "";

    $myName = "Stores And Inventory Manager";
    $myDesc = "This module helps you to manage your stores!";
    $audit_tbl_name = "rpt.rpt_audit_trail_tbl";

    $smplRoleName = "Stores And Inventory Manager Administrator";

    checkNAssignReqrmnts($myName, $myDesc, $audit_tbl_name, $smplRoleName, $DefaultPrvldgs, $subGrpNames, $mainTableNames, $keyColumnNames);
    createRqrdLOVs();
    echo "<p style=\"font-size:12px;\">Completed Checking & Requirements load for <b>$myName!</b></p>";
}

$menuItems = array("Sales/Item Issues", "Purchasing", "Item/Services List", "Categories",
    "Stores", "Receipts", "Receipts Returns", "Item/Service Templates", "GL Interface Table", "Item Balances", "Unit of Measures");

//$menuItems = array("Sales/Item Issues", "Purchasing", "Item/Services List", "Categories",
//                    "Stores", "Receipts & Returns", "Item/Service Templates", "GL Interface Table", "Item Balances");

require $base_dir . "admin_funcs.php";


$vwtyp1 = 0;
//echo $vwtyp1;
if (isset($_REQUEST['vwtyp']))
    $vwtyp1 = $_REQUEST['vwtyp'];
//echo $vwtyp1;
if ($vwtyp1 <= 100) {
    require $base_dir . "rho_header.php";
}
$pgNo = 0;
$grp = 12;
$typ = 1;

$mdlNm = "Stores And Inventory Manager";
$ModuleName = $mdlNm;
if (isset($_REQUEST['pg']))
    $pgNo = $_REQUEST['pg'];
if (isset($_REQUEST['q']))
    $mdlNm = $_REQUEST['q'];
if (isset($_REQUEST['grp']))
    $grp = $_REQUEST['grp'];
if (isset($_REQUEST['typ']))
    $typ = $_REQUEST['typ'];

$dfltPrvldgs = array("View Inventory Manager",
    /* 1 */ "View Item List", "View Product Categories", "View Stores/Warehouses"
    /* 4 */, "View Receipts", "View Receipt Returns", "View Item Type Templates",
    /* 7 */ "View Item Balances",
    /* 8 */ "Add Items", "Update Items",
    /* 10 */ "Add Item Stores", "Update Item Stores", "Delete Item Stores",
    /* 13 */ "Add Product Category", "Update Product Category",
    /* 15 */ "Add Stores", "Update Stores",
    /* 17 */ "Add Store Users", "Update Store Users", "Delete Store Users",
    /* 20 */ "Add Store Shelves", "Delete Store Shelves",
    /* 22 */ "Add Receipt", "Delete Receipt",
    /* 24 */ "Add Receipt Return", "Delete Receipt Return",
    /* 26 */ "Add Item Template", "Update Item Template",
    /* 28 */ "Add Template Stores", "Update Template Stores",
    /* 30 */ "View GL Interface",
    /* 31 */ "View SQL", "View Record History", "Send To GL Interface Table",
    /* 34 */ "View Purchases", "View Sales/Item Issues", "View Sales Returns",
    /* 37 */ "View Payments Received",
    /* 38 */ "View Purchase Requisitions", "Add Purchase Requisitions", "Edit Purchase Requisitions", "Delete Purchase Requisitions",
    /* 42 */ "View Purchase Orders", "Add Purchase Orders", "Edit Purchase Orders", "Delete Purchase Orders",
    /* 46 */ "View Pro-Forma Invoices", "Add Pro-Forma Invoices", "Edit Pro-Forma Invoices", "Delete Pro-Forma Invoices",
    /* 50 */ "View Sales Orders", "Add Sales Orders", "Edit Sales Orders", "Delete Sales Orders",
    /* 54 */ "View Sales Invoices", "Add Sales Invoices", "Edit Sales Invoices", "Delete Sales Invoices",
    /* 58 */ "View Internal Item Requests", "Add Internal Item Requests", "Edit Internal Item Requests", "Delete Internal Item Requests",
    /* 62 */ "View Item Issues-Unbilled", "Add Item Issues-Unbilled", "Edit Item Issues-Unbilled", "Delete Item Issues-Unbilled",
    /* 66 */ "View Sales Returns", "Add Sales Return", "Edit Sales Return", "Delete Sales Return",
    /* 70 */ "Send GL Interface Records to GL", "Cancel Documents", "View only Self-Created Documents",
    /* 73 */ "View UOM", "Add UOM", "Edit UOM", "Delete UOM", "Make Payments", "Delete Product Category",
    /* 79 */ "View UOM Conversion", "Add UOM Conversion", "Edit UOM Conversion", "Delete UOM Conversion",
    /* 83 */ "View Drug Interactions", "Add Drug Interactions", "Edit Drug Interactions", "Delete Drug Interactions",
    /* 87 */ "Edit Receipt", "Edit Returns", "Edit Store Transfers", "Edit Adjustments",
    /* 91 */ "Clear Stock Balance", "Do Quick Receipt");

function createRqrdLOVs() {

    $sysLovs = array("Cash Accounts", "Inventory/Asset Accounts", "Contra Expense Accounts",
        "Contra Revenue Accounts", "Customer Classifications", "Supplier Classifications",
        "Tax Codes", "Discount Codes", "Extra Charges", "Approved Requisitions",
        "Suppliers", "Customer/Supplier Sites", "Users' Sales Stores", "Approved Pro-Forma Invoices",
        "Approved Sales Orders", "Approved Internal Item Requests",
        "Customers", "Approved Sales Invoices/Item Issues", "Inventory Items",
        "Shelves", "Categories", "Stores", "Item Templates", "Purchase Orders", "Items Stores", "Consignment Conditions",
        "Receipt Return Reasons", "Store Shelves", "Unit Of Measures");
    $sysLovsDesc = array("Cash Accounts", "Inventory/Asset Accounts", "Contra Expense Accounts",
        "Contra Revenue Accounts", "Customer Classifications", "Supplier Classifications",
        "Tax Codes", "Discount Codes", "Extra Charges", "Approved Requisitions",
        "Suppliers", "Customer/Supplier Sites", "Users' Sales Stores", "Approved Pro-Forma Invoices",
        "Approved Sales Orders", "Approved Internal Item Requests",
        "Customers", "Approved Sales Invoices/Item Issues", "Inventory Items",
        "Shelves", "Categories", "Stores", "Item Templates", "Purchase Orders", "Items Stores", "Consignment Conditions",
        "Receipt Return Reasons", "Store Shelves", "Unit Of Measures");
    $sysLovsDynQrys = array("", "",
        "select distinct trim(to_char(accnt_id,'999999999999999999999999999999')) a, accnt_name b, '' c, org_id d, accnt_num e from accb.accb_chart_of_accnts where (accnt_type = 'EX' and is_prnt_accnt = '0' and is_enabled = '1' and is_contra = '1') order by accnt_num",
        "select distinct trim(to_char(accnt_id,'999999999999999999999999999999')) a, accnt_name b, '' c, org_id d, accnt_num e from accb.accb_chart_of_accnts where (accnt_type = 'R' and is_prnt_accnt = '0' and is_enabled = '1' and is_contra = '1') order by accnt_num",
        "", "",
        "select distinct trim(to_char(code_id,'999999999999999999999999999999')) a, code_name b, '' c, org_id d from scm.scm_tax_codes where (itm_type = 'Tax' and is_enabled = '1') order by code_name",
        "select distinct trim(to_char(code_id,'999999999999999999999999999999')) a, code_name b, '' c, org_id d from scm.scm_tax_codes where (itm_type = 'Discount' and is_enabled = '1') order by code_name",
        "select distinct trim(to_char(code_id,'999999999999999999999999999999')) a, code_name b, '' c, org_id d from scm.scm_tax_codes where (itm_type = 'Extra Charge' and is_enabled = '1') order by code_name",
        "select distinct trim(to_char(y.prchs_doc_hdr_id,'999999999999999999999999999999')) a, y.purchase_doc_num b, '' c, y.org_id d, y.prchs_doc_hdr_id g " .
        "from scm.scm_prchs_docs_hdr y, scm.scm_prchs_docs_det z " .
        "where (y.purchase_doc_type = 'Purchase Requisition' " .
        "and y.approval_status = 'Approved' " .
        "and z.prchs_doc_hdr_id = y.prchs_doc_hdr_id and (z.quantity - z.rqstd_qty_ordrd)>0) order by y.prchs_doc_hdr_id DESC",
        "select distinct trim(to_char(cust_sup_id,'999999999999999999999999999999')) a, cust_sup_name b, '' c, org_id d from scm.scm_cstmr_suplr where (cust_or_sup = 'Supplier') order by 2",
        "select distinct trim(to_char(cust_sup_site_id,'999999999999999999999999999999')) a, site_name b, '' c, cust_supplier_id d from scm.scm_cstmr_suplr_sites order by 2",
        "select distinct trim(to_char(y.subinv_id,'999999999999999999999999999999')) a, y.subinv_name b, '' c, y.org_id d, trim(to_char(z.user_id,'999999999999999999999999999999')) e from inv.inv_itm_subinventories y, inv.inv_user_subinventories z where y.subinv_id=z.subinv_id and y.allow_sales = '1' order by 2",
        "select distinct trim(to_char(y.invc_hdr_id,'999999999999999999999999999999')) a, y.invc_number b, '' c, y.org_id d, y.invc_hdr_id g " .
        "from scm.scm_sales_invc_hdr y, scm.scm_sales_invc_det z " .
        "where (y.invc_type = 'Pro-Forma Invoice' " .
        "and y.approval_status = 'Approved' " .
        "and z.invc_hdr_id = y.invc_hdr_id and (z.doc_qty - z.qty_trnsctd_in_dest_doc)>0) order by y.invc_hdr_id DESC",
        "select distinct trim(to_char(y.invc_hdr_id,'999999999999999999999999999999')) a, y.invc_number b, '' c, y.org_id d, y.invc_hdr_id g " .
        "from scm.scm_sales_invc_hdr y, scm.scm_sales_invc_det z " .
        "where (y.invc_type = 'Sales Order' " .
        "and y.approval_status = 'Approved' " .
        "and z.invc_hdr_id = y.invc_hdr_id and (z.doc_qty - z.qty_trnsctd_in_dest_doc)>0) order by y.invc_hdr_id DESC",
        "select distinct trim(to_char(y.invc_hdr_id,'999999999999999999999999999999')) a, y.invc_number b, '' c, y.org_id d, y.invc_hdr_id g " .
        "from scm.scm_sales_invc_hdr y, scm.scm_sales_invc_det z " .
        "where (y.invc_type = 'Internal Item Request' " .
        "and y.approval_status = 'Approved' " .
        "and z.invc_hdr_id = y.invc_hdr_id and (z.doc_qty - z.qty_trnsctd_in_dest_doc)>0) order by y.invc_hdr_id DESC",
        "select distinct trim(to_char(cust_sup_id,'999999999999999999999999999999')) a, cust_sup_name b, '' c, org_id d from scm.scm_cstmr_suplr where (cust_or_sup = 'Customer') order by 2",
        "select distinct trim(to_char(y.invc_hdr_id,'999999999999999999999999999999')) a, y.invc_number b, '' c, y.org_id d, y.invc_hdr_id g " .
        "from scm.scm_sales_invc_hdr y, scm.scm_sales_invc_det z " .
        "where ((y.invc_type = 'Item Issue-Unbilled' or y.invc_type = 'Sales Invoice') " .
        "and (y.approval_status = 'Approved') " .
        "and (z.invc_hdr_id = y.invc_hdr_id) and ((z.doc_qty - z.qty_trnsctd_in_dest_doc)>0)) order by y.invc_hdr_id DESC",
        "SELECT distinct trim(item_code) a, item_desc b, '' c FROM inv.inv_itm_list WHERE org_id = 6 order by 2",
        "",
        "select distinct trim(to_char(cat_id,'999999999999999999999999999999')) a, cat_name b, '' c, org_id d from inv.inv_product_categories where (enabled_flag = '1') order by cat_name",
        "select distinct trim(to_char(subinv_id,'999999999999999999999999999999')) a, subinv_name b, '' c, org_id d from inv.inv_itm_subinventories where (enabled_flag = '1') order by subinv_name",
        "select distinct trim(to_char(item_type_id,'999999999999999999999999999999')) a, item_type_name b, '' c, org_id d from inv.inv_itm_type_templates where (is_tmplt_enabled_flag = '1') order by item_type_name",
        "select distinct trim(to_char(prchs_doc_hdr_id,'999999999999999999999999999999')) a, purchase_doc_num b, '' c, org_id d from scm.scm_prchs_docs_hdr where approval_status = 'Approved' order by purchase_doc_num",
        "select distinct trim(to_char(y.subinv_id,'999999999999999999999999999999')) a, y.subinv_name b, '' c, y.org_id d, trim(to_char(z.itm_id,'999999999999999999999999999999')) e from inv.inv_itm_subinventories y, inv.inv_stock z " +
        " where y.subinv_id = z.subinv_id and to_date(z.start_date,'YYYY-MM-DD') <= now()::Date and (to_date(z.end_date,'YYYY-MM-DD') >= now()::Date or end_date = '')  order by 2",
        "", "",
        "select distinct trim(to_char(y.shelf_id,'999999999999999999999999999999')) a, (SELECT pssbl_value ||' ('||pssbl_value_desc||') ' FROM gst.gen_stp_lov_values
WHERE pssbl_value_id = y.shelf_id) b, '' c, store_id d from inv.inv_shelf y order by 1",
        "select distinct trim(to_char(uom_id,'999999999999999999999999999999')) a, uom_name b, '' c, org_id d from inv.unit_of_measure where (enabled_flag = '1') order by uom_name");

    $pssblVals = array(
        "4", "Retail Customer", "Retail Customer"
        , "4", "Wholesale customer", "Wholesale customer",
        "4", "Individual", "Individual Person"
        , "4", "Organisation", "Company/Organisation",
        "5", "Service Provider", "Service Provider"
        , "5", "Goods Provider", "Goods Provider",
        "5", "Service and Goods Provider", "Service and Goods Provider"
        , "5", "Consultant", "Consultant"
        , "5", "Training Provider", "Training Provider",
        "19", "Shelf 1A", "First Floor shelf A"
        , "19", "Shelf 1B", "First Floor shelf B",
        "19", "Shelf 1C", "First Floor shelf C"
        , "19", "Shelf 2A", "Second Floor shelf A",
        "19", "Shelf 2B", "Second Floor shelf B"
        , "19", "Shelf 2C", "Second Floor shelf C",
        "19", "Shelf 3A", "Third Floor shelf A"
        , "19", "Shelf 3B", "Third Floor shelf B"
        , "19", "Shelf 3C", "Third Floor shelf C"
        , "25", "Excellent", "In Execellent Condition"
        , "25", "Very Good", "In Very Good Condition"
        , "25", "Good", "In Good Condition"
        , "25", "Bad", "In Poor Condition"
        , "25", "Defective", "Defective"
        , "26", "Expired", "Expired"
        , "26", "Defective", "Defective"
        , "26", "Malfunctioning", "Malfunctioning"
        , "26", "Wrong Receipt", "Wrong Receipt"
        , "26", "Over Receipt", "Over Receipt");

    createSysLovs($sysLovs, $sysLovsDesc, $sysLovsDynQrys);
    createSysLovsPssblVals($pssblVals, $sysLovs);
}

$canview = test_prmssns($dfltPrvldgs[0], $ModuleName);

if ($lgn_num > 0 && $canview == true) {
    if ($pgNo == 0) {
        $cntent = "<div id='rho_form' style=\"min-height:150px;\">
            <form id='sysAdminForm' action='' method='post' accept-charset='UTF-8'>
                <fieldset style=\"padding:10px 20px 20px 20px;\">
                    <legend>   $mdlNm                
                    </legend>
                    <div style=\"margin-bottom:10px;\">
                    <h2>Welcome to the Stores And Inventory Manager Module</h2>
                    </div>                    
                    <div class='rho_form1' style=\"background-color:#e3e3e3;border: 1px solid #999;padding:10px;\" class=\"rho-postcontent rho-postcontent-0 clearfix\">                    
      <h5>FUNCTIONALITIES OF THE PHARMACY/STORES MODULE</h5>
      <p>This module automates the management of the Pharmacy and Clinic stores. The module has the ff areas:";

        $grpcntr = 0;
        for ($i = 0; $i < count($menuItems); $i++) {
            $No = $i + 1;
            if ($grpcntr == 0) {
                $cntent .= "<div style=\"float:left;\">"
                        . "<ul class=\"no_bullet\">";
            }
            $cntent .= "<li class=\"leaf\" style=\"background: url('cmn_images/start.png') no-repeat 3px 4px; background-size:20px 20px;\">"
                    . "<a onclick=\"modules('$mdlNm', $No);\">$menuItems[$i]</a></li>";

            if ($grpcntr == 2) {
                $cntent.= "</ul>"
                        . "</div>";
                $grpcntr = 0;
            } else {
                $grpcntr = $grpcntr + 1;
            }
        }

        $cntent.= "
      </p>
    </div>
    </fieldset>
            </form>
        </div>
<div id=\"mydialog\" title=\"Basic dialog\" style=\"display:none;\">
</div>";
        echo $cntent;
        echo "<script type=\"text/javascript\"> 
        load('$mdlNm');
              </script>";
    } else if ($pgNo == 1) {
        require "sales_itm_issues.php";
    } else if ($pgNo == 2) {
        require "prchsn.php";
    } else if ($pgNo == 3) {
        require "itm_lst.php";
    } else if ($pgNo == 4) {
        require "catgs.php";
    } else if ($pgNo == 5) {
        require "stores.php";
    } else if ($pgNo == 6) {
        require "rcpt.php";
    } else if ($pgNo == 7) {
        require "rcpt_rtns.php";
    } else if ($pgNo == 8) {
        require "itm_def_tmplts.php";
    } else if ($pgNo == 9) {
        require "gl_intfc.php";
    } else if ($pgNo == 10) {
        require "bals.php";
    } else if ($pgNo == 11) {
        require "uom.php";
    } else if ($pgNo == 13) {
        echo "<p style=\"font-size:12px;\">calling functions for checking and creating requirements</p>";
        loadInvRqrmnts();
    } else {
        restricted();
    }
} else {
    restricted();
}

if ($vwtyp1 <= 100) {
    require $base_dir . 'rho_footer.php';
}
?>
