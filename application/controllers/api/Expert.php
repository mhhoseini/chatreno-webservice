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
class Expert extends REST_Controller {

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
        $this->load->model('B_expert');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('expert', $command, get_client_ip(),50,50)) {
            if ($command == "add_expert") {
                $expert_code = $this->post('expert_code');
                $expert_name = $this->post('expert_name');
                $expert_family = $this->post('expert_family');
                $expert_gender = $this->post('expert_gender');
                $expert_mobile = $this->post('expert_mobile');
                $expert_pass = $this->post('expert_pass');
                $expert_tell = $this->post('expert_tell');
                $expert_email = $this->post('expert_email');
                $expert_required_phone = $this->post('expert_required_phone');
                $expert_address = $this->post('expert_address');
                $expert_state_id = $this->post('expert_state_id');
                $expert_city_id = $this->post('expert_city_id');
                $expert_sector_name = $this->post('expert_sector_name');
                $expert_long = $this->post('expert_long');
                $expert_lat = $this->post('expert_lat');
                $expert_banknum = $this->post('expert_banknum');
                $expert_bankname = $this->post('expert_bankname');
                $expert_banksheba = $this->post('expert_banksheba');
                $expert_image_code = $this->post('expert_image_code');
                $expert_evaluatorco_id = $this->post('expert_evaluatorco_id');
                $expert_deactive = $this->post('expert_deactive');
                $expert_status = $this->post('expert_status');
                $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'expert');
                if ($employeetoken[0] == 'ok') {
                    $result = $this->B_expert->get_expert_by($expert_code, $expert_name);
                    if (empty($result)) {
                        $expert_id = $this->B_expert->add_expert($expert_code, $expert_name, $expert_family, $expert_gender, $expert_mobile, $expert_pass, $expert_tell, $expert_email, $expert_required_phone, $expert_address, $expert_state_id, $expert_city_id, $expert_sector_name, $expert_long, $expert_lat, $expert_banknum, $expert_bankname, $expert_banksheba, $expert_image_code, $expert_deactive, $expert_evaluatorco_id);
                        if (isset($expert_status)) {
                            $expert_status = $this->post('expert_status');
                            $this->B_expert->add_expert_status($expert_id, $expert_status);
                        }
                        echo json_encode(array('result' => "ok"
                        , "data" => array('expert_id' => $expert_id)
                        , 'desc' => 'نماینده اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    } else {
                        $carmode = $result[0];
                        echo json_encode(array('result' => "error"
                        , "data" => array('expert_id' => $carmode['expert_id'])
                        , 'desc' => 'نماینده تکراری است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                } else {
                    echo json_encode(array('result' => $employeetoken[0]
                    , "data" => $employeetoken[1]
                    , 'desc' => $employeetoken[2]));
                }
            } else
                if ($command == "get_expert") {
                    $employeetoken = checkpermissionemployeetoken($employee_token_str, 'view', 'expert');
                    if ($employeetoken[0] == 'ok') {
                        $query = "select * from expert_tb,state_tb,city_tb,evaluatorco_tb where evaluatorco_id=expert_evaluatorco_id AND city_id=expert_city_id AND state_id=expert_state_id AND ";
                        if (isset($_REQUEST['expert_state_id'])) {
                            $expert_state_id = $this->post('expert_state_id');
                            $query .= " expert_state_id=$expert_state_id ";
                        } else {
                            $query .= " 1=1 ";
                        }
                        $query .= " AND ";
                        if (isset($_REQUEST['expert_city_id'])) {
                            $expert_city_id = $this->post('expert_city_id');
                            $query .= " expert_city_id=$expert_city_id ";
                        } else {
                            $query .= " 1=1 ";
                        }
                        $query .= " AND ";
                        if (isset($_REQUEST['expert_mobile'])) {
                            $expert_mobile = $this->post('expert_mobile');
                            $query .= " expert_mobile=$expert_mobile ";
                        } else {
                            $query .= " 1=1 ";
                        }
                        $query .= " AND ";
                        if (isset($_REQUEST['expert_evaluatorco_id'])) {
                            $expert_evaluatorco_id = $this->post('expert_evaluatorco_id');
                            $query .= " expert_evaluatorco_id=$expert_evaluatorco_id ";
                        } else {
                            $query .= " 1=1 ";
                        }
                        $query .= " ORDER BY expert_id ASC";
                        $result = $this->B_db->run_query($query);
                        $output = array();
                        foreach ($result as $row) {
                            $record = array();
                            $record['expert_id'] = $row['expert_id'];
                            $record['expert_code'] = $row['expert_code'];
                            $record['expert_name'] = $row['expert_name'];
                            $record['expert_family'] = $row['expert_family'];
                            $record['expert_gender'] = $row['expert_gender'];
                            $record['expert_mobile'] = $row['expert_mobile'];
                            $record['expert_tell'] = $row['expert_tell'];
                            $record['expert_email'] = $row['expert_email'];
                            $record['expert_required_phone'] = $row['expert_required_phone'];
                            $record['expert_address'] = $row['expert_address'];
                            $record['expert_state_id'] = $row['expert_state_id'];
                            $record['expert_city_id'] = $row['expert_city_id'];
                            $record['evaluatorco_id'] = $row['evaluatorco_id'];
                            $record['expert_evaluatorco_name'] = $row['evaluatorco_name'];
                            $record['expert_evaluatorco_logo_url'] = IMGADD . $row['evaluatorco_logo_url'];
                            $record['expert_state_name'] = $row['state_name'];
                            $record['expert_city_name'] = $row['city_name'];
                            $record['expert_sector_name'] = $row['expert_sector_name'];
                            $record['expert_long'] = $row['expert_long'];
                            $record['expert_lat'] = $row['expert_lat'];
                            $record['expert_banknum'] = $row['expert_banknum'];
                            $record['expert_bankname'] = $row['expert_bankname'];
                            $record['expert_banksheba'] = $row['expert_banksheba'];
                            $record['expert_image_code'] = $row['expert_image_code'];
                            $result1 = $this->B_db->get_image($row['expert_image_code']);
                            if ($result1) {
                                $image = $result1[0];
                                if ($image['image_tumb_url']) {
                                    $record['fieldinsurance_timage'] =  $image['image_tumb_url'];
                                }
                                if ($image['image_url']) {
                                    $record['fieldinsurance_image'] =  $image['image_url'];
                                }
                            }
                            $record['expert_evaluatorco_id'] = $row['expert_evaluatorco_id'];
                            $record['expert_deactive'] = $row['expert_deactive'];
                            $record['expert_register_date'] = $row['expert_register_date'];
                            $result1 = $this->B_expert->get_expert_status($row['expert_id']);
                            $expert_statuss1 = $result1[0];
                            $record['expert_status'] = $expert_statuss1['expert_status'];
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
                    if ($command == "delete_expert") {
                        $output = array();
                        $expert_id = $this->post('expert_id');
                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'delete', 'expert');
                        if ($employeetoken[0] == 'ok') {
                            $user_id = $employeetoken[1];
                            $result = $this->B_expert->del_expert($expert_id);
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
                        if ($command == "modify_expert") {
                            $expert_id = $this->post('expert_id');
                            $employeetoken = checkpermissionemployeetoken($employee_token_str, 'modify', 'expert');
                            if ($employeetoken[0] == 'ok') {
                                $query = "UPDATE expert_tb SET ";
                                if (isset($_REQUEST['expert_code'])) {
                                    $expert_code = $this->post('expert_code');
                                    $query .= "expert_code='" . $expert_code . "' ";
                                }

                                if (isset($_REQUEST['expert_name']) && isset($_REQUEST['expert_code'])) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['expert_name'])) {
                                    $expert_name = $this->post('expert_name');
                                    $query .= "expert_name='" . $expert_name . "'";
                                }

                                if (isset($_REQUEST['expert_family']) && (isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_code']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['expert_family'])) {
                                    $expert_family = $this->post('expert_family');
                                    $query .= "expert_family='" . $expert_family . "'";
                                }

                                if (isset($_REQUEST['expert_gender']) && (isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_code']) || isset($_REQUEST['expert_family']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['expert_gender'])) {
                                    $expert_gender = $this->post('expert_gender');
                                    $query .= "expert_gender='" . $expert_gender . "'";
                                }

                                if (isset($_REQUEST['expert_mobile']) && (isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_code']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['expert_mobile'])) {
                                    $expert_mobile = $this->post('expert_mobile');
                                    $query .= "expert_mobile='" . $expert_mobile . "'";
                                }

                                if (isset($_REQUEST['expert_pass']) && (isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_code']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['expert_pass'])) {
                                    $expert_pass = $this->post('expert_pass');
                                    $query .= "expert_pass='" . $expert_pass . "' ";
                                }

                                if (isset($_REQUEST['expert_tell']) && (isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_code']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['expert_tell'])) {
                                    $expert_tell = $this->post('expert_tell');
                                    $query .= "expert_tell='" . $expert_tell . "' ";
                                }

                                if (isset($_REQUEST['expert_email']) && (isset($_REQUEST['expert_tell']) || isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_code']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['expert_email'])) {
                                    $expert_email = $this->post('expert_email');
                                    $query .= "expert_email='" . $expert_email . "' ";
                                }

                                if (isset($_REQUEST['expert_required_phone']) && (isset($_REQUEST['expert_email']) || isset($_REQUEST['expert_tell']) || isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_code']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['expert_required_phone'])) {
                                    $expert_required_phone = $this->post('expert_required_phone');
                                    $query .= "expert_required_phone='" . $_REQUEST['expert_required_phone'] . "' ";
                                }

                                if (isset($_REQUEST['expert_address']) && (isset($_REQUEST['expert_required_phone']) || isset($_REQUEST['expert_email']) || isset($_REQUEST['expert_tell']) || isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_code']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['expert_address'])) {
                                    $expert_address = $this->post('expert_address');
                                    $query .= "expert_address='" . $expert_address . "' ";
                                }

                                if (isset($_REQUEST['expert_state_id']) && (isset($_REQUEST['expert_address']) || isset($_REQUEST['expert_required_phone']) || isset($_REQUEST['expert_email']) || isset($_REQUEST['expert_tell']) || isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_code']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['expert_state_id'])) {
                                    $expert_state_id = $this->post('expert_state_id');
                                    $query .= "expert_state_id=" . $expert_state_id . " ";
                                }

                                if (isset($_REQUEST['expert_city_id']) && (isset($_REQUEST['expert_state_id']) || isset($_REQUEST['expert_address']) || isset($_REQUEST['expert_required_phone']) || isset($_REQUEST['expert_email']) || isset($_REQUEST['expert_tell']) || isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_code']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['expert_city_id'])) {
                                    $expert_city_id = $this->post('expert_city_id');
                                    $query .= "expert_city_id=" . $expert_city_id . " ";
                                }

                                if (isset($_REQUEST['expert_sector_name']) && (isset($_REQUEST['expert_state_id']) || isset($_REQUEST['expert_address']) || isset($_REQUEST['expert_required_phone']) || isset($_REQUEST['expert_email']) || isset($_REQUEST['expert_tell']) || isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_code']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['expert_sector_name'])) {
                                    $expert_sector_name = $this->post('expert_sector_name');
                                    $query .= "expert_sector_name='" . $expert_sector_name . "' ";
                                }

                                if (isset($_REQUEST['expert_long']) && (isset($_REQUEST['expert_sector_name']) || isset($_REQUEST['expert_state_id']) || isset($_REQUEST['expert_address']) || isset($_REQUEST['expert_required_phone']) || isset($_REQUEST['expert_email']) || isset($_REQUEST['expert_tell']) || isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_code']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['expert_long'])) {
                                    $expert_long = $this->post('expert_long');
                                    $query .= "expert_long='" . $expert_long . "' ";
                                }

                                if (isset($_REQUEST['expert_lat']) && (isset($_REQUEST['expert_long']) || isset($_REQUEST['expert_sector_name']) || isset($_REQUEST['expert_state_id']) || isset($_REQUEST['expert_address']) || isset($_REQUEST['expert_required_phone']) || isset($_REQUEST['expert_email']) || isset($_REQUEST['expert_tell']) || isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_code']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['expert_lat'])) {
                                    $expert_lat = $this->post('expert_lat');
                                    $query .= "expert_lat='" . $expert_lat . "' ";
                                }

                                if (isset($_REQUEST['expert_image_code']) && (isset($_REQUEST['expert_lat']) || isset($_REQUEST['expert_long']) || isset($_REQUEST['expert_sector_name']) || isset($_REQUEST['expert_state_id']) || isset($_REQUEST['expert_address']) || isset($_REQUEST['expert_required_phone']) || isset($_REQUEST['expert_email']) || isset($_REQUEST['expert_tell']) || isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_code']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['expert_image_code'])) {
                                    $expert_image_code = $this->post('expert_image_code');
                                    $query .= "expert_image_code='" . $expert_image_code . "' ";
                                }

                                if (isset($_REQUEST['expert_evaluatorco_id']) && (isset($_REQUEST['expert_image_code']) || isset($_REQUEST['expert_lat']) || isset($_REQUEST['expert_long']) || isset($_REQUEST['expert_sector_name']) || isset($_REQUEST['expert_state_id']) || isset($_REQUEST['expert_address']) || isset($_REQUEST['expert_required_phone']) || isset($_REQUEST['expert_email']) || isset($_REQUEST['expert_tell']) || isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_code']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['expert_evaluatorco_id'])) {
                                    $expert_evaluatorco_id = $this->post('expert_evaluatorco_id');
                                    $query .= "expert_evaluatorco_id=" . $expert_evaluatorco_id . " ";
                                }

                                if (isset($_REQUEST['expert_deactive']) && (isset($_REQUEST['expert_evaluatorco_id']) || isset($_REQUEST['expert_image_code']) || isset($_REQUEST['expert_lat']) || isset($_REQUEST['expert_long']) || isset($_REQUEST['expert_sector_name']) || isset($_REQUEST['expert_state_id']) || isset($_REQUEST['expert_address']) || isset($_REQUEST['expert_required_phone']) || isset($_REQUEST['expert_email']) || isset($_REQUEST['expert_tell']) || isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_code']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['expert_deactive'])) {
                                    $expert_deactive = $this->post('expert_deactive');
                                    $query .= "expert_deactive=" . $expert_deactive . " ";
                                }

                                if (isset($_REQUEST['expert_banknum']) && (isset($_REQUEST['expert_deactive']) || isset($_REQUEST['expert_evaluatorco_id']) || isset($_REQUEST['expert_image_code']) || isset($_REQUEST['expert_lat']) || isset($_REQUEST['expert_long']) || isset($_REQUEST['expert_sector_name']) || isset($_REQUEST['expert_state_id']) || isset($_REQUEST['expert_address']) || isset($_REQUEST['expert_required_phone']) || isset($_REQUEST['expert_email']) || isset($_REQUEST['expert_tell']) || isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_code']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['expert_banknum'])) {
                                    $expert_banknum = $this->post('expert_banknum');
                                    $query .= "expert_banknum='" . $expert_banknum . "' ";
                                }

                                if (isset($_REQUEST['expert_bankname']) && (isset($_REQUEST['expert_banknum']) || isset($_REQUEST['expert_deactive']) || isset($_REQUEST['expert_evaluatorco_id']) || isset($_REQUEST['expert_image_code']) || isset($_REQUEST['expert_lat']) || isset($_REQUEST['expert_long']) || isset($_REQUEST['expert_sector_name']) || isset($_REQUEST['expert_state_id']) || isset($_REQUEST['expert_address']) || isset($_REQUEST['expert_required_phone']) || isset($_REQUEST['expert_email']) || isset($_REQUEST['expert_tell']) || isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_code']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['expert_bankname'])) {
                                    $expert_bankname = $this->post('expert_bankname');
                                    $query .= "expert_bankname='" . $expert_bankname . "' ";
                                }

                                if (isset($_REQUEST['expert_banksheba']) && (isset($_REQUEST['expert_bankname']) || isset($_REQUEST['expert_banknum']) || isset($_REQUEST['expert_deactive']) || isset($_REQUEST['expert_evaluatorco_id']) || isset($_REQUEST['expert_image_code']) || isset($_REQUEST['expert_lat']) || isset($_REQUEST['expert_long']) || isset($_REQUEST['expert_sector_name']) || isset($_REQUEST['expert_state_id']) || isset($_REQUEST['expert_address']) || isset($_REQUEST['expert_required_phone']) || isset($_REQUEST['expert_email']) || isset($_REQUEST['expert_tell']) || isset($_REQUEST['expert_pass']) || isset($_REQUEST['expert_name']) || isset($_REQUEST['expert_code']) || isset($_REQUEST['expert_family']) || isset($_REQUEST['expert_gender']) || isset($_REQUEST['expert_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['expert_banksheba'])) {
                                    $expert_banksheba = $this->post('expert_banksheba');
                                    $query .= "expert_banksheba='" . $expert_banksheba . "' ";
                                }

                                $query .= "where expert_id=" . $expert_id;
                                $result = $this->B_db->run_query_put($query);
                                if (isset($_REQUEST['expert_status'])) {
                                    $expert_status = $this->post('expert_status');
                                    $result = $this->B_expert->get_expert_byid($expert_id);
                                    $expert_old_status = $result[0];
                                    $expert_old_status = $expert_old_status['expert_status'];
                                    if ($expert_old_status) {
                                        $expert_old_status = 0;
                                    } else {
                                        $expert_old_status = 1;
                                    }
                                    if ($expert_old_status != $expert_status) {
                                    } else {

                                        $result = $this->B_expert->add_expert_status($expert_id, $expert_status);
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
                            if ($command=="add_expert_extraemployee")
                            {
                                $expert_extra_expert_id=$this->post('expert_extra_expert_id');
                                $expert_extra_employee_id=$this->post('expert_extra_employee_id');

                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','expert');
                                if($employeetoken[0]=='ok')
                                {
                                    $query="select * from expert_extra_tb where expert_extra_employee_id=".$expert_extra_employee_id." AND expert_extra_expert_id=".$expert_extra_expert_id."";

                                    $result=$this->B_db->run_query($query);
                                    $num=count($result[0]);
                                    if ($num==0)
                                    {

                                        $query="INSERT INTO expert_extra_tb(expert_extra_expert_id,expert_extra_employee_id)
    VALUES ( $expert_extra_expert_id,$expert_extra_employee_id);";
                                        $result=$this->B_db->run_query_put($query);
                                        $expert_extra_id=$this->db->insert_id();

                                        echo json_encode(array('result'=>"ok"
                                        ,"data"=>array('expert_extra_id'=>$expert_extra_id)
                                        ,'desc'=>'   کارمند جدید شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }else{
                                        $carmode=$result[0];
                                        echo json_encode(array('result'=>"error"
                                        ,"data"=>array('expert_extra_id'=>$carmode['expert_extra_id'])
                                        ,'desc'=>' کارمند تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }
                                }else{
                                    echo json_encode(array('result'=>$employeetoken[0]
                                    ,"data"=>$employeetoken[1]
                                    ,'desc'=>$employeetoken[2]));
                                }
                            }
                            else
                                if ($command=="get_expert_extraemployee")
                                {
                                    $expert_id=$this->post('expert_id');
                                    $query="select * from expert_extra_tb,employee_tb where employee_id=expert_extra_employee_id AND expert_extra_expert_id=$expert_id  ORDER BY expert_extra_id ASC";

                                    $result = $this->B_db->run_query($query);
                                    $output =array();
                                    foreach($result as $row)
                                    {
                                        $record=array();
                                        $record['expert_extra_id']=$row['expert_extra_id'];
                                        $record['expert_extra_expert_id']=$row['expert_extra_expert_id'];
                                        $record['expert_extra_employee_id']=$row['expert_extra_employee_id'];
                                        $record['employee_name']=$row['employee_name'];
                                        $record['employee_family']=$row['employee_family'];
                                        $output[]=$record;
                                    }
                                    echo json_encode(array('result'=>"ok"
                                    ,"data"=>$output
                                    ,'desc'=>'لیست کارمندان این نمایندگی ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }
                                else
                                    if ($command=="delete_expert_extraemployee")
                                    {
                                        $expert_extra_id=$this->post('expert_extra_id');

                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','expert');
                                        if($employeetoken[0]=='ok')
                                        {
                                            $output =array();
                                            $query="DELETE FROM expert_extra_tb  where expert_extra_id=".$expert_extra_id."";
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