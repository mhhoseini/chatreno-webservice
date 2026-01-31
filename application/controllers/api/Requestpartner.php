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
class Requestpartner extends REST_Controller {

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
        
        if (isset($this->input->request_headers()['Authorization'])) $this->user_token_str = $this->input->request_headers()['Authorization'];
        if ($command=="add")
        {
            $requestpartner_name=$this->post('requestpartner_name') ;
            $requestpartner_family=$this->post('requestpartner_family') ;
            $requestpartner_nationalcode=$this->post('requestpartner_nationalcode');
            $requestpartner_state_id=$this->post('requestpartner_state_id');
            $requestpartner_city_id=$this->post('requestpartner_city_id');
            $requestpartner_address=$this->post('requestpartner_address');
            $requestpartner_tell=$this->post('requestpartner_tell');
            $requestpartner_fieldofstudy=$this->post('requestpartner_fieldofstudy');
            $requestpartner_lastjob=$this->post('requestpartner_lastjob');
            $requestpartner_insurancehistory=$this->post('requestpartner_insurancehistory');
            $requestpartner_type=$this->post('requestpartner_type');
            $requestpartner_method=$this->post('requestpartner_method');
            $requestpartner_imageId=$this->post('requestpartner_imageId');
            $requestpartner_fileId=$this->post('requestpartner_fileId');
            $usertoken=checkusertoken($this->user_token_str);
            if($usertoken[0]=='ok')
            {
                $user_id =$usertoken[1];
                $sql = "SELECT requestpartner_id,requestpartner_name,requestpartner_family FROM request_partner_tb where requestpartner_nationalcode='".$requestpartner_nationalcode."'";
                $res = $this->B_db->run_query($sql);
                if($res){
                    $data = array('requestpartner_name'=>$res[0]['requestpartner_name'],'requestpartner_family'=>$res[0]['requestpartner_family'],'requestpartner_id'=>$res[0]['requestpartner_id']);
                    echo json_encode(array('result'=>"error"
                    ,"data"=>$data
                    ,'desc'=>$desc.'کدملی فرد مورد نظر قبلا در سیستم وارد شده است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
                else{
                    $query="INSERT INTO request_partner_tb
                    (requestpartner_user_id, requestpartner_name, requestpartner_family, requestpartner_nationalcode, 
                    requestpartner_state_id, requestpartner_city_id, requestpartner_address, requestpartner_tell, requestpartner_educationlevel, 
                    requestpartner_fieldofstudy, requestpartner_lastjob, requestpartner_insurancehistory, requestpartner_type, requestpartner_method, 
                    requestpartner_imageId, requestpartner_fileId, requestpartner_date)
                    VALUES($user_id, '".$requestpartner_name."','". $requestpartner_family."','".$requestpartner_nationalcode."',
                    $requestpartner_state_id , $requestpartner_city_id,'".$requestpartner_address."','".$requestpartner_tell."','".$requestpartner_educationlevel."',
                    '".$requestpartner_fieldofstudy."','". $requestpartner_lastjob."','". $requestpartner_insurancehistory."', '".$requestpartner_type."','".$requestpartner_method."',
                    '".$requestpartner_imageId."', '".$requestpartner_fileId."',now())";
                    $result = $this->B_db->run_query_put($query);
                    $requestpartnerid=$this->db->insert_id();
                    if($result){
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>array('request_partner_id'=>$requestpartnerid)
                        ,'desc'=>'اطلاعات با موفقیت ثبت گردید'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); 
                    }
                    else{
                        echo json_encode(array('result'=>"error"
                        ,'desc'=>$desc.' خطایی در ورود اطلاعات وجود دارد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                }
            }else{
                echo json_encode(array('result'=>$usertoken[0]
                ,"data"=>$usertoken[1]
                ,'desc'=>$usertoken[2]));
            } 
        }
        if ($command=="get")
        {
            $usertoken=checkpermissionemployeetoken($employee_token_str,'new','partner');
            $requestpartner_id=$this->post('requestpartner_id');
            $requestpartner_nationalcode=$this->post('requestpartner_nationalcode');
            if($usertoken[0]=='ok')
            {
                $user_id =$usertoken[1];
                $query="select * from request_partner_tb where ";
                if($requestpartner_id=='' and $requestpartner_nationalcode==''){
                    $result=$this->B_db->run_query($query);
                    echo json_encode(array('result'=>"error"
                    ,'desc'=>'مقادیر ارسالی نمی تواند تهی باشد!'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    if($requestpartner_id!='')
                        $query.="requestpartner_id=".$requestpartner_id;
                    elseif($requestpartner_nationalcode!='')
                        $query.="requestpartner_nationalcode=".$requestpartner_nationalcode;
                    $result=$this->B_db->run_query($query);
                    if($result){
                        echo json_encode(array('result'=>"error"
                        ,"data"=>$result[0]
                        ,'desc'=>'مشخصات با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        echo json_encode(array('result'=>"ok"
                        ,'desc'=>'هیچ رکوردی یافت نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                    
                }
            }else{
                echo json_encode(array('result'=>$usertoken[0]
                ,"data"=>$usertoken[1]
                ,'desc'=>$usertoken[2]));
            } 
        }
        if ($command=="get_requestpartner")
        {
            $filter=" 1=1 ";
            if(isset($_REQUEST['fieldinsurance'])){
                $fieldinsurance=$this->post('fieldinsurance') ;
                $filter=" request_partner_fieldinsurance='$fieldinsurance' ";
            }

            $query="select * from request_partner_tb,fieldinsurance_tb where fieldinsurance=request_partner_fieldinsurance AND $filter ORDER BY request_partner_id ASC";
            $result = $this->B_db->run_query($query);
            $output =array();
            foreach($result as $row)
            {
                $record=array();
                $record['request_partner_id']=$row['request_partner_id'];
                $record['request_partner_fieldinsurance']=$row['request_partner_fieldinsurance'];
                $record['fieldinsurance_fa']=$row['fieldinsurance_fa'];
                $record['request_partner_name']=$row['request_partner_name'];
                $record['request_partner_desc']=$row['request_partner_desc'];
                $record['request_partner_active']=$row['request_partner_active'];
                 $output[]=$record;
            }
            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'مشحصات عکس های مورد نیاز رشته با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

        }
        else
            if ($command=="delete_requestpartner")
            {
                $request_partner_id=$this->post('request_partner_id') ;

                $usertoken=checkpermissionemployeetoken($employee_token_str,'delete','requestpartner');
                if($usertoken[0]=='ok')
                {

                    $query="DELETE FROM request_partner_tb  where request_partner_id=".$request_partner_id."";
                    $result = $this->B_db->run_query_put($query);
                    if($result){echo json_encode(array('result'=>"ok"
                    ,"data"=>''
                    ,'desc'=>'عکس های مورد نیاز رشته مورد نظر حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        echo json_encode(array('result'=>"error"
                        ,"data"=>''
                        ,'desc'=>'عکس های مورد نیاز رشته مورد نظر حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                }else{
                    echo json_encode(array('result'=>$usertoken[0]
                    ,"data"=>$usertoken[1]
                    ,'desc'=>$usertoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                }

            }
            else
                if ($command=="modify_requestpartner") {
                    $request_partner_id = $this->post('request_partner_id');

                    $usertoken = checkpermissionemployeetoken($employee_token_str, 'modify', 'requestpartner');
                    if ($usertoken[0] == 'ok') {
                        $query = "UPDATE request_partner_tb SET ";
                        if (isset($_REQUEST['request_partner_name'])) {
                            $request_partner_name = $this->post('request_partner_name');
                            $query .= "request_partner_name='" . $request_partner_name . "'";
                        }

                        if (isset($_REQUEST['request_partner_desc']) && (isset($_REQUEST['request_partner_name']))) {
                            $query .= ",";
                        }
                        if (isset($_REQUEST['request_partner_desc'])) {
                            $request_partner_desc = $this->post('request_partner_desc');
                            $query .= "request_partner_desc='" . $request_partner_desc . "'";
                        }


                        if (isset($_REQUEST['request_partner_fieldinsurance']) && (isset($_REQUEST['request_partner_name']) || isset($_REQUEST['request_partner_desc']))) {
                            $query .= ",";
                        }
                        if (isset($_REQUEST['request_partner_fieldinsurance'])) {
                            $request_partner_fieldinsurance = $this->post('request_partner_fieldinsurance');
                            $query .= "request_partner_fieldinsurance='" . $request_partner_fieldinsurance . "' ";
                        }

						 if (isset($_REQUEST['request_partner_active']) && (isset($_REQUEST['request_partner_fieldinsurance'])||isset($_REQUEST['request_partner_name']) || isset($_REQUEST['request_partner_desc']) )) {
                            $query .= ",";
                        }
                        if (isset($_REQUEST['request_partner_active'])) {
                            $request_partner_active = $this->post('request_partner_active');
                            $query .= "request_partner_active=" . $request_partner_active . " ";
                        }
						

                        $query .= " where request_partner_id=" . $request_partner_id;

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
                    } else {
                        echo json_encode(array('result' => $usertoken[0]
                        , "data" => $usertoken[1]
                        , 'desc' => $usertoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                    }

                }
    }
}
