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
$sql = "SELECT id,refer_code,l_referral_count FROM users WHERE l_referral_count != 0 AND project_type = 'champion' AND status = 1";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {

    foreach ($res as $row) {
        $user_id = $row['id'];
        $refer_code = $row['refer_code'];
        $l_referral_count = $row['l_referral_count'];
        $sql = "SELECT id,project_type FROM `users` WHERE referred_by = '$refer_code' AND status = 1 ORDER BY joined_date DESC LIMIT $l_referral_count";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        if($num >= 1){
            foreach ($res as $row) {
                $ID = $row['id'];
                $project_type = $row['project_type'];
                if($project_type == 'champion'){
                    $pre_bonus = 1000;
                }else{
                    $pre_bonus = 500;
                }
                $sql_query = "INSERT INTO champion_refer_bonus (user_id,refer_user_id,status,amount,datetime)VALUES($user_id,$ID,0,$pre_bonus,'$datetime')";
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