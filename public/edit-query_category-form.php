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

            $name = $db->escapeString(($_POST['name']));
            $error = array();

     if (!empty($name)) 
		{

        $sql_query = "UPDATE query_category SET name='$name' WHERE id =  $ID";
        $db->sql($sql_query);
        $update_result = $db->getResult();
        if (!empty($update_result)) {
            $update_result = 0;
        } else {
            $update_result = 1;
        }

        // check update result
        if ($update_result == 1) {
            $error['update_query_category'] = " <section class='content-header'><span class='label label-success'>Query Category updated Successfully</span></section>";
        } else {
            $error['update_query_category'] = " <span class='label label-danger'>Failed update</span>";
        }
    }
}


// create array variable to store previous data
$data = array();

$sql_query = "SELECT * FROM query_category WHERE id =" . $ID;
$db->sql($sql_query);
$res = $db->getResult();

if (isset($_POST['btnCancel'])) { ?>
    <script>
        window.location.href = "query_category.php";
    </script>
<?php } ?>
<section class="content-header">
    <h1>
        Edit Query Category<small><a href='query_category.php'><i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Query Category</a></small></h1>
    <small><?php echo isset($error['update_query_category']) ? $error['update_query_category'] : ''; ?></small>
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
                <!-- /.box-header -->
                <!-- form start -->
                <form url="edit-query_category-form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                       <div class="row">
                            <div class="form-group">
                                <div class='col-md-6'>
                                    <label for="exampleInputdate">Name</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="name" value="<?php echo $res[0]['name']; ?>">
                                </div>

                            </div>
                        </div>
                        </div>

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