<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once('../includes/crud.php');

$db = new Database();
$db->connect();

if (empty($_POST['staff_id'])) {
    $response['success'] = false;
    $response['message'] = "staffs Id is Empty";
    print_r(json_encode($response));
    return false;
}

$staff_id = $db->escapeString($_POST['staff_id']);

$sql = "SELECT * FROM staffs WHERE id=" . $staff_id;
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {

    $sql = "SELECT SUM(amount) AS salary_amount FROM staff_transactions WHERE type='salary' AND staff_id=" . $staff_id;
    $db->sql($sql);
    $result = $db->getResult();
    $salary=$result[0]['salary_amount'];
    
    $sql = "SELECT SUM(amount) AS incentive_amount FROM staff_transactions WHERE type!='salary' AND staff_id=" . $staff_id;
    $db->sql($sql);
    $result1 = $db->getResult();
    $incentive=$result1[0]['incentive_amount'];

    $sql ="SELECT COUNT(id) AS total_leads FROM users WHERE lead_id='$staff_id'";
    $db->sql($sql);
    $res_count = $db->getResult();
    $sql ="SELECT COUNT(id) AS total_joinings FROM users WHERE support_id='$staff_id'";
    $db->sql($sql);
    $res_count1= $db->getResult();
    
    $response['success'] = true;
    $response['message'] = "staff details Retrieved Successfully";
    if(!empty($res[0]['resume']) && !empty($res[0]['aadhar_card']) && !empty($res[0]['education_certificate']) && !empty($res[0]['photo'])){
        $response['document_upload'] = 1;
    }
    else{
        $response['document_upload'] = 0;
    }
    
    $response['salary'] = $salary;
    $response['incentive_earn'] = $incentive;
    $response['total_earnings'] = $salary + $incentive;
    $response['total_leads'] = $res_count[0]['total_leads'];
    $response['total_joinings'] =$res_count1[0]['total_joinings'];
    $response['data'] = $res;
    print_r(json_encode($response));
}
else{
    $response['success'] = false;
    $response['message'] ="Staff Not Found";
    print_r(json_encode($response));
}
?>