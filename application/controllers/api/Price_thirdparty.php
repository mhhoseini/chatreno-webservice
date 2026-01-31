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
class Price_thirdparty extends REST_Controller {

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
		
        if ($command=="get_price")
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
            $carmode_id=$this->post('carmode_id') ;
            $thirdparty_mode_id=$this->post('thirdparty_mode_id',1) ;
            $transition=$this->post('transition') ;
$thirdparty_damage_driver_id=$this->post('thirdparty_damage_driver_id',1) ;
							$thirdparty_damage_human_id=$this->post('thirdparty_damage_human_id',1) ;
							$thirdparty_damage_financial_id=$this->post('thirdparty_damage_financial_id',1) ;
            $thirdparty_yadak = $this->post('thirdparty_yadak');

            $query13="select * from thirdparty_mode_tb where thirdparty_mode_id=$thirdparty_mode_id";
            $result13=$this->B_db->run_query($query13);
            $thirdparty_mode=$result13[0];

		if($transition=='true')
        {
            $thirdparty_discnt_thirdparty_id=1;
             $thirdparty_discnt_driver_id=1;
        }
            $fieldinsurance=$thirdparty_mode['thirdparty_mode_fieldinsurance'];
            $thirdparty_lastcompany_name="";
			if($thirdparty_lastcompany_id&&$thirdparty_lastcompany_id!=0&&$thirdparty_lastcompany_id!='0'){
				$query4="select * from company_tb where company_id=$thirdparty_lastcompany_id";
            	$result4=$this->B_db->run_query($query4);
            	$thirdparty_lastcompany=$result4[0];
                $thirdparty_lastcompany_name=$thirdparty_lastcompany['company_name'];
			}else{
                $thirdparty_lastcompany_name='بیمه نامه ندارد';
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
 $temp='';

            $query="select * from company_tb,fieldinsurance_tb,fieldcompany_tb where
 company_deactive=0
 AND fieldinsurance='$fieldinsurance'
 AND fieldinsurance_deactive=0
 AND fieldcompany_company_id=company_id
 AND fieldcompany_fieldinsurance_id=fieldinsurance_id
 AND fieldcompany_deactive=0
  ORDER BY fieldcompany_priority DESC ";

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
                  AND 	thirdparty_price_mode_id=$thirdparty_mode_id
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
                    if($numberOfDays>365){$numberOfDays=365;}


                    if($percentpenaltyFinancial>0||$percentpenaltyForcedThird>0||$diffOfDays<350){
                        $percentdiscThirdparty=$thirdparty_discnt_thirdparty['thirdparty_discnt_thirdparty_digt'];//درصد مالی
                    }else{
                        $percentdiscThirdparty=$thirdparty_discnt_thirdparty['thirdparty_discnt_thirdparty_digt']+5;//درصد مالی
                    }
                    if($percentdiscThirdparty<0){
                        $percentdiscThirdparty=0;
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

                    if ($thirdparty_yadak == 'true') {
                        $pricePassenger=$pricePassenger+$pricePassenger*intval($thirdparty_price['thirdparty_price_yadak'])/100;//حق بیمه مازاد مالی
                        $priceForcedThird=$priceForcedThird+$priceForcedThird*intval($thirdparty_price['thirdparty_price_yadak'])/100;//حق بیمه مازاد مالی
                        $priceExtraFinancial=$priceExtraFinancial+$priceExtraFinancial*intval($thirdparty_price['thirdparty_price_yadak'])/100;//حق بیمه مازاد مالی

                    }

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
                    $priceEndExtraFinancial= $priceExtraFinancial-($priceExtraFinancial*($percentdiscThirdparty-$percentpenalty)/100);
                    $pricePassenger=$pricePassenger-($pricePassenger*($percentdiscPassenger-$percentpenaltyPassenger)/100);
                    $priceForcedThird=$priceForcedThird-($priceForcedThird*($percentdiscThirdparty-$percentpenalty)/100);

                    $discExtraFinancial=0;
                    if(intval($thirdparty_price['extrafinancial_extrafinancial_calmode_id'])==1)
                    {
                        $discExtaraPercent=$thirdparty_price['extrafinancial_percent']-($percentdiscThirdparty*10);
                        if( $discExtaraPercent<0){
                            $discExtaraPercent=0;
                        }
                        $discExtraFinancial=$priceEndExtraFinancial*$discExtaraPercent/1000;

                    }else if(intval($thirdparty_price['extrafinancial_extrafinancial_calmode_id'])==0)
                    {
                        $discExtaraPercent=$thirdparty_price['extrafinancial_percent'];
                        $discExtraFinancial=$priceEndExtraFinancial*$discExtaraPercent/1000;
                    }else if(intval($thirdparty_price['extrafinancial_extrafinancial_calmode_id'])==2)
                    {
                        $discExtaraPercent=$thirdparty_price['extrafinancial_percent'];
                        $discExtraFinancial=(($thirdparty_price['extrafinancial_price']-($priceExtraFinancial*($percentdiscThirdparty-$percentpenalty)/100))*$discExtaraPercent)/1000;
                    }
                    $pricefine=0;
                   // if($thirdparty_price['thirdpartyprice_time_time_id']!='1'&&$thirdparty_price['thirdpartyprice_time_time_id']!=1){
                    $pricefine=($priceForcedThird/365)*$numberOfDays;// دیر کرد و صندوق
                  //  }

                    $price=$priceEndExtraFinancial+$priceForcedThird+$pricePassenger-$discExtraFinancial;

                    $price=$price*$thirdparty_price['thirdpartyprice_time_percent']/100;

                    $price=$price-($price*$thirdparty_price['thirdparty_price_disc']/100);

                    $price=($price*109)/100+$pricefine;
                    // $price=($price*100)/100+$pricefine;

                }
                $record['desc']=$thirdparty_price['thirdparty_price_disc'];

                $price_disc=0;  
                //*******************************************************************************************

                //*******************************************************************************************
                $query1="select * from managdiscount_tb,fieldinsurance_tb where managdiscount_company_id=".$row['company_id']." AND managdiscount_fieldinsurance_id=fieldinsurance_id AND fieldinsurance='$fieldinsurance'
		 AND (managdiscount_date_start='' OR managdiscount_date_start > now()) AND  (managdiscount_date_end='' OR managdiscount_date_end< now())
		 AND managdiscount_max_all>(SELECT COALESCE(SUM(managdiscount_use_amount),0) FROM managdiscount_use_tb WHERE managdiscount_mngdiscnt_id=managdiscount_tb.managdiscount_id)
		 AND managdiscount_deactive=0";
                $result1=$this->B_db->run_query($query1);

                $temp.=$query1.' ----- ';


                if(!empty($result1)){
					$managdiscount=$result1[0];
                if($managdiscount['managdiscounts_mode']=='fix'){
                    $price_disc=intval($price-$managdiscount['managdiscount_amount']);
                    $record['price_disc']=$price_disc;
                    $record['managdiscount_desc']=$managdiscount['managdiscount_desc'];
                    $record['managdiscount_id']=$managdiscount['managdiscount_id'];
                    $record['managdiscount_amount']=$managdiscount['managdiscount_amount'];


                }else if($managdiscount['managdiscounts_mode']=='percent'){
                    $discount=$price*$managdiscount['managdiscount_amount']/100;
                    if($discount>$managdiscount['managdiscount_max_forone']){
                        $discount=$managdiscount['managdiscount_max_forone'];
                    }
                    $price_disc=$price-$discount;
                    $record['managdiscount_desc']=$managdiscount['managdiscount_desc'];
                    $record['managdiscount_id']=$managdiscount['managdiscount_id'];
                    $record['managdiscount_amount']=$discount;

                }
				
				}
				
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
                    . ' و شرکت قبلی '.$thirdparty_lastcompany_name. 'و  عدم خسارت ثالث '.$thirdparty_discnt_thirdparty['thirdparty_discnt_thirdparty_name']. 'و  عدم خسارت راننده'.$thirdparty_discnt_driver['thirdparty_discnt_driver_name']
                ;
                if($thirdparty_damage_human_id!=0){$request_description=$request_description.' تعداد خسارت جانی '.$thirdparty_damage_human['thirdparty_damage_human_name'];}
                if($thirdparty_damage_financial_id!=0){$request_description=$request_description.' تعداد خسارت جانی '.$thirdparty_damage_financial['thirdparty_damage_financial_name']; }
                if($thirdparty_damage_driver_id!=0){$request_description=$request_description.' تعداد خسارت جانی '.$thirdparty_damage_driver['thirdparty_damage_driver_name'];}
                $request_description=$request_description.' مورد استفاده '.$thirdparty_usefor['thirdparty_usefor_desc'];
                $request_description=$request_description.' و پوشش انتخابی '.$thirdparty_coverage['thirdparty_coverage_desc'];


                $record['numberOfDays']=$numberOfDays;
                $record['pricefine']=$pricefine;
                $record['tip']=' ';
                $record['priceEndExtraFinancial']=$priceEndExtraFinancial;
                $record['priceForcedThird']=$priceForcedThird;
                $record['pricePassenger']=$pricePassenger;
                $record['discExtraFinancial']=$discExtraFinancial;
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

                $query2="select * from instalment_tb,fieldinsurance_tb where
 instalment_company_id=".$row['company_id']."
 AND instalment_fieldinsurance_id=fieldinsurance_id
 AND fieldinsurance='$fieldinsurance'
 AND instalment_deactive=0
AND (instalment_date_start='' OR instalment_date_start > now()) AND  (instalment_date_end='' OR instalment_date_end< now())
";
                $result2=$this->B_db->run_query($query2);

                if(!empty($result2)){
                    $instalment=$result2[0];
                    $record['instalment_desc']=$instalment['instalment_desc'];
                    $query3="select * from instalment_conditions_tb,instalment_mode_tb where instalment_mode_tb.instalment_mode_mode_id=instalment_conditions_tb.instalment_conditions_mode_id AND instalment_conditions_instalment_id=".$instalment['instalment_id']." ORDER BY instalment_conditions_date DESC";
                    $result3 = $this->B_db->run_query($query3);
                    $output3 =array();
                    if($price_disc==0){$price_instalment=$price;}else{$price_instalment=$price_disc;}
                    $tprice_instalment=$price_instalment;
                    foreach($result3 as $row3)
                    {
                        if ($row3['instalment_conditions_date'] != 0) {
                            if ($instalment['instalment_round_id'] == '1'||$instalment['instalment_round_id'] == 1) {
                                $record3['instalment_conditions_amount'] =intval($price_instalment * $row3['instalment_conditions_percent'] / 100 );
                                $tprice_instalment -= $price_instalment * $row3['instalment_conditions_percent'] / 100;
                            }else {
                                $record3['instalment_conditions_amount'] = round($price_instalment * $row3['instalment_conditions_percent'] / 100, -1*intval($instalment['instalment_numround']), PHP_ROUND_HALF_DOWN);
                                $tprice_instalment -= round($price_instalment * $row3['instalment_conditions_percent'] / 100, -1*intval($instalment['instalment_numround']), PHP_ROUND_HALF_DOWN);
                            }
                        } else {
                            $record3['instalment_conditions_amount'] = intval($tprice_instalment);
                        }
                        $record3['instalment_conditions_id'] = $row3['instalment_conditions_id'];
                        $record3['instalment_conditions_percent'] = $row3['instalment_conditions_percent'];
                        $record3['instalment_conditions_desc'] = $row3['instalment_conditions_desc'];
                        $record3['instalment_conditions_date'] = $row3['instalment_conditions_date'];
                        $record3['instalment_conditions_mode_id'] = $row3['instalment_conditions_mode_id'];

                        $output3[] = $record3;
                    }

                    $record['instalment_conditions']=$output3;

                }
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
            ,'desc'=>'قیمت بیمه ثالث با موفقیت ارسال شد'.$query1),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);


//****************************************************************************************************************

        }

        }
}