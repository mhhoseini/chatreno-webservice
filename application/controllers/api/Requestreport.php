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
class Requestreport extends REST_Controller {

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
        if ($command=="getuser_request")
        {//register marketer

            $usertoken=checkusertoken($user_token_str);
            if($usertoken[0]=='ok')
            {
                $user_id=$usertoken[1];
                $query1="select * from request_tb,request_state,fieldinsurance_tb,user_tb where user_id=request_user_id AND fieldinsurance=request_fieldinsurance AND request_state_id=request_last_state_id  AND user_id=".$user_id;
                $query2="select count(*) AS cnt from request_tb,request_state,fieldinsurance_tb,user_tb where user_id=request_user_id AND fieldinsurance=request_fieldinsurance AND request_state_id=request_last_state_id  AND user_id=".$user_id;
                $query="";
                if(isset($_REQUEST['request_last_state_id'])){
                    $request_last_state_id=$this->post('request_last_state_id');
                    $query.=" AND request_last_state_id=".$request_last_state_id."";
                }
                $query.=' ORDER BY request_id  DESC';

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
                    if($row['request_company_id']!='0'){
                        $query110=" SELECT * FROM company_tb WHERE  company_id=".$row['request_company_id'];
                        $result110 = $this->B_db->run_query($query110);
                        $company=$result110[0];


                        $record['company_name']=$company['company_name'];
                        $record['company_logo_url']=IMGADD.$company['company_logo_url'];

                    }else{
                        $record['company_name']="";
                        $record['company_logo_url']="https://aref24.com/imagess/big-logo.png";

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
                    if($row['request_adderss_id']){
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
                    }
//*************************************************************************************************************
                    if($row['request_addressofinsured_id']){
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
                    }

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



                        $result1 = $this->B_db->get_image($row1['user_pey_image_code']);
                        $image = $result1[0];

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
                    if(!empty($result7)){
                        foreach($result7 as $row7)
                        {
                            $record7['staterequest_id']=$row7['staterequest_id'];
                            $record7['request_state_name']=$row7['request_state_name'];
                            $record7['staterequest_timestamp']=$row7['staterequest_timestamp'];
                            $record7['staterequest_desc']=$row7['staterequest_desc'];

                            if($row7['staterequest_agent_id']){
                                $query71=" SELECT * FROM agent_tb WHERE agent_id =".$row7['staterequest_agent_id'];
                                $result71=$this->B_db->run_query($query71);
                                $agent=$result71[0];
                                if($agent['agent_code']==null){ $record7['agent_code']=null;}else{ $record7['agent_code']=$agent['agent_code'];}
                                if($agent['agent_name']==null){ $record7['agent_name']=null;}else{$record7['agent_name']=$agent['agent_name'];}
                                if($agent['agent_family']==null){ $record7['agent_family']=null;}else{$record7['agent_family']=$agent['agent_family'];}
                            }
                            $output7[]=$record7;
                        }
                        $record['request_stats']=$output7;
                    }

                    //***************************************************************************************************************
                    //***************************************************************************************************************
                    $query6=" SELECT * FROM requst_ready_tb WHERE  requst_ready_request_id=".$request_id;
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
                        //*************************************************************************************************************
                        $query61=" SELECT * FROM requst_ready_image_tb,image_tb WHERE  image_code=requst_ready_image_code AND requst_ready_request_id=".$request_id;
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

                        $result1 = $this->B_db->get_image($row5['request_delivered_receipt_image_code']);
                        $image = $result1[0];

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
                    $output[]=$record;
                }
                echo json_encode(array('result'=>"ok"
                ,"data"=>$output
                ,"cnt"=>$count[0]['cnt']
                ,'desc'=>'لیست درخواست ها با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

            }else{
                echo json_encode(array('result'=>$usertoken[0]
                ,"data"=>$usertoken[1]
                ,'desc'=>$usertoken[2]));
            }
        }else
            if ($command=="getuser_request_small")
            {//register marketer

                $usertoken=checkusertoken($user_token_str);
                if($usertoken[0]=='ok')
                {
                    $user_id=$usertoken[1];
                    $query1="select * from request_tb,request_state,fieldinsurance_tb,user_tb where user_id=request_user_id AND fieldinsurance=request_fieldinsurance AND request_state_id=request_last_state_id  AND user_id=".$user_id;

                        $query1.=" AND( request_last_state_id=10 OR  request_last_state_id=11 ) ";

                    $query1.=' ORDER BY request_id  DESC';



                    $result = $this->B_db->run_query($query1);

                    $output =array();
                    foreach($result as $row)
                    {
                        $record=array();

                        $record['request_id']=$row['request_id'];
                        $request_id=$row['request_id'];
                        $record['request_company_id']=$row['request_company_id'];
                        if($row['request_company_id']!='0'){
                            $query110=" SELECT * FROM company_tb WHERE  company_id=".$row['request_company_id'];
                            $result110 = $this->B_db->run_query($query110);
                            $company=$result110[0];


                            $record['company_name']=$company['company_name'];
                            $record['company_logo_url']=IMGADD.$company['company_logo_url'];

                        }else{
                            $record['company_name']="";
                            $record['company_logo_url']="https://aref24.com/imagess/big-logo.png";

                        }

                        //*************************************************************************************************************


//******************************************************************************************

                        //***************************************************************************************************************
                        //***************************************************************************************************************
                        $query6=" SELECT * FROM requst_ready_tb WHERE  requst_ready_request_id=".$request_id;
                        $result6 = $this->B_db->run_query($query6);
                        foreach($result6 as $row6)
                        {
                            $record['requst_ready_start_date']=$row6['requst_ready_start_date'];
                            $record['requst_ready_end_date']=$row6['requst_ready_end_date'];
                            $record['requst_ready_end_price']=$row6['requst_ready_end_price'];
                            $record['requst_ready_num_ins']=$row6['requst_ready_num_ins'];
                            $record['requst_ready_code_rayane']=$row6['requst_ready_code_rayane'];
                            $record['requst_ready_code_penalty']=$row6['requst_ready_code_penalty'];
                            $record['requst_ready_code_yekta']=$row6['requst_ready_code_yekta'];
                            $record['requst_ready_name_insurer']=$row6['requst_ready_name_insurer'];
                            $record['requst_ready_code_insurer']=$row6['requst_ready_code_insurer'];
                            $record['requst_suspend_desc']=$row6['requst_suspend_desc'];

                            //*************************************************************************************************************

                            //*************************************************************************************************************
                        }
                        //***************************************************************************************************************

                        //***************************************************************************************************************

                        $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'لیست درخواست ها با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                }else{
                    echo json_encode(array('result'=>$usertoken[0]
                    ,"data"=>$usertoken[1]
                    ,'desc'=>$usertoken[2]));
                }
            }else
            if ($command=="getuser_request_detail")
            {//register marketer

                $usertoken=checkusertoken($user_token_str);
                if($usertoken[0]=='ok')
                {
                    $request_id = $this->post("request_id");
                    $user_id=$usertoken[1];
                    $query="select * from request_tb,company_tb,request_state,fieldinsurance_tb,user_tb where user_id=request_user_id AND fieldinsurance=request_fieldinsurance AND request_state_id=request_last_state_id AND company_id=request_company_id AND user_id=".$user_id." AND request_id=".$request_id;
                    if(isset($_REQUEST['request_last_state_id'])){
                        $request_last_state_id=$this->post('request_last_state_id');
                        $query.=" AND request_last_state_id=".$request_last_state_id."";
                    }
                    $result = $this->B_db->run_query($query);
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
                        if($row['request_adderss_id']){
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
                        }else{
                            $record['request_adderss']=[];
                        }
//*************************************************************************************************************
                        if($row['request_addressofinsured_id']){
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
                        }else{
                            $record['request_addressofinsured']=[];

                        }
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

                            $result1 = $this->B_db->get_image($row1['user_pey_image_code']);
                            $image = $result1[0];

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
                        if(!empty($result7)){
                            foreach($result7 as $row7)
                            {
                                $record7['staterequest_id']=$row7['staterequest_id'];
                                $record7['request_state_name']=$row7['request_state_name'];
                                $record7['staterequest_timestamp']=$row7['staterequest_timestamp'];
                                $record7['staterequest_desc']=$row7['staterequest_desc'];

                                if($row7['staterequest_agent_id']){
                                    $query71=" SELECT * FROM agent_tb WHERE agent_id =".$row7['staterequest_agent_id'];
                                    $result71=$this->B_db->run_query($query71);
                                    $agent=$result71[0];
                                    if($agent['agent_code']==null){ $record7['agent_code']=null;}else{ $record7['agent_code']=$agent['agent_code'];}
                                    if($agent['agent_name']==null){ $record7['agent_name']=null;}else{$record7['agent_name']=$agent['agent_name'];}
                                    if($agent['agent_family']==null){ $record7['agent_family']=null;}else{$record7['agent_family']=$agent['agent_family'];}
                                }
                                $output7[]=$record7;
                            }
                            $record['request_stats']=$output7;
                        }

                        //***************************************************************************************************************
                        //***************************************************************************************************************
                        $query6=" SELECT * FROM requst_ready_tb WHERE  requst_ready_request_id=".$request_id;
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
                            //*************************************************************************************************************
                            $query61=" SELECT * FROM requst_ready_image_tb,image_tb WHERE  image_code=requst_ready_image_code AND requst_ready_request_id=".$request_id;
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

                            $result1 = $this->B_db->get_image($row5['request_delivered_receipt_image_code']);
                            $image = $result1[0];

                            if($image['image_tumb_url']==null){ $record5['user_pey_image_turl']=null;}else{ $record5['user_pey_image_turl']=$image['image_tumb_url'];}
                            if($image['image_url']==null){ $record5['user_pey_image_url']=null;}else{$record5['user_pey_image_url']=$image['image_url'];}
                            $output5[]=$record5;
                        }
                        $record['request_delivered']=$output5;
                        //***************************************************************************************************************
                        $query4=" SELECT * FROM request_img_tb,image_tb WHERE image_id=request_img_image_code AND request_img_request_id=".$request_id;
                        $result4 = $this->B_db->run_query($query4);
                        $output4 =array();
                        foreach($result4 as $row4)
                        {
                            $result1 = $this->B_db->get_image($row4['image_code']);
                            $image = $result1[0];


                            $record4['image_code']=$row4['image_code'];
                            $record4['image_url']=$image['image_url'];
                            $record4['image_tumb_url']=$image['image_tumb_url'];
                            $record4['image_name']=$row4['image_name'];
                            $record4['image_desc']=$row4['image_desc'];
                            $output4[]=$record4;
                        }
                        $record['request_image']=$output4;
                        $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'لیست درخواست ها با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                }else{
                    echo json_encode(array('result'=>$usertoken[0]
                    ,"data"=>$usertoken[1]
                    ,'desc'=>$usertoken[2]));
                }
            }else
                if ($command=="get_requesr")
                {
                    $usertoken=checkusertoken($user_token_str);
                    if($usertoken[0]=='ok')
                    {
                        $request_id=$this->post('request_id') ;
                        $result3=$this->B_requests->get_request($request_id,$usertoken[1]);
                        $request=$result3[0];
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>$request
                        ,'desc'=>'درخواست مورد نظر ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        echo json_encode(array('result'=>$usertoken[0]
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
                            $request_id=$this->post('request_id') ;
                            $request_usercancel_desc=$this->post('request_usercancel_desc') ;
                            $result3=$this->B_requests->get_state_request($request_id);
                            $request=$result3[0];
                            if($request['request_last_state_id']!=9&&$request['request_last_state_id']!=10&&$request['request_last_state_id']!=11){
                                $result=$this->B_requests->get_request_usercancel($request_id);
                                if (empty($result))
                                {
                                    $request_usercancel_id = $this->B_requests->add_request_usercancel($request_id,$request_usercancel_desc);
                                    $this->B_requests->request_refuse(12,$request_id);
                                    $desc='درخواست انصراف توسط کاربر به دلیل '.$request_usercancel_desc.' انجام شد ';
                                    $this->B_requests->set_request_refuse($request_id,12,$desc);
                                    $this->B_requests->del_request_releations($request_id);
                                    $result2=$this->B_requests->get_request_peycash($request_id);
                                    foreach($result2 as $row)
                                    {
                                        $desc='بازگشت پرداختی نقدی با شماره پیگیری'.$row['user_pey_code']. ' و جزئیات '.$row['user_pey_desc'].'برای بازگشت درخواست شماره '.$request_id;
                                        $result = $this->B_user->set_user_wallet($user_id ,$row['user_pey_amount'],'add',$desc ,$row['user_pey_code']);
                                    }
                                    $this->B_requests->del_request_pey($request_id);
                                    echo json_encode(array('result'=>"ok"
                                    ,"data"=>array('request_usercancel_id'=>$request_usercancel_id)
                                    ,'desc'=>'درخواست لغو ثبت شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                                }else{
                                    $request_usercancel=$result[0];
                                    echo json_encode(array('result'=>"error"
                                    ,"data"=>array('request_usercancel_id'=>$request_usercancel['request_usercancel_id'])
                                    ,'desc'=>'درخواست لغو تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }
                            }else{
                                echo json_encode(array('result'=>"error"
                                ,"data"=>array('request_state_name'=>$request['request_state_name'])
                                ,'desc'=>'درخواست با این وضعیت قابل کنسل کردن نیست و باید درخواست الحاقیه دهید'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
                        }else{
                            echo json_encode(array('result'=>$usertoken[0]
                            ,"data"=>$usertoken[1]
                            ,'desc'=>$usertoken[2]));

                        }
                    }else if ($command=="getstate_request")
                    {//register marketerstate

                        $request_request_id=$this->post('request_id') ;

                        $usertoken=checkusertoken($user_token_str);
                        if($usertoken[0]=='ok')
                        {
                            $user_id=$usertoken[1];
                            $result=$this->B_requests->get_request_state($request_request_id);
                            $output =array();
                            foreach($result as $row)
                            {
                                $record['staterequest_id']=$row['staterequest_id'];
                                $record['staterequest_state_id']=$row['staterequest_state_id'];
                                $record['request_state_name']=$row['request_state_name'];
                                $record['staterequest_timestamp']=$row['staterequest_timestamp'];
                                $record['staterequest_desc']=$row['staterequest_desc'];
                                $output[]=$record;
                            }
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>$output
                            ,'desc'=>'لیست وضعیت های درخواست  با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            echo json_encode(array('result'=>$usertoken[0]
                            ,"data"=>$usertoken[1]
                            ,'desc'=>$usertoken[2]));
                        }
                    }
                    else if ($command=="getpey_request")
                    {//register marketerstate

                        $user_pey_request_id=$this->post('request_id') ;
                        $usertoken=checkusertoken($user_token_str);
                        if($usertoken[0]=='ok')
                        {
                            $user_id=$usertoken[1];
                            $result=$this->B_requests->get_user_pey($user_pey_request_id);
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
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>$output
                            ,'desc'=>'لیست  پرداختی های درخواست  با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            echo json_encode(array('result'=>$usertoken[0]
                            ,"data"=>$usertoken[1]
                            ,'desc'=>$usertoken[2]));

                        }
                    }else if ($command=="getbackuser_request")
                    {//register marketerstate

                        $request_backuser_request_id=$this->post('request_id') ;
                        $usertoken=checkusertoken($user_token_str);
                        if($usertoken[0]=='ok')
                        {
                            $user_id=$usertoken[1];
                            $result=$this->B_requests->request_backuser($request_backuser_request_id);
                            $output =array();
                            foreach($result as $row)
                            {
                                $record['request_backuser_timestamp']=$row['request_backuser_timestamp'];
                                $record['request_backuser_desc']=$row['request_backuser_desc'];
                                $record['request_backuser_agent_id']=$row['request_backuser_agent_id'];
                                $output[]=$record;
                            }
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>$output
                            ,'desc'=>'جزئیات درخواست  با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            echo json_encode(array('result'=>$usertoken[0]
                            ,"data"=>$usertoken[1]
                            ,'desc'=>$usertoken[2]));
                        }
                    }else if ($command=="getdeficitpey_request")
                    {//register marketerstate

                        $deficit_pey_request_id=$this->post('request_id') ;
                        $usertoken=checkusertoken($user_token_str);
                        if($usertoken[0]=='ok')
                        {
                            $user_id=$usertoken[1];
                            $result=$this->B_user->deficit_user_pey($deficit_pey_request_id);
                            $output =array();
                            foreach($result as $row)
                            {
                                $record['deficit_pey_amount']=$row['deficit_pey_amount'];
                                $record['deficit_pey_reason']=$row['deficit_pey_reason'];
                                $record['user_pey_desc']=$row['user_pey_desc'];
                                $output[]=$record;
                            }
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>$output
                            ,'desc'=>'جزئیات درخواست  با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                            //****************************************************************************************************************

                        }else{
                            echo json_encode(array('result'=>$usertoken[0]
                            ,"data"=>$usertoken[1]
                            ,'desc'=>$usertoken[2]));
                        }
                    }
                    else if ($command=="getsuspend_request")
                    {//register marketerstate

                        $requst_suspend_request_id=$this->post('request_id') ;
                        $usertoken=checkusertoken($user_token_str);
                        if($usertoken[0]=='ok')
                        {
                            $user_id=$usertoken[1];
                            $result=$this->B_requests->requst_suspend($requst_suspend_request_id);
                            $output =array();
                            foreach($result as $row)
                            {
                                $record['requst_suspend_timestamp']=$row['requst_suspend_timestamp'];
                                $record['requst_suspend_end_date']=$row['requst_suspend_end_date'];
                                $record['requst_suspend_desc']=$row['requst_suspend_desc'];
                                $output[]=$record;
                            }
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>$output
                            ,'desc'=>'جزئیات درخواست  با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            echo json_encode(array('result'=>$usertoken[0]
                            ,"data"=>$usertoken[1]
                            ,'desc'=>$usertoken[2]));
                        }
                    }
                    else if ($command=="getdifficult_request")
                    {//register marketerstate

                        $request_difficult_request_id=$this->post('request_id') ;
                        $usertoken=checkusertoken($user_token_str);
                        if($usertoken[0]=='ok')
                        {
                            $user_id=$usertoken[1];
                            $result=$this->B_requests->requst_difficult($request_difficult_request_id);
                            $output =array();
                            foreach($result as $row)
                            {
                                $record['request_difficult_timestamp']=$row['request_difficult_timestamp'];
                                $record['request_difficult_desc']=$row['request_difficult_desc'];
                                $output[]=$record;
                            }
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>$output
                            ,'desc'=>'جزئیات درخواست  با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            echo json_encode(array('result'=>$usertoken[0]
                            ,"data"=>$usertoken[1]
                            ,'desc'=>$usertoken[2]));
                        }
                    }
                    else if ($command=="getready_request")
                    {//register marketerstate

                        $requst_ready_request_id=$this->post('request_id') ;
                        $usertoken=checkusertoken($user_token_str);
                        if($usertoken[0]=='ok')
                        {
                            $user_id=$usertoken[1];
                            $result=$this->B_requests->get_request_ready($requst_ready_request_id);
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
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>$output
                            ,'desc'=>'جزئیات درخواست  با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                            //****************************************************************************************************************

                        }else{
                            echo json_encode(array('result'=>$usertoken[0]
                            ,"data"=>$usertoken[1]
                            ,'desc'=>$usertoken[2]));
                        }
                    }
                    else if ($command=="getready_image_request")
                    {//register marketerstate

                        $requst_ready_request_id=$this->post('request_id') ;
                        $usertoken=checkusertoken($user_token_str);
                        if($usertoken[0]=='ok')
                        {
                            $user_id=$usertoken[1];
                            $result=$this->B_requests->requst_ready_image($requst_ready_request_id);
                            $output =array();
                            foreach($result as $row)
                            {
                                $record['requst_ready_image_code']=$row['requst_ready_image_code'];
                                $output[]=$record;
                            }
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>$output
                            ,'desc'=>'عکسهای درخواست  با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            echo json_encode(array('result'=>$usertoken[0]
                            ,"data"=>$usertoken[1]
                            ,'desc'=>$usertoken[2]));
                        }
                    }
                    else if ($command=="getdelivered_request")
                    {//register marketerstate

                        $request_delivered_request_id=$this->post('request_id') ;
                        $usertoken=checkusertoken($user_token_str);
                        if($usertoken[0]=='ok')
                        {
                            $user_id=$usertoken[1];
                            $result=$this->B_requests->request_delivered_city($request_delivered_request_id);
                            $output =array();
                            foreach($result as $row)
                            {
                                $record['request_delivered_timesatmp']=$row['request_delivered_timesatmp'];
                                $record['request_delivered_mode']=$row['request_delivered_mode_id'];
                                $record['request_delivered_dsc']=$row['request_delivered_dsc'];
                                $record['request_delivered_receipt_image_code']=$row['request_delivered_receipt_image_code'];
                                $record['request_delivered_state_id']=$row['request_delivered_state_id'];
                                $record['state_name']=$row['state_name'];
                                $record['request_delivered_city_id']=$row['request_delivered_city_id'];
                                $record['city_name']=$row['city_name'];
                                $output[]=$record;
                            }
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>$output
                            ,'desc'=>'جزئیات درخواست  با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                            //****************************************************************************************************************

                        }else{
                            echo json_encode(array('result'=>$usertoken[0]
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
                                $record['deficit_pey_request_id']=$row['deficit_pey_request_id'];
                                $record['company_name']=$row['company_name'];
                                $record['company_logo_url']=IMGADD.$row['company_logo_url'];
                                $record['fieldinsurance_fa']=$row['fieldinsurance_fa'];
                                $record['deficit_pey_amount']=$row['deficit_pey_amount'];
                                $record['deficit_pey_reason']=$row['deficit_pey_reason'];
                                $record['deficit_pey_user_pey_id']=$row['deficit_pey_user_pey_id'];
                                $record['deficit_pey_user_pey_date']=$row['deficit_pey_user_pey_date'];
                                $output[]=$record;
                            }
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>$output
                            ,'desc'=>'جزئیات درخواست  با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            echo json_encode(array('result'=>$usertoken[0]
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
                                $record['request_visit_id']=$row['request_visit_id'];
                                $record['request_visit_request_id']=$row['request_visit_request_id'];
                                $record['company_name']=$row['company_name'];
                                $record['company_logo_url']=IMGADD.$row['company_logo_url'];
                                $record['fieldinsurance_fa']=$row['fieldinsurance_fa'];
                                $record['request_visit_date']=$row['request_visit_date'];
                                $output[]=$record;
                            }
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>$output
                            ,'desc'=>'جزئیات درخواست  با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            echo json_encode(array('result'=>$usertoken[0]
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
                            $request_id=$this->post('request_id') ;
                            $result=$this->B_requests->get_request_by(8 ,$request_id);
                            if (!empty($result))
                            {
                                $this->B_requests->request_refuse(3,$request_id);
                                $this->B_requests->set_request_refuse($request_id,3,"تصاویر توسط کاربر ارسال شد.");
                                echo json_encode(array('result'=>"ok"
                                ,"data"=>""
                                ,'desc'=>'وضعیت درخواست به در حال بررسی توسط نماینده تغییر یافت'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }else{
                                echo json_encode(array('result'=>"error"
                                ,"data"=>""
                                ,'desc'=>'وضعیت درخواست مشکل برای صدور نیست و قابل تغییر نمیباشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                            }
                        }else{
                            echo json_encode(array('result'=>$usertoken[0]
                            ,"data"=>$usertoken[1]
                            ,'desc'=>$usertoken[2]));
                        }
                    }
    }
}
