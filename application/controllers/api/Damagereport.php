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
 *
 * @package         CodeIgniter
 * @subpackage      aref24 Project
 * @category        Controller
 * @author          Mohammad Hoseini, Abolfazl Ganji
 * @license         MIT
 * @link            https://aref24.ir
 */
class Damagereport extends REST_Controller {

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
        if(isset($this->input->request_headers()['Authorization']))$user_token_str = $this->input->request_headers()['Authorization'];
        $this->load->model('B_user');
        $this->load->model('B_requests');
        $this->load->model('B_db');
        $this->load->helper('my_helper');
        $command = $this->post("command");
            if ($command == "getuser_damagefile")
            {//register marketer

                $usertoken=checkusertoken($user_token_str);
               
                if($usertoken[0]=='ok') {
                    $user_id=$usertoken[1];

                    $query = "select * from damagefile_tb,evaluatorco_tb,damagefile_state,fielddamagefile_tb,user_tb where user_id=damagefile_user_id AND fielddamagefile_id=damagefile_fielddamagefile_id AND damagefile_state.damagefile_state_id=damagefile_last_state_id AND evaluatorco_id=damagefile_evaluatorco_id AND damagefile_user_id=" . $user_id;
                    $query2 = "select count(*) AS cnt  from damagefile_tb,evaluatorco_tb,damagefile_state,fielddamagefile_tb,user_tb where user_id=damagefile_user_id AND fielddamagefile_id=damagefile_fielddamagefile_id AND damagefile_state.damagefile_state_id=damagefile_last_state_id AND evaluatorco_id=damagefile_evaluatorco_id AND damagefile_user_id=" . $user_id;


                    $query .= ' ORDER BY damagefile_id  DESC';

                    $limit = $this->post("limit");
                    $offset = $this->post("offset");
                    $limit_state ="";
                    if($limit!="" & $offset!="") {
                        $limit_state = " LIMIT " . $offset . "," . $limit;
                    }

                    $result = $this->B_db->run_query($query.$limit_state);
                    $count  = $this->B_db->run_query($query2);


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
                            $query61 = " SELECT * FROM damagefile_ready_image_tb,image_tb WHERE damagefile_ready_image_code=image_code AND damagefile_ready_damagefile_id=" . $damagefile_id;
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

                            $result51 = $this->B_db->get_image($row5['damagefile_delivered_receipt_image_code']);
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

                            $record4['image_url'] = $image['image_url'];
                            $record4['image_tumb_url'] =  $image['image_tumb_url'];
                            $record4['image_name'] = $row4['image_name'];
                            $record4['image_desc'] = $row4['image_desc'];
                            $output4[] = $record4;
                        }
                        $record['damagefile_image'] = $output4;




                        //***************************************************************************************************************


                        $output[] = $record;

                    }
                    header('Content-Type: application/json'); echo json_encode(array('result' => "ok"
                    , "data" => $output
                    ,"cnt"=>$count[0]['cnt']
                    , 'desc' => 'لیست درخواست ها با موفقیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                }
              
               else{
                    header('Content-Type: application/json'); echo json_encode(array('result'=>$usertoken[0]
                    ,"data"=>$usertoken[1]
                    ,'desc'=>$usertoken[2]));
                }


            } else
                if ($command=="getuser_damagefile_detail")
        {//register marketer

            $usertoken=checkusertoken($user_token_str);
            $damagefile_id = $this->post("damagefile_id");
            $user_id=$usertoken[1];
            if($usertoken[0]=='ok') {
                $user_id=$usertoken[1];

                $query = "select * from damagefile_tb,evaluatorco_tb,damagefile_state,fielddamagefile_tb,user_tb where user_id=damagefile_user_id AND fielddamagefile_id=damagefile_fielddamagefile_id AND damagefile_state.damagefile_state_id=damagefile_last_state_id AND evaluatorco_id=damagefile_evaluatorco_id AND damagefile_id=$damagefile_id AND damagefile_user_id=" . $user_id;


                $query .= ' ORDER BY damagefile_id  DESC';


                $result = $this->B_db->run_query($query);


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

                        $result51 = $this->B_db->get_image($row5['damagefile_delivered_receipt_image_code']);
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
                header('Content-Type: application/json'); echo json_encode(array('result' => "ok"
                , "data" => $output
                , 'desc' => 'لیست درخواست ها با موفقیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


            }

            else{
                header('Content-Type: application/json'); echo json_encode(array('result'=>$usertoken[0]
                ,"data"=>$usertoken[1]
                ,'desc'=>$usertoken[2]));
            }
        }else
            if ($command=="get_requesr")
            {
              $usertoken=checkusertoken($user_token_str);
                if($usertoken[0]=='ok')
                {
                    $damagefile_id=$this->post('damagefile_id') ;
                    $result3=$this->B_requests->get_request($damagefile_id,$usertoken[1]);
                    $request=$result3[0];
					 header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                            ,"data"=>$request
                            ,'desc'=>'درخواست مورد نظر ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
				}else{
                    header('Content-Type: application/json'); echo json_encode(array('result'=>$usertoken[0]
                    ,"data"=>$usertoken[1]
                    ,'desc'=>$usertoken[2]));
                }	
			}else
            if ($command=="usercancel_request")
            {//register marketer
                $usertoken=checkusertoken($user_token_str);
                if($usertoken[0]=='ok')
                {
                    $user_id=$usertoken[1];
                    $damagefile_id=$this->post('damagefile_id') ;
                    $damagefile_usercancel_desc=$this->post('damagefile_usercancel_desc') ;
                    $result3=$this->B_requests->get_state_request($damagefile_id);
                    $request=$result3[0];
                    if($request['damagefile_last_state_id']!=9&&$request['damagefile_last_state_id']!=10&&$request['damagefile_last_state_id']!=11){
                        $result=$this->B_requests->get_damagefile_usercancel($damagefile_id);
                        if (empty($result))
                        {
                            $damagefile_usercancel_id = $this->B_requests->add_damagefile_usercancel($damagefile_id,$damagefile_usercancel_desc);
                            $this->B_requests->damagefile_refuse(12,$damagefile_id);
                            $desc='درخواست انصراف توسط کاربر به دلیل '.$damagefile_usercancel_desc.' انجام شد ';
                            $this->B_requests->set_damagefile_refuse($damagefile_id,12,$desc);
                            $this->B_requests->del_damagefile_releations($damagefile_id);
                            $result2=$this->B_requests->get_damagefile_peycash($damagefile_id);
                            foreach($result2 as $row)
                            {
                                $desc='بازگشت پرداختی نقدی با شماره پیگیری'.$row['user_pey_code']. ' و جزئیات '.$row['user_pey_desc'].'برای بازگشت درخواست شماره '.$damagefile_id;
                                $result = $this->B_user->set_user_wallet($user_id ,$row['user_pey_amount'],'add',$desc ,$row['user_pey_code']);
                            }
                            $this->B_requests->del_damagefile_pey($damagefile_id);
                            header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                            ,"data"=>array('damagefile_usercancel_id'=>$damagefile_usercancel_id)
                            ,'desc'=>'درخواست لغو ثبت شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                        }else{
                            $damagefile_usercancel=$result[0];
                            header('Content-Type: application/json'); echo json_encode(array('result'=>"error"
                            ,"data"=>array('damagefile_usercancel_id'=>$damagefile_usercancel['damagefile_usercancel_id'])
                            ,'desc'=>'درخواست لغو تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                    }else{
                        header('Content-Type: application/json'); echo json_encode(array('result'=>"error"
                        ,"data"=>array('damagefile_state_name'=>$request['damagefile_state_name'])
                        ,'desc'=>'درخواست با این وضعیت قابل کنسل کردن نیست و باید درخواست الحاقیه دهید'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                }else{
                    header('Content-Type: application/json'); echo json_encode(array('result'=>$usertoken[0]
                    ,"data"=>$usertoken[1]
                    ,'desc'=>$usertoken[2]));

                }
            }
            else if ($command=="getstate_request")
            {//register marketerstate

                $damagefile_damagefile_id=$this->post('damagefile_id') ;

                $usertoken=checkusertoken($user_token_str);
                if($usertoken[0]=='ok')
                {
                    $user_id=$usertoken[1];
                    $result=$this->B_requests->get_damagefile_state($damagefile_damagefile_id);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record['statedamagefile_id']=$row['statedamagefile_id'];
                        $record['statedamagefile_state_id']=$row['statedamagefile_state_id'];
                        $record['damagefile_state_name']=$row['damagefile_state_name'];
                        $record['statedamagefile_timestamp']=$row['statedamagefile_timestamp'];
                        $record['statedamagefile_desc']=$row['statedamagefile_desc'];
                        $output[]=$record;
                    }
                    header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'لیست وضعیت های درخواست  با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    header('Content-Type: application/json'); echo json_encode(array('result'=>$usertoken[0]
                    ,"data"=>$usertoken[1]
                    ,'desc'=>$usertoken[2]));
                }
            }
            else if ($command=="getpey_request")
            {//register marketerstate

                $user_pey_damagefile_id=$this->post('damagefile_id') ;
                $usertoken=checkusertoken($user_token_str);
                if($usertoken[0]=='ok')
                {
                    $user_id=$usertoken[1];
                    $result=$this->B_requests->get_user_pey($user_pey_damagefile_id);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record['user_pey_id']=$row['user_pey_id'];
                        $record['user_pey_amount']=$row['user_pey_amount'];
                        $record['user_pey_mode']=$row['user_pey_mode'];
                        $record['pey_mod_fa']=$row['pey_mod_fa'];
                        $record['user_pey_code']=$row['user_pey_code'];
                        $record['user_pey_desc']=$row['user_pey_desc'];
                        $record['user_pey_image_code']=$row['user_pey_image_code'];
                        $record['user_pey_timestamp']=$row['user_pey_timestamp'];
                        $output[]=$record;
                    }
                    header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'لیست  پرداختی های درخواست  با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    header('Content-Type: application/json'); echo json_encode(array('result'=>$usertoken[0]
                    ,"data"=>$usertoken[1]
                    ,'desc'=>$usertoken[2]));

                }
            }
            else if ($command=="getbackuser_request")
            {//register marketerstate

                $damagefile_backuser_damagefile_id=$this->post('damagefile_id') ;
                $usertoken=checkusertoken($user_token_str);
                if($usertoken[0]=='ok')
                {
                    $user_id=$usertoken[1];
                    $result=$this->B_requests->damagefile_backuser($damagefile_backuser_damagefile_id);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record['damagefile_backuser_timestamp']=$row['damagefile_backuser_timestamp'];
                        $record['damagefile_backuser_desc']=$row['damagefile_backuser_desc'];
                        $record['damagefile_backuser_agent_id']=$row['damagefile_backuser_agent_id'];
                        $output[]=$record;
                    }
                    header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'جزئیات درخواست  با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    header('Content-Type: application/json'); echo json_encode(array('result'=>$usertoken[0]
                    ,"data"=>$usertoken[1]
                    ,'desc'=>$usertoken[2]));
                }
            }
            else if ($command=="getdeficitpey_request")
            {//register marketerstate

                $deficit_pey_damagefile_id=$this->post('damagefile_id') ;
                $usertoken=checkusertoken($user_token_str);
                if($usertoken[0]=='ok')
                {
                    $user_id=$usertoken[1];
                    $result=$this->B_user->deficit_user_pey($deficit_pey_damagefile_id);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record['deficit_pey_amount']=$row['deficit_pey_amount'];
                        $record['deficit_pey_reason']=$row['deficit_pey_reason'];
                        $record['user_pey_desc']=$row['user_pey_desc'];
                        $output[]=$record;
                    }
                    header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'جزئیات درخواست  با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                    //****************************************************************************************************************

                }else{
                    header('Content-Type: application/json'); echo json_encode(array('result'=>$usertoken[0]
                    ,"data"=>$usertoken[1]
                    ,'desc'=>$usertoken[2]));
                }
            }
            else if ($command=="getsuspend_request")
            {//register marketerstate

                $requst_suspend_damagefile_id=$this->post('damagefile_id') ;
                $usertoken=checkusertoken($user_token_str);
                if($usertoken[0]=='ok')
                {
                    $user_id=$usertoken[1];
                    $result=$this->B_requests->requst_suspend($requst_suspend_damagefile_id);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record['requst_suspend_timestamp']=$row['requst_suspend_timestamp'];
                        $record['requst_suspend_end_date']=$row['requst_suspend_end_date'];
                        $record['requst_suspend_desc']=$row['requst_suspend_desc'];
                        $output[]=$record;
                    }
                    header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'جزئیات درخواست  با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    header('Content-Type: application/json'); echo json_encode(array('result'=>$usertoken[0]
                    ,"data"=>$usertoken[1]
                    ,'desc'=>$usertoken[2]));
                }
            }
            else if ($command=="getdifficult_request")
            {//register marketerstate

                $damagefile_difficult_damagefile_id=$this->post('damagefile_id') ;
                $usertoken=checkusertoken($user_token_str);
                if($usertoken[0]=='ok')
                {
                    $user_id=$usertoken[1];
                    $result=$this->B_requests->requst_difficult($damagefile_difficult_damagefile_id);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record['damagefile_difficult_timestamp']=$row['damagefile_difficult_timestamp'];
                        $record['damagefile_difficult_desc']=$row['damagefile_difficult_desc'];
                        $output[]=$record;
                    }
                    header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'جزئیات درخواست  با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    header('Content-Type: application/json'); echo json_encode(array('result'=>$usertoken[0]
                    ,"data"=>$usertoken[1]
                    ,'desc'=>$usertoken[2]));
                }
            }
            else if ($command=="getready_request")
            {//register marketerstate

                $requst_ready_damagefile_id=$this->post('damagefile_id') ;
                $usertoken=checkusertoken($user_token_str);
                if($usertoken[0]=='ok')
                {
                    $user_id=$usertoken[1];
                    $result=$this->B_requests->get_damagefile_ready($requst_ready_damagefile_id);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record['requst_ready_start_date']=$row['requst_ready_start_date'];
                        $record['requst_ready_end_date']=$row['requst_ready_end_date'];
                        $record['requst_ready_end_price']=$row['requst_ready_end_price'];
                        $record['requst_ready_num_ins']=$row['requst_ready_num_ins'];
                        $record['requst_ready_code_insurer']=$row['requst_ready_code_insurer'];
                        $record['requst_ready_name_insurer']=$row['requst_ready_name_insurer'];
                        $record['requst_suspend_desc']=$row['requst_suspend_desc'];
                        $output[]=$record;
                    }
                    header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'جزئیات درخواست  با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                    //****************************************************************************************************************

                }else{
                    header('Content-Type: application/json'); echo json_encode(array('result'=>$usertoken[0]
                    ,"data"=>$usertoken[1]
                    ,'desc'=>$usertoken[2]));
                }
            }
            else if ($command=="getready_image_request")
            {//register marketerstate

                $requst_ready_damagefile_id=$this->post('damagefile_id') ;
                $usertoken=checkusertoken($user_token_str);
                if($usertoken[0]=='ok')
                {
                    $user_id=$usertoken[1];
                    $result=$this->B_requests->requst_ready_image($requst_ready_damagefile_id);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record['requst_ready_image_code']=$row['requst_ready_image_code'];
                        $output[]=$record;
                    }
                    header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'عکسهای درخواست  با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    header('Content-Type: application/json'); echo json_encode(array('result'=>$usertoken[0]
                    ,"data"=>$usertoken[1]
                    ,'desc'=>$usertoken[2]));
                }
            }
            else if ($command=="getdelivered_request")
            {//register marketerstate

                $damagefile_delivered_damagefile_id=$this->post('damagefile_id') ;
                $usertoken=checkusertoken($user_token_str);
                if($usertoken[0]=='ok')
                {
                    $user_id=$usertoken[1];
                    $result=$this->B_requests->damagefile_delivered_city($damagefile_delivered_damagefile_id);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record['damagefile_delivered_timesatmp']=$row['damagefile_delivered_timesatmp'];
                        $record['damagefile_delivered_mode']=$row['damagefile_delivered_mode_id'];
                        $record['damagefile_delivered_dsc']=$row['damagefile_delivered_dsc'];
                        $record['damagefile_delivered_receipt_image_code']=$row['damagefile_delivered_receipt_image_code'];
                        $record['damagefile_delivered_state_id']=$row['damagefile_delivered_state_id'];
                        $record['state_name']=$row['state_name'];
                        $record['damagefile_delivered_city_id']=$row['damagefile_delivered_city_id'];
                        $record['city_name']=$row['city_name'];
                        $output[]=$record;
                    }
                    header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'جزئیات درخواست  با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                    //****************************************************************************************************************

                }else{
                    header('Content-Type: application/json'); echo json_encode(array('result'=>$usertoken[0]
                    ,"data"=>$usertoken[1]
                    ,'desc'=>$usertoken[2]));
                }
            }
            else if ($command=="getdeficitpey_user")
            {//register marketerstate

                $usertoken=checkusertoken($user_token_str);
                if($usertoken[0]=='ok')
                {
                    $user_id=$usertoken[1];
                    $result=$this->B_requests->requst_deficit_pey($user_id);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record['deficit_pey_id']=$row['deficit_pey_id'];
                        $record['deficit_pey_damagefile_id']=$row['deficit_pey_damagefile_id'];
                        $record['company_name']=$row['company_name'];
                        $record['company_logo_url']=IMGADD.$row['company_logo_url'];
                        $record['fielddamagefile_fa']=$row['fielddamagefile_fa'];
                        $record['deficit_pey_amount']=$row['deficit_pey_amount'];
                        $record['deficit_pey_reason']=$row['deficit_pey_reason'];
                        $record['deficit_pey_user_pey_id']=$row['deficit_pey_user_pey_id'];
                        $record['deficit_pey_user_pey_date']=$row['deficit_pey_user_pey_date'];
                        $output[]=$record;
                    }
                    header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'جزئیات درخواست  با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    header('Content-Type: application/json'); echo json_encode(array('result'=>$usertoken[0]
                    ,"data"=>$usertoken[1]
                    ,'desc'=>$usertoken[2]));
                }
            }
            else if ($command=="getvisitrequest")
            {//register marketerstate

                $usertoken=checkusertoken($user_token_str);
                if($usertoken[0]=='ok')
                {
                    $user_id=$usertoken[1];
                    $result=$this->B_requests->requst_visit($user_id);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record['damagefile_visit_id']=$row['damagefile_visit_id'];
                        $record['damagefile_visit_damagefile_id']=$row['damagefile_visit_damagefile_id'];
                        $record['company_name']=$row['company_name'];
                        $record['company_logo_url']=IMGADD.$row['company_logo_url'];
                        $record['fielddamagefile_fa']=$row['fielddamagefile_fa'];
                        $record['damagefile_visit_date']=$row['damagefile_visit_date'];
                         $output[]=$record;
                    }
                    header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'جزئیات درخواست  با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    header('Content-Type: application/json'); echo json_encode(array('result'=>$usertoken[0]
                    ,"data"=>$usertoken[1]
                    ,'desc'=>$usertoken[2]));
                }
            }
            else if ($command=="changedifficult_user")
            {//register marketerstate

                $usertoken=checkusertoken($user_token_str);
                if($usertoken[0]=='ok')
                {
                    $user_id=$usertoken[1];
                    $damagefile_id=$this->post('damagefile_id') ;
                    $result=$this->B_requests->get_damagefile_by(8 ,$damagefile_id);
                    if (!empty($result))
                    {
                        $this->B_requests->damagefile_refuse(3,$damagefile_id);
                        $this->B_requests->set_damagefile_refuse($damagefile_id,3,"تصاویر توسط کاربر ارسال شد.");
                        header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                        ,"data"=>""
                        ,'desc'=>'وضعیت درخواست به در حال بررسی توسط نماینده تغییر یافت'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        header('Content-Type: application/json'); echo json_encode(array('result'=>"error"
                        ,"data"=>""
                        ,'desc'=>'وضعیت درخواست مشکل برای صدور نیست و قابل تغییر نمیباشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                    }
                }else{
                    header('Content-Type: application/json'); echo json_encode(array('result'=>$usertoken[0]
                    ,"data"=>$usertoken[1]
                    ,'desc'=>$usertoken[2]));
                }
            }
    }
}
