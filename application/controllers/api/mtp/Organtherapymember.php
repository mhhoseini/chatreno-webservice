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
class Organtherapymember extends REST_Controller
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
        $this->load->model('B_organ');
        $this->load->model('B_user');
        $this->load->model('B_db');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('organmember', $command, get_client_ip(),50,50)) {
            $usertoken = checkusertoken($user_token_str);
            if ($command == "get_organ") {
                if ($usertoken[0] == 'ok') {
                    $user_id = $usertoken[1];

                        $sql = "SELECT * FROM organ_user_therapy_tb,user_therapy_bank_tb,user_gender_tb,organ_tb,user_tb,user_therapy_kind_tb,user_therapy_kindship_tb,
organ_therapycontract_tb,user_therapy_baseinsurer_tb
WHERE organ_user_therapy_bank_id=user_therapy_bank_id
AND organ_user_therapy_gender_id=user_gender_id
AND organ_user_therapy_organ_id=organ_id
AND organ_user_therapy_main_user_id=user_id
AND organ_user_therapy_kind_id=user_therapy_kind_id
AND user_therapy_kindship_id=organ_user_therapy_kinship_id
AND organ_user_therapy_organ_therapycontract_id=organ_therapycontract_id
AND organ_user_therapy_basebime_id=user_therapy_baseinsurer_id
AND user_id=" . $user_id;
                        $result = $this->db->query($sql)->result_array();

                    if (!empty($result)) {
                        $i = 0;
                        foreach ($result as $row) {

                          $result1 = $this->B_organ->get_organ_therapycontract_conditions_contract($row['organ_therapycontract_id']);
                            if($result1==null){
                                $result[$i]['conditions'] = '';
                           }else{
                               //***************************************************************
                                $i2 = 0;
                                foreach ($result1 as $row2) {

                                    $res = $this->B_organ->get_covarage_used($row['organ_user_therapy_id'],$row2['organ_therapycontract_conditions_covarage_id']);
                                    if($res==null){
                                        $result1[$i2]['sumprice'] = 0;
                                    }else{
                                        $result1[$i2]['sumprice'] = $res['sumprice'];
                                    }


                                    $i2++;
                                }
                               //***************************************************************
                                $result[$i]['conditions'] = $result1;

                            }


                            $i++;
                        }
                        header('Content-Type: application/json'); echo json_encode(array('result' => "ok"
                        , "data" => $result
                        , 'desc' => 'قراردادها یافت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    }else {
                        header('Content-Type: application/json'); echo json_encode(array('result' => "ok"
                        , "data" => []
                        , 'desc' => 'قراردادی یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }


                }
            }else
            if ($command == "get_organ_therapycontract_conditions") {
                if ($usertoken[0] == 'ok') {
                    $organ_therapycontract_conditions_contract_id = $this->post('organ_therapycontract_conditions_contract_id');
                    $organ_user_therapy_id = $this->post('organ_user_therapy_id');
                    $result = $this->B_organ->get_organ_therapycontract_conditions_contract($organ_therapycontract_conditions_contract_id);
                    if (!empty($result)) {
                        $i = 0;
                        foreach ($result as $row) {

                                $res = $this->B_organ->get_covarage_used($organ_user_therapy_id,$row['organ_therapycontract_conditions_covarage_id']);
                           if($res==null){
                               $result[$i]['sumprice'] = 0;
                           }else{
                               $result[$i]['sumprice'] = $res['sumprice'];
                           }


                            $i++;
                        }
                        header('Content-Type: application/json'); echo json_encode(array('result' => "ok"
                        , "data" => $result
                        , 'desc' => 'قراردادها یافت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    }else {
                        header('Content-Type: application/json'); echo json_encode(array('result' => "ok"
                        , "data" => []
                        , 'desc' => 'قراردادی یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }

                }
            }

        }
    }
}