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
class Reportinstallmentpay extends REST_Controller {

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
        $this->load->model('B_organ');

        $command = $this->post("command");
        if ($command=="get_instalment_check")
        {//register marketer
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','reportinstallmentpay');
            if($employeetoken[0]=='ok')
            {
                $report_mode = $this->post("report_mode");
                $filter="";
                $start_instalment_check_amount = $this->post("start_instalment_check_amount");
                $end_instalment_check_amount = $this->post("end_instalment_check_amount");
                $request_id = $this->post("request_id");
                $request_user_id = $this->post("user_id");
                $request_fieldinsurance = $this->post("fieldinsurance_id");
                $request_last_state_id = $this->post("request_last_state_id");
                $instalment_check_date_pass = $this->post("instalment_check_date_pass");
                $start_instalment_check_date = $this->post("start_instalment_check_date");
                $end_instalment_check_date =   $this->post("end_instalment_check_date");
                $start_request_ready_timestamp = $this->post("start_request_ready_timestamp");
                $end_request_ready_timestamp   = $this->post("end_request_ready_timestamp");

                if($request_id !='') {
                    $filter .= " AND request_id='" . $request_id . "'";
                }else{$filter .=" AND 1=1 "; }

                if($report_mode==="check_notpass")
                {
                    $filter.=" AND (instalment_conditions_mode_id=1 OR instalment_conditions_mode_id=3) AND instalment_check_pass=0 ";
                }else if($report_mode==="check_passed")
                {
                    $filter.=' AND (instalment_conditions_mode_id=1 OR instalment_conditions_mode_id=3)  AND instalment_check_pass=1  ';
                }

                if($start_instalment_check_amount !='') {
                    $filter .= " AND instalment_check_amount>=" . $start_instalment_check_amount;
                }else{$filter .=" AND 1=1 "; }
                if($end_instalment_check_amount !='') {
                    $filter .= " AND instalment_check_amount<=" . $end_instalment_check_amount;
                }else{$filter .=" AND 1=1 "; }
                if($request_user_id !='') {
                    $filter .= " AND request_user_id='" . $request_user_id . "'";
                }else{$filter .=" AND 1=1 "; }
                if($request_fieldinsurance !='') {
                    $filter .= " AND request_fieldinsurance='" . $request_fieldinsurance . "'";
                }else{$filter .=" AND 1=1 "; }
                if($request_last_state_id !='') {
                    $filter .= " AND request_last_state_id='" . $request_last_state_id . "'";
                }else{$filter .=" AND 1=1 "; }
                if($instalment_check_date_pass !='') {
                    $filter .= " AND instalment_check_date_pass='" . $instalment_check_date_pass . "'";
                }else{$filter .=" AND 1=1 "; }

                if($start_instalment_check_date !='') {
                    $filter .= " AND instalment_check_date >='" . $start_instalment_check_date . "'";
                }else{$filter .=" AND 1=1 "; }
                if($end_instalment_check_date !='') {
                    $filter .= " AND instalment_check_date <='" . $end_instalment_check_date . "'";
                }else{$filter .=" AND 1=1 "; }

                if($start_request_ready_timestamp !='') {
                    $filter .= " AND requst_ready_timestamp >= '" . $start_request_ready_timestamp . "'";
                }else{$filter .=" AND 1=1 "; }
                if($end_request_ready_timestamp !='') {
                    $filter .= " AND requst_ready_timestamp <= '" . $end_request_ready_timestamp . "'";
                }else{$filter .=" AND 1=1 "; }

                $limit = $this->post("limit");
                $offset = $this->post("offset");
                $limit_state ="";
                if($limit!="" & $offset!="")
                    $limit_state = "LIMIT ".$offset.",".$limit;

                $query1="select *               from instalment_check_tb,instalment_conditions_tb,user_tb,agent_tb,request_tb,company_tb,fieldinsurance_tb,request_state,requst_ready_tb where instalment_conditions_id=instalment_check_condition_id AND  request_state_id=request_last_state_id AND user_id=request_user_id AND agent_id=request_agent_id AND fieldinsurance=request_fieldinsurance  AND  company_id=request_company_id AND instalment_check_request_id=request_id AND requst_ready_request_id=request_id ";
                $query2="select count(*) AS cnt from instalment_check_tb,instalment_conditions_tb,user_tb,agent_tb,request_tb,company_tb,fieldinsurance_tb,request_state,requst_ready_tb where instalment_conditions_id=instalment_check_condition_id AND  request_state_id=request_last_state_id AND user_id=request_user_id AND agent_id=request_agent_id AND fieldinsurance=request_fieldinsurance  AND  company_id=request_company_id AND instalment_check_request_id=request_id AND requst_ready_request_id=request_id ";

                $limit = $this->post("limit");
                $offset = $this->post("offset");
                $limit_state ="";
                if($limit!="" & $offset!="") {
                    $limit_state = " LIMIT " . $offset . "," . $limit;
                }

                //echo $filter;
                $result = $this->B_db->run_query($query1.$filter.$limit_state);
                $count  = $this->B_db->run_query($query2.$filter);

                $output =array();
                foreach($result as $row)
                {
                    $record=array();

                    $record['user_id']=$row['user_id'];
                    $record['user_name']=$row['user_name'];
                    $record['user_family']=$row['user_family'];
                    $record['user_mobile']=$row['user_mobile'];

                    $record['agent_id']=$row['agent_id'];
                    $record['agent_name']=$row['agent_name'];
                    $record['agent_family']=$row['agent_family'];
                    $record['agent_mobile']=$row['agent_mobile'];
                    $record['agent_code']=$row['agent_code'];


                    $record['instalment_check_id']=$row['instalment_check_id'];
                    $record['instalment_check_condition_id']=$row['instalment_check_condition_id'];
                    $record['instalment_check_instalment_id']=$row['instalment_check_instalment_id'];
                    $record['instalment_check_user_pey_id']=$row['instalment_check_user_pey_id'];
                    $record['instalment_check_date']=$row['instalment_check_date'];
                    $record['instalment_check_num']=$row['instalment_check_num'];
                    $record['instalment_check_amount']=$row['instalment_check_amount'];
                    $record['instalment_check_desc']=$row['instalment_check_desc'];
                    $record['instalment_check_date_pass']=$row['instalment_check_date_pass'];
                    $record['instalment_check_image_code']=$row['instalment_check_image_code'];
//                    if ($row['instalment_check_image_code'] != '') {
//                        $result1 = $this->B_db->get_image($row['instalment_check_image_code']);
//                        $image = $result1[0];
//                        if ($image['image_url']) {
//                            $imageurl =  $image['image_url'];
//                            $imageturl = $image['image_tumb_url'];
//                        }
//                        $record['instalment_check_image_url'] = $imageurl;
//                        $record['instalment_check_image_turl'] = $imageturl;
//                    }else{
//                        $record['instalment_check_image_url'] = "";
//                        $record['instalment_check_image_turl'] = "";
//                    }

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
                echo json_encode(array('result'=>"ok",
                    "filter"=>$filter
                ,"data"=>$output
                ,"cnt"=>$count[0]['cnt']
                ,'desc'=>'لیست درخواست های بازگشت وجه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }

        }else

            if ($command=="get_instalment_deficit")
            {//register marketer
                $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','reportinstallmentpay');
                if($employeetoken[0]=='ok')
                {
                    $report_mode = $this->post("report_mode");

                    $query1="select * from instalment_check_tb,instalment_conditions_tb,user_tb,agent_tb,request_tb,company_tb,fieldinsurance_tb,request_state,organ_contract_tb,organ_tb where organ_contract_organ_id=organ_id AND instalment_condition_contract_id=organ_contract_id AND instalment_conditions_id=instalment_check_condition_id AND  request_state_id=request_last_state_id AND user_id=request_user_id AND agent_id=request_agent_id AND fieldinsurance=request_fieldinsurance  AND  company_id=request_company_id AND instalment_check_request_id=request_id ";
                    $query2="select  count(*) AS cnt from instalment_check_tb,instalment_conditions_tb,user_tb,agent_tb,request_tb,company_tb,fieldinsurance_tb,request_state,organ_contract_tb,organ_tb where organ_contract_organ_id=organ_id AND instalment_condition_contract_id=organ_contract_id AND instalment_conditions_id=instalment_check_condition_id AND  request_state_id=request_last_state_id AND user_id=request_user_id AND agent_id=request_agent_id AND fieldinsurance=request_fieldinsurance  AND  company_id=request_company_id AND instalment_check_request_id=request_id ";
                    $query="";
                    if($report_mode==="deficit_notpass")
                    {
                        $query.=" AND instalment_conditions_mode_id=2 AND instalment_check_pass=0  ";
                    }   else if($report_mode==="deficit_passed")
                    {
                        $query.=' AND instalment_conditions_mode_id=2  AND instalment_check_pass=1 ';
                    }

                    $limit = $this->post("limit");
                    $offset = $this->post("offset");
                    $limit_state ="";
                    if($limit!="" & $offset!="") {
                        $limit_state = " LIMIT " . $offset . "," . $limit;
                    }
                    //echo $query1.$query.$limit_state;
                    $result = $this->B_db->run_query($query1.$query.$limit_state);
                    $count  = $this->B_db->run_query($query2.$query);

                    $output =array();
                    foreach($result as $row)
                    {
                        $record=array();
                        $record['user_id']=$row['user_id'];
                        $record['user_name']=$row['user_name'];
                        $record['user_family']=$row['user_family'];
                        $record['user_mobile']=$row['user_mobile'];

                        $record['organ_id']=$row['organ_id'];
                        $record['organ_name']=$row['organ_name'];
                        $record['organ_contract_id']=$row['organ_contract_id'];
                        $record['organ_contract_num']=$row['organ_contract_num'];

                        $result2 = $this->B_organ->get_one_user_organ($row['organ_id'], $row['user_id']);
                        $organ_user=$result2[0];
                        $record['organ_user_personal_code']=$organ_user['organ_user_personal_code'];



                        $record['instalment_check_doc']=$row['instalment_check_doc'];

                        $record['agent_id']=$row['agent_id'];
                        $record['agent_name']=$row['agent_name'];
                        $record['agent_family']=$row['agent_family'];
                        $record['agent_mobile']=$row['agent_mobile'];
                        $record['agent_code']=$row['agent_code'];


                        $record['instalment_check_id']=$row['instalment_check_id'];
                        $record['instalment_check_condition_id']=$row['instalment_check_condition_id'];
                        $record['instalment_check_instalment_id']=$row['instalment_check_instalment_id'];
                        $record['instalment_check_user_pey_id']=$row['instalment_check_user_pey_id'];
                        $record['instalment_check_date']=$row['instalment_check_date'];
                        $record['instalment_check_num']=$row['instalment_check_num'];
                        $record['instalment_check_amount']=$row['instalment_check_amount'];
                        $record['instalment_check_desc']=$row['instalment_check_desc'];
                        $record['instalment_check_date_pass']=$row['instalment_check_date_pass'];
                        $record['instalment_check_image_code']=$row['instalment_check_image_code'];
                        if ($row['instalment_check_image_code'] != '') {
                            $result1 = $this->B_db->get_image($row['instalment_check_image_code']);
                            $image = $result1[0];
                            if ($image['image_url']) {
                                $imageurl =  $image['image_url'];
                                $imageturl =  $image['image_tumb_url'];
                            }
                            $record['instalment_check_image_url'] = $imageurl;
                            $record['instalment_check_image_turl'] = $imageturl;
                        }else{
                            $record['instalment_check_image_url'] = "";
                            $record['instalment_check_image_turl'] = "";
                        }

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
                    ,"cnt"=>$count[0]['cnt']
                    ,'desc'=>'لیست درخواست های بازگشت وجه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                }else{
                    echo json_encode(array('result'=>$employeetoken[0]
                    ,"data"=>$employeetoken[1]
                    ,'desc'=>$employeetoken[2]));

                }

            }else
                if ($command=="instalment_check_pass")
                {//register marketer
                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','reportinstallmentpay');
                    if($employeetoken[0]=='ok')
                    {
                        $instalment_check_id=$this->post('instalment_check_id');
                        $instalment_check_date_pass=$this->post('instalment_check_date_pass');
                        $instalment_check_doc=$this->post('instalment_check_doc');
                        $request_id=$this->post('request_id');
                        $instalment_check_amount=$this->post('instalment_check_amount');

                        $query17="select * from instalment_check_tb where instalment_check_id=".$instalment_check_id." AND instalment_check_pass=1  ";
                        $result17=$this->B_db->run_query($query17);
                        if (empty($result17)) {

                            $query = "UPDATE instalment_check_tb SET instalment_check_doc='$instalment_check_doc',instalment_check_pass=1,instalment_check_date_pass='$instalment_check_date_pass',instalment_check_employee_id=" . $employeetoken[1] . "  WHERE instalment_check_id=$instalment_check_id";
                            $result = $this->B_db->run_query_put($query);
                            $instalment_check_amount = ($instalment_check_amount) * 100 / 109;

                            $this->B_db->peyback_decision($request_id, $instalment_check_amount, 'add', 'پرداخت تعهدی', 'nomain');


                            echo json_encode(array('result' => "ok"
                            , "data" => ""
                            , 'desc' => 'پرداخت چک وجه ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }else{

                            echo json_encode(array('result' => "ok"
                            , "data" => ""
                            , 'desc' => 'پرداخت چک قبلا ثبت شده است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]));

                    }

                }    else
                    if ($command=="instalment_deficit_pass")
                    {//register marketer
                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','reportinstallmentpay');
                        if($employeetoken[0]=='ok')
                        {
                            $instalment_check_id=$this->post('instalment_check_id');
                            $instalment_check_date_pass=$this->post('instalment_check_date_pass');
                            $instalment_check_doc=$this->post('instalment_check_doc');

                            $query17="select * from instalment_check_tb where instalment_check_id=".$instalment_check_id." AND instalment_check_pass=1  ";
                            $result17=$this->B_db->run_query($query17);
                            if (empty($result17)) {


                                $query="UPDATE instalment_check_tb SET instalment_check_doc='$instalment_check_doc',instalment_check_pass=1,instalment_check_date_pass='$instalment_check_date_pass',instalment_check_employee_id=".$employeetoken[1]."  WHERE instalment_check_id=$instalment_check_id";
                                $result = $this->B_db->run_query_put($query);



                                echo json_encode(array('result'=>"ok"
                                ,"data"=>$query
                                ,'desc'=>'پرداخت درخواست بازگشت وجه ثبت شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }else{

                                echo json_encode(array('result' => "ok"
                                , "data" => ""
                                , 'desc' => 'پرداخت چک قبلا ثبت شده است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }

                        }else{
                            echo json_encode(array('result'=>$employeetoken[0]
                            ,"data"=>$employeetoken[1]
                            ,'desc'=>$employeetoken[2]));

                        }

                    }    else

                        if ($command == "get_user_organ_installment")
                        {
                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','reportinstallmentpay');
                            $organ_id=$this->post('organ_id');
                            $limit = $this->post("limit");
                            $offset = $this->post("offset");
                            $date_end=$this->post('date_end') ;
                            $date_start=$this->post('date_start') ;
                            if($employeetoken[0]=='ok')
                            {
                                $filter = "";
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
                                ,"cnt"=>$count[0]['cnt']
                                ,"data"=>$output
                                ,'desc'=>'لیست درخواست های بازگشت وجه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                            }  else{
                                echo json_encode(array('result'=>$employeetoken[0]
                                ,"data"=>$employeetoken[1]
                                ,'desc'=>$employeetoken[2]));

                            }
                        }
                        else if ($command == "get_check_clearing_confirms") {
                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','reportinstallmentpay');
                            if($employeetoken[0]=='ok')
                            {                    $instalment_check_organ_temp_confirm_id=$this->post('instalment_check_organ_temp_confirm_id') ;
                                $result = $this->B_organ->get_clearing_confirm_check($instalment_check_organ_temp_confirm_id);
                                if (!empty($result)) {

                                    echo json_encode(array('result' => "ok",
                                        'data' => $result
                                    , 'desc' => 'اطلاعات کانفریمهای ارگان بازیابی شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                } else {
                                    echo json_encode(array('result' => "error"
                                    , 'desc' => 'اطلاعات کانفریمها یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                }
                            } else{
                                echo json_encode(array('result'=>$employeetoken[0]
                                ,"data"=>$employeetoken[1]
                                ,'desc'=>$employeetoken[2]));

                            }

                        }
                        else if ($command == "create_confirms_organ") {
                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','reportinstallmentpay');
                            if($employeetoken[0]=='ok')
                            {
                                $sms_confirm_date_start=$this->post('sms_confirm_date_start') ;
                                $sms_confirm_date_end=$this->post('sms_confirm_date_end') ;
                                $token = generateToken(20);


                                $query1 = "INSERT INTO organ_confirm_tb
( organ_confitm_sms_send_date, organ_confirm_sms_code, organ_confirm_sms_recive_date, organ_confirm_token, sms_confirm_date_start, sms_confirm_date_end)
VALUES( now()                , $employeetoken[1]     , now()                        , '$token'             , '$sms_confirm_date_start', '$sms_confirm_date_end'); ";
                                $result1 = $this->B_db->run_query_put($query1);
                                $organ_confirm_id = $this->db->insert_id();

                                echo json_encode(array('result' => "ok",
                                    'data' => array("organ_confirm_id" => $organ_confirm_id, 'token' => $token)
                                , 'desc' => 'اصالت مدیر تایید شد'.$query1), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                            } else{
                                echo json_encode(array('result'=>$employeetoken[0]
                                ,"data"=>$employeetoken[1]
                                ,'desc'=>$employeetoken[2]));

                            }

                        }
                        else   if ($command == "add_organ_instalment_temp")
                        {
                            $instalment_check_organ_temp_check_id = $this->post('instalment_check_organ_temp_check_id');
                            $instalment_check_organ_temp_confirm_id= $this->post('instalment_check_organ_temp_confirm_id');

                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','reportinstallmentpay');
                            if($employeetoken[0]=='ok')
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
                            } else{
                                echo json_encode(array('result'=>$employeetoken[0]
                                ,"data"=>$employeetoken[1]
                                ,'desc'=>$employeetoken[2]));

                            }
                        }



    }
}