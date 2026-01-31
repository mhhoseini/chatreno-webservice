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
 * @link            https://aref24.com
 */
class Price_organthirdparty extends REST_Controller {

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
        if (isset($this->input->request_headers()['Authorization'])) $this->user_token_str = $this->input->request_headers()['Authorization'];
        $this->load->model('B_user');
        $this->load->model('B_db');
        $this->load->model('B_agent');
        $this->load->helper('my_helper');
        $this->load->model('B_organ');
        $command = $this->post("command");

        if ($command=="get_price")
        {

            $usertoken=checkusertoken($this->user_token_str);
            if($usertoken[0]=='ok')
            {

                $car_id=$this->post('car_id');
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
                $transition=$this->post('transition') ;

                $thirdparty_damage_driver_id=$this->post('thirdparty_damage_driver_id',1) ;
                $thirdparty_damage_human_id=$this->post('thirdparty_damage_human_id',1) ;
                $thirdparty_damage_financial_id=$this->post('thirdparty_damage_financial_id',1) ;
                if($transition=='true')
                {
                    $thirdparty_discnt_thirdparty_id=1;
                    $thirdparty_discnt_driver_id=1;
                }
                $fieldinsurance='thirdpartyins';
                if($thirdparty_lastcompany_id){
                    $query4="select * from company_tb where company_id=$thirdparty_lastcompany_id";
                    $result4=$this->B_db->run_query($query4);
                    $thirdparty_lastcompany=$result4[0];
                }


                $query5="select * from thirdparty_discnt_thirdparty_tb where thirdparty_discnt_thirdparty_id=$thirdparty_discnt_thirdparty_id";
                $result5=$this->B_db->run_query($query5);
                $thirdparty_discnt_thirdparty=$result5[0];

                $query6="select * from thirdparty_discnt_driver_tb where thirdparty_discnt_driver_id=$thirdparty_discnt_driver_id";
                $result6=$this->B_db->run_query($query6);
                $thirdparty_discnt_driver=$result6[0];

                $query7="select * from thirdparty_damage_human_tb where thirdparty_damage_human_id=$thirdparty_damage_human_id";
                $result7=$this->B_db->run_query($query7);
                $thirdparty_damage_human=$result7[0];

                $query8="select * from thirdparty_damage_financial_tb where thirdparty_damage_financial_id=$thirdparty_damage_financial_id";
                $result8=$this->B_db->run_query($query8);
                $thirdparty_damage_financial=$result8[0];

                $query9="select * from thirdparty_damage_driver_tb  where thirdparty_damage_driver_id=$thirdparty_damage_driver_id";
                $result9=$this->B_db->run_query($query9);
                $thirdparty_damage_driver=$result9[0];

                $query10="select * from thirdparty_usefor_tb  where thirdparty_usefor_id=$thirdparty_usefor_id";
                $result10=$this->B_db->run_query($query10);
                $thirdparty_usefor=$result10[0];

                $query11="select * from thirdparty_coverage_tb  where thirdparty_coverage_id=$thirdparty_coverage_id";
                $result11=$this->B_db->run_query($query11);
                $thirdparty_coverage=$result11[0];
//**************************************************************************************************************




                $query="SELECT * FROM
chatreno_db.organ_contract_tb a 
JOIN
(SELECT DISTINCT organ_tb.* FROM chatreno_db.organ_tb  join chatreno_db.organ_user_tb     on     organ_user_organ_id    = organ_id   where organ_user_user_id=".$usertoken[1]." AND organ_deactive=0)
                                              b   ON a.organ_contract_organ_id=b.organ_id
             join chatreno_db.fieldinsurance_tb c     on     c.fieldinsurance    LIKE '%$fieldinsurance%'  AND  c.fieldinsurance_id=a.organ_contract_fieldinsuranc_id
             join chatreno_db.company_tb d     on     d.company_id    = a.organ_contract_company_id
             join chatreno_db.fieldcompany_tb e     on     e.fieldcompany_company_id    = a.organ_contract_company_id and e.fieldcompany_fieldinsurance_id= c.fieldinsurance_id
              WHERE a.organ_contract_deactive=0 AND a.organ_contract_date_end>now() and a.organ_contract_date_start<now() AND c.fieldinsurance_deactive=0 AND d.company_deactive=0
            AND c.fieldinsurance_deactive=0";

                $result = $this->B_db->run_query($query);
                $output =array();
                $price=0;
                foreach($result as $row)
                {
                    $record=array();

                    $record['company_id']=$row['company_id'];
                    //*******************************************************************************************
                    $price=0;
                    $query3="select * from thirdparty_price_tb,car_tb,extrafinancial_tb,thirdpartyprice_time_tb,thirdpartyprice_usefor_tb,carmode_tb where  thirdparty_price_cargroup_id=car_group_id
                  AND 	thirdpartyprice_time_thirdparty_price_id=thirdparty_price_id
                  AND 	thirdpartyprice_usefor_thirdparty_price_id=thirdparty_price_id
                  AND 	extrafinancial_thirdparty_price_id=thirdparty_price_id
                  AND 	extrafinancial_thirdparty_coverage_id=$thirdparty_coverage_id
                  AND 	thirdpartyprice_time_time_id=$thirdparty_time_id
                  AND 	thirdpartyprice_usefor_usefor_id=$thirdparty_usefor_id
                  AND car_id=$car_id
                  AND car_mode_id=carmode_id
                 AND thirdparty_price_deactive=0
                 AND thirdparty_price_fieldcompany_id=".$row['fieldcompany_id']."";
                    $result3=$this->B_db->run_query($query3);

                    if(!empty($result3)){
                        $thirdparty_price=$result3[0];
                        $priceExtraFinancial=0;//حق بیمه مازاد مالی
                        $priceForcedThird=0;// حق بیمه ثالث اجباری
                        $pricePassenger =0;// حق بیمه سرنشیم


                        $percentpenaltyFinancial=$thirdparty_damage_financial['thirdparty_damage_financial_digit'];//خسارت مالی
                        $percentpenaltyPassenger=$thirdparty_damage_driver['thirdparty_damage_driver_digit'];// خسارت سرنشین
                        $percentpenaltyForcedThird=$thirdparty_damage_human['thirdparty_damage_human_digit'];//درخسارت جانی

                        $numberOfDays=intval((time()-strtotime($thirdparty_last_date_end))/86400);
                        $diffOfDays=intval((time()-strtotime($thirdparty_last_date_sart))/86400)-intval((time()-strtotime($thirdparty_last_date_end))/86400);
                        if($numberOfDays<0){$numberOfDays=0;}


                        if($percentpenaltyFinancial>0||$percentpenaltyForcedThird>0||$diffOfDays<350){
                            $percentdiscThirdparty=$thirdparty_discnt_thirdparty['thirdparty_discnt_thirdparty_digt'];//درصد مالی
                        }else{
                            $percentdiscThirdparty=$thirdparty_discnt_thirdparty['thirdparty_discnt_thirdparty_digt']+5;//درصد مالی
                        }
                        if($percentpenaltyPassenger>0||$diffOfDays<350){
                            $percentdiscPassenger=$thirdparty_discnt_driver['thirdparty_discnt_driver_digt'];// درصد سرنشین
                        }else{
                            $percentdiscPassenger=$thirdparty_discnt_driver['thirdparty_discnt_driver_digt']+5;// درصد سرنشین
                        }
                        if($percentdiscThirdparty>70){$percentdiscThirdparty=70;}
                        if($percentdiscPassenger>70){$percentdiscPassenger=70;}



                        $priceExtraFinancial=$thirdparty_price['extrafinancial_price'];//حق بیمه مازاد مالی
                        $priceForcedThird=$thirdparty_price['thirdparty_price_forcedthird'];// حق بیمه ثالث اجباری
                        $pricePassenger =$thirdparty_price['thirdparty_price_passenger'];// حق بیمه سرنشیم

                        $oldpriceForcedThird=0;
                        $oldpriceExtraFinancial=0;
                        $oldpricePassenger=0;
                        $YearOfconstruction=$thirdparty_yearofcons_id;
                        if($YearOfconstruction>15){
                            $percenty=($YearOfconstruction-15)*0.02;
                            if($percenty<0.2){
                                $oldpriceForcedThird=$priceForcedThird*($percenty);
                                $oldpriceExtraFinancial= $priceExtraFinancial*($percenty);
                                $oldpricePassenger=$pricePassenger*($percenty);
                            }else{
                                $oldpriceForcedThird=$priceForcedThird*0.2;
                                $oldpriceExtraFinancial=$priceExtraFinancial*0.2;
                                $oldpricePassenger=$pricePassenger*0.2;
                            }
                        }

                        $priceExtraFinancial=$priceExtraFinancial+($priceExtraFinancial*$thirdparty_price['thirdpartyprice_usefor_percent']/100);
                        $pricePassenger=$pricePassenger+($pricePassenger*$thirdparty_price['thirdpartyprice_usefor_percent']/100);
                        $priceForcedThird=$priceForcedThird+($priceForcedThird*$thirdparty_price['thirdpartyprice_usefor_percent']/100);

                        $priceExtraFinancial+=$oldpriceExtraFinancial ;
                        $pricePassenger+=$oldpricePassenger;
                        $priceForcedThird+=$oldpriceForcedThird;

                        $percentpenalty=0;
                        if($percentpenaltyFinancial>$percentpenaltyForcedThird)
                        {
                            $percentpenalty=$percentpenaltyFinancial;
                        }else{
                            $percentpenalty=$percentpenaltyForcedThird;
                        }
                        $priceExtraFinancial= $priceExtraFinancial-($priceExtraFinancial*($percentdiscThirdparty-$percentpenalty)/100);
                        $pricePassenger=$pricePassenger-($pricePassenger*($percentdiscPassenger-$percentpenaltyPassenger)/100);
                        $priceForcedThird=$priceForcedThird-($priceForcedThird*($percentdiscThirdparty-$percentpenalty)/100);

                        $discExtaraPercent=0;
                        if($thirdparty_price['extrafinancial_extrafinancial_calmode_id']==1||$thirdparty_price['extrafinancial_extrafinancial_calmode_id']=='1')
                        {
                            $discExtaraPercent=$thirdparty_price['extrafinancial_percent']-($percentdiscThirdparty*10);
                            if( $discExtaraPercent<0){
                                $discExtaraPercent=0;
                            }

                        }else if($thirdparty_price['extrafinancial_extrafinancial_calmode_id']==0||$thirdparty_price['extrafinancial_extrafinancial_calmode_id']=='0')
                        {
                            $discExtaraPercent=$thirdparty_price['extrafinancial_percent'];
                        }
                        $discExtraFinancial=$priceExtraFinancial*$discExtaraPercent/1000;


                        $pricefine=($priceForcedThird/365)*$numberOfDays;// دیر کرد و صندوق

                        $price=$priceExtraFinancial+$priceForcedThird+$pricePassenger-$discExtraFinancial;

                        $price=$price*$thirdparty_price['thirdpartyprice_time_percent']/100;

                        $price=($price*109)/100+$pricefine;
                        // $price=($price*100)/100+$pricefine;

                    }

                    $price_disc=0;
                    $discount1=0;
                    $discount2=0;
                    //*******************************************************************************************
                    if($row['organ_contract_discount_amount']>0||$row['organ_contract_discount_percent']>0){
                        $discount1=$row['organ_contract_discount_amount'];
                        $discount2=$price*$row['organ_contract_discount_percent']/100;
                        if($discount2>$row['organ_contract_discount_max_amount']){
                            $discount2=$row['organ_contract_discount_max_amount'];
                        }
                        $price_disc=$price-$discount1-$discount2;
                        $record['qroupdiscount_amount']=$discount1+$discount2;
                        $record['qroupdiscount_id']=$row['organ_contract_id'];
                        $record['qroupdiscount_desc']='تخفیف گروهی';

                    }
//*******************************************************************************************


                    if($price_disc>0){
                        $record['price']=intval($price_disc);
                        $record['price_disc']=intval($price);
                    }else{
                        $record['price']=intval($price);
                    }
                    $thirdparty_yearofcons_g = "";
                    $thirdparty_yearofcons_j = "";
                    //*****************************************************************************************
                    $request_description='بیمه نامه در رشته '.$row['fieldinsurance_fa']. ' برای اتوموبیل '. $thirdparty_price['carmode_name'] .' '. $thirdparty_price['car_name'] . ' با سال ساخت شمسی '.$thirdparty_yearofcons_j
                        . ' و سال ساخت میلادی '.$thirdparty_yearofcons_g. ' تاریخ شروع بیمه نامه سال قبل '.$thirdparty_last_date_sart. 'و تاریخ پایان بیمه نامه سال قبل  '.$thirdparty_last_date_end
                        . ' و شرکت قبلی '.$thirdparty_lastcompany['company_name']. 'و  عدم خسارت ثالث '.$thirdparty_discnt_thirdparty['thirdparty_discnt_thirdparty_name']. 'و  عدم خسارت راننده'.$thirdparty_discnt_driver['thirdparty_discnt_driver_name']
                    ;
                    if($thirdparty_damage_human_id!=0){$request_description=$request_description.' تعداد خسارت جانی '.$thirdparty_damage_human['thirdparty_damage_human_name'];}
                    if($thirdparty_damage_financial_id!=0){$request_description=$request_description.' تعداد خسارت جانی '.$thirdparty_damage_financial['thirdparty_damage_financial_name']; }
                    if($thirdparty_damage_driver_id!=0){$request_description=$request_description.' تعداد خسارت جانی '.$thirdparty_damage_driver['thirdparty_damage_driver_name'];}
                    $request_description=$request_description.' مورد استفاده '.$thirdparty_usefor['thirdparty_usefor_desc'];
                    $request_description=$request_description.' و پوشش انتخابی '.$thirdparty_coverage['thirdparty_coverage_desc'];


                    if($row['organ_logo']!=''){
                        $result1= $this->B_db->get_image($row['organ_logo']);
                        $image=$result1[0];
                        if($image['image_url']){$imageurl=$image['image_url'];}
                        $record['organ_logo']=$imageurl;
                    }
                    $record['organ_name']=$row['organ_name'];
                    $record['organ_contract_id']=$row['organ_contract_id'];
                    $record['tip']=$row['organ_contract_num'];
                    $record['request_description']=$request_description;
                    $record['company_name']=$row['company_name'];
                    $record['company_levelof_prosperity']=$row['company_levelof_prosperity'];
                    $record['company_num_branchesdamages']=$row['company_num_branchesdamages'];
                    $record['company_customer_satisfaction']=$row['company_customer_satisfaction'];
                    $record['company_timeanswer_complaints']=$row['company_timeanswer_complaints'];
                    $record['company_description']=$row['company_description'];
                    $record['company_logo_url']=IMGADD.$row['company_logo_url'];

                    $record['fieldcompany_desc']=$row['fieldcompany_desc'];
                    $record['fieldcompany_link']=$row['fieldcompany_link'];
                    //*****************************************************************************************
                    //$arrinstalmentpermount= $this->B_organ->get_instalmentnopass_permonth($usertoken[1],$row['organ_id']);
                    $arrinstalmentpermount= $this->B_organ->get_instalmentnopass_permonth($usertoken[1],$row['organ_id']);

                    $count_query="select a.organ_user_commitment_amount
                    FROM chatreno_db.organ_user_tb a
                    INNER JOIN (
                       SELECT *, MAX(organ_user_confirm_id) AS max_confirm_id
                    FROM chatreno_db.organ_user_tb
                    WHERE organ_user_organ_id=".$row['organ_id']." and organ_user_confirm_id<=100000000000 
                    GROUP by organ_user_user_id
                    ) b ON a.organ_user_confirm_id = b.max_confirm_id AND a.organ_user_user_id = b.organ_user_user_id AND a.organ_user_organ_id=".$row['organ_id']."
                    INNER JOIN chatreno_db.user_tb c
                    ON c.user_id=a.organ_user_user_id AND a.organ_user_user_id=".$usertoken[1]."
                    INNER JOIN chatreno_db.organ_confirm_tb d
                    ON a.organ_user_confirm_id=d.organ_confirm_id";
                    $organ_user  = $this->B_db->run_query($count_query);
                    $organ_user_commitment_amount=intval($organ_user[0]['organ_user_commitment_amount']);
                    $request_accept=1;
//******************************************************************************************************************************************
                    $query3="select * from instalment_conditions_tb,instalment_mode_tb where instalment_mode_tb.instalment_mode_mode_id=instalment_conditions_tb.instalment_conditions_mode_id AND instalment_condition_contract_id=".$row['organ_contract_id']." ORDER BY instalment_conditions_date DESC";

                    $result3 = $this->B_db->run_query($query3);
                    $num=count($result3[0]);
                    if($num>0) {
                        $record['instalment_conditions_desc']="اقساط";

                        $output3 =array();
                        if($price_disc==0){$price_instalment=$price;}else{$price_instalment=$price_disc;}
                        $tprice_instalment=$price_instalment;
                        foreach($result3 as $row3)
                        {
                            $instalment_conditions_amount=0;
                            if($row['organ_contract_instalment_round_id']==0){
                                if($row3['instalment_conditions_date']!=0){
                                    $instalment_conditions_amount=round($price_instalment*$row3['instalment_conditions_percent']/100, -5, PHP_ROUND_HALF_DOWN);
                                    $tprice_instalment-=round($price_instalment*$row3['instalment_conditions_percent']/100, -5, PHP_ROUND_HALF_DOWN);
                                }else{
                                    $instalment_conditions_amount=intval($tprice_instalment);
                                }
                                $record3['instalment_conditions_amount']=$instalment_conditions_amount;

                            }else{
                                if($row3['instalment_conditions_date']!=0){
                                    $instalment_conditions_amount=round($price_instalment*$row3['instalment_conditions_percent']/100, 0, PHP_ROUND_HALF_DOWN);
                                    $tprice_instalment-=round($price_instalment*$row3['instalment_conditions_percent']/100, 0, PHP_ROUND_HALF_DOWN);
                                }else{
                                    $instalment_conditions_amount=intval($tprice_instalment);
                                }
                                $record3['instalment_conditions_amount']=$instalment_conditions_amount;
                            }
                            $record3['instalmentpermount']=$arrinstalmentpermount[intval($row3['instalment_conditions_date'])];
                            $record3['suminstalmentpermount']=$arrinstalmentpermount[intval($row3['instalment_conditions_date'])]+$instalment_conditions_amount;
                            $record3['organ_user_commitment_amount']=$organ_user_commitment_amount;
                            $record3['organ_user_commitment_amount_diff']=$organ_user_commitment_amount-($arrinstalmentpermount[intval($row3['instalment_conditions_date'])]+$instalment_conditions_amount);
                            $record3['instalment_conditions_id']=$row3['instalment_conditions_id'];
                            $record3['instalment_conditions_percent']=$row3['instalment_conditions_percent'];
                            $record3['instalment_conditions_desc']=$row3['instalment_conditions_desc'];
                            $record3['instalment_conditions_date']=$row3['instalment_conditions_date'];
                            $record3['instalment_conditions_mode_id']=$row3['instalment_conditions_mode_id'];
                            $record3['instalment_mode_mode_name']=$row3['instalment_mode_mode_name'];
if($arrinstalmentpermount[intval($row3['instalment_conditions_date'])]+$instalment_conditions_amount<$organ_user_commitment_amount||$row3['instalment_conditions_mode_id']!="2"){
    $record3['instalment_accept']=1;
}else{
    $record3['instalment_accept']=0;
    $request_accept=0;
}
                            $output3[]=$record3;
                        }

                        $record['instalment_conditions']=$output3;
                    }
                    $record['request_accept']=$request_accept;

                    $query1="INSERT INTO jsonpricing_tb( jsonpricing_text, jsonpricing_date,	jsonpricing_fieldinsurance) VALUES
                                      ( '".json_encode( $record, JSON_UNESCAPED_UNICODE )."'      ,   now()         , '$fieldinsurance') ";
                    $result1=$this->B_db->run_query_put($query1);
                    $jsonpricing_id=$this->db->insert_id();
                    $record['jsonpricing_id']=$jsonpricing_id;
                    //*****************************************************************************************
                    if($price>0){
                        $output[]=$record;
                    }
                }
                echo json_encode(array('result'=>"ok"
                ,"data"=>$output
                ,'desc'=>'قیمت بیمه ثالث با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);


//****************************************************************************************************************

            }
        }
    }
}
