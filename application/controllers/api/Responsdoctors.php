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
class Responsdoctors extends REST_Controller {

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
        //var_dump($this->post);
        if(isset($this->input->request_headers()['Authorization']))$employee_token_str = $this->input->request_headers()['Authorization'];
        $this->load->model('B_user');
        $this->load->model('B_db');
        $this->load->model('B_agent');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if ($command=="add_responsdoctors_medicspecialty")
        {

            $responsdoctors_medicspecialty_id=$this->post('responsdoctors_medicspecialty_id') ;
            $responsdoctors_medicspecialty_name=$this->post('responsdoctors_medicspecialty_name') ;
            $responsdoctors_medicspecialty_desc=$this->post('responsdoctors_medicspecialty_desc') ;




            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','responsdoctors');
            if($employeetoken[0]=='ok')
            {
//************************************************************************;****************************************
                $query="select * from responsdoctors_medicspecialty_tb where responsdoctors_medicspecialty_name='".$responsdoctors_medicspecialty_name."' OR responsdoctors_medicspecialty_id=".$responsdoctors_medicspecialty_id."";
                $result=$this->B_db->run_query($query);
                $num=count($result[0]);
                if ($num==0)
                {
                    $query="INSERT INTO responsdoctors_medicspecialty_tb(responsdoctors_medicspecialty_id, responsdoctors_medicspecialty_name, responsdoctors_medicspecialty_desc)
	                            VALUES ( $responsdoctors_medicspecialty_id,'$responsdoctors_medicspecialty_name', '$responsdoctors_medicspecialty_desc');";

                    $result=$this->B_db->run_query_put($query);
                    // $responsdoctors_medicspecialty_id=$this->db->insert_id();
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('responsdoctors_medicspecialty_id'=>$responsdoctors_medicspecialty_id)
                    ,'desc'=>'تخصص پزشک اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    $responsdoctors_medicspecialty=$result[0];
                    echo json_encode(array('result'=>"error"
                    ,"data"=>array('responsdoctors_medicspecialty_id'=>$responsdoctors_medicspecialty['responsdoctors_medicspecialty_id'])
                    ,'desc'=>'تخصص پزشک تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
//***************************************************************************************************************
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }
        }

        if ($command=="get_responsdoctors_medicspecialty")
        {
//************************************************************************;****************************************

            $query="select * from responsdoctors_medicspecialty_tb where 1 ORDER BY responsdoctors_medicspecialty_id ASC";
            $result = $this->B_db->run_query($query);
            $output =array();
            foreach($result as $row)
            {
                $record=array();
                $record['responsdoctors_medicspecialty_id']=$row['responsdoctors_medicspecialty_id'];
                $record['responsdoctors_medicspecialty_name']=$row['responsdoctors_medicspecialty_name'];
                $record['responsdoctors_medicspecialty_desc']=$row['responsdoctors_medicspecialty_desc'];
                $output[]=$record;
            }
            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'تخصص پزشک با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

        }
        else
            if ($command=="delete_responsdoctors_medicspecialty")
            {
                $responsdoctors_medicspecialty_id=$this->post('responsdoctors_medicspecialty_id') ;

                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','responsdoctors');
                if($employeetoken[0]=='ok')
                {
//************************************************************************;****************************************
                    $output = array();$user_id=$employeetoken[0];

                    $query="DELETE FROM responsdoctors_medicspecialty_tb  where responsdoctors_medicspecialty_id=".$responsdoctors_medicspecialty_id."";
                    $result = $this->B_db->run_query_put($query);
                    if($result){echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'تخصص پزشک حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        echo json_encode(array('result'=>"error"
                        ,"data"=>$output
                        ,'desc'=>'تخصص پزشک حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
//***************************************************************************************************************
                }else{
                    echo json_encode(array('result'=>$employeetoken[0]
                    ,"data"=>$employeetoken[1]
                    ,'desc'=>$employeetoken[2]));
                }

            }
            else
                if ($command=="modify_responsdoctors_medicspecialty")
                {
                    $responsdoctors_medicspecialty_id=$this->post('responsdoctors_medicspecialty_id') ;


                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','responsdoctors');
                    if($employeetoken[0]=='ok')
                    {
                        $query="UPDATE responsdoctors_medicspecialty_tb SET ";

                        if(isset($_REQUEST['responsdoctors_medicspecialty_name'])){
                            $responsdoctors_medicspecialty_name=$this->post('responsdoctors_medicspecialty_name');
                            $query.="responsdoctors_medicspecialty_name='".$responsdoctors_medicspecialty_name."'";}

                        if(isset($_REQUEST['responsdoctors_medicspecialty_desc'])&&(isset($_REQUEST['responsdoctors_medicspecialty_name']))){ $query.=",";}
                        if(isset($_REQUEST['responsdoctors_medicspecialty_desc'])){
                            $responsdoctors_medicspecialty_desc=$this->post('responsdoctors_medicspecialty_desc');
                            $query.="responsdoctors_medicspecialty_desc='".$responsdoctors_medicspecialty_desc."'";}

                        $query.="where responsdoctors_medicspecialty_id=".$responsdoctors_medicspecialty_id;

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
                    if ($command=="add_responsdoctors_paramedicspecialty")
                    {
                        $responsdoctors_paramedicspecialty_id=$this->post('responsdoctors_paramedicspecialty_id') ;
                        $responsdoctors_paramedicspecialty_name=$this->post('responsdoctors_paramedicspecialty_name') ;
                        $responsdoctors_paramedicspecialty_desc=$this->post('responsdoctors_paramedicspecialty_desc') ;



                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','responsdoctors');
                        if($employeetoken[0]=='ok')
                        {
//****************************************************************************************************************
                            $query="select * from responsdoctors_paramedicspecialty_tb where responsdoctors_paramedicspecialty_id=$responsdoctors_paramedicspecialty_id AND responsdoctors_paramedicspecialty_name='".$responsdoctors_paramedicspecialty_name."'";
                            $result=$this->B_db->run_query($query);
                            $num=count($result[0]);
                            if ($num==0)
                            {
                                $query="INSERT INTO responsdoctors_paramedicspecialty_tb(responsdoctors_paramedicspecialty_id, responsdoctors_paramedicspecialty_name, responsdoctors_paramedicspecialty_desc)
	                            VALUES ( $responsdoctors_paramedicspecialty_id,'$responsdoctors_paramedicspecialty_name', '$responsdoctors_paramedicspecialty_desc');";

                                $result=$this->B_db->run_query_put($query);
                                //   $responsdoctors_paramedicspecialty_id=$this->db->insert_id();

                                echo json_encode(array('result'=>"ok"
                                ,"data"=>array('responsdoctors_paramedicspecialty_id'=>$responsdoctors_paramedicspecialty_id)
                                ,'desc'=>'تخصص پیراپزشک اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }else{
                                $responsdoctors_paramedicspecialty=$result[0];
                                echo json_encode(array('result'=>"error"
                                ,"data"=>array('responsdoctors_paramedicspecialty_id'=>$responsdoctors_paramedicspecialty['responsdoctors_paramedicspecialty_id'])
                                ,'desc'=>'تخصص پیراپزشک تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
//***************************************************************************************************************
                        }else{
                            echo json_encode(array('result'=>$employeetoken[0]
                            ,"data"=>$employeetoken[1]
                            ,'desc'=>$employeetoken[2]));

                        }






                    }
                    else
                        if ($command=="get_responsdoctors_paramedicspecialty")
                        {
//************************************************************************;****************************************

                            $query="select * from responsdoctors_paramedicspecialty_tb where 1 ORDER BY responsdoctors_paramedicspecialty_id ASC";
                            $result = $this->B_db->run_query($query);
                            $output =array();
                            foreach($result as $row)
                            {
                                $record=array();
                                $record['responsdoctors_paramedicspecialty_id']=$row['responsdoctors_paramedicspecialty_id'];
                                $record['responsdoctors_paramedicspecialty_name']=$row['responsdoctors_paramedicspecialty_name'];
                                $record['responsdoctors_paramedicspecialty_desc']=$row['responsdoctors_paramedicspecialty_desc'];
                                $output[]=$record;
                            }
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>$output
                            ,'desc'=>'تخصص پیراپزشک با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                        }
                        else
                            if ($command=="delete_responsdoctors_paramedicspecialty")
                            {
                                $responsdoctors_paramedicspecialty_id=$this->post('responsdoctors_paramedicspecialty_id') ;

                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','responsdoctors');
                                if($employeetoken[0]=='ok')
                                {
//************************************************************************;****************************************
                                    $output = array();$user_id=$employeetoken[0];

                                    $query="DELETE FROM responsdoctors_paramedicspecialty_tb  where responsdoctors_paramedicspecialty_id=".$responsdoctors_paramedicspecialty_id."";
                                    $result = $this->B_db->run_query_put($query);
                                    if($result){echo json_encode(array('result'=>"ok"
                                    ,"data"=>$output
                                    ,'desc'=>'تخصص پیراپزشک حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }else{
                                        echo json_encode(array('result'=>"error"
                                        ,"data"=>$output
                                        ,'desc'=>'تخصص پیراپزشک حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }
//***************************************************************************************************************
                                }else{
                                    echo json_encode(array('result'=>$employeetoken[0]
                                    ,"data"=>$employeetoken[1]
                                    ,'desc'=>$employeetoken[2]));

                                }

                            }
                            else



                                if ($command=="modify_responsdoctors_paramedicspecialty")
                                {
                                    $responsdoctors_paramedicspecialty_id=$this->post('responsdoctors_paramedicspecialty_id') ;


                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','responsdoctors');
                                    if($employeetoken[0]=='ok')
                                    {
//*****************************************************************************************

                                        $query="UPDATE responsdoctors_paramedicspecialty_tb SET ";

                                        if(isset($_REQUEST['responsdoctors_paramedicspecialty_name'])){
                                            $responsdoctors_paramedicspecialty_name=$this->post('responsdoctors_paramedicspecialty_name');
                                            $query.="responsdoctors_paramedicspecialty_name='".$responsdoctors_paramedicspecialty_name."'";}

                                        if(isset($_REQUEST['responsdoctors_paramedicspecialty_desc'])&&(isset($_REQUEST['responsdoctors_paramedicspecialty_name']))){ $query.=",";}
                                        if(isset($_REQUEST['responsdoctors_paramedicspecialty_desc'])){
                                            $responsdoctors_paramedicspecialty_desc=$this->post('responsdoctors_paramedicspecialty_desc');
                                            $query.="responsdoctors_paramedicspecialty_desc='".$responsdoctors_paramedicspecialty_desc."'";}

                                        $query.="where responsdoctors_paramedicspecialty_id=".$responsdoctors_paramedicspecialty_id;

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
                                    if ($command=="add_responsdoctors_damage")
                                    {
                                        $responsdoctors_damage_id=$this->post('responsdoctors_damage_id') ;
                                        $responsdoctors_damage_name=$this->post('responsdoctors_damage_name') ;
                                        $responsdoctors_damage_percent=$this->post('responsdoctors_damage_percent') ;



                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','responsdoctors');
                                        if($employeetoken[0]=='ok')
                                        {
//****************************************************************************************************************
                                            $query="select * from responsdoctors_damage_tb where responsdoctors_damage_name='".$responsdoctors_damage_name."'";
                                            $result=$this->B_db->run_query($query);
                                            $num=count($result[0]);
                                            if ($num==0)
                                            {
                                                $query="INSERT INTO responsdoctors_damage_tb(responsdoctors_damage_id, responsdoctors_damage_name, responsdoctors_damage_percent)
	                            VALUES ( $responsdoctors_damage_id,'$responsdoctors_damage_name', '$responsdoctors_damage_percent');";

                                                $result=$this->B_db->run_query_put($query);
                                                //  $responsdoctors_damage_id=$this->db->insert_id();

                                                echo json_encode(array('result'=>"ok"
                                                ,"data"=>array('responsdoctors_damage_id'=>$responsdoctors_damage_id)
                                                ,'desc'=>'تخفیف عدم خسارت اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                            }else{
                                                $responsdoctors_damage=$result[0];
                                                echo json_encode(array('result'=>"error"
                                                ,"data"=>array('responsdoctors_damage_id'=>$responsdoctors_damage['responsdoctors_damage_id'])
                                                ,'desc'=>'تخفیف عدم خسارت تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                            }
//***************************************************************************************************************
                                        }else{
                                            echo json_encode(array('result'=>$employeetoken[0]
                                            ,"data"=>$employeetoken[1]
                                            ,'desc'=>$employeetoken[2]));

                                        }

                                    }
                                    else
                                        if ($command=="get_responsdoctors_damage")
                                        {
//************************************************************************;****************************************

                                            $query="select * from responsdoctors_damage_tb where 1 ORDER BY responsdoctors_damage_id ASC";
                                            $result = $this->B_db->run_query($query);
                                            $output =array();
                                            foreach($result as $row)
                                            {
                                                $record=array();
                                                $record['responsdoctors_damage_id']=$row['responsdoctors_damage_id'];
                                                $record['responsdoctors_damage_name']=$row['responsdoctors_damage_name'];
                                                $record['responsdoctors_damage_percent']=$row['responsdoctors_damage_percent'];
                                                $output[]=$record;
                                            }
                                            echo json_encode(array('result'=>"ok"
                                            ,"data"=>$output
                                            ,'desc'=>'تخفیف عدم خسارت با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                        }
                                        else
                                            if ($command=="delete_responsdoctors_damage")
                                            {
                                                $responsdoctors_damage_id=$this->post('responsdoctors_damage_id') ;


                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','responsdoctors');
                                                if($employeetoken[0]=='ok')
                                                {
//************************************************************************;****************************************
                                                    $output = array();$user_id=$employeetoken[0];

                                                    $query="DELETE FROM responsdoctors_damage_tb  where responsdoctors_damage_id=".$responsdoctors_damage_id."";
                                                    $result = $this->B_db->run_query_put($query);
                                                    if($result){echo json_encode(array('result'=>"ok"
                                                    ,"data"=>$output
                                                    ,'desc'=>'تخفیف عدم خسارت حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                    }else{
                                                        echo json_encode(array('result'=>"error"
                                                        ,"data"=>$output
                                                        ,'desc'=>'تخفیف عدم خسارت حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                    }
//***************************************************************************************************************
                                                }else{
                                                    echo json_encode(array('result'=>$employeetoken[0]
                                                    ,"data"=>$employeetoken[1]
                                                    ,'desc'=>$employeetoken[2]));

                                                }

                                            }
                                            else



                                                if ($command=="modify_responsdoctors_damage")
                                                {
                                                    $responsdoctors_damage_id=$this->post('responsdoctors_damage_id') ;


                                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','responsdoctors');
                                                    if($employeetoken[0]=='ok')
                                                    {
//*****************************************************************************************

                                                        $query="UPDATE responsdoctors_damage_tb SET ";

                                                        if(isset($_REQUEST['responsdoctors_damage_name'])){
                                                            $responsdoctors_damage_name=$this->post('responsdoctors_damage_name');
                                                            $query.="responsdoctors_damage_name='".$responsdoctors_damage_name."'";}

                                                        if(isset($_REQUEST['responsdoctors_damage_percent'])&&(isset($_REQUEST['responsdoctors_damage_name']))){ $query.=",";}
                                                        if(isset($_REQUEST['responsdoctors_damage_percent'])){
                                                            $responsdoctors_damage_percent=$this->post('responsdoctors_damage_percent');
                                                            $query.="responsdoctors_damage_percent='".$responsdoctors_damage_percent."'";}

                                                        $query.="where responsdoctors_damage_id=".$responsdoctors_damage_id;

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
if ($command=="add_responsdoctors_price")
{
$responsdoctors_price_fieldcompany_id=$this->post('responsdoctors_price_fieldcompany_id');
$responsdoctors_price_discount=$this->post('responsdoctors_price_discount');
$responsdoctors_price_additionalcoverage=$this->post('responsdoctors_price_additionalcoverage');
$responsdoctors_price_student=$this->post('responsdoctors_price_student');
$responsdoctors_price_resident=$this->post('responsdoctors_price_resident');

$employeetoken=checkpermissionemployeetoken($employee_token_str,'new','responsdoctors');
if($employeetoken[0]=='ok')
{
//****************************************************************************************************************
$query="select * from responsdoctors_price_tb where responsdoctors_price_fieldcompany_id=".$responsdoctors_price_fieldcompany_id."";
$result=$this->B_db->run_query($query);
$num=count($result[0]);
if ($num==0)
{
$query="INSERT INTO responsdoctors_price_tb(responsdoctors_price_additionalcoverage,responsdoctors_price_fieldcompany_id,responsdoctors_price_discount, responsdoctors_price_student, responsdoctors_price_resident)
VALUES ( '$responsdoctors_price_additionalcoverage',$responsdoctors_price_fieldcompany_id,'$responsdoctors_price_discount','$responsdoctors_price_student', '$responsdoctors_price_resident');";

$result=$this->B_db->run_query_put($query);
$responsdoctors_price_id=$this->db->insert_id();
echo json_encode(array('result'=>"ok"
,"data"=>array('responsdoctors_price_id'=>$responsdoctors_price_id)
,'desc'=>'قیمت بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
}else{
$responsdoctors_price=$result[0];
echo json_encode(array('result'=>"error"
,"data"=>array('responsdoctors_price_id'=>$responsdoctors_price['responsdoctors_price_id'])
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
if ($command=="get_responsdoctors_price")
{
//************************************************************************;****************************************

$query="select * from responsdoctors_price_tb,fieldcompany_tb,company_tb where responsdoctors_price_fieldcompany_id=fieldcompany_id
AND fieldcompany_company_id=company_id
ORDER BY responsdoctors_price_id ASC";
$result = $this->B_db->run_query($query);
$output =array();
foreach($result as $row)
{
$record=array();
$record['responsdoctors_price_id']=$row['responsdoctors_price_id'];
$record['responsdoctors_price_fieldcompany_id']=$row['responsdoctors_price_fieldcompany_id'];
$record['company_name']=$row['company_name'];
$record['company_logo_url']=IMGADD.$row['company_logo_url'];
$record['responsdoctors_price_discount']=$row['responsdoctors_price_discount'];
$record['responsdoctors_price_additionalcoverage']=$row['responsdoctors_price_additionalcoverage'];
$record['responsdoctors_price_student']=$row['responsdoctors_price_student'];
$record['responsdoctors_price_resident']=$row['responsdoctors_price_resident'];
$record['responsdoctors_price_deactive']=$row['responsdoctors_price_deactive'];
$output[]=$record;
}
echo json_encode(array('result'=>"ok"
,"data"=>$output
,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

}
else
if ($command=="delete_responsdoctors_price")
{
$responsdoctors_price_id=$this->post('responsdoctors_price_id');

$employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','responsdoctors');
if($employeetoken[0]=='ok')
{
//************************************************************************;****************************************
$output = array();$user_id=$employeetoken[0];

$query="DELETE FROM responsdoctors_price_tb  where responsdoctors_price_id=".$responsdoctors_price_id."";
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



if ($command=="modify_responsdoctors_price")
{
$responsdoctors_price_id=$this->post('responsdoctors_price_id');

$employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','responsdoctors');
if($employeetoken[0]=='ok')
{
//*****************************************************************************************

    $query="UPDATE responsdoctors_price_tb SET ";

    if(isset($_REQUEST['responsdoctors_price_student'])){
        $responsdoctors_price_student=$this->post('responsdoctors_price_student');
        $query.="responsdoctors_price_student='".$responsdoctors_price_student."'";}

    if(isset($_REQUEST['responsdoctors_price_resident'])&&(isset($_REQUEST['responsdoctors_price_student']))){ $query.=",";}
    if(isset($_REQUEST['responsdoctors_price_resident'])){
        $responsdoctors_price_resident=$this->post('responsdoctors_price_resident');
        $query.="responsdoctors_price_resident='".$responsdoctors_price_resident."'";}


    if(isset($_REQUEST['responsdoctors_price_deactive'])&&(isset($_REQUEST['responsdoctors_price_resident'])||isset($_REQUEST['responsdoctors_price_student']))){ $query.=",";}
    if(isset($_REQUEST['responsdoctors_price_deactive'])){
        $responsdoctors_price_deactive=$this->post('responsdoctors_price_deactive');
        $query.="responsdoctors_price_deactive=".$responsdoctors_price_deactive."";}


    if(isset($_REQUEST['responsdoctors_price_additionalcoverage'])&&(isset($_REQUEST['responsdoctors_price_deactive'])||isset($_REQUEST['responsdoctors_price_resident'])||isset($_REQUEST['responsdoctors_price_student']))){ $query.=",";}
    if(isset($_REQUEST['responsdoctors_price_additionalcoverage'])){
        $responsdoctors_price_additionalcoverage=$this->post('responsdoctors_price_additionalcoverage');
        $query.="responsdoctors_price_additionalcoverage=".$responsdoctors_price_additionalcoverage."";}

    if(isset($_REQUEST['responsdoctors_price_discount'])&&(isset($_REQUEST['responsdoctors_price_additionalcoverage'])||isset($_REQUEST['responsdoctors_price_deactive'])||isset($_REQUEST['responsdoctors_price_resident'])||isset($_REQUEST['responsdoctors_price_student']))){ $query.=",";}
    if(isset($_REQUEST['responsdoctors_price_discount'])){
        $responsdoctors_price_discount=$this->post('responsdoctors_price_discount');
        $query.="responsdoctors_price_discount=".$responsdoctors_price_discount."";}

    $query.=" where responsdoctors_price_id=".$responsdoctors_price_id;

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
if ($command=="add_price_damage")
{
    $responsdoctors_price_damage_responsdoctors_price_id=$this->post('responsdoctors_price_damage_responsdoctors_price_id');
    $responsdoctors_price_damage_responsdoctors_damage_id=$this->post('responsdoctors_price_damage_responsdoctors_damage_id');
    $responsdoctors_price_damage_percent=$this->post('responsdoctors_price_damage_percent');

    $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','responsdoctors');
    if($employeetoken[0]=='ok')
    {
//****************************************************************************************************************
        $query="select * from responsdoctors_price_damage_tb where responsdoctors_price_damage_responsdoctors_damage_id=".$responsdoctors_price_damage_responsdoctors_damage_id." AND responsdoctors_price_damage_responsdoctors_price_id=".$responsdoctors_price_damage_responsdoctors_price_id."";
        $result=$this->B_db->run_query($query);
        $num=count($result[0]);
        if ($num==0)
        {
            $query="INSERT INTO responsdoctors_price_damage_tb(responsdoctors_price_damage_responsdoctors_price_id,responsdoctors_price_damage_responsdoctors_damage_id, responsdoctors_price_damage_percent)
VALUES ( $responsdoctors_price_damage_responsdoctors_price_id,$responsdoctors_price_damage_responsdoctors_damage_id, '$responsdoctors_price_damage_percent');";

            $result=$this->B_db->run_query_put($query);
            $responsdoctors_price_damage_id=$this->db->insert_id();
            echo json_encode(array('result'=>"ok"
            ,"data"=>array('responsdoctors_price_damage_id'=>$responsdoctors_price_damage_id)
            ,'desc'=>'درصد تخفیف عدم خسارت بیمه مسئولیت پزشکان اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }else{
            $responsdoctors_price_damage=$result[0];
            echo json_encode(array('result'=>"error"
            ,"data"=>array('responsdoctors_price_damage_id'=>$responsdoctors_price_damage['responsdoctors_price_damage_id'])
            ,'desc'=>' درصد تخفیف عدم خسارت بیمه مسئولیت پزشکان تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
//***************************************************************************************************************
    }else{
        echo json_encode(array('result'=>$employeetoken[0]
        ,"data"=>$employeetoken[1]
        ,'desc'=>$employeetoken[2]));

    }
}
else
    if ($command=="get_price_damage")
    {
        $responsdoctors_price_id=$this->post('responsdoctors_price_id');

//************************************************************************;****************************************

        $query="select * from responsdoctors_price_damage_tb,responsdoctors_damage_tb where responsdoctors_damage_id=responsdoctors_price_damage_responsdoctors_damage_id AND responsdoctors_price_damage_responsdoctors_price_id=$responsdoctors_price_id  ORDER BY responsdoctors_price_damage_id ASC";
        $result = $this->B_db->run_query($query);
        $output =array();
        foreach($result as $row)
        {
            $record=array();
            $record['responsdoctors_price_damage_id']=$row['responsdoctors_price_damage_id'];
            $record['responsdoctors_price_damage_responsdoctors_price_id']=$row['responsdoctors_price_damage_responsdoctors_price_id'];
            $record['responsdoctors_price_damage_responsdoctors_damage_id']=$row['responsdoctors_price_damage_responsdoctors_damage_id'];
            $record['responsdoctors_damage_name']=$row['responsdoctors_damage_name'];
            $record['responsdoctors_damage_percent']=$row['responsdoctors_damage_percent'];
            $record['responsdoctors_price_damage_percent']=$row['responsdoctors_price_damage_percent'];
            $output[]=$record;
        }
        echo json_encode(array('result'=>"ok"
        ,"data"=>$output
        ,'desc'=>'درصد تخفیف عدم خسارت بیمه مسئولیت پزشکان با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

    }
    else
        if ($command=="delete_price_damage")
        {
            $responsdoctors_price_damage_id=$this->post('responsdoctors_price_damage_id');

            $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','responsdoctors');
            if($employeetoken[0]=='ok')
            {
//************************************************************************;****************************************
                $output = array();$user_id=$employeetoken[0];

                $query="DELETE FROM responsdoctors_price_damage_tb  where responsdoctors_price_damage_id=".$responsdoctors_price_damage_id."";
                $result = $this->B_db->run_query_put($query);
                if($result){echo json_encode(array('result'=>"ok"
                ,"data"=>$output
                ,'desc'=>'درصد تخفیف عدم خسارت بیمه مسئولیت پزشکان حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    echo json_encode(array('result'=>"error"
                    ,"data"=>$output
                    ,'desc'=>'درصد تخفیف عدم خسارت بیمه مسئولیت پزشکان حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
//***************************************************************************************************************
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }

        }
        else

            if ($command=="modify_price_damage")
            {
                $responsdoctors_price_damage_id=$this->post('responsdoctors_price_damage_id');

                $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','responsdoctors');
                if($employeetoken[0]=='ok')
                {
//*****************************************************************************************

                    $query="UPDATE responsdoctors_price_damage_tb SET ";


                    if(isset($_REQUEST['responsdoctors_price_damage_percent'])){
                        $responsdoctors_price_damage_percent=$this->post('responsdoctors_price_damage_percent');
                        $query.="responsdoctors_price_damage_percent='".$responsdoctors_price_damage_percent."'";}

                    $query.="where responsdoctors_price_damage_id=".$responsdoctors_price_damage_id;

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
                if ($command=="add_price_medic_tb")
                {
                    $responsdoctors_price_medic_responsdoctors_price_id=$this->post('responsdoctors_price_medic_responsdoctors_price_id');
                    $responsdoctors_price_medic_responsdoctors_medicspecialty_id=$this->post('responsdoctors_price_medic_responsdoctors_medicspecialty_id');
                    $responsdoctors_price_medic_price=$this->post('responsdoctors_price_medic_price');
                    $responsdoctors_price_medic_aditionalcovarage=$this->post('responsdoctors_price_medic_aditionalcovarage');
                    $responsdoctors_price_medic_resident=$this->post('responsdoctors_price_medic_resident');

                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','responsdoctors');
                    if($employeetoken[0]=='ok')
                    {
//****************************************************************************************************************
                        $query="select * from responsdoctors_price_medic_tb where responsdoctors_price_medic_responsdoctors_medicspecialty_id=".$responsdoctors_price_medic_responsdoctors_medicspecialty_id." AND responsdoctors_price_medic_responsdoctors_price_id=".$responsdoctors_price_medic_responsdoctors_price_id."";
                        $result=$this->B_db->run_query($query);
                        $num=count($result[0]);
                        if ($num==0)
                        {
                            $query="INSERT INTO responsdoctors_price_medic_tb(responsdoctors_price_medic_responsdoctors_price_id,responsdoctors_price_medic_responsdoctors_medicspecialty_id, responsdoctors_price_medic_price, responsdoctors_price_medic_aditionalcovarage, responsdoctors_price_medic_resident)
VALUES ( $responsdoctors_price_medic_responsdoctors_price_id,$responsdoctors_price_medic_responsdoctors_medicspecialty_id, '$responsdoctors_price_medic_price','$responsdoctors_price_medic_aditionalcovarage', '$responsdoctors_price_medic_resident');";

                            $result=$this->B_db->run_query_put($query);
                            $responsdoctors_price_medic_id=$this->db->insert_id();
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>array('responsdoctors_price_medic_id'=>$responsdoctors_price_medic_id)
                            ,'desc'=>'قیمت تخصص پزشکی بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            $responsdoctors_price_medic=$result[0];
                            echo json_encode(array('result'=>"error"
                            ,"data"=>array('responsdoctors_price_medic_id'=>$responsdoctors_price_medic['responsdoctors_price_medic_id'])
                            ,'desc'=>' قیمت تخصص پزشکی بیمه نامه نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
//***************************************************************************************************************
                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]));

                    }
                }
                else
                    if ($command=="get_price_medic_tb")
                    {
                        $responsdoctors_price_id=$this->post('responsdoctors_price_id');

//************************************************************************;****************************************

                        $query="select * from responsdoctors_price_medic_tb,responsdoctors_medicspecialty_tb where responsdoctors_medicspecialty_id=responsdoctors_price_medic_responsdoctors_medicspecialty_id AND responsdoctors_price_medic_responsdoctors_price_id=$responsdoctors_price_id  ORDER BY responsdoctors_price_medic_id ASC";
                        $result = $this->B_db->run_query($query);
                        $output =array();
                        foreach($result as $row)
                        {
                            $record=array();
                            $record['responsdoctors_price_medic_id']=$row['responsdoctors_price_medic_id'];
                            $record['responsdoctors_price_medic_responsdoctors_price_id']=$row['responsdoctors_price_medic_responsdoctors_price_id'];
                            $record['responsdoctors_price_medic_responsdoctors_medicspecialty_id']=$row['responsdoctors_price_medic_responsdoctors_medicspecialty_id'];
                            $record['responsdoctors_medicspecialty_name']=$row['responsdoctors_medicspecialty_name'];
                            $record['responsdoctors_medicspecialty_desc']=$row['responsdoctors_medicspecialty_desc'];
                            $record['responsdoctors_price_medic_price']=$row['responsdoctors_price_medic_price'];
                            $record['responsdoctors_price_medic_aditionalcovarage']=$row['responsdoctors_price_medic_aditionalcovarage'];
                            $record['responsdoctors_price_medic_resident']=$row['responsdoctors_price_medic_resident'];
                            $output[]=$record;
                        }
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>$output
                        ,'desc'=>'قیمت تخصص پزشکی بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                    }
                    else
                        if ($command=="delete_price_medic_tb")
                        {
                            $responsdoctors_price_medic_id=$this->post('responsdoctors_price_medic_id');

                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','responsdoctors');
                            if($employeetoken[0]=='ok')
                            {
//************************************************************************;****************************************
                                $output = array();$user_id=$employeetoken[0];

                                $query="DELETE FROM responsdoctors_price_medic_tb  where responsdoctors_price_medic_id=".$responsdoctors_price_medic_id."";
                                $result = $this->B_db->run_query_put($query);
                                if($result){echo json_encode(array('result'=>"ok"
                                ,"data"=>$output
                                ,'desc'=>'قیمت تخصص پزشکی بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }else{
                                    echo json_encode(array('result'=>"error"
                                    ,"data"=>$output
                                    ,'desc'=>'قیمت تخصص پزشکی بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }
//***************************************************************************************************************
                            }else{
                                echo json_encode(array('result'=>$employeetoken[0]
                                ,"data"=>$employeetoken[1]
                                ,'desc'=>$employeetoken[2]));

                            }

                        }
                        else



                            if ($command=="modify_price_medic_tb")
                            {
                                $responsdoctors_price_medic_id=$this->post('responsdoctors_price_medic_id');

                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','responsdoctors');
                                if($employeetoken[0]=='ok')
                                {
//*****************************************************************************************

                                    $query="UPDATE responsdoctors_price_medic_tb SET ";


                                    if(isset($_REQUEST['responsdoctors_price_medic_price'])){
                                        $responsdoctors_price_medic_price=$this->post('responsdoctors_price_medic_price');
                                        $query.="responsdoctors_price_medic_price='".$responsdoctors_price_medic_price."'";}

                                    if(isset($_REQUEST['responsdoctors_price_medic_resident'])&&(isset($_REQUEST['responsdoctors_price_medic_price']))){ $query.=",";}
                                    if(isset($_REQUEST['responsdoctors_price_medic_resident'])){
                                        $responsdoctors_price_medic_resident=$this->post('responsdoctors_price_medic_resident');
                                        $query.="responsdoctors_price_medic_resident='".$responsdoctors_price_medic_resident."'";}

                                    if(isset($_REQUEST['responsdoctors_price_medic_aditionalcovarage'])&&(isset($_REQUEST['responsdoctors_price_medic_resident'])||isset($_REQUEST['responsdoctors_price_medic_price']))){ $query.=",";}
                                    if(isset($_REQUEST['responsdoctors_price_medic_aditionalcovarage'])){
                                        $responsdoctors_price_medic_aditionalcovarage=$this->post('responsdoctors_price_medic_aditionalcovarage');
                                        $query.="responsdoctors_price_medic_aditionalcovarage='".$responsdoctors_price_medic_aditionalcovarage."'";}


                                    $query.=" where responsdoctors_price_medic_id=".$responsdoctors_price_medic_id;

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
                                if ($command=="add_price_paramedic_tb")
                                {
                                    $responsdoctors_price_paramedic_responsdoctors_price_id=$this->post('responsdoctors_price_paramedic_responsdoctors_price_id');
                                    $responsdoctors_price_paramedic_responsdoctors_paramedic_id=$this->post('responsdoctors_price_paramedic_responsdoctors_paramedic_id');
                                    $responsdoctors_price_paramedic_price=$this->post('responsdoctors_price_paramedic_price');
                                    $responsdoctors_price_paramedic_aditionalcovarage=$this->post('responsdoctors_price_paramedic_aditionalcovarage');
                                    $responsdoctors_price_paramedic_student=$this->post('responsdoctors_price_paramedic_student');

                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','responsdoctors');
                                    if($employeetoken[0]=='ok')
                                    {
//****************************************************************************************************************
                                        $query="select * from responsdoctors_price_paramedic_tb where responsdoctors_price_paramedic_responsdoctors_paramedic_id=".$responsdoctors_price_paramedic_responsdoctors_paramedic_id." AND responsdoctors_price_paramedic_responsdoctors_price_id=".$responsdoctors_price_paramedic_responsdoctors_price_id."";
                                        $result=$this->B_db->run_query($query);
                                        $num=count($result[0]);
                                        if ($num==0)
                                        {
                                            $query="INSERT INTO responsdoctors_price_paramedic_tb(responsdoctors_price_paramedic_responsdoctors_price_id,responsdoctors_price_paramedic_responsdoctors_paramedic_id, responsdoctors_price_paramedic_price, responsdoctors_price_paramedic_aditionalcovarage, responsdoctors_price_paramedic_student)
VALUES ( $responsdoctors_price_paramedic_responsdoctors_price_id,$responsdoctors_price_paramedic_responsdoctors_paramedic_id, '$responsdoctors_price_paramedic_price','$responsdoctors_price_paramedic_aditionalcovarage', '$responsdoctors_price_paramedic_student');";

                                            $result=$this->B_db->run_query_put($query);
                                            $responsdoctors_price_paramedic_id=$this->db->insert_id();
                                            echo json_encode(array('result'=>"ok"
                                            ,"data"=>array('responsdoctors_price_paramedic_id'=>$responsdoctors_price_paramedic_id)
                                            ,'desc'=>'قیمت تخصص پیرا پزشکی بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                        }else{
                                            $responsdoctors_price_paramedic=$result[0];
                                            echo json_encode(array('result'=>"error"
                                            ,"data"=>array('responsdoctors_price_paramedic_id'=>$responsdoctors_price_paramedic['responsdoctors_price_paramedic_id'])
                                            ,'desc'=>' قیمت تخصص پیرا پزشکی بیمه نامه نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                        }
//***************************************************************************************************************
                                    }else{
                                        echo json_encode(array('result'=>$employeetoken[0]
                                        ,"data"=>$employeetoken[1]
                                        ,'desc'=>$employeetoken[2]));

                                    }
                                }
                                else
                                    if ($command=="get_price_paramedic_tb")
                                    {
                                        $responsdoctors_price_id=$this->post('responsdoctors_price_id');

//************************************************************************;****************************************

                                        $query="select * from responsdoctors_price_paramedic_tb,responsdoctors_paramedicspecialty_tb where responsdoctors_paramedicspecialty_id=responsdoctors_price_paramedic_responsdoctors_paramedic_id AND responsdoctors_price_paramedic_responsdoctors_price_id=$responsdoctors_price_id  ORDER BY responsdoctors_price_paramedic_id ASC";
                                        $result = $this->B_db->run_query($query);
                                        $output =array();
                                        foreach($result as $row)
                                        {
                                            $record=array();
                                            $record['responsdoctors_price_paramedic_id']=$row['responsdoctors_price_paramedic_id'];
                                            $record['responsdoctors_price_paramedic_responsdoctors_price_id']=$row['responsdoctors_price_paramedic_responsdoctors_price_id'];
                                            $record['responsdoctors_price_paramedic_responsdoctors_paramedic_id']=$row['responsdoctors_price_paramedic_responsdoctors_paramedic_id'];
                                            $record['responsdoctors_paramedicspecialty_name']=$row['responsdoctors_paramedicspecialty_name'];
                                            $record['responsdoctors_paramedicspecialty_desc']=$row['responsdoctors_paramedicspecialty_desc'];
                                            $record['responsdoctors_price_paramedic_price']=$row['responsdoctors_price_paramedic_price'];
                                            $record['responsdoctors_price_paramedic_aditionalcovarage']=$row['responsdoctors_price_paramedic_aditionalcovarage'];
                                            $record['responsdoctors_price_paramedic_student']=$row['responsdoctors_price_paramedic_student'];
                                            $output[]=$record;
                                        }
                                        echo json_encode(array('result'=>"ok"
                                        ,"data"=>$output
                                        ,'desc'=>'قیمت تخصص پیرا پزشکی بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                    }
                                    else
                                        if ($command=="delete_price_paramedic_tb")
                                        {
                                            $responsdoctors_price_paramedic_id=$this->post('responsdoctors_price_paramedic_id');

                                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','responsdoctors');
                                            if($employeetoken[0]=='ok')
                                            {
//************************************************************************;****************************************
                                                $output = array();$user_id=$employeetoken[0];

                                                $query="DELETE FROM responsdoctors_price_paramedic_tb  where responsdoctors_price_paramedic_id=".$responsdoctors_price_paramedic_id."";
                                                $result = $this->B_db->run_query_put($query);
                                                if($result){echo json_encode(array('result'=>"ok"
                                                ,"data"=>$output
                                                ,'desc'=>'قیمت تخصص پیرا پزشکی بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                }else{
                                                    echo json_encode(array('result'=>"error"
                                                    ,"data"=>$output
                                                    ,'desc'=>'قیمت تخصص پیرا پزشکی بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                }
//***************************************************************************************************************
                                            }else{
                                                echo json_encode(array('result'=>$employeetoken[0]
                                                ,"data"=>$employeetoken[1]
                                                ,'desc'=>$employeetoken[2]));

                                            }

                                        }
                                        else



                                            if ($command=="modify_price_paramedic_tb")
                                            {
                                                $responsdoctors_price_paramedic_id=$this->post('responsdoctors_price_paramedic_id');

                                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','responsdoctors');
                                                if($employeetoken[0]=='ok')
                                                {
//*****************************************************************************************

                                                    $query="UPDATE responsdoctors_price_paramedic_tb SET ";


                                                    if(isset($_REQUEST['responsdoctors_price_paramedic_price'])){
                                                        $responsdoctors_price_paramedic_price=$this->post('responsdoctors_price_paramedic_price');
                                                        $query.="responsdoctors_price_paramedic_price='".$responsdoctors_price_paramedic_price."'";}

                                                    if(isset($_REQUEST['responsdoctors_price_paramedic_student'])&&(isset($_REQUEST['responsdoctors_price_paramedic_price']))){ $query.=",";}
                                                    if(isset($_REQUEST['responsdoctors_price_paramedic_student'])){
                                                        $responsdoctors_price_paramedic_student=$this->post('responsdoctors_price_paramedic_student');
                                                        $query.="responsdoctors_price_paramedic_student='".$responsdoctors_price_paramedic_student."'";}

                                                    if(isset($_REQUEST['responsdoctors_price_paramedic_aditionalcovarage'])&&(isset($_REQUEST['responsdoctors_price_paramedic_student'])||isset($_REQUEST['responsdoctors_price_paramedic_price']))){ $query.=",";}
                                                    if(isset($_REQUEST['responsdoctors_price_paramedic_aditionalcovarage'])){
                                                        $responsdoctors_price_paramedic_aditionalcovarage=$this->post('responsdoctors_price_paramedic_aditionalcovarage');
                                                        $query.="responsdoctors_price_paramedic_aditionalcovarage='".$responsdoctors_price_paramedic_aditionalcovarage."'";}


                                                    $query.=" where responsdoctors_price_paramedic_id=".$responsdoctors_price_paramedic_id;

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