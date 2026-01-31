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
class Elevator extends REST_Controller {

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
        $this->load->helper('time_helper');
        $command = $this->post("command");
        if ($command=="get_yearofcons")
        {
            $output =array();
            $def=date("Y")-jdate('Y','',"",'','en');
            $ynow=date("Y");
            for($i=0;$i<30;$i++)
            {
                $record=array();
                $record['elevator_yearofcons_id']=$i;
                $record['elevator_yearofcons_name']=($ynow-$i).'-'.($ynow-$i-$def);
                $output[]=$record;
            }
            $record=array();
            $record['elevator_yearofcons_id']=$i;
            $record['elevator_yearofcons_name']=($ynow-$i).'-'.($ynow-$i-$def).' و ماقبل';
            $output[]=$record;
            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>' سال ساخت با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
        else
            if ($command=="get_numberstop")
            {
                $output =array();
                 for($i=2;$i<60;$i++)
                {
                    $record=array();
                    $record['elevator_numberstop_id']=$i;
                    $record['elevator_numberstop_name']=$i.' توقف';
                    $output[]=$record;
                }
                echo json_encode(array('result'=>"ok"
                ,"data"=>$output
                ,'desc'=>'  تعداد توقف با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }
            else
                if ($command=="get_capacity")
                {
                    $output =array();
                    for($i=2;$i<60;$i++)
                    {
                        $record=array();
                        $record['elevator_capacity_id']=$i;
                        $record['elevator_capacity_name']=$i.' نفر';
                        $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'  تعداد توقف با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
                else
        if ($command=="add_elevator_uses")
        {

            $elevator_uses_id=$this->post('elevator_uses_id') ;
            $elevator_uses_name=$this->post('elevator_uses_name') ;
            $elevator_uses_desc=$this->post('elevator_uses_desc') ;
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','travel');
            if($employeetoken[0]=='ok')
            {
//************************************************************************;****************************************
                $query="select * from elevator_uses_tb where elevator_uses_name='".$elevator_uses_name."' OR elevator_uses_id=".$elevator_uses_id."";
                $result=$this->B_db->run_query($query);
                $num=count($result[0]);
                if ($num==0)
                {
                    $query="INSERT INTO elevator_uses_tb(elevator_uses_id, elevator_uses_name, elevator_uses_desc)
	                            VALUES ( $elevator_uses_id,'$elevator_uses_name', '$elevator_uses_desc');";

                    $result=$this->B_db->run_query_put($query);
                    // $elevator_uses_id=$this->db->insert_id();
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('elevator_uses_id'=>$elevator_uses_id)
                    ,'desc'=>'طرح مسافرتی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    $carmode=$result[0];
                    echo json_encode(array('result'=>"error"
                    ,"data"=>array('elevator_uses_id'=>$carmode['elevator_uses_id'])
                    ,'desc'=>'طرح مسافرتی تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
//***************************************************************************************************************
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }






        }

        if ($command=="get_elevator_uses")
        {
//************************************************************************;****************************************

            $query="select * from elevator_uses_tb where 1 ORDER BY elevator_uses_id ASC";
            $result = $this->B_db->run_query($query);
            $output =array();
            foreach($result as $row)
            {
                $record=array();
                $record['elevator_uses_id']=$row['elevator_uses_id'];
                $record['elevator_uses_name']=$row['elevator_uses_name'];
                $record['elevator_uses_desc']=$row['elevator_uses_desc'];
                $output[]=$record;
            }
            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'طرح مسافرتی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

        }
        else
            if ($command=="delete_elevator_uses")
            {
                $elevator_uses_id=$this->post('elevator_uses_id') ;

                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','travel');
                if($employeetoken[0]=='ok')
                {
//************************************************************************;****************************************
                    $output = array();$user_id=$employeetoken[0];

                    $query="DELETE FROM elevator_uses_tb  where elevator_uses_id=".$elevator_uses_id."";
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



                if ($command=="modify_elevator_uses")
                {
                    $elevator_uses_id=$this->post('elevator_uses_id') ;


                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','travel');
                    if($employeetoken[0]=='ok')
                    {
//*****************************************************************************************

                        $query="UPDATE elevator_uses_tb SET ";

                        if(isset($_REQUEST['elevator_uses_name'])){
                            $elevator_uses_name=$this->post('elevator_uses_name');
                            $query.="elevator_uses_name='".$elevator_uses_name."'";}

                        if(isset($_REQUEST['elevator_uses_desc'])&&(isset($_REQUEST['elevator_uses_name']))){ $query.=",";}
                        if(isset($_REQUEST['elevator_uses_desc'])){
                            $elevator_uses_desc=$this->post('elevator_uses_desc');
                            $query.="elevator_uses_desc='".$elevator_uses_desc."'";}

                        $query.="where elevator_uses_id=".$elevator_uses_id;

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
//*************************************************************************************************************
                                else
                                    if ($command=="add_elevator_kind")
                                    {
                                        $elevator_kind_id=$this->post('elevator_kind_id') ;
                                        $elevator_kind_name=$this->post('elevator_kind_name') ;
                                        $elevator_kind_percent=$this->post('elevator_kind_percent') ;



                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','travel');
                                        if($employeetoken[0]=='ok')
                                        {
//****************************************************************************************************************
                                            $query="select * from elevator_kind_tb where elevator_kind_id=$elevator_kind_id AND elevator_kind_name='".$elevator_kind_name."'";
                                            $result=$this->B_db->run_query($query);
                                            $num=count($result[0]);
                                            if ($num==0)
                                            {
                                                $query="INSERT INTO elevator_kind_tb(elevator_kind_id, elevator_kind_name, elevator_kind_percent)
	                            VALUES ( $elevator_kind_id,'$elevator_kind_name', '$elevator_kind_percent');";

                                                $result=$this->B_db->run_query_put($query);
                                                //   $elevator_kind_id=$this->db->insert_id();

                                                echo json_encode(array('result'=>"ok"
                                                ,"data"=>array('elevator_kind_id'=>$elevator_kind_id)
                                                ,'desc'=>'مدت زمان بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                            }else{
                                                $carmode=$result[0];
                                                echo json_encode(array('result'=>"error"
                                                ,"data"=>array('elevator_kind_id'=>$carmode['elevator_kind_id'])
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
                                        if ($command=="get_elevator_kind")
                                        {
//************************************************************************;****************************************

                                            $query="select * from elevator_kind_tb where 1 ORDER BY elevator_kind_id ASC";
                                            $result = $this->B_db->run_query($query);
                                            $output =array();
                                            foreach($result as $row)
                                            {
                                                $record=array();
                                                $record['elevator_kind_id']=$row['elevator_kind_id'];
                                                $record['elevator_kind_name']=$row['elevator_kind_name'];
                                                $record['elevator_kind_percent']=$row['elevator_kind_percent'];
                                                $output[]=$record;
                                            }
                                            echo json_encode(array('result'=>"ok"
                                            ,"data"=>$output
                                            ,'desc'=>'مدت زمان بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                        }
                                        else
                                            if ($command=="delete_elevator_kind")
                                            {
                                                $elevator_kind_id=$this->post('elevator_kind_id') ;

                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','travel');
                                                if($employeetoken[0]=='ok')
                                                {
//************************************************************************;****************************************
                                                    $output = array();$user_id=$employeetoken[0];

                                                    $query="DELETE FROM elevator_kind_tb  where elevator_kind_id=".$elevator_kind_id."";
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



                                                if ($command=="modify_elevator_kind")
                                                {
                                                    $elevator_kind_id=$this->post('elevator_kind_id') ;


                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','travel');
                                                    if($employeetoken[0]=='ok')
                                                    {
//*****************************************************************************************

                                                        $query="UPDATE elevator_kind_tb SET ";

                                                        if(isset($_REQUEST['elevator_kind_name'])){
                                                            $elevator_kind_name=$this->post('elevator_kind_name');
                                                            $query.="elevator_kind_name='".$elevator_kind_name."'";}

                                                        if(isset($_REQUEST['elevator_kind_percent'])&&(isset($_REQUEST['elevator_kind_name']))){ $query.=",";}
                                                        if(isset($_REQUEST['elevator_kind_percent'])){
                                                            $elevator_kind_percent=$this->post('elevator_kind_percent');
                                                            $query.="elevator_kind_percent='".$elevator_kind_percent."'";}

                                                        $query.="where elevator_kind_id=".$elevator_kind_id;

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
                                                    if ($command=="add_elevator_coverage")
                                                    {
                                                        $elevator_coverage_id=$this->post('elevator_coverage_id') ;
                                                        $elevator_coverage_name=$this->post('elevator_coverage_name') ;
                                                        $elevator_coverage_desc=$this->post('elevator_coverage_desc') ;



                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','travel');
                                                        if($employeetoken[0]=='ok')
                                                        {
//****************************************************************************************************************
                                                            $query="select * from elevator_coverage_tb where elevator_coverage_name='".$elevator_coverage_name."'";
                                                            $result=$this->B_db->run_query($query);
                                                            $num=count($result[0]);
                                                            if ($num==0)
                                                            {
                                                                $query="INSERT INTO elevator_coverage_tb(elevator_coverage_id, elevator_coverage_name, elevator_coverage_desc)
	                            VALUES ( $elevator_coverage_id,'$elevator_coverage_name', '$elevator_coverage_desc');";

                                                                $result=$this->B_db->run_query_put($query);
                                                                //  $elevator_coverage_id=$this->db->insert_id();

                                                                echo json_encode(array('result'=>"ok"
                                                                ,"data"=>array('elevator_coverage_id'=>$elevator_coverage_id)
                                                                ,'desc'=>'پوشش بیمه مسافرتی اضافه شد'.$query),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                            }else{
                                                                $carmode=$result[0];
                                                                echo json_encode(array('result'=>"error"
                                                                ,"data"=>array('elevator_coverage_id'=>$carmode['elevator_coverage_id'])
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
                                                        if ($command=="get_elevator_coverage")
                                                        {
//************************************************************************;****************************************

                                                            $query="select * from elevator_coverage_tb where 1 ORDER BY elevator_coverage_id ASC";
                                                            $result = $this->B_db->run_query($query);
                                                            $output =array();
                                                            foreach($result as $row)
                                                            {
                                                                $record=array();
                                                                $record['elevator_coverage_id']=$row['elevator_coverage_id'];
                                                                $record['elevator_coverage_name']=$row['elevator_coverage_name'];
                                                                $record['elevator_coverage_desc']=$row['elevator_coverage_desc'];
                                                                $output[]=$record;
                                                            }
                                                            echo json_encode(array('result'=>"ok"
                                                            ,"data"=>$output
                                                            ,'desc'=>'پوشش بیمه مسافرتی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                        }
                                                        else
                                                            if ($command=="delete_elevator_coverage")
                                                            {
                                                                $elevator_coverage_id=$this->post('elevator_coverage_id') ;


                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','travel');
                                                                if($employeetoken[0]=='ok')
                                                                {
//************************************************************************;****************************************
                                                                    $output = array();$user_id=$employeetoken[0];

                                                                    $query="DELETE FROM elevator_coverage_tb  where elevator_coverage_id=".$elevator_coverage_id."";
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



                                                                if ($command=="modify_elevator_coverage")
                                                                {
                                                                    $elevator_coverage_id=$this->post('elevator_coverage_id') ;


                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','travel');
                                                                    if($employeetoken[0]=='ok')
                                                                    {
//*****************************************************************************************

                                                                        $query="UPDATE elevator_coverage_tb SET ";

                                                                        if(isset($_REQUEST['elevator_coverage_name'])){
                                                                            $elevator_coverage_name=$this->post('elevator_coverage_name');
                                                                            $query.="elevator_coverage_name='".$elevator_coverage_name."'";}

                                                                        if(isset($_REQUEST['elevator_coverage_desc'])&&(isset($_REQUEST['elevator_coverage_name']))){ $query.=",";}
                                                                        if(isset($_REQUEST['elevator_coverage_desc'])){
                                                                            $elevator_coverage_desc=$this->post('elevator_coverage_desc');
                                                                            $query.="elevator_coverage_desc='".$elevator_coverage_desc."' ";}

                                                                        $query.="where elevator_coverage_id=".$elevator_coverage_id;

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
                                                                    if ($command=="add_elevator_kinddoor")
                                                                    {
                                                                        $elevator_kinddoor_id=$this->post('elevator_kinddoor_id') ;
                                                                        $elevator_kinddoor_name=$this->post('elevator_kinddoor_name') ;
                                                                        $elevator_kinddoor_price=$this->post('elevator_kinddoor_price') ;



                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','travel');
                                                                        if($employeetoken[0]=='ok')
                                                                        {
//****************************************************************************************************************
                                                                            $query="select * from elevator_kinddoor_tb where elevator_kinddoor_name='".$elevator_kinddoor_name."'";
                                                                            $result=$this->B_db->run_query($query);
                                                                            $num=count($result[0]);
                                                                            if ($num==0)
                                                                            {
                                                                                $query="INSERT INTO elevator_kinddoor_tb(elevator_kinddoor_id, elevator_kinddoor_name, elevator_kinddoor_price)
	                            VALUES ( $elevator_kinddoor_id,'$elevator_kinddoor_name', '$elevator_kinddoor_price');";

                                                                                $result=$this->B_db->run_query_put($query);
                                                                                //  $elevator_kinddoor_id=$this->db->insert_id();

                                                                                echo json_encode(array('result'=>"ok"
                                                                                ,"data"=>array('elevator_kinddoor_id'=>$elevator_kinddoor_id)
                                                                                ,'desc'=>'پوشش بیمه مسافرتی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                            }else{
                                                                                $carmode=$result[0];
                                                                                echo json_encode(array('result'=>"error"
                                                                                ,"data"=>array('elevator_kinddoor_id'=>$carmode['elevator_kinddoor_id'])
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
                                                                        if ($command=="get_elevator_kinddoor")
                                                                        {
//************************************************************************;****************************************

                                                                            $query="select * from elevator_kinddoor_tb where 1 ORDER BY elevator_kinddoor_id ASC";
                                                                            $result = $this->B_db->run_query($query);
                                                                            $output =array();
                                                                            foreach($result as $row)
                                                                            {
                                                                                $record=array();
                                                                                $record['elevator_kinddoor_id']=$row['elevator_kinddoor_id'];
                                                                                $record['elevator_kinddoor_name']=$row['elevator_kinddoor_name'];
                                                                                $record['elevator_kinddoor_price']=$row['elevator_kinddoor_price'];
                                                                                $output[]=$record;
                                                                            }
                                                                            echo json_encode(array('result'=>"ok"
                                                                            ,"data"=>$output
                                                                            ,'desc'=>'پوشش بیمه مسافرتی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                        }
                                                                        else
                                                                            if ($command=="delete_elevator_kinddoor")
                                                                            {
                                                                                $elevator_kinddoor_id=$this->post('elevator_kinddoor_id') ;


                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','travel');
                                                                                if($employeetoken[0]=='ok')
                                                                                {
//************************************************************************;****************************************
                                                                                    $output = array();$user_id=$employeetoken[0];

                                                                                    $query="DELETE FROM elevator_kinddoor_tb  where elevator_kinddoor_id=".$elevator_kinddoor_id."";
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



                                                                                if ($command=="modify_elevator_kinddoor")
                                                                                {
                                                                                    $elevator_kinddoor_id=$this->post('elevator_kinddoor_id') ;


                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','travel');
                                                                                    if($employeetoken[0]=='ok')
                                                                                    {
//*****************************************************************************************

                                                                                        $query="UPDATE elevator_kinddoor_tb SET ";

                                                                                        if(isset($_REQUEST['elevator_kinddoor_name'])){
                                                                                            $elevator_kinddoor_name=$this->post('elevator_kinddoor_name');
                                                                                            $query.="elevator_kinddoor_name='".$elevator_kinddoor_name."'";}

                                                                                        if(isset($_REQUEST['elevator_kinddoor_price'])&&(isset($_REQUEST['elevator_kinddoor_name']))){ $query.=",";}
                                                                                        if(isset($_REQUEST['elevator_kinddoor_price'])){
                                                                                            $elevator_kinddoor_price=$this->post('elevator_kinddoor_price');
                                                                                            $query.="elevator_kinddoor_price='".$elevator_kinddoor_price."'";}

                                                                                        $query.="where elevator_kinddoor_id=".$elevator_kinddoor_id;

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
                                                                                else
                                                                                    if ($command=="add_elevator_kindrespons")
                                                                                    {
                                                                                        $elevator_kindrespons_id=$this->post('elevator_kindrespons_id') ;
                                                                                        $elevator_kindrespons_name=$this->post('elevator_kindrespons_name') ;
                                                                                        $elevator_kindrespons_price=$this->post('elevator_kindrespons_price') ;



                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','travel');
                                                                                        if($employeetoken[0]=='ok')
                                                                                        {
//****************************************************************************************************************
                                                                                            $query="select * from elevator_kindrespons_tb where elevator_kindrespons_name='".$elevator_kindrespons_name."'";
                                                                                            $result=$this->B_db->run_query($query);
                                                                                            $num=count($result[0]);
                                                                                            if ($num==0)
                                                                                            {
                                                                                                $query="INSERT INTO elevator_kindrespons_tb(elevator_kindrespons_id, elevator_kindrespons_name, elevator_kindrespons_price)
	                            VALUES ( $elevator_kindrespons_id,'$elevator_kindrespons_name', '$elevator_kindrespons_price');";

                                                                                                $result=$this->B_db->run_query_put($query);
                                                                                                //  $elevator_kindrespons_id=$this->db->insert_id();

                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                ,"data"=>array('elevator_kindrespons_id'=>$elevator_kindrespons_id)
                                                                                                ,'desc'=>'پوشش بیمه مسافرتی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                            }else{
                                                                                                $carmode=$result[0];
                                                                                                echo json_encode(array('result'=>"error"
                                                                                                ,"data"=>array('elevator_kindrespons_id'=>$carmode['elevator_kindrespons_id'])
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
                                                                                        if ($command=="get_elevator_kindrespons")
                                                                                        {
//************************************************************************;****************************************

                                                                                            $query="select * from elevator_kindrespons_tb where 1 ORDER BY elevator_kindrespons_id ASC";
                                                                                            $result = $this->B_db->run_query($query);
                                                                                            $output =array();
                                                                                            foreach($result as $row)
                                                                                            {
                                                                                                $record=array();
                                                                                                $record['elevator_kindrespons_id']=$row['elevator_kindrespons_id'];
                                                                                                $record['elevator_kindrespons_name']=$row['elevator_kindrespons_name'];
                                                                                                $record['elevator_kindrespons_price']=$row['elevator_kindrespons_price'];
                                                                                                $output[]=$record;
                                                                                            }
                                                                                            echo json_encode(array('result'=>"ok"
                                                                                            ,"data"=>$output
                                                                                            ,'desc'=>'پوشش بیمه مسافرتی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                        }
                                                                                        else
                                                                                            if ($command=="delete_elevator_kindrespons")
                                                                                            {
                                                                                                $elevator_kindrespons_id=$this->post('elevator_kindrespons_id') ;


                                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','travel');
                                                                                                if($employeetoken[0]=='ok')
                                                                                                {
//************************************************************************;****************************************
                                                                                                    $output = array();$user_id=$employeetoken[0];

                                                                                                    $query="DELETE FROM elevator_kindrespons_tb  where elevator_kindrespons_id=".$elevator_kindrespons_id."";
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



                                                                                                if ($command=="modify_elevator_kindrespons")
                                                                                                {
                                                                                                    $elevator_kindrespons_id=$this->post('elevator_kindrespons_id') ;


                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','travel');
                                                                                                    if($employeetoken[0]=='ok')
                                                                                                    {
//*****************************************************************************************

                                                                                                        $query="UPDATE elevator_kindrespons_tb SET ";

                                                                                                        if(isset($_REQUEST['elevator_kindrespons_name'])){
                                                                                                            $elevator_kindrespons_name=$this->post('elevator_kindrespons_name');
                                                                                                            $query.="elevator_kindrespons_name='".$elevator_kindrespons_name."'";}

                                                                                                        if(isset($_REQUEST['elevator_kindrespons_price'])&&(isset($_REQUEST['elevator_kindrespons_name']))){ $query.=",";}
                                                                                                        if(isset($_REQUEST['elevator_kindrespons_price'])){
                                                                                                            $elevator_kindrespons_price=$this->post('elevator_kindrespons_price');
                                                                                                            $query.="elevator_kindrespons_price='".$elevator_kindrespons_price."'";}

                                                                                                        $query.="where elevator_kindrespons_id=".$elevator_kindrespons_id;

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
///************************************************************************************************************************************************************
//***************************************************************************************************************

                                                                                                else
                                                                                                    if ($command=="add_elevator_price")
                                                                                                    {
                                                                                                        $elevator_price_uses_id=$this->post('elevator_price_uses_id') ;
                                                                                                        $elevator_price_until_year=$this->post('elevator_price_until_year') ;
                                                                                                        $elevator_price_kind_id=$this->post('elevator_price_kind_id') ;
                                                                                                        $elevator_price_extraprice_nodoor=$this->post('elevator_price_extraprice_nodoor') ;
                                                                                                        $elevator_price_amount=$this->post('elevator_price_amount') ;
                                                                                                        $elevator_price_rate=$this->post('elevator_price_rate') ;
                                                                                                        $elevator_price_extrapercent_nodoor=$this->post('elevator_price_extrapercent_nodoor') ;
                                                                                                        $elevator_price_max_floor=$this->post('elevator_price_max_floor') ;
                                                                                                        $elevator_price_min_floor=$this->post('elevator_price_min_floor') ;
                                                                                                        $elevator_price_from_year=$this->post('elevator_price_from_year') ;
                                                                                                        $elevator_price_disc=$this->post('elevator_price_disc') ;
                                                                                                        $elevator_price_fieldcompany_id=$this->post('elevator_price_fieldcompany_id') ;



                                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','travel');
                                                                                                        if($employeetoken[0]=='ok')
                                                                                                        {
//****************************************************************************************************************
                                                                                                            $query="select * from elevator_price_tb where elevator_price_fieldcompany_id=".$elevator_price_fieldcompany_id." AND elevator_price_max_floor=".$elevator_price_max_floor." AND elevator_price_min_floor=".$elevator_price_min_floor." AND elevator_price_from_year=".$elevator_price_from_year." AND elevator_price_kind_id=".$elevator_price_kind_id." AND elevator_price_until_year=".$elevator_price_until_year." AND elevator_price_uses_id=".$elevator_price_uses_id."";
                                                                                                            $result=$this->B_db->run_query($query);
                                                                                                            $num=count($result[0]);
                                                                                                            if ($num==0)
                                                                                                            {
                                                                                                                $query1="INSERT INTO elevator_price_tb(elevator_price_kind_id,elevator_price_uses_id,elevator_price_until_year, elevator_price_min_floor, elevator_price_amount, elevator_price_rate, elevator_price_extraprice_nodoor, elevator_price_extrapercent_nodoor, elevator_price_max_floor, elevator_price_from_year, elevator_price_disc, elevator_price_fieldcompany_id)
	                            VALUES ( $elevator_price_kind_id,$elevator_price_uses_id,$elevator_price_until_year,$elevator_price_min_floor,$elevator_price_amount,$elevator_price_rate,$elevator_price_extraprice_nodoor,$elevator_price_extrapercent_nodoor,$elevator_price_max_floor,$elevator_price_from_year,'$elevator_price_disc',$elevator_price_fieldcompany_id);";

                                                                                                                $result1=$this->B_db->run_query_put($query1);
                                                                                                                $elevator_price_id=$this->db->insert_id();
                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                ,"data"=>array('elevator_price_id'=>$elevator_price_id,'query'=>$query)
                                                                                                                ,'desc'=>'قیمت بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                            }else{
                                                                                                                $carmode=$result[0];
                                                                                                                echo json_encode(array('result'=>"error"
                                                                                                                ,"data"=>array('elevator_price_id'=>$carmode['elevator_price_id'])
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
                                                                                                        if ($command=="get_elevator_price")
                                                                                                        {
//************************************************************************;****************************************

                                                                                                            $query="select * from elevator_price_tb,fieldcompany_tb,elevator_uses_tb,company_tb,elevator_kind_tb
  where elevator_price_fieldcompany_id=fieldcompany_id
  AND elevator_price_uses_id=elevator_uses_id
  AND elevator_price_kind_id=elevator_kind_id
  AND fieldcompany_company_id=company_id
 ORDER BY elevator_price_id ASC";
                                                                                                            $result = $this->B_db->run_query($query);
                                                                                                            $output =array();
                                                                                                            foreach($result as $row)
                                                                                                            {
                                                                                                                $record=array();
                                                                                                                $record['elevator_price_id']=$row['elevator_price_id'];
                                                                                                                $record['elevator_price_uses_id']=$row['elevator_price_uses_id'];
                                                                                                                $record['elevator_uses_name']=$row['elevator_uses_name'];
                                                                                                                $record['elevator_price_fieldcompany_id']=$row['elevator_price_fieldcompany_id'];
                                                                                                                $record['company_name']=$row['company_name'];
                                                                                                                $record['company_logo_url']=IMGADD.$row['company_logo_url'];
                                                                                                                $record['elevator_price_kind_id']=$row['elevator_price_kind_id'];
                                                                                                                $record['elevator_kind_name']=$row['elevator_kind_name'];
                                                                                                                $record['elevator_price_amount']=$row['elevator_price_amount'];
                                                                                                                $record['elevator_price_rate']=$row['elevator_price_rate'];
                                                                                                                $record['elevator_price_extraprice_nodoor']=$row['elevator_price_extraprice_nodoor'];
                                                                                                                $record['elevator_price_extrapercent_nodoor']=$row['elevator_price_extrapercent_nodoor'];
                                                                                                                $record['elevator_price_max_floor']=$row['elevator_price_max_floor'];
                                                                                                                $record['elevator_price_min_floor']=$row['elevator_price_min_floor'];
                                                                                                                $record['elevator_price_from_year']=$row['elevator_price_from_year'];
                                                                                                                $record['elevator_price_until_year']=$row['elevator_price_until_year'];
                                                                                                                $record['elevator_price_disc']=$row['elevator_price_disc'];
                                                                                                                $record['elevator_price_deactive']=$row['elevator_price_deactive'];
                                                                                                                $output[]=$record;
                                                                                                            }
                                                                                                            echo json_encode(array('result'=>"ok"
                                                                                                            ,"data"=>$output
                                                                                                            ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                                        }
                                                                                                        else
                                                                                                            if ($command=="delete_elevator_price")
                                                                                                            {
                                                                                                                $elevator_price_id=$this->post('elevator_price_id');

                                                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','travel');
                                                                                                                if($employeetoken[0]=='ok')
                                                                                                                {
//************************************************************************;****************************************
                                                                                                                    $output = array();$user_id=$employeetoken[0];

                                                                                                                    $query="DELETE FROM elevator_price_tb  where elevator_price_id=".$elevator_price_id."";
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



                                                                                                                if ($command=="modify_elevator_price")
                                                                                                                {
                                                                                                                    $elevator_price_id=$this->post('elevator_price_id');

                                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','travel');
                                                                                                                    if($employeetoken[0]=='ok')
                                                                                                                    {
//*****************************************************************************************

                                                                                                                        $query="UPDATE elevator_price_tb SET ";

                                                                                                                        if(isset($_REQUEST['elevator_price_from_year'])){
                                                                                                                            $elevator_price_from_year=$this->post('elevator_price_from_year');
                                                                                                                            $query.="elevator_price_from_year='".$elevator_price_from_year."'";}



                                                                                                                        if(isset($_REQUEST['elevator_price_deactive'])&&(isset($_REQUEST['elevator_price_from_year']))){ $query.=",";}
                                                                                                                        if(isset($_REQUEST['elevator_price_deactive'])){
                                                                                                                            $elevator_price_deactive=$this->post('elevator_price_deactive');
                                                                                                                            $query.="elevator_price_deactive=".$elevator_price_deactive."";}

                                                                                                                        if(isset($_REQUEST['elevator_price_disc'])&&(isset($_REQUEST['elevator_price_deactive'])||isset($_REQUEST['elevator_price_from_year']))){ $query.=",";}
                                                                                                                        if(isset($_REQUEST['elevator_price_disc'])){
                                                                                                                            $elevator_price_disc=$this->post('elevator_price_disc');
                                                                                                                            $query.="elevator_price_disc=".$elevator_price_disc."";}


                                                                                                                        if(isset($_REQUEST['elevator_price_until_year'])&&(isset($_REQUEST['elevator_price_disc'])||isset($_REQUEST['elevator_price_deactive'])||isset($_REQUEST['elevator_price_from_year']))){ $query.=",";}
                                                                                                                        if(isset($_REQUEST['elevator_price_until_year'])){
                                                                                                                            $elevator_price_until_year=$this->post('elevator_price_until_year');
                                                                                                                            $query.="elevator_price_until_year=".$elevator_price_until_year."";}

                                                                                                                        if(isset($_REQUEST['elevator_price_min_floor'])&&(isset($_REQUEST['elevator_price_until_year'])||isset($_REQUEST['elevator_price_disc'])||isset($_REQUEST['elevator_price_deactive'])||isset($_REQUEST['elevator_price_from_year']))){ $query.=",";}
                                                                                                                        if(isset($_REQUEST['elevator_price_min_floor'])){
                                                                                                                            $elevator_price_min_floor=$this->post('elevator_price_min_floor');
                                                                                                                            $query.="elevator_price_min_floor=".$elevator_price_min_floor."";}

                                                                                                                        if(isset($_REQUEST['elevator_price_max_floor'])&&(isset($_REQUEST['elevator_price_min_floor'])||isset($_REQUEST['elevator_price_until_year'])||isset($_REQUEST['elevator_price_disc'])||isset($_REQUEST['elevator_price_deactive'])||isset($_REQUEST['elevator_price_from_year']))){ $query.=",";}
                                                                                                                        if(isset($_REQUEST['elevator_price_max_floor'])){
                                                                                                                            $elevator_price_max_floor=$this->post('elevator_price_max_floor');
                                                                                                                            $query.="elevator_price_max_floor=".$elevator_price_max_floor."";}

                                                                                                                        if(isset($_REQUEST['elevator_price_extrapercent_nodoor'])&&(isset($_REQUEST['elevator_price_max_floor'])||isset($_REQUEST['elevator_price_min_floor'])||isset($_REQUEST['elevator_price_until_year'])||isset($_REQUEST['elevator_price_disc'])||isset($_REQUEST['elevator_price_deactive'])||isset($_REQUEST['elevator_price_from_year']))){ $query.=",";}
                                                                                                                        if(isset($_REQUEST['elevator_price_extrapercent_nodoor'])){
                                                                                                                            $elevator_price_extrapercent_nodoor=$this->post('elevator_price_extrapercent_nodoor');
                                                                                                                            $query.="elevator_price_extrapercent_nodoor=".$elevator_price_extrapercent_nodoor."";}

                                                                                                                        if(isset($_REQUEST['elevator_price_extraprice_nodoor'])&&(isset($_REQUEST['elevator_price_extrapercent_nodoor'])||isset($_REQUEST['elevator_price_max_floor'])||isset($_REQUEST['elevator_price_min_floor'])||isset($_REQUEST['elevator_price_until_year'])||isset($_REQUEST['elevator_price_disc'])||isset($_REQUEST['elevator_price_deactive'])||isset($_REQUEST['elevator_price_from_year']))){ $query.=",";}
                                                                                                                        if(isset($_REQUEST['elevator_price_extraprice_nodoor'])){
                                                                                                                            $elevator_price_extraprice_nodoor=$this->post('elevator_price_extraprice_nodoor');
                                                                                                                            $query.="elevator_price_extraprice_nodoor=".$elevator_price_extraprice_nodoor."";}

                                                                                                                        if(isset($_REQUEST['elevator_price_rate'])&&(isset($_REQUEST['elevator_price_extraprice_nodoor'])||isset($_REQUEST['elevator_price_extrapercent_nodoor'])||isset($_REQUEST['elevator_price_max_floor'])||isset($_REQUEST['elevator_price_min_floor'])||isset($_REQUEST['elevator_price_until_year'])||isset($_REQUEST['elevator_price_disc'])||isset($_REQUEST['elevator_price_deactive'])||isset($_REQUEST['elevator_price_from_year']))){ $query.=",";}
                                                                                                                        if(isset($_REQUEST['elevator_price_rate'])){
                                                                                                                            $elevator_price_rate=$this->post('elevator_price_rate');
                                                                                                                            $query.="elevator_price_rate=".$elevator_price_rate."";}

                                                                                                                        if(isset($_REQUEST['elevator_price_amount'])&&(isset($_REQUEST['elevator_price_rate'])||isset($_REQUEST['elevator_price_extraprice_nodoor'])||isset($_REQUEST['elevator_price_extrapercent_nodoor'])||isset($_REQUEST['elevator_price_max_floor'])||isset($_REQUEST['elevator_price_min_floor'])||isset($_REQUEST['elevator_price_until_year'])||isset($_REQUEST['elevator_price_disc'])||isset($_REQUEST['elevator_price_deactive'])||isset($_REQUEST['elevator_price_from_year']))){ $query.=",";}
                                                                                                                        if(isset($_REQUEST['elevator_price_amount'])){
                                                                                                                            $elevator_price_amount=$this->post('elevator_price_amount');
                                                                                                                            $query.="elevator_price_amount=".$elevator_price_amount."";}

                                                                                                                        $query.=" where elevator_price_id=".$elevator_price_id;

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
                                                                                                                    if ($command=="add_price_covarage")
                                                                                                                    {
                                                                                                                        $elevator_price_covarage_elevator_price_id=$this->post('elevator_price_covarage_elevator_price_id');
                                                                                                                        $elevator_price_covarage_elevator_covarage_id=$this->post('elevator_price_covarage_elevator_covarage_id');
                                                                                                                        $elevator_price_covarage_elevator_kinddoor_id=$this->post('elevator_price_covarage_elevator_kinddoor_id');
                                                                                                                        $elevator_price_covarage_price=$this->post('elevator_price_covarage_price');

                                                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','travel');
                                                                                                                        if($employeetoken[0]=='ok')
                                                                                                                        {
//****************************************************************************************************************
                                                                                                                            $query="select * from elevator_price_coverage_tb where elevator_price_covarage_elevator_covarage_id=".$elevator_price_covarage_elevator_covarage_id." AND elevator_price_covarage_elevator_price_id=".$elevator_price_covarage_elevator_price_id."  AND elevator_price_covarage_elevator_kinddoor_id=".$elevator_price_covarage_elevator_kinddoor_id."";
                                                                                                                            $result=$this->B_db->run_query($query);
                                                                                                                            $num=count($result[0]);
                                                                                                                            if ($num==0)
                                                                                                                            {
                                                                                                                                $query="INSERT INTO elevator_price_coverage_tb(elevator_price_covarage_elevator_price_id,elevator_price_covarage_elevator_covarage_id, elevator_price_covarage_price, elevator_price_covarage_elevator_kinddoor_id)
	                            VALUES ( $elevator_price_covarage_elevator_price_id,$elevator_price_covarage_elevator_covarage_id, '$elevator_price_covarage_price', '$elevator_price_covarage_elevator_kinddoor_id');";

                                                                                                                                $result=$this->B_db->run_query_put($query);
                                                                                                                                $elevator_price_covarage_id=$this->db->insert_id();
                                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                                ,"data"=>array('elevator_price_covarage_id'=>$elevator_price_covarage_id,'query'=>$query)
                                                                                                                                ,'desc'=>'قیمت  بر اساس سن در بیمه مسافرتی  اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                            }else{
                                                                                                                                $carmode=$result[0];
                                                                                                                                echo json_encode(array('result'=>"error"
                                                                                                                                ,"data"=>array('elevator_price_covarage_id'=>$carmode['elevator_price_covarage_id'])
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
                                                                                                                        if ($command=="get_price_covarage")
                                                                                                                        {
                                                                                                                            $elevator_price_id=$this->post('elevator_price_id');

//************************************************************************;****************************************

                                                                                                                            $query="select * from elevator_price_coverage_tb,elevator_coverage_tb,elevator_kinddoor_tb where elevator_kinddoor_id=elevator_price_covarage_elevator_kinddoor_id AND  elevator_coverage_id=elevator_price_covarage_elevator_covarage_id AND elevator_price_covarage_elevator_price_id=$elevator_price_id  ORDER BY elevator_price_covarage_id ASC";
                                                                                                                            $result = $this->B_db->run_query($query);
                                                                                                                            $output =array();
                                                                                                                            foreach($result as $row)
                                                                                                                            {
                                                                                                                                $record=array();
                                                                                                                                $record['elevator_price_covarage_id']=$row['elevator_price_covarage_id'];
                                                                                                                                $record['elevator_price_covarage_elevator_price_id']=$row['elevator_price_covarage_elevator_price_id'];
                                                                                                                                $record['elevator_price_covarage_elevator_covarage_id']=$row['elevator_price_covarage_elevator_covarage_id'];
                                                                                                                                $record['elevator_coverage_name']=$row['elevator_coverage_name'];
                                                                                                                                $record['elevator_price_covarage_elevator_kinddoor_id']=$row['elevator_price_covarage_elevator_kinddoor_id'];
                                                                                                                                $record['elevator_kinddoor_name']=$row['elevator_kinddoor_name'];
                                                                                                                                $record['elevator_price_covarage_elevator_kinddoor_id']=$row['elevator_price_covarage_elevator_kinddoor_id'];
                                                                                                                                $record['elevator_price_covarage_price']=$row['elevator_price_covarage_price'];
                                                                                                                                $output[]=$record;
                                                                                                                            }
                                                                                                                            echo json_encode(array('result'=>"ok"
                                                                                                                            ,"data"=>$output
                                                                                                                            ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                                                        }
                                                                                                                        else
                                                                                                                            if ($command=="delete_price_covarage")
                                                                                                                            {
                                                                                                                                $elevator_price_covarage_id=$this->post('elevator_price_covarage_id');

                                                                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','travel');
                                                                                                                                if($employeetoken[0]=='ok')
                                                                                                                                {
//************************************************************************;****************************************
                                                                                                                                    $output = array();$user_id=$employeetoken[0];

                                                                                                                                    $query="DELETE FROM elevator_price_coverage_tb  where elevator_price_covarage_id=".$elevator_price_covarage_id."";
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



                                                                                                                                if ($command=="modify_price_covarage")
                                                                                                                                {
                                                                                                                                    $elevator_price_covarage_id=$this->post('elevator_price_covarage_id');

                                                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','travel');
                                                                                                                                    if($employeetoken[0]=='ok')
                                                                                                                                    {
//*****************************************************************************************

                                                                                                                                        $query="UPDATE elevator_price_coverage_tb SET ";


                                                                                                                                        if(isset($_REQUEST['elevator_price_covarage_price'])){
                                                                                                                                            $elevator_price_covarage_price=$this->post('elevator_price_covarage_price');
                                                                                                                                            $query.="elevator_price_covarage_price='".$elevator_price_covarage_price."'";}

                                                                                                                                        if(isset($_REQUEST['elevator_price_covarage_elevator_kinddoor_id'])&&(isset($_REQUEST['elevator_price_covarage_price']))){ $query.=",";}
                                                                                                                                        if(isset($_REQUEST['elevator_price_covarage_elevator_kinddoor_id'])){
                                                                                                                                            $elevator_price_covarage_elevator_kinddoor_id=$this->post('elevator_price_covarage_elevator_kinddoor_id');
                                                                                                                                            $query.="elevator_price_covarage_elevator_kinddoor_id=".$elevator_price_covarage_elevator_kinddoor_id."";}

                                                                                                                                        $query.=" where elevator_price_covarage_id=".$elevator_price_covarage_id;

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
//***************************************************************************************************************
                                                                                                                                else
                                                                                                                                    if ($command=="add_price_kindrespons")
                                                                                                                                    {
                                                                                                                                        $elevator_price_kindrespons_elevator_price_id=$this->post('elevator_price_kindrespons_elevator_price_id');
                                                                                                                                        $elevator_price_kindrespons_elevator_kindrespons_id=$this->post('elevator_price_kindrespons_elevator_kindrespons_id');
                                                                                                                                        $elevator_price_kindrespons_price=$this->post('elevator_price_kindrespons_price');

                                                                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','travel');
                                                                                                                                        if($employeetoken[0]=='ok')
                                                                                                                                        {
//****************************************************************************************************************
                                                                                                                                            $query="select * from elevator_price_kindrespons_tb where elevator_price_kindrespons_elevator_kindrespons_id=".$elevator_price_kindrespons_elevator_kindrespons_id." AND elevator_price_kindrespons_elevator_price_id=".$elevator_price_kindrespons_elevator_price_id." ";
                                                                                                                                            $result=$this->B_db->run_query($query);
                                                                                                                                            $num=count($result[0]);
                                                                                                                                            if ($num==0)
                                                                                                                                            {
                                                                                                                                                $query="INSERT INTO elevator_price_kindrespons_tb(elevator_price_kindrespons_elevator_price_id,elevator_price_kindrespons_elevator_kindrespons_id, elevator_price_kindrespons_price)
	                            VALUES ( $elevator_price_kindrespons_elevator_price_id,$elevator_price_kindrespons_elevator_kindrespons_id, '$elevator_price_kindrespons_price');";

                                                                                                                                                $result=$this->B_db->run_query_put($query);
                                                                                                                                                $elevator_price_kindrespons_id=$this->db->insert_id();
                                                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                                                ,"data"=>array('elevator_price_kindrespons_id'=>$elevator_price_kindrespons_id,'query'=>$query)
                                                                                                                                                ,'desc'=>'قیمت  بر اساس سن در بیمه مسافرتی  اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                                            }else{
                                                                                                                                                $carmode=$result[0];
                                                                                                                                                echo json_encode(array('result'=>"error"
                                                                                                                                                ,"data"=>array('elevator_price_kindrespons_id'=>$carmode['elevator_price_kindrespons_id'])
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
                                                                                                                                        if ($command=="get_price_kindrespons")
                                                                                                                                        {
                                                                                                                                            $elevator_price_id=$this->post('elevator_price_id');

//************************************************************************;****************************************

                                                                                                                                            $query="select * from elevator_price_kindrespons_tb,elevator_kindrespons_tb where  elevator_kindrespons_id=elevator_price_kindrespons_elevator_kindrespons_id AND elevator_price_kindrespons_elevator_price_id=$elevator_price_id  ORDER BY elevator_price_kindrespons_id ASC";
                                                                                                                                            $result = $this->B_db->run_query($query);
                                                                                                                                            $output =array();
                                                                                                                                            foreach($result as $row)
                                                                                                                                            {
                                                                                                                                                $record=array();
                                                                                                                                                $record['elevator_price_kindrespons_id']=$row['elevator_price_kindrespons_id'];
                                                                                                                                                $record['elevator_price_kindrespons_elevator_price_id']=$row['elevator_price_kindrespons_elevator_price_id'];
                                                                                                                                                $record['elevator_price_kindrespons_elevator_kindrespons_id']=$row['elevator_price_kindrespons_elevator_kindrespons_id'];
                                                                                                                                                $record['elevator_kindrespons_name']=$row['elevator_kindrespons_name'];
                                                                                                                                                $record['elevator_price_kindrespons_price']=$row['elevator_price_kindrespons_price'];
                                                                                                                                                $output[]=$record;
                                                                                                                                            }
                                                                                                                                            echo json_encode(array('result'=>"ok"
                                                                                                                                            ,"data"=>$output
                                                                                                                                            ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                                                                        }
                                                                                                                                        else
                                                                                                                                            if ($command=="delete_price_kindrespons")
                                                                                                                                            {
                                                                                                                                                $elevator_price_kindrespons_id=$this->post('elevator_price_kindrespons_id');

                                                                                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','travel');
                                                                                                                                                if($employeetoken[0]=='ok')
                                                                                                                                                {
//************************************************************************;****************************************
                                                                                                                                                    $output = array();$user_id=$employeetoken[0];

                                                                                                                                                    $query="DELETE FROM elevator_price_kindrespons_tb  where elevator_price_kindrespons_id=".$elevator_price_kindrespons_id."";
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



                                                                                                                                                if ($command=="modify_price_kindrespons")
                                                                                                                                                {
                                                                                                                                                    $elevator_price_kindrespons_id=$this->post('elevator_price_kindrespons_id');

                                                                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','travel');
                                                                                                                                                    if($employeetoken[0]=='ok')
                                                                                                                                                    {
//*****************************************************************************************

                                                                                                                                                        $query="UPDATE elevator_price_kindrespons_tb SET ";


                                                                                                                                                        if(isset($_REQUEST['elevator_price_kindrespons_price'])){
                                                                                                                                                            $elevator_price_kindrespons_price=$this->post('elevator_price_kindrespons_price');
                                                                                                                                                            $query.="elevator_price_kindrespons_price='".$elevator_price_kindrespons_price."'";}



                                                                                                                                                        $query.=" where elevator_price_kindrespons_id=".$elevator_price_kindrespons_id;

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