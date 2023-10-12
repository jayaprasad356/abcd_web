<?php
if (isset($_GET['message'])) {
    $message = urldecode($_GET['message']);
    echo '<script>alert("' . $message . '");</script>';
}
?>
<!-- Rest of your queries.php page content -->
<section class="content-header">
    <h1>Queries /<small><a href="home.php"><i class="fa fa-home"></i> Home</a></small></h1>
</section>
    <!-- Main content -->
    <section class="content">
        <!-- Main row -->
        <div class="row">
            <!-- Left col -->
            <div class="col-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                    <table id='users_table' class="table table-hover" data-toggle="table" data-url="api-firebase/get-bootstrap-table-data.php?table=query" data-page-list="[5, 10, 20, 50, 100, 200, 500]" data-show-refresh="true" data-show-columns="true" data-side-pagination="server" data-pagination="true" data-search="true" data-trim-on-search="false" data-filter-control="true" data-query-params="queryParams" data-sort-name="id" data-sort-order="desc" data-show-export="true" data-export-types='["txt","csv"]' data-export-options='{
                            "fileName": "yellow app-users-list-<?= date('d-m-Y') ?>",
                            "ignoreColumn": ["operate"] 
                        }'>
                            <thead>
                                <tr>
                                <th data-field="edit_commission" data-sortable="true">View</th>
                                    <th data-field="id" data-sortable="true">ID</th>
                                    <th data-field="query_category_name" data-sortable="true">Query Category Name</th>
                                    <th data-field="user_name" data-sortable="true">User Name</th>
                                    <th data-field="mobile" data-sortable="true">Mobile</th>
                                    <th data-field="title" data-sortable="true">Title</th>
                                    <th data-field="description" data-sortable="true">Description</th>
                                    <th data-field="remarks" data-sortable="true">Remarks</th>
                                    <th data-field="status" data-sortable="true">Status</th>
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
        <div class="modal fade" id='category-wise-commission-modal' tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Set category wise seller commission</h4>
                        <hr>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Field</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Query Category Name</td>
                            <td><span id="query-category-name"></span></td>
                        </tr>
                        <tr>
                            <td>User Name</td>
                            <td><span id="user-name"></span></td>
                        </tr>
                        <tr>
                            <td>Mobile</td>
                            <td><span id="mobile"></span></td>
                        </tr>
                        <tr>
                            <td>Title</td>
                            <td><span id="title"></span></td>
                        </tr>
                        <tr>
                            <td>Description</td>
                            <td><span id="description"></span></td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td><span id="status"></span></td>
                        </tr>
                        <tr>
                      <td>Remarks</td>
                      <td>
                       <form id="update_remarks_form" method="POST" action="update-seller-commission.php">
                       <input type="hidden" name="update_remarks" value="1">
                        <input type="hidden" name="query_id" value="1">
                        <textarea id="remarks" name="remarks"></textarea>
                        <button type="submit">Update Remarks</button>
                        </form>
                      </td>
                      </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</section>

<script>
    $('#transfer_form').validate({

        rules: {
            amount: "required",

        }
    });
</script>
<script>
    $('#transfer_form').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        if ($("#transfer_form").validate().form()) {
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: formData,
                beforeSend: function() {
                    $('#submit_button').html('Please wait..');
                },
                cache: false,
                contentType: false,
                processData: false,
                success: function(result) {

                    $('#transfer_result').html(result);
                    $('#transfer_result').show().delay(3000).fadeOut();
                    $('#submit_button').html('Submit');
                    $('#amount').val('');
                    $('#delivery-boys').bootstrapTable('refresh');
                    setTimeout(function() {
                        $('#fundTransferModal').modal('hide');
                    }, 3000);
                }
            });
        }
    });
</script>
<script>
    $('#users_table').on('click-row.bs.table', function (e, row, $element) {

        // Populate the modal with data from the selected row
        $('#query-category-name').text(row['query_category_name']);
        $('#user-name').text(row['user_name']);
        $('#mobile').text(row['mobile']);
        $('#title').text(row['title']);
        $('#description').text(row['description']);
        if (row['status'] === 1) {
        $('#status').text('Active');
    } else {
        $('#status').text('pending');
    }
    $('#remarks').text(row['remarks']);

        // Show the modal
        $('#category-wise-commission-modal').modal('show');
    });
</script>

<script>
    $("#category-wise-commission-modal").on("hidden.bs.modal", function() {
        location.reload();
    });
</script>
<!-- Add this element to display the update message -->
<div id="updateMessage" class="alert alert-success" style="display: none;"></div>




