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
    $reward_codes = $res[0]['reward_codes'];
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
    $level = $res[0]['level'];
    $sync_refer_wallet = $res[0]['sync_refer_wallet'];
    $target_bonus_sent = $res[0]['target_bonus_sent'];
    $num_target_bonus = $res[0]['num_target_bonus'];
    $old_monthly_wallet = $res[0]['old_monthly_wallet'];
    $joined_date = $res[0]['joined_date'];
    $total_codes = $res[0]['total_codes'];
    $monthly_wallet_status = $res[0]['monthly_wallet_status'];
    $ch_daily_wallet = $res[0]['ch_daily_wallet'];
    $ch_monthly_wallet = $res[0]['ch_monthly_wallet'];
    $amail_refer = $res[0]['amail_refer'];
    $reward_codes = $res[0]['reward_codes'];
    $l_referral_count = $res[0]['l_referral_count'];
    $target_date = '2023-08-21';
    $joined_date_timestamp = strtotime($joined_date);
    $target_date_timestamp = strtotime($target_date);






    if ($status == 0 || (($wallet_type == 'earnings_wallet' || $wallet_type == 'bonus_wallet' ) && $status == 1 && $project_type != 'amail')) {
        $response['success'] = false;
        $response['message'] = "Purchase Plan";
        print_r(json_encode($response));
        return false;
    }

    if($wallet_type == 'earnings_wallet'){
        if ($earnings_wallet < 25) {
            $response['success'] = false;
            $response['message'] = "Minimum ₹75 to add balance";
            print_r(json_encode($response));
            return false;
        }
        $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'earnings_wallet','$datetime',$earnings_wallet)";
        $db->sql($sql);
        $sql = "UPDATE users SET balance= balance + earnings_wallet,earn = earn + earnings_wallet,earnings_wallet = 0 WHERE id=" . $user_id;
        $db->sql($sql);

    }
    if($wallet_type == 'daily_wallet'){
        if ($daily_wallet <= 0)  {
            $response['success'] = false;
            $response['message'] = "Your wallet is empty";
            print_r(json_encode($response));
            return false;
        }
        if($level == 1){
            $min_daily_wallet = 50;

        }
        elseif($plan == 50){
            $min_daily_wallet = 60;
        }
        else{
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
        $sql = "UPDATE users SET balance= balance + daily_wallet,earn = earn + daily_wallet,daily_wallet = 0 WHERE id=" . $user_id;
        $db->sql($sql);

    }
    if($wallet_type == 'monthly_wallet'){
        if ($monthly_wallet_status == 0 )  {
            $response['success'] = false;
            $response['message'] = "Your wallet is disabled";
            print_r(json_encode($response));
            return false;
        }
        if ($monthly_wallet <= 0)  {
            $response['success'] = false;
            $response['message'] = "Your wallet is empty";
            print_r(json_encode($response));
            return false;
        }
        if($plan == 50){
            if($level <= 2){
                if($user_id  == 24900){
                    if($level == 1){
                        if ($level == 1 && $worked_days < $duration)  {
                            $response['success'] = false;
                            $response['message'] = "Complete 60000 Codes To Withdraw";
                            print_r(json_encode($response));
                            return false;
                        }
                        $percent = 29;
                        $monthly_wallet = $monthly_wallet - $old_monthly_wallet;
                        $result = ($percent / 100) * $monthly_wallet;
                        $monthly_wallet = $old_monthly_wallet + $result;
                        $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'monthly_wallet','$datetime',$monthly_wallet)";
                        $db->sql($sql);
                        $sql = "UPDATE users SET balance= balance + $monthly_wallet,earn = earn + $monthly_wallet,monthly_wallet = monthly_wallet - $monthly_wallet,old_monthly_wallet = 0,monthly_wallet_status = 0 WHERE id=" . $user_id;
                        $db->sql($sql);
                        $response['success'] = true;
                        $response['message'] = "Added to Main Balance Successfully";
                        $response['data'] = $res;
                        print_r(json_encode($response));
                        return false;
    
                    }else{
                        if ($level == 2 && $worked_days < $duration)  {
                            $response['success'] = false;
                            $response['message'] = "Complete 60000 Codes To Withdraw";
                            print_r(json_encode($response));
                            return false;
                
                        }
                        $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'monthly_wallet','$datetime',$monthly_wallet)";
                        $db->sql($sql);
                        $sql = "UPDATE users SET balance= balance + monthly_wallet,earn = earn + monthly_wallet,monthly_wallet = 0 WHERE id=" . $user_id;
                        $db->sql($sql);
        
                        $sql = "SELECT * FROM users WHERE id = '" . $user_id . "'";
                        $db->sql($sql);
                        $res = $db->getResult();
                        $response['success'] = true;
                        $response['message'] = "Added to Main Balance Successfully";
                        $response['data'] = $res;
                        print_r(json_encode($response));
                        return false;
    
                    }
                    
                }else{
                    $response['success'] = false;
                    $response['message'] = "Disabled";
                    print_r(json_encode($response));
                    return false;

                }



                
            }else{
                $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'monthly_wallet','$datetime',$monthly_wallet)";
                $db->sql($sql);
                $sql = "UPDATE users SET balance= balance + monthly_wallet,earn = earn + monthly_wallet,monthly_wallet = 0 WHERE id=" . $user_id;
                $db->sql($sql);

                $sql = "SELECT * FROM users WHERE id = '" . $user_id . "'";
                $db->sql($sql);
                $res = $db->getResult();
                $response['success'] = true;
                $response['message'] = "Added to Main Balance Successfully";
                $response['data'] = $res;
                print_r(json_encode($response));
                return false;

            }
            // if($level == 1 && $plan == 50 && $worked_days >= $duration){
            //     $percent = 29;
            //     $monthly_wallet = $monthly_wallet - $old_monthly_wallet;
            //     $result = ($percent / 100) * $monthly_wallet;
            //     $monthly_wallet = $old_monthly_wallet + $result;
            //     $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'monthly_wallet','$datetime',$monthly_wallet)";
            //     $db->sql($sql);
            //     $sql = "UPDATE users SET balance= balance + $monthly_wallet,earn = earn + $monthly_wallet,monthly_wallet = monthly_wallet - $monthly_wallet,old_monthly_wallet = 0,monthly_wallet_status = 0 WHERE id=" . $user_id;
            //     $db->sql($sql);

            //     $sql = "SELECT * FROM users WHERE id = '" . $user_id . "'";
            //     $db->sql($sql);
            //     $res = $db->getResult();
            //     $response['success'] = true;
            //     $response['message'] = "Added to Main Balance Successfully";
            //     $response['data'] = $res;
            //     print_r(json_encode($response));
            //     return false;
            // }else{
            //     if($level > 1){
            //         $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'monthly_wallet','$datetime',$monthly_wallet)";
            //         $db->sql($sql);
            //         $sql = "UPDATE users SET balance= balance + monthly_wallet,earn = earn + monthly_wallet,monthly_wallet = 0 WHERE id=" . $user_id;
            //         $db->sql($sql);

            //         $sql = "SELECT * FROM users WHERE id = '" . $user_id . "'";
            //         $db->sql($sql);
            //         $res = $db->getResult();
            //         $response['success'] = true;
            //         $response['message'] = "Added to Main Balance Successfully";
            //         $response['data'] = $res;
            //         print_r(json_encode($response));
            //         return false;
            //     }else{
            //         $response['success'] = false;
            //         $response['message'] = "Complete 50 Days To Withdraw";
            //         print_r(json_encode($response));
            //         return false;

            //     }

    
            // }
        }

        if ($level == 1 && $worked_days < 60)  {
            $response['success'] = false;
            $response['message'] = "Complete 60000 Codes To Withdraw";
            print_r(json_encode($response));
            return false;

        }
        if($total_codes < 60000){
            if ($worked_days < $duration && $level < 3)  {
                $response['success'] = false;
                $response['message'] = "Reach level 3 and above to withdraw";
                print_r(json_encode($response));
                return false;
            }

        }

        if ($level == 1 && $worked_days >= 60)  {
            $percent = 29;
            $monthly_wallet = $monthly_wallet - $old_monthly_wallet;
            $result = ($percent / 100) * $monthly_wallet;
            $monthly_wallet = $old_monthly_wallet + $result;
            $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'monthly_wallet','$datetime',$monthly_wallet)";
            $db->sql($sql);
            $sql = "UPDATE users SET balance= balance + $monthly_wallet,earn = earn + $monthly_wallet,monthly_wallet = monthly_wallet - $monthly_wallet,old_monthly_wallet = 0,monthly_wallet_status = 0 WHERE id=" . $user_id;
            $db->sql($sql);
        }
        else {
            $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'monthly_wallet','$datetime',$monthly_wallet)";
            $db->sql($sql);
            $sql = "UPDATE users SET balance= balance + monthly_wallet,earn = earn + monthly_wallet,monthly_wallet = 0 WHERE id=" . $user_id;
            $db->sql($sql);
        }



    }
    if($wallet_type == 'bonus_wallet'){
        // // $response['success'] = false;
        // // $response['message'] = "disabled";
        // // print_r(json_encode($response));
        // return false;
        if($amail_refer == 0){
            if ($bonus_wallet < 700) {
                $response['success'] = false;
                $response['message'] = "Minimum ₹700 to add balance";
                print_r(json_encode($response));
                return false;
            }
            $bonus_wallet = 700;
            $sql_query = "SELECT * FROM `bonus_refer_bonus` WHERE user_id = $user_id AND status = 0";
            $db->sql($sql_query);
            $res = $db->getResult();
            $num = $db->numRows($res);
            if($num>=1){
                $bonus_id = $res[0]['id'];
                $sql = "UPDATE bonus_refer_bonus SET status= 1 WHERE id=" . $bonus_id;
                $db->sql($sql);
    
            }else{
                $response['success'] = false;
                $response['message'] = "Refer 1 Person to get ₹700";
                print_r(json_encode($response));
                return false;

            }
        }else{
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
        }
        
        $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'bonus_wallet','$datetime',$bonus_wallet)";
        $db->sql($sql);
        $sql = "UPDATE users SET balance= balance + $bonus_wallet,earn = earn + $bonus_wallet,bonus_wallet = bonus_wallet - $bonus_wallet WHERE id=" . $user_id;
        $db->sql($sql);

    
    }
    if($wallet_type == 'target_bonus'){

        if ($status == 0) {
            $response['success'] = false;
            $response['message'] = "Purchase Plan";
            print_r(json_encode($response));
            return false;
        }
        if ($project_type == 'amail')  {
            $response['success'] = false;
            $response['message'] = "Disabled";
            print_r(json_encode($response));
            return false;
            $amount = 500;
            $total_num = intval($worked_days / 30);

            if($num_target_bonus > $total_num && $current_refers >= $target_refers){
                $amount = $amount - $sync_refer_wallet;
                $sql = "UPDATE users SET num_target_bonus = num_target_bonus + 1,balance= balance + $amount,earn = earn + $amount,sync_refer_wallet = 0,target_bonus_sent = 1 WHERE id=" . $user_id;
                $db->sql($sql);
                $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'target_bonus','$datetime',$amount)";
                $db->sql($sql);

            }
            echo $total_num;


        }else{


            $amount = 2000;
            if ($joined_date_timestamp < $target_date_timestamp) {
                $response['success'] = false;
                $response['message'] = "Disabled";
                print_r(json_encode($response));
                return false;
            } 
            if ($target_bonus_sent == 1) {
                $response['success'] = false;
                $response['message'] = "You Already claimed bonus";
                print_r(json_encode($response));
                return false;
            }
            if($level < 5){
                $response['success'] = false;
                $response['message'] = "Reach Level 5 and get bonus";
                print_r(json_encode($response));
                return false;
            }

            

            $sql = "UPDATE users SET balance= balance + $amount,earn = earn + $amount,target_bonus_sent = 1 WHERE id=" . $user_id;
            $db->sql($sql);
            $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'target_bonus','$datetime',$amount)";
            $db->sql($sql);
        }



    }

    if($wallet_type == 'reward_codes'){
        if ($reward_codes == 0) {
            $response['success'] = false;
            $response['message'] = "Your Reward is Empty";
            print_r(json_encode($response));
            return false;
        }

        if ($project_type == 'abcd') {
            if ($level == 1) {
                $response['success'] = false;
                $response['message'] = "Claim This Codes For Free Reaching Level 2 - Helps Achieving Your Target.";
                print_r(json_encode($response));
                return false;
            }
    
             if ($reward_codes < 120) {
                $response['success'] = false;
                $response['message'] = "Minimum ₹120 to add balance";
                print_r(json_encode($response));
                return false;
            }
            $bal_codes = 60000 - $total_codes;
            if($bal_codes < $reward_codes){
                $reward_codes = $bal_codes;
    
            }
        }else{
            if ($l_referral_count == 0) {
                $response['success'] = false;
                $response['message'] = "1 refer to eligible add balance";
                print_r(json_encode($response));
                return false;
            }
        }


        $amount = $reward_codes * 0.17;

        $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`,`codes`) VALUES ($user_id,'reward_codes','$datetime','$amount','$reward_codes')";
        $db->sql($sql);
        $sql = "UPDATE users SET reward_codes = 0,balance= balance + $amount , today_codes = today_codes + $reward_codes , total_codes = total_codes + $reward_codes , earn= earn + $amount WHERE id=" . $user_id;
        $db->sql($sql);

    }
    if($wallet_type == 'ch_daily_wallet'){
        if ($ch_daily_wallet < 30) {
            $response['success'] = false;
            $response['message'] = "Minimum ₹30 to add balance";
            print_r(json_encode($response));
            return false;
        }
        $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'ch_daily_wallet','$datetime',$ch_daily_wallet)";
        $db->sql($sql);
        $sql = "UPDATE users SET balance= balance + ch_daily_wallet,earn = earn + ch_daily_wallet,ch_daily_wallet = 0 WHERE id=" . $user_id;
        $db->sql($sql);

    }
    if($wallet_type == 'ch_monthly_wallet'){
        if ($ch_monthly_wallet < 1000) {
            $response['success'] = false;
            $response['message'] = "Minimum ₹1000 to add balance";
            print_r(json_encode($response));
            return false;
        }
        $sql_query = "SELECT * FROM `champion_refer_bonus` WHERE user_id = $user_id AND status = 0 ORDER BY id ";
        $db->sql($sql_query);
        $res = $db->getResult();
        $num = $db->numRows($res);
        if($num>=1){
            $bonus_id = $res[0]['id'];
            $bonus_wallet = $res[0]['amount'];
            $sql = "UPDATE champion_refer_bonus SET status= 1 WHERE id=" . $bonus_id;
            $db->sql($sql);

        }else{
            $response['success'] = false;
            $response['message'] = "Refer 1 Person to get ₹1000";
            print_r(json_encode($response));
            return false;

        }

        $sql = "INSERT INTO transactions (`user_id`,`type`,`datetime`,`amount`) VALUES ($user_id,'ch_monthly_wallet','$datetime',$bonus_wallet)";
        $db->sql($sql);
        $sql = "UPDATE users SET balance= balance + $bonus_wallet,earn = earn + $bonus_wallet,ch_monthly_wallet = ch_monthly_wallet - $bonus_wallet WHERE id=" . $user_id;
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
