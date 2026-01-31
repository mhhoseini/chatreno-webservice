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
class Partner extends REST_Controller {

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
        if (isset($this->input->request_headers()['Authorization'])) $employee_token_str = $this->input->request_headers()['Authorization'];
        $this->load->model('B_user');
        $this->load->model('B_db');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('partner', $command, get_client_ip(),200,50)) {
            if ($command == "get_partner_marketer") {

                $partner_marketer_name = $this->post("partner_marketer_name");

//************************************************************************;****************************************
                $filter="";
                if($partner_marketer_name !='') {
                    $filter .= "  partner_marketer_name like '%" . $partner_marketer_name . "%' ";
                }else{$filter .=" 1=1 "; }

                $query1="select * from partner_marketer_tb where $filter ";
                $query2="select count(*) AS cnt  from partner_marketer_tb where $filter ";

                $limit = $this->post("limit");
                $offset = $this->post("offset");
                $limit_state ="";
                if($limit!="" & $offset!="") {
                    $limit_state = " LIMIT " . $offset . "," . $limit;
                }
                $query=" ORDER BY partner_marketer_id ASC";
                $result = $this->B_db->run_query($query1.$query.$limit_state);
                $count  = $this->B_db->run_query($query2.$query);

                    $output =array();
                    foreach($result as $row)
                    {
                        $record=array();
                        $record['count']=$count[0]['cnt'];
                        $record['partner_marketer_id']=$row['partner_marketer_id'];
                        $record['partner_marketer_user_id']=$row['partner_marketer_user_id'];
                        $record['partner_marketer_user_refferal_name']=$row['partner_marketer_user_refferal_name'];
                        $record['partner_marketer_name']=$row['partner_marketer_name'];
                        $record['partner_marketer_desc']=$row['partner_marketer_desc'];
                        $record['partner_marketer_image_code']=$row['partner_marketer_image_code'];
                        $result1=$this->B_db->get_image($row['partner_marketer_image_code']);
                        $image=$result1[0];
                        $record['partner_marketer_image']=$image['image_url'];
                        $record['partner_marketer_timage']=$image['image_tumb_url'];

                        $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,"cnt"=>$count[0]['cnt']
                    ,'desc'=>'نوع ملک بیمه اتش سوزی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************




            }
            else
            if ($command == "get_partner") {
                $employeetoken = checkpermissionemployeetoken($employee_token_str, 'view', 'partner');
                if ($employeetoken[0] == 'ok') {

//************************************************************************;****************************************

                    $query="select * from partner_tb where 1 ORDER BY partner_id ASC";
                    $result = $this->B_db->run_query($query);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record=array();
                        $record['partner_id']=$row['partner_id'];
                        $record['partner_name']=$row['partner_name'];
                        $record['partner_user_id']=$row['partner_user_id'];
                        $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'نوع ملک بیمه اتش سوزی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************



                } else {
                    echo json_encode(array('result' => $employeetoken[0]
                    , "data" => $employeetoken[1]
                    , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }
            else
                if ($command=="add_partner_request")
                {

                    $requestpartner_partner_id=$this->post('requestpartner_partner_id') ;
                    $company_id=$this->post('company_id') ;
                    $sodor_date=$this->post('sodor_date') ;
                    $start_date=$this->post('start_date') ;
                    $fieldinsurance_id=$this->post('fieldinsurance_id') ;
                    $fieldinsurance=$this->post('fieldinsurance') ;
                    $agent_id=$this->post('agent_id') ;
                    $code_yekta=$this->post('code_yekta',0) ;
                    $end_date=$this->post('end_date') ;
                    $code_rayane=$this->post('code_rayane',0) ;
                    $requst_penalty=$this->post('requst_penalty',0) ;
                    $name_insurer=$this->post('name_insurer') ;
                    $nationalcode_insurer=$this->post('nationalcode_insurer') ;
                    $clearing=$this->post('clearing') ;
                    $num_ins=$this->post('num_ins') ;
                    $requst_end_price=$this->post('requst_end_price') ;
                    $requestpartner_code=$this->post('requestpartner_code') ;
                    $employee_id=$this->post('employee_id') ;

                    $jsonpricing_text=$this->post('jsonpricing_text') ;



                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','partner');
                    if($employeetoken[0]=='ok')
                    {
                        $query="select * from requestpartner1_tb where requestpartner_code='".$requestpartner_code."' AND requestpartner_partner_id=".$requestpartner_partner_id;
                        $result=$this->B_db->run_query($query);

                        if(empty($result))
                        {
                        $query="select * from partner_tb,user_tb where partner_user_id=user_id ANd partner_id=$requestpartner_partner_id";
                        $result1=$this->B_db->run_query($query);
                        $partner=$result1[0];
                        $partner_user_id =$partner['partner_user_id'];
                        $user_mobile =$partner['user_mobile'];
                        $partner_address_id =$partner['partner_address_id'];

                        //************************************************************************;****************************************
                        $jsonpricing_text1=str_replace('&#34;','"',$jsonpricing_text);



                        $query1="INSERT INTO jsonpricing_tb( jsonpricing_text, jsonpricing_date,	jsonpricing_fieldinsurance) VALUES
                                      ( '".$jsonpricing_text1."'      ,   now()         , '$fieldinsurance') ";
                        $result1=$this->B_db->run_query_put($query1);
                        $jsonpricing_id=$this->db->insert_id();

//************************************************************************;****************************************
                        $query="INSERT INTO request_tb(request_user_id     ,request_agent_id , request_fieldinsurance,request_company_id, request_price_app, request_last_state_id,request_jsonpricing_id ,request_organ,request_reagent_mobile  ,request_adderss_id ,request_addressofinsured_id,request_fieldinsurance_id ) VALUES
                                                      (".$partner_user_id.",$agent_id,'$fieldinsurance'           ,$company_id    ,$requst_end_price,0                               ,$jsonpricing_id      ,0            ,'$user_mobile'       ,$partner_address_id, $partner_address_id,$fieldinsurance_id) ";
                        $result=$this->B_db->run_query_put($query);
                        $request_id=$this->db->insert_id();

//************************************************************************;****************************************

                        $query="DELETE FROM requst_ready_tb WHERE requst_ready_request_id=$request_id";
                        $result = $this->B_db->run_query_put($query);


                        $query3 = "INSERT INTO requst_ready_tb( requst_ready_request_id, requst_ready_timestamp, requst_ready_start_date, requst_ready_end_date, requst_ready_end_price, requst_ready_num_ins, requst_ready_code_yekta,requst_ready_code_rayane,requst_ready_code_penalty, requst_ready_name_insurer, requst_ready_code_insurer, requst_suspend_desc, requst_suspend_agent_id,requst_ready_employee_id,request_ready_clearing_id) VALUES
                                                               ( $request_id           , ' $sodor_date  '      ,'$start_date'          ,'$end_date'            ,'$requst_end_price'            ,'$num_ins'           ,'$code_yekta'           ,'$code_rayane'          ,    '$requst_penalty'    ,'$name_insurer'           ,'$nationalcode_insurer'    ,''                  ,$agent_id              ,$employee_id              ,$clearing)";
                        $requst_ready_id = $this->B_db->run_query_put($query3);
                        if($requst_ready_id){
                            $query1 = "UPDATE request_tb SET request_last_state_id=10  WHERE request_id=$request_id";
                            $result1 = $this->B_db->run_query_put($query1);

                            $desc = ' صادر شده و آماده تحویل ';
                            $query2 = "INSERT INTO state_request_tb( staterequest_request_id, staterequest_state_id, staterequest_timestamp,staterequest_desc,staterequest_agent_id,staterequest_employee_id) VALUES
                                                                   ( $request_id, 10, now(),'$desc',$agent_id,$employee_id)";
                            $result1 = $this->B_db->run_query_put($query2);
////************************************************************************;****************************************
//
                            $query2 = "DELETE FROM  instalment_check_tb WHERE instalment_check_request_id=$request_id ";
                            $result2 = $this->B_db->run_query_put($query2);

                            $user_pey_desc = 'پرداخت شده توسط آپ' ;
                            $query1 = "INSERT INTO user_pey_tb( user_pey_request_id, user_pey_amount, user_pey_mode, user_pey_code, user_pey_desc  ,user_pey_image_code,user_pey_timestamp) VALUES
                                                                   ( $request_id              , $requst_end_price   , 'instalment'     , 105               ,'$user_pey_desc','',now())      ";
                            $result1 = $this->B_db->run_query_put($query1);

                            $user_pey_id=$this->db->insert_id();


                            $query2="INSERT INTO instalment_check_tb( instalment_check_condition_id,  instalment_check_instalment_id ,instalment_check_user_pey_id,     instalment_check_amount, instalment_check_desc                ,  instalment_check_request_id, instalment_check_image_code,instalment_check_date_pass,instalment_check_pass) VALUES
                                                                    ( 105                          , 1                               ,".$user_pey_id."               , '".$requst_end_price."' , '".$user_pey_desc."'                  ,  $request_id          ,''                               ,'$sodor_date'             ,1)  ";
                            $result2=$this->B_db->run_query_put($query2);


////************************************************************************;****************************************
//
                            $query="INSERT INTO requestpartner1_tb(requestpartner_partner_id, requestpartner_code,requestpartner_request_id)
	                            VALUES ( $requestpartner_partner_id,'$requestpartner_code',$request_id);";

                            $result=$this->B_db->run_query_put($query);
                             $requestpartner_partner_id=$this->db->insert_id();
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>array('requestpartner_partner_id'=>$requestpartner_partner_id)
                            ,'desc'=>' درخواست اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                        $this->B_db->peyback_decision($request_id, ($requst_end_price* 100 / 109), 'add', 'نقد', 'main');

                            if(intval($requst_penalty)>0){
                                $this->B_db->peyback_decision($request_id, intval($requst_penalty), 'get', 'مبلغی که شامل کارمزد نمیشود','nomain');

                            }


                        //***************************************************************************************************************
                    }else{

                            $carmode=$result[0];
                            echo json_encode(array('result'=>"repeat"
                            ,"data"=>array('requestpartner_partner_id'=>$carmode['requestpartner_partner_id'])
                            ,'desc'=>'درخواست تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]));

                    }


        }



        }

    }
}
