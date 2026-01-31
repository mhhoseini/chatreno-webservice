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
class Packageinsurance extends REST_Controller {

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
        if ($command=="add_packageinsurance")
        {
            $packageinsurance_company_id=$this->post('packageinsurance_company_id') ;
            $packageinsurance_fieldinsurance_id=$this->post('packageinsurance_fieldinsurance_id') ;
            $packageinsurance_title=$this->post('packageinsurance_title') ;
            $packageinsurance_disctitle=$this->post('packageinsurance_disctitle') ;
            $packageinsurance_discamount=$this->post('packageinsurance_discamount') ;
            $packageinsurance_amount=$this->post('packageinsurance_amount') ;
            $packageinsurance_logo=$this->post('packageinsurance_logo') ;
            $packageinsurance_desc=$this->post('packageinsurance_desc') ;
            $packageinsurance_date_start=$this->post('packageinsurance_date_start') ;
            $packageinsurance_date_end=$this->post('packageinsurance_date_end') ;
            $packageinsurance_coverage=$this->post('packageinsurance_coverage') ;
            $packageinsurance_extracoverage=$this->post('packageinsurance_extracoverage') ;



            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','packageinsurance');
            if($employeetoken[0]=='ok')
            {
//**************************************************************************************************************
                $query="select * from packageinsurance_tb where packageinsurance_deactive=0 AND  packageinsurance_company_id='".$packageinsurance_company_id."' AND packageinsurance_fieldinsurance_id='".$packageinsurance_fieldinsurance_id."' AND packageinsurance_title='".$packageinsurance_title."'";
                $result=$this->B_db->run_query($query);
                $num=count($result[0]);
                if ($num==0)
                {
                    $query="INSERT INTO packageinsurance_tb(   packageinsurance_company_id,  packageinsurance_fieldinsurance_id,   packageinsurance_title,  packageinsurance_discamount, packageinsurance_disctitle, packageinsurance_amount,    packageinsurance_logo ,   packageinsurance_desc,   packageinsurance_date_start,packageinsurance_date_end,packageinsurance_coverage,packageinsurance_extracoverage)
	                                VALUES ($packageinsurance_company_id,$packageinsurance_fieldinsurance_id,'$packageinsurance_title' , '$packageinsurance_discamount' , '$packageinsurance_disctitle' , '$packageinsurance_amount' ,  '$packageinsurance_logo'  , '$packageinsurance_desc' ,'$packageinsurance_date_start' ,'$packageinsurance_date_end','$packageinsurance_coverage','$packageinsurance_extracoverage');";

                    $result=$this->B_db->run_query_put($query);
                    $packageinsurance_id=$this->db->insert_id();
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('packageinsurance_id'=>$packageinsurance_id)
                    ,'desc'=>'تخفیف مدیریتی اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    $carmode=$result[0];
                    echo json_encode(array('result'=>"error"
                    ,"data"=>array('packageinsurance_id'=>$carmode['packageinsurance_id'])
                    ,'desc'=>'تخفیف مدیریتی تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
//***************************************************************************************************************
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }




        }
        else
            if ($command=="get_packageinsurance")
            {

//                $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','packageinsurance');
//
//                if($employeetoken[0]=='ok')
//                {
//************************************************************************;****************************************

                    $query="select * from packageinsurance_tb,company_tb,fieldinsurance_tb where company_id=packageinsurance_company_id AND fieldinsurance_id=packageinsurance_fieldinsurance_id AND ";
                    if(isset($_REQUEST['filter1'])){
                        $filter1=$this->post('filter1');
                        $query.=$filter1;}else{$query.=" 1=1 ";}
                    $query.=" AND ";
                    if(isset($_REQUEST['filter2'])){
                        $filter2=$this->post('filter2');
                        $query.=$filter2;}else{$query.=" 1=1 ";}
                    $query.=" AND ";
                    if(isset($_REQUEST['filter3'])){
                        $filter3=$this->post('filter3');
                        $query.=$filter3;}else{$query.=" 1=1 ";}
                    $query.=" ORDER BY packageinsurance_id ASC";

                    $result = $this->B_db->run_query($query);
                    $output =array();
                    foreach($result as $row)
                    {
                        $record=array();
                        $record['company_name']=$row['company_name'];
                        $record['company_logo_url']=IMGADD.$row['company_logo_url'];

                        $record['fieldinsurance']=$row['fieldinsurance'];
                        $record['fieldinsurance_fa']=$row['fieldinsurance_fa'];
                        $record['fieldinsurance_logo_url']=IMGADD.$row['fieldinsurance_logo_url'];

                        $record['packageinsurance_id']=$row['packageinsurance_id'];
                        $record['packageinsurance_company_id']=$row['packageinsurance_company_id'];
                        $record['packageinsurance_fieldinsurance_id']=$row['packageinsurance_fieldinsurance_id'];
                        $record['packageinsurance_title']=$row['packageinsurance_title'];
                        $record['packageinsurance_disctitle']=$row['packageinsurance_disctitle'];
                        $record['packageinsurance_discamount']=$row['packageinsurance_discamount'];
                        $record['packageinsurance_amount']=$row['packageinsurance_amount'];

                        $result1 = $this->B_db->get_image($row['packageinsurance_logo']);
                        $imageurl = "";
                        $imageturl = "";
                        if (!empty($result1)) {
                            $image = $result1[0];
                            if ($image['image_url']) {
                                $imageurl =  $image['image_url'];
                                $imageturl =  $image['image_tumb_url'];
                            }
                        }
                        $record['packageinsurance_t_logo']=$imageturl;
                        $record['packageinsurance_logo']=$imageurl;
                        $record['packageinsurance_desc']=$row['packageinsurance_desc'];
                        $record['packageinsurance_date_start']=$row['packageinsurance_date_start'];
                        $record['packageinsurance_date_end']=$row['packageinsurance_date_end'];
                        $record['packageinsurance_coverage']=$row['packageinsurance_coverage'];
                        $record['packageinsurance_extracoverage']=$row['packageinsurance_extracoverage'];
                        $record['packageinsurance_deactive']=$row['packageinsurance_deactive'];

                        $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'مشحصات تخفیف مدیریتی با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************
//                }else{
//                    echo json_encode(array('result'=>$employeetoken[0]
//                    ,"data"=>$employeetoken[1]
//                    ,'desc'=>$employeetoken[2]));
//                }


            }
            else
                if ($command=="delete_packageinsurance")
                {

                    $packageinsurance_id=$this->post('packageinsurance_id') ;

                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','packageinsurance');
                    if($employeetoken[0]=='ok')
                    {
//************************************************************************;****************************************
                        $query1="select * from request_tb where request_packageinsurance_id=".$packageinsurance_id;
                        $result1 = $this->B_db->run_query($query1);
                        $num=count($result1[0]);
                        if($num==0){
//************************************************************************;****************************************
                            $output = array();$user_id=$employeetoken[0];

                            $query="DELETE FROM packageinsurance_tb  where packageinsurance_id=".$packageinsurance_id."";
                            $result = $this->B_db->run_query_put($query);
                            if($result){echo json_encode(array('result'=>"ok"
                            ,"data"=>""
                            ,'desc'=>'تخفیف مدیریتی مورد نظر حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }else{
                                echo json_encode(array('result'=>"error"
                                ,"data"=>$query
                                ,'desc'=>'تخفیف مدیریتی مورد نظر حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
//***************************************************************************************************************
                        }else{
                            echo json_encode(array('result'=>"error"
                            ,"data"=>""
                            ,'desc'=>'این پکیج استفاده شده است و قابل حذف نیست'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }

                        //************************************************************************;****************************************
                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]));

                    }

                }
                else



                    if ($command=="modify_packageinsurance")
                    {
                        $packageinsurance_id=$this->post('packageinsurance_id') ;

                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','packageinsurance');
                        if($employeetoken[0]=='ok')
                        {
//*****************************************************************************************
                            $query="UPDATE packageinsurance_tb SET ";
                            if(isset($_REQUEST['packageinsurance_title'])){
                                $packageinsurance_title=$this->post('packageinsurance_title');
                                $query.="packageinsurance_title='".$packageinsurance_title."'";}


                            if(isset($_REQUEST['packageinsurance_amount'])&&isset($_REQUEST['packageinsurance_title'])){ $query.=",";}
                            if(isset($_REQUEST['packageinsurance_amount'])){
                                $packageinsurance_amount=$this->post('packageinsurance_amount') ;
                                $query.="packageinsurance_amount=".$packageinsurance_amount."";}

                            if(isset($_REQUEST['packageinsurance_logo'])&&(isset($_REQUEST['packageinsurance_title'])||isset($_REQUEST['packageinsurance_amount']))){ $query.=",";}
                            if(isset($_REQUEST['packageinsurance_logo'])){
                                $packageinsurance_logo=$this->post('packageinsurance_logo') ;
                                $query.="packageinsurance_logo='".$packageinsurance_logo."'";}

                            if(isset($_REQUEST['packageinsurance_desc'])&&(isset($_REQUEST['packageinsurance_title'])||isset($_REQUEST['packageinsurance_amount'])||isset($_REQUEST['packageinsurance_logo']))){$query.=",";}
                            if(isset($_REQUEST['packageinsurance_desc'])){
                                $packageinsurance_desc=$this->post('packageinsurance_desc') ;
                                $query.="packageinsurance_desc='".$packageinsurance_desc."' ";}

                            if(isset($_REQUEST['packageinsurance_date_start'])&&(isset($_REQUEST['packageinsurance_desc'])||isset($_REQUEST['packageinsurance_title'])||isset($_REQUEST['packageinsurance_amount'])||isset($_REQUEST['packageinsurance_logo']))){$query.=",";}
                            if(isset($_REQUEST['packageinsurance_date_start'])){
                                $packageinsurance_date_start=$this->post('packageinsurance_date_start');
                                $query.="packageinsurance_date_start='".$packageinsurance_date_start."' ";}

                            if(isset($_REQUEST['packageinsurance_date_end'])&&(isset($_REQUEST['packageinsurance_desc'])||isset($_REQUEST['packageinsurance_date_start'])||isset($_REQUEST['packageinsurance_title'])||isset($_REQUEST['packageinsurance_amount'])||isset($_REQUEST['packageinsurance_logo']))){$query.=",";}
                            if(isset($_REQUEST['packageinsurance_date_end'])){
                                $packageinsurance_date_end=$this->post('packageinsurance_date_end');
                                $query.="packageinsurance_date_end='".$packageinsurance_date_end."' ";}

                            if(isset($_REQUEST['packageinsurance_deactive'])&&(isset($_REQUEST['packageinsurance_date_end'])||isset($_REQUEST['packageinsurance_date_start'])||isset($_REQUEST['packageinsurance_desc'])||isset($_REQUEST['packageinsurance_title'])||isset($_REQUEST['packageinsurance_amount'])||isset($_REQUEST['packageinsurance_logo']))){$query.=",";}
                            if(isset($_REQUEST['packageinsurance_deactive'])){
                                $packageinsurance_deactive=$this->post('packageinsurance_deactive') ;
                                $query.="packageinsurance_deactive=".$packageinsurance_deactive." ";}

                            if(isset($_REQUEST['packageinsurance_coverage'])&&(isset($_REQUEST['packageinsurance_deactive'])||isset($_REQUEST['packageinsurance_date_end'])||isset($_REQUEST['packageinsurance_date_start'])||isset($_REQUEST['packageinsurance_desc'])||isset($_REQUEST['packageinsurance_title'])||isset($_REQUEST['packageinsurance_amount'])||isset($_REQUEST['packageinsurance_logo']))){
                                $query.=",";}
                            if(isset($_REQUEST['packageinsurance_coverage'])){
                                $packageinsurance_coverage=$this->post('packageinsurance_coverage');
                                $query.="packageinsurance_coverage='".$packageinsurance_coverage."' ";}


                            if(isset($_REQUEST['packageinsurance_extracoverage'])&&(isset($_REQUEST['packageinsurance_coverage'])||isset($_REQUEST['packageinsurance_deactive'])||isset($_REQUEST['packageinsurance_date_end'])||isset($_REQUEST['packageinsurance_date_start'])||isset($_REQUEST['packageinsurance_desc'])||isset($_REQUEST['packageinsurance_title'])||isset($_REQUEST['packageinsurance_amount'])||isset($_REQUEST['packageinsurance_logo']))){
                                $query.=",";}
                            if(isset($_REQUEST['packageinsurance_extracoverage'])){
                                $packageinsurance_extracoverage=$this->post('packageinsurance_extracoverage');
                                $query.="packageinsurance_extracoverage='".$packageinsurance_extracoverage."' ";}

                            if(isset($_REQUEST['packageinsurance_discamount'])&&(isset($_REQUEST['packageinsurance_extracoverage'])||isset($_REQUEST['packageinsurance_coverage'])||isset($_REQUEST['packageinsurance_deactive'])||isset($_REQUEST['packageinsurance_date_end'])||isset($_REQUEST['packageinsurance_date_start'])||isset($_REQUEST['packageinsurance_desc'])||isset($_REQUEST['packageinsurance_title'])||isset($_REQUEST['packageinsurance_amount'])||isset($_REQUEST['packageinsurance_logo']))){
                                $query.=",";}
                            if(isset($_REQUEST['packageinsurance_discamount'])){
                                $packageinsurance_discamount=$this->post('packageinsurance_discamount');
                                $query.="packageinsurance_discamount='".$packageinsurance_discamount."' ";}

                            if(isset($_REQUEST['packageinsurance_disctitle'])&&(isset($_REQUEST['packageinsurance_discamount'])||isset($_REQUEST['packageinsurance_extracoverage'])||isset($_REQUEST['packageinsurance_coverage'])||isset($_REQUEST['packageinsurance_deactive'])||isset($_REQUEST['packageinsurance_date_end'])||isset($_REQUEST['packageinsurance_date_start'])||isset($_REQUEST['packageinsurance_desc'])||isset($_REQUEST['packageinsurance_title'])||isset($_REQUEST['packageinsurance_amount'])||isset($_REQUEST['packageinsurance_logo']))){
                                $query.=",";}
                            if(isset($_REQUEST['packageinsurance_disctitle'])){
                                $packageinsurance_disctitle=$this->post('packageinsurance_disctitle');
                                $query.=" packageinsurance_disctitle='".$packageinsurance_disctitle."' ";}


                            $query.=" where packageinsurance_id=".$packageinsurance_id;

                            $result=$this->B_db->run_query_put($query);
                            if($result){
                                echo json_encode(array('result'=>"ok"
                                ,"data"=>""
                                ,'desc'=>'تغییرات انجام شد' ),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }else {
                                echo json_encode(array('result'=>"ok"
                                ,"data"=>""
                                ,'desc'=>'تغییرات انجام نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
//**************************************************************************************************************

                        }else{
                            echo json_encode(array('result'=>$employeetoken[0]
                            ,"data"=>$employeetoken[1]
                            ,'desc'=>$employeetoken[2]));

                        }
                    }
                    else
                        if ($command=="deactive_packageinsurance")
                        {
                            $packageinsurance_id=$this->post('packageinsurance_id') ;

                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','packageinsurance');
                            if($employeetoken[0]=='ok')
                            {
//*****************************************************************************************
                                $query="UPDATE packageinsurance_tb SET packageinsurance_deactive=1 where packageinsurance_id=".$packageinsurance_id;

                                $result=$this->B_db->run_query_put($query);
                                if($result){
                                    echo json_encode(array('result'=>"ok"
                                    ,"data"=>""
                                    ,'desc'=>'تخفیف مدیریتی  غیر فعال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }else {
                                    echo json_encode(array('result'=>"ok"
                                    ,"data"=>""
                                    ,'desc'=>'تخفیف مدیریتی  غیر فعال  نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }
//**************************************************************************************************************

                            }else{
                                echo json_encode(array('result'=>$employeetoken[0]
                                ,"data"=>$employeetoken[1]
                                ,'desc'=>$employeetoken[2]));

                            }
                        }
                        else

                            if ($command=="active_packageinsurance")
                            {
                                $packageinsurance_id=$this->post('packageinsurance_id') ;

                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','packageinsurance');
                                if($employeetoken[0]=='ok')
                                {
//*****************************************************************************************
                                    $query="UPDATE packageinsurance_tb SET packageinsurance_deactive=0 where packageinsurance_id=".$packageinsurance_id;

                                    $result=$this->B_db->run_query_put($query);
                                    if($result){
                                        echo json_encode(array('result'=>"ok"
                                        ,"data"=>""
                                        ,'desc'=>'تخفیف مدیریتی فعال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }else {
                                        echo json_encode(array('result'=>"ok"
                                        ,"data"=>""
                                        ,'desc'=>'تخفیف مدیریتی فعال نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }
//**************************************************************************************************************

                                }else{
                                    echo json_encode(array('result'=>$employeetoken[0]
                                    ,"data"=>$employeetoken[1]
                                    ,'desc'=>$employeetoken[2]));
                                }
                            }
                            else
                                if ($command=="get_packageinsurance_use")
                                {
                                    $packageinsurance_id=$this->post('packageinsurance_id') ;
                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','packageinsurance');
                                    if($employeetoken[0]=='ok')
                                    {
                                        $query="select * from packageinsurance_use_tb,request_tb,user_tb,agent_tb,company_tb where
 packageinsurance_request_id=request_id
 AND user_id=request_user_id AND
  agent_id=request_agent_id AND
  agent_company_id=company_id AND
 packageinsurance_mngdiscnt_id=".$packageinsurance_id." AND ";
                                        if(isset($_REQUEST['filter1'])){$query.=$this->post('filter1');}else{$query.=" 1=1 ";}
                                        $query.=" AND ";
                                        if(isset($_REQUEST['filter2'])){$query.=$this->post('filter2');}else{$query.=" 1=1 ";}
                                        $query.=" AND ";
                                        if(isset($_REQUEST['filter3'])){$query.=$this->post('filter3');}else{$query.=" 1=1 ";}
                                        $query.=" ORDER BY packageinsurance_use_id ASC";
                                        $result = $this->B_db->run_query($query);
                                        $output =array();
                                        foreach($result as $row)
                                        {
                                            $record=array();
                                            $record['packageinsurance_use_id']=$row['packageinsurance_use_id'];
                                            $record['packageinsurance_mngdiscnt_id']=$row['packageinsurance_mngdiscnt_id'];
                                            $record['packageinsurance_request_id']=$row['packageinsurance_request_id'];
                                            $record['packageinsurance_use_timestamp']=$row['packageinsurance_use_timestamp'];
                                            $record['packageinsurance_use_amount']=$row['packageinsurance_use_amount'];

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
                                        ,'desc'=>'مشحصات تخفیف مدیریتی با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************
                                    }else{
                                        echo json_encode(array('result'=>$employeetoken[0]
                                        ,"data"=>$employeetoken[1]
                                        ,'desc'=>$employeetoken[2]));
                                    }
                                }
                        }
}