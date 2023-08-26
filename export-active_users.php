<?php
include_once('includes/crud.php');
$db = new Database();
$db->connect();

$date = $db->escapeString(($_POST['date']));

$sql_query = "SELECT users.id, users.name, users.mobile, users.level, users.worked_days, users.duration, staffs.name AS staff_name, users.today_codes, users.earn, users.joined_date, users.l_referral_count 
    FROM users
    JOIN transactions ON users.id = transactions.user_id JOIN staffs ON staffs.id = users.support_id WHERE DATE(transactions.datetime) = '$date' AND transactions.type = 'generate' GROUP BY users.id ORDER BY today_codes DESC";
$db->sql($sql_query);
$developer_records = $db->getResult();

$filename = "Activeusers-data" . $date . ".csv";
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=\"$filename\"");
$show_coloumn = false;

if (!empty($developer_records)) {
    $file = fopen('php://output', 'w');

    foreach ($developer_records as $record) {
        if (!$show_coloumn) {
            // display field/column names in the first row
            fputcsv($file, array_keys($record));
            $show_coloumn = true;
        }
        fputcsv($file, array_values($record));
    }

    fclose($file);
    exit;
}
?>
