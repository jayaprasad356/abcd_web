<?php
$currentdate = date('Y-m-d');
if (isset($_POST['btnUnpaid']) && isset($_POST['enable'])) {
    for ($i = 0; $i < count($_POST['enable']); $i++) {
        
    
        $enable = $db->escapeString($fn->xss_clean($_POST['enable'][$i]));
        $sql = "UPDATE withdrawals SET status=0 WHERE id = $enable";
        $db->sql($sql);
        $result = $db->getResult();
    }
}
if (isset($_POST['btnPaid'])  && isset($_POST['enable'])) {
    for ($i = 0; $i < count($_POST['enable']); $i++) {
    
        $enable = $db->escapeString($fn->xss_clean($_POST['enable'][$i]));
        $sql = "SELECT * FROM `withdrawals` WHERE id = $enable";
        $db->sql($sql);
        $res = $db->getResult();
        $withdrawal_type = $res[0]['withdrawal_type'];
        $amount = $res[0]['amount'];
        $datetime = date('Y-m-d H:i:s');
        $user_id=$res[0]['user_id'];
        if($withdrawal_type=='sa_withdrawal'){
            $sql = "UPDATE withdrawals SET status=1 WHERE id = $enable";
            $db->sql($sql);
            
            // Calculate EMI due dates
            $emi_count = ceil($amount / 500);
            $due_dates = array();
            for ($i = 1; $i <= $emi_count; $i++) {
                $due_date = date('Y-m-d', strtotime("+$i week", strtotime($datetime)));
                array_push($due_dates, $due_date);
            }
            //calculate Due Amount
            $due_amount = $amount / $emi_count;
    
            // Add due dates to the database
            foreach ($due_dates as $due_date) {
                $sql = "INSERT INTO repayments (`user_id`, amount, `due_date`,`status`) VALUES ('$user_id', '$due_amount', '$due_date',0)";
                $db->sql($sql);
            }
            $result = $db->getResult();
        }
        else{
            $sql = "UPDATE withdrawals SET status=1 WHERE id = $enable";
            $db->sql($sql);
            $result = $db->getResult();
        }

        //send notification 
        $sql = "SELECT * FROM `users` WHERE id = $user_id";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        $mobile=$res[0]['mobile'];
        $title = "Withdrawal Request";
        $description = "Your request is accepted and Paid Successfully";
        $type = (isset($_POST['type']) && !empty($_POST['type'])) ? $db->escapeString($_POST['type']) : "chat";
        if ($num >= 1) {
            $url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $url .= $_SERVER['SERVER_NAME'];
            $url .= $_SERVER['REQUEST_URI'];
            $server_url = dirname($url).'/';
            
            $push = null;
            $id = "0";
            $devicetoken = $fnc->getTokenByMobile($mobile);
            $push = new Push(
                $title,
                $description,
                null,
                $type,
                $id
            );
            $mPushNotification = $push->getPush();
        
        
            $f_tokens = array_unique($devicetoken);
            $devicetoken_chunks = array_chunk($f_tokens,1000);
            foreach($devicetoken_chunks as $devicetokens){
                //creating firebase class object 
                $firebase = new Firebase(); 
        
                //sending push notification and displaying result 
                $response['token'] = $devicetokens;
                $firebase->send($devicetokens, $mPushNotification);
            }
        }
       
    }
}
if (isset($_POST['btnCancel'])  && isset($_POST['enable'])) {
    for ($i = 0; $i < count($_POST['enable']); $i++) {
        $enable = $db->escapeString($fn->xss_clean($_POST['enable'][$i]));

        $sql = "SELECT * FROM `withdrawals` WHERE id = $enable AND status != 2 ";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        if ($num >= 1) {
            $sql = "UPDATE withdrawals SET status=2 WHERE id = $enable";
            $db->sql($sql);
            $sql = "SELECT * FROM `withdrawals` WHERE id = $enable ";
            $db->sql($sql);
            $res = $db->getResult();
            $user_id= $res[0]['user_id'];
            $amount= $res[0]['amount'];
            $withdrawal_type= $res[0]['withdrawal_type'];
            if($withdrawal_type == 'code_withdrawal'){
                $sql = "UPDATE users SET balance= balance + $amount,withdrawal = withdrawal - $amount WHERE id = $user_id";
                $db->sql($sql);

            }else if($withdrawal_type == 'sa_withdrawal'){
                $sql = "UPDATE users SET ongoing_sa_balance= ongoing_sa_balance - $amount,salary_advance_balance = salary_advance_balance + $amount,withdrawal = withdrawal - $amount WHERE id = $user_id";
                $db->sql($sql);
                $sql = "DELETE FROM repayments WHERE user_id = $user_id AND expiry = 0";
                $db->sql($sql);

            }else{
                $sql = "UPDATE users SET refer_balance= refer_balance + $amount,withdrawal = withdrawal - $amount WHERE id = $user_id";
                $db->sql($sql);
            }

            
            $datetime = date('Y-m-d H:i:s');
            $sql = "INSERT INTO transactions (user_id,amount,datetime,type) VALUES ('$user_id','$amount','$datetime','cancelled')";
            $db->sql($sql);
            
        }
    }
}

?>
<?php
if (isset($_POST['export_all'])) {
	$join = "WHERE w.user_id = u.id AND w.user_id = b.user_id AND w.status= 0";
	$sql = "SELECT w.id AS id,w.*,u.name,u.total_codes,u.total_referrals,u.balance,u.mobile,u.referred_by,u.refer_code,DATEDIFF( '$currentdate',u.joined_date) AS history,b.branch,b.bank,b.account_num,b.ifsc,b.holder_name FROM `withdrawals` w,`users` u,`bank_details` b $join";
	$db->sql($sql);
	$developer_records = $db->getResult();
	
	$filename = "withdrawals-data".date('Ymd') . ".xls";			
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=\"$filename\"");	
	$show_coloumn = false;
	if(!empty($developer_records)) {
	  foreach($developer_records as $record) {
		if(!$show_coloumn) {
		  // display field/column names in first row
		  echo implode("\t", array_keys($record)) . "\n";
		  $show_coloumn = true;
		}
		echo implode("\t", array_values($record)) . "\n";
	  }
	}
	exit;  
}
?>

<section class="content-header">
    <h1>Withdrawals /<small><a href="home.php"><i class="fa fa-home"></i> Home</a></small></h1>

</section>
    <!-- Main content -->
    <section class="content">
        <!-- Main row -->
        <form name="withdrawal_form" method="post" enctype="multipart/form-data">
            <div class="row">
                <!-- Left col -->
                <div class="col-12">
                    <div class="box">
                        <div class="box-header">
                                <div class="row">
                                        <div class="form-group col-md-3">
                                            <h4 class="box-title">Filter by Name </h4>
                                                <select id='user_id' name="user_id" class='form-control'>
                                                <option value=''>All</option>
                                                
                                                        <?php
                                                        $sql = "SELECT id,name FROM `users`";
                                                        $db->sql($sql);
                                                        $result = $db->getResult();
                                                        foreach ($result as $value) {
                                                        ?>
                                                            <option value='<?= $value['id'] ?>'><?= $value['name'] ?></option>
                                                    <?php } ?>
                                                </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <a href="export-withdrawals.php" class="btn btn-primary"><i class="fa fa-download"></i> Export All Withdrawals</a>
                                        <!-- <button type='submit' name="export_all"  class="btn btn-primary"><i class="fa fa-download"></i> Export All Withdrawals</button> -->
                                        </div>
                                        <div class="form-group col-md-3">
                                            <a href="export-unpaid-withdrawals.php" class="btn btn-primary"><i class="fa fa-download"></i> Export Unpaid Withdrawals</a>
                                        <!-- <button type='submit' name="export_all"  class="btn btn-primary"><i class="fa fa-download"></i> Export All Withdrawals</button> -->
                                        </div>

                                        
                                </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body table-responsive">
                                <div class="row">
                                    <?php 
                                    if($_SESSION['role'] == 'Super Admin'){?>
                                        <div class="text-left col-md-2">
                                            <input type="checkbox" onchange="checkAll(this)" name="chk[]" > Select All</input>
                                        </div> 
                                        <div class="col-md-3">
                                                <button type="submit" class="btn btn-primary" name="btnUnpaid">Unpaid</button>
                                                <button type="submit" class="btn btn-success" name="btnPaid">Paid</button>
                                                <button type="submit" class="btn btn-danger" name="btnCancel">Cancelled</button>
                                                
                                        </div>
                                    <?php } ?>
                                </div>
                            <table id='users_table' class="table table-hover" data-toggle="table" data-url="api-firebase/get-bootstrap-table-data.php?table=withdrawals" data-page-list="[5, 10, 20, 50, 100, 200,500,700,1000]" data-show-refresh="true" data-show-columns="true" data-side-pagination="server" data-pagination="true" data-search="true" data-trim-on-search="false" data-filter-control="true" data-query-params="queryParams" data-sort-name="w.id" data-show-footer="true" data-sort-order="desc" data-show-export="true" data-export-types='["txt","csv"]' data-export-options='{
                                "fileName": "Yellow app-withdrawals-list-<?= date('d-m-Y') ?>",
                                "ignoreColumn": ["operate"] 
                            }'>
                                <thead>
                                    <tr>
                                        <th data-field="column"> All</th>
                                        <th data-field="id" data-sortable="true">ID</th>
                                        <th data-field="name" data-sortable="true" data-visible="true" data-footer-formatter="totalFormatter">Name</th>
                                        <th data-field="amount" data-sortable="true" data-visible="true" data-footer-formatter="priceFormatter">Amount</th>
                                        <th data-field="total_refund" data-sortable="true">Total Refund</th>
                                        <th data-field="status" data-sortable="true">Status</th>
                                        <th data-field="balance" data-sortable="true">Balance</th>
                                        <th data-field="withdrawal_type" data-sortable="true">Withdrawal Type</th>
                                        <th data-field="datetime" data-sortable="true">DateTime</th>
                                        <th data-field="account_num" data-sortable="true">Account Number</th>
                                        <th data-field="holder_name" data-sortable="true">Holder Name</th>
                                        <th data-field="bank" data-sortable="true">Bank</th>
                                        <th data-field="branch" data-sortable="true">Branch</th>
                                        <th data-field="ifsc" data-sortable="true">IFSC</th>
                                        <th data-field="total_codes" data-sortable="true">Total Codes</th>
                                        <th data-field="total_referrals" data-sortable="true">Total Referrals</th>
                                        <th data-field="mobile" data-sortable="true">Mobile</th>
                                        <th data-field="referred_by" data-sortable="true">Referred By</th>
                                        <th data-field="refer_code" data-sortable="true">Refer Code</th>
                                        <th data-field="history" data-sortable="true">History</th>

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
        </form>

        <!-- /.row (main row) -->
    </section>
<script>
 function checkAll(ele) {
     var checkboxes = document.getElementsByTagName('input');
     if (ele.checked) {
         for (var i = 0; i < checkboxes.length; i++) {
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = true;
             }
         }
     } else {
         for (var i = 0; i < checkboxes.length; i++) {
             console.log(i)
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = false;
             }
         }
     }
 }
    
</script>
<script>
        $('#user_id').on('change', function() {
            id = $('#user_id').val();
            $('#users_table').bootstrapTable('refresh');
        });

    function queryParams(p) {
        return {
            "user_id": $('#user_id').val(),
            limit: p.limit,
            sort: p.sort,
            order: p.order,
            offset: p.offset,
            search: p.search
        };
    }
    function totalFormatter() {
        return '<span style="color:green;font-weight:bold;font-size:large;">TOTAL</span>'
    }

    var total = 0;

    function priceFormatter(data) {
        var field = this.field
        return '<span style="color:green;font-weight:bold;font-size:large;"> ' + data.map(function(row) {
                return +row[field]
            })
            .reduce(function(sum, i) {
                return sum + i
            }, 0);
    }
</script>
<script>
    $(document).ready(function () {
        $('#user_id').select2({
        width: 'element',
        placeholder: 'Type in name to search',

    });
    });

    if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}

</script>

