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
class Discountcode extends REST_Controller {

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
        $this->load->model('B_discount');
        $this->load->model('B_db');
        $this->load->helper('my_helper');
        $this->load->model('B_user');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('discountcode', $command, get_client_ip(),50,50)) {
                if ($command=="add_discount_code")
        {
            $discount_code_company_id=$this->post('discount_code_company_id');
            $discount_code_fieldinsurance_id=$this->post('discount_code_fieldinsurance_id');
            $discount_code=$this->post('discount_code') ;
            $discount_code_number=$this->post('discount_code_number') ;
            $discount_code_amount=$this->post('discount_code_amount') ;
            $discount_code_desc=$this->post('discount_code_desc') ;
            $discount_code_date_start=$this->post('discount_code_date_start') ;
            $discount_code_date_end=$this->post('discount_code_date_end') ;
            $discount_code_fieldinsurance_id=str_replace('&#34;','"',$discount_code_fieldinsurance_id);
            $discount_code_company_id=str_replace('&#34;','"',$discount_code_company_id);
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','discount_code');
            if($employeetoken[0]=='ok')
            {
                $result=$this->B_discount->get_discount_code($discount_code);
                $num=count($result[0]);
                if ($num==0)
                {
                    $discount_code_id=$this->B_discount->add_discount_code($discount_code_company_id,$discount_code_fieldinsurance_id,$discount_code , $discount_code_amount ,  $discount_code_number  , $discount_code_desc ,$discount_code_date_start ,$discount_code_date_end);
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$result
                    ,'desc'=>'کد تخفیف اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    $carmode=$result[0];
                    echo json_encode(array('result'=>"error"
                    ,"data"=>array('discount_code_id'=>$carmode['discount_code_id'])
                    ,'desc'=>'کد تخفیف تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));
            }
        }
        else
            if ($command=="get_discount_code")
            {
                $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','discount_code');
                if($employeetoken[0]=='ok')
                {
                    $query="select * from discount_code_tb where 1 ORDER BY discount_code_id ASC";
                    $result = $this->B_db->run_query($query);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record=array();
                        $record['discount_code_id']=$row['discount_code_id'];
                        $record['discount_code_company_id']= $row['discount_code_company_id'];
                        $record['discount_code_fieldinsurance_id']= $row['discount_code_fieldinsurance_id'];
                        $record['discount_code']=$row['discount_code'];
                        $record['discount_code_number']=$row['discount_code_number'];
                        $record['discount_code_amount']=$row['discount_code_amount'];
                        $record['discount_code_desc']=$row['discount_code_desc'];
                        $record['discount_code_date_start']=$row['discount_code_date_start'];
                        $record['discount_code_date_end']=$row['discount_code_date_end'];
                        $record['discount_code_deactive']=$row['discount_code_deactive'];
                        if($row['discount_code_id']){
							$result1 = $this->B_discount->discount_code_use_amount($row['discount_code_id']);
                        	$row2 = $result1[0];
                        	$record['discount_code_sum']=$row2['value_sum'];

							$result2 = $this->B_discount->discount_code_count($row['discount_code_id']);
                        	$row3 = $result2[0];
                        	$record['discount_code_cnt']=$row3['value_cnt'];
						}

                        $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'مشحصات کد تخفیف با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    echo json_encode(array('result'=>$employeetoken[0]
                    ,"data"=>$employeetoken[1]
                    ,'desc'=>$employeetoken[2]));
                }
            }
            else
                if ($command=="delete_discount_code")
                {
                    $discount_code_id=$this->post('discount_code_id');
                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','discount_code');
                    if($employeetoken[0]=='ok')
                    {
                        $result1 = $this->B_discount->get_discount_code_by($discount_code_id);
                        $num=count($result1[0]);
                        if($num==0){
                            $result = $this->B_discount->del_discount_code($discount_code_id);
                            if($result){echo json_encode(array('result'=>"ok"
                            ,"data"=>""
                            ,'desc'=>'کد تخفیف مورد نظر حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }else{
                                echo json_encode(array('result'=>"error"
                                ,"data"=>""
                                ,'desc'=>'کد تخفیف مورد نظر حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
                        }else{
                            echo json_encode(array('result'=>"error"
                            ,"data"=>""
                            ,'desc'=>'کد تخفیف مورد نظر به علت استفاده حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]));
                    }
                }
                else
                    if ($command=="modify_discount_code")
                    {
                        $discount_code_id=$this->post('discount_code_id');
                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','discount_code');
                        if($employeetoken[0]=='ok')
                        {
                            $query="UPDATE discount_code_tb SET ";
                            if(isset($_REQUEST['discount_code'])){
                                $discount_code=$this->post('discount_code') ;
                                $query.="discount_code='".$discount_code."'";}


                            if(isset($_REQUEST['discount_code_number'])&&isset($_REQUEST['discount_code'])){ $query.=",";}
                            if(isset($_REQUEST['discount_code_number'])){
                                $discount_code_number=$this->post('discount_code_number') ;
                                $query.="discount_code_number=".$discount_code_number."";}

                            if(isset($_REQUEST['discount_code_amount'])&&(isset($_REQUEST['discount_code'])||isset($_REQUEST['discount_code_number']))){ $query.=",";}
                            if(isset($_REQUEST['discount_code_amount'])){
                                $discount_code_amount=$this->post('discount_code_amount') ;
                                $query.="discount_code_amount='".$discount_code_amount."'";}

                            if(isset($_REQUEST['discount_code_desc'])&&(isset($_REQUEST['discount_code'])||isset($_REQUEST['discount_code_number'])||isset($_REQUEST['discount_code_amount']))){$query.=",";}
                            if(isset($_REQUEST['discount_code_desc'])){
                                $discount_code_desc=$this->post('discount_code_desc') ;
                                $query.="discount_code_desc='".$discount_code_desc."' ";}

                            if(isset($_REQUEST['discount_code_date_start'])&&(isset($_REQUEST['discount_code_desc'])||isset($_REQUEST['discount_code'])||isset($_REQUEST['discount_code_number'])||isset($_REQUEST['discount_code_amount']))){$query.=",";}
                            if(isset($_REQUEST['discount_code_date_start'])){
                                $discount_code_date_start=$this->post('discount_code_date_start') ;
                                $query.="discount_code_date_start='".$discount_code_date_start."' ";}

                            if(isset($_REQUEST['discount_code_date_end'])&&(isset($_REQUEST['discount_code_desc'])||isset($_REQUEST['discount_code_date_start'])||isset($_REQUEST['discount_code'])||isset($_REQUEST['discount_code_number'])||isset($_REQUEST['discount_code_amount']))){$query.=",";}
                            if(isset($_REQUEST['discount_code_date_end'])){
                                $discount_code_date_end=$this->post('discount_code_date_end') ;
                                $query.="discount_code_date_end='".$discount_code_date_end."' ";}

                            if(isset($_REQUEST['discount_code_deactive'])&&(isset($_REQUEST['discount_code_date_end'])||isset($_REQUEST['discount_code_date_start'])||isset($_REQUEST['discount_code_desc'])||isset($_REQUEST['discount_code'])||isset($_REQUEST['discount_code_number'])||isset($_REQUEST['discount_code_amount']))){$query.=",";}
                            if(isset($_REQUEST['discount_code_deactive'])){
                                $discount_code_deactive=$this->post('discount_code_deactive',0) ;
                                $query.="discount_code_deactive=".$discount_code_deactive." ";}

                            if(isset($_REQUEST['discount_code_company_id'])&&(isset($_REQUEST['discount_code_deactive'])||isset($_REQUEST['discount_code_date_end'])||isset($_REQUEST['discount_code_date_start'])||isset($_REQUEST['discount_code_desc'])||isset($_REQUEST['discount_code'])||isset($_REQUEST['discount_code_number'])||isset($_REQUEST['discount_code_amount']))){$query.=",";}
                            if(isset($_REQUEST['discount_code_company_id'])){
                                $discount_code_company_id=$this->post('discount_code_company_id') ;
                                $discount_code_company_id=str_replace('&#34;','"',$discount_code_company_id);

                                $query.="discount_code_company_id='".$discount_code_company_id."' ";}

                            if(isset($_REQUEST['discount_code_fieldinsurance_id'])&&(isset($_REQUEST['discount_code_company_id'])||isset($_REQUEST['discount_code_deactive'])||isset($_REQUEST['discount_code_date_end'])||isset($_REQUEST['discount_code_date_start'])||isset($_REQUEST['discount_code_desc'])||isset($_REQUEST['discount_code'])||isset($_REQUEST['discount_code_number'])||isset($_REQUEST['discount_code_amount']))){$query.=",";}
                            if(isset($_REQUEST['discount_code_fieldinsurance_id'])){
                                $discount_code_fieldinsurance_id=$this->post('discount_code_fieldinsurance_id') ;
                                $discount_code_fieldinsurance_id=str_replace('&#34;','"',$discount_code_fieldinsurance_id);
                                $query.="discount_code_fieldinsurance_id='".$discount_code_fieldinsurance_id."' ";}

                            $query.=" where discount_code_id=".$discount_code_id;
                            $result=$this->B_db->run_query_put($query);
                            if($result){
                                echo json_encode(array('result'=>"ok"
                                ,"data"=>""
                                ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }else {
                                echo json_encode(array('result'=>"ok"
                                ,"data"=> $query
                                ,'desc'=>'تغییرات انجام نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
                        }else{
                            echo json_encode(array('result'=>$employeetoken[0]
                            ,"data"=>$employeetoken[1]
                            ,'desc'=>$employeetoken[2]));
                        }
                    }
                    else
                        if ($command=="deactive_discount_code")
                        {
                            $discount_code_id=$this->post('discount_code_id');
                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','discount_code');
                            if($employeetoken[0]=='ok')
                            {
                                $result=$this->B_discount->update_discount_code($discount_code_id,1);
                                if($result){
                                    echo json_encode(array('result'=>"ok"
                                    ,"data"=>""
                                    ,'desc'=>'کد تخفیف  غیر فعال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }else {
                                    echo json_encode(array('result'=>"error"
                                    ,"data"=>""
                                    ,'desc'=>'کد تخفیف  غیر فعال  نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }
                            }else{
                                echo json_encode(array('result'=>$employeetoken[0]
                                ,"data"=>$employeetoken[1]
                                ,'desc'=>$employeetoken[2]));

                            }
                        }
                        else
                            if ($command=="active_discount_code")
                            {
                                $discount_code_id=$this->post('discount_code_id');
                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','discount_code');
                                if($employeetoken[0]=='ok')
                                {
                                    $result=$this->B_discount->update_discount_code($discount_code_id,0);
                                    if($result){
                                        echo json_encode(array('result'=>"ok"
                                        ,"data"=>""
                                        ,'desc'=>'کد تخفیف فعال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }else {
                                        echo json_encode(array('result'=>"ok"
                                        ,"data"=>""
                                        ,'desc'=>'کد تخفیف فعال نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }
                                }else{
                                    echo json_encode(array('result'=>$employeetoken[0]
                                    ,"data"=>$employeetoken[1]
                                    ,'desc'=>$employeetoken[2]));
                                }
                            }
                            else
                                if ($command=="get_discount_code_use")
                                {
                                    $discount_code_id=$this->post('discount_code_id');
                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','discount_code');
                                    if($employeetoken[0]=='ok')
                                    {
                                        $query="select * from discount_code_use_tb,request_tb,user_tb,agent_tb,company_tb where
                                                 discount_code_request_id=request_id
                                                 AND user_id=request_user_id AND
                                                 agent_id=request_agent_id AND
                                                 agent_company_id=company_id AND
                                                 discount_code_mngdiscnt_id=".$discount_code_id." AND ";
                                        if(isset($_REQUEST['filter1'])){
                                            $filter1=$this->post('filter1') ;
                                            $query.=$filter1;}else{$query.=" 1=1 ";}
                                        $query.=" AND ";
                                        if(isset($_REQUEST['filter2'])){
                                            $filter2=$this->post('filter2') ;
                                            $query.=$filter2;}else{$query.=" 1=1 ";}
                                        $query.=" AND ";
                                        if(isset($_REQUEST['filter3'])){
                                            $filter3=$this->post('filter3') ;
                                            $query.=$filter3;}else{$query.=" 1=1 ";}
                                        $query.=" ORDER BY discount_code_use_id ASC";
                                        echo $query;
                                        $result = $this->B_db->run_query($query);
                                        $output =array();
                                        foreach($result as $row)
                                        {
                                            $record=array();
                                            $record['discount_code_use_id']=$row['discount_code_use_id'];
                                            $record['discount_code_mngdiscnt_id']=$row['discount_code_mngdiscnt_id'];
                                            $record['discount_code_request_id']=$row['discount_code_request_id'];
                                            $record['discount_code_use_timestamp']=$row['discount_code_use_timestamp'];
                                            $record['discount_code_use_amount']=$row['discount_code_use_amount'];
                                            $record['request_user_id']=$row['request_user_id'];
                                            $record['request_fieldinsurance']=$row['request_fieldinsurance'];
                                            $record['request_description']=$row['request_description'];
                                            $record['user_name']=$row['user_name'];
                                            $record['user_family']=$row['user_family'];
                                            $record['user_mobile']=$row['user_mobile'];
                                            $record['company_name']=$row['company_name'];
                                            $record['agent_company_id']=$row['agent_company_id'];
                                            $record['agent_name']=$row['agent_name'];
                                            $record['agent_family']=$row['agent_family'];
                                            $record['agent_code']=$row['agent_code'];
                                            $record['agent_mobile']=$row['agent_mobile'];
                                            $output[]=$record;
                                        }
                                        echo json_encode(array('result'=>"ok"
                                        ,"data"=>$output
                                        ,'desc'=>'مشحصات کد تخفیف با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }else{
                                        echo json_encode(array('result'=>$employeetoken[0]
                                        ,"data"=>$employeetoken[1]
                                        ,'desc'=>$employeetoken[2]));
                                    }
                                }
}
}
}