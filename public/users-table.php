<section class="content-header">
    <h1>Users /<small><a href="home.php"><i class="fa fa-home"></i> Home</a></small></h1>

    <ol class="breadcrumb">
        <a class="btn btn-block btn-default" href="add-user.php"><i class="fa fa-plus-square"></i> Add New User</a>
    </ol>

</section>
    <!-- Main content -->
    <section class="content">
        <!-- Main row -->
        <div class="row">
            <!-- Left col -->
            <div class="col-xs-12">
                <div class="box">
                <form action="export-user.php">
                    <button type='submit'  class="btn btn-primary"><i class="fa fa-download"></i> Export All Users</button>
                </form>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <table id='users_table' class="table table-hover" data-toggle="table" data-url="api-firebase/get-bootstrap-table-data.php?table=users" data-page-list="[5, 10, 20, 50, 100, 200]" data-show-refresh="true" data-show-columns="true" data-side-pagination="server" data-pagination="true" data-search="true" data-trim-on-search="false" data-filter-control="true" data-query-params="queryParams" data-sort-name="id" data-sort-order="desc" data-show-export="true" data-export-types='["txt","csv"]' data-export-options='{
                            "fileName": "yellow app-users-list-<?= date('d-m-Y') ?>",
                            "ignoreColumn": ["operate"] 
                        }'>
                            <thead>
                                <tr>
                                   <th data-field="operate">Action</th>
                                    <th data-field="id" data-sortable="true">ID</th>
                                    <th data-field="name" data-sortable="true">Name</th>
                                    <th data-field="mobile" data-sortable="true">Phone Number</th>
                                    <th data-field="password" data-sortable="true">Password</th>
                                    <th data-field="dob" data-sortable="true">Date of Birth</th>
                                    <th data-field="status" data-sortable="true">Status</th>
                                    <th data-field="email" data-sortable="true">Email</th>
                                    <th data-field="city" data-sortable="true">City</th>
                                    <th data-field="device_id" data-sortable="true">Device Id</th>
                                    <th data-field="earn" data-sortable="true">Earn</th>
                                    <th data-field="total_referrals" data-sortable="true">Total Referrals</th>
                                    <th data-field="balance" data-sortable="true">Balance</th>
                                    <th data-field="withdrawal" data-sortable="true">Withdrawal</th>
                                    <th data-field="today_codes" data-sortable="true">Today Codes</th>
                                    <th data-field="total_codes" data-sortable="true">Total Codes</th>
                                    <th data-field="refer_code" data-sortable="true">Refer Code</th>
                                    <th data-field="referred_by" data-sortable="true">Refered By</th>
                                    <th data-field="history" data-sortable="true">History</th>
                                    <th data-field="code_generate" data-sortable="true">Code Generate</th>
                                    <th data-field="withdrawal_status" data-sortable="true">Withdrawal Status</th>
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

