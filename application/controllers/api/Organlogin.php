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
class Organlogin extends REST_Controller
{

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
        if (isset($this->input->request_headers()['Authorization'])) $organ_token_str = $this->input->request_headers()['Authorization'];
        $this->load->model('B_organ');
        $this->load->model('B_db');
        $this->load->helper('my_helper');
        $this->load->model('B_user');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('organlogin', $command, get_client_ip(),50,50)) {
            if ($command == "login_organ") {//register organ
                $organ_username = $this->post('organ_username');
                $organ_pass = $this->post('organ_pass');
                $result = $this->B_organ->organ_login($organ_username, $organ_pass);
                if (empty($result)) {
                    echo json_encode(array('result' => "error"
                    , "data" => ''
                    , 'desc' => 'شماره همراه یا رمز عبور اشتباه است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } else {
                    $organ_token_app_version = $this->post('organ_token_app_version');
                    $organ_token_mode = $this->post('organ_token_mode');
                    $organ_token_device_name = $this->post('organ_token_device_name');
                    $organ_token_device_version = $this->post('organ_token_device_version');
                    $organ_token_ip = $this->post('organ_token_ip');
                    $organ = $result[0];
                    $organ_token_organ_id = $organ['organ_id'];
                    $organ_token_str = generateToken(30);
                    $result = $this->B_organ->add_organ_token($organ_token_organ_id, $organ_token_str, $organ_token_mode, $organ_token_app_version, $organ_token_device_name, $organ_token_device_version, $organ_token_ip);
                    $result1 = $this->B_db->get_image($organ['organ_logo']);
                    $image_url = "";
                    if (!empty($result1)) {
                        $image = $result1[0];
                        $image_url = $image['image_tumb_url'];
                    }
                    echo json_encode(array('result' => "ok"
                    , "data" => array('organ_id' => $organ_token_organ_id, 'organ_token_str' => $organ_token_str, 'organ_agentmobile' => $organ['organ_agentmobile'], 'organ_agent' => $organ['organ_agent'], 'organ_name' => $organ['organ_name'], 'organ_logo' => $image_url)
                    , 'desc' => 'ورود موفقیت آمیز بود'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            } else if ($command == "checkorganlogin") {
                $organtoken = checkorgantoken($organ_token_str);
                echo json_encode(array('result' => $organtoken[0]
                , "data" => $organtoken[1]
                , 'desc' => $organtoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else if ($command == "getorgan") {
                $organtoken = checkorgantoken($organ_token_str);
                if ($organtoken[0] == 'ok') {
                    $organ_id = $organtoken[1];
                    $result = $this->B_organ->all_organ($organ_id);
                    if (!empty($result)) {
                        $organ = $result[0];
                        $result1 = $this->B_db->get_image($organ['organ_logo']);
                        $imageurl = "";
                        if (!empty($result1)) {
                            $image = $result1[0];
                            if ($image['image_url']) {
                                $imageurl =  $image['image_url'];
                            }
                        }
                        echo json_encode(array('result' => "ok"
                        , "data" => array('organ_id' => $organ['organ_id']
                            , 'organ_agent' => $organ['organ_agent'], 'organ_name' => $organ['organ_name'], 'organ_agentmobile' => $organ['organ_agentmobile']
                            , 'organ_tell' => $organ['organ_tell']
                            , 'organ_address' => $organ['organ_address']
                            , 'organ_logo' => $imageurl
                            , 'desc' => 'ورود شما به سیستم مورد تایید است')), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                } else {
                    echo json_encode(array('result' => $organtoken[0]
                    , "data" => $organtoken[1]
                    , 'desc' => $organtoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            } else if ($command == "logout") {
                $result = $this->B_organ->get_organ_token($organ_token_str);
                $organtoken = checkorgantoken($organ_token_str);
                if ($organtoken[0] == 'ok') {
                    $result = $this->B_organ->update_organ_token($organ_token_str);
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
                $organtoken = checkorgantoken($organ_token_str);
                if ($organtoken[0] == 'ok') {
                    $organ_pass = $this->post('organ_pass');
                    $result = $this->B_organ->organ_login_by($organtoken[1], $organ_pass);
                    $num = count($result[0]);
                    if ($num == 0) {
                        echo json_encode(array('result' => "error"
                        , "data" => ''
                        , 'desc' => 'رمز عبور قدیم اشتباه است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    } else {
                        $organ_newpass = $this->post('organ_newpass');
                        $result = $this->B_organ->update_organ_pass($organ_newpass, $organtoken[1]);
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
                    echo json_encode(array('result' => $organtoken[0]
                    , "data" => $organtoken[1]
                    , 'desc' => $organtoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                }
            } else if ($command == "changeproperty") {
                $organtoken = checkorgantoken($organ_token_str);
                if ($organtoken[0] == 'ok') {
                    $query = "UPDATE organ_tb SET ";

                    if (isset($_REQUEST['organ_name'])) {
                        $organ_name = $this->post('organ_name');
                        $query .= "organ_name='" . $organ_name . "'";
                    }

                    if ((isset($_REQUEST['organ_agent'])) && (isset($_REQUEST['organ_name']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['organ_agent'])) {
                        $organ_agent = $this->post('organ_agent');
                        $query .= "organ_agent='" . $organ_agent . "'";
                    }

                    if (isset($_REQUEST['organ_agentmobile']) && (isset($_REQUEST['organ_name']) || isset($_REQUEST['organ_agent']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['organ_agentmobile'])) {
                        $organ_agentmobile = $this->post('organ_agentmobile');
                        $query .= "organ_agentmobile='" . $organ_agentmobile . "'";
                    }

                    if (isset($_REQUEST['organ_pass']) && (isset($_REQUEST['organ_name']) || isset($_REQUEST['organ_agent']) || isset($_REQUEST['organ_agentmobile']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['organ_pass'])) {
                        $organ_pass = $this->post('organ_pass');
                        $query .= "organ_pass='" . $organ_pass . "' ";
                    }

                    if (isset($_REQUEST['organ_tell']) && (isset($_REQUEST['organ_pass']) || isset($_REQUEST['organ_name']) || isset($_REQUEST['organ_agent']) || isset($_REQUEST['organ_agentmobile']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['organ_tell'])) {
                        $organ_tell = $this->post('organ_tell');
                        $query .= "organ_tell='" . $organ_tell . "' ";
                    }

                    if (isset($_REQUEST['organ_address']) && isset($_REQUEST['organ_tell']) || isset($_REQUEST['organ_pass']) || isset($_REQUEST['organ_name']) || isset($_REQUEST['organ_agent']) || isset($_REQUEST['organ_agentmobile'])) {
                        $query .= " ";
                    }
                    if (isset($_REQUEST['organ_address'])) {
                        $organ_address = $this->post('organ_address');
                        $query .= "organ_address='" . $organ_address . "' ";
                    }

                    $query .= "where organ_id=" . $organtoken[1];
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
                    echo json_encode(array('result' => $organtoken[0]
                    , "data" => $organtoken[1]
                    , 'desc' => $organtoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                }
            } else if ($command == "forgetpasstell") {
                $organtoken = checkorgantoken($organ_token_str);
                if ($organtoken[0] == 'ok') {
                    $organ_agentmobile = $this->post('organ_agentmobile');
                    $result = $this->B_organ->get_organ_bymobile($organ_agentmobile);
                    if (empty($result)) {
                        echo json_encode(array('result' => "error"
                        , "data" => ''
                        , 'desc' => 'شماره همراه مورد نظر در سیستم ثبت نشده است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    } else {
                        $organ = $result[0];
                        $organ_id = $organ['organ_id'];
                        $organ_agentmobile = $organ['organ_agentmobile'];
                        $organ_pass = $organ['organ_pass'];
                        if ($result) {
                            send_sms($organ_agentmobile, $organ_pass);
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
                    echo json_encode(array('result' => $organtoken[0]
                    , "data" => $organtoken[1]
                    , 'desc' => $organtoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }
        }
    }
}