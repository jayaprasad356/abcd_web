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
$sql = "SELECT * FROM `withdrawals` WHERE status = 0 AND user_id = 43548";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {

    foreach ($res as $row) {
        $w_id = $row['id'];
        $user_id= $row['user_id'];
        $amount= $row['amount'];
        $sql = "UPDATE withdrawals SET status=2 WHERE id = $w_id";
        $db->sql($sql);


        $sql = "UPDATE users SET balance= balance + $amount,withdrawal = withdrawal - $amount WHERE id = $user_id";
        $db->sql($sql);
        
        $sql = "INSERT INTO transactions (user_id,amount,datetime,type) VALUES ('$user_id','$amount','$datetime','cancelled')";
        $db->sql($sql);


    }
    $response['success'] = true;
    $response['message'] = "balance minus";
    
    print_r(json_encode($response));

}else{
    $response['success'] = false;
    $response['message'] = "No Results Found";
    print_r(json_encode($response));

}




?>