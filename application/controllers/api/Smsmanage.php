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
class Smsmanage extends REST_Controller
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
        if ($this->B_user->checkrequestip('smsmanage', $command, get_client_ip(),10,60)) {

                if ($command == "getsmsmanage") {
                    $employeetoken = checkpermissionemployeetoken($employee_token_str, 'view', 'smsmanage');
                    if ($employeetoken[0] == 'ok') {


                        $filter="";
                        $sms_rahyab_status = $this->post("sms_rahyab_status");
                        $sms_rahyab_receiver = $this->post("sms_rahyab_receiver");
                        $sms_rahyab_timestamp_start = $this->post("sms_rahyab_timestamp_start");
                        $sms_rahyab_timestamp_end = $this->post("sms_rahyab_timestamp_end");
                        $sms_rahyab_delivery_time_start = $this->post("sms_rahyab_delivery_time_start");
                        $sms_rahyab_delivery_time_end = $this->post("sms_rahyab_delivery_time_end");
                        if($sms_rahyab_status !='') {
                           $filter .= "  sms_rahyab_status='" . $sms_rahyab_status . "'";
                       }else{$filter .=" 1=1 "; }
                        if($sms_rahyab_receiver !='') {
                            $filter .= "AND sms_rahyab_receiver like '%" . $sms_rahyab_receiver . "%' ";
                        }else{$filter .=" AND 1=1 "; }
                        if($sms_rahyab_timestamp_start !='') {
                            $filter .= " And sms_rahyab_timestamp>='" . $sms_rahyab_timestamp_start . "'";
                        }else{$filter .=" AND 1=1 "; }
                        if($sms_rahyab_timestamp_end !='') {
                            $filter .= " And sms_rahyab_timestamp<='" . $sms_rahyab_timestamp_end . "'";
                        }else{$filter .=" AND 1=1 "; }

                        if($sms_rahyab_delivery_time_start !='') {
                            $filter .= " And sms_rahyab_delivery_time>='" . $sms_rahyab_delivery_time_start . "'";
                        }else{$filter .=" AND 1=1 "; }
                        if($sms_rahyab_delivery_time_end !='') {
                            $filter .= " And sms_rahyab_delivery_time<='" . $sms_rahyab_delivery_time_end . "'";
                        }else{$filter .=" AND 1=1 "; }
                        $limit = $this->post("limit");
                        $offset = $this->post("offset");

                        $limit_state ="";
                        if($limit!="" & $offset!="")
                            $limit_state = "LIMIT ".$offset.",".$limit;

                    $query = "select * from sms_rahyab_tb where  ".$filter."   ORDER BY sms_rahyab_id DESC ".$limit_state;

                    $result = $this->B_db->run_query($query);
                    $output = array();
                    foreach ($result as $row) {
                        $record = array();
                        $record['sms_rahyab_id'] = $row['sms_rahyab_id'];
                        $record['sms_rahyab_txt'] = $row['sms_rahyab_txt'];
                        $record['sms_rahyab_timestamp'] = $row['sms_rahyab_timestamp'];
                        $record['sms_rahyab_delivery_time'] = $row['sms_rahyab_delivery_time'];
                        $record['sms_rahyab_status'] = $row['sms_rahyab_status'];
                        $record['sms_rahyab_receiver'] = $row['sms_rahyab_receiver'];
                        $record['sms_rahyab_identity'] = $row['sms_rahyab_identity'];
                        $record['sms_rahyab_errormsg'] = $row['sms_rahyab_errormsg'];

                        $output[] = $record;
                    }
                        $query1 = "select count(*) AS cnt from sms_rahyab_tb where   ".$filter;
                        $count  = $this->B_db->run_query($query1);


                    echo json_encode(array('result' => "ok"
                    , "data" => $output
                    , "credit" => rahyab_get_credit()
                    ,"cnt"=>$count[0]['cnt']
                    , 'desc' => 'مشحصات  مرحله ورود پارامتر در رشته بیمه با  موفقیت ارسال شد'.$query), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    } else {
                        echo json_encode(array('result' => $employeetoken[0]
                        , "data" => $employeetoken[1]
                        , 'desc' => $employeetoken[2]));

                    }
                }
else                 if ($command == "getsms_delivery") {
    $employeetoken = checkpermissionemployeetoken($employee_token_str, 'modify', 'smsmanage');
    if ($employeetoken[0] == 'ok') {


        $query = "select * from sms_rahyab_tb where  sms_rahyab_status=1 AND sms_rahyab_delivery_time=''  ORDER BY sms_rahyab_id DESC";

        $result = $this->B_db->run_query($query);
        foreach ($result as $row) {

            rahyab_sms_status($row['sms_rahyab_identity']);
        }
        echo json_encode(array('result' => "ok"
        , "data" => ''
        , 'desc' => 'مشحصات  مرحله ورود پارامتر در رشته بیمه با  موفقیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    } else {
        echo json_encode(array('result' => $employeetoken[0]
        , "data" => $employeetoken[1]
        , 'desc' => $employeetoken[2]));

    }
}

else                 if ($command == "getsms_credeit") {
    $employeetoken = checkpermissionemployeetoken($employee_token_str, 'modify', 'smsmanage');
    if ($employeetoken[0] == 'ok') {



            $result =rahyab_get_credit();

        echo json_encode(array('result' => "ok"
        , "data" => $result
        , 'desc' => 'مشحصات  مرحله ورود پارامتر در رشته بیمه با  موفقیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    } else {
        echo json_encode(array('result' => $employeetoken[0]
        , "data" => $employeetoken[1]
        , 'desc' => $employeetoken[2]));

    }
}

else                 if ($command == "send_sms_reminder") {
  //  $employeetoken = checkpermissionemployeetoken($employee_token_str, 'modify', 'smsmanage');
    if (true) {


        $query="select * from reminder_tb,user_tb,fieldinsurance_tb where reminder_fieldinsurance_id=fieldinsurance_id AND  user_mobile=reminder_mobile AND reminder_user_deactive=0 AND DATE(reminder_timestamp)=DATE(NOW())";
        $result = $this->B_db->run_query($query);
        foreach ($result as $row) {
            send_sms_rahyab($row['reminder_mobile'],'با سلام یادآوری با عنوان '.$row['reminder_desc'] .' در عارف۲۴ سامانه آنلاین بیمه در رشته '.$row['fieldinsurance_fa'] .' ثبت شده است');

        }

        echo json_encode(array('result' => "ok"
        , "data" => ''
        , 'desc' => 'مشحصات  مرحله ورود پارامتر در رشته بیمه با  موفقیت ارسال شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    } else {
        echo json_encode(array('result' => $employeetoken[0]
        , "data" => $employeetoken[1]
        , 'desc' => $employeetoken[2]));

    }
}
else                 if ($command == "other") {
    $query="UPDATE partner_marketer_tb SET  partner_marketer_user_refferal_name='unsco' WHERE partner_marketer_id=3";
    $result1 = $this->B_db->run_query_put($query);
    $query="UPDATE partner_marketer_tb SET  partner_marketer_user_refferal_name='jahadi' WHERE partner_marketer_id=2";
    $result1 = $this->B_db->run_query_put($query);
    $query="UPDATE partner_marketer_tb SET  partner_marketer_user_refferal_name='misclub' WHERE partner_marketer_id=1";
    $result1 = $this->B_db->run_query_put($query);
echo "dsadsaddasd";
}


        }

    }
}