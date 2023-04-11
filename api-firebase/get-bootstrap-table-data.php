<?php
session_start();

// set time for session timeout
$currentTime = time() + 25200;
$expired = 3600;

// if session not set go to login page
if (!isset($_SESSION['username'])) {
    header("location:index.php");
}

// if current time is more than session timeout back to login page
if ($currentTime > $_SESSION['timeout']) {
    session_destroy();
    header("location:index.php");
}

// destroy previous session timeout and create new one
unset($_SESSION['timeout']);
$_SESSION['timeout'] = $currentTime + $expired;

header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


include_once('../includes/custom-functions.php');
$fn = new custom_functions;
include_once('../includes/crud.php');
include_once('../includes/variables.php');
include_once('../includes/functions.php');
$fnc = new functions;
$db = new Database();
$db->connect();
$currentdate = date('Y-m-d');
if (isset($config['system_timezone']) && isset($config['system_timezone_gmt'])) {
    date_default_timezone_set($config['system_timezone']);
    $db->sql("SET `time_zone` = '" . $config['system_timezone_gmt'] . "'");
} else {
    date_default_timezone_set('Asia/Kolkata');
    $db->sql("SET `time_zone` = '+05:30'");
}
if (isset($_GET['table']) && $_GET['table'] == 'users') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if ((isset($_GET['date'])  && $_GET['date'] != '')) {
        $date = $db->escapeString($fn->xss_clean($_GET['date']));
        $where .= "AND u.joined_date='$date' AND u.status=1 ";
    }
    if ((isset($_GET['activeusers'])  && $_GET['activeusers'] != '')) {
        $where .= "AND u.status=1 AND u.today_codes != 0 AND u.total_codes != 0 AND DATE(u.last_updated) = '$currentdate' ";
    }
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND u.name like '%" . $search . "%' OR u.mobile like '%" . $search . "%' OR u.city like '%" . $search . "%' OR u.email like '%" . $search . "%' OR u.refer_code like '%" . $search . "%' OR u.registered_date like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }


    
    
    if($_SESSION['role'] == 'Super Admin'){
        $join = "LEFT JOIN `branches` b ON u.branch_id = b.id LEFT JOIN `employees` e ON u.lead_id = e.id LEFT JOIN `employees` s ON u.support_id = s.id WHERE u.id IS NOT NULL";
    }
    else{
        $refer_code = $_SESSION['refer_code'];
        $join = "LEFT JOIN `branches` b ON u.branch_id = b.id LEFT JOIN `employees` e ON u.lead_id = e.id LEFT JOIN `employees` s ON u.support_id = s.id WHERE u.refer_code REGEXP '^$refer_code' ";
    }
    $sql = "SELECT COUNT(u.id) as total FROM `users` u $join " . $where . "";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

        
        
    $sql = "SELECT u.id AS id,u.*,u.name AS name,u.mobile AS mobile,DATEDIFF( '$currentdate',u.joined_date) AS history,e.name AS support_name,s.name AS lead_name,b.name AS branch_name FROM `users` u $join 
                $where ORDER BY $sort $order LIMIT $offset, $limit";
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $user_id = $row['id'];
        $history_days = $fnc->get_leave($user_id);
        $row['history'] = $history_days;
        $operate = '<a href="edit-user.php?id=' . $row['id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-user.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['registered_date'] = $row['registered_date'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['password'] = $row['password'];
        $tempRow['dob'] = $row['dob'];
        $tempRow['email'] = $row['email'];
        $tempRow['city'] = $row['city'];
        $tempRow['device_id'] = $row['device_id'];
        $tempRow['refer_code'] = $row['refer_code'];
        $tempRow['referred_by'] = $row['referred_by'];
        $tempRow['earn'] = $row['earn'];
        $tempRow['total_referrals'] = $row['total_referrals'];
        $tempRow['today_codes'] = $row['today_codes'];
        $tempRow['total_codes'] = $row['total_codes'];
        $tempRow['balance'] = $row['balance'];
        $tempRow['history'] = $row['history'];
        $tempRow['ongoing_sa_balance'] = $row['ongoing_sa_balance'];
        $tempRow['salary_advance_balance'] = $row['salary_advance_balance'];
        $tempRow['sa_refer_count'] = $row['sa_refer_count'];
        $tempRow['withdrawal'] = $row['withdrawal'];
        $tempRow['support'] = $row['support_name'];
        $tempRow['lead'] = $row['lead_name'];
        $tempRow['branch'] = $row['branch_name'];
        $tempRow['refund_wallet'] = $row['refund_wallet'];
        $tempRow['total_refund'] = $row['total_refund'];
        $tempRow['trial_wallet'] = $row['trial_wallet'];
        if($row['status']==0)
            $tempRow['status'] ="<label class='label label-default'>Not Verify</label>";
        elseif($row['status']==1)
            $tempRow['status']="<label class='label label-success'>Verified</label>";        
        else
            $tempRow['status']="<label class='label label-danger'>Blocked</label>";
        if($row['code_generate']==1)
            $tempRow['code_generate'] ="<p class='text text-success'>enabled</p>";
        else
            $tempRow['code_generate']="<p class='text text-danger'>disabled</p>";

        if($row['withdrawal_status']==1)
            $tempRow['withdrawal_status'] ="<p class='text text-success'>enabled</p>";
        else
            $tempRow['withdrawal_status']="<p class='text text-danger'>disabled</p>";
        $tempRow['operate'] = $operate;

        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
if (isset($_GET['table']) && $_GET['table'] == 'referral_users') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if ((isset($_GET['date'])  && $_GET['date'] != '')) {
        $date = $db->escapeString($fn->xss_clean($_GET['date']));
        $where .= "AND joined_date='$date' ";
    }
    if ((isset($_GET['activeusers'])  && $_GET['activeusers'] != '')) {
        $where .= "AND status=1 AND today_codes != 0 AND total_codes != 0 AND DATE(last_updated) = '$currentdate' ";
    }
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND name like '%" . $search . "%' OR mobile like '%" . $search . "%' OR city like '%" . $search . "%' OR email like '%" . $search . "%' OR refer_code like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }


    
    if($_SESSION['role'] == 'Super Admin'){
        $join = "WHERE sync_refer_wallet != 0";
    }
    else{
        $refer_code = $_SESSION['refer_code'];
        $join = "WHERE sync_refer_wallet != 0 AND  refer_code REGEXP '^$refer_code'";
    }
    $sql = "SELECT COUNT(`id`) as total FROM `users` $join " . $where;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

        
    $sql = "SELECT *,DATEDIFF( '$currentdate',joined_date) AS history FROM `users` $join " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . "," . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $operate = '<a href="edit-user.php?id=' . $row['id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-user.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['password'] = $row['password'];
        $tempRow['dob'] = $row['dob'];
        $tempRow['email'] = $row['email'];
        $tempRow['city'] = $row['city'];
        $tempRow['device_id'] = $row['device_id'];
        $tempRow['refer_code'] = $row['refer_code'];
        $tempRow['referred_by'] = $row['referred_by'];
        $tempRow['earn'] = $row['earn'];
        $tempRow['total_referrals'] = $row['total_referrals'];
        $tempRow['today_codes'] = $row['today_codes'];
        $tempRow['total_codes'] = $row['total_codes'];
        $tempRow['balance'] = $row['balance'];
        $tempRow['history'] = $row['history'];
        $tempRow['withdrawal'] = $row['withdrawal'];
        $tempRow['sync_refer_wallet'] = $row['sync_refer_wallet'];
        if($row['status']==0)
            $tempRow['status'] ="<label class='label label-default'>Not Verify</label>";
        elseif($row['status']==1)
            $tempRow['status']="<label class='label label-success'>Verified</label>";        
        else
            $tempRow['status']="<label class='label label-danger'>Blocked</label>";
        if($row['code_generate']==1)
            $tempRow['code_generate'] ="<p class='text text-success'>enabled</p>";
        else
            $tempRow['code_generate']="<p class='text text-danger'>disabled</p>";

        if($row['withdrawal_status']==1)
            $tempRow['withdrawal_status'] ="<p class='text text-success'>enabled</p>";
        else
            $tempRow['withdrawal_status']="<p class='text text-danger'>disabled</p>";
        $tempRow['operate'] = $operate;

        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
//champion users table goes here
if (isset($_GET['table']) && $_GET['table'] == 'champion_users') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if ((isset($_GET['date'])  && $_GET['date'] != '')) {
        $date = $db->escapeString($fn->xss_clean($_GET['date']));
        $where .= "AND joined_date='$date' ";
    }
    if ((isset($_GET['activeusers'])  && $_GET['activeusers'] != '')) {
        $where .= "AND status=1 AND today_codes != 0 AND total_codes != 0 AND DATE(last_updated) = '$currentdate' ";
    }
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND name like '%" . $search . "%' OR mobile like '%" . $search . "%' OR city like '%" . $search . "%' OR email like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }


    
    if($_SESSION['role'] == 'Super Admin'){
        $join = "WHERE id IS NOT NULL AND task_type='champion'";
    }
    else{
        $refer_code = $_SESSION['refer_code'];
        $join = "WHERE refer_code REGEXP '^$refer_code'";
    }
    $sql = "SELECT COUNT(`id`) as total FROM `users` $join " . $where;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

        
    $sql = "SELECT *,DATEDIFF( '$currentdate',joined_date) AS history FROM `users` $join " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . "," . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $operate = '<a href="edit-user.php?id=' . $row['id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-user.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['password'] = $row['password'];
        $tempRow['dob'] = $row['dob'];
        $tempRow['email'] = $row['email'];
        $tempRow['city'] = $row['city'];
        $tempRow['device_id'] = $row['device_id'];
        $tempRow['refer_code'] = $row['refer_code'];
        $tempRow['referred_by'] = $row['referred_by'];
        $tempRow['earn'] = $row['earn'];
        $tempRow['total_referrals'] = $row['total_referrals'];
        $tempRow['today_codes'] = $row['today_codes'];
        $tempRow['total_codes'] = $row['total_codes'];
        $tempRow['balance'] = $row['balance'];
        $tempRow['history'] = $row['history'];
        $tempRow['withdrawal'] = $row['withdrawal'];
        if($row['status']==0)
            $tempRow['status'] ="<label class='label label-default'>Not Verify</label>";
        elseif($row['status']==1)
            $tempRow['status']="<label class='label label-success'>Verified</label>";        
        else
            $tempRow['status']="<label class='label label-danger'>Blocked</label>";
        if($row['code_generate']==1)
            $tempRow['code_generate'] ="<p class='text text-success'>enabled</p>";
        else
            $tempRow['code_generate']="<p class='text text-danger'>disabled</p>";

        if($row['withdrawal_status']==1)
            $tempRow['withdrawal_status'] ="<p class='text text-success'>enabled</p>";
        else
            $tempRow['withdrawal_status']="<p class='text text-danger'>disabled</p>";
        if($row['champion_task_eligible']==1)
            $tempRow['champion_task_eligible'] ="<p class='text text-success'>Eligible</p>";
        else
            $tempRow['champion_task_eligible']="<p class='text text-danger'>Not-Eligible</p>";
         $tempRow['task_type'] = $row['task_type'];
         $tempRow['trial_count'] = $row['trial_count'];
        $tempRow['operate'] = $operate;

        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}


if (isset($_GET['table']) && $_GET['table'] == 'bank_details') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "WHERE u.name like '%" . $search . "%' OR b.account_num like '%" . $search . "%' OR b.holder_name like '%" . $search . "%' OR b.bank like '%" . $search . "%' OR b.branch like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $join = "LEFT JOIN `users` u ON b.user_id = u.id";

    $sql = "SELECT COUNT(b.id) as total FROM `bank_details` b $join " . $where . "";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT b.id AS id,b.*,u.name,u.mobile FROM `bank_details` b $join 
    $where ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $operate = ' <a href="edit-bank_detail.php?id=' . $row['id'] . '"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-bank_detail.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['account_num'] = $row['account_num'];
        $tempRow['holder_name'] = $row['holder_name'];
        $tempRow['bank'] = $row['bank'];
        $tempRow['branch'] = $row['branch'];
        $tempRow['ifsc'] = $row['ifsc'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
if (isset($_GET['table']) && $_GET['table'] == 'withdrawals') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'w.id';
    $order = 'DESC';
    if ((isset($_GET['user_id']) && $_GET['user_id'] != '')) {
        $user_id = $db->escapeString($fn->xss_clean($_GET['user_id']));
        $where .= "AND w.user_id = '$user_id'";
    }
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND u.mobile like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    if($_SESSION['role'] == 'Super Admin'){
        $join = "WHERE w.user_id = u.id AND w.user_id = b.user_id ";

    }
    else{
        $refer_code = $_SESSION['refer_code'];
        $join = "WHERE w.user_id = u.id AND w.user_id = b.user_id AND u.refer_code REGEXP '^$refer_code'";
    }
    
    // Calculate the date 7 days ago
    $seven_days_ago = date('Y-m-d', strtotime('-7 days'));
    $where .= " AND w.datetime >= '$seven_days_ago' ";

    $sql = "SELECT COUNT(w.id) as total FROM `withdrawals` w,`users` u,`bank_details` b $join ". $where ."";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $sql = "SELECT w.id AS id,w.*,w.withdrawal_type,u.name,u.total_codes,u.total_referrals,u.balance,u.mobile,u.referred_by,u.refer_code,DATEDIFF( '$currentdate',u.joined_date) AS history,b.branch,b.bank,b.account_num,b.ifsc,b.holder_name FROM `withdrawals` w,`users` u,`bank_details` b $join
                        $where ORDER BY $sort $order LIMIT $offset, $limit";
             $db->sql($sql);
        }
        else{
            $sql = "SELECT w.id AS id,w.*,w.withdrawal_type,u.name,u.total_codes,u.total_referrals,u.balance,u.mobile,u.referred_by,u.refer_code,DATEDIFF( '$currentdate',u.joined_date) AS history,b.branch,b.bank,b.account_num,b.ifsc,b.holder_name FROM `withdrawals` w,`users` u,`bank_details` b $join
                    AND w.status=0 $where ORDER BY $sort $order LIMIT $offset, $limit";
             $db->sql($sql);
        }
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        // $operate = ' <a class="text text-danger" href="delete-withdrawal.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        // $operate .= ' <a href="edit-withdrawal.php?id=' . $row['id'] . '"><i class="fa fa-edit"></i>Edit</a>';
        $refer_refund=$row['total_referrals'] *250;
        $code_refund=($row['total_codes']/3000)*100;
        $total_refund= $refer_refund +  $code_refund;
        $total_refund = number_format($total_refund, 2);
        $checkbox = '<input type="checkbox" name="enable[]" value="'.$row['id'].'">';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['amount'] = $row['amount'];
        $tempRow['datetime'] = $row['datetime'];
        $tempRow['account_num'] = ','.$row['account_num'].',';
        $tempRow['holder_name'] = $row['holder_name'];
        $tempRow['withdrawal_type'] = $row['withdrawal_type'];
        $tempRow['bank'] = $row['bank'];
        $tempRow['branch'] = $row['branch'];
        $tempRow['total_codes'] = $row['total_codes'];
        $tempRow['total_referrals'] = $row['total_referrals'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['balance'] = $row['balance'];
        $tempRow['referred_by'] = $row['referred_by'];
        $tempRow['refer_code'] = $row['refer_code'];
        $tempRow['history'] = $row['history'];
        $tempRow['ifsc'] = $row['ifsc'];
        $tempRow['column'] = $checkbox;
        $tempRow['total_refund'] = $total_refund;
        if($row['status']==1)
            $tempRow['status'] ="<p class='text text-success'>Paid</p>";
        elseif($row['status']==0)
            $tempRow['status']="<p class='text text-primary'>Unpaid</p>";
        else
            $tempRow['status']="<p class='text text-danger'>Cancelled</p>";
        // $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

//transactions table goes here
if (isset($_GET['table']) && $_GET['table'] == 'transactions') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));
    if (isset($_GET['type']) && !empty($_GET['type']))
        $type = $db->escapeString($fn->xss_clean($_GET['type']));
        $where .= "AND t.type = '$type' ";

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND u.name like '%" . $search . "%' OR t.amount like '%" . $search . "%' OR t.id like '%" . $search . "%'  OR t.type like '%" . $search . "%' OR u.mobile like '%" . $search . "%' OR u.task_type like '%" . $search . "%' ";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $join = "LEFT JOIN `users` u ON t.user_id = u.id WHERE t.id IS NOT NULL ";

    $sql = "SELECT COUNT(t.id) as total FROM `transactions` t $join " . $where . "";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT t.id AS id,t.*,u.name,u.mobile,u.task_type FROM `transactions` t $join 
    $where ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['amount'] = $row['amount'];
        $tempRow['codes'] = $row['codes'];
        $tempRow['datetime'] = $row['datetime'];
        $tempRow['type'] = $row['type'];
        $tempRow['task_type'] = $row['task_type'];
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

if (isset($_GET['table']) && $_GET['table'] == 'notifications') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "WHERE title like '%" . $search . "%' OR description like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $sql = "SELECT COUNT(`id`) as total FROM `notifications`" . $where;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT * FROM notifications " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . "," . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $operate = ' <a class="text text-danger" href="delete-notification.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['title'] = $row['title'];
        $tempRow['description'] = $row['description'];
        $tempRow['datetime'] = $row['datetime'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

//admins table goes here
if (isset($_GET['table']) && $_GET['table'] == 'admin') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "WHERE name like '%" . $search . "%' OR email like '%" . $search . "%' OR role like '%" . $search . "%' OR status like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $sql = "SELECT COUNT(`id`) as total FROM `admin`" . $where;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT * FROM admin " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . "," . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $operate = '<a href="edit-admin.php?id=' . $row['id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-admin.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['password'] = $row['password'];
        $tempRow['role'] = $row['role'];
        $tempRow['email'] = $row['email'];
        $tempRow['refer_code'] = $row['refer_code'];
        if($row['status']==0)
            $tempRow['status'] ="<label class='label label-danger'>Deactive</label>";
        else
            $tempRow['status']="<label class='label label-success'>Active</label>";
        $tempRow['operate'] = $operate;

        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

if (isset($_GET['table']) && $_GET['table'] == 'manage_devices') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'dq.id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND name like '%" . $search . "%' OR mobile like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $join = "WHERE u.id = dq.user_id ";
    $sql = "SELECT COUNT(*) as total FROM users u,device_requests dq $join " ;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];


    $sql = "SELECT *,dq.id AS id,dq.device_id AS device_id FROM users u,device_requests dq $join " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . "," . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $operate = ' <a class="btn btn-success" href="verify-device.php?id=' . $row['id'] . '">Verify</a>';

        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['device_id'] = $row['device_id'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

if (isset($_GET['table']) && $_GET['table'] == 'search_withdrawals') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'w.id';
    $order = 'DESC';
    if ((isset($_GET['mobile']) && $_GET['mobile'] != '')) {
        $mobile = $db->escapeString($fn->xss_clean($_GET['mobile']));
        $where .= "AND u.mobile='$mobile' ";
    }
    if ((isset($_GET['status'])  && $_GET['status'] != '')) {
        $status = $db->escapeString($fn->xss_clean($_GET['status']));
        $where .= "AND w.status='$status' ";
    }
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND u.mobile like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $join = "WHERE w.user_id = u.id AND w.user_id = b.user_id ";

    $sql = "SELECT COUNT(w.id) as total FROM `withdrawals` w,`users` u,`bank_details` b $join ". $where ."";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT w.id AS id,w.*,w.withdrawal_type,u.name,u.total_codes,u.total_referrals,u.balance,u.mobile,u.referred_by,u.refer_code,DATEDIFF( '$currentdate',u.joined_date) AS history,b.branch,b.bank,b.account_num,b.ifsc,b.holder_name FROM `withdrawals` w,`users` u,`bank_details` b $join
    $where ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        // $operate = ' <a class="text text-danger" href="delete-withdrawal.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        // $operate .= ' <a href="edit-withdrawal.php?id=' . $row['id'] . '"><i class="fa fa-edit"></i>Edit</a>';
        $checkbox = '<input type="checkbox" name="enable[]" value="'.$row['id'].'">';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['amount'] = $row['amount'];
        $tempRow['datetime'] = $row['datetime'];
        $tempRow['account_num'] = ','.$row['account_num'].',';
        $tempRow['holder_name'] = $row['holder_name'];
        $tempRow['withdrawal_type'] = $row['withdrawal_type'];
        $tempRow['bank'] = $row['bank'];
        $tempRow['branch'] = $row['branch'];
        $tempRow['total_codes'] = $row['total_codes'];
        $tempRow['total_referrals'] = $row['total_referrals'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['balance'] = $row['balance'];
        $tempRow['referred_by'] = $row['referred_by'];
        $tempRow['refer_code'] = $row['refer_code'];
        $tempRow['history'] = $row['history'];
        $tempRow['ifsc'] = $row['ifsc'];
        $tempRow['column'] = $checkbox;
        if($row['status']==1)
            $tempRow['status'] ="<p class='text text-success'>Paid</p>";
        elseif($row['status']==0)
            $tempRow['status']="<p class='text text-primary'>Unpaid</p>";
        else
            $tempRow['status']="<p class='text text-danger'>Cancelled</p>";
        // $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

if (isset($_GET['table']) && $_GET['table'] == 'system-users') {

    $offset = 0;
    $limit = 10;
    $sort = 'id';
    $order = 'ASC';
    $where = '';
    $condition = '';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && $_GET['search'] != '') {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where = " Where `id` like '%" . $search . "%' OR `username` like '%" . $search . "%' OR `email` like '%" . $search . "%' OR `role` like '%" . $search . "%' OR `date_created` like '%" . $search . "%'";
    }
    if ($_SESSION['role'] != 'Super Admin') {
        if (empty($where)) {
            $condition .= ' where created_by=' . $_SESSION['id'];
        } else {
            $condition .= ' and created_by=' . $_SESSION['id'];
        }
    }

    $sql = "SELECT COUNT(id) as total FROM `admin`" . $where . "" . $condition;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT * FROM `admin`" . $where . "" . $condition . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . ", " . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();

    foreach ($res as $row) {
        if ($row['created_by'] != 0) {
            $sql = "SELECT username FROM admin WHERE id=" . $row['created_by'];
            $db->sql($sql);
            $created_by = $db->getResult();
        }

        if ($row['role'] != 'Super Admin') {
            $operate = "<a class='btn btn-xs btn-primary edit-system-user' data-id='" . $row['id'] . "' data-toggle='modal' data-target='#editSystemUserModal' title='Edit'><i class='fa fa-pencil-square-o'></i></a>";
            $operate .= " <a class='btn btn-xs btn-danger delete-system-user' data-id='" . $row['id'] . "' title='Delete'><i class='fa fa-trash-o'></i></a>";
        } else {
            $operate = '';
        }
        if ($row['role'] == 'Super Admin') {
            $role = '<span class="label label-success">Super Admin</span>';
        }
        if ($row['role'] == 'Admin') {
            $role = '<span class="label label-primary">Admin</span>';
        }
        if ($row['role'] == 'editor') {
            $role = '<span class="label label-warning">Editor</span>';
        }
        $tempRow['id'] = $row['id'];
        $tempRow['username'] = $row['username'];
        $tempRow['email'] = $row['email'];
        $tempRow['password'] = $row['password'];
        $tempRow['refer_code'] = $row['refer_code'];
        $tempRow['permissions'] = $row['permissions'];
        $tempRow['role'] = $role;
        $tempRow['created_by_id'] = $row['created_by'] != 0 ? $row['created_by'] : '-';
        $tempRow['created_by'] = $row['created_by'] != 0 ? $created_by[0]['username'] : '-';
        $tempRow['date_created'] = date('d-m-Y h:i:sa', strtotime($row['date_created']));
        $tempRow['operate'] = $operate;

        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}


//urls table goes here
if (isset($_GET['table']) && $_GET['table'] == 'urls') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "WHERE url like '%" . $search . "%' OR id like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $sql = "SELECT COUNT(`id`) as total FROM `urls`" . $where;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT * FROM urls " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . "," . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $operate = ' <a class="text text-danger" href="delete-url.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['url'] = $row['url'];
        $tempRow['destination_url'] = $row['destination_url'];
        $tempRow['codes'] = $row['codes'];
        $tempRow['views'] =$row['views'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

//valid_urls table goes here
if (isset($_GET['table']) && $_GET['table'] == 'valid_urls') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "WHERE url like '%" . $search . "%' OR id like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $sql = "SELECT COUNT(`id`) as total FROM `valid_urls`" . $where;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT * FROM valid_urls " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . "," . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $operate = ' <a class="text text-danger" href="delete-valid_url.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['url'] = $row['url'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}


//task champions table goes here
if (isset($_GET['table']) && $_GET['table'] == 'task_champions') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "WHERE user_id like '%" . $search . "%' OR id like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $sql = "SELECT COUNT(`id`) as total FROM `task_champions`" . $where;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT * FROM task_champions " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . "," . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $operate = ' <a class="text text-danger" href="delete-champion.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['user_id'] = $row['user_id'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}


//user reports table goes here
if (isset($_GET['table']) && $_GET['table'] == 'user_reports') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if ((isset($_GET['date'])  && $_GET['date'] != '')) {
        $date = $db->escapeString($fn->xss_clean($_GET['date']));
        $where .= "AND joined_date='$date' ";
    }
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND u.name like '%" . $search . "%' OR u.mobile like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }

    $sql = "SELECT COUNT(`id`) as total FROM `users`" . $where;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

        
    $sql = "SELECT u.id AS id,u.mobile,u.name,AVG(t.codes) AS avg_codes FROM `transactions` t JOIN `users` u ON t.user_id = u.id WHERE DATE(t.datetime) = '2023-01-26' AND t.datetime BETWEEN '2023-01-26 11:00:00' AND '2023-01-26 17:00:00' AND t.type = 'generate' GROUP BY t.user_id ORDER BY `avg_codes` DESC";
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        // $operate = '<a href="edit-user.php?id=' . $row['u.id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';
        // $operate .= ' <a class="text text-danger" href="delete-user.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['avg_codes'] = $row['avg_codes'];
        // $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}


//Join reports table goes here
if (isset($_GET['table']) && $_GET['table'] == 'join_reports') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['month']) && !empty($_GET['month'] != '')){
        $month = $db->escapeString($fn->xss_clean($_GET['month']));
        $where .= "AND MONTH(joined_date) = '$month' ";  
    }
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND u.joined_date like '%" . $search . "%'";
    }
    $join = "WHERE id IS NOT NULL ";

    $sql = "SELECT COUNT(`id`) as total FROM `users` u LIMIT 31 $join " . $where . "";
    $db->sql($sql);
    $res = $db->getResult();
    $total = '10';

    $sql = "SELECT joined_date FROM `users` $join $where GROUP BY joined_date ORDER BY $sort $order LIMIT " . intval($offset) . ", " . intval($limit);

    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    foreach ($res as $row) {
        
        $joindate = $row['joined_date'];
        $sql = "SELECT COUNT(joined_date) AS join_count FROM `users`WHERE joined_date = '$joindate'";
        $db->sql($sql);
        $res = $db->getResult();
        $tempRow['total_registrations'] = $res[0]['join_count'];
        $sql = "SELECT SUM(amount) AS total_with FROM `withdrawals`WHERE DATE(datetime) = '$joindate' AND status = 1";
        $db->sql($sql);
        $res = $db->getResult();
        $tempRow['paid_withdrawals'] = $res[0]['total_with'];
        $tempRow['date'] = $row['joined_date'];
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

//Month Join reports table goes here
if (isset($_GET['table']) && $_GET['table'] == 'month_join_reports') {
    if (isset($_GET['offset']))
         $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND MONTH(u.joined_date) = '" . $search . "'";
    }

    $sql = "SELECT COUNT(u.id) as total FROM `users` u LIMIT 31";
    $db->sql($sql);
    $res = $db->getResult();
    $total = '10';

    $sql = "SELECT MONTH(joined_date) as month, YEAR(joined_date) as year FROM `users` GROUP BY YEAR(joined_date), MONTH(joined_date) ORDER BY joined_date DESC LIMIT 31";
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    foreach ($res as $row) {
        
        $month = $row['month'];
        $year = $row['year'];
        $sql = "SELECT COUNT(joined_date) AS join_count FROM `users` WHERE MONTH(joined_date) = '$month' AND YEAR(joined_date) = '$year'";
        $db->sql($sql);
        $res = $db->getResult();
        $tempRow['total_registrations'] = $res[0]['join_count'];
        $sql = "SELECT SUM(amount) AS total_with FROM `withdrawals` WHERE MONTH(datetime) = '$month' AND YEAR(datetime) = '$year' AND status = 1";
        $db->sql($sql);
        $res = $db->getResult();
        $tempRow['paid_withdrawals'] = $res[0]['total_with'];
        $tempRow['date'] = date("F Y", strtotime("$year-$month-01"));
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));

}



// data of 'Top Coders' table goes here
if (isset($_GET['table']) && $_GET['table'] == 'top_coders') {

    $where = '';
    $offset = (isset($_GET['offset']) && !empty(trim($_GET['offset'])) && is_numeric($_GET['offset'])) ? $db->escapeString(trim($fn->xss_clean($_GET['offset']))) : 0;
    $limit = (isset($_GET['limit']) && !empty(trim($_GET['limit'])) && is_numeric($_GET['limit'])) ? $db->escapeString(trim($fn->xss_clean($_GET['limit']))) : 5;
    $sort = (isset($_GET['sort']) && !empty(trim($_GET['sort']))) ? $db->escapeString(trim($fn->xss_clean($_GET['sort']))) : 'today_codes';
    $order = (isset($_GET['order']) && !empty(trim($_GET['order']))) ? $db->escapeString(trim($fn->xss_clean($_GET['order']))) : 'DESC';
    $currentdate = $db->escapeString(trim($fn->xss_clean($_GET['current_date'])));
    // $currentdate = '2023-02-02';




    $sql = "SELECT COUNT(users.id) AS total, users.name, SUM(transactions.codes) AS today_codes,users.joined_date,users.mobile
    FROM users
    JOIN transactions ON users.id = transactions.user_id WHERE DATE(transactions.datetime) = '$currentdate' AND transactions.type = 'generate'
    GROUP BY users.id";
    $db->sql($sql);
    $res = $db->getResult();
    $total = $db->numRows($res);
    $sql = "SELECT users.id,users.task_type,users.name, SUM(transactions.codes) AS today_codes,SUM(transactions.amount) AS earn,users.joined_date,users.mobile,users.total_referrals,users.earn AS total_earn
    FROM users
    JOIN transactions ON users.id = transactions.user_id WHERE DATE(transactions.datetime) = '$currentdate' AND transactions.type = 'generate'
    GROUP BY users.id ORDER BY today_codes DESC LIMIT " . $offset . "," . $limit;
    $db->sql($sql);
    $res = $db->getResult();
    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    $i = 1;
    foreach ($res as $row) {

        // $operate = '<a href="users.php"><i class="fa fa-eye"></i>View </a>';
        $tempRow['id'] = $i;
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['today_codes'] = $row['today_codes'];
        $tempRow['task_type'] = $row['task_type'];
        $tempRow['earn'] = $row['earn'];
        $tempRow['total_earn'] = $row['total_earn'];
        $tempRow['joined_date'] = $row['joined_date'];
        $tempRow['total_referrals'] = $row['total_referrals'];
        // $tempRow['operate'] = $operate;
        $i++;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
//faq table goes here
if (isset($_GET['table']) && $_GET['table'] == 'faq') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "WHERE question like '%" . $search . "%' OR id like '%" . $search . "%' OR answer like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $sql = "SELECT COUNT(`id`) as total FROM `faq`" . $where;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT * FROM faq " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . "," . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $operate = '<a href="edit-faq.php?id=' . $row['id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-faq.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['question'] = $row['question'];
        $tempRow['answer'] = $row['answer'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

//employees table goes here
if (isset($_GET['table']) && $_GET['table'] == 'employees') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    // if (isset($_GET['type']) && !empty($_GET['type'])){
    //     $type = $db->escapeString($fn->xss_clean($_GET['type']));
    //     $where .= "AND t.type = '$type' ";
    // }
      
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND e.name like '%" . $search . "%' OR e.mobile like '%" . $search . "%' OR e.password like '%" . $search . "%'  OR e.email like '%" . $search . "%' OR b.name like '%" . $search . "%' ";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $join = "LEFT JOIN `branches` b ON e.branch_id = b.id WHERE e.id IS NOT NULL ";

    $sql = "SELECT COUNT(e.id) as total FROM `employees` e $join " . $where . "";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT e.id AS id,e.*,b.name AS branch_name FROM `employees` e $join 
    $where ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
                $operate = '<a href="edit-employee.php?id=' . $row['id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';

        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['email'] = $row['email'];
        $tempRow['password'] = $row['password'];
        $tempRow['branch'] = $row['branch_name'];
 if($row['status']==1)
            $tempRow['status']="<label class='label label-success'>Active</label>";        
        else
            $tempRow['status']="<label class='label label-danger'>Inactive</label>";
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}


//salary advance Transactions table goes here
if (isset($_GET['table']) && $_GET['table'] == 'salary_advance_transactions') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    // if (isset($_GET['type']) && !empty($_GET['type'])){
    //     $type = $db->escapeString($fn->xss_clean($_GET['type']));
    //     $where .= "AND t.type = '$type' ";
    // }
      
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND u.name like '%" . $search . "%' OR t.amount like '%" . $search . "%' OR t.id like '%" . $search . "%'  OR t.type like '%" . $search . "%' OR u.mobile like '%" . $search . "%' ";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $join = "LEFT JOIN `users` u ON t.refer_user_id = u.id WHERE t.id IS NOT NULL ";

    $sql = "SELECT COUNT(t.id) as total FROM `salary_advance_trans` t $join " . $where . "";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT t.id AS id,t.*,u.name,u.mobile FROM `salary_advance_trans` t $join 
    $where ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['amount'] = $row['amount'];
        $tempRow['datetime'] = $row['datetime'];
        $tempRow['type'] = $row['type'];
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

//repayments table goes here
if (isset($_GET['table']) && $_GET['table'] == 'repayments') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if ((isset($_GET['user_id']) && $_GET['user_id'] != '')) {
        $user_id = $db->escapeString($fn->xss_clean($_GET['user_id']));
        $where .= "AND r.user_id = '$user_id'";
    }
      
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND u.name like '%" . $search . "%' OR r.amount like '%" . $search . "%' OR r.id like '%" . $search . "%'  OR r.due_date like '%" . $search . "%' OR u.mobile like '%" . $search . "%' ";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $join = "LEFT JOIN `users` u ON r.user_id = u.id WHERE r.id IS NOT NULL ";

    $sql = "SELECT COUNT(r.id) as total FROM `repayments` r $join " . $where . "";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT r.id AS id,r.*,u.name,u.mobile,r.status AS status FROM `repayments` r $join 
    $where ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        if($row['status']==0){
            $checkbox = '<input type="checkbox" name="enable[]" value="'.$row['id'].'">';
        }
        else{
            $checkbox = '';
        }
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['amount'] = $row['amount'];
        $tempRow['due_date'] = $row['due_date'];
        if($row['status']==1)
              $tempRow['status']="<p class='text text-success'>Paid</p>";        
       else
             $tempRow['status']="<p class='text text-danger'>Unpaid</p>";
        $tempRow['column'] = $checkbox;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

//leaves table goes here
if (isset($_GET['table']) && $_GET['table'] == 'leaves') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'date';
    $order = 'DESC';
    if ((isset($_GET['type']) && $_GET['type'] != '')) {
        $type = $db->escapeString($fn->xss_clean($_GET['type']));
        $where .= "AND l.type = '$type'";
    }
    if (isset($_GET['date']) && $_GET['date'] != '') {
        $date = $db->escapeString($fn->xss_clean($_GET['date']));
        $where .= " AND l.date = '$date'";
    }
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND u.name like '%" . $search . "%' OR l.reason like '%" . $search . "%' OR l.id like '%" . $search . "%'  OR l.date like '%" . $search . "%' OR u.mobile like '%" . $search . "%' OR l.type like '%" . $search . "%' ";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $join = "LEFT JOIN `users` u ON l.user_id = u.id WHERE l.id IS NOT NULL " . $where;

    $sql = "SELECT COUNT(l.id) AS total FROM `leaves` l " . $join;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];
   
     $sql = "SELECT l.id AS id,l.*,u.name,u.mobile,l.status AS status FROM `leaves` l " . $join . " ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
     $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {

        $operate = '<a href="edit-leave.php?id=' . $row['id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-leave.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['date'] = $row['date'];
        $tempRow['type'] = $row['type'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['reason'] = $row['reason'];
        if($row['status']==0){
            $tempRow['status']="<p class='text text-primary'>Pending</p>";        
        }
        elseif($row['status']==1){
            $tempRow['status']="<p class='text text-success'>Approved</p>";        
        }
        else{
            $tempRow['status']="<p class='text text-danger'>Not-Approved</p>";        
        }
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}


//ratings table goes here
if (isset($_GET['table']) && $_GET['table'] == 'ratings') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    // if ((isset($_GET['user_id']) && $_GET['user_id'] != '')) {
    //     $user_id = $db->escapeString($fn->xss_clean($_GET['user_id']));
    //     $where .= "AND r.user_id = '$user_id'";
    // }
      
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND u.name like '%" . $search . "%' OR r.description like '%" . $search . "%' OR r.id like '%" . $search . "%'  OR r.ticket_id like '%" . $search . "%' OR r.ratings like '%" . $search . "%' OR u.mobile like '%" . $search . "%' ";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $join = "LEFT JOIN `users` u ON r.user_id = u.id WHERE r.id IS NOT NULL ";

    $sql = "SELECT COUNT(r.id) as total FROM `ratings` r $join " . $where . "";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT r.id AS id,r.*,u.name AS name,u.mobile AS mobile FROM `ratings` r $join 
    $where ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {

        // $operate = '<a href="edit-leave.php?id=' . $row['id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';
        // $operate .= ' <a class="text text-danger" href="delete-leave.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['description'] = $row['description'];
        $tempRow['ratings'] = $row['ratings'];
        $tempRow['ticket_id'] = $row['ticket_id'];
        // $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

//branches table goes here
if (isset($_GET['table']) && $_GET['table'] == 'branches') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "WHERE name like '%" . $search . "%' OR short_code like '%" . $search . "%' OR min_withdrawal like '%" . $search . "%'";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $sql = "SELECT COUNT(`id`) as total FROM `branches`" . $where;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT * FROM branches " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . "," . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        
         $operate = '<a href="edit-branches.php?id=' . $row['id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-branches.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['short_code'] = $row['short_code'];
        $tempRow['min_withdrawal'] = $row['min_withdrawal'];
        if($row['trial_earnings']==1)
        $tempRow['trial_earnings'] ="<p class='text text-success'>enabled</p>";
        else
        $tempRow['trial_earnings']="<p class='text text-danger'>disabled</p>";
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}



$db->disconnect();
