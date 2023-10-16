<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;
?>
<?php

if (isset($_GET['id'])) {
    $ID = $db->escapeString($_GET['id']);
} else {
    // $ID = "";
    return false;
    exit(0);
}
if (isset($_POST['btnEdit'])) {
            $datetime = date('Y-m-d H:i:s');
            $date = date('Y-m-d');

            $name = $db->escapeString(($_POST['name']));
            $device_id = (isset($_POST['device_id']) && !empty($_POST['device_id'])) ? $db->escapeString($_POST['device_id']) : '';
            $mobile = $db->escapeString(($_POST['mobile']));
            $password = $db->escapeString(($_POST['password']));
            $dob = $db->escapeString(($_POST['dob']));
            $email = $db->escapeString(($_POST['email']));
            $city = $db->escapeString(($_POST['city']));
            $status = $db->escapeString(($_POST['status']));
            $refer_code = $db->escapeString(($_POST['refer_code']));
            $security = $db->escapeString(($_POST['security']));
            $black_box = $db->escapeString(($_POST['black_box']));
            $joined_date = (isset($_POST['joined_date']) && !empty($_POST['joined_date'])) ? $db->escapeString($_POST['joined_date']) : $date;
            $code_generate_time = $db->escapeString(($_POST['code_generate_time']));
            $withdrawal_status = $db->escapeString(($_POST['withdrawal_status']));
            $refer_bonus_sent = (isset($_POST['refer_bonus_sent']) && !empty($_POST['refer_bonus_sent'])) ? $db->escapeString($_POST['refer_bonus_sent']) : 0;
            $register_bonus_sent = (isset($_POST['register_bonus_sent']) && !empty($_POST['register_bonus_sent'])) ? $db->escapeString($_POST['register_bonus_sent']) : 0;
            $target_bonus_sent = (isset($_POST['target_bonus_sent']) && !empty($_POST['target_bonus_sent'])) ? $db->escapeString($_POST['target_bonus_sent']) : 0;
            $referred_by = (isset($_POST['referred_by']) && !empty($_POST['referred_by'])) ? $db->escapeString($_POST['referred_by']) : "";
            $earn = (isset($_POST['earn']) && !empty($_POST['earn'])) ? $db->escapeString($_POST['earn']) : 0;
            $code_generate = (isset($_POST['code_generate']) && !empty($_POST['code_generate'])) ? $db->escapeString($_POST['code_generate']) : 0;
            $balance = (isset($_POST['balance']) && !empty($_POST['balance'])) ? $db->escapeString($_POST['balance']) : "0";
        
            $today_codes = (isset($_POST['today_codes']) && !empty($_POST['today_codes'])) ? $db->escapeString($_POST['today_codes']) : 0;
            $total_codes = (isset($_POST['total_codes']) && !empty($_POST['total_codes'])) ? $db->escapeString($_POST['total_codes']) : 0;
            $join_type = (isset($_POST['join_type']) && !empty($_POST['join_type'])) ? $db->escapeString($_POST['join_type']) : 0;
            $mcg_timer = $db->escapeString(($_POST['mcg_timer']));
            $salary_advance_balance = $db->escapeString(($_POST['salary_advance_balance']));
            $duration = $db->escapeString(($_POST['duration']));
            $worked_days = $db->escapeString(($_POST['worked_days']));
            $lead_id = $db->escapeString(($_POST['lead_id']));
            $support_id = $db->escapeString(($_POST['support_id']));
            $branch_id = $db->escapeString(($_POST['branch_id']));
          
            $trial_wallet = $db->escapeString(($_POST['trial_wallet']));
            $per_code_cost = $db->escapeString(($_POST['per_code_cost']));
            $num_sync_times = (isset($_POST['num_sync_times']) && !empty($_POST['num_sync_times'])) ? $db->escapeString($_POST['num_sync_times']) : 17;
            $l_referral_count = (isset($_POST['l_referral_count']) && !empty($_POST['l_referral_count'])) ? $db->escapeString($_POST['l_referral_count']) : 0;

            $sa_withdrawal = $db->escapeString(($_POST['sa_withdrawal']));
            $level = $db->escapeString(($_POST['level']));
            $per_code_val = $db->escapeString(($_POST['per_code_val']));
            $earnings_wallet = $db->escapeString(($_POST['earnings_wallet']));
            $bonus_wallet = $db->escapeString(($_POST['bonus_wallet']));
            $project_type = $db->escapeString(($_POST['project_type']));
            $today_mails = $db->escapeString(($_POST['today_mails']));
            $total_mails = $db->escapeString(($_POST['total_mails']));
            $current_refers = $db->escapeString(($_POST['current_refers']));
            $target_refers = $db->escapeString(($_POST['target_refers']));
            $daily_wallet = $db->escapeString(($_POST['daily_wallet']));
            $monthly_wallet = $db->escapeString(($_POST['monthly_wallet']));
            $ch_daily_wallet = $db->escapeString(($_POST['ch_daily_wallet']));
            $ch_monthly_wallet = $db->escapeString(($_POST['ch_monthly_wallet']));
            $reward_codes = $db->escapeString(($_POST['reward_codes']));

            $error = array();

            if (empty($lead_id)) {
                $error['update_users'] = " <span class='label label-danger'> Lead Required!</span>";
            }
            if (empty($support_id)) {
                $error['update_users'] = " <span class='label label-danger'> Support Required!</span>";
            }
            if (empty($branch_id)) {
                $error['update_users'] = " <span class='label label-danger'> Branch Required!</span>";
            }

     if (!empty($name) && !empty($mobile) && !empty($password)&& !empty($dob) && !empty($email)&& !empty($city) && 
     !empty($code_generate_time) && 
     !empty($lead_id)  && 
     !empty($support_id) && 
     !empty($branch_id)) {
        $refer_bonus_sent = $fn->get_value('users','refer_bonus_sent',$ID);

        if($status == 1 && !empty($referred_by) && $refer_bonus_sent != 1){
            $refer_bonus_codes = $function->getSettingsVal('refer_bonus_codes');
            $referral_bonus = 250;
            $sql_query = "SELECT *,datediff('$date', joined_date) AS history_days FROM users WHERE refer_code =  '$referred_by'";
            $db->sql($sql_query);
            $res = $db->getResult();
            $num = $db->numRows($res);
            if ($num == 1){
                $user_id = $res[0]['id'];
                $user_project_type = $res[0]['project_type'];
                if($user_project_type == 'amail'){
                    $user_current_refers = $res[0]['current_refers'];
                    $user_target_refers = $res[0]['target_refers'];
                    $referral_bonus = 500;
                    $sql_query = "UPDATE users SET `current_refers` = current_refers + 1,`l_referral_count` = l_referral_count + 1,`earn` = earn + $referral_bonus,`balance` = balance + $referral_bonus WHERE id =  $user_id AND status = 1";
                    $db->sql($sql_query);
                    $sql_query = "INSERT INTO bonus_refer_bonus (user_id,refer_user_id,status,amount,datetime)VALUES($user_id,$ID,0,700,'$datetime')";
                    $db->sql($sql_query);
                    $sql_query = "INSERT INTO transactions (user_id,amount,datetime,type)VALUES($user_id,$referral_bonus,'$datetime','refer_bonus')";
                    $db->sql($sql_query);

                }elseif($user_project_type == 'champion'){
                    $user_current_refers = $res[0]['current_refers'];
                    $user_target_refers = $res[0]['target_refers'];
                    $referral_bonus = 600;
                    if($project_type == 'champion'){
                        $pre_bonus = 1000;
                    

                    }else{
                        $pre_bonus = 500;
                    
                    }
                    
                    $sql_query = "UPDATE users SET `l_referral_count` = l_referral_count + 1,`earn` = earn + $referral_bonus,`balance` = balance + $referral_bonus WHERE id =  $user_id  AND status = 1";
                    $db->sql($sql_query);
                    $sql_query = "INSERT INTO champion_refer_bonus (user_id,refer_user_id,status,amount,datetime)VALUES($user_id,$ID,0,$pre_bonus,'$datetime')";
                    $db->sql($sql_query);
                    $sql_query = "INSERT INTO transactions (user_id,amount,datetime,type)VALUES($user_id,$referral_bonus,'$datetime','refer_bonus')";
                    $db->sql($sql_query);

                    $fn->update_refer_code_cost_champion($user_id);

                }else{
                    $set_duration = $function->getSettingsVal('duration');
                   
                    $sql_query = "SELECT id FROM leaves WHERE user_id =  $user_id AND date = '$date'";
                    $db->sql($sql_query);
                    $lres = $db->getResult();
                    $lnum = $db->numRows($lres);
                    $ref_code_generate = $res[0]['code_generate'];
                    $ref_worked_days = $res[0]['worked_days'];
                    $ref_duration = $res[0]['duration'];
                    $ref_user_status = $res[0]['status'];
                    $ref_user_history_days = $res[0]['history_days'];
                    $ref_total_refund = $res[0]['total_refund'];
                    if($ref_user_status == 1 && ($ref_code_generate == 1 || $ref_code_generate == 0 && $ref_worked_days < $ref_duration && $lnum == 1)  ){
                        $referral_bonus = $function->getSettingsVal('refer_bonus_amount');
    
                    }
                    else if($ref_user_status == 1 && $ref_code_generate == 0 && $ref_worked_days >= $ref_duration ){
                        $referral_bonus = 500;
    
                    }
    
                    $sa_refer_count=$res[0]['sa_refer_count'];
                    $refer_sa_balance=200;
                
                  
                    $sql_query = "UPDATE users SET `l_referral_count` = l_referral_count + 1,`earn` = earn + $referral_bonus,`balance` = balance + $referral_bonus,`salary_advance_balance`=salary_advance_balance +$refer_sa_balance,`sa_refer_count`=sa_refer_count + 1 WHERE id =  $user_id";
                    $db->sql($sql_query);
                    $fn->update_refer_code_cost($user_id);
                    $sql_query = "INSERT INTO transactions (user_id,amount,datetime,type)VALUES($user_id,$referral_bonus,'$datetime','refer_bonus')";
                    $db->sql($sql_query);
                    $sql_query = "INSERT INTO salary_advance_trans (user_id,refer_user_id,amount,datetime,type)VALUES($ID,$user_id,'$refer_sa_balance','$datetime','credit')";
                    $db->sql($sql_query);
                    if($ref_user_status == 1 && ($ref_code_generate == 1 || $ref_code_generate == 0 && $ref_worked_days < $ref_duration)  ){
    
                        $ref_per_code_cost = $fn->get_code_per_cost($user_id);
    
    
                        $amount = $refer_bonus_codes  * $ref_per_code_cost;
                        $sql_query = "UPDATE users SET `earn` = earn + $amount,`balance` = balance + $amount,`today_codes` = today_codes + $refer_bonus_codes,`total_codes` = total_codes + $refer_bonus_codes WHERE refer_code =  '$referred_by' AND status = 1";
                        $db->sql($sql_query);
                        $sql_query = "INSERT INTO transactions (user_id,amount,codes,datetime,type)VALUES($user_id,$amount,$refer_bonus_codes,'$datetime','code_bonus')";
                        $db->sql($sql_query);
                    }

                }

                $sql_query = "UPDATE users SET refer_bonus_sent = 1 WHERE id =  $ID";
                $db->sql($sql_query);

            }


        }
   
        $fn->update_refer_code_cost($ID);
        $register_bonus_sent = $fn->get_value('users','register_bonus_sent',$ID);


        if($status == 1 && $register_bonus_sent != 1 && $join_type == 0){
            if($project_type == 'amail'){
                $per_code_cost = 0.17;
                $per_code_val = 1;
                $duration = 300;
                $sql_query = "UPDATE users SET register_bonus_sent = 1 WHERE id =  $ID";
                $db->sql($sql_query);
                $sql_query = "INSERT INTO transactions (user_id,amount,codes,datetime,type)VALUES($ID,0,0,'$datetime','register_amail')";
                $db->sql($sql_query);
                if(strlen($referred_by) == 3){
                    $incentives = 125;
                }else{
                    $incentives = 15;
                    
                }

            }else if($project_type == 'champion'){
                $per_code_val = 1;
                $per_code_cost = 3;
                $duration = 300;
                $sql_query = "UPDATE users SET register_bonus_sent = 1 WHERE id =  $ID";
                $db->sql($sql_query);
                $sql_query = "INSERT INTO transactions (user_id,amount,codes,datetime,type)VALUES($ID,0,0,'$datetime','register_champion')";
                $db->sql($sql_query);
                if(strlen($referred_by) == 3){
                    $incentives = 125;
                }else{
                    $incentives = 15;
                    
                }

            }else{
                   $per_code_cost = 3;
                $join_codes = $function->getSettingsVal('join_codes');
                $amount = $join_codes  * $per_code_cost;
                $register_bonus = $amount;
                $total_codes = $total_codes + $join_codes;
                $today_codes = $today_codes + $join_codes;
                $salary_advance_balance = $salary_advance_balance + 200;
                $earn = $earn + $register_bonus;
                $balance = $balance + $register_bonus;
                $duration = $plan;
    
                $sql_query = "UPDATE users SET register_bonus_sent = 1 WHERE id =  $ID";
                $db->sql($sql_query);
                $sql_query = "INSERT INTO transactions (user_id,amount,codes,datetime,type)VALUES($ID,$amount,$join_codes,'$datetime','register_bonus')";
                $db->sql($sql_query);
                if(strlen($referred_by) == 3){
                    $incentives = 100;
                }else{

                    
                }

            }


            $sql_query = "UPDATE staffs SET incentives = incentives + $incentives,earn = earn + $incentives,balance = balance + $incentives,supports = supports + 1 WHERE id =  $support_id";
            $db->sql($sql_query);

            $sql_query = "UPDATE staffs SET incentives = incentives + $incentives,earn = earn + $incentives,balance = balance + $incentives,leads = leads + 1 WHERE id =  $lead_id";
            $db->sql($sql_query);
            
            $sql_query = "INSERT INTO incentives (user_id,staff_id,amount,datetime,type)VALUES($ID,$support_id,$incentives,'$datetime','support')";
            $db->sql($sql_query);

            $sql_query = "INSERT INTO incentives (user_id,staff_id,amount,datetime,type)VALUES($ID,$lead_id,$incentives,'$datetime','lead')";
            $db->sql($sql_query);

            $sql_query = "INSERT INTO staff_transactions (staff_id,amount,datetime,type)VALUES($support_id,$incentives,'$datetime','incentives')";
            $db->sql($sql_query);

            $sql_query = "INSERT INTO staff_transactions (staff_id,amount,datetime,type)VALUES($lead_id,$incentives,'$datetime','incentives')";
            $db->sql($sql_query);
            
        }
        if($status == 1 && ($join_type == 1 || $join_type == 2 || $join_type == 3)){
            $total_codes = 0;
            $today_codes = 0;
      
            $withdrawal = 0;
            $worked_days = 0;
            $level = 1;
            $l_referral_count = 0;
            $per_code_val = 1;
            $salary_advance_balance = 200;
            $joined_date = $date;
            $target_bonus_sent = 0;

            $incentives = 25;
            $reward_codes = 0;
            

            if($join_type == 1){

                $referred_by = 'rejoin';
    
                $sql_query = "UPDATE staffs SET incentives = incentives + $incentives,earn = earn + $incentives,balance = balance + $incentives,supports = supports + 1 WHERE id =  $support_id";
                $db->sql($sql_query);
    
                $sql_query = "UPDATE staffs SET incentives = incentives + $incentives,earn = earn + $incentives,balance = balance + $incentives,leads = leads + 1 WHERE id =  $lead_id";
                $db->sql($sql_query);
                
                $sql_query = "INSERT INTO incentives (user_id,staff_id,amount,datetime,type)VALUES($ID,$support_id,$incentives,'$datetime','support')";
                $db->sql($sql_query);
    
                $sql_query = "INSERT INTO incentives (user_id,staff_id,amount,datetime,type)VALUES($ID,$lead_id,$incentives,'$datetime','lead')";
                $db->sql($sql_query);
    
                $sql_query = "INSERT INTO staff_transactions (staff_id,amount,datetime,type)VALUES($support_id,$incentives,'$datetime','incentives')";
                $db->sql($sql_query);
    
                $sql_query = "INSERT INTO staff_transactions (staff_id,amount,datetime,type)VALUES($lead_id,$incentives,'$datetime','incentives')";
                $db->sql($sql_query);

                $sql_query = "DELETE FROM `leaves` WHERE user_id = $ID";
                $db->sql($sql_query);

            }if($join_type == 3){

                $referred_by = 'unlimited_shift';
                $per_code_cost = 0.17;
                
    


            }else{
                $referred_by = 'free';
                $per_code_cost = 0.12;
            }
            
        }
       
        
        if($project_type == 'champion'){
            $per_code_cost = $fn->update_refer_code_cost_champion($ID);
        }
        else{
            if($referred_by == 'free'){
                $per_code_cost = 0.12;

            }else{
                $per_code_cost = 0.17;
            }
         
        }
        
        if($project_type == 'amail'){
            $duration = 300;
        }
        if($status == 0 && $project_type == 'free_project'){
            $per_code_cost = 0.06;
        } 
    
        $sql_query = "UPDATE users SET name='$name', mobile='$mobile', password='$password', dob='$dob', email='$email', city='$city', refer_code='$refer_code', referred_by='$referred_by', earn='$earn', balance='$balance', withdrawal_status=$withdrawal_status,total_codes=$total_codes, today_codes=$today_codes,device_id='$device_id',status = $status,code_generate = $code_generate,code_generate_time = $code_generate_time,joined_date = '$joined_date',mcg_timer='$mcg_timer',security='$security',black_box='$black_box',salary_advance_balance='$salary_advance_balance',duration='$duration',worked_days='$worked_days',lead_id='$lead_id',support_id='$support_id',branch_id='$branch_id',trial_wallet='$trial_wallet',per_code_cost=$per_code_cost,num_sync_times=$num_sync_times,l_referral_count=$l_referral_count,sa_withdrawal=$sa_withdrawal,level=$level,per_code_val=$per_code_val,earnings_wallet=$earnings_wallet,bonus_wallet=$bonus_wallet,project_type='$project_type' ,today_mails=$today_mails,total_mails=$total_mails,current_refers=$current_refers,target_refers=$target_refers,daily_wallet=$daily_wallet,monthly_wallet=$monthly_wallet,target_bonus_sent = $target_bonus_sent,ch_daily_wallet = $ch_daily_wallet,ch_monthly_wallet = $ch_monthly_wallet,reward_codes=$reward_codes WHERE id =  $ID";
        $db->sql($sql_query);
        $update_result = $db->getResult();
        if (!empty($update_result)) {
            $update_result = 0;
        } else {
            $update_result = 1;
        }

        // check update result
        if ($update_result == 1) {
            $error['update_users'] = " <section class='content-header'><span class='label label-success'>Users updated Successfully</span></section>";
        } else {
            $error['update_users'] = " <span class='label label-danger'>Failed update users</span>";
        }


    }
}


// create array variable to store previous data
$data = array();
$sql_query = "SELECT id FROM leaves WHERE user_id =" . $ID;
$db->sql($sql_query);
$lres = $db->getResult();
$balance_leave = 4 - $db->numRows($lres);
$sql_query = "SELECT * FROM users WHERE id =" . $ID;
$db->sql($sql_query);
$res = $db->getResult();
if (isset($_POST['btnCancel'])) { ?>
    <script>
        window.location.href = "users.php";
    </script>
<?php } ?>
<section class="content-header">
    <h1>
        Edit User<small><a href='users.php'><i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Users</a></small></h1>
    <small><?php echo isset($error['update_users']) ? $error['update_users'] : ''; ?></small>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
</section>
<section class="content">
</secction>
<section class="content">
    <!-- Main row -->

    <div class="row">
        <div class="col-md-10">
            <div class="box box-primary">
                <div class="box-header with-border">


                <!-- /.box-header -->
                <!-- form start -->
                <form id="edit_user_form" method="post" enctype="multipart/form-data">
                    <input type="hidden" class="form-control" name="refer_bonus_sent" value="<?php echo $res[0]['refer_bonus_sent']; ?>">
                    <input type="hidden" class="form-control" name="register_bonus_sent" value="<?php echo $res[0]['register_bonus_sent']; ?>">
                    <input type="hidden" class="form-control" name="target_bonus_sent" value="<?php echo $res[0]['target_bonus_sent']; ?>">
                    <div class="box-body">
                    <div class="row">
                            <div class="form-group">
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Name</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="name" value="<?php echo $res[0]['name']; ?>">
                                </div>
                                <div class="col-md-3" >
                                    <label for="exampleInputEmail1">Phone Number</label><i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="mobile" value="<?php echo $res[0]['mobile']; ?>" style="background-color: #7EC8E3;">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Password</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="password" value="<?php echo $res[0]['password']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Referred By</label>
                                    <input type="text" class="form-control" name="referred_by" value="<?php echo $res[0]['referred_by']; ?>" style="background-color: #7EC8E3;">
                                </div>


                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">

                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Refer Code</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="refer_code" value="<?php echo $res[0]['refer_code']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Joined Date</label><i class="text-danger asterik">*</i>
                                    <input type="date" class="form-control" name="joined_date" value="<?php echo $res[0]['joined_date']; ?>" <?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>

                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">E-mail</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="email" value="<?php echo $res[0]['email']; ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">City</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="city" value="<?php echo $res[0]['city']; ?>">
                                </div>


                            </div>
                        </div>
                        <br>
                    <div class="row">
                            <div class="form-group col-md-3">
                                    <label for="exampleInputEmail1">Select Lead</label> <i class="text-danger asterik">*</i>
                                    <select id='lead_id' name="lead_id" class='form-control' style="background-color: #7EC8E3">
                                           <option value="">--Select--</option>
                                                <?php
                                                $sql = "SELECT * FROM `staffs`";
                                                $db->sql($sql);

                                                $result = $db->getResult();
                                                foreach ($result as $value) {
                                                ?>
                                                    <option value='<?= $value['id'] ?>' <?= $value['id']==$res[0]['lead_id'] ? 'selected="selected"' : '';?>><?= $value['name'] ?></option>
                                                    
                                                <?php } ?>
                                    </select>
                            </div>
                            <div class="form-group col-md-3">
                                    <label for="exampleInputEmail1">Select Support</label> <i class="text-danger asterik">*</i>
                                    <select id='support_id' name="support_id" class='form-control' style="background-color: #7EC8E3">
                                             <option value="">--Select--</option>
                                                <?php
                                                $sql = "SELECT * FROM `staffs`";
                                                $db->sql($sql);

                                                $result = $db->getResult();
                                                foreach ($result as $value) {
                                                ?>
                                                    <option value='<?= $value['id'] ?>' <?= $value['id']==$res[0]['support_id'] ? 'selected="selected"' : '';?>><?= $value['name'] ?></option>
                                                    
                                                <?php } ?>
                                    </select>
                            </div>
                            <div class="form-group col-md-3">
                                    <label for="exampleInputEmail1">Select Branch</label> <i class="text-danger asterik">*</i>
                                    <select id='branch_id' name="branch_id" class='form-control'>
                                           <option value="">--Select--</option>
                                                <?php
                                                $sql = "SELECT * FROM `branches`";
                                                $db->sql($sql);

                                                $result = $db->getResult();
                                                foreach ($result as $value) {
                                                ?>
                                                    <option value='<?= $value['id'] ?>' <?= $value['id']==$res[0]['branch_id'] ? 'selected="selected"' : '';?>><?= $value['name'] ?></option>
                                                    
                                                <?php } ?>
                                    </select>
                            </div>
                            <div class="form-group col-md-3">
                            <label for="exampleInputEmail1">Join Type</label> <i class="text-danger asterik">*</i>
                                    <select id='join_type' name="join_type" class='form-control'>
                                           <option value="0">None</option>
                                           <option value='1'>Rejoin</option>
                                           <option value='2'>Free</option>
                                           <option value='3'>unlimited_shift</option>
                                    </select>
                                </div>
                        </div>
                        <br>
                        <div class="row">
                        
                                <div class="col-md-3">
                                   <label for="exampleInputEmail1">Project Type</label> <i class="text-danger asterik">*</i>
                                    <select id='project_type' name="project_type" class='form-control'>
                                     <option value='abcd' <?php if ($res[0]['project_type'] == 'abcd') echo 'selected'; ?>>abcd</option>
                                      <option value='amail' <?php if ($res[0]['project_type'] == 'amail') echo 'selected'; ?>>amail</option>
                                      <option value='champion' <?php if ($res[0]['project_type'] == 'champion') echo 'selected'; ?>>champion</option>
                                      <option value='free_project' <?php if ($res[0]['project_type'] == 'free_project') echo 'selected'; ?>>Free Project</option>
                                      <option value='unlimited' <?php if ($res[0]['project_type'] == 'unlimited') echo 'selected'; ?>>unlimited</option>
                                    </select>
                                    </div>
                                <div class="form-group col-md-5">
                                    <label class="control-label">Status</label><i class="text-danger asterik">*</i><br>
                                    <div id="status" class="btn-group">
                                        <label class="btn btn-primary" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                            <input type="radio" name="status" value="0" <?= ($res[0]['status'] == 0) ? 'checked' : ''; ?>> Not-verified
                                        </label>
                                        <label class="btn btn-success" data-toggle-class="btn-default" data-toggle-passive-class="btn-default">
                                            <input type="radio" name="status" value="1" <?= ($res[0]['status'] == 1) ? 'checked' : ''; ?>> Verified
                                        </label>
                                        <label class="btn btn-danger" data-toggle-class="btn-default" data-toggle-passive-class="btn-default">
                                            <input type="radio" name="status" value="2" <?= ($res[0]['status'] == 2) ? 'checked' : ''; ?>> Blocked
                                        </label>
                                    </div>
                                </div>
                        </div>
                        <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnEdit">Update</button>

                    </div>
                    <hr>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Date of Birth</label><i class="text-danger asterik">*</i>
                                    <input type="date" class="form-control" name="dob" value="<?php echo $res[0]['dob']; ?>" required <?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                </div>

                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Device Id</label>
                                    <input type="text" class="form-control" name="device_id" value="<?php echo $res[0]['device_id']; ?>" <?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Level Referral Count</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="l_referral_count" value="<?php echo $res[0]['l_referral_count']; ?>"<?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Reward Codes</label><i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="reward_codes" value="<?php echo $res[0]['reward_codes']; ?>"<?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                </div>

                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                            <div class="col-md-3">
                                    <label for="exampleInputEmail1">Earn</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="earn" value="<?php echo $res[0]['earn']; ?>"<?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                </div>

                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Balance</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="balance" value="<?php echo $res[0]['balance']; ?>"<?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Today Codes</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="today_codes" value="<?php echo $res[0]['today_codes']; ?>"<?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Total Codes</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="total_codes" value="<?php echo $res[0]['total_codes']; ?>"<?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                </div>
                            </div>
                        </div>

                                <br>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Code Generate</label><br>
                                    <input type="checkbox" id="code_generate_button" class="js-switch" <?= isset($res[0]['code_generate']) && $res[0]['code_generate'] == 1 ? 'checked' : '' ?>>
                                    <input type="hidden" id="code_generate_status" name="code_generate" value="<?= isset($res[0]['code_generate']) && $res[0]['code_generate'] == 1 ? 1 : 0 ?>" >
                                </div>

                            </div>
                            <div class="col-md-3">
                                    <label for="exampleInputEmail1">Code Generate Time</label><i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="code_generate_time" value="<?php echo $res[0]['code_generate_time']; ?>" <?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Num Sync Times</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="num_sync_times" value="<?php echo $res[0]['num_sync_times']; ?>" <?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                </div>

                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">MCG Timer</label><i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="mcg_timer" value="<?php echo $res[0]['mcg_timer']; ?>">
                                </div>

                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Withdrawal Status</label><br>
                                    <input type="checkbox" id="withdrawal_button" class="js-switch" <?= isset($res[0]['withdrawal_status']) && $res[0]['withdrawal_status'] == 1 ? 'checked' : '' ?>>
                                    <input type="hidden" id="withdrawal_status" name="withdrawal_status" value="<?= isset($res[0]['withdrawal_status']) && $res[0]['withdrawal_status'] == 1 ? 1 : 0 ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Security</label><br>
                                    <input type="checkbox" id="security_button" class="js-switch" <?= isset($res[0]['security']) && $res[0]['security'] == 1 ? 'checked' : '' ?>>
                                    <input type="hidden" id="security" name="security" value="<?= isset($res[0]['security']) && $res[0]['security'] == 1 ? 1 : 0 ?>">
                                </div>
                                
                            </div>
                            <div class="col-md-3">
                                <label for="exampleInputEmail1">Salary Advance Balance</label><i class="text-danger asterik">*</i>
                                <input type="text" class="form-control" name="salary_advance_balance" value="<?php echo $res[0]['salary_advance_balance']; ?>" <?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                            </div>
                            <div class="col-md-3">
                                    <label for="exampleInputEmail1">Per Code Cost</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="per_code_cost" value="<?php echo $res[0]['per_code_cost']; ?>" <?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Duration</label><i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="duration" value="<?php echo $res[0]['duration']; ?>" <?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Worked Days</label><i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="worked_days" value="<?php echo $res[0]['worked_days']; ?>" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Trial Wallet</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="trial_wallet" value="<?php echo $res[0]['trial_wallet']; ?>"<?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                </div>
                                <div class="col-md-3">
                                    <label for="">Salary Advance Withdrawal</label><br>
                                    <input type="checkbox" id="sa_withdrawal_button" class="js-switch" <?= isset($res[0]['sa_withdrawal']) && $res[0]['sa_withdrawal'] == 1 ? 'checked' : '' ?>>
                                    <input type="hidden" id="sa_withdrawal" name="sa_withdrawal" value="<?= isset($res[0]['sa_withdrawal']) && $res[0]['sa_withdrawal'] == 1 ? 1 : 0 ?>">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Level</label><i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="level" value="<?php echo $res[0]['level']; ?>" <?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Per Code Value</label><i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="per_code_val" value="<?php echo $res[0]['per_code_val']; ?>" <?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Balance Leave</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="balance_leave" value="<?php echo $balance_leave ?>" readonly>
                                </div>
                                <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Black Box</label><br>
                                    <input type="checkbox" id="black_box_button" class="js-switch" <?= isset($res[0]['black_box']) && $res[0]['black_box'] == 1 ? 'checked' : '' ?>>
                                    <input type="hidden" id="black_box" name="black_box" value="<?= isset($res[0]['black_box']) && $res[0]['black_box'] == 1 ? 1 : 0 ?>">
                                </div>
                              </div>
                           </div>
                         </div>
                        <br>
                        <div class="row">   
                                  <div class="form-group">
                                    <div class="col-md-3">
                                       <label for="exampleInputEmail1">Today Mails</label><i class="text-danger asterik">*</i>
                                        <input type="number" class="form-control" name="today_mails" value="<?php echo $res[0]['today_mails']; ?>" <?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                     </div>
                                 <div class="col-md-3">
                                        <label for="exampleInputEmail1">Total Mails</label><i class="text-danger asterik">*</i>
                                        <input type="number" class="form-control" name="total_mails" value="<?php echo $res[0]['total_mails']; ?>" <?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                    </div>
                                <div class="col-md-3">
                                         <label for="exampleInputEmail1">Current Refers</label><i class="text-danger asterik">*</i>
                                         <input type="number" class="form-control" name="current_refers" value="<?php echo $res[0]['current_refers']; ?>"<?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                     </div>
                                <div class="col-md-3">
                                         <label for="exampleInputEmail1">Target Refers</label><i class="text-danger asterik">*</i>
                                         <input type="number" class="form-control" name="target_refers" value="<?php echo $res[0]['target_refers']; ?>"<?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                    </div>
                                </div>
                            </div>
                         <br>
                     <div class="row">   
                            <div class="form-group">
                                  <div class="col-md-3">
                                        <label for="exampleInputEmail1">Earning Wallet</label><i class="text-danger asterik">*</i>
                                        <input type="number" class="form-control" name="earnings_wallet" value="<?php echo $res[0]['earnings_wallet']; ?>"style="background-color:#e6def3;" <?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                    </div>
                                 <div class="col-md-3">
                                        <label for="exampleInputEmail1">Bonus Wallet</label><i class="text-danger asterik">*</i>
                                         <input type="number" class="form-control" name="bonus_wallet" value="<?php echo $res[0]['bonus_wallet']; ?>"style="background-color:#e6def3;" <?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                    </div>
                                 <div class="col-md-3">
                                        <label for="exampleInputEmail1">Daily Wallet</label><i class="text-danger asterik">*</i>
                                        <input type="number" class="form-control" name="daily_wallet" value="<?php echo $res[0]['daily_wallet']; ?>"style="background-color:#e6def3;" <?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                    </div>
                                <div class="col-md-3">
                                       <label for="exampleInputEmail1">Monthly Wallet</label><i class="text-danger asterik">*</i>
                                        <input type="number" class="form-control" name="monthly_wallet" value="<?php echo $res[0]['monthly_wallet']; ?>"style="background-color:#e6def3;" <?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                     </div>
                                  </div>
                             </div><!-- /.box-body -->
                    <div class="row">   
                            <div class="form-group">
                                  <div class="col-md-3">
                                        <label for="exampleInputEmail1">Champion Daily Wallet</label><i class="text-danger asterik">*</i>
                                        <input type="number" class="form-control" name="ch_daily_wallet" value="<?php echo $res[0]['ch_daily_wallet']; ?>"style="background-color:#e6def3;" <?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                    </div>
                                 <div class="col-md-3">
                                        <label for="exampleInputEmail1">Champion Monthly Wallet</label><i class="text-danger asterik">*</i>
                                         <input type="number" class="form-control" name="ch_monthly_wallet" value="<?php echo $res[0]['ch_monthly_wallet']; ?>"style="background-color:#e6def3;" <?php if($_SESSION['role'] == 'Admin'){ echo 'readonly'; } ?>>
                                    </div>
                                </div>
                             </div><!-- /.box-body -->
                       </form>
                     <br>
                         <div class="form-group col-md-3">
                                <h4 class="box-title"> </h4>
                                <a class="btn btn-block btn-primary" href="add-codes.php?id=<?php echo $ID ?>"><i class="fa fa-plus-square"></i> Add Codes</a>
                            </div>
                         <div class="form-group col-md-3">
                                <h4 class="box-title"> </h4>
                                <a class="btn btn-block btn-success" href="add-balance.php?id=<?php echo $ID ?>"><i class="fa fa-plus-square"></i>  Add Balance</a>
                            </div>
                    </div>
                <div class="box-header">
                    <?php echo isset($error['cancelable']) ? '<span class="label label-danger">Till status is required.</span>' : ''; ?>
                </div>
            </div><!-- /.box -->
        </div>
    </div>
</section>

<div class="separator"> </div>
<?php $db->disconnect(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<?php if($_SESSION['role'] == 'Super Admin'){?> 
<script>
    var changeCheckbox = document.querySelector('#code_generate_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#code_generate_status').val(1);

        } else {
            $('#code_generate_status').val(0);
        }
    };
</script>

<script>
    var changeCheckbox = document.querySelector('#withdrawal_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#withdrawal_status').val(1);

        } else {
            $('#withdrawal_status').val(0);
        }
    };
</script>

<script>
    var changeCheckbox = document.querySelector('#sa_withdrawal_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#sa_withdrawal').val(1);

        } else {
            $('#sa_withdrawal').val(0);
        }
    };
</script>
<script>
    var changeCheckbox = document.querySelector('#security_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#security').val(1);

        } else {
            $('#security').val(0);
        }
    };
</script>
<script>
    var changeCheckbox = document.querySelector('#black_box_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#black_box').val(1);

        } else {
            $('#black_box').val(0);
        }
    };
</script>


<?php } ?>


