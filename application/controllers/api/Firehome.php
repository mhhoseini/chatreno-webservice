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
class Firehome extends REST_Controller {

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
        $this->load->model('B_car');
        $this->load->helper('my_helper');
        $this->load->helper('time_helper');
         $command = $this->post("command");
        if ($this->B_user->checkrequestip('firehome', $command, get_client_ip(),50,50)) {
        if ($command=="get_yearofcons")
        {
            $output =array();
            $def=date("Y")-jdate('Y','',"",'','en');
            $ynow=date("Y");
            for($i=0;$i<50;$i++)
            {
                $record=array();
                $record['firehome_buildinglife_id']=$i;
                $record['firehome_buildinglife_name']=($ynow-$i-$def);
                $output[]=$record;
            }
            $record=array();
            $record['firehome_buildinglife_id']=$i;
            $record['firehome_buildinglife_name']=($ynow-$i-$def).' و ماقبل';
            $output[]=$record;
            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>' سال ساخت با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }else if ($command=="add_firehome_kind")
        {

            $firehome_kind_id=$this->post('firehome_kind_id') ;
            $firehome_kind_name=$this->post('firehome_kind_name') ;
            $firehome_kind_desc=$this->post('firehome_kind_desc') ;




            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','firehome');
            if($employeetoken[0]=='ok')
            {
//************************************************************************;****************************************
                $query="select * from firehome_kind_tb where firehome_kind_name='".$firehome_kind_name."' OR firehome_kind_id=".$firehome_kind_id."";
                $result=$this->B_db->run_query($query);

                if(empty($result))
                {
                    $query="INSERT INTO firehome_kind_tb(firehome_kind_id, firehome_kind_name, firehome_kind_desc)
	                            VALUES ( $firehome_kind_id,'$firehome_kind_name', '$firehome_kind_desc');";

                    $result=$this->B_db->run_query_put($query);
                    // $firehome_kind_id=$this->db->insert_id();
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('firehome_kind_id'=>$firehome_kind_id)
                    ,'desc'=>'نوع ملک بیمه اتش سوزی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    $carmode=$result[0];
                    echo json_encode(array('result'=>"error"
                    ,"data"=>array('firehome_kind_id'=>$carmode['firehome_kind_id'])
                    ,'desc'=>'نوع ملک بیمه اتش سوزی تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
//***************************************************************************************************************
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }
        }

        if ($command=="get_firehome_kind")
        {
//************************************************************************;****************************************

            $query="select * from firehome_kind_tb where 1 ORDER BY firehome_kind_id ASC";
            $result = $this->B_db->run_query($query);
            $output =array();
            foreach($result as $row)
            {
                $record=array();
                $record['firehome_kind_id']=$row['firehome_kind_id'];
                $record['firehome_kind_name']=$row['firehome_kind_name'];
                $record['firehome_kind_desc']=$row['firehome_kind_desc'];
                $output[]=$record;
            }
            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'نوع ملک بیمه اتش سوزی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

        }
        else
            if ($command=="delete_firehome_kind")
            {
                $firehome_kind_id=$this->post('firehome_kind_id') ;

                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','firehome');
                if($employeetoken[0]=='ok')
                {
//************************************************************************;****************************************
                    $user_id=$employeetoken[1];
                    $output =array();
                    $query="DELETE FROM firehome_kind_tb  where firehome_kind_id=".$firehome_kind_id."";
                    $result = $this->B_db->run_query_put($query);
                    if($result){echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'نوع ملک بیمه اتش سوزی حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        echo json_encode(array('result'=>"error"
                        ,"data"=>$output
                        ,'desc'=>'نوع ملک بیمه اتش سوزی حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
//***************************************************************************************************************
                }else{
                    echo json_encode(array('result'=>$employeetoken[0]
                    ,"data"=>$employeetoken[1]
                    ,'desc'=>$employeetoken[2]));

                }

            }
            else



                if ($command=="modify_firehome_kind")
                {
                    $firehome_kind_id=$this->post('firehome_kind_id') ;


                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','firehome');
                    if($employeetoken[0]=='ok')
                    {
//*****************************************************************************************

                        $query="UPDATE firehome_kind_tb SET ";

                        if(isset($_REQUEST['firehome_kind_name'])){
                            $firehome_kind_name=$this->post('firehome_kind_name');
                            $query.="firehome_kind_name='".$firehome_kind_name."'";}

                        if(isset($_REQUEST['firehome_kind_desc'])&&(isset($_REQUEST['firehome_kind_name']))){ $query.=",";}
                        if(isset($_REQUEST['firehome_kind_desc'])){
                            $firehome_kind_desc=$this->post('firehome_kind_desc');
                            $query.="firehome_kind_desc='".$firehome_kind_desc."'";}

                        $query.="where firehome_kind_id=".$firehome_kind_id;

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
                    if ($command=="add_firehome_buildinglife")
                    {
                        $firehome_buildinglife_id=$this->post('firehome_buildinglife_id') ;
                        $firehome_buildinglife_name=$this->post('firehome_buildinglife_name') ;
                        $firehome_buildinglife_desc=$this->post('firehome_buildinglife_desc') ;



                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','firehome');
                        if($employeetoken[0]=='ok')
                        {
//****************************************************************************************************************
                            $query="select * from firehome_buildinglife_tb where firehome_buildinglife_id=$firehome_buildinglife_id AND firehome_buildinglife_name='".$firehome_buildinglife_name."'";
                            $result=$this->B_db->run_query($query);

                            if(empty($result))
                            {
                                $query="INSERT INTO firehome_buildinglife_tb(firehome_buildinglife_id, firehome_buildinglife_name, firehome_buildinglife_desc)
	                            VALUES ( $firehome_buildinglife_id,'$firehome_buildinglife_name', '$firehome_buildinglife_desc');";

                                $result=$this->B_db->run_query_put($query);
                                //   $firehome_buildinglife_id=$this->db->insert_id();

                                echo json_encode(array('result'=>"ok"
                                ,"data"=>array('firehome_buildinglife_id'=>$firehome_buildinglife_id)
                                ,'desc'=>'تعداد واحد بیمه اتش سوزی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }else{
                                $carmode=$result[0];
                                echo json_encode(array('result'=>"error"
                                ,"data"=>array('firehome_buildinglife_id'=>$carmode['firehome_buildinglife_id'])
                                ,'desc'=>'تعداد واحد بیمه اتش سوزی تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
//***************************************************************************************************************
                        }else{
                            echo json_encode(array('result'=>$employeetoken[0]
                            ,"data"=>$employeetoken[1]
                            ,'desc'=>$employeetoken[2]));

                        }
                    }
                    else
                        if ($command=="get_firehome_buildinglife")
                        {
//************************************************************************;****************************************

                            $query="select * from firehome_buildinglife_tb where 1 ORDER BY firehome_buildinglife_id ASC";
                            $result = $this->B_db->run_query($query);
                            $output =array();
                            foreach($result as $row)
                            {
                                $record=array();
                                $record['firehome_buildinglife_id']=$row['firehome_buildinglife_id'];
                                $record['firehome_buildinglife_name']=$row['firehome_buildinglife_name'];
                                $record['firehome_buildinglife_desc']=$row['firehome_buildinglife_desc'];
                                $output[]=$record;
                            }
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>$output
                            ,'desc'=>'تعداد واحد بیمه اتش سوزی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                        }
                        else
                            if ($command=="delete_firehome_buildinglife")
                            {
                                $firehome_buildinglife_id=$this->post('firehome_buildinglife_id') ;

                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','firehome');
                                if($employeetoken[0]=='ok')
                                {
//************************************************************************;****************************************
                                    $user_id=$employeetoken[1];
                                    $output =array();
                                    $query="DELETE FROM firehome_buildinglife_tb  where firehome_buildinglife_id=".$firehome_buildinglife_id."";
                                    $result = $this->B_db->run_query_put($query);
                                    if($result){echo json_encode(array('result'=>"ok"
                                    ,"data"=>$output
                                    ,'desc'=>'تعداد واحد بیمه اتش سوزی حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }else{
                                        echo json_encode(array('result'=>"error"
                                        ,"data"=>$output
                                        ,'desc'=>'تعداد واحد بیمه اتش سوزی حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }
//***************************************************************************************************************
                                }else{
                                    echo json_encode(array('result'=>$employeetoken[0]
                                    ,"data"=>$employeetoken[1]
                                    ,'desc'=>$employeetoken[2]));
                                }

                            }
                            else
                                if ($command=="modify_firehome_buildinglife")
                                {
                                    $firehome_buildinglife_id=$this->post('firehome_buildinglife_id') ;
                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','firehome');
                                    if($employeetoken[0]=='ok')
                                    {
                                        $query="UPDATE firehome_buildinglife_tb SET ";
                                        if(isset($_REQUEST['firehome_buildinglife_name'])){
                                            $firehome_buildinglife_name=$this->post('firehome_buildinglife_name');
                                            $query.="firehome_buildinglife_name='".$firehome_buildinglife_name."'";}
                                        if(isset($_REQUEST['firehome_buildinglife_desc'])&&(isset($_REQUEST['firehome_buildinglife_name']))){ $query.=",";}
                                        if(isset($_REQUEST['firehome_buildinglife_desc'])){
                                            $firehome_buildinglife_desc=$this->post('firehome_buildinglife_desc');
                                            $query.="firehome_buildinglife_desc='".$firehome_buildinglife_desc."'";}
                                        $query.="where firehome_buildinglife_id=".$firehome_buildinglife_id;

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
                                else
                                    if ($command=="add_firehome_costcons")
                                    {
                                        $firehome_costcons_id=$this->post('firehome_costcons_id') ;
                                        $firehome_costcons_name=$this->post('firehome_costcons_name') ;
                                        $firehome_costcons_price=$this->post('firehome_costcons_price') ;
                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','firehome');
                                        if($employeetoken[0]=='ok')
                                        {
                                            $query="select * from firehome_costcons_tb where firehome_costcons_name='".$firehome_costcons_name."'";
                                            $result=$this->B_db->run_query($query);

                                            if(empty($result))
                                            {
                                                $query="INSERT INTO firehome_costcons_tb(firehome_costcons_id, firehome_costcons_name, firehome_costcons_price)
	                            VALUES ( $firehome_costcons_id,'$firehome_costcons_name', '$firehome_costcons_price');";

                                                $result=$this->B_db->run_query_put($query);
                                                //  $firehome_costcons_id=$this->db->insert_id();

                                                echo json_encode(array('result'=>"ok"
                                                ,"data"=>array('firehome_costcons_id'=>$firehome_costcons_id)
                                                ,'desc'=>'هزینه ساخت بیمه اتش سوزی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                            }else{
                                                $carmode=$result[0];
                                                echo json_encode(array('result'=>"error"
                                                ,"data"=>array('firehome_costcons_id'=>$carmode['firehome_costcons_id'])
                                                ,'desc'=>'هزینه ساخت بیمه اتش سوزی تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                            }
//***************************************************************************************************************
                                        }else{
                                            echo json_encode(array('result'=>$employeetoken[0]
                                            ,"data"=>$employeetoken[1]
                                            ,'desc'=>$employeetoken[2]));

                                        }
                                    }
                                    else
                                        if ($command=="get_firehome_costcons")
                                        {
//************************************************************************;****************************************
                                            $query="select * from firehome_costcons_tb where 1 ORDER BY firehome_costcons_id ASC";
                                            $result = $this->B_db->run_query($query);
                                            $output =array();
                                            foreach($result as $row)
                                            {
                                                $record=array();
                                                $record['firehome_costcons_id']=$row['firehome_costcons_id'];
                                                $record['firehome_costcons_name']=$row['firehome_costcons_name'];
                                                $record['firehome_costcons_price']=$row['firehome_costcons_price'];
                                                $output[]=$record;
                                            }
                                            echo json_encode(array('result'=>"ok"
                                            ,"data"=>$output
                                            ,'desc'=>'هزینه ساخت بیمه اتش سوزی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                        }
                                        else
                                            if ($command=="delete_firehome_costcons")
                                            {
                                                $firehome_costcons_id=$this->post('firehome_costcons_id') ;
                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','firehome');
                                                if($employeetoken[0]=='ok')
                                                {
                                                    $user_id=$employeetoken[1];
                                                    $output =array();
                                                    $query="DELETE FROM firehome_costcons_tb  where firehome_costcons_id=".$firehome_costcons_id."";
                                                    $result = $this->B_db->run_query_put($query);
                                                    if($result){echo json_encode(array('result'=>"ok"
                                                    ,"data"=>$output
                                                    ,'desc'=>'هزینه ساخت بیمه اتش سوزی حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                    }else{
                                                        echo json_encode(array('result'=>"error"
                                                        ,"data"=>$output
                                                        ,'desc'=>'هزینه ساخت بیمه اتش سوزی حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                    }
                                                }else{
                                                    echo json_encode(array('result'=>$employeetoken[0]
                                                    ,"data"=>$employeetoken[1]
                                                    ,'desc'=>$employeetoken[2]));
                                                }

                                            }
                                            else
                                                if ($command=="modify_firehome_costcons")
                                                {
                                                    $firehome_costcons_id=$this->post('firehome_costcons_id') ;
                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','firehome');
                                                    if($employeetoken[0]=='ok')
                                                    {
                                                        $query="UPDATE firehome_costcons_tb SET ";

                                                        if(isset($_REQUEST['firehome_costcons_name'])){
                                                            $firehome_costcons_name=$this->post('firehome_costcons_name');
                                                            $query.="firehome_costcons_name='".$firehome_costcons_name."'";}

                                                        if(isset($_REQUEST['firehome_costcons_price'])&&(isset($_REQUEST['firehome_costcons_name']))){ $query.=",";}
                                                        if(isset($_REQUEST['firehome_costcons_price'])){
                                                            $firehome_costcons_price=$this->post('firehome_costcons_price');
                                                            $query.="firehome_costcons_price='".$firehome_costcons_price."'";}

                                                        $query.="where firehome_costcons_id=".$firehome_costcons_id;

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
                                                    if ($command=="add_firehome_coverage")
                                                    {
                                                        $firehome_coverage_id=$this->post('firehome_coverage_id') ;
                                                        $firehome_coverage_name=$this->post('firehome_coverage_name') ;
                                                        $firehome_coverage_desc=$this->post('firehome_coverage_desc') ;
                                                        $firehome_coverage_calculat_id=$this->post('firehome_coverage_calculat_id') ;

                                                        $firehome_coverage_extrafield=$this->post('firehome_coverage_extrafield') ;

                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','firehome');
                                                        if($employeetoken[0]=='ok')
                                                        {
//****************************************************************************************************************
                                                            $query="select * from firehome_coverage_tb where firehome_coverage_name='".$firehome_coverage_name."'";
                                                            $result=$this->B_db->run_query($query);

                                                            if(empty($result))
                                                            {
                                                                $query="INSERT INTO firehome_coverage_tb(firehome_coverage_id, firehome_coverage_name, firehome_coverage_desc, firehome_coverage_calculat_id, firehome_coverage_extrafield)
	                            VALUES ( $firehome_coverage_id,'$firehome_coverage_name', '$firehome_coverage_desc', $firehome_coverage_calculat_id, $firehome_coverage_extrafield);";
                                                                $result=$this->B_db->run_query_put($query);
                                                                //  $firehome_costcons_id=$this->db->insert_id();

                                                                echo json_encode(array('result'=>"ok"
                                                                ,"data"=>array('firehome_coverage_id'=>$firehome_coverage_id)
                                                                ,'desc'=>'پوشش بیمه اتش سوزی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                            }else{
                                                                $carmode=$result[0];
                                                                echo json_encode(array('result'=>"error"
                                                                ,"data"=>array('firehome_coverage_id'=>$carmode['firehome_coverage_id'])
                                                                ,'desc'=>'پوشش بیمه اتش سوزی تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                            }
                                                        }else{
                                                            echo json_encode(array('result'=>$employeetoken[0]
                                                            ,"data"=>$employeetoken[1]
                                                            ,'desc'=>$employeetoken[2]));
                                                        }
                                                    }
                                                    else
                                                        if ($command=="get_firehome_coverage")
                                                        {
                                                            $query="select * from firehome_coverage_tb where 1 ORDER BY firehome_coverage_id ASC";
                                                            $result = $this->B_db->run_query($query);
                                                            $output =array();
                                                            foreach($result as $row)
                                                            {
                                                                $record=array();
                                                                $record['firehome_coverage_id']=$row['firehome_coverage_id'];
                                                                $record['firehome_coverage_name']=$row['firehome_coverage_name'];
                                                                $record['firehome_coverage_desc']=$row['firehome_coverage_desc'];
                                                                $record['firehome_coverage_calculat_id']=$row['firehome_coverage_calculat_id'];
                                                                $record['firehome_coverage_extrafield']=$row['firehome_coverage_extrafield'];
                                                                $output[]=$record;
                                                            }
                                                            echo json_encode(array('result'=>"ok"
                                                            ,"data"=>$output
                                                            ,'desc'=>'پوشش بیمه اتش سوزی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                        }
                                                        else
                                                            if ($command=="delete_firehome_coverage")
                                                            {
                                                                $firehome_coverage_id=$this->post('firehome_coverage_id') ;


                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','firehome');
                                                                if($employeetoken[0]=='ok')
                                                                {
//************************************************************************;****************************************
                                                                    $user_id=$employeetoken[1];
                                                                    $output =array();
                                                                    $query="DELETE FROM firehome_coverage_tb  where firehome_coverage_id=".$firehome_coverage_id."";
                                                                    $result = $this->B_db->run_query_put($query);
                                                                    if($result){echo json_encode(array('result'=>"ok"
                                                                    ,"data"=>$output
                                                                    ,'desc'=>'پوشش بیمه اتش سوزی حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                    }else{
                                                                        echo json_encode(array('result'=>"error"
                                                                        ,"data"=>$output
                                                                        ,'desc'=>'پوشش بیمه اتش سوزی حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                    }
//***************************************************************************************************************
                                                                }else{
                                                                    echo json_encode(array('result'=>$employeetoken[0]
                                                                    ,"data"=>$employeetoken[1]
                                                                    ,'desc'=>$employeetoken[2]));

                                                                }

                                                            }
                                                            else



                                                                if ($command=="modify_firehome_coverage")
                                                                {
                                                                    $firehome_coverage_id=$this->post('firehome_coverage_id') ;


                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','firehome');
                                                                    if($employeetoken[0]=='ok')
                                                                    {
//*****************************************************************************************

                                                                        $query="UPDATE firehome_coverage_tb SET ";

                                                                        if(isset($_REQUEST['firehome_coverage_name'])){
                                                                            $firehome_coverage_name=$this->post('firehome_coverage_name');
                                                                            $query.="firehome_coverage_name='".$firehome_coverage_name."'";}

                                                                        if(isset($_REQUEST['firehome_coverage_desc'])&&(isset($_REQUEST['firehome_coverage_name']))){ $query.=",";}
                                                                        if(isset($_REQUEST['firehome_coverage_desc'])){
                                                                            $firehome_coverage_desc=$this->post('firehome_coverage_desc');
                                                                            $query.="firehome_coverage_desc='".$firehome_coverage_desc."'";}

                                                                        if(isset($_REQUEST['firehome_coverage_extrafield'])&&(isset($_REQUEST['firehome_coverage_desc'])||isset($_REQUEST['firehome_coverage_name']))){ $query.=",";}
                                                                        if(isset($_REQUEST['firehome_coverage_extrafield'])){
                                                                            $firehome_coverage_extrafield=$this->post('firehome_coverage_extrafield') ;
                                                                            $query.="firehome_coverage_extrafield=".$firehome_coverage_extrafield." ";}

                                                                        if(isset($_REQUEST['firehome_coverage_calculat_id'])&&(isset($_REQUEST['firehome_coverage_extrafield'])||isset($_REQUEST['firehome_coverage_desc'])||isset($_REQUEST['firehome_coverage_name']))){ $query.=",";}
                                                                        if(isset($_REQUEST['firehome_coverage_calculat_id'])){
                                                                            $firehome_coverage_calculat_id=$this->post('firehome_coverage_calculat_id') ;
                                                                            $query.="firehome_coverage_calculat_id=".$firehome_coverage_calculat_id." ";}

                                                                        $query.=" where firehome_coverage_id=".$firehome_coverage_id;

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
                                                                    if ($command=="add_firehome_typeofcons")
                                                                    {
                                                                        $firehome_typeofcons_id=$this->post('firehome_typeofcons_id') ;
                                                                        $firehome_typeofcons_name=$this->post('firehome_typeofcons_name') ;
                                                                        $firehome_typeofcons_desc=$this->post('firehome_typeofcons_desc') ;



                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','firehome');
                                                                        if($employeetoken[0]=='ok')
                                                                        {
//****************************************************************************************************************
                                                                            $query="select * from firehome_typeofcons_tb where firehome_typeofcons_name='".$firehome_typeofcons_name."'";
                                                                            $result=$this->B_db->run_query($query);

                                                                            if(empty($result))
                                                                            {
                                                                                $query="INSERT INTO firehome_typeofcons_tb(firehome_typeofcons_id, firehome_typeofcons_name, firehome_typeofcons_desc)
	                            VALUES ( $firehome_typeofcons_id,'$firehome_typeofcons_name', '$firehome_typeofcons_desc');";


                                                                                $result=$this->B_db->run_query_put($query);
                                                                                //  $firehome_costcons_id=$this->db->insert_id();

                                                                                echo json_encode(array('result'=>"ok"
                                                                                ,"data"=>array('firehome_typeofcons_id'=>$firehome_typeofcons_id)
                                                                                ,'desc'=>'نوع سازه  بیمه اتش سوزی اضافه شد'),JSON_UNESCAPED_UNICODE);
                                                                            }else{
                                                                                $carmode=$result[0];
                                                                                echo json_encode(array('result'=>"error"
                                                                                ,"data"=>array('firehome_typeofcons_id'=>$carmode['firehome_typeofcons_id'])
                                                                                ,'desc'=>'نوع سازه  بیمه اتش سوزی تکراری است'),JSON_UNESCAPED_UNICODE);
                                                                            }
//***************************************************************************************************************
                                                                        }else{
                                                                            echo json_encode(array('result'=>$employeetoken[0]
                                                                            ,"data"=>$employeetoken[1]
                                                                            ,'desc'=>$employeetoken[2]));

                                                                        }






                                                                    }
                                                                    else
                                                                        if ($command=="get_firehome_typeofcons")
                                                                        {
//************************************************************************;****************************************

                                                                            $query="select * from firehome_typeofcons_tb where 1 ORDER BY firehome_typeofcons_id ASC";
                                                                            $result = $this->B_db->run_query($query);
                                                                            $output =array();
                                                                            foreach($result as $row)
                                                                            {
                                                                                $record=array();
                                                                                $record['firehome_typeofcons_id']=$row['firehome_typeofcons_id'];
                                                                                $record['firehome_typeofcons_name']=$row['firehome_typeofcons_name'];
                                                                                $record['firehome_typeofcons_desc']=$row['firehome_typeofcons_desc'];
                                                                                $output[]=$record;
                                                                            }
                                                                            echo json_encode(array('result'=>"ok"
                                                                            ,"data"=>$output
                                                                            ,'desc'=>'نوع سازه  بیمه اتش سوزی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                        }
                                                                        else
                                                                            if ($command=="delete_firehome_typeofcons")
                                                                            {
                                                                                $firehome_typeofcons_id=$this->post('firehome_typeofcons_id') ;


                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','firehome');
                                                                                if($employeetoken[0]=='ok')
                                                                                {
//************************************************************************;****************************************
                                                                                    $user_id=$employeetoken[1];
                                                                                    $output =array();
                                                                                    $query="DELETE FROM firehome_typeofcons_tb  where firehome_typeofcons_id=".$firehome_typeofcons_id."";
                                                                                    $result = $this->B_db->run_query_put($query);
                                                                                    if($result){echo json_encode(array('result'=>"ok"
                                                                                    ,"data"=>$output
                                                                                    ,'desc'=>'نوع سازه  بیمه اتش سوزی حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                    }else{
                                                                                        echo json_encode(array('result'=>"error"
                                                                                        ,"data"=>$output
                                                                                        ,'desc'=>'نوع سازه  بیمه اتش سوزی حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                    }
//***************************************************************************************************************
                                                                                }else{
                                                                                    echo json_encode(array('result'=>$employeetoken[0]
                                                                                    ,"data"=>$employeetoken[1]
                                                                                    ,'desc'=>$employeetoken[2]));

                                                                                }

                                                                            }
                                                                            else



                                                                                if ($command=="modify_firehome_typeofcons")
                                                                                {
                                                                                    $firehome_typeofcons_id=$this->post('firehome_typeofcons_id') ;


                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','firehome');
                                                                                    if($employeetoken[0]=='ok')
                                                                                    {
//*****************************************************************************************

                                                                                        $query="UPDATE firehome_typeofcons_tb SET ";

                                                                                        if(isset($_REQUEST['firehome_typeofcons_name'])){
                                                                                            $firehome_typeofcons_name=$this->post('firehome_typeofcons_name');
                                                                                            $query.="firehome_typeofcons_name='".$firehome_typeofcons_name."'";}

                                                                                        if(isset($_REQUEST['firehome_typeofcons_desc'])&&(isset($_REQUEST['firehome_typeofcons_name']))){ $query.=",";}
                                                                                        if(isset($_REQUEST['firehome_typeofcons_desc'])){
                                                                                            $firehome_typeofcons_desc=$this->post('firehome_typeofcons_desc');
                                                                                            $query.="firehome_typeofcons_desc='".$firehome_typeofcons_desc."'";}

                                                                                        $query.="where firehome_typeofcons_id=".$firehome_typeofcons_id;

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
                                                                                    if ($command=="add_firehome_price")
                                                                                    {
                                                                                        $firehome_price_fieldcompany_id=$this->post('firehome_price_fieldcompany_id');
                                                                                        $firehome_price_kind_id=$this->post('firehome_price_kind_id');
                                                                                        $firehome_price_typeofcons_id=$this->post('firehome_price_typeofcons_id');
                                                                                        $firehome_price_buildpercent=$this->post('firehome_price_buildpercent');
                                                                                        $firehome_price_furniturepercent=$this->post('firehome_price_furniturepercent');
                                                                                        $firehome_price_disc=$this->post('firehome_price_disc');

                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','firehome');
                                                                                        if($employeetoken[0]=='ok')
                                                                                        {
//****************************************************************************************************************
                                                                                            $query="select * from firehome_price_tb where firehome_price_typeofcons_id=".$firehome_price_typeofcons_id." AND firehome_price_kind_id=".$firehome_price_kind_id." AND firehome_price_fieldcompany_id=".$firehome_price_fieldcompany_id."";
                                                                                            $result=$this->B_db->run_query($query);

                                                                                            if(empty($result))
                                                                                            {
                                                                                                $query="INSERT INTO firehome_price_tb(firehome_price_typeofcons_id,firehome_price_fieldcompany_id,firehome_price_kind_id, firehome_price_buildpercent, firehome_price_furniturepercent, firehome_price_disc)
	                            VALUES ( $firehome_price_typeofcons_id,$firehome_price_fieldcompany_id,$firehome_price_kind_id,'$firehome_price_buildpercent', '$firehome_price_furniturepercent', '$firehome_price_disc');";

                                                                                                $result=$this->B_db->run_query_put($query);
                                                                                                $firehome_price_id=$this->db->insert_id();
                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                ,"data"=>array('firehome_price_id'=>$firehome_price_id)
                                                                                                ,'desc'=>'قیمت بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                            }else{
                                                                                                $carmode=$result[0];
                                                                                                echo json_encode(array('result'=>"error"
                                                                                                ,"data"=>array('firehome_price_id'=>$carmode['firehome_price_id'])
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
                                                                                        if ($command=="get_firehome_price")
                                                                                        {
//************************************************************************;****************************************

                                                                                            $query="select * from firehome_price_tb,fieldcompany_tb,firehome_kind_tb,company_tb,firehome_typeofcons_tb where firehome_price_fieldcompany_id=fieldcompany_id
  AND firehome_price_kind_id=firehome_kind_id
  AND firehome_price_typeofcons_id=firehome_typeofcons_id
  AND fieldcompany_company_id=company_id
 ORDER BY firehome_price_id ASC";
                                                                                            $result = $this->B_db->run_query($query);
                                                                                            $output =array();
                                                                                            foreach($result as $row)
                                                                                            {
                                                                                                $record=array();
                                                                                                $record['firehome_price_id']=$row['firehome_price_id'];
                                                                                                $record['firehome_price_fieldcompany_id']=$row['firehome_price_fieldcompany_id'];
                                                                                                $record['company_name']=$row['company_name'];
                                                                                                $record['company_logo_url']=IMGADD.$row['company_logo_url'];
                                                                                                $record['firehome_kind_name']=$row['firehome_kind_name'];
                                                                                                $record['firehome_typeofcons_name']=$row['firehome_typeofcons_name'];
                                                                                                $record['firehome_price_kind_id']=$row['firehome_price_kind_id'];
                                                                                                $record['firehome_price_typeofcons_id']=$row['firehome_price_typeofcons_id'];
                                                                                                $record['firehome_price_buildpercent']=$row['firehome_price_buildpercent'];
                                                                                                $record['firehome_price_furniturepercent']=$row['firehome_price_furniturepercent'];
                                                                                                $record['firehome_price_disc']=$row['firehome_price_disc'];
                                                                                                $record['firehome_price_deactive']=$row['firehome_price_deactive'];
                                                                                                $output[]=$record;
                                                                                            }
                                                                                            echo json_encode(array('result'=>"ok"
                                                                                            ,"data"=>$output
                                                                                            ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                        }
                                                                                        else
                                                                                            if ($command=="delete_firehome_price")
                                                                                            {
                                                                                                $firehome_price_id=$this->post('firehome_price_id');

                                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','firehome');
                                                                                                if($employeetoken[0]=='ok')
                                                                                                {
//************************************************************************;****************************************
                                                                                                    $user_id=$employeetoken[1];
                                                                                                    $output =array();
                                                                                                    $query="DELETE FROM firehome_price_tb  where firehome_price_id=".$firehome_price_id."";
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



                                                                                                if ($command=="modify_firehome_price")
                                                                                                {
                                                                                                    $firehome_price_id=$this->post('firehome_price_id');

                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','firehome');
                                                                                                    if($employeetoken[0]=='ok')
                                                                                                    {
//*****************************************************************************************

                                                                                                        $query="UPDATE firehome_price_tb SET ";

                                                                                                        if(isset($_REQUEST['firehome_price_buildpercent'])){
                                                                                                            $firehome_price_buildpercent=$this->post('firehome_price_buildpercent');
                                                                                                            $query.="firehome_price_buildpercent='".$firehome_price_buildpercent."'";}

                                                                                                        if(isset($_REQUEST['firehome_price_furniturepercent'])&&(isset($_REQUEST['firehome_price_buildpercent']))){ $query.=",";}
                                                                                                        if(isset($_REQUEST['firehome_price_furniturepercent'])){
                                                                                                            $firehome_price_furniturepercent=$this->post('firehome_price_furniturepercent');
                                                                                                            $query.="firehome_price_furniturepercent='".$firehome_price_furniturepercent."'";}


                                                                                                        if(isset($_REQUEST['firehome_price_disc'])&&(isset($_REQUEST['firehome_price_furniturepercent'])||isset($_REQUEST['firehome_price_buildpercent']))){ $query.=",";}
                                                                                                        if(isset($_REQUEST['firehome_price_disc'])){
                                                                                                            $firehome_price_disc=$this->post('firehome_price_disc');
                                                                                                            $query.="firehome_price_disc='".$firehome_price_disc."'";}


                                                                                                        if(isset($_REQUEST['firehome_price_deactive'])&&(isset($_REQUEST['firehome_price_disc'])||isset($_REQUEST['firehome_price_furniturepercent'])||isset($_REQUEST['firehome_price_buildpercent']))){ $query.=",";}
                                                                                                        if(isset($_REQUEST['firehome_price_deactive'])){
                                                                                                            $firehome_price_deactive=$this->post('firehome_price_deactive');
                                                                                                            $query.="firehome_price_deactive=".$firehome_price_deactive."";}

                                                                                                        $query.=" where firehome_price_id=".$firehome_price_id;

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
                                                                                                        $firehome_price_covarage_firehome_price_id=$this->post('firehome_price_covarage_firehome_price_id');
                                                                                                        $firehome_price_covarage_firehome_covarage_id=$this->post('firehome_price_covarage_firehome_covarage_id');
                                                                                                        $firehome_price_covarage_percent=$this->post('firehome_price_covarage_percent');

                                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','firehome');
                                                                                                        if($employeetoken[0]=='ok')
                                                                                                        {
//****************************************************************************************************************
                                                                                                            $query="select * from firehome_price_covarage_tb where firehome_price_covarage_firehome_covarage_id=".$firehome_price_covarage_firehome_covarage_id." AND firehome_price_covarage_firehome_price_id=".$firehome_price_covarage_firehome_price_id."";
                                                                                                            $result=$this->B_db->run_query($query);

                                                                                                            if(empty($result))
                                                                                                            {
                                                                                                                $query="INSERT INTO firehome_price_covarage_tb(firehome_price_covarage_firehome_price_id,firehome_price_covarage_firehome_covarage_id, firehome_price_covarage_percent)
	                            VALUES ( $firehome_price_covarage_firehome_price_id,$firehome_price_covarage_firehome_covarage_id, '$firehome_price_covarage_percent');";

                                                                                                                $result=$this->B_db->run_query_put($query);
                                                                                                                $firehome_price_covarage_id=$this->db->insert_id();
                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                ,"data"=>array('firehome_price_covarage_id'=>$firehome_price_covarage_id)
                                                                                                                ,'desc'=>'مدت زمان بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                            }else{
                                                                                                                $carmode=$result[0];
                                                                                                                echo json_encode(array('result'=>"error"
                                                                                                                ,"data"=>array('firehome_price_covarage_id'=>$carmode['firehome_price_covarage_id'])
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
                                                                                                        if ($command=="get_price_covarage")
                                                                                                        {
                                                                                                            $firehome_price_id=$this->post('firehome_price_id');

//************************************************************************;****************************************

                                                                                                            $query="select * from firehome_price_covarage_tb,firehome_coverage_tb where firehome_coverage_id=firehome_price_covarage_firehome_covarage_id AND firehome_price_covarage_firehome_price_id=$firehome_price_id  ORDER BY firehome_price_covarage_id ASC";
                                                                                                            $result = $this->B_db->run_query($query);
                                                                                                            $output =array();
                                                                                                            foreach($result as $row)
                                                                                                            {
                                                                                                                $record=array();
                                                                                                                $record['firehome_price_covarage_id']=$row['firehome_price_covarage_id'];
                                                                                                                $record['firehome_price_covarage_firehome_price_id']=$row['firehome_price_covarage_firehome_price_id'];
                                                                                                                $record['firehome_price_covarage_firehome_covarage_id']=$row['firehome_price_covarage_firehome_covarage_id'];
                                                                                                                $record['firehome_coverage_name']=$row['firehome_coverage_name'];
                                                                                                                $record['firehome_coverage_desc']=$row['firehome_coverage_desc'];
                                                                                                                $record['firehome_coverage_calculat_id']=$row['firehome_coverage_calculat_id'];
                                                                                                                $record['firehome_price_covarage_percent']=$row['firehome_price_covarage_percent'];
                                                                                                                $output[]=$record;
                                                                                                            }
                                                                                                            echo json_encode(array('result'=>"ok"
                                                                                                            ,"data"=>$output
                                                                                                            ,'desc'=>'مدت زمان بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                                        }
                                                                                                        else
                                                                                                            if ($command=="delete_price_covarage")
                                                                                                            {
                                                                                                                $firehome_price_covarage_id=$this->post('firehome_price_covarage_id');

                                                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','firehome');
                                                                                                                if($employeetoken[0]=='ok')
                                                                                                                {
//************************************************************************;****************************************
                                                                                                                    $user_id=$employeetoken[1];
                                                                                                                    $output =array();
                                                                                                                    $query="DELETE FROM firehome_price_covarage_tb  where firehome_price_covarage_id=".$firehome_price_covarage_id."";
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



                                                                                                                if ($command=="modify_price_covarage")
                                                                                                                {
                                                                                                                    $firehome_price_covarage_id=$this->post('firehome_price_covarage_id');

                                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','firehome');
                                                                                                                    if($employeetoken[0]=='ok')
                                                                                                                    {
//*****************************************************************************************

                                                                                                                        $query="UPDATE firehome_price_covarage_tb SET ";


                                                                                                                        if(isset($_REQUEST['firehome_price_covarage_percent'])){
                                                                                                                            $firehome_price_covarage_percent=$this->post('firehome_price_covarage_percent');
                                                                                                                            $query.="firehome_price_covarage_percent='".$firehome_price_covarage_percent."'";}

                                                                                                                        $query.="where firehome_price_covarage_id=".$firehome_price_covarage_id;

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
                                                                                                                    if ($command=="add_price_buildinglife")
                                                                                                                    {
                                                                                                                        $firehome_price_buildinglife_firehome_price_id=$this->post('firehome_price_buildinglife_firehome_price_id');
                                                                                                                        $firehome_price_buildinglife_firehome_buildinglife_id=$this->post('firehome_price_buildinglife_firehome_buildinglife_id');
                                                                                                                        $firehome_price_buildinglife_percent=$this->post('firehome_price_buildinglife_percent');

                                                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','firehome');
                                                                                                                        if($employeetoken[0]=='ok')
                                                                                                                        {
//****************************************************************************************************************
                                                                                                                            $query="select * from firehome_price_buildinglife_tb where firehome_price_buildinglife_firehome_buildinglife_id=".$firehome_price_buildinglife_firehome_buildinglife_id." AND firehome_price_buildinglife_firehome_price_id=".$firehome_price_buildinglife_firehome_price_id."";
                                                                                                                            $result=$this->B_db->run_query($query);

                                                                                                                            if(empty($result))
                                                                                                                            {
                                                                                                                                $query="INSERT INTO firehome_price_buildinglife_tb(firehome_price_buildinglife_firehome_price_id,firehome_price_buildinglife_firehome_buildinglife_id, firehome_price_buildinglife_percent)
	                            VALUES ( $firehome_price_buildinglife_firehome_price_id,$firehome_price_buildinglife_firehome_buildinglife_id, '$firehome_price_buildinglife_percent');";

                                                                                                                                $result=$this->B_db->run_query_put($query);
                                                                                                                                $firehome_price_buildinglife_id=$this->db->insert_id();
                                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                                ,"data"=>array('firehome_price_buildinglife_id'=>$firehome_price_buildinglife_id)
                                                                                                                                ,'desc'=>'مدت زمان بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                            }else{
                                                                                                                                $carmode=$result[0];
                                                                                                                                echo json_encode(array('result'=>"error"
                                                                                                                                ,"data"=>array('firehome_price_buildinglife_id'=>$carmode['firehome_price_buildinglife_id'])
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
                                                                                                                        if ($command=="get_price_buildinglife")
                                                                                                                        {
                                                                                                                            $firehome_price_id=$this->post('firehome_price_id');

//************************************************************************;****************************************

                                                                                                                            $query="select * from firehome_price_buildinglife_tb,firehome_buildinglife_tb where firehome_buildinglife_id=firehome_price_buildinglife_firehome_buildinglife_id AND firehome_price_buildinglife_firehome_price_id=$firehome_price_id  ORDER BY firehome_price_buildinglife_id ASC";
                                                                                                                            $result = $this->B_db->run_query($query);
                                                                                                                            $output =array();
                                                                                                                            foreach($result as $row)
                                                                                                                            {
                                                                                                                                $record=array();
                                                                                                                                $record['firehome_price_buildinglife_id']=$row['firehome_price_buildinglife_id'];
                                                                                                                                $record['firehome_price_buildinglife_firehome_price_id']=$row['firehome_price_buildinglife_firehome_price_id'];
                                                                                                                                $record['firehome_price_buildinglife_firehome_buildinglife_id']=$row['firehome_price_buildinglife_firehome_buildinglife_id'];
                                                                                                                                $record['firehome_buildinglife_name']=$row['firehome_buildinglife_name'];
                                                                                                                                $record['firehome_buildinglife_desc']=$row['firehome_buildinglife_desc'];
                                                                                                                                $record['firehome_price_buildinglife_percent']=$row['firehome_price_buildinglife_percent'];
                                                                                                                                $output[]=$record;
                                                                                                                            }
                                                                                                                            echo json_encode(array('result'=>"ok"
                                                                                                                            ,"data"=>$output
                                                                                                                            ,'desc'=>'مدت زمان بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                                                        }
                                                                                                                        else
                                                                                                                            if ($command=="delete_price_buildinglife")
                                                                                                                            {
                                                                                                                                $firehome_price_buildinglife_id=$this->post('firehome_price_buildinglife_id');

                                                                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','firehome');
                                                                                                                                if($employeetoken[0]=='ok')
                                                                                                                                {
//************************************************************************;****************************************
                                                                                                                                    $user_id=$employeetoken[1];
                                                                                                                                    $output =array();
                                                                                                                                    $query="DELETE FROM firehome_price_buildinglife_tb  where firehome_price_buildinglife_id=".$firehome_price_buildinglife_id."";
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



                                                                                                                                if ($command=="modify_price_buildinglife") {
                                                                                                                                    $firehome_price_buildinglife_id = $this->post('firehome_price_buildinglife_id');

                                                                                                                                    $employeetoken = checkpermissionemployeetoken($employee_token_str, 'modify', 'firehome');
                                                                                                                                    if ($employeetoken[0] == 'ok') {
//*****************************************************************************************

                                                                                                                                        $query = "UPDATE firehome_price_buildinglife_tb SET ";


                                                                                                                                        if (isset($_REQUEST['firehome_price_buildinglife_percent'])) {
                                                                                                                                            $firehome_price_buildinglife_percent = $this->post('firehome_price_buildinglife_percent');
                                                                                                                                            $query .= "firehome_price_buildinglife_percent='" . $firehome_price_buildinglife_percent . "'";
                                                                                                                                        }

                                                                                                                                        $query .= "where firehome_price_buildinglife_id=" . $firehome_price_buildinglife_id;

                                                                                                                                        $result = $this->B_db->run_query_put($query);
                                                                                                                                        if ($result) {
                                                                                                                                            echo json_encode(array('result' => "ok"
                                                                                                                                            , "data" => ""
                                                                                                                                            , 'desc' => 'تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                                                                                                        }
//***************************************************************************************************************
                                                                                                                                    } else {
                                                                                                                                        echo json_encode(array('result' => $employeetoken[0]
                                                                                                                                        , "data" => $employeetoken[1]
                                                                                                                                        , 'desc' => $employeetoken[2]));

                                                                                                                                    }


                                                                                                                                }                                                                                                                                }
    }
}