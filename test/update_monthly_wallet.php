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
$sql = "SELECT id,refer_code FROM users WHERE current_refers > 0 AND  current_refers < 4 AND project_type = 'amail' AND status = 1";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {

    foreach ($res as $row) {
        $user_id = $row['id'];
        $refer_code = $row['refer_code'];
        $sql = "SELECT id FROM `users` WHERE referred_by = '$refer_code' AND status = 1";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        if($num >= 1){
            foreach ($res as $row) {
                $ID = $row['id'];
                $sql_query = "INSERT INTO bonus_refer_bonus (user_id,refer_user_id,status,amount,datetime)VALUES($user_id,$ID,0,700,'$datetime')";
                $db->sql($sql_query);

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