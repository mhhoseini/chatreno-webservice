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
class Therapy extends REST_Controller {

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
        
                                    
                                                    if ($command=="add_therapy_coverage")
                                                    {
                                                        $therapy_coverage_id=$this->post('therapy_coverage_id') ;
                                                        $therapy_coverage_name=$this->post('therapy_coverage_name') ;
                                                        $therapy_coverage_price=$this->post('therapy_coverage_price') ;



                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','therapy');
                                                        if($employeetoken[0]=='ok')
                                                        {
//****************************************************************************************************************
                                                            $query="select * from therapy_coverage_tb where therapy_coverage_name='".$therapy_coverage_name."'";
                                                            $result=$this->B_db->run_query($query);
                                                            $num=count($result[0]);
                                                            if ($num==0)
                                                            {
                                                                $query="INSERT INTO therapy_coverage_tb(therapy_coverage_id, therapy_coverage_name, therapy_coverage_price)
	                            VALUES ( $therapy_coverage_id,'$therapy_coverage_name', '$therapy_coverage_price');";

                                                                $result=$this->B_db->run_query_put($query);
                                                                //  $therapy_coverage_id=$this->db->insert_id();

                                                                echo json_encode(array('result'=>"ok"
                                                                ,"data"=>array('therapy_coverage_id'=>$therapy_coverage_id)
                                                                ,'desc'=>'پوشش بیمه مسافرتی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                            }else{
                                                                $carmode=$result[0];
                                                                echo json_encode(array('result'=>"error"
                                                                ,"data"=>array('therapy_coverage_id'=>$carmode['therapy_coverage_id'])
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
                                                        if ($command=="get_therapy_coverage")
                                                        {
//************************************************************************;****************************************

                                                            $query="select * from therapy_coverage_tb where 1 ORDER BY therapy_coverage_id ASC";
                                                            $result = $this->B_db->run_query($query);
                                                            $output =array();
                                                            foreach($result as $row)
                                                            {
                                                                $record=array();
                                                                $record['therapy_coverage_id']=$row['therapy_coverage_id'];
                                                                $record['therapy_coverage_name']=$row['therapy_coverage_name'];
                                                                $record['therapy_coverage_price']=$row['therapy_coverage_price'];
                                                                $output[]=$record;
                                                            }
                                                            echo json_encode(array('result'=>"ok"
                                                            ,"data"=>$output
                                                            ,'desc'=>'پوشش بیمه مسافرتی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                        }
                                                        else
                                                            if ($command=="delete_therapy_coverage")
                                                            {
                                                                $therapy_coverage_id=$this->post('therapy_coverage_id') ;


                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','therapy');
                                                                if($employeetoken[0]=='ok')
                                                                {
//************************************************************************;****************************************
                                                                    $output = array();$user_id=$employeetoken[0];

                                                                    $query="DELETE FROM therapy_coverage_tb  where therapy_coverage_id=".$therapy_coverage_id."";
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



                                                                if ($command=="modify_therapy_coverage")
                                                                {
                                                                    $therapy_coverage_id=$this->post('therapy_coverage_id') ;


                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','therapy');
                                                                    if($employeetoken[0]=='ok')
                                                                    {
//*****************************************************************************************

                                                                        $query="UPDATE therapy_coverage_tb SET ";

                                                                        if(isset($_REQUEST['therapy_coverage_name'])){
                                                                            $therapy_coverage_name=$this->post('therapy_coverage_name');
                                                                            $query.="therapy_coverage_name='".$therapy_coverage_name."'";}

                                                                        if(isset($_REQUEST['therapy_coverage_price'])&&(isset($_REQUEST['therapy_coverage_name']))){ $query.=",";}
                                                                        if(isset($_REQUEST['therapy_coverage_price'])){
                                                                            $therapy_coverage_price=$this->post('therapy_coverage_price');
                                                                            $query.="therapy_coverage_price='".$therapy_coverage_price."'";}

                                                                        $query.="where therapy_coverage_id=".$therapy_coverage_id;

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
                                                                    if ($command=="add_organ_therapycontract_conditions_covarage")
                                                                    {
                                                                        $organ_therapycontract_conditions_covarage_id=$this->post('organ_therapycontract_conditions_covarage_id') ;
                                                                        $organ_therapycontract_conditions_covarage_name=$this->post('organ_therapycontract_conditions_covarage_name') ;
                                                                        $organ_therapycontract_conditions_covarage_desc=$this->post('organ_therapycontract_conditions_covarage_desc') ;



                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','therapy');
                                                                        if($employeetoken[0]=='ok')
                                                                        {
//****************************************************************************************************************
                                                                            $query="select * from organ_therapycontract_conditions_covarage_tb where organ_therapycontract_conditions_covarage_name='".$organ_therapycontract_conditions_covarage_name."'";
                                                                            $result=$this->B_db->run_query($query);
                                                                            $num=count($result[0]);
                                                                            if ($num==0)
                                                                            {
                                                                                $query="INSERT INTO organ_therapycontract_conditions_covarage_tb(organ_therapycontract_conditions_covarage_id, organ_therapycontract_conditions_covarage_name, organ_therapycontract_conditions_covarage_desc)
	                            VALUES ( $organ_therapycontract_conditions_covarage_id,'$organ_therapycontract_conditions_covarage_name', '$organ_therapycontract_conditions_covarage_desc');";

                                                                                $result=$this->B_db->run_query_put($query);
                                                                                //  $organ_therapycontract_conditions_covarage_id=$this->db->insert_id();

                                                                                echo json_encode(array('result'=>"ok"
                                                                                ,"data"=>array('organ_therapycontract_conditions_covarage_id'=>$organ_therapycontract_conditions_covarage_id)
                                                                                ,'desc'=>'پوشش بیمه مسافرتی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                            }else{
                                                                                $carmode=$result[0];
                                                                                echo json_encode(array('result'=>"error"
                                                                                ,"data"=>array('organ_therapycontract_conditions_covarage_id'=>$carmode['organ_therapycontract_conditions_covarage_id'])
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
                                                                        if ($command=="get_organ_therapycontract_conditions_covarage")
                                                                        {
//************************************************************************;****************************************

                                                                            $query="select * from organ_therapycontract_conditions_covarage_tb where 1 ORDER BY organ_therapycontract_conditions_covarage_id ASC";
                                                                            $result = $this->B_db->run_query($query);
                                                                            $output =array();
                                                                            foreach($result as $row)
                                                                            {
                                                                                $record=array();
                                                                                $record['organ_therapycontract_conditions_covarage_id']=$row['organ_therapycontract_conditions_covarage_id'];
                                                                                $record['organ_therapycontract_conditions_covarage_name']=$row['organ_therapycontract_conditions_covarage_name'];
                                                                                $record['organ_therapycontract_conditions_covarage_desc']=$row['organ_therapycontract_conditions_covarage_desc'];
                                                                                $output[]=$record;
                                                                            }
                                                                            echo json_encode(array('result'=>"ok"
                                                                            ,"data"=>$output
                                                                            ,'desc'=>'پوشش بیمه مسافرتی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                        }
                                                                        else
                                                                            if ($command=="delete_organ_therapycontract_conditions_covarage")
                                                                            {
                                                                                $organ_therapycontract_conditions_covarage_id=$this->post('organ_therapycontract_conditions_covarage_id') ;


                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','therapy');
                                                                                if($employeetoken[0]=='ok')
                                                                                {
//************************************************************************;****************************************
                                                                                    $output = array();$user_id=$employeetoken[0];

                                                                                    $query="DELETE FROM organ_therapycontract_conditions_covarage_tb  where organ_therapycontract_conditions_covarage_id=".$organ_therapycontract_conditions_covarage_id."";
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



                                                                                if ($command=="modify_organ_therapycontract_conditions_covarage")
                                                                                {
                                                                                    $organ_therapycontract_conditions_covarage_id=$this->post('organ_therapycontract_conditions_covarage_id') ;


                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','therapy');
                                                                                    if($employeetoken[0]=='ok')
                                                                                    {
//*****************************************************************************************

                                                                                        $query="UPDATE organ_therapycontract_conditions_covarage_tb SET ";

                                                                                        if(isset($_REQUEST['organ_therapycontract_conditions_covarage_name'])){
                                                                                            $organ_therapycontract_conditions_covarage_name=$this->post('organ_therapycontract_conditions_covarage_name');
                                                                                            $query.="organ_therapycontract_conditions_covarage_name='".$organ_therapycontract_conditions_covarage_name."'";}

                                                                                        if(isset($_REQUEST['organ_therapycontract_conditions_covarage_desc'])&&(isset($_REQUEST['organ_therapycontract_conditions_covarage_name']))){ $query.=",";}
                                                                                        if(isset($_REQUEST['organ_therapycontract_conditions_covarage_desc'])){
                                                                                            $organ_therapycontract_conditions_covarage_desc=$this->post('organ_therapycontract_conditions_covarage_desc');
                                                                                            $query.="organ_therapycontract_conditions_covarage_desc='".$organ_therapycontract_conditions_covarage_desc."'";}

                                                                                        $query.="where organ_therapycontract_conditions_covarage_id=".$organ_therapycontract_conditions_covarage_id;

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
                                                                    if ($command=="add_therapy_baseinsurer")
                                                                    {
                                                                        $therapy_baseinsurer_id=$this->post('therapy_baseinsurer_id') ;
                                                                        $therapy_baseinsurer_name=$this->post('therapy_baseinsurer_name') ;
                                                                        $therapy_baseinsurer_desc=$this->post('therapy_baseinsurer_desc') ;



                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','therapy');
                                                                        if($employeetoken[0]=='ok')
                                                                        {
//****************************************************************************************************************
                                                                            $query="select * from therapy_baseinsurer_tb where therapy_baseinsurer_name='".$therapy_baseinsurer_name."'";
                                                                            $result=$this->B_db->run_query($query);
                                                                            $num=count($result[0]);
                                                                            if ($num==0)
                                                                            {
                                                                                $query="INSERT INTO therapy_baseinsurer_tb(therapy_baseinsurer_id, therapy_baseinsurer_name, therapy_baseinsurer_desc)
	                            VALUES ( $therapy_baseinsurer_id,'$therapy_baseinsurer_name', '$therapy_baseinsurer_desc');";

                                                                                $result=$this->B_db->run_query_put($query);
                                                                                //  $therapy_coverage_id=$this->db->insert_id();

                                                                                echo json_encode(array('result'=>"ok"
                                                                                ,"data"=>array('therapy_baseinsurer_id'=>$therapy_baseinsurer_id)
                                                                                ,'desc'=>'کمک رسان بیمه مسافرتی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                            }else{
                                                                                $carmode=$result[0];
                                                                                echo json_encode(array('result'=>"error"
                                                                                ,"data"=>array('therapy_baseinsurer_id'=>$carmode['therapy_baseinsurer_id'])
                                                                                ,'desc'=>'کمک رسان بیمه مسافرتی تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                            }
//***************************************************************************************************************
                                                                        }else{
                                                                            echo json_encode(array('result'=>$employeetoken[0]
                                                                            ,"data"=>$employeetoken[1]
                                                                            ,'desc'=>$employeetoken[2]));

                                                                        }






                                                                    }
                                                                    else
                                                                        if ($command=="get_therapy_baseinsurer")
                                                                        {
//************************************************************************;****************************************

                                                                            $query="select * from therapy_baseinsurer_tb where 1 ORDER BY therapy_baseinsurer_id ASC";
                                                                            $result = $this->B_db->run_query($query);
                                                                            $output =array();
                                                                            foreach($result as $row)
                                                                            {
                                                                                $record=array();
                                                                                $record['therapy_baseinsurer_id']=$row['therapy_baseinsurer_id'];
                                                                                $record['therapy_baseinsurer_name']=$row['therapy_baseinsurer_name'];
                                                                                $record['therapy_baseinsurer_desc']=$row['therapy_baseinsurer_desc'];
                                                                                $output[]=$record;
                                                                            }
                                                                            echo json_encode(array('result'=>"ok"
                                                                            ,"data"=>$output
                                                                            ,'desc'=>'کمک رسان بیمه مسافرتی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                        }
                                                                        else
                                                                            if ($command=="delete_therapy_baseinsurer")
                                                                            {
                                                                                $therapy_baseinsurer_id=$this->post('therapy_baseinsurer_id') ;


                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','therapy');
                                                                                if($employeetoken[0]=='ok')
                                                                                {
//************************************************************************;****************************************
                                                                                    $output = array();$user_id=$employeetoken[0];

                                                                                    $query="DELETE FROM therapy_baseinsurer_tb  where therapy_baseinsurer_id=".$therapy_baseinsurer_id."";
                                                                                    $result = $this->B_db->run_query_put($query);
                                                                                    if($result){echo json_encode(array('result'=>"ok"
                                                                                    ,"data"=>$output
                                                                                    ,'desc'=>'کمک رسان بیمه مسافرتی حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                    }else{
                                                                                        echo json_encode(array('result'=>"error"
                                                                                        ,"data"=>$output
                                                                                        ,'desc'=>'کمک رسان بیمه مسافرتی حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                    }
//***************************************************************************************************************
                                                                                }else{
                                                                                    echo json_encode(array('result'=>$employeetoken[0]
                                                                                    ,"data"=>$employeetoken[1]
                                                                                    ,'desc'=>$employeetoken[2]));

                                                                                }

                                                                            }
                                                                            else



                                                                                if ($command=="modify_therapy_baseinsurer")
                                                                                {
                                                                                    $therapy_baseinsurer_id=$this->post('therapy_baseinsurer_id') ;


                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','therapy');
                                                                                    if($employeetoken[0]=='ok')
                                                                                    {
//*****************************************************************************************

                                                                                        $query="UPDATE therapy_baseinsurer_tb SET ";

                                                                                        if(isset($_REQUEST['therapy_baseinsurer_name'])){
                                                                                            $therapy_baseinsurer_name=$this->post('therapy_baseinsurer_name');
                                                                                            $query.="therapy_baseinsurer_name='".$therapy_baseinsurer_name."'";}

                                                                                        if(isset($_REQUEST['therapy_baseinsurer_desc'])&&(isset($_REQUEST['therapy_baseinsurer_name']))){ $query.=",";}
                                                                                        if(isset($_REQUEST['therapy_baseinsurer_desc'])){
                                                                                            $therapy_baseinsurer_desc=$this->post('therapy_baseinsurer_desc');
                                                                                            $query.="therapy_baseinsurer_desc='".$therapy_baseinsurer_desc."'";}

                                                                                        $query.="where therapy_baseinsurer_id=".$therapy_baseinsurer_id;

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
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//***************************************************************************************************************

                                                                                else
                                                                                    if ($command=="add_therapy_age")
                                                                                    {
                                                                                        $therapy_age_id=$this->post('therapy_age_id') ;
                                                                                        $therapy_age_name=$this->post('therapy_age_name') ;
                                                                                        $therapy_age_desc=$this->post('therapy_age_desc') ;



                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','therapy');
                                                                                        if($employeetoken[0]=='ok')
                                                                                        {
//****************************************************************************************************************
                                                                                            $query="select * from therapy_age_tb where therapy_age_name='".$therapy_age_name."'";
                                                                                            $result=$this->B_db->run_query($query);
                                                                                            $num=count($result[0]);
                                                                                            if ($num==0)
                                                                                            {
                                                                                                $query="INSERT INTO therapy_age_tb(therapy_age_id, therapy_age_name, therapy_age_desc)
	                            VALUES ( $therapy_age_id,'$therapy_age_name', '$therapy_age_desc');";

                                                                                                $result=$this->B_db->run_query_put($query);
                                                                                                //  $therapy_coverage_id=$this->db->insert_id();

                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                ,"data"=>array('therapy_age_id'=>$therapy_age_id)
                                                                                                ,'desc'=>'تعداد مسافر بیمه مسافرتی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                            }else{
                                                                                                $carmode=$result[0];
                                                                                                echo json_encode(array('result'=>"error"
                                                                                                ,"data"=>array('therapy_age_id'=>$carmode['therapy_age_id'])
                                                                                                ,'desc'=>' تعداد مسافر بیمه مسافرتی تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                            }
//***************************************************************************************************************
                                                                                        }else{
                                                                                            echo json_encode(array('result'=>$employeetoken[0]
                                                                                            ,"data"=>$employeetoken[1]
                                                                                            ,'desc'=>$employeetoken[2]));

                                                                                        }






                                                                                    }
                                                                                    else
                                                                                        if ($command=="get_therapy_age")
                                                                                        {
//************************************************************************;****************************************

                                                                                            $query="select * from therapy_age_tb where 1 ORDER BY therapy_age_id ASC";
                                                                                            $result = $this->B_db->run_query($query);
                                                                                            $output =array();
                                                                                            foreach($result as $row)
                                                                                            {
                                                                                                $record=array();
                                                                                                $record['therapy_age_id']=$row['therapy_age_id'];
                                                                                                $record['therapy_age_name']=$row['therapy_age_name'];
                                                                                                $record['therapy_age_desc']=$row['therapy_age_desc'];
                                                                                                $output[]=$record;
                                                                                            }
                                                                                            echo json_encode(array('result'=>"ok"
                                                                                            ,"data"=>$output
                                                                                            ,'desc'=>' تعداد مسافر بیمه مسافرتی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                        }
                                                                                        else
                                                                                            if ($command=="delete_therapy_age")
                                                                                            {
                                                                                                $therapy_age_id=$this->post('therapy_age_id') ;


                                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','therapy');
                                                                                                if($employeetoken[0]=='ok')
                                                                                                {
//************************************************************************;****************************************
                                                                                                    $output = array();$user_id=$employeetoken[0];

                                                                                                    $query="DELETE FROM therapy_age_tb  where therapy_age_id=".$therapy_age_id."";
                                                                                                    $result = $this->B_db->run_query_put($query);
                                                                                                    if($result){echo json_encode(array('result'=>"ok"
                                                                                                    ,"data"=>$output
                                                                                                    ,'desc'=>'تعداد مسافر بیمه مسافرتی حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                    }else{
                                                                                                        echo json_encode(array('result'=>"error"
                                                                                                        ,"data"=>$output
                                                                                                        ,'desc'=>'تعداد مسافر بیمه مسافرتی حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                    }
//***************************************************************************************************************
                                                                                                }else{
                                                                                                    echo json_encode(array('result'=>$employeetoken[0]
                                                                                                    ,"data"=>$employeetoken[1]
                                                                                                    ,'desc'=>$employeetoken[2]));

                                                                                                }

                                                                                            }
                                                                                            else



                                                                                                if ($command=="modify_therapy_age")
                                                                                                {
                                                                                                    $therapy_age_id=$this->post('therapy_age_id') ;


                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','therapy');
                                                                                                    if($employeetoken[0]=='ok')
                                                                                                    {
//*****************************************************************************************

                                                                                                        $query="UPDATE therapy_age_tb SET ";

                                                                                                        if(isset($_REQUEST['therapy_age_name'])){
                                                                                                            $therapy_age_name=$this->post('therapy_age_name');
                                                                                                            $query.="therapy_age_name='".$therapy_age_name."'";}

                                                                                                        if(isset($_REQUEST['therapy_age_desc'])&&(isset($_REQUEST['therapy_age_name']))){ $query.=",";}
                                                                                                        if(isset($_REQUEST['therapy_age_desc'])){
                                                                                                            $therapy_age_desc=$this->post('therapy_age_desc');
                                                                                                            $query.="therapy_age_desc='".$therapy_age_desc."'";}

                                                                                                        $query.="where therapy_age_id=".$therapy_age_id;

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
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//***************************************************************************************************************

                                                                                                else
                                                                                                    if ($command=="add_therapy_price")
                                                                                                    {
                                                                                                        $therapy_price_coverage_id=$this->post('therapy_price_coverage_id') ;
                                                                                                        $therapy_price_urldesc=$this->post('therapy_price_urldesc') ;
                                                                                                        $therapy_price_desc=$this->post('therapy_price_desc') ;
                                                                                                        $therapy_price_disc=$this->post('therapy_price_disc') ;
                                                                                                        $therapy_price_fieldcompany_id=$this->post('therapy_price_fieldcompany_id') ;



                                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','therapy');
                                                                                                        if($employeetoken[0]=='ok')
                                                                                                        {
//****************************************************************************************************************
                                                                                                            $query="select * from therapy_price_tb where therapy_price_fieldcompany_id=".$therapy_price_fieldcompany_id." AND  therapy_price_coverage_id=".$therapy_price_coverage_id." ";
                                                                                                            $result=$this->B_db->run_query($query);
                                                                                                            $num=count($result[0]);
                                                                                                            if ($num==0)
                                                                                                            {
                                                                                                                $query1="INSERT INTO therapy_price_tb(therapy_price_coverage_id, therapy_price_urldesc, therapy_price_desc, therapy_price_disc, therapy_price_fieldcompany_id)
	                            VALUES ( $therapy_price_coverage_id,'$therapy_price_urldesc','$therapy_price_desc','$therapy_price_disc',$therapy_price_fieldcompany_id);";

                                                                                                                $result1=$this->B_db->run_query_put($query1);
                                                                                                                $therapy_price_id=$this->db->insert_id();
                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                ,"data"=>array('therapy_price_id'=>$therapy_price_id,'query'=>$query)
                                                                                                                ,'desc'=>'قیمت بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                            }else{
                                                                                                                $carmode=$result[0];
                                                                                                                echo json_encode(array('result'=>"error"
                                                                                                                ,"data"=>array('therapy_price_id'=>$carmode['therapy_price_id'])
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
                                                                                                        if ($command=="get_therapy_price")
                                                                                                        {
//************************************************************************;****************************************

                                                                                                            $query="select * from therapy_price_tb,fieldcompany_tb,company_tb,therapy_coverage_tb
  where therapy_price_fieldcompany_id=fieldcompany_id
  AND therapy_price_coverage_id=therapy_coverage_id
  AND fieldcompany_company_id=company_id
 ORDER BY therapy_price_id ASC";
                                                                                                            $result = $this->B_db->run_query($query);
                                                                                                            $output =array();
                                                                                                            foreach($result as $row)
                                                                                                            {
                                                                                                                $record=array();
                                                                                                                $record['therapy_price_id']=$row['therapy_price_id'];
                                                                                                                $record['therapy_price_fieldcompany_id']=$row['therapy_price_fieldcompany_id'];
                                                                                                                $record['company_name']=$row['company_name'];
                                                                                                                $record['company_logo_url']=IMGADD.$row['company_logo_url'];
                                                                                                                $record['therapy_price_coverage_id']=$row['therapy_price_coverage_id'];
                                                                                                                $record['therapy_coverage_name']=$row['therapy_coverage_name'];
                                                                                                                $record['therapy_price_urldesc']=$row['therapy_price_urldesc'];
                                                                                                                $record['therapy_price_desc']=$row['therapy_price_desc'];
                                                                                                                $record['therapy_price_disc']=$row['therapy_price_disc'];
                                                                                                                $record['therapy_price_deactive']=$row['therapy_price_deactive'];
                                                                                                                $output[]=$record;
                                                                                                            }
                                                                                                            echo json_encode(array('result'=>"ok"
                                                                                                            ,"data"=>$output
                                                                                                            ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                                        }
                                                                                                        else
                                                                                                            if ($command=="delete_therapy_price")
                                                                                                            {
                                                                                                                $therapy_price_id=$this->post('therapy_price_id');

                                                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','therapy');
                                                                                                                if($employeetoken[0]=='ok')
                                                                                                                {
//************************************************************************;****************************************
                                                                                                                    $output = array();$user_id=$employeetoken[0];

                                                                                                                    $query="DELETE FROM therapy_price_tb  where therapy_price_id=".$therapy_price_id."";
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



                                                                                                                if ($command=="modify_therapy_price")
                                                                                                                {
                                                                                                                    $therapy_price_id=$this->post('therapy_price_id');

                                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','therapy');
                                                                                                                    if($employeetoken[0]=='ok')
                                                                                                                    {
//*****************************************************************************************

                                                                                                                        $query="UPDATE therapy_price_tb SET ";





                                                                                                                        if(isset($_REQUEST['therapy_price_deactive'])){
                                                                                                                            $therapy_price_deactive=$this->post('therapy_price_deactive');
                                                                                                                            $query.="therapy_price_deactive=".$therapy_price_deactive."";}

                                                                                                                        if(isset($_REQUEST['therapy_price_disc'])&&isset($_REQUEST['therapy_price_deactive'])){ $query.=",";}
                                                                                                                        if(isset($_REQUEST['therapy_price_disc'])){
                                                                                                                            $therapy_price_disc=$this->post('therapy_price_disc');
                                                                                                                            $query.="therapy_price_disc=".$therapy_price_disc."";}


                                                                                                                        if(isset($_REQUEST['therapy_price_coverage_id'])&&(isset($_REQUEST['therapy_price_disc'])||isset($_REQUEST['therapy_price_deactive']))){ $query.=",";}
                                                                                                                        if(isset($_REQUEST['therapy_price_coverage_id'])){
                                                                                                                            $therapy_price_coverage_id=$this->post('therapy_price_coverage_id');
                                                                                                                            $query.="therapy_price_coverage_id=".$therapy_price_coverage_id."";}

                                                                                                                        if(isset($_REQUEST['therapy_price_desc'])&&(isset($_REQUEST['therapy_price_coverage_id'])||isset($_REQUEST['therapy_price_disc'])||isset($_REQUEST['therapy_price_deactive']))){ $query.=",";}
                                                                                                                        if(isset($_REQUEST['therapy_price_desc'])){
                                                                                                                            $therapy_price_desc=$this->post('therapy_price_desc');
                                                                                                                            $query.="therapy_price_desc='".$therapy_price_desc."'";}

                                                                                                                        if(isset($_REQUEST['therapy_price_urldesc'])&&(isset($_REQUEST['therapy_price_desc'])||isset($_REQUEST['therapy_price_coverage_id'])||isset($_REQUEST['therapy_price_disc'])||isset($_REQUEST['therapy_price_deactive']))){ $query.=",";}
                                                                                                                        if(isset($_REQUEST['therapy_price_urldesc'])){
                                                                                                                            $therapy_price_urldesc=$this->post('therapy_price_urldesc');
                                                                                                                            $query.="therapy_price_urldesc='".$therapy_price_urldesc."' ";
                                                                                                                        }


                                                                                                                        $query.=" where therapy_price_id=".$therapy_price_id;



                                                                                                                        $result=$this->B_db->run_query_put($query);
                                                                                                                        if($result){
                                                                                                                            echo json_encode(array('result'=>"ok"
                                                                                                                            ,"data"=>""
                                                                                                                            ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                        }else{
                                                                                                                            echo json_encode(array('result'=>"error"
                                                                                                                            ,"data"=>""
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
                                                                                                                    if ($command=="add_price_age")
                                                                                                                    {
                                                                                                                        $therapy_price_age_therapy_price_id=$this->post('therapy_price_age_therapy_price_id');
                                                                                                                        $therapy_price_age_therapy_age_id=$this->post('therapy_price_age_therapy_age_id');
                                                                                                                        $therapy_price_age_price=$this->post('therapy_price_age_price');

                                                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','therapy');
                                                                                                                        if($employeetoken[0]=='ok')
                                                                                                                        {
//****************************************************************************************************************
                                                                                                                            $query="select * from therapy_price_age_tb where therapy_price_age_therapy_age_id=".$therapy_price_age_therapy_age_id." AND therapy_price_age_therapy_price_id=".$therapy_price_age_therapy_price_id."";
                                                                                                                            $result=$this->B_db->run_query($query);
                                                                                                                            $num=count($result[0]);
                                                                                                                            if ($num==0)
                                                                                                                            {
                                                                                                                                $query="INSERT INTO therapy_price_age_tb(therapy_price_age_therapy_price_id,therapy_price_age_therapy_age_id, therapy_price_age_price)
	                            VALUES ( $therapy_price_age_therapy_price_id,$therapy_price_age_therapy_age_id, '$therapy_price_age_price');";

                                                                                                                                $result=$this->B_db->run_query_put($query);
                                                                                                                                $therapy_price_age_id=$this->db->insert_id();
                                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                                ,"data"=>array('therapy_price_age_id'=>$therapy_price_age_id)
                                                                                                                                ,'desc'=>'قیمت  بر اساس سن در بیمه مسافرتی  اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                            }else{
                                                                                                                                $carmode=$result[0];
                                                                                                                                echo json_encode(array('result'=>"error"
                                                                                                                                ,"data"=>array('therapy_price_age_id'=>$carmode['therapy_price_age_id'])
                                                                                                                                ,'desc'=>' قیمت  بر اساس سن در بیمه مسافرتی  نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                            }
//***************************************************************************************************************
                                                                                                                        }else{
                                                                                                                            echo json_encode(array('result'=>$employeetoken[0]
                                                                                                                            ,"data"=>$employeetoken[1]
                                                                                                                            ,'desc'=>$employeetoken[2]));

                                                                                                                        }






                                                                                                                    }
                                                                                                                    else
                                                                                                                        if ($command=="get_price_age")
                                                                                                                        {
                                                                                                                            $therapy_price_id=$this->post('therapy_price_id');

//************************************************************************;****************************************

                                                                                                                            $query="select * from therapy_price_age_tb,therapy_age_tb where therapy_age_id=therapy_price_age_therapy_age_id AND therapy_price_age_therapy_price_id=$therapy_price_id  ORDER BY therapy_price_age_id ASC";
                                                                                                                            $result = $this->B_db->run_query($query);
                                                                                                                            $output =array();
                                                                                                                            foreach($result as $row)
                                                                                                                            {
                                                                                                                                $record=array();
                                                                                                                                $record['therapy_price_age_id']=$row['therapy_price_age_id'];
                                                                                                                                $record['therapy_price_age_therapy_price_id']=$row['therapy_price_age_therapy_price_id'];
                                                                                                                                $record['therapy_price_age_therapy_age_id']=$row['therapy_price_age_therapy_age_id'];
                                                                                                                                $record['therapy_age_name']=$row['therapy_age_name'];
                                                                                                                                $record['therapy_price_age_price']=$row['therapy_price_age_price'];
                                                                                                                                $output[]=$record;
                                                                                                                            }
                                                                                                                            echo json_encode(array('result'=>"ok"
                                                                                                                            ,"data"=>$output
                                                                                                                            ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                                                        }
                                                                                                                        else
                                                                                                                            if ($command=="delete_price_age")
                                                                                                                            {
                                                                                                                                $therapy_price_age_id=$this->post('therapy_price_age_id');

                                                                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','therapy');
                                                                                                                                if($employeetoken[0]=='ok')
                                                                                                                                {
//************************************************************************;****************************************
                                                                                                                                    $output = array();$user_id=$employeetoken[0];

                                                                                                                                    $query="DELETE FROM therapy_price_age_tb  where therapy_price_age_id=".$therapy_price_age_id."";
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



                                                                                                                                if ($command=="modify_price_age")
                                                                                                                                {
                                                                                                                                    $therapy_price_age_id=$this->post('therapy_price_age_id');

                                                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','therapy');
                                                                                                                                    if($employeetoken[0]=='ok')
                                                                                                                                    {
//*****************************************************************************************

                                                                                                                                        $query="UPDATE therapy_price_age_tb SET ";


                                                                                                                                        if(isset($_REQUEST['therapy_price_age_price'])){
                                                                                                                                            $therapy_price_age_price=$this->post('therapy_price_age_price');
                                                                                                                                            $query.="therapy_price_age_price='".$therapy_price_age_price."'";}

                                                                                                                                        $query.="where therapy_price_age_id=".$therapy_price_age_id;

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
                                                                                                                                //********************************************************************

                                                                                                                                else
                                                                                                                                    if ($command=="add_price_baseinsurer")
                                                                                                                                    {
                                                                                                                                        $therapy_price_baseinsurer_therapy_price_id=$this->post('therapy_price_baseinsurer_therapy_price_id');
                                                                                                                                        $therapy_price_baseinsurer_therapy_baseinsurer_id=$this->post('therapy_price_baseinsurer_therapy_baseinsurer_id');
                                                                                                                                        $therapy_price_baseinsurer_percent=$this->post('therapy_price_baseinsurer_percent');

                                                                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','therapy');
                                                                                                                                        if($employeetoken[0]=='ok')
                                                                                                                                        {
//****************************************************************************************************************
                                                                                                                                            $query="select * from therapy_price_baseinsurer_tb where therapy_price_baseinsurer_therapy_baseinsurer_id=".$therapy_price_baseinsurer_therapy_baseinsurer_id." AND therapy_price_baseinsurer_therapy_price_id=".$therapy_price_baseinsurer_therapy_price_id."";
                                                                                                                                            $result=$this->B_db->run_query($query);
                                                                                                                                            $num=count($result[0]);
                                                                                                                                            if ($num==0)
                                                                                                                                            {
                                                                                                                                                $query="INSERT INTO therapy_price_baseinsurer_tb(therapy_price_baseinsurer_therapy_price_id,therapy_price_baseinsurer_therapy_baseinsurer_id, therapy_price_baseinsurer_percent)
	                            VALUES ( $therapy_price_baseinsurer_therapy_price_id,$therapy_price_baseinsurer_therapy_baseinsurer_id, '$therapy_price_baseinsurer_percent');";

                                                                                                                                                $result=$this->B_db->run_query_put($query);
                                                                                                                                                $therapy_price_baseinsurer_id=$this->db->insert_id();
                                                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                                                ,"data"=>array('therapy_price_baseinsurer_id'=>$therapy_price_baseinsurer_id,'query'=>$query)
                                                                                                                                                ,'desc'=>'قیمت  بر اساس سن در بیمه مسافرتی  اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                            }else{
                                                                                                                                                $carmode=$result[0];
                                                                                                                                                echo json_encode(array('result'=>"error"
                                                                                                                                                ,"data"=>array('therapy_price_baseinsurer_id'=>$carmode['therapy_price_baseinsurer_id'])
                                                                                                                                                ,'desc'=>' قیمت  بر اساس سن در بیمه مسافرتی  نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                            }
//***************************************************************************************************************
                                                                                                                                        }else{
                                                                                                                                            echo json_encode(array('result'=>$employeetoken[0]
                                                                                                                                            ,"data"=>$employeetoken[1]
                                                                                                                                            ,'desc'=>$employeetoken[2]));

                                                                                                                                        }






                                                                                                                                    }
                                                                                                                                    else
                                                                                                                                        if ($command=="get_price_baseinsurer")
                                                                                                                                        {
                                                                                                                                            $therapy_price_id=$this->post('therapy_price_id');

//************************************************************************;****************************************

                                                                                                                                            $query="select * from therapy_price_baseinsurer_tb,therapy_baseinsurer_tb where therapy_baseinsurer_id=therapy_price_baseinsurer_therapy_baseinsurer_id AND therapy_price_baseinsurer_therapy_price_id=$therapy_price_id  ORDER BY therapy_price_baseinsurer_id ASC";
                                                                                                                                            $result = $this->B_db->run_query($query);
                                                                                                                                            $output =array();
                                                                                                                                            foreach($result as $row)
                                                                                                                                            {
                                                                                                                                                $record=array();
                                                                                                                                                $record['therapy_price_baseinsurer_id']=$row['therapy_price_baseinsurer_id'];
                                                                                                                                                $record['therapy_price_baseinsurer_therapy_price_id']=$row['therapy_price_baseinsurer_therapy_price_id'];
                                                                                                                                                $record['therapy_price_baseinsurer_therapy_baseinsurer_id']=$row['therapy_price_baseinsurer_therapy_baseinsurer_id'];
                                                                                                                                                $record['therapy_baseinsurer_name']=$row['therapy_baseinsurer_name'];
                                                                                                                                                $record['therapy_price_baseinsurer_percent']=$row['therapy_price_baseinsurer_percent'];
                                                                                                                                                $output[]=$record;
                                                                                                                                            }
                                                                                                                                            echo json_encode(array('result'=>"ok"
                                                                                                                                            ,"data"=>$output
                                                                                                                                            ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                                                                        }
                                                                                                                                        else
                                                                                                                                            if ($command=="delete_price_baseinsurer")
                                                                                                                                            {
                                                                                                                                                $therapy_price_baseinsurer_id=$this->post('therapy_price_baseinsurer_id');

                                                                                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','therapy');
                                                                                                                                                if($employeetoken[0]=='ok')
                                                                                                                                                {
//************************************************************************;****************************************
                                                                                                                                                    $output = array();$user_id=$employeetoken[0];

                                                                                                                                                    $query="DELETE FROM therapy_price_baseinsurer_tb  where therapy_price_baseinsurer_id=".$therapy_price_baseinsurer_id."";
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



                                                                                                                                                if ($command=="modify_price_baseinsurer")
                                                                                                                                                {
                                                                                                                                                    $therapy_price_baseinsurer_id=$this->post('therapy_price_baseinsurer_id');

                                                                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','therapy');
                                                                                                                                                    if($employeetoken[0]=='ok')
                                                                                                                                                    {
//*****************************************************************************************

                                                                                                                                                        $query="UPDATE therapy_price_baseinsurer_tb SET ";


                                                                                                                                                        if(isset($_REQUEST['therapy_price_baseinsurer_percent'])){
                                                                                                                                                            $therapy_price_baseinsurer_percent=$this->post('therapy_price_baseinsurer_percent');
                                                                                                                                                            $query.="therapy_price_baseinsurer_percent='".$therapy_price_baseinsurer_percent."'";}

                                                                                                                                                        $query.="where therapy_price_baseinsurer_id=".$therapy_price_baseinsurer_id;

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
                                                                                                                                                //********************************************************************************************
                                                                                                                                                //********************************************************************************************
                                                                                                                                                else
                                                                                                                                                    if ($command=="add_therapy_slideold")
                                                                                                                                                    {
                                                                                                                                                        $therapy_slideold_therapy_price_id=$this->post('therapy_slideold_therapy_price_id');
                                                                                                                                                        $therapy_slideold_min=$this->post('therapy_slideold_min');
                                                                                                                                                        $therapy_slideold_max=$this->post('therapy_slideold_max');
                                                                                                                                                        $therapy_slideold_percent=$this->post('therapy_slideold_percent');
                                                                                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','therapy');
                                                                                                                                                        if($employeetoken[0]=='ok')
                                                                                                                                                        {
                                                                                                                                                            $query="select * from therapy_slideold_tb where therapy_slideold_therapy_price_id=".$therapy_slideold_therapy_price_id." AND therapy_slideold_min=".$therapy_slideold_min." AND therapy_slideold_max=".$therapy_slideold_max."";
                                                                                                                                                            $result=$this->B_db->run_query($query);
                                                                                                                                                            $num=count($result[0]);
                                                                                                                                                            if ($num==0)
                                                                                                                                                            {
                                                                                                                                                                $query="INSERT INTO therapy_slideold_tb(therapy_slideold_therapy_price_id,therapy_slideold_min, therapy_slideold_max, therapy_slideold_percent)
                                                                                                                                                                             VALUES ( $therapy_slideold_therapy_price_id,'$therapy_slideold_min', '$therapy_slideold_max', '$therapy_slideold_percent');";
                                                                                                                                                               $result=$this->B_db->run_query_put($query);
                                                                                                                                                                $therapy_slideold_id=$this->db->insert_id();

                                                                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                                                                ,"data"=>array('therapy_slideold_id'=>$therapy_slideold_id)
                                                                                                                                                                ,'desc'=>'پوشش مازاد مالی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                          
                                                                                                                                                            }else{
                                                                                                                                                                $carmode=$result[0];
                                                                                                                                                                echo json_encode(array('result'=>"error"
                                                                                                                                                                ,"data"=>array('therapy_slideold_id'=>$carmode['therapy_slideold_id'])
                                                                                                                                                                ,'desc'=>' پوشش مازاد مالی نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                            }

                                                                                                                                                        }else{
                                                                                                                                                            echo json_encode(array('result'=>$employeetoken[0]
                                                                                                                                                            ,"data"=>$employeetoken[1]
                                                                                                                                                            ,'desc'=>$employeetoken[2]));
                                                                                                                                                        }
                                                                                                                                                    }
                                                                                                                                                    else
                                                                                                                                                        if ($command=="get_therapy_slideold")
                                                                                                                                                        {
                                                                                                                                                            $therapy_price_id=$this->post('therapy_price_id');
                                                                                                                                                            $query="select * from therapy_slideold_tb where therapy_slideold_therapy_price_id=$therapy_price_id  ORDER BY therapy_slideold_id ASC";
                                                                                                                                                            $result = $this->B_db->run_query($query);

                                                                                                                                                            $output =array();
                                                                                                                                                            foreach($result as $row)
                                                                                                                                                            {
                                                                                                                                                                $record=array();
                                                                                                                                                                $record['therapy_slideold_id']=$row['therapy_slideold_id'];
                                                                                                                                                                $record['therapy_slideold_therapy_price_id']=$row['therapy_slideold_therapy_price_id'];
                                                                                                                                                                $record['therapy_slideold_min']=$row['therapy_slideold_min'];
                                                                                                                                                                $record['therapy_slideold_percent']=$row['therapy_slideold_percent'];
                                                                                                                                                                $record['therapy_slideold_max']=$row['therapy_slideold_max'];
                                                                                                                                                                $output[]=$record;
                                                                                                                                                            }
                                                                                                                                                            echo json_encode(array('result'=>"ok"
                                                                                                                                                            ,"data"=>$output
                                                                                                                                                            ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                        }
                                                                                                                                                        else
                                                                                                                                                            if ($command=="delete_therapy_slideold")
                                                                                                                                                            {
                                                                                                                                                                $therapy_slideold_id=$this->post('therapy_slideold_id');

                                                                                                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','therapy');
                                                                                                                                                                if($employeetoken[0]=='ok')
                                                                                                                                                                {
                                                                                                                                                                    $query="DELETE FROM therapy_slideold_tb  where therapy_slideold_id=".$therapy_slideold_id."";
                                                                                                                                                                    $result = $this->B_db->run_query_put($query);

                                                                                                                                                                    if($result){echo json_encode(array('result'=>"ok"
                                                                                                                                                                    ,"data"=>''
                                                                                                                                                                    ,'desc'=>'قیمت بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                                    }else{
                                                                                                                                                                        echo json_encode(array('result'=>"error"
                                                                                                                                                                        ,"data"=>''
                                                                                                                                                                        ,'desc'=>'قیمت بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                                                    }
                                                                                                                                                                }else{
                                                                                                                                                                    echo json_encode(array('result'=>$employeetoken[0]
                                                                                                                                                                    ,"data"=>$employeetoken[1]
                                                                                                                                                                    ,'desc'=>$employeetoken[2]));
                                                                                                                                                                }
                                                                                                                                                            }
                                                                                                                                                            else
                                                                                                                                                                if ($command=="modify_therapy_slideold")
                                                                                                                                                                {
                                                                                                                                                                    $therapy_slideold_id=$this->post('therapy_slideold_id');
                                                                                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','therapy');
                                                                                                                                                                    if($employeetoken[0]=='ok')
                                                                                                                                                                    {
                                                                                                                                                                        $query="UPDATE therapy_slideold_tb SET ";
                                                                                                                                                                        if(isset($_REQUEST['therapy_slideold_percent'])){
                                                                                                                                                                            $therapy_slideold_percent=$this->post('therapy_slideold_percent');
                                                                                                                                                                            $query.="therapy_slideold_percent='".$therapy_slideold_percent."'";}
                                                                                                                                                                        if(isset($_REQUEST['therapy_slideold_min'])&&(isset($_REQUEST['therapy_slideold_percent']))){ $query.=",";}
                                                                                                                                                                        if(isset($_REQUEST['therapy_slideold_min'])){
                                                                                                                                                                            $therapy_slideold_min=$this->post('therapy_slideold_min');
                                                                                                                                                                            $query.="therapy_slideold_min='".$therapy_slideold_min."'";
                                                                                                                                                                        }
                                                                                                                                                                        if(isset($_REQUEST['therapy_slideold_max'])&&(isset($_REQUEST['therapy_slideold_min'])||isset($_REQUEST['therapy_slideold_percent']))){ $query.=",";}
                                                                                                                                                                        if(isset($_REQUEST['therapy_slideold_max'])){
                                                                                                                                                                            $therapy_slideold_max=$this->post('therapy_slideold_max');
                                                                                                                                                                            $query.="therapy_slideold_max='".$therapy_slideold_max."'";
                                                                                                                                                                        }
                                                                                                                                                                        $query.="where therapy_slideold_id=".$therapy_slideold_id;
                                                                                                                                                                        $result=$this->B_db->run_query_put($query);
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
        //********************************************************************************************
        //********************************************************************************************

        //********************************************************************


        }
}