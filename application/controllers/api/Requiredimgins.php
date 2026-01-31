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
 *
 * @package         CodeIgniter
 * @subpackage      aref24 Project
 * @category        Controller
 * @author          Mohammad Hoseini, Abolfazl Ganji
 * @license         MIT
 * @link            https://aref24.ir
 */
class Requiredimgins extends REST_Controller {

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
        $this->load->model('B_user');
        $this->load->model('B_requests');
        $this->load->model('B_db');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if(isset($this->input->request_headers()['Authorization']))$employee_token_str = $this->input->request_headers()['Authorization'];
        if ($command=="add_requiredimgins")
        {
            $required_img_fieldinsurance=$this->post('required_img_fieldinsurance') ;
            $required_img_ins_name=$this->post('required_img_ins_name') ;
            $required_img_ins_desc=$this->post('required_img_ins_desc') ;
            $required_img_ins_force=$this->post('required_img_ins_force') ;
            $required_img_ins_sample=$this->post('required_img_ins_sample') ;

            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','requiredimgins');
            if($employeetoken[0]=='ok')
            {
//**************************************************************************************************************
                $query="select * from required_img_ins_tb where required_img_fieldinsurance='".$required_img_fieldinsurance."' AND required_img_ins_name='".$required_img_ins_name."'";
                $result=$this->B_db->run_query($query);
                $num=count($result[0]);
                if ($num==0)
                {
                    $query="INSERT INTO required_img_ins_tb(   required_img_fieldinsurance,   required_img_ins_name,  required_img_ins_desc,required_img_ins_force   )
	                                   VALUES ('$required_img_fieldinsurance','$required_img_ins_name' , '$required_img_ins_desc',$required_img_ins_force );";
                    $this->B_db->run_query_put($query);
                    $requiredimginsid=$this->db->insert_id();

                    //***********************************************************************************************************
                    $query2="select * from image_tb where image_code='".$required_img_ins_sample."'";
                    $result2=$this->B_db->run_query($query2);


                    $image=$result2[0];
                    $ext = pathinfo($image['image_url'], PATHINFO_EXTENSION);

                    $required_img_ins_sample='filefolder/requiredimg/'.$required_img_fieldinsurance.$requiredimginsid.'.'.$ext;
                    $required_tum_img_ins_sample='filefolder/requiredimg/t'.$required_img_fieldinsurance.$requiredimginsid.'.'.$ext;

                    copy($image['image_url'], $required_img_ins_sample);
                    copy($image['image_tumb_url'], $required_tum_img_ins_sample);
                    $query1="UPDATE required_img_ins_tb SET required_tum_img_ins_sample='$required_tum_img_ins_sample',required_img_ins_sample ='$required_img_ins_sample' WHERE required_img_ins_id=$requiredimginsid";

                    $result=$this->B_db->run_query_put($query1);
                    //***********************************************************************************************************

                    if($result){echo json_encode(array('result'=>"ok"
                    ,"data"=>array('required_img_ins_id'=>$requiredimginsid)
                    ,'desc'=>'عکس های مورد نیاز رشته اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); }
                    else{
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>array('required_img_ins_id'=>$requiredimginsid)
                        ,'desc'=>'عکس های مورد نیاز رشته اضافه نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }

                }else{
                    $requiredimgins=$result[0];
                    echo json_encode(array('result'=>"error"
                    ,"data"=>array('required_img_ins_id'=>$requiredimgins['required_img_ins_id'])
                    ,'desc'=>'عکس های مورد نیاز رشته تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
//***************************************************************************************************************
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }
        }
        if ($command=="get_requiredimginsid")
        {
            $request_id=$this->post('request_id') ;
            $query="select * from required_img_ins_tb,fieldinsurance_tb,request_tb where fieldinsurance=required_img_fieldinsurance AND required_img_fieldinsurance=request_fieldinsurance AND request_id=$request_id ORDER BY required_img_ins_id ASC";
            $result=$this->B_db->run_query($query);
            $output =array();
            foreach($result as $row)
            {
                $record=array();
                $record['required_img_ins_id']=$row['required_img_ins_id'];
                $record['required_img_fieldinsurance']=$row['required_img_fieldinsurance'];
                $record['fieldinsurance_fa']=$row['fieldinsurance_fa'];
                $record['required_img_ins_name']=$row['required_img_ins_name'];
                $record['required_img_ins_desc']=$row['required_img_ins_desc'];
                $record['required_img_ins_force']=$row['required_img_ins_force'];
                $record['required_img_ins_sample']=IMGADD.$row['required_img_ins_sample'];
                $record['required_tum_img_ins_sample']=IMGADD.$row['required_tum_img_ins_sample'];
                $output[]=$record;
            }
            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'مشحصات عکس های مورد نیاز رشته با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

        }else
            if ($command=="get_requiredimgins")
        {
            $filter=" 1=1 ";
            if(isset($_REQUEST['fieldinsurance'])){
                $fieldinsurance=$this->post('fieldinsurance') ;
                $filter=" required_img_fieldinsurance='$fieldinsurance' ";
            }

            $query="select * from required_img_ins_tb,fieldinsurance_tb where fieldinsurance=required_img_fieldinsurance AND $filter ORDER BY required_img_ins_id ASC";
            $result = $this->B_db->run_query($query);
            $output =array();
            foreach($result as $row)
            {
                $record=array();
                $record['required_img_ins_id']=$row['required_img_ins_id'];
                $record['required_img_fieldinsurance']=$row['required_img_fieldinsurance'];
                $record['fieldinsurance_fa']=$row['fieldinsurance_fa'];
                $record['required_img_ins_name']=$row['required_img_ins_name'];
                $record['required_img_ins_desc']=$row['required_img_ins_desc'];
                $record['required_img_ins_force']=$row['required_img_ins_force'];
                $record['required_img_ins_sample']=IMGADD.$row['required_img_ins_sample'];
                $record['required_tum_img_ins_sample']=IMGADD.$row['required_tum_img_ins_sample'];
                $output[]=$record;
            }
            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'مشحصات عکس های مورد نیاز رشته با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

        }
        else
            if ($command=="delete_requiredimgins")
            {
                $required_img_ins_id=$this->post('required_img_ins_id') ;

                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','requiredimgins');
                if($employeetoken[0]=='ok')
                {
                    $query="select * from required_img_ins_tb where required_img_ins_id=".$required_img_ins_id."";
                    $result=$this->B_db->run_query($query);
                    $requiredimgins=$result[0];
                    unlink($requiredimgins['required_img_ins_sample']);
                    unlink($requiredimgins['required_tum_img_ins_sample']);
                    $output = array();$user_id=$employeetoken[0];

                    $query="DELETE FROM required_img_ins_tb  where required_img_ins_id=".$required_img_ins_id."";
                    $result = $this->B_db->run_query_put($query);
                    if($result){echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'عکس های مورد نیاز رشته مورد نظر حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        echo json_encode(array('result'=>"error"
                        ,"data"=>$output
                        ,'desc'=>'عکس های مورد نیاز رشته مورد نظر حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
//***************************************************************************************************************
                }else{
                    echo json_encode(array('result'=>$employeetoken[0]
                    ,"data"=>$employeetoken[1]
                    ,'desc'=>$employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                }

            }
            else
                if ($command=="modify_requiredimgins") {
                    $required_img_ins_id = $this->post('required_img_ins_id');

                    $employeetoken = checkpermissionemployeetoken($employee_token_str, 'modify', 'requiredimgins');
                    if ($employeetoken[0] == 'ok') {
//*****************************************************************************************
                        $query = "UPDATE required_img_ins_tb SET ";
                        if (isset($_REQUEST['required_img_ins_name'])) {
                            $required_img_ins_name = $this->post('required_img_ins_name');
                            $query .= "required_img_ins_name='" . $required_img_ins_name . "'";
                        }

                        if (isset($_REQUEST['required_img_ins_desc']) && (isset($_REQUEST['required_img_ins_name']))) {
                            $query .= ",";
                        }
                        if (isset($_REQUEST['required_img_ins_desc'])) {
                            $required_img_ins_desc = $this->post('required_img_ins_desc');
                            $query .= "required_img_ins_desc='" . $required_img_ins_desc . "'";
                        }

                        if (isset($_REQUEST['required_img_ins_sample']) && (isset($_REQUEST['required_img_ins_name']) || isset($_REQUEST['required_img_ins_desc']))) {
                            $query .= ",";
                        }
                        if (isset($_REQUEST['required_img_ins_sample'])) {
                            $required_img_ins_sample = $this->post('required_img_ins_sample');
                            //*************************************
                            $query2="select * from image_tb where image_code='".$required_img_ins_sample."'";
                            $result2 = $this->B_db->run_query($query2);
                            $image = $result2[0];
                            $ext = pathinfo($image['image_url'], PATHINFO_EXTENSION);
                            $required_img_fieldinsurance=$this->post('required_img_fieldinsurance');
                            $required_img_ins_sample='filefolder/requiredimg/'.$required_img_fieldinsurance.$required_img_ins_id.'.'.$ext;
                            $required_tum_img_ins_sample='filefolder/requiredimg/t'.$required_img_fieldinsurance.$required_img_ins_id.'.'.$ext;
                            copy($image['image_url'], $required_img_ins_sample);
                            copy($image['image_tumb_url'], $required_tum_img_ins_sample);
                            //*************************************
                            $query .= " required_img_ins_sample='" . $required_img_ins_sample . "',required_tum_img_ins_sample='" . $required_tum_img_ins_sample . "' ";
                        }
                        if (isset($_REQUEST['required_img_fieldinsurance']) && (isset($_REQUEST['required_img_ins_name']) || isset($_REQUEST['required_img_ins_desc']) || isset($_REQUEST['required_img_ins_sample']))) {
                            $query .= ",";
                        }
                        if (isset($_REQUEST['required_img_fieldinsurance'])) {
                            $required_img_fieldinsurance = $this->post('required_img_fieldinsurance');
                            $query .= "required_img_fieldinsurance='" . $required_img_fieldinsurance . "' ";
                        }

						 if (isset($_REQUEST['required_img_ins_force']) && (isset($_REQUEST['required_img_fieldinsurance'])||isset($_REQUEST['required_img_ins_name']) || isset($_REQUEST['required_img_ins_desc']) || isset($_REQUEST['required_img_ins_sample']))) {
                            $query .= ",";
                        }
                        if (isset($_REQUEST['required_img_ins_force'])) {
                            $required_img_ins_force = $this->post('required_img_ins_force');
                            $query .= "required_img_ins_force=" . $required_img_ins_force . " ";
                        }
						

                        $query .= " where required_img_ins_id=" . $required_img_ins_id;

                        $result = $this->B_db->run_query_put($query);
                        if ($result) {
                            echo json_encode(array('result' => "ok"
                            , "data" => ""
                            , 'desc' => 'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        } else {
                            echo json_encode(array('result' => "ok"
                            , "data" => ""
                            , 'desc' => 'تغییرات انجام نشد' . $query),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
//**************************************************************************************************************

                    } else {
                        echo json_encode(array('result' => $employeetoken[0]
                        , "data" => $employeetoken[1]
                        , 'desc' => $employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                    }

                }
    }
}
