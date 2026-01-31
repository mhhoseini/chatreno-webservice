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
class Fielddamagefile extends REST_Controller {

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
        $this->load->helper("my_helper");
        $command = $this->post("command");
        if($this->B_user->checkrequestip('fielddamagefile',$command,get_client_ip(),50,50)) {
            if ($command=="add_fielddamagefile")
            {
                $fielddamagefile=$this->post('fielddamagefile') ;
                $fielddamagefile_fa=$this->post('fielddamagefile_fa') ;
                $fielddamagefile_logo_code=$this->post('fielddamagefile_logo_code') ;
                $fielddamagefile_desc=$this->post('fielddamagefile_desc') ;
                $fielddamagefile_link=$this->post('fielddamagefile_link') ;
                $fielddamagefile_commission=$this->post('fielddamagefile_commission') ;
                $fielddamagefile_image_code=$this->post('fielddamagefile_image_code') ;
                $fielddamagefile_deactive=$this->post('fielddamagefile_deactive') ;
                $fielddamagefile_mode=$this->post('fielddamagefile_mode') ;
                $fielddamagefile_organ_therapycontract_conditions_covarage_id=$this->post('fielddamagefile_organ_therapycontract_conditions_covarage_id') ;
                $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','fielddamagefile');
                if($employeetoken[0]=='ok')
                {
                    $result=$this->B_company->get_fielddamagefile_by($fielddamagefile, $fielddamagefile_fa );
                    $num=count($result[0]);
                    if ($num==0)
                    {
                        $result2=$this->B_db->get_image_whitoururl($fielddamagefile_logo_code);
                        $image=$result2[0];
                        $ext = pathinfo($image['image_url'], PATHINFO_EXTENSION);
                        $fielddamagefile_logo_url='filefolder/fielddamagefilelogo/'.$fielddamagefile.'.'.$ext;
                        copy($image['image_url'], $fielddamagefile_logo_url);
                        $result=$this->B_company->add_fielddamagefile($fielddamagefile,$fielddamagefile_fa ,$fielddamagefile_logo_url , $fielddamagefile_desc , $fielddamagefile_link, $fielddamagefile_commission,$fielddamagefile_image_code,$fielddamagefile_deactive,$fielddamagefile_mode,$fielddamagefile_organ_therapycontract_conditions_covarage_id);

                        if($result){header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                        ,"data"=>array('fielddamagefile_id'=>'')
                        ,'desc'=>'رشته بیمه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); }
                        else{
                            header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                            ,"data"=>array('fielddamagefile_id'=>'')
                            ,'desc'=>'رشته بیمه اضافه نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                    }else{
                        $fielddamagefile=$result[0];
                        header('Content-Type: application/json'); echo json_encode(array('result'=>"error"
                        ,"data"=>array('fielddamagefile_id'=>$fielddamagefile['fielddamagefile_id'])
                        ,'desc'=>'رشته بیمه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                }else{
                    header('Content-Type: application/json'); echo json_encode(array('result'=>$employeetoken[0]
                    ,"data"=>$employeetoken[1]
                    ,'desc'=>$employeetoken[2]));
                }
            }
else
            if($command == 'get_fielddamagefile'){
                $condition = "";
                if($this->post('filter1') != ''){
                    $filter1=$this->post('filter1');
                    $condition.=$filter1;}
                else{
                    $condition.=" 1=1 ";
                }

                $condition.=" AND ";
                if($this->post('filter2') != ''){
                    $filter2=$this->post('filter2');
                    $condition.=$filter2;
                }
                else{
                    $condition.=" 1=1 ";
                }
                $condition.=" AND ";
                if($this->post('filter3') != ''){
                    $filter3=$this->post('filter3');
                    $condition.=$filter3;}else{$condition.=" 1=1 ";}
                $condition.=" ORDER BY fielddamagefile_id ASC";
                    $result = $this->B_db->get_fielddamagefile($condition);
                foreach($result as $key=>$row)
                {
                    $record=array();
                    $record['fielddamagefile_id']=$row['fielddamagefile_id'];
                    $record['fielddamagefile']=$row['fielddamagefile'];
                    $record['fielddamagefile_fa']=$row['fielddamagefile_fa'];
                    $record['fielddamagefile_logo_url']=IMGADD.$row['fielddamagefile_logo_url'];
                    $record['fielddamagefile_desc']=$row['fielddamagefile_desc'];
                    $record['fielddamagefile_link']=$row['fielddamagefile_link'];
                    $record['fielddamagefile_image_code']=$row['fielddamagefile_image_code'];
                    //****************************************************************************
                    $image = $this->B_db->get_image($row['fielddamagefile_image_code']);
                    //*******************************************************************
                    if(!empty($image)){
                        if($image[0]['image_tumb_url']){
                            $record['fielddamagefile_timage']=$image[0]['image_tumb_url'];}
                        if($image[0]['image_url']){
                            $record['fielddamagefile_image']=$image[0]['image_url'];}
                    }
                    $record['fielddamagefile_deactive']=$row['fielddamagefile_deactive'];
                    $record['fielddamagefile_mode']=$row['fielddamagefile_mode'];
                    $record['organ_therapycontract_conditions_covarage_id']=$row['organ_therapycontract_conditions_covarage_id'];
                    $record['organ_therapycontract_conditions_covarage_name']=$row['organ_therapycontract_conditions_covarage_name'];

                    $output[]=$record;
                }
                header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                ,"data"=>$output
                ,'desc'=>'مشحصات رشته بیمه با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }
            else
                if($command == 'get_offline_fielddamagefile'){

;

                    $condition=" fielddamagefile_mode=1 ORDER BY fielddamagefile_id ASC";
                    $result = $this->B_db->get_fielddamagefile($condition);
                    foreach($result as $key=>$row)
                    {
                        $record=array();
                        $record['fielddamagefile_id']=$row['fielddamagefile_id'];
                        $record['fielddamagefile']=$row['fielddamagefile'];
                        $record['fielddamagefile_fa']=$row['fielddamagefile_fa'];
                        $record['fielddamagefile_logo_url']=IMGADD.$row['fielddamagefile_logo_url'];
                        $record['fielddamagefile_desc']=$row['fielddamagefile_desc'];
                        $record['fielddamagefile_group']=$row['fielddamagefile_group'];
                        $record['fielddamagefile_link']=$row['fielddamagefile_link'];
                        $record['fielddamagefile_image_code']=$row['fielddamagefile_image_code'];
                        //****************************************************************************
                        $image = $this->B_db->get_image($row['fielddamagefile_image_code']);
                        //*******************************************************************
                        if(!empty($image)){
                            if($image[0]['image_tumb_url']){
                                $record['fielddamagefile_timage']=$image[0]['image_tumb_url'];}
                            if($image[0]['image_url']){
                                $record['fielddamagefile_image']=$image[0]['image_url'];}
                        }
                        $record['fielddamagefile_deactive']=$row['fielddamagefile_deactive'];
                        $record['fielddamagefile_mode']=$row['fielddamagefile_mode'];
                        $output[]=$record;
                    }
                    header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'مشحصات رشته بیمه با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
                else
                if ($command=="delete_fielddamagefile")
                {
                    $fielddamagefile_id=$this->post('fielddamagefile_id') ;
                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','fielddamagefile');
                    if($employeetoken[0]=='ok')
                    {
                        $output = array();
                        $result = $this->B_company->del_fielddamagefile($fielddamagefile_id);
                        if($result){header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                        ,"data"=>$output
                        ,'desc'=>'رشته بیمه مورد نظر حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            header('Content-Type: application/json'); echo json_encode(array('result'=>"error"
                            ,"data"=>$output
                            ,'desc'=>'رشته بیمه مورد نظر حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                    }else{
                        header('Content-Type: application/json'); echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]));
                    }
                }
                else
                    if ($command=="modify_fielddamagefile")
                    {
                        $fielddamagefile_id=$this->post('fielddamagefile_id') ;
                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','fielddamagefile');
                        if($employeetoken[0]=='ok')
                        {
                            $query="UPDATE fielddamagefile_tb SET ";
                            if(isset($_REQUEST['fielddamagefile_fa'])){
                                $fielddamagefile_fa=$this->post('fielddamagefile_fa') ;
                                $query.="fielddamagefile_fa='".$fielddamagefile_fa."'";}
                            if(isset($_REQUEST['fielddamagefile_logo_code'])&&(isset($_REQUEST['fielddamagefile_fa']))){ $query.=",";}
                            if(isset($_REQUEST['fielddamagefile_logo_code'])){
                                $fielddamagefile_logo_code=$this->post('fielddamagefile_logo_code') ;
                                $fielddamagefile_logo_code=$fielddamagefile_logo_code;
                                $result2=$this->B_db->get_image_whitoururl($fielddamagefile_logo_code);
                                $image=$result2[0];
                                $ext = pathinfo($image['image_url'], PATHINFO_EXTENSION);
                                $fielddamagefile_logo_url='filefolder/fielddamagefilelogo/'.$fielddamagefile_id.'.'.$ext;
                                copy($image['image_url'], $fielddamagefile_logo_url);
                                $query.="fielddamagefile_logo_url='".$fielddamagefile_logo_url."'";
                            }
                            if(isset($_REQUEST['fielddamagefile_desc'])&&(isset($_REQUEST['fielddamagefile_fa'])||isset($_REQUEST['fielddamagefile_logo_code']))){ $query.=",";}
                            if(isset($_REQUEST['fielddamagefile_desc'])){
                                $fielddamagefile_desc=$this->post('fielddamagefile_desc') ;
                                $query.="fielddamagefile_desc='".$fielddamagefile_desc."'";}

                            if(isset($_REQUEST['fielddamagefile_link'])&&(isset($_REQUEST['fielddamagefile_fa'])||isset($_REQUEST['fielddamagefile_logo_code'])||isset($_REQUEST['fielddamagefile_desc']))){$query.=",";}
                            if(isset($_REQUEST['fielddamagefile_link'])){
                                $fielddamagefile_link=$this->post('fielddamagefile_link') ;
                                $query.="fielddamagefile_link='".$fielddamagefile_link."' ";}

                            if(isset($_REQUEST['fielddamagefile_image_code'])&&(isset($_REQUEST['fielddamagefile_link'])||iisset($_REQUEST['fielddamagefile_fa'])||isset($_REQUEST['fielddamagefile_logo_code'])||isset($_REQUEST['fielddamagefile_desc']))){$query.=",";}
                            if(isset($_REQUEST['fielddamagefile_image_code'])){
                                $fielddamagefile_image_code=$this->post('fielddamagefile_image_code') ;
                                $query.="fielddamagefile_image_code='".$fielddamagefile_image_code."' ";}

                            if(isset($_REQUEST['fielddamagefile_deactive'])&&(isset($_REQUEST['fielddamagefile_image_code'])||isset($_REQUEST['fielddamagefile_link'])||isset($_REQUEST['fielddamagefile_fa'])||isset($_REQUEST['fielddamagefile_logo_code'])||isset($_REQUEST['fielddamagefile_desc']))){$query.=",";}
                            if(isset($_REQUEST['fielddamagefile_deactive'])){
                                $fielddamagefile_deactive=$this->post('fielddamagefile_deactive') ;
                                $query.="fielddamagefile_deactive=".$fielddamagefile_deactive." ";}

                            if(isset($_REQUEST['fielddamagefile'])&&(isset($_REQUEST['fielddamagefile_deactive'])||isset($_REQUEST['fielddamagefile_image_code'])||isset($_REQUEST['fielddamagefile_link'])||isset($_REQUEST['fielddamagefile_fa'])||isset($_REQUEST['fielddamagefile_logo_code'])||isset($_REQUEST['fielddamagefile_desc']))){$query.=",";}
                            if(isset($_REQUEST['fielddamagefile'])){
                                $fielddamagefile=$this->post('fielddamagefile') ;
                                $query.="fielddamagefile='".$fielddamagefile."' ";}


                            if(isset($_REQUEST['fielddamagefile_commission'])&&(isset($_REQUEST['fielddamagefile'])||isset($_REQUEST['fielddamagefile_deactive'])||isset($_REQUEST['fielddamagefile_image_code'])||isset($_REQUEST['fielddamagefile_link'])||isset($_REQUEST['fielddamagefile_fa'])||isset($_REQUEST['fielddamagefile_logo_code'])||isset($_REQUEST['fielddamagefile_desc']))){$query.=",";}
                            if(isset($_REQUEST['fielddamagefile_commission'])){
                                $fielddamagefile_commission=$this->post('fielddamagefile_commission') ;
                                $query.="fielddamagefile_commission='".$fielddamagefile_commission."' ";}

                            if(isset($_REQUEST['fielddamagefile_mode'])&&(isset($_REQUEST['fielddamagefile_commission'])||isset($_REQUEST['fielddamagefile'])||isset($_REQUEST['fielddamagefile_deactive'])||isset($_REQUEST['fielddamagefile_image_code'])||isset($_REQUEST['fielddamagefile_link'])||isset($_REQUEST['fielddamagefile_fa'])||isset($_REQUEST['fielddamagefile_logo_code'])||isset($_REQUEST['fielddamagefile_desc']))){$query.=",";}
                            if(isset($_REQUEST['fielddamagefile_mode'])){
                                $fielddamagefile_mode=$this->post('fielddamagefile_mode') ;
                                $query.="fielddamagefile_mode=".$fielddamagefile_mode." ";}

                            if(isset($_REQUEST['fielddamagefile_organ_therapycontract_conditions_covarage_id'])&&(isset($_REQUEST['fielddamagefile_mode'])||isset($_REQUEST['fielddamagefile_commission'])||isset($_REQUEST['fielddamagefile'])||isset($_REQUEST['fielddamagefile_deactive'])||isset($_REQUEST['fielddamagefile_image_code'])||isset($_REQUEST['fielddamagefile_link'])||isset($_REQUEST['fielddamagefile_fa'])||isset($_REQUEST['fielddamagefile_logo_code'])||isset($_REQUEST['fielddamagefile_desc']))){$query.=",";}
                            if(isset($_REQUEST['fielddamagefile_organ_therapycontract_conditions_covarage_id'])){
                                $fielddamagefile_organ_therapycontract_conditions_covarage_id=$this->post('fielddamagefile_organ_therapycontract_conditions_covarage_id') ;
                                $query.="fielddamagefile_organ_therapycontract_conditions_covarage_id=".$fielddamagefile_organ_therapycontract_conditions_covarage_id." ";}



                            $query.=" where fielddamagefile_id=".$fielddamagefile_id;
                            $result=$this->B_db->run_query_put($query);
                            if($result){
                                header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                                ,"data"=>$query
                                ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }else {
                                header('Content-Type: application/json'); echo json_encode(array('result'=>"error"
                                ,"data"=>""
                                ,'desc'=>'تغییرات انجام نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }

                        }else{
                            header('Content-Type: application/json'); echo json_encode(array('result'=>$employeetoken[0]
                            ,"data"=>$employeetoken[1]
                            ,'desc'=>$employeetoken[2]));
                        }
                    }
                    else
                        if ($command=="deactive_fielddamagefile")
                        {
                            $fielddamagefile_id=$this->post('fielddamagefile_id') ;
                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','fielddamagefile');
                            if($employeetoken[0]=='ok')
                            {
                                $result=$this->B_company->update_fielddamagefile($fielddamagefile_id, 1);
                                if($result){
                                    header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                                    ,"data"=>""
                                    ,'desc'=>'رشته بیمه  غیر فعال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }else {
                                    header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                                    ,"data"=>""
                                    ,'desc'=>'رشته بیمه  غیر فعال  نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }
                            }else{
                                header('Content-Type: application/json'); echo json_encode(array('result'=>$employeetoken[0]
                                ,"data"=>$employeetoken[1]
                                ,'desc'=>$employeetoken[2]));
                            }
                        }
                        else
                            if ($command=="active_fielddamagefile")
                            {
                                $fielddamagefile_id=$this->post('fielddamagefile_id') ;
                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','fielddamagefile');
                                if($employeetoken[0]=='ok')
                                {
                                    $result=$this->B_company->update_fielddamagefile($fielddamagefile_id, 0);
                                    if($result){
                                        header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                                        ,"data"=>""
                                        ,'desc'=>'رشته بیمه فعال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }else {
                                        header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                                        ,"data"=>""
                                        ,'desc'=>'رشته بیمه فعال نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }
                                }else{
                                    header('Content-Type: application/json'); echo json_encode(array('result'=>$employeetoken[0]
                                    ,"data"=>$employeetoken[1]
                                    ,'desc'=>$employeetoken[2]));

                                }
                            }
    }
}
}