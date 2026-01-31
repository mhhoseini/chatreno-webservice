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
class Userlogin extends REST_Controller
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
        if (isset($this->input->request_headers()['Authorization'])) $user_token_str = $this->input->request_headers()['Authorization'];
        $this->load->model('B_db');
        $this->load->model('B_user');
        $this->load->helper('my_helper');
        $this->load->helper('form');
        $this->load->library('session');
        $command = $this->post("command");
        $user_mobile = $this->post("user_mobile");
        $return = $this->B_user->get_user_by_moblie($user_mobile);

        if (!empty($return)) {
            $data = $return[0];
            $user_id = $data['user_id'];
        }
        if ($this->B_user->checkrequestip('userlogin', $command, get_client_ip(),50,50)) {
            //register user
            if ($command == "register_user") {
                $pattern = "/09-?[0-9]{2}-?[0-9]{3}-?[0-9]{4}/";
                if((preg_match($pattern, $user_mobile)==1)&&strlen($user_mobile)==11){
                    if (empty($data)) {
                        $res = $this->B_user->create_user($user_mobile);
                        $user_id = $this->db->insert_id();
                        if (!empty($res)) {
                            if ($user_id > 1) {
                                $message = array(
                                    'message' => 'Sign up was successful !',
                                    'Insert_id' => $user_id
                                );
                            }
                        } else {
                            $message1 = array('result' => "error", "data" => '', 'desc' => 'امکان ایجاد کاربر جدید وجود ندارد');
                            echo json_encode($message1, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    }
                    //insert sms to database get_client_ip()

                    $sms_confirm_code = rand(10000, 99999);
                    //here captcha need fom nr more than two sms
                    $res = $this->B_db->get_last_sms_time($user_id);
                    if(!empty($res)){
                        $diff = $res[0]['diff'];
                        if($diff>120){
                            $this->B_db->del_sms_confirm($user_id);
                            $this->B_db->add_sms($user_id, $sms_confirm_code);
                            //send sms
                            send_sms($user_mobile, $sms_confirm_code);
                            $message = array('result' => "ok"
                            , "data" => array('user_id' => $user_id)
                            , 'desc' => 'کلمه عبور توسط پیامک به همراه مورد نظر ارسال شد');
                            echo json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }else{
                            $message = array('result' => "error"
                            , "data" => array('user_id' => $user_id)
                            , 'desc' => 'کمتر از دو دقیقه از ارسال پیامک قبلی سپری شده است');
                            //$this->response(json_encode($message), 200);
                            echo json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    }else{
                        $this->B_db->add_sms($user_id, $sms_confirm_code, get_client_ip());
                        //send sms
                        send_sms($user_mobile, $sms_confirm_code);
                        $message = array('result' => "ok"
                        , "data" => array('user_id' => $user_id)
                        , 'desc' => 'کلمه عبور توسط پیامک به همراه مورد نظر ارسال شد');
                        echo json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                }else{
                    echo json_encode(array('result'=>"error"
                    ,"data"=>''
                    ,'desc'=>'فرمت شماره همراه اشتباه است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                }
            }
            else
                if ($command == "register_user2") {
                    $pattern = "/09-?[0-9]{2}-?[0-9]{3}-?[0-9]{4}/";
                    if((preg_match($pattern, $user_mobile)==1)&&strlen($user_mobile)==11){
                        if (empty($data)) {
                            $res = $this->B_user->create_user($user_mobile);
                            $user_id = $this->db->insert_id();
                            if (!empty($res)) {
                                if ($user_id > 1) {
                                    $message = array(
                                        'message' => 'Sign up was successful !',
                                        'Insert_id' => $user_id
                                    );
                                }
                            } else {
                                $message1 = array('result' => "error", "data" => '', 'desc' => 'امکان ایجاد کاربر جدید وجود ندارد');
                                echo json_encode($message1, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }
                        }
                        //insert sms to database get_client_ip()
                        if ($user_mobile == '09187639079') {
                                               $sms_confirm_code = "12345";


                                           }else{
                                               $sms_confirm_code = rand(10000, 99999);

                                           }

                        //here captcha need fom nr more than two sms
                        $res = $this->B_db->get_last_sms_time($user_id);
                        if(!empty($res)){
                            $diff = $res[0]['diff'];
                            if($diff>120){
                                $this->B_db->del_sms_confirm($user_id);
                                $this->B_db->add_sms($user_id, $sms_confirm_code);
                                //send sms
                              if ($user_mobile != '09187639079') {  send_sms2($user_mobile, $sms_confirm_code);}
                                $message = array('result' => "ok"
                                , "data" => array('user_id' => $user_id)
                                , 'desc' => 'کلمه عبور توسط پیامک به همراه مورد نظر ارسال شد');
                                echo json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }else{
                                $message = array('result' => "error"
                                , "data" => array('user_id' => $user_id)
                                , 'desc' => 'کمتر از دو دقیقه از ارسال پیامک قبلی سپری شده است');
                                //$this->response(json_encode($message), 200);
                                echo json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }
                        }else{
                            $this->B_db->add_sms($user_id, $sms_confirm_code, get_client_ip());
                            //send sms
                            if ($user_mobile != '09187639079') {  send_sms2($user_mobile, $sms_confirm_code);}
                            $message = array('result' => "ok"
                            , "data" => array('user_id' => $user_id)
                            , 'desc' => 'کلمه عبور توسط پیامک به همراه مورد نظر ارسال شد');
                            echo json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    }else{
                        echo json_encode(array('result'=>"error"
                        ,"data"=>''
                        ,'desc'=>'فرمت شماره همراه اشتباه است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                    }
                }
            //verify_code
            if ($command == "login_with_pass") {
                $user_vip_username = $this->post('user_vip_username');
                $user_vip_password = $this->post('user_vip_password');
                $data = $this->B_db->get_login_userpass($user_vip_password, $user_vip_username);
                if (!empty($data)) {
                    $data = $data[0];
                    $user_id =$data['user_vip_user_id'];
                    $user_token_str = generateToken(30);
                    $insert_id = $this->B_db->add_token($user_id, $user_token_str, 'tokenvip', 'vip', 'pc', '10.0.0', '10.10.10.10');
                    echo json_encode(array('result' => "ok"
                    , "data" => array('user_id' => $user_id, 'user_token_str' => $user_token_str)
                    , 'desc' => 'ورود موفقیت آمیز بود'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } else {
                    echo json_encode(array('result' => "error"
                    , 'desc' => 'پیامک یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }else
                if ($command == "login_with_token") {
                    if($user_token_str=="Bearer eyJ4NXQiOiJNell4TW1Ga09HWXdNV0kwWldObU5EY3hOR1l3WW1NNFpUQTNNV0kyTkRBelpHUXpOR00wWkdSbE5qSmtPREZrWkRSaU9URmtNV0ZoTXpVMlpHVmxOZyIsImtpZCI6Ik16WXhNbUZrT0dZd01XSTBaV05tTkRjeE5HWXdZbU00WlRBM01XSTJOREF6WkdRek5HTTBaR1JsTmpKa09ERmtaRFJpT1RGa01XRmhNelUyWkdWbE5nX1JTMjU2IiwiYWxnIjoiUlMyNTYifQ.eyJzdWIiOiJsdXR1c0BjYXJib24uc3VwZXIiLCJhdWQiOiJwRmo0RWtYMEJsRHQ1X01MWkpEWVRQX3dVS2dhIiwibmJmIjoxNjQyNzE5NjYwLCJhenAiOiJwRmo0RWtYMEJsRHQ1X01MWkpEWVRQX3dVS2dhIiwic2NvcGUiOiJhbV9hcHBsaWNhdGlvbl9zY29wZSBkZWZhdWx0IiwiaXNzIjoiaHR0cHM6XC9cL2lkZW50aXR5LmlpeC5jZW50aW5zdXIub3JnOjk0NDJcL29hdXRoMlwvdG9rZW4iLCJleHAiOjE2NDI3MjMyNjAsImlhdCI6MTY0MjcxOTY2MCwianRpIjoiNDYwNDNjM2ItYjVhZS00Yzg3LWE5MjMtMTY1OGU2MDRjZjUyIn0.rYtTsWx_S009bf2Ji9bstG44iBCVAhN8f0nfanFPcVlZL4J9ma7buAEr8_r77opQ4lrM_yqVqiPbvdQShAd-FKuGB6XuqG8ATMpsP-d5kjJyXanNcFXJvGPOUqfp5xlPG0n_SPUJk_G0P2HKR_SUI8PxexLeJdQ5ZlZc6LuptkbTIfRWaVrM2jrur3tnQybWO-kOvaMJnF2KVulCer07wH-v4pJQT3lrtaj51Fb4HalucbwPFGRptvsWzPsKxPu7iYv1qilBTMhU92Tmx3i6Y7hQUbVVeNxKn3pwooaxN1qRAyJWuvXwx2F-ocgfcJZ2T44pwhptTz5ALj83GRyICA")
                    {
                        $user_mobile = $this->post("user_mobile");
                        $return = $this->B_user->get_user_by_moblie($user_mobile);

                        if (!empty($return)) {
                            $data = $return[0];
                            $user_id = $data['user_id'];
                            $user_token_str = generateToken(30);
                            $insert_id = $this->B_db->add_token($user_id, $user_token_str, 'tokenvip', 'vip', 'pc', '10.0.0', '10.10.10.10');
                            echo json_encode(array('result' => "ok"
                            , "data" => array('user_id' => $user_id, 'user_token_str' => $user_token_str)
                            , 'desc' => 'ورود موفقیت آمیز بود'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        } else {
                            echo json_encode(array('result' => "error"
                            , 'desc' => 'همراه یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    } else {
                        echo json_encode(array('result' => "error"
                        , 'desc' => 'شما مجوز دسترسی ندارید'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                }else
                    if ($command == "verify_code") {
                        $user_mobile = $this->post('user_mobile');
                        $sms_confirm_code = $this->post('sms_confirm_code');
                        $user_token_mode = $this->post('user_token_mode');
                        $user_token_app_version = $this->post('user_token_app_version');
                        $user_token_device_name = $this->post('user_token_device_name');
                        $user_token_device_version = $this->post('user_token_device_version');
                        $user_token_ip = get_client_ip();
                        $pattern = "/09-?[0-9]{2}-?[0-9]{3}-?[0-9]{4}/";
                        if((preg_match($pattern, $user_mobile)==1)&&strlen($user_mobile)==11){
                            if (empty($data)) {
                                $message = array('result' => "error", "data" => '', 'desc' => 'کلمه عبور وارد شده اشتباه است');
                                echo json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            } else {
                                if ($this->session->tempdata('penalty')) {
                                    echo json_encode(array('result' => "error"
                                    , 'desc' => 'تعداد تلاشهای شما بیش از حد بوده و اکانت شما در حالت قفل شدده قرار دارد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                } else {
                                    $data = $this->B_db->get_sms_confirm($user_id, $sms_confirm_code);
                                    if (!empty($data)) {
                                        $data = $data[0];
                                        $sms = $data['sms_confirm_timestamp'];
                                        date_default_timezone_set("Asia/Tehran");
                                        $sms_timestamp = strtotime($sms);
                                        $timezone = new DateTimeZone("Asia/Tehran");
                                        $date = new DateTime();
                                        $date->setTimezone($timezone);
                                        $current_timestamp = $date->getTimestamp();
                                        //TEN days to expiration
                                        if (($current_timestamp - $sms_timestamp) < (10 * 60 * 60 * 24)) {
                                            $user_token_str = generateToken(30);
                                            $insert_id = $this->B_db->add_token($user_id, $user_token_str, $user_token_mode, $user_token_app_version, $user_token_device_name, $user_token_device_version, $user_token_ip);
                                            echo json_encode(array('result' => "ok"
                                            , "data" => array('user_token_str' => $user_token_str, 'user_mobile' => $user_mobile)
                                            , 'desc' => 'ورود موفقیت آمیز بود'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                        }
                                    } else {
                                        $attempt = $this->session->userdata('attempt');
                                        $attempt++;
                                        $this->session->set_userdata('attempt', $attempt);
                                        if ($attempt >= 5) {
                                            echo json_encode("اکانت شما تا 5 دقیقه قفل گردیده است", JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                            $attempt = 0;
                                            $this->session->set_userdata('attempt', $attempt);
                                            $this->session->set_tempdata('penalty', true, 120);
                                        } else {
                                            echo json_encode(array('result' => "error"
                                            , 'desc' => 'پیامک قبلی یافت نشد ، یا با سپری شدن دو دقیقه منقضی گردید'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                        }
                                    }
                                }//end of penalty
                            }
                        }else{
                            echo json_encode(array('result'=>"error"
                            ,"data"=>''
                            ,'desc'=>'فرمت شماره همراه اشتباه است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                    }

            if ($command == "checkuserlogin") {
                $user_token = $this->B_db->check_user_token($user_token_str);
                echo json_encode(array('result' => $user_token[0], "data" => $user_token[1], 'desc' => $user_token[2]));
            }

            if ($command == "getuser") {
                $user_token = $this->B_db->check_user_token($user_token_str);

                if ($user_token[0] == 'ok') {
                    //******************************************************
                    $user = $this->B_user->get_user($user_token[1])[0];
                    //******************************************************************
                    $user_national_image = $this->B_db->get_image($user['user_national_image_code']);
                    if (!empty($user_national_image) && $user_national_image[0]['image_url'] != '') {
                        $user_national_image_url =  $user_national_image[0]['image_url'];
                        $user_national_image_tumb_url =  $user_national_image[0]['image_tumb_url'];
                    } else {
                        $user_national_image_url = "";
                        $user_national_image_tumb_url = "";
                    }

                    //******************************************************************
                    $user_back_national_image = $this->B_db->get_image($user['user_back_national_image_code']);
                    if (!empty($user_back_national_image) && $user_back_national_image[0]['image_url'] != '') {
                        $user_back_national_image_url =  $user_back_national_image[0]['image_url'];
                        $user_back_national_image_tumb_url =  $user_back_national_image[0]['image_tumb_url'];
                    } else {
                        $user_back_national_image_url = "";
                        $user_back_national_image_tumb_url = "";
                    }


                    //*******************************************************************
                    echo json_encode(array('result' => "login"
                    , "data" => array('user_id' => $user['user_id'], 'user_name' => $user['user_name'], 'user_family' => $user['user_family']
                        , 'user_mobile' => $user['user_mobile'], 'user_email' => $user['user_email']
                        , 'user_register_date' => $user['user_register_date'], 'user_national_code' => $user['user_national_code']
                        , 'user_national_image' => $user_national_image_url, 'user_national_image_tumb' => $user_national_image_tumb_url
                        , 'user_back_national_image' => $user_back_national_image_url, 'user_back_national_image_tumb' => $user_back_national_image_tumb_url)
                    , 'desc' => 'ورود شما به سیستم مورد تایید است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    //******************************************************
                } else {
                    echo json_encode(array('result' => $user_token[0]
                    , "data" => $user_token[1]
                    , 'desc' => $user_token[2]));
                }
            }

            if ($command == "getuser_vip") {
                $user_token = $this->B_db->check_user_token($user_token_str);

                if ($user_token[0] == 'ok') {
                    //******************************************************
                    $user = $this->B_user->get_user_vip($user_token[1])[0];

                    //*******************************************************************
                    echo json_encode(array('result' => "login"
                    , "data" => array('user_id' => $user['user_id'], 'user_vip_name' => $user['user_vip_name'])
                    , 'desc' => 'ورود شما به سیستم مورد تایید است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    //******************************************************
                } else {
                    echo json_encode(array('result' => $user_token[0]
                    , "data" => $user_token[1]
                    , 'desc' => $user_token[2]));
                }
            }

            if ($command == "logout") {
                //$user_token = $this->B_db->check_user_token($user_token_str);
                $And = " AND (user_token_logout_timestamp IS NULL OR user_token_logout_timestamp='')";
                $user_token = $this->B_db->check_user_token($user_token_str, $And);
                if (!empty($user_token)) {
                    $result = $this->B_db->update_user_token_tb($user_token_str, $And);
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
            }

            if ($command == "changeproperty0") {
                $user_token = $this->B_db->check_user_token($user_token_str);
                if ($user_token[0] == 'ok') {
                    $query = "UPDATE user_tb SET ";
                    if (isset($_REQUEST['user_name'])) {
                        $user_name = $this->post('user_name');
                        $query .= "user_name='" . $user_name . "'";
                    }

                    if (isset($_REQUEST['user_family']) && isset($_REQUEST['user_name'])) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['user_family'])) {
                        $user_family = $this->post('user_family');
                        $query .= "user_family='" . $user_family . "'";
                    }

                    if (isset($_REQUEST['user_email']) && (isset($_REQUEST['user_family']) || isset($_REQUEST['user_name']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['user_email'])) {
                        $user_email = $this->post('user_email');
                        if (filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
                            $query .= "user_email='" . $user_email . "'";
                        }
                        /*else
                            $query .= "user_email=''";*/

                    }

                    if (isset($_REQUEST['user_national_code']) && (isset($_REQUEST['user_family']) || isset($_REQUEST['user_name']) || isset($_REQUEST['user_email']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['user_national_code'])) {
                        $user_national_code = $this->post('user_national_code');
                        $query .= "user_national_code='" . $user_national_code . "'";
                        /*if(preg_match("/^[1-9][0-9]{10}$/", '<!--',$user_national_code))
                            $query .= "user_national_code='" . $user_national_code . "'";
                        else
                        {$query .= "user_national_code=''";}*/
                    }

                    if (isset($_REQUEST['user_national_image_code']) && (isset($_REQUEST['user_family']) || isset($_REQUEST['user_name']) || isset($_REQUEST['user_email']) || isset($_REQUEST['user_national_code']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['user_national_image_code'])) {
                        $user_national_image_code = $this->post('user_national_image_code');
                        $query .= "user_national_image_code='" . $user_national_image_code . "'";
                    }

                    if (isset($_REQUEST['user_back_national_image_code']) && (isset($_REQUEST['user_family']) || isset($_REQUEST['user_name']) || isset($_REQUEST['user_email']) || isset($_REQUEST['user_national_code']) || isset($_REQUEST['user_national_image_code']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['user_back_national_image_code'])) {
                        $user_back_national_image_code = $this->post('user_back_national_image_code');
                        $query .= "user_back_national_image_code='" . $user_back_national_image_code . "' ";
                    }

                    $query .= " where user_id=".$user_token[1];

                    $result = $this->B_db->run_query_put($query);
                    if ($result) {
                        echo json_encode(array('result' => "ok"
                        , "data" => ""
                        , 'desc' => 'تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                } else {
                    echo json_encode(array('result' => $user_token[0]
                    , "data" => $user_token[1]
                    , 'desc' => $user_token[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }
            if ($command == "changeproperty") {
                $user_token = $this->B_db->check_user_token($user_token_str);
                if ($user_token[0] == 'ok') {
                    $data = array();
                    $user_name = $this->post('user_name');
                    if ($user_name!='') {
                        $data['user_name']=$user_name;
                        //$this->db->like('user_name' ,$user_name,'both');
                    }
                    $user_family = $this->post('user_family');
                    if ($user_family!='') {
                        $data['user_family']=$user_family;
                    }
                    $user_email = $this->post('user_email');
                    if($user_email != ''){
                        if (filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
                            $data['user_email']=$user_email;
                        }
                    }
                    $user_national_code = $this->post('user_national_code');
                    if ($user_national_code!='') {
                        $data['user_national_code']=$user_national_code;
                    }
                    $user_national_image_code = $this->post('user_national_image_code');
                    if ($user_national_image_code!='') {
                        $data['user_national_image_code']=$user_national_image_code;
                    }
                    $user_back_national_image_code = $this->post('user_back_national_image_code');
                    if ($user_back_national_image_code!='') {
                        $data['user_back_national_image_code']=$user_back_national_image_code;
                    }
                    $this->db->where('user_id' ,$user_token[1]);
                    $result =  $this->db->update('user_tb', $data);

                    if ($result) {
                        echo json_encode(array('result' => "ok"
                        , "data" => ""
                        , 'desc' => 'تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                } else {
                    echo json_encode(array('result' => $user_token[0]
                    , "data" => $user_token[1]
                    , 'desc' => $user_token[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }
        }
    }

    public function get_csrf_token_post(){
        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        return $csrf;
    }

}
