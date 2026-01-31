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
class Requeststep1 extends REST_Controller {

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
            $fieldinsurance=$this->post('fieldinsurance') ;
            $fieldinsurance_id = 0;
            $query1 = "SELECT fieldinsurance_id FROM fieldinsurance_tb where fieldinsurance='".$fieldinsurance."'";
            $result1 = $this->B_db->run_query($query1);
            if(!empty($result1)){
                $fieldinsurance_id = $result1[0]['fieldinsurance_id'];
            }

            if ($command=="get_request_package"){
                

                $request_price_app=$this->post('request_price_app') ;
                $request_company_id=$this->post('request_company_id') ;
                $jsonpricing_text=$this->post('jsonpricing_text') ;
                $request_organ=$this->post('request_organ',0) ;
                $request_packageinsurance_id=$this->post('packageinsurance_id',0) ;

                $jsonpricing_text1=str_replace('&#34;','"',$jsonpricing_text);



                $query1="INSERT INTO jsonpricing_tb( jsonpricing_text, jsonpricing_date,	jsonpricing_fieldinsurance) VALUES
                                                  ( '".$jsonpricing_text1."'      ,   now()         , '$fieldinsurance') ";
                $result1=$this->B_db->run_query_put($query1);
                $jsonpricing_id=$this->db->insert_id();


                $query2="INSERT INTO request_tb(request_user_id, request_fieldinsurance,request_company_id, request_price_app, request_last_state_id,request_jsonpricing_id,request_organ,request_packageinsurance_id,request_fieldinsurance_id) VALUES
                                             (".$usertoken[1].",'$fieldinsurance'     ,$request_company_id ,$request_price_app,0                   ,$jsonpricing_id       ,$request_organ,$request_packageinsurance_id,$fieldinsurance_id) ";
                $result=$this->B_db->run_query_put($query2);
                $request_id=$this->db->insert_id();

                if($request_organ!=0)
                {
                    $contract_id=$this->post('contract_id') ;
                    $query11="INSERT INTO organ_request_tb
                        (organ_request_request_id, organ_request_contract_id)
                    VALUES($request_id, $contract_id); ";
                    $result11=$this->B_db->run_query_put($query11);
                }

                $query3="INSERT INTO state_request_tb(staterequest_request_id, staterequest_state_id, staterequest_timestamp, staterequest_desc          ) VALUES
                                                     (".$request_id."        ,      0               ,  now()                 ,' انتخاب قیمت درخواست') ";
                $staterequest_id=$this->B_db->run_query_put($query3);

                if($result){
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('request_id'=>$request_id)
                    ,'desc'=>'درخواست اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    echo json_encode(array('result'=>"error"
                    ,"data"=>$request_id
                    ,'desc'=>'درخواست اضافه نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }

                }else
                if ($command=="get_requestid"){
                $request_price_app=$this->post('request_price_app') ;
                $request_company_id=$this->post('request_company_id') ;
                $request_jsonpricing_id=$this->post('request_jsonpricing_id') ;
                $fieldinsurance=$this->post('fieldinsurance') ;

                $request_organ=$this->post('request_organ',0) ;
                $query="INSERT INTO request_tb(request_user_id, request_fieldinsurance,request_company_id, request_price_app, request_last_state_id,request_jsonpricing_id,request_organ,request_fieldinsurance_id) VALUES
                             (".$usertoken[1].",'$fieldinsurance' ,$request_company_id ,$request_price_app,0,$request_jsonpricing_id,$request_organ,$fieldinsurance_id) ";
                $result=$this->B_db->run_query_put($query);
                  $request_id=$this->db->insert_id();

        $query1="INSERT INTO state_request_tb(staterequest_request_id, staterequest_state_id, staterequest_timestamp, staterequest_desc          ) VALUES
                                    (".$request_id."        ,      0               ,  now()                 ,' انتخاب قیمت درخواست') ";
                    $staterequest_id=$this->B_db->run_query_put($query1);
				if($request_organ!=0)
				{
				$contract_id=$this->post('contract_id') ;
				$query11="INSERT INTO organ_request_tb
                        (organ_request_request_id, organ_request_contract_id)
                    VALUES($request_id, $contract_id); ";
	                $result11=$this->B_db->run_query_put($query11);
				}
if(strpos($fieldinsurance, "thirdpartyins")!== false)
               // if ($fieldinsurance=="thirdpartyins"||$fieldinsurance=="thirdpartyins2"||$fieldinsurance=="thirdpartyins3"||$fieldinsurance=="thirdpartyins4")
                {
                    $car_id=$this->post('car_id') ;
            $thirdparty_discnt_thirdparty_id=$this->post('thirdparty_discnt_thirdparty_id') ;
            $thirdparty_discnt_driver_id=$this->post('thirdparty_discnt_driver_id') ;
            $thirdparty_coverage_id=$this->post('thirdparty_coverage_id') ;
            $thirdparty_yearofcons_id=$this->post('thirdparty_yearofcons_id') ;
            $thirdparty_last_date_sart=$this->post('thirdparty_last_date_sart') ;
            $thirdparty_last_date_end=$this->post('thirdparty_last_date_end') ;
            $thirdparty_lastcompany_id=$this->post('thirdparty_lastcompany') ;
            $thirdparty_usefor_id=$this->post('thirdparty_usefor_id') ;
            $thirdparty_time_id=$this->post('thirdparty_time_id') ;
            $car_mode_id=$this->post('car_mode_id') ;
            $thirdparty_damage_driver_id=$this->post('thirdparty_damage_driver_id',1) ;
			$thirdparty_damage_human_id=$this->post('thirdparty_damage_human_id',1) ;
						$thirdparty_damage_financial_id=$this->post('thirdparty_damage_financial_id',1) ;
						$thirdparty_transition=$this->post('transition',1) ;
                    $thirdparty_yadak = $this->post('thirdparty_yadak',0);

            $def=date("Y")-jdate('Y','',"",'','en');
            $ynow=date("Y");
			$thirdparty_yersofcons=($ynow-$thirdparty_yearofcons_id).'-'.($ynow-$thirdparty_yearofcons_id-$def);
                    $query2="INSERT INTO request_thirdparty_tb( request_thirdparty_requset_id, request_thirdparty_car_id, request_thirdparty_yearofcons_id, request_thirdparty_lastcompany_id, request_thirdparty_last_date_sart, request_thirdparty_last_date_end, request_thirdparty_discnt_thirdprty_id, request_thirdparty_discnt_driver_id, request_thirdparty_damage_human_id, request_thirdparty_damage_financial_id, request_thirdparty_damage_drive_id, request_thirdparty_coverage_id, request_damage_human_id    , request_damage_financial_id    , request_damage_driver_id        ,request_thirdparty_usefor_id,request_thirdparty_time_id,request_thirdparty_transition,request_thirdparty_yadak) VALUES 
                                                               ( $request_id                 ,  $car_id                 , '$thirdparty_yersofcons'       ,    $thirdparty_lastcompany_id    , '$thirdparty_last_date_sart'       , '$thirdparty_last_date_end'      ,      $thirdparty_discnt_thirdparty_id ,       $thirdparty_discnt_driver_id , $thirdparty_damage_human_id      , $thirdparty_damage_financial_id        ,  $thirdparty_damage_driver_id     , $thirdparty_coverage_id       , $thirdparty_damage_human_id, $thirdparty_damage_financial_id, $thirdparty_damage_driver_id,    $thirdparty_usefor_id   ,        $thirdparty_time_id ,$thirdparty_transition,'$thirdparty_yadak') ";
                    $result2=$this->B_db->run_query_put($query2);
                }else  if ($fieldinsurance=="elevatorins")
                {
                    $elevator_coverage_id = $this->post('elevator_coverage_id', 1);
                    $elevator_numberstop_id = $this->post('elevator_numberstop_id', 1);
                    $elevator_kind_id = $this->post('elevator_kind_id', 1);
                    $elevator_kinddoor_id = $this->post('elevator_kinddoor_id', 1);
                    $elevator_capacity_id = $this->post('elevator_capacity_id', 1);
                    $elevator_yearofcons_id= $this->post('elevator_yearofcons_id', 1);

                    $elevator_uses_id = $this->post('elevator_uses_id', 1);

                    $def=date("Y")-jdate('Y','',"",'','en');
                    $ynow=date("Y");
                    $elevator_yearofcons=($ynow-$elevator_yearofcons_id).'-'.($ynow-$elevator_yearofcons_id-$def);
                    $query2="INSERT INTO request_elevator_tb( request_elevator_requset_id, request_elevator_numberstop_id, request_elevator_kinddoor_id, request_elevator_capacity_id, request_elevator_yearofcons_id, request_elevator_uses_id, request_elevator_kind_id,request_elevator_coverage_id) VALUES 
                                                               ( $request_id             ,  $elevator_numberstop_id         , $elevator_kinddoor_id     ,    $elevator_capacity_id    , '$elevator_yearofcons'         , $elevator_uses_id       ,  $elevator_kind_id ,       $elevator_coverage_id  ) ";
                    $result2=$this->B_db->run_query_put($query2);
                }else if ($fieldinsurance=="firehomeins")
                {
                    $firehome_kind_id=$this->post('firehome_kind_id') ;
                    $firehome_unit=$this->post('firehome_unit',0) ;
                    $firehome_typeofcons_id=$this->post('firehome_typeofcons_id') ;
                    $firehome_buildinglife_id=$this->post('firehome_buildinglife_id',0) ;
                    $firehome_area=$this->post('firehome_area') ;
                    $firehome_cost_furniture=$this->post('firehome_cost_furniture') ;
                    $firehome_costcons_id=$this->post('firehome_costcons_id') ;
						
						 $def=date("Y")-jdate('Y','',"",'','en');
            $ynow=date("Y");
			$firehome_buildinglife_id=($ynow-$firehome_buildinglife_id-$def);
						 
		  
						if(isset($_REQUEST['firehome_coverage_id']))
					{
                    $firehome_coverage =json_encode( $_REQUEST['firehome_coverage_id']);
					}else{
				    $firehome_coverage ='';
					}
						if(isset($_REQUEST['firehome_exterafield']))
					{
							             $output =array();
                                        $valueextrafields= $this->post('firehome_exterafield');
                                        foreach($valueextrafields as $firehome_exterafield) {
                                            $output[] = json_decode($firehome_exterafield);
                                        }
                                    																				
                    $firehome_exterafield = json_encode($output);
					}else{
				    $firehome_exterafield ='';
					}

					
                    $query2="INSERT INTO request_firehome_tb( request_firehome_requset_id, request_firehome_kind_id, request_firehome_unit, request_firehome_typeofcons_id, request_firehome_buildinglife_id, request_firehome_area, request_firehome_cost_furniture, request_firehome_costcons_id, request_firehome_coverage, request_firehome_exterafield) VALUES
                                                 ( $request_id,               $firehome_kind_id,           $firehome_unit,           $firehome_typeofcons_id  , $firehome_buildinglife_id, $firehome_area       ,      $firehome_cost_furniture         ,    $firehome_costcons_id               ,'$firehome_coverage'           ,'$firehome_exterafield') ";
                    $result2=$this->B_db->run_query_put($query2);
                }else  if ($fieldinsurance=="buildingqualityins")
                {
                    $buildingquality_area=$this->post('buildingquality_area') ;
                    $buildingquality_costcons_id=$this->post('buildingquality_costcons_id') ;
                    $query2="INSERT INTO request_buildingquality_tb( request_buildingquality_requset_id, request_buildingquality_area, request_buildingquality_costcons_id) VALUES 
                                                        ( $request_id       ,             $buildingquality_area            ,       $buildingquality_costcons_id) ";
                    $result2=$this->B_db->run_query_put($query2);
                }else  if ($fieldinsurance=="coronains")
                {
                    $corona_old_id=$this->post('corona_old_id') ;
                    $corona_coverage_id=$this->post('corona_coverage_id') ;
                    $corona_time_id=$this->post('corona_time_id') ;

                    $query2="INSERT INTO request_corona_tb
( request_corona_requset_id, request_corona_old_id, request_corona_coverage_id, request_corona_time_id) VALUES
( $request_id       ,             $corona_old_id   ,    $corona_coverage_id,    $corona_time_id) ";
                    $result2=$this->B_db->run_query_put($query2);
                }
                else  if ($fieldinsurance=="therapyins")
                {
                    $therapy_oneormore=$this->post('therapy_oneormore') ;
                    $therapy_baseinsurer_id=$this->post('therapy_baseinsurer_id') ;
                    $therapy_coverage_id=$this->post('therapy_coverage_id') ;
                    $therapy_age0_15=$this->post('therapy_age0_15') ;
                    $therapy_age16_50=$this->post('therapy_age16_50') ;
                    $therapy_age51_60=$this->post('therapy_age51_60') ;
                    $therapy_age61_70=$this->post('therapy_age61_70') ;

                    $query2="INSERT INTO request_therapy_tb
( request_therapy_requset_id, request_therapy_oneormore, request_therapy_baseinsurer_id, request_therapy_coverage_id, request_therapy_age0_15, request_therapy_age16_50, request_therapy_age51_60, request_therapy_age61_70)
VALUES( $request_id       ,             $therapy_oneormore   ,   $therapy_baseinsurer_id,   $therapy_coverage_id,   $therapy_age0_15      ,   $therapy_age16_50            ,   $therapy_age51_60,   $therapy_age61_70) ";
                    $result2=$this->B_db->run_query_put($query2);
                }else if ($fieldinsurance=="bodycarins")
                {
                    $car_id=$this->post('car_id') ;
                    $bodycar_usefor_id=$this->post('bodycar_usefor_id') ;
                    $bodycar_time_id=$this->post('bodycar_time_id') ;
                    $bodycar_yearofcons_id=$this->post('bodycar_yearofcons_id') ;
                    $bodycar_price=$this->post('bodycar_price') ;
                    $car_mode_id=$this->post('car_mode_id') ;
                    $bodycar_discnt_life_id=$this->post('bodycar_discnt_life_id',0) ;
                    $bodycar_discnt_accbank_id=$this->post('bodycar_discnt_accbank_id',0) ;
                    $bodycar_discnt_id=$this->post('bodycar_discnt_id',0) ;
                    $bodycar_discnt_thirdparty_id=$this->post('bodycar_discnt_thirdparty_id',1) ;
                    $bodycar_discnt_thirdparty_company_id=$this->post('bodycar_discnt_thirdparty_company_id',0) ;
                    $body_car_import=$this->post('body_car_import') ;
                    $bodycar_not_used=$this->post('bodycar_not_used') ;
                    $bodycar_cash=$this->post('bodycar_cash') ;

					if(isset($_REQUEST['bodycar_coverage_id']))
					{
                    $bodycar_coverage =json_encode( $_REQUEST['bodycar_coverage_id']);
					}else{
				    $bodycar_coverage ='';
					}
					
					 $def=date("Y")-jdate('Y','',"",'','en');
                     $ynow=date("Y");
			         $bodycar_yearofcons=($ynow-$bodycar_yearofcons_id).'-'.($ynow-$bodycar_yearofcons_id-$def);
           
		   
					$query2="INSERT INTO request_bodycar_tb( request_bodycar_requset_id, request_bodycar_car_id, request_bodycar_yearofcons_id, request_bodycar_price, request_bodycar_discnt_life_id, request_bodycar_discnt_accbank_id, request_bodycar_discnt_id, request_bodycar_discnt_thirdparty_id, request_bodycar_discnt_thirdparty_company_id, request_bodycar_coverage, request_bodycar_time_id, request_bodycar_not_used, request_body_car_import, request_bodycar_usefor_id, request_bodycar_cash) VALUES 
                                                             ( $request_id,               $car_id           ,       '$bodycar_yearofcons',         $bodycar_price   , $bodycar_discnt_life_id             , $bodycar_discnt_accbank_id  ,    $bodycar_discnt_id     ,$bodycar_discnt_thirdparty_id      ,$bodycar_discnt_thirdparty_company_id        ,  '$bodycar_coverage'   ,    $bodycar_time_id   ,       $bodycar_not_used     ,  $body_car_import   , $bodycar_usefor_id   , $bodycar_cash    ) ";
                    $result2=$this->B_db->run_query_put($query2);
                }else if ($fieldinsurance=="responsdoctorsins")
                {
                    $responsdoctors_para_medic=$this->post('responsdoctors_para_medic',0) ;
                    $responsdoctors_medicspecialty_id=$this->post('responsdoctors_medicspecialty_id',0) ;
                    $responsdoctors_paramedicspecialty_id=$this->post('responsdoctors_paramedicspecialty_id',0) ;
                    $responsdoctors_damage_id=$this->post('responsdoctors_damage_id',0) ;
                    $responsdoctors_resident=$this->post('responsdoctors_resident',0) ;
                    $responsdoctors_students=$this->post('responsdoctors_students',0) ;
                    $responsdoctors_incrementalcoverage=$this->post('responsdoctors_incrementalcoverage',0) ;
                    $query2="INSERT INTO request_responsdoctors_tb( request_responsdoctors_requset_id, request_responsdoctors_para_medic, request_responsdoctors_medicspecialty_id, request_responsdoctors_paramedicspecialty_id, request_responsdoctors_damage_id, request_responsdoctors_resident, request_responsdoctors_students, request_responsdoctors_incrementalcoverage) VALUES 
                                                 ( $request_id             ,            '$responsdoctors_para_medic',  $responsdoctors_medicspecialty_id,         $responsdoctors_paramedicspecialty_id        , $responsdoctors_damage_id            , '$responsdoctors_resident'  ,   '$responsdoctors_students'     ,'$responsdoctors_incrementalcoverage'  ) ";
                    $result2=$this->B_db->run_query_put($query2);
                }else if ($fieldinsurance=="travelins")
                {
                    $travel_oneormore=$this->post('travel_oneormore') ;
                    $travel_plan_id=$this->post('travel_plan_id') ;
                    $travel_destination_id=$this->post('travel_destination_id') ;
                    $travel_time_id=$this->post('travel_time_id') ;
                    $travel_coverage_id=$this->post('travel_coverage_id') ;
                    $travel_helper_id=$this->post('travel_helper_id') ;
                    $travel_pasenger0_12=$this->post('travel_pasenger0_12') ;
                    $travel_pasenger13_65=$this->post('travel_pasenger13_65') ;
                    $travel_pasenger66_70=$this->post('travel_pasenger66_70') ;
                    $travel_pasenger71_75=$this->post('travel_pasenger71_75') ;
                    $travel_pasenger76_80=$this->post('travel_pasenger76_80') ;
                    $travel_pasenger81_85=$this->post('travel_pasenger81_85') ;
                    $query2="INSERT INTO request_travel_tb( request_travel_requset_id, request_travel_oneormore, request_travel_plan_id, request_travel_destination_id, request_travel_time_id, request_travel_coverage_id, request_travel_helper_id, request_travel_pasenger0_12, request_travel_pasenger13_65, request_travel_pasenger66_70, request_travel_pasenger71_75, request_travel_pasenger76_80, request_travel_pasenger81_85) VALUES
                                    (  $request_id              , '$travel_oneormore'       , $travel_plan_id      , $travel_destination_id           , $travel_time_id    , $travel_coverage_id      , $travel_helper_id      ,     $travel_pasenger0_12     ,    $travel_pasenger13_65    ,     $travel_pasenger66_70   ,  $travel_pasenger71_75      , $travel_pasenger76_80     ,           $travel_pasenger81_85)";
                    $result2=$this->B_db->run_query_put($query2);
                }
                if($result){
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('request_id'=>$request_id)
                    ,'desc'=>'درخواست اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    echo json_encode(array('result'=>"error"
                    ,"data"=>$request_id
                    ,'desc'=>'درخواست اضافه نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            } else
                if ($command=="changerequestoffline"){
                $request_price_app=$this->post('request_price_app') ;
                $request_company_id=$this->post('request_company_id') ;
                $request_jsonpricing_id=$this->post('request_jsonpricing_id') ;
                $request_id=$this->post('request_id') ;
                $request_organ=$this->post('request_organ',0) ;
                $query="UPDATE request_tb SET request_last_state_id=0,request_company_id=$request_company_id,request_price_app=$request_price_app,request_jsonpricing_id=$request_jsonpricing_id  WHERE request_id=$request_id";
                $result = $this->B_db->run_query_put($query);


                $query1="INSERT INTO state_request_tb(staterequest_request_id, staterequest_state_id, staterequest_timestamp, staterequest_desc          ) VALUES
                                    (".$request_id."        ,      0               ,  now()                 ,' انتخاب قیمت درخواست') ";
                $result1=$this->B_db->run_query_put($query1);
                if($request_organ!=0)
                {
                    $contract_id=$this->post('contract_id') ;
                    $query11="INSERT INTO organ_request_tb
                        (organ_request_request_id, organ_request_contract_id)
                    VALUES($request_id, $contract_id); ";
                    $result11=$this->B_db->run_query_put($query11);
                }
                if($result){
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('request_id'=>$request_id)
                    ,'desc'=>'درخواست اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    echo json_encode(array('result'=>"error"
                    ,"data"=>$request_id
                    ,'desc'=>'درخواست اضافه نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }
            else if($command=="set_step1"){
                $request_id=$this->post('request_id') ;
                $request_partner=$this->post('request_partner',0) ;
                $request_adderss_id=$this->post('request_adderss_id') ;
                $request_addressofinsured_id=$this->post('request_addressofinsured_id') ;
                $request_reagent_mobile=$this->post('request_reagent_mobile') ;
                $request_reagent_mobile_refralcode="";
                $user_refferal_name=$this->post('user_refferal_name') ;
				if($user_refferal_name!= '' and $request_reagent_mobile==''){
					$query="SELECT user_mobile,user_referral_title FROM user_tb,user_referral_tb WHERE user_id=user_referral_user_id AND    user_refferal_name='$user_refferal_name'";
					$result=$this->B_db->run_query($query);
                    if(!empty($result)){
                        $request_reagent_mobile = $result[0]['user_mobile'];
                    $request_reagent_mobile_refralcode=$result[0]['user_referral_title'];
                    }
				}else{

                    $request_reagent_mobile_refralcode=$request_reagent_mobile;

                }
if($request_reagent_mobile!=''){
    $query="SELECT marketer_leader_mobile FROM user_tb,usermarketer_tb WHERE marketer_user_id=user_id AND user_mobile='$request_reagent_mobile'";
    $result=$this->B_db->run_query($query);
    if(!empty($result))
        $request_leader_mobile = $result[0]['marketer_leader_mobile'];
}
                
                $query="UPDATE request_tb SET request_partner=$request_partner, request_adderss_id=$request_adderss_id,request_leader_mobile='$request_leader_mobile',request_addressofinsured_id=$request_addressofinsured_id ,request_reagent_mobile='$request_reagent_mobile',request_last_state_id=1 ,request_reagent_mobile_refralcode='$request_reagent_mobile_refralcode' WHERE request_id=$request_id";
                $result=$this->B_db->run_query_put($query);
                $query="select * from state_request_tb where staterequest_state_id=1 AND staterequest_request_id=".$request_id."";
                $result=$this->B_db->run_query($query);
                $num=count($result[0]);
                $staterequest_id=0;
                if ($num==0)
                {
                    $query1="INSERT INTO state_request_tb(staterequest_request_id, staterequest_state_id, staterequest_timestamp, staterequest_desc          ) VALUES
                                    (".$request_id."        ,      1               ,  now()                 ,'درخواست توسط کابر ثبت شد') ";
                    $staterequest_id=$this->B_db->run_query_put($query1);
                }else{
                    $state_request=$result[0];
                    $query1="UPDATE state_request_tb SET staterequest_timestamp=now()  WHERE staterequest_request_id=".$state_request['staterequest_request_id'];
                    $result1=$this->B_db->run_query_put($query1);
                }
                if($result1==1){

                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('staterequest_id'=>$staterequest_id)
                    ,'desc'=>'درخواست تغییر یافت'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    die;
                }else{
                    echo json_encode(array('result'=>"error"
                    ,"data"=>""
                    ,'desc'=>'درخواست تغییر نیافت'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }
            else if($command=="save_img_request"){
                $request_id=$this->post('request_id') ;
                $image_code=$this->post('image_code') ;
                $result1=$this->B_db->get_image_whitoururl($image_code);

                $image=$result1[0];
                $query="select * from request_img_tb where request_img_request_id=$request_id AND request_img_image_code='".$image['image_id']."'";
                $result=$this->B_db->run_query($query);
                $num=count($result[0]);
                if ($num==0)
                {
                    $query="INSERT INTO request_img_tb(request_img_request_id, request_img_image_code)  VALUES
                                    ($request_id,". $image['image_id'].")";
                    $request_img_id=$this->B_db->run_query_put($query);
                    if($request_img_id){
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>array('request_img_id'=>$request_img_id)
                        ,'desc'=>'عکس  به درخواست اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        echo json_encode(array('result'=>"error"
                        ,"data"=>""
                        ,'desc'=>'عکس  به درخواست اضافه نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                }else{
                    echo json_encode(array('result'=>"error"
                    ,"data"=>""
                    ,'desc'=>'عکس برای این درخواست تکراری است '),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }
            else  if ($command=="get_offlinerequestid"){
                $fieldinsurance=$this->post('fieldinsurance') ;
                $request_organ=$this->post('request_organ',0) ;
                $request_description=$this->post('request_description','') ;
                $query="INSERT INTO request_tb(request_user_id, request_fieldinsurance, request_last_state_id,request_organ,request_description,request_fieldinsurance_id) VALUES
                                              (".$usertoken[1].",'$fieldinsurance'  ,13,$request_organ,'$request_description',$fieldinsurance_id) ";
                $result=$this->B_db->run_query_put($query);
                $request_id=$this->db->insert_id();

                $query1="INSERT INTO state_request_tb(staterequest_request_id, staterequest_state_id, staterequest_timestamp, staterequest_desc          ) VALUES
                                    (".$request_id."        ,      13               ,  now()                 ,'استعلام قیمت') ";
                $staterequest_id=$this->B_db->run_query_put($query1);
                if($result){

                    $query1="SELECT user_mobile FROM user_tb WHERE user_id=".$usertoken[1]."";
                    $result1=$this->B_db->run_query($query1);
                        $user_mobile = $result1[0]['user_mobile'];

                    $query1="SELECT fieldinsurance_fa FROM fieldinsurance_tb WHERE fieldinsurance='".$fieldinsurance."'";
                    $result1=$this->B_db->run_query($query1);
                    $fieldinsurance_fa = $result1[0]['fieldinsurance_fa'];
                    if($user_mobile!='09187639079') {
                        requestoffline_send_sms($user_mobile, $fieldinsurance_fa, $request_id);
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('request_id'=>$request_id)
                    ,'desc'=>'درخواست اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    echo json_encode(array('result'=>"error"
                    ,"data"=>$request_id
                    ,'desc'=>'درخواست اضافه نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }
        }else{
            echo json_encode(array('result'=>$usertoken[0]
            ,"data"=>$usertoken[1]
            ,'desc'=>$usertoken[2]));

        }
    }
}