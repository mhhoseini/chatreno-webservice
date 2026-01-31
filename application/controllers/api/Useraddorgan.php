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
class Useraddorgan extends REST_Controller {

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
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('useraddress', $command, get_client_ip(),50,50)) {
            if ($command == "add_user") {
                $usertoken = checkusertoken($user_token_str);


                if ($usertoken[0] == 'ok') {
                        $organ_user_organ_id = $this->post('organ_id');
                        $user_name = $this->post('user_name');
                        $user_family = $this->post('user_family');
                        $user_personal_code = $this->post('user_personal_code');
                        $user_national_code = $this->post('user_national_code');
                        $commitment_amount = $this->post('commitment_amount');
                        $commitment_num = $this->post('commitment_num');
                    $user_id=$usertoken[1];
                    $confirm_id=803;

                    $sql = "SELECT organ_user_confirm_id
                FROM organ_user_tb,organ_tb where organ_user_organ_id=organ_id AND organ_public=1 AND organ_user_user_id=$user_id AND organ_user_organ_id=$organ_user_organ_id";
                    $result=$this->B_db->run_query($sql);
                    $num=count($result[0]);
                    if ($num==0)
                    {

                                    $result = $this->B_organ->add_organ_user($organ_user_organ_id, $user_id, $commitment_amount, $commitment_num, $confirm_id, $user_personal_code);
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


                        if (isset($_REQUEST['user_national_code']) && (isset($_REQUEST['user_family']) || isset($_REQUEST['user_name']) )) {
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

                                    $msg = "این کاربر با این کد تایید قبلا ثبت شده است.";
                                    $data = array("user_id" => $user_id, "confirm_id" => $confirm_id);
                        echo json_encode(array('result' => "ok"
                        , "data" => $data
                        , 'desc' => $msg), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                }





                } else {
                    echo json_encode(array('result' => $usertoken[0]
                    , "data" => $usertoken[1]
                    , 'desc' => $usertoken[2]));

                }
            }
            else
                        if ($command == "getpublic_organ") {
                            $usertoken = checkusertoken($user_token_str);


                            if ($usertoken[0] == 'ok') {

                                $query1="select * from organ_tb where organ_public=1";
                                $result1=$this->B_db->run_query($query1);

                                echo json_encode(array('result' => "ok"
                                , "data" => $result1
                                , 'desc' => "لیست سازمان ها ارسال شد"));
                            } else {
                                echo json_encode(array('result' => $usertoken[0]
                                , "data" => $usertoken[1]
                                , 'desc' => $usertoken[2]));

                            }
                        }else
                            if ($command == "getetma_organ") {
                                $usertoken = checkusertoken($user_token_str);


                                if ($usertoken[0] == 'ok') {

                                    $query1="select * from organ_tb where organ_public=2";
                                    $result1=$this->B_db->run_query($query1);

                                    echo json_encode(array('result' => "ok"
                                    , "data" => $result1
                                    , 'desc' => "لیست سازمان ها ارسال شد"));
                                } else {
                                    echo json_encode(array('result' => $usertoken[0]
                                    , "data" => $usertoken[1]
                                    , 'desc' => $usertoken[2]));

                                }
                            }else
                            if ($command == "getuser") {
                            $user_token = checkusertoken($user_token_str);

            if ($user_token[0] == 'ok') {

                $query1="select user_name,user_family,user_national_code from user_tb where user_id=".$user_token[1];
                $result1=$this->B_db->run_query($query1)[0];

                $query2="SELECT organ_id,organ_user_personal_code FROM organ_user_tb,organ_tb WHERE  organ_user_organ_id=organ_id AND organ_public=1 And organ_user_user_id=".$user_token[1];
              $result2=$this->B_db->run_query($query2)[0];
              if($result2){
                  $result=array_merge($result1,$result2);
              }else{
                  $result=$result1;

              }
                echo json_encode(array('result' => "ok"
                , "data" =>$result
                , 'desc' => "لیست سازمان ها ارسال شد"));

            }else {
                echo json_encode(array('result' => $user_token[0]
                , "data" => $user_token[1]
                , 'desc' => $user_token[2]));

            }
            }


        }
    }
}
