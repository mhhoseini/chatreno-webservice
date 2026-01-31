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
class Getrequest extends REST_Controller {

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

        if($this->B_user->checkrequestip('getrequest',$command,get_client_ip(),50,50)){
            if ($command=="get_request")
            {
                $usertoken=checkusertoken($this->user_token_str);
                if($usertoken[0]=='ok')
                {
                    $user_id = $usertoken[1];
                    $request_id = $this->post('request_id');
                    $sql = "SELECT * FROM request_tb WHERE request_id=".$request_id;
                    $result20 = $this->B_db->run_query($sql);
                    if(!empty($result20[0])){
                        $fieldinsurance = $result20[0]['request_fieldinsurance'];
                        //check is the user the owner of requst
                        if($user_id == $result20[0]['request_user_id']){
                            $covarage =array();
                            if( $result20[0]['request_packageinsurance_id']==0||$result20[0]['request_packageinsurance_id']=='0')
                            {
                                if(strpos($fieldinsurance, "thirdpartyins")!== false)

                                //    if ($fieldinsurance=="thirdpartyins"||$fieldinsurance=="thirdpartyins2"||$fieldinsurance=="thirdpartyins3"||$fieldinsurance=="thirdpartyins4")
                                {
                                $query = "SELECT *
                                    FROM request_thirdparty_tb,request_tb,car_tb,carmode_tb,carcompany_tb,thirdparty_discnt_thirdparty_tb,thirdparty_discnt_driver_tb,thirdparty_damage_driver_tb,thirdparty_damage_financial_tb,thirdparty_damage_human_tb,thirdparty_coverage_tb,thirdparty_usefor_tb,thirdparty_time_tb,jsonpricing_tb
                                    WHERE request_thirdparty_requset_id=request_id
                                    AND request_thirdparty_car_id=car_id
                                    AND car_mode_id=carmode_id
                                    AND carcompany_id=car_company_id
                                    AND request_thirdparty_discnt_thirdprty_id=thirdparty_discnt_thirdparty_id
                                    AND request_thirdparty_discnt_driver_id=thirdparty_discnt_driver_id
                                    AND request_thirdparty_damage_drive_id=thirdparty_damage_driver_id
                                    AND request_thirdparty_damage_financial_id=thirdparty_damage_financial_id
                                    AND request_thirdparty_damage_human_id=thirdparty_damage_human_id
                                    AND request_thirdparty_coverage_id=thirdparty_coverage_id
                                    AND request_thirdparty_usefor_id=thirdparty_usefor_id
                                    AND request_thirdparty_time_id=thirdparty_time_id
                                    AND request_jsonpricing_id=jsonpricing_id
                                    AND request_id=$request_id
                                    AND request_user_id=$user_id";
                                $result = $this->B_db->run_query($query);
                                $thirdparty=$result[0];
                                if($thirdparty[request_thirdparty_lastcompany_id]==0||$thirdparty[request_thirdparty_lastcompany_id]=='0'){
                                    $result[0]['company_name']=' بدون بیمه نامه';

                                }else
                                {
                                    $query0=" SELECT * FROM company_tb WHERE company_id=$thirdparty[request_thirdparty_lastcompany_id]";
                                    $result0 = $this->B_db->run_query($query0);
                                    $company=$result0[0];
                                    $result[0]['company_name']=$company['company_name'];

                                }

                            }else
                            if($fieldinsurance == "bodycarins"){
                                $query = "SELECT *
                                    FROM request_bodycar_tb,request_tb,car_tb,carmode_tb,carcompany_tb,
                                    bodycar_time_tb,jsonpricing_tb,bodycar_usefor_tb,bodycar_discnt_life_tb,bodycar_discnt_accbank_tb,bodycar_discnt_tb,
                                    bodycar_discnt_thirdparty_tb
                                    WHERE request_bodycar_requset_id=request_id
                                    AND request_bodycar_car_id=car_id
                                    AND car_mode_id=carmode_id
                                    AND carcompany_id=car_company_id
                                    AND request_bodycar_time_id=bodycar_time_id
                                    AND request_bodycar_usefor_id=bodycar_usefor_id
                                    AND request_bodycar_discnt_life_id=bodycar_discnt_life_id
                                    AND request_bodycar_discnt_accbank_id=bodycar_discnt_accbank_id
                                    AND request_bodycar_discnt_id=bodycar_discnt_id
                                    AND request_jsonpricing_id=jsonpricing_id
                                    AND request_bodycar_discnt_thirdparty_id=bodycar_discnt_thirdparty_id
                                    AND request_id=$request_id
                                    AND request_user_id=$user_id";
                                $result = $this->B_db->run_query($query);
                                $bodycar=$result[0];
                                foreach(json_decode($bodycar['request_bodycar_coverage']) as $bodycar_coverage) {

                                    $query0=" SELECT * FROM bodycar_coverage_tb WHERE bodycar_coverage_id=$bodycar_coverage";
                                    $result0 = $this->B_db->run_query($query0);
                                    $covarage[]=$result0;
                                }
                            }else if($fieldinsurance == "travelins"){
                                $query = "SELECT *
                                    FROM request_travel_tb,request_tb,jsonpricing_tb,travel_plan_tb,travel_destination_tb,travel_time_tb,
                                    travel_coverage_tb,travel_helper_tb
                                    WHERE request_travel_requset_id=request_id
                                    AND request_jsonpricing_id=jsonpricing_id
                                    AND request_travel_plan_id=travel_plan_id
                                    AND request_travel_destination_id=travel_destination_id
                                    AND request_travel_time_id=travel_time_id
                                    AND request_travel_coverage_id=travel_coverage_id
                                    AND request_travel_helper_id=travel_helper_id
                                    AND request_id=$request_id
                                    AND request_user_id=$user_id";
                                $result = $this->B_db->run_query($query);

                            }else if($fieldinsurance == "elevatorins"){
                                $query = "SELECT * FROM request_elevator_tb,request_tb,jsonpricing_tb,elevator_kind_tb,elevator_kinddoor_tb,elevator_uses_tb,elevator_coverage_tb
                                    WHERE request_elevator_requset_id=request_id
                                    AND request_elevator_coverage_id = elevator_coverage_id 
                                    AND request_elevator_uses_id =elevator_uses_id 
                                    AND request_elevator_kinddoor_id =elevator_kinddoor_id 
                                    AND request_elevator_kind_id =elevator_kind_id 
                                    AND request_jsonpricing_id=jsonpricing_id
                                    AND request_id=$request_id
                                    AND request_user_id=$user_id";
                                $result = $this->B_db->run_query($query);

                            }else if($fieldinsurance == "firehomeins"){
                                $query = "SELECT *
                                    FROM request_firehome_tb,request_tb,jsonpricing_tb,firehome_kind_tb,
                                    firehome_typeofcons_tb,firehome_costcons_tb
                                    WHERE request_firehome_requset_id=request_id
                                    AND request_jsonpricing_id=jsonpricing_id
                                    and request_firehome_kind_id=firehome_kind_id
                                    AND request_firehome_typeofcons_id=firehome_typeofcons_id
                                    AND request_firehome_costcons_id=firehome_costcons_id
                                    AND request_id=$request_id
                                    AND request_user_id=$user_id";
                                $result = $this->B_db->run_query($query);
                                $firehome=$result[0];
                                foreach(json_decode($firehome['request_firehome_coverage']) as $firehome_coverage) {

                                    $query0=" SELECT * FROM firehome_coverage_tb WHERE firehome_coverage_id=$firehome_coverage";
                                    $result0 = $this->B_db->run_query($query0);
                                    $covarage[]=$result0;
                                }

                            }else if($fieldinsurance == "responsdoctorsins"){
                                $query = "SELECT *
                                    FROM request_responsdoctors_tb,request_tb,jsonpricing_tb,responsdoctors_medicspecialty_tb,responsdoctors_damage_tb
                                    WHERE request_responsdoctors_requset_id=request_id
                                    AND request_jsonpricing_id=jsonpricing_id
                                    AND request_responsdoctors_medicspecialty_id=responsdoctors_medicspecialty_id
                                    AND request_responsdoctors_damage_id=responsdoctors_damage_id
                                    AND request_id=$request_id
                                    AND request_user_id=$user_id";
                                    $result = $this->B_db->run_query($query);
                                if(empty($result)){
                                    $query = "SELECT *
                                    FROM request_responsdoctors_tb,request_tb,jsonpricing_tb,responsdoctors_paramedicspecialty_tb,responsdoctors_damage_tb
                                    WHERE request_responsdoctors_requset_id=request_id
                                    AND request_jsonpricing_id=jsonpricing_id
                                    AND request_responsdoctors_paramedicspecialty_id=responsdoctors_paramedicspecialty_id
                                    AND request_responsdoctors_damage_id=responsdoctors_damage_id
                                    AND request_id=$request_id
                                    AND request_user_id=$user_id";
                                    $result = $this->B_db->run_query($query);
                                }

                            } else if($fieldinsurance == "buildingqualityins"){
                                $query = "SELECT *
                                    FROM request_buildingquality_tb,request_tb,jsonpricing_tb,buildingquality_costcons_tb
                                    WHERE request_buildingquality_requset_id=request_id
                                    AND request_jsonpricing_id=jsonpricing_id
                                    AND request_buildingquality_costcons_id=buildingquality_costcons_id
                                    AND request_id=$request_id
                                    AND request_user_id=$user_id";
                                $result = $this->B_db->run_query($query);
                            }else if($fieldinsurance == "coronains"){
                                $query = "SELECT *
                                    FROM request_corona_tb,request_tb,jsonpricing_tb,corona_old_tb,corona_coverage_tb,corona_time_tb
                                    WHERE request_corona_requset_id=request_id
                                    AND request_jsonpricing_id=jsonpricing_id
                                    AND request_corona_old_id=corona_old_id
                                    AND request_corona_coverage_id=corona_coverage_id
                                    AND request_corona_time_id=corona_time_id
                                    AND request_id=$request_id
                                    AND request_user_id=$user_id";
                                $result = $this->B_db->run_query($query);
                            }else if($fieldinsurance == "therapyins"){
                                $query = "SELECT *
                                    FROM request_therapy_tb,request_tb,jsonpricing_tb,therapy_baseinsurer_tb,therapy_coverage_tb
                                    WHERE request_therapy_requset_id=request_id
                                    AND request_jsonpricing_id=jsonpricing_id
                                    AND therapy_baseinsurer_id=request_therapy_baseinsurer_id
                                    AND request_therapy_coverage_id=therapy_coverage_id
                                    AND request_id=$request_id
                                    AND request_user_id=$user_id ";
                                $result = $this->B_db->run_query($query);
                            }else {
                                $query = "SELECT *
                                    FROM request_tb,jsonpricing_tb
                                    WHERE  request_jsonpricing_id=jsonpricing_id
                                    AND request_id=$request_id
                                    AND request_user_id=$user_id ";
                                $result = $this->B_db->run_query($query);
                            }
							//*********************************************************************
                   
                         $organ=$result20[0]['request_organ'];

					//*********************************************************************
                            echo json_encode(array('result'=>"ok" ,"data"=>$result,'covarage'=> $covarage,'organ'=> $organ),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }else{
                                $organ=$result20[0]['request_organ'];
                                $query = "SELECT *
                                    FROM request_tb,jsonpricing_tb,packageinsurance_tb
                                    WHERE  request_jsonpricing_id=jsonpricing_id
                                    AND packageinsurance_id=request_packageinsurance_id  
                                    AND request_id=$request_id
                                    AND request_user_id=$user_id ";
                                $result = $this->B_db->run_query($query);
                                echo json_encode(array('result'=>"ok" ,"data"=>$result,'covarage'=> $covarage,'organ'=> $organ),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
                        }else {
                            echo json_encode(array('result'=>"error"
                            ,'data'=>''
                            ,'desc'=>'کاربر اصلی با کاربر درخواست دهنده تطابق ندارد!'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                    }
                }else{
                    echo json_encode(array('result'=>"error"
                    ,'data'=>''
                    ,'desc'=>'کد ارسالی شما صحیح نمی باشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }else
                if ($command=="get_pricerequest") {
                $usertoken = checkusertoken($this->user_token_str);
                if ($usertoken[0] == 'ok') {
                    //**************************************************
                    $request_id = $this->post("request_id");
                    $query = "select * from jsonpricing_tb,request_tb,fieldinsurance_tb where  fieldinsurance=request_fieldinsurance AND jsonpricing_request_id=request_id AND jsonpricing_request_id=$request_id AND request_user_id=$usertoken[1]";

                    $result = $this->B_db->run_query($query);
                    $output = array();
                    foreach ($result as $row) {
                        $record = array();
                        $record['fieldinsurance_logo_url']=IMGADD.$row['fieldinsurance_logo_url'];
                        $record['fieldinsurance_id']=$row['fieldinsurance_id'];
                        $record['request_fieldinsurance_fa']=$row['fieldinsurance_fa'];
                        $record['jsonpricing_id']=$row['jsonpricing_id'];
                        $record['jsonpricing_text']=$row['jsonpricing_text'];
                        $record['jsonpricing_employee_id']=$row['jsonpricing_employee_id'];
                        $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>' قیمت بیمه نامه ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    //**************************************************

                } else {
                echo json_encode(array('result' => $usertoken[0]
                , "data" => $usertoken[1]
                , 'desc' => $usertoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                 }
            }else
                    if ($command=="get_thirdparty_request") {

                            //**************************************************
                            $request_id = $this->post("request_id");
                            $query = "select * from request_thirdparty_tb,car_tb where car_id=request_thirdparty_car_id AND  request_thirdparty_requset_id=$request_id";

                            $result = $this->B_db->run_query($query);
                        $output = array();
                        foreach ($result as $row) {
                            $record = array();
                            $record['car_id']=$row['request_thirdparty_car_id'];
                            $record['carmode_id']=$row['car_mode_id'];
                            $record['car_group_id']=$row['car_group_id'];
                            $record['car_company_id']=$row['car_company_id'];
                             $record['thirdparty_yearofcons_id'] =$row['request_thirdparty_yearofcons_id'];
                             $record['thirdparty_lastcompany']=$row['request_thirdparty_lastcompany_id'];
                             $record['thirdparty_last_date_sart']=$row['request_thirdparty_last_date_sart'];
                             $record['thirdparty_last_date_end']=$row['request_thirdparty_last_date_end'];
                             $record['thirdparty_discnt_thirdparty_id']=$row['request_thirdparty_discnt_thirdprty_id'];
                             $record['thirdparty_discnt_driver_id']=$row['request_thirdparty_discnt_driver_id'];
                             $record['thirdparty_damage_human_id']=$row['request_thirdparty_damage_human_id'];
                             $record['thirdparty_damage_financial_id']=$row['request_thirdparty_damage_financial_id'];
                             $record['thirdparty_damage_driver_id']=$row['request_thirdparty_damage_drive_id'];
                             $record['thirdparty_coverage_id']=$row['request_thirdparty_coverage_id'];
                             $record['damage_human_id']=$row['request_damage_human_id'];
                             $record['damage_financial_id']=$row['request_damage_financial_id'];
                             $record['damage_driver_id']=$row['request_damage_driver_id'];
                             $record['thirdparty_usefor_id']=$row['request_thirdparty_usefor_id'];
                             $record['thirdparty_time_id']=$row['request_thirdparty_time_id'];
                             $record['transition']=$row['request_thirdparty_transition'];
                             $record['thirdparty_yadak']=$row['request_thirdparty_yadak'];


                            $output[]=$record;
                        }
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>$output
                            ,'desc'=>' قیمت بیمه نامه ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            //**************************************************

                    }
                    else
                        if ($command=="get_thirdparty_request_ready") {

                            //**************************************************
                            $request_id = $this->post("request_id");
                            $query1 = "SELECT MAX(thirdparty_discnt_thirdparty_id) as max FROM thirdparty_discnt_thirdparty_tb where 1 ";
                            $result1 = $this->B_db->run_query($query1)[0];
                             $max_discnt_thirdparty=  $result1['max'];

                            $query2 = "SELECT MAX(thirdparty_discnt_driver_id) as max FROM thirdparty_discnt_driver_tb where 1 ";
                            $result2 = $this->B_db->run_query($query2)[0];
                            $max_discnt_driver=  $result2['max'];


                            $query = "select * from request_thirdparty_tb,car_tb,requst_ready_tb,request_tb where requst_ready_request_id=request_id AND requst_ready_request_id=request_thirdparty_requset_id AND car_id=request_thirdparty_car_id AND  request_thirdparty_requset_id=$request_id";
                            $result = $this->B_db->run_query($query);
                            $output = array();
                            foreach ($result as $row) {
                                $record = array();
                                $record['car_id']=$row['request_thirdparty_car_id'];
                                $record['carmode_id']=$row['car_mode_id'];
                                $record['car_group_id']=$row['car_group_id'];
                                $record['car_company_id']=$row['car_company_id'];
                                $record['thirdparty_yearofcons_id'] =$row['request_thirdparty_yearofcons_id'];
                                $record['thirdparty_lastcompany']=$row['request_company_id'];
                                $record['thirdparty_last_date_sart']=$row['requst_ready_start_date'];
                                $record['thirdparty_last_date_end']=$row['requst_ready_end_date'];
                                if(intval($row['request_thirdparty_discnt_thirdprty_id'])+1>$max_discnt_thirdparty)
                                {
                                    $record['thirdparty_discnt_thirdparty_id']=$max_discnt_thirdparty;
                                }else{
                                    $record['thirdparty_discnt_thirdparty_id']=strval(intval($row['request_thirdparty_discnt_thirdprty_id'])+1);
                                }
                                if(intval($row['thirdparty_discnt_driver_id'])+1>$max_discnt_driver)
                                {
                                    $record['thirdparty_discnt_driver_id']=$max_discnt_driver;
                                }else{
                                    $record['thirdparty_discnt_driver_id']=strval(intval($row['request_thirdparty_discnt_driver_id'])+1);
                                }
//                                $record['thirdparty_damage_human_id']=$row['request_thirdparty_damage_human_id'];
//                                $record['thirdparty_damage_financial_id']=$row['request_thirdparty_damage_financial_id'];
//                                $record['thirdparty_damage_driver_id']=$row['request_thirdparty_damage_drive_id'];
                                $record['thirdparty_usefor_id']=$row['request_thirdparty_usefor_id'];
                                $record['thirdparty_yadak']=$row['request_thirdparty_yadak'];


                                $output[]=$record;
                            }
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>$output
                            ,'desc'=>' قیمت بیمه نامه ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            //**************************************************

                        }

            }
        }
    }

