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

             $withdrawal_date = $db->escapeString(($_POST['withdrawal_date']));
             $amount = $db->escapeString(($_POST['amount']));
             $account_num = $db->escapeString(($_POST['account_num']));
             $ifsc_code = $db->escapeString(($_POST['ifsc_code']));
             $remarks = $db->escapeString(($_POST['remarks']));
             $datetime = $db->escapeString(($_POST['datetime']));
             $status = $db->escapeString(($_POST['status']));
             $error = array();

    
		{

        $sql_query = "UPDATE withdrawal_not_receive SET withdrawal_date='$withdrawal_date',amount='$amount',account_num='$account_num',ifsc_code='$ifsc_code',remarks='$remarks',datetime='$datetime',status='$status' WHERE id =  $ID";
        $db->sql($sql_query);
        $update_result = $db->getResult();
        if (!empty($update_result)) {
            $update_result = 0;
        } else {
            $update_result = 1;
        }

        // check update result
        if ($update_result == 1) {
            $error['update_withdrawal_not_receive'] = " <section class='content-header'><span class='label label-success'>withdrawal Not Receive updated Successfully</span></section>";
        } else {
            $error['update_withdrawal_not_receive'] = " <span class='label label-danger'>Failed to Update</span>";
        }
    }
}


// create array variable to store previous data
$data = array();

$sql_query = "SELECT * FROM withdrawal_not_receive WHERE id =" . $ID;
$db->sql($sql_query);
$res = $db->getResult();

$sql_query = "SELECT * FROM withdrawal_not_receive JOIN users WHERE withdrawal_not_receive.user_id=users.id" ;
$db->sql($sql_query);
$result = $db->getResult();

if (isset($_POST['btnCancel'])) { ?>
    <script>
        window.location.href = "withdrawal_not_receive.php";
    </script>
<?php } ?>
<section class="content-header">
    <h1>
        Edit withdrawal Not Receive<small><a href='withdrawal_not_receive.php'><i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to withdrawal Not Receive</a></small></h1>
    <small><?php echo isset($error['update_withdrawal_not_receive']) ? $error['update_withdrawal_not_receive'] : ''; ?></small>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
</section>
<section class="content">
    <!-- Main row -->

    <div class="row">
        <div class="col-md-8">

            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                </div>
                <div class="box-header">
                    <?php echo isset($error['cancelable']) ? '<span class="label label-danger">Till status is required.</span>' : ''; ?>
                </div>

                <!-- /.box-header -->
                <!-- form start -->
                <form id="edit_withdrawal_not_receive_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-6'>
                                    <label for="exampleInputEmail1">Name</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="name" value="<?php echo $result[0]['name']; ?>" readonly>
                                </div>
                                <div class='col-md-6'>
                                    <label for="exampleInputEmail1">Mobile</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="mobile" value="<?php echo $result[0]['mobile']; ?>" readonly>
                                </div>
                            </div> 
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-6'>
                                    <label for="exampleInputEmail1">Withdrawal Date</label> <i class="text-danger asterik">*</i>
                                    <input type="date" class="form-control" name="withdrawal_date" value="<?php echo $result[0]['withdrawal_date']; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputEmail1">Amount</label><i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="amount" value="<?php echo $res[0]['amount']; ?>">
                                </div>
                            </div> 
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-4'>
                                    <label for="exampleInputEmail1">Account Number</label> <i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="account_num" value="<?php echo $result[0]['account_num']; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">IFSC Code</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="ifsc_code" value="<?php echo $res[0]['ifsc_code']; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Date Time</label><i class="text-danger asterik">*</i>
                                    <input type="datetime-local" class="form-control" name="datetime" value="<?php echo $res[0]['datetime']; ?>">
                                </div>
                            </div> 
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="exampleInputEmail1">Remarks</label><i class="text-danger asterik">*</i>
                                    <textarea type="text" rows="3" class="form-control" name="remarks" ><?php echo $res[0]['remarks']?></textarea>
                                </div>
                            </div>
                        </div>
                       <br>
                        <div class="row">
                            <div class="form-group col-md-12">
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

                    </div><!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnEdit">Update</button>

                    </div>
                </form>
            </div><!-- /.box -->
        </div>
    </div>
</section>

<div class="separator"> </div>
<?php $db->disconnect(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
