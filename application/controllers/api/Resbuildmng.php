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
class Resbuildmng extends REST_Controller {

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
        if ($this->B_user->checkrequestip('resbuildmng', $command, get_client_ip(),50,50)) {
        if ($command=="get_yearofcons")
        {
            $output =array();
            $def=date("Y")-jdate('Y','',"",'','en');
            $ynow=date("Y");
            for($i=0;$i<50;$i++)
            {
                $record=array();
                $record['resbuildmng_buildinglife_id']=$i;
                $record['resbuildmng_buildinglife_name']=($ynow-$i-$def);
                $output[]=$record;
            }
            $record=array();
            $record['resbuildmng_buildinglife_id']=$i;
            $record['resbuildmng_buildinglife_name']=($ynow-$i-$def).' و ماقبل';
            $output[]=$record;
            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>' سال ساخت با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }else   if ($command=="get_numunit")
        {
            $output =array();
           for($i=1;$i<150;$i++)
            {
                $record=array();
                $record['resbuildmng_numunit_id']=$i;
                $record['resbuildmng_numunit_name']=($i .' واحد ');
                $output[]=$record;
            }
            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>' سال ساخت با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }else if ($command=="add_resbuildmng_kind")
        {

            $resbuildmng_kind_id=$this->post('resbuildmng_kind_id') ;
            $resbuildmng_kind_name=$this->post('resbuildmng_kind_name') ;
            $resbuildmng_kind_desc=$this->post('resbuildmng_kind_desc') ;




            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','resbuildmng');
            if($employeetoken[0]=='ok')
            {
//************************************************************************;****************************************
                $query="select * from resbuildmng_kind_tb where resbuildmng_kind_name='".$resbuildmng_kind_name."' OR resbuildmng_kind_id=".$resbuildmng_kind_id."";
                $result=$this->B_db->run_query($query);

                if(empty($result))
                {
                    $query="INSERT INTO resbuildmng_kind_tb(resbuildmng_kind_id, resbuildmng_kind_name, resbuildmng_kind_desc)
	                            VALUES ( $resbuildmng_kind_id,'$resbuildmng_kind_name', '$resbuildmng_kind_desc');";

                    $result=$this->B_db->run_query_put($query);
                    // $resbuildmng_kind_id=$this->db->insert_id();
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('resbuildmng_kind_id'=>$resbuildmng_kind_id)
                    ,'desc'=>'نوع ملک بیمه اتش سوزی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    $carmode=$result[0];
                    echo json_encode(array('result'=>"error"
                    ,"data"=>array('resbuildmng_kind_id'=>$carmode['resbuildmng_kind_id'])
                    ,'desc'=>'نوع ملک بیمه اتش سوزی تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
//***************************************************************************************************************
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }
        }

        if ($command=="get_resbuildmng_kind")
        {
//************************************************************************;****************************************

            $query="select * from resbuildmng_kind_tb where 1 ORDER BY resbuildmng_kind_id ASC";
            $result = $this->B_db->run_query($query);
            $output =array();
            foreach($result as $row)
            {
                $record=array();
                $record['resbuildmng_kind_id']=$row['resbuildmng_kind_id'];
                $record['resbuildmng_kind_name']=$row['resbuildmng_kind_name'];
                $record['resbuildmng_kind_desc']=$row['resbuildmng_kind_desc'];
                $output[]=$record;
            }
            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'نوع ملک بیمه اتش سوزی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

        }
        else
            if ($command=="delete_resbuildmng_kind")
            {
                $resbuildmng_kind_id=$this->post('resbuildmng_kind_id') ;

                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','resbuildmng');
                if($employeetoken[0]=='ok')
                {
//************************************************************************;****************************************
                    $user_id=$employeetoken[1];
                    $output =array();
                    $query="DELETE FROM resbuildmng_kind_tb  where resbuildmng_kind_id=".$resbuildmng_kind_id."";
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



                if ($command=="modify_resbuildmng_kind")
                {
                    $resbuildmng_kind_id=$this->post('resbuildmng_kind_id') ;


                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','resbuildmng');
                    if($employeetoken[0]=='ok')
                    {
//*****************************************************************************************

                        $query="UPDATE resbuildmng_kind_tb SET ";

                        if(isset($_REQUEST['resbuildmng_kind_name'])){
                            $resbuildmng_kind_name=$this->post('resbuildmng_kind_name');
                            $query.="resbuildmng_kind_name='".$resbuildmng_kind_name."'";}

                        if(isset($_REQUEST['resbuildmng_kind_desc'])&&(isset($_REQUEST['resbuildmng_kind_name']))){ $query.=",";}
                        if(isset($_REQUEST['resbuildmng_kind_desc'])){
                            $resbuildmng_kind_desc=$this->post('resbuildmng_kind_desc');
                            $query.="resbuildmng_kind_desc='".$resbuildmng_kind_desc."'";}

                        $query.="where resbuildmng_kind_id=".$resbuildmng_kind_id;

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
                    if ($command=="add_resbuildmng_buildinglife")
                    {
                        $resbuildmng_buildinglife_id=$this->post('resbuildmng_buildinglife_id') ;
                        $resbuildmng_buildinglife_name=$this->post('resbuildmng_buildinglife_name') ;
                        $resbuildmng_buildinglife_desc=$this->post('resbuildmng_buildinglife_desc') ;



                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','resbuildmng');
                        if($employeetoken[0]=='ok')
                        {
//****************************************************************************************************************
                            $query="select * from resbuildmng_buildinglife_tb where resbuildmng_buildinglife_id=$resbuildmng_buildinglife_id AND resbuildmng_buildinglife_name='".$resbuildmng_buildinglife_name."'";
                            $result=$this->B_db->run_query($query);

                            if(empty($result))
                            {
                                $query="INSERT INTO resbuildmng_buildinglife_tb(resbuildmng_buildinglife_id, resbuildmng_buildinglife_name, resbuildmng_buildinglife_desc)
	                            VALUES ( $resbuildmng_buildinglife_id,'$resbuildmng_buildinglife_name', '$resbuildmng_buildinglife_desc');";

                                $result=$this->B_db->run_query_put($query);
                                //   $resbuildmng_buildinglife_id=$this->db->insert_id();

                                echo json_encode(array('result'=>"ok"
                                ,"data"=>array('resbuildmng_buildinglife_id'=>$resbuildmng_buildinglife_id)
                                ,'desc'=>'تعداد واحد بیمه اتش سوزی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }else{
                                $carmode=$result[0];
                                echo json_encode(array('result'=>"error"
                                ,"data"=>array('resbuildmng_buildinglife_id'=>$carmode['resbuildmng_buildinglife_id'])
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
                        if ($command=="get_resbuildmng_buildinglife")
                        {
//************************************************************************;****************************************

                            $query="select * from resbuildmng_buildinglife_tb where 1 ORDER BY resbuildmng_buildinglife_id ASC";
                            $result = $this->B_db->run_query($query);
                            $output =array();
                            foreach($result as $row)
                            {
                                $record=array();
                                $record['resbuildmng_buildinglife_id']=$row['resbuildmng_buildinglife_id'];
                                $record['resbuildmng_buildinglife_name']=$row['resbuildmng_buildinglife_name'];
                                $record['resbuildmng_buildinglife_desc']=$row['resbuildmng_buildinglife_desc'];
                                $output[]=$record;
                            }
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>$output
                            ,'desc'=>'تعداد واحد بیمه اتش سوزی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                        }
                        else
                            if ($command=="delete_resbuildmng_buildinglife")
                            {
                                $resbuildmng_buildinglife_id=$this->post('resbuildmng_buildinglife_id') ;

                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','resbuildmng');
                                if($employeetoken[0]=='ok')
                                {
//************************************************************************;****************************************
                                    $user_id=$employeetoken[1];
                                    $output =array();
                                    $query="DELETE FROM resbuildmng_buildinglife_tb  where resbuildmng_buildinglife_id=".$resbuildmng_buildinglife_id."";
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
                                if ($command=="modify_resbuildmng_buildinglife")
                                {
                                    $resbuildmng_buildinglife_id=$this->post('resbuildmng_buildinglife_id') ;
                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','resbuildmng');
                                    if($employeetoken[0]=='ok')
                                    {
                                        $query="UPDATE resbuildmng_buildinglife_tb SET ";
                                        if(isset($_REQUEST['resbuildmng_buildinglife_name'])){
                                            $resbuildmng_buildinglife_name=$this->post('resbuildmng_buildinglife_name');
                                            $query.="resbuildmng_buildinglife_name='".$resbuildmng_buildinglife_name."'";}
                                        if(isset($_REQUEST['resbuildmng_buildinglife_desc'])&&(isset($_REQUEST['resbuildmng_buildinglife_name']))){ $query.=",";}
                                        if(isset($_REQUEST['resbuildmng_buildinglife_desc'])){
                                            $resbuildmng_buildinglife_desc=$this->post('resbuildmng_buildinglife_desc');
                                            $query.="resbuildmng_buildinglife_desc='".$resbuildmng_buildinglife_desc."'";}
                                        $query.="where resbuildmng_buildinglife_id=".$resbuildmng_buildinglife_id;

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
                                    if ($command=="add_resbuildmng_financemedic")
                                    {
                                        $resbuildmng_financemedic_id=$this->post('resbuildmng_financemedic_id') ;
                                        $resbuildmng_financemedic_name=$this->post('resbuildmng_financemedic_name') ;
                                        $resbuildmng_financemedic_price=$this->post('resbuildmng_financemedic_price') ;
                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','resbuildmng');
                                        if($employeetoken[0]=='ok')
                                        {
                                            $query="select * from resbuildmng_financemedic_tb where resbuildmng_financemedic_name='".$resbuildmng_financemedic_name."'";
                                            $result=$this->B_db->run_query($query);

                                            if(empty($result))
                                            {
                                                $query="INSERT INTO resbuildmng_financemedic_tb(resbuildmng_financemedic_id, resbuildmng_financemedic_name, resbuildmng_financemedic_price)
	                            VALUES ( $resbuildmng_financemedic_id,'$resbuildmng_financemedic_name', '$resbuildmng_financemedic_price');";

                                                $result=$this->B_db->run_query_put($query);
                                                //  $resbuildmng_financemedic_id=$this->db->insert_id();

                                                echo json_encode(array('result'=>"ok"
                                                ,"data"=>array('resbuildmng_financemedic_id'=>$resbuildmng_financemedic_id)
                                                ,'desc'=>'هزینه ساخت بیمه اتش سوزی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                            }else{
                                                $carmode=$result[0];
                                                echo json_encode(array('result'=>"error"
                                                ,"data"=>array('resbuildmng_financemedic_id'=>$carmode['resbuildmng_financemedic_id'])
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
                                        if ($command=="get_resbuildmng_financemedic")
                                        {
//************************************************************************;****************************************
                                            $query="select * from resbuildmng_financemedic_tb where 1 ORDER BY resbuildmng_financemedic_id ASC";
                                            $result = $this->B_db->run_query($query);
                                            $output =array();
                                            foreach($result as $row)
                                            {
                                                $record=array();
                                                $record['resbuildmng_financemedic_id']=$row['resbuildmng_financemedic_id'];
                                                $record['resbuildmng_financemedic_name']=$row['resbuildmng_financemedic_name'];
                                                $record['resbuildmng_financemedic_price']=$row['resbuildmng_financemedic_price'];
                                                $output[]=$record;
                                            }
                                            echo json_encode(array('result'=>"ok"
                                            ,"data"=>$output
                                            ,'desc'=>'هزینه ساخت بیمه اتش سوزی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                        }
                                        else
                                            if ($command=="delete_resbuildmng_financemedic")
                                            {
                                                $resbuildmng_financemedic_id=$this->post('resbuildmng_financemedic_id') ;
                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','resbuildmng');
                                                if($employeetoken[0]=='ok')
                                                {
                                                    $user_id=$employeetoken[1];
                                                    $output =array();
                                                    $query="DELETE FROM resbuildmng_financemedic_tb  where resbuildmng_financemedic_id=".$resbuildmng_financemedic_id."";
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
                                                if ($command=="modify_resbuildmng_financemedic")
                                                {
                                                    $resbuildmng_financemedic_id=$this->post('resbuildmng_financemedic_id') ;
                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','resbuildmng');
                                                    if($employeetoken[0]=='ok')
                                                    {
                                                        $query="UPDATE resbuildmng_financemedic_tb SET ";

                                                        if(isset($_REQUEST['resbuildmng_financemedic_name'])){
                                                            $resbuildmng_financemedic_name=$this->post('resbuildmng_financemedic_name');
                                                            $query.="resbuildmng_financemedic_name='".$resbuildmng_financemedic_name."'";}

                                                        if(isset($_REQUEST['resbuildmng_financemedic_price'])&&(isset($_REQUEST['resbuildmng_financemedic_name']))){ $query.=",";}
                                                        if(isset($_REQUEST['resbuildmng_financemedic_price'])){
                                                            $resbuildmng_financemedic_price=$this->post('resbuildmng_financemedic_price');
                                                            $query.="resbuildmng_financemedic_price='".$resbuildmng_financemedic_price."'";}

                                                        $query.="where resbuildmng_financemedic_id=".$resbuildmng_financemedic_id;

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
                                                    if ($command=="add_resbuildmng_coverage")
                                                    {
                                                        $resbuildmng_coverage_id=$this->post('resbuildmng_coverage_id') ;
                                                        $resbuildmng_coverage_name=$this->post('resbuildmng_coverage_name') ;
                                                        $resbuildmng_coverage_desc=$this->post('resbuildmng_coverage_desc') ;
                                                        $resbuildmng_coverage_calculat_id=$this->post('resbuildmng_coverage_calculat_id') ;

                                                        $resbuildmng_coverage_extrafield=$this->post('resbuildmng_coverage_extrafield') ;

                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','resbuildmng');
                                                        if($employeetoken[0]=='ok')
                                                        {
//****************************************************************************************************************
                                                            $query="select * from resbuildmng_coverage_tb where resbuildmng_coverage_name='".$resbuildmng_coverage_name."'";
                                                            $result=$this->B_db->run_query($query);

                                                            if(empty($result))
                                                            {
                                                                $query="INSERT INTO resbuildmng_coverage_tb(resbuildmng_coverage_id, resbuildmng_coverage_name, resbuildmng_coverage_desc, resbuildmng_coverage_calculat_id, resbuildmng_coverage_extrafield)
	                            VALUES ( $resbuildmng_coverage_id,'$resbuildmng_coverage_name', '$resbuildmng_coverage_desc', $resbuildmng_coverage_calculat_id, $resbuildmng_coverage_extrafield);";
                                                                $result=$this->B_db->run_query_put($query);
                                                                //  $resbuildmng_financemedic_id=$this->db->insert_id();

                                                                echo json_encode(array('result'=>"ok"
                                                                ,"data"=>array('resbuildmng_coverage_id'=>$resbuildmng_coverage_id)
                                                                ,'desc'=>'پوشش بیمه اتش سوزی اضافه شد'.$query),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                            }else{
                                                                $carmode=$result[0];
                                                                echo json_encode(array('result'=>"error"
                                                                ,"data"=>array('resbuildmng_coverage_id'=>$carmode['resbuildmng_coverage_id'])
                                                                ,'desc'=>'پوشش بیمه اتش سوزی تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                            }
                                                        }else{
                                                            echo json_encode(array('result'=>$employeetoken[0]
                                                            ,"data"=>$employeetoken[1]
                                                            ,'desc'=>$employeetoken[2]));
                                                        }
                                                    }
                                                    else
                                                        if ($command=="get_resbuildmng_coverage")
                                                        {
                                                            $query="select * from resbuildmng_coverage_tb where 1 ORDER BY resbuildmng_coverage_id ASC";
                                                            $result = $this->B_db->run_query($query);
                                                            $output =array();
                                                            foreach($result as $row)
                                                            {
                                                                $record=array();
                                                                $record['resbuildmng_coverage_id']=$row['resbuildmng_coverage_id'];
                                                                $record['resbuildmng_coverage_name']=$row['resbuildmng_coverage_name'];
                                                                $record['resbuildmng_coverage_desc']=$row['resbuildmng_coverage_desc'];
                                                                $record['resbuildmng_coverage_calculat_id']=$row['resbuildmng_coverage_calculat_id'];
                                                                $record['resbuildmng_coverage_extrafield']=$row['resbuildmng_coverage_extrafield'];
                                                                $output[]=$record;
                                                            }
                                                            echo json_encode(array('result'=>"ok"
                                                            ,"data"=>$output
                                                            ,'desc'=>'پوشش بیمه اتش سوزی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                        }
                                                        else
                                                            if ($command=="delete_resbuildmng_coverage")
                                                            {
                                                                $resbuildmng_coverage_id=$this->post('resbuildmng_coverage_id') ;


                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','resbuildmng');
                                                                if($employeetoken[0]=='ok')
                                                                {
//************************************************************************;****************************************
                                                                    $user_id=$employeetoken[1];
                                                                    $output =array();
                                                                    $query="DELETE FROM resbuildmng_coverage_tb  where resbuildmng_coverage_id=".$resbuildmng_coverage_id."";
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



                                                                if ($command=="modify_resbuildmng_coverage")
                                                                {
                                                                    $resbuildmng_coverage_id=$this->post('resbuildmng_coverage_id') ;


                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','resbuildmng');
                                                                    if($employeetoken[0]=='ok')
                                                                    {
//*****************************************************************************************

                                                                        $query="UPDATE resbuildmng_coverage_tb SET ";

                                                                        if(isset($_REQUEST['resbuildmng_coverage_name'])){
                                                                            $resbuildmng_coverage_name=$this->post('resbuildmng_coverage_name');
                                                                            $query.="resbuildmng_coverage_name='".$resbuildmng_coverage_name."'";}

                                                                        if(isset($_REQUEST['resbuildmng_coverage_desc'])&&(isset($_REQUEST['resbuildmng_coverage_name']))){ $query.=",";}
                                                                        if(isset($_REQUEST['resbuildmng_coverage_desc'])){
                                                                            $resbuildmng_coverage_desc=$this->post('resbuildmng_coverage_desc');
                                                                            $query.="resbuildmng_coverage_desc='".$resbuildmng_coverage_desc."'";}

                                                                        if(isset($_REQUEST['resbuildmng_coverage_extrafield'])&&(isset($_REQUEST['resbuildmng_coverage_desc'])||isset($_REQUEST['resbuildmng_coverage_name']))){ $query.=",";}
                                                                        if(isset($_REQUEST['resbuildmng_coverage_extrafield'])){
                                                                            $resbuildmng_coverage_extrafield=$this->post('resbuildmng_coverage_extrafield') ;
                                                                            $query.="resbuildmng_coverage_extrafield=".$resbuildmng_coverage_extrafield." ";}

                                                                        if(isset($_REQUEST['resbuildmng_coverage_calculat_id'])&&(isset($_REQUEST['resbuildmng_coverage_extrafield'])||isset($_REQUEST['resbuildmng_coverage_desc'])||isset($_REQUEST['resbuildmng_coverage_name']))){ $query.=",";}
                                                                        if(isset($_REQUEST['resbuildmng_coverage_calculat_id'])){
                                                                            $resbuildmng_coverage_calculat_id=$this->post('resbuildmng_coverage_calculat_id') ;
                                                                            $query.="resbuildmng_coverage_calculat_id=".$resbuildmng_coverage_calculat_id." ";}

                                                                        $query.=" where resbuildmng_coverage_id=".$resbuildmng_coverage_id;

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
                                                                    if ($command=="add_resbuildmng_typeofcons")
                                                                    {
                                                                        $resbuildmng_typeofcons_id=$this->post('resbuildmng_typeofcons_id') ;
                                                                        $resbuildmng_typeofcons_name=$this->post('resbuildmng_typeofcons_name') ;
                                                                        $resbuildmng_typeofcons_desc=$this->post('resbuildmng_typeofcons_desc') ;



                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','resbuildmng');
                                                                        if($employeetoken[0]=='ok')
                                                                        {
//****************************************************************************************************************
                                                                            $query="select * from resbuildmng_typeofcons_tb where resbuildmng_typeofcons_name='".$resbuildmng_typeofcons_name."'";
                                                                            $result=$this->B_db->run_query($query);

                                                                            if(empty($result))
                                                                            {
                                                                                $query="INSERT INTO resbuildmng_typeofcons_tb(resbuildmng_typeofcons_id, resbuildmng_typeofcons_name, resbuildmng_typeofcons_desc)
	                            VALUES ( $resbuildmng_typeofcons_id,'$resbuildmng_typeofcons_name', '$resbuildmng_typeofcons_desc');";


                                                                                $result=$this->B_db->run_query_put($query);
                                                                                //  $resbuildmng_financemedic_id=$this->db->insert_id();

                                                                                echo json_encode(array('result'=>"ok"
                                                                                ,"data"=>array('resbuildmng_typeofcons_id'=>$resbuildmng_typeofcons_id)
                                                                                ,'desc'=>'نوع سازه  بیمه اتش سوزی اضافه شد'),JSON_UNESCAPED_UNICODE);
                                                                            }else{
                                                                                $carmode=$result[0];
                                                                                echo json_encode(array('result'=>"error"
                                                                                ,"data"=>array('resbuildmng_typeofcons_id'=>$carmode['resbuildmng_typeofcons_id'])
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
                                                                        if ($command=="get_resbuildmng_typeofcons")
                                                                        {
//************************************************************************;****************************************

                                                                            $query="select * from resbuildmng_typeofcons_tb where 1 ORDER BY resbuildmng_typeofcons_id ASC";
                                                                            $result = $this->B_db->run_query($query);
                                                                            $output =array();
                                                                            foreach($result as $row)
                                                                            {
                                                                                $record=array();
                                                                                $record['resbuildmng_typeofcons_id']=$row['resbuildmng_typeofcons_id'];
                                                                                $record['resbuildmng_typeofcons_name']=$row['resbuildmng_typeofcons_name'];
                                                                                $record['resbuildmng_typeofcons_desc']=$row['resbuildmng_typeofcons_desc'];
                                                                                $output[]=$record;
                                                                            }
                                                                            echo json_encode(array('result'=>"ok"
                                                                            ,"data"=>$output
                                                                            ,'desc'=>'نوع سازه  بیمه اتش سوزی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                        }
                                                                        else
                                                                            if ($command=="delete_resbuildmng_typeofcons")
                                                                            {
                                                                                $resbuildmng_typeofcons_id=$this->post('resbuildmng_typeofcons_id') ;


                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','resbuildmng');
                                                                                if($employeetoken[0]=='ok')
                                                                                {
//************************************************************************;****************************************
                                                                                    $user_id=$employeetoken[1];
                                                                                    $output =array();
                                                                                    $query="DELETE FROM resbuildmng_typeofcons_tb  where resbuildmng_typeofcons_id=".$resbuildmng_typeofcons_id."";
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



                                                                                if ($command=="modify_resbuildmng_typeofcons")
                                                                                {
                                                                                    $resbuildmng_typeofcons_id=$this->post('resbuildmng_typeofcons_id') ;


                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','resbuildmng');
                                                                                    if($employeetoken[0]=='ok')
                                                                                    {
//*****************************************************************************************

                                                                                        $query="UPDATE resbuildmng_typeofcons_tb SET ";

                                                                                        if(isset($_REQUEST['resbuildmng_typeofcons_name'])){
                                                                                            $resbuildmng_typeofcons_name=$this->post('resbuildmng_typeofcons_name');
                                                                                            $query.="resbuildmng_typeofcons_name='".$resbuildmng_typeofcons_name."'";}

                                                                                        if(isset($_REQUEST['resbuildmng_typeofcons_desc'])&&(isset($_REQUEST['resbuildmng_typeofcons_name']))){ $query.=",";}
                                                                                        if(isset($_REQUEST['resbuildmng_typeofcons_desc'])){
                                                                                            $resbuildmng_typeofcons_desc=$this->post('resbuildmng_typeofcons_desc');
                                                                                            $query.="resbuildmng_typeofcons_desc='".$resbuildmng_typeofcons_desc."'";}

                                                                                        $query.="where resbuildmng_typeofcons_id=".$resbuildmng_typeofcons_id;

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
                                                                                    if ($command=="add_resbuildmng_price")
                                                                                    {
                                                                                        $resbuildmng_price_fieldcompany_id=$this->post('resbuildmng_price_fieldcompany_id');
                                                                                        $resbuildmng_price_kind_id=$this->post('resbuildmng_price_kind_id');
                                                                                        $resbuildmng_price_financemedic_id=$this->post('resbuildmng_price_financemedic_id');
                                                                                        $resbuildmng_price_typeofcons_id=$this->post('resbuildmng_price_typeofcons_id');
                                                                                        $resbuildmng_price_fromyear=$this->post('resbuildmng_price_fromyear');
                                                                                        $resbuildmng_price_amount=$this->post('resbuildmng_price_amount');
                                                                                        $resbuildmng_price_toyear=$this->post('resbuildmng_price_toyear');
                                                                                        $resbuildmng_price_chashdisc=$this->post('resbuildmng_price_chashdisc');
                                                                                        $resbuildmng_price_disc=$this->post('resbuildmng_price_disc');

                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','resbuildmng');
                                                                                        if($employeetoken[0]=='ok')
                                                                                        {
//****************************************************************************************************************
                                                                                            $query1="select * from resbuildmng_price_tb where resbuildmng_price_fromyear='$resbuildmng_price_fromyear' AND resbuildmng_price_toyear='$resbuildmng_price_toyear' AND resbuildmng_price_financemedic_id=".$resbuildmng_price_financemedic_id."  AND resbuildmng_price_kind_id=".$resbuildmng_price_kind_id." AND resbuildmng_price_fieldcompany_id=".$resbuildmng_price_fieldcompany_id."";
                                                                                            $result=$this->B_db->run_query($query1);

                                                                                            if(empty($result))
                                                                                            {
                                                                                                $query="INSERT INTO resbuildmng_price_tb(resbuildmng_price_financemedic_id,resbuildmng_price_typeofcons_id,resbuildmng_price_fieldcompany_id,resbuildmng_price_kind_id, resbuildmng_price_fromyear, resbuildmng_price_amount, resbuildmng_price_toyear, resbuildmng_price_disc, resbuildmng_price_chashdisc)
	                                                                                                                             VALUES ( $resbuildmng_price_financemedic_id,$resbuildmng_price_typeofcons_id,$resbuildmng_price_fieldcompany_id,$resbuildmng_price_kind_id,'$resbuildmng_price_fromyear', '$resbuildmng_price_amount', '$resbuildmng_price_toyear', '$resbuildmng_price_disc', '$resbuildmng_price_chashdisc');";

                                                                                                $result=$this->B_db->run_query_put($query);
                                                                                                $resbuildmng_price_id=$this->db->insert_id();
                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                ,"data"=>array('resbuildmng_price_id'=>$resbuildmng_price_id)
                                                                                                ,'desc'=>'قیمت بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                            }else{
                                                                                                $carmode=$result[0];
                                                                                                echo json_encode(array('result'=>"error"
                                                                                                ,"data"=>array('resbuildmng_price_id'=>$carmode['resbuildmng_price_id'])
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
                                                                                        if ($command=="get_resbuildmng_price")
                                                                                        {
//************************************************************************;****************************************

                                                                                            $query="select * from resbuildmng_price_tb,fieldcompany_tb,resbuildmng_kind_tb,company_tb,resbuildmng_financemedic_tb where resbuildmng_price_fieldcompany_id=fieldcompany_id
  AND resbuildmng_price_kind_id=resbuildmng_kind_id
  AND resbuildmng_price_financemedic_id=resbuildmng_financemedic_id
  AND fieldcompany_company_id=company_id
 ORDER BY resbuildmng_price_id ASC";
                                                                                            $result = $this->B_db->run_query($query);
                                                                                            $output =array();
                                                                                            foreach($result as $row)
                                                                                            {
                                                                                                $record=array();
                                                                                                $record['resbuildmng_price_id']=$row['resbuildmng_price_id'];
                                                                                                $record['resbuildmng_price_fieldcompany_id']=$row['resbuildmng_price_fieldcompany_id'];
                                                                                                $record['company_name']=$row['company_name'];
                                                                                                $record['company_logo_url']=IMGADD.$row['company_logo_url'];
                                                                                                $record['resbuildmng_kind_name']=$row['resbuildmng_kind_name'];
                                                                                                $record['resbuildmng_financemedic_name']=$row['resbuildmng_financemedic_name'];
                                                                                                $record['resbuildmng_price_kind_id']=$row['resbuildmng_price_kind_id'];
                                                                                                $record['resbuildmng_price_typeofcons_id']=$row['resbuildmng_price_typeofcons_id'];
                                                                                                $record['resbuildmng_price_financemedic_id']=$row['resbuildmng_price_financemedic_id'];
                                                                                                $record['resbuildmng_price_fromyear']=$row['resbuildmng_price_fromyear'];
                                                                                                $record['resbuildmng_price_toyear']=$row['resbuildmng_price_toyear'];
                                                                                                $record['resbuildmng_price_amount']=$row['resbuildmng_price_amount'];
                                                                                                $record['resbuildmng_price_chashdisc']=$row['resbuildmng_price_chashdisc'];
                                                                                                $record['resbuildmng_price_disc']=$row['resbuildmng_price_disc'];
                                                                                                $record['resbuildmng_price_deactive']=$row['resbuildmng_price_deactive'];
                                                                                                $output[]=$record;
                                                                                            }
                                                                                            echo json_encode(array('result'=>"ok"
                                                                                            ,"data"=>$output
                                                                                            ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                                        }
                                                                                        else
                                                                                            if ($command=="delete_resbuildmng_price")
                                                                                            {
                                                                                                $resbuildmng_price_id=$this->post('resbuildmng_price_id');

                                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','resbuildmng');
                                                                                                if($employeetoken[0]=='ok')
                                                                                                {
//************************************************************************;****************************************
                                                                                                    $user_id=$employeetoken[1];
                                                                                                    $output =array();
                                                                                                    $query="DELETE FROM resbuildmng_price_tb  where resbuildmng_price_id=".$resbuildmng_price_id."";
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



                                                                                                if ($command=="modify_resbuildmng_price")
                                                                                                {
                                                                                                    $resbuildmng_price_id=$this->post('resbuildmng_price_id');

                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','resbuildmng');
                                                                                                    if($employeetoken[0]=='ok')
                                                                                                    {
//*****************************************************************************************

                                                                                                        $query="UPDATE resbuildmng_price_tb SET ";

                                                                                                        if(isset($_REQUEST['resbuildmng_price_fromyear'])){
                                                                                                            $resbuildmng_price_fromyear=$this->post('resbuildmng_price_fromyear');
                                                                                                            $query.="resbuildmng_price_fromyear='".$resbuildmng_price_fromyear."' ";}

                                                                                                        if(isset($_REQUEST['resbuildmng_price_toyear'])&&(isset($_REQUEST['resbuildmng_price_fromyear']))){ $query.=",";}
                                                                                                        if(isset($_REQUEST['resbuildmng_price_toyear'])){
                                                                                                            $resbuildmng_price_toyear=$this->post('resbuildmng_price_toyear');
                                                                                                            $query.="resbuildmng_price_toyear='".$resbuildmng_price_toyear."' ";}


                                                                                                        if(isset($_REQUEST['resbuildmng_price_disc'])&&(isset($_REQUEST['resbuildmng_price_toyear'])||isset($_REQUEST['resbuildmng_price_fromyear']))){ $query.=",";}
                                                                                                        if(isset($_REQUEST['resbuildmng_price_disc'])){
                                                                                                            $resbuildmng_price_disc=$this->post('resbuildmng_price_disc');
                                                                                                            $query.="resbuildmng_price_disc='".$resbuildmng_price_disc."' ";}


                                                                                                        if(isset($_REQUEST['resbuildmng_price_deactive'])&&(isset($_REQUEST['resbuildmng_price_disc'])||isset($_REQUEST['resbuildmng_price_toyear'])||isset($_REQUEST['resbuildmng_price_fromyear']))){ $query.=",";}
                                                                                                        if(isset($_REQUEST['resbuildmng_price_deactive'])){
                                                                                                            $resbuildmng_price_deactive=$this->post('resbuildmng_price_deactive');
                                                                                                            $query.="resbuildmng_price_deactive=".$resbuildmng_price_deactive." ";}

                                                                                                        if(isset($_REQUEST['resbuildmng_price_amount'])&&(isset($_REQUEST['resbuildmng_price_deactive'])||isset($_REQUEST['resbuildmng_price_disc'])||isset($_REQUEST['resbuildmng_price_toyear'])||isset($_REQUEST['resbuildmng_price_fromyear']))){ $query.=",";}
                                                                                                        if(isset($_REQUEST['resbuildmng_price_amount'])){
                                                                                                            $resbuildmng_price_amount=$this->post('resbuildmng_price_amount');
                                                                                                            $query.="resbuildmng_price_amount=".$resbuildmng_price_amount."  ";}

                                                                                                      if(isset($_REQUEST['resbuildmng_price_typeofcons_id'])&&(isset($_REQUEST['resbuildmng_price_amount'])||isset($_REQUEST['resbuildmng_price_deactive'])||isset($_REQUEST['resbuildmng_price_disc'])||isset($_REQUEST['resbuildmng_price_toyear'])||isset($_REQUEST['resbuildmng_price_fromyear']))){ $query.=",";}
                                                                                                        if(isset($_REQUEST['resbuildmng_price_typeofcons_id'])){
                                                                                                            $resbuildmng_price_typeofcons_id=$this->post('resbuildmng_price_typeofcons_id');
                                                                                                            $query.="resbuildmng_price_typeofcons_id=".$resbuildmng_price_typeofcons_id."  ";}

                                                                                                        if(isset($_REQUEST['resbuildmng_price_chashdisc'])&&(isset($_REQUEST['resbuildmng_price_amount'])||isset($_REQUEST['resbuildmng_price_deactive'])||isset($_REQUEST['resbuildmng_price_disc'])||isset($_REQUEST['resbuildmng_price_toyear'])||isset($_REQUEST['resbuildmng_price_fromyear']))){ $query.=",";}
                                                                                                        if(isset($_REQUEST['resbuildmng_price_chashdisc'])){
                                                                                                            $resbuildmng_price_chashdisc=$this->post('resbuildmng_price_chashdisc');
                                                                                                            $query.="resbuildmng_price_chashdisc='".$resbuildmng_price_chashdisc."'  ";}



                                                                                                        $query.=" where resbuildmng_price_id=".$resbuildmng_price_id;

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
                                                                                                        $resbuildmng_price_covarage_resbuildmng_price_id=$this->post('resbuildmng_price_covarage_resbuildmng_price_id');
                                                                                                        $resbuildmng_price_covarage_resbuildmng_covarage_id=$this->post('resbuildmng_price_covarage_resbuildmng_covarage_id');
                                                                                                        $resbuildmng_price_covarage_percent=$this->post('resbuildmng_price_covarage_percent');

                                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','resbuildmng');
                                                                                                        if($employeetoken[0]=='ok')
                                                                                                        {
//****************************************************************************************************************
                                                                                                            $query="select * from resbuildmng_price_covarage_tb where resbuildmng_price_covarage_resbuildmng_covarage_id=".$resbuildmng_price_covarage_resbuildmng_covarage_id." AND resbuildmng_price_covarage_resbuildmng_price_id=".$resbuildmng_price_covarage_resbuildmng_price_id."";
                                                                                                            $result=$this->B_db->run_query($query);

                                                                                                            if(empty($result))
                                                                                                            {
                                                                                                                $query="INSERT INTO resbuildmng_price_covarage_tb(resbuildmng_price_covarage_resbuildmng_price_id,resbuildmng_price_covarage_resbuildmng_covarage_id, resbuildmng_price_covarage_percent)
	                            VALUES ( $resbuildmng_price_covarage_resbuildmng_price_id,$resbuildmng_price_covarage_resbuildmng_covarage_id, '$resbuildmng_price_covarage_percent');";

                                                                                                                $result=$this->B_db->run_query_put($query);
                                                                                                                $resbuildmng_price_covarage_id=$this->db->insert_id();
                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                ,"data"=>array('resbuildmng_price_covarage_id'=>$resbuildmng_price_covarage_id)
                                                                                                                ,'desc'=>'مدت زمان بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                            }else{
                                                                                                                $carmode=$result[0];
                                                                                                                echo json_encode(array('result'=>"error"
                                                                                                                ,"data"=>array('resbuildmng_price_covarage_id'=>$carmode['resbuildmng_price_covarage_id'])
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
                                                                                                            $resbuildmng_price_id=$this->post('resbuildmng_price_id');

//************************************************************************;****************************************

                                                                                                            $query="select * from resbuildmng_price_covarage_tb,resbuildmng_coverage_tb where resbuildmng_coverage_id=resbuildmng_price_covarage_resbuildmng_covarage_id AND resbuildmng_price_covarage_resbuildmng_price_id=$resbuildmng_price_id  ORDER BY resbuildmng_price_covarage_id ASC";
                                                                                                            $result = $this->B_db->run_query($query);
                                                                                                            $output =array();
                                                                                                            foreach($result as $row)
                                                                                                            {
                                                                                                                $record=array();
                                                                                                                $record['resbuildmng_price_covarage_id']=$row['resbuildmng_price_covarage_id'];
                                                                                                                $record['resbuildmng_price_covarage_resbuildmng_price_id']=$row['resbuildmng_price_covarage_resbuildmng_price_id'];
                                                                                                                $record['resbuildmng_price_covarage_resbuildmng_covarage_id']=$row['resbuildmng_price_covarage_resbuildmng_covarage_id'];
                                                                                                                $record['resbuildmng_coverage_name']=$row['resbuildmng_coverage_name'];
                                                                                                                $record['resbuildmng_coverage_desc']=$row['resbuildmng_coverage_desc'];
                                                                                                                $record['resbuildmng_coverage_calculat_id']=$row['resbuildmng_coverage_calculat_id'];
                                                                                                                $record['resbuildmng_price_covarage_percent']=$row['resbuildmng_price_covarage_percent'];
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
                                                                                                                $resbuildmng_price_covarage_id=$this->post('resbuildmng_price_covarage_id');

                                                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','resbuildmng');
                                                                                                                if($employeetoken[0]=='ok')
                                                                                                                {
//************************************************************************;****************************************
                                                                                                                    $user_id=$employeetoken[1];
                                                                                                                    $output =array();
                                                                                                                    $query="DELETE FROM resbuildmng_price_covarage_tb  where resbuildmng_price_covarage_id=".$resbuildmng_price_covarage_id."";
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
                                                                                                                    $resbuildmng_price_covarage_id=$this->post('resbuildmng_price_covarage_id');

                                                                                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','resbuildmng');
                                                                                                                    if($employeetoken[0]=='ok')
                                                                                                                    {
//*****************************************************************************************

                                                                                                                        $query="UPDATE resbuildmng_price_covarage_tb SET ";


                                                                                                                        if(isset($_REQUEST['resbuildmng_price_covarage_percent'])){
                                                                                                                            $resbuildmng_price_covarage_percent=$this->post('resbuildmng_price_covarage_percent');
                                                                                                                            $query.="resbuildmng_price_covarage_percent='".$resbuildmng_price_covarage_percent."'";}

                                                                                                                        $query.="where resbuildmng_price_covarage_id=".$resbuildmng_price_covarage_id;

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
                                                                                                                        $resbuildmng_price_buildinglife_resbuildmng_price_id=$this->post('resbuildmng_price_buildinglife_resbuildmng_price_id');
                                                                                                                        $resbuildmng_price_buildinglife_resbuildmng_buildinglife_id=$this->post('resbuildmng_price_buildinglife_resbuildmng_buildinglife_id');
                                                                                                                        $resbuildmng_price_buildinglife_percent=$this->post('resbuildmng_price_buildinglife_percent');

                                                                                                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','resbuildmng');
                                                                                                                        if($employeetoken[0]=='ok')
                                                                                                                        {
//****************************************************************************************************************
                                                                                                                            $query="select * from resbuildmng_price_buildinglife_tb where resbuildmng_price_buildinglife_resbuildmng_buildinglife_id=".$resbuildmng_price_buildinglife_resbuildmng_buildinglife_id." AND resbuildmng_price_buildinglife_resbuildmng_price_id=".$resbuildmng_price_buildinglife_resbuildmng_price_id."";
                                                                                                                            $result=$this->B_db->run_query($query);

                                                                                                                            if(empty($result))
                                                                                                                            {
                                                                                                                                $query="INSERT INTO resbuildmng_price_buildinglife_tb(resbuildmng_price_buildinglife_resbuildmng_price_id,resbuildmng_price_buildinglife_resbuildmng_buildinglife_id, resbuildmng_price_buildinglife_percent)
	                            VALUES ( $resbuildmng_price_buildinglife_resbuildmng_price_id,$resbuildmng_price_buildinglife_resbuildmng_buildinglife_id, '$resbuildmng_price_buildinglife_percent');";

                                                                                                                                $result=$this->B_db->run_query_put($query);
                                                                                                                                $resbuildmng_price_buildinglife_id=$this->db->insert_id();
                                                                                                                                echo json_encode(array('result'=>"ok"
                                                                                                                                ,"data"=>array('resbuildmng_price_buildinglife_id'=>$resbuildmng_price_buildinglife_id)
                                                                                                                                ,'desc'=>'مدت زمان بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                                                                                            }else{
                                                                                                                                $carmode=$result[0];
                                                                                                                                echo json_encode(array('result'=>"error"
                                                                                                                                ,"data"=>array('resbuildmng_price_buildinglife_id'=>$carmode['resbuildmng_price_buildinglife_id'])
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
                                                                                                                            $resbuildmng_price_id=$this->post('resbuildmng_price_id');

//************************************************************************;****************************************

                                                                                                                            $query="select * from resbuildmng_price_buildinglife_tb,resbuildmng_buildinglife_tb where resbuildmng_buildinglife_id=resbuildmng_price_buildinglife_resbuildmng_buildinglife_id AND resbuildmng_price_buildinglife_resbuildmng_price_id=$resbuildmng_price_id  ORDER BY resbuildmng_price_buildinglife_id ASC";
                                                                                                                            $result = $this->B_db->run_query($query);
                                                                                                                            $output =array();
                                                                                                                            foreach($result as $row)
                                                                                                                            {
                                                                                                                                $record=array();
                                                                                                                                $record['resbuildmng_price_buildinglife_id']=$row['resbuildmng_price_buildinglife_id'];
                                                                                                                                $record['resbuildmng_price_buildinglife_resbuildmng_price_id']=$row['resbuildmng_price_buildinglife_resbuildmng_price_id'];
                                                                                                                                $record['resbuildmng_price_buildinglife_resbuildmng_buildinglife_id']=$row['resbuildmng_price_buildinglife_resbuildmng_buildinglife_id'];
                                                                                                                                $record['resbuildmng_buildinglife_name']=$row['resbuildmng_buildinglife_name'];
                                                                                                                                $record['resbuildmng_buildinglife_desc']=$row['resbuildmng_buildinglife_desc'];
                                                                                                                                $record['resbuildmng_price_buildinglife_percent']=$row['resbuildmng_price_buildinglife_percent'];
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
                                                                                                                                $resbuildmng_price_buildinglife_id=$this->post('resbuildmng_price_buildinglife_id');

                                                                                                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','resbuildmng');
                                                                                                                                if($employeetoken[0]=='ok')
                                                                                                                                {
//************************************************************************;****************************************
                                                                                                                                    $user_id=$employeetoken[1];
                                                                                                                                    $output =array();
                                                                                                                                    $query="DELETE FROM resbuildmng_price_buildinglife_tb  where resbuildmng_price_buildinglife_id=".$resbuildmng_price_buildinglife_id."";
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
                                                                                                                                    $resbuildmng_price_buildinglife_id = $this->post('resbuildmng_price_buildinglife_id');

                                                                                                                                    $employeetoken = checkpermissionemployeetoken($employee_token_str, 'modify', 'resbuildmng');
                                                                                                                                    if ($employeetoken[0] == 'ok') {
//*****************************************************************************************

                                                                                                                                        $query = "UPDATE resbuildmng_price_buildinglife_tb SET ";


                                                                                                                                        if (isset($_REQUEST['resbuildmng_price_buildinglife_percent'])) {
                                                                                                                                            $resbuildmng_price_buildinglife_percent = $this->post('resbuildmng_price_buildinglife_percent');
                                                                                                                                            $query .= "resbuildmng_price_buildinglife_percent='" . $resbuildmng_price_buildinglife_percent . "'";
                                                                                                                                        }

                                                                                                                                        $query .= "where resbuildmng_price_buildinglife_id=" . $resbuildmng_price_buildinglife_id;

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