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
$currentdate = date('Y-m-d');
$sql = "UPDATE users SET code_generate_time = 3";
$db->sql($sql);


$sql = "UPDATE users SET code_generate_time = 6 WHERE DATEDIFF( '$currentdate',joined_date) >= 15 AND total_referrals = 0";
$db->sql($sql);

$sql = "UPDATE users SET code_generate_time = 8 WHERE DATEDIFF( '$currentdate',joined_date) >= 30 AND total_referrals = 0";
$db->sql($sql);


$sql = "UPDATE users SET champion_task_eligible = 1 WHERE joined_date < DATE_SUB( '$currentdate', INTERVAL 30 DAY) AND status = 1 AND champion_task_eligible = 0";
$db->sql($sql);

$sql = "UPDATE users SET champion_task_eligible = 1 WHERE joined_date < DATE_SUB( '$currentdate', INTERVAL 15 DAY) AND status = 1 AND champion_task_eligible = 0 AND total_referrals = 0";
$db->sql($sql);

$sql = "UPDATE users SET mcg_timer=40 WHERE task_type = 'champion' ";
$db->sql($sql);

$sql = "UPDATE users SET mcg_timer=45 WHERE task_type = 'champion' AND DATEDIFF( '$currentdate',joined_date) >= 30 ";
$db->sql($sql);

$sql = "UPDATE users SET mcg_timer=15 WHERE task_type = 'regular'";
$db->sql($sql);

$sql = "UPDATE users SET mcg_timer=60 WHERE task_type = 'champion' AND DATEDIFF( '$currentdate',joined_date) >= 30 AND total_referrals = 0";
$db->sql($sql);

$sql = "UPDATE users SET mcg_timer=50 WHERE task_type = 'champion' AND DATEDIFF( '$currentdate',joined_date) >= 15 AND total_referrals = 0";
$db->sql($sql);

$sql = "UPDATE users SET mcg_timer=20 WHERE task_type = 'regular' AND DATEDIFF( '$currentdate',joined_date) >= 30";
$db->sql($sql);

$sql = "UPDATE users SET mcg_timer=20 WHERE task_type = 'regular' AND DATEDIFF( '$currentdate',joined_date) >= 15 AND total_referrals = 0";
$db->sql($sql);

$sql = "UPDATE users SET mcg_timer=45 WHERE task_type = 'champion' AND DATEDIFF( '$currentdate',joined_date) >= 15 AND total_referrals = 0";
$db->sql($sql);


$sql = "UPDATE users SET code_generate=0 WHERE total_codes >= 60000";
$db->sql($sql);



?>