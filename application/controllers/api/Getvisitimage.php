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
class Getvisitimage extends REST_Controller {

    public $user_token_str;
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
    }

    public function index_post()
    {
        if (isset($this->input->request_headers()['Authorization'])) $this->user_token_str = $this->input->request_headers()['Authorization'];
        $this->load->helper('my_helper');
        $this->load->model('B_user');
        $this->load->model('B_db');
        $command = $this->post("command");

        if($this->B_user->checkrequestip('getvsitimage',$command,get_client_ip(),50,50)){
            if ($command=="get_tempimage")
            {
                $usertoken=checkusertoken($this->user_token_str);
                if($usertoken[0]=='ok')
                {
                    $user_id = $usertoken[1];
                    $request_visit_id = $this->post('request_visit_id');
                    $sql = "SELECT * FROM request_tb,request_visit_tb WHERE request_visit_request_id=request_id  AND request_visit_id=".$request_visit_id;
                    $result20 = $this->B_db->run_query($sql);
                    if(!empty($result20[0]))
                    {
                        $sql1 = "SELECT  request_visit_temp_image_text,  request_visit_temp_image_priority,request_visit_temp_image 
FROM request_visit_temp_image_tb WHERE  request_visit_temp_image_fieldinsurance='".$result20[0]['request_fieldinsurance']."' order by request_visit_temp_image_priority";
                        $result21 = $this->B_db->run_query($sql1);
                        $output =array();
                        foreach($result21 as $row)
                        {
                            $record=array();
                            $record['request_visit_temp_image_text']=$row['request_visit_temp_image_text'];
                            $record['request_visit_temp_image_priority']=$row['request_visit_temp_image_priority'];
                            $result1 = $this->B_db->get_image($row['request_visit_temp_image']);
                            $image = $result1[0];

                            $record['image_url']=$image['image_url'];
                            $record['image_tumb_url']=$image['image_tumb_url'];
                            $output[]=$record;
                        }

                        echo json_encode(array('result'=>"ok"
                        ,'data'=>$output
                        ,'desc'=>'کد ارسالی شما صحیح نمی باشد'.$sql1),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                    }else{
                        echo json_encode(array('result'=>"error"
                        ,'data'=>''
                        ,'desc'=>'شماره درخواست موجود نمیباشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                }else{
                    echo json_encode(array('result'=>"error"
                    ,'data'=>''
                    ,'desc'=>'کد ارسالی شما صحیح نمی باشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }else  if ($command=="add_video_visit")
            {
                $usertoken=checkusertoken($this->user_token_str);
                if($usertoken[0]=='ok')
                {
                    $user_id = $usertoken[1];
                    $request_visit_id=$this->post('request_visit_id') ;
                    $sql = "SELECT * FROM request_visit_tb WHERE request_visit_id=".$request_visit_id;
                    $result20 = $this->B_db->run_query($sql);
                    if(!empty($result20[0]))
                    {
                        $request_id=$result20[0]['request_visit_request_id'];
                        //*************************************************************************************************************
                        //***************************************************************************************************************
                        //***************************************************************************************************************
                        $timezone=new DateTimeZone("Asia/Tehran");
                        $date=new DateTime();
                        $date->setTimezone($timezone);
                        $year=$date->format("Y");
                        $month=$date->format("m");
                        $dey=$date->format("d");
                        $upload_path = 'filefolder/uploadfile/'.$year.'/'.$month.'/'.$dey.'/';

                        if ( !file_exists( $upload_path ) ) {
                            @mkdir( $upload_path, 0755, true ) ;
                        }

                        if(isset($_FILES['filevisit']['name'])){

                            $fileinfo = pathinfo($_FILES['filevisit']['name']);
                            $extension = $fileinfo['extension'];

                            $date=new DateTime();
                            $date->setTimezone($timezone);
                            $current_timestamp=$date->getTimestamp();
                            $image_code=$current_timestamp;

                            $file_url = $upload_path .$image_code. '.' . $extension;
                            if (move_uploaded_file($_FILES['filevisit']['tmp_name'],$file_url)){
                                //***************************************************************************************************************
                                $request_file_url=$file_url ;

                                $query="UPDATE request_visit_tb
SET  request_visit_date=now(), request_visit_user_id=$user_id, request_visit_vedio_url='$request_file_url'
WHERE request_visit_id=$request_visit_id;";
                                $this->B_db->run_query_put($query);

                                $query1="INSERT INTO state_request_tb(staterequest_request_id, staterequest_state_id, staterequest_timestamp, staterequest_desc      ) VALUES
                                    (".$request_id."        ,      3               ,  now()                 ,'در حال بررسی توسط نماینده')";
                                $result1=$this->B_db->run_query_put($query1);

                                $query2="UPDATE request_tb SET request_last_state_id=3  WHERE  request_id = $request_id";
                                $result2=$this->B_db->run_query_put($query2);


                                echo json_encode(array('result'=>"ok"
                                ,"data"=>$request_visit_id
                                ,'desc'=>'فایل  بیمه نامه اضافه شد'.$query1),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                //***************************************************************************************************************

                            }else{

                                echo json_encode(array('result'=>"error"
                                ,"data"=>""
                                ,'desc'=>'بارگزاری موفقیت آمیز نبود'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
                        }else{
                            echo json_encode(array('result'=>"error"
                            ,"data"=>""
                            ,'desc'=>'فایل موجود نیست'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                        //***************************************************************************************************************

                        //*************************************************************************************************************

                    }else{
                        echo json_encode(array('result'=>"error"
                        ,'data'=>''
                        ,'desc'=>'شماره درخواست موجود نمیباشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                }else{
                    echo json_encode(array('result'=>"error"
                    ,'data'=>''
                    ,'desc'=>'کد ارسالی شما صحیح نمی باشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }else  if ($command=="add_image_visit"){
                $usertoken=checkusertoken($this->user_token_str);
                if($usertoken[0]=='ok')
                {
                    $user_id = $usertoken[1];
                    $request_visit_id=$this->post('request_visit_id') ;
                    $request_visit_image_code=$this->post('request_visit_image_code') ;


                        //***************************************************************************************************************


                                $query="INSERT INTO request_visit_image_tb
                                       ( request_visit_image_code, request_visit_image_timestamp, request_visit_image_visit_id)
VALUES
                                        ( '$request_visit_image_code' ,now()                     ,$request_visit_id     )";
                                $this->B_db->run_query_put($query);
                                $request_visit_id = $this->db->insert_id();

                                echo json_encode(array('result'=>"ok"
                                ,"data"=>$request_visit_id
                                ,'desc'=>'فایل  بیمه نامه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                //***************************************************************************************************************


                }else{
                    echo json_encode(array('result'=>"error"
                    ,'data'=>''
                    ,'desc'=>'کد ارسالی شما صحیح نمی باشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }
            }
        }
    }

