<section class="content-header">
    <h1>Users /<small><a href="home.php"><i class="fa fa-home"></i> Home</a></small></h1>
    <?php
         if($_SESSION['role'] == 'Super Admin'){?>
            <ol class="breadcrumb">
                <a class="btn btn-block btn-default" href="add-user.php"><i class="fa fa-plus-square"></i> Add New User</a>
            </ol>
    <?php } ?>
</section>
    <!-- Main content -->
    <section class="content">
        <!-- Main row -->
        <div class="row">
            <!-- Left col -->
            <div class="col-xs-12">
                <div class="box">
                <div class="box-header">
                    <?php
                if($_SESSION['role'] == 'Admin'){?>
                        <form action="export-unpaid-withdrawals.php">
                            <button type='submit'  class="btn btn-primary"><i class="fa fa-download"></i> Export Unpaid Withdrawal</button>
                        </form>
                        <?php } ?>
                    <div class="box-header">
                    <?php
                if($_SESSION['role'] == 'Super Admin'){?>
                        <form action="export-user.php">
                            <button type='submit'  class="btn btn-primary"><i class="fa fa-download"></i> Export All Users</button>
                        </form>
                        <?php } ?>
                        <br>
                        <div class="col-md-2">
                                <h4 class="box-title">Joined Date </h4>
                                <input type="date" class="form-control" id="date" name="date" value="<?php echo (isset($_GET['date'])) ? $_GET['date'] : "" ?>"></input>
                        </div>
                        <div class="col-md-2">
                                <h4 class="box-title">Filter Users</h4>
                                <select id='activeusers' name="activeusers" class='form-control'>
                                        <option value="">All</option>
                                        <option value="1"<?php echo (isset($_GET['activeusers'])) ? 'selected' : "" ?>>Active Users</option>
                                </select>
                        </div>
                      
                        <div class="col-md-2">
                        <h4 class="box-title">Filter by support</h4>
                           
                            <select id='support_id' name="support_id" class='form-control'>
                                <option value=''>All</option>
                                
                                        <?php
                                        $sql = "SELECT name FROM `staffs`";
                                        $db->sql($sql);
                                        $result = $db->getResult();
                                        foreach ($result as $value) {
                                        ?>
                                            <option value='<?= $value['id'] ?>'><?= $value['name'] ?></option>
                                    <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                                <h4 class="box-title">Status</h4>
                                <select id='status' name="status" class='form-control'>
                                        <option value="">All</option>
                                        <option value="0">Non Verfied</option>
                                        <option value="1">Verfied</option>
                                        <option value="2">Blocked</option>
                                </select>
                        </div>
                        <div class="col-md-2">
                                <h4 class="box-title">Black Box</h4>
                                <select id='black_box' name="black_box" class='form-control'>
                                        <option value="">All</option>
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                </select>
                        </div>
                        <div class="col-md-2">
                        <h4 class="box-title">Filter by Month </h4>
                                    <select id='month' name="month" class='form-control'>
                                        <option value="">select</option>
                                            <?php
                                            $sql = "SELECT id,month FROM `months`";
                                            $db->sql($sql);
                                            $result = $db->getResult();
                                            foreach ($result as $value) {
                                            ?>
                                                <option value='<?= $value['id'] ?>'><?= $value['month'] ?></option>
                                        <?php } ?>
                                    </select>
                        </div>
                        <div class="col-md-2">
                        <h4 class="box-title">Referred By</h4>
                    
                                    <input type="text" class="form-control" name="referred_by" id="referred_by" >
                        </div>

                    </div>
                    
                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <table id='users_table' class="table table-hover" data-toggle="table" data-url="api-firebase/get-bootstrap-table-data.php?table=users" data-page-list="[5, 10, 20, 50, 100, 200, 500]" data-show-refresh="true" data-show-columns="true" data-side-pagination="server" data-pagination="true" data-search="true" data-trim-on-search="false" data-filter-control="true" data-query-params="queryParams" data-sort-name="id" data-sort-order="desc" data-show-export="true" data-export-types='["txt","csv"]' data-export-options='{
                            "fileName": "yellow app-users-list-<?= date('d-m-Y') ?>",
                            "ignoreColumn": ["operate"] 
                        }'>
                        <thead>
                            <tr>
                                    <th data-field="operate">Action</th>
                                    <th data-field="id" data-sortable="true">ID</th>
                                    <th data-field="registered_date" data-sortable="true">Registration Date</th>
                                    <th data-field="name" data-sortable="true">User Name</th>
                                    <th data-field="mobile" data-sortable="true">Mobile Number</th>
                                    <th data-field="referred_by" data-sortable="true">Referred By</th>
                                    <th data-field="refer_code" data-sortable="true">Refer Code</th>
                                   
                                    <th data-field="lead_id" data-sortable="true">Lead</th>
                                    <th data-field="support_id" data-sortable="true">Support</th>
                                    
                                    <?php if ($_SESSION['role'] == 'Super Admin') { ?>
                                    <th data-field="level" data-sortable="true">Level</th>
                                    <th data-field="status" data-sortable="true">Status</th>
                                    <th data-field="total_referrals" data-sortable="true">Total Referrals</th>
                                    <th data-field="plan" data-sortable="true">Plan</th>
                                    <th data-field="project_type" data-sortable="true">Project Type</th>
                                    <th data-field="balance" data-sortable="true">Balance</th>
                                    <th data-field="withdrawal" data-sortable="true">Withdrawal</th>
                                    <th data-field="history" data-sortable="true">History</th>
                                    <th data-field="code_generate" data-sortable="true">Code Generate</th>
                                    <th data-field="withdrawal_status" data-sortable="true">Withdrawal Status</th>
                                    <th data-field="today_codes" data-sortable="true">Today Codes</th>
                                    <th data-field="total_codes" data-sortable="true">Total Codes</th>
                                    <th data-field="refer_name" >Refer Name</th>
                                    <th data-field="refer_mobile" >Refer Mobile</th>
                                    <th data-field="refer_support" >Refer Support</th>
                                    <th data-field="salary_advance_balance" data-sortable="true">Salary Advance Balance</th>
                                    <th data-field="ongoing_sa_balance" data-sortable="true">Ongoing SA Balance</th>
                                    <th data-field="sa_refer_count" data-sortable="true">SA Refer Count</th>
                                    <th data-field="branch" data-sortable="true">Branch</th>
                                    <th data-field="refund_wallet" data-sortable="true">Refund Wallet</th>
                                    <th data-field="total_refund" data-sortable="true">Total Refund</th>
                                    <th data-field="trial_wallet" data-sortable="true">Trial Wallet</th>
                                    <th data-field="email" data-sortable="true">Email</th>
                                    <th data-field="city" data-sortable="true">City</th>
                                    <th data-field="device_id" data-sortable="true">Device Id</th>
                                    <th data-field="earn" data-sortable="true">Earn</th>
                                    <th data-field="password" data-sortable="true">Password</th>
                                    <th data-field="dob" data-sortable="true">Date of Birth</th>
                                <?php } ?>
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
      $('#date').on('change', function() {
            id = $('#date').val();
            $('#users_table').bootstrapTable('refresh');
        });
        $('#activeusers').on('change', function() {
            idf = $('#activeusers').val();
            $('#users_table').bootstrapTable('refresh');
        });
        $('#support_id').on('change', function() {
            idf = $('#support_id').val();
            $('#users_table').bootstrapTable('refresh');
        });
        $('#status').on('change', function() {
            idf = $('#status').val();
            $('#users_table').bootstrapTable('refresh');
        });
        $('#black_box').on('change', function() {
            idf = $('#black_box').val();
            $('#users_table').bootstrapTable('refresh');
        });
        $('#month').on('change', function() {
            id = $('#month').val();
            $('#users_table').bootstrapTable('refresh');
        });
        $('#referred_by').on('change', function() {
            id = $('#month').val();
            $('#users_table').bootstrapTable('refresh');
        });
   
   

    function queryParams(p) {
        return {
            "date": $('#date').val(),
            "support_id": $('#support_id').val(),
            "activeusers": $('#activeusers').val(),
            "status": $('#status').val(),
            "black_box": $('#black_box').val(),
            "month": $('#month').val(),
            "referred_by": $('#referred_by').val(),
            limit: p.limit,
            sort: p.sort,
            order: p.order,
            offset: p.offset,
            search: p.search
        };
    }
</script>
   