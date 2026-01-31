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
class Statecity extends REST_Controller {

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

    public function index_post(){
        $command = $this->post("command");
        $this->load->model('B_db');
        $this->load->helper('my_helper');
        if ($command=="get_state")
        {
            $query="select * from state_tb where 1";
            $result = $this->B_db->run_query($query);
            $output =array();
            foreach($result as $row)
            {
                $record=array();
                $record['state_id']=$row['state_id'];
                $record['state_name']=$row['state_name'];
                $record['state_city_center_id']=$row['state_city_center_id'];
                $output[]=$record;
            }

            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'مشخصات استان ها ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

        }else
            if ($command=="get_city")
            {
                $state_id=$this->post('state_id') ;

                $query="select * from city_tb where city_state_id=$state_id";
                $result = $this->B_db->run_query($query);
                $output =array();
                foreach($result as $row)
                {
                    $record=array();
                    $record['city_id']=$row['city_id'];
                    $record['city_name']=$row['city_name'];
                    $record['city_state_id']=$row['city_state_id'];
                    $record['city_num_region']=$row['city_num_region'];
                    $output[]=$record;
                }

                echo json_encode(array('result'=>"ok"
                ,"data"=>$output
                ,'desc'=>'مشخصات استان ها ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

            }else if ($command=="get_clearingmode")
            {
                $query="select * from request_ready_clearing_mode_tb where 1";
                $result = $this->B_db->run_query($query);
                $output =array();
                foreach($result as $row)
                {
                    $record=array();
                    $record['request_ready_clearing_mode_id']=$row['request_ready_clearing_mode_id'];
                    $record['request_ready_clearing_mode_name']=$row['request_ready_clearing_mode_name'];
                    $output[]=$record;
                }

                echo json_encode(array('result'=>"ok"
                ,"data"=>$output
                ,'desc'=>'مشخصات استان ها ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

            }
    }

}
