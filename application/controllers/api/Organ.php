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
class Organ extends REST_Controller {

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
        $this->load->model('B_organ');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('organ', $command, get_client_ip(),50,50)) {
            if ($command == "add_organ") {
                $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'organ');
                $organ_name = $this->post('organ_name');
                $organ_username = $this->post('organ_username');
                $organ_pass = $this->post('organ_pass');
                $organ_tell = $this->post('organ_tell');
                $organ_agent = $this->post('organ_agent');//gender
                $organ_agentmobile = $this->post('organ_agentmobile');//mobile
                $organ_managemobile = $this->post('organ_managemobile');//mobile
                $organ_address = $this->post('organ_address');
                $organ_logo = $this->post('organ_logo');//long
                if ($employeetoken[0] == 'ok') {
                    $result = $this->B_organ->get_organ_by($organ_name);
                    if (empty($result)) {
                        $organ_id = $this->B_organ->add_organ($organ_name, $organ_username, $organ_agent, $organ_agentmobile, $organ_pass, $organ_tell, $organ_address, $organ_logo, $organ_managemobile);
                        echo json_encode(array('result' => "ok"
                        , "data" => array('organ_id' => $organ_id)
                        , 'desc' => 'ارگان مورد نظر اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    } else {
                        $carmode = $result[0];
                        echo json_encode(array('result' => "error"
                        , "data" => array('organ_id' => $carmode['organ_id'])
                        , 'desc' => 'نام ارگان مورد نظر تکراری است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                } else {
                    echo json_encode(array('result' => $employeetoken[0]
                    , "data" => $employeetoken[1]
                    , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            } else
                if ($command == "get_organ") {
                $employeetoken = checkpermissionemployeetoken($employee_token_str, 'view', 'organ');
                if ($employeetoken[0] == 'ok') {
                    $result = $this->B_organ->get_organ();
                    if (!empty($result)) {
                        $i = 0;
                        foreach ($result as $row) {
                            $res = $this->db->query("Select * from instalment_conditions_tb where  instalment_condition_contract_id=" . $row['organ_contract_id']);
                            $result[$i]['organ_contract_instalment'] = $res;

                            if ($row['organ_logo'] != '') {
                                $result1 = $this->B_db->get_image($row['organ_logo']);
                                $image = $result1[0];
                                if ($image['image_url']) {
                                    $imageurl =  $image['image_url'];
                                }
                                $result[$i]['organ_logo'] = $imageurl;
                            }
                            $i++;

                        }
                        echo json_encode(array('result' => "ok"
                        , "data" => $result
                        , 'desc' => 'ارگان مورد نظر یافت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    } else {
                        echo json_encode(array('result' => "error"
                        , "data" => ""
                        , 'desc' => 'ارگان مورد نظر یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }

                } else {
                    echo json_encode(array('result' => $employeetoken[0]
                    , "data" => $employeetoken[1]
                    , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            } else
                if ($command == "delete_organ") {
                    $output = array();
                    $organ_id = $this->post('organ_id');
                    $employeetoken = checkpermissionemployeetoken($employee_token_str, 'delete', 'organ');
                    if ($employeetoken[0] == 'ok') {
                        $user_id = $employeetoken[1];
                        $result = $this->B_organ->del_organ($organ_id);
                        if ($result) {
                            echo json_encode(array('result' => "ok"
                            , "data" => $output
                            , 'desc' => 'ارگان مورد نظر حذف شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        } else {
                            echo json_encode(array('result' => "error"
                            , "data" => $output
                            , 'desc' => 'ارگان مورد نظر حذف نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    } else {
                        echo json_encode(array('result' => $employeetoken[0]
                        , "data" => $employeetoken[1]
                        , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                } else
                    if ($command == "modify_organ") {
                        $organ_id = $this->post('organ_id');
                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'modify', 'organ');
                        if ($employeetoken[0] == 'ok') {
                            $query = "UPDATE organ_tb SET ";

                            if (isset($_REQUEST['organ_name'])) {
                                $organ_name = $this->post('organ_name');
                                $query .= "organ_name='" . $organ_name . "'";
                            }

                            if (isset($_REQUEST['organ_username']) && (isset($_REQUEST['organ_name']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['organ_username'])) {
                                $organ_username = $this->post('organ_username');
                                $query .= "organ_username='" . $organ_username . "'";
                            }

                            if (isset($_REQUEST['organ_agent']) && (isset($_REQUEST['organ_name']) || isset($_REQUEST['organ_username']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['organ_agent'])) {
                                $organ_agent = $this->post('organ_agent');
                                $query .= "organ_agent='" . $organ_agent . "'";
                            }

                            if (isset($_REQUEST['organ_agentmobile']) && (isset($_REQUEST['organ_name']) || isset($_REQUEST['organ_username']) || isset($_REQUEST['organ_agent']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['organ_agentmobile'])) {
                                $organ_agentmobile = $this->post('organ_agentmobile');
                                $query .= "organ_agentmobile='" . $organ_agentmobile . "'";
                            }

                            if (isset($_REQUEST['organ_pass']) && (isset($_REQUEST['organ_name']) || isset($_REQUEST['organ_username']) || isset($_REQUEST['organ_agent']) || isset($_REQUEST['organ_agentmobile']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['organ_pass'])) {
                                $organ_pass = $this->post('organ_pass');
                                $query .= "organ_pass='" . $organ_pass . "' ";
                            }

                            if (isset($_REQUEST['organ_tell']) && (isset($_REQUEST['organ_pass']) || isset($_REQUEST['organ_name']) || isset($_REQUEST['organ_username']) || isset($_REQUEST['organ_agent']) || isset($_REQUEST['organ_agentmobile']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['organ_tell'])) {
                                $organ_tell = $this->post('organ_tell');
                                $query .= "organ_tell='" . $organ_tell . "' ";
                            }

                            if (isset($_REQUEST['organ_address']) && (isset($_REQUEST['organ_tell']) || isset($_REQUEST['organ_pass']) || isset($_REQUEST['organ_name']) || isset($_REQUEST['organ_username']) || isset($_REQUEST['organ_agent']) || isset($_REQUEST['organ_agentmobile']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['organ_address'])) {
                                $organ_address = $this->post('organ_address');
                                $query .= "organ_address='" . $organ_address . "' ";
                            }

                            if (isset($_REQUEST['organ_deactive']) && (isset($_REQUEST['organ_address']) || isset($_REQUEST['organ_tell']) || isset($_REQUEST['organ_pass']) || isset($_REQUEST['organ_name']) || isset($_REQUEST['organ_username']) || isset($_REQUEST['organ_agent']) || isset($_REQUEST['organ_agentmobile']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['organ_deactive'])) {
                                $organ_deactive = $this->post('organ_deactive');
                                $query .= "organ_deactive=" . $organ_deactive . " ";
                            }

                            if (isset($_REQUEST['organ_public']) && (isset($_REQUEST['organ_deactive'])||isset($_REQUEST['organ_address']) || isset($_REQUEST['organ_tell']) || isset($_REQUEST['organ_pass']) || isset($_REQUEST['organ_name']) || isset($_REQUEST['organ_username']) || isset($_REQUEST['organ_agent']) || isset($_REQUEST['organ_agentmobile']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['organ_public'])) {
                                $organ_public = $this->post('organ_public');
                                $query .= "organ_public=" . $organ_public . " ";
                            }

                            if (isset($_REQUEST['organ_logo']) && (isset($_REQUEST['organ_public']) ||isset($_REQUEST['organ_deactive']) || isset($_REQUEST['organ_address']) || isset($_REQUEST['organ_tell']) || isset($_REQUEST['organ_pass']) || isset($_REQUEST['organ_name']) || isset($_REQUEST['organ_username']) || isset($_REQUEST['organ_agent']) || isset($_REQUEST['organ_agentmobile']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['organ_logo'])) {
                                $organ_logo = $this->post('organ_logo');
                                $query .= "organ_logo='" . $organ_logo . "' ";
                            }


                            $query .= " where organ_id=" . $organ_id;
                            $result = $this->B_db->run_query_put($query);

                            if ($result) {
                                echo json_encode(array('result' => "ok"
                                , "data" => ""
                                , 'desc' => ' تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            } else {
                                echo json_encode(array('result' => "ok"
                                , "data" => ""
                                , 'desc' => 'تغییرات انجام نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }

                        } else {
                            echo json_encode(array('result' => $employeetoken[0]
                            , "data" => $employeetoken[1]
                            , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    } else
                        if ($command == "add_contract") {
                        $organ_contract_fieldinsuranc_id = $this->post('organ_contract_fieldinsuranc_id');
                        $organ_contract_num = $this->post('organ_contract_num');
                        $organ_contract_date = $this->post('organ_contract_date');
                        $organ_contract_date_start = $this->post('organ_contract_date_start');
                        $organ_contract_date_end = $this->post('organ_contract_date_end');//gender
                        $organ_contract_clearing_day = $this->post('organ_contract_clearing_day');//mobile
                        $organ_contract_discount_percent = $this->post('organ_contract_discount_percent');
                        $organ_contract_discount_amount = $this->post('organ_contract_discount_amount');
                        $organ_contract_editable = $this->post('organ_contract_editable');
                        $organ_contract_discount_max_amount = $this->post('organ_contract_discount_max_amount');
                        $organ_contract_deactive = $this->post('organ_contract_deactive');
                        $organ_contract_company_id = $this->post('organ_contract_company_id');
                        $organ_contract_organ_id = $this->post('organ_contract_organ_id');
                        $organ_contract_file_id = $this->post('organ_contract_file_id');
                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'organ');
                        if ($employeetoken[0] == 'ok') {
                            $organ_contract_employee_id = $employeetoken[1];
                            $organ_id = $this->B_organ->add_organ_contract($organ_contract_fieldinsuranc_id, $organ_contract_num, $organ_contract_date, $organ_contract_date_start, $organ_contract_date_end, $organ_contract_clearing_day, $organ_contract_discount_percent, $organ_contract_discount_amount, $organ_contract_editable, $organ_contract_discount_max_amount, $organ_contract_deactive, $organ_contract_company_id, $organ_contract_organ_id, $organ_contract_file_id, $organ_contract_employee_id);
                            echo json_encode(array('result' => "ok"
                            , "data" => array('organ_id' => $organ_id)
                            , 'desc' => 'قرارداد ارگان مورد نظر اضافه گردید!'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        } else {
                            echo json_encode(array('result' => $employeetoken[0]
                            , "data" => $employeetoken[1]
                            , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    }else
                         if ($command == "get_contract") {
                $employeetoken = checkpermissionemployeetoken($employee_token_str, 'view', 'organ');
                if ($employeetoken[0] == 'ok') {
                    $result = $this->B_organ->get_contracts();
                    if (!empty($result)) {
                        $i = 0;
                        foreach ($result as $row) {
                            $res = $this->B_organ->get_company_name($row['organ_contract_company_id']);
                            $result[$i]['company_name'] = $res['company_name'];
                            $result[$i]['company_logo_url'] = IMGADD . $res['company_logo_url'];

                            $res1 = $this->B_organ->get_fieldinsurance_name($row['organ_contract_fieldinsuranc_id']);
                            $result[$i]['fieldinsurance_fa'] = $res1['fieldinsurance_fa'];
                            $result[$i]['fieldinsurance_logo_url'] = IMGADD . $res1['fieldinsurance_logo_url'];

                            if ($row['organ_logo'] != '') {
                                $result1 = $this->B_db->get_image($row['organ_logo']);
                                $image = $result1[0];
                                if ($image['image_url']) {
                                    $imageurl =  $image['image_url'];
                                }
                                $result[$i]['organ_logo'] = $imageurl;
                            }
                            $i++;
                        }
                        echo json_encode(array('result' => "ok"
                        , "data" => $result
                        , 'desc' => 'قراردادها یافت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    } else {
                        echo json_encode(array('result' => "error"
                        , "data" => ""
                        , 'desc' => 'قراردادی یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }

                } else {
                    echo json_encode(array('result' => $employeetoken[0]
                    , "data" => $employeetoken[1]
                    , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }else
                 if ($command == "modify_contract") {
                $organ_contract_id = $this->post('organ_contract_id');
                $employeetoken = checkpermissionemployeetoken($employee_token_str, 'modify', 'organ');
                if ($employeetoken[0] == 'ok') {
                    $query = "UPDATE organ_contract_tb SET ";

                    if (isset($_REQUEST['organ_contract_fieldinsuranc_id'])) {
                        $organ_contract_fieldinsuranc_id = $this->post('organ_contract_fieldinsuranc_id');
                        $query .= "organ_contract_fieldinsuranc_id=" . $organ_contract_fieldinsuranc_id . "";
                    }

                    if (isset($_REQUEST['organ_contract_num']) && (isset($_REQUEST['organ_contract_fieldinsuranc_id']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['organ_contract_num'])) {
                        $organ_contract_num = $this->post('organ_contract_num');
                        $query .= "organ_contract_num='" . $organ_contract_num . "'";
                    }

                    if (isset($_REQUEST['organ_contract_date']) && (isset($_REQUEST['organ_contract_fieldinsuranc_id']) || isset($_REQUEST['organ_contract_num']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['organ_contract_date'])) {
                        $organ_contract_date = $this->post('organ_contract_date');
                        $query .= "organ_contract_date='" . $organ_contract_date . "'";
                    }

                    if (isset($_REQUEST['organ_contract_discount_max_amount']) && (isset($_REQUEST['organ_contract_fieldinsuranc_id']) || isset($_REQUEST['organ_contract_num']) || isset($_REQUEST['organ_contract_date']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['organ_contract_discount_max_amount'])) {
                        $organ_contract_discount_max_amount = $this->post('organ_contract_discount_max_amount');
                        $query .= "organ_contract_discount_max_amount='" . $organ_contract_discount_max_amount . "'";
                    }

                    if (isset($_REQUEST['organ_contract_date_start']) && (isset($_REQUEST['organ_contract_fieldinsuranc_id']) || isset($_REQUEST['organ_contract_num']) || isset($_REQUEST['organ_contract_date']) || isset($_REQUEST['organ_contract_discount_max_amount']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['organ_contract_date_start'])) {
                        $organ_contract_date_start = $this->post('organ_contract_date_start');
                        $query .= "organ_contract_date_start='" . $organ_contract_date_start . "' ";
                    }

                    if (isset($_REQUEST['organ_contract_date_end']) && (isset($_REQUEST['organ_contract_date_start']) || isset($_REQUEST['organ_contract_fieldinsuranc_id']) || isset($_REQUEST['organ_contract_num']) || isset($_REQUEST['organ_contract_date']) || isset($_REQUEST['organ_contract_discount_max_amount']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['organ_contract_date_end'])) {
                        $organ_contract_date_end = $this->post('organ_contract_date_end');
                        $query .= "organ_contract_date_end='" . $organ_contract_date_end . "' ";
                    }

                    if (isset($_REQUEST['organ_contract_clearing_day']) && (isset($_REQUEST['organ_contract_date_end']) || isset($_REQUEST['organ_contract_date_start']) || isset($_REQUEST['organ_contract_fieldinsuranc_id']) || isset($_REQUEST['organ_contract_num']) || isset($_REQUEST['organ_contract_date']) || isset($_REQUEST['organ_contract_discount_max_amount']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['organ_contract_clearing_day'])) {
                        $organ_contract_clearing_day = $this->post('organ_contract_clearing_day');
                        $query .= "organ_contract_clearing_day=" . $organ_contract_clearing_day . " ";
                    }

                    if (isset($_REQUEST['organ_contract_discount_percent']) && (isset($_REQUEST['organ_contract_clearing_day']) || isset($_REQUEST['organ_contract_date_end']) || isset($_REQUEST['organ_contract_date_start']) || isset($_REQUEST['organ_contract_fieldinsuranc_id']) || isset($_REQUEST['organ_contract_num']) || isset($_REQUEST['organ_contract_date']) || isset($_REQUEST['organ_contract_discount_max_amount']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['organ_contract_discount_percent'])) {
                        $organ_contract_discount_percent = $this->post('organ_contract_discount_percent');
                        $query .= "organ_contract_discount_percent=" . $organ_contract_discount_percent . " ";
                    }

                    if (isset($_REQUEST['organ_contract_discount_amount']) && (isset($_REQUEST['organ_contract_discount_percent']) || isset($_REQUEST['organ_contract_clearing_day']) || isset($_REQUEST['organ_contract_date_end']) || isset($_REQUEST['organ_contract_date_start']) || isset($_REQUEST['organ_contract_fieldinsuranc_id']) || isset($_REQUEST['organ_contract_num']) || isset($_REQUEST['organ_contract_date']) || isset($_REQUEST['organ_contract_discount_max_amount']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['organ_contract_discount_amount'])) {
                        $organ_contract_discount_amount = $this->post('organ_contract_discount_amount');
                        $query .= "organ_contract_discount_amount=" . $organ_contract_discount_amount . " ";
                    }

                    if (isset($_REQUEST['$organ_contract_company_id']) && (isset($_REQUEST['organ_contract_discount_amount']) || isset($_REQUEST['organ_contract_discount_percent']) || isset($_REQUEST['organ_contract_clearing_day']) || isset($_REQUEST['organ_contract_date_end']) || isset($_REQUEST['organ_contract_date_start']) || isset($_REQUEST['organ_contract_fieldinsuranc_id']) || isset($_REQUEST['organ_contract_num']) || isset($_REQUEST['organ_contract_date']) || isset($_REQUEST['organ_contract_discount_max_amount']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['$organ_contract_company_id'])) {
                        $organ_contract_company_id = $this->post('$organ_contract_company_id');
                        $query .= "$organ_contract_company_id=" . $organ_contract_company_id . " ";
                    }

                    if (isset($_REQUEST['organ_contract_deactive']) && (isset($_REQUEST['$organ_contract_company_id']) || isset($_REQUEST['organ_contract_discount_amount']) || isset($_REQUEST['organ_contract_discount_percent']) || isset($_REQUEST['organ_contract_clearing_day']) || isset($_REQUEST['organ_contract_date_end']) || isset($_REQUEST['organ_contract_date_start']) || isset($_REQUEST['organ_contract_fieldinsuranc_id']) || isset($_REQUEST['organ_contract_num']) || isset($_REQUEST['organ_contract_date']) || isset($_REQUEST['organ_contract_discount_max_amount']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['organ_contract_deactive'])) {
                        $organ_contract_deactive = $this->post('organ_contract_deactive');
                        $query .= "organ_contract_deactive=" . $organ_contract_deactive . " ";
                    }


                    if (isset($_REQUEST['organ_contract_editable']) && (isset($_REQUEST['organ_contract_deactive']) || isset($_REQUEST['$organ_contract_company_id']) || isset($_REQUEST['organ_contract_discount_amount']) || isset($_REQUEST['organ_contract_discount_percent']) || isset($_REQUEST['organ_contract_clearing_day']) || isset($_REQUEST['organ_contract_date_end']) || isset($_REQUEST['organ_contract_date_start']) || isset($_REQUEST['organ_contract_fieldinsuranc_id']) || isset($_REQUEST['organ_contract_num']) || isset($_REQUEST['organ_contract_date']) || isset($_REQUEST['organ_contract_discount_max_amount']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['organ_contract_editable'])) {
                        $organ_contract_editable = $this->post('organ_contract_editable');
                        $query .= "organ_contract_editable=" . $organ_contract_editable . " ";
                    }

                    if (isset($_REQUEST['organ_contract_instalment_round_id']) && (isset($_REQUEST['organ_contract_editable'])||isset($_REQUEST['organ_contract_deactive']) || isset($_REQUEST['$organ_contract_company_id']) || isset($_REQUEST['organ_contract_discount_amount']) || isset($_REQUEST['organ_contract_discount_percent']) || isset($_REQUEST['organ_contract_clearing_day']) || isset($_REQUEST['organ_contract_date_end']) || isset($_REQUEST['organ_contract_date_start']) || isset($_REQUEST['organ_contract_fieldinsuranc_id']) || isset($_REQUEST['organ_contract_num']) || isset($_REQUEST['organ_contract_date']) || isset($_REQUEST['organ_contract_discount_max_amount']))) {
                        $query .= ",";
                    }
                    if (isset($_REQUEST['organ_contract_instalment_round_id'])) {
                        $organ_contract_instalment_round_id = $this->post('organ_contract_instalment_round_id');
                        $query .= "organ_contract_instalment_round_id=" . $organ_contract_instalment_round_id . " ";
                    }


                    $query .= " where organ_contract_id=" . $organ_contract_id;
                    $result = $this->B_db->run_query_put($query);

                    if ($result) {
                        echo json_encode(array('result' => "ok"
                        , "data" => ""
                        , 'desc' => ' تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    } else {
                        echo json_encode(array('result' => "ok"
                        , "data" => ""
                        , 'desc' => 'تغییرات انجام نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }

                } else {
                    echo json_encode(array('result' => $employeetoken[0]
                    , "data" => $employeetoken[1]
                    , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            } else
                if ($command == "delete_organ_contract") {
                    $organ_contract_id = $this->post('organ_contract_id');
                    $employeetoken = checkpermissionemployeetoken($employee_token_str, 'delete', 'organ');
                    if ($employeetoken[0] == 'ok') {
                        $query = " DELETE FROM organ_contract_tb where organ_contract_id=" . $organ_contract_id;
                        $result = $this->B_db->run_query_put($query);

                        if ($result) {
                            echo json_encode(array('result' => "ok"
                            , "data" => ""
                            , 'desc' => ' تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        } else {
                            echo json_encode(array('result' => "ok"
                            , "data" => ""
                            , 'desc' => 'تغییرات انجام نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    } else {
                        echo json_encode(array('result' => $employeetoken[0]
                        , "data" => $employeetoken[1]
                        , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                } else

                    if ($command == "add_therapycontract") {
                        $organ_therapycontract_state_id = $this->post('organ_therapycontract_state_id');
                        $organ_therapycontract_num = $this->post('organ_therapycontract_num');
                        $organ_therapycontract_date = $this->post('organ_therapycontract_date');
                        $organ_therapycontract_date_start = $this->post('organ_therapycontract_date_start');
                        $organ_therapycontract_date_end = $this->post('organ_therapycontract_date_end');//gender
                        $organ_therapycontract_clearing_day = $this->post('organ_therapycontract_clearing_day');//mobile
                        $organ_therapycontract_discount_percent = $this->post('organ_therapycontract_discount_percent');
                        $organ_therapycontract_discount_amount = $this->post('organ_therapycontract_discount_amount');
                        $organ_therapycontract_editable = $this->post('organ_therapycontract_editable');
                        $organ_therapycontract_discount_max_amount = $this->post('organ_therapycontract_discount_max_amount');
                        $organ_therapycontract_deactive = $this->post('organ_therapycontract_deactive');
                        $organ_therapycontract_company_id = $this->post('organ_therapycontract_company_id');
                        $organ_therapycontract_organ_id = $this->post('organ_therapycontract_organ_id');
                        $organ_therapycontract_file_id = $this->post('organ_therapycontract_file_id');
                        $organ_therapycontract_city_id = $this->post('organ_therapycontract_city_id');
                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'organ');
                        if ($employeetoken[0] == 'ok') {
                            $organ_therapycontract_employee_id = $employeetoken[1];
                            $organ_id = $this->B_organ->add_organ_therapycontract($organ_therapycontract_state_id, $organ_therapycontract_num, $organ_therapycontract_date, $organ_therapycontract_date_start, $organ_therapycontract_date_end, $organ_therapycontract_clearing_day, $organ_therapycontract_discount_percent, $organ_therapycontract_discount_amount, $organ_therapycontract_editable, $organ_therapycontract_discount_max_amount, $organ_therapycontract_deactive, $organ_therapycontract_company_id, $organ_therapycontract_organ_id, $organ_therapycontract_file_id, $organ_therapycontract_employee_id,$organ_therapycontract_city_id);
                            echo json_encode(array('result' => "ok"
                            , "data" => array('organ_id' => $organ_id)
                            , 'desc' => 'قرارداد ارگان مورد نظر اضافه گردید!'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        } else {
                            echo json_encode(array('result' => $employeetoken[0]
                            , "data" => $employeetoken[1]
                            , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    }else
                        if ($command == "get_therapycontract") {
                            $employeetoken = checkpermissionemployeetoken($employee_token_str, 'view', 'organ');
                            if ($employeetoken[0] == 'ok') {
                                $result = $this->B_organ->get_therapycontracts();
                                if (!empty($result)) {
                                    $i = 0;
                                    foreach ($result as $row) {
                                        $res = $this->B_organ->get_company_name($row['organ_therapycontract_company_id']);
                                        $result[$i]['company_name'] = $res['company_name'];
                                        $result[$i]['company_logo_url'] = IMGADD . $res['company_logo_url'];

                                        $sql = "SELECT * FROM state_tb where state_id=".$row['organ_therapycontract_state_id'];
                                        $res1=$this->B_db->run_query($sql)[0];
if($res1['state_name']){
                                        $result[$i]['state_name'] = $res1['state_name'];
    }else{
    $result[$i]['state_name'] =' همه استان ها';

}
                                        $sql = "SELECT * FROM city_tb where city_id=".$row['organ_therapycontract_city_id'];
                                        $res1=$this->B_db->run_query($sql)[0];
                                        if($res1['city_name']){
                                            $result[$i]['city_name'] = $res1['city_name'];
                                        }else{
                                            $result[$i]['city_name'] =' همه شهر ها';

                                        }

                                        if ($row['organ_logo'] != '') {
                                            $result1 = $this->B_db->get_image($row['organ_logo']);
                                            $image = $result1[0];
                                            if ($image['image_url']) {
                                                $imageurl =  $image['image_url'];
                                            }
                                            $result[$i]['organ_logo'] = $imageurl;
                                        }
                                        $i++;
                                    }
                                    echo json_encode(array('result' => "ok"
                                    , "data" => $result
                                    , 'desc' => 'قراردادها یافت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                } else {
                                    echo json_encode(array('result' => "error"
                                    , "data" => ""
                                    , 'desc' => 'قراردادی یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                }

                            } else {
                                echo json_encode(array('result' => $employeetoken[0]
                                , "data" => $employeetoken[1]
                                , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }
                        }else
                            if ($command == "modify_therapycontract") {
                                $organ_therapycontract_id = $this->post('organ_therapycontract_id');
                                $employeetoken = checkpermissionemployeetoken($employee_token_str, 'modify', 'organ');
                                if ($employeetoken[0] == 'ok') {
                                    $query = "UPDATE organ_therapycontract_tb SET ";

                                    if (isset($_REQUEST['organ_therapycontract_state_id'])) {
                                        $organ_therapycontract_state_id = $this->post('organ_therapycontract_state_id');
                                        $query .= "organ_therapycontract_state_id=" . $organ_therapycontract_state_id . "";
                                    }

                                    if (isset($_REQUEST['organ_therapycontract_num']) && (isset($_REQUEST['organ_therapycontract_state_id']))) {
                                        $query .= ",";
                                    }
                                    if (isset($_REQUEST['organ_therapycontract_num'])) {
                                        $organ_therapycontract_num = $this->post('organ_therapycontract_num');
                                        $query .= "organ_therapycontract_num='" . $organ_therapycontract_num . "'";
                                    }

                                    if (isset($_REQUEST['organ_therapycontract_date']) && (isset($_REQUEST['organ_therapycontract_state_id']) || isset($_REQUEST['organ_therapycontract_num']))) {
                                        $query .= ",";
                                    }
                                    if (isset($_REQUEST['organ_therapycontract_date'])) {
                                        $organ_therapycontract_date = $this->post('organ_therapycontract_date');
                                        $query .= "organ_therapycontract_date='" . $organ_therapycontract_date . "'";
                                    }

                                    if (isset($_REQUEST['organ_therapycontract_discount_max_amount']) && (isset($_REQUEST['organ_therapycontract_state_id']) || isset($_REQUEST['organ_therapycontract_num']) || isset($_REQUEST['organ_therapycontract_date']))) {
                                        $query .= ",";
                                    }
                                    if (isset($_REQUEST['organ_therapycontract_discount_max_amount'])) {
                                        $organ_therapycontract_discount_max_amount = $this->post('organ_therapycontract_discount_max_amount');
                                        $query .= "organ_therapycontract_discount_max_amount='" . $organ_therapycontract_discount_max_amount . "'";
                                    }

                                    if (isset($_REQUEST['organ_therapycontract_date_start']) && (isset($_REQUEST['organ_therapycontract_state_id']) || isset($_REQUEST['organ_therapycontract_num']) || isset($_REQUEST['organ_therapycontract_date']) || isset($_REQUEST['organ_therapycontract_discount_max_amount']))) {
                                        $query .= ",";
                                    }
                                    if (isset($_REQUEST['organ_therapycontract_date_start'])) {
                                        $organ_therapycontract_date_start = $this->post('organ_therapycontract_date_start');
                                        $query .= "organ_therapycontract_date_start='" . $organ_therapycontract_date_start . "' ";
                                    }

                                    if (isset($_REQUEST['organ_therapycontract_date_end']) && (isset($_REQUEST['organ_therapycontract_date_start']) || isset($_REQUEST['organ_therapycontract_state_id']) || isset($_REQUEST['organ_therapycontract_num']) || isset($_REQUEST['organ_therapycontract_date']) || isset($_REQUEST['organ_therapycontract_discount_max_amount']))) {
                                        $query .= ",";
                                    }
                                    if (isset($_REQUEST['organ_therapycontract_date_end'])) {
                                        $organ_therapycontract_date_end = $this->post('organ_therapycontract_date_end');
                                        $query .= "organ_therapycontract_date_end='" . $organ_therapycontract_date_end . "' ";
                                    }

                                    if (isset($_REQUEST['organ_therapycontract_clearing_day']) && (isset($_REQUEST['organ_therapycontract_date_end']) || isset($_REQUEST['organ_therapycontract_date_start']) || isset($_REQUEST['organ_therapycontract_state_id']) || isset($_REQUEST['organ_therapycontract_num']) || isset($_REQUEST['organ_therapycontract_date']) || isset($_REQUEST['organ_therapycontract_discount_max_amount']))) {
                                        $query .= ",";
                                    }
                                    if (isset($_REQUEST['organ_therapycontract_clearing_day'])) {
                                        $organ_therapycontract_clearing_day = $this->post('organ_therapycontract_clearing_day');
                                        $query .= "organ_therapycontract_clearing_day=" . $organ_therapycontract_clearing_day . " ";
                                    }

                                    if (isset($_REQUEST['organ_therapycontract_discount_percent']) && (isset($_REQUEST['organ_therapycontract_clearing_day']) || isset($_REQUEST['organ_therapycontract_date_end']) || isset($_REQUEST['organ_therapycontract_date_start']) || isset($_REQUEST['organ_therapycontract_state_id']) || isset($_REQUEST['organ_therapycontract_num']) || isset($_REQUEST['organ_therapycontract_date']) || isset($_REQUEST['organ_therapycontract_discount_max_amount']))) {
                                        $query .= ",";
                                    }
                                    if (isset($_REQUEST['organ_therapycontract_discount_percent'])) {
                                        $organ_therapycontract_discount_percent = $this->post('organ_therapycontract_discount_percent');
                                        $query .= "organ_therapycontract_discount_percent=" . $organ_therapycontract_discount_percent . " ";
                                    }

                                    if (isset($_REQUEST['organ_therapycontract_discount_amount']) && (isset($_REQUEST['organ_therapycontract_discount_percent']) || isset($_REQUEST['organ_therapycontract_clearing_day']) || isset($_REQUEST['organ_therapycontract_date_end']) || isset($_REQUEST['organ_therapycontract_date_start']) || isset($_REQUEST['organ_therapycontract_state_id']) || isset($_REQUEST['organ_therapycontract_num']) || isset($_REQUEST['organ_therapycontract_date']) || isset($_REQUEST['organ_therapycontract_discount_max_amount']))) {
                                        $query .= ",";
                                    }
                                    if (isset($_REQUEST['organ_therapycontract_discount_amount'])) {
                                        $organ_therapycontract_discount_amount = $this->post('organ_therapycontract_discount_amount');
                                        $query .= "organ_therapycontract_discount_amount=" . $organ_therapycontract_discount_amount . " ";
                                    }

                                    if (isset($_REQUEST['$organ_therapycontract_company_id']) && (isset($_REQUEST['organ_therapycontract_discount_amount']) || isset($_REQUEST['organ_therapycontract_discount_percent']) || isset($_REQUEST['organ_therapycontract_clearing_day']) || isset($_REQUEST['organ_therapycontract_date_end']) || isset($_REQUEST['organ_therapycontract_date_start']) || isset($_REQUEST['organ_therapycontract_state_id']) || isset($_REQUEST['organ_therapycontract_num']) || isset($_REQUEST['organ_therapycontract_date']) || isset($_REQUEST['organ_therapycontract_discount_max_amount']))) {
                                        $query .= ",";
                                    }
                                    if (isset($_REQUEST['$organ_therapycontract_company_id'])) {
                                        $organ_therapycontract_company_id = $this->post('$organ_therapycontract_company_id');
                                        $query .= "$organ_therapycontract_company_id=" . $organ_therapycontract_company_id . " ";
                                    }

                                    if (isset($_REQUEST['organ_therapycontract_deactive']) && (isset($_REQUEST['$organ_therapycontract_company_id']) || isset($_REQUEST['organ_therapycontract_discount_amount']) || isset($_REQUEST['organ_therapycontract_discount_percent']) || isset($_REQUEST['organ_therapycontract_clearing_day']) || isset($_REQUEST['organ_therapycontract_date_end']) || isset($_REQUEST['organ_therapycontract_date_start']) || isset($_REQUEST['organ_therapycontract_state_id']) || isset($_REQUEST['organ_therapycontract_num']) || isset($_REQUEST['organ_therapycontract_date']) || isset($_REQUEST['organ_therapycontract_discount_max_amount']))) {
                                        $query .= ",";
                                    }
                                    if (isset($_REQUEST['organ_therapycontract_deactive'])) {
                                        $organ_therapycontract_deactive = $this->post('organ_therapycontract_deactive');
                                        $query .= "organ_therapycontract_deactive=" . $organ_therapycontract_deactive . " ";
                                    }


                                    if (isset($_REQUEST['organ_therapycontract_editable']) && (isset($_REQUEST['organ_therapycontract_deactive']) || isset($_REQUEST['$organ_therapycontract_company_id']) || isset($_REQUEST['organ_therapycontract_discount_amount']) || isset($_REQUEST['organ_therapycontract_discount_percent']) || isset($_REQUEST['organ_therapycontract_clearing_day']) || isset($_REQUEST['organ_therapycontract_date_end']) || isset($_REQUEST['organ_therapycontract_date_start']) || isset($_REQUEST['organ_therapycontract_state_id']) || isset($_REQUEST['organ_therapycontract_num']) || isset($_REQUEST['organ_therapycontract_date']) || isset($_REQUEST['organ_therapycontract_discount_max_amount']))) {
                                        $query .= ",";
                                    }
                                    if (isset($_REQUEST['organ_therapycontract_editable'])) {
                                        $organ_therapycontract_editable = $this->post('organ_therapycontract_editable');
                                        $query .= "organ_therapycontract_editable=" . $organ_therapycontract_editable . " ";
                                    }

                                    if (isset($_REQUEST['organ_therapycontract_city_id']) && (isset($_REQUEST['organ_therapycontract_editable'])||isset($_REQUEST['organ_therapycontract_deactive']) || isset($_REQUEST['$organ_therapycontract_company_id']) || isset($_REQUEST['organ_therapycontract_discount_amount']) || isset($_REQUEST['organ_therapycontract_discount_percent']) || isset($_REQUEST['organ_therapycontract_clearing_day']) || isset($_REQUEST['organ_therapycontract_date_end']) || isset($_REQUEST['organ_therapycontract_date_start']) || isset($_REQUEST['organ_therapycontract_state_id']) || isset($_REQUEST['organ_therapycontract_num']) || isset($_REQUEST['organ_therapycontract_date']) || isset($_REQUEST['organ_therapycontract_discount_max_amount']))) {
                                        $query .= ",";
                                    }
                                    if (isset($_REQUEST['organ_therapycontract_city_id'])) {
                                        $organ_therapycontract_city_id = $this->post('organ_therapycontract_city_id');
                                        $query .= "organ_therapycontract_city_id=" . $organ_therapycontract_city_id . " ";
                                    }


                                    $query .= " where organ_therapycontract_id=" . $organ_therapycontract_id;
                                    $result = $this->B_db->run_query_put($query);

                                    if ($result) {
                                        echo json_encode(array('result' => "ok"
                                        , "data" => ""
                                        , 'desc' => ' تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                    } else {
                                        echo json_encode(array('result' => "ok"
                                        , "data" => ""
                                        , 'desc' => 'تغییرات انجام نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                    }

                                } else {
                                    echo json_encode(array('result' => $employeetoken[0]
                                    , "data" => $employeetoken[1]
                                    , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                }
                            } else
                                if ($command == "delete_organ_therapycontract") {
                                    $organ_therapycontract_id = $this->post('organ_therapycontract_id');
                                    $employeetoken = checkpermissionemployeetoken($employee_token_str, 'delete', 'organ');
                                    if ($employeetoken[0] == 'ok') {
                                        $query = " DELETE FROM organ_therapycontract_tb where organ_therapycontract_id=" . $organ_therapycontract_id;
                                        $result = $this->B_db->run_query_put($query);

                                        if ($result) {
                                            echo json_encode(array('result' => "ok"
                                            , "data" => ""
                                            , 'desc' => ' تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                        } else {
                                            echo json_encode(array('result' => "ok"
                                            , "data" => ""
                                            , 'desc' => 'تغییرات انجام نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                        }
                                    } else {
                                        echo json_encode(array('result' => $employeetoken[0]
                                        , "data" => $employeetoken[1]
                                        , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                    }
                                } else


                                    if ($command == "get_contract_byorgan") {
                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'view', 'organ');
                        if ($employeetoken[0] == 'ok') {
                            $organ_contract_organ_id = $this->post('organ_contract_organ_id');
                            $result = $this->B_organ->get_contracts_byorgan($organ_contract_organ_id);
                            if (!empty($result)) {
                                $i = 0;
                                foreach ($result as $row) {
                                    $res = $this->B_organ->get_company_name($row['organ_contract_company_id']);
                                    $result[$i]['company_name'] = $res['company_name'];
                                    $result[$i]['company_logo_url'] = IMGADD . $res['company_logo_url'];

                                    $res1 = $this->B_organ->get_fieldinsurance_name($row['organ_contract_fieldinsuranc_id']);
                                    $result[$i]['fieldinsurance_fa'] = $res1['fieldinsurance_fa'];
                                    $result[$i]['fieldinsurance_logo_url'] = IMGADD . $res1['fieldinsurance_logo_url'];

                                    if ($row['organ_logo'] != '') {
                                        $result1 = $this->B_db->get_image($row['organ_logo']);
                                        $image = $result1[0];
                                        if ($image['image_url']) {
                                            $imageurl =  $image['image_url'];
                                        }
                                        $result[$i]['organ_logo'] = $imageurl;
                                    }
                                    $i++;
                                }
                                echo json_encode(array('result' => "ok"
                                , "data" => $result
                                , 'desc' => 'قراردادها یافت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                            } else {
                                echo json_encode(array('result' => "error"
                                , "data" => ""
                                , 'desc' => 'قراردادی یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }

                        } else {
                            echo json_encode(array('result' => $employeetoken[0]
                            , "data" => $employeetoken[1]
                            , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    } else

                        if($command == "get_instalment_mode_round")
                    {
                        //************************************************************************;****************************************

                        $query="select * from instalment_round_tb where 1 ORDER BY instalment_round_id ASC";
                        $result = $this->B_db->run_query($query);
                        $output =array();
                        foreach($result as $row)
                        {
                            $record=array();
                            $record['instalment_round_id']=$row['instalment_round_id'];
                            $record['instalment_round_name']=$row['instalment_round_name'];
                            $output[]=$record;
                        }
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>$output
                        ,'desc'=>'انواع محاسبه  با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                    } else
                        if ($command == "modify_contract") {
                        $organ_contract_id = $this->post('organ_contract_id');
                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'modify', 'organ');
                        if ($employeetoken[0] == 'ok') {
                            $query = "UPDATE organ_contract_tb SET ";

                            if (isset($_REQUEST['organ_contract_fieldinsuranc_id'])) {
                                $organ_contract_fieldinsuranc_id = $this->post('organ_contract_fieldinsuranc_id');
                                $query .= "organ_contract_fieldinsuranc_id=" . $organ_contract_fieldinsuranc_id . "";
                            }

                            if (isset($_REQUEST['organ_contract_num']) && (isset($_REQUEST['organ_contract_fieldinsuranc_id']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['organ_contract_num'])) {
                                $organ_contract_num = $this->post('organ_contract_num');
                                $query .= "organ_contract_num='" . $organ_contract_num . "'";
                            }

                            if (isset($_REQUEST['organ_contract_date']) && (isset($_REQUEST['organ_contract_fieldinsuranc_id']) || isset($_REQUEST['organ_contract_num']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['organ_contract_date'])) {
                                $organ_contract_date = $this->post('organ_contract_date');
                                $query .= "organ_contract_date='" . $organ_contract_date . "'";
                            }

                            if (isset($_REQUEST['organ_contract_discount_max_amount']) && (isset($_REQUEST['organ_contract_fieldinsuranc_id']) || isset($_REQUEST['organ_contract_num']) || isset($_REQUEST['organ_contract_date']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['organ_contract_discount_max_amount'])) {
                                $organ_contract_discount_max_amount = $this->post('organ_contract_discount_max_amount');
                                $query .= "organ_contract_discount_max_amount='" . $organ_contract_discount_max_amount . "'";
                            }

                            if (isset($_REQUEST['organ_contract_date_start']) && (isset($_REQUEST['organ_contract_fieldinsuranc_id']) || isset($_REQUEST['organ_contract_num']) || isset($_REQUEST['organ_contract_date']) || isset($_REQUEST['organ_contract_discount_max_amount']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['organ_contract_date_start'])) {
                                $organ_contract_date_start = $this->post('organ_contract_date_start');
                                $query .= "organ_contract_date_start='" . $organ_contract_date_start . "' ";
                            }

                            if (isset($_REQUEST['organ_contract_date_end']) && (isset($_REQUEST['organ_contract_date_start']) || isset($_REQUEST['organ_contract_fieldinsuranc_id']) || isset($_REQUEST['organ_contract_num']) || isset($_REQUEST['organ_contract_date']) || isset($_REQUEST['organ_contract_discount_max_amount']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['organ_contract_date_end'])) {
                                $organ_contract_date_end = $this->post('organ_contract_date_end');
                                $query .= "organ_contract_date_end='" . $organ_contract_date_end . "' ";
                            }

                            if (isset($_REQUEST['organ_contract_clearing_day']) && (isset($_REQUEST['organ_contract_date_end']) || isset($_REQUEST['organ_contract_date_start']) || isset($_REQUEST['organ_contract_fieldinsuranc_id']) || isset($_REQUEST['organ_contract_num']) || isset($_REQUEST['organ_contract_date']) || isset($_REQUEST['organ_contract_discount_max_amount']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['organ_contract_clearing_day'])) {
                                $organ_contract_clearing_day = $this->post('organ_contract_clearing_day');
                                $query .= "organ_contract_clearing_day=" . $organ_contract_clearing_day . " ";
                            }

                            if (isset($_REQUEST['organ_contract_discount_percent']) && (isset($_REQUEST['organ_contract_clearing_day']) || isset($_REQUEST['organ_contract_date_end']) || isset($_REQUEST['organ_contract_date_start']) || isset($_REQUEST['organ_contract_fieldinsuranc_id']) || isset($_REQUEST['organ_contract_num']) || isset($_REQUEST['organ_contract_date']) || isset($_REQUEST['organ_contract_discount_max_amount']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['organ_contract_discount_percent'])) {
                                $organ_contract_discount_percent = $this->post('organ_contract_discount_percent');
                                $query .= "organ_contract_discount_percent=" . $organ_contract_discount_percent . " ";
                            }

                            if (isset($_REQUEST['organ_contract_discount_amount']) && (isset($_REQUEST['organ_contract_discount_percent']) || isset($_REQUEST['organ_contract_clearing_day']) || isset($_REQUEST['organ_contract_date_end']) || isset($_REQUEST['organ_contract_date_start']) || isset($_REQUEST['organ_contract_fieldinsuranc_id']) || isset($_REQUEST['organ_contract_num']) || isset($_REQUEST['organ_contract_date']) || isset($_REQUEST['organ_contract_discount_max_amount']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['organ_contract_discount_amount'])) {
                                $organ_contract_discount_amount = $this->post('organ_contract_discount_amount');
                                $query .= "organ_contract_discount_amount=" . $organ_contract_discount_amount . " ";
                            }

                            if (isset($_REQUEST['$organ_contract_company_id']) && (isset($_REQUEST['organ_contract_discount_amount']) || isset($_REQUEST['organ_contract_discount_percent']) || isset($_REQUEST['organ_contract_clearing_day']) || isset($_REQUEST['organ_contract_date_end']) || isset($_REQUEST['organ_contract_date_start']) || isset($_REQUEST['organ_contract_fieldinsuranc_id']) || isset($_REQUEST['organ_contract_num']) || isset($_REQUEST['organ_contract_date']) || isset($_REQUEST['organ_contract_discount_max_amount']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['$organ_contract_company_id'])) {
                                $organ_contract_company_id = $this->post('$organ_contract_company_id');
                                $query .= "$organ_contract_company_id=" . $organ_contract_company_id . " ";
                            }

                            if (isset($_REQUEST['organ_contract_deactive']) && (isset($_REQUEST['$organ_contract_company_id']) || isset($_REQUEST['organ_contract_discount_amount']) || isset($_REQUEST['organ_contract_discount_percent']) || isset($_REQUEST['organ_contract_clearing_day']) || isset($_REQUEST['organ_contract_date_end']) || isset($_REQUEST['organ_contract_date_start']) || isset($_REQUEST['organ_contract_fieldinsuranc_id']) || isset($_REQUEST['organ_contract_num']) || isset($_REQUEST['organ_contract_date']) || isset($_REQUEST['organ_contract_discount_max_amount']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['organ_contract_deactive'])) {
                                $organ_contract_deactive = $this->post('organ_contract_deactive');
                                $query .= "organ_contract_deactive=" . $organ_contract_deactive . " ";
                            }


                            if (isset($_REQUEST['organ_contract_editable']) && (isset($_REQUEST['organ_contract_deactive']) || isset($_REQUEST['$organ_contract_company_id']) || isset($_REQUEST['organ_contract_discount_amount']) || isset($_REQUEST['organ_contract_discount_percent']) || isset($_REQUEST['organ_contract_clearing_day']) || isset($_REQUEST['organ_contract_date_end']) || isset($_REQUEST['organ_contract_date_start']) || isset($_REQUEST['organ_contract_fieldinsuranc_id']) || isset($_REQUEST['organ_contract_num']) || isset($_REQUEST['organ_contract_date']) || isset($_REQUEST['organ_contract_discount_max_amount']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['organ_contract_editable'])) {
                                $organ_contract_editable = $this->post('organ_contract_editable');
                                $query .= "organ_contract_editable=" . $organ_contract_editable . " ";
                            }

                            if (isset($_REQUEST['organ_contract_instalment_round_id']) && (isset($_REQUEST['organ_contract_editable'])||isset($_REQUEST['organ_contract_deactive']) || isset($_REQUEST['$organ_contract_company_id']) || isset($_REQUEST['organ_contract_discount_amount']) || isset($_REQUEST['organ_contract_discount_percent']) || isset($_REQUEST['organ_contract_clearing_day']) || isset($_REQUEST['organ_contract_date_end']) || isset($_REQUEST['organ_contract_date_start']) || isset($_REQUEST['organ_contract_fieldinsuranc_id']) || isset($_REQUEST['organ_contract_num']) || isset($_REQUEST['organ_contract_date']) || isset($_REQUEST['organ_contract_discount_max_amount']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['organ_contract_instalment_round_id'])) {
                                $organ_contract_instalment_round_id = $this->post('organ_contract_instalment_round_id');
                                $query .= "organ_contract_instalment_round_id=" . $organ_contract_instalment_round_id . " ";
                            }


                            $query .= " where organ_contract_id=" . $organ_contract_id;
                            $result = $this->B_db->run_query_put($query);

                            if ($result) {
                                echo json_encode(array('result' => "ok"
                                , "data" => ""
                                , 'desc' => ' تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            } else {
                                echo json_encode(array('result' => "ok"
                                , "data" => ""
                                , 'desc' => 'تغییرات انجام نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }

                        } else {
                            echo json_encode(array('result' => $employeetoken[0]
                            , "data" => $employeetoken[1]
                            , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    } else
                        if ($command == "delete_organ_contract") {
                        $organ_contract_id = $this->post('organ_contract_id');
                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'delete', 'organ');
                        if ($employeetoken[0] == 'ok') {
                            $query = " DELETE FROM organ_contract_tb where organ_contract_id=" . $organ_contract_id;
                            $result = $this->B_db->run_query_put($query);

                            if ($result) {
                                echo json_encode(array('result' => "ok"
                                , "data" => ""
                                , 'desc' => ' تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            } else {
                                echo json_encode(array('result' => "ok"
                                , "data" => ""
                                , 'desc' => 'تغییرات انجام نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }
                        } else {
                            echo json_encode(array('result' => $employeetoken[0]
                            , "data" => $employeetoken[1]
                            , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    } else
                        if ($command == "get_instalment_mode") {
                     
                            $result = $this->B_organ->get_instalment_mode();
                            if (!empty($result)) {

                                echo json_encode(array('result' => "ok"
                                , "data" => $result
                                , 'desc' => 'قراردادها یافت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                            } else {
                                echo json_encode(array('result' => "error"
                                , "data" => ""
                                , 'desc' => 'قراردادی یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }


                    } else
                        if ($command == "get_contract_instalment") {
                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'view', 'organ');
                        if ($employeetoken[0] == 'ok') {
                            $instalment_condition_contract_id = $this->post('instalment_condition_contract_id');
                            $result = $this->B_organ->get_organ_instalment($instalment_condition_contract_id);
                            if (!empty($result)) {

                                echo json_encode(array('result' => "ok"
                                , "data" => $result
                                , 'desc' => 'قراردادها یافت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                            } else {
                                echo json_encode(array('result' => "ok"
                                , "data" => []
                                , 'desc' => 'قراردادی یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }

                        } else {
                            echo json_encode(array('result' => $employeetoken[0]
                            , "data" => $employeetoken[1]
                            , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    } else
                        if ($command == "add_contract_instalment") {
                        $instalment_condition_contract_id = $this->post('instalment_condition_contract_id');
                        $instalment_conditions_percent = $this->post('instalment_conditions_percent');
                        $instalment_conditions_date = $this->post('instalment_conditions_date');
                        $instalment_conditions_desc = $this->post('instalment_conditions_desc');
                        $instalment_conditions_mode_id = $this->post('instalment_conditions_mode_id');
                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'organ');
                        if ($employeetoken[0] == 'ok') {
                            $instalment_condition_employee_id = $employeetoken[1];
                            $organ_id = $this->B_organ->add_contract_instalment($instalment_condition_contract_id, $instalment_conditions_percent, $instalment_conditions_date, $instalment_conditions_mode_id, $instalment_condition_employee_id, $instalment_conditions_desc);
                            echo json_encode(array('result' => "ok"
                            , "data" => array('organ_id' => $organ_id)
                            , 'desc' => 'قرارداد ارگان مورد نظر اضافه گردید!'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        } else {
                            echo json_encode(array('result' => $employeetoken[0]
                            , "data" => $employeetoken[1]
                            , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    } else
                        if ($command == "modify_contract_instalment") {
                            $instalment_conditions_id = $this->post('instalment_conditions_id');
                            $employeetoken = checkpermissionemployeetoken($employee_token_str, 'modify', 'organ');
                            if ($employeetoken[0] == 'ok') {
                                $query = "UPDATE instalment_conditions_tb SET ";

                                if (isset($_REQUEST['instalment_conditions_desc'])) {
                                    $instalment_conditions_desc = $this->post('instalment_conditions_desc');
                                    $query .= "instalment_conditions_desc='" . $instalment_conditions_desc . "'";
                                }

                                if (isset($_REQUEST['instalment_conditions_percent']) && (isset($_REQUEST['instalment_conditions_desc']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['instalment_conditions_percent'])) {
                                    $instalment_conditions_percent = $this->post('instalment_conditions_percent');
                                    $query .= "instalment_conditions_percent='" . $instalment_conditions_percent . "'";
                                }

                                if (isset($_REQUEST['instalment_conditions_date']) && (isset($_REQUEST['instalment_conditions_desc']) || isset($_REQUEST['instalment_conditions_percent']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['instalment_conditions_date'])) {
                                    $instalment_conditions_date = $this->post('instalment_conditions_date');
                                    $query .= "instalment_conditions_date='" . $instalment_conditions_date . "'";
                                }

                                if (isset($_REQUEST['instalment_conditions_mode_id']) && (isset($_REQUEST['instalment_conditions_desc']) || isset($_REQUEST['instalment_conditions_percent']) || isset($_REQUEST['instalment_conditions_date']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['instalment_conditions_mode_id'])) {
                                    $organ_agentmobile = $this->post('instalment_conditions_mode_id');
                                    $query .= "instalment_conditions_mode_id='" . $organ_agentmobile . "'";
                                }

                                if (isset($_REQUEST['instalment_conditions_desc']) || isset($_REQUEST['instalment_conditions_percent']) || isset($_REQUEST['instalment_conditions_date']) || isset($_REQUEST['instalment_conditions_mode_id'])) {
                                    $query .= ",";
                                }
                                $query .= " instalment_condition_employee_id='" . $employeetoken[1] . "' ";


                                $query .= " where instalment_conditions_id=" . $instalment_conditions_id;
                                $result = $this->B_db->run_query_put($query);

                                if ($result) {
                                    echo json_encode(array('result' => "ok"
                                    , "data" => ""
                                    , 'desc' => ' تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                } else {
                                    echo json_encode(array('result' => "ok"
                                    , "data" => ""
                                    , 'desc' => 'تغییرات انجام نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                }

                            } else {
                                echo json_encode(array('result' => $employeetoken[0]
                                , "data" => $employeetoken[1]
                                , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }
                        } else
                            if ($command == "delete_contract_instalment") {
                            $instalment_conditions_id = $this->post('instalment_conditions_id');
                            $employeetoken = checkpermissionemployeetoken($employee_token_str, 'delete', 'organ');
                            if ($employeetoken[0] == 'ok') {
                                $query = " DELETE FROM instalment_conditions_tb where instalment_conditions_id=" . $instalment_conditions_id;
                                $result = $this->B_db->run_query_put($query);

                                if ($result) {
                                    echo json_encode(array('result' => "ok"
                                    , "data" => ""
                                    , 'desc' => ' تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                } else {
                                    echo json_encode(array('result' => "ok"
                                    , "data" => ""
                                    , 'desc' => 'تغییرات انجام نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                }
                            } else {
                                echo json_encode(array('result' => $employeetoken[0]
                                , "data" => $employeetoken[1]
                                , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }
                        }else

                                if ($command == "get_organ_therapycontract_conditions") {
                                    $employeetoken = checkpermissionemployeetoken($employee_token_str, 'view', 'organ');
                                    if ($employeetoken[0] == 'ok') {
                                        $organ_therapycontract_conditions_contract_id = $this->post('organ_therapycontract_conditions_contract_id');
                                        $result = $this->B_organ->get_organ_therapycontract_conditions_contract($organ_therapycontract_conditions_contract_id);
                                        if (!empty($result)) {

                                            echo json_encode(array('result' => "ok"
                                            , "data" => $result
                                            , 'desc' => 'قراردادها یافت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                        } else {
                                            echo json_encode(array('result' => "ok"
                                            , "data" => []
                                            , 'desc' => 'قراردادی یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                        }

                                    } else {
                                        echo json_encode(array('result' => $employeetoken[0]
                                        , "data" => $employeetoken[1]
                                        , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                    }
                                } else
                                    if ($command == "add_organ_therapycontract_conditions") {
                                        $organ_therapycontract_conditions_contract_id = $this->post('organ_therapycontract_conditions_contract_id');
                                        $organ_therapycontract_conditions_percent = $this->post('organ_therapycontract_conditions_amount');
                                        $organ_therapycontract_conditions_desc = $this->post('organ_therapycontract_conditions_desc');
                                        $organ_therapycontract_conditions_mode_id = $this->post('organ_therapycontract_conditions_tc_c_covarage_id');
                                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'organ');
                                        if ($employeetoken[0] == 'ok') {
                                            $organ_therapycontract_conditions_employee_id = $employeetoken[1];
                                            $organ_id = $this->B_organ->add_organ_therapycontract_conditions($organ_therapycontract_conditions_contract_id, $organ_therapycontract_conditions_percent,  $organ_therapycontract_conditions_mode_id, $organ_therapycontract_conditions_employee_id, $organ_therapycontract_conditions_desc);
                                            echo json_encode(array('result' => "ok"
                                            , "data" => array('organ_id' => $organ_id)
                                            , 'desc' => 'قرارداد ارگان مورد نظر اضافه گردید!'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                        } else {
                                            echo json_encode(array('result' => $employeetoken[0]
                                            , "data" => $employeetoken[1]
                                            , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                        }
                                    } else
                                        if ($command == "modify_organ_therapycontract_conditions") {
                                            $organ_therapycontract_conditions_id = $this->post('organ_therapycontract_conditions_id');
                                            $employeetoken = checkpermissionemployeetoken($employee_token_str, 'modify', 'organ');
                                            if ($employeetoken[0] == 'ok') {
                                                $query = "UPDATE organ_therapycontract_conditions_tb SET ";

                                                if (isset($_REQUEST['organ_therapycontract_conditions_desc'])) {
                                                    $organ_therapycontract_conditions_desc = $this->post('organ_therapycontract_conditions_desc');
                                                    $query .= "organ_therapycontract_conditions_desc='" . $organ_therapycontract_conditions_desc . "'";
                                                }

                                                if (isset($_REQUEST['organ_therapycontract_conditions_amount']) && (isset($_REQUEST['organ_therapycontract_conditions_desc']))) {
                                                    $query .= ",";
                                                }
                                                if (isset($_REQUEST['organ_therapycontract_conditions_amount'])) {
                                                    $organ_therapycontract_conditions_percent = $this->post('organ_therapycontract_conditions_amount');
                                                    $query .= "organ_therapycontract_conditions_amount='" . $organ_therapycontract_conditions_percent . "'";
                                                }



                                                if (isset($_REQUEST['organ_therapycontract_conditions_tc_c_covarage_id']) && (isset($_REQUEST['organ_therapycontract_conditions_desc']) || isset($_REQUEST['organ_therapycontract_conditions_amount']) )) {
                                                    $query .= ",";
                                                }
                                                if (isset($_REQUEST['organ_therapycontract_conditions_tc_c_covarage_id'])) {
                                                    $organ_agentmobile = $this->post('organ_therapycontract_conditions_tc_c_covarage_id');
                                                    $query .= "organ_therapycontract_conditions_tc_c_covarage_id='" . $organ_agentmobile . "'";
                                                }

                                                if (isset($_REQUEST['organ_therapycontract_conditions_desc']) || isset($_REQUEST['organ_therapycontract_conditions_amount']) || isset($_REQUEST['organ_therapycontract_conditions_tc_c_covarage_id'])) {
                                                    $query .= ",";
                                                }
                                                $query .= " organ_therapycontract_conditions_employee_id='" . $employeetoken[1] . "' ";


                                                $query .= " where organ_therapycontract_conditions_id=" . $organ_therapycontract_conditions_id;
                                                $result = $this->B_db->run_query_put($query);

                                                if ($result) {
                                                    echo json_encode(array('result' => "ok"
                                                    , "data" => ""
                                                    , 'desc' => ' تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                } else {
                                                    echo json_encode(array('result' => "ok"
                                                    , "data" => ""
                                                    , 'desc' => 'تغییرات انجام نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                }

                                            } else {
                                                echo json_encode(array('result' => $employeetoken[0]
                                                , "data" => $employeetoken[1]
                                                , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                            }
                                        } else
                                            if ($command == "delete_organ_therapycontract_conditions") {
                                                $organ_therapycontract_conditions_id = $this->post('organ_therapycontract_conditions_id');
                                                $employeetoken = checkpermissionemployeetoken($employee_token_str, 'delete', 'organ');
                                                if ($employeetoken[0] == 'ok') {
                                                    $query = " DELETE FROM organ_therapycontract_conditions_tb where organ_therapycontract_conditions_id=" . $organ_therapycontract_conditions_id;
                                                    $result = $this->B_db->run_query_put($query);

                                                    if ($result) {
                                                        echo json_encode(array('result' => "ok"
                                                        , "data" => ""
                                                        , 'desc' => ' تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                    } else {
                                                        echo json_encode(array('result' => "ok"
                                                        , "data" => ""
                                                        , 'desc' => 'تغییرات انجام نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                    }
                                                } else {
                                                    echo json_encode(array('result' => $employeetoken[0]
                                                    , "data" => $employeetoken[1]
                                                    , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                }
                                            }else
                                           if ($command == "get_organ_therapycontract_conditions_covarage") {

                                                    $result = $this->B_organ->get_organ_therapycontract_conditions_covarage();
                                                    if (!empty($result)) {

                                                        echo json_encode(array('result' => "ok"
                                                        , "data" => $result
                                                        , 'desc' => 'قراردادها یافت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                                    } else {
                                                        echo json_encode(array('result' => "error"
                                                        , "data" => ""
                                                        , 'desc' => 'قراردادی یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                    }


                                                } else
                                            if ($command == "get_therapycontract_byorgan") {
                                                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'view', 'organ');
                                                        if ($employeetoken[0] == 'ok') {
                                                            $organ_therapycontract_organ_id = $this->post('organ_therapycontract_organ_id');
                                                            $result = $this->B_organ->get_therapycontract_byorgan($organ_therapycontract_organ_id);
                                                            if (!empty($result)) {

                                                                echo json_encode(array('result' => "ok"
                                                                , "data" => $result
                                                                , 'desc' => 'قراردادها یافت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                                            } else {
                                                                echo json_encode(array('result' => "error"
                                                                , "data" => ""
                                                                , 'desc' => 'قراردادی یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                            }

                                                        } else {
                                                            echo json_encode(array('result' => $employeetoken[0]
                                                            , "data" => $employeetoken[1]
                                                            , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                        }
                                                    } else
                                           if ($command == "get_user_therapy")
                                                        {
                                                            $organ_user_therapy_name=$this->post('organ_user_therapy_name') ;
                                                            $organ_user_therapy_family=$this->post('organ_user_therapy_family') ;
                                                            $user_family=$this->post('user_family') ;
                                                            $user_mobile=$this->post('user_mobile') ;
                                                            $user_name=$this->post('user_name') ;
                                                            $organ_user_therapy_national_code=$this->post('organ_user_therapy_national_code') ;
                                                            $organ_user_therapy_personal_code=$this->post('organ_user_therapy_personal_code') ;
                                                            $organ_user_therapy_kind_id = $this->post('organ_user_therapy_kind_id');
                                                            $organ_user_therapy_bimeno = $this->post('organ_user_therapy_bimeno');
                                                            $organ_user_therapy_idcardno = $this->post('organ_user_therapy_idcardno');
                                                            $organ_user_therapy_gender_id = $this->post('organ_user_therapy_gender_id');
                                                            $organ_user_therapy_main_national_code = $this->post('organ_user_therapy_main_national_code');
                                                            $organ_user_therapy_year = $this->post('organ_user_therapy_year');
                                                            $organ_user_therapy_month = $this->post('organ_user_therapy_month');
                                                            $organ_user_therapy_day = $this->post('organ_user_therapy_day');
                                                            $organ_user_therapy_fathername = $this->post('organ_user_therapy_fathername');
                                                            $organ_user_therapy_kinship_id = $this->post('organ_user_therapy_kinship_id');
                                                            $organ_user_therapy_basebime_id = $this->post('organ_user_therapy_basebime_id');
                                                            $organ_user_therapy_bank_id = $this->post('organ_user_therapy_bank_id');
                                                            $organ_user_therapy_cardno = $this->post('organ_user_therapy_cardno');
                                                            $organ_user_therapy_accno = $this->post('organ_user_therapy_accno');
                                                            $organ_user_therapy_shebano = $this->post('organ_user_therapy_shebano');
                                                            $limit = $this->post("limit");
                                                            $offset = $this->post("offset");
                                                            $therapycontract_id=$this->post("therapycontract_id");
                                                            $organ_id=$this->post("organ_id");
                                                            $employeetoken = checkpermissionemployeetoken($employee_token_str, 'view', 'organ');
                                                            if ($employeetoken[0] == 'ok') {

                                                                $filter = "";
                                                                if($organ_user_therapy_name !='')
                                                                    $filter .= " And organ_user_therapy_name like   '%".$organ_user_therapy_name."%'   ESCAPE '!'";
                                                                if($organ_user_therapy_family !='')
                                                                    $filter .= " And organ_user_therapy_family like   '%".$organ_user_therapy_family."%'   ESCAPE '!'";
                                                                if($user_family !='')
                                                                    $filter .= " And user_family like '%".$user_family."%' ESCAPE '!'";
                                                                if($user_name !='')
                                                                    $filter .= " And user_name like '%".$user_name."%' ESCAPE '!'";
                                                                if($organ_user_therapy_fathername !='')
                                                                    $filter .= " And organ_user_therapy_fathername like '%".$organ_user_therapy_fathername."%' ESCAPE '!'";
                                                                if($user_mobile !='')
                                                                        $filter .= " And user_mobile=".$user_mobile;
                                                                if($organ_user_therapy_year !='')
                                                                    $filter .= " And organ_user_therapy_year=".$organ_user_therapy_year;
                                                                if($organ_user_therapy_month !='')
                                                                    $filter .= " And organ_user_therapy_month=".$organ_user_therapy_month;
                                                                if($organ_user_therapy_day !='')
                                                                    $filter .= " And organ_user_therapy_day=".$organ_user_therapy_day;
                                                                if($organ_user_therapy_national_code !='')
                                                                        $filter .= " And organ_user_therapy_national_code=".$organ_user_therapy_national_code;
                                                                if($organ_user_therapy_gender_id !='')
                                                                    $filter .= " And organ_user_therapy_gender_id=".$organ_user_therapy_gender_id;
                                                                    if($organ_user_therapy_personal_code !='')
                                                                        $filter .= " And organ_user_therapy_personal_code='".$organ_user_therapy_personal_code."'";
                                                                if($organ_user_therapy_kind_id !='')
                                                                    $filter .= " And organ_user_therapy_kind_id=".$organ_user_therapy_kind_id;
                                                                if($organ_user_therapy_kinship_id !='')
                                                                    $filter .= " And organ_user_therapy_kinship_id=".$organ_user_therapy_kinship_id;
                                                                if($organ_user_therapy_basebime_id !='')
                                                                    $filter .= " And organ_user_therapy_basebime_id=".$organ_user_therapy_basebime_id;
                                                                if($organ_user_therapy_cardno !='')
                                                                    $filter .= " And organ_user_therapy_cardno=".$organ_user_therapy_cardno;
                                                                if($organ_user_therapy_accno !='')
                                                                    $filter .= " And organ_user_therapy_accno=".$organ_user_therapy_accno;
                                                                if($organ_user_therapy_shebano !='')
                                                                    $filter .= " And organ_user_therapy_shebano=".$organ_user_therapy_shebano;
                                                                if($organ_user_therapy_bank_id !='')
                                                                    $filter .= " And organ_user_therapy_bank_id=".$organ_user_therapy_bank_id;
                                                                if($organ_user_therapy_bimeno !='')
                                                                        $filter .= " And organ_user_therapy_bimeno=".$organ_user_therapy_bimeno;
                                                                    if($organ_user_therapy_idcardno !='')
                                                                        $filter .= " And organ_user_therapy_idcardno='".$organ_user_therapy_idcardno."'";
                                                                    if($organ_user_therapy_main_national_code !='')
                                                                        $filter .= " And organ_user_therapy_main_national_code='".$organ_user_therapy_main_national_code."'";
                                                                    if($therapycontract_id !='')
                                                                        $filter .= " And organ_user_therapy_organ_therapycontract_id=".$therapycontract_id."";
                                                                if($organ_id !='')
                                                                    $filter .= " And organ_therapycontract_organ_id=".$organ_id."";


                                                                $limit_state ="";
                                                                if($limit!="" & $offset!="")
                                                                    $limit_state = "LIMIT ".$limit.",".$offset;

                                                                $query_body = "FROM organ_user_therapy_tb,user_tb,organ_therapycontract_tb
WHERE 
  organ_user_therapy_main_user_id=user_id
AND organ_user_therapy_organ_therapycontract_id=organ_therapycontract_id";
                                                                $query="select * ".$query_body." ".$filter."  ".$limit_state;
                                                                $count_query="select count(*) as count ".$query_body." ".$filter;
                                                                $result = $this->B_db->run_query($query);


                                                            $count  = $this->B_db->run_query($count_query);

                                                                if (!empty($result)) {
//                                                                    $i = 0;
//                                                                    foreach ($result as $row) {
//
//                                                                        $result1 = $this->B_organ->get_organ_therapycontract_conditions_contract($row['organ_therapycontract_id']);
//                                                                        if($result1==null){
//                                                                            $result[$i]['conditions'] = '';
//                                                                        }else{
//                                                                            //***************************************************************
//                                                                            $i2 = 0;
//                                                                            foreach ($result1 as $row2) {
//
//                                                                                $res = $this->B_organ->get_covarage_used($row['organ_user_therapy_id'],$row2['organ_therapycontract_conditions_covarage_id']);
//                                                                                if($res==null){
//                                                                                    $result1[$i2]['sumprice'] = 0;
//                                                                                }else{
//                                                                                    $result1[$i2]['sumprice'] = $res['sumprice'];
//                                                                                }
//
//
//                                                                                $i2++;
//                                                                            }
//                                                                            //***************************************************************
//                                                                            $result[$i]['conditions'] = $result1;
//
//                                                                        }
//
//
//                                                                        $i++;
//                                                                    }
                                                                    echo json_encode(array('result' => "ok"
                                                                    , "data" => $result
                                                                    ,"cnt"=>$count[0]['count']
                                                                    , 'desc' => 'قراردادها یافت شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                                                }else {
                                                                    echo json_encode(array('result' => "ok"
                                                                    , "data" => []
                                                                    , 'desc' => 'قراردادی یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                                }

                                                            } else {
                                                                echo json_encode(array('result' => $employeetoken[0]
                                                                , "data" => $employeetoken[1]
                                                                , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                            }
                                                        }else
                                            if ($command=="get_user_therapy_kind")
                                                            {
//************************************************************************;****************************************

                                                                $query="select * from user_therapy_kind_tb where 1 ORDER BY user_therapy_kind_id ASC";
                                                                $result = $this->B_db->run_query($query);
                                                                $output =array();
                                                                foreach($result as $row)
                                                                {
                                                                    $record=array();
                                                                    $record['user_therapy_kind_id']=$row['user_therapy_kind_id'];
                                                                    $record['user_therapy_kind_name']=$row['user_therapy_kind_name'];
                                                                    $output[]=$record;
                                                                }
                                                                echo json_encode(array('result'=>"ok"
                                                                ,"data"=>$output
                                                                ,'desc'=>'نوع ملک بیمه اتش سوزی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                            }else
                                             if ($command=="get_user_therapy_kindship")
                                                                {
//************************************************************************;****************************************

                                                                    $query="select * from user_therapy_kindship_tb where 1 ORDER BY user_therapy_kindship_id ASC";
                                                                    $result = $this->B_db->run_query($query);
                                                                    $output =array();
                                                                    foreach($result as $row)
                                                                    {
                                                                        $record=array();
                                                                        $record['user_therapy_kindship_id']=$row['user_therapy_kindship_id'];
                                                                        $record['user_therapy_kindship_name']=$row['user_therapy_kindship_fa'];
                                                                        $output[]=$record;
                                                                    }
                                                                    echo json_encode(array('result'=>"ok"
                                                                    ,"data"=>$output
                                                                    ,'desc'=>'نوع ملک بیمه اتش سوزی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                                }else
                                                 if ($command=="get_user_therapy_baseinsurer")
                                                 {
//************************************************************************;****************************************

                                                     $query="select * from user_therapy_baseinsurer_tb where 1 ORDER BY user_therapy_baseinsurer_id ASC";
                                                     $result = $this->B_db->run_query($query);
                                                     $output =array();
                                                     foreach($result as $row)
                                                     {
                                                         $record=array();
                                                         $record['user_therapy_baseinsurer_id']=$row['user_therapy_baseinsurer_id'];
                                                         $record['user_therapy_baseinsurer_name']=$row['user_therapy_baseinsurer_name'];
                                                         $output[]=$record;
                                                     }
                                                     echo json_encode(array('result'=>"ok"
                                                     ,"data"=>$output
                                                     ,'desc'=>'نوع ملک بیمه اتش سوزی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                 }else
                                                     if ($command=="get_user_therapy_bank")
                                                     {
//************************************************************************;****************************************

                                                         $query="select * from user_therapy_bank_tb where 1 ORDER BY user_therapy_bank_id ASC";
                                                         $result = $this->B_db->run_query($query);
                                                         $output =array();
                                                         foreach($result as $row)
                                                         {
                                                             $record=array();
                                                             $record['user_therapy_bank_id']=$row['user_therapy_bank_id'];
                                                             $record['user_therapy_bank_name']=$row['user_therapy_bank_name'];
                                                             $output[]=$record;
                                                         }
                                                         echo json_encode(array('result'=>"ok"
                                                         ,"data"=>$output
                                                         ,'desc'=>'نوع ملک بیمه اتش سوزی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                     }else
                                                         if ($command=="get_user_gender")
                                                         {
//************************************************************************;****************************************

                                                             $query="select * from user_gender_tb where 1 ORDER BY user_gender_id ASC";
                                                             $result = $this->B_db->run_query($query);
                                                             $output =array();
                                                             foreach($result as $row)
                                                             {
                                                                 $record=array();
                                                                 $record['user_gender_id']=$row['user_gender_id'];
                                                                 $record['user_gender_name']=$row['user_gender_fa'];
                                                                 $output[]=$record;
                                                             }
                                                             echo json_encode(array('result'=>"ok"
                                                             ,"data"=>$output
                                                             ,'desc'=>'نوع ملک بیمه اتش سوزی با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************

                                                         }else
                                                             if ($command == "add_user_therapy")
                                                             {

                                                                 $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'organ');
                                                                 if ($employeetoken[0] == 'ok')
                                                                 {
                                                                     $organ_user_therapy_organ_id = $this->post('organ_user_therapy_organ_id');
                                                                     $organ_user_therapy_kind_id = $this->post('organ_user_therapy_kind_id');
                                                                     $organ_user_therapy_name = $this->post('organ_user_therapy_name');
                                                                     $organ_user_therapy_family = $this->post('organ_user_therapy_family');
                                                                     $organ_user_therapy_national_code = $this->post('organ_user_therapy_national_code');
                                                                     $organ_user_therapy_mobile = $this->post('organ_user_therapy_mobile');
                                                                     $organ_user_therapy_gender_id = $this->post('organ_user_therapy_gender_id');
                                                                     $organ_user_therapy_year = $this->post('organ_user_therapy_year');
                                                                     $organ_user_therapy_month = $this->post('organ_user_therapy_month');
                                                                     $organ_user_therapy_day = $this->post('organ_user_therapy_day');
                                                                     $organ_user_therapy_fathername = $this->post('organ_user_therapy_fathername');
                                                                     $organ_user_therapy_kinship_id = $this->post('organ_user_therapy_kinship_id');
                                                                     $organ_user_therapy_organ_therapycontract_id = $this->post('organ_user_therapy_organ_therapycontract_id');
                                                                     $organ_user_therapy_basebime_id = $this->post('organ_user_therapy_basebime_id');
                                                                     $organ_user_therapy_bank_id = $this->post('organ_user_therapy_bank_id');
                                                                     $organ_user_therapy_cardno = $this->post('organ_user_therapy_cardno');
                                                                     $organ_user_therapy_accno = $this->post('organ_user_therapy_accno');
                                                                     $organ_user_therapy_shebano = $this->post('organ_user_therapy_shebano');
                                                                     $organ_user_therapy_bimeno = $this->post('organ_user_therapy_bimeno');
                                                                     $organ_user_therapy_idcardno = $this->post('organ_user_therapy_idcardno');
                                                                     $organ_user_therapy_main_national_code = $this->post('organ_user_therapy_main_national_code');
                                                                     $organ_user_therapy_personal_code = $this->post('organ_user_therapy_personal_code');


                                                                     try {
                                                                         $data_['organ_user_therapy_organ_id'] =  $organ_user_therapy_organ_id;
                                                                         $data_['organ_user_therapy_name'] =  $organ_user_therapy_name;
                                                                         $data_['organ_user_therapy_family'] =  $organ_user_therapy_family;
                                                                         $data_['organ_user_therapy_national_code'] =  $organ_user_therapy_national_code;
                                                                         $data_['organ_user_therapy_kind_id'] =  $organ_user_therapy_kind_id;
                                                                         $data_['organ_user_therapy_gender_id'] =  $organ_user_therapy_gender_id;
                                                                         $data_['organ_user_therapy_year'] =  $organ_user_therapy_year;
                                                                         $data_['organ_user_therapy_month'] =  $organ_user_therapy_month;
                                                                         $data_['organ_user_therapy_day'] =  $organ_user_therapy_day;
                                                                         $data_['organ_user_therapy_fathername'] =  $organ_user_therapy_fathername;
                                                                         $data_['organ_user_therapy_kinship_id'] =  $organ_user_therapy_kinship_id;
                                                                         $data_['organ_user_therapy_organ_therapycontract_id'] =  $organ_user_therapy_organ_therapycontract_id;
                                                                         $data_['organ_user_therapy_basebime_id'] =  $organ_user_therapy_basebime_id;
                                                                         $data_['organ_user_therapy_bank_id'] =  $organ_user_therapy_bank_id;
                                                                         $data_['organ_user_therapy_cardno'] =  $organ_user_therapy_cardno;
                                                                         $data_['organ_user_therapy_accno'] =  $organ_user_therapy_accno;
                                                                         $data_['organ_user_therapy_shebano'] =  $organ_user_therapy_shebano;
                                                                         $data_['organ_user_therapy_bimeno'] =  $organ_user_therapy_bimeno;
                                                                         $data_['organ_user_therapy_idcardno'] =  $organ_user_therapy_idcardno;
                                                                         $data_['organ_user_therapy_main_national_code'] =  $organ_user_therapy_main_national_code;
                                                                         $data_['organ_user_therapy_personal_code'] =  $organ_user_therapy_personal_code;


                                                                         //find the main_member record by "organ_user_therapy_mobile"
                                                                         $user = $this->B_user->get_user_by_moblie($organ_user_therapy_mobile);
                                                                         $msg = "";
                                                                         if(empty($user)){
                                                                             //add user because it is not exist
                                                                             if( $organ_user_therapy_kind_id == 1){
                                                                                 if($this->B_user->create_user($organ_user_therapy_mobile)){
                                                                                     $user_id = $this->db->insert_id();
                                                                                     $this->B_user->update_user_tb($data_,$user_id);
                                                                                     $msg =  'یک عضو سرپرست اضافه شد';
                                                                                 }
                                                                             }elseif( $organ_user_therapy_kind_id > 1){
                                                                                 $message1 = array('result' => "error", "organ_user_therapy_national_code" => $organ_user_therapy_national_code, 'desc' => 'عضو سرپرست این شخص یافت نشد');
                                                                                 echo json_encode($message1, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                                             return;
                                                                             }
                                                                         }else{
                                                                             $user_id = $user[0]['user_id'];
                                                                             //update exist user data
                                                                             $existed_list[] = $organ_user_therapy_national_code;
                                                                             //$this->B_user->update_user_tb($data_,$user_id);
                                                                             //$msg =  'اطلاعات این سرپرست بروزرسانی گردید';
                                                                         }
                                                                         //check member existing in therapy table by $national_code
                                                                         if(empty($this->B_user->get_organ_user_therapy($organ_user_therapy_national_code))){
                                                                             //add main member to organ_user_therapy
                                                                             $this->B_user->create_organ_user_therapy($data_, $user_id);
                                                                             $msg .= "اطلاعات شخص در بانک درمان اضافه گردید";
                                                                         }else{
                                                                             $this->B_user->update_organ_user_therapy($data_,$organ_user_therapy_national_code,$user_id);
                                                                             $msg .=  'اطلاعات شخص در بانک درمان بروزرسانی گردید';
                                                                         }
                                                                         $message1 = array('result' => "ok",  'desc' => $msg);
                                                                         echo json_encode($message1, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                                     } catch (Exception $e) {
                                                                         $message = array('result' => "error"
                                                                         , "data" => array('message'=>$e->getMessage())
                                                                         , 'desc' => 'عملیات با خطا پایان یافت');
                                                                         echo json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                                     }
                                                                 }
                                                                 else {
                                                                     echo json_encode(array('result' => $employeetoken[0]
                                                                     , "data" => $employeetoken[1]
                                                                     , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                                 }
                                                             }else
                                                                 if ($command == "delete_user_therapy")
                                                                 {
                                                                     $organ_user_therapy_id=$this->post('organ_user_therapy_id') ;

                                                                     $employeetoken = checkpermissionemployeetoken($employee_token_str, 'delete', 'organ');
                                                                     if ($employeetoken[0] == 'ok') {

                                                                         $query="DELETE FROM organ_user_therapy_tb  where organ_user_therapy_id=".$organ_user_therapy_id."";

                                                                         $result = $this->B_db->run_query_put($query);
                                                                         if($result){
                                                                             echo json_encode(array('result' => "ok"
                                                                             , "data" => ''
                                                                             , 'desc' => 'حذف با موفقیت انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                                                         }else {
                                                                             echo json_encode(array('result' => "ok"
                                                                             , "data" => []
                                                                             , 'desc' => 'حذف با موفقیت انجام نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                                         }

                                                                     } else {
                                                                         echo json_encode(array('result' => $employeetoken[0]
                                                                         , "data" => $employeetoken[1]
                                                                         , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                                     }
                                                                 }else

                                                             if ($command == "modify_user_therapy")
                                                             {

                                                                 $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'organ');
                                                                 if ($employeetoken[0] == 'ok')
                                                                 {
                                                                     $organ_user_therapy_id= $this->post('organ_user_therapy_id');

                                                                     
                                                                     $query = "UPDATE organ_user_therapy_tb SET ";
                                                                     if (isset($_REQUEST['organ_user_therapy_kind_id'])) {
                                                                         $organ_user_therapy_kind_id = $this->post('organ_user_therapy_kind_id');
                                                                         $query .= "organ_user_therapy_kind_id=" . $organ_user_therapy_kind_id . " ";
                                                                     }

                                                                     if (isset($_REQUEST['organ_user_therapy_name']) && isset($_REQUEST['organ_user_therapy_kind_id'])) {
                                                                         $query .= ",";
                                                                     }
                                                                     if (isset($_REQUEST['organ_user_therapy_name'])) {
                                                                         $organ_user_therapy_name = $this->post('organ_user_therapy_name');
                                                                         $query .= "organ_user_therapy_name='" . $organ_user_therapy_name . "'";
                                                                     }

                                                                     if (isset($_REQUEST['organ_user_therapy_family']) && (isset($_REQUEST['organ_user_therapy_name']) || isset($_REQUEST['organ_user_therapy_kind_id']))) {
                                                                         $query .= ",";
                                                                     }
                                                                     if (isset($_REQUEST['organ_user_therapy_family'])) {
                                                                         $organ_user_therapy_family = $this->post('organ_user_therapy_family');
                                                                         $query .= "organ_user_therapy_family='" . $organ_user_therapy_family . "'";
                                                                     }

                                                                     if (isset($_REQUEST['organ_user_therapy_national_code']) && (isset($_REQUEST['organ_user_therapy_name']) || isset($_REQUEST['organ_user_therapy_kind_id']) || isset($_REQUEST['organ_user_therapy_family']))) {
                                                                         $query .= ",";
                                                                     }
                                                                     if (isset($_REQUEST['organ_user_therapy_national_code'])) {
                                                                         $organ_user_therapy_national_code = $this->post('organ_user_therapy_national_code');
                                                                         $query .= "organ_user_therapy_national_code='" . $organ_user_therapy_national_code . "'";
                                                                     }

                                                                     if (isset($_REQUEST['organ_user_therapy_gender_id']) && (isset($_REQUEST['organ_user_therapy_name']) || isset($_REQUEST['organ_user_therapy_kind_id']) || isset($_REQUEST['organ_user_therapy_family']) || isset($_REQUEST['organ_user_therapy_national_code']))) {
                                                                         $query .= ",";
                                                                     }
                                                                     if (isset($_REQUEST['organ_user_therapy_gender_id'])) {
                                                                         $organ_user_therapy_gender_id = $this->post('organ_user_therapy_gender_id');
                                                                         $query .= "organ_user_therapy_gender_id=" . $organ_user_therapy_gender_id . "";
                                                                     }

                                                                     if (isset($_REQUEST['organ_user_therapy_year']) && (isset($_REQUEST['organ_user_therapy_name']) || isset($_REQUEST['organ_user_therapy_kind_id']) || isset($_REQUEST['organ_user_therapy_family']) || isset($_REQUEST['organ_user_therapy_national_code']) || isset($_REQUEST['organ_user_therapy_gender_id']))) {
                                                                         $query .= ",";
                                                                     }
                                                                     if (isset($_REQUEST['organ_user_therapy_year'])) {
                                                                         $organ_user_therapy_year = $this->post('organ_user_therapy_year');
                                                                         $query .= "organ_user_therapy_year=" . $organ_user_therapy_year . " ";
                                                                     }

                                                                     if (isset($_REQUEST['organ_user_therapy_month']) && (isset($_REQUEST['organ_user_therapy_year']) || isset($_REQUEST['organ_user_therapy_name']) || isset($_REQUEST['organ_user_therapy_kind_id']) || isset($_REQUEST['organ_user_therapy_family']) || isset($_REQUEST['organ_user_therapy_national_code']) || isset($_REQUEST['organ_user_therapy_gender_id']))) {
                                                                         $query .= ",";
                                                                     }
                                                                     if (isset($_REQUEST['organ_user_therapy_month'])) {
                                                                         $organ_user_therapy_month = $this->post('organ_user_therapy_month');
                                                                         $query .= "organ_user_therapy_month=" . $organ_user_therapy_month . " ";
                                                                     }

                                                                     if (isset($_REQUEST['organ_user_therapy_day']) && (isset($_REQUEST['organ_user_therapy_month']) || isset($_REQUEST['organ_user_therapy_year']) || isset($_REQUEST['organ_user_therapy_name']) || isset($_REQUEST['organ_user_therapy_kind_id']) || isset($_REQUEST['organ_user_therapy_family']) || isset($_REQUEST['organ_user_therapy_national_code']) || isset($_REQUEST['organ_user_therapy_gender_id']))) {
                                                                         $query .= ",";
                                                                     }
                                                                     if (isset($_REQUEST['organ_user_therapy_day'])) {
                                                                         $organ_user_therapy_day = $this->post('organ_user_therapy_day');
                                                                         $query .= "organ_user_therapy_day=" . $organ_user_therapy_day . " ";
                                                                     }

                                                                     if (isset($_REQUEST['organ_user_therapy_fathername']) && (isset($_REQUEST['organ_user_therapy_day']) || isset($_REQUEST['organ_user_therapy_month']) || isset($_REQUEST['organ_user_therapy_year']) || isset($_REQUEST['organ_user_therapy_name']) || isset($_REQUEST['organ_user_therapy_kind_id']) || isset($_REQUEST['organ_user_therapy_family']) || isset($_REQUEST['organ_user_therapy_national_code']) || isset($_REQUEST['organ_user_therapy_gender_id']))) {
                                                                         $query .= ",";
                                                                     }
                                                                     if (isset($_REQUEST['organ_user_therapy_fathername'])) {
                                                                         $organ_user_therapy_fathername = $this->post('organ_user_therapy_fathername');
                                                                         $query .= "organ_user_therapy_fathername='" .$organ_user_therapy_fathername . "' ";
                                                                     }

                                                                     if (isset($_REQUEST['organ_user_therapy_kinship_id']) && (isset($_REQUEST['organ_user_therapy_fathername']) || isset($_REQUEST['organ_user_therapy_day']) || isset($_REQUEST['organ_user_therapy_month']) || isset($_REQUEST['organ_user_therapy_year']) || isset($_REQUEST['organ_user_therapy_name']) || isset($_REQUEST['organ_user_therapy_kind_id']) || isset($_REQUEST['organ_user_therapy_family']) || isset($_REQUEST['organ_user_therapy_national_code']) || isset($_REQUEST['organ_user_therapy_gender_id']))) {
                                                                         $query .= ",";
                                                                     }
                                                                     if (isset($_REQUEST['organ_user_therapy_kinship_id'])) {
                                                                         $organ_user_therapy_kinship_id = $this->post('organ_user_therapy_kinship_id');
                                                                         $query .= "organ_user_therapy_kinship_id=" . $organ_user_therapy_kinship_id . " ";
                                                                     }

                                                                     if (isset($_REQUEST['organ_user_therapy_basebime_id']) && (isset($_REQUEST['organ_user_therapy_kinship_id']) || isset($_REQUEST['organ_user_therapy_fathername']) || isset($_REQUEST['organ_user_therapy_day']) || isset($_REQUEST['organ_user_therapy_month']) || isset($_REQUEST['organ_user_therapy_year']) || isset($_REQUEST['organ_user_therapy_name']) || isset($_REQUEST['organ_user_therapy_kind_id']) || isset($_REQUEST['organ_user_therapy_family']) || isset($_REQUEST['organ_user_therapy_national_code']) || isset($_REQUEST['organ_user_therapy_gender_id']))) {
                                                                         $query .= ",";
                                                                     }
                                                                     if (isset($_REQUEST['organ_user_therapy_basebime_id'])) {
                                                                         $organ_user_therapy_basebime_id = $this->post('organ_user_therapy_basebime_id');
                                                                         $query .= "organ_user_therapy_basebime_id=" . $organ_user_therapy_basebime_id . " ";
                                                                     }

                                                                     if (isset($_REQUEST['organ_user_therapy_bank_id']) && (isset($_REQUEST['organ_user_therapy_basebime_id']) || isset($_REQUEST['organ_user_therapy_kinship_id']) || isset($_REQUEST['organ_user_therapy_fathername']) || isset($_REQUEST['organ_user_therapy_day']) || isset($_REQUEST['organ_user_therapy_month']) || isset($_REQUEST['organ_user_therapy_year']) || isset($_REQUEST['organ_user_therapy_name']) || isset($_REQUEST['organ_user_therapy_kind_id']) || isset($_REQUEST['organ_user_therapy_family']) || isset($_REQUEST['organ_user_therapy_national_code']) || isset($_REQUEST['organ_user_therapy_gender_id']))) {
                                                                         $query .= ",";
                                                                     }
                                                                     if (isset($_REQUEST['organ_user_therapy_bank_id'])) {
                                                                         $organ_user_therapy_bank_id = $this->post('organ_user_therapy_bank_id');
                                                                         $query .= "organ_user_therapy_bank_id=" . $organ_user_therapy_bank_id . " ";
                                                                     }

                                                                     if (isset($_REQUEST['organ_user_therapy_cardno']) && (isset($_REQUEST['organ_user_therapy_basebime_id']) || isset($_REQUEST['organ_user_therapy_kinship_id']) || isset($_REQUEST['organ_user_therapy_fathername']) || isset($_REQUEST['organ_user_therapy_day']) || isset($_REQUEST['organ_user_therapy_month']) || isset($_REQUEST['organ_user_therapy_year']) || isset($_REQUEST['organ_user_therapy_name']) || isset($_REQUEST['organ_user_therapy_kind_id']) || isset($_REQUEST['organ_user_therapy_family']) || isset($_REQUEST['organ_user_therapy_national_code']) || isset($_REQUEST['organ_user_therapy_gender_id']))) {
                                                                         $query .= ",";
                                                                     }
                                                                     if (isset($_REQUEST['organ_user_therapy_cardno'])) {
                                                                         $organ_user_therapy_cardno = $this->post('organ_user_therapy_cardno');
                                                                         $query .= "organ_user_therapy_cardno='" . $organ_user_therapy_cardno . "' ";
                                                                     }

                                                                     if (isset($_REQUEST['organ_user_therapy_accno']) && (isset($_REQUEST['organ_user_therapy_cardno']) || isset($_REQUEST['organ_user_therapy_basebime_id']) || isset($_REQUEST['organ_user_therapy_kinship_id']) || isset($_REQUEST['organ_user_therapy_fathername']) || isset($_REQUEST['organ_user_therapy_day']) || isset($_REQUEST['organ_user_therapy_month']) || isset($_REQUEST['organ_user_therapy_year']) || isset($_REQUEST['organ_user_therapy_name']) || isset($_REQUEST['organ_user_therapy_kind_id']) || isset($_REQUEST['organ_user_therapy_family']) || isset($_REQUEST['organ_user_therapy_national_code']) || isset($_REQUEST['organ_user_therapy_gender_id']))) {
                                                                         $query .= ",";
                                                                     }
                                                                     if (isset($_REQUEST['organ_user_therapy_accno'])) {
                                                                         $organ_user_therapy_accno = $this->post('organ_user_therapy_accno');
                                                                         $query .= "organ_user_therapy_accno='" . $organ_user_therapy_accno . "' ";
                                                                     }

                                                                     if (isset($_REQUEST['organ_user_therapy_shebano']) && (isset($_REQUEST['organ_user_therapy_accno']) || isset($_REQUEST['organ_user_therapy_cardno']) || isset($_REQUEST['organ_user_therapy_basebime_id']) || isset($_REQUEST['organ_user_therapy_kinship_id']) || isset($_REQUEST['organ_user_therapy_fathername']) || isset($_REQUEST['organ_user_therapy_day']) || isset($_REQUEST['organ_user_therapy_month']) || isset($_REQUEST['organ_user_therapy_year']) || isset($_REQUEST['organ_user_therapy_name']) || isset($_REQUEST['organ_user_therapy_kind_id']) || isset($_REQUEST['organ_user_therapy_family']) || isset($_REQUEST['organ_user_therapy_national_code']) || isset($_REQUEST['organ_user_therapy_gender_id']))) {
                                                                         $query .= ",";
                                                                     }
                                                                     if (isset($_REQUEST['organ_user_therapy_shebano'])) {
                                                                         $organ_user_therapy_shebano = $this->post('organ_user_therapy_shebano');
                                                                         $query .= "organ_user_therapy_shebano='" . $organ_user_therapy_shebano . "' ";
                                                                     }

                                                                     if (isset($_REQUEST['organ_user_therapy_bimeno']) && (isset($_REQUEST['organ_user_therapy_shebano']) || isset($_REQUEST['organ_user_therapy_accno']) || isset($_REQUEST['organ_user_therapy_cardno']) || isset($_REQUEST['organ_user_therapy_basebime_id']) || isset($_REQUEST['organ_user_therapy_kinship_id']) || isset($_REQUEST['organ_user_therapy_fathername']) || isset($_REQUEST['organ_user_therapy_day']) || isset($_REQUEST['organ_user_therapy_month']) || isset($_REQUEST['organ_user_therapy_year']) || isset($_REQUEST['organ_user_therapy_name']) || isset($_REQUEST['organ_user_therapy_kind_id']) || isset($_REQUEST['organ_user_therapy_family']) || isset($_REQUEST['organ_user_therapy_national_code']) || isset($_REQUEST['organ_user_therapy_gender_id']))) {
                                                                         $query .= ",";
                                                                     }
                                                                     if (isset($_REQUEST['organ_user_therapy_bimeno'])) {
                                                                         $organ_user_therapy_bimeno = $this->post('organ_user_therapy_bimeno');
                                                                         $query .= "organ_user_therapy_bimeno='" . $organ_user_therapy_bimeno . "' ";
                                                                     }

                                                                     if (isset($_REQUEST['organ_user_therapy_idcardno']) && (isset($_REQUEST['organ_user_therapy_bimeno']) || isset($_REQUEST['organ_user_therapy_shebano']) || isset($_REQUEST['organ_user_therapy_accno']) || isset($_REQUEST['organ_user_therapy_cardno']) || isset($_REQUEST['organ_user_therapy_basebime_id']) || isset($_REQUEST['organ_user_therapy_kinship_id']) || isset($_REQUEST['organ_user_therapy_fathername']) || isset($_REQUEST['organ_user_therapy_day']) || isset($_REQUEST['organ_user_therapy_month']) || isset($_REQUEST['organ_user_therapy_year']) || isset($_REQUEST['organ_user_therapy_name']) || isset($_REQUEST['organ_user_therapy_kind_id']) || isset($_REQUEST['organ_user_therapy_family']) || isset($_REQUEST['organ_user_therapy_national_code']) || isset($_REQUEST['organ_user_therapy_gender_id']))) {
                                                                         $query .= ",";
                                                                     }
                                                                     if (isset($_REQUEST['organ_user_therapy_idcardno'])) {
                                                                         $organ_user_therapy_idcardno = $this->post('organ_user_therapy_idcardno');
                                                                         $query .= "organ_user_therapy_idcardno='" . $organ_user_therapy_idcardno . "' ";
                                                                     }

                                                                     if (isset($_REQUEST['organ_user_therapy_main_national_code']) && (isset($_REQUEST['organ_user_therapy_idcardno']) || isset($_REQUEST['organ_user_therapy_bimeno']) || isset($_REQUEST['organ_user_therapy_shebano']) || isset($_REQUEST['organ_user_therapy_accno']) || isset($_REQUEST['organ_user_therapy_cardno']) || isset($_REQUEST['organ_user_therapy_basebime_id']) || isset($_REQUEST['organ_user_therapy_kinship_id']) || isset($_REQUEST['organ_user_therapy_fathername']) || isset($_REQUEST['organ_user_therapy_day']) || isset($_REQUEST['organ_user_therapy_month']) || isset($_REQUEST['organ_user_therapy_year']) || isset($_REQUEST['organ_user_therapy_name']) || isset($_REQUEST['organ_user_therapy_kind_id']) || isset($_REQUEST['organ_user_therapy_family']) || isset($_REQUEST['organ_user_therapy_national_code']) || isset($_REQUEST['organ_user_therapy_gender_id']))) {
                                                                         $query .= ",";
                                                                     }
                                                                     if (isset($_REQUEST['organ_user_therapy_main_national_code'])) {
                                                                         $organ_user_therapy_main_national_code = $this->post('organ_user_therapy_main_national_code');
                                                                         $query .= "organ_user_therapy_main_national_code=" . $organ_user_therapy_main_national_code . " ";
                                                                     }



                                                                     $query .= "where organ_user_therapy_id=" . $organ_user_therapy_id;
                                                                     $result = $this->B_db->run_query_put($query);
                                                                     echo json_encode(array('result' => "ok"
                                                                     , "data" => ''
                                                                     , 'desc' => ' تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                                 }
                                                                 else {
                                                                     echo json_encode(array('result' => $employeetoken[0]
                                                                     , "data" => $employeetoken[1]
                                                                     , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                                 }
                                                             }
                                                             else
                            if ($command == "get_clearing_confirms") {
                            $employeetoken = checkpermissionemployeetoken($employee_token_str, 'delete', 'organ');
                            if ($employeetoken[0] == 'ok') {
                                $organ_id = $this->post('organ_id');
                                $result = $this->B_organ->get_clearing_organ_confirms($organ_id);
                                if (!empty($result)) {

                                    echo json_encode(array('result' => "ok",
                                        'data' => $result
                                    , 'desc' => 'اطلاعات کانفریمهای ارگان بازیابی شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                } else {
                                    echo json_encode(array('result' => "error"
                                    , 'desc' => 'اطلاعات کانفریمها یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                }
                            }else {
                                echo json_encode(array('result' => $employeetoken[0]
                                , "data" => $employeetoken[1]
                                , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                            }

                        }
        }

    }
}
