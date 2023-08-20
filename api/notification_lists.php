<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once('../includes/crud.php');
date_default_timezone_set('Asia/Kolkata');
$db = new Database();
$db->connect();
include_once('../includes/functions.php');
$fn = new functions;
$fn->monitorApi('notification_lists');
if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User Id is Empty";
    print_r(json_encode($response));
    return false;
}
$user_id = $db->escapeString($_POST['user_id']);
$sql = "SELECT project_type,status,joined_date FROM `users` WHERE id = $user_id ";
$db->sql($sql);
$res = $db->getResult();
$status = $res[0]['status'];
$joined_date = $res[0]['joined_date'];
$project_type = $res[0]['project_type'];
$expiry_date = '2023-02-06';

$joined_timestamp = strtotime($joined_date);
$expiry_timestamp = strtotime($expiry_date);
$join = '';
if($project_type == 'amail'){
    $join = "AND project_type = '$project_type'";

}
if($status == 0){
    $sql = "SELECT * FROM `notifications` WHERE send_to = 0 OR send_to = 1 $join ORDER BY id DESC LIMIT 20 ";

}
elseif($joined_timestamp >= $expiry_timestamp ||  $status == 0){
    $sql = "SELECT * FROM `notifications` WHERE send_to = 0 OR send_to = 2 OR send_to = 3 $join ORDER BY id DESC LIMIT 20 ";

}
else{
    $sql = "SELECT * FROM `notifications` WHERE send_to = 0 OR send_to = 2 $join ORDER BY id DESC LIMIT 20 ";


}

$db->sql($sql);
$res = $db->getResult();

$num = $db->numRows($res);
if ($num >= 1) {
    $response['success'] = true;
    $response['message'] = "Notification listed Successfully";
    $response['data'] = $res;
    print_r(json_encode($response));

}else{
    $response['success'] = false;
    $response['message'] = "No Data Found";
    print_r(json_encode($response));

}

?>