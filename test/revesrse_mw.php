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
$sql = "SELECT u.id,t.id AS t_id,u.mobile,u.name,t.amount,u.worked_days,u.duration,u.monthly_wallet FROM `users`u,`transactions`t WHERE u.id = t.user_id AND u.level = 1 AND u.plan = 50 AND worked_days < 50 AND t.type = 'monthly_wallet' AND DATE(t.datetime) = '2023-10-18' AND u.id != 58467";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {

    foreach ($res as $row) {
        $user_id = $row['id'];
        $amount = $row['amount'];
        $sql = "UPDATE users SET balance= balance + $amount,earn = earn + $amount,monthly_wallet = monthly_wallet - $monthly_wallet,old_monthly_wallet = 0,monthly_wallet_status = 0 WHERE id=" . $user_id;
        $db->sql($sql);
        $sql = "SELECT id FROM `users` WHERE referred_by = '$refer_code' AND status = 1 ORDER BY joined_date DESC LIMIT $current_refers";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        if($num >= 1){
            foreach ($res as $row) {
                $ID = $row['id'];


            }
        }


    }
    $response['success'] = true;
    $response['message'] = "refer added";
    
    print_r(json_encode($response));

}else{
    $response['success'] = false;
    $response['message'] = "No Results Found";
    print_r(json_encode($response));

}




?>