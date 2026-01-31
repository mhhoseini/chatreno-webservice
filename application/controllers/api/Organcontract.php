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
class Organcontract extends REST_Controller
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
        if ($this->B_user->checkrequestip('organcontract', $command, get_client_ip(),50,50)) {
            $organtoken = checkorgantoken($organ_token_str);
            if ($organtoken[0] == 'ok') {
                if ($command == "get_contracts") {
                    $organ_id = $organtoken[1];
                    $result = $this->B_organ->get_organ_contracts($organ_id);
                    $i = 0;
                    foreach ($result as $row) {
                        if ($row['organ_contract_id'] != '') {
                            $res = $this->db->query("Select * from instalment_conditions_tb,instalment_mode_tb where instalment_mode_tb.instalment_mode_mode_id=instalment_conditions_tb.instalment_conditions_mode_id AND instalment_condition_contract_id=" . $row['organ_contract_id'])->result_array();
                            if (!empty($res)) {
                                foreach ($res as $row1)
                                    $result[$i]['organ_contract_instalment'][] = $row1;
                            }
                        }
                        $i++;
                    }
                    if (empty($result)) {
                        echo json_encode(array('result' => "error"
                        , "data" => ''
                        , 'desc' => 'قراردادی یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    } else {
                        echo json_encode(array('result' => "ok"
                        , "data" => $result
                        , 'desc' => 'لیست کامل قراردادها بازیابی گردید.'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                } else if ($command == "get_contract") {
                    $organ_contract_id = $this->post('organ_contract_id');
                    $result = $this->B_organ->get_organ_contract($organ_contract_id);
                    if (empty($result)) {
                        echo json_encode(array('result' => "error"
                        , "data" => ''
                        , 'desc' => 'قراردادی یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    } else {
                        echo json_encode(array('result' => "ok"
                        , "data" => $result[0]
                        , 'desc' => 'قرارداد مورد نظر بازیابی گردید.'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                } else if ($command == "get_instalment") {
                    $instalment_condition_contract_id = $this->post('instalment_condition_contract_id');
                    $result = $this->B_organ->get_organ_instalment($instalment_condition_contract_id);
                    if (empty($result)) {
                        echo json_encode(array('result' => "error"
                        , 'desc' => 'شرایط اقساط بیمه نامه بازیابی شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    } else {
                        echo json_encode(array('result' => "ok"
                        , "data" => $result[0]
                        , 'desc' => 'شرایط اقساطی یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                } else if ($command == "new_confirm") {
                    $organ_id = $organtoken[1];
                    //$organ_confirm_contract_id = $this->post('contract_id');
                    $sms_confirm_code = rand(10000, 99999);
                    $this->B_organ->add_confirm($sms_confirm_code);
                    $organ_confirm_id = $this->db->insert_id();
                    $result2 = $this->B_organ->get_organ_byid($organ_id);
                    if (!empty($result2))
                        $organ_managemobile = $result2[0]['organ_managemobile'];
                    else
                        $organ_managemobile = '';
                    if ($organ_managemobile != '') {
                        if ($organ_confirm_id) {
                            //send sms
                            send_sms($organ_managemobile, $sms_confirm_code);
                            echo json_encode(array('result' => "ok"
                            , "data" => array('organ_confirm_id' => $organ_confirm_id, 'sms_code' => $sms_confirm_code)
                            , 'desc' => 'پیامک ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        } else {
                            echo json_encode(array('result' => "error"
                            , 'desc' => 'خطایی رخ داده است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    } else {
                        echo json_encode(array('result' => "error"
                        , 'desc' => 'شماره مدیریت ارگان یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                } else if ($command == "check_confirm") {
                    $organ_confirm_id = $this->post('organ_confirm_id');
                    $sms_confirm_code = $this->post('sms_confirm_code');
                    $sms_confirm_date_start = $this->post('sms_confirm_date_start');
                    $sms_confirm_date_end = $this->post('sms_confirm_date_end');
                    if ($organtoken[0] == 'ok') {
                        $organ_id = $organtoken[1];
                        $confirmed = $this->B_organ->get_organ_confirm_by($organ_confirm_id, $sms_confirm_code);

                        if (!empty($confirmed)) {
                            $token = generateToken(20);
                            $this->B_organ->check_confirm($sms_confirm_code, $token,$sms_confirm_date_start,$sms_confirm_date_end);
                            $organ_confirm_id = $this->db->insert_id();
                            if ($this->db->affected_rows()) {

                                echo json_encode(array('result' => "ok",
                                    'data' => array("organ_confirm_id" => $organ_confirm_id, 'token' => $token)
                                , 'desc' => 'اصالت مدیر تایید شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            } else {
                                echo json_encode(array('result' => "error"
                                , 'desc' => 'مدیر مورد تایید نمی باشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }
                        } else {
                            echo json_encode(array('result' => "error"
                            , 'desc' => 'این قرارداد متعلق به مجموعه شما نمی باشد.'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    }
                } else if ($command == "get_confirms") {
                    $organ_confirm_token = $this->post('token');
                    if ($organtoken[0] == 'ok') {
                        $organ_id = $organtoken[1];
                        $result = $this->B_organ->get_organ_confirms($organ_id, $organ_confirm_token);
                        if (!empty($result)) {

                            echo json_encode(array('result' => "ok",
                                'data' => $result
                            , 'desc' => 'اطلاعات کانفریمهای ارگان بازیابی شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        } else {
                            echo json_encode(array('result' => "error"
                            , 'desc' => 'اطلاعات کانفریمها یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    }

                } else if ($command == "distinct_confirms") {
                    if ($organtoken[0] == 'ok') {
                        $organ_id = $organtoken[1];
                        $result = $this->B_organ->get_distinct_organ_confirms($organ_id);
                        if (!empty($result)) {

                            echo json_encode(array('result' => "ok",
                                'data' => $result
                            , 'desc' => 'اطلاعات کانفریمهای ارگان بازیابی شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        } else {
                            echo json_encode(array('result' => "error"
                            , 'desc' => 'اطلاعات کانفریمها یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    }

                } else if ($command == "get_organ_user") {
                    if ($organtoken[0] == 'ok') {
                        $organ_confirm_id = $this->post('organ_confirm_id');
                        $organ_id = $organtoken[1];
                        $result = $this->B_organ->get_user_organ($organ_id, $organ_confirm_id);
                        if (!empty($result)) {

                            echo json_encode(array('result' => "ok",
                                'data' => $result
                            , 'desc' => 'اطلاعات کانفریمهای ارگان بازیابی شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        } else {
                            echo json_encode(array('result' => "ok",
                                'data' => []
                            , 'desc' => 'اطلاعات کانفریمها یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
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
