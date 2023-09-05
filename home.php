<?php session_start();

include_once('includes/custom-functions.php');
include_once('includes/functions.php');
$function = new custom_functions;

// set time for session timeout
$currentTime = time();
$expired = 3600; // 1 hour in seconds

// if session not set go to login page
if (!isset($_SESSION['username'])) {
    header("location: index.php");
}

// if current time is more than session timeout, go back to login page
if ($currentTime > $_SESSION['timeout']) {

}

// destroy previous session timeout and create new one
unset($_SESSION['timeout']);
$_SESSION['timeout'] = $currentTime + $expired;

include "header.php";

?>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>ABCD - Dashboard <?php echo $_SESSION['timeout'] ;?></title>
</head>

<body>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->


        <section class="content-header">
            <h1>Home</h1>
            <ol class="breadcrumb">
                <li>
                    <a href="home.php"> <i class="fa fa-home"></i> Home</a>
                </li>
            </ol>
        </section>
        <section class="content">
        <?php
                if($_SESSION['role'] == 'Super Admin'){?>
            <div class="row">
                <div class="col-md-12">
                    <form id='employee_form' method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="form-group">
                                <div class='col-md-4'>
                                        <select id='branch_id' name="branch_id" class='form-control'>
                                                <option value="">--Select--</option>
                                                <?php
                                                $sql = "SELECT id,name FROM `branches`";
                                                $db->sql($sql);
                                                $result = $db->getResult();
                                                foreach ($result as $value) {
                                                ?>
                                                    <option value='<?= $value['id'] ?>'><?= $value['name'] ?></option>
                                            <?php } ?>
                                        </select>                    
                                </div>
                                <div class='col-md-6'>
                                    <button type="submit" class="btn btn-primary" name="btnSubmit">Submit</button>
                        
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php } ?>
            <br>
            <div class="row">
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3><?php
                            if($_SESSION['role'] == 'Admin'){
                                $join = "WHERE id IS NOT NULL";
                            }
                            else{
                                $refer_code = $_SESSION['refer_code'];
                                $join = "WHERE refer_code REGEXP '^$refer_code'";
                            }
                            $branch_id = (isset($_POST['branch_id']) && $_POST['branch_id']!='') ? $_POST['branch_id'] :"";
                            if ($branch_id != '') {
                                $join1="AND branch_id='$branch_id'";
                            } else {
                                $join1="";
                            }
                            $sql = "SELECT id FROM users $join $join1";
                            $db->sql($sql);
                            $res = $db->getResult();
                            $num = $db->numRows($res);
                            echo $num;
                             ?></h3>
                            <p>Users</p>
                        </div>
                        
                        <a href="users.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <?php
                if($_SESSION['role'] == 'Super Admin'){?>
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                        <h3><?php
                            $currentdate = date('Y-m-d');
                            if($_SESSION['role'] == 'Super Admin'){
                                $join = "WHERE status=1 AND code_generate = 1 AND today_codes != 0";
                            }
                            else{
                                $refer_code = $_SESSION['refer_code'];
                                $join = "WHERE status=1 AND code_generate = 1 AND refer_code REGEXP '^$refer_code' AND today_codes != 0 ";
                            }
                            $branch_id = (isset($_POST['branch_id']) && $_POST['branch_id']!='') ? $_POST['branch_id'] :"";
                            if ($branch_id != '') {
                                $join1="AND branch_id='$branch_id'";
                            } else {
                                $join1="";
                            }
                            $sql = "SELECT id FROM users $join $join1";
                            $db->sql($sql);
                            $res = $db->getResult();
                            $num = $db->numRows($res);
                            echo $num;
                             ?></h3>
                            <p>ABCD Active Users</p>
                        </div>
                        
                        <a href="users.php?activeusers=1" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                    <div class="col-lg-4 col-xs-6">
                        <div class="small-box bg-teal">
                            <div class="inner">
                                <h3><?php
                                    $currentdate = date('Y-m-d');
                                    $sql = "SELECT SUM(codes) AS today_codes FROM transactions WHERE DATE(datetime) = '$currentdate'";
                                    $db->sql($sql);
                                    $res = $db->getResult();
                                    echo $res[0]['today_codes'];
                                    ?></h3>
                                    <p>Users Today Codes</p>
                            </div>
                            
                            <a href="users.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-purple">
                        <div class="inner">
                            <h3><?php
                            $currentdate = date('Y-m-d');
                            $sql = "SELECT t.id FROM transactions t,users u WHERE t.user_id = u.id AND u.l_referral_count = 1 AND t.type = 'refer_bonus' AND DATE(t.datetime) = '$currentdate' GROUP BY user_id";
                            $db->sql($sql);
                            $res = $db->getResult();
                            $num = $db->numRows($res);
                            echo $num;
                             ?></h3>
                            <p>Today New Referred Users</p>
                        </div>
                        
                        <a href="users.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-orange">
                        <div class="inner">
                            <h3><?php
                            $currentdate = date('Y-m-d');
                            $sql = "SELECT u.id FROM `transactions` t,`users` u  WHERE t.user_id = u.id AND t.type = 'refer_bonus' AND DATE(t.datetime) = '$currentdate' AND u.project_type = 'amail' GROUP BY u.id";
                            $db->sql($sql);
                            $res = $db->getResult();
                            $num = $db->numRows($res);
                            echo $num;
                             ?></h3>
                            <p>Today Amail Users Refer Count</p>
                        </div>
                        
                        <a href="users.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-purple">
                        <div class="inner">
                            <h3><?php
                            // if($_SESSION['role'] == 'Super Admin'){
                            //     $join = "WHERE task_type='champion' AND status=1 AND code_generate = 1 AND today_codes != 0";
                            // }
                            // else{
                            //     $refer_code = $_SESSION['refer_code'];
                            //     $join = "WHERE refer_code REGEXP '^$refer_code' AND task_type='champion' AND status=1 AND code_generate = 1 AND today_codes != 0";
                            // }
                            // $branch_id = (isset($_POST['branch_id']) && $_POST['branch_id']!='') ? $_POST['branch_id'] :"";
                            // if ($branch_id != '') {
                            //     $join1="AND branch_id='$branch_id'";
                            // } else {
                            //     $join1="";
                            // }
                            $sql = "SELECT SUM(codes) AS codes FROM transactions WHERE task_type = 'regular' AND DATE(datetime) = '$currentdate'";
                            $db->sql($sql);
                            $res = $db->getResult();
                            $num = $res[0]['codes'];
                            echo $num;
                             ?></h3>
                            <p>Regular Today Codes</p>
                        </div>
                        
                        <a href="champion_users.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <?php
                if($_SESSION['role'] == 'Super Admin'){?>
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-maroon">
                        <div class="inner">
                            <h3><?php
                                $currentdate = date('Y-m-d');
                                $sql = "SELECT COUNT(id) AS total FROM users WHERE status=1 AND code_generate = 1 AND today_mails != 0 AND project_type = 'amail'";
                                $db->sql($sql);
                                $res = $db->getResult();
                                $num = $res[0]['total'];
                                echo $num;
                                ?></h3>
                                <p>Amail Active Users</p>
                        </div>
                       
                        <a href="champion_users.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3><?php
                                $currentdate = date('Y-m-d');
                                $branch_id = (isset($_POST['branch_id']) && $_POST['branch_id']!='') ? $_POST['branch_id'] :"";
                                if ($branch_id != '') {
                                    $join1="AND branch_id='$branch_id'";
                                } else {
                                    $join1="";
                                }
                                $sql = "SELECT t.id FROM transactions t WHERE t.type = 'register_amail' AND DATE(t.datetime) = '$currentdate'";
                                $db->sql($sql);
                                $res = $db->getResult();
                                $anum = $db->numRows($res);
                                $sql = "SELECT u.id FROM users u,transactions t WHERE u.id = t.user_id AND t.type = 'register_bonus' AND DATE(t.datetime) = '$currentdate' AND u.joined_date= '$currentdate' AND u.status=1 $join1";
                                $db->sql($sql);
                                $res = $db->getResult();
                                $num = $db->numRows($res);
                                echo $num.'/'.$anum;
                                ?></h3>
                                <p>Today Joins</p>
                        </div>
                        
                        <a href="users.php?date=<?php echo date('Y-m-d') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-orange">
                        <div class="inner">
                        <h3><?php
                            $branch_id = (isset($_POST['branch_id']) && $_POST['branch_id']!='') ? $_POST['branch_id'] :"";
                            if ($branch_id != '') {
                                $join1="AND users.branch_id='$branch_id'";
                            } else {
                                $join1="";
                            }
                            $sql = "SELECT SUM(withdrawals.amount) AS amount,withdrawals.user_id,users.id FROM withdrawals,users WHERE withdrawals.user_id=users.id AND withdrawals.status=0 $join1";
                            $db->sql($sql);
                            $res = $db->getResult();
                            $totalamount = $res[0]['amount'];
                            echo "Rs.".$totalamount;
                             ?></h3>
                            <p>Unpaid Withdrawals</p>
                        </div>
                        
                        <a href="withdrawals.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                        <h3><?php
                            $branch_id = (isset($_POST['branch_id']) && $_POST['branch_id']!='') ? $_POST['branch_id'] :"";
                            if ($branch_id != '') {
                                $join1="AND branch_id='$branch_id'";
                            } else {
                                $join1="";
                            }
                            $sql = "SELECT COUNT(id) AS total FROM users WHERE DATE(registered_date) = '$currentdate' $join1";
                            $db->sql($sql);
                            $res = $db->getResult();
                            $total = $res[0]['total'];
                            echo $total;
                             ?></h3>
                            <p>Total Registration</p>
                        </div>
                        
                        <a href="users.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-teal">
                        <div class="inner">
                        <?php
                            $currentdate = date('Y-m-d');
                            $sql = "SELECT SUM(mails) AS today_mails FROM transactions WHERE DATE(datetime) = '$currentdate'";
                            $db->sql($sql);
                            $res = $db->getResult();
                            $num = $res[0]['today_mails'];
                           ?>
                          <h3><?php echo $num; ?></h3>
                            <p>Today mails</p>
                        </div>
                        
                        <a href="users.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                
                <!-- <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                        <h3><?php
                            $sql = "SELECT SUM(amount) AS amount FROM withdrawals WHERE status=0";
                            $db->sql($sql);
                            $res = $db->getResult();
                            $totalamount = $res[0]['amount'];
                            $currentdate = date('Y-m-d');
                            $sql = "SELECT id FROM users WHERE joined_date= '$currentdate'";
                            $db->sql($sql);
                            $res = $db->getResult();
                            $num = $db->numRows($res);
                            $today_reg = $num * 3000;
                            $tvalue = $today_reg - $totalamount;
                            echo "Rs.". $tvalue;
                             ?></h3>
                            <p>Profit</p>
                        </div>
                        
                        <a href="withdrawals.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-xs-6">
                    <div class="small-box bg-dark">
                        <div class="inner">
                        <h3><?php
                            $sql = "SELECT SUM(balance) + SUM(refer_balance) + SUM(sync_refer_wallet) AS balance FROM users WHERE status=1 AND today_codes != 0";
                            $db->sql($sql);
                            $res = $db->getResult();
                            $balance = $res[0]['balance'];
                           
                            echo "Rs.". round($balance);
                             ?></h3>
                            <p>Expect Withdrawals</p>
                        </div>
                        
                        <a href="withdrawals.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div> -->
                <?php    
                }
                ?>

            </div>
            <div class="row">
                <div class="col-md-12">
                <form id='notification_form' method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="form-group">
                            <div class='col-md-3'>
                                <input type="date" class="form-control" id="date" name="date" value="<?php echo (isset($_POST['date']) && $_POST['date']!='') ? $_POST['date'] : date('Y-m-d') ?>"></input>       
                    
                            </div>
                            <div class='col-md-6'>
                                <button type="submit" class="btn btn-primary" name="btnSubmit">Submit</button>
                    
                            </div>
                        </div>
                    </div>
                    
                </form>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="box box-success">
                        <?php 
                        $currentdate = (isset($_POST['date']) && $_POST['date']!='') ? $_POST['date'] : date('Y-m-d');
                        
                        $sql ="SELECT * FROM `join_reports`  WHERE total_paid > 100000 ORDER BY date DESC LIMIT 10 ";
                        $db->sql($sql);
                        $result_order = $db->getResult();
                        
                         ?>
                        <div class="tile-stats" style="padding:10px;">
                            <div id="earning_chart" style="width:100%;height:350px;"></div>
                        </div>
                    </div>

                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="box box-success">
                        <?php 
                        $currentdate = (isset($_POST['date']) && $_POST['date']!='') ? $_POST['date'] : date('Y-m-d');
                        
                        $sql ="SELECT hour(datetime) AS time, SUM(codes) AS codes FROM transactions WHERE datetime BETWEEN '$currentdate 00:00:00' AND '$currentdate 23:59:59' AND type = 'generate' GROUP BY hour( datetime ) , day( datetime )";
                        $db->sql($sql);
                        $result_order2 = $db->getResult();
                        $sql ="SELECT SUM(codes) AS total FROM transactions WHERE datetime BETWEEN '$currentdate 00:00:00' AND '$currentdate 23:59:59' AND type = 'generate' ";
                        $db->sql($sql);
                        $stu_total2 = $db->getResult();
                        
                         ?>
                        <div class="tile-stats" style="padding:10px;">
                            <div id="earning_chart2" style="width:100%;height:350px;"></div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-warning">

                        <div class="box-header with-border">
                            
                            <h3 class="box-title">Top Today Coders <small>( Day: <?= date("D"); ?>)</small></h3>
                            <div class="box-tools pull-right">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                            <form method="post" action="export-active_users.php" enctype="multipart/form-data" >
                            <div class="col-md-3">
                                    
                                    <input type="date" class="form-control" name="date">
                                </div>
                                <div class="col-md-3">
                                    <button type='submit'  class="btn btn-primary"><i class="fa fa-download"></i> Export Active Users</button>
                                </div>
                            
                        </form>
                        </div>
                        <div class="box-body">

                            <div class="table-responsive">
                                <table class="table no-margin" id='top_seller_table' class="table table-hover" data-toggle="table" data-url="api-firebase/get-bootstrap-table-data.php?table=top_coders" data-page-list="[5, 10, 20, 50, 100, 200,500]" data-show-refresh="true" data-show-columns="true" data-side-pagination="server" data-pagination="true" data-search="true" data-trim-on-search="false" data-filter-control="true" data-query-params="queryParams_top_seller" data-sort-name="today_codes" data-sort-order="desc" data-show-export="true" data-export-types='["txt","csv"]' data-export-options='{
                                "fileName": "Yellow app-withdrawals-list-<?= date('d-m-Y') ?>",
                                "ignoreColumn": ["operate"] 
                            }'>
                                    <thead>
                                        <tr>
                                            <th data-field="id" data-sortable='true'>ID</th>
                                            <!-- <th data-field="joined_date" data-visible="true">Joined Date</th> -->
                                            <th data-field="name" data-sortable='true'>Name</th>
                                            <th data-field="mobile">Mobile</th>
                                            <th data-field="today_codes" data-sortable='true'>Codes</th>
                                            <th data-field="today_mails" data-sortable='true'>Mails</th>
                                            <th data-field="project_type" data-sortable='true'>Project Type</th>
                                            <th data-field="current_refers" data-sortable='true'>Current Refers</th>
                                            <th data-field="support" data-sortable='true'>Support</th>
                                            <th data-field="earn" >Earn</th>
                                            <th data-field="duration" data-sortable='true'>Duration</th>
                                            <th data-field="worked_days" data-sortable='true'>Worked Days</th>
                                            <th data-field="total_earn" >Total Earn</th>
                                            <th data-field="l_referral_count" data-sortable='true'>Level Referals Count</th>
                                            <th data-field="level" data-sortable='true'>Level</th>
                                            
                                        
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                <?php } ?>
                <!-- <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Top Categories <small> ( Month: <?= date("M"); ?>) </small></h3>
                            <div class="box-tools pull-right">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <div class="box-body">

                            <div class="table-responsive">
                                <table class="table no-margin" id='top_seller_table' data-toggle="table" data-url="api-firebase/get-bootstrap-table-data.php?table=top_categories" data-page-list="[5,10]" data-page-size="5" data-show-refresh="true" data-show-columns="true" data-side-pagination="server" data-pagination="true" data-sort-name="total_revenues" data-sort-order="desc" data-toolbar="#toolbar" data-query-params="queryParams_top_cat">
                                    <thead>
                                        <tr>
                                            <th data-field="id" data-sortable='true'>Rank</th>
                                            <th data-field="cat_name" data-sortable='true' data-visible="true">Category</th>
                                            <th data-field="p_name" data-sortable='true' data-visible="true">Product Name</th>
                                            <th data-field="total_revenues" data-sortable='true'>Total Revenue(<?= $settings['currency'] ?>)</th>
                                            <th data-field="operate">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                    </div>
                </div> -->
            </div>
        </section>
    </div>
    <script>
    
        $('#filter_order').on('change', function() {
            $('#orders_table').bootstrapTable('refresh');
        });
        $('#seller_id').on('change', function() {
            $('#orders_table').bootstrapTable('refresh');
        });
    </script>
    <script>
        function queryParams(p) {
            return {
                "filter_order": $('#filter_order').val(),
                limit: p.limit,
                sort: p.sort,
                order: p.order,
                offset: p.offset,
                search: p.search
            };
        }
        function queryParams_top_seller(p) {
            return {
                "current_date": $('#date').val(),
                limit: p.limit,
                sort: p.sort,
                order: p.order,
                offset: p.offset
            };
        }

        function queryParams_top_cat(p) {
            return {
                limit: p.limit,
                sort: p.sort,
                order: p.order,
                offset: p.offset
            };
        }

    </script>
    <?php include "footer.php"; ?>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
        google.charts.load('current', {
            'packages': ['bar']
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {


            var data = google.visualization.arrayToDataTable([
                ['Hour', 'Total - <?= $stu_total2[0]['total'] .'\n₹'.$stu_total2[0]['total'] * COST_PER_CODE ?>'],
                <?php foreach ($result_order2 as $row) {
                    //$date = date('d-M', strtotime($row['order_date']));
                    echo "['" . $row['time'] . "'," . $row['codes'] . "],";
                } ?>
            ]);
            var options = {
                chart: {
                    title: 'Codes By Hour Wise',
                    //subtitle: 'Total Sale In Last Week (Month: <?php echo date("M"); ?>)',
                }
            };

            var chart = new google.charts.Bar(document.getElementById('earning_chart2'));
            chart.draw(data, google.charts.Bar.convertOptions(options));
        }
    </script>

<script>
google.charts.load('current',{packages:['corechart']});
google.charts.setOnLoadCallback(drawChart2);

function drawChart2() {

    var data = google.visualization.arrayToDataTable([
        ['Date', 'Joins', 'Withdrawals'],
                
                <?php foreach ($result_order as $row) {
                    //$date = date('d-M', strtotime($row['order_date']));
                    echo "[new Date('" . $row['date'] . "')," . $row['total_users'] * 3000 . "," . $row['total_paid'] . "],";
                } ?>
            ]);

// // Set Data
// const data = google.visualization.arrayToDataTable([
//   ['Date', 'Price', 'Size'],
//   [new Date('2023-07-01'), 200000, 200000],
//   [new Date('2023-07-02'), 300000, 250000],
//   [new Date('2023-07-03'), 245000, 300000],
//   [new Date('2023-07-04'), 1700000, 200000],

// ]);

// Set Options
const options = {
  title: 'Joins vs Withdrawals',
  hAxis: { title: 'Date' },
  vAxis: { title: 'Charges' },
  legend: 'none'
};



// Draw
const chart = new google.visualization.LineChart(document.getElementById('earning_chart'));
chart.draw(data, options);

}
</script>
</body>
</html>