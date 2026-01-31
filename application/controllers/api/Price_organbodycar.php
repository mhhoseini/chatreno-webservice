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
class Price_organbodycar extends REST_Controller
{

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
        if ($this->B_user->checkrequestip('price_bodycar', $command, get_client_ip(),50,50)) {
            if ($command == "get_price") {
                $usertoken=checkusertoken($this->user_token_str);
                if($usertoken[0]=='ok')
                {
                $car_id = $this->post('car_id');
                $bodycar_usefor_id = $this->post('bodycar_usefor_id');
                $bodycar_time_id = $this->post('bodycar_time_id');
                $bodycar_yearofcons_id = $this->post('bodycar_yearofcons_id');
                $bodycarprice = $this->post('bodycar_price');
                $car_mode_id = $this->post('car_mode_id');
                $body_car_import = $this->post('body_car_import');
                $bodycar_not_used = $this->post('bodycar_not_used');
                $bodycar_cash = $this->post('bodycar_cash');

                if (isset($_REQUEST['bodycar_discnt_life_id'])) {

                    $bodycar_discnt_life_id = $this->post('bodycar_discnt_life_id');

                }

                if (isset($_REQUEST['bodycar_discnt_accbank_id'])) {
                    $bodycar_discnt_accbank_id = $this->post('bodycar_discnt_accbank_id');

                }

                if (isset($_REQUEST['bodycar_discnt_another_id'])) {
                    $bodycar_discnt_another_id = $this->post('bodycar_discnt_another_id');

                }






                $fieldinsurance = 'bodycarins';
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
                    $bodycarprice = $this->post('bodycar_price');

                    $record['company_id'] = $row['company_id'];
                    //*******************************************************************************************
                    $price = 0;
                    $query31 = "select * from chatreno_db.bodycar_price_tb,chatreno_db.car_tb,chatreno_db.carmode_tb,chatreno_db.bodycar_price_extracar_tb where  bodycar_price_id=bodycar_price_extracar_bodycar_price_id
  AND car_id=$car_id
  AND car_id=bodycar_price_extracar_car_id
  AND car_mode_id=carmode_id
 AND bodycar_price_deactive=0
 AND bodycar_price_fieldcompany_id=". $row['fieldcompany_id'];
                    $result31 = $this->B_db->run_query($query31);

                    $query32 = "select * from bodycar_price_tb,car_tb,carmode_tb where  bodycar_price_cargroup_id=car_group_id
  AND car_id=$car_id
  AND car_mode_id=carmode_id
 AND bodycar_price_deactive=0
 AND bodycar_price_fieldcompany_id=" . $row['fieldcompany_id'] . " AND $car_id NOT IN(
	 SELECT bodycar_price_exceptions_car_id AS car_id FROM bodycar_price_exceptions_tb  WHERE bodycar_price_exceptions_bodycar_price_id=bodycar_price_id)";
                    $result32 = $this->B_db->run_query($query32);


                    if (!empty($result31)||!empty($result32)) {
                        $num31 = count($result31[0]);
                        if($num31>0) {
                            $bodycar_price = $result31[0];
                        }else{
                            $num32 = count($result32[0]);
                            $bodycar_price = $result32[0];
                        }

                        if (($num31 > 0||$num32 > 0) && $bodycar_price['bodycar_price_min_price'] <= $bodycarprice && $bodycar_price['bodycar_price_max_price'] >= $bodycarprice) {
                            $price = $bodycar_price['bodycar_price_fixed_amount'];
                            $query4 = "select * from bodycar_slideprice_tb where bodycar_slideprice_bodycar_price_id= " . $bodycar_price['bodycar_price_id'] . "  ORDER by bodycar_slideprice_min DESC";
                            $result4 = $this->B_db->run_query($query4);
                            $output4 = array();
                            $num4 = count($result4[0]);


                            $pricedesc = ' ';
                            foreach ($result4 as $row4) {
                                if ($bodycarprice > $row4['bodycar_slideprice_min'] && $row4['bodycar_slideprice_max'] == 0) {
                                    $pricedesc = $pricedesc . ($row4['bodycar_slideprice_percent']) . ',' . strval($bodycarprice - $row4['bodycar_slideprice_min']) . '--';
                                    $price += (($bodycarprice - $row4['bodycar_slideprice_min']) * $row4['bodycar_slideprice_percent'] / 100);
                                    $bodycarprice = $bodycarprice - ($bodycarprice - $row4['bodycar_slideprice_min']);
                                } else if ($bodycarprice > $row4['bodycar_slideprice_min'] && $bodycarprice <= $row4['bodycar_slideprice_max']) {
                                    $pricedesc = $pricedesc . ($row4['bodycar_slideprice_percent']) . ',' . strval($bodycarprice - $row4['bodycar_slideprice_min']) . '--';
                                    $price += (($bodycarprice - $row4['bodycar_slideprice_min']) * $row4['bodycar_slideprice_percent'] / 100);
                                    $bodycarprice = $bodycarprice - ($bodycarprice - $row4['bodycar_slideprice_min']);
                                }

                            }
                            //*******************************************************************************************************
                            $useforexsist = 1;

                            $query7 = "select * from 	bodycar_price_usefor_tb,bodycar_usefor_tb where
	bodycar_usefor_id=bodycar_price_usefor_bodyusefor_id
AND bodycar_price_usefor_bodycar_price_id=" . $bodycar_price['bodycar_price_id'] . "
AND bodycar_price_usefor_bodyusefor_id=" . $bodycar_usefor_id . "";
                            $result7 = $this->B_db->run_query($query7);
                            $bodycar_price_usefor = $result7[0];
                            $num7 = count($result7[0]);
                            if ($num7 > 0) {
                                if($bodycar_price_usefor['bodycar_price_usefor_calmode_id']==0) {
                                    $price = $price + ($bodycar_price_usefor['bodycar_price_usefor_percent'] * $price / 100);
                                }else if($bodycar_price_usefor['bodycar_price_usefor_calmode_id']==1) {
                                    $price = $price + ($bodycar_price_usefor['bodycar_price_usefor_percent'] * $this->post('bodycar_price') / 100);
                                }else if($bodycar_price_usefor['bodycar_price_usefor_calmode_id']==2) {
                                    $price = $price + $bodycar_price_usefor['bodycar_price_usefor_percent'];
                                }

                            } else {
                                $useforexsist = 0;
                            }

                            $oldexsist = 0;
                            $query44 = "select * from bodycar_extra_slideold_tb where bodycar_extra_slideold_bodycar_price_id= " . $bodycar_price['bodycar_price_id'] . "  ";
                            $result44 = $this->B_db->run_query($query44);

                            foreach ($result44 as $row44) {
                                if ($bodycar_yearofcons_id >= $row44['bodycar_extra_slideold_min'] && $bodycar_yearofcons_id< $row44['bodycar_extra_slideold_max']) {
                                    $price += ($price * $row44['bodycar_extra_slideold_percent'] / 100);
                                    $oldexsist = 1;
                                }
                            }



                            $pricecovrage = 0;
                            $cvrgexsist = 1;
                            if (isset($_REQUEST['bodycar_coverage_id'])) {
                                $bodycar_coverage_id =$_REQUEST['bodycar_coverage_id'] ;
                                foreach ($bodycar_coverage_id as $bodycar_coverage) {

                                    $query9 = "select * from 	bodycar_price_covarage_tb,bodycar_coverage_tb where
	bodycar_coverage_id=bodycar_price_covarage_bodycoverage_id
AND bodycar_price_covarage_bodycar_price_id=" . $bodycar_price['bodycar_price_id'] . "
AND bodycar_price_covarage_bodycoverage_id=" . $bodycar_coverage . "";
                                    $result9 = $this->B_db->run_query($query9);
                                    $bodycar_price_covarage = $result9[0];
                                    $num9 = count($result9[0]);
                                    if ($num9 > 0) {
                                        if($bodycar_price_covarage['bodycar_price_covarage_calmode_id']==0) {
                                            $pricecovrage = $pricecovrage + ($bodycar_price_covarage['bodycar_price_covarage_percent'] * $price / 100);
                                        }else if($bodycar_price_covarage['bodycar_price_covarage_calmode_id']==1) {
                                            $pricecovrage = $pricecovrage + ($bodycar_price_covarage['bodycar_price_covarage_percent'] * $this->post('bodycar_price') / 100);
                                        }
                                        $request_description = '';
                                        $request_description = $request_description . '-' . ' پوشش اضافی: ' . $bodycar_price_covarage['bodycar_coverage_name'];
                                    } else {
                                        $cvrgexsist = 0;
                                    }

                                }
                            }
                            if ($cvrgexsist == 0||$useforexsist==0||$oldexsist==0) {
                                $price = 0;
                            } else {
                                $price = ($pricecovrage + $price);
                                //*******************************************************************************************************
                                $bodycar_price_min_disc2 = 0;
                                $bodycar_price_max_disc2 = 0;
                                $query8 = "select * from bodycar_slidedisc_tb where
	   bodycar_slidedisc_bodycar_price_id=" . $bodycar_price['bodycar_price_id'] . "";
                                $result8 = $this->B_db->run_query($query8);
                                if (!empty($result8)) {
                                    foreach ($result8 as $row8) {
                                        if ($this->post('bodycar_price') > $row8['bodycar_slidedisc_min'] && $this->post('bodycar_price') <= $row8['bodycar_slidedisc_max']) {
                                            if ($bodycar_cash == 'true') {
                                                $bodycar_price_min_disc2 = $row8['bodycar_slidedisc_minpercent'];
                                                $bodycar_price_max_disc2 = $row8['bodycar_slidedisc_maxpercent'];
                                            }else{
                                                $bodycar_price_min_disc2 = $row8['bodycar_slidedisc_instalment_minpercent'];
                                                $bodycar_price_max_disc2 = $row8['bodycar_slidedisc_instalment_maxpercent'];
                                            }
                                        }

                                    }
                                }

                                //شروع تخفیف
                                $disc = 0;

                                if (isset($_REQUEST['bodycar_discnt_id'])) {
                                    $bodycar_discnt_id = $this->post('bodycar_discnt_id');

                                    $query10 = "select * from bodycar_price_discbody_tb,bodycar_discnt_tb where
	  bodycar_discnt_id=bodycar_price_discbody_bodycar_discnt_id
	  AND bodycar_price_discbody_bodycar_price_id=" . $bodycar_price['bodycar_price_id'] . "
	AND bodycar_discnt_id=" . $bodycar_discnt_id . "";
                                    $result10 = $this->B_db->run_query($query10);
                                    $bodycar_discnt = $result10[0];
                                    $num10 = count($result10[0]);
                                    if ($num10 > 0 && $bodycar_discnt['bodycar_price_discbody_percent'] != 0 && $bodycar_discnt['bodycar_price_discbody_percent'] != '0') {
                                        $price = $price - ($price * $bodycar_discnt['bodycar_price_discbody_percent'] / 100);
                                        $disc = $bodycar_discnt['bodycar_price_discbody_percent'];
                                    }
                                }

                                if (isset($_REQUEST['bodycar_discnt_thirdparty_id']) ) {
                                    $bodycar_discnt_thirdparty_id = $this->post('bodycar_discnt_thirdparty_id');
                                    $bodycar_last_company_id = $this->post('bodycar_last_company_id');

                                    $query11 = "select * from bodycar_price_thirdparty_tb,bodycar_discnt_thirdparty_tb where
	  bodycar_discnt_thirdparty_id=bodycar_price_thirdparty_bodycar_discnt_thirdparty_id
	  AND bodycar_price_thirdparty_bodycar_price_id=" . $bodycar_price['bodycar_price_id'] . "
	AND bodycar_discnt_thirdparty_id=" . $bodycar_discnt_thirdparty_id . "";
                                    $result11 = $this->B_db->run_query($query11);
                                    $bodycar_discnt_thirdparty = $result11[0];
                                    $num11 = count($result11[0]);
                                    if ($num11 > 0) {
                                        //****************************************************************
                                        if($bodycar_price['bodycar_price_together_disc']==0) {
                                            if( $disc == 0) {

                                                if($bodycar_price['bodycar_price_robonbaseprice']==0) {
                                                    $price = $price - ($price * $bodycar_discnt_thirdparty['bodycar_price_thirdparty_percent'] / 100);
                                                    $disc = $bodycar_discnt_thirdparty['bodycar_price_thirdparty_percent'];
                                                }else if($bodycar_price['bodycar_price_robonbaseprice']==1 && $row['company_id']==$bodycar_last_company_id ) {
                                                    $price = $price - ($price * $bodycar_discnt_thirdparty['bodycar_price_thirdparty_percent'] / 100);
                                                    $disc = $bodycar_discnt_thirdparty['bodycar_price_thirdparty_percent'];
                                                }
                                            }
                                        }else {
                                            //****************************************************************
                                            $bodycar_price_thirdparty_percent=0;
                                            if($bodycar_price['bodycar_price_robonbaseprice']==0) {
                                                $bodycar_price_thirdparty_percent=  $bodycar_discnt_thirdparty['bodycar_price_thirdparty_percent'] ;
                                            }else if($bodycar_price['bodycar_price_robonbaseprice']==1 && $row['company_id']==$bodycar_last_company_id ) {
                                                $bodycar_price_thirdparty_percent=  $bodycar_discnt_thirdparty['bodycar_price_thirdparty_percent'] ;
                                            }
                                            if ($bodycar_price_thirdparty_percent + $disc > $bodycar_price_max_disc2) {
                                                $bodycar_price_thirdparty_percent = $bodycar_price_max_disc2 - $disc;
                                            }

                                            $price = $price - ($price * $bodycar_price_thirdparty_percent / 100);
                                            $disc = $disc + $bodycar_price_thirdparty_percent;
                                            //****************************************************************

                                        }
                                        //****************************************************************

                                    }
                                }

                                if ($bodycar_not_used == 'true' && $disc < $bodycar_price_max_disc2) {
                                    $disc_new_percent = $bodycar_price['bodycar_price_new_percent'];
                                    if ($disc_new_percent + $disc > $bodycar_price_max_disc2) {
                                        $disc_new_percent = $bodycar_price_max_disc2 - $disc;
                                    }

                                    $price = $price - ($price * $disc_new_percent / 100);
                                    $disc = $disc + $disc_new_percent;
                                }


                                if ($disc < $bodycar_price_max_disc2) {//تخفیف سال ساخت
                                    $bodycar_disc_slideold_percent =0;
                                    $query80 = "select * from bodycar_disc_slideold_tb where
	   bodycar_disc_slideold_bodycar_price_id=" . $bodycar_price['bodycar_price_id'] ;
                                    $result80 = $this->B_db->run_query($query80);
                                    if (!empty($result80)) {
                                        foreach ($result80 as $row80) {
                                            if ($bodycar_yearofcons_id >= $row80['bodycar_disc_slideold_min'] && $bodycar_yearofcons_id< $row80['bodycar_disc_slideold_max']) {
                                                $bodycar_disc_slideold_percent = $row80['bodycar_disc_slideold_percent'];
                                            }

                                        }
                                    }

                                    if ($bodycar_disc_slideold_percent>0&&($bodycar_disc_slideold_percent + $disc) > $bodycar_price_max_disc2) {
                                        $bodycar_disc_slideold_percent = $bodycar_price_max_disc2 - $disc;
                                    }

                                    $price = $price - ($price * $bodycar_disc_slideold_percent / 100);
                                    $disc = $disc + $bodycar_disc_slideold_percent;
                                }

                                if ($disc < $bodycar_price_max_disc2) {//تخفیف بیمه عمر
                                    $bodycar_price_discntlife_percent =0;
                                    $query81 = "select * from bodycar_price_discntlife_tb where
	   bodycar_price_discntlife_bodycar_price_id=" . $bodycar_price['bodycar_price_id'] ."
	   AND bodycar_price_discntlife_bodycar_discnt_life_id=".$bodycar_discnt_life_id ;
                                    $result81 = $this->B_db->run_query($query81);

                                    if ($result81) {
                                        $bodycar_price_discntlife = $result81[0];
                                        $bodycar_price_discntlife_percent = $bodycar_price_discntlife['bodycar_price_discntlife_percent'];

                                    }

                                    if ($bodycar_price_discntlife_percent>0&&($bodycar_price_discntlife_percent + $disc) > $bodycar_price_max_disc2) {
                                        $bodycar_price_discntlife_percent = $bodycar_price_max_disc2 - $disc;
                                    }

                                    $price = $price - ($price * $bodycar_price_discntlife_percent / 100);
                                    $disc = $disc + $bodycar_price_discntlife_percent;
                                }

                                if ($disc < $bodycar_price_max_disc2) {//تخفیف بانک
                                    $bodycar_price_accbank_percent =0;
                                    $query81 = "select * from bodycar_price_accbank_tb where
	   bodycar_price_accbank_bodycar_price_id=" . $bodycar_price['bodycar_price_id'] ."
	   AND bodycar_price_accbank_bodycar_discnt_accbank_id=".$bodycar_discnt_accbank_id ;
                                    $result81 = $this->B_db->run_query($query81);

                                    if ($result81) {
                                        $bodycar_price_accbank = $result81[0];
                                        $bodycar_price_accbank_percent = $bodycar_price_accbank['bodycar_price_accbank_percent'];

                                    }

                                    if ($bodycar_price_accbank_percent>0&&($bodycar_price_accbank_percent + $disc) > $bodycar_price_max_disc2) {
                                        $bodycar_price_accbank_percent = $bodycar_price_max_disc2 - $disc;
                                    }

                                    $price = $price - ($price * $bodycar_price_accbank_percent / 100);
                                    $disc = $disc + $bodycar_price_accbank_percent;
                                }


                                if ($disc < $bodycar_price_max_disc2) {//تخفیف متفرقه
                                    $bodycar_price_another_percent =0;
                                    $query81 = "select * from bodycar_price_another_tb where
	   bodycar_price_another_bodycar_price_id=" . $bodycar_price['bodycar_price_id'] ."
	   AND bodycar_price_another_bodycar_discnt_another_id=".$bodycar_discnt_another_id ;
                                    $result81 = $this->B_db->run_query($query81);

                                    if ($result81) {
                                        $bodycar_price_accbank = $result81[0];
                                        $bodycar_price_another_percent = $bodycar_price_accbank['bodycar_price_another_percent'];

                                    }

                                    if ($bodycar_price_another_percent>0&&($bodycar_price_another_percent + $disc) > $bodycar_price_max_disc2) {
                                        $bodycar_price_another_percent = $bodycar_price_max_disc2 - $disc;
                                    }

                                    $price = $price - ($price * $bodycar_price_another_percent / 100);
                                    $disc = $disc + $bodycar_price_another_percent;
                                }


                                if ($disc < $bodycar_price_max_disc2) {
                                    //$bodycar_price_min_disc=$bodycar_price['bodycar_price_min_disc'];
                                    if ($bodycar_price_min_disc2 + $disc > $bodycar_price_max_disc2) {
                                        $bodycar_price_min_disc2 = $bodycar_price_max_disc2 - $disc;
                                    }

                                    $price = $price - ($price * $bodycar_price_min_disc2 / 100);
                                    $disc = $disc + $bodycar_price_min_disc2;
                                }

                                if ($disc < $bodycar_price_max_disc2) {
                                    $bodycar_price_min_disc=$bodycar_price['bodycar_price_min_disc'];
                                    if ($bodycar_price_min_disc + $disc > $bodycar_price_max_disc2) {
                                        $bodycar_price_min_disc = $bodycar_price_max_disc2 - $disc;
                                    }

                                    $price = $price - ($price * $bodycar_price_min_disc / 100);
                                    $disc = $disc + $bodycar_price_min_disc;
                                }



                                if ($bodycar_cash == 'true'&&$bodycar_price['bodycar_price_stairs']!=0) {
                                    $bodycar_price_chash = $bodycar_price['bodycar_price_chash'];
                                    if ($bodycar_price_chash + $disc > $bodycar_price_max_disc2) {
                                        $bodycar_price_chash = $bodycar_price_max_disc2 - $disc;
                                    }

                                    $price = $price - ($price * $bodycar_price_chash / 100);
                                    $disc = $disc + $bodycar_price_chash;

                                }else
                                    if ($bodycar_cash == 'true'&&$bodycar_price['bodycar_price_stairs']==0) {
                                        $price = $price - ($price * $bodycar_price['bodycar_price_chash'] / 100);
                                        $disc = $disc + $bodycar_price['bodycar_price_chash'];
                                    }
//پایان تخفیف
                                //*******************************************************************************************************
                                if (isset($_REQUEST['bodycar_coverage_id'])) {
                                    $bodycar_coverage_id =$_REQUEST['bodycar_coverage_id'] ;
                                    foreach ($bodycar_coverage_id as $bodycar_coverage) {

                                        $query9 = "select * from 	bodycar_price_covarage_tb,bodycar_coverage_tb where
	bodycar_coverage_id=bodycar_price_covarage_bodycoverage_id
AND bodycar_price_covarage_bodycar_price_id=" . $bodycar_price['bodycar_price_id'] . "
AND bodycar_price_covarage_bodycoverage_id=" . $bodycar_coverage . "";
                                        $result9 = $this->B_db->run_query($query9);
                                        $bodycar_price_covarage = $result9[0];
                                        $num9 = count($result9[0]);
                                        if ($num9 > 0) {
                                            if($bodycar_price_covarage['bodycar_price_covarage_calmode_id']==2) {
                                                $price = $price + $bodycar_price_covarage['bodycar_price_covarage_percent'] ;
                                            }
                                        }

                                    }
                                }
                                //*******************************************************************************************************
                            }
                            //*******************************************************************************************************

                        }
                    }


                    $price=$price*109/100;
                    $price_disc = 0;
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
                    if($price_disc>0){
                        $record['price']=intval($price_disc);
                        $record['price_disc']=intval($price);
                    }else{
                        $record['price']=intval($price);
                    }
                    //****************************************************************************************
                    $request_description = 'بیمه نامه در رشته ' . $row['fieldinsurance_fa'];
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
            }
        }
    }
    }
}