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
$sql = "SELECT id,total_codes,joined_date FROM `users` WHERE joined_date >= '2023-09-07' AND new_total_codes = 0 AND status = 1 AND code_generate = 1 AND project_type = 'abcd' AND level = 1 ORDER BY joined_date LIMIT 100";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {
    foreach ($res as $row) {
        
        $id = $row['id'];
        $total_codes = $row['total_codes'];
        $joined_date = $row['joined_date'];
        
        $sql = "SELECT SUM(codes) AS total_codes FROM `transactions` WHERE type = 'generate' AND user_id = $id AND DATE(datetime) >= '$joined_date' ";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        $g_total_codes = 0;
        if ($num >= 1) {
            $g_total_codes = $res[0]['total_codes'];
            $h_total_codes = $g_total_codes/2;
            $total_codes = $total_codes -  $h_total_codes;

        }
        $sql = "UPDATE `users` SET `new_total_codes` = $total_codes, `reward_codes` = $h_total_codes WHERE `id` = $id";
        $db->sql($sql);
        
 
    }
    $response['success'] = true;
    $response['message'] = "Codes Updated Successfully";
    print_r(json_encode($response));

}else{
    $response['success'] = false;
    $response['message'] = "No Results Found";
    print_r(json_encode($response));

}




?>