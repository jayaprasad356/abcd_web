<?php
include_once('includes/crud.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');
$currentdate = date('Y-m-d');

	$join = "WHERE w.user_id = u.id AND w.user_id = b.user_id AND w.status= 0";
	$sql = "SELECT w.id AS id,w.*,w.datetime,u.name,u.mobile,u.l_referral_count,u.current_refers,u.today_codes,u.total_codes,u.today_mails,u.total_mails,u.balance,u.mobile,u.referred_by,u.refer_code,u.worked_days,u.plan,u.bonus_wallet,u.earnings_wallet,u.daily_wallet,u.monthly_wallet,u.level,u.support_id,DATEDIFF( '$currentdate',u.joined_date) AS history,u.duration,b.bank,CONCAT(',' , `account_num`, ',') AS account_num,b.ifsc,b.holder_name,u.project_type FROM `withdrawals` w,`users` u,`bank_details` b $join";
	$db->sql($sql);
	$developer_records = $db->getResult();
	
	$filename = "unpaid-withdrawals-data".date('Ymd') . ".xls";			
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
?>
