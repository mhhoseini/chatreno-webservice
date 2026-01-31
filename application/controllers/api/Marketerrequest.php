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
class Marketerrequest extends REST_Controller {

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
        $this->load->model('B_requests');
        $this->load->model('B_db');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if($this->B_user->checkrequestip('marketerrequest',$command,get_client_ip(),50,50)){
        if ($command=="request_marketer")
        {//register marketer
            $marketer_image_code=$this->post('marketer_image_code') ;
            $marketer_leader_mobile=$this->post('marketer_leader_mobile') ;
            $marketer_coworker=$this->post('marketer_coworker') ;


            $usertoken=checkusertoken($user_token_str);
            if($usertoken[0]=='ok')
            {
                $marketer_user_id=$usertoken[1];
                $result= $this->B_requests->usermarketer_byuser($marketer_user_id);
                if(empty($result))
                {
                    $this->B_requests->del_usermarketer($marketer_user_id);
                    $this->B_requests->add_usermarketer($marketer_user_id,$marketer_image_code,$marketer_leader_mobile,$marketer_coworker);
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('marketer_user_id'=>$marketer_user_id)
                    ,'desc'=>'درخواست شما برای بازاریابی برای بررسی ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                }else
                {
                    echo json_encode(array('result'=>"error"
                    ,"data"=>array('marketer_user_id'=>$marketer_user_id)
                    ,'desc'=>'شما قبلا برای بازاریابی درخواست داده اید'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }

            }else{
                echo json_encode(array('result'=>$usertoken[0]
                ,"data"=>$usertoken[1]
                ,'desc'=>$usertoken[2]));
            }
        }

        if ($command=="checkusermarketer")
        {
            echo json_encode(checkusermarketertoken($user_token_str));
        }
        else if ($command=="changeproperty")
        {

            $usertoken=checkusertoken($user_token_str);
            if($usertoken[0]=='ok')
            {
                if(checkusermarketertoken($user_token_str)['result']=='ok'){
                    $marketer_leader_mobile=$this->post('marketer_leader_mobile');

                    $query="select * from usermarketer_tb where marketer_user_id='".$usertoken[1] ."'";
                    $result1=$this->B_db->run_query($query);
                    $usermarketer=$result1[0];
if($usermarketer['marketer_leader_mobile']==$marketer_leader_mobile) {
    echo json_encode(array('result' => "error"
    , "data" => ""
    , 'desc' => 'شماره همراه هماهنگ کننده قبلا ثبت شده است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}else if($usermarketer['marketer_timestamp_change']!=''){

    $sql="SELECT DATEDIFF(now(),marketer_timestamp_change) as diff
        from usermarketer_tb
        where marketer_user_id=".$usertoken[1];
    $result10=$this->B_db->run_query($sql);
    $diff = $result10[0]['diff'];
    if($diff>90){
        $result = $this->B_requests->update_usermarketer($usertoken[1], $marketer_leader_mobile);
        if ($result) {
            echo json_encode(array('result' => "ok"
            , "data" => $diff
            , 'desc' => 'تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } else {
            echo json_encode(array('result' => "error"
            , "data" => $diff
            , 'desc' => 'تغییرات انجام نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }else{
        echo json_encode(array('result' => "error"
        , "data" => $diff
        , 'desc' => 'کمتر از سه ماه از تغییر هماهنگ کننده گذشته است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

}else{
//**********************************************************************************************************
    $result = $this->B_requests->update_usermarketer($usertoken[1], $marketer_leader_mobile);
    if ($result) {
        echo json_encode(array('result' => "ok"
        , "data" => ""
        , 'desc' => 'تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(array('result' => "ok"
        , "data" => ""
        , 'desc' => 'تغییرات انجام نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    //**********************************************************************************************************
}
                }else{
                    echo json_encode(checkusermarketertoken($user_token_str));
                }
            }else{
                echo json_encode(array('result'=>$usertoken[0]
                ,"data"=>$usertoken[1]
                ,'desc'=>$usertoken[2]));
            }
        }

        else if ($command=="get_comission")
        {

            $usertoken=checkusertoken($user_token_str);
            if($usertoken[0]=='ok')
            {
                if(checkusermarketertoken($user_token_str)['result']=='ok'){
                    $result=$this->B_requests->collect_usermarketer($usertoken[1]);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record=array();
                        $record['marketer_commission_defult_id']=$row['marketer_commission_defult_id'];
                        $record['marketer_user_id']=$row['marketer_user_id'];
                        $record['marketer_commission_defult_percent']=$row['marketer_commission_defult_percent'];
                        $record['marketer_commission_defult_wage']=$row['marketer_commission_defult_wage'];
                        $record['marketer_commission_defult_fieldinsurance_id']=$row['marketer_commission_defult_fieldinsurance_id'];
                        $record['fieldinsurance']=$row['fieldinsurance'];
                        $record['fieldinsurance_id']=$row['fieldinsurance_id'];
                        $record['fieldinsurance_fa']=$row['fieldinsurance_fa'];
                        $record['fieldinsurance_logo_url']=IMGADD.$row['fieldinsurance_logo_url'];
                        $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'درصد بازاریاب در این رشته ها با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//****************************************************************************************************************
                }else{
                    echo json_encode(checkusermarketertoken($user_token_str));
                }
            }else{
                echo json_encode(array('result'=>$usertoken[0]
                ,"data"=>$usertoken[1]
                ,'desc'=>$usertoken[2]));
            }
        }
        else if ($command=="get_comission_leader")
        {

            $usertoken=checkusertoken($user_token_str);
            if($usertoken[0]=='ok')
            {
                if(checkusermarketertoken($user_token_str)['result']=='ok'){
                    $result=$this->B_requests->get_leader_commission();
                    foreach($result as $row)
                    {
                        $record=array();
                        $record['leader_commission_defult_id']=$row['leader_commission_defult_id'];
                        $record['leader_commission_defult_percent']=$row['leader_commission_defult_percent'];
                        $record['leader_commission_defult_wage']=$row['leader_commission_defult_wage'];
                        $record['leader_commission_defult_fieldinsurance_id']=$row['leader_commission_defult_fieldinsurance_id'];
                        $record['fieldinsurance']=$row['fieldinsurance'];
                        $record['fieldinsurance_id']=$row['fieldinsurance_id'];
                        $record['fieldinsurance_fa']=$row['fieldinsurance_fa'];
                        $record['fieldinsurance_logo_url']=IMGADD.$row['fieldinsurance_logo_url'];
                        $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'درصد لیدری بازاریاب در این رشته ها با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    echo json_encode(checkusermarketertoken($user_token_str));
                }
            }else{
                echo json_encode(array('result'=>$usertoken[0]
                ,"data"=>$usertoken[1]
                ,'desc'=>$usertoken[2]));
            }
        }

        else if ($command=="get_reagentrequest")
        {

            $usertoken=checkusertoken($user_token_str);
            if($usertoken[0]=='ok')
            {
                if(checkusermarketertoken($user_token_str)['result']=='ok'){
                    $result = $this->B_requests->get_user_company($usertoken[1]);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record=array();
                        $record['request_id']=$row['request_id'];
                        $record['request_company_id']=$row['request_company_id'];
                        $record['company_name']=$row['company_name'];
                        //$record['company_logo_code']=$row['company_logo_code'];
                        $record['fieldinsurance_id']=$row['fieldinsurance_id'];
                        $record['request_fieldinsurance']=$row['request_fieldinsurance'];
                        $record['request_fieldinsurance_fa']=$row['fieldinsurance_fa'];
                        $record['request_fieldinsurance_image_code']=$row['fieldinsurance_image_code'];
                        $record['request_description']=$row['request_description'];
                        $record['request_price_app']=$row['request_price_app'];
                        $record['request_price_agent']=$row['request_price_agent'];
                        $record['request_last_state_id']=$row['request_last_state_id'];
                        $record['request_reagent_mobile_refralcode']=$row['request_reagent_mobile_refralcode'];
						 //***************************************************************************************************************
                    $query17="select * from state_request_tb where staterequest_request_id=".$row['request_id']." ORDER BY staterequest_id DESC LIMIT 1 ";
                    $result17=$this->B_db->run_query($query17);
                    $state_request17=$result17[0];
                    $record['staterequest_last_timestamp']=$state_request17['staterequest_timestamp'];

                    //***************************************************************************************************************
                   
                        $record['request_last_state_name']=$row['request_state_name'];
                        $record['request_reagent_mobile']=$row['request_reagent_mobile'];
                        $result2 = $this->B_user->get_user($row['request_user_id']);
                        $wallet=$result2[0];
                        if (!empty($result2))
                        {
                            $record['user_name']=$wallet['user_name'];
                            $record['user_family']=$wallet['user_family'];
                        }
                        $result2 = $this->B_requests->get_user_wallet_peycommision_marketer($row['request_id']);
                        if (!empty($result2))
                        {
                            $wallet=$result2[0];
                            $record['user_wallet_id']=$wallet['user_wallet_id'];
                            $record['user_wallet_amount']=$wallet['user_wallet_amount'];
                            $record['user_wallet_timestamp']=$wallet['user_wallet_timestamp'];
                            $record['user_wallet_detail']=$wallet['user_wallet_detail'];
                        }



                        $query10="SELECT SUM(user_wallet_amount) AS sumwallet,user_id,user_name,user_family,user_mobile FROM peycommision_leader_tb,user_wallet_tb,user_tb WHERE user_id=user_wallet_user_id AND user_wallet_id=peycommision_leader_user_wallet_id  AND peycommision_leader_peyback_id=0 
 AND peycommision_leader_request_id=".$row['request_id']. " GROUP BY user_id";
                        $result10=$this->B_db->run_query($query10);
                        $peycommision_leader=$result10[0];
                        $record['peycommision_leader']=$peycommision_leader['sumwallet']+0;
                        $record['peycommision_leader_id']=$peycommision_leader['user_id'];
                        $record['peycommision_leader_name']=$peycommision_leader['user_name'];
                        $record['peycommision_leader_family']=$peycommision_leader['user_family'];
                        $record['peycommision_leader_mobile']=$peycommision_leader['user_mobile'];




                        $query101="SELECT SUM(user_wallet_amount) AS sumwallet,user_id,user_name,user_family,user_mobile FROM peycommision_marketer_tb,user_wallet_tb,user_tb WHERE  user_id=user_wallet_user_id AND user_wallet_id=peycommision_marketer_user_wallet_id  AND peycommision_marketer_payback_id=0 
 AND peycommision_marketer_request_id=".$row['request_id']. " GROUP BY user_id";
                        $result101=$this->B_db->run_query($query101);
                        $peycommision_marketer=$result101[0];
                        $record['peycommision_marketer']=$peycommision_marketer['sumwallet']+0;
                        $record['peycommision_marketer_id']=$peycommision_marketer['user_id'];
                        $record['peycommision_marketer_name']=$peycommision_marketer['user_name'];
                        $record['peycommision_marketer_family']=$peycommision_marketer['user_family'];
                        $record['peycommision_marketer_mobile']=$peycommision_marketer['user_mobile'];

                        $query102="SELECT SUM(user_wallet_amount) AS sumwallet,user_id,user_name,user_family,user_mobile FROM peycommision_user_tb,user_wallet_tb,user_tb WHERE  user_id=user_wallet_user_id AND user_wallet_id=peycommision_user_wallet_id  AND peycommision_user_request_id=".$row['request_id']. " GROUP BY user_id";
                        $result102=$this->B_db->run_query($query102);
                        $peycommision_user=$result102[0];
                        $record['peycommision_user']=$peycommision_user['sumwallet']+0;
                        $record['peycommision_user_id']=$peycommision_user['user_id'];
                        $record['peycommision_user_name']=$peycommision_user['user_name'];
                        $record['peycommision_user_family']=$peycommision_user['user_family'];
                        $record['peycommision_user_mobile']=$peycommision_user['user_mobile'];

                        $record['peysumcommision']=$peycommision_user['sumwallet']+$peycommision_marketer['sumwallet']+$peycommision_leader['sumwallet'];



                        $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'لیست درخواست ها با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                }else{
                    echo json_encode(checkusermarketertoken($user_token_str));
                }
            }else{
                echo json_encode(array('result'=>$usertoken[0]
                ,"data"=>$usertoken[1]
                ,'desc'=>$usertoken[2]));
            }
        }else
            if ($command=="get_leaderrequest")
            {
    
                $usertoken=checkusertoken($user_token_str);
                if($usertoken[0]=='ok')
                {
                    if(checkusermarketertoken($user_token_str)['result']=='ok'){
                        $user_id=$usertoken[1];
                        $result2 = $this->B_user->get_user($user_id);
                        $user=$result2[0];
                        $mobile=$user['user_mobile'];
                        $result = $this->B_requests->collect_company_usermarketer($mobile);
                        $output =array();
                        foreach($result as $row)
                        {
                            $record=array();
                            $record['request_id']=$row['request_id'];
                            $record['request_company_id']=$row['request_company_id'];
                            $record['company_name']=$row['company_name'];
                            $record['company_logo_code']=$row['company_logo_code'];
                            $record['fieldinsurance_id']=$row['fieldinsurance_id'];
                            $record['request_fieldinsurance']=$row['request_fieldinsurance'];
                            $record['request_fieldinsurance_fa']=$row['fieldinsurance_fa'];
                            $record['request_fieldinsurance_image_code']=$row['fieldinsurance_image_code'];
                            $record['request_description']=$row['request_description'];
                            $record['request_price_app']=$row['request_price_app'];
                            $record['request_price_agent']=$row['request_price_agent'];
                            $record['request_last_state_id']=$row['request_last_state_id'];
                            $record['request_last_state_name']=$row['request_state_name'];
                            $record['request_reagent_mobile_refralcode']=$row['request_reagent_mobile_refralcode'];
                            //***************************************************************************************************************
                    $query17="select * from state_request_tb where staterequest_request_id=".$row['request_id']." ORDER BY staterequest_id DESC LIMIT 1 ";
                    $result17=$this->B_db->run_query($query17);
                    $state_request17=$result17[0];
                    $record['staterequest_last_timestamp']=$state_request17['staterequest_timestamp'];

                    //***************************************************************************************************************
                   
                            $record['request_reagent_mobile']=$row['request_reagent_mobile'];

                            $result2 = $this->B_user->get_user_by_moblie($row['request_reagent_mobile']);
                            $wallet=$result2[0];
                            if (!empty($result2))
                            {
                                $record['marketer_name']=$wallet['user_name'];
                                $record['marketer_family']=$wallet['user_family'];
                            }


                            $result2 = $this->B_requests->get_user_wallet_peycommision_leader($row['request_id']);
                            $wallet=$result2[0];
                            if (!empty($result2))
                            {
                                $record['user_wallet_id']=$wallet['user_wallet_id'];
                                $record['user_wallet_amount']=$wallet['user_wallet_amount'];
                                $record['user_wallet_timestamp']=$wallet['user_wallet_timestamp'];
                                $record['user_wallet_detail']=$wallet['user_wallet_detail'];
                            }

                            $query10="SELECT SUM(user_wallet_amount) AS sumwallet,user_id,user_name,user_family,user_mobile FROM peycommision_leader_tb,user_wallet_tb,user_tb WHERE user_id=user_wallet_user_id AND user_wallet_id=peycommision_leader_user_wallet_id AND peycommision_leader_peyback_id=0 
 AND peycommision_leader_request_id=".$row['request_id']. " GROUP BY user_id";
                            $result10=$this->B_db->run_query($query10);
                            $peycommision_leader=$result10[0];
                            $record['peycommision_leader']=$peycommision_leader['sumwallet']+0;
                            $record['peycommision_leader_id']=$peycommision_leader['user_id'];
                            $record['peycommision_leader_name']=$peycommision_leader['user_name'];
                            $record['peycommision_leader_family']=$peycommision_leader['user_family'];
                            $record['peycommision_leader_mobile']=$peycommision_leader['user_mobile'];

                            $query101="SELECT SUM(user_wallet_amount) AS sumwallet,user_id,user_name,user_family,user_mobile FROM peycommision_marketer_tb,user_wallet_tb,user_tb WHERE  user_id=user_wallet_user_id AND user_wallet_id=peycommision_marketer_user_wallet_id  AND peycommision_marketer_payback_id=0 
 AND peycommision_marketer_request_id=".$row['request_id']. " GROUP BY user_id";
                            $result101=$this->B_db->run_query($query101);
                            $peycommision_marketer=$result101[0];
                            $record['peycommision_marketer']=$peycommision_marketer['sumwallet']+0;
                            $record['peycommision_marketer_id']=$peycommision_marketer['user_id'];
                            $record['peycommision_marketer_name']=$peycommision_marketer['user_name'];
                            $record['peycommision_marketer_family']=$peycommision_marketer['user_family'];
                            $record['peycommision_marketer_mobile']=$peycommision_marketer['user_mobile'];

                            $query102="SELECT SUM(user_wallet_amount) AS sumwallet,user_id,user_name,user_family,user_mobile FROM peycommision_user_tb,user_wallet_tb,user_tb WHERE  user_id=user_wallet_user_id AND user_wallet_id=peycommision_user_wallet_id  AND peycommision_user_request_id=".$row['request_id']. " GROUP BY user_id";
                            $result102=$this->B_db->run_query($query102);
                            $peycommision_user=$result102[0];
                            $record['peycommision_user']=$peycommision_user['sumwallet']+0;
                            $record['peycommision_user_id']=$peycommision_user['user_id'];
                            $record['peycommision_user_name']=$peycommision_user['user_name'];
                            $record['peycommision_user_family']=$peycommision_user['user_family'];
                            $record['peycommision_user_mobile']=$peycommision_user['user_mobile'];

                            $record['peysumcommision']=$peycommision_user['sumwallet']+$peycommision_marketer['sumwallet']+$peycommision_leader['sumwallet'];



                            $output[]=$record;
                        }
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>$output
                        ,'desc'=>'لیست درخواست ها با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

//****************************************************************************************************************
                    }else{
                        echo json_encode(checkusermarketertoken($user_token_str));
                    }
                }else{
                    echo json_encode(array('result'=>$usertoken[0]
                    ,"data"=>$usertoken[1]
                    ,'desc'=>$usertoken[2]));
                }

            }
    }
}}
