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
class Instalment extends REST_Controller {

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
        $this->load->model('B_car');
        $command = $this->post("command");
        $this->load->helper('my_helper');
        if ($this->B_user->checkrequestip('instalment', $command, get_client_ip(),50,50)) {
        if ($command=="add_instalment")
        {
            $instalment_company_id=$this->post('instalment_company_id') ;
            $instalment_numround=$this->post('instalment_numround') ;
            $instalment_round_id=$this->post('instalment_round_id') ;
            $instalment_fieldinsurance_id=$this->post('instalment_fieldinsurance_id') ;
            $instalment_desc=$this->post('instalment_desc') ;
            $instalment_date_start=$this->post('instalment_date_start') ;
            $instalment_date_end=$this->post('instalment_date_end') ;


            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','instalment');
            if($employeetoken[0]=='ok')
            {
//**************************************************************************************************************
                $query="select * from instalment_tb where instalment_deactive=0 AND  instalment_company_id='".$instalment_company_id."' AND instalment_fieldinsurance_id='".$instalment_fieldinsurance_id."'";
                $result=$this->B_db->run_query($query);
                $num=count($result[0]);
                if ($num==0)
                {
                    $query="INSERT INTO instalment_tb(  instalment_company_id,  instalment_round_id,  instalment_numround,  instalment_fieldinsurance_id,      instalment_desc,   instalment_date_start,instalment_date_end)
	                                VALUES ($instalment_company_id,$instalment_round_id,$instalment_numround,$instalment_fieldinsurance_id,   '$instalment_desc' ,'$instalment_date_start' ,'$instalment_date_end');";

                    $result=$this->B_db->run_query_put($query);
                    $instalment_id=$this->db->insert_id();
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('instalment_id'=>$instalment_id)
                    ,'desc'=>'اقساط اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    $carmode=$result[0];
                    echo json_encode(array('result'=>"error"
                    ,"data"=>array('instalment_id'=>$carmode['instalment_id'])
                    ,'desc'=>'اقساط تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
//***************************************************************************************************************
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]));

            }




        }
        else
            if ($command=="get_instalment")
            {

                $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','instalment');

                if($employeetoken[0]=='ok')
                {
//************************************************************************;****************************************

                    $query="select * from instalment_tb,company_tb,fieldinsurance_tb,instalment_round_tb where instalment_tb.instalment_round_id=instalment_round_tb.instalment_round_id AND company_id=instalment_company_id AND fieldinsurance_id=instalment_fieldinsurance_id AND ";
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
                    $query.=" ORDER BY instalment_id ASC";

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


                        $record['instalment_id']=$row['instalment_id'];
                        $record['instalment_company_id']=$row['instalment_company_id'];
                        $record['instalment_numround']=$row['instalment_numround'];
                        $record['instalment_round_id']=$row['instalment_round_id'];
                        $record['instalment_round_name']=$row['instalment_round_name'];
                        $record['instalment_fieldinsurance_id']=$row['instalment_fieldinsurance_id'];
                        $record['instalment_desc']=$row['instalment_desc'];
                        $record['instalment_date_start']=$row['instalment_date_start'];
                        $record['instalment_date_end']=$row['instalment_date_end'];
                        $record['instalment_deactive']=$row['instalment_deactive'];

                        $query1="select DISTINCT instalment_check_request_id from instalment_check_tb where instalment_check_instalment_id=".$row['instalment_id'];
                        $result1 = $this->B_db->run_query($query1);
                        $num=count($result1[0]);
                        $record['instalment_num']=$num;


                        $output[]=$record;
                    }
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'مشحصات اقساط با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************
                }else{
                    echo json_encode(array('result'=>$employeetoken[0]
                    ,"data"=>$employeetoken[1]
                    ,'desc'=>$employeetoken[2]));
                }


            }
            else
                if ($command=="delete_instalment")
                {
                    $instalment_id=$this->post('instalment_id') ;

                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','instalment');
                    if($employeetoken[0]=='ok')
                    {
//************************************************************************;****************************************
                        $query1="select * from instalment_check_tb where instalment_check_instalment_id=".$instalment_id;
                        $result1 = $this->B_db->run_query($query1);
                        $num=count($result1[0]);
                        if($num==0){
//************************************************************************;****************************************
                            $user_id=$employeetoken[1];

                            $query="DELETE FROM instalment_tb  where instalment_id=".$instalment_id."";
                            $result = $this->B_db->run_query_put($query);

                            $query1="DELETE FROM instalment_conditions_tb  where instalment_conditions_instalment_id=".$instalment_id."";
                            $result1 = $this->B_db->run_query_put($query1);

                            if($result){echo json_encode(array('result'=>"ok"
                            ,"data"=>""
                            ,'desc'=>'اقساط مورد نظر حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }else{
                                echo json_encode(array('result'=>"error"
                                ,"data"=>""
                                ,'desc'=>'اقساط مورد نظر حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
//***************************************************************************************************************
                        }else{
                            echo json_encode(array('result'=>"error"
                            ,"data"=>""
                            ,'desc'=>'اقساط مورد نظر به علت استفاده حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }

                        //************************************************************************;****************************************
                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]));

                    }

                }
                else



                    if ($command=="modify_instalment")
                    {
                        $instalment_id=$this->post('instalment_id') ;

                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','instalment');
                        if($employeetoken[0]=='ok')
                        {

//*****************************************************************************************
                            $query="UPDATE instalment_tb SET ";


                            if(isset($_REQUEST['instalment_desc'])){
                                $instalment_desc=$this->post('instalment_desc') ;
                                $query.="instalment_desc='".$instalment_desc."' ";}




                            if(isset($_REQUEST['instalment_date_start'])&&(isset($_REQUEST['instalment_desc']))){$query.=",";}
                            if(isset($_REQUEST['instalment_date_start'])){
                                $instalment_date_start=$this->post('instalment_date_start') ;
                                $query.="instalment_date_start='".$instalment_date_start."' ";}

                            if(isset($_REQUEST['instalment_date_end'])&&(isset($_REQUEST['instalment_date_start'])||isset($_REQUEST['instalment_desc']))){$query.=",";}
                            if(isset($_REQUEST['instalment_date_end'])){
                                $instalment_date_end=$this->post('instalment_date_end') ;
                                $query.="instalment_date_end='".$instalment_date_end."' ";}

                            if(isset($_REQUEST['instalment_deactive'])&&(isset($_REQUEST['instalment_date_end'])||isset($_REQUEST['instalment_date_start'])||isset($_REQUEST['instalment_desc']))){$query.=",";}
                            if(isset($_REQUEST['instalment_deactive'])){
                                $instalment_deactive=$this->post('instalment_deactive') ;
                                $query.="instalment_deactive=".$_REQUEST['instalment_deactive']." ";}

                            if(isset($_REQUEST['instalment_round_id'])&&(isset($_REQUEST['instalment_deactive'])||isset($_REQUEST['instalment_date_end'])||isset($_REQUEST['instalment_date_start'])||isset($_REQUEST['instalment_desc']))){$query.=",";}
                            if(isset($_REQUEST['instalment_round_id'])){
                                $instalment_round_id=$this->post('instalment_round_id') ;
                                $query.="instalment_round_id=".$instalment_round_id." ";}

                            if(isset($_REQUEST['instalment_numround'])&&(isset($_REQUEST['instalment_round_id'])||isset($_REQUEST['instalment_deactive'])||isset($_REQUEST['instalment_date_end'])||isset($_REQUEST['instalment_date_start'])||isset($_REQUEST['instalment_desc']))){$query.=",";}
                            if(isset($_REQUEST['instalment_numround'])){
                                $instalment_numround=$this->post('instalment_numround') ;
                                $query.="instalment_numround=".$instalment_numround." ";}

                            $query.=" where instalment_id=".$instalment_id;

                            $result=$this->B_db->run_query_put($query);
                            if($result){
                                echo json_encode(array('result'=>"ok"
                                ,"data"=>""
                                ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }else {
                                echo json_encode(array('result'=>"ok"
                                ,"data"=>$query
                                ,'desc'=>'تغییرات انجام نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
                            //***************************************************************************************************************

//**************************************************************************************************************

                        }else{
                            echo json_encode(array('result'=>$employeetoken[0]
                            ,"data"=>$employeetoken[1]
                            ,'desc'=>$employeetoken[2]));

                        }



                    }
                    else
                        if ($command=="deactive_instalment")
                        {
                            $instalment_id=$this->post('instalment_id') ;

                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','instalment');
                            if($employeetoken[0]=='ok')
                            {
//*****************************************************************************************
                                $query="UPDATE instalment_tb SET instalment_deactive=1 where instalment_id=".$instalment_id;

                                $result=$this->B_db->run_query_put($query);
                                if($result){
                                    echo json_encode(array('result'=>"ok"
                                    ,"data"=>""
                                    ,'desc'=>'اقساط  غیر فعال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }else {
                                    echo json_encode(array('result'=>"error"
                                    ,"data"=>""
                                    ,'desc'=>'اقساط  غیر فعال  نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }
//**************************************************************************************************************

                            }else{
                                echo json_encode(array('result'=>$employeetoken[0]
                                ,"data"=>$employeetoken[1]
                                ,'desc'=>$employeetoken[2]));

                            }



                        }
                        else

                            if ($command=="active_instalment")
                            {
                                $instalment_id=$this->post('instalment_id') ;

                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','instalment');
                                if($employeetoken[0]=='ok')
                                {
//*****************************************************************************************
                                    $query="UPDATE instalment_tb SET instalment_deactive=0 where instalment_id=".$instalment_id;

                                    $result=$this->B_db->run_query_put($query);
                                    if($result){
                                        echo json_encode(array('result'=>"ok"
                                        ,"data"=>""
                                        ,'desc'=>'اقساط فعال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }else {
                                        echo json_encode(array('result'=>"ok"
                                        ,"data"=>""
                                        ,'desc'=>'اقساط فعال نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }
//**************************************************************************************************************

                                }else{
                                    echo json_encode(array('result'=>$employeetoken[0]
                                    ,"data"=>$employeetoken[1]
                                    ,'desc'=>$employeetoken[2]));

                                }



                            }

//**********************************************************************************************************
//**********************************************************************************************************
//**********************************************************************************************************
//**********************************************************************************************************
                            else if ($command=="add_instalment_conditions")
                            {
                                $instalment_conditions_instalment_id=$this->post('instalment_conditions_instalment_id') ;
                                $instalment_conditions_percent=$this->post('instalment_conditions_percent') ;
                                $instalment_conditions_desc=$this->post('instalment_conditions_desc') ;
                                $instalment_conditions_date=$this->post('instalment_conditions_date') ;
                                $instalment_conditions_mode_id=$this->post('instalment_conditions_mode_id') ;



                                $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','instalment');
                                if($employeetoken[0]=='ok')
                                {
                                    //**************************************************************************************************************
                                    $query="select * from instalment_tb where  instalment_id=".$instalment_conditions_instalment_id;
                                    $result=$this->B_db->run_query($query);
                                    $num=count($result[0]);
                                    if ($num!=0)
                                    {
//**************************************************************************************************************
                                        $query="select * from instalment_conditions_tb where  instalment_conditions_instalment_id='".$instalment_conditions_instalment_id."' AND instalment_conditions_date='".$instalment_conditions_date."'";
                                        $result=$this->B_db->run_query($query);
                                        $num=count($result[0]);
                                        if ($num==0)
                                        {
                                            $query="INSERT INTO instalment_conditions_tb(  instalment_conditions_instalment_id,  instalment_conditions_percent,      instalment_conditions_desc,    instalment_conditions_date   ,  instalment_conditions_mode_id)
	                                VALUES ($instalment_conditions_instalment_id,'$instalment_conditions_percent',   '$instalment_conditions_desc' ,'$instalment_conditions_date' ,$instalment_conditions_mode_id);";

                                            $result=$this->B_db->run_query_put($query);
                                            $instalment_conditions_id=$this->db->insert_id();
                                            echo json_encode(array('result'=>"ok"
                                            ,"data"=>array('instalment_conditions_id'=>$instalment_conditions_id)
                                            ,'desc'=>'شرایط اقساط اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                        }else{
                                            $instalment_conditions=$result[0];
                                            echo json_encode(array('result'=>"error"
                                            ,"data"=>array('instalment_conditions_id'=>$instalment_conditions['instalment_conditions_id'])
                                            ,'desc'=>'شرایط اقساط تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                        }
//***************************************************************************************************************
                                    }else{
                                        $carmode=$result[0];
                                        echo json_encode(array('result'=>"error"
                                        ,"data"=>array('instalment_id'=>$carmode['instalment_id'])
                                        ,'desc'=>' این شرایط اقساط ثبت نشده است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                    }
//***************************************************************************************************************
                                }else{
                                    echo json_encode(array('result'=>$employeetoken[0]
                                    ,"data"=>$employeetoken[1]
                                    ,'desc'=>$employeetoken[2]));

                                }




                            } else
                                if ($command=="delete_instalment_conditions")
                                {
                                    $instalment_conditions_id=$this->post('instalment_conditions_id') ;

                                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','instalment');
                                    if($employeetoken[0]=='ok')
                                    {
//************************************************************************;****************************************
                                        $query1="select * from instalment_check_tb where instalment_check_condition_id=".$instalment_conditions_id;
                                        $result1 = $this->B_db->run_query($query1);
                                        $num=count($result1[0]);
                                        if($num==0){
//************************************************************************;****************************************

                                            $query="DELETE FROM instalment_conditions_tb  where instalment_conditions_id=".$instalment_conditions_id."";
                                            $result = $this->B_db->run_query_put($query);

                                            if($result){echo json_encode(array('result'=>"ok"
                                            ,"data"=>""
                                            ,'desc'=>'شرایط اقساط مورد نظر حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                            }else{
                                                echo json_encode(array('result'=>"error"
                                                ,"data"=>""
                                                ,'desc'=>'شرایط اقساط مورد نظر حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                            }
//***************************************************************************************************************
                                        }else{
                                            echo json_encode(array('result'=>"error"
                                            ,"data"=>""
                                            ,'desc'=>'شرایط اقساط مورد نظر به علت استفاده حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                        }

                                        //************************************************************************;****************************************
                                    }else{
                                        echo json_encode(array('result'=>$employeetoken[0]
                                        ,"data"=>$employeetoken[1]
                                        ,'desc'=>$employeetoken[2]));

                                    }

                                }
                                else



                                    if ($command=="modify_instalment_conditions")
                                    {
                                        $instalment_conditions_id=$this->post('instalment_conditions_id') ;

                                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','instalment');
                                        if($employeetoken[0]=='ok')
                                        {
//************************************************************************;****************************************
                                            $query1="select * from instalment_check_tb where instalment_check_condition_id=".$instalment_conditions_id;
                                            $result1 = $this->B_db->run_query($query1);
                                            $num=count($result1[0]);
                                            if($num==0){
//************************************************************************;****************************************
                                                $query="UPDATE instalment_conditions_tb SET ";


                                                if(isset($_REQUEST['instalment_conditions_percent'])){
                                                    $instalment_conditions_percent=$this->post('instalment_conditions_percent') ;
                                                    $query.="instalment_conditions_percent=".$instalment_conditions_percent."";}




                                                if(isset($_REQUEST['instalment_conditions_desc'])&&(isset($_REQUEST['instalment_conditions_percent']))){$query.=",";}
                                                if(isset($_REQUEST['instalment_conditions_desc'])){
                                                    $instalment_conditions_desc=$this->post('instalment_conditions_desc') ;
                                                    $query.="instalment_conditions_desc='".$instalment_conditions_desc."' ";}

                                                if(isset($_REQUEST['instalment_conditions_date'])&&(isset($_REQUEST['instalment_conditions_desc'])||isset($_REQUEST['instalment_conditions_percent']))){$query.=",";}
                                                if(isset($_REQUEST['instalment_conditions_date'])){
                                                    $instalment_conditions_date=$this->post('instalment_conditions_date') ;
                                                    $query.="instalment_conditions_date='".$instalment_conditions_date."' ";}

                                                if(isset($_REQUEST['instalment_conditions_mode_id'])&&(isset($_REQUEST['instalment_conditions_date'])||isset($_REQUEST['instalment_conditions_desc'])||isset($_REQUEST['instalment_conditions_percent']))){$query.=",";}
                                                if(isset($_REQUEST['instalment_conditions_mode_id'])){
                                                    $instalment_conditions_mode_id=$this->post('instalment_conditions_mode_id') ;
                                                    $query.="instalment_conditions_mode_id=".$instalment_conditions_mode_id." ";}


                                                $query.=" where instalment_conditions_id=".$instalment_conditions_id;

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
//***************************************************************************************************************
                                            }else{
                                                echo json_encode(array('result'=>"error"
                                                ,"data"=>""
                                                ,'desc'=>'شرایط اقساط مورد نظر به علت استفاده تغییر نیافت'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                            }

                                            //************************************************************************;****************************************
                                        }else{
                                            echo json_encode(array('result'=>$employeetoken[0]
                                            ,"data"=>$employeetoken[1]
                                            ,'desc'=>$employeetoken[2]));

                                        }


                                    }else
                                        if ($command=="get_instalment_conditions")
                                        {
                                            $instalment_id=$this->post('instalment_id') ;

                                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'view','instalment');

                                            if($employeetoken[0]=='ok')
                                            {
//************************************************************************;****************************************

                                                $query="select * from instalment_conditions_tb,instalment_mode_tb where instalment_mode_mode_id=instalment_conditions_mode_id AND instalment_conditions_instalment_id=$instalment_id ORDER BY CAST(instalment_conditions_date  AS SIGNED) ASC";
                                                $result = $this->B_db->run_query($query);
                                                $output =array();
                                                foreach($result as $row)
                                                {
                                                    $record=array();
                                                    $record['instalment_conditions_id']=$row['instalment_conditions_id'];
                                                    $record['instalment_conditions_instalment_id']=$row['instalment_conditions_instalment_id'];
                                                    $record['instalment_conditions_percent']=$row['instalment_conditions_percent'];
                                                    $record['instalment_conditions_desc']=$row['instalment_conditions_desc'];
                                                    $record['instalment_conditions_date']=$row['instalment_conditions_date'];
                                                    $record['instalment_conditions_mode_id']=$row['instalment_conditions_mode_id'];
                                                    $record['instalment_mode_mode_name']=$row['instalment_mode_mode_name'];


                                                    $output[]=$record;
                                                }
                                                echo json_encode(array('result'=>"ok"
                                                ,"data"=>$output
                                                ,'desc'=>'مشحصات اقساط با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
//***************************************************************************************************************
                                            }else{
                                                echo json_encode(array('result'=>$employeetoken[0]
                                                ,"data"=>$employeetoken[1]
                                                ,'desc'=>$employeetoken[2]));
                                            }


                                        }
    }
}
}