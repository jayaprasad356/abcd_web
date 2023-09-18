<?php

include_once('crud.php');
require_once('firebase.php');
require_once('push.php');
require_once('functions.php');


$fn = new functions;
class custom_functions
{
    protected $db;
    function __construct()
    {
        $this->db = new Database();
        $this->db->connect();
    }


    function xss_clean_array($array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = $this->xss_clean($value);
            }
        } else {
            $array = $this->xss_clean($array);
        }
        return $array;
    }

    function xss_clean($data)
    {
        $data = trim($data);
        // Fix &entity\n;
        $data = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        } while ($old_data !== $data);

        // we are done...
        return $data;
    }
    public function get_configurations()
    {
        $sql = "SELECT value FROM system_settings WHERE `variable`='system_timezone'";
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return json_decode($res[0]['value'], true);
        } else {
            return false;
        }
    }
    public function get_value($table,$col,$id)
    {
        $sql = "SELECT $col FROM $table WHERE `id`= $id";
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res)) {
            return $res[0][$col];
        } else {
            return 0;
        }
    }
    public function validate_image($file, $is_image = true)
    {
        if (function_exists('finfo_file')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $type = finfo_file($finfo, $file['tmp_name']);
        } else if (function_exists('mime_content_type')) {
            $type = mime_content_type($file['tmp_name']);
        } else {
            $type = $file['type'];
        }
        $type = strtolower($type);
        if ($is_image == false) {
            if (in_array($type, array('text/plain', 'application/csv', 'application/vnd.ms-excel', 'text/csv'))) {
                return true;
            } else {
                return false;
            }
        } else {
            if (in_array($type, array('image/jpg', 'image/jpeg', 'image/gif', 'image/png', 'application/octet-stream'))) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function get_settings($variable, $is_json = false)
    {
        if ($variable == 'logo' || $variable == 'Logo') {
            $sql = "select value from `system_settings` where variable='Logo' OR variable='logo'";
        } else {
            $sql = "SELECT value FROM `system_settings` WHERE `variable`='$variable'";
        }

        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res) && isset($res[0]['value'])) {
            if ($is_json)
                return json_decode($res[0]['value'], true);
            else
                return $res[0]['value'];
        } else {
            return false;
        }
    }
    public function send_notification_to_user($notify_user_id, $notify_post_id, $type, $category)
    {
        if($type == 'post'){
            $sql = "SELECT * FROM users WHERE id = '" . $notify_user_id . "'";
            $this->db->sql($sql);
            $res = $this->db->getResult();
            $sql = "SELECT * FROM posts WHERE id = '" . $notify_post_id . "'";
            $this->db->sql($sql);
            $postres = $this->db->getResult();
            $user_id = $postres[0]['user_id'];
            $user_name = $res[0]['name'];
            $title = $user_name .' '. $category . ' Your Post';
            $sql = "INSERT INTO notifications (`user_id`,`notify_user_id`,`notify_post_id`,`title`,`type`) VALUES ('$user_id','$notify_user_id','$notify_post_id','$title','$type')";
            $this->db->sql($sql);

        }
        else{
            $sql = "SELECT * FROM users WHERE id = '" . $notify_user_id . "'";
            $this->db->sql($sql);
            $res = $this->db->getResult();
            $user_name = $res[0]['name'];
            $title = $user_name .' Started Following You';
            $sql = "INSERT INTO notifications (`user_id`,`notify_user_id`,`title`,`type`) VALUES ('$notify_post_id','$notify_user_id','$title','$type')";
            $this->db->sql($sql);

        }

    }

    public function get_role($id)
    {
        $sql = "SELECT role FROM admin WHERE id=" . $id;
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res) && isset($res[0]['role'])) {
            return $res[0]['role'];
        } else {
            return 0;
        }
    }
    public function get_branch_num($id)
    {
        if($id != ''){
            $sql = "SELECT support_id,branch_id FROM users WHERE id=" . $id;
            $this->db->sql($sql);
            $res = $this->db->getResult();
            if (!empty($res) && isset($res[0]['support_id']) && $res[0]['support_id'] != '') {
                $staff_id = $res[0]['support_id'];
                $sql = "SELECT mobile FROM staffs WHERE id=" . $staff_id;
                $this->db->sql($sql);
                $res = $this->db->getResult();
                return $res[0]['mobile'];
            } elseif (!empty($res) && isset($res[0]['branch_id']) && $res[0]['branch_id'] != '') {
                $branch_id = $res[0]['branch_id'];
                $sql = "SELECT mobile FROM branches WHERE id=" . $branch_id;
                $this->db->sql($sql);
                $res = $this->db->getResult();
                return $res[0]['mobile'];
            }else {
                return '7676636990';
            }

        }else{
            return '7676636990';

        }

    }
    public function get_code_per_cost($id)
    {
        $sql = "SELECT per_code_cost FROM users WHERE id=" . $id;
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res) && isset($res[0]['per_code_cost'])) {
            return $res[0]['per_code_cost'];
        } else {
            return COST_PER_CODE;
        }
    }
    public function get_code_per_cost_champion($id)
    {
        $sql = "SELECT level FROM users WHERE id = " . $id;
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res) && isset($res[0]['level'])) {
            $level =  $res[0]['level'];
            $per_code_cost = 3;
            if($level == 1){
                $per_code_cost = 3;


            }elseif($level == 2){
                $per_code_cost = 4;

            }
            elseif($level == 3){
                $per_code_cost = 6;

            }
            elseif($level == 4){
                $per_code_cost = 8;

            }
            else{
                $per_code_val = 10;

            }
            // $sql_query = "UPDATE users SET `per_code_val` = $per_code_val,`level` = $level WHERE id =  $id";
            // $this->db->sql($sql_query);

            return $per_code_cost;
        } else {
            return '';
        }
    }
    public function update_refer_code_cost($id)
    {
        $sql = "SELECT l_referral_count FROM users WHERE id = " . $id;
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res) && isset($res[0]['l_referral_count'])) {
            $l_referral_count =  $res[0]['l_referral_count'];
            $per_code_val = 2;
            if($l_referral_count <= 2){
                $per_code_val = 2;
                $level = 1;


            }elseif($l_referral_count >= 3 && $l_referral_count <= 4){
                $per_code_val = 2;
                $level = 2;

            }
            elseif($l_referral_count >= 5 && $l_referral_count <= 7){
                $per_code_val = 3;
                $level = 3;

            }
            elseif($l_referral_count >= 8 && $l_referral_count <= 9){
                $per_code_val = 4;
                $level = 4;

            }else{
                $per_code_val = 6;
                $level = 5;

            }
            $sql_query = "UPDATE users SET `per_code_val` = $per_code_val,`level` = $level WHERE id =  $id";
            $this->db->sql($sql_query);

            return '';
        } else {
            return '';
        }
    }
    public function update_refer_code_cost_champion($id)
    {
        $sql = "SELECT l_referral_count FROM users WHERE id = " . $id;
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res) && isset($res[0]['l_referral_count'])) {
            $l_referral_count =  $res[0]['l_referral_count'];
            $per_code_cost = 4;
            if($l_referral_count <= 2){
                $per_code_cost = 2;
                $level = 1;


            }elseif($l_referral_count >= 3 && $l_referral_count <= 5){
                $per_code_cost = 4;
                $level = 2;

            }
            elseif($l_referral_count >= 6 && $l_referral_count <= 8){
                $per_code_cost = 6;
                $level = 3;

            }
            elseif($l_referral_count >= 9 && $l_referral_count <= 11){
                $per_code_cost = 8;
                $level = 4;

            }else{
                $per_code_cost = 10;
                $level = 5;

            }
            $sql_query = "UPDATE users SET `per_code_cost` = $per_code_cost,`level` = $level WHERE id =  $id";
            $this->db->sql($sql_query);

            return '';
        } else {
            return '';
        }
    }
    public function get_sync_time($level)
    {
        if ($level == 1) {
            $time = 22;

        }
        elseif($level == 2){
            $time = 22 ;
        }
        elseif($level == 3){
            $time = 16;
        }
        elseif($level == 4){
            $time = 10;
        }
        elseif($level == 5){
            $time = 6;
        }
        else {
            $time = 22;
        }

        return $time;
    } 
    public function get_permissions($id)
    {
        $sql = "SELECT permissions FROM admin WHERE id=" . $id;
        $this->db->sql($sql);
        $res = $this->db->getResult();
        if (!empty($res) && isset($res[0]['permissions'])) {
            return json_decode($res[0]['permissions'], true);
        } else {
            return 0;
        }
    }

}
// $this->db->disconnect();
