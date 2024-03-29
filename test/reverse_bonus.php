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
$sql = "SELECT id,user_id,amount,datetime FROM`transactions`t WHERE type = 'bonus_wallet' AND DATE(datetime) = '2023-12-13' AND amount > 0";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {

    foreach ($res as $row) {
        $user_id = $row['user_id'];
        $trans_id = $row['id'];
        $bonus_wallet = -200;
        $sql = "UPDATE transactions SET amount = 500  WHERE id=" . $trans_id;
        $db->sql($sql);
        $sql = "UPDATE users SET balance= balance + $bonus_wallet,earn = earn + $bonus_wallet,bonus_wallet = bonus_wallet - $bonus_wallet WHERE id=" . $user_id;
        $db->sql($sql);

        // $sql = "UPDATE bonus_refer_bonus SET status = 0 WHERE user_id = $user_id ORDER BY ID LIMIT 1";
        // $db->sql($sql);



    }
    $response['success'] = true;
    $response['message'] = "main bonus wallet  added";
    
    print_r(json_encode($response));

}else{
    $response['success'] = false;
    $response['message'] = "No Results Found";
    print_r(json_encode($response));

}




?>