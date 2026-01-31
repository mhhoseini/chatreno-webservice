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
class Damagereportorgan extends REST_Controller {

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
        $this->load->model('B_user');
        $this->load->model('B_requests');
        $this->load->model('B_db');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('organmember', $command, get_client_ip(),50,50)) {

            if ($command == "getuser_damagefile")
            {//register marketer

                $organtoken = checkorgantoken($organ_token_str);
               
                if($organtoken[0]=='ok') {
                    $user_id = $this->post("user_id");

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

                        $query4 = " SELECT * FROM damagefile_img_tb,image_tb WHERE image_code=damagefile_img_image_code AND  damagefile_img_damagefile_id=" . $damagefile_id;
                        $result4 = $this->B_db->run_query($query4);
                        $output4 = array();
                        foreach ($result4 as $row4) {
                            $result1 = $this->B_db->get_image($row4['damagefile_img_image_code']);
                            $image = $result1[0];

                            $record4['image_url'] =  $image['image_url'];
                            $record4['image_tumb_url'] = $image['image_tumb_url'];
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


                }
              
               else{
                    echo json_encode(array('result'=>$organtoken[0]
                    ,"data"=>$organtoken[1]
                    ,'desc'=>$organtoken[2]));
                }


            } else
                if ($command=="getuser_damagefile_detail")
        {//register marketer

            $organtoken=checkorgantoken($organ_token_str);
            $damagefile_id = $this->post("damagefile_id");
            if($organtoken[0]=='ok') {

                $query = "select * from damagefile_tb,evaluatorco_tb,damagefile_state,fielddamagefile_tb,user_tb where user_id=damagefile_user_id AND fielddamagefile_id=damagefile_fielddamagefile_id AND damagefile_state.damagefile_state_id=damagefile_last_state_id AND evaluatorco_id=damagefile_evaluatorco_id AND damagefile_id=$damagefile_id ";


                $query .= ' ORDER BY damagefile_id  DESC';


                $result = $this->B_db->run_query($query);


                $output = array();
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
                echo json_encode(array('result' => "ok"
                , "data" => $output
                , 'desc' => 'لیست درخواست ها با موفقیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


            }

            else{
                echo json_encode(array('result'=>$organtoken[0]
                ,"data"=>$organtoken[1]
                ,'desc'=>$organtoken[2]));
            }
        }

    }
        }
}
