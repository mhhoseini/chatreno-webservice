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
class Marketeractive extends REST_Controller {

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
        if($this->B_user->checkrequestip('marketerrequest',$command,get_client_ip(),50,50)){
        if ($command=="avtive_marketer")
        {
            $marketer_user_id=$this->post('marketer_user_id') ;
            $leader_mode_id=$this->post('leader_mode_id') ;
            $marketer_mode_id=$this->post('marketer_mode_id') ;
            $marketer_reason=$this->post('marketer_reason');
            $marketer_city=$this->post('marketer_city');
            $marketer_check_coworker=$this->post('marketer_check_coworker');
            $marketer_refund=$this->post('marketer_refund');
            $marketer_filedinsur_favorit=$this->post('marketer_filedinsur_favorit');
            $marketer_reason_select=$this->post('marketer_reason_select');
            $marketer_expect=$this->post('marketer_expect');
            $marketer_detail=$this->post('marketer_detail');


	  
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','acvtivemarketer');
            if($employeetoken[0]=='ok')
            {
                //***************************************************************************************************************
                $query="UPDATE usermarketer_tb SET marketer_reject=0 ,marketer_deactive=0,marketer_request=0,marketer_mode_id=$marketer_mode_id,leader_mode_id=$leader_mode_id,marketer_reason='".$marketer_reason."',marketer_city='".$marketer_city."',marketer_check_coworker='".$marketer_check_coworker."',marketer_refund='".$marketer_refund."',marketer_filedinsur_favorit='".$marketer_filedinsur_favorit."',marketer_reason_select='".$marketer_reason_select."',marketer_expect='".$marketer_expect."',marketer_detail='".$marketer_detail."' where marketer_user_id=".$marketer_user_id;


                $result=$this->B_db->run_query_put($query);
                if($result){
                      send_marketeractive_sms($marketer_user_id, '');
                   echo json_encode(array('result'=>"ok"
                    ,"data"=>""
                    ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>""
                    ,'desc'=>'تغییرات انجام نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }


//**************************************************************************************************************s*

            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }

        }else
            if ($command=="deavtive_marketer")
            {
                $marketer_user_id=$this->post('marketer_user_id') ;
                $marketer_reason=$this->post('marketer_reason');
$marketer_city=$this->post('marketer_city');
            $marketer_check_coworker=$this->post('marketer_check_coworker');
            $marketer_refund=$this->post('marketer_refund');
            $marketer_filedinsur_favorit=$this->post('marketer_filedinsur_favorit');
            $marketer_reason_select=$this->post('marketer_reason_select');
            $marketer_expect=$this->post('marketer_expect');
            $marketer_detail=$this->post('marketer_detail');

                $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','acvtivemarketer');
                if($employeetoken[0]=='ok')
                {
                    //***************************************************************************************************************
                    $query="UPDATE usermarketer_tb SET marketer_reject=1 ,marketer_reason='".$marketer_reason."',marketer_city='".$marketer_city."',marketer_check_coworker='".$marketer_check_coworker."',marketer_refund='".$marketer_refund."',marketer_filedinsur_favorit='".$marketer_filedinsur_favorit."',marketer_reason_select='".$marketer_reason_select."',marketer_expect='".$marketer_expect."',marketer_detail='".$marketer_detail."' where marketer_user_id=".$marketer_user_id;


                    $result=$this->B_db->run_query_put($query);
                    if($result){
						send_marketerdeactive_sms($marketer_user_id, $marketer_reason);
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>""
                        ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>""
                        ,'desc'=>'تغییرات انجام نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }

//**************************************************************************************************************s*

                }else{
                    echo json_encode(array('result'=>$employeetoken[0]
                    ,"data"=>$employeetoken[1]
                    ,'desc'=>$employeetoken[2]));

                }







            }
            else
                if ($command=="get_request_marketer")
                {

                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','acvtivemarketer');
                    if($employeetoken[0]=='ok')
                    {
//************************************************************************;****************************************

                        $query1="select * from usermarketer_tb,user_tb,marketer_mode_tb,leader_mode_tb where usermarketer_tb.marketer_mode_id=marketer_mode_tb.marketer_mode_id AND  usermarketer_tb.leader_mode_id=leader_mode_tb.leader_mode_id AND marketer_user_id=user_id AND
 marketer_request=1 AND marketer_reject=0
   ORDER BY usermarketer_id ASC";
                        $query2="select count(*) AS cnt from usermarketer_tb,user_tb,marketer_mode_tb,leader_mode_tb where usermarketer_tb.marketer_mode_id=marketer_mode_tb.marketer_mode_id AND usermarketer_tb.leader_mode_id=leader_mode_tb.leader_mode_id AND marketer_user_id=user_id AND
 marketer_request=1 AND marketer_reject=0
   ORDER BY usermarketer_id ASC";

                        $limit = $this->post("limit");
                        $offset = $this->post("offset");
                        $limit_state ="";
                        if($limit!="" & $offset!="") {
                            $limit_state = " LIMIT " . $offset . "," . $limit;
                        }

                        $result = $this->B_db->run_query($query1.$limit_state);
                        $count  = $this->B_db->run_query($query2);

                        $output =array();
                        foreach($result as $row)
                        {
                            $record=array();
                            $record['usermarketer_id']=$row['usermarketer_id'];
                            $record['marketer_user_id']=$row['marketer_user_id'];
                            $record['marketer_reason']=$row['marketer_reason'];
							

                            $record['marketer_refund']=$row['marketer_refund'];
                            $record['marketer_city']=$row['marketer_city'];
                            $record['marketer_check_coworker']=$row['marketer_check_coworker'];
                            $record['marketer_filedinsur_favorit']=$row['marketer_filedinsur_favorit'];
                            $record['marketer_reason_select']=$row['marketer_reason_select'];
                            $record['marketer_expect']=$row['marketer_expect'];
                            $record['marketer_detail']=$row['marketer_detail'];
							
                            $record['user_name']=$row['user_name'];
                            $record['user_family']=$row['user_family'];
                            $record['user_mobile']=$row['user_mobile'];
                            $record['user_email']=$row['user_email'];
                            $record['user_register_date']=$row['user_register_date'];
                            $record['user_national_code']=$row['user_national_code'];
                            $record['user_national_image_code']=$row['user_national_image_code'];
                            //****************************************************************************

                            $result1 = $this->B_db->get_image($row['user_national_image_code']);
                            $image = $result1[0];
                            //*******************************************************************

                            $record['user_national_image']=$image['image_url'];
                            $record['user_national_image_tumb']=$image['image_tumb_url'];

                            $record['user_back_national_image_code']=$row['user_back_national_image_code'];

                            //****************************************************************************

                            $result1 = $this->B_db->get_image($row['user_back_national_image_code']);
                            $image = $result1[0];
                            //*******************************************************************

                            $record['user_back_national_image']=$image['image_url'];
                            $record['user_back_national_image_tumb']=$image['image_tumb_url'];


                            $record['marketer_image_code']=$row['marketer_image_code'];

                            //****************************************************************************

                            $result1 = $this->B_db->get_image($row['marketer_image_code']);
                            $image = $result1[0];
                            //*******************************************************************

                            $record['marketer_image']=$image['image_url'];
                            $record['marketer_image_tumb']=$image['image_tumb_url'];

                            $record['marketer_timestamp']=$row['marketer_timestamp'];
                            $record['marketer_leader_mobile']=$row['marketer_leader_mobile'];
                            $record['marketer_coworker']=$row['marketer_coworker'];
                            $record['marketer_mode_id']=$row['marketer_mode_id'];
                            $record['marketer_mode_namefa']=$row['marketer_mode_namefa'];
                            $record['marketer_mode_color']=$row['marketer_mode_color'];
                            $record['marketer_mode_logourl']=IMGADD.$row['marketer_mode_logourl'];

                            $record['leader_mode_id']=$row['leader_mode_id'];
                            $record['leader_mode_namefa']=$row['leader_mode_namefa'];
                            $record['leader_mode_color']=$row['leader_mode_color'];
                            $record['leader_mode_logourl']=IMGADD.$row['leader_mode_logourl'];



                            $output[]=$record;
                        }
                        echo json_encode(array('result'=>"ok"
                        ,"cnt"=>$count[0]['cnt']
                        ,"data"=>$output
                        ,'desc'=>'درخواست های معرفی شده نماینده فروش با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************
                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]));

                    }


                }else

                    if ($command=="get_reject_marketer")
                    {

                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','acvtivemarketer');
                        if($employeetoken[0]=='ok')
                        {
//************************************************************************;****************************************

                            $query1="select * from usermarketer_tb,user_tb,marketer_mode_tb,leader_mode_tb  where usermarketer_tb.leader_mode_id=leader_mode_tb.leader_mode_id AND usermarketer_tb.marketer_mode_id=marketer_mode_tb.marketer_mode_id AND marketer_user_id=user_id AND
 marketer_request=1 AND marketer_reject=1
   ORDER BY usermarketer_id ASC";

                            $query2="select count(*) AS cnt from usermarketer_tb,user_tb,marketer_mode_tb,leader_mode_tb where usermarketer_tb.marketer_mode_id=marketer_mode_tb.marketer_mode_id AND usermarketer_tb.leader_mode_id=leader_mode_tb.leader_mode_id AND marketer_user_id=user_id AND
 marketer_request=1 AND marketer_reject=1
   ORDER BY usermarketer_id ASC";

                            $limit = $this->post("limit");
                            $offset = $this->post("offset");
                            $limit_state ="";
                            if($limit!="" & $offset!="") {
                                $limit_state = " LIMIT " . $offset . "," . $limit;
                            }

                            $result = $this->B_db->run_query($query1.$limit_state);
                            $count  = $this->B_db->run_query($query2);

                            $output =array();
                            foreach($result as $row)
                            {
                                $record=array();
                                $record['usermarketer_id']=$row['usermarketer_id'];
                                $record['marketer_user_id']=$row['marketer_user_id'];
                                $record['marketer_reason']=$row['marketer_reason'];
								
							$record['marketer_refund']=$row['marketer_refund'];
                            $record['marketer_city']=$row['marketer_city'];
                            $record['marketer_check_coworker']=$row['marketer_check_coworker'];
                            $record['marketer_filedinsur_favorit']=$row['marketer_filedinsur_favorit'];
                            $record['marketer_reason_select']=$row['marketer_reason_select'];
                            $record['marketer_expect']=$row['marketer_expect'];
                            $record['marketer_detail']=$row['marketer_detail'];
							
                                $record['user_name']=$row['user_name'];
                                $record['user_family']=$row['user_family'];
                                $record['user_mobile']=$row['user_mobile'];
                                $record['user_email']=$row['user_email'];
                                $record['user_register_date']=$row['user_register_date'];
                                $record['user_national_code']=$row['user_national_code'];
                                $record['user_national_image_code']=$row['user_national_image_code'];
                                //****************************************************************************
                                 $result1 = $this->B_db->get_image($row['user_national_image_code']);
                                $image = $result1[0];
                                //*******************************************************************

                                $record['user_national_image']=$image['image_url'];
                                $record['user_national_image_tumb']=$image['image_tumb_url'];

                                $record['user_back_national_image_code']=$row['user_back_national_image_code'];

                                //****************************************************************************
                               $result1 = $this->B_db->get_image($row['user_back_national_image_code']);
                                $image = $result1[0];
                                //*******************************************************************

                                $record['user_back_national_image']=$image['image_url'];
                                $record['user_back_national_image_tumb']=$image['image_tumb_url'];


                                $record['marketer_image_code']=$row['marketer_image_code'];

                                //****************************************************************************
                                $result1 = $this->B_db->get_image($row['marketer_image_code']);
                                $image = $result1[0];
                                //*******************************************************************

                                $record['marketer_image']=$image['image_url'];
                                $record['marketer_image_tumb']=$image['image_tumb_url'];

                                $record['marketer_timestamp']=$row['marketer_timestamp'];
                                $record['marketer_leader_mobile']=$row['marketer_leader_mobile'];
                                $record['marketer_coworker']=$row['marketer_coworker'];
                                $record['marketer_mode_id']=$row['marketer_mode_id'];
                                $record['marketer_mode_namefa']=$row['marketer_mode_namefa'];
                                $record['marketer_mode_color']=$row['marketer_mode_color'];
                                $record['marketer_mode_logourl']=IMGADD.$row['marketer_mode_logourl'];

                                $record['leader_mode_id']=$row['leader_mode_id'];
                                $record['leader_mode_namefa']=$row['leader_mode_namefa'];
                                $record['leader_mode_color']=$row['leader_mode_color'];
                                $record['leader_mode_logourl']=IMGADD.$row['leader_mode_logourl'];


                                $output[]=$record;
                            }
                            echo json_encode(array('result'=>"ok"
                            ,"cnt"=>$count[0]['cnt']
                            ,"data"=>$output
                            ,'desc'=>'مشحصات همکاران فروش رد شده با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************
                        }else{
                            echo json_encode(array('result'=>$employeetoken[0]
                            ,"data"=>$employeetoken[1]
                            ,'desc'=>$employeetoken[2]));

                        }


                    }else

                        if ($command=="get_marketer_active")
                        {

                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','acvtivemarketer');
                            if($employeetoken[0]=='ok')
                            {
//************************************************************************;****************************************

                                $query1="select * from usermarketer_tb,user_tb,marketer_mode_tb,leader_mode_tb where usermarketer_tb.leader_mode_id=leader_mode_tb.leader_mode_id AND usermarketer_tb.marketer_mode_id=marketer_mode_tb.marketer_mode_id AND marketer_user_id=user_id AND
 marketer_request=0  AND marketer_reject=0
   ORDER BY usermarketer_id ASC";

                                $query2="select count(*) AS cnt from usermarketer_tb,user_tb,marketer_mode_tb,leader_mode_tb where usermarketer_tb.leader_mode_id=leader_mode_tb.leader_mode_id AND  usermarketer_tb.marketer_mode_id=marketer_mode_tb.marketer_mode_id AND marketer_user_id=user_id AND
 marketer_request=0  AND marketer_reject=0
   ORDER BY usermarketer_id ASC";

                                $limit = $this->post("limit");
                                $offset = $this->post("offset");
                                $limit_state ="";
                                if($limit!="" & $offset!="") {
                                    $limit_state = " LIMIT " . $offset . "," . $limit;
                                }

                                $result = $this->B_db->run_query($query1.$limit_state);
                                $count  = $this->B_db->run_query($query2);

                                $output =array();
                                foreach($result as $row)
                                {
                                    $record=array();
                                    $record['usermarketer_id']=$row['usermarketer_id'];
                                    $record['marketer_user_id']=$row['marketer_user_id'];
                                    $record['marketer_reason']=$row['marketer_reason'];
									
									$record['marketer_refund']=$row['marketer_refund'];
                            $record['marketer_city']=$row['marketer_city'];
                            $record['marketer_check_coworker']=$row['marketer_check_coworker'];
                            $record['marketer_filedinsur_favorit']=$row['marketer_filedinsur_favorit'];
                            $record['marketer_reason_select']=$row['marketer_reason_select'];
                            $record['marketer_expect']=$row['marketer_expect'];
                            $record['marketer_detail']=$row['marketer_detail'];
							
							
                                    $record['marketer_deactive']=$row['marketer_deactive'];
                                    $record['user_name']=$row['user_name'];
                                    $record['user_family']=$row['user_family'];
                                    $record['user_mobile']=$row['user_mobile'];
                                    $record['user_email']=$row['user_email'];
                                    $record['user_register_date']=$row['user_register_date'];
                                    $record['user_national_code']=$row['user_national_code'];
                                    $record['user_national_image_code']=$row['user_national_image_code'];
                                    //****************************************************************************
                                  $result1 = $this->B_db->get_image($row['user_national_image_code']);
                                    $image = $result1[0];
                                    //*******************************************************************

                                    $record['user_national_image']=$image['image_url'];
                                    $record['user_national_image_tumb']=$image['image_tumb_url'];

                                    $record['user_back_national_image_code']=$row['user_back_national_image_code'];

                                    //****************************************************************************
                                   $result1 = $this->B_db->get_image($row['user_back_national_image_code']);
                                    $image = $result1[0];
                                    //*******************************************************************

                                    $record['user_back_national_image']=$image['image_url'];
                                    $record['user_back_national_image_tumb']=$image['image_tumb_url'];


                                    $record['marketer_image_code']=$row['marketer_image_code'];

                                    //****************************************************************************
                                    $result1 = $this->B_db->get_image($row['marketer_image_code']);
                                    $image = $result1[0];
                                    //*******************************************************************

                                    $record['marketer_image']=$image['image_url'];
                                    $record['marketer_image_tumb']=$image['image_tumb_url'];

                                    $record['marketer_timestamp']=$row['marketer_timestamp'];
                                    $record['marketer_leader_mobile']=$row['marketer_leader_mobile'];
                                    $record['marketer_coworker']=$row['marketer_coworker'];

                                    //****************************************************************************
                                    $query1=" SELECT * FROM user_tb WHERE user_mobile='".$row['marketer_leader_mobile']."'";
                                    $result1=$this->B_db->run_query($query1);
                                    $user=$result1[0];
                                    //*******************************************************************

                                    $record['marketer_leader_id']=$user['user_id'];
                                    $record['marketer_leader_name']=$user['user_name'];
                                    $record['marketer_leader_family']=$user['user_family'];



                                    $record['marketer_mode_id']=$row['marketer_mode_id'];
                                    $record['marketer_mode_namefa']=$row['marketer_mode_namefa'];
                                    $record['marketer_mode_color']=$row['marketer_mode_color'];
                                    $record['marketer_mode_logourl']=IMGADD.$row['marketer_mode_logourl'];


                                    $record['leader_mode_id']=$row['leader_mode_id'];
                                    $record['leader_mode_namefa']=$row['leader_mode_namefa'];
                                    $record['leader_mode_color']=$row['leader_mode_color'];
                                    $record['leader_mode_logourl']=IMGADD.$row['leader_mode_logourl'];



                                    $output[]=$record;
                                }
                                echo json_encode(array('result'=>"ok"
                                ,"cnt"=>$count[0]['cnt']
                                ,"data"=>$output
                                ,'desc'=>'مشحصات همکاران فروش فعال با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************
                            }else{
                                echo json_encode(array('result'=>$employeetoken[0]
                                ,"data"=>$employeetoken[1]
                                ,'desc'=>$employeetoken[2]));

                            }


                        }else



                            if ($command=="modify_marketer_active")
                            {
                                $marketer_user_id=$this->post('marketer_user_id') ;


                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','acvtivemarketer');
                                if($employeetoken[0]=='ok')
                                {
//*****************************************************************************************

                                    $query="UPDATE usermarketer_tb SET ";

                                    if(isset($_REQUEST['marketer_reason'])){
                                        $marketer_reason=$this->post('marketer_reason');
                                        $query.="marketer_reason='".$marketer_reason."'";}

                                    if(isset($_REQUEST['marketer_mode_id'])&&(isset($_REQUEST['marketer_reason']))){ $query.=",";}
                                    if(isset($_REQUEST['marketer_mode_id'])){
                                        $marketer_mode_id=$this->post('marketer_mode_id') ;
                                        $query.="marketer_mode_id=".$marketer_mode_id." ";


                                    }

                                    if(isset($_REQUEST['marketer_deactive'])&&(isset($_REQUEST['marketer_mode_id'])||isset($_REQUEST['marketer_reason']))){ $query.=",";}
                                    if(isset($_REQUEST['marketer_deactive'])){
                                        $marketer_deactive=$this->post('marketer_deactive',0) ;
                                        $query.="marketer_deactive=".$marketer_deactive." ";}

                                    if(isset($_REQUEST['marketer_city'])&&(isset($_REQUEST['marketer_deactive'])||isset($_REQUEST['marketer_mode_id'])||isset($_REQUEST['marketer_reason']))){ $query.=",";}
                                    if(isset($_REQUEST['marketer_city'])){
                                        $marketer_city=$this->post('marketer_city') ;
                                        $query.="marketer_city='".$marketer_city."' ";}

                                     if(isset($_REQUEST['marketer_check_coworker'])&&(isset($_REQUEST['marketer_deactive'])||isset($_REQUEST['marketer_mode_id'])||isset($_REQUEST['marketer_reason']))){ $query.=",";}
                                    if(isset($_REQUEST['marketer_check_coworker'])){
                                        $marketer_check_coworker=$this->post('marketer_check_coworker') ;
                                        $query.="marketer_check_coworker='".$marketer_check_coworker."' ";}	

                                     if(isset($_REQUEST['marketer_refund'])&&(isset($_REQUEST['marketer_check_coworker'])||isset($_REQUEST['marketer_deactive'])||isset($_REQUEST['marketer_mode_id'])||isset($_REQUEST['marketer_reason']))){ $query.=",";}
                                    if(isset($_REQUEST['marketer_refund'])){
                                        $marketer_refund=$this->post('marketer_refund') ;
                                        $query.="marketer_refund='".$marketer_refund."' ";}	

                                     if(isset($_REQUEST['marketer_filedinsur_favorit'])&&(isset($_REQUEST['marketer_refund'])||isset($_REQUEST['marketer_check_coworker'])||isset($_REQUEST['marketer_deactive'])||isset($_REQUEST['marketer_mode_id'])||isset($_REQUEST['marketer_reason']))){ $query.=",";}
                                    if(isset($_REQUEST['marketer_filedinsur_favorit'])){
                                        $marketer_filedinsur_favorit=$this->post('marketer_filedinsur_favorit') ;
                                        $query.="marketer_filedinsur_favorit='".$marketer_filedinsur_favorit."' ";}	

                                     if(isset($_REQUEST['marketer_reason_select'])&&(isset($_REQUEST['marketer_filedinsur_favorit'])||isset($_REQUEST['marketer_refund'])||isset($_REQUEST['marketer_check_coworker'])||isset($_REQUEST['marketer_deactive'])||isset($_REQUEST['marketer_mode_id'])||isset($_REQUEST['marketer_reason']))){ $query.=",";}
                                    if(isset($_REQUEST['marketer_reason_select'])){
                                        $marketer_reason_select=$this->post('marketer_filedinsur_favorit') ;
                                        $query.="marketer_reason_select='".$marketer_reason_select."' ";}	

                                     if(isset($_REQUEST['marketer_expect'])&&(isset($_REQUEST['marketer_reason_select'])||isset($_REQUEST['marketer_filedinsur_favorit'])||isset($_REQUEST['marketer_refund'])||isset($_REQUEST['marketer_check_coworker'])||isset($_REQUEST['marketer_deactive'])||isset($_REQUEST['marketer_mode_id'])||isset($_REQUEST['marketer_reason']))){ $query.=",";}
                                    if(isset($_REQUEST['marketer_expect'])){
                                        $marketer_expect=$this->post('marketer_expect') ;
                                        $query.="marketer_expect='".$marketer_expect."' ";}	

									if(isset($_REQUEST['marketer_detail'])&&(isset($_REQUEST['marketer_expect'])||isset($_REQUEST['marketer_reason_select'])||isset($_REQUEST['marketer_filedinsur_favorit'])||isset($_REQUEST['marketer_refund'])||isset($_REQUEST['marketer_check_coworker'])||isset($_REQUEST['marketer_deactive'])||isset($_REQUEST['marketer_mode_id'])||isset($_REQUEST['marketer_reason']))){ $query.=",";}
                                    if(isset($_REQUEST['marketer_detail'])){
                                        $marketer_detail=$this->post('marketer_detail') ;
                                        $query.="marketer_detail='".$marketer_detail."' ";}

                                    if(isset($_REQUEST['leader_mode_id'])&&(isset($_REQUEST['marketer_detail'])||isset($_REQUEST['marketer_expect'])||isset($_REQUEST['marketer_reason_select'])||isset($_REQUEST['marketer_filedinsur_favorit'])||isset($_REQUEST['marketer_refund'])||isset($_REQUEST['marketer_check_coworker'])||isset($_REQUEST['marketer_deactive'])||isset($_REQUEST['marketer_mode_id'])||isset($_REQUEST['marketer_reason']))){ $query.=",";}
                                    if(isset($_REQUEST['leader_mode_id'])){
                                        $leader_mode_id=$this->post('leader_mode_id') ;
                                        $query.="leader_mode_id=".$leader_mode_id." ";}



                                    $query.=" where marketer_user_id=".$marketer_user_id;

                                    $result=$this->B_db->run_query_put($query);
                                    if($result){
                                        echo json_encode(array('result'=>"ok"
                                        ,"data"=>""
                                        ,'desc'=>'تغییرات انجام شد' ),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }
//***************************************************************************************************************

                                }else{
                                    echo json_encode(array('result'=>$employeetoken[0]
                                    ,"data"=>$employeetoken[1]
                                    ,'desc'=>$employeetoken[2]));

                                }


                                }
                        }
}}