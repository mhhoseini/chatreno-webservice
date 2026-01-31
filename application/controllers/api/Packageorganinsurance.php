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
class Packageorganinsurance extends REST_Controller {

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
        $this->load->model('B_db');
        $this->load->model('B_agent');
        $this->load->helper('my_helper');
        $this->load->model('B_organ');
        $command = $this->post("command");

            if ($command=="get_packageinsurance")
            {
                $usertoken=checkusertoken($user_token_str);
                if($usertoken[0]=='ok')
                {
//************************************************************************;****************************************

                    $query="select * from packageinsurance_tb,company_tb,fieldinsurance_tb,organ_tb,organ_user_tb,organ_contract_tb where company_id=organ_contract_company_id AND company_id=packageinsurance_company_id AND fieldinsurance_id=packageinsurance_fieldinsurance_id AND organ_contract_fieldinsuranc_id =fieldinsurance_id AND organ_contract_organ_id=organ_user_organ_id AND organ_user_organ_id=organ_id AND `organ_user_confirm_id` IN( select MAX(organ_user_confirm_id) from packageinsurance_tb,company_tb,fieldinsurance_tb,organ_tb,organ_user_tb,organ_contract_tb where company_id=organ_contract_company_id AND company_id=packageinsurance_company_id AND fieldinsurance_id=packageinsurance_fieldinsurance_id AND organ_contract_fieldinsuranc_id =fieldinsurance_id AND organ_contract_organ_id=organ_user_organ_id AND organ_user_organ_id=organ_id AND organ_user_user_id=".$usertoken[1]." ) AND organ_user_user_id=".$usertoken[1]."  ";
                    $query.=" ORDER BY packageinsurance_id ASC";

                    $result = $this->B_db->run_query($query);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record=array();
                        $record['company_name']=$row['company_name'];
                        $record['company_logo_url']=IMGADD.$row['company_logo_url'];
                        $record['contract_id']=IMGADD.$row['contract_id'];

                        if($row['organ_logo']!=''){
                            $result1= $this->B_db->get_image($row['organ_logo']);
                            $image=$result1[0];
                            if($image['image_url']){$imageurl=$image['image_url'];}
                            $record['organ_logo']=$imageurl;
                        }
                        $record['organ_name']=$row['organ_name'];
                        $record['organ_contract_id']=$row['organ_contract_id'];

                        $record['company_levelof_prosperity']=$row['company_levelof_prosperity'];


                        $fieldinsurance=$row['fieldinsurance'];
                        $record['fieldinsurance']=$row['fieldinsurance'];
                        $record['fieldinsurance_fa']=$row['fieldinsurance_fa'];
                        $record['fieldinsurance_logo_url']=IMGADD.$row['fieldinsurance_logo_url'];
س
                        $record['packageinsurance_id']=$row['packageinsurance_id'];
                        $record['packageinsurance_company_id']=$row['packageinsurance_company_id'];
                        $record['packageinsurance_fieldinsurance_id']=$row['packageinsurance_fieldinsurance_id'];
                        $record['packageinsurance_title']=$row['packageinsurance_title'];
                        $record['packageinsurance_disctitle']=$row['packageinsurance_disctitle'];

                        $price_disc=$row['packageinsurance_discamount'];
                        $price=$row['packageinsurance_amount'];

                        if($price_disc>0){
                            $record['price']=intval($price_disc);
                            $record['price_disc']=intval($price);
                        }else{
                            $record['price']=intval($price);
                        }



                        $result1 = $this->B_db->get_image($row['packageinsurance_logo']);
                        $imageurl = "";
                        $imageturl = "";
                        if (!empty($result1)) {
                            $image = $result1[0];
                            if ($image['image_url']) {
                                $imageurl =  $image['image_url'];
                                $imageturl =  $image['image_tumb_url'];
                            }
                        }
                        $record['packageinsurance_t_logo']=$imageturl;
                        $record['packageinsurance_logo']=$imageurl;
                        $record['packageinsurance_desc']=$row['packageinsurance_desc'];
                        $record['packageinsurance_date_start']=$row['packageinsurance_date_start'];
                        $record['packageinsurance_date_end']=$row['packageinsurance_date_end'];
                        $record['packageinsurance_coverage']=$row['packageinsurance_coverage'];
                        $record['packageinsurance_extracoverage']=$row['packageinsurance_extracoverage'];
                        $record['packageinsurance_deactive']=$row['packageinsurance_deactive'];


                        //*****************************************************************************************

                        $arrinstalmentpermount= $this->B_organ->get_instalmentnopass_permonth($usertoken[1],$row['organ_id']);

                        $count_query="select a.organ_user_commitment_amount
                    FROM organ_user_tb a
                    INNER JOIN (
                       SELECT organ_user_user_id, MAX(organ_user_confirm_id) AS max_confirm_id
                    FROM organ_user_tb
                    WHERE organ_user_organ_id=".$row['organ_id']." and organ_user_confirm_id<=100000000000 
                    GROUP by organ_user_user_id
                    ) b ON a.organ_user_confirm_id = b.max_confirm_id AND a.organ_user_user_id = b.organ_user_user_id AND a.organ_user_organ_id=".$row['organ_id']."
                    INNER JOIN user_tb c
                    ON c.user_id=a.organ_user_user_id AND a.organ_user_user_id=".$usertoken[1]."
                    INNER JOIN organ_confirm_tb d
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

                                }else if($row['organ_contract_instalment_round_id']==1){
                                    if($row3['instalment_conditions_date']!=0){
                                        $instalment_conditions_amount=round($price_instalment*$row3['instalment_conditions_percent']/100, 0, PHP_ROUND_HALF_DOWN);
                                        $tprice_instalment-=round($price_instalment*$row3['instalment_conditions_percent']/100, 0, PHP_ROUND_HALF_DOWN);
                                    }else{
                                        $instalment_conditions_amount=intval($tprice_instalment);
                                    }
                                    $record3['instalment_conditions_amount']=$instalment_conditions_amount;
                                }else if($row['organ_contract_instalment_round_id']==2){
                                    if($row3['instalment_conditions_date']!=($num-1)){
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

                        $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'مشحصات تخفیف مدیریتی با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************
                }else{
                    echo json_encode(array('result'=>$usertoken[0]
                    ,"data"=>$usertoken[1]
                    ,'desc'=>$usertoken[2]));
                }


            }






                        }
}