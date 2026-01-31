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
class Price_therapy extends REST_Controller {

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

            $therapy_oneormore=$this->post('therapy_oneormore') ;
            $therapy_baseinsurer_id=$this->post('therapy_baseinsurer_id') ;
            $therapy_coverage_id=$this->post('therapy_coverage_id') ;
            $therapy_age0_15=$this->post('therapy_age0_15') ;
            $therapy_age16_50=$this->post('therapy_age16_50') ;
            $therapy_age51_60=$this->post('therapy_age51_60') ;
            $therapy_age61_70=$this->post('therapy_age61_70') ;

$cnt_all_age=$therapy_age0_15+$therapy_age16_50+$therapy_age51_60+$therapy_age61_70;


            $query14="select * from therapy_baseinsurer_tb where therapy_baseinsurer_id=$therapy_baseinsurer_id";
            $result14=$this->B_db->run_query($query14);
            $therapy_baseinsurer=$result14[0];

            $query15="select * from therapy_coverage_tb where therapy_coverage_id=$therapy_coverage_id";
            $result15=$this->B_db->run_query($query15);
            $therapy_coverage=$result15[0];





            $fieldinsurance='therapyins';


//***************************************************************************************************************
$temp='';

            $query="select * from company_tb,fieldinsurance_tb,fieldcompany_tb where
 company_deactive=0
 AND fieldinsurance='$fieldinsurance'
 AND fieldinsurance_deactive=0
 AND fieldcompany_company_id=company_id
 AND fieldcompany_fieldinsurance_id=fieldinsurance_id
 AND fieldcompany_deactive=0
  ORDER BY company_id ASC";

            $result = $this->B_db->run_query($query);
            $output =array();
            $price=0;
            foreach($result as $row)
            {
                $record=array();

                $record['company_id']=$row['company_id'];

                $query3="select * from therapy_price_tb,therapy_coverage_tb,therapy_price_baseinsurer_tb
where  therapy_price_baseinsurer_therapy_price_id=therapy_price_id
    AND therapy_price_baseinsurer_therapy_baseinsurer_id=$therapy_baseinsurer_id
    AND   therapy_price_coverage_id=therapy_coverage_id 
and   therapy_price_fieldcompany_id=".$row['fieldcompany_id'];

$temp=$temp.'--'.$query3;

                $result3=$this->B_db->run_query($query3);
                $num3=count($result3[0]);
                foreach($result3 as $row3)
                {
                    $price=0;
                    //*******************************************************************************************

                    $request_description='بیمه نامه در رشته :'.$row['fieldinsurance_fa'] .'-';
                    if($therapy_oneormore=='true')
                    {$request_description=$request_description.'سفر به یک کشور-';}
                    else
                    {$request_description=$request_description.'سفر به چند کشور-';}

                    //*******************************************************************************************
                    if($therapy_age0_15>0){
                        $query4="select * from therapy_price_age_tb where
  therapy_price_age_therapy_price_id= ".$row3['therapy_price_id']."
AND  therapy_price_age_therapy_age_id=1";
                        $result4=$this->B_db->run_query($query4);
                        $therapy_price_age=$result4[0];
                        $price+=($therapy_age0_15*$therapy_price_age['therapy_price_age_price']);
                        $request_description=$request_description. ' تعداد مسافر 0-12: '.$therapy_age0_15.'-';
                    }

                    if($therapy_age16_50>0){
                        $query5="select * from therapy_price_age_tb where
  therapy_price_age_therapy_price_id= ".$row3['therapy_price_id']."
AND  therapy_price_age_therapy_age_id=2";
                        $result5=$this->B_db->run_query($query5);
                        $therapy_price_age=$result5[0];
                        $price+=($therapy_age16_50*$therapy_price_age['therapy_price_age_price']);
                        $request_description=$request_description. ' تعداد مسافر 13-65: '.$therapy_age16_50.'-';
                    }


                    if($therapy_age51_60>0){
                        $query6="select * from therapy_price_age_tb where
  therapy_price_age_therapy_price_id= ".$row3['therapy_price_id']."
AND  therapy_price_age_therapy_age_id=3";
                        $result6=$this->B_db->run_query($query6);
                        $therapy_price_age=$result6[0];
                        $price+=($therapy_age51_60*$therapy_price_age['therapy_price_age_price']);
                        $request_description=$request_description. ' تعداد مسافر 66-70: '.$therapy_age51_60.'-';
                    }

                    if($therapy_age61_70>0){
                        $query7="select * from therapy_price_age_tb where
  therapy_price_age_therapy_price_id= ".$row3['therapy_price_id']."
AND  therapy_price_age_therapy_age_id=4";
                        $result7=$this->B_db->run_query($query7);
                        $therapy_price_age=$result7[0];
                        $price+=($therapy_age61_70*$therapy_price_age['therapy_price_age_price']);
                        $request_description=$request_description. ' تعداد مسافر 71-75: '.$therapy_age61_70.'-';
                    }



                    $price=$price+($price* $row3['therapy_price_baseinsurer_percent']/100);


                    $therapy_slideold_percent =0;
                    if($therapy_oneormore!='true') {
                        $query80 = "select * from therapy_slideold_tb where
	   therapy_slideold_therapy_price_id=" . $row3['therapy_price_id'];
                        $result80 = $this->B_db->run_query($query80);
                        if (!empty($result80)) {
                            foreach ($result80 as $row80) {
                                if ($cnt_all_age >= $row80['therapy_slideold_min'] && $cnt_all_age < $row80['therapy_slideold_max']) {
                                    $therapy_slideold_percent = $row80['therapy_slideold_percent'];

                                }

                            }
                        }
                        $record['therapy_slideold_percent']=$row80['$therapy_slideold_percent'];

                        $price = $price - ($price * ($therapy_slideold_percent+$row3['therapy_price_disc']) / 100);
                    }else{
                        $price=$price-($price* $row3['therapy_price_disc']/100);

                    }



                    $request_description=$request_description. 'پوشش انتخابی:'.$row3['therapy_coverage_name'].'-';



                    $tip= '';

                    if($therapy_coverage_id!=1)
                    {
                        $query10="select * from therapy_price_tb where
  therapy_price_id= ".$row3['therapy_price_id']."
AND  therapy_price_coverage_id=$therapy_coverage_id";
                        $result10=$this->B_db->run_query($query10);
                        $num10=count($result10[0]);
                        if($num10==0){
                            $price=0;
                        }

                    }else{
                        $tip=$tip.'پوشش :'.$row3['therapy_coverage_name'];

                    }
                    $record['fieldcompany_desc']=$row3['therapy_price_desc'];
                    $record['fieldcompany_url_desc']=$row3['therapy_price_urldesc'];



                    $price_disc=0;

                    //*******************************************************************************************

                    //*******************************************************************************************
                    $query1="select * from managdiscount_tb,fieldinsurance_tb where managdiscount_company_id='".$row['company_id']."' AND managdiscount_fieldinsurance_id=fieldinsurance_id AND fieldinsurance='$fieldinsurance'
		 AND (managdiscount_date_start='' OR managdiscount_date_start > now()) AND  (managdiscount_date_end='' OR managdiscount_date_end< now())
		 AND managdiscount_max_all>(SELECT COALESCE(SUM(`managdiscount_use_amount`),0) FROM `managdiscount_use_tb` WHERE managdiscount_mngdiscnt_id=managdiscount_tb.managdiscount_id)
		 AND managdiscount_deactive=0";
                    $result1=$this->B_db->run_query($query1);
					if(!empty($result1)){
						$managdiscount=$result1[0];
                    if($managdiscount['managdiscounts_mode']=='fix'){
                        $price_disc=$price-$managdiscount['managdiscount_amount'];
                        $record['managdiscount_desc']=$managdiscount['managdiscount_desc'];
                        $record['managdiscount_id']=$managdiscount['managdiscount_id'];
                        $record['managdiscount_amount']=$managdiscount['managdiscount_amount'];;


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
                    
                    //****************************************************************************************
                    if($price_disc>0){
                        $record['price']=intval($price_disc);
                        $record['price_disc']=intval($price);
                    }else{
                        $record['price']=intval($price);
                    }
                    $record['tip']=$tip;
                    $record['request_description']=$request_description;
                    $record['company_name']=$row['company_name'];
                    $record['company_levelof_prosperity']=$row['company_levelof_prosperity'];
                    $record['company_num_branchesdamages']=$row['company_num_branchesdamages'];
                    $record['company_customer_satisfaction']=$row['company_customer_satisfaction'];
                    $record['company_timeanswer_complaints']=$row['company_timeanswer_complaints'];
                    $record['company_description']=$row['company_description'];
                    $record['company_logo_url']=IMGADD.$row['company_logo_url'];

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
                                    $record3['instalment_conditions_amount'] = round($price_instalment * $row3['instalment_conditions_percent'] / 100, -5, PHP_ROUND_HALF_DOWN);
                                    $tprice_instalment -= round($price_instalment * $row3['instalment_conditions_percent'] / 100, -5, PHP_ROUND_HALF_DOWN);
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
            }
            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'مشحصات شرکت بیمه با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

        }

        }
}