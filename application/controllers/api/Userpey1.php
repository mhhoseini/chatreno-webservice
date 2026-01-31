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
class Userpey1 extends REST_Controller {

    protected $soapClient;
    protected $wsdl;
    public $config;
    public $callBackURL;
    public $url;
    public $bank_messages = array(
        "11"=>"شماره کارت نامعتبر است",
        "12"=>"موجودی کافی نیست",
        "13"=>"رمز نادرست است",
        "14"=>"تعداد دفعات وارد کردن رمز بيش از حد مجاز است",
        "15"=>"کارت نامعتبر است",
        "16"=>"دفعات برداشت وجه بيش از حد مجاز است",
        "17"=>"کاربر از انجام تراکنش منصرف شده است",
        "18"=>"تاريخ انقضای کارت گذشته است",
        "19"=>"مبلغ برداشت وجه بيش از حد مkoجاز است",
        "32"=>"فرمت اطلاعات وارد شده صحیح نمی باشد",
        "41"=>"شماره درخواست تکراری است",
        //ادامه دارد .................
        );

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
        $command = $this->post("command");
        $this->load->helper('my_helper');
        $this->load->model('B_user');
        $this->load->model('B_db');
        if ($this->B_user->checkrequestip('userpey', $command, get_client_ip(),50,50)) {
            if ($command == "check_discount_code") {
                $usertoken = checkusertoken($user_token_str);
                if ($usertoken[0] == 'ok') {
                    $request_id = $this->post('request_id');
                    $discount_code = $this->post('discount_code');
                    $user_id = $usertoken[1];
                    $query1 = "select * from discount_code_tb,fieldinsurance_tb,request_tb,company_tb where discount_code_company_id LIKE concat('%',company_name,'%')    AND request_company_id=company_id AND discount_code_fieldinsurance_id LIKE concat('%',fieldinsurance,'%') AND fieldinsurance=request_fieldinsurance
                 AND request_id=" . $request_id . " AND discount_code='" . $discount_code . "'
                 AND (discount_code_date_start='' OR discount_code_date_start > now()) AND  (discount_code_date_end='' OR discount_code_date_end< now())
                 AND discount_code_number>(SELECT COUNT(*) FROM discount_code_use_tb WHERE discount_code_use_dscntcode_id=discount_code_tb.discount_code_id)
                 AND discount_code_deactive=0";
                    $result1 = $this->B_db->run_query($query1);

                    if (!empty($result1)) {
                        $discount_code = $result1[0];
                        //***************************************************************************************************************************
                        $discount_code_amount=$discount_code['discount_code_amount'];
                        $discount_code_id=$discount_code['discount_code_id'];
                        //***************************************************************************************************************************

                        $request_id = $this->post('request_id');
                        $query = "SELECT jsonpricing_text FROM request_tb ,jsonpricing_tb where request_jsonpricing_id =jsonpricing_id AND  request_id =" . $request_id . "";
                        $result = $this->B_db->run_query($query)[0];
                        $jsonpricing=json_decode($result['jsonpricing_text'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);;
                       $request_price= $jsonpricing['price'];

                            //***************************************************************************************************************************

                            $query2 = "DELETE FROM user_pey_temp_tb WHERE user_pey_temp_mode='discount_code' AND user_pey_temp_request_id = $request_id";
                        $this->B_db->run_query_put($query2);

                        $sql1 = "select SUM(user_pey_temp_amount) from user_pey_temp_tb where user_pey_temp_request_id = " . $request_id;
                        $result = $this->B_db->run_query($sql1);
                        $user_pey = $result[0];
                        $sum_user_pey_temp_amount= $user_pey['user_pey_temp_amount'];

                        if(!$sum_user_pey_temp_amount){$sum_user_pey_temp_amount=0;}
                        if(($request_price-$sum_user_pey_temp_amount)>=$discount_code_amount) {

                            $user_pey_temp_desc = 'پرداخت شده توسط کد تخفیف' . $discount_code['discount_code_desc'];
                            $query1 = "INSERT INTO user_pey_temp_tb( user_pey_temp_request_id, user_pey_temp_amount, user_pey_temp_mode, user_pey_temp_code, user_pey_temp_desc,user_pey_temp_timestamp) VALUES
                                      (  $request_id           , $discount_code_amount, 'discount_code'    , $discount_code_id,'$user_pey_temp_desc',now())      ";
                            $result1 = $this->B_db->run_query_put($query1);

                            //***************************************************************************************************************************
                            echo json_encode(array('result' => "ok"
                            , "data" => array('discount_code_id' => $discount_code_id, 'discount_code_amount' => $discount_code_amount, 'discount_code_desc' => $discount_code['discount_code_desc'])
                            , 'desc' => 'کد تخفیف  با موفقیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }else{
                            $user_pey_temp_desc = 'پرداخت شده توسط کد تخفیف' . $discount_code['discount_code_desc'];
                            $discount_code_amount1=$request_price-$sum_user_pey_temp_amount;
                            $query1 = "INSERT INTO user_pey_temp_tb( user_pey_temp_request_id, user_pey_temp_amount, user_pey_temp_mode, user_pey_temp_code, user_pey_temp_desc,user_pey_temp_timestamp) VALUES
                                      (  $request_id           , $discount_code_amount1, 'discount_code'    , $discount_code_id,'$user_pey_temp_desc',now())      ";
                            $result1 = $this->B_db->run_query_put($query1);

                            //***************************************************************************************************************************
                            echo json_encode(array('result' => "ok"
                            , "data" => array('discount_code_id' => $discount_code_id, 'discount_code_amount' => $discount_code_amount1, 'discount_code_desc' => $discount_code['discount_code_desc'])
                            , 'desc' => 'مبلغ کد تخفیف از مبلغ پرداختی بیشتر است در صورت نیاز در درخواست دیگری استفاده کنید.مبلغ کد تخفیف:'.$discount_code_amount), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    } else {
                        echo json_encode(array('result' => "error"
                        , "data" => ""
                        , 'desc' => 'کد تخفیف موجود نیست'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                } else {
                    echo json_encode(array('result' => $usertoken[0]
                    , "data" => $usertoken[1]
                    , 'desc' => $usertoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                }
            }
           else
               if ($command == "delete_discount_code") {
               $usertoken = checkusertoken($user_token_str);
               if ($usertoken[0] == 'ok') {
                   $request_id = $this->post('request_id');


                       $query2 = "DELETE FROM user_pey_temp_tb WHERE user_pey_temp_mode='discount_code' AND user_pey_temp_request_id = $request_id";
                   $result1=$this->B_db->run_query_put($query2);


                   if ($result1) {

                       //***************************************************************************************************************************
                       echo json_encode(array('result' => "ok"
                       , "data" =>""
                       , 'desc' => 'کد تخفیف  با موفقیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                   } else {
                       echo json_encode(array('result' => "error"
                       , "data" => ""
                       , 'desc' => 'کد تخفیف موجود نیست'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                   }
               } else {
                   echo json_encode(array('result' => $usertoken[0]
                   , "data" => $usertoken[1]
                   , 'desc' => $usertoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

               }
           }


            else if ($command == "get_pey") {
                $pey_id = $this->post('pey_id');
                $sql1 = "select * from pey_tb where pey_id = " . $pey_id;
                $result = $this->B_db->run_query($sql1);
                if (empty($result))
                    echo json_encode(array('result' => 'error', 'data' => 'not found!'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                else
                    echo json_encode(array('result' => 'ok', 'data' => $result[0]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else if ($command == "get_deficitpey") {
                $pey_id = $this->post('pey_id');
                $sql1 = "select * from pey_tb where pey_id = " . $pey_id;
                $result = $this->B_db->run_query($sql1);
                if (empty($result))
                    echo json_encode(array('result' => 'error', 'data' => 'not found!'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                else
                    echo json_encode(array('result' => 'ok', 'data' => $result[0]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else
                if ($command == "check_pey") {
                    $usertoken = checkusertoken($user_token_str);
                    if ($usertoken[0] == 'ok') {
                        $request_id = $this->post('request_id');
                        $instalment = $this->post('instalment');
                        $query = "SELECT jsonpricing_text FROM request_tb ,jsonpricing_tb where request_jsonpricing_id =jsonpricing_id AND  request_id =" . $request_id . "";
                        $result = $this->B_db->run_query($query)[0];
                        $jsonpricing=json_decode($result['jsonpricing_text'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);;

                        $query2 = "DELETE FROM user_pey_temp_tb WHERE user_pey_temp_request_id = $request_id";
                        $this->B_db->run_query_put($query2);

                        $price=$jsonpricing['price'];
                        $managdiscount_amount=0;
                        $pricechash=$jsonpricing['price'];
                        if($jsonpricing['managdiscount_amount'])
                        {
                            if($jsonpricing['managdiscount_amount']>0)
                            {
                                $managdiscount_amount=$jsonpricing['managdiscount_amount'];
                                $managdiscount_id=$jsonpricing['managdiscount_id'];
                                $query = "select * from managdiscount_tb where managdiscount_id=" . $managdiscount_id . "";
                                $result = $this->B_db->run_query($query);

                                $managdiscount = $result[0];

                                $user_pey_temp_desc = 'پرداخت شده توسط تخفیف مدیریتی' . $managdiscount['managdiscount_desc'];
                                $query1 = "INSERT INTO user_pey_temp_tb( user_pey_temp_request_id, user_pey_temp_amount, user_pey_temp_mode, user_pey_temp_code, user_pey_temp_desc,user_pey_temp_timestamp) VALUES
                                      (  $request_id           , $managdiscount_amount, 'managdiscount'    , $managdiscount_id,'$user_pey_temp_desc',now())      ";
                                $result1 = $this->B_db->run_query_put($query1);

                            }
                        }
                        $instalment_conditions=[];
                        if($jsonpricing["instalment_conditions"]&&($instalment=='1'||$instalment==1))
                        {
                            $instalment_conditions=$jsonpricing["instalment_conditions"];
                            foreach($instalment_conditions as $row) {
                                if($row['instalment_conditions_mode_id']!='0'||$row['instalment_conditions_mode_id']!=0) {
                                    $instalment_conditions_id = $row['instalment_conditions_id'];
                                    $instalment_conditions_amount = $row['instalment_conditions_amount'];
                                    $user_pey_temp_image_code = '';
                                    //****************************************************************************************************************
                                    $user_id = $usertoken[1];
                                    $query = "select * from instalment_conditions_tb where instalment_conditions_id=" . $instalment_conditions_id . "";

                                    $result = $this->B_db->run_query($query);
                                    $instalment_condition = $result[0];

                                    $user_pey_temp_desc = 'پرداخت شده توسط ' . $instalment_condition['instalment_conditions_desc'];
                                    $query1 = "INSERT INTO user_pey_temp_tb( user_pey_temp_request_id, user_pey_temp_amount, user_pey_temp_mode, user_pey_temp_code, user_pey_temp_desc,user_pey_temp_image_code,user_pey_temp_timestamp) VALUES
                                      ( $request_id , $instalment_conditions_amount, 'instalment', $instalment_conditions_id,'$user_pey_temp_desc','$user_pey_temp_image_code',now())      ";
                                    $result1 = $this->B_db->run_query_put($query1);
                                }else{
                                    $pricechash=$row['instalment_conditions_amount'];
                                }
                            }

                        }
                      echo json_encode(array('result' => "ok"
                      , "data" => array(
                              'price' => $price,
                              'pricechash' => $pricechash,
                              'managdiscount_amount' => $managdiscount_amount,
                              'instalment_conditions' => $instalment_conditions
                              )
                      , 'desc' => 'شما قبلا درخواست باگشت وجه ' ));
                        //***************************************************************************************************************
                    } else {
                        echo json_encode(array('result' => $usertoken[0]
                        , "data" => $usertoken[1]
                        , 'desc' => $usertoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    }

                }else
                if ($command == "peyby_managdiscount") {

                $usertoken = checkusertoken($user_token_str);
                if ($usertoken[0] == 'ok') {
                    $request_id = $this->post('request_id');
                    $query = "SELECT jsonpricing_text FROM request_tb ,jsonpricing_tb where request_jsonpricing_id =jsonpricing_id AND  request_id =" . $request_id . "";
                    $result = $this->B_db->run_query($query)[0];
                    $jsonpricing=json_decode($result['jsonpricing_text'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    if($jsonpricing['managdiscount_amount'])
                    {
                        if($jsonpricing['managdiscount_amount']>0)
                        {
                            $managdiscount_amount=$jsonpricing['managdiscount_amount'];
                            $managdiscount_id=$jsonpricing['managdiscount_id'];
                            $query = "select * from managdiscount_tb where managdiscount_id=" . $managdiscount_id . "";
                            $result = $this->B_db->run_query($query);

                            $query2 = "DELETE FROM user_pey_temp_tb WHERE user_pey_temp_mode='managdiscount' AND user_pey_temp_request_id = $request_id";
                            $this->B_db->run_query_put($query2);


                            $managdiscount = $result[0];

                            $user_pey_temp_desc = 'پرداخت شده توسط تخفیف مدیریتی' . $managdiscount['managdiscount_desc'];
                            $query1 = "INSERT INTO user_pey_temp_tb( user_pey_temp_request_id, user_pey_temp_amount, user_pey_temp_mode, user_pey_temp_code, user_pey_temp_desc,user_pey_temp_timestamp) VALUES
                                      (  $request_id           , $managdiscount_amount, 'managdiscount'    , $managdiscount_id,'$user_pey_temp_desc',now())      ";
                            $result1 = $this->B_db->run_query_put($query1);
                            if ($result1) {
                                echo json_encode(array('result' => "ok"
                                , "data" => ""
                                , 'desc' => 'پرداخت با تخفیف مدیریتی  با موفقیت ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            } else {
                                echo json_encode(array('result' => "error"
                                , "data" => ""
                                , 'desc' => 'پرداخت با تخفیف مدیریتی  با موفقیت ثبت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }
                        }else{
                            echo json_encode(array('result' => "error"
                            , "data" => ""
                            , 'desc' => '  تخفیف مدیریتی موجود نیست'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    }else{
                        echo json_encode(array('result' => "error"
                        , "data" => ""
                        , 'desc' => '  تخفیف مدیریتی موجود نیست'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                    //***************************************************************************************************************
                } else {
                    echo json_encode(array('result' => $usertoken[0]
                    , "data" => $usertoken[1]
                    , 'desc' => $usertoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                }
            } else if ($command == "peyby_user_wallet") {

                $usertoken = checkusertoken($user_token_str);
                if ($usertoken[0] == 'ok') {
                    $request_id = $this->post('request_id');
                    $user_wallet_amount = $this->post('user_wallet_amount', '0');
                    $user_id = $usertoken[1];
                    $query = "select * from user_wallet_tb where  user_wallet_user_id=" . $user_id;
                    $result = $this->B_db->run_query($query);
                    $sum_user_wallet = 0;
                    foreach ($result as $row) {
                        $record = array();
                        if ($row['user_wallet_mode'] == 'add') {
                            $sum_user_wallet += $row['user_wallet_amount'];
                        } else {
                            $sum_user_wallet -= $row['user_wallet_amount'];
                        }
                    }
                    if (intval($sum_user_wallet) >= intval($user_wallet_amount)) {
                        $result6 = $this->B_user->refund_user($user_id);
                        $output1 = array();
                        $refund_user = 0;
                        foreach ($result6 as $row1) {
                            $record = array();
                            if($row1['refund_user_pey']=='0') {
                                $refund_user += $row1['refund_user_amount'];
                            }
                        }
                        if ($user_wallet_amount <= $sum_user_wallet - $refund_user) {

                            $query2 = "DELETE FROM user_pey_temp_tb WHERE user_pey_temp_mode='user_wallet' AND user_pey_temp_request_id = $request_id";
                            $this->B_db->run_query_put($query2);
                            $user_pey_temp_desc = 'پرداخت شده توسط  کیف پول کاربر';
                            $query1 = "INSERT INTO user_pey_temp_tb( user_pey_temp_request_id, user_pey_temp_amount, user_pey_temp_mode, user_pey_temp_code, user_pey_temp_desc,user_pey_temp_timestamp) VALUES
                                      (  $request_id           , $user_wallet_amount, 'user_wallet'    ,  $user_id   ,'$user_pey_temp_desc',now())      ";
                            $result1 = $this->B_db->run_query_put($query1);
                            if ($result1) {
                                echo json_encode(array('result' => "ok"
                                , "data" => $user_wallet_amount
                                , 'desc' => 'پرداخت با کیف پول با موفقیت ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            } else {
                                echo json_encode(array('result' => "error"
                                , "data" => ''
                                , 'desc' => 'پرداخت با کیف پول  با موفقیت ثبت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }
                            //****************************************************************************************************
                            //****************************************************************************************************
                        } else {
                            echo json_encode(array('result' => "error"
                            , "data" => array('sum_user_wallet' => $sum_user_wallet)
                            , 'desc' => 'شما قبلا درخواست باگشت وجه ' . number_format($refund_user, 0) . ' ریال  کرده اید و میتوانید حداکثر درخواست ' . number_format($sum_user_wallet - $refund_user) . ' ریال دیگر نمایید'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    } else {
                        echo json_encode(array('result' => "error"
                        , "data" => ""
                        , 'desc' => 'مبلغ درخواست شده از جمع کیف پول بیشتر است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                } else {
                    echo json_encode(array('result' => $usertoken[0]
                    , "data" => $usertoken[1]
                    , 'desc' => $usertoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                }
            } else if ($command == "peyby_instalmen") {

                $usertoken = checkusertoken($user_token_str);
                if ($usertoken[0] == 'ok') {
                    $request_id = $this->post('request_id');
                    $instalment_conditions_id = $this->post('instalment_conditions_id');
                    $user_pey_temp_image_code = $this->post('image_code');
                    //****************************************************************************************************************

                    $query1 = "UPDATE user_pey_temp_tb
SET   user_pey_temp_image_code='$user_pey_temp_image_code'
WHERE user_pey_temp_request_id=$request_id AND user_pey_temp_code =$instalment_conditions_id ;";
                    $result1 = $this->B_db->run_query_put($query1);
                    if ($result1) {
                        echo json_encode(array('result' => "ok"
                        , "data" => ""
                        , 'desc' => 'پرداخت با چک  با موفقیت ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    } else {
                        echo json_encode(array('result' => "error"
                        , "data" => ""
                        , 'desc' => 'پرداخت با چک  با موفقیت ثبت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                    //***************************************************************************************************************
                } else {
                    echo json_encode(array('result' => $usertoken[0]
                    , "data" => $usertoken[1]
                    , 'desc' => $usertoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                }

                }else if ($command == "check_cash") {
                    $request_id = $this->post('request_id');
                    $query = "SELECT jsonpricing_text FROM request_tb ,jsonpricing_tb where request_jsonpricing_id =jsonpricing_id AND  request_id =" . $request_id . "";
                    $result = $this->B_db->run_query($query)[0];
                    $jsonpricing=json_decode($result['jsonpricing_text'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    $sql1 = "select SUM(user_pey_temp_amount) AS sumpey_temp from user_pey_temp_tb where user_pey_temp_request_id = " . $request_id;
                    $result = $this->B_db->run_query($sql1);
                    $user_pey = $result[0];
                    $sum_user_pey_temp_amount= $user_pey['sumpey_temp'];
                    if(!$sum_user_pey_temp_amount){$sum_user_pey_temp_amount=0;}

                    echo $sum_user_pey_temp_amount.' --- '.$jsonpricing['price'].' --- '.($jsonpricing['price']-$sum_user_pey_temp_amount);
                }
                else if ($command == "peyby_cash") {
                $usertoken = checkusertoken($user_token_str);
                if ($usertoken[0] == 'ok') {
                    $request_id = $this->post('request_id');
//************************************************************************
                    $query = "SELECT jsonpricing_text FROM request_tb ,jsonpricing_tb where request_jsonpricing_id =jsonpricing_id AND  request_id =" . $request_id . "";
                    $result = $this->B_db->run_query($query)[0];
                    $jsonpricing=json_decode($result['jsonpricing_text'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    $sql1 = "select SUM(user_pey_temp_amount) AS sumpey_temp from user_pey_temp_tb where user_pey_temp_request_id = " . $request_id;
                    $result = $this->B_db->run_query($sql1);
                    $user_pey = $result[0];
                    $sum_user_pey_temp_amount= $user_pey['sumpey_temp'];
                    if(!$sum_user_pey_temp_amount){$sum_user_pey_temp_amount=0;}

                    //************************************************************************


                    $amount =$jsonpricing['price']-$sum_user_pey_temp_amount;
                    if($amount>0) {
                        //$data = array('request_id' => $request_id, 'amount' => $amount, 'user_token_str' => $user_token_str);
                        $this->callBackURL = $this->config->item('callBackURL');
                        $res = $this->add_pey_tb($request_id, $amount, 'pey');
                        if ($res == 1)
                            $orderId = $this->db->insert_id();
                        else {
                            echo "OrderId doesnot created...";
                            die;
                        }

                        $BankResponse = $this->payment($amount, $orderId, $additionalData = '', $payerId = 0, $payment_type = "main");
                        //Success Payment
                        if ($BankResponse['res_code'] == 0) {
                            $RefId = $BankResponse["RefId"];
                            $url_operationServer = $this->config->item('operationServer');
                            $BankResponse['url'] = $url_operationServer;
                            $BankResponse['callBackURL'] = $this->callBackURL;
                            $this->update_pey_tb($orderId, $RefId);
                            //move to bank payment page
                            echo json_encode($BankResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            //Failed Payment
                        } else {
                            //Failed Codes Messages
                            echo json_encode(array('result' => "error"
                            , "data" => $BankResponse['res_code']
                            , 'desc' => $this->bank_messages[$BankResponse['res_code']]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    }
                } else {
                    echo json_encode(array('result' => $usertoken[0]
                    , "data" => $usertoken[1]
                    , 'desc' => $usertoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }

            } else
                if ($command == "get_json") {

                $usertoken = checkusertoken($user_token_str);
                if ($usertoken[0] == 'ok') {

                    $request_id = $this->post('request_id');

                    //************************************************************************;****************************************
                    $user_id = $usertoken[1];
                    $query1 = "SELECT * FROM request_tb,jsonpricing_tb WHERE request_user_id=$user_id AND request_jsonpricing_id=jsonpricing_id AND request_id = $request_id";

                    $result1 = $this->B_db->run_query($query1);
                    $request = $result1[0];

                    if ($result1[0]) {
                        echo json_encode(array('result' => "ok"
                        , "data" => $request['jsonpricing_text']
                        , 'desc' => 'قیمت ها و تخفیف های درخوایت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    } else {
                        echo json_encode(array('result' => "error"
                        , "data" => ""
                        , 'desc' => 'قیمت ها و تخفیف های درخوایت ارسال نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                    //**************************************************************************************************************
                } else {
                    echo json_encode(array('result' => $usertoken[0]
                    , "data" => $usertoken[1]
                    , 'desc' => $usertoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                }
            }
            else if ($command == "delete_temp_pey") {

                $usertoken = checkusertoken($user_token_str);
                if ($usertoken[0] == 'ok') {

                    $request_id = $this->post('request_id');

                    //************************************************************************;****************************************
                    $user_id = $usertoken[1];

                    $query1 = "DELETE FROM user_pey_temp_tb WHERE  user_pey_temp_request_id = $request_id";
                    $result1 = $this->B_db->run_query_put($query1);

                    if ($result1) {
                        echo json_encode(array('result' => "ok"
                        , "data" => ""
                        , 'desc' => 'پرداخت های ذخیره شده حذف شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    } else {
                        echo json_encode(array('result' => "error"
                        , "data" => ""
                        , 'desc' => 'پرداخت های ذخیره شده حذف نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                    //**************************************************************************************************************
                } else {
                    echo json_encode(array('result' => $usertoken[0]
                    , "data" => $usertoken[1]
                    , 'desc' => $usertoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                }

            } else
                if ($command == "deficitpey_cash") {
                $usertoken = checkusertoken($user_token_str);

                if ($usertoken[0] == 'ok') {
                    $deficit_pey_id = $this->post('deficit_pey_id');
                    $user_id = $usertoken[1];
                    $query3 = "select * from request_tb,deficit_pey_tb where request_id=deficit_pey_request_id AND deficit_pey_id=" . $deficit_pey_id . "";
                    $result3 = $this->B_db->run_query($query3);
                    if (!empty($result3)) {
                        $request = $result3[0];
                        $request_id = $request['request_id'];
                        $deficit_pey_amount = $request['deficit_pey_amount'];
                        if ($request['deficit_pey_user_pey_id'] == null) {
                            if (isset($_REQUEST['user_wallet_amount']) && $_REQUEST['user_wallet_amount'] > 0) {
                                $user_wallet_amount = $this->post('user_wallet_amount');

                                $result5 = $this->B_user->user_wallet($user_id);
                                $output = array();
                                $sum_user_wallet = 0;
                                foreach ($result5 as $row) {
                                    $record = array();
                                    if ($row['user_wallet_mode'] == 'add') {
                                        $sum_user_wallet += $row['user_wallet_amount'];
                                    } else {
                                        $sum_user_wallet -= $row['user_wallet_amount'];
                                    }
                                }
                                if ($sum_user_wallet >= $user_wallet_amount) {
                                    $result6 = $this->B_user->refund_user($user_id);
                                    $output1 = array();
                                    $refund_user = 0;
                                    foreach ($result6 as $row1) {
                                        $record = array();
                                        if($row1['refund_user_pey']=='0') {
                                            $refund_user += $row1['refund_user_amount'];
                                        }
                                    }
                                    if ($user_wallet_amount <= $sum_user_wallet - $refund_user) {
                                        //***************************************************************************
                                        if ($deficit_pey_amount > $user_wallet_amount) {
                                            $query2 = "DELETE FROM user_pey_temp_tb WHERE user_pey_temp_mode='user_wallet' AND user_pey_temp_request_id = $request_id";
                                            $this->B_db->run_query_put($query2);

                                            $user_pey_temp_desc = 'پرداخت شده توسط  کیف پول کاربر';
                                            $query1 = "INSERT INTO user_pey_temp_tb( user_pey_temp_request_id, user_pey_temp_amount, user_pey_temp_mode, user_pey_temp_code, user_pey_temp_desc,user_pey_temp_timestamp) VALUES
                                        ($request_id , $user_wallet_amount, 'user_wallet'    ,  $user_id   ,'$user_pey_temp_desc',now())      ";
                                            $result1 = $this->B_db->run_query_put($query1);
                                            //???????????????????????????????????????????????????????????????????????????????????????
                                            $amount = $this->post('amount');

                                            //complex payment of wallet and cash for kasri varizi
                                            $result = $this->deficit_payment($request_id, $amount, $deficit_pey_id);

                                            if ($result === FALSE) { /* Handle error */
                                            }
                                            //???????????????????????????????????????????????????????????????????????????????????????
                                        } else {
                                            $user_pey_temp_desc = 'پرداخت شده توسط  کیف پول کاربر';
                                            $query1 = "INSERT INTO user_pey_tb( user_pey_request_id, user_pey_amount, user_pey_mode, user_pey_code, user_pey_desc,user_pey_timestamp) VALUES
                                        ($request_id , $user_wallet_amount, 'user_wallet'    ,  $user_id   ,'$user_pey_temp_desc',now())      ";
                                            $result1 = $this->B_db->run_query_put($query1);
                                            $user_pey_id = $this->db->insert_id();

                                            $user_wallet_detail = 'پرداخت سفارش کد' . $request_id . ' توسط  کیف پول ';
                                            $query2 = "INSERT INTO user_wallet_tb( user_wallet_user_id, user_wallet_amount, user_wallet_mode, user_wallet_timestamp, user_wallet_detail, user_wallet_code) VALUES
                                        ($user_id , $user_wallet_amount   ,    'get'      ,now()               ,'$user_wallet_detail',$request_id)      ";
                                            $result2 = $this->B_db->run_query_put($query2);

                                            $query3 = "UPDATE deficit_pey_tb SET deficit_pey_user_pey_id=$user_pey_id , deficit_pey_user_pey_date=now() WHERE deficit_pey_request_id=$request_id   ";
                                            $result3 = $this->B_db->run_query_put($query3);
                                            $query4 = "UPDATE request_tb SET request_last_state_id=2  WHERE  request_id = $request_id";
                                            $result4 = $this->B_db->run_query_put($query4);

                                            $query5 = "INSERT INTO state_request_tb(staterequest_request_id, staterequest_state_id, staterequest_timestamp, staterequest_desc         ) VALUES
                                    (" . $request_id . "        ,      2               ,  now()                 ,'پرداخت توسط کابر انجام شد') ";
                                            $result5 = $this->B_db->run_query_put($query5);
                                            $staterequest_id = $result5[0];

                                            echo json_encode(array('result' => "ok_wallet"
                                            , "data" => ''
                                            , 'desc' => 'پرداخت با موفقیت انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                        }
                                        //***************************************************************************
                                        //***************************************************************************
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

                            } else {
                                //???????????????????????????????????????????????????????????????????????????????????????
                                $amount = $this->post('amount');
                                //payment for all amount of kasri varizi
                                $result = $this->deficit_payment($request_id, $amount, $deficit_pey_id);
                                if ($result === FALSE) { /* Handle error */
                                }
                                echo $result;
                                //???????????????????????????????????????????????????????????????????????????????????????
                            }

//***************************************************************************************************************
                        } else {
                            echo json_encode(array('result' => "error"
                            , "data" => ""
                            , 'desc' => 'پرداخت با موفقیت انجام شده است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                        }
                    } else {
                        echo json_encode(array('result' => "error"
                        , "data" => ""
                        , 'desc' => 'شماره درخواست برای پرداخت کسری واریزی موجود نیست'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    }

                } else {
                    echo json_encode(array('result' => $usertoken[0]
                    , "data" => $usertoken[1]
                    , 'desc' => $usertoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                }


            }else
                if ($command == "peyby_cashzero") {
                $usertoken = checkusertoken($user_token_str);
                if ($usertoken[0] == 'ok') {
                //*********************************************************************************
                    $request_id = $this->post('request_id');
                    $query51="SELECT * FROM request_tb WHERE request_id = $request_id ";
                    $result51=$this->B_db->run_query($query51);
                    $request51=$result51[0];
                    if($request51['request_last_state_id']<2) {
                        $query = "SELECT jsonpricing_text FROM request_tb ,jsonpricing_tb where request_jsonpricing_id =jsonpricing_id AND  request_id =" . $request_id . "";
                        $result = $this->B_db->run_query($query)[0];
                        $jsonpricing=json_decode($result['jsonpricing_text'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                        $sql1 = "select SUM(user_pey_temp_amount) AS sumpey_temp from user_pey_temp_tb where user_pey_temp_request_id = " . $request_id;
                        $result = $this->B_db->run_query($sql1);
                        $user_pey = $result[0];
                        $sum_user_pey_temp_amount= $user_pey['sumpey_temp'];
                        if(!$sum_user_pey_temp_amount){$sum_user_pey_temp_amount=0;}

                        //************************************************************************


                        $amount =$jsonpricing['price']-$sum_user_pey_temp_amount;
                        if($amount==0) {
                            $query = "INSERT INTO user_pey_tb( user_pey_request_id, user_pey_amount , user_pey_mode     , user_pey_code    , user_pey_desc    ,user_pey_image_code     ,user_pey_timestamp)
                                SELECT user_pey_temp_request_id,user_pey_temp_amount,user_pey_temp_mode,user_pey_temp_code,user_pey_temp_desc,user_pey_temp_image_code,user_pey_temp_timestamp
                                FROM user_pey_temp_tb WHERE user_pey_temp_request_id = $request_id     ";
                            $result = $this->B_db->run_query_put($query);
                            //*****************************************************************************
                            $query = "select * from user_pey_tb where user_pey_request_id = $request_id     ";
                            $result = $this->B_db->run_query($query);
                            foreach ($result as $row) {
                                if ($row['user_pey_mode'] == 'instalment') {
                                    $query1 = "SELECT instalment_conditions_instalment_id,instalment_condition_contract_id FROM instalment_conditions_tb WHERE instalment_conditions_id=" . $row['user_pey_code'];
                                    $result1 = $this->B_db->run_query($query1);
                                    $instalment_conditions = $result1[0];
                                    $conditions_id = 0;
                                    if ($instalment_conditions['instalment_conditions_instalment_id'] != null) {
                                        $conditions_id = $instalment_conditions['instalment_conditions_instalment_id'];
                                    } else {
                                        $conditions_id = $instalment_conditions['instalment_condition_contract_id'];
                                    }
                                    $query2 = "INSERT INTO instalment_check_tb( instalment_check_condition_id,    instalment_check_instalment_id                     ,instalment_check_user_pey_id,     instalment_check_amount, instalment_check_desc                ,  instalment_check_request_id, instalment_check_image_code) VALUES
                                                        ( " . $row['user_pey_code'] . ", " . $conditions_id . "," . $row['user_pey_id'] . ", '" . $row['user_pey_amount'] . "', '" . $row['user_pey_desc'] . "',  $request_id          ,'" . $row['user_pey_image_code'] . "')  ";
                                    $result2 = $this->B_db->run_query_put($query2);
                                } else if ($row['user_pey_mode'] == 'managdiscount') {
                                    $query2 = "INSERT INTO managdiscount_use_tb( managdiscount_mngdiscnt_id, managdiscount_request_id, managdiscount_use_timestamp, managdiscount_use_amount) VALUES
                                                        ( " . $row['user_pey_code'] . ",$request_id ,  '" . $row['user_pey_timestamp'] . "','" . $row['user_pey_amount'] . "')  ";
                                    $result2 = $this->B_db->run_query_put($query2);
                                } else if ($row['user_pey_mode'] == 'discount_code') {
                                    $query2 = "INSERT INTO discount_code_use_tb(discount_code_use_dscntcode_id, discount_code_use_request_id, discount_code_use_timestamp, discount_code_use_amount) VALUES
                                                        ( " . $row['user_pey_code'] . ",$request_id ,  '" . $row['user_pey_timestamp'] . "','" . $row['user_pey_amount'] . "')  ";
                                    $result2 = $this->B_db->run_query_put($query2);
                                } else if ($row['user_pey_mode'] == 'user_wallet') {

                                    $user_wallet_detail = 'پرداخت سفارش کد' . $request_id . ' توسط  کیف پول ';
                                    $query2 = "INSERT INTO user_wallet_tb( user_wallet_user_id, user_wallet_amount, user_wallet_mode, user_wallet_timestamp, user_wallet_detail, user_wallet_code) VALUES
                                        (" . $row['user_pey_code'] . ",'" . $row['user_pey_amount'] . "' , 'get'      ,now()               ,'$user_wallet_detail',$request_id)      ";
                                    $result2 = $this->B_db->run_query_put($query2);
                                }
                            }
                            $query3 = "DELETE FROM user_pey_temp_tb WHERE  user_pey_temp_request_id = $request_id";
                            $result3 = $this->B_db->run_query_put($query3);
                            //*****************************************************************************
                            $query41 = "SELECT * FROM request_tb WHERE request_id = $request_id ";
                            $result41 = $this->B_db->run_query($query41);
                            $request1 = $result41[0];
                            if ($request1['request_organ'] == 0) {
                                $query4 = "SELECT * FROM request_tb,user_address_tb WHERE request_addressofinsured_id=user_address_id AND request_id = $request_id ";
                                $result4 = $this->B_db->run_query($query4);
                                $request_address = $result4[0];
                                $agent_id = 0;
                                $query5 = "SELECT * FROM kindsendrequest_tb WHERE kindsendrequest_company_id=" . $request_address['request_company_id'] . "";
                                $result5 = $this->B_db->run_query($query5);
                                foreach ($result5 as $row) {
                                    if ($row['kindsendrequest_state_id'] == 0 && $row['kindsendrequest_city_id'] == 0 && $row['kindsendrequest_kind_id'] == 1 && $row['kindsendrequest_fieldinsurance_id'] == 0) {
                                        $agent_id = $row['kindsendrequest_agent_id'];
                                    }
                                }
                            } else {
                                $query4 = "SELECT * FROM request_tb,user_address_tb,organ_request_tb WHERE request_id=organ_request_request_id AND request_addressofinsured_id=user_address_id AND request_id = $request_id ";
                                $result4 = $this->B_db->run_query($query4);
                                $request_address = $result4[0];
                                $agent_id = 0;
                                $query5 = "SELECT * FROM kindorgansendrequest_tb WHERE kindorgansendrequest_company_id=" . $request_address['request_company_id'] . "";
                                $result5 = $this->B_db->run_query($query5);
                                foreach ($result5 as $row) {
                                    if ($row['kindorgansendrequest_state_id'] == 0 && $row['kindorgansendrequest_city_id'] == 0 && $row['kindorgansendrequest_kind_id'] == 1 && $row['kindorgansendrequest_fieldinsurance_id'] == 0 && $row['kindorgansendrequest_organ_id'] == 0 && $row['kindorgansendrequest_contract_id'] == 0) {
                                        $agent_id = $row['kindorgansendrequest_agent_id'];
                                    }
                                }
                            }
                            $query2 = "UPDATE request_tb SET request_last_state_id=2 ,request_agent_id=$agent_id WHERE  request_id = $request_id";
                            $result2 = $this->B_db->run_query_put($query2);
                            request_send_sms($request_id, 'agent', '');
                            //****************************************************************************
                            $query1 = "INSERT INTO state_request_tb(staterequest_request_id, staterequest_state_id, staterequest_timestamp, staterequest_desc          , staterequest_agent_id) VALUES
                                    (" . $request_id . "        ,      2               ,  now()                 ,'پرداخت توسط کابر انجام شد',$agent_id) ";
                            $result1 = $this->B_db->run_query_put($query1);
                            $staterequest_id = $this->db->insert_id();
                            //  request_send_sms($request_id,'agent','');
                            echo json_encode(array('result' => "ok"
                            , "data" => ""
                            , 'desc' => 'پرداخت با موفقیت انجام شده است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                        //*********************************************************************************
                    }else{
                        echo json_encode(array('result' => "error"
                        , "data" => ""
                        , 'desc' => 'درخواست قبلا پرداخت شده است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    }

                }
            }
        }
    }

    /**
     * Verify Payment
     * @author Abolfazl Ganji
     * @param $orderId - create by store
     * @param $saleOrderId - create by store after success payment
     * @return mixed - false for failed
     */
    public function payment($amount, $orderId, $additionalData = '', $payerId = 0 , $payment_type = "main")
    {
        $this->soapClient = new SoapClient($this->config->item('wsdl'));

        if($payment_type == "main"){
            $callback_url  = $this->config->item('callBackURL');
        }else{
            $callback_url  = $this->config->item('deficitCallBackURL');
        }

        if($amount && $amount > 100 && $orderId ) {
            //tabdil be rial
            $parameters = [
                'terminalId' => $this->config->item('terminalId'),
                'userName' => $this->config->item('userName'),
                'userPassword' => $this->config->item('userPassword'),
                'orderId' => $orderId,
                'amount' => $amount,
                'localDate' => date("Ymd"),
                'localTime' => date("His"),
                'additionalData' => $additionalData,
                'callBackUrl' => $callback_url,
                'payerId' => $payerId
            ];
            
            try {

                // Call the SOAP method
                $result = $this->soapClient->bpPayRequest($parameters);
                
                // Display the result
                $res = explode(',', $result->return);
                if ($res[0] == "0") {
                    return [
                        'result' => true, 
                        'res_code' => $res[0],
                        'RefId' => $res[1]
                    ];
                } else {
                    return [
                        'result' => false,
                        'res_code' => $res[0],
                        'RefId' => isset($res[1]) ? $res[1] : null
                    ];
                }
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }else
        {
            echo "the is error in connecting to bank!";
            die;
        }
    }

    public function add_pey_tb($pey_request_id, $pey_amount,$mode='pey',$deficit_pey_id=0){      
        $this->load->model('B_db');
        $sql1 = "INSERT INTO pey_tb
        (pey_id, pey_request_id, pey_date, pey_amount,pey_mode,pey_deficit_pey_id)
        VALUES(0,$pey_request_id, now(),$pey_amount,'$mode',$deficit_pey_id)";       
        return $result=$this->B_db->run_query_put($sql1);
    }

    public function update_pey_tb($pey_request_id, $pey_refid){
        $this->load->model('B_db');
        $sql2 = "UPDATE pey_tb
        SET pey_refid='$pey_refid', pey_date=now()
        WHERE  pey_id = $pey_request_id";
        $this->B_db->run_query_put($sql2);
    }


    public function deficit_payment($request_id,$deficit_pey_amount,$deficit_pey_id){

        $res = $this->add_pey_tb($request_id,$deficit_pey_amount,'deficitpey',$deficit_pey_id);
        if($res==1)
            $orderId = $this->db->insert_id();
        else{
            echo "OrderId doesnot created...";
            die;
        }
        
        $BankResponse = $this->payment($deficit_pey_amount, $orderId, $additionalData = '', $payerId = 0 , $payment_type = "deficit");
        //Success Payment
        if($BankResponse['res_code'] == 0){
            $RefId = $BankResponse["RefId"];
            $url_operationServer  = $this->config->item('operationServer');
            $BankResponse['url']        = $url_operationServer;
            $BankResponse['callBackURL']=$this->deficitCallBackURL;
            $this->update_pey_tb($orderId, $RefId);
            //move to bank payment page
            echo json_encode($BankResponse,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            //Failed Payment
        }else{
            //Do something ............
            echo json_encode(array('result'=>"error"
            ,"data"=>$BankResponse['res_code']
            ,'desc'=>$this->bank_messages[$BankResponse['res_code']]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
    }

}
