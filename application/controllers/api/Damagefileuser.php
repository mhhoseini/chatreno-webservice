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
class Damagefileuser extends REST_Controller {

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
        $this->load->model('B_requests');
        $this->load->model('B_db');
        $this->load->helper('my_helper');
		$this->load->helper('time_helper');
            $this->load->helper('time_helper');
         $command = $this->post("command");
        $usertoken=checkusertoken($user_token_str);
        if($usertoken[0]=='ok')
        {
            if ($command=="get_damagefileid"){
                $damagefile_price_user=$this->post('damagefile_price_user') ;
                $damagefile_company_id=$this->post('damagefile_company_id') ;
                $damagefile_fielddamagefile_id=$this->post('damagefile_fielddamagefile_id') ;
                $damagefile_therapycontract_id=$this->post('damagefile_therapycontract_id',0) ;
                $damagefile_user_therapy_id=$this->post('damagefile_user_therapy_id',0) ;
                $damagefile_state_id=$this->post('damagefile_state_id',0) ;
                $damagefile_city_id=$this->post('damagefile_city_id',0) ;
                $query="INSERT INTO damagefile_tb(damagefile_user_id, damagefile_fielddamagefile_id,damagefile_company_id, damagefile_price_user  , damagefile_last_state_id,damagefile_therapycontract_id,damagefile_user_therapy_id, damagefile_state_id, damagefile_city_id) VALUES
                                                 (".$usertoken[1]." ,$damagefile_fielddamagefile_id,$damagefile_company_id ,$damagefile_price_user,0                       ,$damagefile_therapycontract_id,$damagefile_user_therapy_id,$damagefile_state_id,$damagefile_city_id) ";
                $result=$this->B_db->run_query_put($query);
                  $damagefile_id=$this->db->insert_id();

$query1="INSERT INTO state_damagefile_tb(statedamagefile_damagefile_id, statedamagefile_state_id, statedamagefile_timestamp, statedamagefile_desc          ) VALUES
                                    (".$damagefile_id."        ,      0               ,  now()                 ,' تشکیل پرونده توسط کاربر') ";
                    $statedamagefile_id=$this->B_db->run_query_put($query1);

                //*****************************************************************************

                $expert_id=0;
                $evaluatorco_id=0;
                $query5="SELECT * FROM kindorgansenddamagefile_tb WHERE kindorgansenddamagefile_contract_id=".$damagefile_therapycontract_id."";
                $result5=$this->B_db->run_query($query5);
                foreach($result5 as $row)
                {
                    if(($row['kindorgansenddamagefile_state_id']==$damagefile_state_id||$row['kindorgansenddamagefile_state_id']==0)
                        &&($row['kindorgansenddamagefile_city_id']==$damagefile_city_id||$row['kindorgansenddamagefile_city_id']==0)
                        &&($row['kindorgansenddamagefile_fielddamagefile_id']==$damagefile_fielddamagefile_id||$row['kindorgansenddamagefile_fielddamagefile_id']==0)
                        )
                    {
                        $expert_id=$row['kindorgansenddamagefile_expert_id'];
                        $evaluatorco_id=$row['kindorgansenddamagefile_evaluatorco_id'];
                    }
                }
                $query2="UPDATE damagefile_tb SET damagefile_last_state_id=1,damagefile_evaluatorco_id=$evaluatorco_id ,damagefile_expert_id=$expert_id WHERE  damagefile_id = $damagefile_id";
                $result2=$this->B_db->run_query_put($query2);

                $query3="INSERT INTO state_damagefile_tb(statedamagefile_damagefile_id, statedamagefile_state_id, statedamagefile_timestamp, statedamagefile_desc          ) VALUES
                                    (".$damagefile_id."        ,      1               ,  now()                 ,' ارجاع به ارزیاب خسارت') ";
                $statedamagefile_id=$this->B_db->run_query_put($query3);

                //*****************************************************************************


                if($result){
                    header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                    ,"data"=>array('damagefile_id'=>$damagefile_id)
                    ,'desc'=>'درخواست اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    header('Content-Type: application/json'); echo json_encode(array('result'=>"error"
                    ,"data"=>""
                    ,'desc'=>'درخواست اضافه نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }else if($command=="save_img_damagefile"){
                $damagefile_id=$this->post('damagefile_id') ;
                $image_code=$this->post('image_code') ;
                $result1=$this->B_db->get_image_whitoururl($image_code);

                $image=$result1[0];
                $query="select * from damagefile_img_tb where damagefile_img_damagefile_id=$damagefile_id AND damagefile_img_image_code='".$image['image_id']."'";
                $result=$this->B_db->run_query($query);
                $num=count($result[0]);
                if ($num==0)
                {
                    $query="INSERT INTO damagefile_img_tb(damagefile_img_damagefile_id, damagefile_img_image_code)  VALUES
                                    ($damagefile_id,". $image['image_id'].")";
                    $damagefile_img_id=$this->B_db->run_query_put($query);
                    if($damagefile_img_id){
                        header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                        ,"data"=>array('damagefile_img_id'=>$damagefile_img_id)
                        ,'desc'=>'عکس  به درخواست اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        header('Content-Type: application/json'); echo json_encode(array('result'=>"error"
                        ,"data"=>""
                        ,'desc'=>'عکس  به درخواست اضافه نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                }else{
                    header('Content-Type: application/json'); echo json_encode(array('result'=>"error"
                    ,"data"=>""
                    ,'desc'=>'عکس برای این درخواست تکراری است '),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }
        }else{
            header('Content-Type: application/json'); echo json_encode(array('result'=>$usertoken[0]
            ,"data"=>$usertoken[1]
            ,'desc'=>$usertoken[2]));

        }
    }
}