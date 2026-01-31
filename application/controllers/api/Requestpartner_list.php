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
class Requestpartner_list extends REST_Controller {

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
        if ($command=="get_list")
        {
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','reportrefral');
            if($employeetoken[0]=='ok')
            {
                $requestpartner_active=$this->post('requestpartner_active');

                $query1="select * from request_partner_tb,user_tb,city_tb,state_tb Where requestpartner_active=$requestpartner_active AND city_id=requestpartner_city_id AND state_id=requestpartner_state_id AND user_id=requestpartner_user_id ";
                $query2="select count(*) as cnt from request_partner_tb,user_tb,city_tb,state_tb  Where requestpartner_active=$requestpartner_active AND city_id=requestpartner_city_id AND state_id=requestpartner_state_id AND user_id=requestpartner_user_id ";

                $query='';
                if(!empty($_REQUEST['requestpartner_id'])){
                    $requestpartner_id=$this->post('requestpartner_id');
                    $query.=" AND requestpartner_id=".$requestpartner_id."";
                }
                if(!empty($_REQUEST['requestpartner_name'])){
                    $requestpartner_name=$this->post('requestpartner_name');
                    $query.=" AND requestpartner_name ='%".$requestpartner_name."%'";
                }
                if(!empty($_REQUEST['requestpartner_family'])){
                    $requestpartner_family=$this->post('requestpartner_family');
                    $query.=" AND requestpartner_family like '%".$requestpartner_family."%'";
                }
                if(!empty($_REQUEST['requestpartner_nationalcode'])){
                    $requestpartner_nationalcode=$this->post('requestpartner_nationalcode');
                    $query.=" AND requestpartner_nationalcode ='".$requestpartner_nationalcode."'";
                }
                if(!empty($_REQUEST['requestpartner_state_id'])){
                    $requestpartner_state_id=$this->post('requestpartner_state_id');
                    $query.=" AND requestpartner_state_id =".$requestpartner_state_id;
                }
                if(!empty($_REQUEST['requestpartner_city_id'])){
                    $requestpartner_city_id=$this->post('requestpartner_city_id');
                    $query.=" AND requestpartner_city_id =".$requestpartner_city_id;
                }
                if(!empty($_REQUEST['requestpartner_address'])){
                    $requestpartner_address=$this->post('requestpartner_address');
                    $query.=" AND requestpartner_address ='%".$requestpartner_address."%'";
                }
                if(!empty($_REQUEST['requestpartner_tell'])){
                    $requestpartner_tell=$this->post('requestpartner_tell');
                    $query.=" AND requestpartner_tell ='".$requestpartner_tell."'";
                }
                if(!empty($_REQUEST['requestpartner_fieldofstudy'])){
                    $requestpartner_fieldofstudy=$this->post('requestpartner_fieldofstudy');
                    $query.=" AND requestpartner_fieldofstudy ='".$requestpartner_fieldofstudy."'";
                }
                if(!empty($_REQUEST['requestpartner_lastjob'])){
                    $requestpartner_lastjob=$this->post('requestpartner_lastjob');
                    $query.=" AND requestpartner_lastjob ='".$requestpartner_lastjob."'";
                }
                if(!empty($_REQUEST['requestjob'])){
                    $requestjob=$this->post('requestjob');
                    $query.=" AND requestjob ='".$requestjob."'";
                }
                if(!empty($_REQUEST['requestpartner_insurancehistory'])){
                    $requestpartner_insurancehistory=$this->post('requestpartner_insurancehistory');
                    $query.=" AND requestpartner_insurancehistory ='".$requestpartner_insurancehistory."'";
                }
                if(!empty($_REQUEST['requestpartner_type'])){
                    $requestpartner_type=$this->post('requestpartner_type');
                    $query.=" AND requestpartner_type ='".$requestpartner_type."'";
                }
                if(!empty($_REQUEST['requestpartner_method'])){
                    $requestpartner_method=$this->post('requestpartner_method');
                    $query.=" AND requestpartner_method ='".$requestpartner_method."'";
                }

                $query .= ' ORDER BY requestpartner_id  DESC ';

                $limit = $this->post("limit");
                $offset = $this->post("offset");
                $limit_state ="";
                if($limit!="" & $offset!="") {
                    $limit_state = " LIMIT " . $offset . "," . $limit;
                }
                
                $result = $this->B_db->run_query($query1.$query.$limit_state);
                $count  = $this->B_db->run_query($query2.$query);

                $output =array();
                $request_id='';
                $i = 0;
                foreach ($result as $row) {
                    $imageurl='';
                    $imageturl='';
                        $result1 = $this->B_db->get_image($row['requestpartner_imageId']);
                        $image1 = $result1[0];
                        if ($image1['image_url']) {
                            $imageurl =  $image1['image_url'];
                        }
                    if ($image1['image_tumb_url']) {
                        $imageturl =  $image1['image_tumb_url'];
                    }
                    $result[$i]['requestpartner_image'] = $imageurl;
                    $result[$i]['requestpartner_timage'] = $imageturl;


                    $fileurl='';
                    $result2 = $this->B_db->get_image($row['requestpartner_fileId']);
                    $file = $result2[0];
                    if ($file['image_url']) {
                        $fileurl =  $file['image_url'];
                    }

                    $result[$i]['requestpartner_file'] = $fileurl;



                    $i++;
                }

                if($result)
                {
                    echo json_encode(array('result'=>"ok"
                    ,"cnt"=>$count[0]['cnt']
                    ,"data"=>$result
                    ,'desc'=>'لیست  با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>[]
                    ,'desc'=>'موردی یافت نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));
            }
        }else if ($command=="changerequestmarketer")
        {
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','reportrefral');
            if($employeetoken[0]=='ok')
            {
                $requestpartner_id=$this->post('requestpartner_id') ;
                $requestpartner_reason=$this->post('requestpartner_reason');
                $requestpartner_city=$this->post('requestpartner_city');
                $requestpartner_check_coworker=$this->post('requestpartner_check_coworker');
                $requestpartner_filedinsur_favorit=$this->post('requestpartner_filedinsur_favorit');
                $requestpartner_reason_select=$this->post('requestpartner_reason_select');
                $requestpartner_expect=$this->post('requestpartner_expect');
                $requestpartner_detail=$this->post('requestpartner_detail');

                $query="UPDATE request_partner_tb SET requestpartner_reason='".$requestpartner_reason."',requestpartner_city='".$requestpartner_city."',requestpartner_check_coworker='".$requestpartner_check_coworker."',requestpartner_filedinsur_favorit='".$requestpartner_filedinsur_favorit."',requestpartner_reason_select='".$requestpartner_reason_select."',requestpartner_expect='".$requestpartner_expect."',requestpartner_detail='".$requestpartner_detail."' where requestpartner_id=".$requestpartner_id;


                $result=$this->B_db->run_query_put($query);
                if($result){
             //       send_requestpartnerdeactive_sms($requestpartner_user_id, $requestpartner_reason);
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>""
                    ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
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
        else if ($command=="changeactive")
        {
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','reportrefral');
            if($employeetoken[0]=='ok')
            {
                $requestpartner_id=$this->post('requestpartner_id') ;
                $requestpartner_active=$this->post('requestpartner_active');


                $query="UPDATE request_partner_tb SET requestpartner_active=$requestpartner_active where requestpartner_id=".$requestpartner_id;


                $result=$this->B_db->run_query_put($query);
                if($result){
                    //       send_requestpartnerdeactive_sms($requestpartner_user_id, $requestpartner_reason);
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>""
                    ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
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
