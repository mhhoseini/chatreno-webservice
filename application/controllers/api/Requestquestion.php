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
 *
 * @package         CodeIgniter
 * @subpackage      aref24 Project
 * @category        Controller
 * @author          Mohammad Hoseini, Abolfazl Ganji
 * @license         MIT
 * @link            https://aref24.ir
 */
class Requestquestion extends REST_Controller {

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
        $this->load->model('B_user');
        $this->load->model('B_requests');
        $this->load->model('B_db');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if(isset($this->input->request_headers()['Authorization']))$employee_token_str = $this->input->request_headers()['Authorization'];
        if ($command=="add_requestquestion")
        {
            $request_question_fieldinsurance=$this->post('request_question_fieldinsurance') ;
            $request_question_name=$this->post('request_question_name') ;
            $request_question_desc=$this->post('request_question_desc') ;
            $request_question_active=$this->post('request_question_active') ;

            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','requestquestion');
            if($employeetoken[0]=='ok')
            {
//**************************************************************************************************************
                $query="select * from request_question_tb where request_question_fieldinsurance='".$request_question_fieldinsurance."' AND request_question_name='".$request_question_name."'";
                $result=$this->B_db->run_query($query);
                $num=count($result[0]);
                if ($num==0)
                {
                    $query="INSERT INTO request_question_tb(   request_question_fieldinsurance,   request_question_name,  request_question_desc,request_question_active   )
	                                   VALUES ('$request_question_fieldinsurance','$request_question_name' , '$request_question_desc',$request_question_active );";
                    $this->B_db->run_query_put($query);
                    $requestquestionid=$this->db->insert_id();


                    //***********************************************************************************************************

                    if($result){echo json_encode(array('result'=>"ok"
                    ,"data"=>array('request_question_id'=>$requestquestionid)
                    ,'desc'=>'عکس های مورد نیاز رشته اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); }
                    else{
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>array('request_question_id'=>$requestquestionid)
                        ,'desc'=>'عکس های مورد نیاز رشته اضافه نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }

                }else{
                    $requestquestion=$result[0];
                    echo json_encode(array('result'=>"error"
                    ,"data"=>array('request_question_id'=>$requestquestion['request_question_id'])
                    ,'desc'=>'عکس های مورد نیاز رشته تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
//***************************************************************************************************************
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }
        }
        if ($command=="get_requestquestionid")
        {
            $request_id=$this->post('request_id') ;
            $query="select * from request_question_tb,fieldinsurance_tb,request_tb where fieldinsurance=request_question_fieldinsurance AND request_question_fieldinsurance=request_fieldinsurance AND request_id=$request_id ORDER BY request_question_id ASC";
            $result=$this->B_db->run_query($query);
            $output =array();
            foreach($result as $row)
            {
                $record=array();
                $record['request_question_id']=$row['request_question_id'];
                $record['request_question_fieldinsurance']=$row['request_question_fieldinsurance'];
                $record['fieldinsurance_fa']=$row['fieldinsurance_fa'];
                $record['request_question_name']=$row['request_question_name'];
                $record['request_question_desc']=$row['request_question_desc'];
                $record['request_question_active']=$row['request_question_active'];
                  $output[]=$record;
            }
            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'مشحصات عکس های مورد نیاز رشته با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

        }else
            if ($command=="get_requestquestion")
        {
            $filter=" 1=1 ";
            if(isset($_REQUEST['fieldinsurance'])){
                $fieldinsurance=$this->post('fieldinsurance') ;
                $filter=" request_question_fieldinsurance='$fieldinsurance' ";
            }

            $query="select * from request_question_tb,fieldinsurance_tb where fieldinsurance=request_question_fieldinsurance AND $filter ORDER BY request_question_id ASC";
            $result = $this->B_db->run_query($query);
            $output =array();
            foreach($result as $row)
            {
                $record=array();
                $record['request_question_id']=$row['request_question_id'];
                $record['request_question_fieldinsurance']=$row['request_question_fieldinsurance'];
                $record['fieldinsurance_fa']=$row['fieldinsurance_fa'];
                $record['request_question_name']=$row['request_question_name'];
                $record['request_question_desc']=$row['request_question_desc'];
                $record['request_question_active']=$row['request_question_active'];
                 $output[]=$record;
            }
            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'مشحصات عکس های مورد نیاز رشته با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

        }
        else
            if ($command=="delete_requestquestion")
            {
                $request_question_id=$this->post('request_question_id') ;

                $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','requestquestion');
                if($employeetoken[0]=='ok')
                {

                    $query="DELETE FROM request_question_tb  where request_question_id=".$request_question_id."";
                    $result = $this->B_db->run_query_put($query);
                    if($result){echo json_encode(array('result'=>"ok"
                    ,"data"=>''
                    ,'desc'=>'عکس های مورد نیاز رشته مورد نظر حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        echo json_encode(array('result'=>"error"
                        ,"data"=>''
                        ,'desc'=>'عکس های مورد نیاز رشته مورد نظر حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
//***************************************************************************************************************
                }else{
                    echo json_encode(array('result'=>$employeetoken[0]
                    ,"data"=>$employeetoken[1]
                    ,'desc'=>$employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                }

            }
            else
                if ($command=="modify_requestquestion") {
                    $request_question_id = $this->post('request_question_id');

                    $employeetoken = checkpermissionemployeetoken($employee_token_str, 'modify', 'requestquestion');
                    if ($employeetoken[0] == 'ok') {
//*****************************************************************************************
                        $query = "UPDATE request_question_tb SET ";
                        if (isset($_REQUEST['request_question_name'])) {
                            $request_question_name = $this->post('request_question_name');
                            $query .= "request_question_name='" . $request_question_name . "'";
                        }

                        if (isset($_REQUEST['request_question_desc']) && (isset($_REQUEST['request_question_name']))) {
                            $query .= ",";
                        }
                        if (isset($_REQUEST['request_question_desc'])) {
                            $request_question_desc = $this->post('request_question_desc');
                            $query .= "request_question_desc='" . $request_question_desc . "'";
                        }


                        if (isset($_REQUEST['request_question_fieldinsurance']) && (isset($_REQUEST['request_question_name']) || isset($_REQUEST['request_question_desc']))) {
                            $query .= ",";
                        }
                        if (isset($_REQUEST['request_question_fieldinsurance'])) {
                            $request_question_fieldinsurance = $this->post('request_question_fieldinsurance');
                            $query .= "request_question_fieldinsurance='" . $request_question_fieldinsurance . "' ";
                        }

						 if (isset($_REQUEST['request_question_active']) && (isset($_REQUEST['request_question_fieldinsurance'])||isset($_REQUEST['request_question_name']) || isset($_REQUEST['request_question_desc']) )) {
                            $query .= ",";
                        }
                        if (isset($_REQUEST['request_question_active'])) {
                            $request_question_active = $this->post('request_question_active');
                            $query .= "request_question_active=" . $request_question_active . " ";
                        }
						

                        $query .= " where request_question_id=" . $request_question_id;

                        $result = $this->B_db->run_query_put($query);
                        if ($result) {
                            echo json_encode(array('result' => "ok"
                            , "data" => ""
                            , 'desc' => 'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        } else {
                            echo json_encode(array('result' => "ok"
                            , "data" => ""
                            , 'desc' => 'تغییرات انجام نشد' . $query),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
//**************************************************************************************************************

                    } else {
                        echo json_encode(array('result' => $employeetoken[0]
                        , "data" => $employeetoken[1]
                        , 'desc' => $employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                    }

                }
    }
}
