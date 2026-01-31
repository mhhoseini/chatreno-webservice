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
class Kindorgansenddamagefile extends REST_Controller {

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
        $this->load->model('B_expert');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if($this->B_user->checkrequestip('kindorgansenddamagefile',$command,get_client_ip(),50,50)){
            if ($command=="add_kindorgansenddamagefile")
            {


                $kindorgansenddamagefile_state_id=$this->post('kindorgansenddamagefile_state_id');

                $kindorgansenddamagefile_city_id=$this->post('kindorgansenddamagefile_city_id');

                $kindorgansenddamagefile_kind_id=$this->post('kindorgansenddamagefile_kind_id');

                $kindorgansenddamagefile_expert_id=$this->post('kindorgansenddamagefile_expert_id');

                $kindorgansenddamagefile_evaluatorco_id=$this->post('kindorgansenddamagefile_evaluatorco_id');

                $kindorgansenddamagefile_organ_id=$this->post('kindorgansenddamagefile_organ_id');

                $kindorgansenddamagefile_contract_id=$this->post('kindorgansenddamagefile_contract_id');

                $kindorgansenddamagefile_fielddamagefile_id=$this->post('kindorgansenddamagefile_fielddamagefile_id');





                $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','kindsenddamagefile');
                if($employeetoken[0]=='ok')
                {
//************************************************************************;****************************************
                    $query="select * from kindorgansenddamagefile_tb where kindorgansenddamagefile_state_id=".$kindorgansenddamagefile_state_id." AND kindorgansenddamagefile_city_id=".$kindorgansenddamagefile_city_id."  AND kindorgansenddamagefile_evaluatorco_id=".$kindorgansenddamagefile_evaluatorco_id." AND kindorgansenddamagefile_organ_id=".$kindorgansenddamagefile_organ_id." AND kindorgansenddamagefile_contract_id=".$kindorgansenddamagefile_contract_id." AND kindorgansenddamagefile_fielddamagefile_id=".$kindorgansenddamagefile_fielddamagefile_id."";

                    $result=$this->B_db->run_query($query);
                    $num=count($result[0]);
                    if ($num==0)
                    {
                        $query1="INSERT INTO kindorgansenddamagefile_tb(kindorgansenddamagefile_state_id,  kindorgansenddamagefile_city_id,  kindorgansenddamagefile_kind_id,  kindorgansenddamagefile_expert_id  ,  kindorgansenddamagefile_evaluatorco_id , kindorgansenddamagefile_organ_id, kindorgansenddamagefile_contract_id ,  kindorgansenddamagefile_fielddamagefile_id)
                                             VALUES ($kindorgansenddamagefile_state_id,    $kindorgansenddamagefile_city_id, $kindorgansenddamagefile_kind_id, $kindorgansenddamagefile_expert_id,  $kindorgansenddamagefile_evaluatorco_id,  $kindorgansenddamagefile_organ_id, $kindorgansenddamagefile_contract_id, $kindorgansenddamagefile_fielddamagefile_id);";
                        $result1=$this->B_db->run_query_put($query1);
                        $kindorgansenddamagefile_id=$this->db->insert_id();



                        echo json_encode(array('result'=>"ok"
                        ,"data"=>array('kindorgansenddamagefile_id'=>$kindorgansenddamagefile_id)
                        ,'desc'=>'نوع ارسال در منطقه مورد نظر اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);


                    }else{
                        $carmode=$result[0];
                        echo json_encode(array('result'=>"error"
                        ,"data"=>array('kindorgansenddamagefile_id'=>$carmode['kindorgansenddamagefile_id'])
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
                if ($command=="get_senddamagefile_kind")
                {

                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','kindsenddamagefile');
                    if($employeetoken[0]=='ok')
                    {
//***************************************************************************************************************

                        $query="select * from senddamagefile_kind_tb";
                        $result = $this->B_db->run_query($query);
                        $output =array();
                        foreach($result as $row)
                        {
                            $record['senddamagefile_kind_id']=$row['senddamagefile_kind_id'];
                            $record['senddamagefile_kind_name']=$row['senddamagefile_kind_name'];

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
                    if ($command=="get_kindorgansenddamagefile")
                    {

                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','kindsenddamagefile');
                        if($employeetoken[0]=='ok')
                        {

//************************************************************************;****************************************

                            $query="select * from kindorgansenddamagefile_tb,evaluatorco_tb,expert_tb,senddamagefile_kind_tb where kindorgansenddamagefile_kind_id=senddamagefile_kind_id AND kindorgansenddamagefile_expert_id=expert_id AND evaluatorco_id=kindorgansenddamagefile_evaluatorco_id AND ";
                            if(isset($_REQUEST['kindorgansenddamagefile_state_id'])){
                                $kindorgansenddamagefile_state_id=$this->post('kindorgansenddamagefile_state_id') ;
                                $query.=" kindorgansenddamagefile_state_id=$kindorgansenddamagefile_state_id ";
                            }else{$query.=" 1=1 ";}
                            $query.=" AND ";
                            if(isset($_REQUEST['kindorgansenddamagefile_city_id'])){
                                $kindorgansenddamagefile_city_id=$this->post('kindorgansenddamagefile_city_id') ;
                                $query.=" kindorgansenddamagefile_city_id=$kindorgansenddamagefile_city_id ";
                            }else{$query.=" 1=1 ";}
                            $query.=" AND ";
                            if(isset($_REQUEST['kindorgansenddamagefile_evaluatorco_id'])){
                                $kindorgansenddamagefile_evaluatorco_id=$this->post('kindorgansenddamagefile_evaluatorco_id') ;
                                $query.=" kindorgansenddamagefile_evaluatorco_id=$kindorgansenddamagefile_evaluatorco_id ";
                            }else{$query.=" 1=1 ";}

                            $query.=" AND ";
                            if(isset($_REQUEST['kindorgansenddamagefile_organ_id'])){
                                $kindorgansenddamagefile_organ_id=$this->post('kindorgansenddamagefile_organ_id') ;
                                $query.=" kindorgansenddamagefile_organ_id=$kindorgansenddamagefile_organ_id ";
                            }else{$query.=" 1=1 ";}

                            $query.=" AND ";
                            if(isset($_REQUEST['kindorgansenddamagefile_contract_id'])){
                                $kindorgansenddamagefile_contract_id=$this->post('kindorgansenddamagefile_contract_id') ;
                                $query.=" kindorgansenddamagefile_contract_id=$kindorgansenddamagefile_contract_id ";
                            }else{$query.=" 1=1 ";}


                            $query.=" ORDER BY kindorgansenddamagefile_id ASC";

                            $result = $this->B_db->run_query($query);
                            $output =array();
                            foreach($result as $row)
                            {
                                $record=array();
                                $record['kindorgansenddamagefile_id']=$row['kindorgansenddamagefile_id'];
                                $record['evaluatorco_id']=$row['evaluatorco_id'];
                                $record['expert_evaluatorco_name']=$row['evaluatorco_name'];
                                $record['expert_evaluatorco_logo_url']=IMGADD.$row['evaluatorco_logo_url'];
                                $record['kindorgansenddamagefile_state_id']=$row['kindorgansenddamagefile_state_id'];
                                $record['kindorgansenddamagefile_city_id']=$row['kindorgansenddamagefile_city_id'];
                                $record['kindorgansenddamagefile_fielddamagefile_id']=$row['kindorgansenddamagefile_fielddamagefile_id'];
                                $record['kindorgansenddamagefile_organ_id']=$row['kindorgansenddamagefile_organ_id'];
                                $record['kindorgansenddamagefile_contract_id']=$row['kindorgansenddamagefile_contract_id'];

                                if($record['kindorgansenddamagefile_city_id']=='0'){
                                    $record['expert_city_name']='همه شهر ها';
                                }else{
                                    $query1=" SELECT * FROM city_tb WHERE city_id=".$row['kindorgansenddamagefile_city_id']."";
                                    $result1=$this->B_db->run_query($query1);
                                    $city=$result1[0];

                                    $record['expert_city_name']=$city['city_name'];
                                }

                                if($record['kindorgansenddamagefile_state_id']=='0'){
                                    $record['expert_state_name']='همه استان ها';
                                }else{
                                    $query1=" SELECT * FROM state_tb WHERE state_id=".$row['kindorgansenddamagefile_state_id']."";
                                    $result1=$this->B_db->run_query($query1);
                                    $state=$result1[0];

                                    $record['expert_state_name']=$state['state_name'];
                                }

                                if($record['kindorgansenddamagefile_fielddamagefile_id']=='0'){
                                    $record['fielddamagefile_name']='همه رشته ها';
                                }else{
                                    $query1=" SELECT * FROM fielddamagefile_tb WHERE fielddamagefile_id=".$row['kindorgansenddamagefile_fielddamagefile_id']."";
                                    $result1=$this->B_db->run_query($query1);
                                    $fielddamagefile=$result1[0];

                                    $record['fielddamagefile_name']=$fielddamagefile['fielddamagefile_fa'];
                                    $record['fielddamagefile_logo_url']=IMGADD.$fielddamagefile['fielddamagefile_logo_url'];
                                }

                                if($record['kindorgansenddamagefile_organ_id']=='0'){
                                    $record['organ_name']='همه سازمان ها ';
                                }else{
                                    $query1=" SELECT * FROM organ_tb WHERE organ_id=".$row['kindorgansenddamagefile_organ_id']."";
                                    $result1=$this->B_db->run_query($query1);
                                    $state=$result1[0];

                                    $record['organ_name']=$state['organ_name'];
                                }

                                if($record['kindorgansenddamagefile_contract_id']=='0'){
                                    $record['organ_contract_num']='همه قرارداد ها ';
                                }else{
                                    $query1=" SELECT * FROM organ_contract_tb WHERE organ_contract_id=".$row['kindorgansenddamagefile_contract_id']."";
                                    $result1=$this->B_db->run_query($query1);
                                    $state=$result1[0];

                                    $record['organ_contract_num']=$state['organ_contract_num'];
                                }


                                $record['kindorgansenddamagefile_kind_id']=$row['kindorgansenddamagefile_kind_id'];
                                $record['senddamagefile_kind_name']=$row['senddamagefile_kind_name'];
                                $record['kindorgansenddamagefile_expert_id']=$row['kindorgansenddamagefile_expert_id'];
                                $record['expert_name']=$row['expert_name'];
                                $record['expert_family']=$row['expert_family'];
                                $record['expert_gender']=$row['expert_gender'];
                                $record['expert_mobile']=$row['expert_mobile'];
                                $record['expert_code']=$row['expert_code'];

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
                        if ($command=="delete_kindorgansenddamagefile")
                        {
                            $kindorgansenddamagefile_id=$this->post('kindorgansenddamagefile_id');
                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','kindsenddamagefile');
                            if($employeetoken[0]=='ok')
                            {
                                $output = array();$user_id=$employeetoken[0];
                                $query="DELETE FROM kindorgansenddamagefile_tb  where kindorgansenddamagefile_id=".$kindorgansenddamagefile_id."";
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
                            if ($command=="modify_kindorgansenddamagefile") {
                                $kindorgansenddamagefile_id = $this->post('kindorgansenddamagefile_id');

                                $employeetoken = checkpermissionemployeetoken($employee_token_str, 'modify', 'kindsenddamagefile');
                                if ($employeetoken[0] == 'ok') {
//*****************************************************************************************

                                    $query="UPDATE kindorgansenddamagefile_tb SET ";
                                    if (isset($_REQUEST['kindorgansenddamagefile_state_id'])) {
                                        $kindorgansenddamagefile_state_id = $this->post('kindorgansenddamagefile_state_id');
                                        $query .= "kindorgansenddamagefile_state_id=" . $kindorgansenddamagefile_state_id . " ";
                                    }

                                    if (isset($_REQUEST['kindorgansenddamagefile_city_id']) && (isset($_REQUEST['kindorgansenddamagefile_state_id']))) {
                                        $query .= ",";
                                    }
                                    if (isset($_REQUEST['kindorgansenddamagefile_city_id'])) {
                                        $kindorgansenddamagefile_city_id = $this->post('kindorgansenddamagefile_city_id');
                                        $query .= "kindorgansenddamagefile_city_id=" . $kindorgansenddamagefile_city_id . " ";
                                    }

                                    if (isset($_REQUEST['kindorgansenddamagefile_kind_id']) && (isset($_REQUEST['kindorgansenddamagefile_state_id']) || isset($_REQUEST['kindorgansenddamagefile_city_id']))) {
                                        $query .= ",";
                                    }
                                    if (isset($_REQUEST['kindorgansenddamagefile_kind_id'])) {
                                        $kindorgansenddamagefile_kind_id = $this->post('kindorgansenddamagefile_kind_id');
                                        $query .= "kindorgansenddamagefile_kind_id=" . $kindorgansenddamagefile_kind_id . " ";
                                    }

                                    if (isset($_REQUEST['kindorgansenddamagefile_expert_id']) && (isset($_REQUEST['kindorgansenddamagefile_kind_id']) || isset($_REQUEST['kindorgansenddamagefile_state_id']) || isset($_REQUEST['kindorgansenddamagefile_city_id']))) {
                                        $query .= ",";
                                    }
                                    if (isset($_REQUEST['kindorgansenddamagefile_expert_id'])) {
                                        $kindorgansenddamagefile_expert_id = $this->post('kindorgansenddamagefile_expert_id');
                                        $query .= "kindorgansenddamagefile_expert_id=" . $kindorgansenddamagefile_expert_id . " ";
                                    }

                                    if (isset($_REQUEST['kindorgansenddamagefile_evaluatorco_id']) && (isset($_REQUEST['kindorgansenddamagefile_expert_id']) || isset($_REQUEST['kindorgansenddamagefile_kind_id']) || isset($_REQUEST['kindorgansenddamagefile_state_id']) || isset($_REQUEST['kindorgansenddamagefile_city_id']))) {
                                        $query .= ",";
                                    }
                                    if (isset($_REQUEST['kindorgansenddamagefile_evaluatorco_id'])) {
                                        $kindorgansenddamagefile_evaluatorco_id = $this->post('kindorgansenddamagefile_evaluatorco_id');
                                        $query .= "kindorgansenddamagefile_evaluatorco_id=" . $kindorgansenddamagefile_evaluatorco_id . " ";
                                    }

                                    if (isset($_REQUEST['kindorgansenddamagefile_fielddamagefile_id']) && (isset($_REQUEST['kindorgansenddamagefile_evaluatorco_id']) || isset($_REQUEST['kindorgansenddamagefile_expert_id']) || isset($_REQUEST['kindorgansenddamagefile_kind_id']) || isset($_REQUEST['kindorgansenddamagefile_state_id']) || isset($_REQUEST['kindorgansenddamagefile_city_id']))) {
                                        $query .= ",";
                                    }
                                    if (isset($_REQUEST['kindorgansenddamagefile_fielddamagefile_id'])) {
                                        $kindorgansenddamagefile_fielddamagefile_id = $this->post('kindorgansenddamagefile_fielddamagefile_id');
                                        $query .= "kindorgansenddamagefile_fielddamagefile_id=" . $kindorgansenddamagefile_fielddamagefile_id . " ";
                                    }

                                    if (isset($_REQUEST['kindorgansenddamagefile_organ_id']) && (isset($_REQUEST['kindorgansenddamagefile_fielddamagefile_id'])||isset($_REQUEST['kindorgansenddamagefile_evaluatorco_id']) || isset($_REQUEST['kindorgansenddamagefile_expert_id']) || isset($_REQUEST['kindorgansenddamagefile_kind_id']) || isset($_REQUEST['kindorgansenddamagefile_state_id']) || isset($_REQUEST['kindorgansenddamagefile_city_id']))) {
                                        $query .= ",";
                                    }
                                    if (isset($_REQUEST['kindorgansenddamagefile_organ_id'])) {
                                        $kindorgansenddamagefile_organ_id = $this->post('kindorgansenddamagefile_organ_id');
                                        $query .= "kindorgansenddamagefile_organ_id=" . $kindorgansenddamagefile_organ_id . " ";
                                    }

                                    if (isset($_REQUEST['kindorgansenddamagefile_contract_id']) && (isset($_REQUEST['kindorgansenddamagefile_organ_id'])||isset($_REQUEST['kindorgansenddamagefile_fielddamagefile_id'])||isset($_REQUEST['kindorgansenddamagefile_evaluatorco_id']) || isset($_REQUEST['kindorgansenddamagefile_expert_id']) || isset($_REQUEST['kindorgansenddamagefile_kind_id']) || isset($_REQUEST['kindorgansenddamagefile_state_id']) || isset($_REQUEST['kindorgansenddamagefile_city_id']))) {
                                        $query .= ",";
                                    }
                                    if (isset($_REQUEST['kindorgansenddamagefile_contract_id'])) {
                                        $kindorgansenddamagefile_contract_id = $this->post('kindorgansenddamagefile_contract_id');
                                        $query .= "kindorgansenddamagefile_contract_id=" . $kindorgansenddamagefile_contract_id . " ";
                                    }

                                    $query .= " where kindorgansenddamagefile_id=" . $kindorgansenddamagefile_id;

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