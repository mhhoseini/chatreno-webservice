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
class Reminder extends REST_Controller {

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
        if(isset($this->input->request_headers()['Authorization']))$user_token_str = $this->input->request_headers()['Authorization'];
        $this->load->model('B_user');
        $this->load->model('B_db');
        $this->load->model('B_agent');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if ($command=="add_reminder")
        {
            $reminder_mobile=$this->post('reminder_mobile') ;
            $reminder_reagent_mobile=$this->post('reminder_reagent_mobile') ;
            $reminder_fieldinsurance_id=$this->post('reminder_fieldinsurance_id') ;
            $reminder_timestamp=$this->post('reminder_timestamp') ;
            $reminder_desc=$this->post('reminder_desc') ;
            $reminder_id = $this->B_db->create_reminder($reminder_mobile,$reminder_reagent_mobile,$reminder_fieldinsurance_id,$reminder_timestamp,$reminder_desc);
            echo json_encode(array('result'=>"ok"
            ,"data"=>array('reminder_id'=>$reminder_id)
            ,'desc'=>'درخواست یادآوری با موفقیت ثبت شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

        }else
            if ($command=="get_userreminder")
            {
                $usertoken=checkusertoken($user_token_str);
                if($usertoken[0]=='ok')
                {
//****************************************************************************************************************
                    $user_id=$usertoken[1];

                    $query="select * from reminder_tb,user_tb,fieldinsurance_tb where reminder_fieldinsurance_id=fieldinsurance_id AND user_mobile=reminder_mobile  AND  user_id=".$user_id ." order by reminder_timestamp desc";
                    $result = $this->B_db->run_query($query);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record=array();
                        $record['reminder_id']=$row['reminder_id'];
                        $record['reminder_mobile']=$row['reminder_mobile'];
                        $record['reminder_reagent_mobile']=$row['reminder_reagent_mobile'];
                        $record['reminder_fieldinsurance_id']=$row['reminder_fieldinsurance_id'];
                        $record['fieldinsurance']=$row['fieldinsurance'];
                        $record['fieldinsurance_fa']=$row['fieldinsurance_fa'];
                        $record['fieldinsurance_logo_url']=IMGADD.$row['fieldinsurance_logo_url'];
                        $record['reminder_timestamp_now']=$row['reminder_timestamp_now'];
                        $record['reminder_timestamp']=$row['reminder_timestamp'];
                        $record['reminder_desc']=$row['reminder_desc'];
                        $record['reminder_user_deactive']=$row['reminder_user_deactive'];
                        $output[]=$record;
                    }

                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'یادآوری ها با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************
                }else{
                    echo json_encode(array('result'=>$usertoken[0]
                    ,"data"=>$usertoken[1]
                    ,'desc'=>$usertoken[2]));

                }



            }else
                if ($command=="get_reagentreminder")
                {


                    $usertoken=checkusertoken($user_token_str);
                    if($usertoken[0]=='ok')
                    {
//****************************************************************************************************************
                        $user_id=$usertoken[1];
                        $query="select * from reminder_tb,user_tb,fieldinsurance_tb where reminder_fieldinsurance_id=fieldinsurance_id AND user_mobile=reminder_reagent_mobile  AND  user_id=".$user_id;
                        $result = $this->B_db->run_query($query);
                        $output =array();
                        foreach($result as $row)
                        {
                            $record=array();
                            $record['reminder_id']=$row['reminder_id'];
                            $record['reminder_mobile']=$row['reminder_mobile'];
                            $record['reminder_reagent_mobile']=$row['reminder_reagent_mobile'];
                            $record['reminder_fieldinsurance_id']=$row['reminder_fieldinsurance_id'];
                            $record['fieldinsurance']=$row['fieldinsurance'];
                            $record['fieldinsurance_fa']=$row['fieldinsurance_fa'];
							$record['fieldinsurance_logo_url']=IMGADD.$row['fieldinsurance_logo_url'];
                            $record['reminder_timestamp_now']=$row['reminder_timestamp_now'];
                            $record['reminder_timestamp']=$row['reminder_timestamp'];
                            $record['reminder_desc']=$row['reminder_desc'];
                            $record['reminder_user_deactive']=$row['reminder_user_deactive'];
                            $output[]=$record;
                        }

                        echo json_encode(array('result'=>"ok"
                        ,"data"=>$output
                        ,'desc'=>'یادآوری ها با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************
                    }else{
                        echo json_encode(array('result'=>$usertoken[0]
                        ,"data"=>$usertoken[1]
                        ,'desc'=>$usertoken[2]));
                    }

                }
                else
                    if ($command=="get_leaderreagentreminder")
                    {


                        $usertoken=checkusertoken($user_token_str);
                        if($usertoken[0]=='ok')
                        {
//****************************************************************************************************************
                            $user_id=$usertoken[1];

                            $query="select * from user_tb where  user_id=".$user_id;
                            $result = $this->B_db->run_query($query);
                            $user=$result[0];
                            $leader_mobile=$user['user_mobile'];
                            $query="select * from reminder_tb,user_tb,fieldinsurance_tb,usermarketer_tb where reminder_fieldinsurance_id=fieldinsurance_id AND	marketer_leader_mobile=$leader_mobile AND marketer_user_id=user_id AND user_mobile=reminder_reagent_mobile";
                            $result = $this->B_db->run_query($query);
                            $output =array();
                            foreach($result as $row)
                            {
                                $record=array();
                                $record['reminder_id']=$row['reminder_id'];
                                $record['reminder_mobile']=$row['reminder_mobile'];
                                $record['reminder_reagent_mobile']=$row['reminder_reagent_mobile'];
                                $record['user_name']=$row['user_name'];
                                $record['user_family']=$row['user_family'];
                                $record['reminder_fieldinsurance_id']=$row['reminder_fieldinsurance_id'];
                                $record['fieldinsurance']=$row['fieldinsurance'];
                                $record['fieldinsurance_fa']=$row['fieldinsurance_fa'];
								$record['fieldinsurance_logo_url']=IMGADD.$row['fieldinsurance_logo_url'];
                                $record['reminder_timestamp_now']=$row['reminder_timestamp_now'];
                                $record['reminder_timestamp']=$row['reminder_timestamp'];
                                $record['reminder_desc']=$row['reminder_desc'];
                                $record['reminder_user_deactive']=$row['reminder_user_deactive'];
                                $output[]=$record;
                            }

                            echo json_encode(array('result'=>"ok"
                            ,"data"=>$output
                            ,'desc'=>'یادآوری ها با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************
                        }else{
                            echo json_encode(array('result'=>$usertoken[0]
                            ,"data"=>$usertoken[1]
                            ,'desc'=>$usertoken[2]));
                        }

                    }else
                        if ($command=="deactive_userreminder")
                        {
                            $reminder_id=$this->post('reminder_id') ;

                            $usertoken=checkusertoken($user_token_str);
                            if($usertoken[0]=='ok')
                            {
//****************************************************************************************************************
                                $user_id=$usertoken[1];

                                $query="select * from user_tb where  user_id=".$user_id;
                                $result = $this->B_db->run_query($query);
                                $user=$result[0];
                                $user_mobile=$user['user_mobile'];

                                $query="UPDATE reminder_tb SET reminder_user_deactive=1 WHERE reminder_id=$reminder_id AND reminder_mobile='".$user_mobile."' ";
                                $result = $this->B_db->run_query_put($query);


                                echo json_encode(array('result'=>"ok"
                                ,"data"=>""
                                ,'desc'=>'یادآوری ها با موفقیت غیر فعال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************
                            }else{
                                echo json_encode(array('result'=>$usertoken[0]
                                ,"data"=>$usertoken[1]
                                ,'desc'=>$usertoken[2]));

                            }



                        }

        }
}