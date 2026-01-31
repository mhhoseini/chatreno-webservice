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
class Price_bodycar extends REST_Controller
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
        if (isset($this->input->request_headers()['Authorization'])) $employee_token_str = $this->input->request_headers()['Authorization'];
        $this->load->model('B_user');
        $this->load->model('B_db');
        $this->load->model('B_agent');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('price_bodycar', $command, get_client_ip(),50,50)) {
            if ($command == "get_price") {

                $car_id = $this->post('car_id');// ای دی خودرو
                $bodycar_usefor_id = $this->post('bodycar_usefor_id');// مورد استفاده خودرو
                $bodycar_time_id = $this->post('bodycar_time_id');// مدت زمان بمیه نامه
                $bodycar_yearofcons_id = $this->post('bodycar_yearofcons_id');//سال ساخت
                $bodycarprice = $this->post('bodycar_price');
                $car_mode_id = $this->post('car_mode_id');
                $body_car_import = $this->post('body_car_import');
                $bodycar_not_used = $this->post('bodycar_not_used');// خودرو صفر کیلومتر
                $bodycar_cash = $this->post('bodycar_cash');// بیمه نامه بدنه نقدی

                if (isset($_REQUEST['bodycar_discnt_life_id'])) {// تحفیف بیمه عمر روی بیمه بدنه

                    $bodycar_discnt_life_id = $this->post('bodycar_discnt_life_id');

                }

                if (isset($_REQUEST['bodycar_discnt_accbank_id'])) {//تحفیف حساب بانکی بلند مدت روی بدنه
                    $bodycar_discnt_accbank_id = $this->post('bodycar_discnt_accbank_id');

                }

                if (isset($_REQUEST['bodycar_discnt_another_id'])) {// سایر تحفیفات
                    $bodycar_discnt_another_id = $this->post('bodycar_discnt_another_id');

                }






                $fieldinsurance = 'bodycarins';
                $query = "select * from company_tb,fieldinsurance_tb,fieldcompany_tb where
 company_deactive=0
 AND fieldinsurance='$fieldinsurance'
 AND fieldinsurance_deactive=0
 AND fieldcompany_company_id=company_id
 AND fieldcompany_fieldinsurance_id=fieldinsurance_id
 AND fieldcompany_deactive=0
  ORDER BY company_id ASC";//در این کوئری لیست شرکت های بیمه ای که بیمه بدنه برای انها فعال است برمیگرداند

                $result = $this->B_db->run_query($query);
                $output = array();
                foreach ($result as $row) { //حلقه ای برای کوئری بالا
                    $record = array();
                    $bodycarprice = $this->post('bodycar_price');// قیمت خودرو

                    $record['company_id'] = $row['company_id'];// ای دی شرکت بیمه ای که قرار است نرخ ان محاسبه گردد
                    //*******************************************************************************************
                    $price = 0;// قیمت اولیه صفر در نظر گرفته میشد
                    $query31 = "select * from bodycar_price_tb,car_tb,carmode_tb,bodycar_price_extracar_tb where  bodycar_price_id=bodycar_price_extracar_bodycar_price_id
  AND car_id=$car_id
  AND car_id=bodycar_price_extracar_car_id
  AND car_mode_id=carmode_id
 AND bodycar_price_deactive=0
 AND bodycar_price_fieldcompany_id=". $row['fieldcompany_id'];// دی این کوئری با اولیت جدول خودرو های اضافه رکورد قیمت شرکت مورد نظر در ان نوع خودرو در جدول bodycar_price_tb پیدا و باز گردانده میشود
                    $result31 = $this->B_db->run_query($query31);

                    $query32 = "select * from bodycar_price_tb,car_tb,carmode_tb where  bodycar_price_cargroup_id=car_group_id
  AND car_id=$car_id
  AND car_mode_id=carmode_id
 AND bodycar_price_deactive=0
 AND bodycar_price_fieldcompany_id=" . $row['fieldcompany_id'] . " AND $car_id NOT IN(
	 SELECT bodycar_price_exceptions_car_id AS car_id FROM bodycar_price_exceptions_tb  WHERE bodycar_price_exceptions_bodycar_price_id=bodycar_price_id)";
                    $result32 = $this->B_db->run_query($query32);// این کوئری همانند کوئری بالاست فقط اگر این خودرو در گروه اضافه خودرو های هیچ رکورد قیمتی نباشد بر اساس نوع خودرو در جدول bodycar_price_tb سرچ و باز گردانده میشود


                    if (!empty($result31)||!empty($result32)) {// اگر هیچکدام از دو کوئری بالا رکوردی برنگردانده باشند این خودرو در این شرکت قیمتی ندارد
                        $num31 = count($result31[0]);
                        if($num31>0) {// تعداد رکورد برگشت داده شده از کوئری مدل اول
                            $bodycar_price = $result31[0];
                        }else{
                            $num32 = count($result32[0]);// تعداد کوئری برگردانده شده از کوئری دوم که با اولنیت کوئری اول می باشد اگر نبود کوئری دوم
                            $bodycar_price = $result32[0];
                        }

                        if (($num31 > 0||$num32 > 0) && $bodycar_price['bodycar_price_min_price'] <= $bodycarprice && $bodycar_price['bodycar_price_max_price'] >= $bodycarprice) {// اگر کوئری اول یا دوم ردیفی برگنداده باشد و همینطور قیمت خودرو در بازه نرخ ثبت شده این شرکت در جدول bodycar_price_tb باشد
                            $price = $bodycar_price['bodycar_price_fixed_amount'];// اگر قیمت ثابتی باشد به عنوان قیمت اولیه ثبت میشودر
                            $query4 = "select * from bodycar_slideprice_tb where bodycar_slideprice_bodycar_price_id= " . $bodycar_price['bodycar_price_id'] . "  ORDER by bodycar_slideprice_min DESC"; // در این کوئری بازه های نرخ برای این ردیف قیمت bodycar_price بازگردانده میشود به ترتیب بازه بالا به بازه پایین
                            $result4 = $this->B_db->run_query($query4);
                            $output4 = array();
                            $num4 = count($result4[0]);


                            $pricedesc = ' ';
                            foreach ($result4 as $row4) {// این حلقه تکرار میشود تا همه بازه ها محاسبه شوند
                                if ($bodycarprice > $row4['bodycar_slideprice_min'] && $row4['bodycar_slideprice_max'] == 0) {// در بالاترین بازه که از یک قیمت تا بی نهایت است
                                    $pricedesc = $pricedesc . ($row4['bodycar_slideprice_percent']) . ',' . strval($bodycarprice - $row4['bodycar_slideprice_min']) . '--';
                                    $price += (($bodycarprice - $row4['bodycar_slideprice_min']) * $row4['bodycar_slideprice_percent'] / 100);// قیمت خودرو منهای حد پایین این بازه ضرب در نرخ این بازه
                                    $bodycarprice = $bodycarprice - ($bodycarprice - $row4['bodycar_slideprice_min']);// اصلاح قیمت خودرو به طوری که قیمت محاسبه شده در بازه فوق از قیمت خودرو کسر میشود
                                } else if ($bodycarprice > $row4['bodycar_slideprice_min'] && $bodycarprice <= $row4['bodycar_slideprice_max']) { // در سایر بازه ها بعد از بزرگترین بازه
                                    $pricedesc = $pricedesc . ($row4['bodycar_slideprice_percent']) . ',' . strval($bodycarprice - $row4['bodycar_slideprice_min']) . '--';
                                    $price += (($bodycarprice - $row4['bodycar_slideprice_min']) * $row4['bodycar_slideprice_percent'] / 100);//قیمت خودرو مانده از محاسبه حلقه قبلی منهای حد پایین بازه ضرب در نرخ
                                    $bodycarprice = $bodycarprice - ($bodycarprice - $row4['bodycar_slideprice_min']);// اصلاح قیمت خودرو به طوری که قیمت محاسبه شده در بازه فوق از قیمت خودرو کسر میشود
                                }

                            }
                            // در پایان این حلقه قیمت پاره در متغیر$price محاسبه شده است
                            //*******************************************************************************************************
                            $useforexsist = 1;// مورد استفاده موجورد است

                            $query7 = "select * from 	bodycar_price_usefor_tb,bodycar_usefor_tb where
	bodycar_usefor_id=bodycar_price_usefor_bodyusefor_id
AND bodycar_price_usefor_bodycar_price_id=" . $bodycar_price['bodycar_price_id'] . "
AND bodycar_price_usefor_bodyusefor_id=" . $bodycar_usefor_id . "";// این کوئری رکورد نرخ های  مورد استفاده را در این شرکت و این نوع خودرو برمیگرداند
                            $result7 = $this->B_db->run_query($query7);
                            $bodycar_price_usefor = $result7[0];
                            $num7 = count($result7[0]);
                            if ($num7 > 0) {
                                if($bodycar_price_usefor['bodycar_price_usefor_calmode_id']==0) {// مدل محاسبه اول که نرخ مورد استفاده به صورت درصد است و به حق بیمه پایه اعمال میشود
                                    $price = $price + ($bodycar_price_usefor['bodycar_price_usefor_percent'] * $price / 100);
                                }else if($bodycar_price_usefor['bodycar_price_usefor_calmode_id']==1) {// مدل محاسبه اول که نرخ مورد استفاده به صورت درصد است و به قیمت خودرو اعمال میشود
                                    $price = $price + ($bodycar_price_usefor['bodycar_price_usefor_percent'] * $this->post('bodycar_price') / 100);
                                }else if($bodycar_price_usefor['bodycar_price_usefor_calmode_id']==2) {// مدل محاسبه اول که نرخ مورد استفاده به صورت قیمت ثابت است و به حق بیمه پایه اعمال میشود
                                    $price = $price + $bodycar_price_usefor['bodycar_price_usefor_percent'];
                                }

                            } else {
                                $useforexsist = 0;// اگر مورد استفاده مورد نظر کاربر در این شرکت ثبت نشده باشد این متغیر صفر میشود
                            }

                            $oldexsist = 0;
                            $query44 = "select * from bodycar_extra_slideold_tb where bodycar_extra_slideold_bodycar_price_id= " . $bodycar_price['bodycar_price_id'] . "  "; // این کوئری بازه های سن خودرو را بررسی میکند که برای این شرکت و این نوع خودرو بازه ای برای این سن ثبت شده و چقدر اضافه نرخ دارد
                            $result44 = $this->B_db->run_query($query44);

                            foreach ($result44 as $row44) {
                                if ($bodycar_yearofcons_id >= $row44['bodycar_extra_slideold_min'] && $bodycar_yearofcons_id< $row44['bodycar_extra_slideold_max']) {
                                    $price += ($price * $row44['bodycar_extra_slideold_percent'] / 100);//اضافه کردن اضافه نرخ این بازه خودرو به قیمت پایه
                                    $oldexsist = 1;// قیمت خودرو در بازه ای ثبت شده است
                                }
                            }



                            $pricecovrage = 0;// حق بیمه پوشش های اضافی که ابتدا صفر است
                            $cvrgexsist = 1;
                            if (isset($_REQUEST['bodycar_coverage_id'])) {// اگر پوشش های اضافی ای کاربر وارد کرده بود
                                $bodycar_coverage_id =$_REQUEST['bodycar_coverage_id'] ;// پوشش های اضافی که از سمت کاربر میاید به صورت ارایه است
                                foreach ($bodycar_coverage_id as $bodycar_coverage) { // پیماش ارایه پوشش ها

                                    $query9 = "select * from 	bodycar_price_covarage_tb,bodycar_coverage_tb where
	bodycar_coverage_id=bodycar_price_covarage_bodycoverage_id
AND bodycar_price_covarage_bodycar_price_id=" . $bodycar_price['bodycar_price_id'] . "
AND bodycar_price_covarage_bodycoverage_id=" . $bodycar_coverage . "";// این کوئری ردیف پوشش مورد نظر را در صورت وجود برای این شرکت و این نوع خودرو برمیگرداند
                                    $result9 = $this->B_db->run_query($query9);
                                    $bodycar_price_covarage = $result9[0];
                                    $num9 = count($result9[0]);
                                    if ($num9 > 0) {
                                        if($bodycar_price_covarage['bodycar_price_covarage_calmode_id']==0) {// مدل محاسبه اول که به صورت درصد به حق بیمه پایه اغمال میشود
                                            $pricecovrage = $pricecovrage + ($bodycar_price_covarage['bodycar_price_covarage_percent'] * $price / 100);
                                        }else if($bodycar_price_covarage['bodycar_price_covarage_calmode_id']==1) {// مدل محاسبه اول که به صورت درصد به قیمت خودرو اغمال میشود
                                            $pricecovrage = $pricecovrage + ($bodycar_price_covarage['bodycar_price_covarage_percent'] * $this->post('bodycar_price') / 100);
                                        }
                                        $request_description = '';
                                        $request_description = $request_description . '-' . ' پوشش اضافی: ' . $bodycar_price_covarage['bodycar_coverage_name'];
                                    } else {
                                        $cvrgexsist = 0;// اگر هر یک از پوشش های انتخابی کاربر برای این شرکت وجود نداشت این متغیر صفر میشود
                                    }

                                }
                            }
                            if ($cvrgexsist == 0||$useforexsist==0||$oldexsist==0) {// اگر پوشش یا سن خودرو در بازه یا مورد استفاده وجود نداشت حق بیمه پایه صفر میشود که نمایش داده نشود
                                $price = 0;
                            } else {
                                $price = ($pricecovrage + $price);// حق بیمه پایه با حق بیمه پوشش ها جمع میشود
                                //*******************************************************************************************************
                                $bodycar_price_min_disc2 = 0;// حد پایین تخفیف
                                $bodycar_price_max_disc2 = 0;// حد بالای تخفیف
                                $query8 = "select * from bodycar_slidedisc_tb where
	   bodycar_slidedisc_bodycar_price_id=" . $bodycar_price['bodycar_price_id'] . "";// این کوئری بازه های قیمتی که برای انها حد بالا و حد پایین تخفیف در نظر گرفته شده را برمیکرداند
                                $result8 = $this->B_db->run_query($query8);
                                if (!empty($result8)) {
                                    foreach ($result8 as $row8) {
                                        if ($this->post('bodycar_price') > $row8['bodycar_slidedisc_min'] && $this->post('bodycar_price') <= $row8['bodycar_slidedisc_max']) {// جسجوی خودرد در بازه های قیمتی
                                            if ($bodycar_cash == 'true') {// اگر کابر پرداخت نقدی را انتخال کرده بود
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
                                $disc = 0;// تخفیف صفر

                                if (isset($_REQUEST['bodycar_discnt_id'])) {
                                    $bodycar_discnt_id = $this->post('bodycar_discnt_id');// تخفیف سال قبل بدنه

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
                                    $bodycar_discnt_thirdparty_id = $this->post('bodycar_discnt_thirdparty_id');//  تخفیف سال قبل ثالث
                                    $bodycar_last_company_id = $this->post('bodycar_last_company_id');// شرکت قبلی

                                    $query11 = "select * from bodycar_price_thirdparty_tb,bodycar_discnt_thirdparty_tb where
	  bodycar_discnt_thirdparty_id=bodycar_price_thirdparty_bodycar_discnt_thirdparty_id
	  AND bodycar_price_thirdparty_bodycar_price_id=" . $bodycar_price['bodycar_price_id'] . "
	AND bodycar_discnt_thirdparty_id=" . $bodycar_discnt_thirdparty_id . "";
                                    $result11 = $this->B_db->run_query($query11);
                                    $bodycar_discnt_thirdparty = $result11[0];
                                    $num11 = count($result11[0]);
                                    if ($num11 > 0) {
                                        //****************************************************************
                                        if($bodycar_price['bodycar_price_together_disc']==0) {// اگر تخفیف عدم خسارت ثالث و بدنه همزمان امکان زیر نیود
                                            if( $disc == 0) {// اگر تخفیف بدنه محاسبه نشده بود

                                                if($bodycar_price['bodycar_price_robonbaseprice']==0) {// اگر تخفیف ثالث در صورتی ارائه میگردد که شرکت بیمه ثالث با همین شرکت نرخ دهنده یکی باشد
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
                                            if($bodycar_price['bodycar_price_robonbaseprice']==0) {// اگر تخفیف ثالث در صورتی ارائه میگردد که شرکت بیمه ثالث با همین شرکت نرخ دهنده یکی باشد
                                                $bodycar_price_thirdparty_percent=  $bodycar_discnt_thirdparty['bodycar_price_thirdparty_percent'] ;
                                            }else if($bodycar_price['bodycar_price_robonbaseprice']==1 && $row['company_id']==$bodycar_last_company_id ) {
                                                $bodycar_price_thirdparty_percent=  $bodycar_discnt_thirdparty['bodycar_price_thirdparty_percent'] ;
                                            }
                                            if ($bodycar_price_thirdparty_percent + $disc > $bodycar_price_max_disc2) {// این شرط در همه تخفیف ها چک میشود که تخفیف قبلی به علاوه تخفیف جدید از حد بالای تخفیف ها بالاتر نرود
                                                $bodycar_price_thirdparty_percent = $bodycar_price_max_disc2 - $disc;
                                            }

                                            $price = $price - ($price * $bodycar_price_thirdparty_percent / 100);
                                            $disc = $disc + $bodycar_price_thirdparty_percent;
                                            //****************************************************************

                                        }
                                        //****************************************************************

                                    }
                                }

                                if ($bodycar_not_used == 'true' && $disc < $bodycar_price_max_disc2) {// ماشین صفر کیلومتر و تخفیف از حد بالا پایین تر باشد
                                    $disc_new_percent = $bodycar_price['bodycar_price_new_percent'];// نرخ صفر کیلومتر
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
                                            if ($bodycar_yearofcons_id >= $row80['bodycar_disc_slideold_min'] && $bodycar_yearofcons_id< $row80['bodycar_disc_slideold_max']) {// جستجوی سن خودرو در بازه های سال ساخت
                                                $bodycar_disc_slideold_percent = $row80['bodycar_disc_slideold_percent'];
                                            }

                                        }
                                    }

                                    if ($bodycar_disc_slideold_percent>0&&($bodycar_disc_slideold_percent + $disc) > $bodycar_price_max_disc2) {// این شرط در همه تخفیف ها چک میشود که تخفیف قبلی به علاوه تخفیف جدید از حد بالای تخفیف ها بالاتر نرود
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

                                    if ($bodycar_price_discntlife_percent>0&&($bodycar_price_discntlife_percent + $disc) > $bodycar_price_max_disc2) {// این شرط در همه تخفیف ها چک میشود که تخفیف قبلی به علاوه تخفیف جدید از حد بالای تخفیف ها بالاتر نرود
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

                                    if ($bodycar_price_accbank_percent>0&&($bodycar_price_accbank_percent + $disc) > $bodycar_price_max_disc2) {// این شرط در همه تخفیف ها چک میشود که تخفیف قبلی به علاوه تخفیف جدید از حد بالای تخفیف ها بالاتر نرود
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

                                    if ($bodycar_price_another_percent>0&&($bodycar_price_another_percent + $disc) > $bodycar_price_max_disc2) {// این شرط در همه تخفیف ها چک میشود که تخفیف قبلی به علاوه تخفیف جدید از حد بالای تخفیف ها بالاتر نرود
                                        $bodycar_price_another_percent = $bodycar_price_max_disc2 - $disc;
                                    }

                                    $price = $price - ($price * $bodycar_price_another_percent / 100);
                                    $disc = $disc + $bodycar_price_another_percent;
                                }


                                if ($disc < $bodycar_price_max_disc2) {//چک کردن  اینکه اگر حداقل تخفیف  در نظر پرفته شده در بازه ها قیمتی خودرو به کاربر نداده شده اعمال شود
                                    //$bodycar_price_min_disc=$bodycar_price['bodycar_price_min_disc'];
                                    if ($bodycar_price_min_disc2 + $disc > $bodycar_price_max_disc2) {
                                        $bodycar_price_min_disc2 = $bodycar_price_max_disc2 - $disc;
                                    }

                                    $price = $price - ($price * $bodycar_price_min_disc2 / 100);
                                    $disc = $disc + $bodycar_price_min_disc2;
                                }

                                if ($disc < $bodycar_price_max_disc2) {//چک کردن  اینکه اگر حداقل تخفیف  در نظر پرفته شده در جدول اصلی قیمت به کاربر نداده شده اعمال شود
                                    $bodycar_price_min_disc=$bodycar_price['bodycar_price_min_disc'];
                                    if ($bodycar_price_min_disc + $disc > $bodycar_price_max_disc2) {
                                        $bodycar_price_min_disc = $bodycar_price_max_disc2 - $disc;
                                    }

                                    $price = $price - ($price * $bodycar_price_min_disc / 100);
                                    $disc = $disc + $bodycar_price_min_disc;
                                }



                                if ($bodycar_cash == 'true'&&$bodycar_price['bodycar_price_stairs']!=0) {// اگر پرداخت کاربر نقدی بود و تخفیف نقدی به علاوه تخفیف های قبلی  باید در حداکثر  تخفیف بالاتر نباشد
                                    $bodycar_price_chash = $bodycar_price['bodycar_price_chash'];
                                    if ($bodycar_price_chash + $disc > $bodycar_price_max_disc2) {
                                        $bodycar_price_chash = $bodycar_price_max_disc2 - $disc;
                                    }

                                    $price = $price - ($price * $bodycar_price_chash / 100);
                                    $disc = $disc + $bodycar_price_chash;

                                }else
                                    if ($bodycar_cash == 'true'&&$bodycar_price['bodycar_price_stairs']==0) {// اگر پرداخت کاربر نقدی بود
                                        $price = $price - ($price * $bodycar_price['bodycar_price_chash'] / 100);
                                        $disc = $disc + $bodycar_price['bodycar_price_chash'];
                                    }
//پایان تخفیف
                                //*******************************************************************************************************
                                if (isset($_REQUEST['bodycar_coverage_id'])) {// محاسبه مجدد پوشش ها که اگر پوششی به قیمت نهایی بیمه نامه اعمال میشود محاسبه گردد
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
                                            if($bodycar_price_covarage['bodycar_price_covarage_calmode_id']==2) {// نوع سوم محاسبه پوشش ها
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


                    $price=$price*109/100;// اضافه کردن مالیات به ارزش  افزوده
                    $price_disc = 0;
                    //*******************************************************************************************
                    $query1 = "select * from managdiscount_tb,fieldinsurance_tb where
                    managdiscount_company_id='" . $row['company_id'] . "' AND
                    managdiscount_fieldinsurance_id=fieldinsurance_id AND fieldinsurance='" . $fieldinsurance . "'
                     AND (managdiscount_date_start='' OR managdiscount_date_start > now())
                     AND (managdiscount_date_end='' OR managdiscount_date_end< now())
                     AND managdiscount_max_all>(SELECT COALESCE(SUM(`managdiscount_use_amount`),0) FROM managdiscount_use_tb WHERE managdiscount_mngdiscnt_id=managdiscount_tb.managdiscount_id)
                     AND managdiscount_deactive=0";
                    $result1 = $this->B_db->run_query($query1);
                    if (!empty($result1)) {
                        $managdiscount = $result1[0];
                        if ($managdiscount['managdiscounts_mode'] == 'fix') {
                            $price_disc = $price - $managdiscount['managdiscount_amount'];
                            $record['managdiscount_desc'] = $managdiscount['managdiscount_desc'];
                            $record['managdiscount_id'] = $managdiscount['managdiscount_id'];
                            $record['managdiscount_amount'] = $managdiscount['managdiscount_amount'];


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
                    $request_description = 'بیمه نامه در رشته ' . $row['fieldinsurance_fa'];
                    $record['request_description'] = $request_description;
                    $record['tip'] ='';
                    if ($price_disc > 0) {
                        $record['price'] = intval($price_disc);
                        $record['price_disc'] = intval($price);
                    } else {
                        $record['price'] = intval($price);
                    }

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
AND (instalment_date_start='' OR instalment_date_start < now()) AND  (instalment_date_end='' OR instalment_date_end > now())
";
                    $result2 = $this->B_db->run_query($query2);
                    if (!empty($result2) && $bodycar_cash != 'true') {
                        $num = count($result2[0]);
                        if ($num > 0) {
                            $instalment = $result2[0];
                            $record['instalment_desc'] = $instalment['instalment_desc'];
                            $query3 = "select * from instalment_conditions_tb where instalment_conditions_instalment_id=" . $instalment['instalment_id'] . " ORDER BY instalment_conditions_mode_id DESC";
                            $result3 = $this->B_db->run_query($query3);
                            $output3 = array();
                            if ($price_disc == 0) {
                                $price_instalment = $price;
                            } else {
                                $price_instalment = $price_disc;
                            }
                            $tprice_instalment = $price_instalment;
                            foreach ($result3 as $row3) {
                                //$record3['instalment_conditions_id']=$row3['instalment_conditions_id'];
                                // $record3['instalment_conditions_instalment_id']=$row3['instalment_conditions_instalment_id'];
                                if ($row3['instalment_conditions_mode_id'] == 1) {
                                    if ($instalment['instalment_round_id'] == '1'||$instalment['instalment_round_id'] == 1) {
                                        $record3['instalment_conditions_amount'] = round($price_instalment * $row3['instalment_conditions_percent'] / 100 );
                                        $tprice_instalment -= round($price_instalment * $row3['instalment_conditions_percent'] / 100);
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
                            $record['instalment_conditions'] = $output3;

                        }
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
            }
        }
    }
}
