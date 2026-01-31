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
class Kindsendrequest extends REST_Controller {

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
        if($this->B_user->checkrequestip('kindsendrequest',$command,get_client_ip(),50,50)){
        if ($command=="add_kindsendrequest")
        {


            $kindsendrequest_state_id=$this->post('kindsendrequest_state_id');

            $kindsendrequest_city_id=$this->post('kindsendrequest_city_id');

            $kindsendrequest_kind_id=$this->post('kindsendrequest_kind_id');

            $kindsendrequest_agent_id=$this->post('kindsendrequest_agent_id');

            $kindsendrequest_company_id=$this->post('kindsendrequest_company_id');

            $kindsendrequest_fieldinsurance_id=$this->post('kindsendrequest_fieldinsurance_id');





            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','kindsendrequest');
            if($employeetoken[0]=='ok')
            {
//************************************************************************;****************************************
                $query="select * from kindsendrequest_tb where kindsendrequest_state_id=".$kindsendrequest_state_id." AND kindsendrequest_city_id=".$kindsendrequest_city_id."  AND kindsendrequest_company_id=".$kindsendrequest_company_id." AND kindsendrequest_fieldinsurance_id=".$kindsendrequest_fieldinsurance_id."";
                $result=$this->B_db->run_query($query);
                $num=count($result[0]);
                if ($num==0)
                {
                    $query1="INSERT INTO kindsendrequest_tb(kindsendrequest_state_id,  kindsendrequest_city_id,  kindsendrequest_kind_id,  kindsendrequest_agent_id  ,  kindsendrequest_company_id ,  kindsendrequest_fieldinsurance_id)
	                                 VALUES ($kindsendrequest_state_id, $kindsendrequest_city_id,$kindsendrequest_kind_id,$kindsendrequest_agent_id, $kindsendrequest_company_id, $kindsendrequest_fieldinsurance_id);";

                    $result1=$this->B_db->run_query_put($query1);
                    $kindsendrequest_id=$this->db->insert_id();




                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('kindsendrequest_id'=>$kindsendrequest_id)
                    ,'desc'=>'نوع ارسال در منطقه مورد نظر اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    $carmode=$result[0];
                    echo json_encode(array('result'=>"error"
                    ,"data"=>array('kindsendrequest_id'=>$carmode['kindsendrequest_id'])
                    ,'desc'=>'نوع ارسال در منطقه مورد نظر تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
//***************************************************************************************************************
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }






        }
        else
            if ($command=="get_sendrequest_kind")
            {

                $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','kindsendrequest');
                if($employeetoken[0]=='ok')
                {
//***************************************************************************************************************

                    $query="select * from sendrequest_kind_tb";
                    $result = $this->B_db->run_query($query);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record['sendrequest_kind_id']=$row['sendrequest_kind_id'];
                        $record['sendrequest_kind_name']=$row['sendrequest_kind_name'];

                        $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'مشحصات نوع ارسال با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************
                }else{
                    echo json_encode(array('result'=>$employeetoken[0]
                    ,"data"=>$employeetoken[1]
                    ,'desc'=>$employeetoken[2]));

                }


            }
            else
                if ($command=="get_kindsendrequest")
                {

                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','kindsendrequest');
                    if($employeetoken[0]=='ok')
                    {

//************************************************************************;****************************************

                        $query="select * from kindsendrequest_tb,company_tb,agent_tb,sendrequest_kind_tb where kindsendrequest_kind_id=sendrequest_kind_id AND kindsendrequest_agent_id=agent_id AND company_id=kindsendrequest_company_id AND ";
                        if(isset($_REQUEST['kindsendrequest_state_id'])){
                            $kindsendrequest_state_id=$this->post('kindsendrequest_state_id') ;
                            $query.=" kindsendrequest_state_id=$kindsendrequest_state_id ";
                        }else{$query.=" 1=1 ";}
                        $query.=" AND ";
                        if(isset($_REQUEST['kindsendrequest_city_id'])){
                            $kindsendrequest_city_id=$this->post('kindsendrequest_city_id') ;
                            $query.=" kindsendrequest_city_id=$kindsendrequest_city_id ";
                        }else{$query.=" 1=1 ";}
                        $query.=" AND ";
                        if(isset($_REQUEST['kindsendrequest_company_id'])){
                            $kindsendrequest_company_id=$this->post('kindsendrequest_company_id') ;
                            $query.=" kindsendrequest_company_id=$kindsendrequest_company_id ";
                        }else{$query.=" 1=1 ";}

                        $query.=" ORDER BY kindsendrequest_id ASC";

                        $result = $this->B_db->run_query($query);
                        $output =array();
                        foreach($result as $row)
                        {
                            $record=array();
                            $record['kindsendrequest_id']=$row['kindsendrequest_id'];
                            $record['company_id']=$row['company_id'];
                            $record['agent_company_name']=$row['company_name'];
                            $record['agent_company_logo_url']=IMGADD.$row['company_logo_url'];
                            $record['kindsendrequest_state_id']=$row['kindsendrequest_state_id'];
                            $record['kindsendrequest_city_id']=$row['kindsendrequest_city_id'];
                            $record['kindsendrequest_fieldinsurance_id']=$row['kindsendrequest_fieldinsurance_id'];

                            if($record['kindsendrequest_city_id']=='0'){
                                $record['agent_city_name']='همه شهر ها';
                            }else{
                                $query1=" SELECT * FROM city_tb WHERE city_id=".$row['kindsendrequest_city_id']."";
                                $result1=$this->B_db->run_query($query1);
                                $city=$result1[0];

                                $record['agent_city_name']=$city['city_name'];
                            }

                            if($record['kindsendrequest_state_id']=='0'){
                                $record['agent_state_name']='همه استان ها';
                            }else{
                                $query1=" SELECT * FROM state_tb WHERE state_id=".$row['kindsendrequest_state_id']."";
                                $result1=$this->B_db->run_query($query1);
                                $state=$result1[0];

                                $record['agent_state_name']=$state['state_name'];
                            }

                            if($record['kindsendrequest_fieldinsurance_id']=='0'){
                                $record['fieldinsurance_name']='همه رشته ها';
                            }else{
                                $query1=" SELECT * FROM fieldinsurance_tb WHERE fieldinsurance_id=".$row['kindsendrequest_fieldinsurance_id']."";
                                $result1=$this->B_db->run_query($query1);
                                $fieldinsurance=$result1[0];

                                $record['fieldinsurance_name']=$fieldinsurance['fieldinsurance_fa'];
                                $record['fieldinsurance_logo_url']=IMGADD.$fieldinsurance['fieldinsurance_logo_url'];
                            }

                            $record['kindsendrequest_kind_id']=$row['kindsendrequest_kind_id'];
                            $record['sendrequest_kind_name']=$row['sendrequest_kind_name'];
                            $record['kindsendrequest_agent_id']=$row['kindsendrequest_agent_id'];
                            $record['agent_name']=$row['agent_name'];
                            $record['agent_family']=$row['agent_family'];
                            $record['agent_gender']=$row['agent_gender'];
                            $record['agent_mobile']=$row['agent_mobile'];
                            $record['agent_code']=$row['agent_code'];

                            $output[]=$record;
                        }
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>$output
                        ,'desc'=>"نوع توزیع درخواست ها با موفقیت ارسال شد"));
//***************************************************************************************************************
                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]));

                    }

                }
                else
                    if ($command=="delete_kindsendrequest")
                    {
                        $kindsendrequest_id=$this->post('kindsendrequest_id');
                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','kindsendrequest');
                        if($employeetoken[0]=='ok')
                        {
                            $output = array();$user_id=$employeetoken[0];
                            $query="DELETE FROM kindsendrequest_tb  where kindsendrequest_id=".$kindsendrequest_id."";
                            $result = $this->B_db->run_query_put($query);
                            if($result){echo json_encode(array('result'=>"ok"
                            ,"data"=>$output
                            ,'desc'=>'نماینده مورد نظر حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }else{
                                echo json_encode(array('result'=>"error"
                                ,"data"=>$output
                                ,'desc'=>'نماینده مورد نظر حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
//***************************************************************************************************************
                        }else{
                            echo json_encode(array('result'=>$employeetoken[0]
                            ,"data"=>$employeetoken[1]
                            ,'desc'=>$employeetoken[2]));

                        }
                    }
                    else
                        if ($command=="modify_kindsendrequest") {
                            $kindsendrequest_id = $this->post('kindsendrequest_id');

                            $employeetoken = checkpermissionemployeetoken($employee_token_str, 'modify', 'kindsendrequest');
                            if ($employeetoken[0] == 'ok') {
//*****************************************************************************************

                                $query="UPDATE kindsendrequest_tb SET ";
                                if (isset($_REQUEST['kindsendrequest_state_id'])) {
                                    $kindsendrequest_state_id = $this->post('kindsendrequest_state_id');
                                    $query .= "kindsendrequest_state_id=" . $kindsendrequest_state_id . " ";
                                }

                                if (isset($_REQUEST['kindsendrequest_city_id']) && (isset($_REQUEST['kindsendrequest_state_id']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['kindsendrequest_city_id'])) {
                                    $kindsendrequest_city_id = $this->post('kindsendrequest_city_id');
                                    $query .= "kindsendrequest_city_id=" . $kindsendrequest_city_id . " ";
                                }

                                if (isset($_REQUEST['kindsendrequest_kind_id']) && (isset($_REQUEST['kindsendrequest_state_id']) || isset($_REQUEST['kindsendrequest_city_id']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['kindsendrequest_kind_id'])) {
                                    $kindsendrequest_kind_id = $this->post('kindsendrequest_kind_id');
                                    $query .= "kindsendrequest_kind_id=" . $kindsendrequest_kind_id . " ";
                                }

                                if (isset($_REQUEST['kindsendrequest_agent_id']) && (isset($_REQUEST['kindsendrequest_kind_id']) || isset($_REQUEST['kindsendrequest_state_id']) || isset($_REQUEST['kindsendrequest_city_id']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['kindsendrequest_agent_id'])) {
                                    $kindsendrequest_agent_id = $this->post('kindsendrequest_agent_id');
                                    $query .= "kindsendrequest_agent_id=" . $kindsendrequest_agent_id . " ";
                                }

                                if (isset($_REQUEST['kindsendrequest_company_id']) && (isset($_REQUEST['kindsendrequest_agent_id']) || isset($_REQUEST['kindsendrequest_kind_id']) || isset($_REQUEST['kindsendrequest_state_id']) || isset($_REQUEST['kindsendrequest_city_id']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['kindsendrequest_company_id'])) {
                                    $kindsendrequest_company_id = $this->post('kindsendrequest_company_id');
                                    $query .= "kindsendrequest_company_id=" . $kindsendrequest_company_id . " ";
                                }

                                if (isset($_REQUEST['kindsendrequest_fieldinsurance_id']) && (isset($_REQUEST['kindsendrequest_company_id']) || isset($_REQUEST['kindsendrequest_agent_id']) || isset($_REQUEST['kindsendrequest_kind_id']) || isset($_REQUEST['kindsendrequest_state_id']) || isset($_REQUEST['kindsendrequest_city_id']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['kindsendrequest_fieldinsurance_id'])) {
                                    $kindsendrequest_fieldinsurance_id = $this->post('kindsendrequest_fieldinsurance_id');
                                    $query .= "kindsendrequest_fieldinsurance_id=" . $kindsendrequest_fieldinsurance_id . " ";
                                }

                                $query .= "where kindsendrequest_id=" . $kindsendrequest_id;

                                $result = $this->B_db->run_query_put($query);


                                if ($result) {
                                    echo json_encode(array('result' => "ok"
                                    , "data" => ""
                                    , 'desc' => ' تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                } else {
                                    echo json_encode(array('result' => "ok"
                                    , "data" => ""
                                    , 'desc' => 'تغییرات انجام نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }
                            } else {
                                echo json_encode(array('result' => $employeetoken[0]
                                , "data" => $employeetoken[1]
                                , 'desc' => $employeetoken[2]));

                            }


                        }
                        }
}
}