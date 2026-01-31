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
class Requiredimgdmg extends REST_Controller {

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
        if ($command=="add_requiredimgdmg")
        {
            $required_img_fielddamagefile_id=$this->post('required_img_fielddamagefile_id') ;
            $required_img_dmg_name=$this->post('required_img_dmg_name') ;
            $required_img_dmg_desc=$this->post('required_img_dmg_desc') ;
            $required_img_dmg_force=$this->post('required_img_dmg_force') ;
            $required_img_dmg_sample=$this->post('required_img_dmg_sample') ;

            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','requiredimgdmg');
            if($employeetoken[0]=='ok')
            {
//**************************************************************************************************************
                $query="select * from required_img_dmg_tb where required_img_fielddamagefile_id=".$required_img_fielddamagefile_id." AND required_img_dmg_name='".$required_img_dmg_name."'";
                $result=$this->B_db->run_query($query);
                $num=count($result[0]);
                if ($num==0)
                {
                    $query="INSERT INTO required_img_dmg_tb(   required_img_fielddamagefile_id,   required_img_dmg_name,  required_img_dmg_desc,required_img_dmg_force   )
	                                   VALUES ($required_img_fielddamagefile_id,'$required_img_dmg_name' , '$required_img_dmg_desc',$required_img_dmg_force );";
                    $this->B_db->run_query_put($query);
                    $requiredimgdmgid=$this->db->insert_id();

                    //***********************************************************************************************************
                    $query2="select * from image_tb where image_code='".$required_img_dmg_sample."'";
                    $result2=$this->B_db->run_query($query2);


                    $image=$result2[0];
                    $ext = pathinfo($image['image_url'], PATHINFO_EXTENSION);

                    $required_img_dmg_sample='filefolder/requiredimg/'.$required_img_fielddamagefile_id.$requiredimgdmgid.'.'.$ext;
                    $required_tum_img_dmg_sample='filefolder/requiredimg/t'.$required_img_fielddamagefile_id.$requiredimgdmgid.'.'.$ext;

                    copy($image['image_url'], $required_img_dmg_sample);
                    copy($image['image_tumb_url'], $required_tum_img_dmg_sample);
                    $query1="UPDATE required_img_dmg_tb SET required_tum_img_dmg_sample='$required_tum_img_dmg_sample',required_img_dmg_sample ='$required_img_dmg_sample' WHERE required_img_dmg_id=$requiredimgdmgid";

                    $result=$this->B_db->run_query_put($query1);
                    //***********************************************************************************************************

                    if($result){header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                    ,"data"=>array('required_img_dmg_id'=>$requiredimgdmgid)
                    ,'desc'=>'عکس های مورد نیاز رشته اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); }
                    else{
                        header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                        ,"data"=>array('required_img_dmg_id'=>$requiredimgdmgid)
                        ,'desc'=>'عکس های مورد نیاز رشته اضافه نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }

                }else{
                    $requiredimgdmg=$result[0];
                    header('Content-Type: application/json'); echo json_encode(array('result'=>"error"
                    ,"data"=>array('required_img_dmg_id'=>$requiredimgdmg['required_img_dmg_id'])
                    ,'desc'=>'عکس های مورد نیاز رشته تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
//***************************************************************************************************************
            }else{
                header('Content-Type: application/json'); echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }
        }
        if ($command=="get_requiredimgdmgid")
        {
            $damagefile_id=$this->post('damagefile_id') ;
            $query="select * from required_img_dmg_tb,fielddamagefile_tb,damagefile_tb where fielddamagefile_id=required_img_fielddamagefile_id AND required_img_fielddamagefile_id=damagefile_fielddamagefile_id AND damagefile_id=$damagefile_id ORDER BY required_img_dmg_id ASC";
            $result=$this->B_db->run_query($query);
            $output =array();
            foreach($result as $row)
            {
                $record=array();
                $record['required_img_dmg_id']=$row['required_img_dmg_id'];
                $record['required_img_fielddamagefile']=$row['required_img_fielddamagefile'];
                $record['fielddamagefile_fa']=$row['fielddamagefile_fa'];
                $record['required_img_dmg_name']=$row['required_img_dmg_name'];
                $record['required_img_dmg_desc']=$row['required_img_dmg_desc'];
                $record['required_img_dmg_force']=$row['required_img_dmg_force'];
                $record['required_img_dmg_sample']=IMGADD.$row['required_img_dmg_sample'];
                $record['required_tum_img_dmg_sample']=IMGADD.$row['required_tum_img_dmg_sample'];
                $output[]=$record;
            }
            header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'مشحصات عکس های مورد نیاز رشته با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

        }else
            if ($command=="get_requiredimgdmg")
        {
            $filter=" 1=1 ";
            if(isset($_REQUEST['fielddamagefile_id'])){
                $fielddamagefile_id=$this->post('fielddamagefile_id') ;
                $filter=" required_img_fielddamagefile_id=$fielddamagefile_id ";
            }

            $query="select * from required_img_dmg_tb,fielddamagefile_tb where 
               fielddamagefile_id=required_img_fielddamagefile_id AND $filter ORDER BY required_img_dmg_id ASC";
            $result = $this->B_db->run_query($query);
            $output =array();
            foreach($result as $row)
            {
                $record=array();
                $record['required_img_dmg_id']=$row['required_img_dmg_id'];
                $record['required_img_fielddamagefile']=$row['required_img_fielddamagefile'];
                $record['fielddamagefile_fa']=$row['fielddamagefile_fa'];
                $record['required_img_dmg_name']=$row['required_img_dmg_name'];
                $record['required_img_dmg_desc']=$row['required_img_dmg_desc'];
                $record['required_img_dmg_force']=$row['required_img_dmg_force'];
                $record['required_img_dmg_sample']=IMGADD.$row['required_img_dmg_sample'];
                $record['required_tum_img_dmg_sample']=IMGADD.$row['required_tum_img_dmg_sample'];
                $record['organ_therapycontract_conditions_covarage_id']=$row['organ_therapycontract_conditions_covarage_id'];
                $record['organ_therapycontract_conditions_covarage_name']=$row['organ_therapycontract_conditions_covarage_name'];
                $output[]=$record;
            }
            header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'مشحصات عکس های مورد نیاز رشته با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

        }
        else
            if ($command=="delete_requiredimgdmg")
            {
                $required_img_dmg_id=$this->post('required_img_dmg_id') ;

                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','requiredimgdmg');
                if($employeetoken[0]=='ok')
                {
                    $query="select * from required_img_dmg_tb where required_img_dmg_id=".$required_img_dmg_id."";
                    $result=$this->B_db->run_query($query);
                    $requiredimgdmg=$result[0];
                    unlink($requiredimgdmg['required_img_dmg_sample']);
                    unlink($requiredimgdmg['required_tum_img_dmg_sample']);
                    $output = array();$user_id=$employeetoken[0];

                    $query="DELETE FROM required_img_dmg_tb  where required_img_dmg_id=".$required_img_dmg_id."";
                    $result = $this->B_db->run_query_put($query);
                    if($result){header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'عکس های مورد نیاز رشته مورد نظر حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        header('Content-Type: application/json'); echo json_encode(array('result'=>"error"
                        ,"data"=>$output
                        ,'desc'=>'عکس های مورد نیاز رشته مورد نظر حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
//***************************************************************************************************************
                }else{
                    header('Content-Type: application/json'); echo json_encode(array('result'=>$employeetoken[0]
                    ,"data"=>$employeetoken[1]
                    ,'desc'=>$employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                }

            }
            else
                if ($command=="modify_requiredimgdmg") {
                    $required_img_dmg_id = $this->post('required_img_dmg_id');

                    $employeetoken = checkpermissionemployeetoken($employee_token_str, 'modify', 'requiredimgdmg');
                    if ($employeetoken[0] == 'ok') {
//*****************************************************************************************
                        $query = "UPDATE required_img_dmg_tb SET ";
                        if (isset($_REQUEST['required_img_dmg_name'])) {
                            $required_img_dmg_name = $this->post('required_img_dmg_name');
                            $query .= "required_img_dmg_name='" . $required_img_dmg_name . "'";
                        }

                        if (isset($_REQUEST['required_img_dmg_desc']) && (isset($_REQUEST['required_img_dmg_name']))) {
                            $query .= ",";
                        }
                        if (isset($_REQUEST['required_img_dmg_desc'])) {
                            $required_img_dmg_desc = $this->post('required_img_dmg_desc');
                            $query .= "required_img_dmg_desc='" . $required_img_dmg_desc . "'";
                        }

                        if (isset($_REQUEST['required_img_dmg_sample']) && (isset($_REQUEST['required_img_dmg_name']) || isset($_REQUEST['required_img_dmg_desc']))) {
                            $query .= ",";
                        }
                        if (isset($_REQUEST['required_img_dmg_sample'])) {
                            $required_img_dmg_sample = $this->post('required_img_dmg_sample');
                            //*************************************
                            $query2="select * from image_tb where image_code='".$required_img_dmg_sample."'";
                            $result2 = $this->B_db->run_query($query2);
                            $image = $result2[0];
                            $ext = pathinfo($image['image_url'], PATHINFO_EXTENSION);
                            $required_img_fielddamagefile=$this->post('required_img_fielddamagefile');
                            $required_img_dmg_sample='filefolder/requiredimg/'.$required_img_fielddamagefile.$required_img_dmg_id.'.'.$ext;
                            $required_tum_img_dmg_sample='filefolder/requiredimg/t'.$required_img_fielddamagefile.$required_img_dmg_id.'.'.$ext;
                            copy($image['image_url'], $required_img_dmg_sample);
                            copy($image['image_tumb_url'], $required_tum_img_dmg_sample);
                            //*************************************
                            $query .= " required_img_dmg_sample='" . $required_img_dmg_sample . "',required_tum_img_dmg_sample='" . $required_tum_img_dmg_sample . "' ";
                        }
                        if (isset($_REQUEST['required_img_fielddamagefile']) && (isset($_REQUEST['required_img_dmg_name']) || isset($_REQUEST['required_img_dmg_desc']) || isset($_REQUEST['required_img_dmg_sample']))) {
                            $query .= ",";
                        }
                        if (isset($_REQUEST['required_img_fielddamagefile'])) {
                            $required_img_fielddamagefile = $this->post('required_img_fielddamagefile');
                            $query .= "required_img_fielddamagefile='" . $required_img_fielddamagefile . "' ";
                        }

						 if (isset($_REQUEST['required_img_dmg_force']) && (isset($_REQUEST['required_img_fielddamagefile'])||isset($_REQUEST['required_img_dmg_name']) || isset($_REQUEST['required_img_dmg_desc']) || isset($_REQUEST['required_img_dmg_sample']))) {
                            $query .= ",";
                        }
                        if (isset($_REQUEST['required_img_dmg_force'])) {
                            $required_img_dmg_force = $this->post('required_img_dmg_force');
                            $query .= "required_img_dmg_force=" . $required_img_dmg_force . " ";
                        }
						

                        $query .= " where required_img_dmg_id=" . $required_img_dmg_id;

                        $result = $this->B_db->run_query_put($query);
                        if ($result) {
                            header('Content-Type: application/json'); echo json_encode(array('result' => "ok"
                            , "data" => ""
                            , 'desc' => 'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        } else {
                            header('Content-Type: application/json'); echo json_encode(array('result' => "ok"
                            , "data" => ""
                            , 'desc' => 'تغییرات انجام نشد' . $query),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
//**************************************************************************************************************

                    } else {
                        header('Content-Type: application/json'); echo json_encode(array('result' => $employeetoken[0]
                        , "data" => $employeetoken[1]
                        , 'desc' => $employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                    }

                }
    }
}
