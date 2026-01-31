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
class Employeelogin extends REST_Controller {

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
        //var_dump($this->input->request_headers());
		//var_dump($this->input->request_headers());
		//die;
		if(isset($this->input->request_headers()['Authorization']))$employee_token_str = $this->input->request_headers()['Authorization'];
        $this->load->model('B_user');
        $this->load->model('B_db');
        $this->load->model('B_employee');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('employeelogin', $command, get_client_ip(),50,50)) {
        if ($command=="login_employee")
        {//register employee
            $employee_mobile=$this->post('employee_mobile') ;
            $employee_pass=$this->post('employee_pass') ;
            $result=$this->B_employee->employee_login_by($employee_mobile,$employee_pass);
            $num=count($result[0]);
            $employee_id=0;
            if ($num==0)
            {
                echo json_encode(array('result'=>"error"
                ,"data"=>''
                ,'desc'=>'شماره همراه یا رمز عبور اشتباه است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }else
            {
                $employee_token_app_version=$this->post('employee_token_app_version') ;
                $employee_token_mode=$this->post('employee_token_mode') ;
                $employee_token_device_name=$this->post('employee_token_device_name') ;
                $employee_token_device_version=$this->post('employee_token_device_version') ;
                $employee_token_ip=$this->post('employee_token_ip') ;
                $employee=$result[0];
                $employee_token_employee_id=$employee['employee_id'];
                $employee_token_str= generateToken(30);
                $this->B_employee->add_employee_token($employee_token_employee_id , $employee_token_str,  $employee_token_mode, $employee_token_app_version, $employee_token_device_name, $employee_token_device_version, $employee_token_ip);

                $result1=$this->B_db->get_image($employee['employee_image_code']);
                $image=$result1[0];

                echo json_encode(array('result'=>"ok"
                ,"data"=>array('employee_id'=>$employee_token_employee_id,'employee_token_str'=>$employee_token_str,'employee_mobile'=>$employee_mobile,'employee_name'=>$employee['employee_name'],'employee_family'=>$employee['employee_family'],'employee_image'=>$image['image_tumb_url'])
                ,'desc'=>'ورود موفقیت آمیز بود'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }
        }

        else if ($command=="changepass")
        {
            $employeetoken=checkemployeetoken($employee_token_str);
            if($employeetoken[0]=='ok')
            {
                $employee_pass=$this->post('employee_pass') ;
                $result=$this->B_employee->employee_login($employeetoken[1],$employee_pass);
                $num=count($result[0]);
                if ($num==0)
                {
                    echo json_encode(array('result'=>"error"
                    ,"data"=>''
                    ,'desc'=>'رمز عبور قدیم اشتباه است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else
                {
                    $employee_newpass=$this->post('employee_newpass') ;
                    $result=$this->B_employee->update_employee_pass($employeetoken[1] ,$employee_newpass);
                    if($result){
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>$this->headers["Authorization"]
                        ,'desc'=>'رمز عبور تغییر یافت'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        echo json_encode(array('result'=>"error"
                        ,"data"=>''
                        ,'desc'=>'رمز عبور  تغییر نیافت دوباره امتحان کنید'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                }
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }
        }
        else if ($command=="checkemployeelogin")
        {
            $employeetoken=checkemployeetoken($employee_token_str);
            echo json_encode(array('result'=>$employeetoken[0]
            ,"data"=>$employeetoken[1]
            ,'desc'=>$employeetoken[2]));
        }
        else if ($command=="getemployee")
        {
            $employee_token_employee_id=$this->post('employee_id') ;
            $employeetoken=checkemployeetoken($employee_token_str);
            if($employeetoken[0]=='ok')
            {
                if($employee_token_employee_id==$employeetoken[1]){
                    $result=$this->B_employee->get_employee($employee_token_employee_id);
                    $employee=$result[0];
                    $result1=$this->B_db->get_image($employee['employee_image_code']);
                    $image=$result1[0];
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('employee_id'=>$employee['employee_id']
                        ,'employee_name'=>$employee['employee_name']
                        ,'employee_family'=>$employee['employee_family']
                        ,'employee_mobile'=>$employee['employee_mobile']
                        ,'employee_email'=>$employee['employee_email']
                        ,'employee_image'=>$image['image_url']
                        ,'employee_image_tumb'=>$image['image_tumb_url']
                        ,'employee_register_date'=>$employee['employee_register_date']
                        ,'employee_image_code'=>$employee['employee_image_code']
                        ,'employee_deactive'=>$employee['employee_deactive'])
                    ,'desc'=>'ورود شما به سیستم مورد تایید است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    echo json_encode(array('result'=>"error"
                    ,"data"=>""
                    ,'desc'=>'توکن مربوط به این کاربر نیست'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));
            }
        }

        else if ($command=="logout")
        {
            $result=$this->B_employee->employee_token($employee_token_str);
            $num=count($result[0]);
            if ($num!=0)
            {
                $result=$this->B_employee->update_employee_token($employee_token_str);
                if($result){
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>""
                    ,'desc'=>'خروج انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }else{
                echo json_encode(array('result'=>"error"
                ,"data"=>""
                ,'desc'=>'قبلا خارج شده اید'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }
        }
        else if ($command=="changeproperty")
        {
            $employeetoken=checkemployeetoken($employee_token_str);
            if($employeetoken[0]=='ok')
            {
                $query="UPDATE employee_tb SET ";
                if(isset($_REQUEST['employee_pass'])){ $query.="employee_pass='".$_REQUEST['employee_pass']."'";}
                if(isset($_REQUEST['employee_email'])&&(isset($_REQUEST['employee_pass']))){ $query.=",";}
                if(isset($_REQUEST['employee_email'])){$query.="employee_email='".$_REQUEST['employee_email']."'";}
                if(isset($_REQUEST['employee_image_code'])&&(isset($_REQUEST['employee_email'])||isset($_REQUEST['employee_pass']))){ $query.=",";}
                if(isset($_REQUEST['employee_image_code'])){$query.="employee_image_code='".$_REQUEST['employee_image_code']."'";}
                $query.="where employee_id=".$employeetoken[1];
                $result=$this->B_db->run_query_put($query);
                if($result){
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>""
                    ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else {
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>""
                    ,'desc'=>'تغییرات انجام نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));
            }
        }
        else if ($command=="forgetpasstell")
        {
            $employee_mobile=$this->post('employee_mobile') ;
            $employeetoken=checkemployeetoken($employee_token_str);
            $result=$this->B_employee->get_employee($employeetoken[1]);
            $employee=$result[0];
            if ($employee_mobile!=$employee['employee_mobile'])
            {
                echo json_encode(array('result'=>"error"
                ,"data"=>$employee_mobile.'   '.$employee['employee_mobile']
                ,'desc'=>'شماره همراه مورد نظر در سیستم ثبت نشده است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

            }else{
                $employee_mobile=$employee['employee_mobile'];
                $employee_pass=$employee['employee_pass'];
                if($result){
                    send_sms($employee_mobile,$employee_pass);
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>''
                    ,'desc'=>'اطلاعات ورود به همراه نماینده ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                }else{
                    echo json_encode(array('result'=>"error"
                    ,"data"=>''
                    ,'desc'=>'اطلاعات ارسال نشد مجددا تلاش نمایید'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }
        }
        else if ($command=="forgetpassemail")
        {
            $employeetoken=checkemployeetoken($employee_token_str);
            $result=$this->B_employee->get_employee($employeetoken[1]);
            $employee=$result[0];
            $employee_email=$employee['employee_email'];
            $employee_mobile=$employee['employee_mobile'];
            $employee_pass=$employee['employee_pass'];
            echo json_encode(array('result'=>"ok"
            ,"data"=>""
            ,'desc'=>'اطلاعات ورود به همراه نماینده ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            send_email($employee_email,$employee_pass,$employee_mobile);
        }
    }
}}