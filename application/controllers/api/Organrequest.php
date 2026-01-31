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
class Organrequest extends REST_Controller {

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
        $this->load->model('B_organ');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('partner', $command, get_client_ip(),200,50)) {

                if ($command=="add_organ_request")
                {

                    $requestorgan_organ_id=$this->post('organ_id') ;
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
                    $requestorgan_code=$this->post('requestorgan_code') ;
                    $employee_id=$this->post('employee_id') ;

                    $contract_id=$this->post('contract_id') ;
                    $organ_user_organ_id=$this->post('organ_id') ;
                    $user_name=$this->post('name_client') ;
                    $user_family=$this->post('family_client') ;
                    $user_national_code=$this->post('nationalcode_client') ;
                    $user_personal_code=$this->post('personalcode_client') ;
                    $commitment_amount=100000000 ;
                    $commitment_num=10 ;
                    $mobile_client=$this->post('mobile_client') ;
                    $installment=$this->post('installment') ;


                    $jsonpricing_text=$this->post('jsonpricing_text') ;



                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','partner');
                    if($employeetoken[0]=='ok')
                    {
                        //**********************************************************************************************************************
                        $user_id=0;
                        $sql = "SELECT user_id
                FROM user_tb where user_mobile='$mobile_client' ";
                        $result=$this->B_db->run_query($sql);
                        $num=count($result[0]);
                        if ($num==0) {
                            $query1 = "INSERT INTO user_tb (user_mobile,user_name,user_family,user_national_code,user_register_date) VALUES ('" . $mobile_client . "','" . $user_name . "','" . $user_family . "','" . $user_national_code . "',now());";
                            $result1 = $this->B_db->run_query_put($query1);
                            $user_id = $this->db->insert_id();
                        }else{
                            $user=$result[0];
                        $user_id =$user['user_id'];
                        }

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



                        $confirm_id=803;

                        $sql = "SELECT organ_user_confirm_id
                FROM organ_user_tb,organ_tb where organ_user_organ_id=organ_id AND organ_public=1 AND organ_user_user_id=$user_id AND organ_user_organ_id=$organ_user_organ_id";
                        $result=$this->B_db->run_query($sql);
                        $num=count($result[0]);
                        if ($num==0)
                        {

                            $result = $this->B_organ->add_organ_user($organ_user_organ_id, $user_id, $commitment_amount, $commitment_num, $confirm_id, $user_personal_code);

                        }



                        //**********************************************************************************************************************
                        $query="select * from requestorgan_tb where requestorgan_code='".$requestorgan_code."' AND requestorgan_organ_id=".$requestorgan_organ_id;
                        $result=$this->B_db->run_query($query);

                        if(empty($result))
                        {
//                        $query="select * from partner_tb,user_tb where partner_user_id=user_id ANd partner_id=$requestorgan_organ_id";
//                        $result1=$this->B_db->run_query($query);
//                        $partner=$result1[0];
//                        $partner_user_id =$partner['partner_user_id'];
//                        $user_mobile =$partner['user_mobile'];
//                        $partner_address_id =$partner['partner_address_id'];

                            $result = $this->B_user->add_user_address($user_id, 8, 117, 'آدرس پیش فرض برای مراجعان حضوری', '000000000', 'آدرس', $mobile_client, '');
                            $user_address_id = $this->db->insert_id();

                        //************************************************************************;****************************************
                        $jsonpricing_text1=str_replace('&#34;','"',$jsonpricing_text);



                        $query1="INSERT INTO jsonpricing_tb( jsonpricing_text, jsonpricing_date,	jsonpricing_fieldinsurance) VALUES
                                      ( '".$jsonpricing_text1."'      ,   now()         , '$fieldinsurance') ";
                        $result1=$this->B_db->run_query_put($query1);
                        $jsonpricing_id=$this->db->insert_id();

//************************************************************************;****************************************
                        $query="INSERT INTO request_tb(request_user_id     ,request_agent_id , request_fieldinsurance,request_company_id, request_price_app, request_last_state_id,request_jsonpricing_id ,request_organ,request_reagent_mobile  ,request_adderss_id ,request_addressofinsured_id,request_fieldinsurance_id ) VALUES
                                                      (".$user_id."                ,$agent_id,'$fieldinsurance'      ,$company_id       ,$requst_end_price,0                        ,$jsonpricing_id      ,1            ,'$mobile_client'       ,$user_address_id, $user_address_id,$fieldinsurance_id) ";
                        $result=$this->B_db->run_query_put($query);
                        $request_id=$this->db->insert_id();

                            $query11="INSERT INTO organ_request_tb
                        (organ_request_request_id, organ_request_contract_id)
                    VALUES($request_id, $contract_id); ";
                            $result11=$this->B_db->run_query_put($query11);

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


                            $query4="select * from instalment_conditions_tb,instalment_mode_tb where instalment_mode_tb.instalment_mode_mode_id=instalment_conditions_tb.instalment_conditions_mode_id AND instalment_condition_contract_id=".$contract_id." ORDER BY instalment_conditions_date DESC";

                            $result4 = $this->B_db->run_query($query4);
                            $query1411='';
                            for($i=1;$i<=(int)$installment;$i++)
                            {
                                $price=$this->post('price'.$i) ;
                                $date=$this->post('date'.$i) ;

                                $user_pey_desc =  $result4[$i-1]['instalment_conditions_desc'] ;
                                $instalment_conditions_id =  $result4[$i-1]['instalment_conditions_id'] ;

                                $query1 = "INSERT INTO user_pey_tb( user_pey_request_id, user_pey_amount, user_pey_mode          , user_pey_code                          , user_pey_desc  ,user_pey_image_code,user_pey_timestamp) VALUES
                                                                   ( $request_id    , $price   , 'instalment'          ,$instalment_conditions_id               ,'$user_pey_desc','',now())      ";
                                $result1 = $this->B_db->run_query_put($query1);

                                $user_pey_id=$this->db->insert_id();


                                $query2="INSERT INTO instalment_check_tb( instalment_check_condition_id,  instalment_check_instalment_id ,instalment_check_user_pey_id,     instalment_check_amount, instalment_check_desc                ,  instalment_check_request_id, instalment_check_image_code,instalment_check_date,instalment_check_pass,instalment_check_num) VALUES
                                                                         ( $instalment_conditions_id   , 1                               ,".$user_pey_id."               , '".$price."'            , '".$user_pey_desc."'                  ,  $request_id          ,''                               ,'$date'              ,0,'0')  ";
                                $result2=$this->B_db->run_query_put($query2);

                                $query1411.="price=".$price." date".$date. " user_pey_desc".$user_pey_desc." instalment_conditions_id".$instalment_conditions_id. $query1.$query2;

                            }





////************************************************************************;****************************************
//
                            $query="INSERT INTO requestorgan_tb(requestorgan_organ_id, requestorgan_code,requestorgan_request_id)
	                            VALUES ( $requestorgan_organ_id,'$requestorgan_code',$request_id);";

                            $result=$this->B_db->run_query_put($query);
                             $requestorgan_organ_id=$this->db->insert_id();
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>array('requestorgan_partner_id'=>$requestorgan_organ_id)
                            ,'desc'=>' درخواست اضافه شد'.$installment.$query1411),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }



                        //***************************************************************************************************************
                    }else{

                            $carmode=$result[0];
                            echo json_encode(array('result'=>"repeat"
                            ,"data"=>array('requestorgan_organ_id'=>$carmode['requestorgan_organ_id'])
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
