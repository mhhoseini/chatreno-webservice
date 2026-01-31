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
class Financialpaying extends REST_Controller {

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
        if(isset($this->input->request_headers()['Authorization']))$employee_token_str = $this->input->request_headers()['Authorization'];
        $this->load->model('B_user');
        $this->load->model('B_db');
        $this->load->model('B_company');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('financialapproval', $command, get_client_ip(),50,50)) {
            if ($command=="get_request")
            {//register marketer
                $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','financialpaing');
                $approvalmode=$this->post('approvalmode');
                if($employeetoken[0]=='ok')
                {
                    $query1="select * from request_tb,company_tb,request_state,fieldinsurance_tb,user_tb where user_id=request_user_id AND fieldinsurance=request_fieldinsurance AND request_state_id=request_last_state_id AND company_id=request_company_id ";
                    $query1.=" AND (request_last_state_id=10 OR request_last_state_id=11)";

                    $query2="select count(*) AS cnt from request_tb,company_tb,request_state,fieldinsurance_tb,user_tb where user_id=request_user_id AND fieldinsurance=request_fieldinsurance AND request_state_id=request_last_state_id AND company_id=request_company_id ";
                    $query2.=" AND (request_last_state_id=10 OR request_last_state_id=11)";

                    $query="";
                    if($approvalmode=='checkedfinancial')	{
                        $query.=" AND request_id IN (SELECT request_financial_approval_request_id AS request_id FROM request_financial_approval_tb WHERE request_financial_approval=1 ) ";
                        $query.=" AND request_id NOT IN (SELECT request_financial_paying_request_id AS request_id FROM request_financial_paying_tb  ) ";
                    }else if($approvalmode=='progresspaing')
                    {
                        $query.=" AND request_id IN (SELECT request_financial_approval_request_id AS request_id FROM request_financial_approval_tb WHERE request_financial_approval=1 ) ";
                        $query.=" AND request_id IN (SELECT request_financial_paying_request_id AS request_id FROM request_financial_paying_tb,request_financial_doc_tb  WHERE request_financial_doc_id=request_financial_paying_doc_id AND request_financial_doc=0 ) ";

                    }else if($approvalmode=='payed')
                    {
                        $query.=" AND request_id IN (SELECT request_financial_approval_request_id AS request_id FROM request_financial_approval_tb WHERE request_financial_approval=1 ) ";
                        $query.=" AND request_id IN (SELECT request_financial_paying_request_id AS request_id FROM request_financial_paying_tb,request_financial_doc_tb  WHERE request_financial_doc_id=request_financial_paying_doc_id AND request_financial_doc=1 ) ";

                    }

                    $limit = $this->post("limit");
                    $offset = $this->post("offset");
                    $limit_state ="";
                    if($limit!="" & $offset!="") {
                        $limit_state = " LIMIT " . $offset . "," . $limit;
                    }

                    $result = $this->B_db->run_query($query1.$query.$limit_state);
                    $count  = $this->B_db->run_query($query2.$query);


                    $output =array();
                    $request_id='';
                    foreach($result as $row)
                    {
                        $record=array();

                        $record['request_id']=$row['request_id'];
                        $request_id=$row['request_id'];
                        $record['request_company_id']=$row['request_company_id'];
                        $record['company_name']=$row['company_name'];
                        $record['company_logo_url']=IMGADD.$row['company_logo_url'];
                        //*************************************************************************************************************
                        if($row['request_agent_id']!=null || $row['request_agent_id']!=""){
                            //   $record['request_agent_id']=$row['request_agent_id'];
                            $query2="select * from agent_tb,state_tb,city_tb where state_id=agent_state_id AND city_id=agent_city_id AND agent_id=".$row['request_agent_id'];
                            $result2 = $this->B_db->run_query($query2);
                            $agent=$result2[0];
                            $record['agent_id']=$agent['agent_id'];
                            $record['agent_code']=$agent['agent_code'];
                            $record['agent_name']=$agent['agent_name'];
                            $record['agent_family']=$agent['agent_family'];
                            $record['agent_gender']=$agent['agent_gender'];
                            $record['agent_mobile']=$agent['agent_mobile'];
                            $record['agent_tell']=$agent['agent_tell'];
                            $record['agent_email']=$agent['agent_email'];
                            $record['agent_required_phone']=$agent['agent_required_phone'];
                            $record['agent_address']=$agent['agent_address'];
                            $record['agent_state_id']=$agent['agent_state_id'];
                            $record['agent_city_id']=$agent['agent_city_id'];
                            $record['agent_state_name']=$agent['state_name'];
                            $record['agent_city_name']=$agent['city_name'];
                            $record['agent_sector_name']=$agent['agent_sector_name'];
                            $record['agent_long']=$agent['agent_long'];
                            $record['agent_lat']=$agent['agent_lat'];
                            $record['agent_banknum']=$agent['agent_banknum'];
                            $record['agent_bankname']=$agent['agent_bankname'];
                            $record['agent_banksheba']=$agent['agent_banksheba'];
                            $record['agent_image_code']=$agent['agent_image_code'];
                            //****************************************************************************

                            $result112 = $this->B_db->get_image($agent['agent_image_code']);
                            $image = $result112[0];
                            //*******************************************************************
                            $record['agent_image']=$image['image_url'];
                            $record['agent_image_tumb']=$image['image_tumb_url'];
                            $record['agent_deactive']=$agent['agent_deactive'];
                            $record['agent_register_date']=$agent['agent_register_date'];
                            //*************************************************************************************
                            $query111="select * from agent_status_tb where agent_status_agent_id=".$agent['agent_id']." ORDER BY agent_status_id DESC LIMIT 1 ";
                            $result111=$this->B_db->run_query($query111);
                            $agent_statuss1=$result111[0];
                            //*************************************************************************************
                            $record['agent_status']=$agent_statuss1['agent_status'];
                        }
                        //*************************************************************************************************************

                        $record['user_id']=$row['user_id'];
                        $record['user_name']=$row['user_name'];
                        $record['user_family']=$row['user_family'];
                        $record['user_mobile']=$row['user_mobile'];
                        $record['fieldinsurance_logo_url']=IMGADD.$row['fieldinsurance_logo_url'];
                        $record['fieldinsurance_id']=$row['fieldinsurance_id'];
                        $record['request_fieldinsurance_fa']=$row['fieldinsurance_fa'];
                        $record['request_description']=$row['request_description'];
                        $record['request_last_state_id']=$row['request_last_state_id'];
                        $record['request_last_state_name']=$row['request_state_name'];

                        //*************************************************************************************************************
                        $query0=" SELECT * FROM user_address_tb,state_tb,city_tb WHERE user_address_state_id=state_id AND user_address_city_id=city_id AND user_address_id=".$row['request_adderss_id'];
                        $result0 = $this->B_db->run_query($query0);
                        $output0 =array();
                            foreach($result0 as $row0)
                        {

                            $record0['user_address_state']=$row0['state_name'];
                            $record0['user_address_city']=$row0['city_name'];
                            $record0['user_address_state_id']=$row0['state_id'];
                            $record0['user_address_city_id']=$row0['city_id'];
                            $record0['user_address_str']=$row0['user_address_str'];
                            $record0['user_address_code']=$row0['user_address_code'];
                            $record0['user_address_name']=$row0['user_address_name'];
                            $record0['user_address_mobile']=$row0['user_address_mobile'];
                            $record0['user_address_tell']=$row0['user_address_tell'];
                            $output0[]=$record0;
                        }
                        $record['request_adderss']=$output0;

                        //*************************************************************************************************************
//*************************************************************************************************************
                        $query01=" SELECT * FROM user_address_tb,state_tb,city_tb WHERE user_address_state_id=state_id AND user_address_city_id=city_id AND user_address_id=".$row['request_addressofinsured_id'];
                        $result01 = $this->B_db->run_query($query01);
                        $output01 =array();
                            foreach($result01 as $row01)
                        {

                            $record01['user_address_state_id']=$row01['state_id'];
                            //$record01['user_address_city_id']=$row01['city_name_id'];
                            $record01['user_address_state']=$row01['state_name'];
                            $record01['user_address_city']=$row01['city_name'];
                            $record01['user_address_str']=$row01['user_address_str'];
                            $record01['user_address_code']=$row01['user_address_code'];
                            $record01['user_address_name']=$row01['user_address_name'];
                            $record01['user_address_mobile']=$row01['user_address_mobile'];
                            $record01['user_address_tell']=$row01['user_address_tell'];
                            $output01[]=$record01;
                        }
                        $record['request_addressofinsured']=$output01;

                        //*************************************************************************************************************

                        
                        $query0="select sum(user_pey_amount) AS overpayment from user_pey_tb where user_pey_mode='overpayment' AND user_pey_request_id=".$request_id;
                        $result0 = $this->B_db->run_query($query0);
                        $user_pey0=$result0[0];
                        $overpayment=$user_pey0['overpayment'];

                        $query1="select sum(user_pey_amount) AS sumpey from user_pey_tb where not (user_pey_mode='overpayment') AND user_pey_request_id=".$request_id." ORDER BY user_pey_request_id";
                        $result1 = $this->B_db->run_query($query1);
                        $user_pey=$result1[0];
                        $record['user_pey_amount']=$user_pey['sumpey']-$overpayment;


                        $query2="select sum(user_pey_amount) AS sumcash from user_pey_tb where not (user_pey_mode = 'instalment')  AND  not (user_pey_mode='overpayment') AND user_pey_request_id=".$request_id." ORDER BY user_pey_request_id";
                        $result2 = $this->B_db->run_query($query2);
                        $user_pey2=$result2[0];
                        $record['user_pey_cash']=$user_pey2['sumcash']-$overpayment;

                        $query20="select sum(user_pey_amount) AS suminstalment from user_pey_tb where user_pey_mode = 'instalment' AND user_pey_request_id=".$request_id;
                        $result20 = $this->B_db->run_query($query20);
                        $user_pey20=$result20[0];
                        $record['user_pey_instalment']=$user_pey20['suminstalment'];

                        $query1="select * from user_pey_tb,instalment_check_tb where user_pey_mode='instalment' AND instalment_check_user_pey_id=user_pey_id AND user_pey_request_id=".$request_id;
                        $result1 = $this->B_db->run_query($query1);
                        $output1 =array();
                            foreach($result1 as $row1)
                        {
                            $record1['user_pey_id']=$row1['user_pey_id'];
                            $record1['user_pey_amount']=$row1['user_pey_amount'];
                            $record1['instalment_check_num']=$row1['instalment_check_num'];
                            $record1['instalment_check_date']=$row1['instalment_check_date'];
                            $record1['user_pey_desc']=$row1['user_pey_desc'];
                            $record1['user_pey_image_code']=$row1['user_pey_image_code'];


                            $result11 = $this->B_db->get_image($row1['user_pey_image_code']);
                            $image = $result11[0];


                            if($image['image_tumb_url']==null){ $record1['user_pey_image_turl']=null;}else{ $record1['user_pey_image_turl']=$image['image_tumb_url'];}
                            if($image['image_url']==null){ $record1['user_pey_image_url']=null;}else{$record1['user_pey_image_url']=$image['image_url'];}

                            $output1[]=$record1;

                        }
                        $record['user_pey_detail']=$output1;

//******************************************************************************************
                        $query121="select * from user_pey_tb where not(user_pey_mode='instalment') AND user_pey_request_id=".$request_id;
                        $result121 = $this->B_db->run_query($query121);
                        $output121 =array();
                            foreach($result121 as $row121)
                        {
                            $record121['user_pey_amount']=$row121['user_pey_amount'];
                            //  $record1['user_pey_mode']=$row1['user_pey_mode'];
                            //$record1['user_pey_code']=$row1['user_pey_code'];
                            $record121['user_pey_desc']=$row121['user_pey_desc'];

                            $output121[]=$record121;

                        }
                        $record['user_pey_detail2']=$output121;


                        //***************************************************************************************************************
                        $query17="select * from state_request_tb where staterequest_request_id=".$request_id." ORDER BY staterequest_id DESC LIMIT 1 ";
                        $result17=$this->B_db->run_query($query17);
                        $state_request17=$result17[0];
                        $record['staterequest_last_timestamp']=$state_request17['staterequest_timestamp'];

                        //***************************************************************************************************************
                        $query7="select * from state_request_tb,request_state where request_state_id=staterequest_state_id AND staterequest_request_id=".$request_id;
                        $result7 = $this->B_db->run_query($query7);
                        $output7 =array();
                            foreach($result7 as $row7)
                        {

                            $record7['staterequest_id']=$row7['staterequest_id'];
                            //  $record7['staterequest_state_id']=$row7['staterequest_state_id'];
                            $record7['request_state_name']=$row7['request_state_name'];
                            $record7['staterequest_timestamp']=$row7['staterequest_timestamp'];
                            $record7['staterequest_desc']=$row7['staterequest_desc'];
                            // $record7['staterequest_agent_id']=$row7['staterequest_agent_id'];
                            if($row7['staterequest_agent_id']) {
                                $query71 = " SELECT * FROM agent_tb WHERE agent_id =" . $row7['staterequest_agent_id'];
                                $result71 = $this->B_db->run_query($query71);
                                $agent = $result71[0];
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

                            $output7[]=$record7;
                        }
                        $record['request_stats']=$output7;
                        //***************************************************************************************************************
                        //***************************************************************************************************************
                        $query6=" SELECT * FROM requst_ready_tb,request_ready_clearing_mode_tb WHERE request_ready_clearing_mode_id=request_ready_clearing_id AND requst_ready_request_id=".$request_id;
                        $result6 = $this->B_db->run_query($query6);
                        $output6 =array();
                            foreach($result6 as $row6)
                        {

                            $record6['requst_ready_start_date']=$row6['requst_ready_start_date'];
                            $record6['requst_ready_end_date']=$row6['requst_ready_end_date'];
                            $record6['requst_ready_end_price']=$row6['requst_ready_end_price'];
                            $record6['requst_ready_num_ins']=$row6['requst_ready_num_ins'];
                            $record6['requst_ready_code_rayane']=$row6['requst_ready_code_rayane'];
                            $record6['requst_ready_code_penalty']=$row6['requst_ready_code_penalty'];
                            $record6['requst_ready_code_yekta']=$row6['requst_ready_code_yekta'];
                            $record6['requst_ready_name_insurer']=$row6['requst_ready_name_insurer'];
                            $record6['requst_ready_code_insurer']=$row6['requst_ready_code_insurer'];
                            $record6['requst_suspend_desc']=$row6['requst_suspend_desc'];
                            $record6['requst_ready_employee_id']=$row6['requst_ready_employee_id'];
                            $record6['request_ready_clearing_id']=$row6['request_ready_clearing_id'];
                            $record6['request_ready_clearing_mode_name']=$row6['request_ready_clearing_mode_name'];

                            if ($row6['requst_ready_employee_id']&&$row6['requst_ready_employee_id']!=0) {
                                $query71 = " SELECT * FROM employee_tb WHERE employee_id =" . $row6['requst_ready_employee_id'];
                                $result71 = $this->B_db->run_query($query71);
                                $employee = $result71[0];

                                if ($employee['employee_name'] == null) {
                                    $record6['employee_name'] = null;
                                } else {
                                    $record6['employee_name'] = $employee['employee_name'];
                                }
                                if ($employee['employee_family'] == null) {
                                    $record6['employee_family'] = null;
                                } else {
                                    $record6['employee_family'] = $employee['employee_family'];
                                }
                            }

                            //*************************************************************************************************************
                            $query61=" SELECT * FROM requst_ready_image_tb,image_tb WHERE  image_code=requst_ready_image_code AND  requst_ready_request_id=".$request_id;
                            $result61 = $this->B_db->run_query($query61);
                            $output61 =array();
                            foreach($result61 as $row61)
                            {
                                $result1 = $this->B_db->get_image($row61['requst_ready_image_code']);
                                $image = $result1[0];

                                $record61['image_url']=$image['image_url'];
                                $record61['image_tumb_url']=$image['image_tumb_url'];
                                $record61['image_name']=$row61['image_name'];
                                $record61['image_desc']=$row61['image_desc'];
                                $output61[]=$record61;
                            }
                            $record6['request_ready_image_tb']=$output61;

                            //*************************************************************************************************************
                            $query62=" SELECT * FROM request_file_tb WHERE request_file_request_id=".$request_id;
                            $result62 = $this->B_db->run_query($query62);
                            $output62 =array();
                                foreach($result62 as $row62)
                            {

                                $record62['request_file_url']=IMGADD.$row62['request_file_url'];
                                $record62['request_file_desc']=$row62['request_file_desc'];
                                $output62[]=$record62;
                            }
                            $record6['request_ready_file_tb']=$output62;

                            //*************************************************************************************************************
                            $output6[]=$record6;
                        }
                        $record['request_ready']=$output6;

                        //***************************************************************************************************************

                        //***************************************************************************************************************
                        $query5=" SELECT * FROM request_delivered_tb,state_tb,city_tb,delivery_mode_tb WHERE delivery_mode_id=request_delivered_mode_id AND state_id=request_delivered_state_id AND city_id=request_delivered_city_id AND request_delivered_request_id=".$row['request_id'];
                        $result5 = $this->B_db->run_query($query5);
                        $output5 =array();
                            foreach($result5 as $row5)
                        {

                            $record5['request_delivered_timesatmp']=$row5['request_delivered_timesatmp'];
                            $record5['request_delivered_mode']=$row5['delivery_mode_name'];
                            $record5['request_delivered_dsc']=$row5['request_delivered_dsc'];
                            $record5['request_delivered_state']=$row5['state_name'];
                            $record5['request_delivered_city']=$row5['city_name'];


                            $result51 = $this->B_db->get_image($row5['request_delivered_receipt_image_code']);
                            $image = $result51[0];

                            if($image['image_tumb_url']==null){ $record5['user_pey_image_turl']=null;}else{ $record5['user_pey_image_turl']=$image['image_tumb_url'];}
                            if($image['image_url']==null){ $record5['user_pey_image_url']=null;}else{$record5['user_pey_image_url']=$image['image_url'];}

                            $output5[]=$record5;
                        }
                        $record['request_delivered']=$output5;

                        //***************************************************************************************************************

                        $query4=" SELECT * FROM request_img_tb,image_tb WHERE  image_code=request_img_image_code AND request_img_request_id=".$request_id;
                        $result4 = $this->B_db->run_query($query4);
                        $output4 =array();
                            foreach($result4 as $row4)
                        {
                            $result1 = $this->B_db->get_image($row4['request_img_image_code']);
                            $image = $result1[0];

                            $record4['image_url']=$image['image_url'];
                            $record4['image_tumb_url']=$image['image_tumb_url'];
                            $record4['image_name']=$row4['image_name'];
                            $record4['image_desc']=$row4['image_desc'];
                            $output4[]=$record4;
                        }
                        $record['request_image']=$output4;

                        //***************************************************************************************************************

                        $query8=" SELECT * FROM request_financial_approval_tb,employee_tb WHERE request_financial_approval_employee_id=employee_id AND request_financial_approval_request_id=".$request_id;
                        $result8 = $this->B_db->run_query($query8);
                        $output8 =array();
                            foreach($result8 as $row8)
                        {

                            $record8['request_financial_approval_id']=$row8['request_financial_approval_id'];
                            $record8['request_financial_approval_employee_id']=$row8['request_financial_approval_employee_id'];
                            $record8['request_financial_approval_date']=$row8['request_financial_approval_date'];
                            $record8['request_financial_approval_desc']=$row8['request_financial_approval_desc'];
                            $record8['request_financial_approval']=$row8['request_financial_approval'];
                            $record8['request_financial_approval_price']=$row8['request_financial_approval_price'];
                            $record8['request_financial_approval_difference_price']=$row8['request_financial_approval_difference_price'];
                            $record8['employee_name']=$row8['employee_name'];
                            $record8['employee_family']=$row8['employee_family'];
                            $record8['employee_mobile']=$row8['employee_mobile'];
                            $output8[]=$record8;
                        }
                        $record['request_financial_approval']=$output8;
                        //***************************************************************************************************************
                        //***************************************************************************************************************

                        $query9=" SELECT * FROM request_financial_doc_tb,employee_tb,request_financial_paying_tb WHERE
		 request_financial_doc_employee_id=employee_id AND
		 request_financial_doc_id=request_financial_paying_doc_id AND
		  request_financial_paying_request_id=".$request_id;
                        $result9 = $this->B_db->run_query($query9);
                        $output9 =array();
                            foreach($result9 as $row9)
                        {

                            $record9['request_financial_doc_id']=$row9['request_financial_doc_id'];
                            $record9['request_financial_doc_price']=$row9['request_financial_doc_price'];
                            $record9['request_financial_doc_num']=$row9['request_financial_doc_num'];
                            $record9['request_financial_doc_numdoc']=$row9['request_financial_doc_numdoc'];
                            $record9['request_financial_doc_date']=$row9['request_financial_doc_date'];
                            $record9['request_financial_doc']=$row9['request_financial_doc'];
                            $record9['request_financial_doc_peydate']=$row9['request_financial_doc_peydate'];
                            $record9['request_financial_doc_code']=$row9['request_financial_doc_code'];
                            $record9['request_financial_doc_employee_id']=$row9['request_financial_doc_employee_id'];
                            $record9['employee_name']=$row9['employee_name'];
                            $record9['employee_family']=$row9['employee_family'];
                            $record9['employee_mobile']=$row9['employee_mobile'];

                            //*************************************************************************************
                            $record9['request_financial_doc_pey_employee_id']=$row9['request_financial_doc_pey_employee_id'];
                            $query91="select * from employee_tb where employee_id=".$row9['request_financial_doc_pey_employee_id']."";
                            $result91=$this->B_db->run_query($query91);
                            $employee=$result91[0];
                            $record9['pey_employee_name']=$employee['employee_name'];
                            $record9['pey_employee_family']=$employee['employee_family'];
                            $record9['pey_employee_mobile']=$employee['employee_mobile'];
                            //*************************************************************************************


                            $output9[]=$record9;
                        }
                        $record['request_financial_doc']=$output9;
                        //***************************************************************************************************************
                        $record['checkfinancialdoc']='0';

                        $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,"cnt"=>$count[0]['cnt']
                    ,'desc'=>'لیست درخواست ها با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                }else{
                    echo json_encode(array('result'=>$employeetoken[0]
                    ,"data"=>$employeetoken[1]
                    ,'desc'=>$employeetoken[2]));

                }
            }else
                if($command=="add_doc")
                {
                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','financialpaing');
                    $request_financial_doc_price=$this->post('request_financial_doc_price') ;
                    $request_financial_doc_num=$this->post('request_financial_doc_num') ;
                    $request_financial_doc_numdoc=$this->post('request_financial_doc_numdoc') ;
                    $request="";

                    if($employeetoken[0]=='ok')
                    {
                        $query="INSERT INTO request_financial_doc_tb( request_financial_doc_price, request_financial_doc_num,  request_financial_doc_numdoc     , request_financial_doc_date,request_financial_doc_employee_id) VALUES
                                                    ('$request_financial_doc_price','".$request_financial_doc_num."','".$request_financial_doc_numdoc."', now()                 , ".$employeetoken[1]." )";
                        $result = $this->B_db->run_query_put($query);
                        $request_financial_doc_id=$this->db->insert_id(); 
                        if($result){
                            if(isset($_REQUEST['array_request_id'])){
                                $array_request_id =$_REQUEST['array_request_id'];

                                foreach($array_request_id as $request_id) {
                                    $request=$request. $request_id;

                                    $query1="INSERT INTO request_financial_paying_tb( request_financial_paying_request_id, request_financial_paying_doc_id) VALUES
                                                    (".$request_id."         ,         ".$request_financial_doc_id."  )";
                                    $result1 = $this->B_db->run_query_put($query1);
                                }
                            }

                            echo json_encode(array('result'=>"ok"
                            ,"data"=>$request
                            ,'desc'=>' سند صادر شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>$request
                            ,'desc'=>' سند صادر نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]));

                    }
                }
                else if ($command=="get_doc")
                {//register marketer
                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','financialpaing');
                    if($employeetoken[0]=='ok')
                    {


                        $query="select * from request_financial_doc_tb,employee_tb where request_financial_doc_employee_id=employee_id AND ";
                        if($_REQUEST['mode']){
                            $mode=$this->post('mode');
                            if($mode=='doc_paying'){
                                $query.=" request_financial_doc=0 AND "	 ;
                            }else if($mode=='doc_payed'){
                                $query.=" request_financial_doc=1 AND "	 ;
                            }
                        }

                        if(isset($_REQUEST['agent_id'])){
                            $agent_id=$this->post('agent_id') ;
                            $query.=" request_financial_doc_id IN (
		 select request_financial_paying_doc_id from request_financial_paying_tb,request_tb
		 where request_financial_paying_request_id=request_id AND request_agent_id=".$agent_id.") ";
                        }else{
                            $query.=" 1 ";
                        }
                        $result = $this->B_db->run_query($query);
                        $output =array();
                        foreach($result as $row)
                        {
                            $record=array();
                            $record['request_financial_doc_id']=$row['request_financial_doc_id'];
                            $record['request_financial_doc_num']=$row['request_financial_doc_num'];
                            $record['request_financial_doc_price']=$row['request_financial_doc_price'];
                            $record['request_financial_doc_numdoc']=$row['request_financial_doc_numdoc'];
                            $record['request_financial_doc_date']=$row['request_financial_doc_date'];
                            $record['request_financial_doc']=$row['request_financial_doc'];
                            $record['request_financial_doc_peydate']=$row['request_financial_doc_peydate'];
                            $record['request_financial_doc_code']=$row['request_financial_doc_code'];
                            $record['request_financial_doc_employee_id']=$row['request_financial_doc_employee_id'];

                            $record['employee_name']=$row['employee_name'];
                            $record['employee_family']=$row['employee_family'];
                            $record['employee_mobile']=$row['employee_mobile'];
                            //*************************************************************************************
                            $record['request_financial_doc_pey_employee_id']=$row['request_financial_doc_pey_employee_id'];
                            $query1="select * from employee_tb where employee_id=".$row['request_financial_doc_pey_employee_id']."";
                            $result1=$this->B_db->run_query($query1);
                            $employee=$result1[0];
                            $record['pey_employee_name']=$employee['employee_name'];
                            $record['pey_employee_family']=$employee['employee_family'];
                            $record['pey_employee_mobile']=$employee['employee_mobile'];
                            //*************************************************************************************
                            $query2="select * from request_financial_doc_tb,request_financial_paying_tb,request_tb,agent_tb where
		  request_financial_doc_id=request_financial_paying_doc_id
		  AND request_financial_paying_request_id=request_id
		  AND request_agent_id=agent_id
		  AND  request_financial_doc_id=".$row['request_financial_doc_id']."";
                            $result2=$this->B_db->run_query($query2);
                            $agent=$result2[0];
                            $record['agent_id']=$agent['agent_id'];
                            $record['agent_name']=$agent['agent_name'];
                            $record['agent_family']=$agent['agent_family'];
                            $record['agent_mobile']=$agent['agent_mobile'];
                            $record['agent_banknum']=$agent['agent_banknum'];
                            $record['agent_bankname']=$agent['agent_bankname'];
                            $record['agent_banksheba']=$agent['agent_banksheba'];
                            //************************************************************************************

                            $output[]=$record;
                        }
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>$output
                        ,'desc'=>'مشحصات اسناد با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]));

                    }


                }
                else if ($command=="pey_doc")
                {//register marketer
                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','financialpaing');
                    if($employeetoken[0]=='ok')
                    {
                        $request_financial_doc_id=$this->post('request_financial_doc_id');
                        $request_financial_doc_peydate=$this->post('request_financial_doc_peydate');
                        $request_financial_doc_code=$this->post('request_financial_doc_code');
                        $query="UPDATE request_financial_doc_tb SET request_financial_doc_peydate='$request_financial_doc_peydate',request_financial_doc_code='$request_financial_doc_code',request_financial_doc_pey_employee_id=".$employeetoken[1].",request_financial_doc=1  WHERE request_financial_doc_id=$request_financial_doc_id";
                        $result = $this->B_db->run_query_put($query);
                        $output = array();
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>$output
                        ,'desc'=>'مشحصات رشته بیمه با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]));

                    }


                }

                else if ($command=="delete_doc")
                {//register marketer
                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','financialpaing');
                    $request_financial_doc_id=$this->post('request_financial_doc_id') ;
                    if($employeetoken[0]=='ok')
                    {
                        $query2="select * from request_financial_doc_tb where request_financial_doc_id=$request_financial_doc_id";
                        $result2=$this->B_db->run_query($query2);
                        $request_financial_doc=$result2[0];
                        if($request_financial_doc['request_financial_doc']=='0'){
                            $query="DELETE FROM request_financial_paying_tb WHERE request_financial_paying_doc_id=$request_financial_doc_id ";
                            $result = $this->B_db->run_query_put($query);

                            $query1="DELETE FROM request_financial_doc_tb WHERE request_financial_doc_id=$request_financial_doc_id ";
                            $result = $this->B_db->run_query_put($query1);

                            echo json_encode(array('result'=>"ok"
                            ,"data"=>''
                            ,'desc'=>'سند مورد نظر حذف گردید'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                        }else{
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>$request_financial_doc_id
                            ,'desc'=>'سند مورد نظر پرداخت شده است و قابل حذف نمیباشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                        }
                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]));

                    }


                }

                else if ($command=="get_fieldinsurance")
                {//register marketer

                    $query="select * from fieldinsurance_tb where 1";
                    $result = $this->B_db->run_query($query);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record=array();
                        $record['fieldinsurance_id']=$row['fieldinsurance_id'];
                        $record['fieldinsurance_fa']=$row['fieldinsurance_fa'];
                        $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'مشحصات رشته بیمه با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                }
        }

    }

}