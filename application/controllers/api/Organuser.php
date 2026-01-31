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
class Organuser extends REST_Controller
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
        $this->load->model('B_user');
        $this->load->model('B_db');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        $organtoken = checkorgantoken($organ_token_str);
        if ($this->B_user->checkrequestip('organuser', $command, get_client_ip(),50,50)) {
            if ($command == "get_organ_user") {
                $user_id = $this->post('user_id');
                $result = $this->B_organ->get_organ_user($user_id);
                if (!empty($result)) {
                    echo json_encode(array('result' => "ok"
                    , "data" => $result[0]
                    , 'desc' => 'ااطلاعات قرارداد پرسنل مورد نظر بازیابی گردید'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } else {
                    echo json_encode(array('result' => "error"
                    , "data" => ''
                    , 'desc' => 'پرسنل مورد نظر یافت نشد!'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            } else if ($command == "add_organ_user") {
                if ($organtoken[0] == 'ok') {
                    $organ_user_organ_id = $organtoken[1];
                    $user_name = $this->post('user_name');
                    $user_family = $this->post('user_family');
                    $user_mobile = $this->post('user_mobile');
                    $user_personal_code = $this->post('user_personal_code');
                    $user_national_code = $this->post('user_national_code');
                    $commitment_amount = $this->post('commitment_amount');
                    $commitment_num = $this->post('commitment_num');
                    $token = $this->post('token');
                    $res_confirm = $this->B_organ->get_confirm_by_token($token);
                    if (!empty($res_confirm)) {
                        $confirm_id = $res_confirm[0]['organ_confirm_id'];
                        $result2 = $this->B_user->get_user_by_moblie($user_mobile);
                        if (!empty($result2)) {
                            $user_id = $result2[0]['user_id'];
                        } else {
                            $this->B_user->create_user($user_mobile);
                            $user_id = $this->db->insert_id();
                        }
                        if ($user_id) {
                            $msg = "";
                            $data = array();
                            $check = $this->B_organ->check_organ_confirm_exist($user_id, $confirm_id);
                            if ($check == false) {
                                $result = $this->B_organ->add_organ_user($organ_user_organ_id, $user_id, $commitment_amount, $commitment_num, $confirm_id, $user_personal_code);
                            } else {
                                $result = 0;
                                $msg = "این کاربر با این کد تایید قبلا ثبت شده است.";
                                $data = array("user_id" => $user_id, "confirm_id" => $confirm_id);

                            }
                            if ($result == 1) {
                                $msg = "ضمن افزودن کاربر جدید ";
                                $query = "UPDATE user_tb SET ";
                                if (isset($_REQUEST['user_name'])) {
                                    $query .= "user_name='" . $user_name . "'";
                                }

                                if (isset($_REQUEST['user_family']) && isset($_REQUEST['user_name'])) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['user_family'])) {
                                    $query .= "user_family='" . $user_family . "'";
                                }

                                if (isset($_REQUEST['user_mobile']) && (isset($_REQUEST['user_family']) || isset($_REQUEST['user_name']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['user_mobile'])) {
                                    $query .= "user_mobile='" . $user_mobile . "'";
                                }

                                if (isset($_REQUEST['user_national_code']) && (isset($_REQUEST['user_family']) || isset($_REQUEST['user_name']) || isset($_REQUEST['user_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['user_national_code'])) {
                                    $query .= "user_national_code='" . $user_national_code . "'";
                                }
                                $query .= " where user_id=" . $user_id;
                                $result2 = $this->B_db->run_query_put($query);
                                if ($result2 == 1) {
                                    $msg .= " تغییرات مورد نظر انجام گردید.";
                                }

                                echo json_encode(array('result' => "ok"
                                , "user_id" => $user_id
                                , 'desc' => $msg), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                            } else {
                                echo json_encode(array('result' => "error"
                                , "data" => $data
                                , 'desc' => $msg . 'در افزودن پرسنل خطایی رخ داده است!'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }
                        } else {
                            echo json_encode(array('result' => 'error'
                            , "data" => ''
                            , 'desc' => 'خطایی رخ داده است!'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    } else {
                        echo json_encode(array('result' => 'error'
                        , "data" => ''
                        , 'desc' => 'توکن ارگان یافت نشد. مجددا تلاش نمایید!'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                } else {
                    echo json_encode(array('result' => $organtoken[0]
                    , "data" => $organtoken[1]
                    , 'desc' => $organtoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            } else if ($command == "modify_organ_user") {
                $user_id = $this->post('user_id');
                $commitment_amount = $this->post('commitment_amount');
                $commitment_num = $this->post('commitment_num');
                $confirm_id = $this->post('confirm_id');
                $organ_user_organ_id = $this->post('organ_user_organ_id');
                $result = $this->B_organ->modify_organ_user($organ_user_organ_id, $user_id, $commitment_amount, $commitment_num, $confirm_id);
                if ($result) {
                    echo json_encode(array('result' => "ok"
                    , "data" => ''
                    , 'desc' => 'اطلاعات پرسنل مورد نظر ویرایش گردید.'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                } else {
                    echo json_encode(array('result' => "error"
                    , "data" => $result[0]
                    , 'desc' => 'در ویرایش اطلاعات پرسنل مورد نظر خطایی رخ داده است!'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            } else if ($command == "del_organ_user") {
                $user_id = $this->post('user_id');
                $organ_user_organ_id = $this->post('contract_id');
                $result = $this->B_organ->del_organ_user($user_id, $organ_user_organ_id);
                if ($result) {
                    echo json_encode(array('result' => "ok"
                    , "data" => ''
                    , 'desc' => 'پرسنل مورد نظر با موفقیت حذف گردید'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                } else {
                    echo json_encode(array('result' => "error"
                    , "data" => ''
                    , 'desc' => 'در حذف پرسنل مورد نظر خطایی رخ داده است!'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }
        }
    }
}