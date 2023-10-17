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
$sql = "SELECT id FROM users WHERE joined_date >= '2023-08-09' AND joined_date <= '2023-10-06' AND  status = 1 AND plan = 30 AND level = 1";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {

    foreach ($res as $row) {
        $user_id = $row['id'];
        $history_days = $fnc->get_leave_temp($user_id);
        $duration = 60 - (($history_days + 1) * 2);
        $sql = "UPDATE `users` SET  `new_duration` = $duration WHERE `id` = $user_id";
        $db->sql($sql);



    }
    $response['success'] = true;
    $response['message'] = "duration added";
    
    print_r(json_encode($response));

}else{
    $response['success'] = false;
    $response['message'] = "No Results Found";
    print_r(json_encode($response));

}




?>