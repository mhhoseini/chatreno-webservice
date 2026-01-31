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
class Reportuserpay extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500;   // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100;  // 100 requests per hour per user/key
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
        if ($command=="get_userokpay")
        {//register marketer
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','reportuserpay');
            if($employeetoken[0]=='ok')
            {
                $report_mode = $this->post("report_mode");

                $query1="select * from pey_tb,user_tb,request_tb,company_tb,fieldinsurance_tb,request_state where  request_state_id=request_last_state_id AND user_id=request_user_id AND fieldinsurance=request_fieldinsurance  AND  company_id=request_company_id AND pey_request_id=request_id ";
                $query2="select  count(*) AS cnt from pey_tb,user_tb,request_tb,company_tb,fieldinsurance_tb,request_state where  request_state_id=request_last_state_id AND user_id=request_user_id AND fieldinsurance=request_fieldinsurance  AND  company_id=request_company_id AND pey_request_id=request_id ";
                $query="";
if($report_mode==="only_ok")
{
    $query.=' AND pey_backcode=0  ';
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
                foreach($result as $row)
                {
                    $record=array();
                    $record['user_id']=$row['user_id'];
                    $record['user_name']=$row['user_name'];
                    $record['user_family']=$row['user_family'];
                    $record['user_mobile']=$row['user_mobile'];


                    $record['pey_id']=$row['pey_id'];
                    $record['pey_request_id']=$row['pey_request_id'];
                    $record['pey_refid']=$row['pey_refid'];
                    $record['pey_date']=$row['pey_date'];
                    $record['pey_backdate']=$row['pey_backdate'];
                    $record['pey_backcode']=$row['pey_backcode'];
                    $record['pey_amount']=$row['pey_amount'];
                    $record['pey_refrenceid']=$row['pey_refrenceid'];
                    $record['pey_mode']=$row['pey_mode'];
                    $record['pey_deficit_pey_id']=$row['pey_deficit_pey_id'];

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




        }

        else if ($command=="pey_doc")
        {//register marketer
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','refunduser');
            if($employeetoken[0]=='ok')
            {
                $refund_user_id=$this->post('refund_user_id');
                $refund_user_code=$this->post('refund_user_code');

                $query3="select  * from refund_user_tb where  refund_user_pey=0 AND refund_user_id =".$refund_user_id;
                $result3 = $this->B_db->run_query($query3);
                $num=count($result3[0]);
                if ($num!=0) {
                    $query = "UPDATE refund_user_tb SET refund_user_datepeyed=now(),refund_user_code='$refund_user_code',refund_user_emloyee_id=" . $employeetoken[1] . ",refund_user_pey=1  WHERE refund_user_id=$refund_user_id";
                    $result = $this->B_db->run_query_put($query);

                    $query1 = "select * from refund_user_tb where refund_user_id= $refund_user_id";
                    $result1 = $this->B_db->run_query($query1);
                    $refund = $result1[0];


                    $user_wallet_detail = 'پرداخت وجه با کد پیگیری' . $refund_user_code . ' به حساب شما انجام شد ';
                    $query2 = "INSERT INTO user_wallet_tb( user_wallet_user_id, user_wallet_amount, user_wallet_mode, user_wallet_timestamp, user_wallet_detail, user_wallet_code) VALUES
                                   (" . $refund['refund_user_user_id'] . ",'" . $refund['refund_user_amount'] . "' , 'get'      ,now()               ,'$user_wallet_detail',$refund_user_code)      ";

                    $result2 = $this->B_db->run_query_put($query2);


                    echo json_encode(array('result' => "ok"
                    , "data" => ""
                    , 'desc' => 'پرداخت درخواست بازگشت وجه ثبت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }else{
                    echo json_encode(array('result' => "ok"
                    , "data" => ''
                    , 'desc' => 'پرداخت درخواست بازگشت وجه  قبلا ثبت شده است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }

        }

        }
}