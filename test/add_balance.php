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
$sql = "SELECT t.user_id FROM `transactions` t,`users` u WHERE t.user_id = u.id AND u.project_type = 'amail' AND t.type = 'refer_bonus' AND DATE(t.datetime) = '2023-09-13'";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {

    foreach ($res as $row) {
        
        $user_id = $row['user_id'];
        $sql = "SELECT id FROM `users` WHERE id = $user_id";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        if($num == 1){
            $ID = $res[0]['id'];
            $balance = 100;
            $datetime = date('Y-m-d H:i:s');
            $type = 'admin_credit_balance';
            $sql = "INSERT INTO transactions (`user_id`,`amount`,`datetime`,`type`)VALUES('$ID','$balance','$datetime','$type')";
            $db->sql($sql);
            $sql_query = "UPDATE users SET balance=balance+ $balance WHERE id=$ID";
            $db->sql($sql_query);
            $result = $db->getResult();

        }


    }
    $response['success'] = true;
    $response['message'] = "balance added";
    
    print_r(json_encode($response));

}else{
    $response['success'] = false;
    $response['message'] = "No Results Found";
    print_r(json_encode($response));

}




?>