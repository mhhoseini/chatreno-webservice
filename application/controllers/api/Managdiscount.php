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
class Managdiscount extends REST_Controller {

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
        if ($command=="add_managdiscount")
        {
            $managdiscount_company_id=$this->post('managdiscount_company_id') ;
            $managdiscount_fieldinsurance_id=$this->post('managdiscount_fieldinsurance_id') ;
            $managdiscounts_mode=$this->post('managdiscounts_mode') ;
            $managdiscount_amount=$this->post('managdiscount_amount') ;
            $managdiscount_max_forone=$this->post('managdiscount_max_forone') ;
            $managdiscount_max_all=$this->post('managdiscount_max_all') ;
            $managdiscount_date_start=$this->post('managdiscount_date_start') ;
            $managdiscount_date_end=$this->post('managdiscount_date_end') ;
            $managdiscount_desc=$this->post('managdiscount_desc') ;



            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','managdiscount');
            if($employeetoken[0]=='ok')
            {
//**************************************************************************************************************
                $query="select * from managdiscount_tb where managdiscount_deactive=0 AND  managdiscount_company_id='".$managdiscount_company_id."' AND managdiscount_fieldinsurance_id='".$managdiscount_fieldinsurance_id."'";
                $result=$this->B_db->run_query($query);
                $num=count($result[0]);
                if ($num==0)
                {
                    $query="INSERT INTO managdiscount_tb(   managdiscount_company_id,  managdiscount_fieldinsurance_id,   managdiscounts_mode,  managdiscount_amount,    managdiscount_max_forone ,   managdiscount_max_all,   managdiscount_date_start,managdiscount_date_end,managdiscount_desc)
	                                VALUES ($managdiscount_company_id,$managdiscount_fieldinsurance_id,'$managdiscounts_mode' , '$managdiscount_amount' ,  '$managdiscount_max_forone'  , '$managdiscount_max_all' ,'$managdiscount_date_start' ,'$managdiscount_date_end','$managdiscount_desc');";

                    $result=$this->B_db->run_query_put($query);
                    $managdiscount_id=$this->db->insert_id();
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('managdiscount_id'=>$managdiscount_id)
                    ,'desc'=>'تخفیف مدیریتی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    $carmode=$result[0];
                    echo json_encode(array('result'=>"error"
                    ,"data"=>array('managdiscount_id'=>$carmode['managdiscount_id'])
                    ,'desc'=>'تخفیف مدیریتی تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
//***************************************************************************************************************
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }




        }
        else
            if ($command=="get_managdiscount")
            {

                $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','managdiscount');

                if($employeetoken[0]=='ok')
                {
//************************************************************************;****************************************

                    $query="select * from managdiscount_tb,company_tb,fieldinsurance_tb where company_id=managdiscount_company_id AND fieldinsurance_id=managdiscount_fieldinsurance_id AND ";
                    if(isset($_REQUEST['filter1'])){
                        $filter1=$this->post('filter1');
                        $query.=$filter1;}else{$query.=" 1=1 ";}
                    $query.=" AND ";
                    if(isset($_REQUEST['filter2'])){
                        $filter2=$this->post('filter2');
                        $query.=$filter2;}else{$query.=" 1=1 ";}
                    $query.=" AND ";
                    if(isset($_REQUEST['filter3'])){
                        $filter3=$this->post('filter3');
                        $query.=$filter3;}else{$query.=" 1=1 ";}
                    $query.=" ORDER BY managdiscount_id ASC";

                    $result = $this->B_db->run_query($query);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record=array();
                        $record['company_name']=$row['company_name'];
                        $record['company_logo_url']=IMGADD.$row['company_logo_url'];

                        $record['fieldinsurance']=$row['fieldinsurance'];
                        $record['fieldinsurance_fa']=$row['fieldinsurance_fa'];
                        $record['fieldinsurance_logo_url']=IMGADD.$row['fieldinsurance_logo_url'];

                        $record['managdiscount_id']=$row['managdiscount_id'];
                        $record['managdiscount_company_id']=$row['managdiscount_company_id'];
                        $record['managdiscount_fieldinsurance_id']=$row['managdiscount_fieldinsurance_id'];
                        $record['managdiscounts_mode']=$row['managdiscounts_mode'];
                        $record['managdiscount_amount']=$row['managdiscount_amount'];
                        $record['managdiscount_max_forone']=$row['managdiscount_max_forone'];
                        $record['managdiscount_max_all']=$row['managdiscount_max_all'];
                        $record['managdiscount_date_start']=$row['managdiscount_date_start'];
                        $record['managdiscount_date_end']=$row['managdiscount_date_end'];
                        $record['managdiscount_desc']=$row['managdiscount_desc'];
                        $record['managdiscount_deactive']=$row['managdiscount_deactive'];
                        $query1='SELECT COALESCE(SUM(`managdiscount_use_amount`),0) AS value_sum FROM `managdiscount_use_tb` WHERE managdiscount_mngdiscnt_id='.$row['managdiscount_id'];
                        $result1 = $this->B_db->run_query($query1);
                        $row2 = $result1[0];
                        $record['managdiscount_sum']=$row2['value_sum'];
                        $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'مشحصات تخفیف مدیریتی با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************
                }else{
                    echo json_encode(array('result'=>$employeetoken[0]
                    ,"data"=>$employeetoken[1]
                    ,'desc'=>$employeetoken[2]));
                }


            }
            else
                if ($command=="delete_managdiscount")
                {

                    $managdiscount_id=$this->post('managdiscount_id') ;

                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','managdiscount');
                    if($employeetoken[0]=='ok')
                    {
//************************************************************************;****************************************
                        $query1="select * from managdiscount_use_tb where managdiscount_mngdiscnt_id=".$managdiscount_id;
                        $result1 = $this->B_db->run_query($query1);
                        $num=count($result1[0]);
                        if($num==0){
//************************************************************************;****************************************
                            $output = array();$user_id=$employeetoken[0];

                            $query="DELETE FROM managdiscount_tb  where managdiscount_id=".$managdiscount_id."";
                            $result = $this->B_db->run_query_put($query);
                            if($result){echo json_encode(array('result'=>"ok"
                            ,"data"=>""
                            ,'desc'=>'تخفیف مدیریتی مورد نظر حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }else{
                                echo json_encode(array('result'=>"error"
                                ,"data"=>$query
                                ,'desc'=>'تخفیف مدیریتی مورد نظر حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
//***************************************************************************************************************
                        }else{
                            echo json_encode(array('result'=>"error"
                            ,"data"=>""
                            ,'desc'=>'تخفیف مدیریتی مورد نظر به علت استفاده حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }

                        //************************************************************************;****************************************
                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]));

                    }

                }
                else



                    if ($command=="modify_managdiscount")
                    {
                        $managdiscount_id=$this->post('managdiscount_id') ;

                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','managdiscount');
                        if($employeetoken[0]=='ok')
                        {
//*****************************************************************************************
                            $query="UPDATE managdiscount_tb SET ";
                            if(isset($_REQUEST['managdiscounts_mode'])){
                                $managdiscounts_mode=$this->post('managdiscounts_mode');
                                $query.="managdiscounts_mode='".$managdiscounts_mode."'";}


                            if(isset($_REQUEST['managdiscount_amount'])&&isset($_REQUEST['managdiscounts_mode'])){ $query.=",";}
                            if(isset($_REQUEST['managdiscount_amount'])){
                                $managdiscount_amount=$this->post('managdiscount_amount') ;
                                $query.="managdiscount_amount=".$managdiscount_amount."";}

                            if(isset($_REQUEST['managdiscount_max_forone'])&&(isset($_REQUEST['managdiscounts_mode'])||isset($_REQUEST['managdiscount_amount']))){ $query.=",";}
                            if(isset($_REQUEST['managdiscount_max_forone'])){
                                $managdiscount_max_forone=$this->post('managdiscount_max_forone') ;
                                $query.="managdiscount_max_forone='".$managdiscount_max_forone."'";}

                            if(isset($_REQUEST['managdiscount_max_all'])&&(isset($_REQUEST['managdiscounts_mode'])||isset($_REQUEST['managdiscount_amount'])||isset($_REQUEST['managdiscount_max_forone']))){$query.=",";}
                            if(isset($_REQUEST['managdiscount_max_all'])){
                                $managdiscount_max_all=$this->post('managdiscount_max_all') ;
                                $query.="managdiscount_max_all='".$managdiscount_max_all."' ";}

                            if(isset($_REQUEST['managdiscount_date_start'])&&(isset($_REQUEST['managdiscount_max_all'])||isset($_REQUEST['managdiscounts_mode'])||isset($_REQUEST['managdiscount_amount'])||isset($_REQUEST['managdiscount_max_forone']))){$query.=",";}
                            if(isset($_REQUEST['managdiscount_date_start'])){
                                $managdiscount_date_start=$this->post('managdiscount_date_start');
                                $query.="managdiscount_date_start='".$managdiscount_date_start."' ";}

                            if(isset($_REQUEST['managdiscount_date_end'])&&(isset($_REQUEST['managdiscount_max_all'])||isset($_REQUEST['managdiscount_date_start'])||isset($_REQUEST['managdiscounts_mode'])||isset($_REQUEST['managdiscount_amount'])||isset($_REQUEST['managdiscount_max_forone']))){$query.=",";}
                            if(isset($_REQUEST['managdiscount_date_end'])){
                                $managdiscount_date_end=$this->post('managdiscount_date_end');
                                $query.="managdiscount_date_end='".$managdiscount_date_end."' ";}

                            if(isset($_REQUEST['managdiscount_deactive'])&&(isset($_REQUEST['managdiscount_date_end'])||isset($_REQUEST['managdiscount_date_start'])||isset($_REQUEST['managdiscount_max_all'])||isset($_REQUEST['managdiscounts_mode'])||isset($_REQUEST['managdiscount_amount'])||isset($_REQUEST['managdiscount_max_forone']))){$query.=",";}
                            if(isset($_REQUEST['managdiscount_deactive'])){
                                $managdiscount_deactive=$this->post('managdiscount_deactive') ;
                                $query.="managdiscount_deactive=".$managdiscount_deactive." ";}

                            if(isset($_REQUEST['managdiscount_desc'])&&(isset($_REQUEST['managdiscount_deactive'])||isset($_REQUEST['managdiscount_date_end'])||isset($_REQUEST['managdiscount_date_start'])||isset($_REQUEST['managdiscount_max_all'])||isset($_REQUEST['managdiscounts_mode'])||isset($_REQUEST['managdiscount_amount'])||isset($_REQUEST['managdiscount_max_forone']))){$query.=",";}
                            if(isset($_REQUEST['managdiscount_desc'])){
                                $managdiscount_desc=$this->post('managdiscount_desc');
                                $query.="managdiscount_desc='".$managdiscount_desc."' ";}


                            $query.=" where managdiscount_id=".$managdiscount_id;

                            $result=$this->B_db->run_query_put($query);
                            if($result){
                                echo json_encode(array('result'=>"ok"
                                ,"data"=>""
                                ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }else {
                                echo json_encode(array('result'=>"ok"
                                ,"data"=>""
                                ,'desc'=>'تغییرات انجام نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
//**************************************************************************************************************

                        }else{
                            echo json_encode(array('result'=>$employeetoken[0]
                            ,"data"=>$employeetoken[1]
                            ,'desc'=>$employeetoken[2]));

                        }
                    }
                    else
                        if ($command=="deactive_managdiscount")
                        {
                            $managdiscount_id=$this->post('managdiscount_id') ;

                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','managdiscount');
                            if($employeetoken[0]=='ok')
                            {
//*****************************************************************************************
                                $query="UPDATE managdiscount_tb SET managdiscount_deactive=1 where managdiscount_id=".$managdiscount_id;

                                $result=$this->B_db->run_query_put($query);
                                if($result){
                                    echo json_encode(array('result'=>"ok"
                                    ,"data"=>""
                                    ,'desc'=>'تخفیف مدیریتی  غیر فعال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }else {
                                    echo json_encode(array('result'=>"ok"
                                    ,"data"=>""
                                    ,'desc'=>'تخفیف مدیریتی  غیر فعال  نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }
//**************************************************************************************************************

                            }else{
                                echo json_encode(array('result'=>$employeetoken[0]
                                ,"data"=>$employeetoken[1]
                                ,'desc'=>$employeetoken[2]));

                            }
                        }
                        else

                            if ($command=="active_managdiscount")
                            {
                                $managdiscount_id=$this->post('managdiscount_id') ;

                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','managdiscount');
                                if($employeetoken[0]=='ok')
                                {
//*****************************************************************************************
                                    $query="UPDATE managdiscount_tb SET managdiscount_deactive=0 where managdiscount_id=".$managdiscount_id;

                                    $result=$this->B_db->run_query_put($query);
                                    if($result){
                                        echo json_encode(array('result'=>"ok"
                                        ,"data"=>""
                                        ,'desc'=>'تخفیف مدیریتی فعال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }else {
                                        echo json_encode(array('result'=>"ok"
                                        ,"data"=>""
                                        ,'desc'=>'تخفیف مدیریتی فعال نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }
//**************************************************************************************************************

                                }else{
                                    echo json_encode(array('result'=>$employeetoken[0]
                                    ,"data"=>$employeetoken[1]
                                    ,'desc'=>$employeetoken[2]));
                                }
                            }
                            else
                                if ($command=="get_managdiscount_use")
                                {
                                    $managdiscount_id=$this->post('managdiscount_id') ;
                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','managdiscount');
                                    if($employeetoken[0]=='ok')
                                    {
                                        $query="select * from managdiscount_use_tb,request_tb,user_tb,agent_tb,company_tb where
 managdiscount_request_id=request_id
 AND user_id=request_user_id AND
  agent_id=request_agent_id AND
  agent_company_id=company_id AND
 managdiscount_mngdiscnt_id=".$managdiscount_id." AND ";
                                        if(isset($_REQUEST['filter1'])){$query.=$this->post('filter1');}else{$query.=" 1=1 ";}
                                        $query.=" AND ";
                                        if(isset($_REQUEST['filter2'])){$query.=$this->post('filter2');}else{$query.=" 1=1 ";}
                                        $query.=" AND ";
                                        if(isset($_REQUEST['filter3'])){$query.=$this->post('filter3');}else{$query.=" 1=1 ";}
                                        $query.=" ORDER BY managdiscount_use_id ASC";
                                        $result = $this->B_db->run_query($query);
                                        $output =array();
                                        foreach($result as $row)
                                        {
                                            $record=array();
                                            $record['managdiscount_use_id']=$row['managdiscount_use_id'];
                                            $record['managdiscount_mngdiscnt_id']=$row['managdiscount_mngdiscnt_id'];
                                            $record['managdiscount_request_id']=$row['managdiscount_request_id'];
                                            $record['managdiscount_use_timestamp']=$row['managdiscount_use_timestamp'];
                                            $record['managdiscount_use_amount']=$row['managdiscount_use_amount'];

                                            $record['request_user_id']=$row['request_user_id'];
                                            $record['request_fieldinsurance']=$row['request_fieldinsurance'];
                                            $record['request_description']=$row['request_description'];

                                            $record['user_name']=$row['user_name'];
                                            $record['user_family']=$row['user_family'];
                                            $record['user_mobile']=$row['user_mobile'];

                                            $record['company_name']=$row['company_name'];
                                            $record['agent_company_id']=$row['agent_company_id'];
                                            $record['agent_name']=$row['agent_name'];
                                            $record['agent_family']=$row['agent_family'];
                                            $record['agent_code']=$row['agent_code'];
                                            $record['agent_mobile']=$row['agent_mobile'];



                                            $output[]=$record;
                                        }
                                        echo json_encode(array('result'=>"ok"
                                        ,"data"=>$output
                                        ,'desc'=>'مشحصات تخفیف مدیریتی با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************
                                    }else{
                                        echo json_encode(array('result'=>$employeetoken[0]
                                        ,"data"=>$employeetoken[1]
                                        ,'desc'=>$employeetoken[2]));
                                    }
                                }
                        }
}