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
class Agent extends REST_Controller {

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
        if ($this->B_user->checkrequestip('agent', $command, get_client_ip(),50,50)) {
            if ($command == "add_agent") {
                $agent_code = $this->post('agent_code');
                $agent_name = $this->post('agent_name');
                $agent_family = $this->post('agent_family');
                $agent_gender = $this->post('agent_gender');
                $agent_mobile = $this->post('agent_mobile');
                $agent_pass = $this->post('agent_pass');
                $agent_tell = $this->post('agent_tell');
                $agent_email = $this->post('agent_email');
                $agent_required_phone = $this->post('agent_required_phone');
                $agent_address = $this->post('agent_address');
                $agent_state_id = $this->post('agent_state_id');
                $agent_city_id = $this->post('agent_city_id');
                $agent_sector_name = $this->post('agent_sector_name');
                $agent_long = $this->post('agent_long');
                $agent_lat = $this->post('agent_lat');
                $agent_banknum = $this->post('agent_banknum');
                $agent_bankname = $this->post('agent_bankname');
                $agent_banksheba = $this->post('agent_banksheba');
                $agent_image_code = $this->post('agent_image_code');
                $agent_company_id = $this->post('agent_company_id');
                $agent_deactive = $this->post('agent_deactive');
                $agent_status = $this->post('agent_status');
                $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'agent');
                if ($employeetoken[0] == 'ok') {
                    $result = $this->B_agent->get_agent_by($agent_code, $agent_name);
                    if (empty($result)) {
                        $agent_id = $this->B_agent->add_agent($agent_code, $agent_name, $agent_family, $agent_gender, $agent_mobile, $agent_pass, $agent_tell, $agent_email, $agent_required_phone, $agent_address, $agent_state_id, $agent_city_id, $agent_sector_name, $agent_long, $agent_lat, $agent_banknum, $agent_bankname, $agent_banksheba, $agent_image_code, $agent_deactive, $agent_company_id);
                        if (isset($agent_status)) {
                            $agent_status = $this->post('agent_status');
                            $this->B_agent->add_agent_status($agent_id, $agent_status);
                        }
                        echo json_encode(array('result' => "ok"
                        , "data" => array('agent_id' => $agent_id)
                        , 'desc' => 'نماینده اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    } else {
                        $carmode = $result[0];
                        echo json_encode(array('result' => "error"
                        , "data" => array('agent_id' => $carmode['agent_id'])
                        , 'desc' => 'نماینده تکراری است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                } else {
                    echo json_encode(array('result' => $employeetoken[0]
                    , "data" => $employeetoken[1]
                    , 'desc' => $employeetoken[2]));
                }
            } else
                if ($command == "get_agent") {
                    $employeetoken = checkpermissionemployeetoken($employee_token_str, 'view', 'agent');
                    if ($employeetoken[0] == 'ok') {
                        $query = "select * from agent_tb,state_tb,city_tb,company_tb where company_id=agent_company_id AND city_id=agent_city_id AND state_id=agent_state_id AND ";
                        if (isset($_REQUEST['agent_state_id'])) {
                            $agent_state_id = $this->post('agent_state_id');
                            $query .= " agent_state_id=$agent_state_id ";
                        } else {
                            $query .= " 1=1 ";
                        }
                        $query .= " AND ";
                        if (isset($_REQUEST['agent_city_id'])) {
                            $agent_city_id = $this->post('agent_city_id');
                            $query .= " agent_city_id=$agent_city_id ";
                        } else {
                            $query .= " 1=1 ";
                        }
                        $query .= " AND ";
                        if (isset($_REQUEST['agent_mobile'])) {
                            $agent_mobile = $this->post('agent_mobile');
                            $query .= " agent_mobile=$agent_mobile ";
                        } else {
                            $query .= " 1=1 ";
                        }
                        $query .= " AND ";
                        if (isset($_REQUEST['agent_company_id'])) {
                            $agent_company_id = $this->post('agent_company_id');
                            $query .= " agent_company_id=$agent_company_id ";
                        } else {
                            $query .= " 1=1 ";
                        }
                        $query .= " ORDER BY agent_id ASC";
                        $result = $this->B_db->run_query($query);
                        $output = array();
                        foreach ($result as $row) {
                            $record = array();
                            $record['agent_id'] = $row['agent_id'];
                            $record['agent_code'] = $row['agent_code'];
                            $record['agent_name'] = $row['agent_name'];
                            $record['agent_family'] = $row['agent_family'];
                            $record['agent_gender'] = $row['agent_gender'];
                            $record['agent_mobile'] = $row['agent_mobile'];
                            $record['agent_tell'] = $row['agent_tell'];
                            $record['agent_email'] = $row['agent_email'];
                            $record['agent_required_phone'] = $row['agent_required_phone'];
                            $record['agent_address'] = $row['agent_address'];
                            $record['agent_state_id'] = $row['agent_state_id'];
                            $record['agent_city_id'] = $row['agent_city_id'];
                            $record['company_id'] = $row['company_id'];
                            $record['agent_company_name'] = $row['company_name'];
                            $record['agent_company_logo_url'] = IMGADD . $row['company_logo_url'];
                            $record['agent_state_name'] = $row['state_name'];
                            $record['agent_city_name'] = $row['city_name'];
                            $record['agent_sector_name'] = $row['agent_sector_name'];
                            $record['agent_long'] = $row['agent_long'];
                            $record['agent_lat'] = $row['agent_lat'];
                            $record['agent_banknum'] = $row['agent_banknum'];
                            $record['agent_bankname'] = $row['agent_bankname'];
                            $record['agent_banksheba'] = $row['agent_banksheba'];
                            $record['agent_image_code'] = $row['agent_image_code'];
                            $result1 = $this->B_db->get_image($row['agent_image_code']);
                            if ($result1) {
                                $image = $result1[0];
                                if ($image['image_tumb_url']) {
                                    $record['fieldinsurance_timage'] =  $image['image_tumb_url'];
                                }
                                if ($image['image_url']) {
                                    $record['fieldinsurance_image'] =  $image['image_url'];
                                }
                            }
                            $record['agent_company_id'] = $row['agent_company_id'];
                            $record['agent_deactive'] = $row['agent_deactive'];
                            $record['agent_register_date'] = $row['agent_register_date'];
                            $result1 = $this->B_agent->get_agent_status($row['agent_id']);
                            $agent_statuss1 = $result1[0];
                            $record['agent_status'] = $agent_statuss1['agent_status'];
                            $output[] = $record;
                        }
                        echo json_encode(array('result' => "ok"
                        , "data" => $output
                        , 'desc' => 'مشحصات نماینده با  موفقیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    } else {
                        echo json_encode(array('result' => $employeetoken[0]
                        , "data" => $employeetoken[1]
                        , 'desc' => $employeetoken[2]));
                    }
                } else
                    if ($command == "delete_agent") {
                        $output = array();
                        $agent_id = $this->post('agent_id');
                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'delete', 'agent');
                        if ($employeetoken[0] == 'ok') {
                            $user_id = $employeetoken[1];
                            $result = $this->B_agent->del_agent($agent_id);
                            if ($result) {
                                echo json_encode(array('result' => "ok"
                                , "data" => $output
                                , 'desc' => 'نماینده مورد نظر حذف شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            } else {
                                echo json_encode(array('result' => "error"
                                , "data" => $output
                                , 'desc' => 'نماینده مورد نظر حذف نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }
                        } else {
                            echo json_encode(array('result' => $employeetoken[0]
                            , "data" => $employeetoken[1]
                            , 'desc' => $employeetoken[2]));
                        }
                    } else
                        if ($command == "modify_agent") {
                            $agent_id = $this->post('agent_id');
                            $employeetoken = checkpermissionemployeetoken($employee_token_str, 'modify', 'agent');
                            if ($employeetoken[0] == 'ok') {
                                $query = "UPDATE agent_tb SET ";
                                if (isset($_REQUEST['agent_code'])) {
                                    $agent_code = $this->post('agent_code');
                                    $query .= "agent_code='" . $agent_code . "' ";
                                }

                                if (isset($_REQUEST['agent_name']) && isset($_REQUEST['agent_code'])) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['agent_name'])) {
                                    $agent_name = $this->post('agent_name');
                                    $query .= "agent_name='" . $agent_name . "'";
                                }

                                if (isset($_REQUEST['agent_family']) && (isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_code']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['agent_family'])) {
                                    $agent_family = $this->post('agent_family');
                                    $query .= "agent_family='" . $agent_family . "'";
                                }

                                if (isset($_REQUEST['agent_gender']) && (isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_code']) || isset($_REQUEST['agent_family']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['agent_gender'])) {
                                    $agent_gender = $this->post('agent_gender');
                                    $query .= "agent_gender='" . $agent_gender . "'";
                                }

                                if (isset($_REQUEST['agent_mobile']) && (isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_code']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['agent_mobile'])) {
                                    $agent_mobile = $this->post('agent_mobile');
                                    $query .= "agent_mobile='" . $agent_mobile . "'";
                                }

                                if (isset($_REQUEST['agent_pass']) && (isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_code']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['agent_pass'])) {
                                    $agent_pass = $this->post('agent_pass');
                                    $query .= "agent_pass='" . $agent_pass . "' ";
                                }

                                if (isset($_REQUEST['agent_tell']) && (isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_code']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['agent_tell'])) {
                                    $agent_tell = $this->post('agent_tell');
                                    $query .= "agent_tell='" . $agent_tell . "' ";
                                }

                                if (isset($_REQUEST['agent_email']) && (isset($_REQUEST['agent_tell']) || isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_code']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['agent_email'])) {
                                    $agent_email = $this->post('agent_email');
                                    $query .= "agent_email='" . $agent_email . "' ";
                                }

                                if (isset($_REQUEST['agent_required_phone']) && (isset($_REQUEST['agent_email']) || isset($_REQUEST['agent_tell']) || isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_code']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['agent_required_phone'])) {
                                    $agent_required_phone = $this->post('agent_required_phone');
                                    $query .= "agent_required_phone='" . $_REQUEST['agent_required_phone'] . "' ";
                                }

                                if (isset($_REQUEST['agent_address']) && (isset($_REQUEST['agent_required_phone']) || isset($_REQUEST['agent_email']) || isset($_REQUEST['agent_tell']) || isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_code']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['agent_address'])) {
                                    $agent_address = $this->post('agent_address');
                                    $query .= "agent_address='" . $agent_address . "' ";
                                }

                                if (isset($_REQUEST['agent_state_id']) && (isset($_REQUEST['agent_address']) || isset($_REQUEST['agent_required_phone']) || isset($_REQUEST['agent_email']) || isset($_REQUEST['agent_tell']) || isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_code']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['agent_state_id'])) {
                                    $agent_state_id = $this->post('agent_state_id');
                                    $query .= "agent_state_id=" . $agent_state_id . " ";
                                }

                                if (isset($_REQUEST['agent_city_id']) && (isset($_REQUEST['agent_state_id']) || isset($_REQUEST['agent_address']) || isset($_REQUEST['agent_required_phone']) || isset($_REQUEST['agent_email']) || isset($_REQUEST['agent_tell']) || isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_code']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['agent_city_id'])) {
                                    $agent_city_id = $this->post('agent_city_id');
                                    $query .= "agent_city_id=" . $agent_city_id . " ";
                                }

                                if (isset($_REQUEST['agent_sector_name']) && (isset($_REQUEST['agent_state_id']) || isset($_REQUEST['agent_address']) || isset($_REQUEST['agent_required_phone']) || isset($_REQUEST['agent_email']) || isset($_REQUEST['agent_tell']) || isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_code']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['agent_sector_name'])) {
                                    $agent_sector_name = $this->post('agent_sector_name');
                                    $query .= "agent_sector_name='" . $agent_sector_name . "' ";
                                }

                                if (isset($_REQUEST['agent_long']) && (isset($_REQUEST['agent_sector_name']) || isset($_REQUEST['agent_state_id']) || isset($_REQUEST['agent_address']) || isset($_REQUEST['agent_required_phone']) || isset($_REQUEST['agent_email']) || isset($_REQUEST['agent_tell']) || isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_code']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['agent_long'])) {
                                    $agent_long = $this->post('agent_long');
                                    $query .= "agent_long='" . $agent_long . "' ";
                                }

                                if (isset($_REQUEST['agent_lat']) && (isset($_REQUEST['agent_long']) || isset($_REQUEST['agent_sector_name']) || isset($_REQUEST['agent_state_id']) || isset($_REQUEST['agent_address']) || isset($_REQUEST['agent_required_phone']) || isset($_REQUEST['agent_email']) || isset($_REQUEST['agent_tell']) || isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_code']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['agent_lat'])) {
                                    $agent_lat = $this->post('agent_lat');
                                    $query .= "agent_lat='" . $agent_lat . "' ";
                                }

                                if (isset($_REQUEST['agent_image_code']) && (isset($_REQUEST['agent_lat']) || isset($_REQUEST['agent_long']) || isset($_REQUEST['agent_sector_name']) || isset($_REQUEST['agent_state_id']) || isset($_REQUEST['agent_address']) || isset($_REQUEST['agent_required_phone']) || isset($_REQUEST['agent_email']) || isset($_REQUEST['agent_tell']) || isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_code']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['agent_image_code'])) {
                                    $agent_image_code = $this->post('agent_image_code');
                                    $query .= "agent_image_code='" . $agent_image_code . "' ";
                                }

                                if (isset($_REQUEST['agent_company_id']) && (isset($_REQUEST['agent_image_code']) || isset($_REQUEST['agent_lat']) || isset($_REQUEST['agent_long']) || isset($_REQUEST['agent_sector_name']) || isset($_REQUEST['agent_state_id']) || isset($_REQUEST['agent_address']) || isset($_REQUEST['agent_required_phone']) || isset($_REQUEST['agent_email']) || isset($_REQUEST['agent_tell']) || isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_code']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['agent_company_id'])) {
                                    $agent_company_id = $this->post('agent_company_id');
                                    $query .= "agent_company_id=" . $agent_company_id . " ";
                                }

                                if (isset($_REQUEST['agent_deactive']) && (isset($_REQUEST['agent_company_id']) || isset($_REQUEST['agent_image_code']) || isset($_REQUEST['agent_lat']) || isset($_REQUEST['agent_long']) || isset($_REQUEST['agent_sector_name']) || isset($_REQUEST['agent_state_id']) || isset($_REQUEST['agent_address']) || isset($_REQUEST['agent_required_phone']) || isset($_REQUEST['agent_email']) || isset($_REQUEST['agent_tell']) || isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_code']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['agent_deactive'])) {
                                    $agent_deactive = $this->post('agent_deactive');
                                    $query .= "agent_deactive=" . $agent_deactive . " ";
                                }

                                if (isset($_REQUEST['agent_banknum']) && (isset($_REQUEST['agent_deactive']) || isset($_REQUEST['agent_company_id']) || isset($_REQUEST['agent_image_code']) || isset($_REQUEST['agent_lat']) || isset($_REQUEST['agent_long']) || isset($_REQUEST['agent_sector_name']) || isset($_REQUEST['agent_state_id']) || isset($_REQUEST['agent_address']) || isset($_REQUEST['agent_required_phone']) || isset($_REQUEST['agent_email']) || isset($_REQUEST['agent_tell']) || isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_code']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['agent_banknum'])) {
                                    $agent_banknum = $this->post('agent_banknum');
                                    $query .= "agent_banknum='" . $agent_banknum . "' ";
                                }

                                if (isset($_REQUEST['agent_bankname']) && (isset($_REQUEST['agent_banknum']) || isset($_REQUEST['agent_deactive']) || isset($_REQUEST['agent_company_id']) || isset($_REQUEST['agent_image_code']) || isset($_REQUEST['agent_lat']) || isset($_REQUEST['agent_long']) || isset($_REQUEST['agent_sector_name']) || isset($_REQUEST['agent_state_id']) || isset($_REQUEST['agent_address']) || isset($_REQUEST['agent_required_phone']) || isset($_REQUEST['agent_email']) || isset($_REQUEST['agent_tell']) || isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_code']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['agent_bankname'])) {
                                    $agent_bankname = $this->post('agent_bankname');
                                    $query .= "agent_bankname='" . $agent_bankname . "' ";
                                }

                                if (isset($_REQUEST['agent_banksheba']) && (isset($_REQUEST['agent_bankname']) || isset($_REQUEST['agent_banknum']) || isset($_REQUEST['agent_deactive']) || isset($_REQUEST['agent_company_id']) || isset($_REQUEST['agent_image_code']) || isset($_REQUEST['agent_lat']) || isset($_REQUEST['agent_long']) || isset($_REQUEST['agent_sector_name']) || isset($_REQUEST['agent_state_id']) || isset($_REQUEST['agent_address']) || isset($_REQUEST['agent_required_phone']) || isset($_REQUEST['agent_email']) || isset($_REQUEST['agent_tell']) || isset($_REQUEST['agent_pass']) || isset($_REQUEST['agent_name']) || isset($_REQUEST['agent_code']) || isset($_REQUEST['agent_family']) || isset($_REQUEST['agent_gender']) || isset($_REQUEST['agent_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['agent_banksheba'])) {
                                    $agent_banksheba = $this->post('agent_banksheba');
                                    $query .= "agent_banksheba='" . $agent_banksheba . "' ";
                                }

                                $query .= "where agent_id=" . $agent_id;
                                $result = $this->B_db->run_query_put($query);
                                if (isset($_REQUEST['agent_status'])) {
                                    $agent_status = $this->post('agent_status');
                                    $result = $this->B_agent->get_agent_byid($agent_id);
                                    $agent_old_status = $result[0];
                                    $agent_old_status = $agent_old_status['agent_status'];
                                    if ($agent_old_status) {
                                        $agent_old_status = 0;
                                    } else {
                                        $agent_old_status = 1;
                                    }
                                    if ($agent_old_status != $agent_status) {
                                    } else {

                                        $result = $this->B_agent->add_agent_status($agent_id, $agent_status);
                                    }
                                }
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
                                , 'desc' => $employeetoken[2]));
                            }
                        } else
                            if ($command=="add_agent_extraemployee")
                            {
                                $agent_extra_agent_id=$this->post('agent_extra_agent_id');
                                $agent_extra_employee_id=$this->post('agent_extra_employee_id');

                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','agent');
                                if($employeetoken[0]=='ok')
                                {
                                    $query="select * from agent_extra_tb where agent_extra_employee_id=".$agent_extra_employee_id." AND agent_extra_agent_id=".$agent_extra_agent_id."";

                                    $result=$this->B_db->run_query($query);
                                    $num=count($result[0]);
                                    if ($num==0)
                                    {

                                        $query="INSERT INTO agent_extra_tb(agent_extra_agent_id,agent_extra_employee_id)
    VALUES ( $agent_extra_agent_id,$agent_extra_employee_id);";
                                        $result=$this->B_db->run_query_put($query);
                                        $agent_extra_id=$this->db->insert_id();

                                        echo json_encode(array('result'=>"ok"
                                        ,"data"=>array('agent_extra_id'=>$agent_extra_id)
                                        ,'desc'=>'   کارمند جدید شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }else{
                                        $carmode=$result[0];
                                        echo json_encode(array('result'=>"error"
                                        ,"data"=>array('agent_extra_id'=>$carmode['agent_extra_id'])
                                        ,'desc'=>' کارمند تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }
                                }else{
                                    echo json_encode(array('result'=>$employeetoken[0]
                                    ,"data"=>$employeetoken[1]
                                    ,'desc'=>$employeetoken[2]));
                                }
                            }
                            else
                                if ($command=="get_agent_extraemployee")
                                {
                                    $agent_id=$this->post('agent_id');
                                    $query="select * from agent_extra_tb,employee_tb where employee_id=agent_extra_employee_id AND agent_extra_agent_id=$agent_id  ORDER BY agent_extra_id ASC";

                                    $result = $this->B_db->run_query($query);
                                    $output =array();
                                    foreach($result as $row)
                                    {
                                        $record=array();
                                        $record['agent_extra_id']=$row['agent_extra_id'];
                                        $record['agent_extra_agent_id']=$row['agent_extra_agent_id'];
                                        $record['agent_extra_employee_id']=$row['agent_extra_employee_id'];
                                        $record['employee_name']=$row['employee_name'];
                                        $record['employee_family']=$row['employee_family'];
                                        $output[]=$record;
                                    }
                                    echo json_encode(array('result'=>"ok"
                                    ,"data"=>$output
                                    ,'desc'=>'لیست کارمندان این نمایندگی ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }
                                else
                                    if ($command=="delete_agent_extraemployee")
                                    {
                                        $agent_extra_id=$this->post('agent_extra_id');

                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','agent');
                                        if($employeetoken[0]=='ok')
                                        {
                                            $output =array();
                                            $query="DELETE FROM agent_extra_tb  where agent_extra_id=".$agent_extra_id."";
                                            $result = $this->B_db->run_query_put($query);
                                            if($result){echo json_encode(array('result'=>"ok"
                                            ,"data"=>$output
                                            ,'desc'=>'قیمت بیمه نامه حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                            }else{
                                                echo json_encode(array('result'=>"error"
                                                ,"data"=>$output
                                                ,'desc'=>'قیمت بیمه نامه حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                            }
                                        }else{
                                            echo json_encode(array('result'=>$employeetoken[0]
                                            ,"data"=>$employeetoken[1]
                                            ,'desc'=>$employeetoken[2]));
                                        }
                                    }
        }
    }
}