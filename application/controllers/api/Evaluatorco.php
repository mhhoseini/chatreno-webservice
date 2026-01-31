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
class Evaluatorco extends REST_Controller {

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
        $this->load->model('B_evaluatorco');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('evaluatorco', $command, get_client_ip(),50,50)) {
        if ($command=="add_evaluatorco")
        {
            $evaluatorco_id=$this->post('evaluatorco_id');
            $evaluatorco_name=$this->post('evaluatorco_name') ;
            $evaluatorco_levelof_prosperity=$this->post('evaluatorco_levelof_prosperity');
            $evaluatorco_num_branchesdamages=$this->post('evaluatorco_num_branchesdamages');
            $evaluatorco_customer_satisfaction=$this->post('evaluatorco_customer_satisfaction');
            $evaluatorco_timeanswer_complaints=$this->post('evaluatorco_timeanswer_complaints');
            $evaluatorco_description=$this->post('evaluatorco_description') ;
            $evaluatorco_logo_code=$this->post('evaluatorco_logo_code') ;
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','evaluatorco');
            if($employeetoken[0]=='ok')
            {
                $result=$this->B_evaluatorco->get_evaluatorco_by($evaluatorco_name,$evaluatorco_id);
                if (empty($result))
                {
                    $result2=$this->B_db->get_image_whitoururl($evaluatorco_logo_code);
                    $image=$result2[0];
                    $ext = pathinfo($image['image_url'], PATHINFO_EXTENSION);
                    $evaluatorco_logo_url='filefolder/sitecontent/'.$evaluatorco_id.'.'.$ext;
                    copy($image['image_url'], $evaluatorco_logo_url);
                    $evaluatorco_id=$this->B_evaluatorco->add_evaluatorco($evaluatorco_id,$evaluatorco_name,$evaluatorco_levelof_prosperity,$evaluatorco_num_branchesdamages , $evaluatorco_customer_satisfaction ,  $evaluatorco_timeanswer_complaints , $evaluatorco_description,$evaluatorco_logo_url);
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('evaluatorco_id'=>$evaluatorco_id)
                    ,'desc'=>'شرکت بیمه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    $carmode=count($result[0]);
                    echo json_encode(array('result'=>"error"
                    ,"data"=>array('evaluatorco_id'=>$carmode['evaluatorco_id'])
                    ,'desc'=>'شرکت بیمه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }
        }
        if ($command=="get_evaluatorco")
        {
            $query="select * from evaluatorco_tb where ";
            if(isset($_REQUEST['filter1'])){
                $filter1=$this->post('filter1');
                $query.=$filter1;
            }else{$query.=" 1=1 ";}
            $query.=" AND ";
            if(isset($_REQUEST['filter2'])){
                $filter2=$this->post('filter2');
                $query.=$filter2;
            }else{$query.=" 1=1 ";}
            $query.=" AND ";
            if(isset($_REQUEST['filter3'])){
                $filter3=$this->post('filter3');
                $query.=$filter3;
            }else{$query.=" 1=1 ";}
            $query.=" ORDER BY evaluatorco_id ASC";
            $result = $this->B_db->run_query($query);
            $output =array();
            foreach($result as $row)
            {
                $record=array();
                $record['evaluatorco_id']=$row['evaluatorco_id'];
                $record['evaluatorco_name']=$row['evaluatorco_name'];
                $record['evaluatorco_levelof_prosperity']=$row['evaluatorco_levelof_prosperity'];
                $record['evaluatorco_num_branchesdamages']=$row['evaluatorco_num_branchesdamages'];
                $record['evaluatorco_customer_satisfaction']=$row['evaluatorco_customer_satisfaction'];
                $record['evaluatorco_timeanswer_complaints']=$row['evaluatorco_timeanswer_complaints'];
                $record['evaluatorco_description']=$row['evaluatorco_description'];
                $record['evaluatorco_logo_url']=IMGADD.$row['evaluatorco_logo_url'];
                $record['evaluatorco_deactive']=$row['evaluatorco_deactive'];
                $output[]=$record;
            }
            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'مشحصات شرکت بیمه با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
        else
            if ($command=="delete_evaluatorco")
            {
                $evaluatorco_id=$this->post('evaluatorco_id');
                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','evaluatorco');
                if($employeetoken[0]=='ok')
                {
                    $output = array();
                    $result = $this->B_evaluatorco->del_evaluatorco($evaluatorco_id);
                    if($result){echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'شرکت بیمه مورد نظر حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        echo json_encode(array('result'=>"error"
                        ,"data"=>$output
                        ,'desc'=>'شرکت بیمه مورد نظر حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                }else{
                    echo json_encode(array('result'=>$employeetoken[0]
                    ,"data"=>$employeetoken[1]
                    ,'desc'=>$employeetoken[2]));
                }
            }
            else
                if ($command=="modify_evaluatorco")
                {
                    $evaluatorco_id=$this->post('evaluatorco_id');
                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','evaluatorco');
                    if($employeetoken[0]=='ok')
                    {
                        $query="UPDATE evaluatorco_tb SET ";
                        if(isset($_REQUEST['evaluatorco_levelof_prosperity'])){
                            $evaluatorco_levelof_prosperity=$this->post('evaluatorco_levelof_prosperity');
                            $query.="evaluatorco_levelof_prosperity=".$evaluatorco_levelof_prosperity."";
                        }

                        if(isset($_REQUEST['evaluatorco_name'])&&isset($_REQUEST['evaluatorco_levelof_prosperity'])){ $query.=",";}
                        if(isset($_REQUEST['evaluatorco_name'])){
                            $evaluatorco_name=$this->post('evaluatorco_name');
                            $query.="evaluatorco_name='".$evaluatorco_name."'";}

                        if(isset($_REQUEST['evaluatorco_num_branchesdamages'])&&(isset($_REQUEST['evaluatorco_name'])||isset($_REQUEST['evaluatorco_levelof_prosperity']))){ $query.=",";}
                        if(isset($_REQUEST['evaluatorco_num_branchesdamages'])){
                            $evaluatorco_num_branchesdamages=$this->post('evaluatorco_num_branchesdamages');
                            $query.="evaluatorco_num_branchesdamages=".$evaluatorco_num_branchesdamages."";
                        }

                        if(isset($_REQUEST['evaluatorco_customer_satisfaction'])&&(isset($_REQUEST['evaluatorco_name'])||isset($_REQUEST['evaluatorco_num_branchesdamages'])||isset($_REQUEST['evaluatorco_levelof_prosperity']))){ $query.=",";}
                        if(isset($_REQUEST['evaluatorco_customer_satisfaction'])){
                            $evaluatorco_customer_satisfaction=$this->post('evaluatorco_customer_satisfaction');
                            $query.="evaluatorco_customer_satisfaction=".$evaluatorco_customer_satisfaction."";}

                        if(isset($_REQUEST['evaluatorco_timeanswer_complaints'])&&(isset($_REQUEST['evaluatorco_name'])||isset($_REQUEST['evaluatorco_num_branchesdamages'])||isset($_REQUEST['evaluatorco_levelof_prosperity'])||isset($_REQUEST['evaluatorco_customer_satisfaction']))){ $query.=",";}
                        if(isset($_REQUEST['evaluatorco_timeanswer_complaints'])){
                            $evaluatorco_timeanswer_complaints=$this->post('evaluatorco_timeanswer_complaints');
                            $query.="evaluatorco_timeanswer_complaints=".$evaluatorco_timeanswer_complaints."";}

                        if(isset($_REQUEST['evaluatorco_description'])&&(isset($_REQUEST['evaluatorco_name'])||isset($_REQUEST['evaluatorco_num_branchesdamages'])||isset($_REQUEST['evaluatorco_levelof_prosperity'])||isset($_REQUEST['evaluatorco_customer_satisfaction'])||isset($_REQUEST['evaluatorco_timeanswer_complaints']))){$query.=",";}
                        if(isset($_REQUEST['evaluatorco_description'])){
                            $evaluatorco_description=$this->post('evaluatorco_description');
                            $query.="evaluatorco_description='".$evaluatorco_description."' ";}

                        if(isset($_REQUEST['evaluatorco_logo_code'])&&(isset($_REQUEST['evaluatorco_description'])||isset($_REQUEST['evaluatorco_name'])||isset($_REQUEST['evaluatorco_num_branchesdamages'])||isset($_REQUEST['evaluatorco_levelof_prosperity'])||isset($_REQUEST['evaluatorco_customer_satisfaction'])||isset($_REQUEST['evaluatorco_timeanswer_complaints']))){$query.=",";}
                        if(isset($_REQUEST['evaluatorco_logo_code'])){
                            $evaluatorco_logo_code=$this->post('evaluatorco_logo_code');
                            $result2=$this->B_db->get_image_whitoururl($evaluatorco_logo_code);
                            $image=$result2[0];
                            $ext = pathinfo($image['image_url'], PATHINFO_EXTENSION);
                            $evaluatorco_logo_url='filefolder/sitecontent/'.$evaluatorco_id.'.'.$ext;
                            copy($image['image_url'], $evaluatorco_logo_url);
                            $query.="evaluatorco_logo_url='".$evaluatorco_logo_url."' ";
                        }
                        if(isset($_REQUEST['evaluatorco_deactive'])&&(isset($_REQUEST['evaluatorco_logo_code'])||isset($_REQUEST['evaluatorco_description'])||isset($_REQUEST['evaluatorco_name'])||isset($_REQUEST['evaluatorco_num_branchesdamages'])||isset($_REQUEST['evaluatorco_num_branchesdamages'])||isset($_REQUEST['evaluatorco_customer_satisfaction'])||isset($_REQUEST['evaluatorco_timeanswer_complaints']))){$query.=",";}
                        if(isset($_REQUEST['evaluatorco_deactive'])){
                            $evaluatorco_deactive=$this->post('evaluatorco_deactive');
                            $query.="evaluatorco_deactive=".$_REQUEST['evaluatorco_deactive']." ";
                        }
                        $query.=" where evaluatorco_id=".$evaluatorco_id;
                        $result=$this->B_db->run_query_put($query);
                        if($result){
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>""
                            ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else {
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>""
                            ,'desc'=>'تغییرات انجام نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
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