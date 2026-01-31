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
class Price_organfirehome extends REST_Controller {

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
        $this->load->model('B_organ');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('price_firehome', $command, get_client_ip(),50,50)) {
            if ($command == "get_price") {
                $usertoken = checkusertoken($this->user_token_str);
                if ($usertoken[0] == 'ok') {
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



                    $query="SELECT * FROM
chatreno_db.organ_contract_tb a 
JOIN
(SELECT DISTINCT organ_tb.* FROM chatreno_db.organ_tb  join chatreno_db.organ_user_tb     on     organ_user_organ_id    = organ_id   where organ_user_user_id=".$usertoken[1]." AND organ_deactive=0)
                                              b   ON a.organ_contract_organ_id=b.organ_id
             join chatreno_db.fieldinsurance_tb c     on     c.fieldinsurance    = '$fieldinsurance'  AND  c.fieldinsurance_id=a.organ_contract_fieldinsuranc_id
             join chatreno_db.company_tb d     on     d.company_id    = a.organ_contract_company_id
             join chatreno_db.fieldcompany_tb e     on     e.fieldcompany_company_id    = a.organ_contract_company_id and e.fieldcompany_fieldinsurance_id= c.fieldinsurance_id
              WHERE a.organ_contract_deactive=0 AND a.organ_contract_date_end>now() and a.organ_contract_date_start<now() AND c.fieldinsurance_deactive=0 AND d.company_deactive=0
            AND c.fieldinsurance_deactive=0";

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
                                $price = ($price * 109) / 100;
                            }
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

                        //****************************************************************************************
                        if ($price_disc > 0) {
                            $record['price'] = intval($price_disc);
                            $record['price_disc'] = intval($price);
                        } else {
                            $record['price'] = intval($price);
                        }
                        if($row['organ_logo']!=''){
                            $result1= $this->B_db->get_image($row['organ_logo']);
                            $image=$result1[0];
                            if($image['image_url']){$imageurl=$image['image_url'];}
                            $record['organ_logo']=$imageurl;
                        }
                        $record['organ_contract_id']=$row['organ_contract_id'];
                        $record['organ_name']=$row['organ_name'];
                        $record['tip']=$row['organ_contract_num'];
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
}