<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//require APPPATH . '/libraries/REST_Controller.php';

// use namespace
use Restserver\Libraries\REST_Controller;

/**
 * This is an code of a few basic user interaction methods
 * all done
 *
 * @package         CodeIgniter
 * @subpackage      aref24 Project
 * @category        Controller
 * @author          Mohammad Hoseini, Abolfazl Ganji
 * @license         MIT
 * @link            https://aref24.ir
 */
class Useraddress extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

    public function index_post()
    {
        if (isset($this->input->request_headers()['Authorization'])) $user_token_str = $this->input->request_headers()['Authorization'];
        $this->load->model('B_user');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('useraddress', $command, get_client_ip(),50,50)) {
            if ($command == "add") {
                $user_address_state_id = $this->post('user_address_state_id');
                $user_address_city_id = $this->post('user_address_city_id');
                $user_address_str = $this->post('user_address_str');
                $user_address_code = $this->post('user_address_code');
                $user_address_name = $this->post('user_address_name');
                $user_address_mobile = $this->post('user_address_mobile');
                $user_address_tell = $this->post('user_address_tell');

                $usertoken = checkusertoken($user_token_str);
                if ($usertoken[0] == 'ok') {
                    $result = $this->B_user->get_user_address($usertoken[1], $user_address_code, $user_address_name);
                    if (empty($result)) {
                        $result = $this->B_user->add_user_address($usertoken[1], $user_address_state_id, $user_address_city_id, $user_address_str, $user_address_code, $user_address_name, $user_address_mobile, $user_address_tell);
                        $user_address_id = $this->db->insert_id();
                        echo json_encode(array('result' => "ok"
                        , "data" => array('user_address_id' => $user_address_id)
                        , 'desc' => 'آدرس با موفقیت ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    } else {
                        $user_address = $result[0];
                        echo json_encode(array('result' => "error"
                        , "data" => array('user_address_id' => $user_address['user_address_id'])
                        , 'desc' => 'عنوان ادرس یا کد پستی تکراری است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                } else {
                    echo json_encode(array('result' => $usertoken[0]
                    , "data" => $usertoken[1]
                    , 'desc' => $usertoken[2]));

                }
            }
            if ($command == "get") {
                $usertoken = checkusertoken($user_token_str);
                if ($usertoken[0] == 'ok') {
                    $user_id = $usertoken[1];
                    $result = $this->B_user->get_user_address_by_userid($user_id);
                    $output = array();
                    foreach ($result as $row) {
                        $record = array();
                        $record['user_address_id'] = $row['user_address_id'];
                        $record['user_address_user_id'] = $row['user_address_user_id'];
                        $record['state_name'] = $row['state_name'];
                        $record['city_name'] = $row['city_name'];
                        $record['state_id'] = $row['state_id'];
                        $record['city_id'] = $row['city_id'];
                        $record['user_address_state_id'] = $row['user_address_state_id'];
                        $record['user_address_city_id'] = $row['user_address_city_id'];
                        $record['user_address_str'] = $row['user_address_str'];
                        $record['user_address_code'] = $row['user_address_code'];
                        $record['user_address_name'] = $row['user_address_name'];
                        $record['user_address_mobile'] = $row['user_address_mobile'];
                        $record['user_address_tell'] = $row['user_address_tell'];
                        $record['user_address_delete'] = $row['user_address_delete'];
                        $record['user_address_timestamp'] = $row['user_address_timestamp'];
                        $output[] = $record;
                    }
                    echo json_encode(array('result' => "ok"
                    , "data" => $output
                    , 'desc' => 'آدرس ها با موفقیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } else {
                    echo json_encode(array('result' => $usertoken[0]
                    , "data" => $usertoken[1]
                    , 'desc' => $usertoken[2]));

                }
            }
            if ($command == "delete") {
                $user_address_id = $this->post('user_address_id');
                $usertoken = checkusertoken($user_token_str);
                $output = array();
                if ($usertoken[0] == 'ok') {
                    $user_id = $usertoken[1];
                    $this->B_user->update_user_address($user_address_id);
                    echo json_encode(array('result' => "ok"
                    , "data" => $output
                    , 'desc' => 'آدرس با موفقیت حذف شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } else {
                    echo json_encode(array('result' => $usertoken[0]
                    , "data" => $usertoken[1]
                    , 'desc' => $usertoken[2]));
                }
            }

            if ($command == "modify") {
                $user_address_id = $this->post('user_address_id');

                $usertoken = checkusertoken($user_token_str);
                if ($usertoken[0] == 'ok') {
//*****************************************************************************************

                    $query = "UPDATE user_address_tb SET ";
                    $user_address_state_id = $this->post('user_address_state_id');
                    if (isset($user_address_state_id)) {
                        $query .= "user_address_state_id='" . $_REQUEST['user_address_state_id'] . "'";
                    }

                    $user_address_city_id = $this->post('user_address_city_id');
                    if (isset($user_address_city_id) && isset($user_address_state_id)) {
                        $query .= ",";
                    }
                    if (isset($user_address_city_id)) {

                        $query .= "user_address_city_id=" . $user_address_city_id . "";
                    }
                    $user_address_str = $this->post('user_address_str');
                    if (isset($user_address_str) && (isset($user_address_city_id) || isset($user_address_state_id))) {
                        $query .= ",";
                    }
                    if (isset($user_address_str)) {
                        $query .= "user_address_str='" . $user_address_str . "'";
                    }
                    $user_address_code = $this->post('user_address_code');
                    if (isset($user_address_code) && (isset($user_address_city_id) || isset($user_address_state_id) || isset($user_address_str))) {
                        $query .= ",";
                    }
                    if (isset($user_address_code)) {
                        $query .= "user_address_code='" . $user_address_code . "'";
                    }
                    $user_address_name = $this->post('user_address_name');
                    if (isset($user_address_name) && (isset($user_address_city_id) || isset($user_address_state_id) || isset($user_address_str) || isset($user_address_code))) {
                        $query .= ",";
                    }
                    if (isset($user_address_name)) {
                        $query .= "user_address_name='" . $user_address_name . "'";
                    }

                    $user_address_mobile = $this->post('user_address_mobile');
                    if (isset($user_address_mobile) && (isset($user_address_city_id) || isset($user_address_state_id) || isset($user_address_str) || isset($user_address_code) || isset($user_address_name))) {
                        $query .= ",";
                    }
                    if (isset($user_address_mobile)) {
                        $query .= "user_address_mobile='" . $user_address_mobile . "' ";
                    }

                    $user_address_tell = $this->post('user_address_tell');
                    if (isset($user_address_tell) && (isset($user_address_mobile) || isset($user_address_city_id) || isset($user_address_state_id) || isset($user_address_str) || isset($user_address_code) || isset($user_address_name))) {
                        $query .= ",";
                    }
                    if (isset($user_address_tell)) {
                        $query .= "user_address_tell='" . $user_address_tell . "' ";
                    }
                    $query .= "where user_address_id=" . $user_address_id;
                    $result = $this->B_db->run_query_put($query);
                    if ($result) {
                        echo json_encode(array('result' => "OK"
                        , "data" => ""
                        , 'desc' => 'تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
//****************************************************************************************************************
                } else {
                    echo json_encode(array('result' => $usertoken[0]
                    , "data" => $usertoken[1]
                    , 'desc' => $usertoken[2]));
                }
            }
        }
    }
}
