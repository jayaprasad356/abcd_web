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
$sql = "SELECT * FROM `bonus_codes`";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {

    foreach ($res as $row) {
        
        $mobile = $row['mobile'];
        $codes = $row['codes'];
        $sql = "SELECT id FROM `users` WHERE mobile = $mobile";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        if($num == 1){
            $ID = $res[0]['id'];
            //$codes = 200;
            $datetime = date('Y-m-d H:i:s');
            $type = 'code_bonus';
            $per_code_cost = $fn->get_code_per_cost($ID);
            $amount = $codes * $per_code_cost;
    
            $sql = "INSERT INTO transactions (`user_id`,`codes`,`amount`,`datetime`,`type`)VALUES('$ID','$codes','$amount','$datetime','$type')";
            $db->sql($sql);
            $res = $db->getResult();
        
            $sql = "UPDATE `users` SET  `today_codes` = today_codes + $codes,`total_codes` = total_codes + $codes,`earn` = earn + $amount,`balance` = balance + $amount WHERE `id` = $ID";
            $db->sql($sql);
             $result = $db->getResult();

        }


    }
    $response['success'] = true;
    $response['message'] = "refer added Bonus Done";
    
    print_r(json_encode($response));

}else{
    $response['success'] = false;
    $response['message'] = "No Results Found";
    print_r(json_encode($response));

}




?>