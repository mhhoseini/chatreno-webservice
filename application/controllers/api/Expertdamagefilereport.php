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
class Expertdamagefilereport extends REST_Controller
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
        if (isset($this->input->request_headers()['Experttokenstr'])) $expert_token_str = $this->input->request_headers()['Experttokenstr'];
        $this->load->model('B_user');
        $this->load->model('B_db');
        $this->load->model('B_expert');
        $this->load->helper('my_helper');
        $this->load->helper('time_helper');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('expertdamagefilereport', $command, get_client_ip(),50,50)) {
            if ($command == "getexpert_damagefile") {//register marketer

                $experttoken = checkexperttoken($expert_token_str);
                $approvalmode = $this->post('approvalmode');
                if ($experttoken[0] == 'ok') {
                    $expert_id = $experttoken[1];

                    $query1 = "select * from damagefile_tb,evaluatorco_tb,damagefile_state,fielddamagefile_tb,user_tb where user_id=damagefile_user_id AND fielddamagefile_id=damagefile_fielddamagefile_id AND damagefile_state.damagefile_state_id=damagefile_last_state_id AND evaluatorco_id=damagefile_evaluatorco_id AND damagefile_expert_id=" . $expert_id;
                    $query2 = "select count(*) AS cnt from damagefile_tb,evaluatorco_tb,damagefile_state,fielddamagefile_tb,user_tb where user_id=damagefile_user_id AND fielddamagefile_id=damagefile_fielddamagefile_id AND damagefile_state.damagefile_state_id=damagefile_last_state_id AND evaluatorco_id=damagefile_evaluatorco_id AND damagefile_expert_id=" . $expert_id;


                    $query = ' ORDER BY damagefile_id  DESC ';

                    $limit = $this->post("limit");
                    $offset = $this->post("offset");
                    $limit_state ="";
                    if($limit!="" & $offset!="") {
                        $limit_state = " LIMIT " . $offset . "," . $limit;
                    }

                    $result = $this->B_db->run_query($query1.$query.$limit_state);
                    $count  = $this->B_db->run_query($query2.$query);



                    $output = array();
                    $damagefile_id = '';
                    foreach ($result as $row) {
                        $record = array();
                        $record['damagefile_id'] = $row['damagefile_id'];
                        $damagefile_id = $row['damagefile_id'];
                        $record['user_id'] = $row['user_id'];
                        $record['user_name'] = $row['user_name'];
                        $record['user_family'] = $row['user_family'];
                        $record['user_mobile'] = $row['user_mobile'];
                        $record['fielddamagefile_logo_url'] = IMGADD . $row['fielddamagefile_logo_url'];
                        $record['fielddamagefile_id'] = $row['fielddamagefile_id'];
                        $record['damagefile_fielddamagefile_fa'] = $row['fielddamagefile_fa'];
                        $record['damagefile_description'] = $row['damagefile_description'];
                        $record['damagefile_price_user'] = $row['damagefile_price_user'];
                        $record['damagefile_last_state_id'] = $row['damagefile_last_state_id'];
                        $record['damagefile_last_state_name'] = $row['damagefile_state_name'];
                        //**************************************************************************************

                        $query201="SELECT * FROM organ_user_therapy_tb,user_therapy_bank_tb,user_gender_tb,user_therapy_kind_tb,user_therapy_kindship_tb,
user_therapy_baseinsurer_tb
WHERE organ_user_therapy_bank_id=user_therapy_bank_id
AND organ_user_therapy_gender_id=user_gender_id
AND organ_user_therapy_kind_id=user_therapy_kind_id
AND user_therapy_kindship_id=organ_user_therapy_kinship_id
AND organ_user_therapy_basebime_id=user_therapy_baseinsurer_id
AND organ_user_therapy_id=".$row['damagefile_user_therapy_id'];
                        $result201 = $this->B_db->run_query($query201);
                        if(!empty($result201)) {
                            $record['organ_user_therapy'] = $result201[0];
                         }
                        //**************************************************************************************

                        $record['damagefile_therapycontract_id']=$row['damagefile_therapycontract_id'];
                        $query200="select * from organ_therapycontract_tb,organ_tb where  organ_id=organ_therapycontract_organ_id AND organ_therapycontract_id=".$row['damagefile_therapycontract_id'];
                        $result200 = $this->B_db->run_query($query200);
                        if(!empty($result200)) {
                            $organ = $result200[0];
                            $record['organ_id']=$organ['organ_id'];
                            $record['organ_name']=$organ['organ_name'];
                            $record['organ_therapycontract_num']=$organ['organ_therapycontract_num'];

                            $result1 = $this->B_db->get_image($organ['organ_logo']);
                            $imageurl = "";
                            if (!empty($result1)) {
                                $image = $result1[0];
                                if ($image['image_url']) {
                                    $imageurl =  $image['image_url'];
                                }
                            }
                            $record['organ_url']=$imageurl;
                        }

                        //**************************************************************************************

                        //*************************************************************************************************************

                        //*************************************************************************************************************
                        //*************************************************************************************************************





                        //***************************************************************************************************************
                        $query17 = "select * from state_damagefile_tb where statedamagefile_damagefile_id=" . $damagefile_id . " ORDER BY statedamagefile_id DESC LIMIT 1 ";
                        $result17 = $this->B_db->run_query($query17);
                        $state_damagefile17 = $result17[0];
                        $record['statedamagefile_last_timestamp'] = $state_damagefile17['statedamagefile_timestamp'];

                        //***************************************************************************************************************
                        $query7 = "select * from state_damagefile_tb,damagefile_state where damagefile_state_id=statedamagefile_state_id AND statedamagefile_damagefile_id=" . $damagefile_id;
                        $result7 = $this->B_db->run_query($query7);
                        $output7 = array();
                        foreach ($result7 as $row7) {

                            $record7['statedamagefile_id'] = $row7['statedamagefile_id'];
                            //  $record7['statedamagefile_state_id']=$row7['statedamagefile_state_id'];
                            $record7['damagefile_state_name'] = $row7['damagefile_state_name'];
                            $record7['statedamagefile_timestamp'] = $row7['statedamagefile_timestamp'];
                            $record7['statedamagefile_desc'] = $row7['statedamagefile_desc'];
                            // $record7['statedamagefile_expert_id']=$row7['statedamagefile_expert_id'];

                            if ($row7['statedamagefile_expert_id']) {
                                $query71 = " SELECT * FROM expert_tb WHERE expert_id =" . $row7['statedamagefile_expert_id'];
                                $result71 = $this->B_db->run_query($query71);
                                $expert = $result71[0];
                                if ($expert['expert_code'] == null) {
                                    $record7['expert_code'] = null;
                                } else {
                                    $record7['expert_code'] = $expert['expert_code'];
                                }
                                if ($expert['expert_name'] == null) {
                                    $record7['expert_name'] = null;
                                } else {
                                    $record7['expert_name'] = $expert['expert_name'];
                                }
                                if ($expert['expert_family'] == null) {
                                    $record7['expert_family'] = null;
                                } else {
                                    $record7['expert_family'] = $expert['expert_family'];
                                }
                            }

                            if ($row7['statedamagefile_employee_id']&&$row7['statedamagefile_employee_id']!=0) {
                                $query71 = " SELECT * FROM employee_tb WHERE employee_id =" . $row7['statedamagefile_employee_id'];
                                $result71 = $this->B_db->run_query($query71);
                                $expert = $result71[0];

                                if ($expert['employee_name'] == null) {
                                    $record7['employee_name'] = null;
                                } else {
                                    $record7['employee_name'] = $expert['employee_name'];
                                }
                                if ($expert['employee_family'] == null) {
                                    $record7['employee_family'] = null;
                                } else {
                                    $record7['employee_family'] = $expert['employee_family'];
                                }
                            }else{
                                $record7['employee_name'] = null;
                                $record7['employee_family'] = null;
                            }

                            $output7[] = $record7;
                        }
                        $record['damagefile_stats'] = $output7;

                        //***************************************************************************************************************
                        //***************************************************************************************************************
                        $query6=" SELECT * FROM damagefile_ready_tb,request_ready_clearing_mode_tb WHERE damagefile_ready_clearing_id=request_ready_clearing_mode_id AND  damagefile_ready_damagefile_id=".$damagefile_id;
                        $result6 = $this->B_db->run_query($query6);
                        $output6 = array();
                        foreach ($result6 as $row6) {

                            $record6['damagefile_ready_expert_date'] = $row6['damagefile_ready_expert_date'];
                            $record6['damagefile_ready_pay_date'] = $row6['damagefile_ready_pay_date'];
                            $record6['damagefile_ready_expert_estimate'] = $row6['damagefile_ready_expert_estimate'];
                            $record6['damagefile_ready_tracking_code'] = $row6['damagefile_ready_tracking_code'];
                            $record6['damagefile_ready_code_yekta'] = $row6['damagefile_ready_code_yekta'];
                            $record6['damagefile_ready_code_rayane'] = $row6['damagefile_ready_code_rayane'];
                            $record6['damagefile_ready_name_insurer'] = $row6['damagefile_ready_name_insurer'];
                            $record6['damagefile_ready_code_insurer'] = $row6['damagefile_ready_code_insurer'];
                            $record6['damagefile_ready_code_penalty'] = $row6['damagefile_ready_code_penalty'];
                            $record6['damagefile_ready_clearing_mode_name']=$row6['request_ready_clearing_mode_name'];
                            $record6['request_ready_clearing_id']=$row6['request_ready_clearing_id'];
                            $record6['damagefile_suspend_desc'] = $row6['damagefile_suspend_desc'];

                            //*************************************************************************************************************
                            $query61 = " SELECT * FROM damagefile_ready_image_tb,image_tb WHERE image_code=damagefile_ready_image_code AND damagefile_ready_damagefile_id=" . $damagefile_id;
                            $result61 = $this->B_db->run_query($query61);
                            $output61 = array();
                            foreach ($result61 as $row61) {

                                $result1 = $this->B_db->get_image($row61['damagefile_ready_image_code']);
                                $image = $result1[0];

                                $record61['image_url'] =  $image['image_url'];
                                $record61['image_tumb_url'] =  $image['image_tumb_url'];
                                $record61['image_name'] = $row61['image_name'];
                                $record61['image_desc'] = $row61['image_desc'];
                                $output61[] = $record61;
                            }
                            $record6['damagefile_ready_image_tb'] = $output61;

                            //*************************************************************************************************************
                            $query62 = " SELECT * FROM damagefile_file_tb WHERE damagefile_file_damagefile_id=" . $damagefile_id;
                            $result62 = $this->B_db->run_query($query62);
                            $output62 = array();
                            foreach ($result62 as $row62) {

                                $record62['damagefile_file_url'] = IMGADD . $row62['damagefile_file_url'];
                                $record62['damagefile_file_desc'] = $row62['damagefile_file_desc'];
                                $output62[] = $record62;
                            }
                            $record6['damagefile_ready_file_tb'] = $output62;

                            //*************************************************************************************************************    $output6[]=$record6;
                            $output6[] = $record6;
                        }
                        $record['damagefile_ready'] = $output6;

                        //***************************************************************************************************************

                        //***************************************************************************************************************
                        $query5 = " SELECT * FROM damagefile_delivered_tb,state_tb,city_tb,delivery_mode_tb WHERE delivery_mode_id=damagefile_delivered_mode_id AND state_id=damagefile_delivered_state_id AND city_id=damagefile_delivered_city_id AND damagefile_delivered_damagefile_id=" . $row['damagefile_id'];
                        $result5 = $this->B_db->run_query($query5);
                        $output5 = array();
                        foreach ($result5 as $row5) {

                            $record5['damagefile_delivered_timesatmp'] = $row5['damagefile_delivered_timesatmp'];
                            $record5['damagefile_delivered_mode'] = $row5['delivery_mode_name'];
                            $record5['damagefile_delivered_dsc'] = $row5['damagefile_delivered_dsc'];
                            $record5['damagefile_delivered_state'] = $row5['state_name'];
                            $record5['damagefile_delivered_city'] = $row5['city_name'];

                            $result51 = $this->B_db->get_image($row['damagefile_delivered_receipt_image_code']);
                            $image = $result51[0];

                            if ($image['image_tumb_url'] == null) {
                                $record5['user_pey_image_turl'] = null;
                            } else {
                                $record5['user_pey_image_turl'] =  $image['image_tumb_url'];
                            }
                            if ($image['image_url'] == null) {
                                $record5['user_pey_image_url'] = null;
                            } else {
                                $record5['user_pey_image_url'] =  $image['image_url'];
                            }

                            $output5[] = $record5;
                        }
                        $record['damagefile_delivered'] = $output5;

                        //***************************************************************************************************************

                        $query4 = " SELECT * FROM damagefile_img_tb,image_tb WHERE image_code=damagefile_img_image_code AND damagefile_img_damagefile_id=" . $damagefile_id;
                        $result4 = $this->B_db->run_query($query4);
                        $output4 = array();
                        foreach ($result4 as $row4) {

                            $result1 = $this->B_db->get_image($row4['damagefile_img_image_code']);
                            $image = $result1[0];

                            $record4['image_url'] =  $image['image_url'];
                            $record4['image_tumb_url'] =  $image['image_tumb_url'];
                            $record4['image_name'] = $row4['image_name'];
                            $record4['image_desc'] = $row4['image_desc'];
                            $output4[] = $record4;
                        }
                        $record['damagefile_image'] = $output4;




                        //***************************************************************************************************************


                        $output[] = $record;

                    }
                    echo json_encode(array('result' => "ok"
                    , "data" => $output
                    ,"cnt"=>$count[0]['cnt']
                    , 'desc' => 'لیست درخواست ها با موفقیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                } else {
                    echo json_encode(array('result' => $experttoken[0]
                    , "data" => $experttoken[1]
                    , 'desc' => $experttoken[2]));

                }


            } else
                if ($command == "damagefilepending2") {//register marketer

                    $experttoken = checkexperttoken($expert_token_str);
                    if ($experttoken[0] == 'ok') {
                        $expert_id = $experttoken[1];
//***************************************************************************************************************
                        $damagefile_id = $this->post('damagefile_id');

                        $query2 = "select * from damagefile_tb where damagefile_id=" . $damagefile_id . "";
                        $result2 = $this->B_db->run_query($query2);
                        $damagefile = $result2[0];
                        if ($damagefile['damagefile_last_state_id'] != 2) {
//***************************************************************************************************************

                            $query1 = "UPDATE damagefile_tb SET damagefile_last_state_id=2 WHERE damagefile_id=$damagefile_id";
                            $result1 = $this->B_db->run_query_put($query1);

                            $desc = 'در حال بررسی توسط ارزیاب خسارت';
                            $query = "INSERT INTO state_damagefile_tb( statedamagefile_damagefile_id, statedamagefile_state_id, statedamagefile_timestamp,statedamagefile_desc,statedamagefile_expert_id,statedamagefile_employee_id) VALUES
                                        ( $damagefile_id, 2, now(),'$desc',$expert_id,$experttoken[3])";
                            $result = $this->B_db->run_query_put($query);

                            damagefile_send_sms($damagefile_id, 'user', $desc);

                            echo json_encode(array('result' => "ok"
                            , "data" => get_damagefile_expert($damagefile_id)
                            , 'desc' => ' تغییر مرحله درخواست ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                            //****************************************************************************************************************
                        } else {
                            echo json_encode(array('result' => "error1"
                            , "data" => ""
                            , 'desc' => ' درخواست نمیتواند به وضعیت در حال بررسی در آید '), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }

                    } else {
                        echo json_encode(array('result' => $experttoken[0]
                        , "data" => $experttoken[1]
                        , 'desc' => $experttoken[2]));

                    }


                } else
                    if ($command == "damagefilebackuser3") {//register marketer

                        $experttoken = checkexperttoken($expert_token_str);
                        if ($experttoken[0] == 'ok') {
                            $expert_id = $experttoken[1];
//***************************************************************************************************************
                            $damagefile_id = $this->post('damagefile_id');
                            $damagefile_backuser_desc = $this->post('damagefile_backuser_desc');

                            $query2 = "select * from damagefile_tb where damagefile_id=" . $damagefile_id . "";
                            $result2 = $this->B_db->run_query($query2);
                            $damagefile = $result2[0];
                            $user_id = $damagefile['damagefile_user_id'];
                            if ($damagefile['damagefile_last_state_id'] != 3) {
//***************************************************************************************************************

                                $query1 = "UPDATE damagefile_tb SET damagefile_last_state_id=3 WHERE damagefile_id=$damagefile_id";
                                $result1 = $this->B_db->run_query_put($query1);

                                $desc = 'برگشت  پرونده خسارت به کاربر به علت ' . $damagefile_backuser_desc;
                                $query = "INSERT INTO state_damagefile_tb( statedamagefile_damagefile_id, statedamagefile_state_id, statedamagefile_timestamp,statedamagefile_desc,statedamagefile_expert_id,statedamagefile_employee_id) VALUES
                                        ( $damagefile_id, 3, now(),'$desc',$expert_id,$experttoken[3])";
                                $result = $this->B_db->run_query_put($query);

                                damagefile_send_sms($damagefile_id, 'user', $desc);


                                $query = "INSERT INTO damagefile_backuser_tb( damagefile_backuser_damagefile_id, damagefile_backuser_timestamp, damagefile_backuser_desc, damagefile_backuser_expert_id) VALUES
                                        ( $damagefile_id, now(),'$damagefile_backuser_desc',$expert_id)";
                                $result = $this->B_db->run_query_put($query);



                                echo json_encode(array('result' => "ok"
                                , "data" => get_damagefile_expert($damagefile_id)
                                , 'desc' => ' تغییر مرحله درخواست ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                //****************************************************************************************************************
                            } else {
                                echo json_encode(array('result' => "error11"
                                , "data" => ""
                                , 'desc' => ' درخواست نمیتواند به کاربر برگشت داده شود '), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }

                        } else {
                            echo json_encode(array('result' => $experttoken[0]
                            , "data" => $experttoken[1]
                            , 'desc' => $experttoken[2]));

                        }


                    } else
                        if ($command == "damagefilebackexpert4") {//register marketer

                        $experttoken = checkexperttoken($expert_token_str);
                        if ($experttoken[0] == 'ok') {
                            $expert_id = $experttoken[1];
//***************************************************************************************************************
                            $damagefile_id = $this->post('damagefile_id');
                            $damagefile_backexpert_desc = $this->post('damagefile_backexpert_desc');

                            $query2 = "select * from damagefile_tb where damagefile_id=" . $damagefile_id . "";
                            $result2 = $this->B_db->run_query($query2);
                            $damagefile = $result2[0];
                            if ($damagefile['damagefile_last_state_id'] != 4) {
//***************************************************************************************************************
                                $newexpert_id = 2;
                                $query1 = "UPDATE damagefile_tb SET damagefile_last_state_id=4 ,damagefile_expert_id=$newexpert_id WHERE damagefile_id=$damagefile_id";
                                $result1 = $this->B_db->run_query_put($query1);

                                $desc = ' درخواست به کارشناس ارزیاب دیگر ارجاع داده شد به علت' . $damagefile_backexpert_desc;
                                $query = "INSERT INTO state_damagefile_tb( statedamagefile_damagefile_id, statedamagefile_state_id, statedamagefile_timestamp,statedamagefile_desc,statedamagefile_expert_id,statedamagefile_employee_id) VALUES
                                        ( $damagefile_id, 4, now(),'$desc',$expert_id,$experttoken[3])";
                                $result = $this->B_db->run_query_put($query);

                                $query = "INSERT INTO damagefile_backexpert_tb( damagefile_backexpert_damagefile_id, damagefile_backexpert_timestamp, damagefile_backexpert_desc, damagefile_backexpert_expert_id) VALUES
                                        ( $damagefile_id, now(),'$damagefile_backexpert_desc',$expert_id)";
                                $result = $this->B_db->run_query_put($query);

                                echo json_encode(array('result' => "ok"
                                , "data" => get_damagefile_expert($damagefile_id)
                                , 'desc' => ' تغییر مرحله درخواست ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                //****************************************************************************************************************
                            } else {
                                echo json_encode(array('result' => "error111"
                                , "data" => ""
                                , 'desc' => ' درخواست نمیتواند به وضعیت برگشت به نماینده دیگر در آید '), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }

                        } else {
                            echo json_encode(array('result' => $experttoken[0]
                            , "data" => $experttoken[1]
                            , 'desc' => $experttoken[2]));

                        }

                    } else
                        if ($command == "waitdoctor5") {//register marketer

                            $experttoken = checkexperttoken($expert_token_str);
                            if ($experttoken[0] == 'ok') {
                                $expert_id = $experttoken[1];
//***************************************************************************************************************
                                $damagefile_id = $this->post('damagefile_id');
                                $waitdoctor_reason = $this->post('waitdoctor_reason');

                                $query2 = "select * from damagefile_tb where damagefile_id=" . $damagefile_id . "";
                                $result2 = $this->B_db->run_query($query2);
                                $damagefile = $result2[0];
                                if ($damagefile['damagefile_last_state_id'] != 5) {
//***************************************************************************************************************
                                    $query1 = "UPDATE damagefile_tb SET damagefile_last_state_id=5 WHERE damagefile_id=$damagefile_id";
                                    $result1 = $this->B_db->run_query_put($query1);

                                    $desc = ' معلق برای بررسی پزشک به علت ' . $waitdoctor_reason . '  ';
                                    $query = "INSERT INTO state_damagefile_tb( statedamagefile_damagefile_id, statedamagefile_state_id, statedamagefile_timestamp,statedamagefile_desc,statedamagefile_expert_id,statedamagefile_employee_id) VALUES
                                        ( $damagefile_id, 5, now(),'$desc',$expert_id,$experttoken[3])";
                                    $result = $this->B_db->run_query_put($query);

                                    damagefile_send_sms($damagefile_id, 'user', $desc);


                                    echo json_encode(array('result' => "ok"
                                    , "data" => get_damagefile_expert($damagefile_id)
                                    , 'desc' => ' تغییر مرحله درخواست ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                    //****************************************************************************************************************
                                } else {
                                    echo json_encode(array('result' => "error1111"
                                    , "data" => ""
                                    , 'desc' => ' درخواست نمیتواند به وضعیت کسری واریزی در آید '), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                }

                            } else {
                                echo json_encode(array('result' => $experttoken[0]
                                , "data" => $experttoken[1]
                                , 'desc' => $experttoken[2]));

                            }


                        } else
                            if ($command == "damagefiledeletedepositdeficit6") {//register marketer

                                $experttoken = checkexperttoken($expert_token_str);
                                if ($experttoken[0] == 'ok') {
                                    $expert_id = $experttoken[1];
//***************************************************************************************************************
                                    $damagefile_id = $this->post('damagefile_id');
                                    $deficit_pey_reason = $this->post('deficit_pey_reason');
                                    $deficit_pey_amount = $this->post('deficit_pey_amount');

                                    $query2 = "select * from damagefile_tb where damagefile_id=" . $damagefile_id . "";
                                    $result2 = $this->B_db->run_query($query2);
                                    $damagefile = $result2[0];
                                    if ($damagefile['damagefile_last_state_id'] == 6) {
//***************************************************************************************************************
                                        $query1 = "UPDATE damagefile_tb SET damagefile_last_state_id=3 WHERE damagefile_id=$damagefile_id";
                                        $result1 = $this->B_db->run_query_put($query1);

                                        $desc = ' حذف کسری واریزی ';
                                        $query = "INSERT INTO state_damagefile_tb( statedamagefile_damagefile_id, statedamagefile_state_id, statedamagefile_timestamp,statedamagefile_desc,statedamagefile_expert_id,statedamagefile_employee_id) VALUES
                                        ( $damagefile_id, 3, now(),'$desc',$expert_id,$experttoken[3])";
                                        $result = $this->B_db->run_query_put($query);
                                        damagefile_send_sms($damagefile_id, 'user', $desc);

                                        $query = "DELETE FROM deficit_pey_tb WHERE deficit_pey_damagefile_id=$damagefile_id";
                                        $result = $this->B_db->run_query_put($query);

                                        echo json_encode(array('result' => "ok"
                                        , "data" => get_damagefile_expert($damagefile_id)
                                        , 'desc' => ' تغییر مرحله درخواست ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                        //****************************************************************************************************************
                                    } else {
                                        echo json_encode(array('result' => "error11111"
                                        , "data" => ""
                                        , 'desc' => ' درخواست نمیتواند از وضعیت کسری واریزی حذف گردد  '), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                    }
                                } else {
                                    echo json_encode(array('result' => $experttoken[0]
                                    , "data" => $experttoken[1]
                                    , 'desc' => $experttoken[2]));

                                }

                            } else
                                if ($command == "damagefilesuspend7") {//register marketer

                                    $experttoken = checkexperttoken($expert_token_str);
                                    if ($experttoken[0] == 'ok') {
                                        $expert_id = $experttoken[1];
//***************************************************************************************************************
                                        $damagefile_id = $this->post('damagefile_id');
                                        $damagefile_suspend_desc = $this->post('damagefile_suspend_desc');

                                        $query2 = "select * from damagefile_tb where damagefile_id=" . $damagefile_id . "";
                                        $result2 = $this->B_db->run_query($query2);
                                        $damagefile = $result2[0];
                                        if ($damagefile['damagefile_last_state_id'] != 7) {
//***************************************************************************************************************
                                            $query1 = "UPDATE damagefile_tb SET damagefile_last_state_id=7 WHERE damagefile_id=$damagefile_id";
                                            $result1 = $this->B_db->run_query_put($query1);

                                            $desc = '   معلق درآمد به علت ' . $damagefile_suspend_desc ;
                                            $query = "INSERT INTO state_damagefile_tb( statedamagefile_damagefile_id, statedamagefile_state_id, statedamagefile_timestamp,statedamagefile_desc,statedamagefile_expert_id,statedamagefile_employee_id) VALUES
                                        ( $damagefile_id, 7, now(),'$desc',$expert_id,$experttoken[3])";
                                            $result = $this->B_db->run_query_put($query);
                                            damagefile_send_sms($damagefile_id, 'user', $desc);



                                            echo json_encode(array('result' => "ok"
                                            , "data" => get_damagefile_expert($damagefile_id)
                                            , 'desc' => ' تغییر مرحله درخواست ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                            //****************************************************************************************************************
                                        } else {
                                            echo json_encode(array('result' => "error2"
                                            , "data" => ""
                                            , 'desc' => ' درخواست نمیتواند به تعلیق تا تاریخ صدور درآید'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                        }

                                    } else {
                                        echo json_encode(array('result' => $experttoken[0]
                                        , "data" => $experttoken[1]
                                        , 'desc' => $experttoken[2]));

                                    }


                                } else
                                    if ($command == "damagefiledifficult6") {//register marketer

                                        $experttoken = checkexperttoken($expert_token_str);
                                        if ($experttoken[0] == 'ok') {
                                            $expert_id = $experttoken[1];
//***************************************************************************************************************
                                            $damagefile_id = $this->post('damagefile_id');
                                            $damagefile_difficult_desc = $this->post('damagefile_difficult_desc');

                                            $query2 = "select * from damagefile_tb where damagefile_id=" . $damagefile_id . "";
                                            $result2 = $this->B_db->run_query($query2);
                                            $damagefile = $result2[0];
                                            if ($damagefile['damagefile_last_state_id'] != 6) {
//***************************************************************************************************************
                                                $query1 = "UPDATE damagefile_tb SET damagefile_last_state_id=6 WHERE damagefile_id=$damagefile_id";
                                                $result1 = $this->B_db->run_query_put($query1);

                                                $desc = ' معلق  برای صدور به علت ' . $damagefile_difficult_desc;
                                                $query = "INSERT INTO state_damagefile_tb( statedamagefile_damagefile_id, statedamagefile_state_id, statedamagefile_timestamp,statedamagefile_desc,statedamagefile_expert_id,statedamagefile_employee_id) VALUES
                                        ( $damagefile_id, 6, now(),'$desc',$expert_id,$experttoken[3])";
                                                $result = $this->B_db->run_query_put($query);

                                                damagefile_send_sms($damagefile_id, 'user', $desc);

                                                $query = "INSERT INTO damagefile_difficult_tb( damagefile_difficult_damagefile_id, damagefile_difficult_timestamp, damagefile_difficult_desc, damagefile_difficult_expert_id) VALUES
                                        ( $damagefile_id, now(),'$damagefile_difficult_desc',$expert_id)";
                                                $result = $this->B_db->run_query_put($query);

                                                echo json_encode(array('result' => "ok"
                                                , "data" => get_damagefile_expert($damagefile_id)
                                                , 'desc' => ' تغییر مرحله درخواست ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                                //****************************************************************************************************************
                                            } else {
                                                echo json_encode(array('result' => "error"
                                                , "data" => ""
                                                , 'desc' => ' درخواست نمیتواند به وضعیت تعلیق به علت وجود مشکل در عکس ها در آید '), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                            }

                                        } else {
                                            echo json_encode(array('result' => $experttoken[0]
                                            , "data" => $experttoken[1]
                                            , 'desc' => $experttoken[2]));

                                        }


                                    } else
                                        if ($command == "damagefileoverpayment12") {//register marketer

                                            $experttoken = checkexperttoken($expert_token_str);
                                            if ($experttoken[0] == 'ok') {
                                                $expert_id = $experttoken[1];
//***************************************************************************************************************
                                                $damagefile_id = $this->post('damagefile_id');
                                                $user_id = $this->post('user_id');
                                                $overpayment_reason = $this->post('overpayment_reason');
                                                $overpayment_amount = $this->post('overpayment_amount');

//***************************************************************************************************************

                                                $desc = 'درخواست، اضافه واریزی دارد به علت ' . $overpayment_reason . ' و مبلغ اضافه واریزی ' . $overpayment_amount . ' ریال است ';
                                                $query = "INSERT INTO state_damagefile_tb( statedamagefile_damagefile_id, statedamagefile_state_id, statedamagefile_timestamp,statedamagefile_desc,statedamagefile_expert_id,statedamagefile_employee_id) VALUES
                                        ( $damagefile_id, 3, now(),'$desc',$expert_id,$experttoken[3])";

                                                $result = $this->B_db->run_query_put($query);


                                                $user_wallet_detail = 'واریز به کیف پول به علت اضافه واریزی سفارش کد' . $damagefile_id . ' به علت ' . $overpayment_reason;
                                                $query2 = "INSERT INTO user_wallet_tb( user_wallet_user_id, user_wallet_amount, user_wallet_mode, user_wallet_timestamp, user_wallet_detail, user_wallet_code) VALUES
                                  ($user_id             , $overpayment_amount   ,    'add'      ,now()               ,'$user_wallet_detail',$damagefile_id)      ";
                                                $result2 = $this->B_db->run_query_put($query2);
                                                $user_wallet_id = $this->db->insert_id();

                                                $user_pey_temp_desc = 'درخواست  به علت ' . $overpayment_reason . ' اضافه واریزی دارد و به کیف پول کاربر بازگشت داده میشود';
                                                $query1 = "INSERT INTO user_pey_tb( user_pey_damagefile_id, user_pey_amount, user_pey_mode, user_pey_code, user_pey_desc,user_pey_timestamp) VALUES
                                      (  $damagefile_id           , $overpayment_amount, 'overpayment'    ,  $user_wallet_id   ,'$user_pey_temp_desc',now())      ";
                                                $result1 = $this->B_db->run_query_put($query1);
                                                $user_pey_id = count($result1[0]);


                                                echo json_encode(array('result' => "ok"
                                                , "data" => get_damagefile_expert($damagefile_id)
                                                , 'desc' => ' تغییر مرحله درخواست ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                                //****************************************************************************************************************


                                            } else {
                                                echo json_encode(array('result' => $experttoken[0]
                                                , "data" => $experttoken[1]
                                                , 'desc' => $experttoken[2]));

                                            }


                                        } else
                                            if ($command == "damagefileissuing9") {//register marketer

                                                $experttoken = checkexperttoken($expert_token_str);
                                                if ($experttoken[0] == 'ok') {
                                                    $expert_id = $experttoken[1];
//***************************************************************************************************************
                                                    $damagefile_id = $this->post('damagefile_id');

                                                    $query2 = "select * from damagefile_tb where damagefile_id=" . $damagefile_id . "";
                                                    $result2 = $this->B_db->run_query($query2);
                                                    $damagefile = $result2[0];
                                                    if ($damagefile['damagefile_last_state_id'] != 9) {
//***************************************************************************************************************

                                                        $query1 = "UPDATE damagefile_tb SET damagefile_last_state_id=9 WHERE damagefile_id=$damagefile_id";
                                                        $result1 = $this->B_db->run_query_put($query1);

                                                        $desc = ' در حال صدور ';
                                                        $query = "INSERT INTO state_damagefile_tb( statedamagefile_damagefile_id, statedamagefile_state_id, statedamagefile_timestamp,statedamagefile_desc,statedamagefile_expert_id,statedamagefile_employee_id) VALUES
                                        ( $damagefile_id, 9, now(),'$desc',$expert_id,$experttoken[3])";
                                                        $result = $this->B_db->run_query_put($query);

                                                        damagefile_send_sms($damagefile_id, 'user', $desc);

                                                        echo json_encode(array('result' => "ok"
                                                        , "data" => get_damagefile_expert($damagefile_id)
                                                        , 'desc' => ' تغییر مرحله درخواست ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                                        //****************************************************************************************************************
                                                    } else {
                                                        echo json_encode(array('result' => "error222"
                                                        , "data" => ""
                                                        , 'desc' => ' درخواست نمیتواند به وضعیت در حال صدور در آید '), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                    }

                                                } else {
                                                    echo json_encode(array('result' => $experttoken[0]
                                                    , "data" => $experttoken[1]
                                                    , 'desc' => $experttoken[2]));

                                                }


                                            } else
                                                if ($command == "damagefileready6") {//register marketer

                                                    $experttoken = checkexperttoken($expert_token_str);
                                                    if ($experttoken[0] == 'ok') {
                                                        $expert_id = $experttoken[1];
//***************************************************************************************************************
                                                        $damagefile_id = $this->post('damagefile_id');
                                                        $damagefile_ready_expert_date = $this->post('damagefile_ready_expert_date');
                                                        $damagefile_ready_pay_date = $this->post('damagefile_ready_pay_date');
                                                        $damagefile_ready_expert_estimate = $this->post('damagefile_ready_expert_estimate');
                                                        $damagefile_ready_tracking_code = $this->post('damagefile_ready_tracking_code');
                                                        $damagefile_ready_code_yekta = $this->post('damagefile_ready_code_yekta');
                                                        $damagefile_ready_code_rayane = $this->post('damagefile_ready_code_rayane');
                                                        $damagefile_ready_name_insurer = $this->post('damagefile_ready_name_insurer');
                                                        $damagefile_ready_code_insurer = $this->post('damagefile_ready_code_insurer');
                                                        $damagefile_ready_clearing_id = $this->post('damagefile_ready_clearing_id');
                                                        $damagefile_suspend_desc = $this->post('damagefile_suspend_desc');
                                                        $damagefile_ready_code_penalty = $this->post('damagefile_ready_code_penalty');

                                                        $query2 = "select * from damagefile_tb where damagefile_id=" . $damagefile_id . "";
                                                        $result2 = $this->B_db->run_query($query2);
                                                        $damagefile = $result2[0];
                                                        if ($damagefile['damagefile_last_state_id'] != 8) {
//***************************************************************************************************************
                                                            $query="DELETE FROM damagefile_ready_tb WHERE damagefile_ready_damagefile_id=$damagefile_id";
                                                            $result = $this->B_db->run_query_put($query);


                                                            $query3 = "INSERT INTO damagefile_ready_tb( damagefile_ready_damagefile_id, damagefile_ready_timestamp, damagefile_ready_expert_date, damagefile_ready_pay_date, damagefile_ready_expert_estimate, damagefile_ready_tracking_code, damagefile_ready_code_yekta,damagefile_ready_code_rayane,damagefile_ready_code_penalty, damagefile_ready_name_insurer, damagefile_ready_code_insurer, damagefile_suspend_desc, damagefile_suspend_expert_id,damagefile_ready_employee_id,damagefile_ready_clearing_id) VALUES
                                                                                                      ( $damagefile_id, now()                       ,'$damagefile_ready_expert_date' ,'$damagefile_ready_pay_date' ,'$damagefile_ready_expert_estimate' ,'$damagefile_ready_tracking_code','$damagefile_ready_code_yekta','$damagefile_ready_code_rayane','$damagefile_ready_code_penalty','$damagefile_ready_name_insurer','$damagefile_ready_code_insurer' ,'$damagefile_suspend_desc',$expert_id,$experttoken[3],$damagefile_ready_clearing_id)";
                                                            $damagefile_ready_id = $this->B_db->run_query_put($query3);
                                                            if($damagefile_ready_id){
                                                                $query1 = "UPDATE damagefile_tb SET damagefile_last_state_id=8  WHERE damagefile_id=$damagefile_id";
                                                                $result1 = $this->B_db->run_query_put($query1);

                                                                $desc = ' پرونده خسارت شما به مبلغ  '.$damagefile_ready_expert_estimate.' تایید و برای پرداخت به مالی ارجاع داده شد';
                                                                $query2 = "INSERT INTO state_damagefile_tb( statedamagefile_damagefile_id, statedamagefile_state_id, statedamagefile_timestamp,statedamagefile_desc,statedamagefile_expert_id,statedamagefile_employee_id) VALUES
                                        ( $damagefile_id, 8, now(),'$desc',$expert_id,$experttoken[3])";
                                                                $result1 = $this->B_db->run_query_put($query2);
                                                                damagefile_send_sms($damagefile_id, 'user', $desc);

                                                                //**********************************************************************************

                                                                //**********************************************************************************

                                                                echo json_encode(array('result' => "ok"
                                                                , "data" => get_damagefile_expert($damagefile_id)
                                                                , 'desc' => ' تغییر مرحله درخواست ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                            }else{

                                                                echo json_encode(array('result' => "error"
                                                                , "data" => get_damagefile_expert($damagefile_id)
                                                                , 'desc' => ' تغییر مرحله درخواست ثبت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                                            }
                                                            //****************************************************************************************************************
                                                        } else {
                                                            echo json_encode(array('result' => "error"
                                                            , "data" => ""
                                                            , 'desc' => ' درخواست نمیتواند به وضعیت صادر شده و آماده تحویل در آید '), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                        }

                                                    } else {
                                                        echo json_encode(array('result' => $experttoken[0]
                                                        , "data" => $experttoken[1]
                                                        , 'desc' => $experttoken[2]));

                                                    }
                                                } else
                                                    if ($command == "damagefilerevoke13") {//register marketer

                                                        $experttoken = checkexperttoken($expert_token_str);
                                                        if ($experttoken[0] == 'ok') {
                                                            $expert_id = $experttoken[1];
//***************************************************************************************************************
                                                            $damagefile_id = $this->post('damagefile_id');
                                                            $damagefile_revoke_date = $this->post('damagefile_revoke_date');
                                                            $damagefile_revoke_desc = $this->post('damagefile_revoke_desc');
                                                            $damagefile_revoke_price = $this->post('damagefile_revoke_price');

                                                            $query2 = "select * from damagefile_tb where damagefile_id=" . $damagefile_id . "";
                                                            $result2 = $this->B_db->run_query($query2);
                                                            $damagefile = $result2[0];
                                                            if (($damagefile['damagefile_last_state_id'] == 10 || $damagefile['damagefile_last_state_id'] == 11) && $damagefile['damagefile_last_state_id'] != 13) {
//***************************************************************************************************************
                                                                $query1 = "UPDATE damagefile_tb SET damagefile_last_state_id=13  WHERE damagefile_id=$damagefile_id";
                                                                $result1 = $this->B_db->run_query_put($query1);

                                                                $desc = 'بیمه نامه به علت ' . $damagefile_revoke_desc . ' ابطال شده است و مبلغ' . $damagefile_revoke_price . '  قیمت نهایی بیمه نامه است';
                                                                $query = "INSERT INTO state_damagefile_tb( statedamagefile_damagefile_id, statedamagefile_state_id, statedamagefile_timestamp,statedamagefile_desc,statedamagefile_expert_id,statedamagefile_employee_id) VALUES
                                                                ( $damagefile_id, 13, now(),'$desc',$expert_id,$experttoken[3])";
                                                                $result = $this->B_db->run_query_put($query);

                                                                damagefile_send_sms($damagefile_id, 'user', $desc);

                                                                $query = "INSERT INTO damagefile_revoke_tb( damagefile_revoke_damagefile_id, damagefile_revoke_date, damagefile_revoke_desc, damagefile_revoke_price, damagefile_revoke_timestamp) VALUES 
                                                                                                  ( $damagefile_id,           '$damagefile_revoke_date' ,'$damagefile_revoke_desc' ,'$damagefile_revoke_price' ,now())";
                                                                $result = $this->B_db->run_query_put($query);
                                                                $damagefile_ready_id = count($result[0]);
                                                                //**********************************************************************************
                                                                $query0 = "select sum(user_pey_amount) AS overpayment from user_pey_tb where user_pey_mode='overpayment' AND user_pey_damagefile_id=" . $damagefile_id;
                                                                $result0 = $this->B_db->run_query($query0);
                                                                $user_pey0 = $result0[0];
                                                                $overpayment = $user_pey0['overpayment'];

                                                                $query2 = "select sum(user_pey_amount) AS sumcash from user_pey_tb where not (user_pey_mode = 'instalment')  AND  not (user_pey_mode='overpayment') AND user_pey_damagefile_id=" . $damagefile_id;
                                                                $result2 = $this->B_db->run_query($query2);
                                                                $user_pey2 = $result2[0];
                                                                $user_pey_cash = $user_pey2['sumcash'] - $overpayment;

                                                                $query2 = "select sum(instalment_check_amount) AS sumchckpassed from instalment_check_tb where 	instalment_check_pass=1 AND instalment_check_damagefile_id=" . $damagefile_id;
                                                                $result2 = $this->B_db->run_query($query2);
                                                                $instalment_check = $result2[0];
                                                                $amount = $instalment_check['sumchckpassed'] + $user_pey_cash - $damagefile_revoke_price;
                                                                if ($amount > 0) {
                                                                    $this->B_db->peyback_decision($damagefile_id, $amount, 'get', $damagefile_revoke_desc,'nomain');
                                                                }

                                                                //**********************************************************************************

                                                                echo json_encode(array('result' => "ok"
                                                                , "data" => get_damagefile_expert($damagefile_id)
                                                                , 'desc' => ' تغییر مرحله درخواست ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                                                //****************************************************************************************************************
                                                            } else {
                                                                echo json_encode(array('result' => "error"
                                                                , "data" => ""
                                                                , 'desc' => ' درخواست نمیتواند به وضعیت صادر شده و آماده تحویل در آید '), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                            }

                                                        } else {
                                                            echo json_encode(array('result' => $experttoken[0]
                                                            , "data" => $experttoken[1]
                                                            , 'desc' => $experttoken[2]));

                                                        }
                                                    } else
                                                        if ($command == "damagefilereadyimgsave") {//register marketer

                                                            $experttoken = checkexperttoken($expert_token_str);
                                                            if ($experttoken[0] == 'ok') {
                                                                $expert_id = $experttoken[1];
//***************************************************************************************************************
                                                                $damagefile_id = $this->post('damagefile_id');
                                                                $damagefile_ready_image_code = $this->post('damagefile_ready_image_code');


                                                                $query2 = "select * from damagefile_tb where damagefile_id=" . $damagefile_id . "";
                                                                $result2 = $this->B_db->run_query($query2);
                                                                $damagefile = $result2[0];
                                                                if ($damagefile['damagefile_last_state_id'] != 2) {
//***************************************************************************************************************


                                                                    $query = "INSERT INTO damagefile_ready_image_tb(damagefile_ready_damagefile_id, damagefile_ready_image_code) VALUES
                                        ( $damagefile_id,'$damagefile_ready_image_code')";
                                                                    $result = $this->B_db->run_query_put($query);


                                                                    echo json_encode(array('result' => "ok"
                                                                    , "data" => get_damagefile_expert($damagefile_id)
                                                                    , 'desc' => ' عکسها به درخواست صادر شده اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                                                    //****************************************************************************************************************
                                                                } else {
                                                                    echo json_encode(array('result' => "error"
                                                                    , "data" => ""
                                                                    , 'desc' => ' عکس ها به درخواست اضافه نشد '), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                                }

                                                            } else {
                                                                echo json_encode(array('result' => $experttoken[0]
                                                                , "data" => $experttoken[1]
                                                                , 'desc' => $experttoken[2]));

                                                            }


                                                        } else
                                                            if ($command == "damagefiledelivered11") {//register marketer

                                                                $experttoken = checkexperttoken($expert_token_str);
                                                                if ($experttoken[0] == 'ok') {
                                                                    $expert_id = $experttoken[1];
//***************************************************************************************************************
                                                                    $damagefile_id = $this->post('damagefile_id');
                                                                    $damagefile_delivered_mode_id = $this->post('damagefile_delivered_mode_id');
                                                                    $damagefile_delivered_dsc = $this->post('damagefile_delivered_dsc');
                                                                    $damagefile_delivered_receipt_image_code = $this->post('damagefile_delivered_receipt_image_code');
                                                                    $damagefile_delivered_state_id = $this->post('damagefile_delivered_state_id');
                                                                    $damagefile_delivered_city_id = $this->post('damagefile_delivered_city_id');

                                                                    $query2 = "select * from damagefile_tb where damagefile_id=" . $damagefile_id . "";
                                                                    $result2 = $this->B_db->run_query($query2);
                                                                    $damagefile = $result2[0];
                                                                    if ($damagefile['damagefile_last_state_id'] == 10 || $damagefile['damagefile_last_state_id'] == 11) {
//***************************************************************************************************************
                                                                        $query1 = "UPDATE damagefile_tb SET damagefile_last_state_id=11  WHERE damagefile_id=$damagefile_id";
                                                                        $result1 = $this->B_db->run_query_put($query1);

                                                                        $desc = ' ارسال به کاربر ';
                                                                        $query = "INSERT INTO state_damagefile_tb( statedamagefile_damagefile_id, statedamagefile_state_id, statedamagefile_timestamp,statedamagefile_desc,statedamagefile_expert_id,statedamagefile_employee_id) VALUES
                                        ( $damagefile_id, 11, now(),'$desc',$expert_id,$experttoken[3])";
                                                                        $result = $this->B_db->run_query_put($query);

                                                                        damagefile_send_sms($damagefile_id, 'user', $desc);

                                                                        $query = "DELETE FROM damagefile_delivered_tb WHERE damagefile_delivered_damagefile_id=$damagefile_id";
                                                                        $result = $this->B_db->run_query_put($query);

                                                                        $query = "INSERT INTO damagefile_delivered_tb( damagefile_delivered_damagefile_id, damagefile_delivered_timesatmp, damagefile_delivered_mode_id, damagefile_delivered_dsc, damagefile_delivered_receipt_image_code, damagefile_delivered_state_id, damagefile_delivered_city_id) VALUES
                                        ( $damagefile_id,                 now()                  ,$damagefile_delivered_mode_id ,'$damagefile_delivered_dsc' ,'$damagefile_delivered_receipt_image_code' ,$damagefile_delivered_state_id ,$damagefile_delivered_city_id)";
                                                                        $result = $this->B_db->run_query_put($query);
                                                                        $damagefile_ready_id = count($result[0]);

                                                                        echo json_encode(array('result' => "ok"
                                                                        , "data" => get_damagefile_expert($damagefile_id)
                                                                        , 'desc' => ' تغییر مرحله درخواست ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                                                        //****************************************************************************************************************
                                                                    } else {
                                                                        echo json_encode(array('result' => "error"
                                                                        , "data" => ""
                                                                        , 'desc' => ' درخواست نمیتواند به وضعیت تحویل به کاربر در آید '), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                                    }

                                                                } else {
                                                                    echo json_encode(array('result' => $experttoken[0]
                                                                    , "data" => $experttoken[1]
                                                                    , 'desc' => $experttoken[2]));

                                                                }


                                                            } else

                                                                            if ($command == "getstatedamagefile") {//register marketer

                                                                                $query = "select * from damagefile_state where 1";
                                                                                $result = $this->B_db->run_query($query);
                                                                                $output = array();
                                                                                foreach ($result as $row) {
                                                                                    $record = array();
                                                                                    $record['damagefile_state_id'] = $row['damagefile_state_id'];
                                                                                    $record['damagefile_state_name'] = $row['damagefile_state_name'];
                                                                                    $output[] = $record;
                                                                                }
                                                                                echo json_encode(array('result' => "ok"
                                                                                , "data" => $output
                                                                                , 'desc' => 'وضعیت درخواست ها با  موفقیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                                                                            } else
                                                                                if ($command == "get_fielddamagefile") {//register marketer

                                                                                    $query = "select * from fielddamagefile_tb where 1";
                                                                                    $result = $this->B_db->run_query($query);
                                                                                    $output = array();
                                                                                    foreach ($result as $row) {
                                                                                        $record = array();
                                                                                        $record['fielddamagefile_id'] = $row['fielddamagefile_id'];
                                                                                        $record['fielddamagefile_fa'] = $row['fielddamagefile_fa'];
                                                                                        $output[] = $record;
                                                                                    }
                                                                                    echo json_encode(array('result' => "ok"
                                                                                    , "data" => $output
                                                                                    , 'desc' => 'مشحصات رشته بیمه با  موفقیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                                                                                }
            if ($command == "get_mode_delivery") {

                $query = "select * from delivery_mode_tb where 1";
                $result = $this->B_db->run_query($query);
                $output = array();
                foreach ($result as $row) {
                    $record = array();
                    $record['delivery_mode_id'] = $row['delivery_mode_id'];
                    $record['delivery_mode_name'] = $row['delivery_mode_name'];
                    $output[] = $record;
                }

                echo json_encode(array('result' => "ok"
                , "data" => $output
                , 'desc' => ' انواع ارسال درخواست ها ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            } else
                if ($command == "add_filedamagefile") {

                    $experttoken = checkexperttoken($expert_token_str);
                    if ($experttoken[0] == 'ok') {
                        $expert_id = $experttoken[1];
//***************************************************************************************************************
                        $damagefile_file_damagefile_id = $this->post('damagefile_file_damagefile_id');
                        $damagefile_file_desc = $this->post('damagefile_file_desc');
//***************************************************************************************************************
                        $timezone = new DateTimeZone("Asia/Tehran");
                        $date = new DateTime();
                        $date->setTimezone($timezone);
                        $year = $date->format("Y");
                        $month = $date->format("m");
                        $dey = $date->format("d");

                        $upload_path = 'filefolder/uploadfile/' . $year . '/' . $month . '/' . $dey . '/';

                        if (!file_exists($upload_path)) {
                            @mkdir($upload_path, 0755, true);
                        }


                        if (isset($_FILES['filedamagefile']['name'])) {

                            $fileinfo = pathinfo($_FILES['filedamagefile']['name']);
                            $extension = $fileinfo['extension'];

                            $date = new DateTime();
                            $date->setTimezone($timezone);
                            $current_timestamp = $date->getTimestamp();
                            $image_code = $current_timestamp;


                            $file_url = $upload_path . $image_code . '.' . $extension;
                            if (move_uploaded_file($_FILES['filedamagefile']['tmp_name'], $file_url)) {
//***************************************************************************************************************
                                $damagefile_file_url = $file_url;

                                $query = "INSERT INTO damagefile_file_tb( damagefile_file_damagefile_id , damagefile_file_url, damagefile_file_desc) VALUES
                                        ( $damagefile_file_damagefile_id ,'$damagefile_file_url' ,'$damagefile_file_desc' )";
                                $result = $this->B_db->run_query_put($query);
                                $damagefile_ready_id = count($result[0]);

                                echo json_encode(array('result' => "ok"
                                , "data" => get_damagefile_expert($damagefile_file_damagefile_id)
                                , 'desc' => 'فایل  بیمه نامه اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                //***************************************************************************************************************

                            } else {

                                echo json_encode(array('result' => "error"
                                , "data" => ""
                                , 'desc' => 'بارگزاری موفقیت آمیز نبود'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }
                        } else {
                            echo json_encode(array('result' => "error"
                            , "data" => ""
                            , 'desc' => 'فایل موجود نیست'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                        //***************************************************************************************************************

                    } else {
                        echo json_encode(array('result' => $experttoken[0]
                        , "data" => $experttoken[1]
                        , 'desc' => $experttoken[2]));

                    }

                } else
                    if ($command == "get_filedamagefile") {

                        $experttoken = checkexperttoken($expert_token_str);
                        if ($experttoken[0] == 'ok') {
                            $expert_id = $experttoken[1];
//***************************************************************************************************************
                            $damagefile_file_damagefile_id = $this->post('damagefile_file_damagefile_id');

                            $query = "select * from damagefile_file_tb where damagefile_file_damagefile_id=$damagefile_file_damagefile_id";
                            $result = $this->B_db->run_query($query);
                            $output = array();
                            foreach ($result as $row) {
                                $record = array();
                                $record['damagefile_file_id'] = $row['damagefile_file_id'];
                                $record['damagefile_file_damagefile_id'] = $row['damagefile_file_damagefile_id'];
                                $record['damagefile_file_url'] = $row['damagefile_file_url'];
                                $record['damagefile_file_desc'] = $row['damagefile_file_desc'];
                                $output[] = $record;
                            }

                            echo json_encode(array('result' => "ok"
                            , "data" => $output
                            , 'desc' => ' فایل های درخواست ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            //***************************************************************************************************************

                        } else {
                            echo json_encode(array('result' => $experttoken[0]
                            , "data" => $experttoken[1]
                            , 'desc' => $experttoken[2]));

                        }

                    } else
                                if ($command=="addcouncildamagefile") {//register marketer
                                    $experttoken = checkexperttoken($expert_token_str);
                                    if ($experttoken[0] == 'ok') {
                                        $damagefile_id=$this->post('damagefile_id') ;
                                        $damagefilecouncil_desc=$this->post('damagefilecouncil_desc') ;
                                        $expert_id=$experttoken[1] ;
                                        $damagefilecouncil_image_code=$this->post('damagefilecouncil_image_code') ;

                                        $query="INSERT INTO damagefilecouncil_tb ( damagefilecouncil_damagefile_id, damagefilecouncil_timestamp, damagefilecouncil_desc, damagefilecouncil_expert_id, damagefilecouncil_employee_id,damagefilecouncil_image_code) VALUES
                                        ( $damagefile_id, now(),'$damagefilecouncil_desc',$expert_id,$experttoken[3],'$damagefilecouncil_image_code')";
                                        $result = $this->B_db->run_query_put($query);

                                        if($result){
                                            echo json_encode(array('result'=>"ok"
                                            ,"data"=>array('damagefile_id'=>$damagefile_id)
                                            ,'desc'=>'درخواست اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                        }else{
                                            echo json_encode(array('result'=>"error"
                                            ,"data"=>$query
                                            ,'desc'=>'درخواست اضافه نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                        }

                                    } else {
                                        echo json_encode(array('result' => $experttoken[0]
                                        , "data" => $experttoken[1]
                                        , 'desc' => $experttoken[2]));

                                    }
                                } else
                                    if ($command=="getcouncildamagefile") {//register marketer
                                        $experttoken = checkexperttoken($expert_token_str);
                                        if ($experttoken[0] == 'ok') {
                                            $damagefile_id=$this->post('damagefile_id') ;

                                            $query7="select * from damagefilecouncil_tb where  damagefilecouncil_damagefile_id=".$damagefile_id;
                                            $result7 = $this->B_db->run_query($query7);
                                            $output7 =array();
                                            if(!empty($result7)) {
                                                foreach ($result7 as $row7) {
                                                    $record7['damagefilecouncil_id'] = $row7['damagefilecouncil_id'];
                                                    $record7['damagefilecouncil_timestamp'] = $row7['damagefilecouncil_timestamp'];
                                                    $record7['damagefilecouncil_desc'] = $row7['damagefilecouncil_desc'];

                                                    if ($row7['damagefilecouncil_expert_id']) {
                                                        $query71 = " SELECT * FROM expert_tb WHERE expert_id =" . $row7['damagefilecouncil_expert_id'];
                                                        $result71 = $this->B_db->run_query($query71);
                                                        if (!empty($result71))
                                                            $expert = $result71[0];
                                                        else
                                                            $expert = array();
                                                        if ($expert['expert_code'] == null) {
                                                            $record7['expert_code'] = null;
                                                        } else {
                                                            $record7['expert_code'] = $expert['expert_code'];
                                                        }
                                                        if ($expert['expert_name'] == null) {
                                                            $record7['expert_name'] = null;
                                                        } else {
                                                            $record7['expert_name'] = $expert['expert_name'];
                                                        }
                                                        if ($expert['expert_family'] == null) {
                                                            $record7['expert_family'] = null;
                                                        } else {
                                                            $record7['expert_family'] = $expert['expert_family'];
                                                        }
                                                    }
                                                    if ($row7['damagefilecouncil_employee_id'] && $row7['damagefilecouncil_employee_id'] != 0) {
                                                        $query71 = " SELECT * FROM employee_tb WHERE employee_id =" . $row7['damagefilecouncil_employee_id'];
                                                        $result71 = $this->B_db->run_query($query71);
                                                        $expert = $result71[0];

                                                        if ($expert['employee_name'] == null) {
                                                            $record7['employee_name'] = null;
                                                        } else {
                                                            $record7['employee_name'] = $expert['employee_name'];
                                                        }
                                                        if ($expert['employee_family'] == null) {
                                                            $record7['employee_family'] = null;
                                                        } else {
                                                            $record7['employee_family'] = $expert['employee_family'];
                                                        }
                                                    } else {
                                                        $record7['employee_name'] = null;
                                                        $record7['employee_family'] = null;
                                                    }
                                                    $result1 = $this->B_db->get_image($row7['damagefilecouncil_image_code']);
                                                    $imageurl = "";
                                                    $imageturl = "";
                                                    if (!empty($result1)) {
                                                        $image = $result1[0];
                                                        if ($image['image_url']) {
                                                            $imageurl =  $image['image_url'];
                                                            $imageturl =  $image['image_tumb_url'];
                                                        }
                                                    }
                                                    $record7['damagefilecouncil_image']=$imageurl;
                                                    $record7['damagefilecouncil_timage']=$imageturl;

                                                    $output7[] = $record7;
                                                }
                                            }
                                            if($result7){
                                                echo json_encode(array('result'=>"ok"
                                                ,"data"=>$output7
                                                ,'desc'=>'درخواست اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                            }else{
                                                echo json_encode(array('result'=>"error"
                                                ,"data"=>$query7
                                                ,'desc'=>'درخواست اضافه نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                            }

                                        } else {
                                            echo json_encode(array('result' => $experttoken[0]
                                            , "data" => $experttoken[1]
                                            , 'desc' => $experttoken[2]));

                                        }
                                    }

        }
    }
}