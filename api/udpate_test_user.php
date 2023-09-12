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
$fn->monitorApi('reset_codes');
date_default_timezone_set('Asia/Kolkata');


$sql = "UPDATE users SET monthly_wallet = 150,old_monthly_wallet = 50 WHERE id = 24900";
$db->sql($sql);

echo 'Time Updated';


?>