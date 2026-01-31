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
class Agentrequestreport extends REST_Controller
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
        if (isset($this->input->request_headers()['Authorization'])) $agent_token_str = $this->input->request_headers()['Authorization'];
        $this->load->model('B_user');
        $this->load->model('B_db');
        $this->load->model('B_agent');
        $this->load->helper('my_helper');
        $this->load->helper('time_helper');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('agentrequestreport', $command, get_client_ip(),50,50)) {
            if ($command == "getagent_request") {//register marketer

                $agenttoken = checkagenttoken($agent_token_str);
                $approvalmode = $this->post('approvalmode');
                if ($agenttoken[0] == 'ok') {
                    $agent_id = $agenttoken[1];

                    $query1 = "select * from request_tb,company_tb,request_state,fieldinsurance_tb,user_tb where user_id=request_user_id AND fieldinsurance=request_fieldinsurance AND request_state_id=request_last_state_id AND company_id=request_company_id AND request_agent_id=" . $agent_id;
                    $query2 = "select count(*) AS cnt from request_tb,company_tb,request_state,fieldinsurance_tb,user_tb where user_id=request_user_id AND fieldinsurance=request_fieldinsurance AND request_state_id=request_last_state_id AND company_id=request_company_id AND request_agent_id=" . $agent_id;
                    $query="";
                    if ($approvalmode == 'notchecked') {
                        $query .= " AND (request_last_state_id=10 OR request_last_state_id=11)";
                        $query .= " AND request_id NOT IN (SELECT request_financial_approval_request_id AS request_id FROM request_financial_approval_tb ) ";
                    } else if ($approvalmode == 'notapprov') {
                        $query .= " AND (request_last_state_id=10 OR request_last_state_id=11)";
                        $query .= " AND request_id IN (SELECT request_financial_approval_request_id AS request_id FROM request_financial_approval_tb WHERE request_financial_approval=0 ) ";
                    } else if ($approvalmode == 'checkedfinancial') {
                        $query .= " AND (request_last_state_id=10 OR request_last_state_id=11) ";
                        $query .= " AND request_id IN (SELECT request_financial_approval_request_id AS request_id FROM request_financial_approval_tb WHERE request_financial_approval=1 ) ";
                        $query .= " AND request_id NOT IN (SELECT request_financial_paying_request_id AS request_id FROM request_financial_paying_tb  ) ";
                    } else if ($approvalmode == 'progresspaing') {
                        $query .= " AND (request_last_state_id=10 OR request_last_state_id=11)";
                        $query .= " AND request_id IN (SELECT request_financial_approval_request_id AS request_id FROM request_financial_approval_tb WHERE request_financial_approval=1 ) ";
                        $query .= " AND request_id IN (SELECT request_financial_paying_request_id AS request_id FROM request_financial_paying_tb,request_financial_doc_tb  WHERE request_financial_doc_id=request_financial_paying_doc_id AND request_financial_doc=0 ) ";

                    } else if ($approvalmode == 'payed') {
                        $query .= " AND (request_last_state_id=10 OR request_last_state_id=11)";
                        $query .= " AND request_id IN (SELECT request_financial_approval_request_id AS request_id FROM request_financial_approval_tb WHERE request_financial_approval=1 ) ";
                        $query .= " AND request_id IN (SELECT request_financial_paying_request_id AS request_id FROM request_financial_paying_tb,request_financial_doc_tb  WHERE request_financial_doc_id=request_financial_paying_doc_id AND request_financial_doc=1 ) ";

                    }
                    $query .= ' ORDER BY request_id  DESC ';

                    $limit = $this->post("limit");
                    $offset = $this->post("offset");
                    $limit_state ="";
                    if($limit!="" & $offset!="") {
                        $limit_state = " LIMIT " . $offset . "," . $limit;
                    }

                    $result = $this->B_db->run_query($query1.$query.$limit_state);
                    $count  = $this->B_db->run_query($query2.$query);

                    $output = array();
                    $request_id = '';
                    foreach ($result as $row) {
                        $record = array();
                        $record['request_id'] = $row['request_id'];
                        $request_id = $row['request_id'];
                        $record['user_id'] = $row['user_id'];
                        $record['user_name'] = $row['user_name'];
                        $record['user_family'] = $row['user_family'];
                        $record['user_mobile'] = $row['user_mobile'];
                        $record['fieldinsurance_logo_url'] = IMGADD . $row['fieldinsurance_logo_url'];
                        $record['fieldinsurance_id'] = $row['fieldinsurance_id'];
                        $record['request_fieldinsurance_fa'] = $row['fieldinsurance_fa'];
                        $record['request_description'] = $row['request_description'];
                        $record['request_last_state_id'] = $row['request_last_state_id'];
                        $record['request_last_state_name'] = $row['request_state_name'];
                        //**************************************************************************************
                        $record['request_organ']=IMGADD.$row['request_organ'];
                        if($row['request_organ']=='1'){
                            $query200="select * from organ_request_tb,organ_contract_tb,organ_tb where organ_contract_organ_id=organ_id  AND organ_contract_id=organ_request_contract_id AND organ_request_request_id=".$request_id;
                            $result200 = $this->B_db->run_query($query200);
                            if(!empty($result200)) {
                                $organ = $result200[0];
                                $record['organ_id']=$organ['organ_id'];
                                $record['organ_name']=$organ['organ_name'];
                                $record['organ_contract_num']=$organ['organ_contract_num'];

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
                        }
                        //**************************************************************************************

                        //*************************************************************************************************************
//                        $query0 = " SELECT * FROM user_address_tb,state_tb,city_tb WHERE user_address_state_id=state_id AND user_address_city_id=city_id AND user_address_id=" . $row['request_adderss_id'];
//                        $result0 = $this->B_db->run_query($query0);
//                        $output0 = array();
//                        foreach ($result0 as $row0) {
//
//                            $record0['user_address_state'] = $row0['state_name'];
//                            $record0['user_address_city'] = $row0['city_name'];
//                            $record0['user_address_str'] = $row0['user_address_str'];
//                            $record0['user_address_code'] = $row0['user_address_code'];
//                            $record0['user_address_name'] = $row0['user_address_name'];
//                            $record0['user_address_mobile'] = $row0['user_address_mobile'];
//                            $record0['user_address_tell'] = $row0['user_address_tell'];
//                            $output0[] = $record0;
//                        }
//                        $record['request_adderss'] = $output0;

                        //*************************************************************************************************************
//*************************************************************************************************************
//                        $query01 = " SELECT * FROM user_address_tb,state_tb,city_tb WHERE user_address_state_id=state_id AND user_address_city_id=city_id AND user_address_id=" . $row['request_addressofinsured_id'];
//                        $result01 = $this->B_db->run_query($query01);
//                        $output01 = array();
//                        foreach ($result01 as $row01) {
//
//                            $record01['user_address_state'] = $row01['state_name'];
//                            $record01['user_address_city'] = $row01['city_name'];
//                            $record01['user_address_str'] = $row01['user_address_str'];
//                            $record01['user_address_code'] = $row01['user_address_code'];
//                            $record01['user_address_name'] = $row01['user_address_name'];
//                            $record01['user_address_mobile'] = $row01['user_address_mobile'];
//                            $record01['user_address_tell'] = $row01['user_address_tell'];
//                            $output01[] = $record01;
//                        }
//                        $record['request_addressofinsured'] = $output01;

                        //*************************************************************************************************************

                        $query0 = "select sum(user_pey_amount) AS overpayment from user_pey_tb where user_pey_mode='overpayment' AND user_pey_request_id=" . $request_id;
                        $result0 = $this->B_db->run_query($query0);
                        $user_pey0 = $result0[0];
                        $overpayment = $user_pey0['overpayment'];

                        $query1 = "select sum(user_pey_amount) AS sumpey from user_pey_tb where not (user_pey_mode='overpayment') AND user_pey_request_id=" . $request_id." ORDER BY user_pey_request_id";
                        $result1 = $this->B_db->run_query($query1);
                        $user_pey = $result1[0];
                        $record['user_pey_amount'] = $user_pey['sumpey'] - $overpayment;


                        $query2 = "select sum(user_pey_amount) AS sumcash from user_pey_tb where not (user_pey_mode = 'instalment')  AND  not (user_pey_mode='overpayment') AND user_pey_request_id=" . $request_id;
                        $result2 = $this->B_db->run_query($query2);
                        $user_pey2 = $result2[0];
                        $record['user_pey_cash'] = $user_pey2['sumcash'] - $overpayment;

                        $query20 = "select sum(user_pey_amount) AS suminstalment from user_pey_tb where user_pey_mode = 'instalment' AND user_pey_request_id=" . $request_id;
                        $result20 = $this->B_db->run_query($query20);
                        $user_pey20 = $result20[0];
                        $record['user_pey_instalment'] = $user_pey20['suminstalment'];

//                        $query1 = "select * from user_pey_tb,instalment_check_tb where user_pey_mode='instalment' AND instalment_check_user_pey_id=user_pey_id AND user_pey_request_id=" . $request_id;
//                        $result1 = $this->B_db->run_query($query1);
//                        $output1 = array();
//
//                        foreach ($result1 as $row1) {
//                            $record1['user_pey_id'] = $row1['user_pey_id'];
//                            $record1['user_pey_amount'] = $row1['user_pey_amount'];
//                            $record1['instalment_check_num'] = $row1['instalment_check_num'];
//                            $record1['instalment_check_date'] = $row1['instalment_check_date'];
//                            $record1['user_pey_desc'] = $row1['user_pey_desc'];
//                            $record1['user_pey_image_code'] = $row1['user_pey_image_code'];
//
//                            $query11=" SELECT * FROM image_tb WHERE image_code='".$row1['user_pey_image_code']."'";
//                            $result11=$this->B_db->run_query($query11);
//                            if(!empty($result11))
//                                $image=$result11[0];
//                            else
//                                $image=array();
//                            if($image['image_tumb_url']==null){ $record1['user_pey_image_turl']=null;}else{ $record1['user_pey_image_turl']=IMGADD.$image['image_tumb_url'];}
//                            if($image['image_url']==null){ $record1['user_pey_image_url']=null;}else{$record1['user_pey_image_url']=IMGADD.$image['image_url'];}
//
//                            $output1[] = $record1;
//
//                        }
//                        $record['user_pey_detail'] = $output1;


                        //***************************************************************************************************************
                        $query17 = "select * from state_request_tb where staterequest_request_id=" . $request_id . " ORDER BY staterequest_id DESC LIMIT 1 ";
                        $result17 = $this->B_db->run_query($query17);
                        $state_request17 = $result17[0];
                        $record['staterequest_last_timestamp'] = $state_request17['staterequest_timestamp'];

                        //***************************************************************************************************************
//                        $query7 = "select * from state_request_tb,request_state where request_state_id=staterequest_state_id AND staterequest_request_id=" . $request_id;
//                        $result7 = $this->B_db->run_query($query7);
//                        $output7 = array();
//                        foreach ($result7 as $row7) {
//
//                            $record7['staterequest_id'] = $row7['staterequest_id'];
//                            //  $record7['staterequest_state_id']=$row7['staterequest_state_id'];
//                            $record7['request_state_name'] = $row7['request_state_name'];
//                            $record7['staterequest_timestamp'] = $row7['staterequest_timestamp'];
//                            $record7['staterequest_desc'] = $row7['staterequest_desc'];
//                            // $record7['staterequest_agent_id']=$row7['staterequest_agent_id'];
//
//                            if ($row7['staterequest_agent_id']) {
//                                $query71 = " SELECT * FROM agent_tb WHERE agent_id =" . $row7['staterequest_agent_id'];
//                                $result71 = $this->B_db->run_query($query71);
//                                $agent = $result71[0];
//                                if ($agent['agent_code'] == null) {
//                                    $record7['agent_code'] = null;
//                                } else {
//                                    $record7['agent_code'] = $agent['agent_code'];
//                                }
//                                if ($agent['agent_name'] == null) {
//                                    $record7['agent_name'] = null;
//                                } else {
//                                    $record7['agent_name'] = $agent['agent_name'];
//                                }
//                                if ($agent['agent_family'] == null) {
//                                    $record7['agent_family'] = null;
//                                } else {
//                                    $record7['agent_family'] = $agent['agent_family'];
//                                }
//                            }
//
//                            if ($row7['staterequest_employee_id']&&$row7['staterequest_employee_id']!=0) {
//                                $query71 = " SELECT * FROM employee_tb WHERE employee_id =" . $row7['staterequest_employee_id'];
//                                $result71 = $this->B_db->run_query($query71);
//                                $agent = $result71[0];
//
//                                if ($agent['employee_name'] == null) {
//                                    $record7['employee_name'] = null;
//                                } else {
//                                    $record7['employee_name'] = $agent['employee_name'];
//                                }
//                                if ($agent['employee_family'] == null) {
//                                    $record7['employee_family'] = null;
//                                } else {
//                                    $record7['employee_family'] = $agent['employee_family'];
//                                }
//                            }else{
//                                $record7['employee_name'] = null;
//                                $record7['employee_family'] = null;
//                            }
//
//                            $output7[] = $record7;
//                        }
//                        $record['request_stats'] = $output7;

                        //***************************************************************************************************************
                        //***************************************************************************************************************
                        $query6=" SELECT * FROM requst_ready_tb,request_ready_clearing_mode_tb WHERE request_ready_clearing_id=request_ready_clearing_mode_id AND  requst_ready_request_id=".$request_id;
                        $result6 = $this->B_db->run_query($query6);
                        $output6 = array();
                        foreach ($result6 as $row6) {
//
                            $record6['requst_ready_start_date'] = $row6['requst_ready_start_date'];
                            $record6['requst_ready_end_date'] = $row6['requst_ready_end_date'];
                            $record6['requst_ready_end_price'] = $row6['requst_ready_end_price'];
//                            $record6['requst_ready_num_ins'] = $row6['requst_ready_num_ins'];
//                            $record6['requst_ready_code_yekta'] = $row6['requst_ready_code_yekta'];
//                            $record6['requst_ready_code_rayane'] = $row6['requst_ready_code_rayane'];
//                            $record6['requst_ready_name_insurer'] = $row6['requst_ready_name_insurer'];
//                            $record6['requst_ready_code_insurer'] = $row6['requst_ready_code_insurer'];
//                            $record6['requst_ready_code_penalty'] = $row6['requst_ready_code_penalty'];
//                            $record6['request_ready_clearing_mode_name']=$row6['request_ready_clearing_mode_name'];
//                            $record6['request_ready_clearing_id']=$row6['request_ready_clearing_id'];
//                            $record6['requst_suspend_desc'] = $row6['requst_suspend_desc'];
//
//                            //*************************************************************************************************************
//                            $query61 = " SELECT * FROM requst_ready_image_tb,image_tb WHERE image_code=requst_ready_image_code AND requst_ready_request_id=" . $request_id;
//                            $result61 = $this->B_db->run_query($query61);
//                            $output61 = array();
//                            foreach ($result61 as $row61) {
//
//                                $record61['image_url'] = IMGADD . $row61['image_url'];
//                                $record61['image_tumb_url'] = IMGADD . $row61['image_tumb_url'];
//                                $record61['image_name'] = $row61['image_name'];
//                                $record61['image_desc'] = $row61['image_desc'];
//                                $output61[] = $record61;
//                            }
//                            $record6['request_ready_image_tb'] = $output61;
//
//                            //*************************************************************************************************************
//                            $query62 = " SELECT * FROM request_file_tb WHERE request_file_request_id=" . $request_id;
//                            $result62 = $this->B_db->run_query($query62);
//                            $output62 = array();
//                            foreach ($result62 as $row62) {
//
//                                $record62['request_file_url'] = IMGADD . $row62['request_file_url'];
//                                $record62['request_file_desc'] = $row62['request_file_desc'];
//                                $output62[] = $record62;
//                            }
//                            $record6['request_ready_file_tb'] = $output62;
//
//                            //*************************************************************************************************************    $output6[]=$record6;
                            $output6[] = $record6;
                        }
                        $record['request_ready'] = $output6;

                        //***************************************************************************************************************

                        //***************************************************************************************************************
//                        $query5 = " SELECT * FROM request_delivered_tb,state_tb,city_tb,delivery_mode_tb WHERE delivery_mode_id=request_delivered_mode_id AND state_id=request_delivered_state_id AND city_id=request_delivered_city_id AND request_delivered_request_id=" . $row['request_id'];
//                        $result5 = $this->B_db->run_query($query5);
//                        $output5 = array();
//                        foreach ($result5 as $row5) {
//
//                            $record5['request_delivered_timesatmp'] = $row5['request_delivered_timesatmp'];
//                            $record5['request_delivered_mode'] = $row5['delivery_mode_name'];
//                            $record5['request_delivered_dsc'] = $row5['request_delivered_dsc'];
//                            $record5['request_delivered_state'] = $row5['state_name'];
//                            $record5['request_delivered_city'] = $row5['city_name'];
//
//                            $result51 = $this->B_db->get_image($row['request_delivered_receipt_image_code']);
//                            $image = $result51[0];
//
//                            if ($image['image_tumb_url'] == null) {
//                                $record5['user_pey_image_turl'] = null;
//                            } else {
//                                $record5['user_pey_image_turl'] = IMGADD . $image['image_tumb_url'];
//                            }
//                            if ($image['image_url'] == null) {
//                                $record5['user_pey_image_url'] = null;
//                            } else {
//                                $record5['user_pey_image_url'] = IMGADD . $image['image_url'];
//                            }
//
//                            $output5[] = $record5;
//                        }
//                        $record['request_delivered'] = $output5;

                        //***************************************************************************************************************

//                        $query4 = " SELECT * FROM request_img_tb,image_tb WHERE image_id=request_img_image_code AND request_img_request_id=" . $request_id;
//                        $result4 = $this->B_db->run_query($query4);
//                        $output4 = array();
//                        foreach ($result4 as $row4) {
//                            $record4['image_url'] = IMGADD . $row4['image_url'];
//                            $record4['image_tumb_url'] = IMGADD . $row4['image_tumb_url'];
//                            $record4['image_name'] = $row4['image_name'];
//                            $record4['image_desc'] = $row4['image_desc'];
//                            $output4[] = $record4;
//                        }
//                        $record['request_image'] = $output4;
                        //***************************************************************************************
//                        $query44=" SELECT * FROM request_visit_tb,user_tb WHERE request_visit_user_id=user_id AND  request_visit_request_id=".$request_id;
//                        $result44 = $this->B_db->run_query($query44);
//                        $output44 =array();
//                        foreach($result44 as $row44)
//                        {
//                            $record44['request_visit_vedio_url']=IMGADD.$row44['request_visit_vedio_url'];
//                            $record44['request_visit_id']=$row44['request_visit_id'];
//                            $record44['request_visit_user_id']=$row44['request_visit_user_id'];
//                            $record44['user_name']=$row44['user_name'];
//                            $record44['user_family']=$row44['user_family'];
//                            $record44['user_mobile']=$row44['user_mobile'];
//                            //**********************************************************
//                            $query40=" SELECT * FROM request_visit_image_tb,image_tb  WHERE  request_visit_image_code=image_code AND request_visit_image_visit_id=".$row44['request_visit_id'];
//
//                            $result40 = $this->B_db->run_query($query40);
//                            $output40 =array();
//                            foreach($result40 as $row40)
//                            {
//                                $record40['image_url']=IMGADD.$row40['image_url'];
//                                $record40['image_tumb_url']=IMGADD.$row40['image_tumb_url'];
//                                $record40['image_name']=$row40['image_name'];
//                                $record40['image_desc']=$row40['image_desc'];
//                                $output40[]=$record40;
//
//                            }
//                                $record44['images_visit']=$output40;
//
//                            //**********************************************************
//                            $output44[]=$record44;
//                        }
//                        $record['visit_image']=$output44;
                        //***************************************************************************************************************

//***************************************************************************************************************

                        $query8 = " SELECT * FROM request_financial_approval_tb,employee_tb WHERE request_financial_approval_employee_id=employee_id AND request_financial_approval_request_id=" . $request_id;
                        $result8 = $this->B_db->run_query($query8);
                        $output8 = array();
                        foreach ($result8 as $row8) {

                            $record8['request_financial_approval_id'] = $row8['request_financial_approval_id'];
                            $record8['request_financial_approval_employee_id'] = $row8['request_financial_approval_employee_id'];
                            $record8['request_financial_approval_date'] = $row8['request_financial_approval_date'];
                            $record8['request_financial_approval_desc'] = $row8['request_financial_approval_desc'];
                            $record8['request_financial_approval'] = $row8['request_financial_approval'];
                            $record8['request_financial_approval_price'] = $row8['request_financial_approval_price'];
                            $record8['request_financial_approval_difference_price'] = $row8['request_financial_approval_difference_price'];
                            $record8['employee_name'] = $row8['employee_name'];
                            $record8['employee_family'] = $row8['employee_family'];
                            $record8['employee_mobile'] = $row8['employee_mobile'];
                            $output8[] = $record8;
                        }
                        $record['request_financial_approval'] = $output8;
                        //***************************************************************************************************************
                        //***************************************************************************************************************

                        $query9 = " SELECT * FROM request_financial_doc_tb,employee_tb,request_financial_paying_tb WHERE
		 request_financial_doc_employee_id=employee_id AND
		 request_financial_doc_id=request_financial_paying_doc_id AND
		  request_financial_paying_request_id=" . $request_id;
                        $result9 = $this->B_db->run_query($query9);
                        $output9 = array();
                        foreach ($result9 as $row9) {

                            $record9['request_financial_doc_id'] = $row9['request_financial_doc_id'];
                            $record9['request_financial_doc_price'] = $row9['request_financial_doc_price'];
                            $record9['request_financial_doc_num'] = $row9['request_financial_doc_num'];
                            $record9['request_financial_doc_numdoc'] = $row9['request_financial_doc_numdoc'];
                            $record9['request_financial_doc_date'] = $row9['request_financial_doc_date'];
                            $record9['request_financial_doc'] = $row9['request_financial_doc'];
                            $record9['request_financial_doc_peydate'] = $row9['request_financial_doc_peydate'];
                            $record9['request_financial_doc_code'] = $row9['request_financial_doc_code'];
                            $record9['request_financial_doc_employee_id'] = $row9['request_financial_doc_employee_id'];
                            $record9['employee_name'] = $row9['employee_name'];
                            $record9['employee_family'] = $row9['employee_family'];
                            $record9['employee_mobile'] = $row9['employee_mobile'];

                            //*************************************************************************************
                            $record9['request_financial_doc_pey_employee_id'] = $row9['request_financial_doc_pey_employee_id'];
                            $query91 = "select * from employee_tb where employee_id=" . $row9['request_financial_doc_pey_employee_id'] . "";
                            $result91 = $this->B_db->run_query($query91);
                            $employee = $result91[0];
                            $record9['pey_employee_name'] = $employee['employee_name'];
                            $record9['pey_employee_family'] = $employee['employee_family'];
                            $record9['pey_employee_mobile'] = $employee['employee_mobile'];
                            //*************************************************************************************


                            $output9[] = $record9;
                        }
                        $record['request_financial_doc'] = $output9;
                        //***************************************************************************************************************

                        //***************************************************************************************************************


                        $output[] = $record;

                    }
                    echo json_encode(array('result' => "ok"
                    , "data" => $output
                    ,"cnt"=>$count[0]['cnt']
                    , 'desc' => 'لیست درخواست ها با موفقیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                } else {
                    echo json_encode(array('result' => $agenttoken[0]
                    , "data" => $agenttoken[1]
                    , 'desc' => $agenttoken[2]));

                }


            } else
                if ($command=="getrequestbyid")
                {//register marketer
                    $agent_id=$this->post('agent_id') ;
                    $agenttoken = checkagenttoken($agent_token_str, 'view', 'financialpaing');
                    if($agenttoken[0]=='ok')
                    {
                        //***************************************************************************************************************
                        $agent_id=$this->post('agent_id') ;
                        $request_id=$this->post('request_id') ;



                        echo json_encode(array('result'=>"ok"
                        ,"data"=>get_request_agent($request_id)
                        ,'desc'=>' تغییر مرحله درخواست ثبت شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        //****************************************************************************************************************

                    }else{
                        echo json_encode(array('result'=>$agenttoken[0]
                        ,"data"=>$agenttoken[1]
                        ,'desc'=>$agenttoken[2]));
                    }
                }else

                    if ($command == "get_doc") {//register marketer
                $agenttoken = checkagenttoken($agent_token_str, 'view', 'financialpaing');
                if ($agenttoken[0] == 'ok') {


                    $query = "select * from request_financial_doc_tb,employee_tb where request_financial_doc_employee_id=employee_id AND ";
                    if ($_REQUEST['mode']) {
                        $mode = $this->post('mode');
                        if ($mode == 'doc_paying') {
                            $query .= " request_financial_doc=0 AND ";
                        } else if ($mode == 'doc_payed') {
                            $query .= " request_financial_doc=1 AND ";
                        }
                    }
                    $query .= " request_financial_doc_id IN (
		 select request_financial_paying_doc_id from request_financial_paying_tb,request_tb
		 where request_financial_paying_request_id=request_id AND request_agent_id=" . $agenttoken[1] . ") ";

                    $result = $this->B_db->run_query($query);
                    $output = array();
                    foreach ($result as $row) {
                        $record = array();
                        $record['request_financial_doc_id'] = $row['request_financial_doc_id'];
                        $record['request_financial_doc_num'] = $row['request_financial_doc_num'];
                        $record['request_financial_doc_price'] = $row['request_financial_doc_price'];
                        $record['request_financial_doc_numdoc'] = $row['request_financial_doc_numdoc'];
                        $record['request_financial_doc_date'] = $row['request_financial_doc_date'];
                        $record['request_financial_doc'] = $row['request_financial_doc'];
                        $record['request_financial_doc_peydate'] = $row['request_financial_doc_peydate'];
                        $record['request_financial_doc_code'] = $row['request_financial_doc_code'];
                        $record['request_financial_doc_employee_id'] = $row['request_financial_doc_employee_id'];

                        $record['employee_name'] = $row['employee_name'];
                        $record['employee_family'] = $row['employee_family'];
                        $record['employee_mobile'] = $row['employee_mobile'];
                        //*************************************************************************************
                        $record['request_financial_doc_pey_employee_id'] = $row['request_financial_doc_pey_employee_id'];
                        $query1 = "select * from employee_tb where employee_id=" . $row['request_financial_doc_pey_employee_id'] . "";
                        $result1 = $this->B_db->run_query($query1);
                        $employee = $result1[0];
                        $record['pey_employee_name'] = $employee['employee_name'];
                        $record['pey_employee_family'] = $employee['employee_family'];
                        $record['pey_employee_mobile'] = $employee['employee_mobile'];
                        //*************************************************************************************
                        $query2 = "select * from request_financial_doc_tb,request_financial_paying_tb,request_tb,agent_tb where
		  request_financial_doc_id=request_financial_paying_doc_id
		  AND request_financial_paying_request_id=request_id
		  AND request_agent_id=agent_id
		  AND  request_financial_doc_id=" . $row['request_financial_doc_id'] . "";
                        $result2 = $this->B_db->run_query($query2);
                        $agent = $result2[0];
                        $record['agent_id'] = $agent['agent_id'];
                        $record['agent_name'] = $agent['agent_name'];
                        $record['agent_family'] = $agent['agent_family'];
                        $record['agent_mobile'] = $agent['agent_mobile'];
                        $record['agent_banknum'] = $agent['agent_banknum'];
                        $record['agent_bankname'] = $agent['agent_bankname'];
                        $record['agent_banksheba'] = $agent['agent_banksheba'];
                        //************************************************************************************
                        $record['ssssssss'] = $query2;

                        $output[] = $record;
                    }
                    echo json_encode(array('result' => "ok"
                    , "data" => $output
                    , 'desc' => 'مشحصات اسناد با  موفقیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                } else {
                    echo json_encode(array('result' => $agenttoken[0]
                    , "data" => $agenttoken[1]
                    , 'desc' => $agenttoken[2]));

                }


            } else
                if ($command == "requestpending3") {//register marketer

                    $agenttoken = checkagenttoken($agent_token_str);
                    if ($agenttoken[0] == 'ok') {
                        $agent_id = $agenttoken[1];
//***************************************************************************************************************
                        $request_id = $this->post('request_id');

                        $query2 = "select * from request_tb where request_id=" . $request_id . "";
                        $result2 = $this->B_db->run_query($query2);
                        $request = $result2[0];
                        if ($request['request_last_state_id'] != 3) {
//***************************************************************************************************************

                            $query1 = "UPDATE request_tb SET request_last_state_id=3 WHERE request_id=$request_id";
                            $result1 = $this->B_db->run_query_put($query1);

                            $desc = 'در حال بررسی توسط نماینده';
                            $query = "INSERT INTO state_request_tb( staterequest_request_id, staterequest_state_id, staterequest_timestamp,staterequest_desc,staterequest_agent_id,staterequest_employee_id) VALUES
                                        ( $request_id, 3, now(),'$desc',$agent_id,$agenttoken[3])";
                            $result = $this->B_db->run_query_put($query);

                            request_send_sms($request_id, 'user', $desc);

                            echo json_encode(array('result' => "ok"
                            , "data" => get_request_agent($request_id)
                            , 'desc' => ' تغییر مرحله درخواست ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                            //****************************************************************************************************************
                        } else {
                            echo json_encode(array('result' => "error1"
                            , "data" => ""
                            , 'desc' => ' درخواست نمیتواند به وضعیت در حال بررسی در آید '), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }

                    } else {
                        echo json_encode(array('result' => $agenttoken[0]
                        , "data" => $agenttoken[1]
                        , 'desc' => $agenttoken[2]));

                    }


                } else
                if ($command == "requestbackuser4") {//register marketer

                        $agenttoken = checkagenttoken($agent_token_str);
                        if ($agenttoken[0] == 'ok') {
                            $agent_id = $agenttoken[1];
//***************************************************************************************************************
                            $request_id = $this->post('request_id');
                            $request_backuser_desc = $this->post('request_backuser_desc');

                            $query2 = "select * from request_tb where request_id=" . $request_id . "";
                            $result2 = $this->B_db->run_query($query2);
                            $request = $result2[0];
                            $user_id = $request['request_user_id'];
                            if ($request['request_last_state_id'] != 4) {
//***************************************************************************************************************

                                $query1 = "UPDATE request_tb SET request_last_state_id=4 WHERE request_id=$request_id";
                                $result1 = $this->B_db->run_query_put($query1);

                                $desc = 'برگشت به کاربر به علت ' . $request_backuser_desc;
                                $query = "INSERT INTO state_request_tb( staterequest_request_id, staterequest_state_id, staterequest_timestamp,staterequest_desc,staterequest_agent_id,staterequest_employee_id) VALUES
                                        ( $request_id, 4, now(),'$desc',$agent_id,$agenttoken[3])";
                                $result = $this->B_db->run_query_put($query);

                                request_send_sms($request_id, 'user', $desc);


                                $query = "INSERT INTO request_backuser_tb( request_backuser_request_id, request_backuser_timestamp, request_backuser_desc, request_backuser_agent_id) VALUES
                                        ( $request_id, now(),'$request_backuser_desc',$agent_id)";
                                $result = $this->B_db->run_query_put($query);

//*****************************************************************************
                                $query2 = "DELETE FROM  instalment_check_tb WHERE instalment_check_request_id=$request_id ";
                                $result2 = $this->B_db->run_query_put($query2);

                                $query2 = "DELETE FROM  managdiscount_use_tb WHERE managdiscount_request_id=$request_id ";
                                $result2 = $this->B_db->run_query_put($query2);

                                $query2 = "DELETE FROM  discount_code_use_tb WHERE discount_code_use_request_id=$request_id ";
                                $result2 = $this->B_db->run_query_put($query2);

                                $query2="INSERT INTO user_wallet_tb ( user_wallet_user_id, user_wallet_amount, user_wallet_gift, user_wallet_mode, user_wallet_timestamp, user_wallet_detail, user_wallet_code, user_wallet_user_mobile)
SELECT user_wallet_user_id, user_wallet_amount, user_wallet_gift, 'add', now(), ' بازگشت به کیف پول به علت برگشت درخواست $request_id' , user_wallet_code, user_wallet_user_mobile
FROM user_wallet_tb
WHERE user_wallet_code=$request_id AND user_wallet_mode='get' ";
                                $result2=$this->B_db->run_query_put($query2);

                                $query2 = "select * from user_pey_tb where user_pey_mode='cash' AND user_pey_request_id=$request_id ";
                                $result2 = $this->B_db->run_query($query2);
                                foreach ($result2 as $row) {
                                    $desc = 'بازگشت پرداختی نقدی با شماره پیگیری' . $row['user_pey_code'] . ' و جزئیات ' . $row['user_pey_desc'];
                                    $query3 = "INSERT INTO user_wallet_tb(user_wallet_user_id,user_wallet_amount,  user_wallet_mode, user_wallet_timestamp, user_wallet_detail, user_wallet_code) VALUES
		                                   (  $user_id       ,'" . $row['user_pey_amount'] . "','add',   now()                    ,'$desc' ,'" . $row['user_pey_code'] . "' )";
                                    $result3 = $this->B_db->run_query_put($query3);
                                }
                                $query2 = "DELETE FROM  user_pey_tb WHERE user_pey_request_id=$request_id ";
                                $result2 = $this->B_db->run_query_put($query2);

//*****************************************************************************


                                echo json_encode(array('result' => "ok"
                                , "data" => get_request_agent($request_id)
                                , 'desc' => ' تغییر مرحله درخواست ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                //****************************************************************************************************************
                            } else {
                                echo json_encode(array('result' => "error11"
                                , "data" => ""
                                , 'desc' => ' درخواست نمیتواند به کاربر برگشت داده شود '), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }

                        } else {
                            echo json_encode(array('result' => $agenttoken[0]
                            , "data" => $agenttoken[1]
                            , 'desc' => $agenttoken[2]));

                        }


                    } else
                if ($command == "requestbackagent5") {//register marketer

                            $agenttoken = checkagenttoken($agent_token_str);
                            if ($agenttoken[0] == 'ok') {
                                $agent_id = $agenttoken[1];
//***************************************************************************************************************
                                $request_id = $this->post('request_id');
                                $request_backagent_desc = $this->post('request_backagent_desc');

                                $query2 = "select * from request_tb where request_id=" . $request_id . "";
                                $result2 = $this->B_db->run_query($query2);
                                $request = $result2[0];
                                if ($request['request_last_state_id'] != 5) {
//***************************************************************************************************************
                                    $newagent_id = 2;
                                    $query1 = "UPDATE request_tb SET request_last_state_id=2 ,request_agent_id=$newagent_id WHERE request_id=$request_id";
                                    $result1 = $this->B_db->run_query_put($query1);

                                    $desc = ' درخواست به نماینده دیگر ارجاع داده شد به علت' . $request_backagent_desc;
                                    $query = "INSERT INTO state_request_tb( staterequest_request_id, staterequest_state_id, staterequest_timestamp,staterequest_desc,staterequest_agent_id,staterequest_employee_id) VALUES
                                        ( $request_id, 5, now(),'$desc',$agent_id,$agenttoken[3])";
                                    $result = $this->B_db->run_query_put($query);

                                    $query = "INSERT INTO request_backagent_tb( request_backagent_request_id, request_backagent_timestamp, request_backagent_desc, request_backagent_agent_id) VALUES
                                        ( $request_id, now(),'$request_backagent_desc',$agent_id)";
                                    $result = $this->B_db->run_query_put($query);

                                    echo json_encode(array('result' => "ok"
                                    , "data" => get_request_agent($request_id)
                                    , 'desc' => ' تغییر مرحله درخواست ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                    //****************************************************************************************************************
                                } else {
                                    echo json_encode(array('result' => "error111"
                                    , "data" => ""
                                    , 'desc' => ' درخواست نمیتواند به وضعیت برگشت به نماینده دیگر در آید '), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                }

                            } else {
                                echo json_encode(array('result' => $agenttoken[0]
                                , "data" => $agenttoken[1]
                                , 'desc' => $agenttoken[2]));

                            }

                        } else
                if ($command == "requestdepositdeficit6") {//register marketer

                                $agenttoken = checkagenttoken($agent_token_str);
                                if ($agenttoken[0] == 'ok') {
                                    $agent_id = $agenttoken[1];
//***************************************************************************************************************
                                    $request_id = $this->post('request_id');
                                    $deficit_pey_reason = $this->post('deficit_pey_reason');
                                    $deficit_pey_amount = $this->post('deficit_pey_amount');

                                    $query2 = "select * from request_tb where request_id=" . $request_id . "";
                                    $result2 = $this->B_db->run_query($query2);
                                    $request = $result2[0];
                                    if ($request['request_last_state_id'] != 6) {
//***************************************************************************************************************
                                        $query1 = "UPDATE request_tb SET request_last_state_id=6 WHERE request_id=$request_id";
                                        $result1 = $this->B_db->run_query_put($query1);

                                        $desc = '  کسری واریزی  به علت' . $deficit_pey_reason . ' و مبلغ کسری واریزی ' . $deficit_pey_amount . '  ریال ';
                                        $query = "INSERT INTO state_request_tb( staterequest_request_id, staterequest_state_id, staterequest_timestamp,staterequest_desc,staterequest_agent_id,staterequest_employee_id) VALUES
                                        ( $request_id, 6, now(),'$desc',$agent_id,$agenttoken[3])";
                                        $result = $this->B_db->run_query_put($query);

                                        request_send_sms($request_id, 'user', $desc);

                                        $query = "INSERT INTO deficit_pey_tb(deficit_pey_request_id, deficit_pey_amount, deficit_pey_reason) VALUES
                                        ( $request_id, $deficit_pey_amount,'$deficit_pey_reason')";
                                        $result = $this->B_db->run_query_put($query);

                                        echo json_encode(array('result' => "ok"
                                        , "data" => get_request_agent($request_id)
                                        , 'desc' => ' تغییر مرحله درخواست ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                        //****************************************************************************************************************
                                    } else {
                                        echo json_encode(array('result' => "error1111"
                                        , "data" => ""
                                        , 'desc' => ' درخواست نمیتواند به وضعیت کسری واریزی در آید '), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                    }

                                } else {
                                    echo json_encode(array('result' => $agenttoken[0]
                                    , "data" => $agenttoken[1]
                                    , 'desc' => $agenttoken[2]));

                                }


                            } else
                if ($command == "requestdeletedepositdeficit6") {//register marketer

                                    $agenttoken = checkagenttoken($agent_token_str);
                                    if ($agenttoken[0] == 'ok') {
                                        $agent_id = $agenttoken[1];
//***************************************************************************************************************
                                        $request_id = $this->post('request_id');
                                        $deficit_pey_reason = $this->post('deficit_pey_reason');
                                        $deficit_pey_amount = $this->post('deficit_pey_amount');

                                        $query2 = "select * from request_tb where request_id=" . $request_id . "";
                                        $result2 = $this->B_db->run_query($query2);
                                        $request = $result2[0];
                                        if ($request['request_last_state_id'] == 6) {
//***************************************************************************************************************
                                            $query1 = "UPDATE request_tb SET request_last_state_id=3 WHERE request_id=$request_id";
                                            $result1 = $this->B_db->run_query_put($query1);

                                            $desc = ' حذف کسری واریزی ';
                                            $query = "INSERT INTO state_request_tb( staterequest_request_id, staterequest_state_id, staterequest_timestamp,staterequest_desc,staterequest_agent_id,staterequest_employee_id) VALUES
                                        ( $request_id, 3, now(),'$desc',$agent_id,$agenttoken[3])";
                                            $result = $this->B_db->run_query_put($query);
                                            request_send_sms($request_id, 'user', $desc);

                                            $query = "DELETE FROM deficit_pey_tb WHERE deficit_pey_request_id=$request_id";
                                            $result = $this->B_db->run_query_put($query);

                                            echo json_encode(array('result' => "ok"
                                            , "data" => get_request_agent($request_id)
                                            , 'desc' => ' تغییر مرحله درخواست ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                            //****************************************************************************************************************
                                        } else {
                                            echo json_encode(array('result' => "error11111"
                                            , "data" => ""
                                            , 'desc' => ' درخواست نمیتواند از وضعیت کسری واریزی حذف گردد  '), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                        }
                                    } else {
                                        echo json_encode(array('result' => $agenttoken[0]
                                        , "data" => $agenttoken[1]
                                        , 'desc' => $agenttoken[2]));

                                    }

                                } else
                if ($command == "requestsuspend7") {//register marketer

                                        $agenttoken = checkagenttoken($agent_token_str);
                                        if ($agenttoken[0] == 'ok') {
                                            $agent_id = $agenttoken[1];
//***************************************************************************************************************
                                            $request_id = $this->post('request_id');
                                            $requst_suspend_end_date = $this->post('requst_suspend_end_date');
                                            $requst_suspend_desc = $this->post('requst_suspend_desc');

                                            $query2 = "select * from request_tb where request_id=" . $request_id . "";
                                            $result2 = $this->B_db->run_query($query2);
                                            $request = $result2[0];
                                            if ($request['request_last_state_id'] != 7) {
//***************************************************************************************************************
                                                $query1 = "UPDATE request_tb SET request_last_state_id=7 WHERE request_id=$request_id";
                                                $result1 = $this->B_db->run_query_put($query1);

                                                $desc = '   معلق درآمد به علت ' . $requst_suspend_desc . ' و در تاریخ ' . $requst_suspend_end_date . 'صادر خواهد شد.';
                                                $query = "INSERT INTO state_request_tb( staterequest_request_id, staterequest_state_id, staterequest_timestamp,staterequest_desc,staterequest_agent_id,staterequest_employee_id) VALUES
                                        ( $request_id, 7, now(),'$desc',$agent_id,$agenttoken[3])";
                                                $result = $this->B_db->run_query_put($query);
                                                request_send_sms($request_id, 'user', $desc);

                                                $query = "INSERT INTO requst_suspend_tb(requst_suspend_request_id, requst_suspend_timestamp, requst_suspend_end_date, requst_suspend_desc, requst_suspend_agent_id) VALUES
                                        (     $request_id      ,         now()          , '$requst_suspend_end_date','$requst_suspend_desc',$agent_id)";
                                                $result = $this->B_db->run_query_put($query);

                                                echo json_encode(array('result' => "ok"
                                                , "data" => get_request_agent($request_id)
                                                , 'desc' => ' تغییر مرحله درخواست ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                                //****************************************************************************************************************
                                            } else {
                                                echo json_encode(array('result' => "error2"
                                                , "data" => ""
                                                , 'desc' => ' درخواست نمیتواند به تعلیق تا تاریخ صدور درآید'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                            }

                                        } else {
                                            echo json_encode(array('result' => $agenttoken[0]
                                            , "data" => $agenttoken[1]
                                            , 'desc' => $agenttoken[2]));

                                        }


                                    } else
                if ($command == "requestdifficult8") {//register marketer

                                            $agenttoken = checkagenttoken($agent_token_str);
                                            if ($agenttoken[0] == 'ok') {
                                                $agent_id = $agenttoken[1];
//***************************************************************************************************************
                                                $request_id = $this->post('request_id');
                                                $request_difficult_desc = $this->post('request_difficult_desc');

                                                $query2 = "select * from request_tb where request_id=" . $request_id . "";
                                                $result2 = $this->B_db->run_query($query2);
                                                $request = $result2[0];
                                                if ($request['request_last_state_id'] != 8) {
//***************************************************************************************************************
                                                    $query1 = "UPDATE request_tb SET request_last_state_id=8  WHERE request_id=$request_id";
                                                    $result1 = $this->B_db->run_query_put($query1);

                                                    $desc = ' معلق  برای صدور به علت ' . $request_difficult_desc;
                                                    $query = "INSERT INTO state_request_tb( staterequest_request_id, staterequest_state_id, staterequest_timestamp,staterequest_desc,staterequest_agent_id,staterequest_employee_id) VALUES
                                        ( $request_id, 8, now(),'$desc',$agent_id,$agenttoken[3])";
                                                    $result = $this->B_db->run_query_put($query);

                                                    request_send_sms($request_id, 'user', $desc);

                                                    $query = "INSERT INTO request_difficult_tb( request_difficult_request_id, request_difficult_timestamp, request_difficult_desc, request_difficult_agent_id) VALUES
                                        ( $request_id, now(),'$request_difficult_desc',$agent_id)";
                                                    $result = $this->B_db->run_query_put($query);

                                                    echo json_encode(array('result' => "ok"
                                                    , "data" => get_request_agent($request_id)
                                                    , 'desc' => ' تغییر مرحله درخواست ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                                    //****************************************************************************************************************
                                                } else {
                                                    echo json_encode(array('result' => "error"
                                                    , "data" => ""
                                                    , 'desc' => ' درخواست نمیتواند به وضعیت تعلیق به علت وجود مشکل در عکس ها در آید '), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                }

                                            } else {
                                                echo json_encode(array('result' => $agenttoken[0]
                                                , "data" => $agenttoken[1]
                                                , 'desc' => $agenttoken[2]));

                                            }


                                        } else
                if ($command == "requestoverpayment12") {//register marketer

                                                $agenttoken = checkagenttoken($agent_token_str);
                                                if ($agenttoken[0] == 'ok') {
                                                    $agent_id = $agenttoken[1];
//***************************************************************************************************************
                                                    $request_id = $this->post('request_id');
                                                    $user_id = $this->post('user_id');
                                                    $overpayment_reason = $this->post('overpayment_reason');
                                                    $overpayment_amount = $this->post('overpayment_amount');

//***************************************************************************************************************

                                                    $desc = 'درخواست، اضافه واریزی دارد به علت ' . $overpayment_reason . ' و مبلغ اضافه واریزی ' . $overpayment_amount . ' ریال است ';
                                                    $query = "INSERT INTO state_request_tb( staterequest_request_id, staterequest_state_id, staterequest_timestamp,staterequest_desc,staterequest_agent_id,staterequest_employee_id) VALUES
                                        ( $request_id, 3, now(),'$desc',$agent_id,$agenttoken[3])";

                                                    $result = $this->B_db->run_query_put($query);


                                                    $user_wallet_detail = 'واریز به کیف پول به علت اضافه واریزی سفارش کد' . $request_id . ' به علت ' . $overpayment_reason;
                                                    $query2 = "INSERT INTO user_wallet_tb( user_wallet_user_id, user_wallet_amount, user_wallet_mode, user_wallet_timestamp, user_wallet_detail, user_wallet_code) VALUES
                                  ($user_id             , $overpayment_amount   ,    'add'      ,now()               ,'$user_wallet_detail',$request_id)      ";
                                                    $result2 = $this->B_db->run_query_put($query2);
                                                    $user_wallet_id = $this->db->insert_id();

                                                    $user_pey_temp_desc = 'درخواست  به علت ' . $overpayment_reason . ' اضافه واریزی دارد و به کیف پول کاربر بازگشت داده میشود';
                                                    $query1 = "INSERT INTO user_pey_tb( user_pey_request_id, user_pey_amount, user_pey_mode, user_pey_code, user_pey_desc,user_pey_timestamp) VALUES
                                      (  $request_id           , $overpayment_amount, 'overpayment'    ,  $user_wallet_id   ,'$user_pey_temp_desc',now())      ";
                                                    $result1 = $this->B_db->run_query_put($query1);
                                                    $user_pey_id = count($result1[0]);


                                                    echo json_encode(array('result' => "ok"
                                                    , "data" => get_request_agent($request_id)
                                                    , 'desc' => ' تغییر مرحله درخواست ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                                    //****************************************************************************************************************


                                                } else {
                                                    echo json_encode(array('result' => $agenttoken[0]
                                                    , "data" => $agenttoken[1]
                                                    , 'desc' => $agenttoken[2]));

                                                }


                                            } else
                if ($command == "requestissuing9") {//register marketer

                                                    $agenttoken = checkagenttoken($agent_token_str);
                                                    if ($agenttoken[0] == 'ok') {
                                                        $agent_id = $agenttoken[1];
//***************************************************************************************************************
                                                        $request_id = $this->post('request_id');

                                                        $query2 = "select * from request_tb where request_id=" . $request_id . "";
                                                        $result2 = $this->B_db->run_query($query2);
                                                        $request = $result2[0];
                                                        if ($request['request_last_state_id'] != 9) {
//***************************************************************************************************************

                                                            $query1 = "UPDATE request_tb SET request_last_state_id=9 WHERE request_id=$request_id";
                                                            $result1 = $this->B_db->run_query_put($query1);

                                                            $desc = ' در حال صدور ';
                                                            $query = "INSERT INTO state_request_tb( staterequest_request_id, staterequest_state_id, staterequest_timestamp,staterequest_desc,staterequest_agent_id,staterequest_employee_id) VALUES
                                        ( $request_id, 9, now(),'$desc',$agent_id,$agenttoken[3])";
                                                            $result = $this->B_db->run_query_put($query);

                                                            request_send_sms($request_id, 'user', $desc);

                                                            echo json_encode(array('result' => "ok"
                                                            , "data" => get_request_agent($request_id)
                                                            , 'desc' => ' تغییر مرحله درخواست ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                                            //****************************************************************************************************************
                                                        } else {
                                                            echo json_encode(array('result' => "error222"
                                                            , "data" => ""
                                                            , 'desc' => ' درخواست نمیتواند به وضعیت در حال صدور در آید '), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                        }

                                                    } else {
                                                        echo json_encode(array('result' => $agenttoken[0]
                                                        , "data" => $agenttoken[1]
                                                        , 'desc' => $agenttoken[2]));

                                                    }


                                                } else
                if ($command == "requestready10") {//register marketer

                                                        $agenttoken = checkagenttoken($agent_token_str);
                                                        if ($agenttoken[0] == 'ok') {
                                                            $agent_id = $agenttoken[1];
//***************************************************************************************************************
                                                            $request_id = $this->post('request_id');
                                                            $requst_ready_start_date = $this->post('requst_ready_start_date');
                                                            $requst_ready_end_date = $this->post('requst_ready_end_date');
                                                            $requst_ready_end_price = $this->post('requst_ready_end_price');
                                                            $requst_ready_num_ins = $this->post('requst_ready_num_ins');
                                                            $requst_ready_code_yekta = $this->post('requst_ready_code_yekta');
                                                            $requst_ready_code_rayane = $this->post('requst_ready_code_rayane');
                                                            $requst_ready_name_insurer = $this->post('requst_ready_name_insurer');
                                                            $requst_ready_code_insurer = $this->post('requst_ready_code_insurer');
                                                            $request_ready_clearing_id = $this->post('request_ready_clearing_id');
                                                            $requst_suspend_desc = $this->post('requst_suspend_desc');
                                                            $requst_ready_code_penalty = $this->post('requst_ready_code_penalty');

                                                            $query2 = "select * from request_tb where request_id=" . $request_id . "";
                                                            $result2 = $this->B_db->run_query($query2);
                                                            $request = $result2[0];
                                                            if ($request['request_last_state_id'] != 10) {
//***************************************************************************************************************
                                                                $query="DELETE FROM requst_ready_tb WHERE requst_ready_request_id=$request_id";
                                                                $result = $this->B_db->run_query_put($query);


                                                                $query3 = "INSERT INTO requst_ready_tb( requst_ready_request_id, requst_ready_timestamp, requst_ready_start_date, requst_ready_end_date, requst_ready_end_price, requst_ready_num_ins, requst_ready_code_yekta,requst_ready_code_rayane,requst_ready_code_penalty, requst_ready_name_insurer, requst_ready_code_insurer, requst_suspend_desc, requst_suspend_agent_id,requst_ready_employee_id,request_ready_clearing_id) VALUES
                                                                                                      ( $request_id, now()                       ,'$requst_ready_start_date' ,'$requst_ready_end_date' ,'$requst_ready_end_price' ,'$requst_ready_num_ins','$requst_ready_code_yekta','$requst_ready_code_rayane','$requst_ready_code_penalty','$requst_ready_name_insurer','$requst_ready_code_insurer' ,'$requst_suspend_desc',$agent_id,$agenttoken[3],$request_ready_clearing_id)";
                                                                $requst_ready_id = $this->B_db->run_query_put($query3);
                                                                      if($requst_ready_id){
                                                                $query1 = "UPDATE request_tb SET request_last_state_id=10  WHERE request_id=$request_id";
                                                                $result1 = $this->B_db->run_query_put($query1);

                                                                $desc = ' صادر شده و آماده تحویل ';
                                                                $query2 = "INSERT INTO state_request_tb( staterequest_request_id, staterequest_state_id, staterequest_timestamp,staterequest_desc,staterequest_agent_id,staterequest_employee_id) VALUES
                                        ( $request_id, 10, now(),'$desc',$agent_id,$agenttoken[3])";
                                                                $result1 = $this->B_db->run_query_put($query2);
                                                                request_send_sms($request_id, 'user', $desc);
                                                                          survey_send_sms($request_id);
                                                                   //**********************************************************************************
                                                                $query4 = "select sum(user_pey_amount) AS overpayment from user_pey_tb where user_pey_mode='overpayment' AND user_pey_request_id=" . $request_id;
                                                                $result4 = $this->B_db->run_query($query4);
                                                                $user_pey4 = $result4[0];
                                                                $overpayment = $user_pey4['overpayment'];

                                                                $query5 = "select sum(user_pey_amount) AS sumcash from user_pey_tb where not (user_pey_mode = 'instalment')  AND  not (user_pey_mode='overpayment') AND user_pey_request_id=" . $request_id;
                                                                $result5 = $this->B_db->run_query($query5);
                                                                $user_pey5 = $result5[0];
                                                                $user_pey_cash = ($user_pey5['sumcash'] - $overpayment) * 100 / 109;
                                                                $this->B_db->peyback_decision($request_id, $user_pey_cash, 'add', 'نقد','main');
if(intval($requst_ready_code_penalty)>0){
    $this->B_db->peyback_decision($request_id, intval($requst_ready_code_penalty), 'get', 'مبلغی که شامل کارمزد نمیشود','nomain');

}

                                                                //**********************************************************************************

                                                                echo json_encode(array('result' => "ok"
                                                                , "data" => get_request_agent($request_id)
                                                                , 'desc' => ' تغییر مرحله درخواست ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}else{

    echo json_encode(array('result' => "error"
    , "data" => get_request($request_id)
    , 'desc' => ' تغییر مرحله درخواست ثبت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

}
                                                                //****************************************************************************************************************
                                                            } else {
                                                                echo json_encode(array('result' => "error"
                                                                , "data" => ""
                                                                , 'desc' => ' درخواست نمیتواند به وضعیت صادر شده و آماده تحویل در آید '), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                            }

                                                        } else {
                                                            echo json_encode(array('result' => $agenttoken[0]
                                                            , "data" => $agenttoken[1]
                                                            , 'desc' => $agenttoken[2]));

                                                        }
                                                    } else
                if ($command == "requestrevoke13") {//register marketer

                                                            $agenttoken = checkagenttoken($agent_token_str);
                                                            if ($agenttoken[0] == 'ok') {
                                                                $agent_id = $agenttoken[1];
//***************************************************************************************************************
                                                                $request_id = $this->post('request_id');
                                                                $request_revoke_date = $this->post('request_revoke_date');
                                                                $request_revoke_desc = $this->post('request_revoke_desc');
                                                                $request_revoke_price = $this->post('request_revoke_price');

                                                                $query2 = "select * from request_tb where request_id=" . $request_id . "";
                                                                $result2 = $this->B_db->run_query($query2);
                                                                $request = $result2[0];
                                                                if (($request['request_last_state_id'] == 10 || $request['request_last_state_id'] == 11) && $request['request_last_state_id'] != 13) {
//***************************************************************************************************************
                                                                    $query1 = "UPDATE request_tb SET request_last_state_id=13  WHERE request_id=$request_id";
                                                                    $result1 = $this->B_db->run_query_put($query1);

                                                                    $desc = 'بیمه نامه به علت ' . $request_revoke_desc . ' ابطال شده است و مبلغ' . $request_revoke_price . '  قیمت نهایی بیمه نامه است';
                                                                    $query = "INSERT INTO state_request_tb( staterequest_request_id, staterequest_state_id, staterequest_timestamp,staterequest_desc,staterequest_agent_id,staterequest_employee_id) VALUES
                                                                ( $request_id, 13, now(),'$desc',$agent_id,$agenttoken[3])";
                                                                    $result = $this->B_db->run_query_put($query);

                                                                    request_send_sms($request_id, 'user', $desc);

                                                                    $query = "INSERT INTO request_revoke_tb( request_revoke_request_id, request_revoke_date, request_revoke_desc, request_revoke_price, request_revoke_timestamp) VALUES 
                                                                                                  ( $request_id,           '$request_revoke_date' ,'$request_revoke_desc' ,'$request_revoke_price' ,now())";
                                                                    $result = $this->B_db->run_query_put($query);
                                                                    $requst_ready_id = count($result[0]);
                                                                    //**********************************************************************************
                                                                    $query0 = "select sum(user_pey_amount) AS overpayment from user_pey_tb where user_pey_mode='overpayment' AND user_pey_request_id=" . $request_id;
                                                                    $result0 = $this->B_db->run_query($query0);
                                                                    $user_pey0 = $result0[0];
                                                                    $overpayment = $user_pey0['overpayment'];

                                                                    $query2 = "select sum(user_pey_amount) AS sumcash from user_pey_tb where not (user_pey_mode = 'instalment')  AND  not (user_pey_mode='overpayment') AND user_pey_request_id=" . $request_id;
                                                                    $result2 = $this->B_db->run_query($query2);
                                                                    $user_pey2 = $result2[0];
                                                                    $user_pey_cash = $user_pey2['sumcash'] - $overpayment;

                                                                    $query2 = "select sum(instalment_check_amount) AS sumchckpassed from instalment_check_tb where 	instalment_check_pass=1 AND instalment_check_request_id=" . $request_id;
                                                                    $result2 = $this->B_db->run_query($query2);
                                                                    $instalment_check = $result2[0];
                                                                    $amount = $instalment_check['sumchckpassed'] + $user_pey_cash - $request_revoke_price;
                                                                    if ($amount > 0) {
                                                                        $this->B_db->peyback_decision($request_id, $amount, 'get', $request_revoke_desc,'nomain');
                                                                    }

                                                                    //**********************************************************************************

                                                                    echo json_encode(array('result' => "ok"
                                                                    , "data" => get_request_agent($request_id)
                                                                    , 'desc' => ' تغییر مرحله درخواست ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                                                    //****************************************************************************************************************
                                                                } else {
                                                                    echo json_encode(array('result' => "error"
                                                                    , "data" => ""
                                                                    , 'desc' => ' درخواست نمیتواند به وضعیت صادر شده و آماده تحویل در آید '), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                                }

                                                            } else {
                                                                echo json_encode(array('result' => $agenttoken[0]
                                                                , "data" => $agenttoken[1]
                                                                , 'desc' => $agenttoken[2]));

                                                            }
                                                        } else
                if ($command=="requestvisit16")
                                                            {//register marketer

                                                                $agenttoken = checkagenttoken($agent_token_str);
                                                                if ($agenttoken[0] == 'ok') {
                                                                    $agent_id = $agenttoken[1];
                                                                    $request_id=$this->post('request_id') ;

                                                                    $query2="select * from request_tb where request_id=".$request_id."";
                                                                    $result2=$this->B_db->run_query($query2);
                                                                    $request=$result2[0];
                                                                    if($request['request_last_state_id']==3){
                                                                        //***************************************************************************************************************
                                                                        $query1="UPDATE request_tb SET request_last_state_id=16 WHERE request_id=$request_id";
                                                                        $result1 = $this->B_db->run_query_put($query1);

                                                                        $desc='نیار به بازدید';
                                                                        $query="INSERT INTO state_request_tb( staterequest_request_id, staterequest_state_id, staterequest_timestamp,staterequest_desc,staterequest_agent_id,staterequest_employee_id) VALUES
                                        ( $request_id, 16, now(),'$desc',$agent_id,$agenttoken[3])";
                                                                        $result = $this->B_db->run_query_put($query);
                                                                        request_send_sms($request_id,'user',$desc);

                                                                        $query="INSERT INTO request_visit_tb
                                          ( request_visit_request_id) VALUES 
                                        ( $request_id )";
                                                                        $this->B_db->run_query_put($query);
                                                                        $request_visit_id = $this->db->insert_id();

                                                                        echo json_encode(array('result'=>"ok"
                                                                        ,"data"=>get_request($request_id)
                                                                        ,'desc'=>' تغییر مرحله درخواست ثبت شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                    }else{
                                                                        echo json_encode(array('result'=>"error"
                                                                        ,"data"=>""
                                                                        ,'desc'=>' درخواست نمیتواند به وضعیت مورد نظر در آید '),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                    }
                                                                } else {
                                                                    echo json_encode(array('result' => $agenttoken[0]
                                                                    , "data" => $agenttoken[1]
                                                                    , 'desc' => $agenttoken[2]));

                                                                }
                                                            }else
                if ($command=="requestdeletevisit16")
                                                                {//register marketer

                                                                    $agenttoken = checkagenttoken($agent_token_str);
                                                                    if ($agenttoken[0] == 'ok') {

                                                                        //***************************************************************************************************************
                                                                        $agent_id=$this->post('agent_id') ;
                                                                        $request_id=$this->post('request_id') ;

                                                                        $query2="select * from request_tb where request_id=".$request_id."";
                                                                        $result2=$this->B_db->run_query($query2);
                                                                        $request=$result2[0];
                                                                        if($request['request_last_state_id']==16){
                                                                            //***************************************************************************************************************
                                                                            $query1="UPDATE request_tb SET request_last_state_id=3 WHERE request_id=$request_id";
                                                                            $result1 = $this->B_db->run_query_put($query1);

                                                                            $desc=' حذف  نیاز به بازدید ';
                                                                            $query="INSERT INTO state_request_tb( staterequest_request_id, staterequest_state_id, staterequest_timestamp,staterequest_desc,staterequest_agent_id,staterequest_employee_id) VALUES
                                                                          ( $request_id, 3, now(),'$desc',$agent_id,$agenttoken[3])";
                                                                            $result = $this->B_db->run_query_put($query);
                                                                            request_send_sms($request_id,'user',$desc);

                                                                            $query="DELETE FROM `request_visit_tb` WHERE request_visit_request_id=$request_id";
                                                                            $result = $this->B_db->run_query_put($query);

                                                                            echo json_encode(array('result'=>"ok"
                                                                            ,"data"=>get_request($request_id)
                                                                            ,'desc'=>' تغییر مرحله درخواست ثبت شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                        }else{
                                                                            echo json_encode(array('result'=>"error"
                                                                            ,"data"=>""
                                                                            ,'desc'=>' درخواست نمیتواند به وضعیت مورد نظر در آید '),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                        }
                                                                    } else {
                                                                        echo json_encode(array('result' => $agenttoken[0]
                                                                        , "data" => $agenttoken[1]
                                                                        , 'desc' => $agenttoken[2]));

                                                                    }
                                                                }else
                if ($command == "requestreadyimgsave") {//register marketer

                                                                $agenttoken = checkagenttoken($agent_token_str);
                                                                if ($agenttoken[0] == 'ok') {
                                                                    $agent_id = $agenttoken[1];
//***************************************************************************************************************
                                                                    $request_id = $this->post('request_id');
                                                                    $requst_ready_image_code = $this->post('requst_ready_image_code');


                                                                    $query2 = "select * from request_tb where request_id=" . $request_id . "";
                                                                    $result2 = $this->B_db->run_query($query2);
                                                                    $request = $result2[0];
                                                                    if ($request['request_last_state_id'] != 2) {
//***************************************************************************************************************


                                                                        $query = "INSERT INTO requst_ready_image_tb(requst_ready_request_id, requst_ready_image_code) VALUES
                                        ( $request_id,'$requst_ready_image_code')";
                                                                        $result = $this->B_db->run_query_put($query);


                                                                        echo json_encode(array('result' => "ok"
                                                                        , "data" => get_request_agent($request_id)
                                                                        , 'desc' => ' عکسها به درخواست صادر شده اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                                                        //****************************************************************************************************************
                                                                    } else {
                                                                        echo json_encode(array('result' => "error"
                                                                        , "data" => ""
                                                                        , 'desc' => ' عکس ها به درخواست اضافه نشد '), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                                    }

                                                                } else {
                                                                    echo json_encode(array('result' => $agenttoken[0]
                                                                    , "data" => $agenttoken[1]
                                                                    , 'desc' => $agenttoken[2]));

                                                                }


                                                            } else
                if ($command == "requestdelivered11") {//register marketer

                                                                    $agenttoken = checkagenttoken($agent_token_str);
                                                                    if ($agenttoken[0] == 'ok') {
                                                                        $agent_id = $agenttoken[1];
//***************************************************************************************************************
                                                                        $request_id = $this->post('request_id');
                                                                        $request_delivered_mode_id = $this->post('request_delivered_mode_id');
                                                                        $request_delivered_dsc = $this->post('request_delivered_dsc');
                                                                        $request_delivered_receipt_image_code = $this->post('request_delivered_receipt_image_code');
                                                                        $request_delivered_state_id = $this->post('request_delivered_state_id');
                                                                        $request_delivered_city_id = $this->post('request_delivered_city_id');

                                                                        $query2 = "select * from request_tb where request_id=" . $request_id . "";
                                                                        $result2 = $this->B_db->run_query($query2);
                                                                        $request = $result2[0];
                                                                        if ($request['request_last_state_id'] == 10 || $request['request_last_state_id'] == 11) {
//***************************************************************************************************************
                                                                            $query1 = "UPDATE request_tb SET request_last_state_id=11  WHERE request_id=$request_id";
                                                                            $result1 = $this->B_db->run_query_put($query1);

                                                                            $desc = ' ارسال به کاربر ';
                                                                            $query = "INSERT INTO state_request_tb( staterequest_request_id, staterequest_state_id, staterequest_timestamp,staterequest_desc,staterequest_agent_id,staterequest_employee_id) VALUES
                                        ( $request_id, 11, now(),'$desc',$agent_id,$agenttoken[3])";
                                                                            $result = $this->B_db->run_query_put($query);

                                                                            request_send_sms($request_id, 'user', $desc);

                                                                            $query = "DELETE FROM request_delivered_tb WHERE request_delivered_request_id=$request_id";
                                                                            $result = $this->B_db->run_query_put($query);

                                                                            $query = "INSERT INTO request_delivered_tb( request_delivered_request_id, request_delivered_timesatmp, request_delivered_mode_id, request_delivered_dsc, request_delivered_receipt_image_code, request_delivered_state_id, request_delivered_city_id) VALUES
                                        ( $request_id,                 now()                  ,$request_delivered_mode_id ,'$request_delivered_dsc' ,'$request_delivered_receipt_image_code' ,$request_delivered_state_id ,$request_delivered_city_id)";
                                                                            $result = $this->B_db->run_query_put($query);
                                                                            $requst_ready_id = count($result[0]);

                                                                            echo json_encode(array('result' => "ok"
                                                                            , "data" => get_request_agent($request_id)
                                                                            , 'desc' => ' تغییر مرحله درخواست ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                                                            //****************************************************************************************************************
                                                                        } else {
                                                                            echo json_encode(array('result' => "error"
                                                                            , "data" => ""
                                                                            , 'desc' => ' درخواست نمیتواند به وضعیت تحویل به کاربر در آید '), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                                        }

                                                                    } else {
                                                                        echo json_encode(array('result' => $agenttoken[0]
                                                                        , "data" => $agenttoken[1]
                                                                        , 'desc' => $agenttoken[2]));

                                                                    }


                                                                } else
                if ($command == "changedatenumcheck") {//register marketer

                                                                        $agenttoken = checkagenttoken($agent_token_str);
                                                                        if ($agenttoken[0] == 'ok') {
                                                                            //***************************************************************************************************************
                                                                            $instalment_check_amount=$this->post('instalment_check_amount') ;
                                                                            $instalment_check_num=$this->post('instalment_check_num') ;
                                                                            $instalment_check_date=$this->post('instalment_check_date') ;
                                                                            $user_pey_id=$this->post('user_pey_id') ;
                                                                            $request_id=$this->post('request_id') ;
                                                                            $query0="select * from request_tb where request_id=".$request_id."";
                                                                            $result0=$this->B_db->run_query($query0);
                                                                            $request2=$result0[0];
                                                                            if($request2['request_organ']==0){
                                                                                $query = "UPDATE instalment_check_tb SET instalment_check_amount='$instalment_check_amount',instalment_check_date='$instalment_check_date',instalment_check_num='$instalment_check_num'  WHERE instalment_check_user_pey_id=$user_pey_id";
                                                                                $result = $this->B_db->run_query_put($query);

                                                                                $query1 = "UPDATE user_pey_tb SET user_pey_amount='$instalment_check_amount'  WHERE user_pey_id=$user_pey_id";
                                                                                $result2 = $this->B_db->run_query_put($query1);

                                                                                echo json_encode(array('result' => "ok"
                                                                                , "data" => get_request($request_id)
                                                                                , "organ_user_commitment_amount" => ''
                                                                                , "instalment_check_amount" => intval($instalment_check_amount)
                                                                                , "sumamount_check" => ''
                                                                                , "diff" => ''
                                                                                , 'desc' => ' تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                                                                            }else {

//***********************************************************************************************************
                                                                                for ($i = 0; $i < 13; $i++) {
                                                                                    $datenow = strtotime(date("d-m-Y", strtotime($i . " Months")));
                                                                                    $Y = jdate('Y', $datenow, "", '', 'fa');
                                                                                    $M = jdate('m', $datenow, "", '', 'fa');
                                                                                    $datafa = jalali_to_gregorian($Y, $M, 1);

                                                                                    $date = date_create();
                                                                                    date_date_set($date, $datafa[0], $datafa[1], $datafa[2]);
                                                                                    $arrdate[] = date_format($date, 'Y-m-d 00:00:00');
                                                                                }
                                                                                $temp = 0;
                                                                                for ($i = 0; $i < 11; $i++) {
                                                                                    $start = new DateTime($arrdate[$i]);
                                                                                    $d2 = new DateTime($instalment_check_date);
                                                                                    $end = new DateTime($arrdate[$i + 1]);

                                                                                    if ($d2 >= $start && $d2 < $end) {
                                                                                        $temp = $i;
                                                                                    }
                                                                                }
                                                                                //***********************************************************************************************************

                                                                                $query6 = "select * from request_tb,organ_request_tb,organ_contract_tb where organ_contract_id=organ_request_contract_id AND organ_request_request_id=request_id AND request_id=" . $request_id . "";
                                                                                $result6 = $this->B_db->run_query($query6);
                                                                                $request = $result6[0];

                                                                                $query67 = "SELECT DISTINCT SUM( instalment_check_tb.instalment_check_amount) as sumamount_check FROM instalment_check_tb,instalment_conditions_tb,request_tb,organ_request_tb,organ_contract_tb WHERE
                                 instalment_check_request_id=request_id AND
                                 request_id=organ_request_request_id AND
                                 organ_request_contract_id=organ_contract_id AND 
                                instalment_check_pass=0 AND instalment_conditions_mode_id=2 AND
                                 instalment_conditions_id=instalment_check_condition_id AND
                                 instalment_check_date>= '" . $arrdate[$temp] . "' AND
                                 instalment_check_date< '" . $arrdate[$temp + 1] . "' AND
                                 request_last_state_id>9 AND
                                 request_user_id=" . $request['request_user_id'] . " AND
                                 organ_contract_organ_id=" . $request['organ_contract_organ_id'];
                                                                                $result67 = $this->B_db->run_query($query67);
                                                                                $sumamount_check = intval($result67[0]['sumamount_check']);

                                                                                $count_query = "select a.organ_user_commitment_amount
                    FROM organ_user_tb a
                    INNER JOIN (
                       SELECT organ_user_user_id, MAX(organ_user_confirm_id) AS max_confirm_id
                    FROM organ_user_tb
                    WHERE organ_user_organ_id=" . $request['organ_contract_organ_id'] . " and organ_user_confirm_id<=100000000000 
                    GROUP by organ_user_user_id
                    ) b ON a.organ_user_confirm_id = b.max_confirm_id AND a.organ_user_user_id = b.organ_user_user_id AND a.organ_user_organ_id=" . $request['organ_contract_organ_id'] . "
                    INNER JOIN user_tb c
                    ON c.user_id=a.organ_user_user_id AND a.organ_user_user_id=" . $request['request_user_id'] . "
                    INNER JOIN organ_confirm_tb d
                    ON a.organ_user_confirm_id=d.organ_confirm_id";
                                                                                $organ_user = $this->B_db->run_query($count_query);
                                                                                $organ_user_commitment_amount = intval($organ_user[0]['organ_user_commitment_amount']);
                                                                                if ($organ_user_commitment_amount - ($sumamount_check + intval($instalment_check_amount)) >= 0) {
                                                                                    //***************************************************************************************************************
                                                                                    $query = "UPDATE instalment_check_tb SET instalment_check_amount='$instalment_check_amount',instalment_check_date='$instalment_check_date',instalment_check_num='$instalment_check_num'  WHERE instalment_check_user_pey_id=$user_pey_id";
                                                                                    $result = $this->B_db->run_query_put($query);

                                                                                    $query1 = "UPDATE user_pey_tb SET user_pey_amount='$instalment_check_amount'  WHERE user_pey_id=$user_pey_id";
                                                                                    $result2 = $this->B_db->run_query_put($query1);

                                                                                    echo json_encode(array('result' => "ok"
                                                                                    , "data" => get_request($request_id)
                                                                                    , "organ_user_commitment_amount" => $organ_user_commitment_amount
                                                                                    , "instalment_check_amount" => intval($instalment_check_amount)
                                                                                    , "sumamount_check" => $sumamount_check
                                                                                    , "diff" => $organ_user_commitment_amount - ($sumamount_check + intval($instalment_check_amount))
                                                                                    , 'desc' => ' تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                                                                    //****************************************************************************************************************
                                                                                } else {
                                                                                    echo json_encode(array('result' => "notok"
                                                                                    , "data" => get_request($request_id)
                                                                                    , "organ_user_commitment_amount" => $organ_user_commitment_amount
                                                                                    , "instalment_check_amount" => intval($instalment_check_amount)
                                                                                    , "sumamount_check" => $sumamount_check
                                                                                    , "diff" => $organ_user_commitment_amount - ($sumamount_check + intval($instalment_check_amount))
                                                                                    , 'desc' => 'تعهدات کاربر درماه ' . $organ_user_commitment_amount . ' ریال و مبلغ استغاده شده در این ماه' . $sumamount_check . ' است که مبلغ انتخاب شده بالا تر از ' . ($organ_user_commitment_amount - $sumamount_check) . ' است '), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                                                                }
                                                                            }
                                                                        } else {
                                                                            echo json_encode(array('result' => $agenttoken[0]
                                                                            , "data" => $agenttoken[1]
                                                                            , 'desc' => $agenttoken[2]));

                                                                        }


                                                                    } else

                    if ($command=="deletecheck")
                    {//register marketer

                        $agenttoken = checkagenttoken($agent_token_str);
                        if ($agenttoken[0] == 'ok') {
                            //***************************************************************************************************************

                            $user_pey_id=$this->post('user_pey_id') ;
                            $request_id=$this->post('request_id') ;
                            $query12="select * from instalment_check_tb WHERE instalment_check_user_pey_id=$user_pey_id";
                            $result12=$this->B_db->run_query($query12);
                            $instalment_check=$result12[0];

                            if($instalment_check['instalment_check_pass']==0){


                                $query = "DELETE FROM  instalment_check_tb WHERE instalment_check_user_pey_id=$user_pey_id";
                                $result = $this->B_db->run_query_put($query);

                                $query1 = "DELETE FROM user_pey_tb   WHERE user_pey_id=$user_pey_id";
                                $result2 = $this->B_db->run_query_put($query1);

                                echo json_encode(array('result' => "ok"
                                , "data" => get_request($request_id)
                                , "query1" => $query.$query1
                                , 'desc' => ' تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                            }else{
                                echo json_encode(array('result'=>'error'
                                ,"data"=>''
                                ,'desc'=>'تعهد های پاس شده را نمی توان تغییر داد'));
                            }
                        } else {
                            echo json_encode(array('result' => $agenttoken[0]
                            , "data" => $agenttoken[1]
                            , 'desc' => $agenttoken[2]));

                        }
                    }else
                        if ($command=="addcheck")
                        {//register marketer

                            $agenttoken = checkagenttoken($agent_token_str);
                            if ($agenttoken[0] == 'ok') {
                                //***************************************************************************************************************
                                $instalment_check_amount=$this->post('instalment_check_amount') ;
                                $instalment_check_num=$this->post('instalment_check_num') ;
                                $instalment_check_desc=$this->post('instalment_check_desc') ;
                                $instalment_check_date=$this->post('instalment_check_date') ;
                                $instalment_check_mode=$this->post('instalment_check_mode') ;
                                $request_id=$this->post('request_id') ;

                                $query0="select * from request_tb where request_id=".$request_id."";
                                $result0=$this->B_db->run_query($query0);
                                $request2=$result0[0];
                                if($request2['request_organ']==0){
                                    if($instalment_check_mode=='1'||$instalment_check_mode==1) {
                                        $query1 = "INSERT INTO user_pey_tb
( user_pey_request_id, user_pey_amount      , user_pey_mode, user_pey_code, user_pey_desc    ,  user_pey_timestamp,user_pey_image_code)
VALUES($request_id, $instalment_check_amount, 'instalment', 83            , 'پرداخت  توسط چک',  now(),'');";
                                        $result2 = $this->B_db->run_query_put($query1);
                                        $instalment_check_user_pey_id = $this->db->insert_id();

                                        $query = "INSERT INTO instalment_check_tb
       ( instalment_check_condition_id, instalment_check_instalment_id, instalment_check_user_pey_id , instalment_check_date, instalment_check_num, instalment_check_amount, instalment_check_desc,  instalment_check_request_id       ,instalment_check_image_code, instalment_check_employee_id)
VALUES( 83                            , 1                              ,$instalment_check_user_pey_id,'$instalment_check_date','$instalment_check_num','$instalment_check_amount','$instalment_check_desc',  $request_id               ,''                         ," . $agenttoken[3] . "          );
";
                                          $result = $this->B_db->run_query_put($query);
                                    }else if($instalment_check_mode=='2'||$instalment_check_mode==2){
                                        $query1 = "INSERT INTO user_pey_tb
( user_pey_request_id, user_pey_amount      , user_pey_mode, user_pey_code, user_pey_desc    ,  user_pey_timestamp,user_pey_image_code)
VALUES($request_id, $instalment_check_amount, 'instalment', 84            , 'پرداخت  توسط کسر از حقوق',  now()    ,'');";
                                        $result2 = $this->B_db->run_query_put($query1);
                                        $instalment_check_user_pey_id = $this->db->insert_id();

                                        $query = "INSERT INTO instalment_check_tb
       ( instalment_check_condition_id, instalment_check_instalment_id, instalment_check_user_pey_id , instalment_check_date, instalment_check_num, instalment_check_amount, instalment_check_desc,  instalment_check_request_id,instalment_check_image_code, instalment_check_employee_id)
VALUES( 84                            , 1                              ,$instalment_check_user_pey_id,'$instalment_check_date','$instalment_check_num','$instalment_check_amount','$instalment_check_desc',  $request_id         ,''                        ," . $agenttoken[3] . "          );
";
                                          $result = $this->B_db->run_query_put($query);
                                    }


                                    echo json_encode(array('result' => "ok"
                                    , "data" => get_request($request_id)
                                    , "organ_user_commitment_amount" => ''
                                    , "instalment_check_amount" => intval($instalment_check_amount)
                                    , "sumamount_check" => ''
                                    , "diff" => ''
                                    , 'desc' => ' تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                                }else {
//***********************************************************************************************************
                                    for ($i = 0; $i < 13; $i++) {
                                        $datenow = strtotime(date("d-m-Y", strtotime($i . " Months")));
                                        $Y = jdate('Y', $datenow, "", '', 'fa');
                                        $M = jdate('m', $datenow, "", '', 'fa');
                                        $datafa = jalali_to_gregorian($Y, $M, 1);

                                        $date = date_create();
                                        date_date_set($date, $datafa[0], $datafa[1], $datafa[2]);
                                        $arrdate[] = date_format($date, 'Y-m-d 00:00:00');
                                    }
                                    $temp = 0;
                                    for ($i = 0; $i < 11; $i++) {
                                        $start = new DateTime($arrdate[$i]);
                                        $d2 = new DateTime($instalment_check_date);
                                        $end = new DateTime($arrdate[$i + 1]);

                                        if ($d2 >= $start && $d2 < $end) {
                                            $temp = $i;
                                        }
                                    }
                                    //***********************************************************************************************************

                                    $query6 = "select * from request_tb,organ_request_tb,organ_contract_tb where organ_contract_id=organ_request_contract_id AND organ_request_request_id=request_id AND request_id=" . $request_id . "";
                                    $result6 = $this->B_db->run_query($query6);
                                    $request = $result6[0];

                                    $query67 = "SELECT DISTINCT SUM( instalment_check_tb.instalment_check_amount) as sumamount_check FROM instalment_check_tb,instalment_conditions_tb,request_tb,organ_request_tb,organ_contract_tb WHERE
                                 instalment_check_request_id=request_id AND
                                 request_id=organ_request_request_id AND
                                 organ_request_contract_id=organ_contract_id AND 
                                instalment_check_pass=0 AND instalment_conditions_mode_id=2 AND
                                 instalment_conditions_id=instalment_check_condition_id AND
                                 instalment_check_date>= '" . $arrdate[$temp] . "' AND
                                 instalment_check_date< '" . $arrdate[$temp + 1] . "' AND
                                 request_last_state_id>9 AND
                                 request_user_id=" . $request['request_user_id'] . " AND
                                 organ_contract_organ_id=" . $request['organ_contract_organ_id'];
                                    $result67 = $this->B_db->run_query($query67);
                                    $sumamount_check = intval($result67[0]['sumamount_check']);

                                    $count_query = "select a.organ_user_commitment_amount
                    FROM organ_user_tb a
                    INNER JOIN (
                       SELECT organ_user_user_id, MAX(organ_user_confirm_id) AS max_confirm_id
                    FROM organ_user_tb
                    WHERE organ_user_organ_id=" . $request['organ_contract_organ_id'] . " and organ_user_confirm_id<=100000000000 
                    GROUP by organ_user_user_id
                    ) b ON a.organ_user_confirm_id = b.max_confirm_id AND a.organ_user_user_id = b.organ_user_user_id AND a.organ_user_organ_id=" . $request['organ_contract_organ_id'] . "
                    INNER JOIN user_tb c
                    ON c.user_id=a.organ_user_user_id AND a.organ_user_user_id=" . $request['request_user_id'] . "
                    INNER JOIN organ_confirm_tb d
                    ON a.organ_user_confirm_id=d.organ_confirm_id";
                                    $organ_user = $this->B_db->run_query($count_query);
                                    $organ_user_commitment_amount = intval($organ_user[0]['organ_user_commitment_amount']);
                                    if ($organ_user_commitment_amount - ($sumamount_check + intval($instalment_check_amount)) >= 0) {
                                        //***************************************************************************************************************
                                        if($instalment_check_mode=='1'||$instalment_check_mode==1) {
                                            $query1 = "INSERT INTO user_pey_tb
( user_pey_request_id, user_pey_amount      , user_pey_mode, user_pey_code, user_pey_desc    ,  user_pey_timestamp,user_pey_image_code)
VALUES($request_id, $instalment_check_amount, 'instalment', 83            , 'پرداخت  توسط چک',   now(),'');";
                                            $result2 = $this->B_db->run_query_put($query1);
                                            $instalment_check_user_pey_id = $this->db->insert_id();

                                            $query = "INSERT INTO instalment_check_tb
       ( instalment_check_condition_id, instalment_check_instalment_id, instalment_check_user_pey_id , instalment_check_date, instalment_check_num, instalment_check_amount, instalment_check_desc,  instalment_check_request_id,instalment_check_image_code, instalment_check_employee_id)
VALUES( 83                            , 1                              ,$instalment_check_user_pey_id,'$instalment_check_date','$instalment_check_num','$instalment_check_amount','$instalment_check_desc',  $request_id         ,   ''   ," . $agenttoken[3] . "          );
";
                                            $result = $this->B_db->run_query_put($query);
                                        }else if($instalment_check_mode=='2'||$instalment_check_mode==2){
                                            $query1 = "INSERT INTO user_pey_tb
( user_pey_request_id, user_pey_amount      , user_pey_mode, user_pey_code, user_pey_desc    ,  user_pey_timestamp,user_pey_image_code)
VALUES($request_id, $instalment_check_amount, 'instalment', 84            , 'پرداخت  توسط کسر از حقوق',   now(),'');";
                                            $result2 = $this->B_db->run_query_put($query1);
                                            $instalment_check_user_pey_id = $this->db->insert_id();

                                            $query = "INSERT INTO instalment_check_tb
       ( instalment_check_condition_id, instalment_check_instalment_id, instalment_check_user_pey_id , instalment_check_date, instalment_check_num, instalment_check_amount, instalment_check_desc,  instalment_check_request_id,instalment_check_image_code, instalment_check_employee_id)
VALUES( 84                            , 1                              ,$instalment_check_user_pey_id,'$instalment_check_date','$instalment_check_num','$instalment_check_amount','$instalment_check_desc',  $request_id         ,  ''                   ," . $agenttoken[3] . "          );
";
                                            $result = $this->B_db->run_query_put($query);
                                        }


                                        echo json_encode(array('result' => "ok"
                                        , "data" => get_request($request_id)
                                        , "query1" => $query1.' '.$query
                                        , "organ_user_commitment_amount" => $organ_user_commitment_amount
                                        , "instalment_check_amount" => intval($instalment_check_amount)
                                        , "sumamount_check" => $sumamount_check
                                        , "diff" => $organ_user_commitment_amount - ($sumamount_check + intval($instalment_check_amount))
                                        , 'desc' => ' تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                        //****************************************************************************************************************
                                    } else {
                                        echo json_encode(array('result' => "notok"
                                        , "data" => get_request($request_id)
                                        , "organ_user_commitment_amount" => $organ_user_commitment_amount
                                        , "instalment_check_amount" => intval($instalment_check_amount)
                                        , "sumamount_check" => $sumamount_check
                                        , "diff" => $organ_user_commitment_amount - ($sumamount_check + intval($instalment_check_amount))
                                        , 'desc' => 'تعهدات کاربر درماه ' . $organ_user_commitment_amount . ' ریال و مبلغ استغاده شده در این ماه' . $sumamount_check . ' است که مبلغ انتخاب شده بالا تر از ' . ($organ_user_commitment_amount - $sumamount_check) . ' است '), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                    }
                                }
                            } else {
                                echo json_encode(array('result' => $agenttoken[0]
                                , "data" => $agenttoken[1]
                                , 'desc' => $agenttoken[2]));

                            }
                        }else
                if ($command == "getstaterequest") {//register marketer

                                                                        $query = "select * from request_state where 1";
                                                                        $result = $this->B_db->run_query($query);
                                                                        $output = array();
                                                                        foreach ($result as $row) {
                                                                            $record = array();
                                                                            $record['request_state_id'] = $row['request_state_id'];
                                                                            $record['request_state_name'] = $row['request_state_name'];
                                                                            $output[] = $record;
                                                                        }
                                                                        echo json_encode(array('result' => "ok"
                                                                        , "data" => $output
                                                                        , 'desc' => 'وضعیت درخواست ها با  موفقیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                                                                    } else
                if ($command == "get_fieldinsurance") {//register marketer

                                                                        $query = "select * from fieldinsurance_tb where 1";
                                                                        $result = $this->B_db->run_query($query);
                                                                        $output = array();
                                                                        foreach ($result as $row) {
                                                                            $record = array();
                                                                            $record['fieldinsurance_id'] = $row['fieldinsurance_id'];
                                                                            $record['fieldinsurance_fa'] = $row['fieldinsurance_fa'];
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
                if ($command == "add_filerequest") {

                $agenttoken = checkagenttoken($agent_token_str);
                if ($agenttoken[0] == 'ok') {
                    $agent_id = $agenttoken[1];
//***************************************************************************************************************
                    $request_file_request_id = $this->post('request_file_request_id');
                    $request_file_desc = $this->post('request_file_desc');
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


                    if (isset($_FILES['filerequest']['name'])) {

                        $fileinfo = pathinfo($_FILES['filerequest']['name']);
                        $extension = $fileinfo['extension'];

                        $date = new DateTime();
                        $date->setTimezone($timezone);
                        $current_timestamp = $date->getTimestamp();
                        $image_code = $current_timestamp;


                        $file_url = $upload_path . $image_code . '.' . $extension;
                        if (move_uploaded_file($_FILES['filerequest']['tmp_name'], $file_url)) {
//***************************************************************************************************************
                            $request_file_url = $file_url;

                            $query = "INSERT INTO request_file_tb( request_file_request_id , request_file_url, request_file_desc) VALUES
                                        ( $request_file_request_id ,'$request_file_url' ,'$request_file_desc' )";
                            $result = $this->B_db->run_query_put($query);
                            $requst_ready_id = count($result[0]);

                            echo json_encode(array('result' => "ok"
                            , "data" => get_request_agent($request_file_request_id)
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
                    echo json_encode(array('result' => $agenttoken[0]
                    , "data" => $agenttoken[1]
                    , 'desc' => $agenttoken[2]));

                }

            } else
                if ($command == "get_filerequest") {

                $agenttoken = checkagenttoken($agent_token_str);
                if ($agenttoken[0] == 'ok') {
                    $agent_id = $agenttoken[1];
//***************************************************************************************************************
                    $request_file_request_id = $this->post('request_file_request_id');

                    $query = "select * from request_file_tb where request_file_request_id=$request_file_request_id";
                    $result = $this->B_db->run_query($query);
                    $output = array();
                    foreach ($result as $row) {
                        $record = array();
                        $record['request_file_id'] = $row['request_file_id'];
                        $record['request_file_request_id'] = $row['request_file_request_id'];
                        $record['request_file_url'] = $row['request_file_url'];
                        $record['request_file_desc'] = $row['request_file_desc'];
                        $output[] = $record;
                    }

                    echo json_encode(array('result' => "ok"
                    , "data" => $output
                    , 'desc' => ' فایل های درخواست ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    //***************************************************************************************************************

                } else {
                    echo json_encode(array('result' => $agenttoken[0]
                    , "data" => $agenttoken[1]
                    , 'desc' => $agenttoken[2]));

                }

            } else
                if ($command == "get_checkrequest") {

                $agenttoken = checkagenttoken($agent_token_str);
                if ($agenttoken[0] == 'ok') {
                    $agent_id = $agenttoken[1];
//***************************************************************************************************************
                    $check_mode = $this->post('check_mode');
                    $query = "select * from request_tb,instalment_check_tb,company_tb,fieldinsurance_tb,user_tb where instalment_check_request_id=request_id
			AND user_id=request_user_id AND fieldinsurance=request_fieldinsurance  AND company_id=request_company_id AND request_agent_id=" . $agent_id;
                    if ($check_mode == 'passed') {
                        $query .= " AND instalment_check_pass=1";
                    } else if ($check_mode == 'notpassed') {
                        $query .= " AND instalment_check_pass=0";
                    }
                    $result = $this->B_db->run_query($query);
                    $output = array();
                    foreach ($result as $row) {
                        $record = array();
                        $record['instalment_check_id'] = $row['instalment_check_id'];
                        $record['instalment_check_date'] = $row['instalment_check_date'];
                        $record['instalment_check_num'] = $row['instalment_check_num'];
                        $record['instalment_check_amount'] = $row['instalment_check_amount'];
                        $record['instalment_check_date_pass'] = $row['instalment_check_date_pass'];
                        $record['instalment_check_pass'] = $row['instalment_check_pass'];
                        $record['instalment_check_request_id'] = $row['instalment_check_request_id'];
                        $record['instalment_check_image_code'] = $row['instalment_check_image_code'];
                        $result1 = $this->B_db->get_image($row['instalment_check_image_code']);
                        $image = $result1[0];
                        if ($image['image_tumb_url'] == null) {
                            $record['check_image_turl'] = null;
                        } else {
                            $record['check_image_turl'] =  $image['image_tumb_url'];
                        }
                        if ($image['image_url'] == null) {
                            $record['check_image_url'] = null;
                        } else {
                            $record['check_image_url'] =  $image['image_url'];
                        }
                        $record['request_id'] = $row['request_id'];
                        $record['user_id'] = $row['user_id'];
                        $record['user_name'] = $row['user_name'];
                        $record['user_family'] = $row['user_family'];
                        $record['user_mobile'] = $row['user_mobile'];
                        $record['fieldinsurance_logo_url'] = IMGADD . $row['fieldinsurance_logo_url'];
                        $record['fieldinsurance_id'] = $row['fieldinsurance_id'];
                        $record['request_fieldinsurance_fa'] = $row['fieldinsurance_fa'];
                        if ($row['instalment_check_employee_id'] != null || $row['instalment_check_employee_id'] != "") {
                            $query11 = " SELECT * FROM employee_tb WHERE employee_id=" . $row['instalment_check_employee_id'];
                            $result11 = $this->B_db->run_query($query11);
                            $employee = $result11[0];
                            $record['employee_name'] = $employee['employee_name'];
                            $record['employee_family'] = $employee['employee_family'];
                            $record['employee_mobile'] = $employee['employee_mobile'];
                        }

                        $output[] = $record;
                    }

                    echo json_encode(array('result' => "ok"
                    , "data" => $output
                    , 'desc' => ' لیست چک ها ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    //***************************************************************************************************************

                } else {
                    echo json_encode(array('result' => $agenttoken[0]
                    , "data" => $agenttoken[1]
                    , 'desc' => $agenttoken[2]));

                }

            } else
                if ($command == "check_passed") {

                $agenttoken = checkagenttoken($agent_token_str);
                if ($agenttoken[0] == 'ok') {
                    $agent_id = $agenttoken[1];
//***************************************************************************************************************
                    $instalment_check_id = $this->post('instalment_check_id');
                    $instalment_check_date_pass = $this->post('instalment_check_date_pass');

                    $query11 = " SELECT * FROM instalment_check_tb WHERE instalment_check_id=$instalment_check_id";
                    $result11 = $this->B_db->run_query($query11);
                    $instalment_check = $result11[0];
                    if ($instalment_check['instalment_check_pass'] == 0) {
                        $instalment_check_amount=intval($instalment_check['instalment_check_amount'] )* 100 / 109;

                        $this->B_db->peyback_decision($instalment_check['instalment_check_request_id'],$instalment_check_amount, 'add', 'چک','nomain');

                        $query1 = "UPDATE instalment_check_tb SET instalment_check_pass=1,instalment_check_date_pass='$instalment_check_date_pass' WHERE instalment_check_id=$instalment_check_id";
                        $result1 = $this->B_db->run_query_put($query1);

                        echo json_encode(array('result' => "ok"
                        , "data" => ''
                        , 'desc' => ' تغییر وضعیت چک انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    } else {
                        echo json_encode(array('result' => "error"
                        , "data" => ''
                        , 'desc' => ' تغییر وضعیت چک قبلا انجام شده است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                    //***************************************************************************************************************

                } else {
                    echo json_encode(array('result' => $agenttoken[0]
                    , "data" => $agenttoken[1]
                    , 'desc' => $agenttoken[2]));

                }

            }else
                if ($command=="addcouncilrequest") {//register marketer
                    $agenttoken = checkagenttoken($agent_token_str);
                    if ($agenttoken[0] == 'ok') {
                        $request_id=$this->post('request_id') ;
                        $requestcouncil_desc=$this->post('requestcouncil_desc') ;
                        $agent_id=$agenttoken[1] ;
                        $requestcouncil_image_code=$this->post('requestcouncil_image_code') ;

                        $query="INSERT INTO requestcouncil_tb ( requestcouncil_request_id, requestcouncil_timestamp, requestcouncil_desc, requestcouncil_agent_id, requestcouncil_employee_id,requestcouncil_image_code) VALUES
                                        ( $request_id, now(),'$requestcouncil_desc',$agent_id,$agenttoken[3],'$requestcouncil_image_code')";
                        $result = $this->B_db->run_query_put($query);

                        if($result){
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>array('request_id'=>$request_id)
                            ,'desc'=>'درخواست اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            echo json_encode(array('result'=>"error"
                            ,"data"=>$query
                            ,'desc'=>'درخواست اضافه نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }

                    } else {
                        echo json_encode(array('result' => $agenttoken[0]
                        , "data" => $agenttoken[1]
                        , 'desc' => $agenttoken[2]));

                    }
                } else
                if ($command=="getcouncilrequest") {//register marketer
                        $agenttoken = checkagenttoken($agent_token_str);
                        if ($agenttoken[0] == 'ok') {
                            $request_id=$this->post('request_id') ;

                            $query7="select * from requestcouncil_tb where  requestcouncil_request_id=".$request_id;
                            $result7 = $this->B_db->run_query($query7);
                            $output7 =array();
                            if(!empty($result7)) {
                                foreach ($result7 as $row7) {
                                    $record7['requestcouncil_id'] = $row7['requestcouncil_id'];
                                    $record7['requestcouncil_timestamp'] = $row7['requestcouncil_timestamp'];
                                    $record7['requestcouncil_desc'] = $row7['requestcouncil_desc'];

                                    if ($row7['requestcouncil_agent_id']) {
                                        $query71 = " SELECT * FROM agent_tb WHERE agent_id =" . $row7['requestcouncil_agent_id'];
                                        $result71 = $this->B_db->run_query($query71);
                                        if (!empty($result71))
                                            $agent = $result71[0];
                                        else
                                            $agent = array();
                                        if ($agent['agent_code'] == null) {
                                            $record7['agent_code'] = null;
                                        } else {
                                            $record7['agent_code'] = $agent['agent_code'];
                                        }
                                        if ($agent['agent_name'] == null) {
                                            $record7['agent_name'] = null;
                                        } else {
                                            $record7['agent_name'] = $agent['agent_name'];
                                        }
                                        if ($agent['agent_family'] == null) {
                                            $record7['agent_family'] = null;
                                        } else {
                                            $record7['agent_family'] = $agent['agent_family'];
                                        }
                                    }
                                    if ($row7['requestcouncil_employee_id'] && $row7['requestcouncil_employee_id'] != 0) {
                                        $query71 = " SELECT * FROM employee_tb WHERE employee_id =" . $row7['requestcouncil_employee_id'];
                                        $result71 = $this->B_db->run_query($query71);
                                        $agent = $result71[0];

                                        if ($agent['employee_name'] == null) {
                                            $record7['employee_name'] = null;
                                        } else {
                                            $record7['employee_name'] = $agent['employee_name'];
                                        }
                                        if ($agent['employee_family'] == null) {
                                            $record7['employee_family'] = null;
                                        } else {
                                            $record7['employee_family'] = $agent['employee_family'];
                                        }
                                    } else {
                                        $record7['employee_name'] = null;
                                        $record7['employee_family'] = null;
                                    }
                                    $result1 = $this->B_db->get_image($row7['requestcouncil_image_code']);
                                    $imageurl = "";
                                    $imageturl = "";
                                    if (!empty($result1)) {
                                        $image = $result1[0];
                                        if ($image['image_url']) {
                                            $imageurl =  $image['image_url'];
                                            $imageturl = $image['image_tumb_url'];
                                        }
                                    }
                                    $record7['requestcouncil_image']=$imageurl;
                                    $record7['requestcouncil_timage']=$imageturl;

                                    $output7[] = $record7;
                                }
                            }
                            if($result7){
                                echo json_encode(array('result'=>"ok"
                                ,"data"=>$output7
                                ,'desc'=>'درخواست اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }else{
                                echo json_encode(array('result'=>"error"
                                ,"data"=>[]
                                ,'desc'=>'درخواست اضافه نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }

                        } else {
                            echo json_encode(array('result' => $agenttoken[0]
                            , "data" => $agenttoken[1]
                            , 'desc' => $agenttoken[2]));

                        }
                    }

        }
    }
}