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
 * @link            https://aref24.com
 */
class Reportreferral2 extends REST_Controller {

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
        $this->load->model('B_agent');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if ($command=="get_request")
        {//register marketer
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','reportrefral');
            if($employeetoken[0]=='ok')
            {
                /*
                $query1="select * from request_tb,company_tb,request_state,fieldinsurance_tb,user_tb,agent_tb,organ_tb where user_id=request_user_id AND fieldinsurance=request_fieldinsurance AND request_state_id=request_last_state_id AND company_id=request_company_id  And request_tb.request_agent_id = agent_tb.agent_id And organ_tb.organ_id=request_tb.request_organ ";
                $query1.=" AND (request_last_state_id=10 OR request_last_state_id=11)";

                $query2="select count(*) AS cnt from request_tb,company_tb,request_state,fieldinsurance_tb,user_tb,agent_tb,organ_tb where user_id=request_user_id AND fieldinsurance=request_fieldinsurance AND request_state_id=request_last_state_id AND company_id=request_company_id  And request_tb.request_agent_id = agent_tb.agent_id And organ_tb.organ_id=request_tb.request_organ ";
                $query2.=" AND (request_last_state_id=10 OR request_last_state_id=11)";
                */

                $query1="select * from request_tb,company_tb,request_state,fieldinsurance_tb,user_tb,requst_ready_tb,user_address_tb where 
                request_adderss_id=user_address_id AND user_id=request_user_id AND request_state_id=request_last_state_id AND company_id=request_company_id and request_id=requst_ready_request_id  AND fieldinsurance_id=request_fieldinsurance_id ";
                $query1.=" AND (request_last_state_id=10 OR request_last_state_id=11)";

                $query2="select count(*) AS cnt from request_tb,company_tb,request_state,fieldinsurance_tb,user_tb,requst_ready_tb,user_address_tb where 
                request_adderss_id=user_address_id AND user_id=request_user_id AND request_state_id=request_last_state_id AND company_id=request_company_id and request_id=requst_ready_request_id  AND fieldinsurance_id=request_fieldinsurance_id ";
                $query2.=" AND (request_last_state_id=10 OR request_last_state_id=11)";
                $query='';
                if(!empty($_REQUEST['request_last_state_id'])){
                    $request_last_state_id=$this->post('request_last_state_id');
                    $query.=" AND request_last_state_id  =".$request_last_state_id;
                }
                if(!empty($_REQUEST['request_id'])){
                    $request_id=$this->post('request_id');
                    $query.=" AND request_id=".$request_id."";
                }
                if(!empty($_REQUEST['fieldinsurance_id'])){
                    $request_fieldinsurance=$this->post('fieldinsurance_id');
                    $query.=" AND request_fieldinsurance_id IN (".$request_fieldinsurance.")";
                }
                if(!empty($_REQUEST['agent_id'])){
                    $request_agent_id=$this->post('agent_id');
                    $query.=" AND request_agent_id=".$request_agent_id."";
                }
                if(!empty($_REQUEST['user_mobile'])){
                    $user_mobile=$this->post('user_mobile');
                    $query.=" AND user_mobile=".$user_mobile."";
                }

                if(!empty($_REQUEST['agent_mobile'])){
                    $agent_mobile=$this->post('agent_mobile');
                    $query.=" AND agent_mobile=".$agent_mobile."";
                }
                if(!empty($_REQUEST['user_address_state_id'])){
                    $user_address_state_id=$this->post('user_address_state_id');
                    $query.=" AND user_address_state_id  =".$user_address_state_id;
                }
                if(!empty($_REQUEST['user_address_city_id'])){
                    $user_address_city_id=$this->post('user_address_city_id');
                    $query.=" AND user_address_city_id  =".$user_address_city_id;
                }
                if(!empty($_REQUEST['request_company_id'])){
                    $request_company_id=$this->post('request_company_id');
                    $query.=" AND request_company_id =".$request_company_id;
                }
                if(!empty($_REQUEST['start_requst_ready_timestamp'])){
                    $requst_ready_start_date=$this->post('start_requst_ready_timestamp');
                    $query.=" AND requst_ready_start_date>'".$requst_ready_start_date."'";
                }
                if(!empty($_REQUEST['end_requst_ready_timestamp'])){
                    $requst_ready_end_date=$this->post('end_requst_ready_timestamp');
                    $query.=" AND requst_ready_start_date<='".$requst_ready_end_date."'";
                }

                $query .= ' ORDER BY request_id  DESC ';

                $limit = $this->post("limit");
                $offset = $this->post("offset");
                $limit_state ="";
                if($limit!="" & $offset!="") {
                    $limit_state = " LIMIT " . $offset . "," . $limit;
                }
                // echo $query1.$query.$limit_state;
                //  die;
                $result = $this->B_db->run_query($query1.$query.$limit_state);
                $count  = $this->B_db->run_query($query2.$query);
                $sssssss=$query1.$query.$limit_state;
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

                    $record['request_agent_id']=$row['request_agent_id'];

                    //****************************************************************************
                    $query112=" SELECT * FROM requestpartner1_tb WHERE requestpartner_request_id=$request_id";
                    $result112=$this->B_db->run_query($query112);
                    $requestpartner=$result112[0];
                    $record['requestpartner']=$requestpartner['requestpartner_code'];

                    //*************************************************************************************************************

                    $record['user_id']=$row['user_id'];
                    $record['user_name']=$row['user_name'];
                    $record['user_family']=$row['user_family'];
                    $record['user_mobile']=$row['user_mobile'];
                    $record['fieldinsurance_logo_url']=IMGADD.$row['fieldinsurance_logo_url'];
                    $record['fieldinsurance_id']=$row['fieldinsurance_id'];
                    $record['request_fieldinsurance_fa']=$row['fieldinsurance_fa'];
                    $record['fieldinsurance_commission']=$row['fieldinsurance_commission'];
                    $record['request_description']=$row['request_description'];
                    $record['request_last_state_id']=$row['request_last_state_id'];
                    $record['request_last_state_name']=$row['request_state_name'];



                    //*************************************************************************************************************
                    $overpayment=0;
                    $query0="select sum(user_pey_amount) AS overpayment from user_pey_tb where user_pey_mode='overpayment' AND user_pey_request_id=".$request_id. " GROUP BY user_pey_request_id";
                    $result0 = $this->B_db->run_query($query0);
                    $user_pey0=$result0[0];
                    if($user_pey0['overpayment'])
                    {$overpayment=$user_pey0['overpayment'];}
                    else
                    {$overpayment=0;}

                    $record['user_pey_overpayment']=$overpayment;

                    $query1="select sum(user_pey_amount) AS sumpey from user_pey_tb where not (user_pey_mode='overpayment') AND user_pey_request_id=".$request_id. " GROUP BY user_pey_request_id";
                    $result1 = $this->B_db->run_query($query1);
                    $user_pey=$result1[0];
                    $record['user_pey_amount']=$user_pey['sumpey']-$overpayment;


                    $query2="select sum(user_pey_amount) AS sumcash from user_pey_tb where not (user_pey_mode = 'instalment')  AND  not (user_pey_mode='overpayment') AND user_pey_request_id=".$request_id. " GROUP BY user_pey_request_id";
                    $result2 = $this->B_db->run_query($query2);
                    $user_pey2=$result2[0];
                    $record['user_pey_cash']=$user_pey2['sumcash']-$overpayment;

                    $query20="select sum(user_pey_amount) AS suminstalment from user_pey_tb where user_pey_mode = 'instalment' AND user_pey_request_id=".$request_id. " GROUP BY user_pey_request_id";
                    $result20 = $this->B_db->run_query($query20);
                    $user_pey20=$result20[0];
                    if($user_pey20['suminstalment'])
                    {$record['user_pey_instalment']=$user_pey20['suminstalment'];}
                    else
                    {$record['user_pey_instalment']=0;}

                    $query21="select sum(user_pey_amount) AS suminstalment from user_pey_tb,instalment_check_tb where instalment_check_user_pey_id=user_pey_id AND instalment_check_pass=1 AND user_pey_mode = 'instalment' AND user_pey_request_id=".$request_id. " GROUP BY user_pey_request_id";
                    $result21 = $this->B_db->run_query($query21);
                    $user_pey21=$result21[0];
                    if($user_pey21['suminstalment'])
                    {$record['user_pey_instalment_pass']=$user_pey21['suminstalment'];}
                    else
                    {$record['user_pey_instalment_pass']=0;}



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

                    //***************************************************************************************************************
                    //***************************************************************************************************************
                    //***************************************************************************************************************
                    $requst_ready_code_penalty=0;
                    $requst_ready_end_price=0;
                    $query6=" SELECT * FROM requst_ready_tb WHERE  requst_ready_request_id=".$request_id;
                    $result6 = $this->B_db->run_query($query6);
                    $output6 =array();
                    foreach($result6 as $row6)
                    {
                        $requst_ready_end_price=$row6['requst_ready_end_price'];
                        $requst_ready_code_penalty=$row6['requst_ready_code_penalty'];

                        $record6['requst_ready_start_date']=$row6['requst_ready_start_date'];
                        $record['requst_ready_start_date']=$row6['requst_ready_start_date'];
                        $record['requst_ready_timestamp']=$row6['requst_ready_timestamp'];
                        $record['requst_ready_num_ins']=$row6['requst_ready_num_ins'];
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


                        //*************************************************************************************************************
                        $output6[]=$record6;
                    }
                    $record['request_ready']=$output6;
                    $record['requst_ready_end_price']=$requst_ready_end_price;
                    $record['requst_ready_code_penalty']=$requst_ready_code_penalty;

                    //***************************************************************************************************************

                    //***************************************************************************************************************
                    //***************************************************************************************************************

                    //***************************************************************************************************************
                    $record['checkfinancialdoc']='0';

                    $query ='';
                    if(!empty($_REQUEST['peycommision_marketer_mobile'])){
                        $request_leader_mobile=$this->post('peycommision_marketer_mobile');
                        $query =" AND user_mobile  =".$request_leader_mobile;
                    }


                    $query10="SELECT SUM(user_wallet_amount) AS sumwallet,user_id,user_name,user_family,user_mobile FROM peycommision_leader_tb,user_wallet_tb,user_tb WHERE user_id=user_wallet_user_id AND user_wallet_id=peycommision_leader_user_wallet_id AND peycommision_leader_peyback_id=0
 AND peycommision_leader_request_id=".$request_id. " GROUP BY user_id";
                    $result10=$this->B_db->run_query($query10.$query);
                    $peycommision_leader=$result10[0];

                    $query1001="SELECT SUM(user_wallet_amount) AS sumwallet FROM peybackcommision_leader_tb,user_wallet_tb WHERE  user_wallet_id=peybackcommision_leader_user_wallet_id 
 AND peybackcommision_leader_request_id=".$request_id. " GROUP BY peybackcommision_leader_request_id";
                    $result1001=$this->B_db->run_query($query1001);
                    $peybackcommision_leader=$result1001[0];


                    $record['peycommision_leader']=$peycommision_leader['sumwallet']-$peybackcommision_leader['sumwallet']+0;
                    $record['peycommision_leader_id']=$peycommision_leader['user_id'];
                    $record['peycommision_leader_name']=$peycommision_leader['user_name'];
                    $record['peycommision_leader_family']=$peycommision_leader['user_family'];
                    $record['peycommision_leader_mobile']=$peycommision_leader['user_mobile'];


                    $query ='';
                    if(!empty($_REQUEST['peycommision_leader_mobile'])){
                        $peycommision_leader_mobile=$this->post('peycommision_leader_mobile');
                        $query =" AND user_mobile  =".$peycommision_leader_mobile;
                    }

                    $query101="SELECT SUM(user_wallet_amount) AS sumwallet,user_id,user_name,user_family,user_mobile FROM peycommision_marketer_tb,user_wallet_tb,user_tb WHERE  user_id=user_wallet_user_id AND user_wallet_id=peycommision_marketer_user_wallet_id AND peycommision_marketer_payback_id=0
 AND peycommision_marketer_request_id=".$request_id. " GROUP BY user_id";
                    $result101=$this->B_db->run_query($query101.$query);
                    $peycommision_marketer=$result101[0];

                    $query2001="SELECT SUM(user_wallet_amount) AS sumwallet FROM peybackcommision_marketer_tb,user_wallet_tb WHERE user_wallet_id=peybackcommision_marketer_user_wallet_id 
 AND peybackcommision_marketer_request_id=".$request_id. " GROUP BY peybackcommision_marketer_request_id";
                    $result2001=$this->B_db->run_query($query2001);
                    $peybackcommision_marketer=$result2001[0];


                    $record['peycommision_marketer']=$peycommision_marketer['sumwallet']-$peybackcommision_marketer['sumwallet']+0;
                    $record['peycommision_marketer_id']=$peycommision_marketer['user_id'];
                    $record['peycommision_marketer_name']=$peycommision_marketer['user_name'];
                    $record['peycommision_marketer_family']=$peycommision_marketer['user_family'];
                    $record['peycommision_marketer_mobile']=$peycommision_marketer['user_mobile'];




                    $query102="SELECT SUM(user_wallet_amount) AS sumwallet,user_id,user_name,user_family,user_mobile FROM peycommision_user_tb,user_wallet_tb,user_tb WHERE  user_id=user_wallet_user_id AND user_wallet_id=peycommision_user_wallet_id  AND peycommision_user_request_id=".$request_id. " GROUP BY user_id";
                    $result102=$this->B_db->run_query($query102);
                    $peycommision_user=$result102[0];
                    $record['peycommision_user']=$peycommision_user['sumwallet']+0;
                    $record['peycommision_user_id']=$peycommision_user['user_id'];
                    $record['peycommision_user_name']=$peycommision_user['user_name'];
                    $record['peycommision_user_family']=$peycommision_user['user_family'];
                    $record['peycommision_user_mobile']=$peycommision_user['user_mobile'];

                    $record['peysumcommision']=$peycommision_user['sumwallet']+$peycommision_marketer['sumwallet']+$peycommision_leader['sumwallet']-$peybackcommision_leader['sumwallet']-$peybackcommision_marketer['sumwallet'];

                    $output[]=$record;
                }
                echo json_encode(array('result'=>"ok"
                ,"cnt"=>$count[0]['cnt']
                ,"data"=>$output
                ,'desc'=>'لیست درخواست ها با موفقیت ارسال شد'.$sssssss),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

            }else{
                header('Content-Type: application/json');
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }




        }
        else       if ($command=="get_request_detail")
        {//register marketer
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','reportrefral');
            if($employeetoken[0]=='ok')
            {

                $query1="select * from request_tb,company_tb,request_state,fieldinsurance_tb,user_tb where user_id=request_user_id AND fieldinsurance=request_fieldinsurance AND request_state_id=request_last_state_id AND company_id=request_company_id ";
                $query1.=" AND (request_last_state_id=10 OR request_last_state_id=11)";

                $query2="select count(*) AS cnt from request_tb,company_tb,request_state,fieldinsurance_tb,user_tb where user_id=request_user_id AND fieldinsurance=request_fieldinsurance AND request_state_id=request_last_state_id AND company_id=request_company_id ";
                $query2.=" AND (request_last_state_id=10 OR request_last_state_id=11)";

                $query1 .= ' ORDER BY request_id  DESC ';

                $limit = $this->post("limit");
                $offset = $this->post("offset");
                $limit_state ="";
                if($limit!="" & $offset!="") {
                    $limit_state = " LIMIT " . $offset . "," . $limit;
                }
                /*
                        $result = $this->B_db->run_query($query1.$limit_state);
                        $count  = $this->B_db->run_query($query2);

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
                                if($result111){
                                    $agent_statuss1=$result111[0];
                                    $record['agent_status']=$agent_statuss1['agent_status'];
                                }

                            }
                            //****************************************************************************
                            $query112=" SELECT * FROM requestpartner1_tb WHERE requestpartner_request_id=$request_id";
                            $result112=$this->B_db->run_query($query112);
                            $requestpartner=$result112[0];
                            $record['requestpartner']=$requestpartner['requestpartner_code'];

                            //*************************************************************************************************************

                            $record['user_id']=$row['user_id'];
                            $record['user_name']=$row['user_name'];
                            $record['user_family']=$row['user_family'];
                            $record['user_mobile']=$row['user_mobile'];
                            $record['fieldinsurance_logo_url']=IMGADD.$row['fieldinsurance_logo_url'];
                            $record['fieldinsurance_id']=$row['fieldinsurance_id'];
                            $record['request_fieldinsurance_fa']=$row['fieldinsurance_fa'];
                            $record['fieldinsurance_commission']=$row['fieldinsurance_commission'];
                            $record['request_description']=$row['request_description'];
                            $record['request_last_state_id']=$row['request_last_state_id'];
                            $record['request_last_state_name']=$row['request_state_name'];












                            //*************************************************************************************************************
                            $overpayment=0;
                            $query0="select sum(user_pey_amount) AS overpayment from user_pey_tb where user_pey_mode='overpayment' AND user_pey_request_id=".$request_id;
                            $result0 = $this->B_db->run_query($query0);
                            $user_pey0=$result0[0];
                            if($user_pey0['overpayment'])
                            {$overpayment=$user_pey0['overpayment'];}
                            else
                            {$overpayment=0;}

                            $record['user_pey_overpayment']=$overpayment;

                            $query1="select sum(user_pey_amount) AS sumpey from user_pey_tb where not (user_pey_mode='overpayment') AND user_pey_request_id=".$request_id;
                            $result1 = $this->B_db->run_query($query1);
                            $user_pey=$result1[0];
                            $record['user_pey_amount']=$user_pey['sumpey']-$overpayment;


                            $query2="select sum(user_pey_amount) AS sumcash from user_pey_tb where not (user_pey_mode = 'instalment')  AND  not (user_pey_mode='overpayment') AND user_pey_request_id=".$request_id;
                            $result2 = $this->B_db->run_query($query2);
                            $user_pey2=$result2[0];
                            $record['user_pey_cash']=$user_pey2['sumcash']-$overpayment;

                            $query20="select sum(user_pey_amount) AS suminstalment from user_pey_tb where user_pey_mode = 'instalment' AND user_pey_request_id=".$request_id;
                            $result20 = $this->B_db->run_query($query20);
                            $user_pey20=$result20[0];
                            if($user_pey20['suminstalment'])
                            {$record['user_pey_instalment']=$user_pey20['suminstalment'];}
                            else
                            {$record['user_pey_instalment']=0;}

                            $query21="select sum(user_pey_amount) AS suminstalment from user_pey_tb,instalment_check_tb where instalment_check_user_pey_id=user_pey_id AND instalment_check_pass=1 AND user_pey_mode = 'instalment' AND user_pey_request_id=".$request_id;
                            $result21 = $this->B_db->run_query($query21);
                            $user_pey21=$result21[0];
                            if($user_pey21['suminstalment'])
                            {$record['user_pey_instalment_pass']=$user_pey21['suminstalment'];}
                            else
                            {$record['user_pey_instalment_pass']=0;}



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


                                $result112 = $this->B_db->get_image($row1['user_pey_image_code']);
                                $image = $result112[0];

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
                            $requst_ready_code_penalty=0;
                            $requst_ready_end_price=0;
                            $query6=" SELECT * FROM requst_ready_tb WHERE  requst_ready_request_id=".$request_id;
                            $result6 = $this->B_db->run_query($query6);
                            $output6 =array();
                            foreach($result6 as $row6)
                            {
                                $requst_ready_end_price=$row6['requst_ready_end_price'];
                                $requst_ready_code_penalty=$row6['requst_ready_code_penalty'];

                                $record6['requst_ready_start_date']=$row6['requst_ready_start_date'];
                                $record['requst_ready_start_date']=$row6['requst_ready_start_date'];
                                $record['requst_ready_timestamp']=$row6['requst_ready_timestamp'];
                                $record['requst_ready_num_ins']=$row6['requst_ready_num_ins'];
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
                            $record['requst_ready_end_price']=$requst_ready_end_price;
                            $record['requst_ready_code_penalty']=$requst_ready_code_penalty;

                            //***************************************************************************************************************


                            //***************************************************************************************************************
                            $request_financial_approval=0;
                            $query8=" SELECT * FROM request_financial_approval_tb,employee_tb WHERE request_financial_approval_employee_id=employee_id AND request_financial_approval_request_id=".$request_id;
                            $result8 = $this->B_db->run_query($query8);
                            $output8 =array();
                            foreach($result8 as $row8)
                            {
                                $request_financial_approval=$row8['request_financial_approval_price'];

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
                            $request_financial_doc=0;
                            $query9=" SELECT * FROM request_financial_doc_tb,employee_tb,request_financial_paying_tb WHERE
                         request_financial_doc_employee_id=employee_id AND
                         request_financial_doc_id=request_financial_paying_doc_id AND
                          request_financial_paying_request_id=".$request_id;
                            $result9 = $this->B_db->run_query($query9);
                            $output9 =array();
                            foreach($result9 as $row9)
                            {
                                $request_financial_doc=$row9['request_financial_doc'];

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
                            if($request_financial_doc==0){$record['request_pey_agent']=0;}else{$record['request_pey_agent']=$request_financial_approval;}
                            $record['request_financial_doc']=$output9;
                            //***************************************************************************************************************
                            $record['checkfinancialdoc']='0';


                            $query10="SELECT SUM(user_wallet_amount) AS sumwallet,user_id,user_name,user_family,user_mobile FROM peycommision_leader_tb,user_wallet_tb,user_tb WHERE user_id=user_wallet_user_id AND user_wallet_id=peycommision_leader_user_wallet_id AND peycommision_leader_peyback_id=0
                 AND peycommision_leader_request_id=".$request_id;
                            $result10=$this->B_db->run_query($query10);
                            $peycommision_leader=$result10[0];
                            $record['peycommision_leader']=$peycommision_leader['sumwallet']+0;
                            $record['peycommision_leader_id']=$peycommision_leader['user_id'];
                            $record['peycommision_leader_name']=$peycommision_leader['user_name'];
                            $record['peycommision_leader_family']=$peycommision_leader['user_family'];
                            $record['peycommision_leader_mobile']=$peycommision_leader['user_mobile'];




                            $query101="SELECT SUM(user_wallet_amount) AS sumwallet,user_id,user_name,user_family,user_mobile FROM peycommision_marketer_tb,user_wallet_tb,user_tb WHERE  user_id=user_wallet_user_id AND user_wallet_id=peycommision_marketer_user_wallet_id AND peycommision_marketer_payback_id=0
                 AND peycommision_marketer_request_id=".$request_id;
                            $result101=$this->B_db->run_query($query101);
                            $peycommision_marketer=$result101[0];
                            $record['peycommision_marketer']=$peycommision_marketer['sumwallet']+0;
                            $record['peycommision_marketer_id']=$peycommision_marketer['user_id'];
                            $record['peycommision_marketer_name']=$peycommision_marketer['user_name'];
                            $record['peycommision_marketer_family']=$peycommision_marketer['user_family'];
                            $record['peycommision_marketer_mobile']=$peycommision_marketer['user_mobile'];




                            $query102="SELECT SUM(user_wallet_amount) AS sumwallet,user_id,user_name,user_family,user_mobile FROM peycommision_user_tb,user_wallet_tb,user_tb WHERE  user_id=user_wallet_user_id AND user_wallet_id=peycommision_user_wallet_id  AND peycommision_user_request_id=".$request_id;
                            $result102=$this->B_db->run_query($query102);
                            $peycommision_user=$result102[0];
                            $record['peycommision_user']=$peycommision_user['sumwallet']+0;
                            $record['peycommision_user_id']=$peycommision_user['user_id'];
                            $record['peycommision_user_name']=$peycommision_user['user_name'];
                            $record['peycommision_user_family']=$peycommision_user['user_family'];
                            $record['peycommision_user_mobile']=$peycommision_user['user_mobile'];

                            $record['peysumcommision']=$peycommision_user['sumwallet']+$peycommision_marketer['sumwallet']+$peycommision_leader['sumwallet'];

                            $output[]=$record;
                        }
                       */
                echo json_encode(array('result'=>"ok"
                    // ,"cnt"=>$count[0]['cnt']
                    //   ,"data"=>$output
                ,"$query1"=>$query1.$limit_state
                ,"$query2"=>$query2
                ,'desc'=>'لیست درخواست ها با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }




        }
        else if($command=="change_referral_mobile") {
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','reportrefral');
            if($employeetoken[0]=='ok')
            {
                $reagent_mobile= $this->post('reagent_mobile');
                $request_id= $this->post('request_id');
                $request_leader_mobile="";

                $query="SELECT marketer_leader_mobile FROM chatreno_db.user_tb,chatreno_db.usermarketer_tb WHERE marketer_user_id=user_id AND user_mobile='$reagent_mobile'";
                $result=$this->B_db->run_query($query);
                if(!empty($result))
                    $request_leader_mobile = $result[0]['marketer_leader_mobile'];
                $request_reagent_mobile_refralcode = $result[0]['marketer_leader_mobile'];


                $query = "UPDATE chatreno_db.request_tb
                SET request_reagent_mobile='$reagent_mobile',request_leader_mobile='$request_leader_mobile'  ,request_reagent_mobile_refralcode='$request_reagent_mobile_refralcode' 
                WHERE request_id=$request_id ";
                $result = $this->B_db->run_query_put($query);

                if($result) {
                    $query0="select sum(user_pey_amount) AS overpayment from user_pey_tb where user_pey_mode='overpayment' AND user_pey_request_id=".$request_id. " GROUP BY user_pey_request_id";
                    $result0 = $this->B_db->run_query($query0);
                    $user_pey0=$result0[0];
                    if($user_pey0['overpayment'])
                    {$overpayment=$user_pey0['overpayment'];}
                    else
                    {$overpayment=0;}

                    $query2="select sum(user_pey_amount) AS sumcash from user_pey_tb where not (user_pey_mode = 'instalment')  AND  not (user_pey_mode='overpayment') AND user_pey_request_id=".$request_id. " GROUP BY user_pey_request_id";
                    $result2 = $this->B_db->run_query($query2);
                    $user_pey2=$result2[0];
                    $user_pey_cash=$user_pey2['sumcash']-$overpayment;
                    $user_pey_cash=$user_pey_cash * 100 / 109;
                    $this->B_db->peyback_decision($request_id, intval($user_pey_cash), 'add', 'نقد', 'main');

                    $query200="select requst_ready_code_penalty from requst_ready_tb where  requst_ready_request_id=".$request_id;
                    $result200 = $this->B_db->run_query($query200);
                    $requst_ready=$result200[0];
                    $requst_ready_code_penalty=$requst_ready['requst_ready_code_penalty'];
                    if(intval($requst_ready_code_penalty>0)){
                        $this->B_db->peyback_decision($request_id, intval($requst_ready_code_penalty), 'get', 'مبلغی که شامل کارمزد نمیشود','nomain');
                    }

                    $query1="select * from user_pey_tb,instalment_check_tb where user_pey_mode='instalment' AND instalment_check_user_pey_id=user_pey_id AND user_pey_request_id=".$request_id;
                    $result1 = $this->B_db->run_query($query1);
                    foreach($result1 as $row1)
                    {
                        if($row1['instalment_check_pass']==1) {
                            $instalment_check_amount=intval($row1['instalment_check_amount'] )* 100 / 109;

                            $this->B_db->peyback_decision($request_id, $instalment_check_amount, 'add', 'چک', 'nomain');
                        }
                    }



                    echo json_encode(array('result' => 'ok',"data"=>$user_pey_cash
                    ,'desc'=>"تغببر معرف انجام شد"), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }else {
                    echo json_encode(array('result' => 'error','data' => '','desc'=>'رکورد مورد نظر یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }

        }
    }
}