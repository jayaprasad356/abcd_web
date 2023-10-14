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
$function = new functions;
include_once('../includes/custom-functions.php');
$fn = new custom_functions;
$function->monitorApi('wallet');

if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User Id is Empty";
    print_r(json_encode($response));
    return false;
}
$user_id = $db->escapeString($_POST['user_id']);


$sql = "SELECT project_type,code_generate,num_sync_times,level,total_codes,status,per_code_cost FROM users WHERE id = $user_id";
$db->sql($sql);
$ures = $db->getResult();
$num = $db->numRows($ures);
if ($num == 1) {
    $user_code_generate = $ures[0]['code_generate'];
    $project_type = $ures[0]['project_type'];
    $status = $ures[0]['status'];
    $level = $ures[0]['level'];
    $sql = "SELECT code_generate,num_sync_times,sync_codes FROM settings";
    $db->sql($sql);
    $set = $db->getResult();
    $code_generate = $set[0]['code_generate'];
    $sync_codes = $set[0]['sync_codes'];
    $datetime = date('Y-m-d H:i:s');
    $currentdate = date('Y-m-d');
    if($project_type == 'amail'){
        $message = 'mail added sucessfully';
       
        $type = 'create_mail';
        $mails = (isset($_POST['mails']) && $_POST['mails'] != "") ? $db->escapeString($_POST['mails']) : 0;
        if($code_generate == 1 && $user_code_generate == 1){
            if($mails != 0){

                $sql = "SELECT COUNT(id) AS count  FROM transactions WHERE user_id = $user_id AND DATE(datetime) = '$currentdate' AND type = 'create_mail'";
                $db->sql($sql);
                $tres = $db->getResult();
                $t_count = $tres[0]['count'];
                if ($t_count >= 1) {
                    $response['success'] = false;
                    $response['message'] = "You Reached Daily Sync Limit";
                    print_r(json_encode($response));
                    return false;
                }
                if ($ures[0]['total_codes'] >= 60000) {
                    $sql = "UPDATE `users` SET  `code_generate` = 0 WHERE `id` = $user_id";
                    $db->sql($sql);
                    $response['success'] = false;
                    $response['message'] = "You Reached Codes Limit";
                    print_r(json_encode($response));
                    return false;
                }
                if ($mails > 10) {
                    $mails = 10;
                }
                $amount = $mails * 30;
                $e_amount = $mails * 2.5;
                $b_amount = $mails * 27.5;

                $sql = "INSERT INTO transactions (`user_id`,`mails`,`amount`,`datetime`,`type`)VALUES('$user_id','$mails','$amount','$datetime','$type')";
                $db->sql($sql);
                $res = $db->getResult();
            
                $sql = "UPDATE `users` SET  `today_mails` = today_mails + $mails,`total_mails` = total_mails + $mails,`bonus_wallet` = bonus_wallet + $b_amount,`earnings_wallet` = earnings_wallet + $e_amount,`last_updated` = '$datetime' WHERE `id` = $user_id";
                $db->sql($sql);
        

            }
        }
        else{
        
            $response['success'] = false;
            $response['message'] = "Cannot Sync Right Now, Code Generate is turned off";
            print_r(json_encode($response));
        }

        

    }elseif($project_type == 'champion'){
        $message = 'code addeed sucessfully';
        
        $task_type = (isset($_POST['task_type']) && $_POST['task_type'] != "") ? $db->escapeString($_POST['task_type']) : '';
        $codes = (isset($_POST['codes']) && $_POST['codes'] != "") ? $db->escapeString($_POST['codes']) : 0;

        $type = 'champion_generate';



        if($code_generate == 1 && $user_code_generate == 1 && $status == 1){
            if($codes != 0){


                    $per_code_cost = $ures[0]['per_code_cost'];
                    $amount = $codes  * $per_code_cost;
                    $sql = "SELECT COUNT(id) AS count  FROM transactions WHERE user_id = $user_id AND DATE(datetime) = '$currentdate' AND type = 'champion_generate'";
                    $db->sql($sql);
                    $tres = $db->getResult();
                    $t_count = $tres[0]['count'];
                    if ($t_count >= 1) {
                        $response['success'] = false;
                        $response['message'] = "You Reached Daily Sync Limit";
                        print_r(json_encode($response));
                        return false;
                    }

                    if ($ures[0]['total_codes'] >= 60000) {
                        $sql = "UPDATE `users` SET  `code_generate` = 0 WHERE `id` = $user_id";
                        $db->sql($sql);
                        $response['success'] = false;
                        $response['message'] = "You Reached Codes Limit";
                        print_r(json_encode($response));
                        return false;
                    }


                    if ($codes > 10) {
                        $codes = 10;
                    }

                    $amount = $codes * 30;
                    $e_amount = $codes * 3;
                    $b_amount = $codes * 27;
    
                    $sql = "INSERT INTO transactions (`user_id`,`codes`,`amount`,`datetime`,`type`)VALUES('$user_id','$codes','$amount','$datetime','$type')";
                    $db->sql($sql);
                    $res = $db->getResult();


                
                    $sql = "UPDATE `users` SET  `today_codes` = today_codes + $codes,`total_codes` = total_codes + $codes,`ch_daily_wallet` = ch_daily_wallet + $e_amount,`ch_monthly_wallet` = ch_monthly_wallet + $b_amount,`last_updated` = '$datetime' WHERE `id` = $user_id";
                    $db->sql($sql);
        
                    
            
            
                }
            
            

        }
        else{
        
            $response['success'] = false;
            $response['message'] = "Cannot Sync Right Now, Code Generate is turned off";
            print_r(json_encode($response));
            return false;
        }

        

    }    elseif($project_type == 'unlimited'){
        $message = 'code addeed sucessfully';
        
        $task_type = (isset($_POST['task_type']) && $_POST['task_type'] != "") ? $db->escapeString($_POST['task_type']) : '';
        $codes = (isset($_POST['codes']) && $_POST['codes'] != "") ? $db->escapeString($_POST['codes']) : 0;

        $type = 'unlimited_generate';



        if($code_generate == 1 && $user_code_generate == 1 && $status == 1){
            if($codes != 0){



                    $sql = "SELECT COUNT(id) AS count  FROM transactions WHERE user_id = $user_id AND DATE(datetime) = '$currentdate' AND type = '$type'";
                    $db->sql($sql);
                    $tres = $db->getResult();
                    $t_count = $tres[0]['count'];
                    if ($t_count >= 5) {
                        $response['success'] = false;
                        $response['message'] = "You Reached Daily Sync Limit";
                        print_r(json_encode($response));
                        return false;
                    }
                    if ($ures[0]['total_codes'] >= 60000) {
                        $sql = "UPDATE `users` SET  `code_generate` = 0 WHERE `id` = $user_id";
                        $db->sql($sql);
                        $response['success'] = false;
                        $response['message'] = "You Reached Codes Limit";
                        print_r(json_encode($response));
                        return false;
                    }


                    if ($codes > 100) {
                        $codes = 100;
                    }

                    $per_code_cost = $ures[0]['per_code_cost'];
                    $amount = $codes  * $per_code_cost;
    
                    $sql = "INSERT INTO transactions (`user_id`,`codes`,`amount`,`datetime`,`type`,`task_type`)VALUES('$user_id','$codes','$amount','$datetime','$type','$task_type')";
                    $db->sql($sql);
                    $res = $db->getResult();

                                
                    $sql = "UPDATE `users` SET `today_codes` = today_codes + $codes,`total_codes` = total_codes + $codes,`balance` = balance + $amount,`earn` = earn + $amount,`last_updated` = '$datetime' WHERE `id` = $user_id";
                    $db->sql($sql);
        
                    
            
            
                }
            
            

        }
        else{
        
            $response['success'] = false;
            $response['message'] = "Cannot Sync Right Now, Code Generate is turned off";
            print_r(json_encode($response));
            return false;
        }

        

    }else{
        $message = 'code addeed sucessfully';
        
        $task_type = (isset($_POST['task_type']) && $_POST['task_type'] != "") ? $db->escapeString($_POST['task_type']) : '';
        $codes = (isset($_POST['codes']) && $_POST['codes'] != "") ? $db->escapeString($_POST['codes']) : 0;

        $type = 'generate';





        $sql = "SELECT datetime FROM transactions WHERE user_id = $user_id AND type = 'generate' ORDER BY datetime DESC LIMIT 1 ";
        $db->sql($sql);
        $tres = $db->getResult();
        $num = $db->numRows($tres);
     
        $code_min_sync_time = $fn->get_sync_time($ures[0]['level']);
        if ($num >= 1) {
            $dt1 = $tres[0]['datetime'];
            $date1 = new DateTime($dt1);
            $date2 = new DateTime($datetime);

            $diff = $date1->diff($date2);
            $totalMinutes = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
            $dfi = $code_min_sync_time - $totalMinutes;
            if($totalMinutes < $code_min_sync_time){
                $response['success'] = false;
                $response['message'] = "Cannot Sync Right Now, Try again after ".$dfi." mins";
                print_r(json_encode($response));
                return false;

            }


        }

        if($code_generate == 1 && $user_code_generate == 1 && $status == 1){
            if($codes != 0){


                    $per_code_cost = $fn->get_code_per_cost($user_id);
                    $amount = $codes  * $per_code_cost;
                    $sql = "SELECT COUNT(id) AS count  FROM transactions WHERE user_id = $user_id AND DATE(datetime) = '$currentdate' AND type = 'generate'";
                    $db->sql($sql);
                    $tres = $db->getResult();
                    $t_count = $tres[0]['count'];
                    if ($t_count >= $ures[0]['num_sync_times']) {
                        $response['success'] = false;
                        $response['message'] = "You Reached Daily Sync Limit";
                        print_r(json_encode($response));
                        return false;
                    }

                    if ($ures[0]['total_codes'] >= 60000) {
                        $sql = "UPDATE `users` SET  `code_generate` = 0 WHERE `id` = $user_id";
                        $db->sql($sql);
                        $response['success'] = false;
                        $response['message'] = "You Reached Codes Limit";
                        print_r(json_encode($response));
                        return false;
                    }
                    $amount = $codes * 0.17;
                    $d_amount = $codes * 0.05;
                    $m_amount = $codes * 0.12;
            
                    $sql = "INSERT INTO transactions (`user_id`,`codes`,`amount`,`datetime`,`type`,`task_type`)VALUES('$user_id','$codes','$amount','$datetime','$type','$task_type')";
                    $db->sql($sql);
                    $res = $db->getResult();

                    $sql = "UPDATE `users` SET  `reward_codes` = reward_codes + $codes WHERE `id` = $user_id AND project_type = 'abcd' AND level = 1";
                    $db->sql($sql);

                                
                    $sql = "UPDATE `users` SET `today_codes` = today_codes + $codes,`total_codes` = total_codes + $codes,`daily_wallet` = daily_wallet + $d_amount,`monthly_wallet` = monthly_wallet + $m_amount,`last_updated` = '$datetime' WHERE `id` = $user_id";
                    $db->sql($sql);
            
            
                }
            
            

        }
        else{
        
            $response['success'] = false;
            $response['message'] = "Cannot Sync Right Now, Code Generate is turned off";
            print_r(json_encode($response));
            return false;
        }

    }

    $sql = "SELECT level,per_code_val,today_codes,total_codes,balance,code_generate,status,referred_by,refund_wallet,total_refund,black_box,today_mails,total_mails,bonus_wallet,earnings_wallet,today_mails,total_mails,daily_wallet,monthly_wallet,ch_daily_wallet,ch_monthly_wallet,reward_codes  FROM users WHERE id = $user_id ";
    $db->sql($sql);
    $res = $db->getResult();
    
    $response['success'] = true;
    $response['message'] = $message;
    $response['black_box'] = $res[0]['black_box'];
    $response['status'] = $res[0]['status'];
    $response['balance'] = $res[0]['balance'];
    $response['level'] = $res[0]['level'];
    $response['reward_codes'] = $res[0]['reward_codes'];
    $response['per_code_val'] = $res[0]['per_code_val'];
    $response['today_mails'] = $res[0]['today_mails'];
    $response['total_mails'] = $res[0]['total_mails'];
    $response['bonus_wallet'] = $res[0]['bonus_wallet'];
    $response['earnings_wallet'] = $res[0]['earnings_wallet'];
    $response['today_codes'] = $res[0]['today_codes'];
    $response['total_codes'] = $res[0]['total_codes'];
    $response['code_generate'] = $res[0]['code_generate'];
    $response['status'] = $res[0]['status'];
    $response['refund_wallet'] = $res[0]['refund_wallet'];
    $response['total_refund'] = $res[0]['total_refund'];
    $response['daily_wallet'] = $res[0]['daily_wallet'];
    $response['monthly_wallet'] = $res[0]['monthly_wallet'];
    $response['ch_daily_wallet'] = $res[0]['ch_daily_wallet'];
    $response['ch_monthly_wallet'] = $res[0]['ch_monthly_wallet'];
    print_r(json_encode($response));

}
else{
    $response['success'] = false;
    $response['message'] = "User Not Found";
    print_r(json_encode($response));
    return false;

}









?>