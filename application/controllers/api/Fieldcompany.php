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
 * @subpackage      chatreno Project
 * @category        Controller
 * @author          Mohammad Hoseini, Abolfazl Ganji
 * @license         MIT
 * @link            https://chatreno.com
 */
class Fieldcompany extends REST_Controller {

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
        $this->load->model('B_company');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if($this->B_user->checkrequestip('fieldcompany',$command,get_client_ip(),50,50)) {
            if ($command=="add_fieldcompany")
            {
                $fieldcompany_fieldinsurance_id=$this->post('fieldcompany_fieldinsurance_id') ;
                $fieldcompany_company_id=$this->post('fieldcompany_company_id') ;
                $fieldcompany_priority=$this->post('fieldcompany_priority') ;
                $fieldcompany_wage=$this->post('fieldcompany_wage') ;
                $fieldcompany_genuine_wage=$this->post('fieldcompany_genuine_wage') ;
                $fieldcompany_desc=$this->post('fieldcompany_desc') ;
                $fieldcompany_link=$this->post('fieldcompany_link') ;
                $fieldcompany_deactive=$this->post('fieldcompany_deactive') ;
                $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','fieldcompany');
                if($employeetoken[0]=='ok')
                {
                    $result=$this->B_company->get_fieldcompany_by($fieldcompany_company_id, $fieldcompany_fieldinsurance_id);
                    $num=count($result[0]);
                    if ($num==0)
                    {
                        $result=$this->B_company->get_company($fieldcompany_company_id);
                        $num=count($result[0]);
                        if ($num!=0)
                        {
                            $result=$this->B_company->get_fieldcompany($fieldcompany_fieldinsurance_id);
                            $num=count($result[0]);
                            if ($num!=0)
                            {
                                $fieldcompany_id=$this->B_company->add_fieldcompany($fieldcompany_fieldinsurance_id , $fieldcompany_company_id ,  $fieldcompany_desc , $fieldcompany_link  , $fieldcompany_deactive,$fieldcompany_priority,$fieldcompany_wage,$fieldcompany_genuine_wage);
                                if($result){echo json_encode(array('result'=>"ok"
                                ,"data"=>array('fieldcompany_id'=>$fieldcompany_id)
                                ,'desc'=>'رشته بیمه اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); }
                                else{
                                    echo json_encode(array('result'=>"ok"
                                    ,"data"=>array('fieldcompany_id'=>$fieldcompany_id)
                                    ,'desc'=>'رشته بیمه اضافه نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }
                                //***************************************************************************************************************
                            }else{
                                echo json_encode(array('result'=>"error"
                                ,"data"=>array('fieldinsurance_id'=>$fieldcompany_fieldinsurance_id)
                                ,'desc'=>'رشته بیمه ای با این شناسه ثبت نشده است  '),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
                            //***************************************************************************************************************
                        }else{
                            echo json_encode(array('result'=>"error"
                            ,"data"=>array('fieldcompany_id'=>$fieldcompany_company_id)
                            ,'desc'=>'شرکت بیمه ای با این شناسه ثبت نشده است  '),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                        //***************************************************************************************************************
                    }else{
                        $fieldcompany=$result[0];
                        echo json_encode(array('result'=>"error"
                        ,"data"=>array('fieldcompany_id'=>$fieldcompany['fieldcompany_id'])
                        ,'desc'=>'رشته بیمه تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                    //***************************************************************************************************************
                }else{
                    echo json_encode(array('result'=>$employeetoken[0]
                    ,"data"=>$employeetoken[1]
                    ,'desc'=>$employeetoken[2]));

                }
            }
            if ($command=="get_fieldcompany")
            {
                $query="select * from fieldcompany_tb,company_tb,fieldinsurance_tb where company_id=fieldcompany_company_id AND fieldinsurance_id=fieldcompany_fieldinsurance_id AND ";

                if(!empty($_REQUEST['fieldinsurance'])){
                    $fieldinsurance=$this->post('fieldinsurance');

                    $query.="  fieldinsurance  = '".$fieldinsurance."' ";
                }else{$query.=" 1=1 ";}
                $query.=" AND ";

                if(!empty($_REQUEST['allfieldinsurance'])){
                    $allfieldinsurance=$this->post('allfieldinsurance');
                    $allfieldinsurance=str_replace('&#39;','"',$allfieldinsurance);

                    $query.="  fieldinsurance  IN (".$allfieldinsurance.") ";
                }else{$query.=" 1=1 ";}
                $query.=" AND ";


                if(isset($_REQUEST['filter2'])){
                    $filter2=$this->post('filter2') ;
                    $query.=$filter2;}else{$query.=" 1=1 ";}
                $query.=" AND ";
                if(isset($_REQUEST['filter3'])){
                    $filter3=$this->post('filter3') ;
                    $query.=$filter3;}else{$query.=" 1=1 ";}
                $query.=" ORDER BY fieldcompany_id ASC";
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

                    $record['fieldcompany_id']=$row['fieldcompany_id'];
                    $record['fieldcompany_fieldinsurance_id']=$row['fieldcompany_fieldinsurance_id'];
                    $record['fieldcompany_company_id']=$row['fieldcompany_company_id'];
                    $record['fieldcompany_priority']=$row['fieldcompany_priority'];
                    $record['fieldcompany_genuine_wage']=$row['fieldcompany_genuine_wage'];
                    $record['fieldcompany_wage']=$row['fieldcompany_wage'];
                    $record['company_id']=$row['company_id'];
                    $record['fieldcompany_desc']=$row['fieldcompany_desc'];
                    $record['fieldcompany_link']=$row['fieldcompany_link'];
                    $record['fieldcompany_deactive']=$row['fieldcompany_deactive'];
                    $output[]=$record;
                }
             echo json_encode(array('result'=>"ok"
                ,"data"=>$output
                ,'desc'=>'مشحصات رشته و شرکت بیمه با  موفقیت ارسال شد'.$query),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }
            else
                if ($command=="delete_fieldcompany")
                {
                    $fieldcompany_id=$this->post('fieldcompany_id') ;
                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','fieldcompany');
                    if($employeetoken[0]=='ok')
                    {
                        $output = array();
                        $result = $this->del_fieldcompany($fieldcompany_id);
                        if($result){echo json_encode(array('result'=>"ok"
                        ,"data"=>$output
                        ,'desc'=>'رشته در شرکت بیمه مورد نظر حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            echo json_encode(array('result'=>"error"
                            ,"data"=>$output
                            ,'desc'=>'رشته در شرکت بیمه مورد نظر حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]));

                    }
                }
                else
                    if ($command=="modify_fieldcompany")
                    {
                        $fieldcompany_id=$this->post('fieldcompany_id') ;
                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','fieldcompany');
                        if($employeetoken[0]=='ok')
                        {
                            $query="UPDATE fieldcompany_tb SET ";
                            if(isset($_REQUEST['fieldcompany_desc'])){
                                $fieldcompany_desc=$this->post('fieldcompany_desc') ;
                                $query.="fieldcompany_desc='".$fieldcompany_desc."'";}
                            if(isset($_REQUEST['fieldcompany_link'])&&(isset($_REQUEST['fieldcompany_desc']))){$query.=",";}
                            if(isset($_REQUEST['fieldcompany_link'])){
                                $fieldcompany_link=$this->post('fieldcompany_link') ;
                                $query.="fieldcompany_link='".$fieldcompany_link."' ";}
                            if(isset($_REQUEST['fieldcompany_deactive'])&&(isset($_REQUEST['fieldcompany_link'])||isset($_REQUEST['fieldcompany_desc']))){$query.=",";}
                            if(isset($_REQUEST['fieldcompany_deactive'])){
                                $fieldcompany_deactive=$this->post('fieldcompany_deactive') ;
                                $query.="fieldcompany_deactive=".$fieldcompany_deactive." ";}


                            if(isset($_REQUEST['fieldcompany_priority'])&&(isset($_REQUEST['fieldcompany_deactive'])||isset($_REQUEST['fieldcompany_link'])||isset($_REQUEST['fieldcompany_desc']))){$query.=",";}
                            if(isset($_REQUEST['fieldcompany_priority'])){
                                $fieldcompany_priority=$this->post('fieldcompany_priority') ;
                                $query.="fieldcompany_priority=".$fieldcompany_priority." ";}


                            if(isset($_REQUEST['fieldcompany_wage'])&&(isset($_REQUEST['fieldcompany_priority'])||isset($_REQUEST['fieldcompany_deactive'])||isset($_REQUEST['fieldcompany_link'])||isset($_REQUEST['fieldcompany_desc']))){$query.=",";}
                            if(isset($_REQUEST['fieldcompany_wage'])){
                                $fieldcompany_wage=$this->post('fieldcompany_wage') ;
                                $query.="fieldcompany_wage=".$fieldcompany_wage." ";}

                            if(isset($_REQUEST['fieldcompany_genuine_wage'])&&(isset($_REQUEST['fieldcompany_wage'])||isset($_REQUEST['fieldcompany_priority'])||isset($_REQUEST['fieldcompany_deactive'])||isset($_REQUEST['fieldcompany_link'])||isset($_REQUEST['fieldcompany_desc']))){$query.=",";}
                            if(isset($_REQUEST['fieldcompany_genuine_wage'])){
                                $fieldcompany_genuine_wage=$this->post('fieldcompany_genuine_wage') ;
                                $query.="fieldcompany_genuine_wage=".$fieldcompany_genuine_wage." ";}



                            $query.=" where fieldcompany_id=".$fieldcompany_id;
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
                    else
                        if ($command=="deactive_fieldcompany")
                        {
                            $fieldcompany_id=$this->post('fieldcompany_id') ;

                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','fieldcompany');
                            if($employeetoken[0]=='ok')
                            {
                                $result=$this->B_company->update_fieldcompany_deactive($fieldcompany_id,1);
                                if($result){
                                    echo json_encode(array('result'=>"ok"
                                    ,"data"=>""
                                    ,'desc'=>'رشته بیمه  غیر فعال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }else {
                                    echo json_encode(array('result'=>"ok"
                                    ,"data"=>""
                                    ,'desc'=>'رشته بیمه  غیر فعال  نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }
                            }else{
                                echo json_encode(array('result'=>$employeetoken[0]
                                ,"data"=>$employeetoken[1]
                                ,'desc'=>$employeetoken[2]));

                            }
                        }
                        else

                            if ($command=="active_fieldcompany")
                            {
                                $fieldcompany_id=$this->post('fieldcompany_id');

                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','fieldcompany');
                                if($employeetoken[0]=='ok')
                                {
                                    $result=$this->B_company->update_fieldcompany_deactive($fieldcompany_id,0);
                                    if($result){
                                        echo json_encode(array('result'=>"ok"
                                        ,"data"=>""
                                        ,'desc'=>'رشته در شرکت بیمه فعال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }else {
                                        echo json_encode(array('result'=>"ok"
                                        ,"data"=>""
                                        ,'desc'=>'رشته در شرکت بیمه فعال نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }

                                }else{
                                    echo json_encode(array('result'=>$employeetoken[0]
                                    ,"data"=>$employeetoken[1]
                                    ,'desc'=>$employeetoken[2]));

                                }
                            }
        }
    }
}
