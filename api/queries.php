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

if (empty($_POST['query_type'])) {
    $response['success'] = false;
    $response['message'] = " Query Type is Empty";
    print_r(json_encode($response));
    return false;
}

$query_type = $db->escapeString($_POST['query_type']);

if($query_type == 'refer_friends'){
    if (empty($_POST['user_id'])) {
        $response['success'] = false;
        $response['message'] = " User Id is Empty";
        print_r(json_encode($response));
        return false;
    }
    if (empty($_POST['friend_mobile'])) {
        $response['success'] = false;
        $response['message'] = "Friend Mobile is Empty";
        print_r(json_encode($response));
        return false;
    }
    if (empty($_POST['description'])) {
        $response['success'] = false;
        $response['message'] = " Description is Empty";
        print_r(json_encode($response));
        return false;
    }
 
    $user_id=$db->escapeString($_POST['user_id']);
    $friend_mobile = $db->escapeString($_POST['friend_mobile']);
    $description=$db->escapeString($_POST['description']);
   
   
   $sql = "INSERT INTO refer_friends (`user_id`,`friend_mobile`,`description`) VALUES ('$user_id','$friend_mobile','$description')";
   $db->sql($sql);

}

if ($query_type == 'refer_not_receive') {
    if (empty($_POST['user_id'])) {
        $response['success'] = false;
        $response['message'] = " User Id is Empty";
        print_r(json_encode($response));
        return false;
    }
    if (empty($_POST['friend_mobile'])) {
        $response['success'] = false;
        $response['message'] = "Friend Mobile is Empty";
        print_r(json_encode($response));
        return false;
    }
    if (empty($_POST['description'])) {
        $response['success'] = false;
        $response['message'] = " Description is Empty";
        print_r(json_encode($response));
        return false;
    }
  
    $user_id=$db->escapeString($_POST['user_id']);
    $friend_mobile = $db->escapeString($_POST['friend_mobile']);
    $description=$db->escapeString($_POST['description']);
   
    
   $sql = "INSERT INTO refer_not_receive (`user_id`,`friend_mobile`,`description`) VALUES ('$user_id','$friend_mobile','$description')";
   $db->sql($sql);

}
if ($query_type == 'withdrawal_not_receive') {
    if (empty($_POST['user_id'])) {
        $response['success'] = false;
        $response['message'] = " User Id is Empty";
        print_r(json_encode($response));
        return false;
    }
    if (empty($_POST['withdrawal_date'])) {
        $response['success'] = false;
        $response['message'] = "Withdrawal Date is Empty";
        print_r(json_encode($response));
        return false;
    }
    if (empty($_POST['amount'])) {
        $response['success'] = false;
        $response['message'] = " Amount is Empty";
        print_r(json_encode($response));
        return false;
    }
    if (empty($_POST['account_num'])) {
        $response['success'] = false;
        $response['message'] = " Account Number is Empty";
        print_r(json_encode($response));
        return false;
    }
    if (empty($_POST['ifsc_code'])) {
        $response['success'] = false;
        $response['message'] = " Ifsc Code is Empty";
        print_r(json_encode($response));
        return false;
    }
    if (empty($_POST['description'])) {
        $response['success'] = false;
        $response['message'] = "Description is Empty";
        print_r(json_encode($response));
        return false;
    }
  
    $user_id=$db->escapeString($_POST['user_id']);
    $withdrawal_date = $db->escapeString($_POST['withdrawal_date']);
    $amount = $db->escapeString($_POST['amount']);
    $account_num = $db->escapeString($_POST['account_num']);
    $ifsc_code = $db->escapeString($_POST['ifsc_code']);
    $description=$db->escapeString($_POST['description']);
   
    
   $sql = "INSERT INTO withdrawal_not_receive (`user_id`,`withdrawal_date`,`amount`,`account_num`,`ifsc_code`,`description`) VALUES ('$user_id','$withdrawal_date','$amount','$account_num','$ifsc_code','$description')";
   $db->sql($sql);

}
if ($query_type == 'withdrawal_cancel') {
    if (empty($_POST['user_id'])) {
        $response['success'] = false;
        $response['message'] = " User Id is Empty";
        print_r(json_encode($response));
        return false;
    }
    if (empty($_POST['account_num'])) {
        $response['success'] = false;
        $response['message'] = " Account Number is Empty";
        print_r(json_encode($response));
        return false;
    }
    if (empty($_POST['ifsc_code'])) {
        $response['success'] = false;
        $response['message'] = " Ifsc Code is Empty";
        print_r(json_encode($response));
        return false;
    }
    if (empty($_POST['description'])) {
        $response['success'] = false;
        $response['message'] = "Description is Empty";
        print_r(json_encode($response));
        return false;
    }
  
    $user_id=$db->escapeString($_POST['user_id']);
    $account_num = $db->escapeString($_POST['account_num']);
    $ifsc_code = $db->escapeString($_POST['ifsc_code']);
    $description=$db->escapeString($_POST['description']);
   
    
   $sql = "INSERT INTO withdrawal_cancel (`user_id`,`account_num`,`ifsc_code`,`description`) VALUES ('$user_id','$account_num','$ifsc_code','$description')";
   $db->sql($sql);

}
$response['success'] = true;
$response['message'] = " Queries Added Successfully";


print_r(json_encode($response));
?>