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
class Company extends REST_Controller {

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
        $this->load->model('B_company');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('company', $command, get_client_ip(),50,50)) {
        if ($command=="add_company")
        {
            $company_id=$this->post('company_id');
            $company_name=$this->post('company_name') ;
            $company_levelof_prosperity=$this->post('company_levelof_prosperity');
            $company_num_branchesdamages=$this->post('company_num_branchesdamages');
            $company_customer_satisfaction=$this->post('company_customer_satisfaction');
            $company_timeanswer_complaints=$this->post('company_timeanswer_complaints');
            $company_description=$this->post('company_description') ;
            $company_logo_code=$this->post('company_logo_code') ;
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','company');
            if($employeetoken[0]=='ok')
            {
                $result=$this->B_company->get_company_by($company_name,$company_id);
                if (empty($result))
                {
                    $result2=$this->B_db->get_image($company_logo_code);
                    $image=$result2[0];
                    $ext = pathinfo($image['image_url'], PATHINFO_EXTENSION);
                    $company_logo_url='filefolder/sitecontent/'.$company_id.'.'.$ext;
                    copy($image['image_url'], $company_logo_url);
                    $company_id=$this->B_company->add_company($company_id,$company_name,$company_levelof_prosperity,$company_num_branchesdamages , $company_customer_satisfaction ,  $company_timeanswer_complaints , $company_description,$company_logo_url);
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('company_id'=>$company_id)
                    ,'desc'=>'شرکت بیمه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    $carmode=count($result[0]);
                    echo json_encode(array('result'=>"error"
                    ,"data"=>array('company_id'=>$carmode['company_id'])
                    ,'desc'=>'شرکت بیمه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }
        }
        if ($command=="get_company")
        {
            $query="select * from company_tb where ";
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
            $query.=" ORDER BY company_id ASC";
            $result = $this->B_db->run_query($query);
            $output =array();
            foreach($result as $row)
            {
                $record=array();
                $record['company_id']=$row['company_id'];
                $record['company_name']=$row['company_name'];
                $record['company_levelof_prosperity']=$row['company_levelof_prosperity'];
                $record['company_num_branchesdamages']=$row['company_num_branchesdamages'];
                $record['company_customer_satisfaction']=$row['company_customer_satisfaction'];
                $record['company_timeanswer_complaints']=$row['company_timeanswer_complaints'];
                $record['company_description']=$row['company_description'];
                $record['company_logo_url']=IMGADD.$row['company_logo_url'];
                $record['company_deactive']=$row['company_deactive'];
                $output[]=$record;
            }
            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'مشحصات شرکت بیمه با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
        else
            if ($command=="delete_company")
            {
                $company_id=$this->post('company_id');
                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','company');
                if($employeetoken[0]=='ok')
                {
                    $output = array();
                    $result = $this->B_company->del_company($company_id);
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
                if ($command=="modify_company")
                {
                    $company_id=$this->post('company_id');
                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','company');
                    if($employeetoken[0]=='ok')
                    {
                        $query="UPDATE company_tb SET ";
                        if(isset($_REQUEST['company_levelof_prosperity'])){
                            $company_levelof_prosperity=$this->post('company_levelof_prosperity');
                            $query.="company_levelof_prosperity=".$company_levelof_prosperity."";
                        }

                        if(isset($_REQUEST['company_name'])&&isset($_REQUEST['company_levelof_prosperity'])){ $query.=",";}
                        if(isset($_REQUEST['company_name'])){
                            $company_name=$this->post('company_name');
                            $query.="company_name='".$company_name."'";}

                        if(isset($_REQUEST['company_num_branchesdamages'])&&(isset($_REQUEST['company_name'])||isset($_REQUEST['company_levelof_prosperity']))){ $query.=",";}
                        if(isset($_REQUEST['company_num_branchesdamages'])){
                            $company_num_branchesdamages=$this->post('company_num_branchesdamages');
                            $query.="company_num_branchesdamages=".$company_num_branchesdamages."";
                        }

                        if(isset($_REQUEST['company_customer_satisfaction'])&&(isset($_REQUEST['company_name'])||isset($_REQUEST['company_num_branchesdamages'])||isset($_REQUEST['company_levelof_prosperity']))){ $query.=",";}
                        if(isset($_REQUEST['company_customer_satisfaction'])){
                            $company_customer_satisfaction=$this->post('company_customer_satisfaction');
                            $query.="company_customer_satisfaction=".$company_customer_satisfaction."";}

                        if(isset($_REQUEST['company_timeanswer_complaints'])&&(isset($_REQUEST['company_name'])||isset($_REQUEST['company_num_branchesdamages'])||isset($_REQUEST['company_levelof_prosperity'])||isset($_REQUEST['company_customer_satisfaction']))){ $query.=",";}
                        if(isset($_REQUEST['company_timeanswer_complaints'])){
                            $company_timeanswer_complaints=$this->post('company_timeanswer_complaints');
                            $query.="company_timeanswer_complaints=".$company_timeanswer_complaints."";}

                        if(isset($_REQUEST['company_description'])&&(isset($_REQUEST['company_name'])||isset($_REQUEST['company_num_branchesdamages'])||isset($_REQUEST['company_levelof_prosperity'])||isset($_REQUEST['company_customer_satisfaction'])||isset($_REQUEST['company_timeanswer_complaints']))){$query.=",";}
                        if(isset($_REQUEST['company_description'])){
                            $company_description=$this->post('company_description');
                            $query.="company_description='".$company_description."' ";}

                        if(isset($_REQUEST['company_logo_code'])&&(isset($_REQUEST['company_description'])||isset($_REQUEST['company_name'])||isset($_REQUEST['company_num_branchesdamages'])||isset($_REQUEST['company_levelof_prosperity'])||isset($_REQUEST['company_customer_satisfaction'])||isset($_REQUEST['company_timeanswer_complaints']))){$query.=",";}
                        if(isset($_REQUEST['company_logo_code'])){
                            $company_logo_code=$this->post('company_logo_code');
                            $result2=$this->B_db->get_image($company_logo_code);
                            $image=$result2[0];
                            $ext = pathinfo($image['image_url'], PATHINFO_EXTENSION);
                            $company_logo_url='filefolder/sitecontent/'.$company_id.'.'.$ext;
                            copy($image['image_url'], $company_logo_url);
                            $query.="company_logo_url='".$company_logo_url."' ";
                        }
                        if(isset($_REQUEST['company_deactive'])&&(isset($_REQUEST['company_logo_code'])||isset($_REQUEST['company_description'])||isset($_REQUEST['company_name'])||isset($_REQUEST['company_num_branchesdamages'])||isset($_REQUEST['company_num_branchesdamages'])||isset($_REQUEST['company_customer_satisfaction'])||isset($_REQUEST['company_timeanswer_complaints']))){$query.=",";}
                        if(isset($_REQUEST['company_deactive'])){
                            $company_deactive=$this->post('company_deactive');
                            $query.="company_deactive=".$_REQUEST['company_deactive']." ";
                        }
                        $query.=" where company_id=".$company_id;
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