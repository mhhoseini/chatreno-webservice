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
class Employee extends REST_Controller {

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
        if(isset($this->input->request_headers()['Authorization']))$employee_token_str = $this->input->request_headers()['Authorization'];
        $this->load->model('B_user');
        $this->load->helper('my_helper');
        $this->load->model('B_db');
        $this->load->model('B_employee');
        $command = $this->post("command");
        if($this->B_user->checkrequestip('employee',$command,get_client_ip(),50,50)){
            if ($command == "add_employee") {
                $employee_name = $this->post('employee_name');
                $employee_family = $this->post('employee_family');
                $employee_mobile = $this->post('employee_mobile');
                $employee_pass = '123';
                $employee_email = '';
                $employee_image_code = '';
                $employee_deactive = $this->post('employee_deactive');
                if(isset($this->input->request_headers()['Authorization']))$employee_token_str = $this->input->request_headers()['Authorization'];
                $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','employee');
                if ($employeetoken[0] == 'ok') {
                    $result = $this->B_employee->get_employee_by_mobile($employee_mobile);
                    $num = count($result[0]);
                    if ($num == 0) {
                        $employee_id = $this->B_employee->add_employee($employee_name,$employee_family,$employee_mobile,$employee_pass,$employee_email,$employee_deactive,$employee_image_code);
                        echo json_encode(array('result' => "ok"
                        , "data" => array('employee_id' => $employee_id)
                        , 'desc' => 'کارمند اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    } else {
                        $carmode = $result[0];
                        echo json_encode(array('result' => "error"
                        , "data" => array('employee_id' => $carmode['employee_id'])
                        , 'desc' => 'کارمند تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                } else {
                    echo json_encode(array('result' => $employeetoken[0]
                    , "data" => $employeetoken[1]
                    , 'desc' => $employeetoken[2]));

                }
            } else
                if ($command == "get_employee") {
                    if(isset($this->input->request_headers()['Authorization']))$employee_token_str = $this->input->request_headers()['Authorization'];
                    $employeetoken = checkpermissionemployeetoken($employee_token_str, 'view', 'employee');
                    if ($employeetoken[0] == 'ok') {
                        $query = "select * from employee_tb where ";
                        if (isset($_REQUEST['filter1'])) {
                            $filter1 = $this->post('filter1');
                            $query .= $filter1;
                        } else {
                            $query .= " 1=1 ";
                        }
                        $query .= " AND ";
                        if (isset($_REQUEST['filter2'])) {
                            $filter2 = $this->post('filter2');
                            $query .= $filter2;
                        } else {
                            $query .= " 1=1 ";
                        }
                        $query .= " AND ";
                        if (isset($_REQUEST['filter3'])) {
                            $filter3 = $this->post('filter3');
                            $query .= $filter3;
                        } else {
                            $query .= " 1=1 ";
                        }
                        $query .= " ORDER BY employee_id ASC";
                        $result = $this->B_db->run_query($query);
                        $output = array();
                        foreach($result as $row){
                            $record = array();
                            $record['employee_id'] = $row['employee_id'];
                            $record['employee_name'] = $row['employee_name'];
                            $record['employee_family'] = $row['employee_family'];
                            $record['employee_mobile'] = $row['employee_mobile'];
                            $record['employee_email'] = $row['employee_email'];
                            $result1 = $this->B_db->get_image($row['employee_image_code']);
                            $image = $result1[0];
                            if ($image['image_tumb_url']) {
                                $record['employee_image'] =  $image['image_tumb_url'];
                            }
                            $record['employee_deactive'] = $row['employee_deactive'];
                            $record['employee_register_date'] = $row['employee_register_date'];
                            $output[] = $record;
                        }
                        echo json_encode(array('result' => "ok"
                        , "data" => $output
                        , 'desc' => 'مشحصات کارمند با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    } else {
                        echo json_encode(array('result' => $employeetoken[0]
                        , "data" => $employeetoken[1]
                        , 'desc' => $employeetoken[2]));

                    }
                } else   if ($command == "get_employee_byagent") {
                    if(isset($this->input->request_headers()['Authorization']))$employee_token_str = $this->input->request_headers()['Authorization'];
                    $employeetoken = checkpermissionemployeetoken($employee_token_str, 'view', 'employee');
                    if ($employeetoken[0] == 'ok') {
                        $query = "select DISTINCT employee_tb.* from employee_tb,agent_extra_tb where agent_extra_employee_id=employee_id AND ";

                        if (isset($_REQUEST['agent_id'])) {
                            $agent_id= $this->post('agent_id');
                            $query .= " agent_extra_agent_id=". $agent_id;
                        } else {
                            $query .= " 1=1 ";
                        }

                        $result = $this->B_db->run_query($query);
                        $output = array();
                        foreach($result as $row){
                            $record = array();
                            $record['employee_id'] = $row['employee_id'];
                            $record['employee_name'] = $row['employee_name'];
                            $record['employee_family'] = $row['employee_family'];
                            $record['employee_mobile'] = $row['employee_mobile'];
                            $record['employee_email'] = $row['employee_email'];
                            $result1 = $this->B_db->get_image($row['employee_image_code']);
                            $image = $result1[0];
                            if ($image['image_tumb_url']) {
                                $record['employee_image'] =  $image['image_tumb_url'];
                            }
                            $record['employee_deactive'] = $row['employee_deactive'];
                            $record['employee_register_date'] = $row['employee_register_date'];
                            $output[] = $record;
                        }
                        echo json_encode(array('result' => "ok"
                        , "data" => $output
                        , 'desc' => 'مشحصات کارمند با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    } else {
                        echo json_encode(array('result' => $employeetoken[0]
                        , "data" => $employeetoken[1]
                        , 'desc' => $employeetoken[2]));

                    }
                } else
                    if ($command == "delete_employee") {

                        $employee_id = $this->post('employee_id');
                        if(isset($this->input->request_headers()['Authorization']))$employee_token_str = $this->input->request_headers()['Authorization'];
                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'delete', 'employee');
                        $output = array();
                        if ($employeetoken[0] == 'ok') {
                            $user_id = $employeetoken[1];
                            $result = $this->B_employee->del_employee($employee_id);
                            if ($result) {
                                echo json_encode(array('result' => "ok"
                                , "data" => $output
                                , 'desc' => 'کارمند مورد نظر حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            } else {
                                echo json_encode(array('result' => "error"
                                , "data" => $output
                                , 'desc' => 'کارمند مورد نظر حذف شد '),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
                        } else {
                            echo json_encode(array('result' => $employeetoken[0]
                            , "data" => $employeetoken[1]
                            , 'desc' => $employeetoken[2]));

                        }
                    } else
                        if ($command == "modify_employee") {
                            $employee_id = $this->post('employee_id');
                            if(isset($this->input->request_headers()['Authorization']))$employee_token_str = $this->input->request_headers()['Authorization'];
                            $employeetoken = checkpermissionemployeetoken($employee_token_str, 'modify', 'employee');
                            if ($employeetoken[0] == 'ok') {
                                $query = "UPDATE employee_tb SET ";
                                if (isset($_REQUEST['employee_name'])) {
                                    $employee_name = $this->post('employee_name');
                                    $query .= "employee_name='" . $employee_name . "'";
                                }

                                if (isset($_REQUEST['employee_family']) && (isset($_REQUEST['employee_name']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['employee_family'])) {
                                    $employee_family = $this->post('employee_family');
                                    $query .= "employee_family='" . $employee_family . "'";
                                }


                                if (isset($_REQUEST['employee_mobile']) && (isset($_REQUEST['employee_name']) || isset($_REQUEST['employee_family']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['employee_mobile'])) {
                                    $employee_mobile = $this->post('employee_mobile');
                                    $query .= "employee_mobile='" . $employee_mobile . "'";
                                }

                                if (isset($_REQUEST['employee_pass']) && (isset($_REQUEST['employee_name']) || isset($_REQUEST['employee_family']) || isset($_REQUEST['employee_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['employee_pass'])) {
                                    $employee_pass = $this->post('employee_pass');
                                    $query .= "employee_pass='" . $employee_pass . "' ";
                                }


                                if (isset($_REQUEST['employee_email']) && (isset($_REQUEST['employee_pass']) || isset($_REQUEST['employee_name']) || isset($_REQUEST['employee_family']) || isset($_REQUEST['employee_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['employee_email'])) {
                                    $employee_email = $this->post('employee_email');
                                    $query .= "employee_email='" . $employee_email . "' ";
                                }


                                if (isset($_REQUEST['employee_deactive']) && (isset($_REQUEST['employee_email']) || isset($_REQUEST['employee_pass']) || isset($_REQUEST['employee_name']) || isset($_REQUEST['employee_family']) || isset($_REQUEST['employee_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['employee_deactive'])) {
                                    $employee_deactive = $this->post('employee_deactive',0);
                                    $query .= "employee_deactive=" . $employee_deactive . " ";
                                }

                                if (isset($_REQUEST['employee_image_code']) && (isset($_REQUEST['employee_deactive']) || isset($_REQUEST['employee_email']) || isset($_REQUEST['employee_pass']) || isset($_REQUEST['employee_name']) || isset($_REQUEST['employee_family']) || isset($_REQUEST['employee_mobile']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['employee_image_code'])) {
                                    $employee_image_code = $this->post('employee_image_code');
                                    $query .= "employee_image_code=" . $employee_image_code . " ";
                                }


                                $query .= "where employee_id=" . $employee_id;
                                $result = $this->B_db->run_query_put($query);
                                if ($result) {
                                    echo json_encode(array('result' => "ok"
                                    , "data" => ""
                                    , 'desc' => 'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                } else {
                                    echo json_encode(array('result' => "ok"
                                    , "data" => ""
                                    , 'desc' => 'تغییرات انجام نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }
                            } else {
                                echo json_encode(array('result' => $employeetoken[0]
                                , "data" => $employeetoken[1]
                                , 'desc' => $employeetoken[2]));

                            }
                        } else
                            if ($command == "get_employee_permission") {
                                if(isset($this->input->request_headers()['Authorization']))$employee_token_str = $this->input->request_headers()['Authorization'];
                                $employeetoken = checkpermissionemployeetoken($employee_token_str, 'view', 'employee');
                                if ($employeetoken[0] == 'ok') {
                                    $query = "select * from emloyee_permision_tb,permision_entity_tb,permision_mode_tb,employee_tb where employee_id=emloyee_permision_emloyee_id AND permision_entity_name=emloyee_permision_entity AND emloyee_permision_mode=permision_mode_name AND ";
                                    if (isset($_REQUEST['filter1'])) {
                                        $filter1 = $this->post('filter1');
                                        $query .= $filter1;
                                    } else {
                                        $query .= " 1=1 ";
                                    }
                                    $query .= " AND ";
                                    if (isset($_REQUEST['filter2'])) {
                                        $filter2 = $this->post('filter2');
                                        $query .= $filter2;
                                    } else {
                                        $query .= " 1=1 ";
                                    }
                                    $query .= " AND ";
                                    if (isset($_REQUEST['filter3'])) {
                                        $filter3 = $this->post('filter3');
                                        $query .= $filter3;
                                    } else {
                                        $query .= " 1=1 ";
                                    }
                                    $query .= " ORDER BY emloyee_permision_id ASC";
                                    $result = $this->B_db->run_query($query);
                                    $output = array();
                                    foreach ($result as $row) {
                                        $record = array();
                                        $record['emloyee_permision_id'] = $row['emloyee_permision_id'];
                                        $record['emloyee_permision_emloyee_id'] = $row['emloyee_permision_emloyee_id'];
                                        $record['employee_name'] = $row['employee_name'] . ' ' . $row['employee_family'];
                                        $record['emloyee_permision_entity'] = $row['emloyee_permision_entity'];
                                        $record['emloyee_permision_mode'] = $row['emloyee_permision_mode'];
                                        $record['permision_mode_desc'] = $row['permision_mode_desc'];
                                        $record['permision_entity_desc'] = $row['permision_entity_desc'];
                                        $output[] = $record;
                                    }

                                    $result1 = $this->B_employee->get_permision_entity();
                                    $output1 = array();
                                    foreach ($result1 as $row1) {
                                        $record1 = array();
                                        $record1['permision_entity_name'] = $row1['permision_entity_name'];
                                        $record1['permision_entity_desc'] = $row1['permision_entity_desc'];
                                        $output1[] = $record1;
                                    }

                                    echo json_encode(array('result' => "ok"
                                    , "data" => $output
                                    , "data1" => $output1
                                    , 'desc' => 'مشحصات دسترسی ها با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                } else {
                                    echo json_encode(array('result' => $employeetoken[0]
                                    , "data" => $employeetoken[1]
                                    , 'desc' => $employeetoken[2]));

                                }
                            } else
                                if ($command == "add_employee_permission") {

                                    $emloyee_permision_emloyee_id = $this->post('emloyee_permision_emloyee_id');
                                    $emloyee_permision_entity = $this->post('emloyee_permision_entity');
                                    $emloyee_permision_mode = $this->post('emloyee_permision_mode');

                                    if(isset($this->input->request_headers()['Authorization']))$employee_token_str = $this->input->request_headers()['Authorization'];
                                    $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'employee');
                                    if ($employeetoken[0] == 'ok') {
                                        $result = $this->B_employee->get_permision_entity_by($emloyee_permision_emloyee_id,$emloyee_permision_entity,$emloyee_permision_mode);
                                        
                                        if (empty($result)) {
                                            $emloyee_permision_emloyee_id = $this->B_employee->add_employee_permision($emloyee_permision_emloyee_id,$emloyee_permision_entity,$emloyee_permision_mode);
                                            echo json_encode(array('result' => "ok"
                                            , "data" => array('emloyee_permision_emloyee_id' => $emloyee_permision_emloyee_id)
                                            , 'desc' => 'دسترسی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                        } else {
                                            $emloyee_permision = $result[0];
                                            echo json_encode(array('result' => "error"
                                            , "data" => array('emloyee_permision_emloyee_id' => $emloyee_permision['emloyee_permision_emloyee_id'])
                                            , 'desc' => 'دسترسی تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                        }
                                    } else {
                                        echo json_encode(array('result' => $employeetoken[0]
                                        , "data" => $employeetoken[1]
                                        , 'desc' => $employeetoken[2]));
                                    }
                                } else
                                    if ($command == "delete_employee_permission") {
                                        $output = array();
                                        $emloyee_permision_id = $this->post('emloyee_permision_id');
                                        if(isset($this->input->request_headers()['Authorization']))$employee_token_str = $this->input->request_headers()['Authorization'];
                                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'delete', 'employee');
                                        if ($employeetoken[0] == 'ok') {
                                            $user_id = $employeetoken[1];
                                            $result = $this->B_employee->del_employee_permision($emloyee_permision_id);
                                            if ($result) {
                                                echo json_encode(array('result' => "ok"
                                                , "data" => $output
                                                , 'desc' => 'دسترسی مورد نظر حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                            } else {
                                                echo json_encode(array('result' => "error"
                                                , "data" => $output
                                                , 'desc' => 'دسترسی مورد نظر حذف نشد '),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                            }
                                        } else {
                                            echo json_encode(array('result' => $employeetoken[0]
                                            , "data" => $employeetoken[1]
                                            , 'desc' => $employeetoken[2]));

                                        }
                                    } else
                                        if ($command == "get_permission_entity") {
                                            if(isset($this->input->request_headers()['Authorization']))$employee_token_str = $this->input->request_headers()['Authorization'];
                                            $employeetoken = checkpermissionemployeetoken($employee_token_str, 'view', 'employee');
                                            if ($employeetoken[0] == 'ok') {
                                                $result = $this->B_employee->get_permision_entity();
                                                $output = array();
                                                foreach ($result as $row) {
                                                    $record = array();
                                                    $record['permision_entity_id'] = $row['permision_entity_id'];
                                                    $record['permision_entity_name'] = $row['permision_entity_name'];
                                                    $record['permision_entity_desc'] = $row['permision_entity_desc'];
                                                    $output[] = $record;
                                                }
                                                echo json_encode(array('result' => "ok"
                                                , "data" => $output
                                                , 'desc' => 'مشحصات دسترسی ها با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                            } else {
                                                echo json_encode(array('result' => $employeetoken[0]
                                                , "data" => $employeetoken[1]
                                                , 'desc' => $employeetoken[2]));

                                            }
                                        } else
                                            if ($command == "get_permission_mode") {
                                                if(isset($this->input->request_headers()['Authorization']))$employee_token_str = $this->input->request_headers()['Authorization'];
                                                $employeetoken = checkpermissionemployeetoken($employee_token_str, 'view', 'employee');
                                                if ($employeetoken[0] == 'ok') {
                                                    $result = $this->B_employee->all_permision_mode();;
                                                    $output = array();
                                                    foreach ($result as $row) {
                                                        $record = array();
                                                        $record['permision_mode_id'] = $row['permision_mode_id'];
                                                        $record['permision_mode_name'] = $row['permision_mode_name'];
                                                        $record['permision_mode_desc'] = $row['permision_mode_desc'];
                                                        $output[] = $record;
                                                    }
                                                    echo json_encode(array('result' => "ok"
                                                    , "data" => $output
                                                    , 'desc' => 'مشحصات دسترسی ها با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                                } else {
                                                    echo json_encode(array('result' => $employeetoken[0]
                                                    , "data" => $employeetoken[1]
                                                    , 'desc' => $employeetoken[2]));

                                                }
                                            }
                                }
    }
}