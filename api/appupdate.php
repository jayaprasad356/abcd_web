<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once('../includes/crud.php');

$db = new Database();
$db->connect();
include_once('../includes/functions.php');
$fn = new functions;
include_once('../includes/custom-functions.php');
$fnc = new custom_functions;

$date = date('Y-m-d');
$datetime = date('Y-m-d H:i:s');
$old_device_id = (isset($_POST['device_id']) && $_POST['device_id'] != "") ? $db->escapeString($_POST['device_id']) : "";
$user_id = (isset($_POST['user_id']) && $_POST['user_id'] != "") ? $db->escapeString($_POST['user_id']) : "";
$fcm_id = (isset($_POST['fcm_id']) && $_POST['fcm_id'] != "") ? $db->escapeString($_POST['fcm_id']) : "";
$app_version = (isset($_POST['app_version']) && $_POST['app_version'] != "") ? $db->escapeString($_POST['app_version']) : 0;
$sql = "SELECT * FROM settings";
$db->sql($sql);
$set = $db->getResult();
$res = array();

$sql = "SELECT * FROM app_settings";
$db->sql($sql);
$appres = $db->getResult();
if($user_id != ''){
    $sql = "SELECT mobile,code_generate_time,total_referrals,withdrawal,last_updated,device_id,datediff('$date', joined_date) AS history_days,datediff('$datetime', last_updated) AS days,code_generate,withdrawal_status,status,joined_date,today_codes,refer_balance,trial_expired,task_type,champion_task_eligible,trial_count,mcg_timer,security,ongoing_sa_balance,salary_advance_balance,sa_refer_count,refund_wallet,total_refund,earn,refer_code,level,per_code_val,worked_days  FROM users WHERE id = $user_id ";
    $db->sql($sql);
    $res = $db->getResult();
    $history_days = $res[0]['history_days'];
    $device_id = $res[0]['device_id'];
    $today_codes = $res[0]['today_codes'];
    $task_type = $res[0]['task_type'];
    $code_generate_time = $res[0]['code_generate_time'];
    $res[0]['joined_date'] = $fn->get_joined_date($user_id);;


    $champion_task = $set[0]['champion_task'];
    if($res[0]['mobile'] == '7406051288'){
        $appres[0]['version'] = '41';

    }
    

    $sql = "UPDATE `users` SET  `app_version` = $app_version WHERE `id` = $user_id";
    $db->sql($sql);
    


    if(!empty($fcm_id)){
        $sql = "UPDATE `users` SET  `fcm_id` = '$fcm_id' WHERE `id` = $user_id";
        $db->sql($sql);
    
    }
    if(isset($_POST['device_id']) && ($device_id != $old_device_id)){
        $sql = "UPDATE `users` SET  `status` = 2 WHERE `id` = $user_id";
        $db->sql($sql);

    }





}

$response['success'] = true;
$response['message'] = "App Update listed Successfully";
$response['data'] = $appres;
$response['settings'] = $set;
$response['user_details'] = $res;
$response['customer_support_mobile'] = $fnc->get_branch_num($user_id);
print_r(json_encode($response));

?>