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
class Buildingquality extends REST_Controller
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
        $this->load->model('B_car');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('buildingquality', $command, get_client_ip(),50,50)) {
            if ($command == "add_buildingquality_costcons") {
                $buildingquality_costcons_id = $this->post('buildingquality_costcons_id');
                $buildingquality_costcons_name = $this->post('buildingquality_costcons_name');
                $buildingquality_costcons_price = $this->post('buildingquality_costcons_price');
                $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'buildingquality');
                if ($employeetoken[0] == 'ok') {
                    $query = "select * from buildingquality_costcons_tb where buildingquality_costcons_name='" . $buildingquality_costcons_name . "'";
                    $result = $this->B_db->run_query($query);
                    $num = count($result[0]);
                    if ($num == 0) {
                        $query = "INSERT INTO buildingquality_costcons_tb(buildingquality_costcons_id, buildingquality_costcons_name, buildingquality_costcons_price)
	                            VALUES ( $buildingquality_costcons_id,'$buildingquality_costcons_name', '$buildingquality_costcons_price');";
                        $result = $this->B_db->run_query_put($query);
                        //  $buildingquality_costcons_id=$this->db->insert_id();

                        echo json_encode(array('result' => "ok"
                        , "data" => array('buildingquality_costcons_id' => $buildingquality_costcons_id)
                        , 'desc' => 'هزینه ساخت بیمه کیفیت ساخت ساختمان اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    } else {
                        $carmode = $result[0];
                        echo json_encode(array('result' => "error"
                        , "data" => array('buildingquality_costcons_id' => $carmode['buildingquality_costcons_id'])
                        , 'desc' => 'هزینه ساخت بیمه کیفیت ساخت ساختمان تکراری است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                } else {
                    echo json_encode(array('result' => $employeetoken[0]
                    , "data" => $employeetoken[1]
                    , 'desc' => $employeetoken[2]));

                }
            } else
                if ($command == "get_buildingquality_costcons") {
                    $query = "select * from buildingquality_costcons_tb where 1 ORDER BY buildingquality_costcons_id ASC";
                    $result = $result = $this->B_db->run_query($query);
                    $output = array();
                    foreach ($result as $row) {
                        $record = array();
                        $record['buildingquality_costcons_id'] = $row['buildingquality_costcons_id'];
                        $record['buildingquality_costcons_name'] = $row['buildingquality_costcons_name'];
                        $record['buildingquality_costcons_price'] = $row['buildingquality_costcons_price'];
                        $output[] = $record;
                    }
                    echo json_encode(array('result' => "ok"
                    , "data" => $output
                    , 'desc' => 'هزینه ساخت بیمه کیفیت ساخت ساختمان با موفقیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } else
                    if ($command == "delete_buildingquality_costcons") {
                        $buildingquality_costcons_id = $this->post('buildingquality_costcons_id');


                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'delete', 'buildingquality');
                        if ($employeetoken[0] == 'ok') {
                            $user_id = $employeetoken[1];
                            $output = array();
                            $query = "DELETE FROM buildingquality_costcons_tb  where buildingquality_costcons_id=" . $buildingquality_costcons_id . "";
                            $result = $result = $this->B_db->run_query_put($query);
                            if ($result) {
                                echo json_encode(array('result' => "ok"
                                , "data" => $output
                                , 'desc' => 'هزینه ساخت بیمه کیفیت ساخت ساختمان حذف شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            } else {
                                echo json_encode(array('result' => "error"
                                , "data" => $output
                                , 'desc' => 'هزینه ساخت بیمه کیفیت ساخت ساختمان حذف نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }
                        } else {
                            echo json_encode(array('result' => $employeetoken[0]
                            , "data" => $employeetoken[1]
                            , 'desc' => $employeetoken[2]));
                        }
                    } else
                        if ($command == "modify_buildingquality_costcons") {
                            $buildingquality_costcons_id = $this->post('buildingquality_costcons_id');
                            $employeetoken = checkpermissionemployeetoken($employee_token_str, 'modify', 'buildingquality');
                            if ($employeetoken[0] == 'ok') {
                                $query = "UPDATE buildingquality_costcons_tb SET ";
                                if (isset($_REQUEST['buildingquality_costcons_name'])) {
                                    $buildingquality_costcons_name = $this->post('buildingquality_costcons_name');
                                    $query .= "buildingquality_costcons_name='" . $buildingquality_costcons_name . "'";
                                }
                                if (isset($_REQUEST['buildingquality_costcons_price']) && (isset($_REQUEST['buildingquality_costcons_name']))) {
                                    $query .= ",";
                                }
                                if (isset($_REQUEST['buildingquality_costcons_price'])) {
                                    $buildingquality_costcons_price = $this->post('buildingquality_costcons_price');
                                    $query .= "buildingquality_costcons_price='" . $buildingquality_costcons_price . "'";
                                }
                                $query .= "where buildingquality_costcons_id=" . $buildingquality_costcons_id;
                                $result = $this->B_db->run_query_put($query);
                                if ($result) {
                                    echo json_encode(array('result' => "ok"
                                    , "data" => ""
                                    , 'desc' => 'تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                }
                            } else {
                                echo json_encode(array('result' => $employeetoken[0]
                                , "data" => $employeetoken[1]
                                , 'desc' => $employeetoken[2]));
                            }
                        } else
                            if ($command == "add_buildingquality_price") {
                                $buildingquality_price_fieldcompany_id = $this->post('buildingquality_price_fieldcompany_id');
                                $buildingquality_price_buildpercent = $this->post('buildingquality_price_buildpercent');
                                $buildingquality_price_disc = $this->post('buildingquality_price_disc');

                                $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'buildingquality');
                                if ($employeetoken[0] == 'ok') {
                                    $query = "select * from buildingquality_price_tb where  buildingquality_price_fieldcompany_id=" . $buildingquality_price_fieldcompany_id . "";
                                    $result = $this->B_db->run_query($query);
                                    $num = count($result[0]);
                                    if ($num == 0) {
                                        $query = "INSERT INTO buildingquality_price_tb(buildingquality_price_fieldcompany_id, buildingquality_price_buildpercent, buildingquality_price_disc)
	                            VALUES ( $buildingquality_price_fieldcompany_id,'$buildingquality_price_buildpercent','$buildingquality_price_disc');";

                                        $result = $this->B_db->run_query_put($query);
                                        $buildingquality_price_id = $this->db->insert_id();
                                        echo json_encode(array('result' => "ok"
                                        , "data" => array('buildingquality_price_id' => $buildingquality_price_id)
                                        , 'desc' => 'قیمت بیمه نامه اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                    } else {
                                        $carmode = $result[0];
                                        echo json_encode(array('result' => "error"
                                        , "data" => array('buildingquality_price_id' => $carmode['buildingquality_price_id'])
                                        , 'desc' => 'قیمت بیمه نامه تکراری است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                    }
                                } else {
                                    echo json_encode(array('result' => $employeetoken[0]
                                    , "data" => $employeetoken[1]
                                    , 'desc' => $employeetoken[2]));

                                }
                            } else
                                if ($command == "get_buildingquality_price") {
                                    $query = "select * from buildingquality_price_tb,fieldcompany_tb,company_tb where buildingquality_price_fieldcompany_id=fieldcompany_id
                                              AND fieldcompany_company_id=company_id
                                             ORDER BY buildingquality_price_id ASC";
                                    $result = $result = $this->B_db->run_query($query);
                                    $output = array();
                                    foreach ($result as $row) {
                                        $record = array();
                                        $record['buildingquality_price_id'] = $row['buildingquality_price_id'];
                                        $record['buildingquality_price_fieldcompany_id'] = $row['buildingquality_price_fieldcompany_id'];
                                        $record['company_name'] = $row['company_name'];
                                        $record['company_logo_url'] = IMGADD . $row['company_logo_url'];
                                        $record['buildingquality_price_buildpercent'] = $row['buildingquality_price_buildpercent'];
                                        $record['buildingquality_price_disc'] = $row['buildingquality_price_disc'];
                                        $record['buildingquality_price_deactive'] = $row['buildingquality_price_deactive'];
                                        $output[] = $record;
                                    }
                                    echo json_encode(array('result' => "ok"
                                    , "data" => $output
                                    , 'desc' => 'قیمت بیمه نامه با موفقیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                } else
                                    if ($command == "delete_buildingquality_price") {
                                        $buildingquality_price_id = $this->post('buildingquality_price_id');

                                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'delete', 'buildingquality');
                                        if ($employeetoken[0] == 'ok') {
                                            $user_id = $employeetoken[1];
                                            $output = array();
                                            $query = "DELETE FROM buildingquality_price_tb  where buildingquality_price_id=" . $buildingquality_price_id . "";
                                            $result = $this->B_db->run_query_put($query);
                                            if ($result) {
                                                echo json_encode(array('result' => "ok"
                                                , "data" => $output
                                                , 'desc' => 'قیمت بیمه نامه حذف شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                            } else {
                                                echo json_encode(array('result' => "error"
                                                , "data" => $output
                                                , 'desc' => 'قیمت بیمه نامه حذف نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                            }
                                        } else {
                                            echo json_encode(array('result' => $employeetoken[0]
                                            , "data" => $employeetoken[1]
                                            , 'desc' => $employeetoken[2]));

                                        }

                                    } else
                                        if ($command == "modify_buildingquality_price") {
                                            $buildingquality_price_id = $this->post('buildingquality_price_id');

                                            $employeetoken = checkpermissionemployeetoken($employee_token_str, 'modify', 'buildingquality');
                                            if ($employeetoken[0] == 'ok') {
//*****************************************************************************************

                                                $query = "UPDATE buildingquality_price_tb SET ";

                                                if (isset($_REQUEST['buildingquality_price_buildpercent'])) {
                                                    $buildingquality_price_buildpercent = $this->post('buildingquality_price_buildpercent');
                                                    $query .= " buildingquality_price_buildpercent='" . $buildingquality_price_buildpercent . "' ";
                                                }


                                                if (isset($_REQUEST['buildingquality_price_disc']) && isset($_REQUEST['buildingquality_price_buildpercent'])) {
                                                    $query .= ",";
                                                }
                                                if (isset($_REQUEST['buildingquality_price_disc'])) {
                                                    $buildingquality_price_disc = $this->post('buildingquality_price_disc');
                                                    $query .= " buildingquality_price_disc='" . $buildingquality_price_disc . "' ";
                                                }


                                                if (isset($_REQUEST['buildingquality_price_deactive']) && (isset($_REQUEST['buildingquality_price_disc']) || isset($_REQUEST['buildingquality_price_buildpercent']))) {
                                                    $query .= ",";
                                                }
                                                if (isset($_REQUEST['buildingquality_price_deactive'])) {
                                                    $buildingquality_price_deactive = $this->post('buildingquality_price_deactive');
                                                    $query .= "buildingquality_price_deactive=" . $buildingquality_price_deactive . "";
                                                }

                                                $query .= " where buildingquality_price_id=" . $buildingquality_price_id;

                                                $result = $this->B_db->run_query_put($query);
                                                if ($result) {
                                                    echo json_encode(array('result' => "ok"
                                                    , "data" => ""
                                                    , 'desc' => 'تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                } else {
                                                    echo json_encode(array('result' => "error"
                                                    , "data" => $query
                                                    , 'desc' => 'تغییرات انجام نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                                                }
                                            } else {
                                                echo json_encode(array('result' => $employeetoken[0]
                                                , "data" => $employeetoken[1]
                                                , 'desc' => $employeetoken[2]));

                                            }
                                        }
        }
    }
}