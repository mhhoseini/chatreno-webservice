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
class Thirdparty extends REST_Controller {

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

    public function index_post(){
        if(isset($this->input->request_headers()['Authorization']))$employee_token_str = $this->input->request_headers()['Authorization'];
        $this->load->model('B_user');
        $this->load->model('B_thirdparty');
        $this->load->model('B_db');
        $this->load->helper('my_helper');
        $this->load->helper('time_helper');
        $command = $this->post("command");
        if ($command=="get_plak")
        {

            $plk1=$this->post('plk1') ;
            $plk2=$this->post('plk2') ;
            $plk3=$this->post('plk3') ;
            $plkSrl=$this->post('plkSrl') ;

            $token="eyJ4NXQiOiJNell4TW1Ga09HWXdNV0kwWldObU5EY3hOR1l3WW1NNFpUQTNNV0kyTkRBelpHUXpOR00wWkdSbE5qSmtPREZrWkRSaU9URmtNV0ZoTXpVMlpHVmxOZyIsImtpZCI6Ik16WXhNbUZrT0dZd01XSTBaV05tTkRjeE5HWXdZbU00WlRBM01XSTJOREF6WkdRek5HTTBaR1JsTmpKa09ERmtaRFJpT1RGa01XRmhNelUyWkdWbE5nX1JTMjU2IiwiYWxnIjoiUlMyNTYifQ.eyJzdWIiOiJsdXR1c0BjYXJib24uc3VwZXIiLCJhdWQiOiJwRmo0RWtYMEJsRHQ1X01MWkpEWVRQX3dVS2dhIiwibmJmIjoxNjQyNzE5NjYwLCJhenAiOiJwRmo0RWtYMEJsRHQ1X01MWkpEWVRQX3dVS2dhIiwic2NvcGUiOiJhbV9hcHBsaWNhdGlvbl9zY29wZSBkZWZhdWx0IiwiaXNzIjoiaHR0cHM6XC9cL2lkZW50aXR5LmlpeC5jZW50aW5zdXIub3JnOjk0NDJcL29hdXRoMlwvdG9rZW4iLCJleHAiOjE2NDI3MjMyNjAsImlhdCI6MTY0MjcxOTY2MCwianRpIjoiNDYwNDNjM2ItYjVhZS00Yzg3LWE5MjMtMTY1OGU2MDRjZjUyIn0.rYtTsWx_S009bf2Ji9bstG44iBCVAhN8f0nfanFPcVlZL4J9ma7buAEr8_r77opQ4lrM_yqVqiPbvdQShAd-FKuGB6XuqG8ATMpsP-d5kjJyXanNcFXJvGPOUqfp5xlPG0n_SPUJk_G0P2HKR_SUI8PxexLeJdQ5ZlZc6LuptkbTIfRWaVrM2jrur3tnQybWO-kOvaMJnF2KVulCer07wH-v4pJQT3lrtaj51Fb4HalucbwPFGRptvsWzPsKxPu7iYv1qilBTMhU92Tmx3i6Y7hQUbVVeNxKn3pwooaxN1qRAyJWuvXwx2F-ocgfcJZ2T44pwhptTz5ALj83GRyICA";
            $url = 'http://91.92.127.79:1439/api/aref242.php?plkSrl='.$plkSrl.'&plk1='.$plk1.'&plk2='.$plk2.'&plk3='.$plk3;
            // create & initialize a curl session
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL,$url);
            curl_setopt($curl, CURLOPT_POST, 0);
            curl_setopt($curl, CURLOPT_HTTPGET, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $headers = array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $output = curl_exec($curl);
            if($output === false){
                print_r('Curl error: ' . curl_error($curl));
            }
            echo $output;
            curl_close($curl);

        }else
            if ($command=="get_yearofcons")
            {
                $output =array();
                $def=date("Y")-jdate('Y','',"",'','en');
                $ynow=date("Y");
                for($i=0;$i<30;$i++)
                {
                    $record=array();
                    $record['thirdparty_yearofcons_id']=$i;
                    $record['thirdparty_yearofcons_name']=($ynow-$i).'-'.($ynow-$i-$def);
                    $output[]=$record;
                }
                $record=array();
                $record['thirdparty_yearofcons_id']=$i;
                $record['thirdparty_yearofcons_name']=($ynow-$i).'-'.($ynow-$i-$def).' و ماقبل';
                $output[]=$record;
                echo json_encode(array('result'=>"ok"
                ,"data"=>$output
                ,'desc'=>' سال ساخت با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }
            else if($command=="add_discnt_thirdparty")
            {
                $thirdparty_discnt_thirdparty_id=$this->post('thirdparty_discnt_thirdparty_id') ;
                $thirdparty_discnt_thirdparty_name=$this->post('thirdparty_discnt_thirdparty_name') ;
                $thirdparty_discnt_thirdparty_digt=$this->post('thirdparty_discnt_thirdparty_digt') ;

                $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','thirdparty');
                if($employeetoken[0]=='ok')
                {
                    $result = $this->B_thirdparty->get_discount_by_thirdparty($thirdparty_discnt_thirdparty_name);
                    if (empty($result))
                    {
                        $thirdparty_discnt_thirdparty_id = $this->B_thirdparty->add_discount($thirdparty_discnt_thirdparty_id,$thirdparty_discnt_thirdparty_name, $thirdparty_discnt_thirdparty_digt);
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>array('thirdparty_discnt_thirdparty_id'=>$thirdparty_discnt_thirdparty_id)
                        ,'desc'=>'تخفیف شخص ثالث اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        $carmode=$result[0];
                        echo json_encode(array('result'=>"error"
                        ,"data"=>array('thirdparty_discnt_thirdparty_id'=>$carmode['thirdparty_discnt_thirdparty_id'])
                        ,'desc'=>'تخفیف شخص ثالث تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                }else{
                    echo json_encode(array('result'=>$employeetoken[0]
                    ,"data"=>$employeetoken[1]
                    ,'desc'=>$employeetoken[2]));
                }
            }

        if ($command=="get_discnt_thirdparty")
        {

            $result = $this->B_thirdparty->get_discount();
            $output =array();

            foreach($result as $row)
            {
                $record=array();
                $record['thirdparty_discnt_thirdparty_id']=$row['thirdparty_discnt_thirdparty_id'];
                $record['thirdparty_discnt_thirdparty_name']=$row['thirdparty_discnt_thirdparty_name'];
                $record['thirdparty_discnt_thirdparty_digt']=$row['thirdparty_discnt_thirdparty_digt'];
                $output[]=$record;
            }
            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'تخفیف شخص ثالث با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
        else
            if ($command=="delete_discnt_thirdparty")
            {
                $thirdparty_discnt_thirdparty_id=$this->post('thirdparty_discnt_thirdparty_id') ;

                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','thirdparty');
                if($employeetoken[0]=='ok')
                {
                    $user_id=$employeetoken[1];
                    $result = $this->B_thirdparty->del_discount($thirdparty_discnt_thirdparty_id);
                    $output =array();
                    if($result){echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'تخفیف شخص ثالث حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        echo json_encode(array('result'=>"error"
                        ,"data"=>$output
                        ,'desc'=>'تخفیف شخص ثالث حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                }else{
                    echo json_encode(array('result'=>$employeetoken[0]
                    ,"data"=>$employeetoken[1]
                    ,'desc'=>$employeetoken[2]));
                }
            }
            else
                if ($command=="modify_discnt_thirdparty")
                {
                    $thirdparty_discnt_thirdparty_id=$this->post('thirdparty_discnt_thirdparty_id') ;

                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','thirdparty');
                    if($employeetoken[0]=='ok')
                    {
                        $query="UPDATE thirdparty_discnt_thirdparty_tb SET ";
                        if(isset($_REQUEST['thirdparty_discnt_thirdparty_name'])){
                            $thirdparty_discnt_thirdparty_name=$this->post('thirdparty_discnt_thirdparty_name') ;
                            $query.="thirdparty_discnt_thirdparty_name='".$thirdparty_discnt_thirdparty_name."'";}

                        if(isset($_REQUEST['thirdparty_discnt_thirdparty_digt'])&&(isset($_REQUEST['thirdparty_discnt_thirdparty_name']))){ $query.=",";}
                        if(isset($_REQUEST['thirdparty_discnt_thirdparty_digt'])){
                            $thirdparty_discnt_thirdparty_digt=$this->post('thirdparty_discnt_thirdparty_digt') ;
                            $query.="thirdparty_discnt_thirdparty_digt='".$thirdparty_discnt_thirdparty_digt."'";}

                        $query.="where thirdparty_discnt_thirdparty_id=".$thirdparty_discnt_thirdparty_id;
                        $result = $this->B_db->run_query_put($query);
                        if($result){
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>""
                            ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }

                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]));

                    }
                }
                else
                    if ($command=="add_discnt_driver")
                    {
                        $thirdparty_discnt_driver_id=$this->post('thirdparty_discnt_driver_id') ;
                        $thirdparty_discnt_driver_name=$this->post('thirdparty_discnt_driver_name') ;
                        $thirdparty_discnt_driver_digt=$this->post('thirdparty_discnt_driver_digt') ;

                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','thirdparty');
                        if($employeetoken[0]=='ok')
                        {
                            $result = $this->B_thirdparty->get_discnt_driver($thirdparty_discnt_driver_name);
                            if (empty($result))
                            {
                                $thirdparty_discnt_driver_id = $this->B_thirdparty->add_discnt_driver($thirdparty_discnt_driver_id,$thirdparty_discnt_driver_name, $thirdparty_discnt_driver_digt);
                                echo json_encode(array('result'=>"ok"
                                ,"data"=>array('thirdparty_discnt_driver_id'=>$thirdparty_discnt_driver_id)
                                ,'desc'=>'تخفیف راننده شخص ثالث اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }else{
                                $carmode=$result[0];
                                echo json_encode(array('result'=>"error"
                                ,"data"=>array('thirdparty_discnt_driver_id'=>$carmode['thirdparty_discnt_driver_id'])
                                ,'desc'=>'تخفیف راننده شخص ثالث تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
                        }else{
                            echo json_encode(array('result'=>$employeetoken[0]
                            ,"data"=>$employeetoken[1]
                            ,'desc'=>$employeetoken[2]));

                        }
                    }
                    else
                        if ($command=="get_discnt_driver")
                        {
                            $result = $this->B_thirdparty->all_discnt_driver();
                            $output =array();
                            foreach($result as $row)
                            {
                                $record=array();
                                $record['thirdparty_discnt_driver_id']=$row['thirdparty_discnt_driver_id'];
                                $record['thirdparty_discnt_driver_name']=$row['thirdparty_discnt_driver_name'];
                                $record['thirdparty_discnt_driver_digt']=$row['thirdparty_discnt_driver_digt'];
                                $output[]=$record;
                            }
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>$output
                            ,'desc'=>'تخفیف راننده شخص ثالث با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                        else
                            if ($command=="delete_discnt_driver")
                            {
                                $output = array();
                                $thirdparty_discnt_driver_id=$this->post('thirdparty_discnt_driver_id') ;

                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','thirdparty');
                                if($employeetoken[0]=='ok')
                                {
                                    $user_id=$employeetoken[1];
                                    $result = $this->B_thirdparty->del_discnt_driver($thirdparty_discnt_driver_id);
                                    if($result){echo json_encode(array('result'=>"ok"
                                    ,"data"=>$output
                                    ,'desc'=>'تخفیف راننده شخص ثالث حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }else{
                                        echo json_encode(array('result'=>"error"
                                        ,"data"=>$output
                                        ,'desc'=>'تخفیف راننده شخص ثالث حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }
                                }else{
                                    echo json_encode(array('result'=>$employeetoken[0]
                                    ,"data"=>$employeetoken[1]
                                    ,'desc'=>$employeetoken[2]));
                                }
                            }
                            else
                                if ($command=="modify_discnt_driver")
                                {
                                    $thirdparty_discnt_driver_id=$this->post('thirdparty_discnt_driver_id') ;

                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','thirdparty');
                                    if($employeetoken[0]=='ok')
                                    {

                                        $query="UPDATE thirdparty_discnt_driver_tb SET ";

                                        if(isset($_REQUEST['thirdparty_discnt_driver_name'])){
                                            $thirdparty_discnt_driver_name=$this->post('thirdparty_discnt_driver_name');
                                            $query.="thirdparty_discnt_driver_name='".$thirdparty_discnt_driver_name."'";}

                                        if(isset($_REQUEST['thirdparty_discnt_driver_digt'])&&(isset($_REQUEST['thirdparty_discnt_driver_name']))){ $query.=",";}
                                        if(isset($_REQUEST['thirdparty_discnt_driver_digt'])){
                                            $thirdparty_discnt_driver_digt=$this->post('thirdparty_discnt_driver_digt');
                                            $query.="thirdparty_discnt_driver_digt='".$thirdparty_discnt_driver_digt."'";}

                                        $query.="where thirdparty_discnt_driver_id=".$thirdparty_discnt_driver_id;
                                        $result = $this->B_db->run_query_put($query);
                                        if($result){
                                            echo json_encode(array('result'=>"ok"
                                            ,"data"=>""
                                            ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                        }

                                    }else{
                                        echo json_encode(array('result'=>$employeetoken[0]
                                        ,"data"=>$employeetoken[1]
                                        ,'desc'=>$employeetoken[2]));

                                    }
                                }
                                else
                                    if ($command=="add_damage_financial")
                                    {

                                        $thirdparty_damage_financial_id=$this->post('thirdparty_damage_financial_id') ;
                                        $thirdparty_damage_financial_name=$this->post('thirdparty_damage_financial_name') ;
                                        $thirdparty_damage_financial_digit=$this->post('thirdparty_damage_financial_digit') ;



                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','thirdparty');
                                        if($employeetoken[0]=='ok')
                                        {
//****************************************************************************************************************
                                            $query="select * from thirdparty_damage_financial_tb where thirdparty_damage_financial_name='".$thirdparty_damage_financial_name."'";
                                            $result=$this->B_db->run_query($query);
                                            $num=count($result[0]);
                                            if ($num==0)
                                            {
                                                $query="INSERT INTO thirdparty_damage_financial_tb(thirdparty_damage_financial_id, thirdparty_damage_financial_name, thirdparty_damage_financial_digit)
                VALUES ( $thirdparty_damage_financial_id,'$thirdparty_damage_financial_name', '$thirdparty_damage_financial_digit');";

                                                $result=$this->B_db->run_query_put($query);
                                                $thirdparty_damage_financial_id=$this->db->insert_id();

                                                echo json_encode(array('result'=>"ok"
                                                ,"data"=>array('thirdparty_damage_financial_id'=>$thirdparty_damage_financial_id)
                                                ,'desc'=>'خسارت بدنی شخص ثالث اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                            }else{
                                                $carmode=$result[0];
                                                echo json_encode(array('result'=>"error"
                                                ,"data"=>array('thirdparty_damage_financial_id'=>$carmode['thirdparty_damage_financial_id'])
                                                ,'desc'=>'خسارت بدنی شخص ثالث تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                            }
//***************************************************************************************************************
                                        }else{
                                            echo json_encode(array('result'=>$employeetoken[0]
                                            ,"data"=>$employeetoken[1]
                                            ,'desc'=>$employeetoken[2]));

                                        }
                                    }
                                    else
                                        if ($command=="get_damage_financial")
                                        {
//************************************************************************;****************************************

                                            $query="select * from thirdparty_damage_financial_tb where 1 ORDER BY thirdparty_damage_financial_id ASC";
                                            $result = $this->B_db->run_query($query);
                                            $output =array();
                                            foreach($result as $row)
                                            {
                                                $record=array();
                                                $record['thirdparty_damage_financial_id']=$row['thirdparty_damage_financial_id'];
                                                $record['thirdparty_damage_financial_name']=$row['thirdparty_damage_financial_name'];
                                                $record['thirdparty_damage_financial_digit']=$row['thirdparty_damage_financial_digit'];
                                                $output[]=$record;
                                            }
                                            echo json_encode(array('result'=>"ok"
                                            ,"data"=>$output
                                            ,'desc'=>'خسارت بدنی شخص ثالث با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************
                                        }
                                        else
                                            if ($command=="delete_damage_financial")
                                            {
                                                $thirdparty_damage_financial_id=$this->post('thirdparty_damage_financial_id') ;

                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','thirdparty');
                                                if($employeetoken[0]=='ok')
                                                {
//************************************************************************;****************************************
                                                    $user_id=$employeetoken[1];
                                                    $query="DELETE FROM thirdparty_damage_financial_tb  where thirdparty_damage_financial_id=".$thirdparty_damage_financial_id."";
                                                    $result = $this->B_db->run_query_put($query);
                                                    if($result){echo json_encode(array('result'=>"ok"
                                                    ,"data"=>$output
                                                    ,'desc'=>'خسارت بدنی شخص ثالث حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                    }else{
                                                        echo json_encode(array('result'=>"error"
                                                        ,"data"=>$output
                                                        ,'desc'=>'خسارت بدنی شخص ثالث حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                    }
//***************************************************************************************************************
                                                }else{
                                                    echo json_encode(array('result'=>$employeetoken[0]
                                                    ,"data"=>$employeetoken[1]
                                                    ,'desc'=>$employeetoken[2]));
                                                }
                                            }
                                            else
                                                if ($command=="modify_damage_financial")
                                                {
                                                    $thirdparty_damage_financial_id=$this->post('thirdparty_damage_financial_id') ;
                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','thirdparty');
                                                    if($employeetoken[0]=='ok')
                                                    {
//*****************************************************************************************

                                                        $query="UPDATE thirdparty_damage_financial_tb SET ";

                                                        if(isset($_REQUEST['thirdparty_damage_financial_name'])){
                                                            $thirdparty_damage_financial_name=$this->post('thirdparty_damage_financial_name');
                                                            $query.="thirdparty_damage_financial_name='".$_REQUEST['thirdparty_damage_financial_name']."'";}

                                                        if(isset($_REQUEST['thirdparty_damage_financial_digit'])&&(isset($_REQUEST['thirdparty_damage_financial_name']))){ $query.=",";}
                                                        if(isset($_REQUEST['thirdparty_damage_financial_digit'])){
                                                            $thirdparty_damage_financial_digit=$this->post('thirdparty_damage_financial_digit');
                                                            $query.="thirdparty_damage_financial_digit='".$_REQUEST['thirdparty_damage_financial_digit']."'";}

                                                        $query.="where thirdparty_damage_financial_id=".$thirdparty_damage_financial_id;

                                                        $result=$this->B_db->run_query_put($query);
                                                        if($result){
                                                            echo json_encode(array('result'=>"ok"
                                                            ,"data"=>""
                                                            ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                        }
//***************************************************************************************************************

                                                    }else{
                                                        echo json_encode(array('result'=>$employeetoken[0]
                                                        ,"data"=>$employeetoken[1]
                                                        ,'desc'=>$employeetoken[2]));

                                                    }



                                                }

//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//***************************************************************************************************************
                                                else
                                                    if ($command=="add_damage_human")
                                                    {
                                                        $thirdparty_damage_human_id=$this->post('thirdparty_damage_human_id') ;
                                                        $thirdparty_damage_human_name=$this->post('thirdparty_damage_human_name') ;
                                                        $thirdparty_damage_human_digit=$this->post('thirdparty_damage_human_digit') ;



                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','thirdparty');
                                                        if($employeetoken[0]=='ok')
                                                        {
//****************************************************************************************************************
                                                            $query="select * from thirdparty_damage_human_tb where thirdparty_damage_human_name='".$thirdparty_damage_human_name."'";
                                                            $result=$this->B_db->run_query($query);
                                                            $num=count($result[0]);
                                                            if ($num==0)
                                                            {
                                                                $query="INSERT INTO thirdparty_damage_human_tb(thirdparty_damage_human_id, thirdparty_damage_human_name, thirdparty_damage_human_digit)
                VALUES ( $thirdparty_damage_human_id,'$thirdparty_damage_human_name', '$thirdparty_damage_human_digit');";

                                                                $result=$this->B_db->run_query_put($query);
                                                                $thirdparty_damage_human_id=$this->db->insert_id();

                                                                echo json_encode(array('result'=>"ok"
                                                                ,"data"=>array('thirdparty_damage_human_id'=>$thirdparty_damage_human_id)
                                                                ,'desc'=>'خسارت بدنی شخص ثالث اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                            }else{
                                                                $carmode=$result[0];
                                                                echo json_encode(array('result'=>"error"
                                                                ,"data"=>array('thirdparty_damage_human_id'=>$carmode['thirdparty_damage_human_id'])
                                                                ,'desc'=>'خسارت بدنی شخص ثالث تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                            }
//***************************************************************************************************************
                                                        }else{
                                                            echo json_encode(array('result'=>$employeetoken[0]
                                                            ,"data"=>$employeetoken[1]
                                                            ,'desc'=>$employeetoken[2]));

                                                        }
                                                    }
                                                    else
                                                        if ($command=="get_damage_human")
                                                        {
                                                            $query="select * from thirdparty_damage_human_tb where 1 ORDER BY thirdparty_damage_human_id ASC";
                                                            $result = $this->B_db->run_query($query);
                                                            $output =array();
                                                            foreach($result as $row)
                                                            {
                                                                $record=array();
                                                                $record['thirdparty_damage_human_id']=$row['thirdparty_damage_human_id'];
                                                                $record['thirdparty_damage_human_name']=$row['thirdparty_damage_human_name'];
                                                                $record['thirdparty_damage_human_digit']=$row['thirdparty_damage_human_digit'];
                                                                $output[]=$record;
                                                            }
                                                            echo json_encode(array('result'=>"ok"
                                                            ,"data"=>$output
                                                            ,'desc'=>'خسارت بدنی شخص ثالث با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                        }
                                                        else
                                                            if ($command=="delete_damage_human")
                                                            {
                                                                $thirdparty_damage_human_id=$this->post('thirdparty_damage_human_id') ;

                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','thirdparty');
                                                                if($employeetoken[0]=='ok')
                                                                {
                                                                    $user_id=$employeetoken[1];

                                                                    $query="DELETE FROM thirdparty_damage_human_tb  where thirdparty_damage_human_id=".$thirdparty_damage_human_id."";
                                                                    $result = $this->B_db->run_query_put($query);
                                                                    if($result){echo json_encode(array('result'=>"ok"
                                                                    ,"data"=>$output
                                                                    ,'desc'=>'خسارت بدنی شخص ثالث حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                    }else{
                                                                        echo json_encode(array('result'=>"error"
                                                                        ,"data"=>$output
                                                                        ,'desc'=>'خسارت بدنی شخص ثالث حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                    }
                                                                }else{
                                                                    echo json_encode(array('result'=>$employeetoken[0]
                                                                    ,"data"=>$employeetoken[1]
                                                                    ,'desc'=>$employeetoken[2]));
                                                                }
                                                            }
                                                            else
                                                                if ($command=="modify_damage_human")
                                                                {
                                                                    $thirdparty_damage_human_id=$this->post('thirdparty_damage_human_id') ;

                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','thirdparty');
                                                                    if($employeetoken[0]=='ok')
                                                                    {
                                                                        $query="UPDATE thirdparty_damage_human_tb SET ";

                                                                        if(isset($_REQUEST['thirdparty_damage_human_name'])){
                                                                            $thirdparty_damage_human_name=$this->post('thirdparty_damage_human_name');
                                                                            $query.="thirdparty_damage_human_name='".$thirdparty_damage_human_name."'";}

                                                                        if(isset($_REQUEST['thirdparty_damage_human_digit'])&&(isset($_REQUEST['thirdparty_damage_human_name']))){ $query.=",";}
                                                                        if(isset($_REQUEST['thirdparty_damage_human_digit'])){
                                                                            $thirdparty_damage_human_digit=$this->post('thirdparty_damage_human_digit');
                                                                            $query.="thirdparty_damage_human_digit='".$thirdparty_damage_human_digit."'";}

                                                                        $query.="where thirdparty_damage_human_id=".$thirdparty_damage_human_id;

                                                                        $result=$this->B_db->run_query_put($query);
                                                                        if($result){
                                                                            echo json_encode(array('result'=>"ok"
                                                                            ,"data"=>""
                                                                            ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                        }
//***************************************************************************************************************

                                                                    }else{
                                                                        echo json_encode(array('result'=>$employeetoken[0]
                                                                        ,"data"=>$employeetoken[1]
                                                                        ,'desc'=>$employeetoken[2]));

                                                                    }



                                                                }
                                                                else
                                                                    if ($command=="add_damage_driver")
                                                                    {
                                                                        $thirdparty_damage_driver_id=$this->post('thirdparty_damage_driver_id') ;
                                                                        $thirdparty_damage_driver_name=$this->post('thirdparty_damage_driver_name') ;
                                                                        $thirdparty_damage_driver_digit=$this->post('thirdparty_damage_driver_digit') ;



                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','thirdparty');
                                                                        if($employeetoken[0]=='ok')
                                                                        {
//****************************************************************************************************************
                                                                            $query="select * from thirdparty_damage_driver_tb where thirdparty_damage_driver_name='".$thirdparty_damage_driver_name."'";
                                                                            $result=$this->B_db->run_query($query);
                                                                            $num=count($result[0]);
                                                                            if ($num==0)
                                                                            {
                                                                                $query="INSERT INTO thirdparty_damage_driver_tb(thirdparty_damage_driver_id, thirdparty_damage_driver_name, thirdparty_damage_driver_digit)
                VALUES ( $thirdparty_damage_driver_id,'$thirdparty_damage_driver_name', '$thirdparty_damage_driver_digit');";

                                                                                $result=$this->B_db->run_query_put($query);
                                                                                $thirdparty_damage_driver_id=$this->db->insert_id();

                                                                                echo json_encode(array('result'=>"ok"
                                                                                ,"data"=>array('thirdparty_damage_driver_id'=>$thirdparty_damage_driver_id)
                                                                                ,'desc'=>'خسارت راننده شخص ثالث اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                            }else{
                                                                                $carmode=$result[0];
                                                                                echo json_encode(array('result'=>"error"
                                                                                ,"data"=>array('thirdparty_damage_driver_id'=>$carmode['thirdparty_damage_driver_id'])
                                                                                ,'desc'=>'خسارت راننده شخص ثالث تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                            }
//***************************************************************************************************************
                                                                        }else{
                                                                            echo json_encode(array('result'=>$employeetoken[0]
                                                                            ,"data"=>$employeetoken[1]
                                                                            ,'desc'=>$employeetoken[2]));

                                                                        }

                                                                    }
                                                                    else
                                                                        if ($command=="get_damage_driver")
                                                                        {
//************************************************************************;****************************************

                                                                            $query="select * from thirdparty_damage_driver_tb where 1 ORDER BY thirdparty_damage_driver_id ASC";
                                                                            $result = $this->B_db->run_query($query);
                                                                            $output =array();
                                                                            foreach($result as $row)
                                                                            {
                                                                                $record=array();
                                                                                $record['thirdparty_damage_driver_id']=$row['thirdparty_damage_driver_id'];
                                                                                $record['thirdparty_damage_driver_name']=$row['thirdparty_damage_driver_name'];
                                                                                $record['thirdparty_damage_driver_digit']=$row['thirdparty_damage_driver_digit'];
                                                                                $output[]=$record;
                                                                            }
                                                                            echo json_encode(array('result'=>"ok"
                                                                            ,"data"=>$output
                                                                            ,'desc'=>'خسارت راننده شخص ثالث با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                        }
                                                                        else
                                                                            if ($command=="delete_damage_driver")
                                                                            {
                                                                                $thirdparty_damage_driver_id=$this->post('thirdparty_damage_driver_id') ;

                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','thirdparty');
                                                                                if($employeetoken[0]=='ok')
                                                                                {
//************************************************************************;****************************************
                                                                                    $user_id=$employeetoken[1];

                                                                                    $query="DELETE FROM thirdparty_damage_driver_tb  where thirdparty_damage_driver_id=".$thirdparty_damage_driver_id."";
                                                                                    $result = $this->B_db->run_query_put($query);
                                                                                    if($result){echo json_encode(array('result'=>"ok"
                                                                                    ,"data"=>$output
                                                                                    ,'desc'=>'خسارت راننده شخص ثالث حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                    }else{
                                                                                        echo json_encode(array('result'=>"error"
                                                                                        ,"data"=>$output
                                                                                        ,'desc'=>'خسارت راننده شخص ثالث حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                    }
//***************************************************************************************************************
                                                                                }else{
                                                                                    echo json_encode(array('result'=>$employeetoken[0]
                                                                                    ,"data"=>$employeetoken[1]
                                                                                    ,'desc'=>$employeetoken[2]));

                                                                                }

                                                                            }
                                                                            else



                                                                                if ($command=="modify_damage_driver")
                                                                                {
                                                                                    $thirdparty_damage_driver_id=$this->post('thirdparty_damage_driver_id') ;

                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','thirdparty');
                                                                                    if($employeetoken[0]=='ok')
                                                                                    {
//*****************************************************************************************

                                                                                        $query="UPDATE thirdparty_damage_driver_tb SET ";

                                                                                        if(isset($_REQUEST['thirdparty_damage_driver_name'])){
                                                                                            $thirdparty_damage_driver_name=$this->post('thirdparty_damage_driver_name');
                                                                                            $query.="thirdparty_damage_driver_name='".$thirdparty_damage_driver_name."'";}

                                                                                        if(isset($_REQUEST['thirdparty_damage_driver_digit'])&&(isset($_REQUEST['thirdparty_damage_driver_name']))){ $query.=",";}
                                                                                        if(isset($_REQUEST['thirdparty_damage_driver_digit'])){
                                                                                            $thirdparty_damage_driver_digit=$this->post('thirdparty_damage_driver_digit');
                                                                                            $query.="thirdparty_damage_driver_digit='".$thirdparty_damage_driver_digit."'";}

                                                                                        $query.="where thirdparty_damage_driver_id=".$thirdparty_damage_driver_id;

                                                                                        $result=$this->B_db->run_query_put($query);
                                                                                        if($result){
                                                                                            echo json_encode(array('result'=>"ok"
                                                                                            ,"data"=>""
                                                                                            ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                        }
//***************************************************************************************************************
                                                                                    }else{
                                                                                        echo json_encode(array('result'=>$employeetoken[0]
                                                                                        ,"data"=>$employeetoken[1]
                                                                                        ,'desc'=>$employeetoken[2]));

                                                                                    }



                                                                                }
                                                                                else
                                                                                    if ($command=="add_time")
                                                                                    {

                                                                                        $thirdparty_time_id=$this->post('thirdparty_time_id') ;
                                                                                        $thirdparty_time_desc=$this->post('thirdparty_time_desc') ;
                                                                                        $thirdparty_time_percent=$this->post('thirdparty_time_percent') ;



                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','thirdparty');
                                                                                        if($employeetoken[0]=='ok')
                                                                                        {
//****************************************************************************************************************
                                                                                            $query="select * from thirdparty_time_tb where thirdparty_time_desc='".$thirdparty_time_desc."' OR thirdparty_time_id=".$thirdparty_time_id."";
                                                                                            $result=$this->B_db->run_query($query);
                                                                                            $num=count($result[0]);
                                                                                            if ($num==0)
                                                                                            {
                                                                                                $query="INSERT INTO thirdparty_time_tb(thirdparty_time_id, thirdparty_time_desc, thirdparty_time_percent)
VALUES ( $thirdparty_time_id,'$thirdparty_time_desc', '$thirdparty_time_percent');";

                                                                                                $result=$this->B_db->run_query_put($query);
                                                                                                $thirdparty_time_id=$this->db->insert_id();

                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                ,"data"=>array('thirdparty_time_id'=>$thirdparty_time_id)
                                                                                                ,'desc'=>'مدت بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                            }else{
                                                                                                $carmode=$result[0];
                                                                                                echo json_encode(array('result'=>"error"
                                                                                                ,"data"=>array('thirdparty_time_id'=>$carmode['thirdparty_time_id'])
                                                                                                ,'desc'=>'مدت بیمه نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                            }
//***************************************************************************************************************
                                                                                        }else{
                                                                                            echo json_encode(array('result'=>$employeetoken[0]
                                                                                            ,"data"=>$employeetoken[1]
                                                                                            ,'desc'=>$employeetoken[2]));

                                                                                        }



                                                                                    }
                                                                                    else
                                                                                        if ($command=="get_time")
                                                                                        {
//************************************************************************;****************************************

                                                                                            $query="select * from thirdparty_time_tb where 1 ORDER BY thirdparty_time_id ASC";
                                                                                            $result = $this->B_db->run_query($query);
                                                                                            $output =array();
                                                                                            foreach($result as $row)
                                                                                            {
                                                                                                $record=array();
                                                                                                $record['thirdparty_time_id']=$row['thirdparty_time_id'];
                                                                                                $record['thirdparty_time_desc']=$row['thirdparty_time_desc'];
                                                                                                $record['thirdparty_time_percent']=$row['thirdparty_time_percent'];
                                                                                                $output[]=$record;
                                                                                            }
                                                                                            echo json_encode(array('result'=>"ok"
                                                                                            ,"data"=>$output
                                                                                            ,'desc'=>'مدت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                        }
                                                                                        else
                                                                                            if ($command=="delete_time")
                                                                                            {
                                                                                                $thirdparty_time_id=$this->post('thirdparty_time_id') ;

                                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','thirdparty');
                                                                                                if($employeetoken[0]=='ok')
                                                                                                {
//************************************************************************;****************************************
                                                                                                    $user_id=$employeetoken[1];

                                                                                                    $query="DELETE FROM thirdparty_time_tb  where thirdparty_time_id=".$thirdparty_time_id."";
                                                                                                    $result = $this->B_db->run_query_put($query);
                                                                                                    if($result){echo json_encode(array('result'=>"ok"
                                                                                                    ,"data"=>$output
                                                                                                    ,'desc'=>'مدت بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                    }else{
                                                                                                        echo json_encode(array('result'=>"error"
                                                                                                        ,"data"=>$output
                                                                                                        ,'desc'=>'مدت بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                    }
//***************************************************************************************************************
                                                                                                }else{
                                                                                                    echo json_encode(array('result'=>$employeetoken[0]
                                                                                                    ,"data"=>$employeetoken[1]
                                                                                                    ,'desc'=>$employeetoken[2]));

                                                                                                }

                                                                                            }
                                                                                            else



                                                                                                if ($command=="modify_time")
                                                                                                {
                                                                                                    $thirdparty_time_id=$this->post('thirdparty_time_id') ;

                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','thirdparty');
                                                                                                    if($employeetoken[0]=='ok')
                                                                                                    {
//*****************************************************************************************

                                                                                                        $query="UPDATE thirdparty_time_tb SET ";

                                                                                                        if(isset($_REQUEST['thirdparty_time_desc'])){ $query.="thirdparty_time_desc='".$_REQUEST['thirdparty_time_desc']."'";}

                                                                                                        if(isset($_REQUEST['thirdparty_time_percent'])&&(isset($_REQUEST['thirdparty_time_desc']))){ $query.=",";}
                                                                                                        if(isset($_REQUEST['thirdparty_time_percent'])){$query.="thirdparty_time_percent='".$_REQUEST['thirdparty_time_percent']."'";}

                                                                                                        $query.="where thirdparty_time_id=".$thirdparty_time_id;

                                                                                                        $result=$this->B_db->run_query_put($query);
                                                                                                        if($result){
                                                                                                            echo json_encode(array('result'=>"ok"
                                                                                                            ,"data"=>""
                                                                                                            ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                        }
//***************************************************************************************************************
                                                                                                    }else{
                                                                                                        echo json_encode(array('result'=>$employeetoken[0]
                                                                                                        ,"data"=>$employeetoken[1]
                                                                                                        ,'desc'=>$employeetoken[2]));

                                                                                                    }



                                                                                                }

//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//***************************************************************************************************************
                                                                                                else
                                                                                                    if ($command=="add_mode")
                                                                                                    {

                                                                                                        $thirdparty_mode_id=$this->post('thirdparty_mode_id') ;
                                                                                                        $thirdparty_mode_desc=$this->post('thirdparty_mode_desc') ;
                                                                                                        $thirdparty_mode_fieldinsurance=$this->post('thirdparty_mode_fieldinsurance') ;



                                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','thirdparty');
                                                                                                        if($employeetoken[0]=='ok')
                                                                                                        {
//****************************************************************************************************************
                                                                                                            $query="select * from thirdparty_mode_tb where thirdparty_mode_desc='".$thirdparty_mode_desc."' OR thirdparty_mode_id=".$thirdparty_mode_id."";
                                                                                                            $result=$this->B_db->run_query($query);
                                                                                                            $num=count($result[0]);
                                                                                                            if ($num==0)
                                                                                                            {
                                                                                                                $query="INSERT INTO thirdparty_mode_tb(thirdparty_mode_id, thirdparty_mode_desc, thirdparty_mode_fieldinsurance)
VALUES ( $thirdparty_mode_id,'$thirdparty_mode_desc', '$thirdparty_mode_fieldinsurance');";

                                                                                                                $result=$this->B_db->run_query_put($query);
                                                                                                                $thirdparty_mode_id=$this->db->insert_id();

                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                ,"data"=>array('thirdparty_mode_id'=>$thirdparty_mode_id)
                                                                                                                ,'desc'=>'مدت بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                            }else{
                                                                                                                $carmode=$result[0];
                                                                                                                echo json_encode(array('result'=>"error"
                                                                                                                ,"data"=>array('thirdparty_mode_id'=>$carmode['thirdparty_mode_id'])
                                                                                                                ,'desc'=>'مدت بیمه نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                            }
//***************************************************************************************************************
                                                                                                        }else{
                                                                                                            echo json_encode(array('result'=>$employeetoken[0]
                                                                                                            ,"data"=>$employeetoken[1]
                                                                                                            ,'desc'=>$employeetoken[2]));

                                                                                                        }



                                                                                                    }
                                                                                                    else
                                                                                                        if ($command=="get_mode")
                                                                                                        {
//************************************************************************;****************************************

                                                                                                            $query="select * from thirdparty_mode_tb where 1 ORDER BY thirdparty_mode_id ASC";
                                                                                                            $result = $this->B_db->run_query($query);
                                                                                                            $output =array();
                                                                                                            foreach($result as $row)
                                                                                                            {
                                                                                                                $record=array();
                                                                                                                $record['thirdparty_mode_id']=$row['thirdparty_mode_id'];
                                                                                                                $record['thirdparty_mode_desc']=$row['thirdparty_mode_desc'];
                                                                                                                $record['thirdparty_mode_fieldinsurance']=$row['thirdparty_mode_fieldinsurance'];
                                                                                                                $output[]=$record;
                                                                                                            }
                                                                                                            echo json_encode(array('result'=>"ok"
                                                                                                            ,"data"=>$output
                                                                                                            ,'desc'=>'مدت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                                        }
                                                                                                        else
                                                                                                            if ($command=="delete_mode")
                                                                                                            {
                                                                                                                $thirdparty_mode_id=$this->post('thirdparty_mode_id') ;

                                                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','thirdparty');
                                                                                                                if($employeetoken[0]=='ok')
                                                                                                                {
//************************************************************************;****************************************
                                                                                                                    $user_id=$employeetoken[1];

                                                                                                                    $query="DELETE FROM thirdparty_mode_tb  where thirdparty_mode_id=".$thirdparty_mode_id."";
                                                                                                                    $result = $this->B_db->run_query_put($query);
                                                                                                                    if($result){echo json_encode(array('result'=>"ok"
                                                                                                                    ,"data"=>$output
                                                                                                                    ,'desc'=>'مدت بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                    }else{
                                                                                                                        echo json_encode(array('result'=>"error"
                                                                                                                        ,"data"=>$output
                                                                                                                        ,'desc'=>'مدت بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                    }
//***************************************************************************************************************
                                                                                                                }else{
                                                                                                                    echo json_encode(array('result'=>$employeetoken[0]
                                                                                                                    ,"data"=>$employeetoken[1]
                                                                                                                    ,'desc'=>$employeetoken[2]));

                                                                                                                }

                                                                                                            }
                                                                                                            else
                                                                                                                if ($command=="modify_mode")
                                                                                                                {
                                                                                                                    $thirdparty_mode_id=$this->post('thirdparty_mode_id') ;

                                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','thirdparty');
                                                                                                                    if($employeetoken[0]=='ok')
                                                                                                                    {
//*****************************************************************************************

                                                                                                                        $query="UPDATE thirdparty_mode_tb SET ";

                                                                                                                        if(isset($_REQUEST['thirdparty_mode_desc'])){ $query.="thirdparty_mode_desc='".$_REQUEST['thirdparty_mode_desc']."'";}

                                                                                                                        if(isset($_REQUEST['thirdparty_mode_fieldinsurance'])&&(isset($_REQUEST['thirdparty_mode_desc']))){ $query.=",";}
                                                                                                                        if(isset($_REQUEST['thirdparty_mode_fieldinsurance'])){$query.="thirdparty_mode_fieldinsurance='".$_REQUEST['thirdparty_mode_fieldinsurance']."'";}

                                                                                                                        $query.="where thirdparty_mode_id=".$thirdparty_mode_id;

                                                                                                                        $result=$this->B_db->run_query_put($query);
                                                                                                                        if($result){
                                                                                                                            echo json_encode(array('result'=>"ok"
                                                                                                                            ,"data"=>""
                                                                                                                            ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                        }
//***************************************************************************************************************
                                                                                                                    }else{
                                                                                                                        echo json_encode(array('result'=>$employeetoken[0]
                                                                                                                        ,"data"=>$employeetoken[1]
                                                                                                                        ,'desc'=>$employeetoken[2]));

                                                                                                                    }



                                                                                                                }

//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//***************************************************************************************************************
                                                                                                                else
                                                                                                                    if ($command=="add_usefor")
                                                                                                                    {
                                                                                                                        $thirdparty_usefor_id=$this->post('thirdparty_usefor_id');
                                                                                                                        $thirdparty_usefor_desc=$this->post('thirdparty_usefor_desc');
                                                                                                                        $thirdparty_usefor_percent=$this->post('thirdparty_usefor_percent');
                                                                                                                        $thirdparty_usefor_carmode_id=$this->post('thirdparty_usefor_carmode_id',1);

                                                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','thirdparty');
                                                                                                                        if($employeetoken[0]=='ok')
                                                                                                                        {
//****************************************************************************************************************
                                                                                                                            $query="select * from thirdparty_usefor_tb where thirdparty_usefor_carmode_id=$thirdparty_usefor_carmode_id AND( thirdparty_usefor_desc='".$thirdparty_usefor_desc."' OR thirdparty_usefor_id=".$thirdparty_usefor_id." )";
                                                                                                                            $result=$this->B_db->run_query($query);
                                                                                                                            $num=count($result[0]);
                                                                                                                            if ($num==0)
                                                                                                                            {
                                                                                                                                $query="INSERT INTO thirdparty_usefor_tb(thirdparty_usefor_id, thirdparty_usefor_desc, thirdparty_usefor_percent,thirdparty_usefor_carmode_id)
VALUES ( $thirdparty_usefor_id,'$thirdparty_usefor_desc', '$thirdparty_usefor_percent',$thirdparty_usefor_carmode_id);";

                                                                                                                                $result=$this->B_db->run_query_put($query);
                                                                                                                                $thirdparty_usefor_id=$this->db->insert_id();

                                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                                ,"data"=>array('thirdparty_usefor_id'=>$thirdparty_usefor_id)
                                                                                                                                ,'desc'=>'مورد استفاده بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                            }else{
                                                                                                                                $carmode=$result[0];
                                                                                                                                echo json_encode(array('result'=>"error"
                                                                                                                                ,"data"=>array('thirdparty_usefor_id'=>$carmode['thirdparty_usefor_id'])
                                                                                                                                ,'desc'=>'مورد استفاده بیمه نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                            }
//***************************************************************************************************************
                                                                                                                        }else{
                                                                                                                            echo json_encode(array('result'=>$employeetoken[0]
                                                                                                                            ,"data"=>$employeetoken[1]
                                                                                                                            ,'desc'=>$employeetoken[2]));

                                                                                                                        }

                                                                                                                    }
                                                                                                                    else
                                                                                                                        if ($command=="get_usefor")
                                                                                                                        {
//************************************************************************;****************************************

                                                                                                                            $query="select * from thirdparty_usefor_tb,carmode_tb where thirdparty_usefor_carmode_id=carmode_id  AND ";

                                                                                                                            if(isset($_REQUEST['carmode_id'])){
                                                                                                                                $carmode_id=$this->post('carmode_id',1);
                                                                                                                                $query.=' thirdparty_usefor_carmode_id='.$carmode_id;}else{$query.=" 1=1 ";}
                                                                                                                            $query.=" ORDER BY thirdparty_usefor_id ASC";

                                                                                                                            $result = $this->B_db->run_query($query);
                                                                                                                            $output =array();
                                                                                                                            foreach($result as $row)
                                                                                                                            {
                                                                                                                                $record=array();
                                                                                                                                $record['thirdparty_usefor_id']=$row['thirdparty_usefor_id'];
                                                                                                                                $record['thirdparty_usefor_desc']=$row['thirdparty_usefor_desc'];
                                                                                                                                $record['thirdparty_usefor_percent']=$row['thirdparty_usefor_percent'];
                                                                                                                                $record['thirdparty_usefor_carmode_id']=$row['thirdparty_usefor_carmode_id'];
                                                                                                                                $record['carmode_name']=$row['carmode_name'];

                                                                                                                                $output[]=$record;
                                                                                                                            }
                                                                                                                            echo json_encode(array('result'=>"ok"
                                                                                                                            ,"data"=>$output
                                                                                                                            ,'desc'=>'مورد استفاده بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                                                        }
                                                                                                                        else
                                                                                                                            if ($command=="delete_usefor")
                                                                                                                            {
                                                                                                                                $thirdparty_usefor_id=$this->post('thirdparty_usefor_id');

                                                                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','thirdparty');
                                                                                                                                if($employeetoken[0]=='ok')
                                                                                                                                {
//************************************************************************;****************************************
                                                                                                                                    $user_id=$employeetoken[1];

                                                                                                                                    $query="DELETE FROM thirdparty_usefor_tb  where thirdparty_usefor_id=".$thirdparty_usefor_id."";
                                                                                                                                    $result = $this->B_db->run_query_put($query);
                                                                                                                                    if($result){echo json_encode(array('result'=>"ok"
                                                                                                                                    ,"data"=>$output
                                                                                                                                    ,'desc'=>'مورد استفاده بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                    }else{
                                                                                                                                        echo json_encode(array('result'=>"error"
                                                                                                                                        ,"data"=>$output
                                                                                                                                        ,'desc'=>'مورد استفاده بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                    }
//***************************************************************************************************************
                                                                                                                                }else{
                                                                                                                                    echo json_encode(array('result'=>$employeetoken[0]
                                                                                                                                    ,"data"=>$employeetoken[1]
                                                                                                                                    ,'desc'=>$employeetoken[2]));

                                                                                                                                }

                                                                                                                            }
                                                                                                                            else



                                                                                                                                if ($command=="modify_usefor")
                                                                                                                                {
                                                                                                                                    $thirdparty_usefor_id=$this->post('thirdparty_usefor_id');

                                                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','thirdparty');
                                                                                                                                    if($employeetoken[0]=='ok')
                                                                                                                                    {
//*****************************************************************************************

                                                                                                                                        $query="UPDATE thirdparty_usefor_tb SET ";

                                                                                                                                        if(isset($_REQUEST['thirdparty_usefor_desc'])){ $query.="thirdparty_usefor_desc='".$_REQUEST['thirdparty_usefor_desc']."'";}

                                                                                                                                        if(isset($_REQUEST['thirdparty_usefor_percent'])&&(isset($_REQUEST['thirdparty_usefor_desc']))){ $query.=",";}
                                                                                                                                        if(isset($_REQUEST['thirdparty_usefor_percent'])){$query.="thirdparty_usefor_percent='".$_REQUEST['thirdparty_usefor_percent']."'";}

                                                                                                                                        if(isset($_REQUEST['thirdparty_usefor_id'])&&(isset($_REQUEST['thirdparty_usefor_percent'])||isset($_REQUEST['thirdparty_usefor_desc']))){ $query.=",";}
                                                                                                                                        if(isset($_REQUEST['thirdparty_usefor_id'])){$query.="thirdparty_usefor_id='".$_REQUEST['thirdparty_usefor_id']."'";}

                                                                                                                                        $query.="where thirdparty_usefor_id=".$thirdparty_usefor_id;

                                                                                                                                        $result=$this->B_db->run_query_put($query);
                                                                                                                                        if($result){
                                                                                                                                            echo json_encode(array('result'=>"ok"
                                                                                                                                            ,"data"=>""
                                                                                                                                            ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                        }
//***************************************************************************************************************
                                                                                                                                    }else{
                                                                                                                                        echo json_encode(array('result'=>$employeetoken[0]
                                                                                                                                        ,"data"=>$employeetoken[1]
                                                                                                                                        ,'desc'=>$employeetoken[2]));

                                                                                                                                    }



                                                                                                                                }
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//***************************************************************************************************************
                                                                                                                                else
                                                                                                                                    if ($command=="add_coverage")
                                                                                                                                    {
                                                                                                                                        $thirdparty_coverage_id=$this->post('thirdparty_coverage_id');
                                                                                                                                        $thirdparty_coverage_desc=$this->post('thirdparty_coverage_desc');
                                                                                                                                        $thirdparty_coverage_price=$this->post('thirdparty_coverage_price');



                                                                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','thirdparty');
                                                                                                                                        if($employeetoken[0]=='ok')
                                                                                                                                        {
//****************************************************************************************************************
                                                                                                                                            $query="select * from thirdparty_coverage_tb where thirdparty_coverage_deactive=0 AND  thirdparty_coverage_desc='".$thirdparty_coverage_desc."' OR thirdparty_coverage_id=".$thirdparty_coverage_id."";
                                                                                                                                            $result=$this->B_db->run_query($query);
                                                                                                                                            $num=count($result[0]);
                                                                                                                                            if ($num==0)
                                                                                                                                            {
                                                                                                                                                $query="INSERT INTO thirdparty_coverage_tb(thirdparty_coverage_id, thirdparty_coverage_desc, thirdparty_coverage_price)
VALUES ( $thirdparty_coverage_id,'$thirdparty_coverage_desc', '$thirdparty_coverage_price');";

                                                                                                                                                $result=$this->B_db->run_query_put($query);
                                                                                                                                                $thirdparty_coverage_id=$this->db->insert_id();

                                                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                                                ,"data"=>array('thirdparty_coverage_id'=>$thirdparty_coverage_id)
                                                                                                                                                ,'desc'=>'مدت بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                            }else{
                                                                                                                                                $carmode=$result[0];
                                                                                                                                                echo json_encode(array('result'=>"error"
                                                                                                                                                ,"data"=>array('thirdparty_coverage_id'=>$carmode['thirdparty_coverage_id'])
                                                                                                                                                ,'desc'=>'مدت بیمه نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                            }
//***************************************************************************************************************
                                                                                                                                        }else{
                                                                                                                                            echo json_encode(array('result'=>$employeetoken[0]
                                                                                                                                            ,"data"=>$employeetoken[1]
                                                                                                                                            ,'desc'=>$employeetoken[2]));

                                                                                                                                        }


                                                                                                                                    }
                                                                                                                                    else
                                                                                                                                        if ($command=="get_coverage")
                                                                                                                                        {
//************************************************************************;****************************************

                                                                                                                                            $query="select * from thirdparty_coverage_tb where thirdparty_coverage_deactive=0 ORDER BY thirdparty_coverage_price ASC";
                                                                                                                                            $result = $this->B_db->run_query($query);
                                                                                                                                            $output =array();
                                                                                                                                            foreach($result as $row)
                                                                                                                                            {
                                                                                                                                                $record=array();
                                                                                                                                                $record['thirdparty_coverage_id']=$row['thirdparty_coverage_id'];
                                                                                                                                                $record['thirdparty_coverage_desc']=$row['thirdparty_coverage_desc'];
                                                                                                                                                $record['thirdparty_coverage_price']=$row['thirdparty_coverage_price'];
                                                                                                                                                $output[]=$record;
                                                                                                                                            }
                                                                                                                                            echo json_encode(array('result'=>"ok"
                                                                                                                                            ,"data"=>$output
                                                                                                                                            ,'desc'=>'مدت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                                                                        }
                                                                                                                                        else
                                                                                                                                            if ($command=="delete_coverage")
                                                                                                                                            {
                                                                                                                                                $thirdparty_coverage_id=$this->post('thirdparty_coverage_id');

                                                                                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','thirdparty');
                                                                                                                                                if($employeetoken[0]=='ok')
                                                                                                                                                {
//************************************************************************;****************************************
                                                                                                                                                    $user_id=$employeetoken[1];

                                                                                                                                                    $query="DELETE FROM thirdparty_coverage_tb  where thirdparty_coverage_id=".$thirdparty_coverage_id."";
                                                                                                                                                    $result = $this->B_db->run_query_put($query);
                                                                                                                                                    if($result){echo json_encode(array('result'=>"ok"
                                                                                                                                                    ,"data"=>$output
                                                                                                                                                    ,'desc'=>'مدت بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                    }else{
                                                                                                                                                        echo json_encode(array('result'=>"error"
                                                                                                                                                        ,"data"=>$output
                                                                                                                                                        ,'desc'=>'مدت بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                    }
//***************************************************************************************************************
                                                                                                                                                }else{
                                                                                                                                                    echo json_encode(array('result'=>$employeetoken[0]
                                                                                                                                                    ,"data"=>$employeetoken[1]
                                                                                                                                                    ,'desc'=>$employeetoken[2]));

                                                                                                                                                }

                                                                                                                                            }
                                                                                                                                            else



                                                                                                                                                if ($command=="modify_coverage")
                                                                                                                                                {
                                                                                                                                                    $thirdparty_coverage_id=$this->post('thirdparty_coverage_id');

                                                                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','thirdparty');
                                                                                                                                                    if($employeetoken[0]=='ok')
                                                                                                                                                    {
//*****************************************************************************************

                                                                                                                                                        $query="UPDATE thirdparty_coverage_tb SET ";

                                                                                                                                                        if(isset($_REQUEST['thirdparty_coverage_desc'])){
                                                                                                                                                            $thirdparty_coverage_desc=$this->post('thirdparty_coverage_desc');
                                                                                                                                                            $query.="thirdparty_coverage_desc='".$thirdparty_coverage_desc."'";}

                                                                                                                                                        if(isset($_REQUEST['thirdparty_coverage_price'])&&(isset($_REQUEST['thirdparty_coverage_desc']))){ $query.=",";}
                                                                                                                                                        if(isset($_REQUEST['thirdparty_coverage_price'])){
                                                                                                                                                            $thirdparty_coverage_price=$this->post('thirdparty_coverage_price');
                                                                                                                                                            $query.="thirdparty_coverage_price='".$thirdparty_coverage_price."'";}

                                                                                                                                                        $query.="where thirdparty_coverage_id=".$thirdparty_coverage_id;

                                                                                                                                                        $result=$this->B_db->run_query_put($query);
                                                                                                                                                        if($result){
                                                                                                                                                            echo json_encode(array('result'=>"ok"
                                                                                                                                                            ,"data"=>""
                                                                                                                                                            ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                        }
//***************************************************************************************************************
                                                                                                                                                    }else{
                                                                                                                                                        echo json_encode(array('result'=>$employeetoken[0]
                                                                                                                                                        ,"data"=>$employeetoken[1]
                                                                                                                                                        ,'desc'=>$employeetoken[2]));

                                                                                                                                                    }



                                                                                                                                                }
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//***************************************************************************************************************

                                                                                                                                                else
                                                                                                                                                    if ($command=="get_extrafinancial_calmode")
                                                                                                                                                    {
//************************************************************************;****************************************

                                                                                                                                                        $query="select * from extrafinancial_calmode_tb where 1 ORDER BY extrafinancial_calmode_id ASC";
                                                                                                                                                        $result = $this->B_db->run_query($query);
                                                                                                                                                        $output =array();
                                                                                                                                                        foreach($result as $row)
                                                                                                                                                        {
                                                                                                                                                            $record=array();
                                                                                                                                                            $record['extrafinancial_calmode_id']=$row['extrafinancial_calmode_id'];
                                                                                                                                                            $record['extrafinancial_calmode_name']=$row['extrafinancial_calmode_name'];
                                                                                                                                                            $output[]=$record;
                                                                                                                                                        }
                                                                                                                                                        echo json_encode(array('result'=>"ok"
                                                                                                                                                        ,"data"=>$output
                                                                                                                                                        ,'desc'=>'مدت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                                                                                    }

//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//***************************************************************************************************************
                                                                                                                                                    else
                                                                                                                                                        if ($command=="add_thirdparty_price")
                                                                                                                                                        {

                                                                                                                                                            $thirdparty_price_mode_id=$this->post('thirdparty_price_mode_id');
                                                                                                                                                            $thirdparty_price_fieldcompany_id=$this->post('thirdparty_price_fieldcompany_id');
                                                                                                                                                            $thirdparty_price_cargroup_id=$this->post('thirdparty_price_cargroup_id');
                                                                                                                                                            $thirdparty_price_car_mode_id=$this->post('thirdparty_price_car_mode_id');
                                                                                                                                                            $thirdparty_price_forcedthird=$this->post('thirdparty_price_forcedthird');
                                                                                                                                                            $thirdparty_price_passenger=$this->post('thirdparty_price_passenger');
                                                                                                                                                            $thirdparty_price_disc=$this->post('thirdparty_price_disc');
                                                                                                                                                            $thirdparty_price_yadak=$this->post('thirdparty_price_yadak');



                                                                                                                                                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','thirdparty');
                                                                                                                                                            if($employeetoken[0]=='ok')
                                                                                                                                                            {
//****************************************************************************************************************
                                                                                                                                                                $query="select * from thirdparty_price_tb where thirdparty_price_car_mode_id=$thirdparty_price_car_mode_id AND thirdparty_price_cargroup_id=".$thirdparty_price_cargroup_id." AND thirdparty_price_fieldcompany_id=".$thirdparty_price_fieldcompany_id." AND thirdparty_price_mode_id=".$thirdparty_price_mode_id."";
                                                                                                                                                                $result=$this->B_db->run_query($query);
                                                                                                                                                                $num=count($result[0]);
                                                                                                                                                                if ($num==0)
                                                                                                                                                                {
                                                                                                                                                                    $query="INSERT INTO thirdparty_price_tb(thirdparty_price_car_mode_id,thirdparty_price_fieldcompany_id,thirdparty_price_mode_id,thirdparty_price_cargroup_id, thirdparty_price_forcedthird, thirdparty_price_disc, thirdparty_price_yadak, thirdparty_price_passenger)
                                                                                VALUES ( $thirdparty_price_car_mode_id,$thirdparty_price_fieldcompany_id,$thirdparty_price_mode_id,$thirdparty_price_cargroup_id,'$thirdparty_price_forcedthird', '$thirdparty_price_disc', '$thirdparty_price_yadak', '$thirdparty_price_passenger');";

                                                                                                                                                                    $result=$this->B_db->run_query_put($query);
                                                                                                                                                                    $thirdparty_price_id=$this->db->insert_id();
                                                                                                                                                                    echo json_encode(array('result'=>"ok"
                                                                                                                                                                    ,"data"=>array('thirdparty_price_id'=>$thirdparty_price_id)
                                                                                                                                                                    ,'desc'=>'قیمت بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                                }else{
                                                                                                                                                                    $carmode=$result[0];
                                                                                                                                                                    echo json_encode(array('result'=>"error"
                                                                                                                                                                    ,"data"=>array('thirdparty_price_id'=>$carmode['thirdparty_price_id'])
                                                                                                                                                                    ,'desc'=>'قیمت بیمه نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                                }
//***************************************************************************************************************
                                                                                                                                                            }else{
                                                                                                                                                                echo json_encode(array('result'=>$employeetoken[0]
                                                                                                                                                                ,"data"=>$employeetoken[1]
                                                                                                                                                                ,'desc'=>$employeetoken[2]));

                                                                                                                                                            }






                                                                                                                                                        }
                                                                                                                                                        else
                                                                                                                                                            if ($command=="get_thirdparty_price")
                                                                                                                                                            {
//************************************************************************;****************************************

                                                                                                                                                                $query="select * from thirdparty_price_tb,fieldcompany_tb,cargroup_tb,company_tb,carmode_tb,thirdparty_mode_tb where thirdparty_price_fieldcompany_id=fieldcompany_id 
                                                                                                         AND  thirdparty_price_mode_id=thirdparty_mode_id  
AND thirdparty_price_cargroup_id=cargroup_id
AND thirdparty_price_car_mode_id=carmode_id
AND fieldcompany_company_id=company_id
ORDER BY thirdparty_price_id ASC";
                                                                                                                                                                $result = $this->B_db->run_query($query);
                                                                                                                                                                $output =array();
                                                                                                                                                                foreach($result as $row)
                                                                                                                                                                {
                                                                                                                                                                    $record=array();
                                                                                                                                                                    $record['thirdparty_price_id']=$row['thirdparty_price_id'];
                                                                                                                                                                    $record['thirdparty_price_fieldcompany_id']=$row['thirdparty_price_fieldcompany_id'];
                                                                                                                                                                    $record['thirdparty_price_mode_id']=$row['thirdparty_price_mode_id'];
                                                                                                                                                                    $record['thirdparty_mode_desc']=$row['thirdparty_mode_desc'];
                                                                                                                                                                    $record['company_name']=$row['company_name'];
                                                                                                                                                                    $record['company_logo_url']=IMGADD.$row['company_logo_url'];
                                                                                                                                                                    $record['cargroup_name']=$row['cargroup_name'];
                                                                                                                                                                    $record['carmode_name']=$row['carmode_name'];
                                                                                                                                                                    $record['thirdparty_price_cargroup_id']=$row['thirdparty_price_cargroup_id'];
                                                                                                                                                                    $record['thirdparty_price_car_mode_id']=$row['thirdparty_price_car_mode_id'];
                                                                                                                                                                    $record['thirdparty_price_forcedthird']=$row['thirdparty_price_forcedthird'];
                                                                                                                                                                    $record['thirdparty_price_passenger']=$row['thirdparty_price_passenger'];
                                                                                                                                                                    $record['thirdparty_price_yadak']=$row['thirdparty_price_yadak'];
                                                                                                                                                                    $record['thirdparty_price_disc']=$row['thirdparty_price_disc'];
                                                                                                                                                                    $record['thirdparty_price_deactive']=$row['thirdparty_price_deactive'];
                                                                                                                                                                    $output[]=$record;
                                                                                                                                                                }
                                                                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                                                                ,"data"=>$output
                                                                                                                                                                ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                                                                                            }
                                                                                                                                                            else
                                                                                                                                                                if ($command=="delete_thirdparty_price")
                                                                                                                                                                {
                                                                                                                                                                    $thirdparty_price_id=$this->post('thirdparty_price_id');

                                                                                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','thirdparty');
                                                                                                                                                                    if($employeetoken[0]=='ok')
                                                                                                                                                                    {
//************************************************************************;****************************************
                                                                                                                                                                        $user_id=$employeetoken[1];

                                                                                                                                                                        $query="DELETE FROM thirdparty_price_tb  where thirdparty_price_id=".$thirdparty_price_id."";
                                                                                                                                                                        $result = $this->B_db->run_query_put($query);
                                                                                                                                                                        if($result){echo json_encode(array('result'=>"ok"
                                                                                                                                                                        ,"data"=>$output
                                                                                                                                                                        ,'desc'=>'قیمت بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                                        }else{
                                                                                                                                                                            echo json_encode(array('result'=>"error"
                                                                                                                                                                            ,"data"=>$output
                                                                                                                                                                            ,'desc'=>'قیمت بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                                        }
//***************************************************************************************************************
                                                                                                                                                                    }else{
                                                                                                                                                                        echo json_encode(array('result'=>$employeetoken[0]
                                                                                                                                                                        ,"data"=>$employeetoken[1]
                                                                                                                                                                        ,'desc'=>$employeetoken[2]));

                                                                                                                                                                    }

                                                                                                                                                                }
                                                                                                                                                                else
                                                                                                                                                                    if ($command=="modify_thirdparty_price")
                                                                                                                                                                    {
                                                                                                                                                                        $thirdparty_price_id=$this->post('thirdparty_price_id');

                                                                                                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','thirdparty');
                                                                                                                                                                        if($employeetoken[0]=='ok')
                                                                                                                                                                        {
//*****************************************************************************************

                                                                                                                                                                            $query="UPDATE thirdparty_price_tb SET ";

                                                                                                                                                                            if(isset($_REQUEST['thirdparty_price_forcedthird'])){
                                                                                                                                                                                $thirdparty_price_forcedthird=$this->post('thirdparty_price_forcedthird');
                                                                                                                                                                                $query.="thirdparty_price_forcedthird='".$thirdparty_price_forcedthird."'";}

                                                                                                                                                                            if(isset($_REQUEST['thirdparty_price_passenger'])&&(isset($_REQUEST['thirdparty_price_forcedthird']))){ $query.=",";}
                                                                                                                                                                            if(isset($_REQUEST['thirdparty_price_passenger'])){
                                                                                                                                                                                $thirdparty_price_passenger=$this->post('thirdparty_price_passenger');
                                                                                                                                                                                $query.="thirdparty_price_passenger='".$thirdparty_price_passenger."'";}

                                                                                                                                                                            if(isset($_REQUEST['thirdparty_price_disc'])&&(isset($_REQUEST['thirdparty_price_passenger'])||isset($_REQUEST['thirdparty_price_forcedthird']))){ $query.=",";}
                                                                                                                                                                            if(isset($_REQUEST['thirdparty_price_disc'])){
                                                                                                                                                                                $thirdparty_price_disc=$this->post('thirdparty_price_disc');
                                                                                                                                                                                $query.="thirdparty_price_disc='".$thirdparty_price_disc."'";}


                                                                                                                                                                            if(isset($_REQUEST['thirdparty_price_deactive'])&&(isset($_REQUEST['thirdparty_price_disc'])||isset($_REQUEST['thirdparty_price_passenger'])||isset($_REQUEST['thirdparty_price_forcedthird']))){ $query.=",";}
                                                                                                                                                                            if(isset($_REQUEST['thirdparty_price_deactive'])){
                                                                                                                                                                                $thirdparty_price_deactive=$this->post('thirdparty_price_deactive');
                                                                                                                                                                                $query.="thirdparty_price_deactive=".$thirdparty_price_deactive."";}

                                                                                                                                                                            if(isset($_REQUEST['thirdparty_price_yadak'])&&(isset($_REQUEST['thirdparty_price_deactive'])||isset($_REQUEST['thirdparty_price_disc'])||isset($_REQUEST['thirdparty_price_passenger'])||isset($_REQUEST['thirdparty_price_forcedthird']))){ $query.=",";}
                                                                                                                                                                            if(isset($_REQUEST['thirdparty_price_yadak'])){
                                                                                                                                                                                $thirdparty_price_yadak=$this->post('thirdparty_price_yadak');
                                                                                                                                                                                $query.="thirdparty_price_yadak='".$thirdparty_price_yadak."' ";}


                                                                                                                                                                            $query.=" where thirdparty_price_id=".$thirdparty_price_id;

                                                                                                                                                                            $result=$this->B_db->run_query_put($query);
                                                                                                                                                                            if($result){
                                                                                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                                                                                ,"data"=>""
                                                                                                                                                                                ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                                            }else{
                                                                                                                                                                                echo json_encode(array('result'=>"error"
                                                                                                                                                                                ,"data"=>$query
                                                                                                                                                                                ,'desc'=>'تغییرات انجام نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);


                                                                                                                                                                            }
//***************************************************************************************************************
                                                                                                                                                                        }else{
                                                                                                                                                                            echo json_encode(array('result'=>$employeetoken[0]
                                                                                                                                                                            ,"data"=>$employeetoken[1]
                                                                                                                                                                            ,'desc'=>$employeetoken[2]));

                                                                                                                                                                        }



                                                                                                                                                                    }

//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//***************************************************************************************************************
                                                                                                                                                                    else
                                                                                                                                                                        if ($command=="add_extrafinancial")
                                                                                                                                                                        {
                                                                                                                                                                            $extrafinancial_thirdparty_price_id=$this->post('extrafinancial_thirdparty_price_id');
                                                                                                                                                                            $extrafinancial_thirdparty_coverage_id=$this->post('extrafinancial_thirdparty_coverage_id');
                                                                                                                                                                            $extrafinancial_extrafinancial_calmode_id=$this->post('extrafinancial_extrafinancial_calmode_id');
                                                                                                                                                                            $extrafinancial_price=$this->post('extrafinancial_price');
                                                                                                                                                                            $extrafinancial_percent=$this->post('extrafinancial_percent');

                                                                                                                                                                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','thirdparty');
                                                                                                                                                                            if($employeetoken[0]=='ok')
                                                                                                                                                                            {
//****************************************************************************************************************
                                                                                                                                                                                $query="select * from extrafinancial_tb where extrafinancial_thirdparty_coverage_id=".$extrafinancial_thirdparty_coverage_id." AND extrafinancial_thirdparty_price_id=".$extrafinancial_thirdparty_price_id."";
                                                                                                                                                                                $result=$this->B_db->run_query($query);
                                                                                                                                                                                $num=count($result[0]);
                                                                                                                                                                                if ($num==0)
                                                                                                                                                                                {
                                                                                                                                                                                    $query="INSERT INTO extrafinancial_tb(extrafinancial_thirdparty_price_id,extrafinancial_thirdparty_coverage_id,extrafinancial_extrafinancial_calmode_id, extrafinancial_price,extrafinancial_percent)
VALUES ( $extrafinancial_thirdparty_price_id,$extrafinancial_thirdparty_coverage_id,$extrafinancial_extrafinancial_calmode_id, '$extrafinancial_price', $extrafinancial_percent);";

                                                                                                                                                                                    $result=$this->B_db->run_query_put($query);
                                                                                                                                                                                    $extrafinancial_id=$this->db->insert_id();
                                                                                                                                                                                    echo json_encode(array('result'=>"ok"
                                                                                                                                                                                    ,"data"=>array('extrafinancial_id'=>$extrafinancial_id)
                                                                                                                                                                                    ,'desc'=>'پوشش مازاد مالی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                                                }else{
                                                                                                                                                                                    $carmode=$result[0];
                                                                                                                                                                                    echo json_encode(array('result'=>"error"
                                                                                                                                                                                    ,"data"=>array('extrafinancial_id'=>$carmode['extrafinancial_id'])
                                                                                                                                                                                    ,'desc'=>' پوشش مازاد مالی نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                                                }
//***************************************************************************************************************
                                                                                                                                                                            }else{
                                                                                                                                                                                echo json_encode(array('result'=>$employeetoken[0]
                                                                                                                                                                                ,"data"=>$employeetoken[1]
                                                                                                                                                                                ,'desc'=>$employeetoken[2]));

                                                                                                                                                                            }


                                                                                                                                                                        }
                                                                                                                                                                        else
                                                                                                                                                                            if ($command=="get_extrafinancial")
                                                                                                                                                                            {
                                                                                                                                                                                $thirdparty_price_id=$this->post('thirdparty_price_id');

//************************************************************************;****************************************

                                                                                                                                                                                $query="select * from extrafinancial_tb,thirdparty_coverage_tb,extrafinancial_calmode_tb where thirdparty_coverage_deactive=0 AND extrafinancial_calmode_id=extrafinancial_extrafinancial_calmode_id AND thirdparty_coverage_id=extrafinancial_thirdparty_coverage_id AND extrafinancial_thirdparty_price_id=$thirdparty_price_id  ORDER BY extrafinancial_id ASC";
                                                                                                                                                                                $result = $this->B_db->run_query($query);
                                                                                                                                                                                $output =array();
                                                                                                                                                                                foreach($result as $row)
                                                                                                                                                                                {
                                                                                                                                                                                    $record=array();
                                                                                                                                                                                    $record['extrafinancial_id']=$row['extrafinancial_id'];
                                                                                                                                                                                    $record['extrafinancial_thirdparty_price_id']=$row['extrafinancial_thirdparty_price_id'];
                                                                                                                                                                                    $record['extrafinancial_thirdparty_coverage_id']=$row['extrafinancial_thirdparty_coverage_id'];
                                                                                                                                                                                    $record['extrafinancial_extrafinancial_calmode_id']=$row['extrafinancial_extrafinancial_calmode_id'];
                                                                                                                                                                                    $record['extrafinancial_calmode_name']=$row['extrafinancial_calmode_name'];
                                                                                                                                                                                    $record['thirdparty_coverage_desc']=$row['thirdparty_coverage_desc'];
                                                                                                                                                                                    $record['thirdparty_coverage_price']=$row['thirdparty_coverage_price'];
                                                                                                                                                                                    $record['extrafinancial_price']=$row['extrafinancial_price'];
                                                                                                                                                                                    $record['extrafinancial_percent']=$row['extrafinancial_percent'];
                                                                                                                                                                                    $output[]=$record;
                                                                                                                                                                                }
                                                                                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                                                                                ,"data"=>$output
                                                                                                                                                                                ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                                                                                                            }
                                                                                                                                                                            else
                                                                                                                                                                                if ($command=="delete_extrafinancial")
                                                                                                                                                                                {
                                                                                                                                                                                    $extrafinancial_id=$this->post('extrafinancial_id');

                                                                                                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','thirdparty');
                                                                                                                                                                                    if($employeetoken[0]=='ok')
                                                                                                                                                                                    {
//************************************************************************;****************************************
                                                                                                                                                                                        $user_id=$employeetoken[1];

                                                                                                                                                                                        $query="DELETE FROM extrafinancial_tb  where extrafinancial_id=".$extrafinancial_id."";
                                                                                                                                                                                        $result = $this->B_db->run_query_put($query);
                                                                                                                                                                                        if($result){echo json_encode(array('result'=>"ok"
                                                                                                                                                                                        ,"data"=>$output
                                                                                                                                                                                        ,'desc'=>'قیمت بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                                                        }else{
                                                                                                                                                                                            echo json_encode(array('result'=>"error"
                                                                                                                                                                                            ,"data"=>$output
                                                                                                                                                                                            ,'desc'=>'قیمت بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                                                        }
//***************************************************************************************************************
                                                                                                                                                                                    }else{
                                                                                                                                                                                        echo json_encode(array('result'=>$employeetoken[0]
                                                                                                                                                                                        ,"data"=>$employeetoken[1]
                                                                                                                                                                                        ,'desc'=>$employeetoken[2]));

                                                                                                                                                                                    }

                                                                                                                                                                                }
                                                                                                                                                                                else



                                                                                                                                                                                    if ($command=="modify_extrafinancial")
                                                                                                                                                                                    {
                                                                                                                                                                                        $extrafinancial_id=$this->post('extrafinancial_id');

                                                                                                                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','thirdparty');
                                                                                                                                                                                        if($employeetoken[0]=='ok')
                                                                                                                                                                                        {
//*****************************************************************************************

                                                                                                                                                                                            $query="UPDATE extrafinancial_tb SET ";


                                                                                                                                                                                            if(isset($_REQUEST['extrafinancial_price'])){
                                                                                                                                                                                                $extrafinancial_price=$this->post('extrafinancial_price');
                                                                                                                                                                                                $query.="extrafinancial_price='".$extrafinancial_price."'";}

                                                                                                                                                                                            if(isset($_REQUEST['extrafinancial_percent'])&&(isset($_REQUEST['extrafinancial_price']))){ $query.=",";}
                                                                                                                                                                                            if(isset($_REQUEST['extrafinancial_percent'])){
                                                                                                                                                                                                $extrafinancial_percent=$this->post('extrafinancial_percent');
                                                                                                                                                                                                $query.="extrafinancial_percent=".$extrafinancial_percent." ";}

                                                                                                                                                                                            if(isset($_REQUEST['extrafinancial_extrafinancial_calmode_id'])&&(isset($_REQUEST['extrafinancial_percent'])||isset($_REQUEST['extrafinancial_price']))){ $query.=",";}
                                                                                                                                                                                            if(isset($_REQUEST['extrafinancial_extrafinancial_calmode_id'])){
                                                                                                                                                                                                $extrafinancial_extrafinancial_calmode_id=$this->post('extrafinancial_extrafinancial_calmode_id');
                                                                                                                                                                                                $query.="extrafinancial_extrafinancial_calmode_id=".$extrafinancial_extrafinancial_calmode_id." ";}


                                                                                                                                                                                            $query.=" where extrafinancial_id=".$extrafinancial_id;

                                                                                                                                                                                            $result=$this->B_db->run_query_put($query);
                                                                                                                                                                                            if($result){
                                                                                                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                                                                                                ,"data"=>''
                                                                                                                                                                                                ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                                                            }
//***************************************************************************************************************
                                                                                                                                                                                        }else{
                                                                                                                                                                                            echo json_encode(array('result'=>$employeetoken[0]
                                                                                                                                                                                            ,"data"=>$employeetoken[1]
                                                                                                                                                                                            ,'desc'=>$employeetoken[2]));

                                                                                                                                                                                        }



                                                                                                                                                                                    }


//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//***************************************************************************************************************
                                                                                                                                                                                    else
                                                                                                                                                                                        if ($command=="add_price_time")
                                                                                                                                                                                        {
                                                                                                                                                                                            $thirdpartyprice_time_thirdparty_price_id=$this->post('thirdpartyprice_time_thirdparty_price_id');
                                                                                                                                                                                            $thirdpartyprice_time_time_id=$this->post('thirdpartyprice_time_time_id');
                                                                                                                                                                                            $thirdpartyprice_time_percent=$this->post('thirdpartyprice_time_percent');

                                                                                                                                                                                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','thirdparty');
                                                                                                                                                                                            if($employeetoken[0]=='ok')
                                                                                                                                                                                            {
//****************************************************************************************************************
                                                                                                                                                                                                $query="select * from thirdpartyprice_time_tb where thirdpartyprice_time_time_id=".$thirdpartyprice_time_time_id." AND thirdpartyprice_time_thirdparty_price_id=".$thirdpartyprice_time_thirdparty_price_id."";
                                                                                                                                                                                                $result=$this->B_db->run_query($query);
                                                                                                                                                                                                $num=count($result[0]);
                                                                                                                                                                                                if ($num==0)
                                                                                                                                                                                                {
                                                                                                                                                                                                    $query="INSERT INTO thirdpartyprice_time_tb(thirdpartyprice_time_thirdparty_price_id,thirdpartyprice_time_time_id, thirdpartyprice_time_percent)
VALUES ( $thirdpartyprice_time_thirdparty_price_id,$thirdpartyprice_time_time_id, '$thirdpartyprice_time_percent');";

                                                                                                                                                                                                    $result=$this->B_db->run_query_put($query);
                                                                                                                                                                                                    $thirdpartyprice_time_id=$this->db->insert_id();
                                                                                                                                                                                                    echo json_encode(array('result'=>"ok"
                                                                                                                                                                                                    ,"data"=>array('thirdpartyprice_time_id'=>$thirdpartyprice_time_id)
                                                                                                                                                                                                    ,'desc'=>'مدت زمان بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                                                                }else{
                                                                                                                                                                                                    $carmode=$result[0];
                                                                                                                                                                                                    echo json_encode(array('result'=>"error"
                                                                                                                                                                                                    ,"data"=>array('thirdpartyprice_time_id'=>$carmode['thirdpartyprice_time_id'])
                                                                                                                                                                                                    ,'desc'=>' مدت زمان بیمه نامه نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                                                                }
//***************************************************************************************************************
                                                                                                                                                                                            }else{
                                                                                                                                                                                                echo json_encode(array('result'=>$employeetoken[0]
                                                                                                                                                                                                ,"data"=>$employeetoken[1]
                                                                                                                                                                                                ,'desc'=>$employeetoken[2]));

                                                                                                                                                                                            }






                                                                                                                                                                                        }
                                                                                                                                                                                        else
                                                                                                                                                                                            if ($command=="get_price_time")
                                                                                                                                                                                            {
                                                                                                                                                                                                $thirdparty_price_id=$this->post('thirdparty_price_id');

//************************************************************************;****************************************

                                                                                                                                                                                                $query="select * from thirdpartyprice_time_tb,thirdparty_time_tb where thirdparty_time_id=thirdpartyprice_time_time_id AND thirdpartyprice_time_thirdparty_price_id=$thirdparty_price_id  ORDER BY thirdpartyprice_time_id ASC";
                                                                                                                                                                                                $result = $this->B_db->run_query($query);
                                                                                                                                                                                                $output =array();
                                                                                                                                                                                                foreach($result as $row)
                                                                                                                                                                                                {
                                                                                                                                                                                                    $record=array();
                                                                                                                                                                                                    $record['thirdpartyprice_time_id']=$row['thirdpartyprice_time_id'];
                                                                                                                                                                                                    $record['thirdpartyprice_time_thirdparty_price_id']=$row['thirdpartyprice_time_thirdparty_price_id'];
                                                                                                                                                                                                    $record['thirdpartyprice_time_time_id']=$row['thirdpartyprice_time_time_id'];
                                                                                                                                                                                                    $record['thirdparty_time_desc']=$row['thirdparty_time_desc'];
                                                                                                                                                                                                    $record['thirdparty_time_percent']=$row['thirdparty_time_percent'];
                                                                                                                                                                                                    $record['thirdpartyprice_time_percent']=$row['thirdpartyprice_time_percent'];
                                                                                                                                                                                                    $output[]=$record;
                                                                                                                                                                                                }
                                                                                                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                                                                                                ,"data"=>$output
                                                                                                                                                                                                ,'desc'=>'مدت زمان بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                                                                                                                            }
                                                                                                                                                                                            else
                                                                                                                                                                                                if ($command=="delete_price_time")
                                                                                                                                                                                                {
                                                                                                                                                                                                    $thirdpartyprice_time_id=$this->post('thirdpartyprice_time_id');

                                                                                                                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','thirdparty');
                                                                                                                                                                                                    if($employeetoken[0]=='ok')
                                                                                                                                                                                                    {
//************************************************************************;****************************************
                                                                                                                                                                                                        $user_id=$employeetoken[1];

                                                                                                                                                                                                        $query="DELETE FROM thirdpartyprice_time_tb  where thirdpartyprice_time_id=".$thirdpartyprice_time_id."";
                                                                                                                                                                                                        $result = $this->B_db->run_query_put($query);
                                                                                                                                                                                                        if($result){echo json_encode(array('result'=>"ok"
                                                                                                                                                                                                        ,"data"=>$output
                                                                                                                                                                                                        ,'desc'=>'مدت زمان بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                                                                        }else{
                                                                                                                                                                                                            echo json_encode(array('result'=>"error"
                                                                                                                                                                                                            ,"data"=>$output
                                                                                                                                                                                                            ,'desc'=>'مدت زمان بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                                                                        }
//***************************************************************************************************************
                                                                                                                                                                                                    }else{
                                                                                                                                                                                                        echo json_encode(array('result'=>$employeetoken[0]
                                                                                                                                                                                                        ,"data"=>$employeetoken[1]
                                                                                                                                                                                                        ,'desc'=>$employeetoken[2]));

                                                                                                                                                                                                    }

                                                                                                                                                                                                }
                                                                                                                                                                                                else



                                                                                                                                                                                                    if ($command=="modify_price_time")
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $thirdpartyprice_time_id=$this->post('thirdpartyprice_time_id');

                                                                                                                                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','thirdparty');
                                                                                                                                                                                                        if($employeetoken[0]=='ok')
                                                                                                                                                                                                        {
//*****************************************************************************************

                                                                                                                                                                                                            $query="UPDATE thirdpartyprice_time_tb SET ";


                                                                                                                                                                                                            if(isset($_REQUEST['thirdpartyprice_time_percent'])){
                                                                                                                                                                                                                $thirdpartyprice_time_percent=$this->post('thirdpartyprice_time_percent');
                                                                                                                                                                                                                $query.="thirdpartyprice_time_percent='".$thirdpartyprice_time_percent."'";}

                                                                                                                                                                                                            $query.="where thirdpartyprice_time_id=".$thirdpartyprice_time_id;

                                                                                                                                                                                                            $result=$this->B_db->run_query_put($query);
                                                                                                                                                                                                            if($result){
                                                                                                                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                                                                                                                ,"data"=>""
                                                                                                                                                                                                                ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                                                                            }
//***************************************************************************************************************
                                                                                                                                                                                                        }else{
                                                                                                                                                                                                            echo json_encode(array('result'=>$employeetoken[0]
                                                                                                                                                                                                            ,"data"=>$employeetoken[1]
                                                                                                                                                                                                            ,'desc'=>$employeetoken[2]));

                                                                                                                                                                                                        }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    //************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//***************************************************************************************************************
                                                                                                                                                                                                    else
                                                                                                                                                                                                        if ($command=="add_price_usefor")
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $thirdpartyprice_usefor_thirdparty_price_id=$this->post('thirdpartyprice_usefor_thirdparty_price_id');
                                                                                                                                                                                                            $thirdpartyprice_usefor_usefor_id=$this->post('thirdpartyprice_usefor_usefor_id');
                                                                                                                                                                                                            $thirdpartyprice_usefor_percent=$this->post('thirdpartyprice_usefor_percent');

                                                                                                                                                                                                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','thirdparty');
                                                                                                                                                                                                            if($employeetoken[0]=='ok')
                                                                                                                                                                                                            {
//****************************************************************************************************************
                                                                                                                                                                                                                $query="select * from thirdpartyprice_usefor_tb where thirdpartyprice_usefor_usefor_id=".$thirdpartyprice_usefor_usefor_id." AND thirdpartyprice_usefor_thirdparty_price_id=".$thirdpartyprice_usefor_thirdparty_price_id."";
                                                                                                                                                                                                                $result=$this->B_db->run_query($query);
                                                                                                                                                                                                                $num=count($result[0]);
                                                                                                                                                                                                                if ($num==0)
                                                                                                                                                                                                                {
                                                                                                                                                                                                                    $query="INSERT INTO thirdpartyprice_usefor_tb(thirdpartyprice_usefor_thirdparty_price_id,thirdpartyprice_usefor_usefor_id, thirdpartyprice_usefor_percent)
VALUES ( $thirdpartyprice_usefor_thirdparty_price_id,$thirdpartyprice_usefor_usefor_id, '$thirdpartyprice_usefor_percent');";

                                                                                                                                                                                                                    $result=$this->B_db->run_query_put($query);
                                                                                                                                                                                                                    $thirdpartyprice_usefor_id=$this->db->insert_id();
                                                                                                                                                                                                                    echo json_encode(array('result'=>"ok"
                                                                                                                                                                                                                    ,"data"=>array('thirdpartyprice_usefor_id'=>$thirdpartyprice_usefor_id)
                                                                                                                                                                                                                    ,'desc'=>'مدت زمان بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                                                                                }else{
                                                                                                                                                                                                                    $carmode=$result[0];
                                                                                                                                                                                                                    echo json_encode(array('result'=>"error"
                                                                                                                                                                                                                    ,"data"=>array('thirdpartyprice_usefor_id'=>$carmode['thirdpartyprice_usefor_id'])
                                                                                                                                                                                                                    ,'desc'=>' مدت زمان بیمه نامه نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                                                                                }
//***************************************************************************************************************
                                                                                                                                                                                                            }else{
                                                                                                                                                                                                                echo json_encode(array('result'=>$employeetoken[0]
                                                                                                                                                                                                                ,"data"=>$employeetoken[1]
                                                                                                                                                                                                                ,'desc'=>$employeetoken[2]));

                                                                                                                                                                                                            }






                                                                                                                                                                                                        }
                                                                                                                                                                                                        else
                                                                                                                                                                                                            if ($command=="get_price_usefor")
                                                                                                                                                                                                            {
                                                                                                                                                                                                                $thirdparty_price_id=$this->post('thirdparty_price_id');

//************************************************************************;****************************************

                                                                                                                                                                                                                $query="select * from thirdpartyprice_usefor_tb,thirdparty_usefor_tb where thirdparty_usefor_id=thirdpartyprice_usefor_usefor_id AND thirdpartyprice_usefor_thirdparty_price_id=$thirdparty_price_id  ORDER BY thirdpartyprice_usefor_id ASC";
                                                                                                                                                                                                                $result = $this->B_db->run_query($query);
                                                                                                                                                                                                                $output =array();
                                                                                                                                                                                                                foreach($result as $row)
                                                                                                                                                                                                                {
                                                                                                                                                                                                                    $record=array();
                                                                                                                                                                                                                    $record['thirdpartyprice_usefor_id']=$row['thirdpartyprice_usefor_id'];
                                                                                                                                                                                                                    $record['thirdpartyprice_usefor_thirdparty_price_id']=$row['thirdpartyprice_usefor_thirdparty_price_id'];
                                                                                                                                                                                                                    $record['thirdpartyprice_usefor_usefor_id']=$row['thirdpartyprice_usefor_usefor_id'];
                                                                                                                                                                                                                    $record['thirdparty_usefor_desc']=$row['thirdparty_usefor_desc'];
                                                                                                                                                                                                                    $record['thirdparty_usefor_percent']=$row['thirdparty_usefor_percent'];
                                                                                                                                                                                                                    $record['thirdpartyprice_usefor_percent']=$row['thirdpartyprice_usefor_percent'];
                                                                                                                                                                                                                    $output[]=$record;
                                                                                                                                                                                                                }
                                                                                                                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                                                                                                                ,"data"=>$output
                                                                                                                                                                                                                ,'desc'=>'مدت زمان بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                                                                                                                                            }
                                                                                                                                                                                                            else
                                                                                                                                                                                                                if ($command=="delete_price_usefor")
                                                                                                                                                                                                                {
                                                                                                                                                                                                                    $thirdpartyprice_usefor_id=$this->post('thirdpartyprice_usefor_id');

                                                                                                                                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','thirdparty');
                                                                                                                                                                                                                    if($employeetoken[0]=='ok')
                                                                                                                                                                                                                    {
//************************************************************************;****************************************
                                                                                                                                                                                                                        $user_id=$employeetoken[1];

                                                                                                                                                                                                                        $query="DELETE FROM thirdpartyprice_usefor_tb  where thirdpartyprice_usefor_id=".$thirdpartyprice_usefor_id."";
                                                                                                                                                                                                                        $result = $this->B_db->run_query_put($query);
                                                                                                                                                                                                                        if($result){echo json_encode(array('result'=>"ok"
                                                                                                                                                                                                                        ,"data"=>$output
                                                                                                                                                                                                                        ,'desc'=>'مدت زمان بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                                                                                        }else{
                                                                                                                                                                                                                            echo json_encode(array('result'=>"error"
                                                                                                                                                                                                                            ,"data"=>$output
                                                                                                                                                                                                                            ,'desc'=>'مدت زمان بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                                                                                        }
//***************************************************************************************************************
                                                                                                                                                                                                                    }else{
                                                                                                                                                                                                                        echo json_encode(array('result'=>$employeetoken[0]
                                                                                                                                                                                                                        ,"data"=>$employeetoken[1]
                                                                                                                                                                                                                        ,'desc'=>$employeetoken[2]));

                                                                                                                                                                                                                    }

                                                                                                                                                                                                                }
                                                                                                                                                                                                                else



                                                                                                                                                                                                                    if ($command=="modify_price_usefor")
                                                                                                                                                                                                                    {
                                                                                                                                                                                                                        $thirdpartyprice_usefor_id=$this->post('thirdpartyprice_usefor_id');

                                                                                                                                                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','thirdparty');
                                                                                                                                                                                                                        if($employeetoken[0]=='ok')
                                                                                                                                                                                                                        {
//*****************************************************************************************

                                                                                                                                                                                                                            $query="UPDATE thirdpartyprice_usefor_tb SET ";


                                                                                                                                                                                                                            if(isset($_REQUEST['thirdpartyprice_usefor_percent'])){
                                                                                                                                                                                                                                $thirdpartyprice_usefor_percent=$this->post('thirdpartyprice_usefor_percent');
                                                                                                                                                                                                                                $query.="thirdpartyprice_usefor_percent='".$thirdpartyprice_usefor_percent."'";}

                                                                                                                                                                                                                            $query.="where thirdpartyprice_usefor_id=".$thirdpartyprice_usefor_id;

                                                                                                                                                                                                                            $result=$this->B_db->run_query_put($query);
                                                                                                                                                                                                                            if($result){
                                                                                                                                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                                                                                                                                ,"data"=>""
                                                                                                                                                                                                                                ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                                                                                            }
//***************************************************************************************************************
                                                                                                                                                                                                                        }else{
                                                                                                                                                                                                                            echo json_encode(array('result'=>$employeetoken[0]
                                                                                                                                                                                                                            ,"data"=>$employeetoken[1]
                                                                                                                                                                                                                            ,'desc'=>$employeetoken[2]));

                                                                                                                                                                                                                        }
                                                                                                                                                                                                                    }
        //***************************************************************************************************************
//***************************************************************************************************************
//***************************************************************************************************************
//***************************************************************************************************************


    }


}
