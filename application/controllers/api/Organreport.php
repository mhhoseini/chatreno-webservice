<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//require APPPATH . '/libraries/REST_Controller.php';

// use namespace
use Restserver\Libraries\REST_Controller;

/**organ_contract_id
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
class Organreport extends REST_Controller
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
        $this->load->helper('my_helper');
        if (isset($this->input->request_headers()['Authorization'])) $organ_token_str = $this->input->request_headers()['Authorization'];
        $this->load->model('B_organ');
        $this->load->model('B_user');
        $this->load->model('B_db');

        $this->load->helper('my_helper');
        $this->load->helper('time_helper');
        $command = $this->post("command");

        $date_end=$this->post('date_end') ;
        $date_start=$this->post('date_start') ;
        $user_name=$this->post('user_name') ;
        $user_family=$this->post('user_family') ;
        $user_mobile=$this->post('user_mobile') ;
        $user_national_code=$this->post('user_national_code') ;
        $organ_user_personal_code=$this->post('personal_code') ;
        $instalment_condition_contract_id = $this->post('instalment_condition_contract_id');
        $instalment_company_id = $this->post('instalment_company_id');
        $fieldinsurance_id = $this->post('instalment_fieldinsurance_id');
        $instalment_date_start = $this->post('instalment_date_start');
        $instalment_date_end = $this->post('instalment_date_end');
        $organ_id = $this->post('organ_id') ;
        $limit = $this->post("limit");
        $offset = $this->post("offset");
        $search_mode = $this->post("search_mode");

        $organtoken = checkorgantoken($organ_token_str);
        if ($this->B_user->checkrequestip('organreport', $command, get_client_ip(),50,50)) {
            if ($command == "get_instalstalment_notpass")
            {
                if($organtoken[0]=='ok')
                {
                    $organ_id=$organtoken[1];
                    $filter = "";
                    if($search_mode ==1){
                        if($user_name !='')
                            $filter .= " And user_tb.user_name like   '%".$user_name."%'   ESCAPE '!'";
                        if($user_family !='')
                            $filter .= " And user_tb.user_family like '%".$user_family."%' ESCAPE '!'";
                        if($user_mobile !='')
                            $filter .= " And user_tb.user_mobile=".$user_mobile;
                        if($user_national_code !='')
                            $filter .= " And user_tb.user_national_code=".$user_national_code;
                        if($organ_user_personal_code !='')
                            $filter .= " And organ_user_personal_code='".$organ_user_personal_code."'";
                        if($instalment_condition_contract_id !='')
                            $filter .= " And organ_contract_id=".$instalment_condition_contract_id;
                        if($instalment_company_id !='')
                            $filter .= " And request_company_id=".$instalment_company_id;
                        if($fieldinsurance_id !='')
                            $filter .= " And fieldinsurance_id=".$fieldinsurance_id;
                        if($instalment_date_start !='')
                            $filter .= " And instalment_check_date>='".$instalment_date_start."'";
                        if($instalment_date_end !='')
                            $filter .= " And instalment_check_date<='".$instalment_date_end."'";
                    }

                    $limit_state ="";
                    if($limit!="" & $offset!="")
                        $limit_state = "LIMIT ".$limit.",".$offset;

                    $query_body = " from instalment_check_tb,instalment_conditions_tb,user_tb,
                      agent_tb,request_tb,company_tb,fieldinsurance_tb,
                      request_state,organ_contract_tb,organ_tb ,requst_ready_tb,organ_user_tb
                      where organ_contract_organ_id=organ_id 
                      AND instalment_condition_contract_id=organ_contract_id 
                      AND instalment_conditions_id=instalment_check_condition_id 
                      AND  request_state_id=request_last_state_id 
                      AND user_id=request_user_id 
                      AND user_id=organ_user_user_id
                      AND agent_id=request_agent_id 
                      AND fieldinsurance=request_fieldinsurance  
                      AND company_id=request_company_id 
                      AND instalment_check_request_id=request_id 
                      AND requst_ready_request_id=request_id
                       AND organ_user_id IN(
                      SELECT a.organ_user_id
FROM organ_user_tb a
INNER JOIN (
   SELECT organ_user_user_id, MAX(organ_user_confirm_id) AS max_confirm_id
FROM organ_user_tb
WHERE organ_user_organ_id=$organ_id and organ_user_confirm_id<=100000000
GROUP by organ_user_user_id
) b ON a.organ_user_confirm_id = b.max_confirm_id AND a.organ_user_user_id = b.organ_user_user_id AND a.organ_user_organ_id=$organ_id
INNER JOIN user_tb c
ON c.user_id=a.organ_user_user_id
INNER JOIN organ_confirm_tb d
ON a.organ_user_confirm_id=d.organ_confirm_id
                      ) 
                      AND instalment_conditions_mode_id=2 AND instalment_check_pass=0";
                    $query="select * ".$query_body." ".$filter." AND organ_id=$organ_id ".$limit_state;
                    $count_query="select count(*) as count ".$query_body." ".$filter." AND organ_id=".$organ_id;
                    $result = $this->B_db->run_query($query);
                    $print = $this->post('print');
                    if($print==126)
                    {
                        echo $_SERVER['SERVER_ADDR'].'@';
                        echo $_SERVER['SERVER_PORT'];
                        echo $this->db->last_query();
                    }

                    $count  = $this->B_db->run_query($count_query);

                    $output =array();
                    foreach($result as $row)
                    {
                        $record=array();
                        $record["count"]=$count[0]['count'];
                        $record['user_id']=$row['user_id'];
                        $record['user_name']=$row['user_name'];
                        $record['user_family']=$row['user_family'];
                        $record['user_mobile']=$row['user_mobile'];
                        $record['user_national_code']=$row['user_national_code'];
                        $record['organ_contract_id']=$row['organ_contract_id'];
                        $record['organ_contract_num']=$row['organ_contract_num'];
                        $record['organ_user_personal_code']=$row['organ_user_personal_code'];
                        //$result2 = $this->B_organ->get_one_user_organ($row['organ_id'], $row['user_id']);
                        //$organ_user=$result2[0];
                        //$record['organ_user_personal_code']=$organ_user['organ_user_personal_code'];
                        $record['requst_ready_start_date']=$row['requst_ready_start_date'];
                        $record['requst_ready_end_date']=$row['requst_ready_end_date'];
                        $record['requst_ready_num_ins']=$row['requst_ready_num_ins'];
                        $record['instalment_check_id']=$row['instalment_check_id'];
                        $record['instalment_check_condition_id']=$row['instalment_check_condition_id'];
                        $record['instalment_check_instalment_id']=$row['instalment_check_instalment_id'];
                        $record['instalment_check_user_pey_id']=$row['instalment_check_user_pey_id'];
                        $record['instalment_check_date']=$row['instalment_check_date'];
                        $record['instalment_check_num']=$row['instalment_check_num'];
                        $record['instalment_check_amount']=$row['instalment_check_amount'];
                        $record['instalment_check_desc']=$row['instalment_check_desc'];
                        $record['instalment_check_date_pass']=$row['instalment_check_date_pass'];
                        $record['instalment_conditions_mode_id']=$row['instalment_conditions_mode_id'];
                        $request_id=$row['request_id'];
                        $record['request_id']=$row['request_id'];
                        $record['request_company_id']=$row['request_company_id'];
                        $record['company_name']=$row['company_name'];
                        $record['company_logo_url']=IMGADD.$row['company_logo_url'];
                        $record['fieldinsurance_logo_url']=IMGADD.$row['fieldinsurance_logo_url'];
                        $record['fieldinsurance_id']=$row['fieldinsurance_id'];
                        $record['request_fieldinsurance_fa']=$row['fieldinsurance_fa'];
                        $record['request_last_state_id']=$row['request_last_state_id'];
                        $record['request_last_state_name']=$row['request_state_name'];

                        //***************************************************************************************************************
                        $query17="select * from state_request_tb where staterequest_request_id=".$request_id." ORDER BY staterequest_id DESC LIMIT 1 ";
                        $result17=$this->B_db->run_query($query17);
                        if(!empty($result17))
                            $state_request17=$result17[0];
                        else
                            $state_request17=array();
                        $record['staterequest_last_timestamp']=$state_request17['staterequest_timestamp'];
                        //*************************************************************************************
                        $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'لیست درخواست های بازگشت وجه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                } else {
                    echo json_encode(array('result' => $organtoken[0]
                    , "data" => $organtoken[1]
                    , 'desc' => $organtoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }
            else  if ($command == "get_instalstalment_pass")
            {
                if($organtoken[0]=='ok')
                {
                    $organ_id=$organtoken[1];
                    $filter = "";
                    if($search_mode ==1){
                        if($user_name !='')
                            $filter .= " And user_tb.user_name like   '%".$user_name."%'   ESCAPE '!'";
                        if($user_family !='')
                            $filter .= " And user_tb.user_family like '%".$user_family."%' ESCAPE '!'";
                        if($user_mobile !='')
                            $filter .= " And user_tb.user_mobile=".$user_mobile;
                        if($user_national_code !='')
                            $filter .= " And user_tb.user_national_code=".$user_national_code;
                        if($organ_user_personal_code !='')
                            $filter .= " And organ_user_personal_code='".$organ_user_personal_code."'";
                        if($instalment_condition_contract_id !='')
                            $filter .= " And organ_contract_id=".$instalment_condition_contract_id;
                        if($instalment_company_id !='')
                            $filter .= " And request_company_id=".$instalment_company_id;
                        if($fieldinsurance_id !='')
                            $filter .= " And fieldinsurance_id=".$fieldinsurance_id;
                        if($instalment_date_start !='')
                            $filter .= " And instalment_check_date >='".$instalment_date_start."'";
                        if($instalment_date_end !='')
                            $filter .= " And instalment_check_date<='".$instalment_date_end."'";
                    }
                $limit_state ="";
                if($limit!="" & $offset!="")
                  $limit_state = "LIMIT ".$limit.",".$offset;

                  $query_body = " from instalment_check_tb,instalment_conditions_tb,user_tb,
                  agent_tb,request_tb,company_tb,fieldinsurance_tb,
                  request_state,organ_contract_tb,organ_tb ,requst_ready_tb,organ_user_tb
                  where organ_contract_organ_id=organ_id
                  AND instalment_condition_contract_id=organ_contract_id
                  AND instalment_conditions_id=instalment_check_condition_id
                  AND  request_state_id=request_last_state_id
                  AND user_id=request_user_id
                  AND user_id=organ_user_user_id
                  AND agent_id=request_agent_id
                  AND fieldinsurance=request_fieldinsurance
                  AND  company_id=request_company_id
                  AND instalment_check_request_id=request_id
                  AND requst_ready_request_id=request_id
                   AND organ_user_id IN(
                      SELECT a.organ_user_id
                        FROM organ_user_tb a
                        INNER JOIN (
                           SELECT organ_user_user_id, MAX(organ_user_confirm_id) AS max_confirm_id
                        FROM organ_user_tb
                        WHERE organ_user_organ_id=$organ_id and organ_user_confirm_id<=100000000
                        GROUP by organ_user_user_id
                        ) b ON a.organ_user_confirm_id = b.max_confirm_id AND a.organ_user_user_id = b.organ_user_user_id AND a.organ_user_organ_id=$organ_id
                        INNER JOIN user_tb c
                        ON c.user_id=a.organ_user_user_id
                        INNER JOIN organ_confirm_tb d
                        ON a.organ_user_confirm_id=d.organ_confirm_id
                      ) 
                  AND instalment_conditions_mode_id=2 AND instalment_check_pass=1";
                $query="select * ".$query_body." ".$filter." AND organ_id=$organ_id ".$limit_state;
                $count_query="select count(*) as count ".$query_body." ".$filter." AND organ_id=".$organ_id;
                $result = $this->B_db->run_query($query);
                    $print = $this->post('print');
                    if($print==126)
                        echo $this->db->last_query();
                    $count  = $this->B_db->run_query($count_query);

                    $output =array();
                    foreach($result as $row)
                    {
                        $record=array();
                        $record["count"]=$count[0]['count'];
                        $record['user_id']=$row['user_id'];
                        $record['user_name']=$row['user_name'];
                        $record['user_family']=$row['user_family'];
                        $record['user_mobile']=$row['user_mobile'];
                        $record['user_national_code']=$row['user_national_code'];
                        $record['organ_contract_id']=$row['organ_contract_id'];
                        $record['organ_contract_num']=$row['organ_contract_num'];
                        $record['organ_user_personal_code']=$row['organ_user_personal_code'];
                        $record['requst_ready_start_date']=$row['requst_ready_start_date'];
                        $record['requst_ready_end_date']=$row['requst_ready_end_date'];
                        $record['requst_ready_num_ins']=$row['requst_ready_num_ins'];
                        $record['instalment_check_id']=$row['instalment_check_id'];
                        $record['instalment_check_condition_id']=$row['instalment_check_condition_id'];
                        $record['instalment_check_instalment_id']=$row['instalment_check_instalment_id'];
                        $record['instalment_check_user_pey_id']=$row['instalment_check_user_pey_id'];
                        $record['instalment_check_date']=$row['instalment_check_date'];
                        $record['instalment_check_num']=$row['instalment_check_num'];
                        $record['instalment_check_amount']=$row['instalment_check_amount'];
                        $record['instalment_check_desc']=$row['instalment_check_desc'];
                        $record['instalment_check_doc']=$row['instalment_check_doc'];
                        $record['instalment_check_date_pass']=$row['instalment_check_date_pass'];
                        $record['instalment_conditions_mode_id']=$row['instalment_conditions_mode_id'];
                        $request_id=$row['request_id'];
                        $record['request_id']=$row['request_id'];
                        $record['request_company_id']=$row['request_company_id'];
                        $record['company_name']=$row['company_name'];
                        $record['company_logo_url']=IMGADD.$row['company_logo_url'];
                        $record['fieldinsurance_logo_url']=IMGADD.$row['fieldinsurance_logo_url'];
                        $record['fieldinsurance_id']=$row['fieldinsurance_id'];
                        $record['request_fieldinsurance_fa']=$row['fieldinsurance_fa'];
                        $record['request_last_state_id']=$row['request_last_state_id'];
                        $record['request_last_state_name']=$row['request_state_name'];
                        //***************************************************************************************************************
                        $query17="select * from state_request_tb where staterequest_request_id=".$request_id." ORDER BY staterequest_id DESC LIMIT 1 ";
                        $result17=$this->B_db->run_query($query17);
                        if(!empty($result17))
                            $state_request17=$result17[0];
                        else
                            $state_request17=array();
                        $record['staterequest_last_timestamp']=$state_request17['staterequest_timestamp'];

                        //***************************************************************************************************************


                        //*************************************************************************************

                        $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'لیست درخواست های بازگشت وجه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                } else {
                    echo json_encode(array('result' => $organtoken[0]
                    , "data" => $organtoken[1]
                    , 'desc' => $organtoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }
            else   if ($command == "get_userorgan_request")
            {

                if($organtoken[0]=='ok')
                {
                    $organ_id=$organtoken[1];
                    $filter = "";
                    if($search_mode ==1){
                        if($user_name !='')
                            $filter .= " And user_tb.user_name like   '%".$user_name."%'   ESCAPE '!'";
                        if($user_family !='')
                            $filter .= " And user_tb.user_family like '%".$user_family."%' ESCAPE '!'";
                        if($user_mobile !='')
                            $filter .= " And user_tb.user_mobile=".$user_mobile;
                        if($user_national_code !='')
                            $filter .= " And user_tb.user_national_code=".$user_national_code;
                        if($organ_user_personal_code !='')
                            $filter .= " And organ_user_personal_code='".$organ_user_personal_code."'";
                        if($instalment_condition_contract_id !='')
                            $filter .= " And organ_contract_id=".$instalment_condition_contract_id;
                        if($instalment_company_id !='')
                            $filter .= " And request_company_id=".$instalment_company_id;
                        if($fieldinsurance_id !='')
                            $filter .= " And fieldinsurance_id=".$fieldinsurance_id;
                        if($instalment_date_start !='')
                            $filter .= " And requst_ready_start_date>= '".$instalment_date_start."'";
                        if($instalment_date_end !='')
                            $filter .= " And requst_ready_start_date<= '".$instalment_date_end."'";
                    }
                    $limit_state ="";
                    if($limit!="" & $offset!="")
                        $limit_state = "LIMIT ".$limit.",".$offset;
                    $query_body = " from user_tb,
                          agent_tb,request_tb,company_tb,fieldinsurance_tb,
                          request_state,organ_contract_tb,organ_tb ,requst_ready_tb,organ_request_tb,organ_user_tb
                          where organ_contract_organ_id=organ_id 
                          AND  request_state_id=request_last_state_id 
                          AND user_id=request_user_id 
                          AND user_id=organ_user_user_id
                          AND agent_id=request_agent_id 
                          AND fieldinsurance=request_fieldinsurance  
                          AND  company_id=request_company_id 
                          AND requst_ready_request_id=request_id 
                          AND request_organ=1
                          AND organ_user_id IN(
                      SELECT a.organ_user_id
                        FROM organ_user_tb a
                        INNER JOIN (
                           SELECT organ_user_user_id, MAX(organ_user_confirm_id) AS max_confirm_id
                        FROM organ_user_tb
                        WHERE organ_user_organ_id=$organ_id and organ_user_confirm_id<=100000000
                        GROUP by organ_user_user_id
                        ) b ON a.organ_user_confirm_id = b.max_confirm_id AND a.organ_user_user_id = b.organ_user_user_id AND a.organ_user_organ_id=$organ_id
                        INNER JOIN user_tb c
                        ON c.user_id=a.organ_user_user_id
                        INNER JOIN organ_confirm_tb d
                        ON a.organ_user_confirm_id=d.organ_confirm_id
                      ) 
                          AND organ_request_request_id=request_id            
                          AND organ_request_contract_id= organ_contract_id";
                    $query="select * ".$query_body." ".$filter." AND organ_id=$organ_id ".$limit_state;
                    $count_query="select count(*) as count ".$query_body." ".$filter." AND organ_id=".$organ_id;
                    $result = $this->B_db->run_query($query);

                    $print = $this->post('print');
                    if($print==126)
                        echo $this->db->last_query();

                    $count  = $this->B_db->run_query($count_query);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record=array();
                        $record["count"]=$count[0]['count'];
                        $record['user_id']=$row['user_id'];
                        $record['user_name']=$row['user_name'];
                        $record['user_family']=$row['user_family'];
                        $record['user_mobile']=$row['user_mobile'];
                        $record['user_national_code']=$row['user_national_code'];
                        $record['organ_contract_id']=$row['organ_contract_id'];
                        $record['organ_contract_num']=$row['organ_contract_num'];
                        $record['organ_user_personal_code']=$row['organ_user_personal_code'];
                        $record['requst_ready_start_date']=$row['requst_ready_start_date'];
                        $record['requst_ready_end_date']=$row['requst_ready_end_date'];
                        $record['requst_ready_num_ins']=$row['requst_ready_num_ins'];
                        $record['requst_ready_end_price']=$row['requst_ready_end_price'];
                        $request_id=$row['request_id'];
                        $record['request_id']=$row['request_id'];
                        //**********************************************************************************************
                        $query62=" SELECT * FROM instalment_check_tb WHERE instalment_check_request_id=".$request_id;
                        $result62 = $this->B_db->run_query($query62);
                        $output62 =array();
                        $output63 =array();
                        $sumnotpass=0;
                        $sumpass=0;
                        foreach($result62 as $row62)
                        {
                            if($row62['instalment_check_pass']==0){
                                $record62=array();
                                $record62['instalment_check_id'] = $row62['instalment_check_id'];
                                $record62['instalment_check_date']=$row62['instalment_check_date'];
                                $record62['instalment_check_amount']=$row62['instalment_check_amount'];
                                $record62['instalment_check_date_pass']=$row62['instalment_check_date_pass'];
                                $record62['instalment_check_doc']=$row62['instalment_check_doc'];
                                $output62[]=$record62;
                                $sumnotpass+=$row62['instalment_check_amount'];

                            }else{
                                $record63=array();
                                $record62['instalment_check_id'] = $row62['instalment_check_id'];
                                $record63['instalment_check_date']=$row62['instalment_check_date'];
                                $record63['instalment_check_amount']=$row62['instalment_check_amount'];
                                $record63['instalment_check_date_pass']=$row62['instalment_check_date_pass'];
                                $record63['instalment_check_doc']=$row62['instalment_check_doc'];
                                $output63[]=$record63;
                                $sumpass+=$row62['instalment_check_amount'];
                            }

                        }
                        $record['request_check_sumpass']=$sumpass;
                        $record['request_check_sumnotpass']=$sumnotpass;
                        $record['request_check_notpass']=$output62;
                        $record['request_check_pass']=$output63;

                        //*************************************************************************************************************
                        $query0="select sum(user_pey_amount) AS overpayment from user_pey_tb where user_pey_mode='overpayment' AND user_pey_request_id=".$request_id;
                        $result0 = $this->B_db->run_query($query0);
                        if(!empty($result0))
                            $user_pey0=$result0[0];
                        else
                            $user_pey0=array();
                        $overpayment=$user_pey0['overpayment'];

                        $query1="select sum(user_pey_amount) AS sumpey from user_pey_tb where not (user_pey_mode='overpayment') AND user_pey_request_id=".$request_id." ORDER BY user_pey_request_id";
                        $result1 = $this->B_db->run_query($query1);
                        if(!empty($result1))
                            $user_pey=$result1[0];
                        else
                            $user_pey=array();
                        $record['user_pey_amount']=$user_pey['sumpey']-$overpayment;

                        $query2="select sum(user_pey_amount) AS sumcash from user_pey_tb where not (user_pey_mode = 'instalment')  AND  not (user_pey_mode='overpayment') AND user_pey_request_id=".$request_id;
                        $result2 = $this->B_db->run_query($query2);
                        if(!empty($result2))
                            $user_pey2=$result2[0];
                        else
                            $user_pey2=array();
                        $record['user_pey_cash']=$user_pey2['sumcash']-$overpayment;

                        $query20="select sum(user_pey_amount) AS suminstalment from user_pey_tb where user_pey_mode = 'instalment' AND user_pey_request_id=".$request_id;
                        $result20 = $this->B_db->run_query($query20);
                        if(!empty($result20))
                            $user_pey20=$result20[0];
                        else
                            $user_pey20=array();
                        $record['user_pey_instalment']=$user_pey20['suminstalment'];
                        //*************************************************************************************************************
                        $request_id=$row['request_id'];
                        $record['request_id']=$row['request_id'];
                        $record['request_company_id']=$row['request_company_id'];
                        $record['company_name']=$row['company_name'];
                        $record['company_logo_url']=IMGADD.$row['company_logo_url'];
                        $record['fieldinsurance_logo_url']=IMGADD.$row['fieldinsurance_logo_url'];
                        $record['fieldinsurance_id']=$row['fieldinsurance_id'];
                        $record['request_fieldinsurance_fa']=$row['fieldinsurance_fa'];
                        $record['request_last_state_id']=$row['request_last_state_id'];
                        $record['request_last_state_name']=$row['request_state_name'];
                        //***************************************************************************************************************
                        $query17="select * from state_request_tb where staterequest_request_id=".$request_id." ORDER BY staterequest_id DESC LIMIT 1 ";
                        $result17=$this->B_db->run_query($query17);
                        if(!empty($result17))
                            $state_request17=$result17[0];
                        else
                            $state_request17=array();
                        $record['staterequest_last_timestamp']=$state_request17['staterequest_timestamp'];
                        //***************************************************************************************************************
                        if($organ_user_personal_code!=''){
                            if($row['organ_user_personal_code'] == $organ_user_personal_code)
                                $output[]=$record;
                        }else
                            $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'لیست درخواست های بازگشت وجه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                } else {
                    echo json_encode(array('result' => $organtoken[0]
                    , "data" => $organtoken[1]
                    , 'desc' => $organtoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }
            else   if ($command == "get_userorgan_request_noconfirm")
            {

                if($organtoken[0]=='ok')
                {
                    $organ_id=$organtoken[1];
                    $filter = "";
                    if($search_mode ==1){
                        if($user_name !='')
                            $filter .= " And user_tb.user_name like   '%".$user_name."%'   ESCAPE '!'";
                        if($user_family !='')
                            $filter .= " And user_tb.user_family like '%".$user_family."%' ESCAPE '!'";
                        if($user_mobile !='')
                            $filter .= " And user_tb.user_mobile=".$user_mobile;
                        if($user_national_code !='')
                            $filter .= " And user_tb.user_national_code=".$user_national_code;
                        if($organ_user_personal_code !='')
                            $filter .= " And organ_user_personal_code='".$organ_user_personal_code."'";
                        if($instalment_condition_contract_id !='')
                            $filter .= " And organ_contract_id=".$instalment_condition_contract_id;
                        if($instalment_company_id !='')
                            $filter .= " And request_company_id=".$instalment_company_id;
                        if($fieldinsurance_id !='')
                            $filter .= " And fieldinsurance_id=".$fieldinsurance_id;
                        if($instalment_date_start !='')
                            $filter .= " And requst_ready_start_date>= '".$instalment_date_start."'";
                        if($instalment_date_end !='')
                            $filter .= " And requst_ready_start_date<= '".$instalment_date_end."'";
                    }
                    $limit_state ="";

                    if($limit!="" & $offset!="")
                        $limit_state = "LIMIT ".$limit.",".$offset;
                    $query_body = " from user_tb,
                          agent_tb,request_tb,company_tb,fieldinsurance_tb,
                          request_state,organ_contract_tb,organ_tb ,organ_request_tb,organ_user_tb
                          where organ_contract_organ_id=organ_id 
                          AND  request_state_id=request_last_state_id 
                          AND user_id=request_user_id 
                          AND user_id=organ_user_user_id
                          AND agent_id=request_agent_id 
                          AND fieldinsurance=request_fieldinsurance  
                          AND  company_id=request_company_id 
                          AND request_organ=1
                          AND organ_request_confirm_admin_id= 0
                          AND organ_user_id IN(
                      SELECT a.organ_user_id
                        FROM organ_user_tb a
                        INNER JOIN (
                           SELECT organ_user_user_id, MAX(organ_user_confirm_id) AS max_confirm_id
                        FROM organ_user_tb
                        WHERE organ_user_organ_id=$organ_id and organ_user_confirm_id<=100000000
                        GROUP by organ_user_user_id
                        ) b ON a.organ_user_confirm_id = b.max_confirm_id AND a.organ_user_user_id = b.organ_user_user_id AND a.organ_user_organ_id=$organ_id
                        INNER JOIN user_tb c
                        ON c.user_id=a.organ_user_user_id
                        INNER JOIN organ_confirm_tb d
                        ON a.organ_user_confirm_id=d.organ_confirm_id
                      ) 
                          AND organ_request_request_id=request_id            
                          AND organ_request_contract_id= organ_contract_id";
                    $query="select * ".$query_body." ".$filter." AND organ_id=$organ_id ".$limit_state;
                    $count_query="select count(*) as count ".$query_body." ".$filter." AND organ_id=".$organ_id;
                    $result = $this->B_db->run_query($query);

                    $print = $this->post('print');
                    if($print==126)
                        echo $this->db->last_query();

                    $count  = $this->B_db->run_query($count_query);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record=array();
                        $record["count"]=$count[0]['count'];
                        $record['user_id']=$row['user_id'];
                        $record['user_name']=$row['user_name'];
                        $record['user_family']=$row['user_family'];
                        $record['user_mobile']=$row['user_mobile'];
                        $record['user_national_code']=$row['user_national_code'];
                        $record['organ_contract_id']=$row['organ_contract_id'];
                        $record['organ_contract_num']=$row['organ_contract_num'];
                        $record['organ_user_personal_code']=$row['organ_user_personal_code'];
                        $request_id=$row['request_id'];
                        $record['request_id']=$row['request_id'];
//**********************************************************************************************
                        $query62=" SELECT DISTINCT instalment_check_tb.* FROM instalment_check_tb,instalment_conditions_tb,request_tb,organ_request_tb,organ_contract_tb WHERE
                               instalment_check_request_id=request_id AND
                               request_id=organ_request_request_id AND
                               organ_request_contract_id=organ_contract_id AND
                               instalment_conditions_mode_id=2 AND
                               instalment_conditions_id=instalment_check_condition_id AND
                               request_last_state_id>9 AND
                               request_user_id=".$row['user_id']." AND                                                                                 
                               organ_contract_organ_id=$organ_id";
                        $result62 = $this->B_db->run_query($query62);
                        if(!empty($result62)) {


                            foreach ($result62 as $row62) {
                                if ($row62['instalment_check_pass'] == 0) {
                                    $record62 = array();
                                    $record62['instalment_check_id'] = $row62['instalment_check_id'];
                                    $record62['instalment_check_date'] = $row62['instalment_check_date'];
                                    $record62['instalment_check_amount'] = $row62['instalment_check_amount'];
                                    $record62['instalment_check_date_pass'] = $row62['instalment_check_date_pass'];
                                    $record62['instalment_check_doc'] = $row62['instalment_check_doc'];
                                    $output62[] = $record62;
                                    $sumnotpass += $row62['instalment_check_amount'];

                                } else {
                                    $record63 = array();
                                    $record63['instalment_check_id'] = $row62['instalment_check_id'];
                                    $record63['instalment_check_date'] = $row62['instalment_check_date'];
                                    $record63['instalment_check_amount'] = $row62['instalment_check_amount'];
                                    $record63['instalment_check_date_pass'] = $row62['instalment_check_date_pass'];
                                    $record63['instalment_check_doc'] = $row62['instalment_check_doc'];
                                    $output63[] = $record63;
                                    $sumpass += $row62['instalment_check_amount'];
                                }

                            }


                        }

                        $record['allrequest_check_sumpass'] = $sumpass;
                        $record['allrequest_check_sumnotpass'] = $sumnotpass;
                        $record['allrequest_check_notpass'] = $output62;
                        $record['allrequest_check_pass'] = $output63;
                        //**********************************************************************************************
                        $query62=" SELECT * FROM instalment_check_tb WHERE instalment_check_request_id=".$request_id;
                        $result62 = $this->B_db->run_query($query62);
                        $output62 =array();
                        $output63 =array();
                        $sumnotpass=0;
                        $sumpass=0;
                        foreach($result62 as $row62)
                        {
                            if($row62['instalment_check_pass']==0){
                                $record62=array();
                                $record62['instalment_check_id'] = $row62['instalment_check_id'];
                                $record62['instalment_check_date']=$row62['instalment_check_date'];
                                $record62['instalment_check_amount']=$row62['instalment_check_amount'];
                                $record62['instalment_check_date_pass']=$row62['instalment_check_date_pass'];
                                $record62['instalment_check_doc']=$row62['instalment_check_doc'];
                                $output62[]=$record62;
                                $sumnotpass+=$row62['instalment_check_amount'];

                            }else{
                                $record63=array();
                                $record62['instalment_check_id'] = $row62['instalment_check_id'];
                                $record63['instalment_check_date']=$row62['instalment_check_date'];
                                $record63['instalment_check_amount']=$row62['instalment_check_amount'];
                                $record63['instalment_check_date_pass']=$row62['instalment_check_date_pass'];
                                $record63['instalment_check_doc']=$row62['instalment_check_doc'];
                                $output63[]=$record63;
                                $sumpass+=$row62['instalment_check_amount'];
                            }

                        }
                        $record['request_check_sumpass']=$sumpass;
                        $record['request_check_sumnotpass']=$sumnotpass;
                        $record['request_check_notpass']=$output62;
                        $record['request_check_pass']=$output63;
                        $arrinstalmentpermount= $this->B_organ->get_instalmentnopass_permonth($row['user_id'],$organ_id);
                        $record['arrinstalmentpermount']=$arrinstalmentpermount;
                        $record['maxinstalmentpermount']=max($arrinstalmentpermount);
                        //*************************************************************************************************************
                        $query0="select sum(user_pey_amount) AS overpayment from user_pey_tb where user_pey_mode='overpayment' AND user_pey_request_id=".$request_id;
                        $result0 = $this->B_db->run_query($query0);
                        if(!empty($result0))
                            $user_pey0=$result0[0];
                        else
                            $user_pey0=array();
                        $overpayment=$user_pey0['overpayment'];

                        $query1="select sum(user_pey_amount) AS sumpey from user_pey_tb where not (user_pey_mode='overpayment') AND user_pey_request_id=".$request_id." ORDER BY user_pey_request_id";
                        $result1 = $this->B_db->run_query($query1);
                        if(!empty($result1))
                            $user_pey=$result1[0];
                        else
                            $user_pey=array();
                        $record['user_pey_amount']=$user_pey['sumpey']-$overpayment;

                        $query2="select sum(user_pey_amount) AS sumcash from user_pey_tb where not (user_pey_mode = 'instalment')  AND  not (user_pey_mode='overpayment') AND user_pey_request_id=".$request_id;
                        $result2 = $this->B_db->run_query($query2);
                        if(!empty($result2))
                            $user_pey2=$result2[0];
                        else
                            $user_pey2=array();
                        $record['user_pey_cash']=$user_pey2['sumcash']-$overpayment;

                        $query20="select sum(user_pey_amount) AS suminstalment from user_pey_tb where user_pey_mode = 'instalment' AND user_pey_request_id=".$request_id;
                        $result20 = $this->B_db->run_query($query20);
                        if(!empty($result20))
                            $user_pey20=$result20[0];
                        else
                            $user_pey20=array();
                        $record['user_pey_instalment']=$user_pey20['suminstalment'];
                        //*************************************************************************************************************
                        $request_id=$row['request_id'];
                        $record['request_id']=$row['request_id'];
                        $record['request_company_id']=$row['request_company_id'];
                        $record['company_name']=$row['company_name'];
                        $record['company_logo_url']=IMGADD.$row['company_logo_url'];
                        $record['fieldinsurance_logo_url']=IMGADD.$row['fieldinsurance_logo_url'];
                        $record['fieldinsurance_id']=$row['fieldinsurance_id'];
                        $record['request_fieldinsurance_fa']=$row['fieldinsurance_fa'];
                        $record['request_last_state_id']=$row['request_last_state_id'];
                        $record['request_last_state_name']=$row['request_state_name'];
                        //***************************************************************************************************************
                        $query17="select * from state_request_tb where staterequest_request_id=".$request_id." ORDER BY staterequest_id DESC LIMIT 1 ";
                        $result17=$this->B_db->run_query($query17);
                        if(!empty($result17))
                            $state_request17=$result17[0];
                        else
                            $state_request17=array();
                        $record['staterequest_last_timestamp']=$state_request17['staterequest_timestamp'];
                        //***************************************************************************************************************
                        if($organ_user_personal_code!=''){
                            if($row['organ_user_personal_code'] == $organ_user_personal_code)
                                $output[]=$record;
                        }else
                            $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'لیست درخواست های بازگشت وجه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                } else {
                    echo json_encode(array('result' => $organtoken[0]
                    , "data" => $organtoken[1]
                    , 'desc' => $organtoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }
            else   if ($command == "get_userorgan_request_confirm")
            {

                if($organtoken[0]=='ok')
                {
                    $organ_id=$organtoken[1];
                    $filter = "";
                    if($search_mode ==1){
                        if($user_name !='')
                            $filter .= " And user_tb.user_name like   '%".$user_name."%'   ESCAPE '!'";
                        if($user_family !='')
                            $filter .= " And user_tb.user_family like '%".$user_family."%' ESCAPE '!'";
                        if($user_mobile !='')
                            $filter .= " And user_tb.user_mobile=".$user_mobile;
                        if($user_national_code !='')
                            $filter .= " And user_tb.user_national_code=".$user_national_code;
                        if($organ_user_personal_code !='')
                            $filter .= " And organ_user_personal_code='".$organ_user_personal_code."'";
                        if($instalment_condition_contract_id !='')
                            $filter .= " And organ_contract_id=".$instalment_condition_contract_id;
                        if($instalment_company_id !='')
                            $filter .= " And request_company_id=".$instalment_company_id;
                        if($fieldinsurance_id !='')
                            $filter .= " And fieldinsurance_id=".$fieldinsurance_id;
                        if($instalment_date_start !='')
                            $filter .= " And requst_ready_start_date>= '".$instalment_date_start."'";
                        if($instalment_date_end !='')
                            $filter .= " And requst_ready_start_date<= '".$instalment_date_end."'";
                    }
                    $limit_state ="";

                    if($limit!="" & $offset!="")
                        $limit_state = "LIMIT ".$limit.",".$offset;
                    $query_body = " from user_tb,
                          agent_tb,request_tb,company_tb,fieldinsurance_tb,
                          request_state,organ_contract_tb,organ_tb ,requst_ready_tb,organ_request_tb,organ_user_tb
                          where organ_contract_organ_id=organ_id 
                          AND  request_state_id=request_last_state_id 
                          AND user_id=request_user_id 
                          AND user_id=organ_user_user_id
                          AND agent_id=request_agent_id 
                          AND fieldinsurance=request_fieldinsurance  
                          AND  company_id=request_company_id 
                          AND requst_ready_request_id=request_id 
                          AND request_organ=1
                          AND organ_request_confirm_admin_id <> 0
                          AND organ_user_id IN(
                      SELECT a.organ_user_id
                        FROM organ_user_tb a
                        INNER JOIN (
                           SELECT organ_user_user_id, MAX(organ_user_confirm_id) AS max_confirm_id
                        FROM organ_user_tb
                        WHERE organ_user_organ_id=$organ_id and organ_user_confirm_id<=100000000
                        GROUP by organ_user_user_id
                        ) b ON a.organ_user_confirm_id = b.max_confirm_id AND a.organ_user_user_id = b.organ_user_user_id AND a.organ_user_organ_id=$organ_id
                        INNER JOIN user_tb c
                        ON c.user_id=a.organ_user_user_id
                        INNER JOIN organ_confirm_tb d
                        ON a.organ_user_confirm_id=d.organ_confirm_id
                      ) 
                          AND organ_request_request_id=request_id            
                          AND organ_request_contract_id= organ_contract_id";
                    $query="select * ".$query_body." ".$filter." AND organ_id=$organ_id ".$limit_state;
                    $count_query="select count(*) as count ".$query_body." ".$filter." AND organ_id=".$organ_id;
                    $result = $this->B_db->run_query($query);

                    $print = $this->post('print');
                    if($print==126)
                        echo $this->db->last_query();

                    $count  = $this->B_db->run_query($count_query);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record=array();
                        $record["count"]=$count[0]['count'];
                        $record['user_id']=$row['user_id'];
                        $record['user_name']=$row['user_name'];
                        $record['user_family']=$row['user_family'];
                        $record['user_mobile']=$row['user_mobile'];
                        $record['user_national_code']=$row['user_national_code'];
                        $record['organ_contract_id']=$row['organ_contract_id'];
                        $record['organ_contract_num']=$row['organ_contract_num'];
                        $record['organ_user_personal_code']=$row['organ_user_personal_code'];
                        $record['requst_ready_start_date']=$row['requst_ready_start_date'];
                        $record['requst_ready_end_date']=$row['requst_ready_end_date'];
                        $record['requst_ready_num_ins']=$row['requst_ready_num_ins'];
                        $record['requst_ready_end_price']=$row['requst_ready_end_price'];
                        $record['organ_request_confirm_admin_id']=$row['organ_request_confirm_admin_id'];
                        $record['organ_request_confirm_admin_date']=$row['organ_request_confirm_admin_date'];
                        $request_id=$row['request_id'];
                        $record['request_id']=$row['request_id'];
//**********************************************************************************************
                        $query62=" SELECT DISTINCT instalment_check_tb.* FROM instalment_check_tb,instalment_conditions_tb,request_tb,organ_request_tb,organ_contract_tb WHERE
                               instalment_check_request_id=request_id AND
                               request_id=organ_request_request_id AND
                               organ_request_contract_id=organ_contract_id AND
                               instalment_conditions_mode_id=2 AND
                               instalment_conditions_id=instalment_check_condition_id AND
                               request_last_state_id>9 AND
                               request_user_id=".$row['user_id']." AND                                                                                 
                               organ_contract_organ_id=$organ_id";
                        $result62 = $this->B_db->run_query($query62);
                        if(!empty($result62)) {


                            foreach ($result62 as $row62) {
                                if ($row62['instalment_check_pass'] == 0) {
                                    $record62 = array();
                                    $record62['instalment_check_id'] = $row62['instalment_check_id'];
                                    $record62['instalment_check_date'] = $row62['instalment_check_date'];
                                    $record62['instalment_check_amount'] = $row62['instalment_check_amount'];
                                    $record62['instalment_check_date_pass'] = $row62['instalment_check_date_pass'];
                                    $record62['instalment_check_doc'] = $row62['instalment_check_doc'];
                                    $output62[] = $record62;
                                    $sumnotpass += $row62['instalment_check_amount'];

                                } else {
                                    $record63 = array();
                                    $record63['instalment_check_id'] = $row62['instalment_check_id'];
                                    $record63['instalment_check_date'] = $row62['instalment_check_date'];
                                    $record63['instalment_check_amount'] = $row62['instalment_check_amount'];
                                    $record63['instalment_check_date_pass'] = $row62['instalment_check_date_pass'];
                                    $record63['instalment_check_doc'] = $row62['instalment_check_doc'];
                                    $output63[] = $record63;
                                    $sumpass += $row62['instalment_check_amount'];
                                }

                            }


                        }

                        $record['allrequest_check_sumpass'] = $sumpass;
                        $record['allrequest_check_sumnotpass'] = $sumnotpass;
                        $record['allrequest_check_notpass'] = $output62;
                        $record['allrequest_check_pass'] = $output63;
                        //**********************************************************************************************
                        $query62=" SELECT * FROM instalment_check_tb WHERE instalment_check_request_id=".$request_id;
                        $result62 = $this->B_db->run_query($query62);
                        $output62 =array();
                        $output63 =array();
                        $sumnotpass=0;
                        $sumpass=0;
                        foreach($result62 as $row62)
                        {
                            if($row62['instalment_check_pass']==0){
                                $record62=array();
                                $record62['instalment_check_id'] = $row62['instalment_check_id'];
                                $record62['instalment_check_date']=$row62['instalment_check_date'];
                                $record62['instalment_check_amount']=$row62['instalment_check_amount'];
                                $record62['instalment_check_date_pass']=$row62['instalment_check_date_pass'];
                                $record62['instalment_check_doc']=$row62['instalment_check_doc'];
                                $output62[]=$record62;
                                $sumnotpass+=$row62['instalment_check_amount'];

                            }else{
                                $record63=array();
                                $record62['instalment_check_id'] = $row62['instalment_check_id'];
                                $record63['instalment_check_date']=$row62['instalment_check_date'];
                                $record63['instalment_check_amount']=$row62['instalment_check_amount'];
                                $record63['instalment_check_date_pass']=$row62['instalment_check_date_pass'];
                                $record63['instalment_check_doc']=$row62['instalment_check_doc'];
                                $output63[]=$record63;
                                $sumpass+=$row62['instalment_check_amount'];
                            }

                        }
                        $record['request_check_sumpass']=$sumpass;
                        $record['request_check_sumnotpass']=$sumnotpass;
                        $record['request_check_notpass']=$output62;
                        $record['request_check_pass']=$output63;
                        $arrinstalmentpermount= $this->B_organ->get_instalmentnopass_permonth($row['user_id'],$organ_id);
                        $record['arrinstalmentpermount']=$arrinstalmentpermount;
                        $record['maxinstalmentpermount']=max($arrinstalmentpermount);
                        //*************************************************************************************************************
                        $query0="select sum(user_pey_amount) AS overpayment from user_pey_tb where user_pey_mode='overpayment' AND user_pey_request_id=".$request_id;
                        $result0 = $this->B_db->run_query($query0);
                        if(!empty($result0))
                            $user_pey0=$result0[0];
                        else
                            $user_pey0=array();
                        $overpayment=$user_pey0['overpayment'];

                        $query1="select sum(user_pey_amount) AS sumpey from user_pey_tb where not (user_pey_mode='overpayment') AND user_pey_request_id=".$request_id." ORDER BY user_pey_request_id";
                        $result1 = $this->B_db->run_query($query1);
                        if(!empty($result1))
                            $user_pey=$result1[0];
                        else
                            $user_pey=array();
                        $record['user_pey_amount']=$user_pey['sumpey']-$overpayment;

                        $query2="select sum(user_pey_amount) AS sumcash from user_pey_tb where not (user_pey_mode = 'instalment')  AND  not (user_pey_mode='overpayment') AND user_pey_request_id=".$request_id;
                        $result2 = $this->B_db->run_query($query2);
                        if(!empty($result2))
                            $user_pey2=$result2[0];
                        else
                            $user_pey2=array();
                        $record['user_pey_cash']=$user_pey2['sumcash']-$overpayment;

                        $query20="select sum(user_pey_amount) AS suminstalment from user_pey_tb where user_pey_mode = 'instalment' AND user_pey_request_id=".$request_id;
                        $result20 = $this->B_db->run_query($query20);
                        if(!empty($result20))
                            $user_pey20=$result20[0];
                        else
                            $user_pey20=array();
                        $record['user_pey_instalment']=$user_pey20['suminstalment'];
                        //*************************************************************************************************************
                        $request_id=$row['request_id'];
                        $record['request_id']=$row['request_id'];
                        $record['request_company_id']=$row['request_company_id'];
                        $record['company_name']=$row['company_name'];
                        $record['company_logo_url']=IMGADD.$row['company_logo_url'];
                        $record['fieldinsurance_logo_url']=IMGADD.$row['fieldinsurance_logo_url'];
                        $record['fieldinsurance_id']=$row['fieldinsurance_id'];
                        $record['request_fieldinsurance_fa']=$row['fieldinsurance_fa'];
                        $record['request_last_state_id']=$row['request_last_state_id'];
                        $record['request_last_state_name']=$row['request_state_name'];
                        //***************************************************************************************************************
                        $query17="select * from state_request_tb where staterequest_request_id=".$request_id." ORDER BY staterequest_id DESC LIMIT 1 ";
                        $result17=$this->B_db->run_query($query17);
                        if(!empty($result17))
                            $state_request17=$result17[0];
                        else
                            $state_request17=array();
                        $record['staterequest_last_timestamp']=$state_request17['staterequest_timestamp'];
                        //***************************************************************************************************************
                        if($organ_user_personal_code!=''){
                            if($row['organ_user_personal_code'] == $organ_user_personal_code)
                                $output[]=$record;
                        }else
                            $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'لیست درخواست های بازگشت وجه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                } else {
                    echo json_encode(array('result' => $organtoken[0]
                    , "data" => $organtoken[1]
                    , 'desc' => $organtoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }
            else   if ($command == "confirm_organ_request")
            {
                if($organtoken[0]=='ok')
                {
                    $request_id = $this->post('request_id');

                    $query1 = "UPDATE organ_request_tb SET organ_request_confirm_admin_id=".$organtoken[1]." ,organ_request_confirm_admin_date=now()  WHERE organ_request_request_id=$request_id";
                    $result1 = $this->B_db->run_query_put($query1);
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>""
                    ,'desc'=>'لیست درخواست های بازگشت وجه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                }else {
                    echo json_encode(array('result' => $organtoken[0]
                    , "data" => $organtoken[1]
                    , 'desc' => $organtoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }
            else   if ($command == "get_user_organ")
            {
                if($organtoken[0]=='ok')
                {
                    $organ_id=$organtoken[1];
                    $filter = "";
                    if($search_mode ==1){
                        if($user_name !='')
                            $filter .= " And c.user_name like   '%".$user_name."%'   ESCAPE '!'";
                        if($user_family !='')
                            $filter .= " And c.user_family like '%".$user_family."%' ESCAPE '!'";
                        if($user_mobile !='')
                            $filter .= " And c.user_mobile=".$user_mobile;
                        if($user_national_code !='')
                            $filter .= " And c.user_national_code=".$user_national_code;
                        //if($organ_user_personal_code !='')
                            //$filter .= " And organ_user_personal_code='".$organ_user_personal_code."'";
                        //if($instalment_condition_contract_id !='')
                            //$filter .= " And organ_contract_id=".$instalment_condition_contract_id;
                        //if($instalment_company_id !='')
                            //$filter .= " And request_company_id=".$instalment_company_id;
                        //if($fieldinsurance_id !='')
                            //$filter .= " And fieldinsurance_id=".$fieldinsurance_id;
                    }
                    $limit_state ="";
                    if($limit!="" & $offset!="")
                        $limit_state = "LIMIT ".$limit.",".$offset;

                    $query="SELECT a.*,c.*,d.*
                    FROM organ_user_tb a
                    INNER JOIN (
                       SELECT organ_user_user_id, MAX(organ_user_confirm_id) AS max_confirm_id
                    FROM organ_user_tb
                    WHERE organ_user_organ_id=$organ_id and organ_user_confirm_id<=100000000000 
                    GROUP by organ_user_user_id
                    ) b ON a.organ_user_confirm_id = b.max_confirm_id AND a.organ_user_user_id = b.organ_user_user_id AND a.organ_user_organ_id=$organ_id
                    INNER JOIN user_tb c
                    ON c.user_id=a.organ_user_user_id ".$filter."
                    INNER JOIN organ_confirm_tb d
                    ON a.organ_user_confirm_id=d.organ_confirm_id ".$limit_state ;

                    $result = $this->B_db->run_query($query);
                    $count_query="SELECT count(*) as count
                    FROM organ_user_tb a
                    INNER JOIN (
                       SELECT organ_user_user_id, MAX(organ_user_confirm_id) AS max_confirm_id
                    FROM organ_user_tb
                    WHERE organ_user_organ_id=$organ_id and organ_user_confirm_id<=100000000000 
                    GROUP by organ_user_user_id
                    ) b ON a.organ_user_confirm_id = b.max_confirm_id AND a.organ_user_user_id = b.organ_user_user_id AND a.organ_user_organ_id=$organ_id
                    INNER JOIN user_tb c
                    ON c.user_id=a.organ_user_user_id ".$filter."
                    INNER JOIN organ_confirm_tb d
                    ON a.organ_user_confirm_id=d.organ_confirm_id";
                    $count  = $this->B_db->run_query($count_query);

                    $output =array();
                    foreach($result as $row)
                    {
                        $record=array();
                        $record["count"]=$count[0]['count'];
                        $record['user_id']=$row['user_id'];
                        $record['user_name']=$row['user_name'];
                        $record['user_family']=$row['user_family'];
                        $record['user_mobile']=$row['user_mobile'];
                        $record['user_national_code']=$row['user_national_code'];

                        $record['organ_user_personal_code'] = $row['organ_user_personal_code'];


                        $arrinstalmentpermount= $this->B_organ->get_instalmentnopass_permonth($row['user_id'],$organ_id);
                        $record['arrinstalmentpermount']=$arrinstalmentpermount;
                        $record['maxinstalmentpermount']=max($arrinstalmentpermount);
                        $output62 = array();
                        $output63 = array();
                        $sumnotpass = 0;
                        $sumpass = 0;

                        //*************************************************************************************************************
                        $query62=" SELECT DISTINCT instalment_check_tb.* FROM instalment_check_tb,instalment_conditions_tb,request_tb,organ_request_tb,organ_contract_tb WHERE
                               instalment_check_request_id=request_id AND
                               request_id=organ_request_request_id AND
                               organ_request_contract_id=organ_contract_id AND
                               instalment_conditions_mode_id=2 AND
                               instalment_conditions_id=instalment_check_condition_id AND
                               request_last_state_id>9 AND
                               request_user_id=".$row['user_id']." AND                                                                                 
                               organ_contract_organ_id=$organ_id";
                        $result62 = $this->B_db->run_query($query62);
                        if(!empty($result62)) {


                            foreach ($result62 as $row62) {
                                if ($row62['instalment_check_pass'] == 0) {
                                    $record62 = array();
                                    $record62['instalment_check_id'] = $row62['instalment_check_id'];
                                    $record62['instalment_check_date'] = $row62['instalment_check_date'];
                                    $record62['instalment_check_amount'] = $row62['instalment_check_amount'];
                                    $record62['instalment_check_date_pass'] = $row62['instalment_check_date_pass'];
                                    $record62['instalment_check_doc'] = $row62['instalment_check_doc'];
                                    $output62[] = $record62;
                                    $sumnotpass += $row62['instalment_check_amount'];

                                } else {
                                    $record63 = array();
                                    $record63['instalment_check_id'] = $row62['instalment_check_id'];
                                    $record63['instalment_check_date'] = $row62['instalment_check_date'];
                                    $record63['instalment_check_amount'] = $row62['instalment_check_amount'];
                                    $record63['instalment_check_date_pass'] = $row62['instalment_check_date_pass'];
                                    $record63['instalment_check_doc'] = $row62['instalment_check_doc'];
                                    $output63[] = $record63;
                                    $sumpass += $row62['instalment_check_amount'];
                                }

                            }


                        }

                        $record['request_check_sumpass'] = $sumpass;
                        $record['request_check_sumnotpass'] = $sumnotpass;
                        $record['request_check_notpass'] = $output62;
                        $record['request_check_pass'] = $output63;

                        //*************************************************************************************

                        $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'لیست درخواست های بازگشت وجه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                } else {
                    echo json_encode(array('result' => $organtoken[0]
                    , "data" => $organtoken[1]
                    , 'desc' => $organtoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }
            else   if ($command == "add_organ_instalment_temp")
            {
                $instalment_check_organ_temp_check_id = $this->post('instalment_check_organ_temp_check_id');
                $instalment_check_organ_temp_confirm_id= $this->post('instalment_check_organ_temp_confirm_id');

                if($organtoken[0]=='ok')
                {
                    $query="select * from instalment_check_organ_temp_tb where instalment_check_organ_temp_check_id=".$instalment_check_organ_temp_check_id." AND instalment_check_organ_temp_confirm_id=".$instalment_check_organ_temp_confirm_id."";

                    $result=$this->B_db->run_query($query);
                    $num=count($result[0]);
                    if ($num==0) {
                        $result2 = $this->B_organ->add_organ_instalment_temp($instalment_check_organ_temp_check_id, $instalment_check_organ_temp_confirm_id);
                        echo json_encode(array('result' => "ok"
                        , "data" => ""
                        , 'desc' => 'سند ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }else{
                        $instalment_check_organ_temp_check=$result[0];
                        echo json_encode(array('result'=>"error"
                        ,"data"=>array('agent_extra_id'=>$instalment_check_organ_temp_check['instalment_check_organ_temp_id'])
                        ,'desc'=>' سند تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                } else {
                    echo json_encode(array('result' => $organtoken[0]
                    , "data" => $organtoken[1]
                    , 'desc' => $organtoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }
            else   if ($command == "get_user_organ_installment")
            {
                if($organtoken[0]=='ok')
                {
                    $organ_id=$organtoken[1];
                    $filter = "";
                    if($search_mode ==1){
                        if($user_name !='')
                            $filter .= " And c.user_name like   '%".$user_name."%'   ESCAPE '!'";
                        if($user_family !='')
                            $filter .= " And c.user_family like '%".$user_family."%' ESCAPE '!'";
                        if($user_mobile !='')
                            $filter .= " And c.user_mobile=".$user_mobile;
                        if($user_national_code !='')
                            $filter .= " And c.user_national_code=".$user_national_code;
                        //if($organ_user_personal_code !='')
                        //$filter .= " And organ_user_personal_code='".$organ_user_personal_code."'";
                        //if($instalment_condition_contract_id !='')
                        //$filter .= " And organ_contract_id=".$instalment_condition_contract_id;
                        //if($instalment_company_id !='')
                        //$filter .= " And request_company_id=".$instalment_company_id;
                        //if($fieldinsurance_id !='')
                        //$filter .= " And fieldinsurance_id=".$fieldinsurance_id;
                    }
                    $limit_state ="";
                    if($limit!="" & $offset!="")
                        $limit_state = "LIMIT ".$limit.",".$offset;

                    $query="SELECT a.*,c.*,d.*
                    FROM organ_user_tb a
                    INNER JOIN (
                       SELECT organ_user_user_id, MAX(organ_user_confirm_id) AS max_confirm_id
                    FROM organ_user_tb
                    WHERE organ_user_organ_id=$organ_id and organ_user_confirm_id<=100000000000 
                    GROUP by organ_user_user_id
                    ) b ON a.organ_user_confirm_id = b.max_confirm_id AND a.organ_user_user_id = b.organ_user_user_id AND a.organ_user_organ_id=$organ_id
                    INNER JOIN user_tb c
                    ON c.user_id=a.organ_user_user_id ".$filter."
                    INNER JOIN organ_confirm_tb d
                    ON a.organ_user_confirm_id=d.organ_confirm_id ".$limit_state ;

                    $result = $this->B_db->run_query($query);
                    $count_query="SELECT count(*) as count
                    FROM organ_user_tb a
                    INNER JOIN (
                       SELECT organ_user_user_id, MAX(organ_user_confirm_id) AS max_confirm_id
                    FROM organ_user_tb
                    WHERE organ_user_organ_id=$organ_id and organ_user_confirm_id<=100000000000 
                    GROUP by organ_user_user_id
                    ) b ON a.organ_user_confirm_id = b.max_confirm_id AND a.organ_user_user_id = b.organ_user_user_id AND a.organ_user_organ_id=$organ_id
                    INNER JOIN user_tb c
                    ON c.user_id=a.organ_user_user_id ".$filter."
                    INNER JOIN organ_confirm_tb d
                    ON a.organ_user_confirm_id=d.organ_confirm_id";
                    $count  = $this->B_db->run_query($count_query);

                    $output =array();
                    foreach($result as $row)
                    {
                        $record=array();
                        $record["count"]=$count[0]['count'];
                        $record['user_id']=$row['user_id'];
                        $record['user_name']=$row['user_name'];
                        $record['user_family']=$row['user_family'];
                        $record['user_mobile']=$row['user_mobile'];
                        $record['user_national_code']=$row['user_national_code'];

                        $record['organ_user_personal_code'] = $row['organ_user_personal_code'];


                        $output62 = array();
                        $sumnotpass = 0;

                        //*************************************************************************************************************
                        $query62=" SELECT  instalment_check_tb.*,fieldinsurance_fa,fieldinsurance_id,company_id,company_name,requst_ready_start_date,requst_ready_end_date,requst_ready_num_ins,requst_ready_end_price
                             FROM requst_ready_tb,instalment_check_tb,company_tb,fieldinsurance_tb,instalment_conditions_tb,request_tb,organ_request_tb,organ_contract_tb WHERE
                               instalment_check_request_id=request_id AND
                               request_id=organ_request_request_id AND
                               organ_request_contract_id=organ_contract_id AND
                               instalment_conditions_mode_id=2 AND
                               instalment_conditions_id=instalment_check_condition_id AND
                                     instalment_check_pass=0 AND 
                               request_last_state_id>9 AND
                                                         requst_ready_request_id=request_id AND                                                                                                                                                                                                   
                       fieldinsurance=request_fieldinsurance  AND
                       company_id=request_company_id AND
                               request_user_id=".$row['user_id']." AND                                                                                 
                               organ_contract_organ_id=$organ_id";
                        if($date_start !=''){
                            $query62.= " AND instalment_check_date>= '".$date_start."' ";
                        }
                        if($date_end !=''){
                            $query62.= " AND instalment_check_date <'".$date_end."' ";
                         }
                        $result62 = $this->B_db->run_query($query62);
                        if(!empty($result62)) {
                            foreach ($result62 as $row62) {

                                $record62 = array();
                                $record62['instalment_check_id'] = $row62['instalment_check_id'];
                                $record62['instalment_check_date'] = $row62['instalment_check_date'];
                                $record62['fieldinsurance_fa'] = $row62['fieldinsurance_fa'];
                                $record62['fieldinsurance_id'] = $row62['fieldinsurance_id'];
                                $record62['company_id'] = $row62['company_id'];
                                $record62['company_name'] = $row62['company_name'];
                                $record62['instalment_check_amount'] = $row62['instalment_check_amount'];
                                $record62['requst_ready_start_date']=$row62['requst_ready_start_date'];
                                $record62['requst_ready_end_date']=$row62['requst_ready_end_date'];
                                $record62['requst_ready_num_ins']=$row62['requst_ready_num_ins'];
                                $record62['requst_ready_end_price']=$row62['requst_ready_end_price'];

                                $output62[] = $record62;
                                    $sumnotpass += $row62['instalment_check_amount'];
                            }
                        }

                        $record['request_check_sumnotpass'] = $sumnotpass;
                        $record['request_check_notpass'] = $output62;

                        //*************************************************************************************
if($sumnotpass>0) {
    $output[] = $record;
}
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'لیست درخواست های بازگشت وجه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                } else {
                    echo json_encode(array('result' => $organtoken[0]
                    , "data" => $organtoken[1]
                    , 'desc' => $organtoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }

            else   if ($command == "get_user_organ_installment_apart")
            {
                if($organtoken[0]=='ok')
                {
                    $organ_id=$organtoken[1];
                    $filter = "";
                    if($search_mode ==1){
                        if($user_name !='')
                            $filter .= " And c.user_name like   '%".$user_name."%'   ESCAPE '!'";
                        if($user_family !='')
                            $filter .= " And c.user_family like '%".$user_family."%' ESCAPE '!'";
                        if($user_mobile !='')
                            $filter .= " And c.user_mobile=".$user_mobile;
                        if($user_national_code !='')
                            $filter .= " And c.user_national_code=".$user_national_code;
                        //if($organ_user_personal_code !='')
                        //$filter .= " And organ_user_personal_code='".$organ_user_personal_code."'";
                        //if($instalment_condition_contract_id !='')
                        //$filter .= " And organ_contract_id=".$instalment_condition_contract_id;
                        //if($instalment_company_id !='')
                        //$filter .= " And request_company_id=".$instalment_company_id;
                        //if($fieldinsurance_id !='')
                        //$filter .= " And fieldinsurance_id=".$fieldinsurance_id;
                    }
                    $limit_state ="";
                    if($limit!="" & $offset!="")
                        $limit_state = "LIMIT ".$limit.",".$offset;

                    $query="SELECT a.*,c.*,d.*
                    FROM organ_user_tb a
                    INNER JOIN (
                       SELECT organ_user_user_id, MAX(organ_user_confirm_id) AS max_confirm_id
                    FROM organ_user_tb
                    WHERE organ_user_organ_id=$organ_id and organ_user_confirm_id<=100000000000 
                    GROUP by organ_user_user_id
                    ) b ON a.organ_user_confirm_id = b.max_confirm_id AND a.organ_user_user_id = b.organ_user_user_id AND a.organ_user_organ_id=$organ_id
                    INNER JOIN user_tb c
                    ON c.user_id=a.organ_user_user_id ".$filter."
                    INNER JOIN organ_confirm_tb d
                    ON a.organ_user_confirm_id=d.organ_confirm_id ".$limit_state ;

                    $result = $this->B_db->run_query($query);
                    $count_query="SELECT count(*) as count
                    FROM organ_user_tb a
                    INNER JOIN (
                       SELECT organ_user_user_id, MAX(organ_user_confirm_id) AS max_confirm_id
                    FROM organ_user_tb
                    WHERE organ_user_organ_id=$organ_id and organ_user_confirm_id<=100000000000 
                    GROUP by organ_user_user_id
                    ) b ON a.organ_user_confirm_id = b.max_confirm_id AND a.organ_user_user_id = b.organ_user_user_id AND a.organ_user_organ_id=$organ_id
                    INNER JOIN user_tb c
                    ON c.user_id=a.organ_user_user_id ".$filter."
                    INNER JOIN organ_confirm_tb d
                    ON a.organ_user_confirm_id=d.organ_confirm_id";
                    $count  = $this->B_db->run_query($count_query);

                    $output =array();
                    foreach($result as $row)
                    {



                        //*************************************************************************************************************
                        $query62=" SELECT  instalment_check_tb.*,fieldinsurance_fa,fieldinsurance_id,company_id,company_name,requst_ready_start_date,requst_ready_end_date,requst_ready_num_ins,requst_ready_end_price
                             FROM requst_ready_tb,instalment_check_tb,company_tb,fieldinsurance_tb,instalment_conditions_tb,request_tb,organ_request_tb,organ_contract_tb WHERE
                               instalment_check_request_id=request_id AND
                               request_id=organ_request_request_id AND
                               organ_request_contract_id=organ_contract_id AND
                               instalment_conditions_mode_id=2 AND
                               instalment_conditions_id=instalment_check_condition_id AND
                                     instalment_check_pass=0 AND 
                               request_last_state_id>9 AND
                                                         requst_ready_request_id=request_id AND                                                                                                                                                                                                   
                       fieldinsurance=request_fieldinsurance  AND
                       company_id=request_company_id AND
                               request_user_id=".$row['user_id']." AND                                                                                 
                               organ_contract_organ_id=$organ_id";
                        if($date_start !=''){
                            $query62.= " AND instalment_check_date>= '".$date_start."' ";
                        }
                        if($date_end !=''){
                            $query62.= " AND instalment_check_date <'".$date_end."' ";
                        }
                        $result62 = $this->B_db->run_query($query62);
                        if(!empty($result62)) {
                            foreach ($result62 as $row62) {
                                $record=array();

                                $record["count"]=$count[0]['count'];
                                $record['user_id']=$row['user_id'];
                                $record['user_name']=$row['user_name'];
                                $record['user_family']=$row['user_family'];
                                $record['user_mobile']=$row['user_mobile'];
                                $record['user_national_code']=$row['user_national_code'];

                                $record['organ_user_personal_code'] = $row['organ_user_personal_code'];
                                $record['instalment_check_id'] = $row62['instalment_check_id'];
                                $record['instalment_check_date'] = $row62['instalment_check_date'];
                                $record['fieldinsurance_fa'] = $row62['fieldinsurance_fa'];
                                $record['fieldinsurance_id'] = $row62['fieldinsurance_id'];
                                $record['company_id'] = $row62['company_id'];
                                $record['company_name'] = $row62['company_name'];
                                $record['instalment_check_amount'] = $row62['instalment_check_amount'];
                                $record['requst_ready_start_date']=$row62['requst_ready_start_date'];
                                $record['requst_ready_end_date']=$row62['requst_ready_end_date'];
                                $record['requst_ready_num_ins']=$row62['requst_ready_num_ins'];
                                $record['requst_ready_end_price']=$row62['requst_ready_end_price'];


                                $output[] = $record;

                            }
                        }


                        //*************************************************************************************

                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'لیست درخواست های بازگشت وجه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                } else {
                    echo json_encode(array('result' => $organtoken[0]
                    , "data" => $organtoken[1]
                    , 'desc' => $organtoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }


            elseif ($command == "search_person")
            {
                if($organtoken[0]=='ok')
                {
                    if($user_name != ''){
                        $this->db->like('user_name' ,$user_name,'both');
                    }
                    if($user_family != ''){
                        $this->db->like('user_family' ,$user_family,'both');
                    }
                    if($user_mobile != ''){
                        $this->db->where('user_mobile' ,$user_mobile);
                    }
                    if($user_national_code != ''){
                        $this->db->where('user_national_code' ,$user_national_code);
                    }
                    if($user_national_code != ''){
                        $this->db->where('organ_user_personal_code' ,$organ_user_personal_code);
                    }
                    if($organ_id != ''){
                        $this->db->where('organ_user_organ_id' ,$organ_id);
                    }
                    $this->db->limit($offset,$limit);
                    $this->db->from("user_tb");
                    $this->db->join('organ_user_tb', 'user_tb.user_id = organ_user_tb.organ_user_user_id  AND organ_user_id IN(
                      SELECT a.organ_user_id
FROM organ_user_tb a
INNER JOIN (
   SELECT organ_user_user_id, MAX(organ_user_confirm_id) AS max_confirm_id
FROM organ_user_tb
WHERE organ_user_organ_id=$organ_id and organ_user_confirm_id<=100000000
GROUP by organ_user_user_id
) b ON a.organ_user_confirm_id = b.max_confirm_id AND a.organ_user_user_id = b.organ_user_user_id AND a.organ_user_organ_id=$organ_id
INNER JOIN user_tb c
ON c.user_id=a.organ_user_user_id
INNER JOIN organ_confirm_tb d
ON a.organ_user_confirm_id=d.organ_confirm_id
                      ) ','right');

                    $result = $this->db->get()->result_array();
                    if (!empty($result)) {
                        echo json_encode(array('result' => "ok"
                        , "data" => $result
                        , 'desc' => ''), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    } else {
                        echo json_encode(array('result' => "error"
                        , 'desc' => 'هیچ رکوردی یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }

                }else{
                    echo json_encode(array('result'=>$organtoken[0]
                    ,"data"=>$organtoken[1]
                    ,'desc'=>$organtoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }
            else if ($command == "distinct_clearing_confirms") {
                if ($organtoken[0] == 'ok') {
                    $organ_id = $organtoken[1];
                    $result = $this->B_organ->get_clearing_organ_confirms($organ_id);
                    if (!empty($result)) {

                        echo json_encode(array('result' => "ok",
                            'data' => $result
                        , 'desc' => 'اطلاعات کانفریمهای ارگان بازیابی شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    } else {
                        echo json_encode(array('result' => "error"
                        , 'desc' => 'اطلاعات کانفریمها یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                }

            }
            else if ($command == "get_check_clearing_confirms") {
                if ($organtoken[0] == 'ok') {
                    $instalment_check_organ_temp_confirm_id=$this->post('instalment_check_organ_temp_confirm_id') ;
                    $result = $this->B_organ->get_clearing_confirm_check($instalment_check_organ_temp_confirm_id);
                    if (!empty($result)) {

                        echo json_encode(array('result' => "ok",
                            'data' => $result
                        , 'desc' => 'اطلاعات کانفریمهای ارگان بازیابی شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    } else {
                        echo json_encode(array('result' => "error"
                        , 'desc' => 'اطلاعات کانفریمها یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                }else {
                    echo json_encode(array('result' => $organtoken[0]
                    , "data" => $organtoken[1]
                    , 'desc' => $organtoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }

            }
            else  if ($command == "get_organ_therapy_user") {
                if ($organtoken[0] == 'ok') {
                    $user_id = $this->post('user_id');

                    $sql = "SELECT * FROM organ_user_therapy_tb,user_therapy_bank_tb,user_gender_tb,organ_tb,user_tb,user_therapy_kind_tb,user_therapy_kindship_tb,
organ_therapycontract_tb,user_therapy_baseinsurer_tb
WHERE organ_user_therapy_bank_id=user_therapy_bank_id
AND organ_user_therapy_gender_id=user_gender_id
AND organ_user_therapy_organ_id=organ_id
AND organ_user_therapy_main_user_id=user_id
AND organ_user_therapy_kind_id=user_therapy_kind_id
AND user_therapy_kindship_id=organ_user_therapy_kinship_id
AND organ_user_therapy_organ_therapycontract_id=organ_therapycontract_id
AND organ_user_therapy_basebime_id=user_therapy_baseinsurer_id
AND user_id=" . $user_id;
                    $result = $this->db->query($sql)->result_array();

                    if (!empty($result)) {
                        $i = 0;
                        foreach ($result as $row) {

                            $result1 = $this->B_organ->get_organ_therapycontract_conditions_contract($row['organ_therapycontract_id']);
                            if($result1==null){
                                $result[$i]['conditions'] = '';
                            }else{
                                //***************************************************************
                                $i2 = 0;
                                foreach ($result1 as $row2) {

                                    $res = $this->B_organ->get_covarage_used($row['organ_user_therapy_id'],$row2['organ_therapycontract_conditions_covarage_id']);
                                    if($res==null){
                                        $result1[$i2]['sumprice'] = 0;
                                    }else{
                                        $result1[$i2]['sumprice'] = $res['sumprice'];
                                    }


                                    $i2++;
                                }
                                //***************************************************************
                                $result[$i]['conditions'] = $result1;

                            }


                            $i++;
                        }
                        echo json_encode(array('result' => "ok"
                        , "data" => $result
                        , 'desc' => 'قراردادها یافت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    }else {
                        echo json_encode(array('result' => "ok"
                        , "data" => []
                        , 'desc' => 'قراردادی یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }


                }else {
                    echo json_encode(array('result' => $organtoken[0]
                    , "data" => $organtoken[1]
                    , 'desc' => $organtoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }

        }
    }
}