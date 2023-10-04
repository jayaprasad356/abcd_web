<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
date_default_timezone_set('Asia/Kolkata');

include_once('../includes/crud.php');

$db = new Database();
$db->connect();
$sql = "SELECT id FROM `users` WHERE support_id = 4 ";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {
    $staff_ids = [3, 5, 7, 9];
    $staff_count = count($staff_ids);
    $staff_index = 0;
    foreach ($res as $row) {
        $ID = $row['id'];
        $staff_id = $staff_ids[$staff_index];
    
        $sql_query = "UPDATE users SET support_id = $staff_id, lead_id = $staff_id WHERE id = $ID";
        $db->sql($sql_query);
    
        // Move to the next staff member in a round-robin fashion
        $staff_index = ($staff_index + 1) % $staff_count;
 
    }
    $response['success'] = true;
    $response['message'] = "Support Updated Successfully";
    print_r(json_encode($response));

}else{
    $response['success'] = false;
    $response['message'] = "No Results Found";
    print_r(json_encode($response));

}




?>