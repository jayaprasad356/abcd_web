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
$sql = "SELECT id,mobile,l_referral_count,old_amail_refer FROM `users` WHERE status = 1 AND project_type = 'amail' AND l_referral_count > 0";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {

    foreach ($res as $row) {
        
        $ID = $row['id'];
        $old_amail_refer = $row['old_amail_refer'];
        $l_referral_count = $row['l_referral_count'];
        $mails = ($l_referral_count - $old_amail_refer) * 10;
        $type = 'mail_bonus';
        $sql = "INSERT INTO transactions (`user_id`,`mails`,`datetime`,`type`)VALUES('$ID','$mails','$datetime','$type')";
        $db->sql($sql);
        $res = $db->getResult();
    
        $sql = "UPDATE `users` SET  `total_mails` = total_mails + $mails WHERE `id` = $ID";
        $db->sql($sql);
        $result = $db->getResult();


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