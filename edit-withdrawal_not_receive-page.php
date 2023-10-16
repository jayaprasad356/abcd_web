<?php
session_start();
include_once('includes/crud.php');
$db = new Database();

if (!$db->connect()) {
    die("Database connection error: " . $db->getErrorMessage());
}

// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Check if the current time has exceeded the session timeout; if yes, redirect to the login page
$currentTime = time() + 25200; // Adjust the time zone offset as needed
$expired = 720000; // Adjust the session timeout as needed

if ($currentTime > $_SESSION['timeout']) {
    header("Location: login.php");
    exit;
}

unset($_SESSION['timeout']);
$_SESSION['timeout'] = $currentTime + $expired;
?>
<?php
if (isset($_POST['btneditAll'])) {
    try {
        // Create a PDO database connection
        $pdo = new PDO('mysql:host=localhost;dbname=abcd', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Define your SQL query
        $sql = "SELECT id FROM withdrawal_not_receive";

        // Prepare and execute the query
        $stmt = $pdo->query($sql);

        // Fetch the 'id' from the first row
        $id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

        if ($id) {
            // Redirect to the edit page with the fetched 'id'
            header("Location: edit-withdrawal_not_receive.php?id=$id");
            exit;
        } else {
            // Handle the case where no 'id' was found
            echo "No 'id' found for this action.";
        }
    } catch (PDOException $e) {
        // Handle any database connection or query errors
        echo "Database error: " . $e->getMessage();
    }
}




if (isset($_POST['btnNo'])) {
    header("Location:withdrawal_not_receive.php");
    exit;
}

if (isset($_POST['btncancel'])) {
    header("Location:withdrawal_not_receive.php");
    exit;
}

?>
<?php include "header.php"; ?>
<html>

<head>
    <title>Edit Withdrawal Not Receive | - Dashboard</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <h1>Confirm Action</h1>
        <hr />
        <form method="post">
            <p>Are you sure you want to edit?</p>
            <input type="submit" class="btn btn-primary" value="Edit" name="btneditAll" />
            <input type="submit" class="btn btn-danger" value="Cancel" name="btnNo" />
            <input type="submit" class="btn btn-warning" value="Back" name="btncancel" />
        </form>
    </div><!-- /.content-wrapper -->
</body>

</html>
<?php include "footer.php"; ?>
