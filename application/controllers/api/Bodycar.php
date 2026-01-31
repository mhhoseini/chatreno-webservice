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
 * @link            https://aref24.com
 */
class Bodycar extends REST_Controller {

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
        $this->load->model('B_bodycar');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('bodycar', $command, get_client_ip(),50,50)) {
        if ($command=="add_discnt_bodycar")
        {
            $bodycar_discnt_id=$this->post('bodycar_discnt_id');
            $bodycar_discnt_name=$this->post('bodycar_discnt_name');
            $bodycar_discnt_digt=$this->post('bodycar_discnt_digt');
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
            if($employeetoken[0]=='ok')
            {
                $result=$this->B_bodycar->get_bodycar_discnt_by($bodycar_discnt_name, $bodycar_discnt_id);
                if(empty($result))
                {
                    $bodycar_discnt_id=$this->B_bodycar->add_bodycar($bodycar_discnt_id, $bodycar_discnt_name,$bodycar_discnt_digt);
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('bodycar_discnt_id'=>$bodycar_discnt_id)
                    ,'desc'=>'تخفیف بیمه بدنه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    $carmode=$result[0];
                    echo json_encode(array('result'=>"error"
                    ,"data"=>array('bodycar_discnt_id'=>$carmode['bodycar_discnt_id'])
                    ,'desc'=>'تخفیف بیمه بدنه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }
        }
        if ($command=="get_discnt_bodycar")
        {
            $result = $this->B_bodycar->all_bodycar_discnt();
            $output =array();
            foreach($result as $row)
            {
                $record=array();
                $record['bodycar_discnt_id']=$row['bodycar_discnt_id'];
                $record['bodycar_discnt_name']=$row['bodycar_discnt_name'];
                $record['bodycar_discnt_digt']=$row['bodycar_discnt_digt'];
                $output[]=$record;
            }
            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'تخفیف بیمه بدنه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
        else
    if ($command=="delete_discnt_bodycar")
    {
    $bodycar_discnt_id=$this->post('bodycar_discnt_id');
    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
    if($employeetoken[0]=='ok')
    {
        $result = $this->B_bodycar->del_bodycar($bodycar_discnt_id);
        $output =array();
        if($result){echo json_encode(array('result'=>"ok"
        ,"data"=>$output
        ,'desc'=>'تخفیف بیمه بدنه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }else{
        echo json_encode(array('result'=>"error"
        ,"data"=>$output
        ,'desc'=>'تخفیف بیمه بدنه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
        }else{
        echo json_encode(array('result'=>$employeetoken[0]
        ,"data"=>$employeetoken[1]
        ,'desc'=>$employeetoken[2]));
    }
    }
    else
    if ($command=="modify_discnt_bodycar")
    {
        $bodycar_discnt_id=$this->post('bodycar_discnt_id');
        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','bodycar');
        if($employeetoken[0]=='ok')
        {
            $query="UPDATE bodycar_discnt_tb SET ";
            if(isset($_REQUEST['bodycar_discnt_name'])){
            $bodycar_discnt_name=$this->post('bodycar_discnt_name');
            $query.="bodycar_discnt_name='".$bodycar_discnt_name."'";
            }
            if(isset($_REQUEST['bodycar_discnt_digt'])&&(isset($_REQUEST['bodycar_discnt_name']))){ $query.=",";}
            if(isset($_REQUEST['bodycar_discnt_digt'])){
            $bodycar_discnt_digt=$this->post('bodycar_discnt_digt');
            $query.="bodycar_discnt_digt='".$bodycar_discnt_digt."'";}
            $query.="where bodycar_discnt_id=".$bodycar_discnt_id;
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
    if ($command=="add_discnt_thirdparty")
    {
        $bodycar_discnt_thirdparty_id=$this->post('bodycar_discnt_thirdparty_id');
        $bodycar_discnt_thirdparty_name=$this->post('bodycar_discnt_thirdparty_name');
        $bodycar_discnt_thirdparty_digt=$this->post('bodycar_discnt_thirdparty_digt');
        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
    if($employeetoken[0]=='ok')
    {
        $result=$this->B_bodycar->get_bodycar_thirdparty($bodycar_discnt_thirdparty_id, $bodycar_discnt_thirdparty_name);
    if(empty($result))
    {
         $bodycar_discnt_thirdparty_id=$this->B_bodycar->add_bodycar_thirdparty($bodycar_discnt_thirdparty_id,$bodycar_discnt_thirdparty_name, $bodycar_discnt_thirdparty_digt);
         echo json_encode(array('result'=>"ok"
        ,"data"=>array('bodycar_discnt_thirdparty_id'=>$bodycar_discnt_thirdparty_id)
        ,'desc'=>'تخفیف راننده بیمه بدنه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }else{
         $carmode=$result[0];
         echo json_encode(array('result'=>"error"
        ,"data"=>array('bodycar_discnt_thirdparty_id'=>$carmode['bodycar_discnt_thirdparty_id'])
        ,'desc'=>'تخفیف راننده بیمه بدنه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    }else{
        echo json_encode(array('result'=>$employeetoken[0]
        ,"data"=>$employeetoken[1]
        ,'desc'=>$employeetoken[2]));
    }
    }
    else
    if ($command=="get_discnt_thirdparty")
    {

        $result = $this->B_bodycar->all_bodycar_thirdparty();
        $output =array();
        foreach($result as $row)
        {
        $record=array();
        $record['bodycar_discnt_thirdparty_id']=$row['bodycar_discnt_thirdparty_id'];
        $record['bodycar_discnt_thirdparty_name']=$row['bodycar_discnt_thirdparty_name'];
        $record['bodycar_discnt_thirdparty_digt']=$row['bodycar_discnt_thirdparty_digt'];
        $output[]=$record;
        }
        echo json_encode(array('result'=>"ok"
        ,"data"=>$output
        ,'desc'=>'تخفیف راننده بیمه بدنه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    else
    if ($command=="delete_discnt_thirdparty")
    {
        $bodycar_discnt_thirdparty_id=$this->post('bodycar_discnt_thirdparty_id');
        $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
    if($employeetoken[0]=='ok')
    {
        $output = array();
        $result = $this->B_bodycar->del_bodycar_thirdparty($bodycar_discnt_thirdparty_id);
        if($result){echo json_encode(array('result'=>"ok"
        ,"data"=>$output
        ,'desc'=>'تخفیف راننده بیمه بدنه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }else{
        echo json_encode(array('result'=>"error"
        ,"data"=>$output
        ,'desc'=>'تخفیف راننده بیمه بدنه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
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
    $bodycar_discnt_thirdparty_id=$this->post('bodycar_discnt_thirdparty_id');
    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','bodycar');
    if($employeetoken[0]=='ok')
    {
        $query="UPDATE bodycar_discnt_thirdparty_tb SET ";
            if(isset($_REQUEST['bodycar_discnt_thirdparty_name'])){
        $bodycar_discnt_thirdparty_name=$this->post('bodycar_discnt_thirdparty_name');
        $query.="bodycar_discnt_thirdparty_name='".$bodycar_discnt_thirdparty_name."'";
        }
        if(isset($_REQUEST['bodycar_discnt_thirdparty_digt'])&&(isset($_REQUEST['bodycar_discnt_thirdparty_name']))){ $query.=",";}
        if(isset($_REQUEST['bodycar_discnt_thirdparty_digt'])){
        $bodycar_discnt_thirdparty_digt=$this->post('bodycar_discnt_thirdparty_digt');
        $query.="bodycar_discnt_thirdparty_digt='".$bodycar_discnt_thirdparty_digt."'";
        }
        $query.="where bodycar_discnt_thirdparty_id=".$bodycar_discnt_thirdparty_id;
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
    if ($command=="add_bodycar_discntlife")
    {
        $bodycar_discnt_life_id=$this->post('bodycar_discnt_life_id');
        $bodycar_discnt_life_company_name=$this->post('bodycar_discnt_life_company_name');
        $bodycar_discnt_life_percent=$this->post('bodycar_discnt_life_percent');
        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
    if($employeetoken[0]=='ok')
    {
    $result=$this->B_bodycar->get_bodycar_discnt_life_by($bodycar_discnt_life_id , $bodycar_discnt_life_company_name);
    $num=count($result[0]);
    if($num==0)
    {
        $bodycar_discnt_life_id=$this->B_bodycar->add_bodycar_thirdparty_percent($bodycar_discnt_life_id,$bodycar_discnt_life_company_name, $bodycar_discnt_life_percent);
        echo json_encode(array('result'=>"ok"
        ,"data"=>array('bodycar_discnt_life_id'=>$bodycar_discnt_life_id)
        ,'desc'=>'خسارت بدنی بیمه بدنه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }else{
        $carmode=$result[0];
        echo json_encode(array('result'=>"error"
        ,"data"=>array('bodycar_discnt_life_id'=>$carmode['bodycar_discnt_life_id'])
        ,'desc'=>'خسارت بدنی بیمه بدنه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
        }else{
        echo json_encode(array('result'=>$employeetoken[0]
        ,"data"=>$employeetoken[1]
        ,'desc'=>$employeetoken[2]));
    }
    }
    else
    if ($command=="get_bodycar_discntlife")
    {
        $result = $this->B_bodycar->all_bodycar_discnt_life();
        $output =array();
        foreach($result as $row)
        {
            $record=array();
            $record['bodycar_discnt_life_id']=$row['bodycar_discnt_life_id'];
            $record['bodycar_discnt_life_company_name']=$row['bodycar_discnt_life_company_name'];
            $record['bodycar_discnt_life_percent']=$row['bodycar_discnt_life_percent'];
            $output[]=$record;
        }
        echo json_encode(array('result'=>"ok"
        ,"data"=>$output
        ,'desc'=>'خسارت بدنی بیمه بدنه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    else
    if ($command=="delete_bodycar_discntlife")
    {
        $bodycar_discnt_life_id=$this->post('bodycar_discnt_life_id');
        $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
    if($employeetoken[0]=='ok')
    {
    $output = array();
    $result = $this->B_bodycar->del_bodycar_discnt_life($bodycar_discnt_life_id);
    if($result){echo json_encode(array('result'=>"ok"
    ,"data"=>$output
    ,'desc'=>'خسارت بدنی بیمه بدنه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }else{
    echo json_encode(array('result'=>"error"
    ,"data"=>$output
    ,'desc'=>'خسارت بدنی بیمه بدنه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    }else{
    echo json_encode(array('result'=>$employeetoken[0]
    ,"data"=>$employeetoken[1]
    ,'desc'=>$employeetoken[2]));
    }
    }
    else
    if ($command=="modify_bodycar_discntlife")
    {
        $bodycar_discnt_life_id=$this->post('bodycar_discnt_life_id');
        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','bodycar');
        if($employeetoken[0]=='ok')
        {
        $query="UPDATE bodycar_discnt_life_tb SET ";

        if(isset($_REQUEST['bodycar_discnt_life_company_name'])){
        $bodycar_discnt_life_company_name=$this->post('bodycar_discnt_life_company_name');
        $query.="bodycar_discnt_life_company_name='".$bodycar_discnt_life_company_name."'";
        }

        if(isset($_REQUEST['bodycar_discnt_life_percent'])&&(isset($_REQUEST['bodycar_discnt_life_company_name']))){ $query.=",";}
        if(isset($_REQUEST['bodycar_discnt_life_percent'])){
        $bodycar_discnt_life_percent=$this->post('bodycar_discnt_life_percent');
        $query.="bodycar_discnt_life_percent='".$bodycar_discnt_life_percent."'";
        }

        $query.="where bodycar_discnt_life_id=".$bodycar_discnt_life_id;
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
    if ($command=="add_discnt_accbank")
    {
        $bodycar_discnt_accbank_id=$this->post('bodycar_discnt_accbank_id');
        $bodycar_discnt_accbank_name=$this->post('bodycar_discnt_accbank_name');
        $bodycar_discnt_accbank_percent=$this->post('bodycar_discnt_accbank_percent');
        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
        if($employeetoken[0]=='ok')
        {
        $result=$this->B_bodycar->get_bodycar_discnt_accbank_by($bodycar_discnt_accbank_name);
            if(empty($result))
        {
        $bodycar_discnt_accbank_id=$this->B_bodycar->add_bodycar_discnt_accbank($bodycar_discnt_accbank_id,$bodycar_discnt_accbank_name, $bodycar_discnt_accbank_percent);
        echo json_encode(array('result'=>"ok"
        ,"data"=>array('bodycar_discnt_accbank_id'=>$bodycar_discnt_accbank_id)
        ,'desc'=>'خسارت بدنی بیمه بدنه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }else{
        $carmode=$result[0];
        echo json_encode(array('result'=>"error"
        ,"data"=>array('bodycar_discnt_accbank_id'=>$carmode['bodycar_discnt_accbank_id'])
        ,'desc'=>'خسارت بدنی بیمه بدنه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
        }else{
        echo json_encode(array('result'=>$employeetoken[0]
        ,"data"=>$employeetoken[1]
        ,'desc'=>$employeetoken[2]));
        
        }
    }
    else
    if ($command=="get_discnt_accbank")
    {
        $result = $this->B_bodycar->all_bodycar_discnt_accbank();
        $output =array();
        foreach($result as $row)
        {
        $record=array();
        $record['bodycar_discnt_accbank_id']=$row['bodycar_discnt_accbank_id'];
        $record['bodycar_discnt_accbank_name']=$row['bodycar_discnt_accbank_name'];
        $record['bodycar_discnt_accbank_percent']=$row['bodycar_discnt_accbank_percent'];
        $output[]=$record;
        }
        echo json_encode(array('result'=>"ok"
        ,"data"=>$output
        ,'desc'=>'خسارت بدنی بیمه بدنه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    else
    if ($command=="delete_discnt_accbank")
    {
        $bodycar_discnt_accbank_id=$this->post('bodycar_discnt_accbank_id');
        $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
        if($employeetoken[0]=='ok')
        {
        $output =array();
        $result = $this->B_bodycar->del_bodycar_discnt_accbank($bodycar_discnt_accbank_id);
        if($result){echo json_encode(array('result'=>"ok"
        ,"data"=>$output
        ,'desc'=>'خسارت بدنی بیمه بدنه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }else{
        echo json_encode(array('result'=>"error"
        ,"data"=>$output
        ,'desc'=>'خسارت بدنی بیمه بدنه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
        }else{
        echo json_encode(array('result'=>$employeetoken[0]
        ,"data"=>$employeetoken[1]
        ,'desc'=>$employeetoken[2]));
        
        }
    }
    else
    if ($command=="modify_discnt_accbank")
    {
    $bodycar_discnt_accbank_id=$this->post('bodycar_discnt_accbank_id');
    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','bodycar');
    if($employeetoken[0]=='ok')
    {
        $query="UPDATE bodycar_discnt_accbank_tb SET ";

        if(isset($_REQUEST['bodycar_discnt_accbank_name'])){
        $bodycar_discnt_accbank_name=$this->post('bodycar_discnt_accbank_name');
        $query.="bodycar_discnt_accbank_name='".$bodycar_discnt_accbank_name."'";
        }

        if(isset($_REQUEST['bodycar_discnt_accbank_percent'])&&(isset($_REQUEST['bodycar_discnt_accbank_name']))){ $query.=",";}
        if(isset($_REQUEST['bodycar_discnt_accbank_percent'])){
        $bodycar_discnt_accbank_percent=$this->post('bodycar_discnt_accbank_percent');
        $query.="bodycar_discnt_accbank_percent='".$_REQUEST['bodycar_discnt_accbank_percent']."'";
        }

        $query.="where bodycar_discnt_accbank_id=".$bodycar_discnt_accbank_id;
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
        if ($command=="add_discnt_another")
        {
            $bodycar_discnt_another_id=$this->post('bodycar_discnt_another_id');
            $bodycar_discnt_another_name=$this->post('bodycar_discnt_another_name');
            $bodycar_discnt_another_percent=$this->post('bodycar_discnt_another_percent');



            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
            if($employeetoken[0]=='ok')
            {
//****************************************************************************************************************
                $query="select * from bodycar_discnt_another_tb where bodycar_discnt_another_name='".$bodycar_discnt_another_name."'";
                $result=$this->B_db->run_query($query);
                if(empty($result))
                {
                    $query="INSERT INTO bodycar_discnt_another_tb(bodycar_discnt_another_id, bodycar_discnt_another_name, bodycar_discnt_another_percent)
	                            VALUES ( $bodycar_discnt_another_id,'$bodycar_discnt_another_name', '$bodycar_discnt_another_percent');";
                    
                    $result=$this->B_db->run_query_put($query);
                    $bodycar_discnt_another_id=$this->db->insert_id();

                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('bodycar_discnt_another_id'=>$bodycar_discnt_another_id)
                    ,'desc'=>'خسارت بدنی بیمه بدنه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    $carmode=$result[0];
                    echo json_encode(array('result'=>"error"
                    ,"data"=>array('bodycar_discnt_another_id'=>$carmode['bodycar_discnt_another_id'])
                    ,'desc'=>'خسارت بدنی بیمه بدنه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
//***************************************************************************************************************
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }
        }
        else
            if ($command=="get_discnt_another")
            {
//************************************************************************;****************************************
                $query="select * from bodycar_discnt_another_tb where 1 ORDER BY bodycar_discnt_another_id ASC";
                $result = $this->B_db->run_query($query);
                $output =array();
                foreach($result as $row)
                {
                    $record=array();
                    $record['bodycar_discnt_another_id']=$row['bodycar_discnt_another_id'];
                    $record['bodycar_discnt_another_name']=$row['bodycar_discnt_another_name'];
                    $record['bodycar_discnt_another_percent']=$row['bodycar_discnt_another_percent'];
                    $output[]=$record;
                }
                echo json_encode(array('result'=>"ok"
                ,"data"=>$output
                ,'desc'=>'خسارت بدنی بیمه بدنه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************
                
            }
            else
                if ($command=="delete_discnt_another")
                {
                    $bodycar_discnt_another_id=$this->post('bodycar_discnt_another_id');

                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
                    $output = array();
                    if($employeetoken[0]=='ok')
                    {
//************************************************************************;****************************************
                        $user_id=$employeetoken[1];
                        
                        $query="DELETE FROM bodycar_discnt_another_tb  where bodycar_discnt_another_id=".$bodycar_discnt_another_id."";
                        $result = $this->B_db->run_query_put($query);
                        if($result){echo json_encode(array('result'=>"ok"
                        ,"data"=>$output
                        ,'desc'=>'خسارت بدنی بیمه بدنه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            echo json_encode(array('result'=>"error"
                            ,"data"=>$output
                            ,'desc'=>'خسارت بدنی بیمه بدنه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
//***************************************************************************************************************
                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]));

                    }
                    
                }
                else

                    if ($command=="modify_discnt_another")
                    {
                        $bodycar_discnt_another_id=$this->post('bodycar_discnt_another_id');

                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','bodycar');
                        if($employeetoken[0]=='ok')
                        {
//*****************************************************************************************

                            $query="UPDATE bodycar_discnt_another_tb SET ";

                            if(isset($_REQUEST['bodycar_discnt_another_name'])){
                                $bodycar_discnt_another_name=$this->post('bodycar_discnt_another_name');
                                $query.="bodycar_discnt_another_name='".$bodycar_discnt_another_name."'";
                            }

                            if(isset($_REQUEST['bodycar_discnt_another_percent'])&&(isset($_REQUEST['bodycar_discnt_another_name']))){ $query.=",";}
                            if(isset($_REQUEST['bodycar_discnt_another_percent'])){
                                $bodycar_discnt_another_percent=$this->post('bodycar_discnt_another_percent');
                                $query.="bodycar_discnt_another_percent='".$_REQUEST['bodycar_discnt_another_percent']."'";
                            }

                            $query.="where bodycar_discnt_another_id=".$bodycar_discnt_another_id;
                            
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
                        if ($command=="add_discnt_priority")
                        {
                            $bodycar_discnt_priority_id=$this->post('bodycar_discnt_priority_id');
                            $bodycar_discnt_priority_name=$this->post('bodycar_discnt_priority_name');
                            $bodycar_discnt_priority_percent=$this->post('bodycar_discnt_priority_percent');



                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
                            if($employeetoken[0]=='ok')
                            {
//****************************************************************************************************************
                                $query="select * from bodycar_discnt_priority_tb where bodycar_discnt_priority_name='".$bodycar_discnt_priority_name."'";
                                $result=$this->B_db->run_query($query);
                                if(empty($result))
                                {
                                    $query="INSERT INTO bodycar_discnt_priority_tb(bodycar_discnt_priority_id, bodycar_discnt_priority_name, bodycar_discnt_priority_percent)
	                            VALUES ( $bodycar_discnt_priority_id,'$bodycar_discnt_priority_name', '$bodycar_discnt_priority_percent');";
                                    
                                    $result=$this->B_db->run_query_put($query);
                                    $bodycar_discnt_priority_id=$this->db->insert_id();

                                    echo json_encode(array('result'=>"ok"
                                    ,"data"=>array('bodycar_discnt_priority_id'=>$bodycar_discnt_priority_id)
                                    ,'desc'=>'خسارت بدنی بیمه بدنه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }else{
                                    $carmode=$result[0];
                                    echo json_encode(array('result'=>"error"
                                    ,"data"=>array('bodycar_discnt_priority_id'=>$carmode['bodycar_discnt_priority_id'])
                                    ,'desc'=>'خسارت بدنی بیمه بدنه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }
//***************************************************************************************************************
                            }else{
                                echo json_encode(array('result'=>$employeetoken[0]
                                ,"data"=>$employeetoken[1]
                                ,'desc'=>$employeetoken[2]));

                            }

                        }
                        else
                            if ($command=="get_discnt_priority")
                            {
//************************************************************************;****************************************
                                
                                $query="select * from bodycar_discnt_priority_tb where 1 ORDER BY bodycar_discnt_priority_id ASC";
                                $result = $this->B_db->run_query($query);
                                $output =array();
                                foreach($result as $row)
                                {
                                    $record=array();
                                    $record['bodycar_discnt_priority_id']=$row['bodycar_discnt_priority_id'];
                                    $record['bodycar_discnt_priority_name']=$row['bodycar_discnt_priority_name'];
                                    $record['bodycar_discnt_priority_percent']=$row['bodycar_discnt_priority_percent'];
                                    $output[]=$record;
                                }
                                echo json_encode(array('result'=>"ok"
                                ,"data"=>$output
                                ,'desc'=>'خسارت بدنی بیمه بدنه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************
                                
                            }
                            else
                                if ($command=="delete_discnt_priority")
                                {
                                    $bodycar_discnt_priority_id=$this->post('bodycar_discnt_priority_id');

                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
                                    $output = array();
                                    if($employeetoken[0]=='ok')
                                    {
//************************************************************************;****************************************
                                        $user_id=$employeetoken[1];
                                        $query="DELETE FROM bodycar_discnt_priority_tb  where bodycar_discnt_priority_id=".$bodycar_discnt_priority_id."";
                                        $result = $this->B_db->run_query_put($query);
                                        if($result){echo json_encode(array('result'=>"ok"
                                        ,"data"=>$output
                                        ,'desc'=>'خسارت بدنی بیمه بدنه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                        }else{
                                            echo json_encode(array('result'=>"error"
                                            ,"data"=>$output
                                            ,'desc'=>'خسارت بدنی بیمه بدنه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                        }
//***************************************************************************************************************
                                    }else{
                                        echo json_encode(array('result'=>$employeetoken[0]
                                        ,"data"=>$employeetoken[1]
                                        ,'desc'=>$employeetoken[2]));

                                    }
                                    
                                }
                                else
                                    if ($command=="modify_discnt_priority")
                                    {
                                        $bodycar_discnt_priority_id=$this->post('bodycar_discnt_priority_id');

                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','bodycar');
                                        if($employeetoken[0]=='ok')
                                        {
//*****************************************************************************************

                                            $query="UPDATE bodycar_discnt_priority_tb SET ";

                                            if(isset($_REQUEST['bodycar_discnt_priority_name'])){
                                                $bodycar_discnt_priority_name=$this->post('bodycar_discnt_priority_name');
                                                $query.="bodycar_discnt_priority_name='".$bodycar_discnt_priority_name."'";
                                            }

                                            if(isset($_REQUEST['bodycar_discnt_priority_percent'])&&(isset($_REQUEST['bodycar_discnt_priority_name']))){ $query.=",";}
                                            if(isset($_REQUEST['bodycar_discnt_priority_percent'])){
                                                $bodycar_discnt_priority_percent=$this->post('bodycar_discnt_priority_percent');
                                                $query.="bodycar_discnt_priority_percent='".$_REQUEST['bodycar_discnt_priority_percent']."'";
                                            }

                                            $query.="where bodycar_discnt_priority_id=".$bodycar_discnt_priority_id;
                                            
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
     if ($command=="add_damage_driver")
    {
        $bodycar_damage_driver_id=$this->post('bodycar_damage_driver_id');
        $bodycar_damage_driver_name=$this->post('bodycar_damage_driver_name');
        $bodycar_damage_driver_digit=$this->post('bodycar_damage_driver_digit');
        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
        if($employeetoken[0]=='ok')
        {
        $result=$this->B_bodycar->get_bodycar_damage_driver_by($bodycar_damage_driver_name);
            if(empty($result))
        {
        $bodycar_damage_driver_id=$this->B_bodycar->add_bodycar_damage_driver($bodycar_damage_driver_id,$bodycar_damage_driver_name, $bodycar_damage_driver_digit);
        echo json_encode(array('result'=>"ok"
        ,"data"=>array('bodycar_damage_driver_id'=>$bodycar_damage_driver_id)
        ,'desc'=>'خسارت بدنی بیمه بدنه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }else{
        $carmode=$result[0];
        echo json_encode(array('result'=>"error"
        ,"data"=>array('bodycar_damage_driver_id'=>$carmode['bodycar_damage_driver_id'])
        ,'desc'=>'خسارت بدنی بیمه بدنه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
        }else{
        echo json_encode(array('result'=>$employeetoken[0]
        ,"data"=>$employeetoken[1]
        ,'desc'=>$employeetoken[2]));

        }
    }
    else
    if ($command=="get_damage_driver")
    {
    $result = $this->B_bodycar->all_bodycar_damage_driver();
    $output =array();
    foreach($result as $row)
    {
        $record=array();
        $record['bodycar_damage_driver_id']=$row['bodycar_damage_driver_id'];
        $record['bodycar_damage_driver_name']=$row['bodycar_damage_driver_name'];
        $record['bodycar_damage_driver_digit']=$row['bodycar_damage_driver_digit'];
        $output[]=$record;
    }
    echo json_encode(array('result'=>"ok"
    ,"data"=>$output
    ,'desc'=>'خسارت راننده بیمه بدنه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    else
    if ($command=="delete_damage_driver")
    {
        $bodycar_damage_driver_id=$this->post('bodycar_damage_driver_id');
        $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
        if($employeetoken[0]=='ok')
        {
        $output =array();
        $result = $this->B_bodycar->del_bodycar_damage_driver($bodycar_damage_driver_id);
        if($result){echo json_encode(array('result'=>"ok"
        ,"data"=>$output
        ,'desc'=>'خسارت راننده بیمه بدنه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }else{
        echo json_encode(array('result'=>"error"
        ,"data"=>$output
        ,'desc'=>'خسارت راننده بیمه بدنه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
        }else{
        echo json_encode(array('result'=>$employeetoken[0]
        ,"data"=>$employeetoken[1]
        ,'desc'=>$employeetoken[2]));

        }
    }
    else
    if ($command=="modify_damage_driver")
    {
        $bodycar_damage_driver_id=$this->post('bodycar_damage_driver_id');
        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','bodycar');
        if($employeetoken[0]=='ok')
        {
        $query="UPDATE bodycar_damage_driver_tb SET ";
        if(isset($_REQUEST['bodycar_damage_driver_name'])){
        $bodycar_damage_driver_name=$this->post('bodycar_damage_driver_name');
        $query.="bodycar_damage_driver_name='".$bodycar_damage_driver_name."'";
        }
        if(isset($_REQUEST['bodycar_damage_driver_digit'])&&(isset($_REQUEST['bodycar_damage_driver_name']))){ $query.=",";}
        if(isset($_REQUEST['bodycar_damage_driver_digit'])){
        $bodycar_damage_driver_digit=$this->post('bodycar_damage_driver_digit');
        $query.="bodycar_damage_driver_digit='".$bodycar_damage_driver_digit."'";
        }
        $query.="where bodycar_damage_driver_id=".$bodycar_damage_driver_id;
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
    if ($command=="add_time")
    {
        $bodycar_time_id=$this->post('bodycar_time_id');
        $bodycar_time_desc=$this->post('bodycar_time_desc');
        $bodycar_time_percent=$this->post('bodycar_time_percent');
        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
    if($employeetoken[0]=='ok')
    {
        $result=$this->B_bodycar->get_bodycar_time_by($bodycar_time_desc,$bodycar_time_id);
        if(empty($result))
    {
        $bodycar_time_id=$this->B_bodycar->add_bodycar_time($bodycar_time_id,$bodycar_time_desc, $bodycar_time_percent);
        echo json_encode(array('result'=>"ok"
        ,"data"=>array('bodycar_time_id'=>$bodycar_time_id)
        ,'desc'=>'مدت بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }else{
        $carmode=$result[0];
        echo json_encode(array('result'=>"error"
        ,"data"=>array('bodycar_time_id'=>$carmode['bodycar_time_id'])
        ,'desc'=>'مدت بیمه نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    }else{
    echo json_encode(array('result'=>$employeetoken[0]
    ,"data"=>$employeetoken[1]
    ,'desc'=>$employeetoken[2]));
    }
    }
    else
    if ($command=="get_time")
    {
        $result = $this->B_bodycar->all_bodycar_time();
        $output =array();
        foreach($result as $row)
        {
            $record=array();
            $record['bodycar_time_id']=$row['bodycar_time_id'];
            $record['bodycar_time_desc']=$row['bodycar_time_desc'];
            $record['bodycar_time_percent']=$row['bodycar_time_percent'];
            $output[]=$record;
        }
        echo json_encode(array('result'=>"ok"
        ,"data"=>$output
        ,'desc'=>'مدت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    else
    if ($command=="delete_time")
    {
        $bodycar_time_id=$this->post('bodycar_time_id');
        $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
        if($employeetoken[0]=='ok')
        {
        $output =array();
        $result = $this->B_bodycar->del_bodycar_time($bodycar_time_id);
        if($result){echo json_encode(array('result'=>"ok"
        ,"data"=>$output
        ,'desc'=>'مدت بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }else{
        echo json_encode(array('result'=>"error"
        ,"data"=>$output
        ,'desc'=>'مدت بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
        }else{
        echo json_encode(array('result'=>$employeetoken[0]
        ,"data"=>$employeetoken[1]
        ,'desc'=>$employeetoken[2]));

        }
    }
    else
    if ($command=="modify_time")
    {
        $bodycar_time_id=$this->post('bodycar_time_id');
        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','bodycar');
        if($employeetoken[0]=='ok')
        {
        $query="UPDATE bodycar_time_tb SET ";
        if(isset($_REQUEST['bodycar_time_desc'])){
        $bodycar_time_desc=$this->post('bodycar_time_desc');
        $query.="bodycar_time_desc='".$_REQUEST['bodycar_time_desc']."'";
        }
        if(isset($_REQUEST['bodycar_time_percent'])&&(isset($_REQUEST['bodycar_time_desc']))){ $query.=",";}
        if(isset($_REQUEST['bodycar_time_percent'])){
        $bodycar_time_percent=$this->post('bodycar_time_percent');
        $query.="bodycar_time_percent='".$_REQUEST['bodycar_time_percent']."'";
        }
        $query.="where bodycar_time_id=".$bodycar_time_id;
        $result=$this->B_db->run_query_put($result);
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
    if ($command=="add_usefor")
    {
    $bodycar_usefor_id=$this->post('bodycar_usefor_id');
    $bodycar_usefor_name=$this->post('bodycar_usefor_name');
    $bodycar_usefor_percent=$this->post('bodycar_usefor_percent');
    $bodycar_usefor_carmode_id=$this->post('bodycar_usefor_carmode_id');
    $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
    if($employeetoken[0]=='ok')
    {
        $result=$this->B_bodycar->get_bodycar_usefor($bodycar_usefor_carmode_id,$bodycar_usefor_name,$bodycar_usefor_id);
        if(empty($result))
    {
        $bodycar_usefor_id=$this->B_bodycar->add_bodycar_usefor($bodycar_usefor_id,$bodycar_usefor_name,$bodycar_usefor_percent,$bodycar_usefor_carmode_id);
        echo json_encode(array('result'=>"ok"
        ,"data"=>array('bodycar_usefor_id'=>$bodycar_usefor_id)
        ,'desc'=>'مورد استفاده بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }else{
        $carmode=$result[0];
        echo json_encode(array('result'=>"error"
        ,"data"=>array('bodycar_usefor_id'=>$carmode['bodycar_usefor_id'])
        ,'desc'=>'مورد استفاده بیمه نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
        }else{
        echo json_encode(array('result'=>$employeetoken[0]
        ,"data"=>$employeetoken[1]
        ,'desc'=>$employeetoken[2]));

        }
    }
    else
    if ($command=="get_usefor")
    {
        $query="select * from bodycar_usefor_tb,carmode_tb  where bodycar_usefor_carmode_id=carmode_id  AND ";
        if(isset($_REQUEST['carmode_id'])){
        $carmode_id=$this->post('carmode_id');
        $query.=' bodycar_usefor_carmode_id='.$carmode_id;}else{$query.=" 1=1 ";}

        $query.=" ORDER BY bodycar_usefor_id ASC";
		
        $result = $this->B_db->run_query($query);
        $output =array();
        foreach($result as $row)
        {
        $record=array();
        $record['bodycar_usefor_id']=$row['bodycar_usefor_id'];
        $record['bodycar_usefor_name']=$row['bodycar_usefor_name'];
        $record['bodycar_usefor_percent']=$row['bodycar_usefor_percent'];
        $record['bodycar_usefor_carmode_id']=$row['bodycar_usefor_carmode_id'];
        $record['carmode_name']=$row['carmode_name'];
        $output[]=$record;
        }
        echo json_encode(array('result'=>"ok"
        ,"data"=>$output
        ,'desc'=>'مورد استفاده بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    else
    if ($command=="delete_usefor")
    {
        $bodycar_usefor_id=$this->post('bodycar_usefor_id');
        $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
    if($employeetoken[0]=='ok')
    {
        $output =array();
        $result = $this->B_bodycar->del_bodycar_usefor($bodycar_usefor_id);
        if($result){echo json_encode(array('result'=>"ok"
        ,"data"=>$output
        ,'desc'=>'مورد استفاده بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }else{
        echo json_encode(array('result'=>"error"
        ,"data"=>$output
        ,'desc'=>'مورد استفاده بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
        }else{
        echo json_encode(array('result'=>$employeetoken[0]
        ,"data"=>$employeetoken[1]
        ,'desc'=>$employeetoken[2]));

        }
        }
    else
    if ($command=="modify_usefor")
    {
    $bodycar_usefor_id=$this->post('bodycar_usefor_id');
    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','bodycar');
    if($employeetoken[0]=='ok')
    {
        $query="UPDATE bodycar_usefor_tb SET ";
        if(isset($_REQUEST['bodycar_usefor_desc'])){
        $bodycar_usefor_desc=$this->post('bodycar_usefor_desc');
        $query.="bodycar_usefor_desc='".$bodycar_usefor_desc."'";
        }
        if(isset($_REQUEST['bodycar_usefor_percent'])&&(isset($_REQUEST['bodycar_usefor_desc']))){ $query.=",";}
        if(isset($_REQUEST['bodycar_usefor_percent'])){
        $bodycar_usefor_percent=$this->post('bodycar_usefor_percent');
        $query.="bodycar_usefor_percent='".$bodycar_usefor_percent."'";
        }
        if(isset($_REQUEST['bodycar_usefor_carmode_id'])&&(isset($_REQUEST['bodycar_usefor_percent'])||isset($_REQUEST['bodycar_usefor_desc']))){ $query.=",";}
        if(isset($_REQUEST['bodycar_usefor_carmode_id'])){
        $bodycar_usefor_carmode_id=$this->post('bodycar_usefor_carmode_id');
        $query.="bodycar_usefor_carmode_id='".$bodycar_usefor_carmode_id."'";
        }
        $query.="where bodycar_usefor_id=".$bodycar_usefor_id;
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
    if ($command=="add_coverage")
    {
    $bodycar_coverage_id=$this->post('bodycar_coverage_id');
    $bodycar_coverage_desc=$this->post('bodycar_coverage_desc');
    $bodycar_coverage_name=$this->post('bodycar_coverage_name');
    $bodycar_coverage_percent=$this->post('bodycar_coverage_percent');
    $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
    if($employeetoken[0]=='ok')
    {
    $result=$this->B_bodycar->get_bodycar_coverage_by($bodycar_coverage_name,$bodycar_coverage_id);
        if(empty($result))
    {
        $bodycar_coverage_id=$this->B_bodycar->add_bodycar_coverage($bodycar_coverage_id,$bodycar_coverage_name,$bodycar_coverage_desc, $bodycar_coverage_percent);
        echo json_encode(array('result'=>"ok"
        ,"data"=>array('bodycar_coverage_id'=>$bodycar_coverage_id)
        ,'desc'=>'مدت بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }else{
        $carmode=$result[0];
        echo json_encode(array('result'=>"error"
        ,"data"=>array('bodycar_coverage_id'=>$carmode['bodycar_coverage_id'])
        ,'desc'=>'مدت بیمه نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    }else{
    echo json_encode(array('result'=>$employeetoken[0]
    ,"data"=>$employeetoken[1]
    ,'desc'=>$employeetoken[2]));
    }
    }
    else
    if ($command=="get_coverage")
    {
        $result = $this->B_bodycar->all_bodycar_coverage();
        $output =array();
        foreach($result as $row)
        {
        $record=array();
        $record['bodycar_coverage_id']=$row['bodycar_coverage_id'];
        $record['bodycar_coverage_name']=$row['bodycar_coverage_name'];
        $record['bodycar_coverage_desc']=$row['bodycar_coverage_desc'];
        $record['bodycar_coverage_percent']=$row['bodycar_coverage_percent'];
        $output[]=$record;
        }
        echo json_encode(array('result'=>"ok"
        ,"data"=>$output
        ,'desc'=>'مدت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    else
    if ($command=="delete_coverage")
    {
    $bodycar_coverage_id=$this->post('bodycar_coverage_id');
    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
    if($employeetoken[0]=='ok')
    {
    $output =array();
    $result = $this->B_bodycar->del_bodycar_coverage($bodycar_coverage_id);
    if($result){echo json_encode(array('result'=>"ok"
    ,"data"=>$output
    ,'desc'=>'مدت بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }else{
    echo json_encode(array('result'=>"error"
    ,"data"=>$output
    ,'desc'=>'مدت بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    }else{
    echo json_encode(array('result'=>$employeetoken[0]
    ,"data"=>$employeetoken[1]
    ,'desc'=>$employeetoken[2]));
    
    }
    }
    else
    if($command=="modify_coverage")
    {
    $bodycar_coverage_id=$this->post('bodycar_coverage_id');
    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','bodycar');
    if($employeetoken[0]=='ok')
    {
        $query="UPDATE bodycar_coverage_tb SET ";
        if(isset($_REQUEST['bodycar_coverage_desc'])){
        $bodycar_coverage_desc=$this->post('bodycar_coverage_desc');
        $query.="bodycar_coverage_desc='".$bodycar_coverage_desc."'";
        }
        if(isset($_REQUEST['bodycar_coverage_percent'])&&(isset($_REQUEST['bodycar_coverage_desc']))){ $query.=",";}
        if(isset($_REQUEST['bodycar_coverage_percent'])){
        $bodycar_coverage_percent=$this->post('bodycar_coverage_percent');
        $query.="bodycar_coverage_percent='".$bodycar_coverage_percent."'";
        }
        if(isset($_REQUEST['bodycar_coverage_name'])&&(isset($_REQUEST['bodycar_coverage_percent'])||isset($_REQUEST['bodycar_coverage_desc']))){ $query.=",";}
        if(isset($_REQUEST['bodycar_coverage_name'])){
        $bodycar_coverage_name=$this->post('bodycar_coverage_name');
        $query.="bodycar_coverage_name='".$bodycar_coverage_name."'";
        }
        $query.="where bodycar_coverage_id=".$bodycar_coverage_id;
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
    if ($command=="add_bodycar_price")
    {
        $bodycar_price_id=$this->post('bodycar_price_id');
        $bodycar_price_fieldcompany_id=$this->post('bodycar_price_fieldcompany_id');
        $bodycar_price_car_mode_id=$this->post('bodycar_price_car_mode_id');
        $bodycar_price_cargroup_id=$this->post('bodycar_price_cargroup_id');
        $bodycar_price_import_year=$this->post('bodycar_price_import_year');
        $bodycar_price_import_percent=$this->post('bodycar_price_import_percent');
        $bodycar_price_new_percent=$this->post('bodycar_price_new_percent');
        $bodycar_price_min_disc=$this->post('bodycar_price_min_disc');
        $bodycar_price_max_disc=$this->post('bodycar_price_max_disc');
        $bodycar_price_chash=$this->post('bodycar_price_chash');
        $bodycar_price_fixed_amount=$this->post('bodycar_price_fixed_amount');
        $bodycar_price_fixed_transportation=$this->post('bodycar_price_fixed_transportation');
        $bodycar_price_min_price=$this->post('bodycar_price_min_price');
        $bodycar_price_max_price=$this->post('bodycar_price_max_price');
        $bodycar_price_cargroup_id=$this->post('bodycar_price_cargroup_id');
        $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
    if($employeetoken[0]=='ok')
    {
        $result=$this->B_bodycar->get_bodycar_price_by($bodycar_price_cargroup_id,$bodycar_price_fieldcompany_id,$bodycar_price_car_mode_id);
        if(empty($result))
        {
        $bodycar_price_id=$this->B_bodycar->add_bodycar_price($bodycar_price_fieldcompany_id, $bodycar_price_car_mode_id,$bodycar_price_cargroup_id,$bodycar_price_import_year,$bodycar_price_import_percent,$bodycar_price_new_percent,$bodycar_price_min_disc,$bodycar_price_max_disc,$bodycar_price_chash,$bodycar_price_fixed_amount,$bodycar_price_fixed_transportation,$bodycar_price_min_price,$bodycar_price_max_price);
        echo json_encode(array('result'=>"ok"
        ,"data"=>array('bodycar_price_id'=>$bodycar_price_id,'query'=>"query")
        ,'desc'=>'مورد استفاده بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }else{
        $carmode=$result[0];
        echo json_encode(array('result'=>"error"
        ,"data"=>array('bodycar_price_id'=>$carmode['bodycar_price_id'])
        ,'desc'=>'مورد استفاده بیمه نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    }else{
    echo json_encode(array('result'=>$employeetoken[0]
    ,"data"=>$employeetoken[1]
    ,'desc'=>$employeetoken[2]));
    }
    }
    else
    if ($command=="get_bodycar_price")
    {
    $result = $this->B_bodycar->all_bodycar_price();
    $output =array();
    foreach($result as $row)
    {
        $record=array();
        $record['bodycar_price_id']=$row['bodycar_price_id'];
        $record['bodycar_price_fieldcompany_id']=$row['bodycar_price_fieldcompany_id'];
        $record['bodycar_price_car_mode_id']=$row['bodycar_price_car_mode_id'];
        $record['bodycar_price_cargroup_id']=$row['bodycar_price_cargroup_id'];
        $record['fieldcompany_company_id']=$row['fieldcompany_company_id'];
        $record['company_name']=$row['company_name'];
        $record['company_logo_url']=IMGADD.$row['company_logo_url'];
        $record['cargroup_name']=$row['cargroup_name'];
        $record['carmode_name']=$row['carmode_name'];
        $record['bodycar_price_import_year']=$row['bodycar_price_import_year'];
        $record['bodycar_price_import_percent']=$row['bodycar_price_import_percent'];
        $record['bodycar_price_new_percent']=$row['bodycar_price_new_percent'];
        $record['bodycar_price_min_disc']=$row['bodycar_price_min_disc'];
        $record['bodycar_price_max_disc']=$row['bodycar_price_max_disc'];
        $record['bodycar_price_chash']=$row['bodycar_price_chash'];
        $record['bodycar_price_fixed_amount']=$row['bodycar_price_fixed_amount'];
        $record['bodycar_price_fixed_transportation']=$row['bodycar_price_fixed_transportation'];
        $record['bodycar_price_min_price']=$row['bodycar_price_min_price'];
        $record['bodycar_price_max_price']=$row['bodycar_price_max_price'];
        $record['bodycar_price_stairs']=$row['bodycar_price_stairs'];
        $record['bodycar_price_robonbaseprice']=$row['bodycar_price_robonbaseprice'];
        $record['bodycar_price_together_disc']=$row['bodycar_price_together_disc'];
        $record['bodycar_price_deactive']=$row['bodycar_price_deactive'];
        $output[]=$record;
    }
    echo json_encode(array('result'=>"ok"
    ,"data"=>$output
    ,'desc'=>'مورد استفاده بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    else
    if ($command=="delete_bodycar_price")
    {
        $bodycar_price_id=$this->post('bodycar_price_id');
        $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
        if($employeetoken[0]=='ok')
        {
            $output =array();
            $result = $this->B_bodycar->del_bodycar_price($bodycar_price_id);
            if($result){echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'مورد استفاده بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }else{
            echo json_encode(array('result'=>"error"
            ,"data"=>$output
            ,'desc'=>'مورد استفاده بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }
        }else{
        echo json_encode(array('result'=>$employeetoken[0]
        ,"data"=>$employeetoken[1]
        ,'desc'=>$employeetoken[2]));
        }
    }
    else
    if ($command=="modify_bodycar_price")
    {
    $bodycar_price_id=$this->post('bodycar_price_id');
    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','bodycar');
    if($employeetoken[0]=='ok')
    {
    $query="UPDATE bodycar_price_tb SET ";
    if(isset($_REQUEST['bodycar_price_deactive'])){
    $bodycar_price_deactive=$this->post('bodycar_price_deactive');
    $query.="bodycar_price_deactive=".$bodycar_price_deactive." ";
    }
    if(isset($_REQUEST['bodycar_price_import_year'])&&(isset($_REQUEST['bodycar_price_deactive']))){ $query.=",";}
    if(isset($_REQUEST['bodycar_price_import_year'])){
    $bodycar_price_import_year=$this->post('bodycar_price_import_year');
    $query.="bodycar_price_import_year=".$bodycar_price_import_year."";
    }
    if(isset($_REQUEST['bodycar_price_import_percent'])&&(isset($_REQUEST['bodycar_price_import_year'])||isset($_REQUEST['bodycar_price_deactive']))){ $query.=",";}
    if(isset($_REQUEST['bodycar_price_import_percent'])){
    $bodycar_price_import_percent=$this->post('bodycar_price_import_percent');
    $query.="bodycar_price_import_percent=".$bodycar_price_import_percent."";
    }
    if(isset($_REQUEST['bodycar_price_new_percent'])&&(isset($_REQUEST['bodycar_price_import_percent'])||isset($_REQUEST['bodycar_price_import_year'])||isset($_REQUEST['bodycar_price_deactive']))){ $query.=",";}
    if(isset($_REQUEST['bodycar_price_new_percent'])){
    $bodycar_price_new_percent=$this->post('bodycar_price_new_percent');
    $query.="bodycar_price_new_percent=".$bodycar_price_new_percent."";
    }
    if(isset($_REQUEST['bodycar_price_min_disc'])&&(isset($_REQUEST['bodycar_price_new_percent'])||isset($_REQUEST['bodycar_price_import_percent'])||isset($_REQUEST['bodycar_price_import_year'])||isset($_REQUEST['bodycar_price_deactive']))){ $query.=",";}
    if(isset($_REQUEST['bodycar_price_min_disc'])){
    $bodycar_price_min_disc=$this->post('bodycar_price_min_disc');
    $query.="bodycar_price_min_disc=".$bodycar_price_min_disc."";
    }
    if(isset($_REQUEST['bodycar_price_max_disc'])&&(isset($_REQUEST['bodycar_price_min_disc'])||isset($_REQUEST['bodycar_price_new_percent'])||isset($_REQUEST['bodycar_price_import_percent'])||isset($_REQUEST['bodycar_price_import_year'])||isset($_REQUEST['bodycar_price_deactive']))){ $query.=",";}
    if(isset($_REQUEST['bodycar_price_max_disc'])){
    $bodycar_price_max_disc=$this->post('bodycar_price_max_disc');
    $query.="bodycar_price_max_disc=".$bodycar_price_max_disc."";
    }
    if(isset($_REQUEST['bodycar_price_fixed_amount'])&&(isset($_REQUEST['bodycar_price_max_disc'])||isset($_REQUEST['bodycar_price_min_disc'])||isset($_REQUEST['bodycar_price_new_percent'])||isset($_REQUEST['bodycar_price_import_percent'])||isset($_REQUEST['bodycar_price_import_year'])||isset($_REQUEST['bodycar_price_deactive']))){ $query.=",";}
    if(isset($_REQUEST['bodycar_price_fixed_amount'])){
    $bodycar_price_fixed_amount=$this->post('bodycar_price_fixed_amount');
    $query.="bodycar_price_fixed_amount=".$bodycar_price_fixed_amount."";
    }
    
    if(isset($_REQUEST['bodycar_price_fixed_amount'])&&(isset($_REQUEST['bodycar_price_max_disc'])||isset($_REQUEST['bodycar_price_min_disc'])||isset($_REQUEST['bodycar_price_new_percent'])||isset($_REQUEST['bodycar_price_import_percent'])||isset($_REQUEST['bodycar_price_import_year'])||isset($_REQUEST['bodycar_price_deactive']))){ $query.=",";}
    if(isset($_REQUEST['bodycar_price_fixed_amount'])){
    $bodycar_price_fixed_amount=$this->post('bodycar_price_fixed_amount');
    $query.="bodycar_price_fixed_amount=".$bodycar_price_fixed_amount."";
    }
    if(isset($_REQUEST['bodycar_price_min_price'])&&(isset($_REQUEST['bodycar_price_fixed_amount'])||isset($_REQUEST['bodycar_price_max_disc'])||isset($_REQUEST['bodycar_price_min_disc'])||isset($_REQUEST['bodycar_price_new_percent'])||isset($_REQUEST['bodycar_price_import_percent'])||isset($_REQUEST['bodycar_price_import_year'])||isset($_REQUEST['bodycar_price_deactive']))){ $query.=",";}
    if(isset($_REQUEST['bodycar_price_min_price'])){
    $bodycar_price_min_price=$this->post('bodycar_price_min_price');
    $query.="bodycar_price_min_price=".$bodycar_price_min_price."";
    }
    if(isset($_REQUEST['bodycar_price_max_price'])&&(isset($_REQUEST['bodycar_price_min_price'])||isset($_REQUEST['bodycar_price_fixed_amount'])||isset($_REQUEST['bodycar_price_max_disc'])||isset($_REQUEST['bodycar_price_min_disc'])||isset($_REQUEST['bodycar_price_new_percent'])||isset($_REQUEST['bodycar_price_import_percent'])||isset($_REQUEST['bodycar_price_import_year'])||isset($_REQUEST['bodycar_price_deactive']))){ $query.=",";}
    if(isset($_REQUEST['bodycar_price_max_price'])){
    $bodycar_price_max_price=$this->post('bodycar_price_max_price');
    $query.="bodycar_price_max_price=".$bodycar_price_max_price."";
    }
    if(isset($_REQUEST['bodycar_price_stairs'])&&(isset($_REQUEST['bodycar_price_max_price'])||isset($_REQUEST['bodycar_price_min_price'])||isset($_REQUEST['bodycar_price_fixed_amount'])||isset($_REQUEST['bodycar_price_max_disc'])||isset($_REQUEST['bodycar_price_min_disc'])||isset($_REQUEST['bodycar_price_new_percent'])||isset($_REQUEST['bodycar_price_import_percent'])||isset($_REQUEST['bodycar_price_import_year'])||isset($_REQUEST['bodycar_price_deactive']))){ $query.=",";}
    if(isset($_REQUEST['bodycar_price_stairs'])){
    $bodycar_price_stairs=$this->post('bodycar_price_stairs');
    $query.="bodycar_price_stairs=".$bodycar_price_stairs."";
    }
	
	if(isset($_REQUEST['bodycar_price_chash'])&&(isset($_REQUEST['bodycar_price_stairs'])||isset($_REQUEST['bodycar_price_max_price'])||isset($_REQUEST['bodycar_price_min_price'])||isset($_REQUEST['bodycar_price_fixed_amount'])||isset($_REQUEST['bodycar_price_max_disc'])||isset($_REQUEST['bodycar_price_min_disc'])||isset($_REQUEST['bodycar_price_new_percent'])||isset($_REQUEST['bodycar_price_import_percent'])||isset($_REQUEST['bodycar_price_import_year'])||isset($_REQUEST['bodycar_price_deactive']))){ $query.=",";}
    if(isset($_REQUEST['bodycar_price_chash'])){
    $bodycar_price_chash=$this->post('bodycar_price_chash');
    $query.="bodycar_price_chash=".$bodycar_price_chash."";
    }
	
	if(isset($_REQUEST['bodycar_price_fixed_transportation'])&&(isset($_REQUEST['bodycar_price_chash'])||isset($_REQUEST['bodycar_price_stairs'])||isset($_REQUEST['bodycar_price_max_price'])||isset($_REQUEST['bodycar_price_min_price'])||isset($_REQUEST['bodycar_price_fixed_amount'])||isset($_REQUEST['bodycar_price_max_disc'])||isset($_REQUEST['bodycar_price_min_disc'])||isset($_REQUEST['bodycar_price_new_percent'])||isset($_REQUEST['bodycar_price_import_percent'])||isset($_REQUEST['bodycar_price_import_year'])||isset($_REQUEST['bodycar_price_deactive']))){ $query.=",";}
    if(isset($_REQUEST['bodycar_price_fixed_transportation'])){
    $bodycar_price_fixed_transportation=$this->post('bodycar_price_fixed_transportation');
    $query.="bodycar_price_fixed_transportation=".$bodycar_price_fixed_transportation." ";
    }

        if(isset($_REQUEST['bodycar_price_robonbaseprice'])&&(isset($_REQUEST['bodycar_price_fixed_transportation'])||isset($_REQUEST['bodycar_price_chash'])||isset($_REQUEST['bodycar_price_stairs'])||isset($_REQUEST['bodycar_price_max_price'])||isset($_REQUEST['bodycar_price_min_price'])||isset($_REQUEST['bodycar_price_fixed_amount'])||isset($_REQUEST['bodycar_price_max_disc'])||isset($_REQUEST['bodycar_price_min_disc'])||isset($_REQUEST['bodycar_price_new_percent'])||isset($_REQUEST['bodycar_price_import_percent'])||isset($_REQUEST['bodycar_price_import_year'])||isset($_REQUEST['bodycar_price_deactive']))){ $query.=",";}
        if(isset($_REQUEST['bodycar_price_robonbaseprice'])){
            $bodycar_price_robonbaseprice=$this->post('bodycar_price_robonbaseprice');
            $query.="bodycar_price_robonbaseprice=".$bodycar_price_robonbaseprice." ";
        }

        if(isset($_REQUEST['bodycar_price_together_disc'])&&(isset($_REQUEST['bodycar_price_robonbaseprice'])||isset($_REQUEST['bodycar_price_fixed_transportation'])||isset($_REQUEST['bodycar_price_chash'])||isset($_REQUEST['bodycar_price_stairs'])||isset($_REQUEST['bodycar_price_max_price'])||isset($_REQUEST['bodycar_price_min_price'])||isset($_REQUEST['bodycar_price_fixed_amount'])||isset($_REQUEST['bodycar_price_max_disc'])||isset($_REQUEST['bodycar_price_min_disc'])||isset($_REQUEST['bodycar_price_new_percent'])||isset($_REQUEST['bodycar_price_import_percent'])||isset($_REQUEST['bodycar_price_import_year'])||isset($_REQUEST['bodycar_price_deactive']))){ $query.=",";}
        if(isset($_REQUEST['bodycar_price_together_disc'])){
            $bodycar_price_together_disc=$this->post('bodycar_price_together_disc');
            $query.="bodycar_price_together_disc=".$bodycar_price_together_disc." ";
        }


        $query.=" where bodycar_price_id=".$bodycar_price_id;
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
    //*******************************************************************************************
    //*******************************************************************************************
    //*******************************************************************************************
    else
    if ($command=="add_bodycar_slideprice")
    {
    $bodycar_slideprice_bodycar_price_id=$this->post('bodycar_slideprice_bodycar_price_id');
    $bodycar_slideprice_min=$this->post('bodycar_slideprice_min');
    $bodycar_slideprice_max=$this->post('bodycar_slideprice_max');
    $bodycar_slideprice_percent=$this->post('bodycar_slideprice_percent');
    $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
    if($employeetoken[0]=='ok')
    {
        $result=$this->B_bodycar->get_bodycar_slideprice_by($bodycar_slideprice_bodycar_price_id,$bodycar_slideprice_min,$bodycar_slideprice_max);
        if(empty($result))
        {
        $bodycar_slideprice_id=$this->B_bodycar->add_bodycar_slideprice($bodycar_slideprice_bodycar_price_id,$bodycar_slideprice_min,$bodycar_slideprice_max,$bodycar_slideprice_percent);
        echo json_encode(array('result'=>"ok"
        ,"data"=>array('bodycar_slideprice_id'=>$bodycar_slideprice_id)
        ,'desc'=>'پوشش مازاد مالی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }else{
        $carmode=$result[0];
        echo json_encode(array('result'=>"error"
        ,"data"=>array('bodycar_slideprice_id'=>$carmode['bodycar_slideprice_id'])
        ,'desc'=>' پوشش مازاد مالی نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
    }else{
    echo json_encode(array('result'=>$employeetoken[0]
    ,"data"=>$employeetoken[1]
    ,'desc'=>$employeetoken[2]));
    }
    }
    else
    if ($command=="get_bodycar_slideprice")
    {
    $bodycar_price_id=$this->post('bodycar_price_id');
    $result = $this->B_bodycar->get_bodycar_slideprice_by_priceid($bodycar_price_id);
    $output =array();
    foreach($result as $row)
    {
        $record=array();
        $record['bodycar_slideprice_id']=$row['bodycar_slideprice_id'];
        $record['bodycar_slideprice_bodycar_price_id']=$row['bodycar_slideprice_bodycar_price_id'];
        $record['bodycar_slideprice_min']=$row['bodycar_slideprice_min'];
        $record['bodycar_slideprice_percent']=$row['bodycar_slideprice_percent'];
        $record['bodycar_slideprice_max']=$row['bodycar_slideprice_max'];
        $output[]=$record;
    }
    echo json_encode(array('result'=>"ok"
    ,"data"=>$output
    ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    else
    if ($command=="delete_bodycar_slideprice")
    {
    $bodycar_slideprice_id=$this->post('bodycar_slideprice_id');
    
    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
    if($employeetoken[0]=='ok')
    {
    $output =array();
    $result = $this->B_bodycar->del_bodycar_slideprice($bodycar_slideprice_id);
    if($result){echo json_encode(array('result'=>"ok"
    ,"data"=>$output
    ,'desc'=>'قیمت بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }else{
    echo json_encode(array('result'=>"error"
    ,"data"=>$output
    ,'desc'=>'قیمت بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    }else{
    echo json_encode(array('result'=>$employeetoken[0]
    ,"data"=>$employeetoken[1]
    ,'desc'=>$employeetoken[2]));
    }
    }
    else
    if ($command=="modify_bodycar_slideprice")
    {
    $bodycar_slideprice_id=$this->post('bodycar_slideprice_id');
    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','bodycar');
    if($employeetoken[0]=='ok')
    {
        $query="UPDATE bodycar_slideprice_tb SET ";
        if(isset($_REQUEST['bodycar_slideprice_percent'])){
        $bodycar_slideprice_percent=$this->post('bodycar_slideprice_percent');
        $query.="bodycar_slideprice_percent='".$bodycar_slideprice_percent."'";}
        if(isset($_REQUEST['bodycar_slideprice_min'])&&(isset($_REQUEST['bodycar_slideprice_percent']))){ $query.=",";}
        if(isset($_REQUEST['bodycar_slideprice_min'])){
        $bodycar_slideprice_min=$this->post('bodycar_slideprice_min');
        $query.="bodycar_slideprice_min='".$bodycar_slideprice_min."'";
        }
        if(isset($_REQUEST['bodycar_slideprice_max'])&&(isset($_REQUEST['bodycar_slideprice_min'])||isset($_REQUEST['bodycar_slideprice_percent']))){ $query.=",";}
        if(isset($_REQUEST['bodycar_slideprice_max'])){
        $bodycar_slideprice_max=$this->post('bodycar_slideprice_max');
        $query.="bodycar_slideprice_max='".$bodycar_slideprice_max."'";
        }
        $query.="where bodycar_slideprice_id=".$bodycar_slideprice_id;
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
    //********************************************************************************************
    else
        if ($command=="add_bodycar_extra_slideold")
        {
            $bodycar_extra_slideold_bodycar_price_id=$this->post('bodycar_extra_slideold_bodycar_price_id');
            $bodycar_extra_slideold_min=$this->post('bodycar_extra_slideold_min');
            $bodycar_extra_slideold_max=$this->post('bodycar_extra_slideold_max');
            $bodycar_extra_slideold_percent=$this->post('bodycar_extra_slideold_percent');
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
            if($employeetoken[0]=='ok')
            {
                $result=$this->B_bodycar->get_bodycar_extra_slideold_by($bodycar_extra_slideold_bodycar_price_id,$bodycar_extra_slideold_min,$bodycar_extra_slideold_max);
                if(empty($result))
                {
                    $bodycar_extra_slideold_id=$this->B_bodycar->add_bodycar_extra_slideold($bodycar_extra_slideold_bodycar_price_id,$bodycar_extra_slideold_min,$bodycar_extra_slideold_max,$bodycar_extra_slideold_percent);
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('bodycar_extra_slideold_id'=>$bodycar_extra_slideold_id)
                    ,'desc'=>'پوشش مازاد مالی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    $carmode=$result[0];
                    echo json_encode(array('result'=>"error"
                    ,"data"=>array('bodycar_extra_slideold_id'=>$carmode['bodycar_extra_slideold_id'])
                    ,'desc'=>' پوشش مازاد مالی نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));
            }
        }
        else
            if ($command=="get_bodycar_extra_slideold")
            {
                $bodycar_price_id=$this->post('bodycar_price_id');
                $result = $this->B_bodycar->get_bodycar_extra_slideold_by_priceid($bodycar_price_id);
                $output =array();
                foreach($result as $row)
                {
                    $record=array();
                    $record['bodycar_extra_slideold_id']=$row['bodycar_extra_slideold_id'];
                    $record['bodycar_extra_slideold_bodycar_price_id']=$row['bodycar_extra_slideold_bodycar_price_id'];
                    $record['bodycar_extra_slideold_min']=$row['bodycar_extra_slideold_min'];
                    $record['bodycar_extra_slideold_percent']=$row['bodycar_extra_slideold_percent'];
                    $record['bodycar_extra_slideold_max']=$row['bodycar_extra_slideold_max'];
                    $output[]=$record;
                }
                echo json_encode(array('result'=>"ok"
                ,"data"=>$output
                ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }
            else
                if ($command=="delete_bodycar_extra_slideold")
                {
                    $bodycar_extra_slideold_id=$this->post('bodycar_extra_slideold_id');

                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
                    if($employeetoken[0]=='ok')
                    {
                        $output =array();
                        $result = $this->B_bodycar->del_bodycar_extra_slideold($bodycar_extra_slideold_id);
                        if($result){echo json_encode(array('result'=>"ok"
                        ,"data"=>$output
                        ,'desc'=>'قیمت بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            echo json_encode(array('result'=>"error"
                            ,"data"=>$output
                            ,'desc'=>'قیمت بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]));
                    }
                }
                else
                    if ($command=="modify_bodycar_extra_slideold")
                    {
                        $bodycar_extra_slideold_id=$this->post('bodycar_extra_slideold_id');
                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','bodycar');
                        if($employeetoken[0]=='ok')
                        {
                            $query="UPDATE bodycar_extra_slideold_tb SET ";
                            if(isset($_REQUEST['bodycar_extra_slideold_percent'])){
                                $bodycar_extra_slideold_percent=$this->post('bodycar_extra_slideold_percent');
                                $query.="bodycar_extra_slideold_percent='".$bodycar_extra_slideold_percent."'";}
                            if(isset($_REQUEST['bodycar_extra_slideold_min'])&&(isset($_REQUEST['bodycar_extra_slideold_percent']))){ $query.=",";}
                            if(isset($_REQUEST['bodycar_extra_slideold_min'])){
                                $bodycar_extra_slideold_min=$this->post('bodycar_extra_slideold_min');
                                $query.="bodycar_extra_slideold_min='".$bodycar_extra_slideold_min."'";
                            }
                            if(isset($_REQUEST['bodycar_extra_slideold_max'])&&(isset($_REQUEST['bodycar_extra_slideold_min'])||isset($_REQUEST['bodycar_extra_slideold_percent']))){ $query.=",";}
                            if(isset($_REQUEST['bodycar_extra_slideold_max'])){
                                $bodycar_extra_slideold_max=$this->post('bodycar_extra_slideold_max');
                                $query.="bodycar_extra_slideold_max='".$bodycar_extra_slideold_max."'";
                            }
                            $query.="where bodycar_extra_slideold_id=".$bodycar_extra_slideold_id;
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
                    //********************************************************************************************
                    else
                        if ($command=="add_bodycar_disc_slideold")
                        {
                            $bodycar_disc_slideold_bodycar_price_id=$this->post('bodycar_disc_slideold_bodycar_price_id');
                            $bodycar_disc_slideold_min=$this->post('bodycar_disc_slideold_min');
                            $bodycar_disc_slideold_max=$this->post('bodycar_disc_slideold_max');
                            $bodycar_disc_slideold_percent=$this->post('bodycar_disc_slideold_percent');
                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
                            if($employeetoken[0]=='ok')
                            {
                                $result=$this->B_bodycar->get_bodycar_disc_slideold_by($bodycar_disc_slideold_bodycar_price_id,$bodycar_disc_slideold_min,$bodycar_disc_slideold_max);
                                if(empty($result))
                                {
                                    $bodycar_disc_slideold_id=$this->B_bodycar->add_bodycar_disc_slideold($bodycar_disc_slideold_bodycar_price_id,$bodycar_disc_slideold_min,$bodycar_disc_slideold_max,$bodycar_disc_slideold_percent);
                                    echo json_encode(array('result'=>"ok"
                                    ,"data"=>array('bodycar_disc_slideold_id'=>$bodycar_disc_slideold_id)
                                    ,'desc'=>'پوشش مازاد مالی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }else{
                                    $carmode=$result[0];
                                    echo json_encode(array('result'=>"error"
                                    ,"data"=>array('bodycar_disc_slideold_id'=>$carmode['bodycar_disc_slideold_id'])
                                    ,'desc'=>' پوشش مازاد مالی نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }
                            }else{
                                echo json_encode(array('result'=>$employeetoken[0]
                                ,"data"=>$employeetoken[1]
                                ,'desc'=>$employeetoken[2]));
                            }
                        }
                        else
                            if ($command=="get_bodycar_disc_slideold")
                            {
                                $bodycar_price_id=$this->post('bodycar_price_id');
                                $result = $this->B_bodycar->get_bodycar_disc_slideold_by_priceid($bodycar_price_id);
                                $output =array();
                                foreach($result as $row)
                                {
                                    $record=array();
                                    $record['bodycar_disc_slideold_id']=$row['bodycar_disc_slideold_id'];
                                    $record['bodycar_disc_slideold_bodycar_price_id']=$row['bodycar_disc_slideold_bodycar_price_id'];
                                    $record['bodycar_disc_slideold_min']=$row['bodycar_disc_slideold_min'];
                                    $record['bodycar_disc_slideold_percent']=$row['bodycar_disc_slideold_percent'];
                                    $record['bodycar_disc_slideold_max']=$row['bodycar_disc_slideold_max'];
                                    $output[]=$record;
                                }
                                echo json_encode(array('result'=>"ok"
                                ,"data"=>$output
                                ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
                            else
                                if ($command=="delete_bodycar_disc_slideold")
                                {
                                    $bodycar_disc_slideold_id=$this->post('bodycar_disc_slideold_id');

                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
                                    if($employeetoken[0]=='ok')
                                    {
                                        $output =array();
                                        $result = $this->B_bodycar->del_bodycar_disc_slideold($bodycar_disc_slideold_id);
                                        if($result){echo json_encode(array('result'=>"ok"
                                        ,"data"=>$output
                                        ,'desc'=>'قیمت بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                        }else{
                                            echo json_encode(array('result'=>"error"
                                            ,"data"=>$output
                                            ,'desc'=>'قیمت بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                        }
                                    }else{
                                        echo json_encode(array('result'=>$employeetoken[0]
                                        ,"data"=>$employeetoken[1]
                                        ,'desc'=>$employeetoken[2]));
                                    }
                                }
                                else
                                    if ($command=="modify_bodycar_disc_slideold")
                                    {
                                        $bodycar_disc_slideold_id=$this->post('bodycar_disc_slideold_id');
                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','bodycar');
                                        if($employeetoken[0]=='ok')
                                        {
                                            $query="UPDATE bodycar_disc_slideold_tb SET ";
                                            if(isset($_REQUEST['bodycar_disc_slideold_percent'])){
                                                $bodycar_disc_slideold_percent=$this->post('bodycar_disc_slideold_percent');
                                                $query.="bodycar_disc_slideold_percent='".$bodycar_disc_slideold_percent."'";}
                                            if(isset($_REQUEST['bodycar_disc_slideold_min'])&&(isset($_REQUEST['bodycar_disc_slideold_percent']))){ $query.=",";}
                                            if(isset($_REQUEST['bodycar_disc_slideold_min'])){
                                                $bodycar_disc_slideold_min=$this->post('bodycar_disc_slideold_min');
                                                $query.="bodycar_disc_slideold_min='".$bodycar_disc_slideold_min."'";
                                            }
                                            if(isset($_REQUEST['bodycar_disc_slideold_max'])&&(isset($_REQUEST['bodycar_disc_slideold_min'])||isset($_REQUEST['bodycar_disc_slideold_percent']))){ $query.=",";}
                                            if(isset($_REQUEST['bodycar_disc_slideold_max'])){
                                                $bodycar_disc_slideold_max=$this->post('bodycar_disc_slideold_max');
                                                $query.="bodycar_disc_slideold_max='".$bodycar_disc_slideold_max."'";
                                            }
                                            $query.="where bodycar_disc_slideold_id=".$bodycar_disc_slideold_id;
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
                    //********************************************************************************************
    else
        if ($command=="add_bodycar_slidedisc")
        {
            $bodycar_slidedisc_bodycar_price_id=$this->post('bodycar_slidedisc_bodycar_price_id');
            $bodycar_slidedisc_min=$this->post('bodycar_slidedisc_min');
            $bodycar_slidedisc_max=$this->post('bodycar_slidedisc_max');
            $bodycar_slidedisc_minpercent=$this->post('bodycar_slidedisc_minpercent');
            $bodycar_slidedisc_maxpercent=$this->post('bodycar_slidedisc_maxpercent');
            $bodycar_slidedisc_instalment_minpercent=$this->post('bodycar_slidedisc_instalment_minpercent');
            $bodycar_slidedisc_instalment_maxpercent=$this->post('bodycar_slidedisc_instalment_maxpercent');
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
            if($employeetoken[0]=='ok')
            {
                $result=$this->B_bodycar->get_bodycar_slidedisc_by($bodycar_slidedisc_bodycar_price_id,$bodycar_slidedisc_min,$bodycar_slidedisc_max);
                if(empty($result))
                {
                    $bodycar_slidedisc_id=$this->B_bodycar->add_bodycar_slidedisc($bodycar_slidedisc_bodycar_price_id,$bodycar_slidedisc_min,$bodycar_slidedisc_max,$bodycar_slidedisc_minpercent,$bodycar_slidedisc_maxpercent,$bodycar_slidedisc_instalment_minpercent,$bodycar_slidedisc_instalment_maxpercent);
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('bodycar_slidedisc_id'=>$bodycar_slidedisc_id)
                    ,'desc'=>'پوشش مازاد مالی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    $carmode=$result[0];
                    echo json_encode(array('result'=>"error"
                    ,"data"=>array('bodycar_slidedisc_id'=>$carmode['bodycar_slidedisc_id'])
                    ,'desc'=>' پوشش مازاد مالی نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));
            }
        }
        else
            if ($command=="get_bodycar_slidedisc")
            {
                $bodycar_price_id=$this->post('bodycar_price_id');
                $result = $this->B_bodycar->get_bodycar_slidedisc_by_priceid($bodycar_price_id);
                $output =array();
                foreach($result as $row)
                {
                    $record=array();
                    $record['bodycar_slidedisc_id']=$row['bodycar_slidedisc_id'];
                    $record['bodycar_slidedisc_bodycar_price_id']=$row['bodycar_slidedisc_bodycar_price_id'];
                    $record['bodycar_slidedisc_min']=$row['bodycar_slidedisc_min'];
                    $record['bodycar_slidedisc_minpercent']=$row['bodycar_slidedisc_minpercent'];
                    $record['bodycar_slidedisc_maxpercent']=$row['bodycar_slidedisc_maxpercent'];
                    $record['bodycar_slidedisc_instalment_maxpercent']=$row['bodycar_slidedisc_instalment_maxpercent'];
                    $record['bodycar_slidedisc_instalment_minpercent']=$row['bodycar_slidedisc_instalment_minpercent'];
                    $record['bodycar_slidedisc_max']=$row['bodycar_slidedisc_max'];
                    $output[]=$record;
                }
                echo json_encode(array('result'=>"ok"
                ,"data"=>$output
                ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }
            else
                if ($command=="delete_bodycar_slidedisc")
                {
                    $bodycar_slidedisc_id=$this->post('bodycar_slidedisc_id');

                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
                    if($employeetoken[0]=='ok')
                    {
                        $output =array();
                        $result = $this->B_bodycar->del_bodycar_slidedisc($bodycar_slidedisc_id);
                        if($result){echo json_encode(array('result'=>"ok"
                        ,"data"=>$output
                        ,'desc'=>'قیمت بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            echo json_encode(array('result'=>"error"
                            ,"data"=>$output
                            ,'desc'=>'قیمت بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]));
                    }
                }
                else
                    if ($command=="modify_bodycar_slidedisc")
                    {
                        $bodycar_slidedisc_id=$this->post('bodycar_slidedisc_id');
                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','bodycar');
                        if($employeetoken[0]=='ok')
                        {
                            $query="UPDATE bodycar_slidedisc_tb SET ";
                            if(isset($_REQUEST['bodycar_slidedisc_minpercent'])){
                                $bodycar_slidedisc_minpercent=$this->post('bodycar_slidedisc_minpercent');
                                $query.="bodycar_slidedisc_minpercent='".$bodycar_slidedisc_minpercent."'";}
                            if(isset($_REQUEST['bodycar_slidedisc_min'])&&(isset($_REQUEST['bodycar_slidedisc_minpercent']))){ $query.=",";}
                            if(isset($_REQUEST['bodycar_slidedisc_min'])){
                                $bodycar_slidedisc_min=$this->post('bodycar_slidedisc_min');
                                $query.="bodycar_slidedisc_min='".$bodycar_slidedisc_min."'";
                            }
                            if(isset($_REQUEST['bodycar_slidedisc_max'])&&(isset($_REQUEST['bodycar_slidedisc_min'])||isset($_REQUEST['bodycar_slidedisc_minpercent']))){ $query.=",";}
                            if(isset($_REQUEST['bodycar_slidedisc_max'])){
                                $bodycar_slidedisc_max=$this->post('bodycar_slidedisc_max');
                                $query.="bodycar_slidedisc_max='".$bodycar_slidedisc_max."'";
                            }
                            if(isset($_REQUEST['bodycar_slidedisc_maxpercent'])&&(isset($_REQUEST['bodycar_slidedisc_max'])||isset($_REQUEST['bodycar_slidedisc_min'])||isset($_REQUEST['bodycar_slidedisc_minpercent']))){ $query.=",";}
                            if(isset($_REQUEST['bodycar_slidedisc_maxpercent'])){
                                $bodycar_slidedisc_maxpercent=$this->post('bodycar_slidedisc_maxpercent');
                                $query.="bodycar_slidedisc_maxpercent='".$bodycar_slidedisc_maxpercent."'";
                            }

                            if(isset($_REQUEST['bodycar_slidedisc_instalment_maxpercent'])&&(isset($_REQUEST['bodycar_slidedisc_maxpercent'])||isset($_REQUEST['bodycar_slidedisc_max'])||isset($_REQUEST['bodycar_slidedisc_min'])||isset($_REQUEST['bodycar_slidedisc_minpercent']))){ $query.=",";}
                            if(isset($_REQUEST['bodycar_slidedisc_instalment_maxpercent'])){
                                $bodycar_slidedisc_instalment_maxpercent=$this->post('bodycar_slidedisc_instalment_maxpercent');
                                $query.="bodycar_slidedisc_instalment_maxpercent='".$bodycar_slidedisc_instalment_maxpercent."'";
                            }

                            if(isset($_REQUEST['bodycar_slidedisc_instalment_minpercent'])&&(isset($_REQUEST['bodycar_slidedisc_instalment_maxpercent'])||isset($_REQUEST['bodycar_slidedisc_maxpercent'])||isset($_REQUEST['bodycar_slidedisc_max'])||isset($_REQUEST['bodycar_slidedisc_min'])||isset($_REQUEST['bodycar_slidedisc_minpercent']))){ $query.=",";}
                            if(isset($_REQUEST['bodycar_slidedisc_instalment_minpercent'])){
                                $bodycar_slidedisc_instalment_minpercent=$this->post('bodycar_slidedisc_instalment_minpercent');
                                $query.="bodycar_slidedisc_instalment_minpercent='".$bodycar_slidedisc_instalment_minpercent."'";
                            }


                            $query.=" where bodycar_slidedisc_id=".$bodycar_slidedisc_id;
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
                    //********************************************************************************************
                    else
                        if ($command=="add_price_coverage")
    {
    $bodycar_price_covarage_bodycar_price_id=$this->post('bodycar_price_covarage_bodycar_price_id');
    $bodycar_price_covarage_bodycoverage_id=$this->post('bodycar_price_covarage_bodycoverage_id');
        $bodycar_price_covarage_percent=$this->post('bodycar_price_covarage_percent');
        $bodycar_price_covarage_calmode_id=$this->post('bodycar_price_covarage_calmode_id');
    $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
    if($employeetoken[0]=='ok')
    {
        $result=$this->B_bodycar->get_bodycar_price_covarage_by($bodycar_price_covarage_bodycoverage_id, $bodycar_price_covarage_bodycar_price_id);
        if(empty($result))
        {
        $bodycar_price_covarage_id=$this->B_bodycar->add_bodycar_price_covarage($bodycar_price_covarage_bodycar_price_id,$bodycar_price_covarage_bodycoverage_id, $bodycar_price_covarage_percent, $bodycar_price_covarage_calmode_id);
        echo json_encode(array('result'=>"ok"
        ,"data"=>array('bodycar_price_covarage_id'=>$bodycar_price_covarage_id)
        ,'desc'=>'پوشش مازاد مالی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }else{
        $carmode=$result[0];
        echo json_encode(array('result'=>"error"
        ,"data"=>array('bodycar_price_covarage_id'=>$carmode['bodycar_price_covarage_id'])
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
    if ($command=="get_price_coverage")
    {
        $bodycar_price_id=$this->post('bodycar_price_id');
        $result = $this->B_bodycar->all_bodycar_price_covarage_by_priceid($bodycar_price_id);
        $output =array();
        foreach($result as $row)
        {
        $record=array();
        $record['bodycar_price_covarage_id']=$row['bodycar_price_covarage_id'];
        $record['bodycar_price_covarage_bodycar_price_id']=$row['bodycar_price_covarage_bodycar_price_id'];
        $record['bodycar_price_covarage_bodycoverage_id']=$row['bodycar_price_covarage_bodycoverage_id'];
        $record['bodycar_coverage_name']=$row['bodycar_coverage_name'];
        $record['bodycar_coverage_percent']=$row['bodycar_coverage_percent'];
            $record['bodycar_price_covarage_percent']=$row['bodycar_price_covarage_percent'];
            $record['bodycar_price_covarage_calmode_id']=$row['bodycar_price_covarage_calmode_id'];
        $output[]=$record;
        }
        echo json_encode(array('result'=>"ok"
        ,"data"=>$output
        ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    else
    if ($command=="delete_price_coverage")
    {
        $bodycar_price_covarage_id=$this->post('bodycar_price_covarage_id');
        $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
        if($employeetoken[0]=='ok')
        {
            $output =array();
            $result = $this->B_bodycar->del_bodycar_price_covarage($bodycar_price_covarage_id);
            if($result){echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'قیمت بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }else{
            echo json_encode(array('result'=>"error"
            ,"data"=>$output
            ,'desc'=>'قیمت بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }
        }else{
        echo json_encode(array('result'=>$employeetoken[0]
        ,"data"=>$employeetoken[1]
        ,'desc'=>$employeetoken[2]));

        }
    }
    else
    if ($command=="modify_price_coverage")
    {
    $bodycar_price_covarage_id=$this->post('bodycar_price_covarage_id');
    
    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','bodycar');
    if($employeetoken[0]=='ok')
    {
    $query="UPDATE bodycar_price_covarage_tb SET ";
    if(isset($_REQUEST['bodycar_price_covarage_percent'])){
    $bodycar_price_covarage_percent=$this->post('bodycar_price_covarage_percent');
    $query.="bodycar_price_covarage_percent='".$bodycar_price_covarage_percent."'";}

        if(isset($_REQUEST['bodycar_price_covarage_calmode_id'])&&(isset($_REQUEST['bodycar_price_covarage_percent']))){ $query.=",";}
        if(isset($_REQUEST['bodycar_price_covarage_calmode_id'])){
            $bodycar_price_covarage_calmode_id=$this->post('bodycar_price_covarage_calmode_id');
            $query.="bodycar_price_covarage_calmode_id=$bodycar_price_covarage_calmode_id ";
        }

    $query.=" where bodycar_price_covarage_id=".$bodycar_price_covarage_id;
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
	//****************************************************************************************
	//****************************************************************************************
	//****************************************************************************************
	              //********************************************************************************************
                    //********************************************************************************************
                    //********************************************************************************************
                    else
                        if ($command=="add_price_usefor")
    {
    $bodycar_price_usefor_bodycar_price_id=$this->post('bodycar_price_usefor_bodycar_price_id');
    $bodycar_price_usefor_bodyusefor_id=$this->post('bodycar_price_usefor_bodyusefor_id');
        $bodycar_price_usefor_percent=$this->post('bodycar_price_usefor_percent');
        $bodycar_price_usefor_calmode_id=$this->post('bodycar_price_usefor_calmode_id');
    $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
    if($employeetoken[0]=='ok')
    {
        $result=$this->B_bodycar->get_bodycar_price_usefor_by($bodycar_price_usefor_bodyusefor_id, $bodycar_price_usefor_bodycar_price_id);
        if(empty($result))
        {
        $bodycar_price_usefor_id=$this->B_bodycar->add_bodycar_price_usefor($bodycar_price_usefor_bodycar_price_id,$bodycar_price_usefor_bodyusefor_id, $bodycar_price_usefor_percent,$bodycar_price_usefor_calmode_id);
        echo json_encode(array('result'=>"ok"
        ,"data"=>array('bodycar_price_usefor_id'=>$bodycar_price_usefor_id)
        ,'desc'=>'پوشش مازاد مالی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }else{
        $carmode=$result[0];
        echo json_encode(array('result'=>"error"
        ,"data"=>array('bodycar_price_usefor_id'=>$carmode['bodycar_price_usefor_id'])
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
    if ($command=="get_price_usefor")
    {
        $bodycar_price_id=$this->post('bodycar_price_id');
        $result = $this->B_bodycar->all_bodycar_price_usefor_by_priceid($bodycar_price_id);
        $output =array();
        foreach($result as $row)
        {
        $record=array();
        $record['bodycar_price_usefor_id']=$row['bodycar_price_usefor_id'];
        $record['bodycar_price_usefor_bodycar_price_id']=$row['bodycar_price_usefor_bodycar_price_id'];
        $record['bodycar_price_usefor_bodyusefor_id']=$row['bodycar_price_usefor_bodyusefor_id'];
        $record['bodycar_usefor_name']=$row['bodycar_usefor_name'];
        $record['bodycar_usefor_percent']=$row['bodycar_usefor_percent'];
            $record['bodycar_price_usefor_percent']=$row['bodycar_price_usefor_percent'];
            $record['bodycar_price_usefor_calmode_id']=$row['bodycar_price_usefor_calmode_id'];
        $output[]=$record;
        }
        echo json_encode(array('result'=>"ok"
        ,"data"=>$output
        ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    else
    if ($command=="delete_price_usefor")
    {
        $bodycar_price_usefor_id=$this->post('bodycar_price_usefor_id');
        $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
        if($employeetoken[0]=='ok')
        {
            $output =array();
            $result = $this->B_bodycar->del_bodycar_price_usefor($bodycar_price_usefor_id);
            if($result){echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'قیمت بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }else{
            echo json_encode(array('result'=>"error"
            ,"data"=>$output
            ,'desc'=>'قیمت بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }
        }else{
        echo json_encode(array('result'=>$employeetoken[0]
        ,"data"=>$employeetoken[1]
        ,'desc'=>$employeetoken[2]));

        }
    }
    else
    if ($command=="modify_price_usefor")
    {
    $bodycar_price_usefor_id=$this->post('bodycar_price_usefor_id');
    
    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','bodycar');
    if($employeetoken[0]=='ok')
    {
    $query="UPDATE bodycar_price_usefor_tb SET ";
    if(isset($_REQUEST['bodycar_price_usefor_percent'])){
    $bodycar_price_usefor_percent=$this->post('bodycar_price_usefor_percent');
    $query.="bodycar_price_usefor_percent='".$bodycar_price_usefor_percent."'";}

        if(isset($_REQUEST['bodycar_price_usefor_calmode_id'])&&(isset($_REQUEST['bodycar_price_usefor_percent']))){ $query.=",";}
        if(isset($_REQUEST['bodycar_price_usefor_calmode_id'])){
            $bodycar_price_usefor_calmode_id=$this->post('bodycar_price_usefor_calmode_id');
            $query.="bodycar_price_usefor_calmode_id=$bodycar_price_usefor_calmode_id";
        }

    $query.=" where bodycar_price_usefor_id=".$bodycar_price_usefor_id;
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
	//****************************************************************************************
	//****************************************************************************************
	//****************************************************************************************
    else
    if ($command=="add_price_discntlife")
    {
    $bodycar_price_discntlife_bodycar_price_id=$this->post('bodycar_price_discntlife_bodycar_price_id');
    $bodycar_price_discntlife_bodycar_discnt_life_id=$this->post('bodycar_price_discntlife_bodycar_discnt_life_id');
    $bodycar_price_discntlife_percent=$this->post('bodycar_price_discntlife_percent');
    $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
    if($employeetoken[0]=='ok')
    {
    $result=$this->B_bodycar->get_bodycar_price_discntlife_by($bodycar_price_discntlife_bodycar_discnt_life_id,$bodycar_price_discntlife_bodycar_price_id);
        if(empty($result))
    {
    $bodycar_price_discntlife_id=$this->B_bodycar->add_bodycar_price_discntlife($bodycar_price_discntlife_bodycar_price_id,$bodycar_price_discntlife_bodycar_discnt_life_id,$bodycar_price_discntlife_percent);
    echo json_encode(array('result'=>"ok"
    ,"data"=>array('bodycar_price_discntlife_id'=>$bodycar_price_discntlife_id)
    ,'desc'=>'پوشش مازاد مالی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }else{
    $carmode=$result[0];
    echo json_encode(array('result'=>"error"
    ,"data"=>array('bodycar_price_discntlife_id'=>$carmode['bodycar_price_discntlife_id'])
    ,'desc'=>' پوشش مازاد مالی نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    }else{
    echo json_encode(array('result'=>$employeetoken[0]
    ,"data"=>$employeetoken[1]
    ,'desc'=>$employeetoken[2]));
    
    }
    }
    else
    if ($command=="get_price_discntlife")
    {
        $bodycar_price_id=$this->post('bodycar_price_id');
        $result = $this->B_bodycar->tog_bodycar_price_discntlife($bodycar_price_id);
        $output =array();
        foreach($result as $row)
        {
        $record=array();
        $record['bodycar_price_discntlife_id']=$row['bodycar_price_discntlife_id'];
        $record['bodycar_price_discntlife_bodycar_price_id']=$row['bodycar_price_discntlife_bodycar_price_id'];
        $record['bodycar_price_discntlife_bodycar_discnt_life_id']=$row['bodycar_price_discntlife_bodycar_discnt_life_id'];
        $record['bodycar_discnt_life_company_name']=$row['bodycar_discnt_life_company_name'];
        $record['bodycar_discnt_life_percent']=$row['bodycar_discnt_life_percent'];
        $record['bodycar_price_discntlife_percent']=$row['bodycar_price_discntlife_percent'];
        $output[]=$record;
        }
        echo json_encode(array('result'=>"ok"
        ,"data"=>$output
        ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    else
    if ($command=="delete_price_discntlife")
    {
        $bodycar_price_discntlife_id=$this->post('bodycar_price_discntlife_id');
        $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
        if($employeetoken[0]=='ok')
        {
        $output =array();
        $result = $this->B_bodycar->del_bodycar_price_discntlife($bodycar_price_discntlife_id);
        if($result){echo json_encode(array('result'=>"ok"
        ,"data"=>$output
        ,'desc'=>'قیمت بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }else{
        echo json_encode(array('result'=>"error"
        ,"data"=>$output
        ,'desc'=>'قیمت بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
        }else{
        echo json_encode(array('result'=>$employeetoken[0]
        ,"data"=>$employeetoken[1]
        ,'desc'=>$employeetoken[2]));
    }
    }
    else
    if ($command=="modify_price_discntlife")
    {
    $bodycar_price_discntlife_id=$this->post('bodycar_price_discntlife_id');
    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','bodycar');
    if($employeetoken[0]=='ok')
    {
    $query="UPDATE bodycar_price_discntlife_tb SET ";
    if(isset($_REQUEST['bodycar_price_discntlife_percent'])){
    $bodycar_price_discntlife_percent=$this->post('bodycar_price_discntlife_percent');
    $query.="bodycar_price_discntlife_percent='".$bodycar_price_discntlife_percent."'";}
    $query.="where bodycar_price_discntlife_id=".$bodycar_price_discntlife_id;
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
    if ($command=="add_priceaccbank")
    {
    $bodycar_price_accbank_bodycar_price_id=$this->post('bodycar_price_accbank_bodycar_price_id');
    $bodycar_price_accbank_bodycar_discnt_accbank_id=$this->post('bodycar_price_accbank_bodycar_discnt_accbank_id');
    $bodycar_price_accbank_percent=$this->post('bodycar_price_accbank_percent');
    $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
    if($employeetoken[0]=='ok')
    {
        $result=$this->B_bodycar->get_bodycar_price_accbank_by($bodycar_price_accbank_bodycar_discnt_accbank_id,$bodycar_price_accbank_bodycar_price_id);
        if(empty($result))
        {
            $bodycar_price_accbank_id=$this->B_bodycar->add_bodycar_price_accbank($bodycar_price_accbank_bodycar_price_id,$bodycar_price_accbank_bodycar_discnt_accbank_id, $bodycar_price_accbank_percent);
            echo json_encode(array('result'=>"ok"
            ,"data"=>array('bodycar_price_accbank_id'=>$bodycar_price_accbank_id)
            ,'desc'=>'پوشش مازاد مالی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }else{
            $carmode=$result[0];
            echo json_encode(array('result'=>"error"
            ,"data"=>array('bodycar_price_accbank_id'=>$carmode['bodycar_price_accbank_id'])
            ,'desc'=>' پوشش مازاد مالی نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
    }else{
    echo json_encode(array('result'=>$employeetoken[0]
    ,"data"=>$employeetoken[1]
    ,'desc'=>$employeetoken[2]));
    }
    }
    else
    if ($command=="get_priceaccbank")
    {
    $bodycar_price_id=$this->post('bodycar_price_id');
    $result = $this->B_bodycar->tog_bodycar_price_accbank($bodycar_price_id);
    $output =array();
    foreach($result as $row)
    {
        $record=array();
        $record['bodycar_price_accbank_id']=$row['bodycar_price_accbank_id'];
        $record['bodycar_price_accbank_bodycar_price_id']=$row['bodycar_price_accbank_bodycar_price_id'];
        $record['bodycar_price_accbank_bodycar_discnt_accbank_id']=$row['bodycar_price_accbank_bodycar_discnt_accbank_id'];
        $record['bodycar_discnt_accbank_name']=$row['bodycar_discnt_accbank_name'];
        $record['bodycar_discnt_accbank_percent']=$row['bodycar_discnt_accbank_percent'];
        $record['bodycar_price_accbank_percent']=$row['bodycar_price_accbank_percent'];
        $output[]=$record;
    }
    echo json_encode(array('result'=>"ok"
    ,"data"=>$output
    ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    else
    if ($command=="delete_priceaccbank")
    {
    $bodycar_price_accbank_id=$this->post('bodycar_price_accbank_id');
    
    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
    if($employeetoken[0]=='ok')
    {
    $output =array();
    $result = $this->B_bodycar->del_bodycar_price_accbank($bodycar_price_accbank_id);
    if($result){echo json_encode(array('result'=>"ok"
    ,"data"=>$output
    ,'desc'=>'قیمت بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }else{
    echo json_encode(array('result'=>"error"
    ,"data"=>$output
    ,'desc'=>'قیمت بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    }else{
    echo json_encode(array('result'=>$employeetoken[0]
    ,"data"=>$employeetoken[1]
    ,'desc'=>$employeetoken[2]));
    }
    }
    else
    if ($command=="modify_priceaccbank")
    {
    $bodycar_price_accbank_id=$this->post('bodycar_price_accbank_id');
    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','bodycar');
    if($employeetoken[0]=='ok')
    {
    $query="UPDATE bodycar_price_accbank_tb SET ";
    if(isset($_REQUEST['bodycar_price_accbank_percent'])){
        $bodycar_price_accbank_percent=$this->post('bodycar_price_accbank_percent');
        $query.="bodycar_price_accbank_percent='".$bodycar_price_accbank_percent."'";}
    $query.="where bodycar_price_accbank_id=".$bodycar_price_accbank_id;
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
        if ($command=="add_priceanother")
        {
            $bodycar_price_another_bodycar_price_id=$this->post('bodycar_price_another_bodycar_price_id');
            $bodycar_price_another_bodycar_discnt_another_id=$this->post('bodycar_price_another_bodycar_discnt_another_id');
            $bodycar_price_another_percent=$this->post('bodycar_price_another_percent');

            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
            if($employeetoken[0]=='ok')
            {
//****************************************************************************************************************
                $query="select * from bodycar_price_another_tb where bodycar_price_another_bodycar_discnt_another_id=".$bodycar_price_another_bodycar_discnt_another_id." AND bodycar_price_another_bodycar_price_id=".$bodycar_price_another_bodycar_price_id."";
                $result=$this->B_db->run_query($query);
                if(empty($result))
                {
                    $query="INSERT INTO bodycar_price_another_tb(bodycar_price_another_bodycar_price_id,bodycar_price_another_bodycar_discnt_another_id, bodycar_price_another_percent)
	                            VALUES ( $bodycar_price_another_bodycar_price_id,$bodycar_price_another_bodycar_discnt_another_id, '$bodycar_price_another_percent');";
                    
                    $result=$this->B_db->run_query_put($query);
                    $bodycar_price_another_id=$this->db->insert_id();
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('bodycar_price_another_id'=>$bodycar_price_another_id)
                    ,'desc'=>'درصد تخفیف متفرقه بیمه نامه بدنه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    $carmode=$result[0];
                    echo json_encode(array('result'=>"error"
                    ,"data"=>array('bodycar_price_another_id'=>$carmode['bodycar_price_another_id'],'query'=>$query)
                    ,'desc'=>' درصد تخفیف متفرقه بیمه نامه بدنه نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
//***************************************************************************************************************
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));
            }
        }
        else
            if ($command=="get_priceanother")
            {
                $bodycar_price_id=$this->post('bodycar_price_id');

//************************************************************************;****************************************
                
                $query="select * from bodycar_price_another_tb,bodycar_discnt_another_tb where bodycar_discnt_another_id=bodycar_price_another_bodycar_discnt_another_id AND bodycar_price_another_bodycar_price_id=$bodycar_price_id  ORDER BY bodycar_price_another_id ASC";
                $result = $this->B_db->run_query($query);
                $output =array();
                foreach($result as $row)
                {
                    $record=array();
                    $record['bodycar_price_another_id']=$row['bodycar_price_another_id'];
                    $record['bodycar_price_another_bodycar_price_id']=$row['bodycar_price_another_bodycar_price_id'];
                    $record['bodycar_price_another_bodycar_discnt_another_id']=$row['bodycar_price_another_bodycar_discnt_another_id'];
                    $record['bodycar_discnt_another_name']=$row['bodycar_discnt_another_name'];
                    $record['bodycar_discnt_another_percent']=$row['bodycar_discnt_another_percent'];
                    $record['bodycar_price_another_percent']=$row['bodycar_price_another_percent'];
                    $output[]=$record;
                }
                echo json_encode(array('result'=>"ok"
                ,"data"=>$output
                ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************
                
            }
            else
                if ($command=="delete_priceanother")
                {
                    $bodycar_price_another_id=$this->post('bodycar_price_another_id');

                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
                    $output =array();
                    if($employeetoken[0]=='ok')
                    {
                        $user_id=$employeetoken[1];
                        $query="DELETE FROM bodycar_price_another_tb  where bodycar_price_another_id=".$bodycar_price_another_id."";
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
                    if ($command=="modify_priceanother")
                    {
                        $bodycar_price_another_id=$this->post('bodycar_price_another_id');

                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','bodycar');
                        if($employeetoken[0]=='ok')
                        {
                            $query="UPDATE bodycar_price_another_tb SET ";


                            if(isset($_REQUEST['bodycar_price_another_percent'])){
                                $bodycar_price_another_percent=$this->post('bodycar_price_another_percent');
                                $query.="bodycar_price_another_percent='".$bodycar_price_another_percent."'";}

                            $query.="where bodycar_price_another_id=".$bodycar_price_another_id;
                            
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
                        if ($command=="add_pricepriority")
                        {
                            $bodycar_price_priority_bodycar_price_id=$this->post('bodycar_price_priority_bodycar_price_id');
                            $bodycar_price_priority_bodycar_discnt_priority_id=$this->post('bodycar_price_priority_bodycar_discnt_priority_id');
                            $bodycar_price_priority_percent=$this->post('bodycar_price_priority_percent');

                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
                            if($employeetoken[0]=='ok')
                            {
//****************************************************************************************************************
                                $query="select * from bodycar_price_priority_tb where bodycar_price_priority_bodycar_discnt_priority_id=".$bodycar_price_priority_bodycar_discnt_priority_id." AND bodycar_price_priority_bodycar_price_id=".$bodycar_price_priority_bodycar_price_id."";
                                $result=$this->B_db->run_query($query);
                                if(empty($result))
                                {
                                    $query="INSERT INTO bodycar_price_priority_tb(bodycar_price_priority_bodycar_price_id,bodycar_price_priority_bodycar_discnt_priority_id, bodycar_price_priority_percent)
	                            VALUES ( $bodycar_price_priority_bodycar_price_id,$bodycar_price_priority_bodycar_discnt_priority_id, '$bodycar_price_priority_percent');";
                                    
                                    $result=$this->B_db->run_query_put($query);
                                    $bodycar_price_priority_id=$this->db->insert_id();
                                    echo json_encode(array('result'=>"ok"
                                    ,"data"=>array('bodycar_price_priority_id'=>$bodycar_price_priority_id)
                                    ,'desc'=>'اولویت تخفیف بیمه نامه بدنه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }else{
                                    $carmode=$result[0];
                                    echo json_encode(array('result'=>"error"
                                    ,"data"=>array('bodycar_price_priority_id'=>$carmode['bodycar_price_priority_id'],'query'=>$query)
                                    ,'desc'=>' اولویت تخفیف بیمه نامه بدنه نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }
//***************************************************************************************************************
                            }else{
                                echo json_encode(array('result'=>$employeetoken[0]
                                ,"data"=>$employeetoken[1]
                                ,'desc'=>$employeetoken[2]));

                            }



                            


                        }
                        else
                            if ($command=="get_pricepriority")
                            {
                                $bodycar_price_id=$this->post('bodycar_price_id');

//************************************************************************;****************************************
                                
                                $query="select * from bodycar_price_priority_tb,bodycar_discnt_priority_tb where bodycar_discnt_priority_id=bodycar_price_priority_bodycar_discnt_priority_id AND bodycar_price_priority_bodycar_price_id=$bodycar_price_id  ORDER BY bodycar_price_priority_id ASC";
                                $result = $this->B_db->run_query($query);
                                $output =array();
                                foreach($result as $row)
                                {
                                    $record=array();
                                    $record['bodycar_price_priority_id']=$row['bodycar_price_priority_id'];
                                    $record['bodycar_price_priority_bodycar_price_id']=$row['bodycar_price_priority_bodycar_price_id'];
                                    $record['bodycar_price_priority_bodycar_discnt_priority_id']=$row['bodycar_price_priority_bodycar_discnt_priority_id'];
                                    $record['bodycar_discnt_priority_name']=$row['bodycar_discnt_priority_name'];
                                    $record['bodycar_discnt_priority_percent']=$row['bodycar_discnt_priority_percent'];
                                    $record['bodycar_price_priority_percent']=$row['bodycar_price_priority_percent'];
                                    $output[]=$record;
                                }
                                echo json_encode(array('result'=>"ok"
                                ,"data"=>$output
                                ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
                            else
                                if ($command=="delete_pricepriority")
                                {
                                    $bodycar_price_priority_id=$this->post('bodycar_price_priority_id');

                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
                                    $output = array();
                                    if($employeetoken[0]=='ok')
                                    {
                                        $user_id=$employeetoken[1];
                                        $query="DELETE FROM bodycar_price_priority_tb  where bodycar_price_priority_id=".$bodycar_price_priority_id."";
                                        $result = $this->B_db->run_query_put($query);
                                        if($result){echo json_encode(array('result'=>"ok"
                                        ,"data"=>$output
                                        ,'desc'=>'قیمت بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                        }else{
                                            echo json_encode(array('result'=>"error"
                                            ,"data"=>$output
                                            ,'desc'=>'قیمت بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                        }
                                    }else{
                                        echo json_encode(array('result'=>$employeetoken[0]
                                        ,"data"=>$employeetoken[1]
                                        ,'desc'=>$employeetoken[2]));
                                    }
                                }
                                else
                                    if ($command=="modify_pricepriority")
                                    {
                                        $bodycar_price_priority_id=$this->post('bodycar_price_priority_id');
                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','bodycar');
                                        if($employeetoken[0]=='ok')
                                        {
                                            $query="UPDATE bodycar_price_priority_tb SET ";
                                            if(isset($_REQUEST['bodycar_price_priority_percent'])){
                                                $bodycar_price_priority_percent=$this->post('bodycar_price_priority_percent');
                                                $query.="bodycar_price_priority_percent='".$bodycar_price_priority_percent."'";}

                                            $query.="where bodycar_price_priority_id=".$bodycar_price_priority_id;
                                            
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
                                        if ($command=="add_pricethirdparty")
    {
    $bodycar_price_thirdparty_bodycar_price_id=$this->post('bodycar_price_thirdparty_bodycar_price_id');
    $bodycar_price_thirdparty_bodycar_discnt_thirdparty_id=$this->post('bodycar_price_thirdparty_bodycar_discnt_thirdparty_id');
    $bodycar_price_thirdparty_percent=$this->post('bodycar_price_thirdparty_percent');
    
    $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
    if($employeetoken[0]=='ok')
    {
        $result=$this->B_bodycar->get_bodycar_price_thirdparty_by($bodycar_price_thirdparty_bodycar_discnt_thirdparty_id,$bodycar_price_thirdparty_bodycar_price_id);
        if(empty($result))
        {
            $bodycar_price_thirdparty_id=$this->B_bodycar->add_bodycar_price_thirdparty($bodycar_price_thirdparty_bodycar_price_id,$bodycar_price_thirdparty_bodycar_discnt_thirdparty_id, $bodycar_price_thirdparty_percent);
            echo json_encode(array('result'=>"ok"
            ,"data"=>array('bodycar_price_thirdparty_id'=>$bodycar_price_thirdparty_id)
            ,'desc'=>'پوشش مازاد مالی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }else{
            $carmode=$result[0];
            echo json_encode(array('result'=>"error"
            ,"data"=>array('bodycar_price_thirdparty_id'=>$carmode['bodycar_price_thirdparty_id'])
            ,'desc'=>' پوشش مازاد مالی نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
    }else{
        echo json_encode(array('result'=>$employeetoken[0]
        ,"data"=>$employeetoken[1]
        ,'desc'=>$employeetoken[2]));
    
    }
    }
    else
    if ($command=="get_pricethirdparty")
    {
        $bodycar_price_id=$this->post('bodycar_price_id');
        $result = $this->B_bodycar->get_bodycar_price_thirdparty($bodycar_price_id);
        $output =array();
        foreach($result as $row)
        {
            $record=array();
            $record['bodycar_price_thirdparty_id']=$row['bodycar_price_thirdparty_id'];
            $record['bodycar_price_thirdparty_bodycar_price_id']=$row['bodycar_price_thirdparty_bodycar_price_id'];
            $record['bodycar_price_thirdparty_bodycar_discnt_thirdparty_id']=$row['bodycar_price_thirdparty_bodycar_discnt_thirdparty_id'];
            $record['bodycar_discnt_thirdparty_name']=$row['bodycar_discnt_thirdparty_name'];
            $record['bodycar_discnt_thirdparty_digt']=$row['bodycar_discnt_thirdparty_digt'];
            $record['bodycar_price_thirdparty_percent']=$row['bodycar_price_thirdparty_percent'];
            $output[]=$record;
        }
        echo json_encode(array('result'=>"ok"
        ,"data"=>$output
        ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    else
        if ($command=="delete_pricethirdparty")
        {
            $bodycar_price_thirdparty_id=$this->post('bodycar_price_thirdparty_id');
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
            if($employeetoken[0]=='ok')
            {
                $output =array();
                $result = $this->B_bodycar->del_bodycar_price_thirdparty($bodycar_price_thirdparty_id);
                if($result){echo json_encode(array('result'=>"ok"
                ,"data"=>$output
                ,'desc'=>'قیمت بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    echo json_encode(array('result'=>"error"
                    ,"data"=>$output
                    ,'desc'=>'قیمت بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));
    
            }
        }
        else
            if ($command=="modify_pricethirdparty")
            {
                $bodycar_price_thirdparty_id=$this->post('bodycar_price_thirdparty_id');
                $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','bodycar');
                if($employeetoken[0]=='ok')
                {
                    $query="UPDATE bodycar_price_thirdparty_tb SET ";
                    if(isset($_REQUEST['bodycar_price_thirdparty_percent'])){
                        $bodycar_price_thirdparty_percent=$this->post('bodycar_price_thirdparty_percent');
                        $query.="bodycar_price_thirdparty_percent='".$bodycar_price_thirdparty_percent."'";}
                    $query.="where bodycar_price_thirdparty_id=".$bodycar_price_thirdparty_id;
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

//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//************************************************************************************************************************************************************
//***************************************************************************************************************
            else
                if ($command=="add_pricediscbody")
                {
                    $bodycar_price_discbody_bodycar_price_id=$this->post('bodycar_price_discbody_bodycar_price_id');
                    $bodycar_price_discbody_bodycar_discnt_id=$this->post('bodycar_price_discbody_bodycar_discnt_id');
                    $bodycar_price_discbody_percent=$this->post('bodycar_price_discbody_percent');

                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
                    if($employeetoken[0]=='ok')
                    {
                        $result=$this->B_bodycar->get_bodycar_price_discbody_by($bodycar_price_discbody_bodycar_discnt_id,$bodycar_price_discbody_bodycar_price_id);
                        if(empty($result))
                        {
                            $bodycar_price_discbody_id=$this->B_bodycar->add_bodycar_price_discbody($bodycar_price_discbody_bodycar_price_id,$bodycar_price_discbody_bodycar_discnt_id, $bodycar_price_discbody_percent);
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>array('bodycar_price_discbody_id'=>$bodycar_price_discbody_id)
                            ,'desc'=>'پوشش مازاد مالی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            $carmode=$result[0];
                            echo json_encode(array('result'=>"error"
                            ,"data"=>array('bodycar_price_discbody_id'=>$carmode['bodycar_price_discbody_id'])
                            ,'desc'=>' پوشش مازاد مالی نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]));

                    }
                }
                else
                    if ($command=="get_pricediscbody")
                    {
                        $bodycar_price_id=$this->post('bodycar_price_id');
                        $result = $this->B_bodycar->get_bodycar_price_discbody($bodycar_price_id);
                        $output =array();
                        foreach($result as $row)
                        {
                            $record=array();
                            $record['bodycar_price_discbody_id']=$row['bodycar_price_discbody_id'];
                            $record['bodycar_price_discbody_bodycar_price_id']=$row['bodycar_price_discbody_bodycar_price_id'];
                            $record['bodycar_price_discbody_bodycar_discnt_id']=$row['bodycar_price_discbody_bodycar_discnt_id'];
                            $record['bodycar_discnt_name']=$row['bodycar_discnt_name'];
                            $record['bodycar_discnt_digt']=$row['bodycar_discnt_digt'];
                            $record['bodycar_price_discbody_percent']=$row['bodycar_price_discbody_percent'];
                            $output[]=$record;
                        }
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>$output
                        ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                    else
                        if ($command=="delete_pricediscbody")
                        {
                            $bodycar_price_discbody_id=$this->post('bodycar_price_discbody_id');
                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
                            if($employeetoken[0]=='ok')
                            {
                                $output =array();
                                $result = $this->B_bodycar->del_bodycar_price_discbody($bodycar_price_discbody_id);
                                if($result){echo json_encode(array('result'=>"ok"
                                ,"data"=>$output
                                ,'desc'=>'قیمت بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }else{
                                    echo json_encode(array('result'=>"error"
                                    ,"data"=>$output
                                    ,'desc'=>'قیمت بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }
                            }else{
                                echo json_encode(array('result'=>$employeetoken[0]
                                ,"data"=>$employeetoken[1]
                                ,'desc'=>$employeetoken[2]));

                            }
                        }
                        else
                            if ($command=="modify_pricediscbody")
                            {
                                $bodycar_price_discbody_id=$this->post('bodycar_price_discbody_id');
                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','bodycar');
                                if($employeetoken[0]=='ok')
                                {
                                    $query="UPDATE bodycar_price_discbody_tb SET ";
                                    if(isset($_REQUEST['bodycar_price_discbody_percent'])){
                                        $bodycar_price_discbody_percent=$this->post('bodycar_price_discbody_percent');
                                        $query.="bodycar_price_discbody_percent='".$bodycar_price_discbody_percent."'";}
                                    $query.="where bodycar_price_discbody_id=".$bodycar_price_discbody_id;
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
                            //*********************************************
                            //*********************************************
                            //*********************************************
            else
                if ($command=="add_price_exceptions")
                {
                    $bodycar_price_exceptions_bodycar_price_id=$this->post('bodycar_price_exceptions_bodycar_price_id');
                    $bodycar_price_exceptions_car_id=$this->post('bodycar_price_exceptions_car_id');
    
                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
                    if($employeetoken[0]=='ok')
                    {
                        $result=$this->B_bodycar->get_bodycar_price_exceptions_by($bodycar_price_exceptions_car_id,$bodycar_price_exceptions_bodycar_price_id);
                        if(empty($result))
                        {
                            $bodycar_price_exceptions_id=$this->B_bodycar->add_bodycar_price_exceptions($bodycar_price_exceptions_bodycar_price_id,$bodycar_price_exceptions_car_id);
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>array('bodycar_price_exceptions_id'=>$bodycar_price_exceptions_id)
                            ,'desc'=>'پوشش مازاد مالی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            $carmode=$result[0];
                            echo json_encode(array('result'=>"error"
                            ,"data"=>array('bodycar_price_exceptions_id'=>$carmode['bodycar_price_exceptions_id'],'query'=>'query')
                            ,'desc'=>' پوشش مازاد مالی نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]));
                    }
                }
                else
                    if ($command=="get_price_exceptions")
                    {
                        $bodycar_price_id=$this->post('bodycar_price_id');
                        $result = $this->B_bodycar->get_bodycar_price_exceptions($bodycar_price_id);
                        $output =array();
                        foreach($result as $row)
                        {
                            $record=array();
                            $record['bodycar_price_exceptions_id']=$row['bodycar_price_exceptions_id'];
                            $record['bodycar_price_exceptions_bodycar_price_id']=$row['bodycar_price_exceptions_bodycar_price_id'];
                            $record['bodycar_price_exceptions_car_id']=$row['bodycar_price_exceptions_car_id'];
                            $record['car_name']=$row['car_name'];
                            $output[]=$record;
                        }
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>$output
                        ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                    else
                        if ($command=="delete_price_exceptions")
                        {
                            $bodycar_price_exceptions_id=$this->post('bodycar_price_exceptions_id');
    
                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
                            if($employeetoken[0]=='ok')
                            {
                                $output =array();
                                $result = $this->B_bodycar->del_bodycar_price_exceptions($bodycar_price_exceptions_id);
                                if($result){echo json_encode(array('result'=>"ok"
                                ,"data"=>$output
                                ,'desc'=>'قیمت بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }else{
                                    echo json_encode(array('result'=>"error"
                                    ,"data"=>$output
                                    ,'desc'=>'قیمت بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }
                            }else{
                                echo json_encode(array('result'=>$employeetoken[0]
                                ,"data"=>$employeetoken[1]
                                ,'desc'=>$employeetoken[2]));
                            }
                        }
            else
                if ($command=="add_price_extracar")
                {
                    $bodycar_price_extracar_bodycar_price_id=$this->post('bodycar_price_extracar_bodycar_price_id');
                    $bodycar_price_extracar_car_id=$this->post('bodycar_price_extracar_car_id');
    
                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','bodycar');
                    if($employeetoken[0]=='ok')
                    {
                        $result=$this->B_bodycar->get_bodycar_price_extracar_by($bodycar_price_extracar_car_id,$bodycar_price_extracar_bodycar_price_id);
                        if(empty($result))
                        {
                            $bodycar_price_extracar_id=$this->B_bodycar->add_bodycar_price_extracar($bodycar_price_extracar_bodycar_price_id,$bodycar_price_extracar_car_id);
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>array('bodycar_price_extracar_id'=>$bodycar_price_extracar_id)
                            ,'desc'=>'پوشش مازاد مالی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            $carmode=$result[0];
                            echo json_encode(array('result'=>"error"
                            ,"data"=>array('bodycar_price_extracar_id'=>$carmode['bodycar_price_extracar_id'],'query'=>'query')
                            ,'desc'=>' پوشش مازاد مالی نامه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]));
                    }
                }
                else
                    if ($command=="get_price_extracar")
                    {
                        $bodycar_price_id=$this->post('bodycar_price_id');
                        $result = $this->B_bodycar->get_bodycar_price_extracar($bodycar_price_id);
                        $output =array();
                        foreach($result as $row)
                        {
                            $record=array();
                            $record['bodycar_price_extracar_id']=$row['bodycar_price_extracar_id'];
                            $record['bodycar_price_extracar_bodycar_price_id']=$row['bodycar_price_extracar_bodycar_price_id'];
                            $record['bodycar_price_extracar_car_id']=$row['bodycar_price_extracar_car_id'];
                            $record['car_name']=$row['car_name'];
                            $output[]=$record;
                        }
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>$output
                        ,'desc'=>'قیمت بیمه نامه با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                    else
                        if ($command=="delete_price_extracar")
                        {
                            $bodycar_price_extracar_id=$this->post('bodycar_price_extracar_id');
    
                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','bodycar');
                            if($employeetoken[0]=='ok')
                            {
                                $output =array();
                                $result = $this->B_bodycar->del_bodycar_price_extracar($bodycar_price_extracar_id);
                                if($result){echo json_encode(array('result'=>"ok"
                                ,"data"=>$output
                                ,'desc'=>'قیمت بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }else{
                                    echo json_encode(array('result'=>"error"
                                    ,"data"=>$output
                                    ,'desc'=>'قیمت بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }
                            }else{
                                echo json_encode(array('result'=>$employeetoken[0]
                                ,"data"=>$employeetoken[1]
                                ,'desc'=>$employeetoken[2]));
                            }
                        }

    }
}
}