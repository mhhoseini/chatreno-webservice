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
class Userbank extends REST_Controller {

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

    public function index_post(){
        if(isset($this->input->request_headers()['Authorization']))$user_token_str = $this->input->request_headers()['Authorization'];
        $this->load->model('B_user');
        $this->load->model('B_db');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if($this->B_user->checkrequestip('userbank',$command,get_client_ip(),50,50)){
            if ($command=="add")
            {

                $useracbank_sheba=$this->post('useracbank_sheba');
                $useracbank_bankname=$this->post('useracbank_bankname');
                
                $usertoken=checkusertoken($user_token_str);
                if($usertoken[0]=='ok')
                {
                    $result=$this->B_user->get_userbank_by($useracbank_sheba);

                    if (1)
                    //if (empty($result))
                    {
                        $result=$this->B_user->create_userbank($usertoken[1], $useracbank_sheba,$useracbank_bankname);
						    $useracbank_id=$this->db->insert_id();
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>array('useracbank_id'=>$useracbank_id)
                        ,'desc'=>'حساب بانکی با موفقیت ثبت شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        $useracbank=$result[0];
                        echo json_encode(array('result'=>"error"
                        ,"data"=>array('useracbank_id'=>$useracbank['useracbank_id'])
                        ,'desc'=>'حساب بانکی تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                }else{
                    echo json_encode(array('result'=>$usertoken[0]
                    ,"data"=>$usertoken[1]
                    ,'desc'=>$usertoken[2]));
                }
            }
            if ($command=="get")
            {
                
                $usertoken=checkusertoken($user_token_str);
                if($usertoken[0]=='ok')
                {
                    $user_id=$usertoken[1];
                    $result=$this->B_user->get_userbank($user_id);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record=array();
                        $record['useracbank_id']=$row['useracbank_id'];
                        $record['useracbank_user_id']=$row['useracbank_user_id'];
                        $record['useracbank_sheba']=$row['useracbank_sheba'];
                        $record['useracbank_bankname']=$row['useracbank_bankname'];
                        $record['useracbank_timestamp']=$row['useracbank_timestamp'];
                        $record['useracbank_delete']=$row['useracbank_delete'];
                        $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'شماره حساب ها با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    echo json_encode(array('result'=>$usertoken[0]
                    ,"data"=>$usertoken[1]
                    ,'desc'=>$usertoken[2]));

                }
            }
            if ($command=="delete")
            {
                $useracbank_id=$this->post('useracbank_id');
                
                $usertoken=checkusertoken($user_token_str);
                if($usertoken[0]=='ok')
                {
                    $user_id=$usertoken[1];
                    $sql="UPDATE useracbank_tb SET useracbank_delete=1 where useracbank_delete=0 AND  useracbank_user_id=".$user_id." AND useracbank_id=".$useracbank_id."";
                    $output = $this->B_db->run_query_put($sql);
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'حساب با موفقیت حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    echo json_encode(array('result'=>$usertoken[0]
                    ,"data"=>$usertoken[1]
                    ,'desc'=>$usertoken[2]));
                }
            }
            else
                if ($command=="modify")
                {
                    $useracbank_id=$this->post('useracbank_id');
                    
                    $usertoken=checkusertoken($user_token_str);
                    if($usertoken[0]=='ok')
                    {
                        $query="UPDATE useracbank_tb SET ";
                        if(isset($_REQUEST['useracbank_sheba'])){
                            $useracbank_sheba=$this->post('useracbank_sheba');
                            $query.="useracbank_sheba='".$useracbank_sheba."'";}

                        if(isset($_REQUEST['useracbank_bankname'])&&isset($_REQUEST['useracbank_sheba'])){ $query.=",";}
                        if(isset($_REQUEST['useracbank_bankname'])){
                            $useracbank_bankname=$this->post('useracbank_bankname');
                            $query.="useracbank_bankname='".$useracbank_bankname."'";}
                        $query.="where useracbank_id=".$useracbank_id;
                        $result = $this->B_db->run_query_put($query);
                        if($result){
                            echo json_encode(array('result'=>"OK"
                            ,"data"=>""
                            ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                    }else{
                        echo json_encode(array('result'=>$usertoken[0]
                        ,"data"=>$usertoken[1]
                        ,'desc'=>$usertoken[2]));
                    }
                }
        }
    }


}
