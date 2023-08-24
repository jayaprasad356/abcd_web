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


if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = " User Id is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['wallet_type'])) {
    $response['success'] = false;
    $response['message'] = " Wallet Type is Empty";
    print_r(json_encode($response));
    return false;
}
$datetime = date('Y-m-d H:i:s');
$user_id=$db->escapeString($_POST['user_id']);
$wallet_type = $db->escapeString($_POST['wallet_type']);

$sql = "SELECT * FROM users WHERE id = '" . $user_id . "'";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);


if ($num == 1) {
    $earnings_wallet = $res[0]['earnings_wallet'];
    $bonus_wallet = $res[0]['bonus_wallet'];
    $current_refers = $res[0]['current_refers'];
    $target_refers = $res[0]['target_refers'];
    $daily_wallet = $res[0]['daily_wallet'];
    $monthly_wallet = $res[0]['monthly_wallet'];
    $status = $res[0]['status'];
    $project_type = $res[0]['project_type'];
    $plan = $res[0]['plan'];
    $worked_days = $res[0]['worked_days'];
    $duration = $res[0]['duration'];


    if ($status == 0 || (($wallet_type == 'earnings_wallet' || $wallet_type == 'bonus_wallet' ) && $status == 1 && $project_type != 'amail')) {
        $response['success'] = false;
        $response['message'] = "Purchase Plan";
        print_r(json_encode($response));
        return false;
    }

    if($wallet_type == 'earnings_wallet'){
        if ($earnings_wallet < 75) {
            $response['success'] = false;
            $response['message'] = "Minimum ₹75 to add balance";
            print_r(json_encode($response));
            return false;
        }
        $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'earnings_wallet','$datetime',$earnings_wallet)";
        $db->sql($sql);
        $sql = "UPDATE users SET balance= balance + earnings_wallet,earnings_wallet = 0 WHERE id=" . $user_id;
        $db->sql($sql);

    }
    if($wallet_type == 'daily_wallet'){
        if($plan == 30){
            $min_daily_wallet = 60;

        }else{
            $min_daily_wallet = 100;

        }
        if ($daily_wallet < $min_daily_wallet)  {
            $response['success'] = false;
            $response['message'] = "Minimum ₹".$min_daily_wallet." to add balance";
            print_r(json_encode($response));
            return false;
        }
        $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'daily_wallet','$datetime',$daily_wallet)";
        $db->sql($sql);
        $sql = "UPDATE users SET balance= balance + daily_wallet,daily_wallet = 0 WHERE id=" . $user_id;
        $db->sql($sql);

    }
    if($wallet_type == 'monthly_wallet'){
        if ($worked_days < $duration)  {
            $response['success'] = false;
            $response['message'] = "Withdraw After Plan Days";
            print_r(json_encode($response));
            return false;
        }
        $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'monthly_wallet','$datetime',$monthly_wallet)";
        $db->sql($sql);
        $sql = "UPDATE users SET balance= balance + monthly_wallet,monthly_wallet = 0 WHERE id=" . $user_id;
        $db->sql($sql);

    }
    if($wallet_type == 'bonus_wallet'){
        if ($current_refers < $target_refers) {
            $response['success'] = false;
            $response['message'] = "Minimum ".$target_refers." refers to add balance";
            print_r(json_encode($response));
            return false;
        }
        if ($bonus_wallet < 225) {
            $response['success'] = false;
            $response['message'] = "Minimum ₹225 to add balance";
            print_r(json_encode($response));
            return false;
        }
        $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'bonus_wallet','$datetime',$bonus_wallet)";
        $db->sql($sql);
        $sql = "UPDATE users SET balance= balance + bonus_wallet,bonus_wallet = 0 WHERE id=" . $user_id;
        $db->sql($sql);
    
    }

    $sql = "SELECT * FROM users WHERE id = '" . $user_id . "'";
    $db->sql($sql);
    $res = $db->getResult();
    $response['success'] = true;
    $response['message'] = "Added to Main Balance Successfully";
    $response['data'] = $res;



}
else{
    $response['success'] = false;
    $response['message'] = "User Not Found";
}

print_r(json_encode($response));




?>
