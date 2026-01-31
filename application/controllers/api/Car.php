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
class Car extends REST_Controller {

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
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if ($this->B_user->checkrequestip('car', $command, get_client_ip(),50,50)) {
        if ($command=="add_carmode")
        {
            $carmode_name=$this->post('carmode_name');
            $carmode_logo=$this->post('carmode_logo');
            $carmode_priority=$this->post('carmode_priority');
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','car');
            if($employeetoken[0]=='ok')
            {
                $result=$this->B_car->get_carmode_by($carmode_name);
                $num=count($result[0]);
                if ($num==0)
                {
                    $carmode_id=$this->B_car->add_carmode($carmode_name, $carmode_priority);
                    $result2=$this->B_db->get_image_whitoururl($carmode_logo);
                    $image=$result2[0];;
                    $ext = pathinfo($image['image_url'], PATHINFO_EXTENSION);
                    $carmode_logo='filefolder/car/carmode'.$carmode_id.'.'.$ext;
                    copy($image['image_url'], $carmode_logo);

                    $this->B_car->update_carmode($carmode_logo , $carmode_id);
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('carmode_id'=>$carmode_id)
                    ,'desc'=>'نوع اتوموبیل اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    $carmode=$result[0];;
                    echo json_encode(array('result'=>"error"
                    ,"data"=>array('carmode_id'=>$carmode['carmode_id'])
                    ,'desc'=>'نوع اتوموبیل تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

            }
        }
        else
            if ($command=="get_carmode")
            {
                $result = $this->B_car->all_carmode();
                $output =array();
                foreach($result as $row)
                {
                    $record=array();
                    $record['carmode_id']=$row['carmode_id'];
                    $record['carmode_name']=$row['carmode_name'];
                    if($row['carmode_logo']!=""){
                        $record['carmode_logo']=IMGADD.$row['carmode_logo'];
                    }else{
                        $record['carmode_logo']="";
                    }
                    $record['carmode_deactive']=$row['carmode_deactive'];
                    $record['carmode_priority']=$row['carmode_priority'];
                    $output[]=$record;
                }
                echo json_encode(array('result'=>"ok"
                ,"data"=>$output
                ,'desc'=>'نوع اتوموبیل ها با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }
            else
                if ($command=="disabel_carmode")
                {
                    $carmode_id=$this->post('carmode_id');
                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','car');
                    $output = array();
                    if($employeetoken[0]=='ok')
                    {
                        $this->B_car->update_carmode_deactive($carmode_id,1,0);
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>$output
                        ,'desc'=>'نوع اتوموبیل غیر فعال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                }
                else
                    if ($command=="enabel_carmode")
                    {
                        $carmode_id=$this->post('carmode_id');
                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','car');
                        $output = array();
                        if($employeetoken[0]=='ok')
                        {
                            $this->B_car->update_carmode_deactive($carmode_id,0,1);
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>$output
                            ,'desc'=>'نوع اتوموبیل فعال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            echo json_encode(array('result'=>$employeetoken[0]
                            ,"data"=>$employeetoken[1]
                            ,'desc'=>$employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                    }
                    else
                        if ($command=="modify_carmode")
                        {
                            $carmode_id=$this->post('carmode_id');
                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','car');
                            if($employeetoken[0]=='ok')
                            {
                                $query="UPDATE carmode_tb SET ";
                                if(isset($_REQUEST['carmode_name'])){
                                    $carmode_name=$this->post('carmode_name');
                                    $query.="carmode_name='".$carmode_name."'";
                                }
                                if(isset($_REQUEST['carmode_name'])&&isset($_REQUEST['carmode_logo'])){ $query.=",";}
                                if(isset($_REQUEST['carmode_logo'])){
                                    $carmode_logo=$this->post('carmode_logo');


                                    $result2=$this->B_db->get_image_whitoururl($carmode_logo);
                                    $image=$result2[0];;
                                    $ext = pathinfo($image['image_url'], PATHINFO_EXTENSION);
                                    $carmode_logo='filefolder/car/carmode'.$carmode_id.'.'.$ext;
                                    copy($image['image_url'], $carmode_logo);
                                    $query.="carmode_logo='".$carmode_logo."'";
                                }
                                if(isset($_REQUEST['carmode_priority'])&&(isset($_REQUEST['carmode_name'])||isset($_REQUEST['carmode_logo']))){ $query.=",";}
                                if(isset($_REQUEST['carmode_priority'])){
                                    $carmode_priority=$this->post('carmode_priority');
                                    $query.="carmode_priority=".$carmode_priority."";}

                                if(isset($_REQUEST['carmode_deactive'])&&(isset($_REQUEST['carmode_priority'])||isset($_REQUEST['carmode_name'])||isset($_REQUEST['carmode_logo']))){ $query.=",";}
                                if(isset($_REQUEST['carmode_deactive'])){
                                    $carmode_deactive=$this->post('carmode_deactive');
                                    $query.="carmode_deactive=".$carmode_deactive." ";}
                                $query.=" where carmode_id=".$carmode_id;
                                $result=$this->B_db->run_query_put($query);
                                if($result){
                                    echo json_encode(array('result'=>"ok"
                                    ,"data"=>$query
                                    ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }
                            }else{
                                echo json_encode(array('result'=>$employeetoken[0]
                                ,"data"=>$employeetoken[1]
                                ,'desc'=>$employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
                        }
        if ($command=="add_carcompany")
        {
            $carcompany_name=$this->post('carcompany_name');
            $carcompany_carmode_id=$this->post('carcompany_carmode_id');
            $carcompany_logo=$this->post('carcompany_logo');
            $carcompany_priority=$this->post('carcompany_priority');
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','car');
            if($employeetoken[0]=='ok')
            {
                $result=$this->B_car->get_carcompany_by($carcompany_name);
                $num=count($result[0]);
                if ($num==0)
                {
                    $carcompany_id=$this->B_car->add_carcompany($carcompany_name, $carcompany_carmode_id,$carcompany_priority);
                    $result2 = $this->B_db->get_image_whitoururl($carcompany_logo);
                    $image=$result2[0];
                    $ext = pathinfo($image['image_url'], PATHINFO_EXTENSION);
                    $carcompany_logo='filefolder/car/carcompany'.$carcompany_id.'.'.$ext;
                    copy($image['image_url'], $carcompany_logo);
                    $this->B_car->update_carcomapany($carcompany_logo, $carcompany_id);
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('carcompany_id'=>$carcompany_id)
                    ,'desc'=>'کمپانی اتوموبیل اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    $carcompany_id=$result[0];;
                    echo json_encode(array('result'=>"error"
                    ,"data"=>array('carcompany_id'=>$carcompany_id['carcompany_id'])
                    ,'desc'=>'کمپانی اتوموبیل تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

            }
        }

        if ($command=="get_carcompany")
        {
            $query="select * from carcompany_tb,carmode_tb where carcompany_carmode_id=carmode_id AND ";
            if(isset($_REQUEST['carmode_id'])){
                $carmode_id=$this->post('carmode_id');
                $query.=' carmode_id='.$carmode_id;}else{$query.=" 1=1 ";}
            $query.=" ORDER BY carcompany_priority DESC";
            $result = $this->B_db->run_query($query);
            $output =array();
            foreach($result as $row)
            {
                $record=array();
                $record['carcompany_id']=$row['carcompany_id'];
                $record['carcompany_name']=$row['carcompany_name'];
                if($row['carcompany_logo']!=""){
                    $record['carcompany_logo']=IMGADD.$row['carcompany_logo'];
                }else{
                    $record['carcompany_logo']="";
                }
                $record['carcompany_deactive']=$row['carcompany_deactive'];
                $record['carcompany_priority']=$row['carcompany_priority'];
                $record['carcompany_carmode_id']=$row['carcompany_carmode_id'];
                $record['carmode_name']=$row['carmode_name'];
                $output[]=$record;
            }
            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'نوع اتوموبیل ها با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
        else
            if ($command=="disabel_carcompany")
            {
                $carcompany_id=$this->post('carcompany_id');
                $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','car');
                if($employeetoken[0]=='ok')
                {
                    $this->B_car->update_carcompany_deactive($carcompany_id,1,0);
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'کمپانی اتوموبیل غیر فعال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    echo json_encode(array('result'=>$employeetoken[0]
                    ,"data"=>$employeetoken[1]
                    ,'desc'=>$employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }
            else
                if ($command=="enabel_carcompany")
                {
                    $carcompany_id=$this->post('carcompany_id');

                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','car');
                    if($employeetoken[0]=='ok')
                    {
                        $$this->B_car->update_carcompany_deactive($carcompany_id,0,1);;
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>$output
                        ,'desc'=>'نوع اتوموبیل فعال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                }
                else

                    if ($command=="modify_carcompany")
                    {
                        $carcompany_id=$this->post('carcompany_id');

                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','car');
                        if($employeetoken[0]=='ok')
                        {
                            $query="UPDATE carcompany_tb SET ";
                            if(isset($_REQUEST['carcompany_name'])){
                                $carcompany_name=$this->post('carcompany_name');
                                $query.="carcompany_name='".$carcompany_name."'";
                            }
                            if(isset($_REQUEST['carcompany_name'])&&isset($_REQUEST['carcompany_logo'])){ $query.=",";}
                            if(isset($_REQUEST['carcompany_logo'])){
                                $carcompany_logo=$this->post('carcompany_logo');
                                $result2=$this->B_db->get_image_whitoururl($carcompany_logo);
                                $image=$result2[0];;
                                $ext = pathinfo($image['image_url'], PATHINFO_EXTENSION);
                                $carcompany_logo='filefolder/car/carcompany'.$carcompany_id.'.'.$ext;
                                copy($image['image_url'], $carcompany_logo);
                                $query.="carcompany_logo='".$carcompany_logo."'";
                            }

                            if(isset($_REQUEST['carcompany_priority'])&&(isset($_REQUEST['carcompany_name'])||isset($_REQUEST['carcompany_logo']))){ $query.=",";}
                            if(isset($_REQUEST['carcompany_priority'])){
                                $carcompany_priority=$this->post('carcompany_priority');
                                $query.="carcompany_priority=".$carcompany_priority."";
                            }

                            if(isset($_REQUEST['carcompany_deactive'])&&(isset($_REQUEST['carcompany_priority'])||isset($_REQUEST['carcompany_name'])||isset($_REQUEST['carcompany_logo']))){ $query.=",";}
                            if(isset($_REQUEST['carcompany_deactive'])){
                                $carcompany_deactive=$this->post('carcompany_deactive');
                                $query.="carcompany_deactive=".$carcompany_deactive." ";
                            }
                             $query.=" where carcompany_id=".$carcompany_id;
                            $result=$this->B_db->run_query_put($query);
                            if($result){
                                echo json_encode(array('result'=>"ok"
                                ,"data"=>""
                                ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
                        }else{
                            echo json_encode(array('result'=>$employeetoken[0]
                            ,"data"=>$employeetoken[1]
                            ,'desc'=>$employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                    }
                    else
                        if ($command=="add_cargroup")
                        {
                            $cargroup_name=$this->post('cargroup_name');
                            $cargroup_carmode_id=$this->post('cargroup_carmode_id');
                            $cargroup_logo=$this->post('cargroup_logo');
                            $cargroup_priority=$this->post('cargroup_priority');
                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','car');
                            if($employeetoken[0]=='ok')
                            {
                                $result=$this->B_car->get_cargroup_by($cargroup_name);
                                $num=count($result[0]);
                                if ($num==0)
                                {
                                    $cargroup_id=$this->B_car->add_cargroup($cargroup_name, $cargroup_carmode_id,$cargroup_priority);
                                    $result2=$this->B_db->get_image_whitoururl($cargroup_logo);
                                    $image=$result2[0];;
                                    $ext = pathinfo($image['image_url'], PATHINFO_EXTENSION);
                                    $cargroup_logo='filefolder/car/cargroup'.$cargroup_id.'.'.$ext;
                                    copy($image['image_url'], $cargroup_logo);
                                    $this->B_car->update_cargroup($cargroup_logo, $cargroup_id);
                                    echo json_encode(array('result'=>"ok"
                                    ,"data"=>array('cargroup_id'=>$cargroup_id)
                                    ,'desc'=>'کمپانی اتوموبیل اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }else{
                                    $cargroup_id=$result[0];;
                                    echo json_encode(array('result'=>"error"
                                    ,"data"=>array('cargroup_id'=>$cargroup_id['cargroup_id'])
                                    ,'desc'=>'کمپانی اتوموبیل تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }
                            }else{
                                echo json_encode(array('result'=>$employeetoken[0]
                                ,"data"=>$employeetoken[1]
                                ,'desc'=>$employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
                        }

        if ($command=="get_cargroup")
        {
            $query="select * from cargroup_tb,carmode_tb where cargroup_carmode_id=carmode_id  AND ";
            if(isset($_REQUEST['carmode_id'])){
                $carmode_id=$this->post('carmode_id');
                $query.=' cargroup_carmode_id='.$carmode_id;}else{$query.=" 1=1 ";}

            $query.=" ORDER BY cargroup_priority ASC";
            $result = $this->B_db->run_query($query);
            $output =array();
            foreach($result as $row)
            {
                $record=array();
                $record['cargroup_id']=$row['cargroup_id'];
                $record['cargroup_name']=$row['cargroup_name'];
                if($row['cargroup_logo']!=""){
                    $record['cargroup_logo']=IMGADD.$row['cargroup_logo'];
                }else{
                    $record['cargroup_logo']="";
                }
                $record['cargroup_deactive']=$row['cargroup_deactive'];
                $record['cargroup_priority']=$row['cargroup_priority'];
                $record['cargroup_carmode_id']=$row['cargroup_carmode_id'];
                $record['carmode_name']=$row['carmode_name'];
                $output[]=$record;
            }
            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'نوع اتوموبیل ها با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
        else
            if ($command=="disabel_cargroup")
            {
                $cargroup_id=$this->post('cargroup_id');
                $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','car');
                if($employeetoken[0]=='ok')
                {
                    $this->B_car->update_cargroup_deactive($cargroup_id,1,0);
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'کمپانی اتوموبیل غیر فعال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    echo json_encode(array('result'=>$employeetoken[0]
                    ,"data"=>$employeetoken[1]
                    ,'desc'=>$employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }
            else
                if ($command=="enabel_cargroup")
                {
                    $cargroup_id=$this->post('cargroup_id');
                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','car');
                    if($employeetoken[0]=='ok')
                    {
                        $this->B_car->update_cargroup_deactive($cargroup_id,0,1);
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>$output
                        ,'desc'=>'نوع اتوموبیل فعال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                }
                else
                    if ($command=="modify_cargroup")
                    {
                        $cargroup_id=$this->post('cargroup_id');
                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','car');
                        if($employeetoken[0]=='ok')
                        {
                            $query="UPDATE cargroup_tb SET ";
                            if(isset($_REQUEST['cargroup_name'])){
                                $cargroup_name=$this->post('cargroup_name');
                                $query.="cargroup_name='".$cargroup_name."'";
                            }
                            if(isset($_REQUEST['cargroup_name'])&&isset($_REQUEST['cargroup_logo'])){ $query.=",";}
                            if(isset($_REQUEST['cargroup_logo'])){
                                $cargroup_logo=$this->post('cargroup_logo');
                                $result2=$this->B_db->get_image_whitoururl($cargroup_logo);
                                $image=$result2[0];;
                                $ext = pathinfo($image['image_url'], PATHINFO_EXTENSION);
                                $cargroup_logo='filefolder/car/cargroup'.$cargroup_id.'.'.$ext;
                                copy($image['image_url'], $cargroup_logo);
                                $query.="cargroup_logo='".$cargroup_logo."'";
                            }
                            if(isset($_REQUEST['cargroup_priority'])&&(isset($_REQUEST['cargroup_name'])||isset($_REQUEST['cargroup_logo']))){ $query.=",";}
                            if(isset($_REQUEST['cargroup_priority'])){
                                $cargroup_priority=$this->post('cargroup_priority');
                                $query.="cargroup_priority=".$cargroup_priority."";
                            }
                            if(isset($_REQUEST['cargroup_deactive'])&&(isset($_REQUEST['cargroup_priority'])||isset($_REQUEST['cargroup_name'])||isset($_REQUEST['cargroup_logo']))){ $query.=",";}
                            if(isset($_REQUEST['cargroup_deactive'])){
                                $cargroup_deactive=$this->post('cargroup_deactive');
                                $query.="cargroup_deactive=".$cargroup_deactive." ";
                            }
                            $query.=" where cargroup_id=".$cargroup_id;
                            $result=$this->B_db->run_query_put($query);
                            if($result){
                                echo json_encode(array('result'=>"ok"
                                ,"data"=>""
                                ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
                        }else{
                            echo json_encode(array('result'=>$employeetoken[0]
                            ,"data"=>$employeetoken[1]
                            ,'desc'=>$employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                    }
                    else
                        if ($command=="add_car")
                        {
                            $car_mode_id=$this->post('car_mode_id');
                            $car_company_id=$this->post('car_company_id');
                            $car_name=$this->post('car_name');
                            $car_passenger=$this->post('car_passenger');
                            $car_group_id=$this->post('car_group_id');
                            $car_image=$this->post('car_image');
                            $car_desc=$this->post('car_desc');
                            $car_priority=$this->post('car_priority');
                            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','car');
                            if($employeetoken[0]=='ok')
                            {
                                $result = $this->B_car->get_car($car_name);
                                $num=count($result[0]);
                                if ($num==0)
                                {
                                    $car_id=$this->B_car->add_car($car_mode_id,$car_company_id, $car_name, $car_passenger,$car_group_id,$car_desc,$car_priority);
                                    $result2=$this->B_db->get_image_whitoururl($car_image);
                                    $image=$result2[0];;
                                    $ext = pathinfo($image['image_url'], PATHINFO_EXTENSION);
                                    $car_image='filefolder/car/car'.$car_id.'.'.$ext;
                                    copy($image['image_url'], $car_image);
                                    $this->B_car->update_car($car_image, $car_id);
                                    echo json_encode(array('result'=>"ok"
                                    ,"data"=>array('car_id'=>$car_id)
                                    ,'desc'=>' اتوموبیل اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }else{
                                    $car_id=$result[0];;
                                    echo json_encode(array('result'=>"error"
                                    ,"data"=>array('car_id'=>$car_id['car_id'])
                                    ,'desc'=>' اتوموبیل تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                                }
                            }else{
                                echo json_encode(array('result'=>$employeetoken[0]
                                ,"data"=>$employeetoken[1]
                                ,'desc'=>$employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
                        }
        if ($command=="get_car")
        {
            $query="select * from car_tb,carmode_tb,carcompany_tb,cargroup_tb where cargroup_id=car_group_id AND car_mode_id=carmode_id AND car_company_id=carcompany_id AND ";
            if(isset($_REQUEST['car_company_id'])){
                $car_company_id=$this->post('car_company_id');
                $query.=' car_company_id='.$car_company_id;}else{$query.=" 1=1 ";}
            $query.=" AND ";
            if(isset($_REQUEST['carmode_id'])){
                $carmode_id=$this->post('carmode_id');
                $query.=' car_mode_id='.$carmode_id;}else{$query.=" 1=1 ";}

            $query.=" AND ";
            if(isset($_REQUEST['car_group_id'])){
                $car_group_id=$this->post('car_group_id');
                $query.=' car_group_id='.$car_group_id;}else{$query.=" 1=1 ";}

            $query.=" ORDER BY car_priority DESC";
            $result = $this->B_db->run_query($query);
            $output =array();
            foreach($result as $row)
            {
                $record=array();
                $record['car_id']=$row['car_id'];
                $record['car_mode_id']=$row['car_mode_id'];
                $record['carmode_name']=$row['carmode_name'];
                $record['car_company_id']=$row['car_company_id'];
                $record['carcompany_name']=$row['carcompany_name'];
                $record['car_name']=$row['car_name'];
                $record['car_passenger']=$row['car_passenger'];
                $record['car_group_id']=$row['car_group_id'];
                $record['cargroup_name']=$row['cargroup_name'];
                if($row['car_image']!=""){
                    $record['car_image']=IMGADD.$row['car_image'];
                }else{
                    $record['car_image']="";
                }
                $record['car_deactive']=$row['car_deactive'];
                $record['car_desc']=$row['car_desc'];
                $record['car_priority']=$row['car_priority'];
                $output[]=$record;
            }
            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'نوع اتوموبیل ها با موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
        else
            if ($command=="disabel_car")
            {
                $car_id=$this->post('car_id');
                $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','car');
                if($employeetoken[0]=='ok')
                {
                    $result = $this->B_car->update_car_deactive($car_id,1,0);
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>$output
                    ,'desc'=>'اتوموبیل غیر فعال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    echo json_encode(array('result'=>$employeetoken[0]
                    ,"data"=>$employeetoken[1]
                    ,'desc'=>$employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }
            else
                if ($command=="enabel_car")
                {
                    $car_id=$this->post('car_id');
                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','car');
                    if($employeetoken[0]=='ok')
                    {
                        $result = $this->B_car->update_car_deactive($car_id,0,1);
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>$output
                        ,'desc'=>'اتوموبیل فعال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                }
                 else
                if ($command=="delete_car")
                {
                    $car_id=$this->post('car_id');
                    $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','car');
                    if($employeetoken[0]=='ok')
                    {
                        $result = $this->B_car->delete_car($car_id);
                        echo json_encode(array('result'=>"ok"
                        ,"data"=>$output
                        ,'desc'=>'اتوموبیل حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }else{
                        echo json_encode(array('result'=>$employeetoken[0]
                        ,"data"=>$employeetoken[1]
                        ,'desc'=>$employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                }
                else
                    if ($command=="modify_car")
                    {
                        $car_id=$this->post('car_id');
                        $employeetoken=checkpermissionemployeetoken($employee_token_str,'modify','car');
                        if($employeetoken[0]=='ok')
                        {
                            $query="UPDATE car_tb SET ";
                            if(isset($_REQUEST['car_mode_id'])){
                                $car_mode_id=$this->post('car_mode_id');
                                $query.="car_mode_id=".$car_mode_id."";}

                            if(isset($_REQUEST['car_company_id'])&&isset($_REQUEST['car_mode_id'])){ $query.=",";}
                            if(isset($_REQUEST['car_company_id'])){
                                $car_company_id=$this->post('car_company_id');
                                $query.="car_company_id=".$car_company_id."";
                            }

                            if(isset($_REQUEST['car_name'])&&(isset($_REQUEST['car_company_id'])||isset($_REQUEST['car_mode_id']))){ $query.=",";}
                            if(isset($_REQUEST['car_name'])){
                                $car_name=$this->post('car_name');
                                $query.="car_name='".$car_name."'";
                            }

                            if(isset($_REQUEST['car_group_id'])&&(isset($_REQUEST['car_company_id'])||isset($_REQUEST['car_mode_id'])||isset($_REQUEST['car_name']))){ $query.=",";}
                            if(isset($_REQUEST['car_group_id'])){
                                $car_group_id=$this->post('car_group_id');
                                $query.="car_group_id=".$car_group_id."";
                            }

                            if(isset($_REQUEST['car_image'])&&(isset($_REQUEST['car_company_id'])||isset($_REQUEST['car_mode_id'])||isset($_REQUEST['car_name'])||isset($_REQUEST['car_group_id']))){ $query.=",";}
                            if(isset($_REQUEST['car_image'])){
                                $car_image=$this->post('car_image');
                                $result2=$this->B_db->get_image_whitoururl($car_image);
                                $image=$result2[0];;
                                $ext = pathinfo($image['image_url'], PATHINFO_EXTENSION);
                                $car_image='filefolder/car/car'.$car_id.'.'.$ext;
                                copy($image['image_url'], $car_image);
                                $query.="car_image='".$car_image."'";
                            }
                            if(isset($_REQUEST['car_desc'])&&(isset($_REQUEST['car_company_id'])||isset($_REQUEST['car_mode_id'])||isset($_REQUEST['car_name'])||isset($_REQUEST['car_group_id'])||isset($_REQUEST['car_image']))){$query.=",";}
                            if(isset($_REQUEST['car_desc'])){
                                $car_desc=$this->post('car_desc');
                                $query.="car_desc='".$car_desc."' ";
                            }

                            if(isset($_REQUEST['car_priority'])&&(isset($_REQUEST['car_desc'])||isset($_REQUEST['car_company_id'])||isset($_REQUEST['car_mode_id'])||isset($_REQUEST['car_name'])||isset($_REQUEST['car_group_id'])||isset($_REQUEST['car_image']))){$query.=",";}
                            if(isset($_REQUEST['car_priority'])){
                                $car_priority=$this->post('car_priority');
                                $query.="car_priority=".$car_priority." ";
                            }

                            if(isset($_REQUEST['car_deactive'])&&(isset($_REQUEST['car_priority'])||isset($_REQUEST['car_desc'])||isset($_REQUEST['car_company_id'])||isset($_REQUEST['car_mode_id'])||isset($_REQUEST['car_name'])||isset($_REQUEST['car_group_id'])||isset($_REQUEST['car_image']))){$query.=",";}
                            if(isset($_REQUEST['car_deactive'])){
                                $car_deactive=$this->post('car_deactive');
                                $query.="car_deactive=".$car_deactive." ";
                            }

                            if(isset($_REQUEST['car_passenger'])&&(isset($_REQUEST['car_deactive'])||isset($_REQUEST['car_priority'])||isset($_REQUEST['car_desc'])||isset($_REQUEST['car_company_id'])||isset($_REQUEST['car_mode_id'])||isset($_REQUEST['car_name'])||isset($_REQUEST['car_group_id'])||isset($_REQUEST['car_image']))){$query.=",";}
                            if(isset($_REQUEST['car_passenger'])){
                                $car_passenger=$this->post('car_passenger');
                                $query.="car_passenger=".$car_passenger." ";
                            }

                            $query.="where car_id=".$car_id;
                            $result=$this->B_db->run_query_put($query);
                            if($result){
                                echo json_encode(array('result'=>"ok"
                                ,"data"=>""
                                ,'desc'=>'تغییرات انجام شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            }
                        }else{
                            echo json_encode(array('result'=>$employeetoken[0]
                            ,"data"=>$employeetoken[1]
                            ,'desc'=>$employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                        }
                    }
    }
}}