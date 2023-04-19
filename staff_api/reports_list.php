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
$fn->monitorApi('reports');
$currentdate = date("Y-m-d");

if (empty($_POST['level'])) {
    $response['success'] = false;
    $response['message'] = "Level is empty";
    echo json_encode($response);
    return false;
}
if (empty($_POST['support_id'])) {
    $response['success'] = false;
    $response['message'] = "support id is empty";
    echo json_encode($response);
    return false;
}

$support_id = $db->escapeString($_POST['support_id']);
$level = $db->escapeString($_POST['level']);

if ($level == 1) {
    $sql = "SELECT * FROM users WHERE DATEDIFF('$currentdate', joined_date) = 1 AND support_id='$support_id' AND status= 1";
    $db->sql($sql);
    $res = $db->getResult();
    $num = $db->numRows($res);
    if ($num >= 1) {
        $response['success'] = true;
        $response['message'] = "Users listed successfully";
        $response['data'] = $res;
        print_r(json_encode($response));
    } else {
        $response['success'] = false;
        $response['message'] = "No users found";
        print_r(json_encode($response));
    }
} elseif ($level == 2) {
    $sql = "SELECT * FROM users WHERE DATEDIFF('$currentdate', joined_date) = 3 AND support_id='$support_id' AND status= 1";
    $db->sql($sql);
    $res = $db->getResult();
    $num = $db->numRows($res);
    if ($num >= 1) {
        $response['success'] = true;
        $response['message'] = "Users listed successfully";
        $response['data'] = $res;
        print_r(json_encode($response));
    } else {
        $response['success'] = false;
        $response['message'] = "No users found";
        print_r(json_encode($response));
    }
} elseif ($level == 3) {
    $sql = "SELECT * FROM users WHERE DATEDIFF('$currentdate', joined_date) = 5 AND support_id='$support_id' AND status= 1";
    $db->sql($sql);
    $res = $db->getResult();
    $num = $db->numRows($res);
    if ($num >= 1) {
        $response['success'] = true;
        $response['message'] = "Users listed successfully";
        $response['data'] = $res;
        print_r(json_encode($response));
    } else {
        $response['success'] = false;
        $response['message'] = "No Users Found";
        print_r(json_encode($response));
    
    }
} elseif ($level == 4) {
        $sql = "SELECT * FROM users WHERE DATEDIFF('$currentdate', joined_date) >= 7 AND support_id='$support_id' AND total_referrals=0 AND status= 1";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        if ($num >= 1) {
            $response['success'] = true;
            $response['message'] = "Users listed successfully";
            $response['data'] = $res;
            print_r(json_encode($response));
        } else {
            $response['success'] = false;
            $response['message'] = "No Users Found";
            print_r(json_encode($response));
        
        }
        
        }



?>