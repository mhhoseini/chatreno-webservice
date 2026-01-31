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
class Assessment extends REST_Controller {

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
        if(isset($this->input->request_headers()['Authorization']))$user_token_str = $this->input->request_headers()['Authorization'];
        $this->load->model('B_user');
        $this->load->model('B_db');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if($this->B_user->checkrequestip('userbank',$command,get_client_ip(),50,50)){
            if ($command=="add")
            {

                $assessment_answer=$this->post('assessment_answer');
                $assessment_question_id=$this->post('assessment_question_id');
                $assessment_request_id=$this->post('assessment_request_id');


                    $query="DELETE FROM assessment_tb  where  assessment_question_id=".$assessment_question_id." AND  assessment_request_id=".$assessment_request_id."";
                     $this->B_db->run_query_put($query);

                    $query1="INSERT INTO assessment_tb(   assessment_question_id,   assessment_request_id,  assessment_answer )
	                                   VALUES ($assessment_question_id,$assessment_request_id , $assessment_answer );";
                    $result= $this->B_db->run_query_put($query1);
                    $requiredimginsid=$this->db->insert_id();


                    if (!empty($result))
                    {
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>array('useracbank_id'=>$requiredimginsid)
                        ,'desc'=>'نظر با موفقیت ثبت شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        echo json_encode(array('result'=>"error"
                        ,"data"=>array('useracbank_id'=>$requiredimginsid)
                        ,'desc'=>'نظر تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }

            }
            else
            if ($command=="get")
            {


                    $query="select * from request_question_tb where request_question_active=1 ";
                    $result=$this->B_db->run_query($query);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record=array();
                        $record['request_question_id']=$row['request_question_id'];
                        $record['request_question_name']=$row['request_question_name'];
                        $record['request_question_desc']=$row['request_question_desc'];
                         $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'شماره حساب ها با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

            }

        }
    }


}
