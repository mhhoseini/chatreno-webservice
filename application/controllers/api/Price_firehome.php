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
class Price_firehome extends REST_Controller {

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
        if ($this->B_user->checkrequestip('price_firehome', $command, get_client_ip(),50,50)) {
            if ($command == "get_price") {

                $firehome_kind_id = $this->post('firehome_kind_id', 1);
                $firehome_unit = $this->post('firehome_unit', 1);
                $firehome_typeofcons_id = $this->post('firehome_typeofcons_id', 1);
                $firehome_buildinglife_id = $this->post('firehome_buildinglife_id', 1);
                $firehome_area = $this->post('firehome_area', 1);
                $firehome_cost_furniture = $this->post('firehome_cost_furniture', 1);

                $firehome_costcons_id = $this->post('firehome_costcons_id', 1);

                $fieldinsurance = 'firehomeins';


                $query5 = "select * from firehome_kind_tb where firehome_kind_id=$firehome_kind_id";
                $result5 = $this->B_db->run_query($query5);
                $firehome_kind = $result5[0];

                $query6 = "select * from firehome_typeofcons_tb where firehome_typeofcons_id=$firehome_typeofcons_id";
                $result6 = $this->B_db->run_query($query6);
                $firehome_typeofcons = $result6[0];

                $query7 = "select * from firehome_buildinglife_tb where firehome_buildinglife_id=$firehome_buildinglife_id";
                $result7 = $this->B_db->run_query($query7);
                $firehome_buildinglife = $result7[0];

                $query8 = "select * from firehome_costcons_tb where firehome_costcons_id=$firehome_costcons_id";
                $result8 = $this->B_db->run_query($query8);
                $firehome_costcons = $result8[0];


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
                    $request_description = ':بیمه نامه در رشته ' . $row['fieldinsurance_fa'] . '-'
                        . ' :نوع ساختمان ' . $firehome_kind['firehome_kind_name'] . '-' . ' :تعداد واحد ' . $firehome_unit . '-'
                        . ' :نوع سازه ' . $firehome_typeofcons['firehome_typeofcons_name'] . '-'
                        . ' :ارزش لوازم منزل ' . $firehome_cost_furniture . '-'
                        . ' :متراژ واحد ' . $firehome_area . '-'
                        . ' :عمر بنا ' . $firehome_buildinglife['firehome_buildinglife_id'] . '-'
                        . ' ارزش ساخت هر متر مربع: ' . $firehome_costcons['firehome_costcons_name'];
                    //*******************************************************************************************

                    $price = 0;
                    $query3 = "select * from firehome_price_tb
where
 firehome_price_kind_id=$firehome_kind_id
AND firehome_price_typeofcons_id=$firehome_typeofcons_id
 AND firehome_price_deactive=0
 AND firehome_price_fieldcompany_id=" . $row['fieldcompany_id'] . "";
                    $result3 = $this->B_db->run_query($query3);
                    $num3 = count($result3[0]);
                    if ($num3 > 0) {
                        $firehome_price = $result3[0];

                        $build = $firehome_costcons['firehome_costcons_price'] * $firehome_area;
                        $furniture = $firehome_cost_furniture;

                        $pricebuild = $build * $firehome_price['firehome_price_buildpercent'];
                        $pricefurniture = $furniture * $firehome_price['firehome_price_furniturepercent'];
//$price=$pricebuild+$pricefurniture;
                        $pricecovrage = 0;
                        $cvrgexsist = 1;
                        if (isset($_REQUEST['firehome_coverage_id'])) {
                            $firehome_coverage_id = $this->post('firehome_coverage_id');
                            foreach ($firehome_coverage_id as $firehome_coverage) {

                                $query9 = "select * from firehome_price_covarage_tb,firehome_coverage_tb where
	firehome_coverage_id=firehome_price_covarage_firehome_covarage_id
AND firehome_price_covarage_firehome_price_id=" . $firehome_price['firehome_price_id'] . "
AND firehome_price_covarage_firehome_covarage_id=" . $firehome_coverage . "";
                                $result9 = $this->B_db->run_query($query9);
                                $firehome_price_covarage = $result9[0];
                                $num9 = count($result9[0]);
                                if ($num9 > 0) {
                                    if ($firehome_price_covarage['firehome_coverage_calculat_id'] == 0) {
                                        $pricecovrage += ($firehome_price_covarage['firehome_price_covarage_percent'] * $build);
                                    } else if ($firehome_price_covarage['firehome_coverage_calculat_id'] == 1) {
                                        $pricecovrage += ($firehome_price_covarage['firehome_price_covarage_percent'] * $furniture);
                                    } else if ($firehome_price_covarage['firehome_coverage_calculat_id'] == 2) {
                                        $pricecovrage += ($firehome_price_covarage['firehome_price_covarage_percent'] * ($furniture + $build));
                                    } else if ($firehome_price_covarage['firehome_coverage_calculat_id'] == 3) {
                                        if (isset($_REQUEST['firehome_exterafield'])) {
                                            $valueextrafields = $this->post('firehome_exterafield');
                                            foreach ($valueextrafields as $firehome_exterafield) {
                                                $obj = json_decode($firehome_exterafield);
                                                if ($firehome_coverage == $obj->{'id'}) {
                                                    $pricecovrage += ($firehome_price_covarage['firehome_price_covarage_percent'] * ($obj->{'value'}));
                                                }
                                            }

                                        }
                                    }
                                    $request_description = $request_description . '-' . ' پوشش اضافی: ' . $firehome_price_covarage['firehome_coverage_name'];
                                } else {
                                    $cvrgexsist = 0;
                                }

                            }
                        }
                        if ($cvrgexsist == 0) {
                            $price = 0;
                        } else {
                            $price = ($pricecovrage + $pricebuild + $pricefurniture) / 1000000000;

                            $price = $price-(($price * $firehome_price['firehome_price_disc'] ) / 100);
                            $price = ($price * 109) / 100;
                        }
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
                , 'desc' => 'مشحصات شرکت بیمه با  موفقیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


//****************************************************************************************************************

            }


        }
    }
}