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
class Refunduser extends REST_Controller {

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
        if ($command=="get_refund")
        {//register marketer
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','refunduser');
            $approvalmode=$this->post('approvalmode');
            if($employeetoken[0]=='ok')
            {

                $query1="select * from refund_user_tb,user_tb,useracbank_tb where user_id=refund_user_user_id AND useracbank_id=refund_user_useracbank_id ";
                $query2="select  count(*) AS cnt  from refund_user_tb,user_tb,useracbank_tb where user_id=refund_user_user_id AND useracbank_id=refund_user_useracbank_id ";

                $query="";
                if($_REQUEST['mode']){
                    $mode=$this->post('mode');
                    if($mode=='notpeyed'){
                        $query.="AND refund_user_pey=0  "	 ;
                    }else if($mode=='payed'){
                        $query.="AND refund_user_pey=1  "	 ;
                    }
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

                    $record['refund_user_id']=$row['refund_user_id'];
                    $record['refund_user_user_id']=$row['refund_user_user_id'];
                    $record['user_name']=$row['user_name'];
                    $record['user_family']=$row['user_family'];
                    $record['user_mobile']=$row['user_mobile'];
                    $record['refund_user_amount']=$row['refund_user_amount'];
                    $record['refund_user_useracbank_id']=$row['refund_user_useracbank_id'];
                    $record['useracbank_sheba']=$row['useracbank_sheba'];
                    $record['useracbank_bankname']=$row['useracbank_bankname'];
                    $record['useracbank_numcard']=$row['useracbank_numcard'];
                    $record['refund_user_code']=$row['refund_user_code'];
                    $record['refund_user_desc']=$row['refund_user_desc'];
                    $record['refund_user_date']=$row['refund_user_date'];
                    $record['refund_user_datepeyed']=$row['refund_user_datepeyed'];
//*************************************************************************************
                    $record['refund_user_emloyee_id']=$row['refund_user_emloyee_id'];
                    $query1="select * from employee_tb where employee_id=".$row['refund_user_emloyee_id']."";
                    $result1=$this->B_db->run_query($query1);
                    $employee=$result1[0];
                    $record['employee_name']=$employee['employee_name'];
                    $record['employee_family']=$employee['employee_family'];
                    $record['employee_mobile']=$employee['employee_mobile'];
                    //*************************************************************************************
                    $query1="select * from usermarketer_tb,marketer_mode_tb where marketer_mode_tb.marketer_mode_id=usermarketer_tb.marketer_mode_id AND marketer_user_id=".$row['refund_user_user_id']."";
                    $result1=$this->B_db->run_query($query1);
                    $employee=$result1[0];
                    $num=count($result1[0]);
                    if ($num==0)
                    {
                        $record['marketer_mode_id']=0;
                        $record['marketer_mode_namefa']='کاربر عادی';
                    }else{
                        $record['marketer_mode_id']=$employee['marketer_mode_id'];
                        $record['marketer_mode_namefa']=$employee['marketer_mode_namefa'];
                    }

                    $record['marketer_mode_name']=$employee['marketer_mode_name'];
                    $record['marketer_leader_mobile']=$employee['marketer_leader_mobile'];
                    $record['marketer_deactive']=$employee['marketer_deactive'];
                    //*************************************************************************************

                    $output[]=$record;
                }
                echo json_encode(array('result'=>"ok"
                ,"cnt"=>$count[0]['cnt']
                ,"data"=>$output
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
                $refund_user_code=$this->post('refund_user_code',0);
                $query3="select  * from refund_user_tb where  refund_user_pey=0 AND refund_user_id =".$refund_user_id;
                $result3 = $this->B_db->run_query($query3);
                $num=count($result3[0]);
                if ($num!=0) {


                $query="UPDATE refund_user_tb SET refund_user_datepeyed=now(),refund_user_code='$refund_user_code',refund_user_emloyee_id=".$employeetoken[1].",refund_user_pey=1  WHERE refund_user_id=$refund_user_id";
                $result = $this->B_db->run_query_put($query);

                $query1="select * from refund_user_tb where refund_user_id= $refund_user_id";
                $result1=$this->B_db->run_query($query1);
                $refund=$result1[0];



                $user_wallet_detail= 'پرداخت وجه با کد پیگیری'.  $refund_user_code .' به حساب شما انجام شد ';
                $user_wallet_detail2= 'باسلام. درخواست پرداخت وجه با کد پیگیری'.  $refund_user_code .' به حساب شما در عارف۲۴ انجام شد ';
                    $query2="INSERT INTO user_wallet_tb( user_wallet_user_id, user_wallet_amount, user_wallet_mode, user_wallet_timestamp, user_wallet_detail, user_wallet_code) VALUES
                                   (".$refund['refund_user_user_id'].",'".$refund['refund_user_amount']."' , 'get'      ,now()               ,'$user_wallet_detail',$refund_user_code)      ";

                $result2=$this->B_db->run_query_put($query2);

                send_refund_sms($refund['refund_user_user_id'], $user_wallet_detail2);

                echo json_encode(array('result'=>"ok"
                ,"data"=>''
                ,'desc'=>'پرداخت درخواست بازگشت وجه ثبت شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
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

        else if ($command=="delete_doc")
        {//register marketer
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','refunduser');
            if($employeetoken[0]=='ok')
            {
                $refund_user_id=$this->post('refund_user_id');
                $query3="select  * from refund_user_tb where  refund_user_pey=0 AND refund_user_id =".$refund_user_id;
                $result3 = $this->B_db->run_query($query3);
                $refund=$result3[0];
                $num=count($result3[0]);
                if ($num!=0) {



                    $query1="delete from refund_user_tb where refund_user_id= $refund_user_id";
                    $result1 = $this->B_db->run_query_put($query1);


                    send_refund_sms($refund['refund_user_user_id'], "با سلام درخواست بازگشت وجه شما در عارف۲۴ به علت مشکل شماره شبا حذف شد . لطفا شماره شبا خود را اصلاح و مجددا درخواست را ارسال نمایید");

                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$refund['refund_user_user_id']
                    ,'desc'=>' درخواست بازگشت وجه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    echo json_encode(array('result' => "ok"
                    , "data" => ''
                    , 'desc' => 'درخواست بازگشت وجه وجود ندارد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }

        }

    }
}