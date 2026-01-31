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
class Price_buildmanage extends REST_Controller {

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
        if (isset($this->input->request_headers()['Authorization'])) $employee_token_str = $this->input->request_headers()['Authorization'];
        $this->load->model('B_user');
        $this->load->model('B_db');
        $this->load->model('B_agent');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('price_elevator', $command, get_client_ip(),50,50)) {
            if ($command == "get_price") {

                $resbuildmng_coverage_id = $this->post('resbuildmng_coverage_id', 1);
                $bodycar_cash = $this->post('bodycar_cash', 1);
                $resbuildmng_kind_id = $this->post('resbuildmng_kind_id', 1);
                $resbuildmng_financemedic_id = $this->post('resbuildmng_financemedic_id', 1);
                $resbuildmng_buildinglife_id = $this->post('resbuildmng_buildinglife_id', 1);

                $resbuildmng_numunit_id = $this->post('resbuildmng_numunit_id', 1);

                $fieldinsurance = 'res_build_manage_ins';





//***************************************************************************************************************


                $query = "select * from company_tb,fieldinsurance_tb,fieldcompany_tb where
 company_deactive=0
 AND fieldinsurance='$fieldinsurance'
 AND fieldinsurance_deactive=0
 AND fieldcompany_company_id=company_id
 AND fieldcompany_fieldinsurance_id=fieldinsurance_id
 AND fieldcompany_deactive=0
  ORDER BY company_id ASC";

                $result = $this->B_db->run_query($query);
                $output = array();
                $price = 0;
                foreach ($result as $row) {
                    $record = array();

                    $record['company_id'] = $row['company_id'];
                    //*******************************************************************************************
                    $request_description = '';
                    //*******************************************************************************************

                    $price = 0;
                    $query3 = "SELECT * FROM chatreno_db.resbuildmng_price_tb
where resbuildmng_price_fieldcompany_id=" . $row['fieldcompany_id'] . "
AND resbuildmng_price_kind_id =$resbuildmng_kind_id
AND  resbuildmng_price_fromyear <= $resbuildmng_buildinglife_id
AND resbuildmng_price_toyear >= $resbuildmng_buildinglife_id
AND resbuildmng_price_financemedic_id = $resbuildmng_financemedic_id ";
                    $result3 = $this->B_db->run_query($query3);
                    $num3 = count($result3[0]);
                    if ($num3 > 0) {
                        $resbuildmng_price = $result3[0];

                                $price= $resbuildmng_price['resbuildmng_price_amount']*$resbuildmng_numunit_id;

                        $cvrgexsist=1;
                        $pricecovrage=0;

                        if (isset($_REQUEST['resbuildmng_coverage_id'])) {
                            $resbuildmng_coverage_id = $this->post('resbuildmng_coverage_id');
                            foreach ($resbuildmng_coverage_id as $resbuildmng_coverage) {
                                $query9 = "select * from resbuildmng_price_covarage_tb,resbuildmng_coverage_tb where
	resbuildmng_coverage_id=resbuildmng_price_covarage_resbuildmng_covarage_id
AND resbuildmng_price_covarage_resbuildmng_price_id=" . $resbuildmng_price['resbuildmng_price_id'] . "
AND resbuildmng_price_covarage_resbuildmng_covarage_id=" . $resbuildmng_coverage . "";
                                $result9 = $this->B_db->run_query($query9);
                                $resbuildmng_price_covarage = $result9[0];
                                $num9 = count($result9[0]);
                                if ($num9 > 0) {
                                        $pricecovrage += (intval($resbuildmng_price_covarage['resbuildmng_price_covarage_percent'] )* $price/100);

                                    $request_description = $request_description . '-' . ' پوشش اضافی: ' . $resbuildmng_price_covarage['resbuildmng_coverage_name'];
                                } else {
                                    $cvrgexsist = 0;
                                }

                            }
                        }
                        if ($cvrgexsist == 0) {
                            $price = 0;
                        }else{
                            $price=$price+$pricecovrage;
                        }


                         //   $price = ($price * 109) / 100;

                    }else{
                        $price=0;
                    }
                    if ($bodycar_cash == 'true'&&$price!=0) {
                        $price = $price - ($resbuildmng_price['resbuildmng_price_chashdisc']*$price/100);
                    }

                    $price_disc = 0;
                    //*******************************************************************************************

                    //*******************************************************************************************
                    $query1 = "select * from managdiscount_tb,fieldinsurance_tb where managdiscount_company_id='" . $row['company_id'] . "' AND managdiscount_fieldinsurance_id=fieldinsurance_id AND fieldinsurance='$fieldinsurance'
		 AND (managdiscount_date_start='' OR managdiscount_date_start > now()) AND  (managdiscount_date_end='' OR managdiscount_date_end< now())
		 AND managdiscount_max_all>(SELECT COALESCE(SUM(`managdiscount_use_amount`),0) FROM `managdiscount_use_tb` WHERE managdiscount_mngdiscnt_id=managdiscount_tb.managdiscount_id)
		 AND managdiscount_deactive=0";
                    $result1 = $this->B_db->run_query($query1);
                    if (!empty($result1)) {
                        $managdiscount = $result1[0];
                        if ($managdiscount['managdiscounts_mode'] == 'fix') {
                            $price_disc = $price - $managdiscount['managdiscount_amount'];
                            $record['managdiscount_desc'] = $managdiscount['managdiscount_desc'];
                            $record['managdiscount_id'] = $managdiscount['managdiscount_id'];
                            $record['managdiscount_amount'] = $managdiscount['managdiscount_amount'];;


                        } else if ($managdiscount['managdiscounts_mode'] == 'percent') {
                            $discount = $price * $managdiscount['managdiscount_amount'] / 100;
                            if ($discount > $managdiscount['managdiscount_max_forone']) {
                                $discount = $managdiscount['managdiscount_max_forone'];
                            }
                            $price_disc = $price - $discount;
                            $record['managdiscount_desc'] = $managdiscount['managdiscount_desc'];
                            $record['managdiscount_id'] = $managdiscount['managdiscount_id'];
                            $record['managdiscount_amount'] = $discount;

                        }
                    }

                    //****************************************************************************************
                    if ($price_disc > 0) {
                        $record['price'] = intval($price_disc);
                        $record['price_disc'] = intval($price);
                    } else {
                        $record['price'] = intval($price);
                    }
                    $record['tip'] = '';
                    $record['request_description'] = $request_description;

                    $record['company_name'] = $row['company_name'];
                    $record['company_levelof_prosperity'] = $row['company_levelof_prosperity'];
                    $record['company_num_branchesdamages'] = $row['company_num_branchesdamages'];
                    $record['company_customer_satisfaction'] = $row['company_customer_satisfaction'];
                    $record['company_timeanswer_complaints'] = $row['company_timeanswer_complaints'];
                    $record['company_description'] = $row['company_description'];
                    $record['company_logo_url'] = IMGADD . $row['company_logo_url'];

                    $record['fieldcompany_desc'] = $row['fieldcompany_desc'];
                    $record['fieldcompany_link'] = $row['fieldcompany_link'];
                    //*****************************************************************************************

                    $query2 = "select * from instalment_tb,fieldinsurance_tb where
 instalment_company_id=" . $row['company_id'] . "
 AND instalment_fieldinsurance_id=fieldinsurance_id
 AND fieldinsurance='$fieldinsurance'
 AND instalment_deactive=0
AND (instalment_date_start='' OR instalment_date_start > now()) AND  (instalment_date_end='' OR instalment_date_end< now())
";
                    $result2 = $this->B_db->run_query($query2);
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

                    $query1 = "INSERT INTO jsonpricing_tb( jsonpricing_text, jsonpricing_date,	jsonpricing_fieldinsurance) VALUES
                                      ( '" . json_encode($record, JSON_UNESCAPED_UNICODE) . "'      ,   now()         , '$fieldinsurance') ";
                    $result1 = $this->B_db->run_query_put($query1);
                    $jsonpricing_id = $this->db->insert_id();
                    $record['jsonpricing_id'] = $jsonpricing_id;
                    //*****************************************************************************************
                    if ($price > 0) {
                        $output[] = $record;
                    }
                }
                echo json_encode(array('result' => "ok"
                , "data" => $output
                , 'desc' => 'مشحصات شرکت بیمه با  موفقیت ارسال شد'.$query3), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


//****************************************************************************************************************

            }


        }
    }
}