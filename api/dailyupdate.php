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

include_once('../includes/functions.php');
$fn = new functions;
$currentdate = date('Y-m-d');
// $sql = "UPDATE users SET mcg_timer=40,code_generate_time = 5 WHERE task_type = 'champion' ";
// $db->sql($sql);

$sql = "UPDATE users SET mcg_timer=18,code_generate_time = 3";
$db->sql($sql);

$sql = "UPDATE users SET code_generate=0 WHERE total_codes >= 60000";
$db->sql($sql);

$sql = "UPDATE users SET code_generate_time = 4,mcg_timer = 22 WHERE DATEDIFF( '$currentdate',joined_date) >= 20 AND total_referrals = 0 ";
$db->sql($sql);

$sql = "UPDATE users SET code_generate_time = 4,mcg_timer = 20 WHERE referred_by LIKE '%rejoin%'";
$db->sql($sql);

$sql = "UPDATE users SET champion_task_eligible = 1 WHERE joined_date < DATE_SUB( '$currentdate', INTERVAL 15 DAY) AND status = 1 AND champion_task_eligible = 0 AND total_referrals = 0";
$db->sql($sql);

$sql = "UPDATE users SET code_generate = 0  WHERE worked_days = duration";
$db->sql($sql);

$sql = "UPDATE users SET referred_by = LEFT(refer_code , 3) WHERE DATEDIFF( '$currentdate',DATE(registered_date)) > 7 AND status = 0 AND LENGTH(referred_by) != 3 AND referred_by != ''";
$db->sql($sql);

$sql = "INSERT INTO join_reports (date, total_users,total_paid) SELECT joined_date, COUNT(id) AS total_users,(SELECT SUM(amount) FROM withdrawals WHERE DATE(datetime) = '$currentdate' AND status = 1) AS total_paid FROM users WHERE status = 1 AND joined_date = '$currentdate'";
$db->sql($sql);

?>