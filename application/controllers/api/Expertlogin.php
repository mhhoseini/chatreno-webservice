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
class Expertlogin extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        // Configure limits on our controller methods
        // Ensure you getvisitrequesthave created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

    public function index_post()
    {
        if (isset($this->input->request_headers()['Experttokenstr'])) $expert_token_str = $this->input->request_headers()['Experttokenstr'];
        $this->load->model('B_expert');
        $this->load->model('B_db');
        $this->load->helper('my_helper');
        $this->load->model('B_user');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('expertlogin', $command, get_client_ip(),50,50)) {
            if ($command == "login_expert") {//register expert
                $employee_mobile = $this->post('employee_mobile');
                $expert_mobile = $this->post('expert_mobile');
                $expert_pass = $this->post('expert_pass');
                if($employee_mobile==''){
                $result = $this->B_expert->expert_login($expert_mobile, $expert_pass);
                $num = count($result[0]);
                if ($num == 0) {
                    echo json_encode(array('result' => "error"
                    , "data" => ''
                    , 'desc' => 'شماره همراه یا رمز عبور اشتباه است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                } else {
                    $expert_token_app_version = $this->post('expert_token_app_version');
                    $expert_token_mode = $this->post('expert_token_mode');
                    $expert_token_device_name = $this->post('expert_token_device_name');
                    $expert_token_device_version = $this->post('expert_token_device_version');
                    $expert_token_ip = $this->post('expert_token_ip');
                    $expert = $result[0];
                    $expert_token_expert_id = $expert['expert_id'];
                    $expert_token_str = generateToken(30);
                    $result = $this->B_expert->add_expert_token($expert_token_expert_id, $expert_token_str, $expert_token_mode, $expert_token_app_version, $expert_token_device_name, $expert_token_device_version, $expert_token_ip,0);
                    $result1 = $this->B_db->get_image($expert['expert_image_code']);
                    $image = $result1[0];
                    echo json_encode(array('result' => "ok"
                    , "data" => array('expert_id' => $expert_token_expert_id, 'expert_token_str' => $expert_token_str, 'expert_mobile' => $expert_mobile, 'expert_gender' => $expert['expert_gender'], 'expert_name' => $expert['expert_name'], 'expert_family' => $expert['expert_family'], 'expert_image' =>  $image['image_tumb_url'])
                    , 'desc' => 'ورود موفقیت آمیز بود'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
                }else{
                 //**********************************************************************
                    $guery="select * from employee_tb where   employee_mobile='".$employee_mobile."' AND employee_pass='".$expert_pass."'";
                    $result = $this->B_db->run_query($guery);

                    $num = count($result[0]);
                    if ($num == 0) {
                        echo json_encode(array('result' => "error"
                        , "data" => ''
                        , 'desc' => 'شماره همراه یا رمز عبور اشتباه است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }else {
                        $employee= $result[0];

                        $guery2 = "select * from expert_tb,expert_extra_tb where  expert_extra_expert_id=expert_id AND expert_extra_employee_id=" . $employee['employee_id'] . " AND expert_mobile='" . $expert_mobile . "'";
                        $result2 = $this->B_db->run_query($guery2);

                        $num2 = count($result2[0]);
                        if ($num2 == 0) {
                            echo json_encode(array('result' => "error"
                            , "data" => ''
                            , 'desc' => 'شماره همراه یا رمز عبور اشتباه است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                        } else {
                            $expert_token_app_version = $this->post('expert_token_app_version');
                            $expert_token_mode = $this->post('expert_token_mode');
                            $expert_token_device_name = $this->post('expert_token_device_name');
                            $expert_token_device_version = $this->post('expert_token_device_version');
                            $expert_token_ip = $this->post('expert_token_ip');
                            $expert = $result2[0];
                            $expert_token_expert_id = $expert['expert_id'];
                            $expert_token_str = generateToken(30);
                            $result = $this->B_expert->add_expert_token($expert_token_expert_id, $expert_token_str, $expert_token_mode, $expert_token_app_version, $expert_token_device_name, $expert_token_device_version, $expert_token_ip,$employee['employee_id']);
                            $result1 = $this->B_db->get_image($expert['expert_image_code']);
                            $image = $result1[0];
                            echo json_encode(array('result' => "ok"
                            , "data" => array('expert_id' => $expert_token_expert_id, 'expert_token_str' => $expert_token_str, 'expert_mobile' => $expert_mobile, 'expert_gender' => $expert['expert_gender'], 'expert_name' => $expert['expert_name'], 'expert_family' => $expert['expert_family'], 'employee_name' => $employee['employee_name'], 'employee_family' => $employee['employee_family'], 'expert_image' =>  $image['image_tumb_url'])
                            , 'desc' => 'ورود موفقیت آمیز بود'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    }
                    //**********************************************************************
                }
            } else if ($command == "checkexpertlogin") {
                $experttoken = checkexperttoken($expert_token_str);
                echo json_encode(array('result' => $experttoken[0]
                , "data" => $experttoken[1]
                , 'desc' => $experttoken[2]));
            } else if ($command == "getexpert") {
                $expert_id = $this->post('expert_id');
                $experttoken = checkexperttoken($expert_token_str);
                if ($experttoken[0] == 'ok') {
                    if ($expert_id == $experttoken[1]) {
                        $result = $this->B_expert->all_expert($expert_id);
                        if (!empty($result)) {
                            $expert = $result[0];
                            $result1 = $this->B_db->get_image($expert['expert_image_code']);
                            $image = $result1[0];
                            if( $experttoken[3]!=0){
                                $guery1="select * from employee_tb where employee_id=".$experttoken[3];
                                $result1 = $this->B_db->run_query($guery1);
                                $employee= $result1[0];

                            }
                            echo json_encode(array('result' => "ok"
                            , "data" => array('expert_id' => $expert['expert_id'], 'expert_code' => $expert['expert_code'], 'expert_gender' => $expert['expert_gender'], 'expert_name' => $expert['expert_name'], 'expert_family' => $expert['expert_family'], 'expert_mobile' => $expert['expert_mobile'], 'expert_tell' => $expert['expert_tell']
                                , 'expert_email' => $expert['expert_email']
                                ,'$experttoken'=>$experttoken[3]
                                , 'employee_name' => $employee['employee_name']
                                , 'employee_family' => $employee['employee_family']
                                , 'expert_required_phone' => $expert['expert_required_phone']
                                , 'expert_address' => $expert['expert_address']
                                , 'expert_register_date' => $expert['expert_register_date']
                                , 'expert_state_name' => $expert['state_name']
                                , 'expert_city_name' => $expert['city_name']
                                , 'expert_sector_name' => $expert['expert_sector_name']
                                , 'expert_long' => $expert['expert_long']
                                , 'expert_lat' => $expert['expert_lat']
                                , 'expert_banknum' => $expert['expert_banknum']
                                , 'expert_bankname' => $expert['expert_bankname']
                                , 'expert_banksheba' => $expert['expert_banksheba']
                                , 'expert_image_code' => $expert['expert_image_code']
                                , 'expert_image' =>  $image['image_url']
                                , 'expert_image_tumb' =>  $image['image_tumb_url']
                                , 'expert_evaluatorco_name' => $expert['evaluatorco_name']
                                , 'expert_evaluatorco_logo_url' => IMGADD . $expert['evaluatorco_logo_url']
                                , 'expert_deactive' => $expert['expert_deactive'])
                            , 'desc' => 'ورود شما به سیستم مورد تایید است'), JSON_UNESCAPED_SLASHES);
                        }
                    } else {
                        echo json_encode(array('result' => "error"
                        , "data" => ""
                        , 'desc' => 'توکن مربوط به این کاربر نیست'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                } else {
                    echo json_encode(array('result' => $experttoken[0]
                    , "data" => $experttoken[1]
                    , 'desc' => $experttoken[2]));
                }
            } else if ($command == "logout") {
                $result = $this->B_expert->get_expert_token($expert_token_str);
                $num = count($result[0]);
                if ($num != 0) {
                    $result = $this->B_expert->update_expert_token($expert_token_str);
                    if ($result) {
                        echo json_encode(array('result' => "ok"
                        , "data" => ""
                        , 'desc' => 'خروج انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                } else {
                    echo json_encode(array('result' => "error"
                    , "data" => ""
                    , 'desc' => 'قبلا خارج شده اید'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            } else if ($command == "changepass") {
                $experttoken = checkexperttoken($expert_token_str);
                if ($experttoken[0] == 'ok') {
                    $expert_pass = $this->post('expert_pass');
                    $result = $this->B_expert->expert_login_by($experttoken[1], $expert_pass);
                    $num = count($result[0]);
                    if ($num == 0) {
                        echo json_encode(array('result' => "error"
                        , "data" => ''
                        , 'desc' => 'رمز عبور قدیم اشتباه است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    } else {
                        $expert_newpass = $this->post('expert_newpass');
                        $result = $this->B_expert->update_expert_pass($expert_newpass, $experttoken[1]);
                        if ($result) {
                            echo json_encode(array('result' => "ok"
                            , "data" => ''
                            , 'desc' => 'رمز عبور تغییر یافت'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        } else {
                            echo json_encode(array('result' => "error"
                            , "data" => ''
                            , 'desc' => 'رمز عبور  تغییر نیافت دوباره امتحان کنید'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    }
                } else {
                    echo json_encode(array('result' => $experttoken[0]
                    , "data" => $experttoken[1]
                    , 'desc' => $experttoken[2]));

                }
            } else if ($command == "changeproperty") {
                $experttoken = checkexperttoken($expert_token_str);
                if ($experttoken[0] == 'ok') {
                    $query = "UPDATE expert_tb SET ";

                    if (isset($_REQUEST['expert_name'])) {
                        $expert_name = $this->post('expert_name');
                        $query .= "expert_name='" . $expert_name . "'";
                    }

                    if (isset($_REQUEST['expert_family']) && (isset($_REQUEST['expert_name']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['expert_family'])) {
                        $expert_family = $this->post('expert_family');
                        $query .= "expert_family='" . $expert_family . "'";
                    }

                    if (isset($_REQUEST['expert_gender']) && (isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_family']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['expert_gender'])) {
                        $expert_gender = $this->post('expert_gender');
                        $query .= "expert_gender='" . $expert_gender . "'";
                    }

                    if (isset($_REQUEST['expert_mobile']) && (isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['expert_mobile'])) {
                        $expert_mobile = $this->post('expert_mobile');
                        $query .= "expert_mobile='" . $expert_mobile . "'";
                    }

                    if (isset($_REQUEST['expert_pass']) && (isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['expert_pass'])) {
                        $expert_pass = $this->post('expert_pass');
                        $query .= "expert_pass='" . $expert_pass . "' ";
                    }

                    if (isset($_REQUEST['expert_tell']) && (isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['expert_tell'])) {
                        $expert_tell = $this->post('expert_tell');
                        $query .= "expert_tell='" . $expert_tell . "' ";
                    }

                    if (isset($_REQUEST['expert_email']) && (isset($_REQUEST['expert_tell']) || isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['expert_email'])) {
                        $expert_email = $this->post('expert_email');
                        $query .= "expert_email='" . $expert_email . "' ";
                    }

                    if (isset($_REQUEST['expert_required_phone']) && (isset($_REQUEST['expert_email']) || isset($_REQUEST['expert_tell']) || isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['expert_required_phone'])) {
                        $expert_required_phone = $this->post('expert_required_phone');
                        $query .= "expert_required_phone='" . $expert_required_phone . "' ";
                    }

                    if (isset($_REQUEST['expert_address']) && (isset($_REQUEST['expert_required_phone']) || isset($_REQUEST['expert_email']) || isset($_REQUEST['expert_tell']) || isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['expert_address'])) {
                        $expert_address = $this->post('expert_address');
                        $query .= "expert_address='" . $expert_address . "' ";
                    }

                    if (isset($_REQUEST['expert_state_id']) && (isset($_REQUEST['expert_address']) || isset($_REQUEST['expert_required_phone']) || isset($_REQUEST['expert_email']) || isset($_REQUEST['expert_tell']) || isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['expert_state_id'])) {
                        $expert_state_id = $this->post('expert_state_id');
                        $query .= "expert_state_id=" . $_REQUEST['expert_state_id'] . " ";
                    }

                    if (isset($_REQUEST['expert_city_id']) && (isset($_REQUEST['expert_state_id']) || isset($_REQUEST['expert_address']) || isset($_REQUEST['expert_required_phone']) || isset($_REQUEST['expert_email']) || isset($_REQUEST['expert_tell']) || isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['expert_city_id'])) {
                        $expert_city_id = $this->post('expert_city_id');
                        $query .= "expert_city_id=" . $expert_city_id . " ";
                    }

                    if (isset($_REQUEST['expert_sector_name']) && (isset($_REQUEST['expert_state_id']) || isset($_REQUEST['expert_address']) || isset($_REQUEST['expert_required_phone']) || isset($_REQUEST['expert_email']) || isset($_REQUEST['expert_tell']) || isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['expert_sector_name'])) {
                        $expert_sector_name = $this->post('expert_sector_name');
                        $query .= "expert_sector_name='" . $expert_sector_name . "' ";
                    }

                    if (isset($_REQUEST['expert_long']) && (isset($_REQUEST['expert_sector_name']) || isset($_REQUEST['expert_state_id']) || isset($_REQUEST['expert_address']) || isset($_REQUEST['expert_required_phone']) || isset($_REQUEST['expert_email']) || isset($_REQUEST['expert_tell']) || isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['expert_long'])) {
                        $expert_long = $this->post('expert_long');
                        $query .= "expert_long='" . $expert_long . "' ";
                    }

                    if (isset($_REQUEST['expert_lat']) && (isset($_REQUEST['expert_long']) || isset($_REQUEST['expert_sector_name']) || isset($_REQUEST['expert_state_id']) || isset($_REQUEST['expert_address']) || isset($_REQUEST['expert_required_phone']) || isset($_REQUEST['expert_email']) || isset($_REQUEST['expert_tell']) || isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['expert_lat'])) {
                        $expert_lat = $this->post('expert_lat');
                        $query .= "expert_lat='" . $_REQUEST['expert_lat'] . "' ";
                    }

                    if (isset($_REQUEST['expert_image_code']) && (isset($_REQUEST['expert_lat']) || isset($_REQUEST['expert_long']) || isset($_REQUEST['expert_sector_name']) || isset($_REQUEST['expert_state_id']) || isset($_REQUEST['expert_address']) || isset($_REQUEST['expert_required_phone']) || isset($_REQUEST['expert_email']) || isset($_REQUEST['expert_tell']) || isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['expert_image_code'])) {
                        $expert_image_code = $this->post('expert_image_code');
                        $query .= "expert_image_code='" . $_REQUEST['expert_image_code'] . "' ";
                    }

                    $query .= "where expert_id=" . $experttoken[1];
                    $result = $this->B_db->run_query_put($query);
                    if ($result) {
                        echo json_encode(array('result' => "ok"
                        , "data" => ""
                        , 'desc' => 'تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    } else {
                        echo json_encode(array('result' => "ok"
                        , "data" => ""
                        , 'desc' => 'تغییرات انجام نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                } else {
                    echo json_encode(array('result' => $experttoken[0]
                    , "data" => $experttoken[1]
                    , 'desc' => $experttoken[2]));

                }
            } else if ($command == "forgetpasstell") {
                $experttoken = checkexperttoken($expert_token_str);
                if ($experttoken[0] == 'ok') {
                    $expert_mobile = $this->post('expert_mobile');
                    $result = $this->B_expert->get_expert_bymobile($expert_mobile);
                    $num = count($result[0]);
                    if ($num == 0) {
                        echo json_encode(array('result' => "error"
                        , "data" => ''
                        , 'desc' => 'شماره همراه مورد نظر در سیستم ثبت نشده است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    } else {
                        $expert = $result[0];
                        $expert_id = $expert['expert_id'];
                        $expert_mobile = $expert['expert_mobile'];
                        $expert_pass = $expert['expert_pass'];
                        if ($result) {
                            send_sms($expert_mobile, $expert_pass);
                            echo json_encode(array('result' => "ok"
                            , "data" => ''
                            , 'desc' => 'اطلاعات ورود به همراه نماینده ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                        } else {
                            echo json_encode(array('result' => "error"
                            , "data" => ''
                            , 'desc' => 'اطلاعات ارسال نشد مجددا تلاش نمایید'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    }
                } else {
                    echo json_encode(array('result' => $experttoken[0]
                    , "data" => $experttoken[1]
                    , 'desc' => $experttoken[2]));
                }
            } else if ($command == "forgetpassemail") {
                $experttoken = checkexperttoken($expert_token_str);
                if ($experttoken[0] == 'ok') {
                    $result = $this->B_expert->get_expert_byid($experttoken[1]);
                    $expert = $result[0];
                    $expert_id = $expert['expert_id'];
                    $expert_email = $expert['expert_email'];
                    $expert_mobile = $expert['expert_mobile'];
                    $expert_pass = $expert['expert_pass'];
                    echo json_encode(array('result' => "ok"
                    , "data" => ""
                    , 'desc' => 'اطلاعات ورود به همراه نماینده ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    send_email($expert_email, $expert_pass, $expert_mobile);
                } else {
                    echo json_encode(array('result' => $experttoken[0]
                    , "data" => $experttoken[1]
                    , 'desc' => $experttoken[2]));

                }
            } else if ($command == "change_status") {
                $experttoken = checkexperttoken($expert_token_str);
                if ($experttoken[0] == 'ok') {
                    $result = $this->B_expert->get_expert_status($experttoken[1]);
                    $expert_status = $result[0];
                    $expert_status = $expert_status['expert_status'];
                    if ($expert_status) {
                        $expert_status = 0;
                    } else {
                        $expert_status = 1;
                    }
                    $result = $this->B_expert->add_expert_status($experttoken[1], $expert_status);
                    if ($result) {
                        echo json_encode(array('result' => "ok"
                        , "data" => array('expert_status' => $expert_status, 'expert_status_timstamp' => time())
                        , 'desc' => 'تغییر وضعیت انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    } else {
                        echo json_encode(array('result' => "error"
                        , "data" => ""
                        , 'desc' => 'تغییر وضعیت انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                } else {
                    echo json_encode(array('result' => $experttoken[0]
                    , "data" => $experttoken[1]
                    , 'desc' => $experttoken[2]));
                }
            } else if ($command == "get_status") {
                $experttoken = checkexperttoken($expert_token_str);
                if ($experttoken[0] == 'ok') {
                    $result = $this->B_expert->get_expert_status($experttoken[1]);
                    $expert_statuss = $result[0];
                    $expert_status = $expert_statuss['expert_status'];
                    echo json_encode(array('result' => "ok"
                    , "data" => array('expert_status' => $expert_status, 'expert_status_timstamp' => $expert_statuss['expert_status_timstamp'])
                    , 'desc' => 'تغییر وضعیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } else {
                    echo json_encode(array('result' => $experttoken[0]
                    , "data" => $experttoken[1]
                    , 'desc' => $experttoken[2]));
                }
            } else if ($command == "get_statusinfo") {
                $experttoken = checkexperttoken($expert_token_str);
                if ($experttoken[0] == 'ok') {
                    $result = $this->B_expert->get_expert_status($experttoken[1]);
                    $output = array();
                    foreach ($result as $row) {
                        $record = array();
                        $record['expert_status'] = $row['expert_status'];
                        $record['expert_status_timstamp'] = $row['expert_status_timstamp'];
                        $output[] = $record;
                    }
                    echo json_encode(array('result' => "ok"
                    , "data" => $output
                    , 'desc' => 'تغییر وضعیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                } else {
                    echo json_encode(array('result' => $experttoken[0]
                    , "data" => $experttoken[1]
                    , 'desc' => $experttoken[2]));

                }
            }
        }
    }
}