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

if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User Id is Empty";
    print_r(json_encode($response));
    return false;
}

$user_id = $db->escapeString($_POST['user_id']);
$sql = "SELECT project_type,status FROM users WHERE id = '$user_id'";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {
    $project_type = $res[0]['project_type'];
    $status = $res[0]['status'];
    $sql = "SELECT * FROM whatsapp_group ";
    $db->sql($sql);
    $res = $db->getResult();
    $num = $db->numRows($res);
    
    if ($num >= 1) {
        $response['success'] = true;
        $response['message'] = "Whatsapp Group Retrived Successfully";
        
        if($status == 0){
            $link = $res[0]['link'];

        }else if($project_type == 'abcd'){
            $link = $res[1]['link'];

        }else if($project_type == 'amail'){
            $link = $res[2]['link'];

        }else if($project_type == 'champion'){
            $link = $res[3]['link'];

        }else {
            $link = $res[4]['link'];

        }
        $response['link'] = $link;
        print_r(json_encode($response));
    }
    else{
        $response['success'] = false;
        $response['message'] =" Not Found";
        print_r(json_encode($response));
    }
}else{
    $response['success'] = false;
    $response['message'] = "No Users Found";
    print_r(json_encode($response));

}



?>
