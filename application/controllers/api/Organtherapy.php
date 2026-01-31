<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//require APPPATH . '/libraries/REST_Controller.php';

// use namespace
use Restserver\Libraries\REST_Controller;

/**organ_contract_id
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
class Organtherapy extends REST_Controller
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
        $this->load->helper('my_helper');
        if (isset($this->input->request_headers()['Authorization'])) $organ_token_str = $this->input->request_headers()['Authorization'];
        $this->load->model('B_organ');
        $this->load->model('B_user');
        $this->load->model('B_db');

        $this->load->helper('my_helper');
        $this->load->helper('time_helper');
        $command = $this->post("command");



        $organtoken = checkorgantoken($organ_token_str);
        if ($this->B_user->checkrequestip('organreport', $command, get_client_ip(),50,50)) {
            if ($command == "get_user_therapy")
            {
                $organ_user_therapy_name=$this->post('organ_user_therapy_name') ;
                $organ_user_therapy_family=$this->post('organ_user_therapy_family') ;
                $user_mobile=$this->post('user_mobile') ;
                $organ_user_therapy_national_code=$this->post('organ_user_therapy_national_code') ;
                $organ_user_therapy_personal_code=$this->post('organ_user_therapy_personal_code') ;
                $organ_user_therapy_organ_therapycontract_id = $this->post('organ_user_therapy_organ_therapycontract_id');
                $organ_user_therapy_kind_id = $this->post('organ_user_therapy_kind_id');
                $organ_user_therapy_bimeno = $this->post('organ_user_therapy_bimeno');
                $organ_user_therapy_idcardno = $this->post('organ_user_therapy_idcardno');
                $organ_user_therapy_main_national_code = $this->post('organ_user_therapy_main_national_code');
                $limit = $this->post("limit");
                $offset = $this->post("offset");
                $search_mode = $this->post("search_mode");
                if($organtoken[0]=='ok')
                {
                    $organ_id=$organtoken[1];
                    $filter = "";
                    if($search_mode ==1){
                        if($organ_user_therapy_name !='')
                            $filter .= " And organ_user_therapy_name like   '%".$organ_user_therapy_name."%'   ESCAPE '!'";
                        if($organ_user_therapy_family !='')
                            $filter .= " And organ_user_therapy_family like '%".$organ_user_therapy_family."%' ESCAPE '!'";
                        if($user_mobile !='')
                            $filter .= " And user_mobile=".$user_mobile;
                        if($organ_user_therapy_national_code !='')
                            $filter .= " And organ_user_therapy_national_code=".$organ_user_therapy_national_code;
                        if($organ_user_therapy_personal_code !='')
                            $filter .= " And organ_user_therapy_personal_code='".$organ_user_therapy_personal_code."'";
                        if($organ_user_therapy_organ_therapycontract_id !='')
                            $filter .= " And organ_user_therapy_organ_therapycontract_id=".$organ_user_therapy_organ_therapycontract_id;
                        if($organ_user_therapy_kind_id !='')
                            $filter .= " And organ_user_therapy_kind_id=".$organ_user_therapy_kind_id;
                        if($organ_user_therapy_bimeno !='')
                            $filter .= " And organ_user_therapy_bimeno=".$organ_user_therapy_bimeno;
                        if($organ_user_therapy_idcardno !='')
                            $filter .= " And organ_user_therapy_idcardno='".$organ_user_therapy_idcardno."'";
                        if($organ_user_therapy_main_national_code !='')
                            $filter .= " And organ_user_therapy_main_national_code='".$organ_user_therapy_main_national_code."'";
                    }

                    $limit_state ="";
                    if($limit!="" & $offset!="")
                        $limit_state = "LIMIT ".$offset.",".$limit;

                    $query_body = "
                    FROM organ_user_therapy_tb,user_tb,organ_therapycontract_tb
WHERE 
  organ_user_therapy_main_user_id=user_id
AND organ_user_therapy_organ_therapycontract_id=organ_therapycontract_id
AND organ_user_therapy_organ_id=$organ_id";
                    $query="select * ".$query_body." ".$filter."  ".$limit_state;
                    $count_query="select count(*) as count ".$query_body." ".$filter;
                    $result = $this->B_db->run_query($query);
                    $print = $this->post('print');
                    if($print==126)
                    {
                        echo $_SERVER['SERVER_ADDR'].'@';
                        echo $_SERVER['SERVER_PORT'];
                        echo $this->db->last_query();
                    }

                    $count  = $this->B_db->run_query($count_query);

                    if (!empty($result)) {
                        $i = 0;
                       // foreach ($result as $row) {
                            $result[$i]['count'] =$count[0]['count'];
//                            $result1 = $this->B_organ->get_organ_therapycontract_conditions_contract($row['organ_therapycontract_id']);
//                            if($result1==null){
//                                $result[$i]['conditions'] = '';
//                            }else{
//                                //***************************************************************
//                                $i2 = 0;
//                                foreach ($result1 as $row2) {
//
//                                    $res = $this->B_organ->get_covarage_used($row['organ_user_therapy_id'],$row2['organ_therapycontract_conditions_covarage_id']);
//                                    if($res==null){
//                                        $result1[$i2]['sumprice'] = 0;
//                                    }else{
//                                        $result1[$i2]['sumprice'] = $res['sumprice'];
//                                    }
//
//
//                                    $i2++;
//                                }
//                                //***************************************************************
//                                $result[$i]['conditions'] = $result1;
//
//                            }


                      //      $i++;
                       // }
                        echo json_encode(array('result' => "ok"
                        , "data" => $result
                        , "count" => $count
                        , 'desc' => 'قراردادها یافت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    }else {
                        echo json_encode(array('result' => "ok"
                        , "data" => []
                        , 'desc' => 'قراردادی یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }

                } else {
                    echo json_encode(array('result' => $organtoken[0]
                    , "data" => $organtoken[1]
                    , 'desc' => $organtoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }
            else
                if ($command=="modify_user_therapy")
                {
                    $organ_user_therapy_id=$this->post('organ_user_therapy_id');
                    if($organtoken[0]=='ok')
                    {
                        $query="UPDATE organ_user_therapy_tb SET ";
                        if(isset($_REQUEST['organ_user_therapy_bank_id'])){
                            $organ_user_therapy_bank_id=$this->post('organ_user_therapy_bank_id');
                            $query.="organ_user_therapy_bank_id=".$organ_user_therapy_bank_id." ";
                        }

                        if(isset($_REQUEST['organ_user_therapy_accno'])&&isset($_REQUEST['organ_user_therapy_bank_id'])){ $query.=",";}
                        if(isset($_REQUEST['organ_user_therapy_accno'])){
                            $organ_user_therapy_accno=$this->post('organ_user_therapy_accno');
                            $query.="organ_user_therapy_accno='".$organ_user_therapy_accno."' ";}

                        if(isset($_REQUEST['organ_user_therapy_cardno'])&&(isset($_REQUEST['organ_user_therapy_accno'])||isset($_REQUEST['organ_user_therapy_bank_id']))){ $query.=",";}
                        if(isset($_REQUEST['organ_user_therapy_cardno'])){
                            $organ_user_therapy_cardno=$this->post('organ_user_therapy_cardno');
                            $query.="organ_user_therapy_cardno='".$organ_user_therapy_cardno."' ";
                        }

                        if(isset($_REQUEST['organ_user_therapy_national_code'])&&(isset($_REQUEST['organ_user_therapy_accno'])||isset($_REQUEST['organ_user_therapy_cardno'])||isset($_REQUEST['organ_user_therapy_bank_id']))){ $query.=",";}
                        if(isset($_REQUEST['organ_user_therapy_national_code'])){
                            $organ_user_therapy_national_code=$this->post('organ_user_therapy_national_code');
                            $query.="organ_user_therapy_national_code='".$organ_user_therapy_national_code."' ";}

                        if(isset($_REQUEST['organ_user_therapy_idcardno'])&&(isset($_REQUEST['organ_user_therapy_accno'])||isset($_REQUEST['organ_user_therapy_cardno'])||isset($_REQUEST['organ_user_therapy_bank_id'])||isset($_REQUEST['organ_user_therapy_national_code']))){ $query.=",";}
                        if(isset($_REQUEST['organ_user_therapy_idcardno'])){
                            $organ_user_therapy_idcardno=$this->post('organ_user_therapy_idcardno');
                            $query.="organ_user_therapy_idcardno='".$organ_user_therapy_idcardno."' ";}

                        if(isset($_REQUEST['organ_user_therapy_bimeno'])&&(isset($_REQUEST['organ_user_therapy_accno'])||isset($_REQUEST['organ_user_therapy_cardno'])||isset($_REQUEST['organ_user_therapy_bank_id'])||isset($_REQUEST['organ_user_therapy_national_code'])||isset($_REQUEST['organ_user_therapy_idcardno']))){$query.=",";}
                        if(isset($_REQUEST['organ_user_therapy_bimeno'])){
                            $organ_user_therapy_bimeno=$this->post('organ_user_therapy_bimeno');
                            $query.="organ_user_therapy_bimeno='".$organ_user_therapy_bimeno."' ";}

                        if(isset($_REQUEST['organ_user_therapy_shebano'])&&(isset($_REQUEST['organ_user_therapy_bimeno'])||isset($_REQUEST['organ_user_therapy_accno'])||isset($_REQUEST['organ_user_therapy_cardno'])||isset($_REQUEST['organ_user_therapy_bank_id'])||isset($_REQUEST['organ_user_therapy_national_code'])||isset($_REQUEST['organ_user_therapy_idcardno']))){$query.=",";}
                        if(isset($_REQUEST['organ_user_therapy_shebano'])){
                            $organ_user_therapy_shebano=$this->post('organ_user_therapy_shebano');
                            $query.="organ_user_therapy_shebano='".$organ_user_therapy_shebano."' ";}


                        $query.=" where organ_user_therapy_id=".$organ_user_therapy_id;
                        $result=$this->B_db->run_query_put($query);
                        if($result){
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>''
                            ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else {
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>""
                            ,'desc'=>'تغییرات انجام نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                    } else {
                        echo json_encode(array('result' => $organtoken[0]
                        , "data" => $organtoken[1]
                        , 'desc' => $organtoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                }

        }
    }
}