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
class Location extends REST_Controller {

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
        if ($command=="get_location")
        {
            $query="select * from location_tb where 1 ORDER BY location_id ASC";
            $result = $this->B_db->run_query($query);
            $output =array();
            foreach($result as $row)
            {
                $record=array();
                $record['location_id']=$row['location_id'];
                $record['location_lat']=$row['location_lat'];
                $record['location_lon']=$row['location_lon'];
                $record['location_device']=$row['location_device'];
                $record['location_time']=$row['location_time'];
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

            }
    }



}
