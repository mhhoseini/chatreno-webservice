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
class Organmember extends REST_Controller
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
                    $result = $this->B_organ->get_organs_by_user($user_id);
                    $i = 0;
                    foreach ($result as $row) {
                        $sql = "Select organ_contract_fieldinsuranc_id From organ_contract_tb where organ_contract_organ_id= " . $row["organ_id"];
                        $result[$i]['fieldinsuranc'] = $this->db->query($sql)->result_array();
                        //**********************************
                        $result1 = $this->B_db->get_image($result[$i]['organ_logo']);
                        if (!empty($result1)) {
                            $image = $result1[0];
                            $result[$i]['organ_logo'] =  $image['image_tumb_url'];
                        }
                        //**********************************
                        $i++;
                    }
                    if (!empty($result)) {
                        echo json_encode(array('result' => "ok"
                        , "data" => $result
                        , 'desc' => ''), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    } else {
                        echo json_encode(array('result' => "error"
                        , 'desc' => ''), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                }
            }
        }
    }
}