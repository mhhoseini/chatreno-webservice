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
class Paramstep extends REST_Controller {

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
        $this->load->model('B_db');
        $this->load->model('B_agent');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('paramstep', $command, get_client_ip(),50,50)) {
            if ($command == "addparamstep") {
                $paramstep_name = $this->post('paramstep_name');
                $paramstep_desc = $this->post('paramstep_desc');
                $paramstep_image_code = $this->post('paramstep_image_code');
                $paramstep_link = $this->post('paramstep_link');
                $paramstep_fieldinsurance = $this->post('paramstep_fieldinsurance');
                $paramstep_num = $this->post('paramstep_num');


                $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'paramstep');
                if ($employeetoken[0] == 'ok') {
//**************************************************************************************************************
                    $query = "select * from paramstep_tb where paramstep_fieldinsurance='" . $paramstep_fieldinsurance . "' AND paramstep_num='" . $paramstep_num . "'";
                    $result = $this->B_db->run_query($query);
                    $num = count($result[0]);
                    if ($num == 0) {
//***********************************************************************************************************
                        $query2 = "select * from image_tb where image_code='" . $paramstep_image_code . "'";
                        $result2 = $this->B_db->run_query($query2);
                        $image = $result2[0];
                        $ext = pathinfo($image['image_url'], PATHINFO_EXTENSION);

                        $paramstep_image_url = 'filefolder/paramstep/' . $paramstep_fieldinsurance . $paramstep_num . '.' . $ext;
                        copy($image['image_url'], $paramstep_image_url);
//********************************************************************************************************************

                        $query = "INSERT INTO paramstep_tb(  paramstep_name,   paramstep_desc,   paramstep_link , paramstep_image_url,  paramstep_fieldinsurance,   paramstep_num)
	                           VALUES ('$paramstep_name','$paramstep_desc'  ,  '$paramstep_link' ,'$paramstep_image_url', '$paramstep_fieldinsurance',$paramstep_num);";

                        $result = $this->B_db->run_query_put($query);
                        $paramstep_id = $this->db->insert_id();
                        if ($result) {

                            echo json_encode(array('result' => "ok"
                            , "data" => array('paramstep_id' => $paramstep_id)
                            , 'desc' => 'مرحله ورود پارامتر در رشته بیمه اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        } else {
                            echo json_encode(array('result' => "ok"
                            , "data" => array('paramstep_id' => $paramstep_id)
                            , 'desc' => 'مرحله ورود پارامتر در رشته بیمه اضافه نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }


                    } else {
                        $paramstep = $result[0];
                        echo json_encode(array('result' => "error"
                        , "data" => array('paramstep_id' => $paramstep['paramstep_id'])
                        , 'desc' => 'مرحله ورود پارامتر در رشته بیمه تکراری است'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
//***************************************************************************************************************
                } else {
                    echo json_encode(array('result' => $employeetoken[0]
                    , "data" => $employeetoken[1]
                    , 'desc' => $employeetoken[2]));

                }


            }

            if ($command == "getparamstep") {
                $filter = " 1=1 ";
                if (isset($_REQUEST['paramstep_fieldinsurance'])) {

                    $paramstep_fieldinsurance = $this->post('paramstep_fieldinsurance');
                    $filter = " paramstep_fieldinsurance='$paramstep_fieldinsurance' ";

                }


                $query = "select * from paramstep_tb,fieldinsurance_tb where fieldinsurance=paramstep_fieldinsurance AND $filter ORDER BY paramstep_num ASC";

                $result = $this->B_db->run_query($query);
                $output = array();
                foreach ($result as $row) {
                    $record = array();
                    $record['paramstep_num'] = $row['paramstep_num'];
                    $record['paramstep_id'] = $row['paramstep_id'];
                    $record['paramstep_name'] = $row['paramstep_name'];
                    $record['paramstep_desc'] = $row['paramstep_desc'];
                    $record['paramstep_image_url'] = IMGADD . $row['paramstep_image_url'];
                    $record['paramstep_link'] = $row['paramstep_link'];
                    $record['paramstep_fieldinsurance'] = $row['paramstep_fieldinsurance'];
                    $record['fieldinsurance_fa'] = $row['fieldinsurance_fa'];

                    $output[] = $record;
                }
                echo json_encode(array('result' => "ok"
                , "data" => $output
                , 'desc' => 'مشحصات  مرحله ورود پارامتر در رشته بیمه با  موفقیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


            } else
                if ($command == "deleteparamstep") {
                    $paramstep_id = $this->post('paramstep_id');

                    $employeetoken = checkpermissionemployeetoken($employee_token_str, 'delete', 'paramstep');
                    if ($employeetoken[0] == 'ok') {
//************************************************************************;****************************************
                        $output = array();
                        $user_id = $employeetoken[0];

                        $query = "DELETE FROM paramstep_tb  where paramstep_id=" . $paramstep_id . "";
                        $result = $this->B_db->run_query_put($query);
                        if ($result) {
                            echo json_encode(array('result' => "ok"
                            , "data" => $output
                            , 'desc' => 'رشته بیمه مورد نظر حذف شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        } else {
                            echo json_encode(array('result' => "error"
                            , "data" => $output
                            , 'desc' => 'رشته بیمه مورد نظر حذف نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
//***************************************************************************************************************
                    } else {
                        echo json_encode(array('result' => $employeetoken[0]
                        , "data" => $employeetoken[1]
                        , 'desc' => $employeetoken[2]));

                    }

                } else


                    if ($command == "modifyparamstep") {
                        $paramstep_id = $this->post('paramstep_id');

                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'modify', 'paramstep');
                        if ($employeetoken[0] == 'ok') {
//*****************************************************************************************
                            $query = "UPDATE paramstep_tb SET ";
                            if (isset($_REQUEST['paramstep_desc'])) {
                                $paramstep_desc = $_REQUEST['paramstep_desc'];
                                $query .= "paramstep_desc='" . $paramstep_desc . "' ";
                            }

                            if (isset($_REQUEST['paramstep_image_code']) && (isset($_REQUEST['paramstep_desc']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['paramstep_image_code'])) {
                                $paramstep_image_code = $this->post('paramstep_image_code');
                                //***********************************************************************************************************
                                $query2 = "select * from image_tb where image_code='" . $paramstep_image_code . "'";
                                $result2 = $this->B_db->run_query($query2);
                                $image = $result2[0];
                                $ext = pathinfo($image['image_url'], PATHINFO_EXTENSION);
                                $paramstep_fieldinsurance = $this->post('paramstep_fieldinsurance');
                                $paramstep_num = $this->post('paramstep_num');
                                $paramstep_image_url = 'filefolder/paramstep/' . $paramstep_fieldinsurance . $paramstep_num . '.' . $ext;
                                copy($image['image_url'], $paramstep_image_url);
//********************************************************************************************************************

                                $query .= "paramstep_image_url='" . $paramstep_image_url . "'";


                            }

                            if (isset($_REQUEST['paramstep_link']) && (isset($_REQUEST['paramstep_desc']) || isset($_REQUEST['paramstep_image_code']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['paramstep_link'])) {
                                $paramstep_link = $this->post('paramstep_link');
                                $query .= "paramstep_link='" . $paramstep_link . "'";
                            }

                            if (isset($_REQUEST['paramstep_fieldinsurance']) && (isset($_REQUEST['paramstep_desc']) || isset($_REQUEST['paramstep_image_code']) || isset($_REQUEST['paramstep_link']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['paramstep_fieldinsurance'])) {
                                $paramstep_fieldinsurance = $this->post('paramstep_fieldinsurance');
                                $query .= "paramstep_fieldinsurance='" . $paramstep_fieldinsurance . "' ";
                            }



                            if (isset($_REQUEST['fieldinsurance_deactive']) && ( isset($_REQUEST['paramstep_fieldinsurance']) || isset($_REQUEST['paramstep_desc']) || isset($_REQUEST['paramstep_image_code']) || isset($_REQUEST['paramstep_link']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['fieldinsurance_deactive'])) {
                                $fieldinsurance_deactive = $this->post('fieldinsurance_deactive');
                                $query .= "fieldinsurance_deactive=" . $fieldinsurance_deactive . " ";
                            }

                            if (isset($_REQUEST['paramstep_name']) && (isset($_REQUEST['fieldinsurance_deactive'])  || isset($_REQUEST['paramstep_fieldinsurance']) || isset($_REQUEST['paramstep_desc']) || isset($_REQUEST['paramstep_image_code']) || isset($_REQUEST['paramstep_link']))) {
                                $query .= ",";
                            }
                            if (isset($_REQUEST['paramstep_name'])) {
                                $paramstep_name = $this->post('paramstep_name');
                                $query .= "paramstep_name='" . $paramstep_name . "' ";
                            }


                            $query .= " where paramstep_id=" . $paramstep_id;

                            $result = $this->B_db->run_query_put($query);
                            if ($result) {
                                echo json_encode(array('result' => "ok"
                                , "data" => ""
                                , 'desc' => 'تغییرات انجام شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            } else {
                                echo json_encode(array('result' => "ok"
                                , "data" => ""
                                , 'desc' => 'تغییرات انجام نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }
//**************************************************************************************************************

                        } else {
                            echo json_encode(array('result' => $employeetoken[0]
                            , "data" => $employeetoken[1]
                            , 'desc' => $employeetoken[2]));

                        }


                    }
        }
    }
}