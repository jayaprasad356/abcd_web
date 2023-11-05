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
include_once('../includes/functions.php');
$fnc = new functions;
include_once('../includes/crud.php');

$db = new Database();
$db->connect();
$currentdate = date('Y-m-d');
$datetime = date('Y-m-d H:i:s');
$sql = "SELECT id,monthly_wallet,reward_codes FROM `old_users` WHERE referred_by = 'rejoin' AND monthly_wallet_status = 0 AND worked_days < duration";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {

    foreach ($res as $row) {
        $user_id = $row['id'];
        $monthly_wallet = $row['monthly_wallet'];
        $reward_codes = $row['reward_codes'];
        $sql = "UPDATE `users` SET  `monthly_wallet` = monthly_wallet + $monthly_wallet,`reward_codes` = reward_codes + $reward_codes WHERE `id` = $user_id";
        $db->sql($sql);



    }
    $response['success'] = true;
    $response['message'] = "monthly wallet  added";
    
    print_r(json_encode($response));

}else{
    $response['success'] = false;
    $response['message'] = "No Results Found";
    print_r(json_encode($response));

}




?>