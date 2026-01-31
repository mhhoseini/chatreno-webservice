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
class Useretma extends REST_Controller {

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
        if ($this->B_user->checkrequestip('userwallet', $command, get_client_ip(),50,50)) {
            if ($command == "get_wallet") {

                $usertoken = checkusertoken($user_token_str);
                if ($usertoken[0] == 'ok') {

                    $output= gettokenetma();

                    echo json_encode(array('result' => "ok"
                    , "data" => $output
                    , 'desc' => 'کیف پول با موفقیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } else {
                    echo json_encode(array('result' => $usertoken[0]
                    , "data" => $usertoken[1]
                    , 'desc' => $usertoken[2]));

                }
            } else if ($command == "get_sumwallet") {

                $usertoken = checkusertoken($user_token_str);
                if ($usertoken[0] == 'ok') {
                    $user_id = $usertoken[1];
                    $result = $this->B_user->user_wallet($user_id);
                    $sum_user_wallet = 0;
                    foreach ($result as $row) {
                        if ($row['user_wallet_mode'] == 'add') {
                            $sum_user_wallet += $row['user_wallet_amount'];
                        } else {
                            $sum_user_wallet -= $row['user_wallet_amount'];
                        }
                    }
                    echo json_encode(array('result' => "ok"
                    , "data" => array('sum_user_wallet' => $sum_user_wallet)
                    , 'desc' => 'جمع کیف پول با موفقیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } else {
                    echo json_encode(array('result' => $usertoken[0]
                    , "data" => $usertoken[1]
                    , 'desc' => $usertoken[2]));
                }
            } else if ($command == "refund_user") {
                $amount = $this->post('amount');
                $useracbank_id = $this->post('useracbank_id');
                $usertoken = checkusertoken($user_token_str);
                if ($usertoken[0] == 'ok') {
                    if($amount>=300000) {
                        $user_id = $usertoken[1];
                        $result = $this->B_user->user_wallet($user_id);
                        $output = array();
                        $sum_user_wallet = 0;
                        foreach ($result as $row) {
                            $record = array();
                            if ($row['user_wallet_mode'] == 'add') {
                                $sum_user_wallet += $row['user_wallet_amount'];
                            } else {
                                $sum_user_wallet -= $row['user_wallet_amount'];
                            }
                        }
                        if ($sum_user_wallet >= $amount) {
                            $result1 = $this->B_user->refund_user($user_id);
                            $output1 = array();
                            $refund_user = 0;
                            foreach ($result1 as $row1) {
                                $record = array();
                                if ($row1['refund_user_pey'] == '0') {
                                    $refund_user += $row1['refund_user_amount'];
                                }
                            }
                            if ($amount <= $sum_user_wallet - $refund_user) {
                                $this->B_user->add_refund_user($user_id, $amount, $useracbank_id);
                                echo json_encode(array('result' => "ok"
                                , "data" => array('sum_user_wallet' => $sum_user_wallet)
                                , 'desc' => 'درخواست بازگشت پول با موفقیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            } else {
                                echo json_encode(array('result' => "error"
                                , "data" => array('sum_user_wallet' => $sum_user_wallet)
                                , 'desc' => 'شما قبلا درخواست باگشت وجه ' . number_format($refund_user, 0) . ' ریال  کرده اید و میتوانید حداکثر درخواست ' . number_format($sum_user_wallet - $refund_user) . ' ریال دیگر نمایید'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }
                        } else {

                            echo json_encode(array('result' => "error"
                            , "data" => array('sum_user_wallet' => $sum_user_wallet)
                            , 'desc' => 'مبلغ درخواست شده از موجودی کیف پول بیشتر است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    }else{
                        echo json_encode(array('result' => "error"
                        , "data" => ""
                        , 'desc' => 'حداقل مبلغ درخواستی 300.000 ریال است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    }
                } else {
                    echo json_encode(array('result' => $usertoken[0]
                    , "data" => $usertoken[1]
                    , 'desc' => $usertoken[2]));
                }
            } else if ($command == "get_refunduser") {
                $usertoken = checkusertoken($user_token_str);
                if ($usertoken[0] == 'ok') {
                    $user_id = $usertoken[1];
                    $query = "select * from refund_user_tb,useracbank_tb  where refund_user_useracbank_id=useracbank_id AND refund_user_user_id=" . $user_id;
                    $result = $this->B_db->run_query($query);
                    $output = array();
                    foreach ($result as $row) {
                        $record = array();
                        $record['useracbank_bankname'] = $row['useracbank_bankname'];
                        $record['refund_user_amount'] = $row['refund_user_amount'];
                        $record['refund_user_code'] = $row['refund_user_code'];
                        $record['refund_user_desc'] = $row['refund_user_desc'];
                        $record['refund_user_pey'] = $row['refund_user_pey'];
                        $record['refund_user_date'] = $row['refund_user_date'];
                        $record['refund_user_datepeyed'] = $row['refund_user_datepeyed'];
                        $output[] = $record;
                    }
                    echo json_encode(array('result' => "ok"
                    , "data" => $output
                    , 'desc' => 'درخواست بازگشت وجه با موفقیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } else {
                    echo json_encode(array('result' => $usertoken[0]
                    , "data" => $usertoken[1]
                    , 'desc' => $usertoken[2]));

                }
            }
        }
    }
}
