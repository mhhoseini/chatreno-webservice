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
class Agentlogin extends REST_Controller {

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
        if (isset($this->input->request_headers()['Authorization'])) $agent_token_str = $this->input->request_headers()['Authorization'];
        $this->load->model('B_agent');
        $this->load->model('B_db');
        $this->load->helper('my_helper');
        $this->load->model('B_user');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('agentlogin', $command, get_client_ip(),50,50)) {
            if ($command == "login_agent") {//register agent
                $employee_mobile = $this->post('employee_mobile');
                $agent_mobile = $this->post('agent_mobile');
                $agent_pass = $this->post('agent_pass');
                if($employee_mobile==''){
                $result = $this->B_agent->agent_login($agent_mobile, $agent_pass);
                $num = count($result[0]);
                if ($num == 0) {
                    echo json_encode(array('result' => "error"
                    , "data" => ''
                    , 'desc' => 'شماره همراه یا رمز عبور اشتباه است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                } else {
                    $agent_token_app_version = $this->post('agent_token_app_version');
                    $agent_token_mode = $this->post('agent_token_mode');
                    $agent_token_device_name = $this->post('agent_token_device_name');
                    $agent_token_device_version = $this->post('agent_token_device_version');
                    $agent_token_ip = $this->post('agent_token_ip');
                    $agent = $result[0];
                    $agent_token_agent_id = $agent['agent_id'];
                    $agent_token_str = generateToken(30);
                    $result = $this->B_agent->add_agent_token($agent_token_agent_id, $agent_token_str, $agent_token_mode, $agent_token_app_version, $agent_token_device_name, $agent_token_device_version, $agent_token_ip,0);
                    $result1 = $this->B_db->get_image($agent['agent_image_code']);
                    $image = $result1[0];
                    echo json_encode(array('result' => "ok"
                    , "data" => array('agent_id' => $agent_token_agent_id, 'agent_token_str' => $agent_token_str, 'agent_mobile' => $agent_mobile, 'agent_gender' => $agent['agent_gender'], 'agent_name' => $agent['agent_name'], 'agent_family' => $agent['agent_family'], 'agent_image' =>  $image['image_tumb_url'])
                    , 'desc' => 'ورود موفقیت آمیز بود'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
                }else{
                 //**********************************************************************
                    $guery="select * from employee_tb where   employee_mobile='".$employee_mobile."' AND employee_pass='".$agent_pass."'";
                    $result = $this->B_db->run_query($guery);

                    $num = count($result[0]);
                    if ($num == 0) {
                        echo json_encode(array('result' => "error"
                        , "data" => ''
                        , 'desc' => 'شماره همراه یا رمز عبور اشتباه است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }else {
                        $employee= $result[0];

                        $guery2 = "select * from agent_tb,agent_extra_tb where  agent_extra_agent_id=agent_id AND agent_extra_employee_id=" . $employee['employee_id'] . " AND agent_mobile='" . $agent_mobile . "'";
                        $result2 = $this->B_db->run_query($guery2);

                        $num2 = count($result2[0]);
                        if ($num2 == 0) {
                            echo json_encode(array('result' => "error"
                            , "data" => ''
                            , 'desc' => 'شماره همراه یا رمز عبور اشتباه است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                        } else {
                            $agent_token_app_version = $this->post('agent_token_app_version');
                            $agent_token_mode = $this->post('agent_token_mode');
                            $agent_token_device_name = $this->post('agent_token_device_name');
                            $agent_token_device_version = $this->post('agent_token_device_version');
                            $agent_token_ip = $this->post('agent_token_ip');
                            $agent = $result2[0];
                            $agent_token_agent_id = $agent['agent_id'];
                            $agent_token_str = generateToken(30);
                            $result = $this->B_agent->add_agent_token($agent_token_agent_id, $agent_token_str, $agent_token_mode, $agent_token_app_version, $agent_token_device_name, $agent_token_device_version, $agent_token_ip,$employee['employee_id']);
                            $result1 = $this->B_db->get_image($agent['agent_image_code']);
                            $image = $result1[0];
                            echo json_encode(array('result' => "ok"
                            , "data" => array('agent_id' => $agent_token_agent_id, 'agent_token_str' => $agent_token_str, 'agent_mobile' => $agent_mobile, 'agent_gender' => $agent['agent_gender'], 'agent_name' => $agent['agent_name'], 'agent_family' => $agent['agent_family'], 'employee_name' => $employee['employee_name'], 'employee_family' => $employee['employee_family'], 'agent_image' =>  $image['image_tumb_url'])
                            , 'desc' => 'ورود موفقیت آمیز بود'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    }
                    //**********************************************************************
                }
            } else if ($command == "checkagentlogin") {
                $agenttoken = checkagenttoken($agent_token_str);
                echo json_encode(array('result' => $agenttoken[0]
                , "data" => $agenttoken[1]
                , 'desc' => $agenttoken[2]));
            } else if ($command == "getagent") {
                $agent_id = $this->post('agent_id');
                $agenttoken = checkagenttoken($agent_token_str);
                if ($agenttoken[0] == 'ok') {
                    if ($agent_id == $agenttoken[1]) {
                        $result = $this->B_agent->all_agent($agent_id);
                        if (!empty($result)) {
                            $agent = $result[0];
                            $result1 = $this->B_db->get_image($agent['agent_image_code']);
                            $image = $result1[0];
                            if( $agenttoken[3]!=0){
                                $guery1="select * from employee_tb where employee_id=".$agenttoken[3];
                                $result1 = $this->B_db->run_query($guery1);
                                $employee= $result1[0];

                            }
                            echo json_encode(array('result' => "ok"
                            , "data" => array('agent_id' => $agent['agent_id'], 'agent_code' => $agent['agent_code'], 'agent_gender' => $agent['agent_gender'], 'agent_name' => $agent['agent_name'], 'agent_family' => $agent['agent_family'], 'agent_mobile' => $agent['agent_mobile'], 'agent_tell' => $agent['agent_tell']
                                , 'agent_email' => $agent['agent_email']
                                ,'$agenttoken'=>$agenttoken[3]
                                , 'employee_name' => $employee['employee_name']
                                , 'employee_family' => $employee['employee_family']
                                , 'agent_required_phone' => $agent['agent_required_phone']
                                , 'agent_address' => $agent['agent_address']
                                , 'agent_register_date' => $agent['agent_register_date']
                                , 'agent_state_name' => $agent['state_name']
                                , 'agent_city_name' => $agent['city_name']
                                , 'agent_sector_name' => $agent['agent_sector_name']
                                , 'agent_long' => $agent['agent_long']
                                , 'agent_lat' => $agent['agent_lat']
                                , 'agent_banknum' => $agent['agent_banknum']
                                , 'agent_bankname' => $agent['agent_bankname']
                                , 'agent_banksheba' => $agent['agent_banksheba']
                                , 'agent_image_code' => $agent['agent_image_code']
                                , 'agent_image' =>  $image['image_url']
                                , 'agent_image_tumb' =>  $image['image_tumb_url']
                                , 'agent_company_name' => $agent['company_name']
                                , 'agent_company_logo_url' => IMGADD . $agent['company_logo_url']
                                , 'agent_deactive' => $agent['agent_deactive'])
                            , 'desc' => 'ورود شما به سیستم مورد تایید است'), JSON_UNESCAPED_SLASHES);
                        }
                    } else {
                        echo json_encode(array('result' => "error"
                        , "data" => ""
                        , 'desc' => 'توکن مربوط به این کاربر نیست'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                } else {
                    echo json_encode(array('result' => $agenttoken[0]
                    , "data" => $agenttoken[1]
                    , 'desc' => $agenttoken[2]));
                }
            } else if ($command == "logout") {
                $result = $this->B_agent->get_agent_token($agent_token_str);
                $num = count($result[0]);
                if ($num != 0) {
                    $result = $this->B_agent->update_agent_token($agent_token_str);
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
                $agenttoken = checkagenttoken($agent_token_str);
                if ($agenttoken[0] == 'ok') {
                    $agent_pass = $this->post('agent_pass');
                    $result = $this->B_agent->agent_login_by($agenttoken[1], $agent_pass);
                    $num = count($result[0]);
                    if ($num == 0) {
                        echo json_encode(array('result' => "error"
                        , "data" => ''
                        , 'desc' => 'رمز عبور قدیم اشتباه است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    } else {
                        $agent_newpass = $this->post('agent_newpass');
                        $result = $this->B_agent->update_agent_pass($agent_newpass, $agenttoken[1]);
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
                    echo json_encode(array('result' => $agenttoken[0]
                    , "data" => $agenttoken[1]
                    , 'desc' => $agenttoken[2]));

                }
            } else if ($command == "changeproperty") {
                $agenttoken = checkagenttoken($agent_token_str);
                if ($agenttoken[0] == 'ok') {
                    $query = "UPDATE agent_tb SET ";

                    if (isset($_REQUEST['agent_name'])) {
                        $agent_name = $this->post('agent_name');
                        $query .= "agent_name='" . $agent_name . "'";
                    }

                    if (isset($_REQUEST['agent_family']) && (isset($_REQUEST['agent_name']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['agent_family'])) {
                        $agent_family = $this->post('agent_family');
                        $query .= "agent_family='" . $agent_family . "'";
                    }

                    if (isset($_REQUEST['agent_gender']) && (isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_family']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['agent_gender'])) {
                        $agent_gender = $this->post('agent_gender');
                        $query .= "agent_gender='" . $agent_gender . "'";
                    }

                    if (isset($_REQUEST['agent_mobile']) && (isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['agent_mobile'])) {
                        $agent_mobile = $this->post('agent_mobile');
                        $query .= "agent_mobile='" . $agent_mobile . "'";
                    }

                    if (isset($_REQUEST['agent_pass']) && (isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['agent_pass'])) {
                        $agent_pass = $this->post('agent_pass');
                        $query .= "agent_pass='" . $agent_pass . "' ";
                    }

                    if (isset($_REQUEST['agent_tell']) && (isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['agent_tell'])) {
                        $agent_tell = $this->post('agent_tell');
                        $query .= "agent_tell='" . $agent_tell . "' ";
                    }

                    if (isset($_REQUEST['agent_email']) && (isset($_REQUEST['agent_tell']) || isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['agent_email'])) {
                        $agent_email = $this->post('agent_email');
                        $query .= "agent_email='" . $agent_email . "' ";
                    }

                    if (isset($_REQUEST['agent_required_phone']) && (isset($_REQUEST['agent_email']) || isset($_REQUEST['agent_tell']) || isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['agent_required_phone'])) {
                        $agent_required_phone = $this->post('agent_required_phone');
                        $query .= "agent_required_phone='" . $agent_required_phone . "' ";
                    }

                    if (isset($_REQUEST['agent_address']) && (isset($_REQUEST['agent_required_phone']) || isset($_REQUEST['agent_email']) || isset($_REQUEST['agent_tell']) || isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['agent_address'])) {
                        $agent_address = $this->post('agent_address');
                        $query .= "agent_address='" . $agent_address . "' ";
                    }

                    if (isset($_REQUEST['agent_state_id']) && (isset($_REQUEST['agent_address']) || isset($_REQUEST['agent_required_phone']) || isset($_REQUEST['agent_email']) || isset($_REQUEST['agent_tell']) || isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['agent_state_id'])) {
                        $agent_state_id = $this->post('agent_state_id');
                        $query .= "agent_state_id=" . $_REQUEST['agent_state_id'] . " ";
                    }

                    if (isset($_REQUEST['agent_city_id']) && (isset($_REQUEST['agent_state_id']) || isset($_REQUEST['agent_address']) || isset($_REQUEST['agent_required_phone']) || isset($_REQUEST['agent_email']) || isset($_REQUEST['agent_tell']) || isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['agent_city_id'])) {
                        $agent_city_id = $this->post('agent_city_id');
                        $query .= "agent_city_id=" . $agent_city_id . " ";
                    }

                    if (isset($_REQUEST['agent_sector_name']) && (isset($_REQUEST['agent_state_id']) || isset($_REQUEST['agent_address']) || isset($_REQUEST['agent_required_phone']) || isset($_REQUEST['agent_email']) || isset($_REQUEST['agent_tell']) || isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['agent_sector_name'])) {
                        $agent_sector_name = $this->post('agent_sector_name');
                        $query .= "agent_sector_name='" . $agent_sector_name . "' ";
                    }

                    if (isset($_REQUEST['agent_long']) && (isset($_REQUEST['agent_sector_name']) || isset($_REQUEST['agent_state_id']) || isset($_REQUEST['agent_address']) || isset($_REQUEST['agent_required_phone']) || isset($_REQUEST['agent_email']) || isset($_REQUEST['agent_tell']) || isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['agent_long'])) {
                        $agent_long = $this->post('agent_long');
                        $query .= "agent_long='" . $agent_long . "' ";
                    }

                    if (isset($_REQUEST['agent_lat']) && (isset($_REQUEST['agent_long']) || isset($_REQUEST['agent_sector_name']) || isset($_REQUEST['agent_state_id']) || isset($_REQUEST['agent_address']) || isset($_REQUEST['agent_required_phone']) || isset($_REQUEST['agent_email']) || isset($_REQUEST['agent_tell']) || isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['agent_lat'])) {
                        $agent_lat = $this->post('agent_lat');
                        $query .= "agent_lat='" . $_REQUEST['agent_lat'] . "' ";
                    }

                    if (isset($_REQUEST['agent_image_code']) && (isset($_REQUEST['agent_lat']) || isset($_REQUEST['agent_long']) || isset($_REQUEST['agent_sector_name']) || isset($_REQUEST['agent_state_id']) || isset($_REQUEST['agent_address']) || isset($_REQUEST['agent_required_phone']) || isset($_REQUEST['agent_email']) || isset($_REQUEST['agent_tell']) || isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['agent_image_code'])) {
                        $agent_image_code = $this->post('agent_image_code');
                        $query .= "agent_image_code='" . $_REQUEST['agent_image_code'] . "' ";
                    }

                    $query .= "where agent_id=" . $agenttoken[1];
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
                    echo json_encode(array('result' => $agenttoken[0]
                    , "data" => $agenttoken[1]
                    , 'desc' => $agenttoken[2]));

                }
            } else if ($command == "forgetpasstell") {
                $agenttoken = checkagenttoken($agent_token_str);
                if ($agenttoken[0] == 'ok') {
                    $agent_mobile = $this->post('agent_mobile');
                    $result = $this->B_agent->get_agent_bymobile($agent_mobile);
                    $num = count($result[0]);
                    if ($num == 0) {
                        echo json_encode(array('result' => "error"
                        , "data" => ''
                        , 'desc' => 'شماره همراه مورد نظر در سیستم ثبت نشده است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    } else {
                        $agent = $result[0];
                        $agent_id = $agent['agent_id'];
                        $agent_mobile = $agent['agent_mobile'];
                        $agent_pass = $agent['agent_pass'];
                        if ($result) {
                            send_sms($agent_mobile, $agent_pass);
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
                    echo json_encode(array('result' => $agenttoken[0]
                    , "data" => $agenttoken[1]
                    , 'desc' => $agenttoken[2]));
                }
            } else if ($command == "forgetpassemail") {
                $agenttoken = checkagenttoken($agent_token_str);
                if ($agenttoken[0] == 'ok') {
                    $result = $this->B_agent->get_agent_byid($agenttoken[1]);
                    $agent = $result[0];
                    $agent_id = $agent['agent_id'];
                    $agent_email = $agent['agent_email'];
                    $agent_mobile = $agent['agent_mobile'];
                    $agent_pass = $agent['agent_pass'];
                    echo json_encode(array('result' => "ok"
                    , "data" => ""
                    , 'desc' => 'اطلاعات ورود به همراه نماینده ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    send_email($agent_email, $agent_pass, $agent_mobile);
                } else {
                    echo json_encode(array('result' => $agenttoken[0]
                    , "data" => $agenttoken[1]
                    , 'desc' => $agenttoken[2]));

                }
            } else if ($command == "change_status") {
                $agenttoken = checkagenttoken($agent_token_str);
                if ($agenttoken[0] == 'ok') {
                    $result = $this->B_agent->get_agent_status($agenttoken[1]);
                    $agent_status = $result[0];
                    $agent_status = $agent_status['agent_status'];
                    if ($agent_status) {
                        $agent_status = 0;
                    } else {
                        $agent_status = 1;
                    }
                    $result = $this->B_agent->add_agent_status($agenttoken[1], $agent_status);
                    if ($result) {
                        echo json_encode(array('result' => "ok"
                        , "data" => array('agent_status' => $agent_status, 'agent_status_timstamp' => time())
                        , 'desc' => 'تغییر وضعیت انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    } else {
                        echo json_encode(array('result' => "error"
                        , "data" => ""
                        , 'desc' => 'تغییر وضعیت انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                } else {
                    echo json_encode(array('result' => $agenttoken[0]
                    , "data" => $agenttoken[1]
                    , 'desc' => $agenttoken[2]));
                }
            } else if ($command == "get_status") {
                $agenttoken = checkagenttoken($agent_token_str);
                if ($agenttoken[0] == 'ok') {
                    $result = $this->B_agent->get_agent_status($agenttoken[1]);
                    $agent_statuss = $result[0];
                    $agent_status = $agent_statuss['agent_status'];
                    echo json_encode(array('result' => "ok"
                    , "data" => array('agent_status' => $agent_status, 'agent_status_timstamp' => $agent_statuss['agent_status_timstamp'])
                    , 'desc' => 'تغییر وضعیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } else {
                    echo json_encode(array('result' => $agenttoken[0]
                    , "data" => $agenttoken[1]
                    , 'desc' => $agenttoken[2]));
                }
            } else if ($command == "get_statusinfo") {
                $agenttoken = checkagenttoken($agent_token_str);
                if ($agenttoken[0] == 'ok') {
                    $result = $this->B_agent->get_agent_status($agenttoken[1]);
                    $output = array();
                    foreach ($result as $row) {
                        $record = array();
                        $record['agent_status'] = $row['agent_status'];
                        $record['agent_status_timstamp'] = $row['agent_status_timstamp'];
                        $output[] = $record;
                    }
                    echo json_encode(array('result' => "ok"
                    , "data" => $output
                    , 'desc' => 'تغییر وضعیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                } else {
                    echo json_encode(array('result' => $agenttoken[0]
                    , "data" => $agenttoken[1]
                    , 'desc' => $agenttoken[2]));

                }
            }
        }
    }
}