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

if (empty($_POST['staff_id'])) {
    $response['success'] = false;
    $response['message'] = "staff Id is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['project_type'])) {
    $response['success'] = false;
    $response['message'] = "Project Type is Empty";
    print_r(json_encode($response));
    return false;
}

$staff_id = $db->escapeString($_POST['staff_id']);
$project_type = $db->escapeString($_POST['project_type']);

$sql = "SELECT * FROM users WHERE support_id = $staff_id AND status = 1 AND code_generate = 1 AND project_type = '$project_type' ORDER BY worked_days DESC";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);

if ($num >= 1) {
    $response['success'] = true;
    $response['message'] = "Users Retrieved Successfully";
    $response['data'] = $res;
    print_r(json_encode($response));
} else {
    $response['success'] = false;
    $response['message'] = "Not Found";
    print_r(json_encode($response));
}
?>
