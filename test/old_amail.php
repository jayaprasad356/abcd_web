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
$sql = "SELECT id,joined_date FROM `users` WHERE joined_date < '2023-09-27' AND status = 1 AND project_type = 'amail'";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {

    foreach ($res as $row) {
        $joined_date = $row['joined_date'];
        $user_id = $row['id'];
        $sql = "SELECT COUNT(id) AS total FROM `transactions` WHERE DATE(datetime) >= '$joined_date' AND DATE(datetime) < '2023-09-27' AND type = 'refer_bonus' AND user_id = $user_id";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        if($num == 1){
            $total = $res[0]['total'];
        
            $sql_query = "UPDATE users SET old_amail_refer = $total WHERE id = $user_id";
            $db->sql($sql_query);
            $result = $db->getResult();

        }


    }
    $response['success'] = true;
    $response['message'] = "user refers updated";
    
    print_r(json_encode($response));

}else{
    $response['success'] = false;
    $response['message'] = "No Results Found";
    print_r(json_encode($response));

}




?>