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
date_default_timezone_set('Asia/Kolkata');
include_once('../includes/functions.php');
$fn = new functions;
$fn->monitorApi('withdrawal');

if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User Id is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['amount'])) {
    $response['success'] = false;
    $response['message'] = "Amount is Empty";
    print_r(json_encode($response));
    return false;
}
$user_id = $db->escapeString($_POST['user_id']);
$amount = $db->escapeString($_POST['amount']);


$sql = "SELECT * FROM settings";
$db->sql($sql);
$mres = $db->getResult();
$main_ws = $mres[0]['withdrawal_status'];
$sql = "SELECT balance,refer_balance,withdrawal_status,branch_id,project_type FROM users WHERE id = $user_id ";
$db->sql($sql);
$res = $db->getResult();
$balance = $res[0]['balance'];
$refer_balance = $res[0]['refer_balance'];
$withdrawal_status = $res[0]['withdrawal_status'];
$branch_id = $res[0]['branch_id'];
$project_type = $res[0]['project_type'];
if(!empty($branch_id)){
    $sql = "SELECT min_withdrawal FROM branches WHERE id = $branch_id";
    $db->sql($sql);
    $result = $db->getResult();
    $min_withdrawal = $result[0]['min_withdrawal'];
}
else{
    $min_withdrawal = $mres[0]['min_withdrawal'];
}
if($project_type == 'abcd'){
    $min_withdrawal = 50;

}
if($project_type == 'amail'){
    $min_withdrawal = 25;

}
if($project_type == 'champion'){
    $min_withdrawal = 30;
}
if($project_type == 'unlimited'){
    $min_withdrawal = 50;
}

$datetime = date('Y-m-d H:i:s');
$sql = "SELECT id FROM bank_details WHERE user_id = $user_id ";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if($withdrawal_status == 1 &&  $main_ws == 1 ){
    if ($num >= 1) {
        if($amount >= $min_withdrawal){
            if($balance >= $amount){
                $sql = "SELECT * FROM `users` WHERE level = 1 AND project_type = 'abcd' AND status = 1 AND worked_days >= duration AND total_codes < 60000 AND monthly_wallet_status = 0 AND due_amt < 1000 AND id = $user_id";
                $db->sql($sql);
                $res = $db->getResult();
                $num = $db->numRows($res);
                if ($num >= 1) {
                    if($balance < 1000){
                        $response['success'] = false;
                        $response['message'] = "You do not have sufficient balance to pay Rs 1000 platform fee for non completion of target.";
                        print_r(json_encode($response));
                        return false;

                    }
                    $type = 'platform_fees';
                    $amount = 1000;
                    $sql = "UPDATE `users` SET `balance` = balance - $amount,`due_amt` = due_amt + $amount WHERE `id` = $user_id";
                    $db->sql($sql);
                    $sql = "INSERT INTO transactions (`user_id`,`amount`,`datetime`,`type`)VALUES('$user_id','$amount','$datetime','$type')";
                    $db->sql($sql);
                    $message = "Rs 1000 platform fee will be charged for non completion of target.";

                }else{
                    $sql = "UPDATE `users` SET `balance` = balance - $amount,`withdrawal` = withdrawal + $amount WHERE `id` = $user_id";
                    $db->sql($sql);
                    $sql = "INSERT INTO withdrawals (`user_id`,`amount`,`datetime`,`withdrawal_type`)VALUES('$user_id','$amount','$datetime','code_withdrawal')";
                    $db->sql($sql);
                    $message = "Withdrawal Requested Successfully";

                }

                $sql = "SELECT balance,refer_balance FROM users WHERE id = $user_id ";
                $db->sql($sql);
                $res = $db->getResult();
                $balance = $res[0]['balance'];
                $refer_balance = $res[0]['refer_balance'];
                $response['success'] = true;
                $response['balance'] = $balance;
                $response['refer_balance'] = $refer_balance;
                $response['message'] = $message;
                print_r(json_encode($response));
        
            }
            else{
                $response['success'] = false;
                $response['message'] = "Insufficent Balance";
                print_r(json_encode($response)); 
            }

        
        }
        else{
            $response['success'] = false;
            $response['message'] = "Required Minimum Amount to Withdrawal is ".$min_withdrawal;
            print_r(json_encode($response)); 
        }
    }else{
        $response['success'] = false;
        $response['message'] = "Update Bank Details first";
        print_r(json_encode($response)); 
    
    }
}else{
    $response['success'] = false;
    $response['message'] = "Withdrawal Mon - Sat 10 am To 6 pm";
    print_r(json_encode($response));    
}






?>