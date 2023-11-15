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
$sql = "SELECT * FROM `old_amail_users` WHERE updated = 0";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {

    foreach ($res as $row) {
        
        $mobile = $row['mobile'];
        $old_amail_refer = $row['old_amail_refer'];
        $old_worked_days = $row['old_worked_days'];
        $old_total_mails = $row['old_total_mails'];
    
        $sql = "UPDATE `users` SET  `old_amail_refer` = $old_amail_refer,`old_worked_days` = $old_worked_days,`old_total_mails` = $old_total_mails WHERE `mobile` = '$mobile'";
        $db->sql($sql);

        $sql = "UPDATE `old_amail_users` SET  `updated` = 1 WHERE `mobile` = '$mobile'";
        $db->sql($sql);


    }
    $response['success'] = true;
    $response['message'] = "old amail users done";
    print_r(json_encode($response));

}else{
    $response['success'] = false;
    $response['message'] = "No Results Found";
    print_r(json_encode($response));

}




?>