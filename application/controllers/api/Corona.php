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
class Corona extends REST_Controller {

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
        if ($command=="add_corona_old")
        {

            $corona_old_id=$this->post('corona_old_id') ;
            $corona_old_name=$this->post('corona_old_name') ;
            $corona_old_desc=$this->post('corona_old_desc') ;
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','corona');
            if($employeetoken[0]=='ok')
            {
//************************************************************************;****************************************
                $query="select * from corona_old_tb where corona_old_name='".$corona_old_name."' OR corona_old_id=".$corona_old_id."";
                $result=$this->B_db->run_query($query);
                $num=count($result[0]);
                if ($num==0)
                {
                    $query="INSERT INTO corona_old_tb(corona_old_id, corona_old_name, corona_old_desc)
	                            VALUES ( $corona_old_id,'$corona_old_name', '$corona_old_desc');";

                    $result=$this->B_db->run_query_put($query);
                    // $corona_old_id=$this->db->insert_id();
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('corona_old_id'=>$corona_old_id)
                    ,'desc'=>'طرح مسافرتی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    $carmode=$result[0];
                    echo json_encode(array('result'=>"error"
                    ,"data"=>array('corona_old_id'=>$carmode['corona_old_id'])
                    ,'desc'=>'طرح مسافرتی تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
//***************************************************************************************************************
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }






        }

        if ($command=="get_corona_old")
        {
//************************************************************************;****************************************

            $query="select * from corona_old_tb where 1 ORDER BY corona_old_id ASC";
            $result = $this->B_db->run_query($query);
            $output =array();
            foreach($result as $row)
            {
                $record=array();
                $record['corona_old_id']=$row['corona_old_id'];
                $record['corona_old_name']=$row['corona_old_name'];
                $record['corona_old_desc']=$row['corona_old_desc'];
                $output[]=$record;
            }
            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'طرح مسافرتی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

        }
        else
            if ($command=="delete_corona_old")
            {
                $corona_old_id=$this->post('corona_old_id') ;

                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','corona');
                if($employeetoken[0]=='ok')
                {
//************************************************************************;****************************************
                    $output = array();$user_id=$employeetoken[0];

                    $query="DELETE FROM corona_old_tb  where corona_old_id=".$corona_old_id."";
                    $result = $this->B_db->run_query_put($query);
                    if($result){echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'طرح مسافرتی حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        echo json_encode(array('result'=>"error"
                        ,"data"=>$output
                        ,'desc'=>'طرح مسافرتی حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
//***************************************************************************************************************
                }else{
                    echo json_encode(array('result'=>$employeetoken[0]
                    ,"data"=>$employeetoken[1]
                    ,'desc'=>$employeetoken[2]));

                }

            }
            else



                if ($command=="modify_corona_old")
                {
                    $corona_old_id=$this->post('corona_old_id') ;


                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','corona');
                    if($employeetoken[0]=='ok')
                    {
//*****************************************************************************************

                        $query="UPDATE corona_old_tb SET ";

                        if(isset($_REQUEST['corona_old_name'])){
                            $corona_old_name=$this->post('corona_old_name');
                            $query.="corona_old_name='".$corona_old_name."'";}

                        if(isset($_REQUEST['corona_old_desc'])&&(isset($_REQUEST['corona_old_name']))){ $query.=",";}
                        if(isset($_REQUEST['corona_old_desc'])){
                            $corona_old_desc=$this->post('corona_old_desc');
                            $query.="corona_old_desc='".$corona_old_desc."'";}

                        $query.="where corona_old_id=".$corona_old_id;

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
//*************************************************************************************************************
                                else
                                    if ($command=="add_corona_time")
                                    {
                                        $corona_time_id=$this->post('corona_time_id') ;
                                        $corona_time_name=$this->post('corona_time_name') ;
                                        $corona_time_percent=$this->post('corona_time_percent') ;



                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','corona');
                                        if($employeetoken[0]=='ok')
                                        {
//****************************************************************************************************************
                                            $query="select * from corona_time_tb where corona_time_id=$corona_time_id AND corona_time_name='".$corona_time_name."'";
                                            $result=$this->B_db->run_query($query);
                                            $num=count($result[0]);
                                            if ($num==0)
                                            {
                                                $query="INSERT INTO corona_time_tb(corona_time_id, corona_time_name, corona_time_percent)
	                            VALUES ( $corona_time_id,'$corona_time_name', '$corona_time_percent');";

                                                $result=$this->B_db->run_query_put($query);
                                                //   $corona_time_id=$this->db->insert_id();

                                                echo json_encode(array('result'=>"ok"
                                                ,"data"=>array('corona_time_id'=>$corona_time_id)
                                                ,'desc'=>'مدت زمان بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                            }else{
                                                $carmode=$result[0];
                                                echo json_encode(array('result'=>"error"
                                                ,"data"=>array('corona_time_id'=>$carmode['corona_time_id'])
                                                ,'desc'=>'مدت زمان بیمه نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                            }
//***************************************************************************************************************
                                        }else{
                                            echo json_encode(array('result'=>$employeetoken[0]
                                            ,"data"=>$employeetoken[1]
                                            ,'desc'=>$employeetoken[2]));

                                        }






                                    }
                                    else
                                        if ($command=="get_corona_time")
                                        {
//************************************************************************;****************************************

                                            $query="select * from corona_time_tb where 1 ORDER BY corona_time_id ASC";
                                            $result = $this->B_db->run_query($query);
                                            $output =array();
                                            foreach($result as $row)
                                            {
                                                $record=array();
                                                $record['corona_time_id']=$row['corona_time_id'];
                                                $record['corona_time_name']=$row['corona_time_name'];
                                                $record['corona_time_percent']=$row['corona_time_percent'];
                                                $output[]=$record;
                                            }
                                            echo json_encode(array('result'=>"ok"
                                            ,"data"=>$output
                                            ,'desc'=>'مدت زمان بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                        }
                                        else
                                            if ($command=="delete_corona_time")
                                            {
                                                $corona_time_id=$this->post('corona_time_id') ;

                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','corona');
                                                if($employeetoken[0]=='ok')
                                                {
//************************************************************************;****************************************
                                                    $output = array();$user_id=$employeetoken[0];

                                                    $query="DELETE FROM corona_time_tb  where corona_time_id=".$corona_time_id."";
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



                                                if ($command=="modify_corona_time")
                                                {
                                                    $corona_time_id=$this->post('corona_time_id') ;


                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','corona');
                                                    if($employeetoken[0]=='ok')
                                                    {
//*****************************************************************************************

                                                        $query="UPDATE corona_time_tb SET ";

                                                        if(isset($_REQUEST['corona_time_name'])){
                                                            $corona_time_name=$this->post('corona_time_name');
                                                            $query.="corona_time_name='".$corona_time_name."'";}

                                                        if(isset($_REQUEST['corona_time_percent'])&&(isset($_REQUEST['corona_time_name']))){ $query.=",";}
                                                        if(isset($_REQUEST['corona_time_percent'])){
                                                            $corona_time_percent=$this->post('corona_time_percent');
                                                            $query.="corona_time_percent='".$corona_time_percent."'";}

                                                        $query.="where corona_time_id=".$corona_time_id;

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
                                                    if ($command=="add_corona_coverage")
                                                    {
                                                        $corona_coverage_id=$this->post('corona_coverage_id') ;
                                                        $corona_coverage_name=$this->post('corona_coverage_name') ;
                                                        $corona_coverage_desc=$this->post('corona_coverage_desc') ;



                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','corona');
                                                        if($employeetoken[0]=='ok')
                                                        {
//****************************************************************************************************************
                                                            $query="select * from corona_coverage_tb where corona_coverage_name='".$corona_coverage_name."'";
                                                            $result=$this->B_db->run_query($query);
                                                            $num=count($result[0]);
                                                            if ($num==0)
                                                            {
                                                                $query="INSERT INTO corona_coverage_tb(corona_coverage_id, corona_coverage_name, corona_coverage_desc)
	                            VALUES ( $corona_coverage_id,'$corona_coverage_name', '$corona_coverage_desc');";

                                                                $result=$this->B_db->run_query_put($query);
                                                                //  $corona_coverage_id=$this->db->insert_id();

                                                                echo json_encode(array('result'=>"ok"
                                                                ,"data"=>array('corona_coverage_id'=>$corona_coverage_id)
                                                                ,'desc'=>'پوشش بیمه مسافرتی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                            }else{
                                                                $carmode=$result[0];
                                                                echo json_encode(array('result'=>"error"
                                                                ,"data"=>array('corona_coverage_id'=>$carmode['corona_coverage_id'])
                                                                ,'desc'=>'پوشش بیمه مسافرتی تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                            }
//***************************************************************************************************************
                                                        }else{
                                                            echo json_encode(array('result'=>$employeetoken[0]
                                                            ,"data"=>$employeetoken[1]
                                                            ,'desc'=>$employeetoken[2]));

                                                        }






                                                    }
                                                    else
                                                        if ($command=="get_corona_coverage")
                                                        {
//************************************************************************;****************************************

                                                            $query="select * from corona_coverage_tb where 1 ORDER BY corona_coverage_id ASC";
                                                            $result = $this->B_db->run_query($query);
                                                            $output =array();
                                                            foreach($result as $row)
                                                            {
                                                                $record=array();
                                                                $record['corona_coverage_id']=$row['corona_coverage_id'];
                                                                $record['corona_coverage_name']=$row['corona_coverage_name'];
                                                                $record['corona_coverage_desc']=$row['corona_coverage_desc'];
                                                                $output[]=$record;
                                                            }
                                                            echo json_encode(array('result'=>"ok"
                                                            ,"data"=>$output
                                                            ,'desc'=>'پوشش بیمه مسافرتی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                        }
                                                        else
                                                            if ($command=="delete_corona_coverage")
                                                            {
                                                                $corona_coverage_id=$this->post('corona_coverage_id') ;


                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','corona');
                                                                if($employeetoken[0]=='ok')
                                                                {
//************************************************************************;****************************************
                                                                    $output = array();$user_id=$employeetoken[0];

                                                                    $query="DELETE FROM corona_coverage_tb  where corona_coverage_id=".$corona_coverage_id."";
                                                                    $result = $this->B_db->run_query_put($query);
                                                                    if($result){echo json_encode(array('result'=>"ok"
                                                                    ,"data"=>$output
                                                                    ,'desc'=>'پوشش بیمه مسافرتی حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                    }else{
                                                                        echo json_encode(array('result'=>"error"
                                                                        ,"data"=>$output
                                                                        ,'desc'=>'پوشش بیمه مسافرتی حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                    }
//***************************************************************************************************************
                                                                }else{
                                                                    echo json_encode(array('result'=>$employeetoken[0]
                                                                    ,"data"=>$employeetoken[1]
                                                                    ,'desc'=>$employeetoken[2]));

                                                                }

                                                            }
                                                            else



                                                                if ($command=="modify_corona_coverage")
                                                                {
                                                                    $corona_coverage_id=$this->post('corona_coverage_id') ;


                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','corona');
                                                                    if($employeetoken[0]=='ok')
                                                                    {
//*****************************************************************************************

                                                                        $query="UPDATE corona_coverage_tb SET ";

                                                                        if(isset($_REQUEST['corona_coverage_name'])){
                                                                            $corona_coverage_name=$this->post('corona_coverage_name');
                                                                            $query.="corona_coverage_name='".$corona_coverage_name."'";}

                                                                        if(isset($_REQUEST['corona_coverage_desc'])&&(isset($_REQUEST['corona_coverage_name']))){ $query.=",";}
                                                                        if(isset($_REQUEST['corona_coverage_desc'])){
                                                                            $corona_coverage_desc=$this->post('corona_coverage_desc');
                                                                            $query.="corona_coverage_desc='".$corona_coverage_desc."'";}

                                                                        $query.="where corona_coverage_id=".$corona_coverage_id;

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
                                                                                                    if ($command=="add_corona_price")
                                                                                                    {
                                                                                                        $corona_price_old_id=$this->post('corona_price_old_id') ;
                                                                                                        $corona_price_time_id=$this->post('corona_price_time_id') ;
                                                                                                        $corona_price_coverage_id=$this->post('corona_price_coverage_id') ;
                                                                                                        $corona_price_amount=$this->post('corona_price_amount') ;
                                                                                                        $corona_price_desc=$this->post('corona_price_desc') ;
                                                                                                        $corona_price_disc=$this->post('corona_price_disc') ;
                                                                                                        $corona_price_fieldcompany_id=$this->post('corona_price_fieldcompany_id') ;



                                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','corona');
                                                                                                        if($employeetoken[0]=='ok')
                                                                                                        {
//****************************************************************************************************************
                                                                                                            $query="select * from corona_price_tb where corona_price_fieldcompany_id=".$corona_price_fieldcompany_id." AND corona_price_amount='".$corona_price_amount."' AND corona_price_coverage_id=".$corona_price_coverage_id." AND corona_price_time_id=".$corona_price_time_id." AND corona_price_old_id=".$corona_price_old_id."";
                                                                                                            $result=$this->B_db->run_query($query);
                                                                                                            $num=count($result[0]);
                                                                                                            if ($num==0)
                                                                                                            {
                                                                                                                $query1="INSERT INTO corona_price_tb(corona_price_coverage_id,corona_price_old_id,corona_price_time_id, corona_price_desc, corona_price_amount, corona_price_disc, corona_price_fieldcompany_id)
	                            VALUES ( $corona_price_coverage_id,$corona_price_old_id,$corona_price_time_id,'$corona_price_desc','$corona_price_amount','$corona_price_disc',$corona_price_fieldcompany_id);";

                                                                                                                $result1=$this->B_db->run_query_put($query1);
                                                                                                                $corona_price_id=$this->db->insert_id();
                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                ,"data"=>array('corona_price_id'=>$corona_price_id,'query'=>$query)
                                                                                                                ,'desc'=>'قیمت بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                            }else{
                                                                                                                $carmode=$result[0];
                                                                                                                echo json_encode(array('result'=>"error"
                                                                                                                ,"data"=>array('corona_price_id'=>$carmode['corona_price_id'])
                                                                                                                ,'desc'=>'قیمت بیمه نامه تکراری است'));
                                                                                                            }
//***************************************************************************************************************
                                                                                                        }else{
                                                                                                            echo json_encode(array('result'=>$employeetoken[0]
                                                                                                            ,"data"=>$employeetoken[1]
                                                                                                            ,'desc'=>$employeetoken[2]));

                                                                                                        }






                                                                                                    }
                                                                                                    else
                                                                                                        if ($command=="get_corona_price")
                                                                                                        {
//************************************************************************;****************************************

                                                                                                            $query="select * from corona_price_tb,fieldcompany_tb,corona_old_tb,company_tb,corona_coverage_tb,corona_time_tb
  where corona_price_fieldcompany_id=fieldcompany_id
  AND corona_price_old_id=corona_old_id
  AND corona_price_time_id=corona_time_id
  AND corona_price_coverage_id=corona_coverage_id
  AND fieldcompany_company_id=company_id
 ORDER BY corona_price_id ASC";
                                                                                                            $result = $this->B_db->run_query($query);
                                                                                                            $output =array();
                                                                                                            foreach($result as $row)
                                                                                                            {
                                                                                                                $record=array();
                                                                                                                $record['corona_price_id']=$row['corona_price_id'];
                                                                                                                $record['corona_price_old_id']=$row['corona_price_old_id'];
                                                                                                                $record['corona_old_name']=$row['corona_old_name'];
                                                                                                                $record['corona_price_fieldcompany_id']=$row['corona_price_fieldcompany_id'];
                                                                                                                $record['company_name']=$row['company_name'];
                                                                                                                $record['company_logo_url']=IMGADD.$row['company_logo_url'];
                                                                                                                $record['corona_price_time_id']=$row['corona_price_time_id'];
                                                                                                                $record['corona_time_name']=$row['corona_time_name'];
                                                                                                                $record['corona_price_coverage_id']=$row['corona_price_coverage_id'];
                                                                                                                $record['corona_coverage_name']=$row['corona_coverage_name'];
                                                                                                                $record['corona_price_desc']=$row['corona_price_desc'];
                                                                                                                $record['corona_price_amount']=$row['corona_price_amount'];
                                                                                                                $record['corona_price_disc']=$row['corona_price_disc'];
                                                                                                                $record['corona_price_deactive']=$row['corona_price_deactive'];
                                                                                                                $output[]=$record;
                                                                                                            }
                                                                                                            echo json_encode(array('result'=>"ok"
                                                                                                            ,"data"=>$output
                                                                                                            ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                                        }
                                                                                                        else
                                                                                                            if ($command=="delete_corona_price")
                                                                                                            {
                                                                                                                $corona_price_id=$this->post('corona_price_id');

                                                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','corona');
                                                                                                                if($employeetoken[0]=='ok')
                                                                                                                {
//************************************************************************;****************************************
                                                                                                                    $output = array();$user_id=$employeetoken[0];

                                                                                                                    $query="DELETE FROM corona_price_tb  where corona_price_id=".$corona_price_id."";
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



                                                                                                                if ($command=="modify_corona_price")
                                                                                                                {
                                                                                                                    $corona_price_id=$this->post('corona_price_id');

                                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','corona');
                                                                                                                    if($employeetoken[0]=='ok')
                                                                                                                    {
//*****************************************************************************************

                                                                                                                        $query="UPDATE corona_price_tb SET ";

                                                                                                                        if(isset($_REQUEST['corona_price_amount'])){
                                                                                                                            $corona_price_amount=$this->post('corona_price_amount');
                                                                                                                            $query.="corona_price_amount='".$corona_price_amount."'";}



                                                                                                                        if(isset($_REQUEST['corona_price_deactive'])&&(isset($_REQUEST['corona_price_amount']))){ $query.=",";}
                                                                                                                        if(isset($_REQUEST['corona_price_deactive'])){
                                                                                                                            $corona_price_deactive=$this->post('corona_price_deactive');
                                                                                                                            $query.="corona_price_deactive=".$corona_price_deactive."";}

                                                                                                                        if(isset($_REQUEST['corona_price_disc'])&&(isset($_REQUEST['corona_price_deactive'])||isset($_REQUEST['corona_price_amount']))){ $query.=",";}
                                                                                                                        if(isset($_REQUEST['corona_price_disc'])){
                                                                                                                            $corona_price_disc=$this->post('corona_price_disc');
                                                                                                                            $query.="corona_price_disc=".$corona_price_disc."";}

                                                                                                                        if(isset($_REQUEST['corona_price_coverage_id'])&&(isset($_REQUEST['corona_price_disc'])||isset($_REQUEST['corona_price_deactive'])||isset($_REQUEST['corona_price_amount']))){ $query.=",";}
                                                                                                                        if(isset($_REQUEST['corona_price_coverage_id'])){
                                                                                                                            $corona_price_coverage_id=$this->post('corona_price_coverage_id');
                                                                                                                            $query.="corona_price_coverage_id=".$corona_price_coverage_id."";}


                                                                                                                        if(isset($_REQUEST['corona_price_desc'])&&(isset($_REQUEST['corona_price_disc'])||isset($_REQUEST['corona_price_deactive'])||isset($_REQUEST['corona_price_amount']))){ $query.=",";}
                                                                                                                        if(isset($_REQUEST['corona_price_desc'])){
                                                                                                                            $corona_price_desc=$this->post('corona_price_desc');
                                                                                                                            $query.="corona_price_desc='".$corona_price_desc."'";}


                                                                                                                        $query.=" where corona_price_id=".$corona_price_id;

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


        }
}