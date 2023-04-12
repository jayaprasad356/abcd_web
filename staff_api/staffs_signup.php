
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
include_once('../includes/custom-functions.php');
include_once('../includes/functions.php');
$fn = new custom_functions;

if (empty($_POST['first_name'])) {
    $response['success'] = false;
    $response['message'] = "First Name is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['email'])) {
    $response['success'] = false;
    $response['message'] = "Email is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['password'])) {
    $response['success'] = false;
    $response['message'] = "Password is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['mobile'])) {
    $response['success'] = false;
    $response['message'] = "Mobile Number is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['join_date'])) {
    $response['success'] = false;
    $response['message'] = "Join Date is Empty";
    print_r(json_encode($response));
    return false;
}
$first_name = $db->escapeString($_POST['first_name']);
$last_name = (isset($_POST['last_name']) && !empty($_POST['last_name'])) ? $db->escapeString($_POST['last_name']) : "";
$email = $db->escapeString($_POST['email']);
$password = $db->escapeString($_POST['password']);
$mobile = $db->escapeString($_POST['mobile']);
$join_date = $db->escapeString($_POST['join_date']);

$sql = "SELECT * FROM staffs WHERE email = '$email'";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);

if ($num >= 1) {
    $response['success'] = false;
    $response['message'] = "You are already registered";
    print_r(json_encode($response));
}
else {
    $sql = "INSERT INTO staffs (first_name,last_name, email, password, mobile,join_date) VALUES ('$first_name','$last_name', '$email', '$password', '$mobile','$join_date')";
    $db->sql($sql);
    $sql = "SELECT id,first_name,last_name,email,mobile,password,join_date FROM staffs WHERE email = '$email'";
    $db->sql($sql);
    $res = $db->getResult();
    $response['success'] = true;
    $response['message'] = "Staff added successfully";
    $response['data'] = $res;
    print_r(json_encode($response));
}

  
