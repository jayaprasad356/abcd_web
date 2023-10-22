<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
date_default_timezone_set('Asia/Kolkata');
include_once('../includes/custom-functions.php');
$fn = new custom_functions;
include_once('../includes/crud.php');

$db = new Database();
$db->connect();
$currentdate = date('Y-m-d');
$datetime = date('Y-m-d H:i:s');


$sql = "SELECT u.id,u.mobile,u.name,u.monthly_wallet,u.level,u.worked_days,u.duration,t.amount,t.datetime ,u.balance
FROM `users` u, `transactions` t 
WHERE u.id = t.user_id 
AND DATE(t.datetime) >= '2023-10-19' AND DATE(t.datetime) <= '2023-10-20' AND t.type = 'monthly_wallet' AND u.plan = 50 AND u.level <= 2 AND .u.worked_days < u.duration AND u.balance < 0";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {

    foreach ($res as $row) {
        $user_id = $row['id'];
        $amount = $row['amount'];
        //$amount = -$amount;
        $type = 'admin_credit_balance';
        $sql = "INSERT INTO transactions (`user_id`,`amount`,`datetime`,`type`)VALUES('$user_id','$amount','$datetime','$type')";
        $db->sql($sql);

        $sql = "UPDATE users SET balance= balance + $amount,earn = earn + $amount,monthly_wallet = monthly_wallet - $amount,monthly_wallet_status = 0 WHERE id=" . $user_id;
        $db->sql($sql);
        


    }
    $response['success'] = true;
    $response['message'] = "reverse new added";
    
    print_r(json_encode($response));

}else{
    $response['success'] = false;
    $response['message'] = "No Results Found";
    print_r(json_encode($response));

}




?>