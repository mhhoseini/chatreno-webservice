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
class Userreferral extends REST_Controller {

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

        if($this->B_user->checkrequestip('userreferral',$command,get_client_ip(),50,50)){
            if ($command=="add_referral")
            {
                $usertoken=checkusertoken($this->user_token_str);
                if($usertoken[0]=='ok')
                {
                    $user_id = $usertoken[1];
                    do
                    {
                        $user_refferal_name = $this->generateToken(8);
                    }while($this->get_referral($user_refferal_name));
                    $user_referral_title = $this->post('referral_title');
                    $query = "INSERT INTO user_referral_tb
                    (user_referral_user_id, user_refferal_name, user_referral_date, user_referral_deactive, user_referral_title)
                    VALUES( $user_id, '$user_refferal_name' , now(), 0,'$user_referral_title')";
                    $this->B_db->run_query_put($query);
                    $user_referral_id = $this->db->insert_id();
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('user_referral_id'=>$user_refferal_name)
                    ,'desc'=>'رکورد مورد نظر با موفقیت ثبت شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                       echo json_encode(array('result'=>"error"
                       ,'data'=>''
                        ,'desc'=>' کد معرفی با موفقیت ثبت نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
            }else if($command=="get_referral"){

                $usertoken=checkusertoken($this->user_token_str);
                if($usertoken[0]=='ok') {
                    $user_referral_user_id =$usertoken[1];
                    $query = "select * from user_referral_tb
                    where user_referral_deactive=0 AND user_referral_user_id=" . $user_referral_user_id . " ";
                    $data = $this->B_db->run_query($query);
                     echo json_encode(array('result'=>"ok"
                    ,'data'=>$data
                    ,'desc'=>'لیست کدهای معرفی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                         echo json_encode(array('result'=>"error"
                    ,'data'=>''
                    ,'desc'=>'لیست کدهای معرفی با موفقیت ارسال نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }

            }else if($command=="check_referral") { 
                    $user_refferal_name = $this->post('user_refferal_name');
                  
                    $query = "select * from user_referral_tb
                    where user_referral_deactive=0  AND user_refferal_name='" . $user_refferal_name . "' ";
                     $data = $this->B_db->run_query($query);
                     echo json_encode(array('result'=>"ok"
                    ,'data'=>$data
                    ,'desc'=>'لیست کدهای معرفی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }else if($command=="check_referral_mobile") { 
                    $user_mobile = $this->post('user_refferal_name');
                  
                    $query = "SELECT usermarketer_id FROM usermarketer_tb,user_tb WHERE user_id=marketer_user_id AND user_mobile='" . $user_mobile . "' ";
                     $data = $this->B_db->run_query($query);
                     echo json_encode(array('result'=>"ok"
                    ,'data'=>$data
                    ,'desc'=>'لیست کدهای معرفی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }
            else if($command=="update_referral") {

                $usertoken = checkusertoken($this->user_token_str);
                if ($usertoken[0] == 'ok') {
                    $request_id = $this->post('request_id');
                    $user_mobile = $this->post('user_mobile');
                    $query = "UPDATE request_tb
                SET request_reagent_mobile=$user_mobile
                WHERE request_last_state_id<2 And  request_id=$request_id ";
                    $result = $this->B_db->run_query_put($query);
                    if($result)
                        echo json_encode(array('result' => 'ok'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    else
                        echo json_encode(array('data' => 'رکورد مورد نظر یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }
            else if($command=="update_referral_title") {

                $usertoken = checkusertoken($this->user_token_str);
                if ($usertoken[0] == 'ok') {
                    $user_referral_id = $this->post('user_referral_id');
                    $user_referral_title = $this->post('user_referral_title');
                    $query = "UPDATE user_referral_tb
                    SET user_referral_title='$user_referral_title'
                    WHERE user_referral_id=$user_referral_id ";
                    $this->B_db->run_query_put($query);
                    if($this->db->affected_rows())
                      echo json_encode(array('result' => 'ok','desc'=>$user_referral_id.'عنوان مرتبط بروزرسانی شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    else
                      echo json_encode(array('data' => 'رکورد مورد نظر یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            } else if($command=="deactive_referral") {

                $usertoken = checkusertoken($this->user_token_str);
                if ($usertoken[0] == 'ok') {
                    $user_referral_id = $this->post('user_referral_id');
                    $query = "UPDATE user_referral_tb
                    SET user_referral_deactive=1
                    WHERE user_referral_id=$user_referral_id ";
                    $this->B_db->run_query_put($query);
                    if($this->db->affected_rows())
                      echo json_encode(array('result' => 'ok','desc'=>' کد معرف غیر فعال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    else
                      echo json_encode(array('data' => 'رکورد مورد نظر یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }
        }
    }

    function generateToken( $length = 8 )
    {
        $temp = "abcdefghijklmnopqrstuvwxyz0123456789" ;
        $generate = "" ;
        for ( $i = 0 ; $i < $length ; $i++ ){
            $begin = 0 ;
            $end = mb_strlen($temp) - 1  ;
            $generate .=  $temp[ random_int( $begin , $end ) ] ;
        }
        return $generate ;
    }

    function get_referral($user_refferal_name){
        $query = "select * from user_referral_tb
                    where  user_refferal_name='$user_refferal_name'";
        $data = $this->B_db->run_query($query);
        if(empty($data))
            return false;
        else
            return true;
    }
}
