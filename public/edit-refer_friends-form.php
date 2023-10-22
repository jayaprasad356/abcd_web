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

             $friend_mobile = $db->escapeString(($_POST['friend_mobile']));
             $description = $db->escapeString(($_POST['description']));
             $remarks = $db->escapeString(($_POST['remarks']));
             $datetime = $db->escapeString(($_POST['datetime']));
             $status = $db->escapeString(($_POST['status']));
             $chat_support = $db->escapeString(($_POST['chat_support']));
             $users_name = $db->escapeString(($_POST['users_name']));
             $project = $db->escapeString(($_POST['project']));
             $total_codes_done = $db->escapeString(($_POST['total_codes_done']));
             $total_refer_counts = $db->escapeString(($_POST['total_refer_counts']));
             $level = $db->escapeString(($_POST['level']));
             $duration = $db->escapeString(($_POST['duration']));
             $worked_days = $db->escapeString(($_POST['worked_days']));
             $main_wallet_balance = $db->escapeString(($_POST['main_wallet_balance']));
             $monthly_balance = $db->escapeString(($_POST['monthly_balance']));
             $bonus_wallet = $db->escapeString(($_POST['bonus_wallet']));
             $l_refer_counts = $db->escapeString(($_POST['l_refer_counts']));
             $error = array();

     {

        $sql_query = "UPDATE refer_friends SET friend_mobile='$friend_mobile',description='$description',remarks='$remarks',datetime='$datetime',status='$status',chat_support='$chat_support',users_name='$users_name',project='$project',total_codes_done='$total_codes_done',total_refer_counts='$total_refer_counts',level='$level',duration='$duration',worked_days='$worked_days',main_wallet_balance='$main_wallet_balance',monthly_balance='$monthly_balance',bonus_wallet='$bonus_wallet',l_refer_counts='$l_refer_counts' WHERE id =  $ID";
        $db->sql($sql_query);
        $update_result = $db->getResult();
        if (!empty($update_result)) {
            $update_result = 0;
        } else {
            $update_result = 1;
        }

        // check update result
        if ($update_result == 1) {
            $error['update_refer_friends'] = " <section class='content-header'><span class='label label-success'>Refer Friends updated Successfully</span></section>";
        } else {
            $error['update_refer_friends'] = " <span class='label label-danger'>Failed to Update</span>";
        }
    }
}


// create array variable to store previous data
$data = array();

$sql_query = "SELECT * FROM refer_friends WHERE id =" . $ID;
$db->sql($sql_query);
$res = $db->getResult();

$sql_query = "SELECT * FROM refer_friends JOIN users WHERE refer_friends.user_id=users.id" ;
$db->sql($sql_query);
$result = $db->getResult();

if (isset($_POST['btnCancel'])) { ?>
    <script>
        window.location.href = "refer_friends.php";
    </script>
<?php } ?>
<section class="content-header">
    <h1>
        Edit Refer Friends<small><a href='refer_friends.php'><i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Refer Friends</a></small></h1>
    <small><?php echo isset($error['update_refer_friends']) ? $error['update_refer_friends'] : ''; ?></small>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
</section>
<section class="content">
    <!-- Main row -->

    <div class="row">
        <div class="col-md-10">

            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                </div>
                <div class="box-header">
                    <?php echo isset($error['cancelable']) ? '<span class="label label-danger">Till status is required.</span>' : ''; ?>
                </div>

                <!-- /.box-header -->
                <!-- form start -->
                <form id="edit_refer_friends_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-4'>
                                    <label for="exampleInputEmail1">Name</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="name" value="<?php echo $result[0]['name']; ?>" readonly>
                                </div>
                                <div class='col-md-4'>
                                    <label for="exampleInputEmail1">Name</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="mobile" value="<?php echo $result[0]['mobile']; ?>" readonly>
                                </div>
                                <div class='col-md-4'>
                                    <label for="exampleInputEmail1">Friend Mobile</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="friend_mobile" value="<?php echo $result[0]['friend_mobile']; ?>">
                                </div>
                            </div> 
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Date Time</label><i class="text-danger asterik">*</i>
                                    <input type="datetime-local" class="form-control" name="datetime" value="<?php echo $res[0]['datetime']; ?>">
                                </div>
                                <div class='col-md-4'>
                                    <label for="exampleInputEmail1">Description</label> <i class="text-danger asterik">*</i>
                                    <textarea type="text" rows="3" class="form-control" name="description" ><?php echo $res[0]['description']?></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Remarks</label><i class="text-danger asterik">*</i>
                                    <textarea type="text" rows="3" class="form-control" name="remarks" ><?php echo $res[0]['remarks']?></textarea>
                                </div>
                            </div> 
                        </div>
                       <br>
                        <div class="row">
                            <div class="form-group col-md-10">
                                <label class="control-label">Status</label><i class="text-danger asterik">*</i><br>
                                <div id="status" class="btn-group">
                                    <label class="btn btn-primary" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                        <input type="radio" name="status" value="0" <?= ($res[0]['status'] == 0) ? 'checked' : ''; ?>> pending
                                    </label>
                                    <label class="btn btn-success" data-toggle-class="btn-default" data-toggle-passive-class="btn-default">
                                        <input type="radio" name="status" value="1" <?= ($res[0]['status'] == 1) ? 'checked' : ''; ?>> Completed
                                    </label>
                                    <label class="btn btn-danger" data-toggle-class="btn-default" data-toggle-passive-class="btn-default">
                                        <input type="radio" name="status" value="2" <?= ($res[0]['status'] == 2) ? 'checked' : ''; ?>> Cancelled
                                    </label>
                                </div>
                            </div>
						</div>
                        <br>
                        <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnEdit">Update</button>

                    </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Chat support</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="chat_support" value="<?php echo $res[0]['chat_support']; ?>">
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Users name</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="users_name" value="<?php echo $res[0]['users_name']; ?>">
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Project</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="project" value="<?php echo $res[0]['project']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Total Codes Done</label><i class="text-danger asterik">*</i>
                                    <input type="int" class="form-control" name="total_codes_done" value="<?php echo $res[0]['total_codes_done']; ?>">
                                </div>
                            </div> 
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Total Refer Counts</label> <i class="text-danger asterik">*</i>
                                    <input type="int" class="form-control" name="total_refer_counts" value="<?php echo $res[0]['total_refer_counts']; ?>">
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">L Refer Counts</label> <i class="text-danger asterik">*</i>
                                    <input type="int" class="form-control" name="l_refer_counts" value="<?php echo $res[0]['l_refer_counts']; ?>">
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Level</label> <i class="text-danger asterik">*</i>
                                    <input type="int" class="form-control" name="level" value="<?php echo $res[0]['level']; ?>">
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Duration</label> <i class="text-danger asterik">*</i>
                                    <input type="int" class="form-control" name="duration" value="<?php echo $res[0]['duration']; ?>">
                                </div>
                            </div> 
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Worked days</label> <i class="text-danger asterik">*</i>
                                    <input type="int" class="form-control" name="worked_days" value="<?php echo $res[0]['worked_days']; ?>">
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Main Wallet Balance</label> <i class="text-danger asterik">*</i>
                                    <input type="int" class="form-control" name="main_wallet_balance" value="<?php echo $res[0]['main_wallet_balance']; ?>">
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Monthly balance</label> <i class="text-danger asterik">*</i>
                                    <input type="int" class="form-control" name="monthly_balance" value="<?php echo $res[0]['monthly_balance']; ?>">
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Bonus wallet</label> <i class="text-danger asterik">*</i>
                                    <input type="int" class="form-control" name="bonus_wallet" value="<?php echo $res[0]['bonus_wallet']; ?>">
                                </div>
                            </div> 
                        </div>

                    </div><!-- /.box-body -->
                </form>
            </div><!-- /.box -->
        </div>
    </div>
</section>

<div class="separator"> </div>
<?php $db->disconnect(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>