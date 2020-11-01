<?php

//require 'globals.php';
//require 'admin_funcs.php';
$usrID = $_SESSION['USRID'];

function get_Users_Roles1($searchFor, $searchIn, $offset, $limit_size) {
    global $usrID;
    $wherecls = "";
    if ($searchIn === "Role Name") {
        $wherecls = " AND (b.role_name ilike '%" . loc_db_escape_string($searchFor) . "%')"; //mb_convert_encoding(htmlentities(), "UTF-8", "ASCII"))
    }
    $sqlStr = "SELECT a.role_id mt, b.role_name, a.valid_start_date 
FROM sec.sec_users_n_roles a LEFT OUTER JOIN sec.sec_roles b ON (a.role_id = b.role_id) 
WHERE ((now() between to_timestamp(a.valid_start_date,'YYYY-MM-DD HH24:MI:SS') AND 
to_timestamp(a.valid_end_date,'YYYY-MM-DD HH24:MI:SS')) 
AND (now() between to_timestamp(b.valid_start_date,'YYYY-MM-DD HH24:MI:SS') AND " .
            "to_timestamp(b.valid_end_date,'YYYY-MM-DD HH24:MI:SS')) AND (a.user_id = " . $usrID .
            ")$wherecls) ORDER BY b.role_name LIMIT " . $limit_size . " OFFSET " . abs($offset * $limit_size);
//echo $sqlStr;
    $result = executeSQLNoParams($sqlStr);
    return $result;
}

function get_Ttl_Users_Roles1($searchFor, $searchIn) {
    global $usrID;
    $wherecls = "";
    if ($searchIn === "Role Name") {
        $wherecls = " AND (b.role_name ilike '%" . loc_db_escape_string($searchFor) . "%')"; //mb_convert_encoding(htmlentities(), "UTF-8", "ASCII"))
    }
    $sqlStr = "SELECT count(1)  
FROM sec.sec_users_n_roles a LEFT OUTER JOIN sec.sec_roles b ON (a.role_id = b.role_id) 
WHERE ((now() between to_timestamp(a.valid_start_date,'YYYY-MM-DD HH24:MI:SS') AND 
to_timestamp(a.valid_end_date,'YYYY-MM-DD HH24:MI:SS')) 
AND (now() between to_timestamp(b.valid_start_date,'YYYY-MM-DD HH24:MI:SS') AND " .
            "to_timestamp(b.valid_end_date,'YYYY-MM-DD HH24:MI:SS')) AND (a.user_id = " . $usrID .
            ")$wherecls)";
//echo $sqlStr;
    $result = executeSQLNoParams($sqlStr);
    while ($row = loc_db_fetch_array($result)) {
        return $row[0];
    }
    return 0;
}

if (array_key_exists('lgn_num', get_defined_vars())) {
    if ($lgn_num > 0) {
        $updtRoles = isset($_POST['uR']) ? $_POST['uR'] : 0;
        if ($updtRoles <= 0) {
            $srchFor = isset($_POST['query']) ? $_POST['query'] : "";
            $total = get_Ttl_Users_Roles1($srchFor, "Role Name");

            $pageNo = isset($_POST['page']) ? $_POST['page'] : 1;
            $lmtSze = isset($_POST['limit']) ? $_POST['limit'] : 1;
            $start = isset($_POST['start']) ? $_POST['start'] : 0;
            if ($pageNo > ceil($total / $lmtSze)) {
                $pageNo = 1;
            }

            $curIdx = $pageNo - 1;
            $result = get_Users_Roles1($srchFor, "Role Name", $curIdx, $lmtSze);

            $slctd = ";" . $_SESSION['ROLE_SET_IDS'] . ";";
            $roles = array();
//$statuses = array('Paid', 'Viewed', 'Draft', 'Partial');
            while ($row = loc_db_fetch_array($result)) {
                //$status = $statuses[rand(0, 3)];
                $chckd = FALSE;
                $posRole = strpos($slctd, ";" . $row[0] . ";");
                /* if ($posRole === FALSE || is_nan($posRole) == TRUE) {
                  $chckd = FALSE;
                  } else */
                if (is_int($posRole)) {
                    $chckd = TRUE;
                } else {
                    $chckd = FALSE;
                }
                /* if ($slctd !== ";;") { */
                //date_default_timezone_set("UTC");
                $role = array(
                    'checked' => var_export($chckd, TRUE),
                    'RoleID' => ($row[0]),
                    'NameofRole' => $row[1], //. "-" . $posRole . "-" . var_export($chckd, TRUE), //Role Name
                    'DateGranted' => $row[2]); //; gmdate('Y-m-d H:i:s', strtotime( . " UTC"))
                $roles[] = $role;
            }

            echo json_encode(array('success' => true, 'total' => $total,
                'rows' => $roles));
        } else {
            //Store Selected Roles in Session
            $selectedRoles = $_POST['selectedRoles'];
            //$in_org_id = $_POST['orgid'];
            //$in_org_nm = $_POST['orgnm'];
            $_SESSION['ROLE_SET_IDS'] = rtrim($selectedRoles, ";");
            //$_SESSION['ORG_NAME'] = $in_org_nm;
            //$_SESSION['ORG_ID'] = $in_org_id;
            echo "Load Inbox";
            //header('Location: index1.php?ajx=1&grp=1&typ=10&q=view');
            exit();
        }
    }
}
?>