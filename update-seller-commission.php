<?php
session_start(); // Start the session (if not already started)

include('includes/crud.php');
$db = new Database();
$db->connect();
include_once('includes/custom-functions.php');
$fn = new custom_functions;

$sql_query = "SELECT * FROM query WHERE id =" . $ID;
$db->sql($sql_query);
$res = $db->getResult();

if (isset($_POST['update_remarks']) && $_POST['update_remarks'] == 1) {
    $query_id = $db->escapeString($fn->xss_clean($_POST['query_id']));
    $remarks = $db->escapeString($fn->xss_clean($_POST['remarks']));

    $sql = "UPDATE query SET remarks = '$remarks' WHERE id = $query_id";
    if ($db->sql($sql)) {
        $_SESSION['update_message'] = 'Updated Successfully!';
    } else {
        $_SESSION['update_message'] = 'Some Error Occurred! Please try again.';
    }

    // Redirect to queries.php with the message as a parameter
    header('Location: queries.php?message=' . urlencode($_SESSION['update_message']));
    exit;
}
?>
