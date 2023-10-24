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


    $sql_query = "SELECT * FROM withdrawal_not_receive WHERE id = $ID";
    $db->sql($sql_query);
    $res = $db->getResult();

        $user_id = $res[0]['user_id'];

        $sql_query_user = "SELECT * FROM users WHERE id = $user_id";
        $db->sql($sql_query_user);
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
                <form id="edit_withdrawal_not_receive_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-4'>
                                    <label for="exampleInputEmail1">Name</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="name" value="<?php echo $result[0]['name']; ?>" readonly>
                                </div>
                                <div class='col-md-4'>
                                    <label for="exampleInputEmail1">Mobile</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="mobile" value="<?php echo $result[0]['mobile']; ?>" readonly>
                                </div>
                                <div class='col-md-4'>
                                    <label for="exampleInputEmail1">Withdrawal Date</label> <i class="text-danger asterik">*</i>
                                    <input type="date" class="form-control" name="withdrawal_date" value="<?php echo $res[0]['withdrawal_date']; ?>">
                                </div>
                            </div> 
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Amount</label><i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="amount" value="<?php echo $res[0]['amount']; ?>">
                                </div>
                                <div class='col-md-4'>
                                    <label for="exampleInputEmail1">Account Number</label> <i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="account_num" value="<?php echo $res[0]['account_num']; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">IFSC Code</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="ifsc_code" value="<?php echo $res[0]['ifsc_code']; ?>">
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
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Users name</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="name" value="<?php echo $result[0]['name']; ?>" readonly>
                                </div>
                                <div class='col-md-3'>
                                  <label for="exampleInputEmail1">Support name</label> <i class="text-danger asterisk">*</i>
                                 <?php
                                   $support_id = $result[0]['support_id'];
                                    $sql_query = "SELECT name FROM staffs WHERE id = $support_id";
                                    $db->sql($sql_query);
                                    $staffResult = $db->getResult();
                                   if (!empty($staffResult)) {
                                   $support_name = $staffResult[0]['name'];
                                   echo "<input type='text' class='form-control' name='support_id' value='$support_name' readonly>";
                                        } 
                                      ?>
                                   </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Project</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="project_type" value="<?php echo $result[0]['project_type']; ?>" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Total Codes Done</label><i class="text-danger asterik">*</i>
                                    <input type="int" class="form-control" name="total_codes" value="<?php echo $result[0]['total_codes']; ?>" readonly>
                                </div>
                            </div> 
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Total Refer Counts</label> <i class="text-danger asterik">*</i>
                                    <input type="int" class="form-control" name="total_referrals" value="<?php echo $result[0]['total_referrals']; ?>" readonly>
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">L Refer Counts</label> <i class="text-danger asterik">*</i>
                                    <input type="int" class="form-control" name="l_referral_count" value="<?php echo $result[0]['l_referral_count']; ?>" readonly>
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Level</label> <i class="text-danger asterik">*</i>
                                    <input type="int" class="form-control" name="level" value="<?php echo $result[0]['level']; ?>" readonly>
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Duration</label> <i class="text-danger asterik">*</i>
                                    <input type="int" class="form-control" name="duration" value="<?php echo $result[0]['duration']; ?>" readonly>
                                </div>
                            </div> 
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Worked days</label> <i class="text-danger asterik">*</i>
                                    <input type="int" class="form-control" name="worked_days" value="<?php echo $result[0]['worked_days']; ?>" readonly>
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Balance</label> <i class="text-danger asterik">*</i>
                                    <input type="int" class="form-control" name="balance" value="<?php echo $result[0]['balance']; ?>" readonly>
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Monthly Wallet</label> <i class="text-danger asterik">*</i>
                                    <input type="int" class="form-control" name="monthly_wallet" value="<?php echo $result[0]['monthly_wallet']; ?>" readonly>
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputEmail1">Bonus wallet</label> <i class="text-danger asterik">*</i>
                                    <input type="int" class="form-control" name="bonus_wallet" value="<?php echo $result[0]['bonus_wallet']; ?>" readonly>
                                </div>
                            </div> 
                        </div>

                    </div><!-- /.box-body -->
                </form>
            </div><!-- /.box -->
        </div>
    </div>
</section>
<section class="content-header">
    <h1>Last 5 Days of Withdrawals<small>
</section>
   
</section>
    <!-- Main content -->
    <section class="content">
        <!-- Main row -->
        <form name="withdrawal_form" method="post" enctype="multipart/form-data">
            <div class="row">
                <!-- Left col -->
                <div class="col-12">
                    <div class="box">
                        <div class="box-body table-responsive">
                        <table id='users_table' class="table table-hover" data-toggle="table" data-url="api-firebase/get-bootstrap-table-data.php?table=withdrawal" data-page-list="[5, 10, 20, 50, 100, 200]" data-show-refresh="true" data-show-columns="true" data-side-pagination="server" data-pagination="true" data-search="true" data-trim-on-search="false" data-filter-control="true" data-query-params="queryParams" data-sort-name="id" data-sort-order="desc" data-show-export="true" data-export-types='["txt","csv"]' data-export-options='{
                            "fileName": "Yellow app-notifications-list-<?= date('d-m-Y') ?>",
                            "ignoreColumn": ["operate"] 
                        }'>
                                <thead>
                                    <tr>
                                        <th data-field="id" data-sortable="true">ID</th>
                                        <th data-field="name" data-sortable="true" data-visible="true" data-footer-formatter="totalFormatter">Name</th>
                                        <th data-field="amount" data-sortable="true" data-visible="true" data-footer-formatter="priceFormatter">Withdrawals Amount</th>
                                        <th data-field="status" data-sortable="true">Status</th>
                                        <th data-field="mobile" data-sortable="true">Mobile</th>
                                        <th data-field="datetime" data-sortable="true">date</th>
                                        <th data-field="referred_by" data-sortable="true">Referred By</th>
                                        <th data-field="refer_code" data-sortable="true">Refer Code</th>
                                        <th data-field="project_type" data-sortable="true">Project Type</th>
                                        <th data-field="plan" data-sortable="true">Plan Days</th>
                                        <th data-field="worked_days" data-sortable="true">Worked Days</th>
                                        <th data-field="level" data-sortable="true">Level</th>
                                        <th data-field="total_referrals" data-sortable="true">Refer Count</th>
                                        <th data-field="support_id" data-sortable="true">Support Name</th>
                                        <th data-field="daily_wallet" data-sortable="true">Daily Wallet</th>
                                        <th data-field="monthly_wallet" data-sortable="true">Monthly Wallet</th>
                                        <th data-field="earnings_wallet" data-sortable="true">Earning Wallet</th>
                                        <th data-field="bonus_wallet" data-sortable="true">Bonus Wallet</th>
                                        <th data-field="balance" data-sortable="true">Main Wallet</th>
                                        <th data-field="account_num" data-sortable="true">Account Number</th>
                                        <th data-field="holder_name" data-sortable="true">Account Holder Name</th>
                                        <th data-field="bank" data-sortable="true">Bank Name</th>
                                        <th data-field="ifsc" data-sortable="true">IFSC</th>    
                                        <!-- <th  data-field="operate" data-events="actionEvents">Action</th> -->
                                        </tr>
                            </thead>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <div class="separator"> </div>
        </div>
        <!-- /.row (main row) -->
    </section>

<script>
    $('#seller_id').on('change', function() {
        $('#products_table').bootstrapTable('refresh');
    });
    $('#community').on('change', function() {
        $('#users_table').bootstrapTable('refresh');
    });

    function queryParams(p) {
        return {
            "category_id": $('#category_id').val(),
            "seller_id": $('#seller_id').val(),
            "community": $('#community').val(),
            limit: p.limit,
            sort: p.sort,
            order: p.order,
            offset: p.offset,
            search: p.search
        };
    }
</script>
<div class="separator"> </div>
<?php $db->disconnect(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>