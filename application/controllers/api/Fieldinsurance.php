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
class Fieldinsurance extends REST_Controller {

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
        if($this->B_user->checkrequestip('fieldindurance',$command,get_client_ip(),50,50)) {
            if ($command=="add_fieldinsurance")
            {
                $fieldinsurance_id=$this->post('fieldinsurance_id') ;
                $fieldinsurance=$this->post('fieldinsurance') ;
                $fieldinsurance_fa=$this->post('fieldinsurance_fa') ;
                $fieldinsurance_logo_code=$this->post('fieldinsurance_logo_code') ;
                $fieldinsurance_desc=$this->post('fieldinsurance_desc') ;
                $fieldinsurance_link=$this->post('fieldinsurance_link') ;
                $fieldinsurance_commission=$this->post('fieldinsurance_commission') ;
                $fieldinsurance_image_code=$this->post('fieldinsurance_image_code') ;
                $fieldinsurance_deactive=$this->post('fieldinsurance_deactive') ;
                $fieldinsurance_mode=$this->post('fieldinsurance_mode') ;
                $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','fieldinsurance');
                if($employeetoken[0]=='ok')
                {
                    $result=$this->B_company->get_fieldinsurance_by($fieldinsurance_id,$fieldinsurance, $fieldinsurance_fa );
                    $num=count($result[0]);
                    if ($num==0)
                    {
                        $result2=$this->B_db->get_image_whitoururl($fieldinsurance_logo_code);
                        $image=$result2[0];
                        $ext = pathinfo($image['image_url'], PATHINFO_EXTENSION);
                        $fieldinsurance_logo_url='filefolder/fieldinsurancelogo/'.$fieldinsurance_id.'.'.$ext;
                        copy($image['image_url'], $fieldinsurance_logo_url);
                        $result=$this->B_company->add_fieldinsurance($fieldinsurance_id,$fieldinsurance,$fieldinsurance_fa ,$fieldinsurance_logo_url , $fieldinsurance_desc , $fieldinsurance_link, $fieldinsurance_commission,$fieldinsurance_image_code,$fieldinsurance_deactive,$fieldinsurance_mode);
                        if($result){echo json_encode(array('result'=>"ok"
                        ,"data"=>array('fieldinsurance_id'=>$fieldinsurance_id)
                        ,'desc'=>'رشته بیمه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); }
                        else{
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>array('fieldinsurance_id'=>$fieldinsurance_id)
                            ,'desc'=>'رشته بیمه اضافه نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                    }else{
                        $fieldinsurance=$result[0];
                        echo json_encode(array('result'=>"error"
                        ,"data"=>array('fieldinsurance_id'=>$fieldinsurance['fieldinsurance_id'])
                        ,'desc'=>'رشته بیمه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                }else{
                    echo json_encode(array('result'=>$employeetoken[0]
                    ,"data"=>$employeetoken[1]
                    ,'desc'=>$employeetoken[2]));
                }
            }
else
            if($command == 'get_fieldinsurance'){
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
                $condition.=" ORDER BY fieldinsurance_id ASC";
                    $result = $this->B_db->get_fieldinsurance($condition);
                foreach($result as $key=>$row)
                {
                    $record=array();
                    $record['fieldinsurance_id']=$row['fieldinsurance_id'];
                    $record['fieldinsurance']=$row['fieldinsurance'];
                    $record['fieldinsurance_fa']=$row['fieldinsurance_fa'];
                    $record['fieldinsurance_logo_url']=IMGADD.$row['fieldinsurance_logo_url'];
                    $record['fieldinsurance_desc']=$row['fieldinsurance_desc'];
                    $record['fieldinsurance_link']=$row['fieldinsurance_link'];
                    $record['fieldinsurance_image_code']=$row['fieldinsurance_image_code'];
                    //****************************************************************************
                    $image = $this->B_db->get_image($row['fieldinsurance_image_code']);
                    //*******************************************************************
                    if(!empty($image)){
                        if($image[0]['image_tumb_url']){
                            $record['fieldinsurance_timage']=$image[0]['image_tumb_url'];}
                        if($image[0]['image_url']){
                            $record['fieldinsurance_image']=$image[0]['image_url'];}
                    }
                    $record['fieldinsurance_deactive']=$row['fieldinsurance_deactive'];
                    $record['fieldinsurance_mode']=$row['fieldinsurance_mode'];
                    $output[]=$record;
                }
                echo json_encode(array('result'=>"ok"
                ,"data"=>$output
                ,'desc'=>'مشحصات رشته بیمه با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }
            else
                if($command == 'get_offline_fieldinsurance'){

;

                    $condition=" fieldinsurance_mode=1 ORDER BY fieldinsurance_id ASC";
                    $result = $this->B_db->get_fieldinsurance($condition);
                    foreach($result as $key=>$row)
                    {
                        $record=array();
                        $record['fieldinsurance_id']=$row['fieldinsurance_id'];
                        $record['fieldinsurance']=$row['fieldinsurance'];
                        $record['fieldinsurance_fa']=$row['fieldinsurance_fa'];
                        $record['fieldinsurance_logo_url']=IMGADD.$row['fieldinsurance_logo_url'];
                        $record['fieldinsurance_desc']=$row['fieldinsurance_desc'];
                        $record['fieldinsurance_group']=$row['fieldinsurance_group'];
                        $record['fieldinsurance_link']=$row['fieldinsurance_link'];
                        $record['fieldinsurance_image_code']=$row['fieldinsurance_image_code'];
                        //****************************************************************************
                        $image = $this->B_db->get_image($row['fieldinsurance_image_code']);
                        //*******************************************************************
                        if(!empty($image)){
                            if($image[0]['image_tumb_url']){
                                $record['fieldinsurance_timage']=$image[0]['image_tumb_url'];}
                            if($image[0]['image_url']){
                                $record['fieldinsurance_image']=$image[0]['image_url'];}
                        }
                        $record['fieldinsurance_deactive']=$row['fieldinsurance_deactive'];
                        $record['fieldinsurance_mode']=$row['fieldinsurance_mode'];
                        $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'مشحصات رشته بیمه با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
                else
                    if($command == 'get_extra_fieldinsurance'){

                        $condition=" fieldinsurance_mode=2 ORDER BY fieldinsurance_id ASC";
                        $result = $this->B_db->get_fieldinsurance($condition);
                        foreach($result as $key=>$row)
                        {
                            $record=array();
                            $record['fieldinsurance_id']=$row['fieldinsurance_id'];
                            $record['fieldinsurance']=$row['fieldinsurance'];
                            $record['fieldinsurance_fa']=$row['fieldinsurance_fa'];
                            $record['fieldinsurance_logo_url']=IMGADD.$row['fieldinsurance_logo_url'];
                            $record['fieldinsurance_desc']=$row['fieldinsurance_desc'];
                            $record['fieldinsurance_group']=$row['fieldinsurance_group'];
                            $record['fieldinsurance_link']=$row['fieldinsurance_link'];
                            $record['fieldinsurance_image_code']=$row['fieldinsurance_image_code'];
                            //****************************************************************************
                            $image = $this->B_db->get_image($row['fieldinsurance_image_code']);
                            //*******************************************************************
                            if(!empty($image)){
                                if($image[0]['image_tumb_url']){
                                    $record['fieldinsurance_timage']=$image[0]['image_tumb_url'];}
                                if($image[0]['image_url']){
                                    $record['fieldinsurance_image']=$image[0]['image_url'];}
                            }
                            $record['fieldinsurance_deactive']=$row['fieldinsurance_deactive'];
                            $record['fieldinsurance_mode']=$row['fieldinsurance_mode'];
                            $output[]=$record;
                        }
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>$output
                        ,'desc'=>'مشحصات رشته بیمه با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                else
                if ($command=="delete_fieldinsurance")
                {
                    $fieldinsurance_id=$this->post('fieldinsurance_id') ;
                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','fieldinsurance');
                    if($employeetoken[0]=='ok')
                    {
                        $output = array();
                        $result = $this->B_company->del_fieldinsurance($fieldinsurance_id);
                        if($result){echo json_encode(array('result'=>"ok"
                        ,"data"=>$output
                        ,'desc'=>'رشته بیمه مورد نظر حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            echo json_encode(array('result'=>"error"
                            ,"data"=>$output
                            ,'desc'=>'رشته بیمه مورد نظر حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]));
                    }
                }
                else
                    if ($command=="modify_fieldinsurance")
                    {
                        $fieldinsurance_id=$this->post('fieldinsurance_id') ;
                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','fieldinsurance');
                        if($employeetoken[0]=='ok')
                        {
                            $query="UPDATE fieldinsurance_tb SET ";
                            if(isset($_REQUEST['fieldinsurance_fa'])){
                                $fieldinsurance_fa=$this->post('fieldinsurance_fa') ;
                                $query.="fieldinsurance_fa='".$fieldinsurance_fa."'";}
                            if(isset($_REQUEST['fieldinsurance_logo_code'])&&(isset($_REQUEST['fieldinsurance_fa']))){ $query.=",";}
                            if(isset($_REQUEST['fieldinsurance_logo_code'])){
                                $fieldinsurance_logo_code=$this->post('fieldinsurance_logo_code') ;
                                $fieldinsurance_logo_code=$fieldinsurance_logo_code;
                                $result2=$this->B_db->get_image_whitoururl($fieldinsurance_logo_code);
                                $image=$result2[0];
                                $ext = pathinfo($image['image_url'], PATHINFO_EXTENSION);
                                $fieldinsurance_logo_url='filefolder/fieldinsurancelogo/'.$fieldinsurance_id.'.'.$ext;
                                copy($image['image_url'], $fieldinsurance_logo_url);
                                $query.="fieldinsurance_logo_url='".$fieldinsurance_logo_url."'";
                            }
                            if(isset($_REQUEST['fieldinsurance_desc'])&&(isset($_REQUEST['fieldinsurance_fa'])||isset($_REQUEST['fieldinsurance_logo_code']))){ $query.=",";}
                            if(isset($_REQUEST['fieldinsurance_desc'])){
                                $fieldinsurance_desc=$this->post('fieldinsurance_desc') ;
                                $query.="fieldinsurance_desc='".$fieldinsurance_desc."'";}

                            if(isset($_REQUEST['fieldinsurance_link'])&&(isset($_REQUEST['fieldinsurance_fa'])||isset($_REQUEST['fieldinsurance_logo_code'])||isset($_REQUEST['fieldinsurance_desc']))){$query.=",";}
                            if(isset($_REQUEST['fieldinsurance_link'])){
                                $fieldinsurance_link=$this->post('fieldinsurance_link') ;
                                $query.="fieldinsurance_link='".$fieldinsurance_link."' ";}

                            if(isset($_REQUEST['fieldinsurance_image_code'])&&(isset($_REQUEST['fieldinsurance_link'])||iisset($_REQUEST['fieldinsurance_fa'])||isset($_REQUEST['fieldinsurance_logo_code'])||isset($_REQUEST['fieldinsurance_desc']))){$query.=",";}
                            if(isset($_REQUEST['fieldinsurance_image_code'])){
                                $fieldinsurance_image_code=$this->post('fieldinsurance_image_code') ;
                                $query.="fieldinsurance_image_code='".$fieldinsurance_image_code."' ";}

                            if(isset($_REQUEST['fieldinsurance_deactive'])&&(isset($_REQUEST['fieldinsurance_image_code'])||isset($_REQUEST['fieldinsurance_link'])||isset($_REQUEST['fieldinsurance_fa'])||isset($_REQUEST['fieldinsurance_logo_code'])||isset($_REQUEST['fieldinsurance_desc']))){$query.=",";}
                            if(isset($_REQUEST['fieldinsurance_deactive'])){
                                $fieldinsurance_deactive=$this->post('fieldinsurance_deactive') ;
                                $query.="fieldinsurance_deactive=".$fieldinsurance_deactive." ";}

                            if(isset($_REQUEST['fieldinsurance'])&&(isset($_REQUEST['fieldinsurance_deactive'])||isset($_REQUEST['fieldinsurance_image_code'])||isset($_REQUEST['fieldinsurance_link'])||isset($_REQUEST['fieldinsurance_fa'])||isset($_REQUEST['fieldinsurance_logo_code'])||isset($_REQUEST['fieldinsurance_desc']))){$query.=",";}
                            if(isset($_REQUEST['fieldinsurance'])){
                                $fieldinsurance=$this->post('fieldinsurance') ;
                                $query.="fieldinsurance='".$fieldinsurance."' ";}


                            if(isset($_REQUEST['fieldinsurance_commission'])&&(isset($_REQUEST['fieldinsurance'])||isset($_REQUEST['fieldinsurance_deactive'])||isset($_REQUEST['fieldinsurance_image_code'])||isset($_REQUEST['fieldinsurance_link'])||isset($_REQUEST['fieldinsurance_fa'])||isset($_REQUEST['fieldinsurance_logo_code'])||isset($_REQUEST['fieldinsurance_desc']))){$query.=",";}
                            if(isset($_REQUEST['fieldinsurance_commission'])){
                                $fieldinsurance_commission=$this->post('fieldinsurance_commission') ;
                                $query.="fieldinsurance_commission='".$fieldinsurance_commission."' ";}

                            if(isset($_REQUEST['fieldinsurance_mode'])&&(isset($_REQUEST['fieldinsurance_commission'])||isset($_REQUEST['fieldinsurance'])||isset($_REQUEST['fieldinsurance_deactive'])||isset($_REQUEST['fieldinsurance_image_code'])||isset($_REQUEST['fieldinsurance_link'])||isset($_REQUEST['fieldinsurance_fa'])||isset($_REQUEST['fieldinsurance_logo_code'])||isset($_REQUEST['fieldinsurance_desc']))){$query.=",";}
                            if(isset($_REQUEST['fieldinsurance_mode'])){
                                $fieldinsurance_mode=$this->post('fieldinsurance_mode') ;
                                $query.="fieldinsurance_mode=".$fieldinsurance_mode." ";}


                            $query.=" where fieldinsurance_id=".$fieldinsurance_id;
                            $result=$this->B_db->run_query_put($query);
                            if($result){
                                echo json_encode(array('result'=>"ok"
                                ,"data"=>""
                                ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }else {
                                echo json_encode(array('result'=>"error"
                                ,"data"=>""
                                ,'desc'=>'تغییرات انجام نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }

                        }else{
                            echo json_encode(array('result'=>$employeetoken[0]
                            ,"data"=>$employeetoken[1]
                            ,'desc'=>$employeetoken[2]));
                        }
                    }
                    else
                        if ($command=="deactive_fieldinsurance")
                        {
                            $fieldinsurance_id=$this->post('fieldinsurance_id') ;
                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','fieldinsurance');
                            if($employeetoken[0]=='ok')
                            {
                                $result=$this->B_company->update_fieldinsurance($fieldinsurance_id, 1);
                                if($result){
                                    echo json_encode(array('result'=>"ok"
                                    ,"data"=>""
                                    ,'desc'=>'رشته بیمه  غیر فعال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }else {
                                    echo json_encode(array('result'=>"ok"
                                    ,"data"=>""
                                    ,'desc'=>'رشته بیمه  غیر فعال  نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }
                            }else{
                                echo json_encode(array('result'=>$employeetoken[0]
                                ,"data"=>$employeetoken[1]
                                ,'desc'=>$employeetoken[2]));
                            }
                        }
                        else
                            if ($command=="active_fieldinsurance")
                            {
                                $fieldinsurance_id=$this->post('fieldinsurance_id') ;
                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','fieldinsurance');
                                if($employeetoken[0]=='ok')
                                {
                                    $result=$this->B_company->update_fieldinsurance($fieldinsurance_id, 0);
                                    if($result){
                                        echo json_encode(array('result'=>"ok"
                                        ,"data"=>""
                                        ,'desc'=>'رشته بیمه فعال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }else {
                                        echo json_encode(array('result'=>"ok"
                                        ,"data"=>""
                                        ,'desc'=>'رشته بیمه فعال نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
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