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
class Travel extends REST_Controller {

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
        if ($command=="add_travel_plan")
        {

            $travel_plan_id=$this->post('travel_plan_id') ;
            $travel_plan_name=$this->post('travel_plan_name') ;
            $travel_plan_desc=$this->post('travel_plan_desc') ;
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','travel');
            if($employeetoken[0]=='ok')
            {
//************************************************************************;****************************************
                $query="select * from travel_plan_tb where travel_plan_name='".$travel_plan_name."' OR travel_plan_id=".$travel_plan_id."";
                $result=$this->B_db->run_query($query);
                $num=count($result[0]);
                if ($num==0)
                {
                    $query="INSERT INTO travel_plan_tb(travel_plan_id, travel_plan_name, travel_plan_desc)
	                            VALUES ( $travel_plan_id,'$travel_plan_name', '$travel_plan_desc');";

                    $result=$this->B_db->run_query_put($query);
                    // $travel_plan_id=$this->db->insert_id();
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('travel_plan_id'=>$travel_plan_id)
                    ,'desc'=>'طرح مسافرتی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    $carmode=$result[0];
                    echo json_encode(array('result'=>"error"
                    ,"data"=>array('travel_plan_id'=>$carmode['travel_plan_id'])
                    ,'desc'=>'طرح مسافرتی تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
//***************************************************************************************************************
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }






        }

        if ($command=="get_travel_plan")
        {
//************************************************************************;****************************************

            $query="select * from travel_plan_tb where 1 ORDER BY travel_plan_id ASC";
            $result = $this->B_db->run_query($query);
            $output =array();
            foreach($result as $row)
            {
                $record=array();
                $record['travel_plan_id']=$row['travel_plan_id'];
                $record['travel_plan_name']=$row['travel_plan_name'];
                $record['travel_plan_desc']=$row['travel_plan_desc'];
                $output[]=$record;
            }
            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'طرح مسافرتی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

        }
        else
            if ($command=="delete_travel_plan")
            {
                $travel_plan_id=$this->post('travel_plan_id') ;

                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','travel');
                if($employeetoken[0]=='ok')
                {
//************************************************************************;****************************************
                    $output = array();$user_id=$employeetoken[0];

                    $query="DELETE FROM travel_plan_tb  where travel_plan_id=".$travel_plan_id."";
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



                if ($command=="modify_travel_plan")
                {
                    $travel_plan_id=$this->post('travel_plan_id') ;


                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','travel');
                    if($employeetoken[0]=='ok')
                    {
//*****************************************************************************************

                        $query="UPDATE travel_plan_tb SET ";

                        if(isset($_REQUEST['travel_plan_name'])){
                            $travel_plan_name=$this->post('travel_plan_name');
                            $query.="travel_plan_name='".$travel_plan_name."'";}

                        if(isset($_REQUEST['travel_plan_desc'])&&(isset($_REQUEST['travel_plan_name']))){ $query.=",";}
                        if(isset($_REQUEST['travel_plan_desc'])){
                            $travel_plan_desc=$this->post('travel_plan_desc');
                            $query.="travel_plan_desc='".$travel_plan_desc."'";}

                        $query.="where travel_plan_id=".$travel_plan_id;

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
                    if ($command=="add_travel_destination")
                    {

                        $travel_destination_name=$this->post('travel_destination_name') ;
                        $travel_destination_desc=$this->post('travel_destination_desc') ;
                        $travel_destination_priority=$this->post('travel_destination_priority') ;
                        $travel_destination_travel_plan_id=$this->post('travel_destination_travel_plan_id') ;



                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','travel');
                        if($employeetoken[0]=='ok')
                        {
//************************************************************************;****************************************
                            $query="select * from travel_destination_tb where  travel_destination_name='".$travel_destination_name."'";
                            $result=$this->B_db->run_query($query);
                            $num=count($result[0]);
                            if ($num==0)
                            {
                                $query="INSERT INTO travel_destination_tb( travel_destination_name, travel_destination_desc, travel_destination_priority,travel_destination_travel_plan_id)
	                            VALUES ( '$travel_destination_name', '$travel_destination_desc', '$travel_destination_priority',$travel_destination_travel_plan_id);";

                                $result=$this->B_db->run_query_put($query);
                                $travel_destination_id=$this->db->insert_id();
                                echo json_encode(array('result'=>"ok"
                                ,"data"=>array('travel_destination_id'=>$travel_destination_id)
                                ,'desc'=>'مقصد مسافرتی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }else{
                                $carmode=$result[0];
                                echo json_encode(array('result'=>"error"
                                ,"data"=>array('travel_destination_id'=>$carmode['travel_destination_id'])
                                ,'desc'=>'مقصد مسافرتی تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
//***************************************************************************************************************
                        }else{
                            echo json_encode(array('result'=>$employeetoken[0]
                            ,"data"=>$employeetoken[1]
                            ,'desc'=>$employeetoken[2]));

                        }






                    }
                    else
                        if ($command=="get_travel_destination")
                        {
//************************************************************************;****************************************

                            $query="select * from travel_destination_tb where 1 ORDER BY travel_destination_priority ASC";
                            $result = $this->B_db->run_query($query);
                            $output =array();
                            foreach($result as $row)
                            {
                                $record=array();
                                $record['travel_destination_id']=$row['travel_destination_id'];
                                $record['travel_destination_name']=$row['travel_destination_name'];
                                $record['travel_destination_desc']=$row['travel_destination_desc'];
                                $record['travel_destination_priority']=$row['travel_destination_priority'];
                                $record['travel_destination_travel_plan_id']=$row['travel_destination_travel_plan_id'];

                                $query1="select * from travel_plan_tb where travel_plan_id=".$row['travel_destination_travel_plan_id']." ";
                                $result1 = $this->B_db->run_query($query1);
								if(!empty($result1)){
									$travel_plan=$result1[0];
                                $num=count($result1[0]);
                                if($num>0){
                                    $record['travel_plan_name']=$travel_plan['travel_plan_name'];
                                }else{
                                    $record['travel_plan_name']="";

                                }
								}
                                
                                $output[]=$record;
                            }
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>$output
                            ,'desc'=>'مقصد مسافرتی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                        }
                        else
                            if ($command=="delete_travel_destination")
                            {
                                $travel_destination_id=$this->post('travel_destination_id') ;


                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','travel');
                                if($employeetoken[0]=='ok')
                                {
//************************************************************************;****************************************
                                    $output = array();$user_id=$employeetoken[0];

                                    $query="DELETE FROM travel_destination_tb  where travel_destination_id=".$travel_destination_id."";
                                    $result = $this->B_db->run_query_put($query);
                                    if($result){echo json_encode(array('result'=>"ok"
                                    ,"data"=>$output
                                    ,'desc'=>'مقصد مسافرتی حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }else{
                                        echo json_encode(array('result'=>"error"
                                        ,"data"=>$output
                                        ,'desc'=>'مقصد مسافرتی حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }
//***************************************************************************************************************
                                }else{
                                    echo json_encode(array('result'=>$employeetoken[0]
                                    ,"data"=>$employeetoken[1]
                                    ,'desc'=>$employeetoken[2]));

                                }

                            }
                            else



                                if ($command=="modify_travel_destination")
                                {
                                    $travel_destination_id=$this->post('travel_destination_id') ;


                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','travel');
                                    if($employeetoken[0]=='ok')
                                    {
//*****************************************************************************************

                                        $query="UPDATE travel_destination_tb SET ";

                                        if(isset($_REQUEST['travel_destination_name'])){
                                            $travel_destination_name=$this->post('travel_destination_name');
                                            $query.="travel_destination_name='".$travel_destination_name."'";}

                                        if(isset($_REQUEST['travel_destination_desc'])&&(isset($_REQUEST['travel_destination_name']))){ $query.=",";}
                                        if(isset($_REQUEST['travel_destination_desc'])){
                                            $travel_destination_desc=$this->post('travel_destination_desc');
                                            $query.="travel_destination_desc='".$travel_destination_desc."'";}

                                        if(isset($_REQUEST['travel_destination_priority'])&&(isset($_REQUEST['travel_destination_desc'])||isset($_REQUEST['travel_destination_name']))){ $query.=",";}
                                        if(isset($_REQUEST['travel_destination_priority'])){
                                            $travel_destination_priority=$this->post('travel_destination_priority');
                                            $query.="travel_destination_priority='".$travel_destination_priority."'";}

                                        if(isset($_REQUEST['travel_destination_travel_plan_id'])&&(isset($_REQUEST['travel_destination_priority'])||isset($_REQUEST['travel_destination_desc'])||isset($_REQUEST['travel_destination_name']))){ $query.=",";}
                                        if(isset($_REQUEST['travel_destination_travel_plan_id'])){
                                            $travel_destination_travel_plan_id=$this->post('travel_destination_travel_plan_id');
                                            $query.="travel_destination_travel_plan_id=".$travel_destination_travel_plan_id."";}


                                        $query.=" where travel_destination_id=".$travel_destination_id;

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
                                    if ($command=="add_travel_destinationplan")
                                    {
                                        $travel_destinationplan_travel_plan_id=$this->post('travel_destinationplan_travel_plan_id') ;
                                        $travel_destinationplan_travel_destination_id=$this->post('travel_destinationplan_travel_destination_id') ;



                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','travel');
                                        if($employeetoken[0]=='ok')
                                        {
//****************************************************************************************************************
                                            $query="select * from travel_destinationplan_tb where travel_destinationplan_travel_plan_id=$travel_destinationplan_travel_plan_id AND travel_destinationplan_travel_destination_id=".$travel_destinationplan_travel_destination_id."";
                                            $result=$this->B_db->run_query($query);
                                            $num=count($result[0]);
                                            if ($num==0)
                                            {
                                                $query="INSERT INTO travel_destinationplan_tb(travel_destinationplan_travel_plan_id, travel_destinationplan_travel_destination_id)
	                            VALUES ( $travel_destinationplan_travel_plan_id,$travel_destinationplan_travel_destination_id);";

                                                $result=$this->B_db->run_query_put($query);
                                                //   $travel_time_id=$this->db->insert_id();

                                                echo json_encode(array('result'=>"ok"
                                                ,"data"=>''
                                                ,'desc'=>'طرح مسافرتی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                            }else{
                                                $carmode=$result[0];
                                                echo json_encode(array('result'=>"error"
                                                ,"data"=>''
                                                ,'desc'=>'طرح مسافرت  تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                            }
//***************************************************************************************************************
                                        }else{
                                            echo json_encode(array('result'=>$employeetoken[0]
                                            ,"data"=>$employeetoken[1]
                                            ,'desc'=>$employeetoken[2]));
                                        }
                                    }
									       else
                                        if ($command=="get_travel_destinationplan")
                                        {
//************************************************************************;****************************************
                                        $travel_destination_id=$this->post('travel_destination_id') ;

                                            $query="select * from travel_destinationplan_tb,travel_plan_tb where travel_destinationplan_travel_destination_id=$travel_destination_id AND travel_destinationplan_travel_plan_id=travel_plan_id ORDER BY travel_destinationplan_id ASC";
                                            $result = $this->B_db->run_query($query);
                                            $output =array();
											if(!empty($result)){
                                            foreach($result as $row)
                                            {
                                                $record=array();
                                                $record['travel_plan_id']=$row['travel_plan_id'];
                                                $record['travel_plan_name']=$row['travel_plan_name'];
                                                $record['travel_destinationplan_id']=$row['travel_destinationplan_id'];
                                                $record['travel_destinationplan_travel_plan_id']=$row['travel_destinationplan_travel_plan_id'];
                                                $record['travel_destinationplan_travel_destination_id']=$row['travel_destinationplan_travel_destination_id'];
                                                $output[]=$record;
                                            }
											}
                                            echo json_encode(array('result'=>"ok"
                                            ,"data"=>$output
                                            ,'desc'=>'طرح مسافرتی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                        }else
                                            if ($command=="delete_travel_destinationplan")
                                            {
                                                $travel_destinationplan_id=$this->post('travel_destinationplan_id') ;

                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','travel');
                                                if($employeetoken[0]=='ok')
                                                {
//************************************************************************;****************************************
                                                    $output = array();
													$user_id=$employeetoken[0];

                                                    $query="DELETE FROM travel_destinationplan_tb  where travel_destinationplan_id=".$travel_destinationplan_id."";
                                                    $result = $this->B_db->run_query_put($query);
                                                    if($result){echo json_encode(array('result'=>"ok"
                                                    ,"data"=>$output
                                                    ,'desc'=>'طرح مسافرتی حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                    }else{
                                                        echo json_encode(array('result'=>"error"
                                                        ,"data"=>$output
                                                        ,'desc'=>'طرح مساتی   حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
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
                                    if ($command=="add_travel_time")
                                    {
                                        $travel_time_id=$this->post('travel_time_id') ;
                                        $travel_time_name=$this->post('travel_time_name') ;
                                        $travel_time_percent=$this->post('travel_time_percent') ;



                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','travel');
                                        if($employeetoken[0]=='ok')
                                        {
//****************************************************************************************************************
                                            $query="select * from travel_time_tb where travel_time_id=$travel_time_id AND travel_time_name='".$travel_time_name."'";
                                            $result=$this->B_db->run_query($query);
                                            $num=count($result[0]);
                                            if ($num==0)
                                            {
                                                $query="INSERT INTO travel_time_tb(travel_time_id, travel_time_name, travel_time_percent)
	                            VALUES ( $travel_time_id,'$travel_time_name', '$travel_time_percent');";

                                                $result=$this->B_db->run_query_put($query);
                                                //   $travel_time_id=$this->db->insert_id();

                                                echo json_encode(array('result'=>"ok"
                                                ,"data"=>array('travel_time_id'=>$travel_time_id)
                                                ,'desc'=>'مدت زمان بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                            }else{
                                                $carmode=$result[0];
                                                echo json_encode(array('result'=>"error"
                                                ,"data"=>array('travel_time_id'=>$carmode['travel_time_id'])
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
                                        if ($command=="get_travel_time")
                                        {
//************************************************************************;****************************************

                                            $query="select * from travel_time_tb where 1 ORDER BY travel_time_id ASC";
                                            $result = $this->B_db->run_query($query);
                                            $output =array();
                                            foreach($result as $row)
                                            {
                                                $record=array();
                                                $record['travel_time_id']=$row['travel_time_id'];
                                                $record['travel_time_name']=$row['travel_time_name'];
                                                $record['travel_time_percent']=$row['travel_time_percent'];
                                                $output[]=$record;
                                            }
                                            echo json_encode(array('result'=>"ok"
                                            ,"data"=>$output
                                            ,'desc'=>'مدت زمان بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                        }
                                        else
                                            if ($command=="delete_travel_time")
                                            {
                                                $travel_time_id=$this->post('travel_time_id') ;

                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','travel');
                                                if($employeetoken[0]=='ok')
                                                {
//************************************************************************;****************************************
                                                    $output = array();$user_id=$employeetoken[0];

                                                    $query="DELETE FROM travel_time_tb  where travel_time_id=".$travel_time_id."";
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



                                                if ($command=="modify_travel_time")
                                                {
                                                    $travel_time_id=$this->post('travel_time_id') ;


                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','travel');
                                                    if($employeetoken[0]=='ok')
                                                    {
//*****************************************************************************************

                                                        $query="UPDATE travel_time_tb SET ";

                                                        if(isset($_REQUEST['travel_time_name'])){
                                                            $travel_time_name=$this->post('travel_time_name');
                                                            $query.="travel_time_name='".$travel_time_name."'";}

                                                        if(isset($_REQUEST['travel_time_percent'])&&(isset($_REQUEST['travel_time_name']))){ $query.=",";}
                                                        if(isset($_REQUEST['travel_time_percent'])){
                                                            $travel_time_percent=$this->post('travel_time_percent');
                                                            $query.="travel_time_percent='".$travel_time_percent."'";}

                                                        $query.="where travel_time_id=".$travel_time_id;

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
                                                    if ($command=="add_travel_coverage")
                                                    {
                                                        $travel_coverage_id=$this->post('travel_coverage_id') ;
                                                        $travel_coverage_name=$this->post('travel_coverage_name') ;
                                                        $travel_coverage_price=$this->post('travel_coverage_price') ;



                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','travel');
                                                        if($employeetoken[0]=='ok')
                                                        {
//****************************************************************************************************************
                                                            $query="select * from travel_coverage_tb where travel_coverage_name='".$travel_coverage_name."'";
                                                            $result=$this->B_db->run_query($query);
                                                            $num=count($result[0]);
                                                            if ($num==0)
                                                            {
                                                                $query="INSERT INTO travel_coverage_tb(travel_coverage_id, travel_coverage_name, travel_coverage_price)
	                            VALUES ( $travel_coverage_id,'$travel_coverage_name', '$travel_coverage_price');";

                                                                $result=$this->B_db->run_query_put($query);
                                                                //  $travel_coverage_id=$this->db->insert_id();

                                                                echo json_encode(array('result'=>"ok"
                                                                ,"data"=>array('travel_coverage_id'=>$travel_coverage_id)
                                                                ,'desc'=>'پوشش بیمه مسافرتی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                            }else{
                                                                $carmode=$result[0];
                                                                echo json_encode(array('result'=>"error"
                                                                ,"data"=>array('travel_coverage_id'=>$carmode['travel_coverage_id'])
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
                                                        if ($command=="get_travel_coverage")
                                                        {
//************************************************************************;****************************************

                                                            $query="select * from travel_coverage_tb where 1 ORDER BY travel_coverage_price ASC";
                                                            $result = $this->B_db->run_query($query);
                                                            $output =array();
                                                            foreach($result as $row)
                                                            {
                                                                $record=array();
                                                                $record['travel_coverage_id']=$row['travel_coverage_id'];
                                                                $record['travel_coverage_name']=$row['travel_coverage_name'];
                                                                $record['travel_coverage_price']=$row['travel_coverage_price'];
                                                                $output[]=$record;
                                                            }
                                                            echo json_encode(array('result'=>"ok"
                                                            ,"data"=>$output
                                                            ,'desc'=>'پوشش بیمه مسافرتی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                        }
                                                        else
                                                            if ($command=="delete_travel_coverage")
                                                            {
                                                                $travel_coverage_id=$this->post('travel_coverage_id') ;


                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','travel');
                                                                if($employeetoken[0]=='ok')
                                                                {
//************************************************************************;****************************************
                                                                    $output = array();$user_id=$employeetoken[0];

                                                                    $query="DELETE FROM travel_coverage_tb  where travel_coverage_id=".$travel_coverage_id."";
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



                                                                if ($command=="modify_travel_coverage")
                                                                {
                                                                    $travel_coverage_id=$this->post('travel_coverage_id') ;


                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','travel');
                                                                    if($employeetoken[0]=='ok')
                                                                    {
//*****************************************************************************************

                                                                        $query="UPDATE travel_coverage_tb SET ";

                                                                        if(isset($_REQUEST['travel_coverage_name'])){
                                                                            $travel_coverage_name=$this->post('travel_coverage_name');
                                                                            $query.="travel_coverage_name='".$travel_coverage_name."'";}

                                                                        if(isset($_REQUEST['travel_coverage_price'])&&(isset($_REQUEST['travel_coverage_name']))){ $query.=",";}
                                                                        if(isset($_REQUEST['travel_coverage_price'])){
                                                                            $travel_coverage_price=$this->post('travel_coverage_price');
                                                                            $query.="travel_coverage_price='".$travel_coverage_price."'";}

                                                                        $query.="where travel_coverage_id=".$travel_coverage_id;

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
                                                                    if ($command=="add_travel_helper")
                                                                    {
                                                                        $travel_helper_id=$this->post('travel_helper_id') ;
                                                                        $travel_helper_name=$this->post('travel_helper_name') ;
                                                                        $travel_helper_desc=$this->post('travel_helper_desc') ;



                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','travel');
                                                                        if($employeetoken[0]=='ok')
                                                                        {
//****************************************************************************************************************
                                                                            $query="select * from travel_helper_tb where travel_helper_name='".$travel_helper_name."'";
                                                                            $result=$this->B_db->run_query($query);
                                                                            $num=count($result[0]);
                                                                            if ($num==0)
                                                                            {
                                                                                $query="INSERT INTO travel_helper_tb(travel_helper_id, travel_helper_name, travel_helper_desc)
	                            VALUES ( $travel_helper_id,'$travel_helper_name', '$travel_helper_desc');";

                                                                                $result=$this->B_db->run_query_put($query);
                                                                                //  $travel_coverage_id=$this->db->insert_id();

                                                                                echo json_encode(array('result'=>"ok"
                                                                                ,"data"=>array('travel_helper_id'=>$travel_helper_id)
                                                                                ,'desc'=>'کمک رسان بیمه مسافرتی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                            }else{
                                                                                $carmode=$result[0];
                                                                                echo json_encode(array('result'=>"error"
                                                                                ,"data"=>array('travel_helper_id'=>$carmode['travel_helper_id'])
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
                                                                        if ($command=="get_travel_helper")
                                                                        {
//************************************************************************;****************************************

                                                                            $query="select * from travel_helper_tb where 1 ORDER BY travel_helper_id ASC";
                                                                            $result = $this->B_db->run_query($query);
                                                                            $output =array();
                                                                            foreach($result as $row)
                                                                            {
                                                                                $record=array();
                                                                                $record['travel_helper_id']=$row['travel_helper_id'];
                                                                                $record['travel_helper_name']=$row['travel_helper_name'];
                                                                                $record['travel_helper_desc']=$row['travel_helper_desc'];
                                                                                $output[]=$record;
                                                                            }
                                                                            echo json_encode(array('result'=>"ok"
                                                                            ,"data"=>$output
                                                                            ,'desc'=>'کمک رسان بیمه مسافرتی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                        }
                                                                        else
                                                                            if ($command=="delete_travel_helper")
                                                                            {
                                                                                $travel_helper_id=$this->post('travel_helper_id') ;


                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','travel');
                                                                                if($employeetoken[0]=='ok')
                                                                                {
//************************************************************************;****************************************
                                                                                    $output = array();$user_id=$employeetoken[0];

                                                                                    $query="DELETE FROM travel_helper_tb  where travel_helper_id=".$travel_helper_id."";
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



                                                                                if ($command=="modify_travel_helper")
                                                                                {
                                                                                    $travel_helper_id=$this->post('travel_helper_id') ;


                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','travel');
                                                                                    if($employeetoken[0]=='ok')
                                                                                    {
//*****************************************************************************************

                                                                                        $query="UPDATE travel_helper_tb SET ";

                                                                                        if(isset($_REQUEST['travel_helper_name'])){
                                                                                            $travel_helper_name=$this->post('travel_helper_name');
                                                                                            $query.="travel_helper_name='".$travel_helper_name."'";}

                                                                                        if(isset($_REQUEST['travel_helper_desc'])&&(isset($_REQUEST['travel_helper_name']))){ $query.=",";}
                                                                                        if(isset($_REQUEST['travel_helper_desc'])){
                                                                                            $travel_helper_desc=$this->post('travel_helper_desc');
                                                                                            $query.="travel_helper_desc='".$travel_helper_desc."'";}

                                                                                        $query.="where travel_helper_id=".$travel_helper_id;

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
                                                                                    if ($command=="add_travel_passenger")
                                                                                    {
                                                                                        $travel_passenger_id=$this->post('travel_passenger_id') ;
                                                                                        $travel_passenger_name=$this->post('travel_passenger_name') ;
                                                                                        $travel_passenger_desc=$this->post('travel_passenger_desc') ;



                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','travel');
                                                                                        if($employeetoken[0]=='ok')
                                                                                        {
//****************************************************************************************************************
                                                                                            $query="select * from travel_passenger_tb where travel_passenger_name='".$travel_passenger_name."'";
                                                                                            $result=$this->B_db->run_query($query);
                                                                                            $num=count($result[0]);
                                                                                            if ($num==0)
                                                                                            {
                                                                                                $query="INSERT INTO travel_passenger_tb(travel_passenger_id, travel_passenger_name, travel_passenger_desc)
	                            VALUES ( $travel_passenger_id,'$travel_passenger_name', '$travel_passenger_desc');";

                                                                                                $result=$this->B_db->run_query_put($query);
                                                                                                //  $travel_coverage_id=$this->db->insert_id();

                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                ,"data"=>array('travel_passenger_id'=>$travel_passenger_id)
                                                                                                ,'desc'=>'تعداد مسافر بیمه مسافرتی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                            }else{
                                                                                                $carmode=$result[0];
                                                                                                echo json_encode(array('result'=>"error"
                                                                                                ,"data"=>array('travel_passenger_id'=>$carmode['travel_passenger_id'])
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
                                                                                        if ($command=="get_travel_passenger")
                                                                                        {
//************************************************************************;****************************************

                                                                                            $query="select * from travel_passenger_tb where 1 ORDER BY travel_passenger_id ASC";
                                                                                            $result = $this->B_db->run_query($query);
                                                                                            $output =array();
                                                                                            foreach($result as $row)
                                                                                            {
                                                                                                $record=array();
                                                                                                $record['travel_passenger_id']=$row['travel_passenger_id'];
                                                                                                $record['travel_passenger_name']=$row['travel_passenger_name'];
                                                                                                $record['travel_passenger_desc']=$row['travel_passenger_desc'];
                                                                                                $output[]=$record;
                                                                                            }
                                                                                            echo json_encode(array('result'=>"ok"
                                                                                            ,"data"=>$output
                                                                                            ,'desc'=>' تعداد مسافر بیمه مسافرتی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                        }
                                                                                        else
                                                                                            if ($command=="delete_travel_passenger")
                                                                                            {
                                                                                                $travel_passenger_id=$this->post('travel_passenger_id') ;


                                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','travel');
                                                                                                if($employeetoken[0]=='ok')
                                                                                                {
//************************************************************************;****************************************
                                                                                                    $output = array();$user_id=$employeetoken[0];

                                                                                                    $query="DELETE FROM travel_passenger_tb  where travel_passenger_id=".$travel_passenger_id."";
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



                                                                                                if ($command=="modify_travel_passenger")
                                                                                                {
                                                                                                    $travel_passenger_id=$this->post('travel_passenger_id') ;


                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','travel');
                                                                                                    if($employeetoken[0]=='ok')
                                                                                                    {
//*****************************************************************************************

                                                                                                        $query="UPDATE travel_passenger_tb SET ";

                                                                                                        if(isset($_REQUEST['travel_passenger_name'])){
                                                                                                            $travel_passenger_name=$this->post('travel_passenger_name');
                                                                                                            $query.="travel_passenger_name='".$travel_passenger_name."'";}

                                                                                                        if(isset($_REQUEST['travel_passenger_desc'])&&(isset($_REQUEST['travel_passenger_name']))){ $query.=",";}
                                                                                                        if(isset($_REQUEST['travel_passenger_desc'])){
                                                                                                            $travel_passenger_desc=$this->post('travel_passenger_desc');
                                                                                                            $query.="travel_passenger_desc='".$travel_passenger_desc."'";}

                                                                                                        $query.="where travel_passenger_id=".$travel_passenger_id;

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
                                                                                                    if ($command=="add_travel_price")
                                                                                                    {
                                                                                                        $travel_price_plan_id=$this->post('travel_price_plan_id') ;
                                                                                                        $travel_price_time_id=$this->post('travel_price_time_id') ;
                                                                                                        $travel_price_coverage_id=$this->post('travel_price_coverage_id') ;
                                                                                                        $travel_price_helper_id=$this->post('travel_price_helper_id') ;
                                                                                                        $travel_price_desc=$this->post('travel_price_desc') ;
                                                                                                        $travel_price_disc=$this->post('travel_price_disc') ;
                                                                                                        $travel_price_fieldcompany_id=$this->post('travel_price_fieldcompany_id') ;



                                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','travel');
                                                                                                        if($employeetoken[0]=='ok')
                                                                                                        {
//****************************************************************************************************************
                                                                                                            $query="select * from travel_price_tb where travel_price_fieldcompany_id=".$travel_price_fieldcompany_id." AND travel_price_helper_id=".$travel_price_helper_id." AND travel_price_coverage_id=".$travel_price_coverage_id." AND travel_price_time_id=".$travel_price_time_id." AND travel_price_plan_id=".$travel_price_plan_id."";
                                                                                                            $result=$this->B_db->run_query($query);
                                                                                                            $num=count($result[0]);
                                                                                                            if ($num==0)
                                                                                                            {
                                                                                                                $query1="INSERT INTO travel_price_tb(travel_price_coverage_id,travel_price_plan_id,travel_price_time_id, travel_price_helper_id, travel_price_desc, travel_price_disc, travel_price_fieldcompany_id)
	                            VALUES ( $travel_price_coverage_id,$travel_price_plan_id,$travel_price_time_id,$travel_price_helper_id,'$travel_price_desc','$travel_price_disc',$travel_price_fieldcompany_id);";

                                                                                                                $result1=$this->B_db->run_query_put($query1);
                                                                                                                $travel_price_id=$this->db->insert_id();
                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                ,"data"=>array('travel_price_id'=>$travel_price_id,'query'=>$query)
                                                                                                                ,'desc'=>'قیمت بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                            }else{
                                                                                                                $carmode=$result[0];
                                                                                                                echo json_encode(array('result'=>"error"
                                                                                                                ,"data"=>array('travel_price_id'=>$carmode['travel_price_id'])
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
                                                                                                        if ($command=="get_travel_price")
                                                                                                        {
//************************************************************************;****************************************

                                                                                                            $query="select * from travel_price_tb,fieldcompany_tb,travel_plan_tb,company_tb,travel_coverage_tb,travel_time_tb,travel_helper_tb
  where travel_price_fieldcompany_id=fieldcompany_id
  AND travel_price_plan_id=travel_plan_id
  AND travel_price_time_id=travel_time_id
  AND travel_price_coverage_id=travel_coverage_id
  AND travel_price_helper_id=travel_helper_id
  AND fieldcompany_company_id=company_id
 ORDER BY travel_price_id ASC";
                                                                                                            $result = $this->B_db->run_query($query);
                                                                                                            $output =array();
                                                                                                            foreach($result as $row)
                                                                                                            {
                                                                                                                $record=array();
                                                                                                                $record['travel_price_id']=$row['travel_price_id'];
                                                                                                                $record['travel_price_plan_id']=$row['travel_price_plan_id'];
                                                                                                                $record['travel_plan_name']=$row['travel_plan_name'];
                                                                                                                $record['travel_price_fieldcompany_id']=$row['travel_price_fieldcompany_id'];
                                                                                                                $record['company_name']=$row['company_name'];
                                                                                                                $record['company_logo_url']=IMGADD.$row['company_logo_url'];
                                                                                                                $record['travel_price_time_id']=$row['travel_price_time_id'];
                                                                                                                $record['travel_time_name']=$row['travel_time_name'];
                                                                                                                $record['travel_price_coverage_id']=$row['travel_price_coverage_id'];
                                                                                                                $record['travel_coverage_name']=$row['travel_coverage_name'];
                                                                                                                $record['travel_price_helper_id']=$row['travel_price_helper_id'];
                                                                                                                $record['travel_price_desc']=$row['travel_price_desc'];
                                                                                                                $record['travel_price_disc']=$row['travel_price_disc'];
                                                                                                                $record['travel_helper_name']=$row['travel_helper_name'];
                                                                                                                $record['travel_price_deactive']=$row['travel_price_deactive'];
                                                                                                                $output[]=$record;
                                                                                                            }
                                                                                                            echo json_encode(array('result'=>"ok"
                                                                                                            ,"data"=>$output
                                                                                                            ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                                        }
                                                                                                        else
                                                                                                            if ($command=="delete_travel_price")
                                                                                                            {
                                                                                                                $travel_price_id=$this->post('travel_price_id');

                                                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','travel');
                                                                                                                if($employeetoken[0]=='ok')
                                                                                                                {
//************************************************************************;****************************************
                                                                                                                    $output = array();$user_id=$employeetoken[0];

                                                                                                                    $query="DELETE FROM travel_price_tb  where travel_price_id=".$travel_price_id."";
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



                                                                                                                if ($command=="modify_travel_price")
                                                                                                                {
                                                                                                                    $travel_price_id=$this->post('travel_price_id');

                                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','travel');
                                                                                                                    if($employeetoken[0]=='ok')
                                                                                                                    {
//*****************************************************************************************

                                                                                                                        $query="UPDATE travel_price_tb SET ";

                                                                                                                        if(isset($_REQUEST['travel_price_helper_id'])){
                                                                                                                            $travel_price_helper_id=$this->post('travel_price_helper_id');
                                                                                                                            $query.="travel_price_helper_id='".$travel_price_helper_id."'";}



                                                                                                                        if(isset($_REQUEST['travel_price_deactive'])&&(isset($_REQUEST['travel_price_helper_id']))){ $query.=",";}
                                                                                                                        if(isset($_REQUEST['travel_price_deactive'])){
                                                                                                                            $travel_price_deactive=$this->post('travel_price_deactive');
                                                                                                                            $query.="travel_price_deactive=".$travel_price_deactive."";}

                                                                                                                        if(isset($_REQUEST['travel_price_disc'])&&(isset($_REQUEST['travel_price_deactive'])||isset($_REQUEST['travel_price_helper_id']))){ $query.=",";}
                                                                                                                        if(isset($_REQUEST['travel_price_disc'])){
                                                                                                                            $travel_price_disc=$this->post('travel_price_disc');
                                                                                                                            $query.="travel_price_disc=".$travel_price_disc."";}


                                                                                                                        if(isset($_REQUEST['travel_price_desc'])&&(isset($_REQUEST['travel_price_disc'])||isset($_REQUEST['travel_price_deactive'])||isset($_REQUEST['travel_price_helper_id']))){ $query.=",";}
                                                                                                                        if(isset($_REQUEST['travel_price_desc'])){
                                                                                                                            $travel_price_desc=$this->post('travel_price_desc');
                                                                                                                            $query.="travel_price_desc='".$travel_price_desc."' ";}



                                                                                                                        $query.=" where travel_price_id=".$travel_price_id;

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
                                                                                                                    if ($command=="add_price_passenger")
                                                                                                                    {
                                                                                                                        $travel_price_passenger_travel_price_id=$this->post('travel_price_passenger_travel_price_id');
                                                                                                                        $travel_price_passenger_travel_passenger_id=$this->post('travel_price_passenger_travel_passenger_id');
                                                                                                                        $travel_price_passenger_price=$this->post('travel_price_passenger_price');

                                                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','travel');
                                                                                                                        if($employeetoken[0]=='ok')
                                                                                                                        {
//****************************************************************************************************************
                                                                                                                            $query="select * from travel_price_passenger_tb where travel_price_passenger_travel_passenger_id=".$travel_price_passenger_travel_passenger_id." AND travel_price_passenger_travel_price_id=".$travel_price_passenger_travel_price_id."";
                                                                                                                            $result=$this->B_db->run_query($query);
                                                                                                                            $num=count($result[0]);
                                                                                                                            if ($num==0)
                                                                                                                            {
                                                                                                                                $query="INSERT INTO travel_price_passenger_tb(travel_price_passenger_travel_price_id,travel_price_passenger_travel_passenger_id, travel_price_passenger_price)
	                            VALUES ( $travel_price_passenger_travel_price_id,$travel_price_passenger_travel_passenger_id, '$travel_price_passenger_price');";

                                                                                                                                $result=$this->B_db->run_query_put($query);
                                                                                                                                $travel_price_passenger_id=$this->db->insert_id();
                                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                                ,"data"=>array('travel_price_passenger_id'=>$travel_price_passenger_id,'query'=>$query)
                                                                                                                                ,'desc'=>'قیمت  بر اساس سن در بیمه مسافرتی  اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                            }else{
                                                                                                                                $carmode=$result[0];
                                                                                                                                echo json_encode(array('result'=>"error"
                                                                                                                                ,"data"=>array('travel_price_passenger_id'=>$carmode['travel_price_passenger_id'])
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
                                                                                                                        if ($command=="get_price_passenger")
                                                                                                                        {
                                                                                                                            $travel_price_id=$this->post('travel_price_id');

//************************************************************************;****************************************

                                                                                                                            $query="select * from travel_price_passenger_tb,travel_passenger_tb where travel_passenger_id=travel_price_passenger_travel_passenger_id AND travel_price_passenger_travel_price_id=$travel_price_id  ORDER BY travel_price_passenger_id ASC";
                                                                                                                            $result = $this->B_db->run_query($query);
                                                                                                                            $output =array();
                                                                                                                            foreach($result as $row)
                                                                                                                            {
                                                                                                                                $record=array();
                                                                                                                                $record['travel_price_passenger_id']=$row['travel_price_passenger_id'];
                                                                                                                                $record['travel_price_passenger_travel_price_id']=$row['travel_price_passenger_travel_price_id'];
                                                                                                                                $record['travel_price_passenger_travel_passenger_id']=$row['travel_price_passenger_travel_passenger_id'];
                                                                                                                                $record['travel_passenger_name']=$row['travel_passenger_name'];
                                                                                                                                $record['travel_price_passenger_price']=$row['travel_price_passenger_price'];
                                                                                                                                $output[]=$record;
                                                                                                                            }
                                                                                                                            echo json_encode(array('result'=>"ok"
                                                                                                                            ,"data"=>$output
                                                                                                                            ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                                                        }
                                                                                                                        else
                                                                                                                            if ($command=="delete_price_passenger")
                                                                                                                            {
                                                                                                                                $travel_price_passenger_id=$this->post('travel_price_passenger_id');

                                                                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','travel');
                                                                                                                                if($employeetoken[0]=='ok')
                                                                                                                                {
//************************************************************************;****************************************
                                                                                                                                    $output = array();$user_id=$employeetoken[0];

                                                                                                                                    $query="DELETE FROM travel_price_passenger_tb  where travel_price_passenger_id=".$travel_price_passenger_id."";
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



                                                                                                                                if ($command=="modify_price_passenger")
                                                                                                                                {
                                                                                                                                    $travel_price_passenger_id=$this->post('travel_price_passenger_id');

                                                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','travel');
                                                                                                                                    if($employeetoken[0]=='ok')
                                                                                                                                    {
//*****************************************************************************************

                                                                                                                                        $query="UPDATE travel_price_passenger_tb SET ";


                                                                                                                                        if(isset($_REQUEST['travel_price_passenger_price'])){
                                                                                                                                            $travel_price_passenger_price=$this->post('travel_price_passenger_price');
                                                                                                                                            $query.="travel_price_passenger_price='".$travel_price_passenger_price."'";}

                                                                                                                                        $query.="where travel_price_passenger_id=".$travel_price_passenger_id;

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


        }
}