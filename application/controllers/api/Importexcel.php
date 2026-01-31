<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

class ImportExcel extends REST_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("B_user");
        //$this->load->model('import_model');
    }

    public function index()
    {

    }

    public function upload_user_therapy_post()
    {
            $command = $this->post('command');
            $path = 'filefolder/upload_excel/';
            require_once APPPATH . "/third_party/PHPExcel.php";
            $config['upload_path'] = $path;
            $config['allowed_types'] = 'xlsx|xls';
            $config['remove_spaces'] = TRUE;
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('uploadFile')) {
                $error = array('error' => $this->upload->display_errors());
            } else {
                $data = array('upload_data' => $this->upload->data());
            }
            if (empty($error)) {
                if (!empty($data['upload_data']['file_name'])) {
                    $import_xls_file = $data['upload_data']['file_name'];
                } else {
                    $import_xls_file = 0;
                }
                $inputFileName = $path . $import_xls_file;

                try {
                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
                    $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                    if(!empty($allDataInSheet)){
                        $flag = true;
                        $i = 0;
                        $labels = array_values($allDataInSheet[1]);
                        $new_member_list = $existed_list = $black_list = $white_list = array();
                        foreach ($allDataInSheet as $key=>$value) {
                            if ($flag) {
                                $flag = false;
                                continue;
                            }
                            $inserdata[$i][$labels[0]] = $value['A'];
                            $inserdata[$i][$labels[1]] = $value['B'];
                            $inserdata[$i][$labels[2]] = $value['C'];
                            $inserdata[$i][$labels[3]] = $value['D'];
                            $inserdata[$i][$labels[4]] = $value['E'];
                            $inserdata[$i][$labels[5]] = $value['F'];
                            $inserdata[$i][$labels[6]] = $value['G'];
                            $inserdata[$i][$labels[7]] = $value['H'];
                            $inserdata[$i][$labels[8]] = $value['I'];
                            $inserdata[$i][$labels[9]] = $value['J'];
                            $inserdata[$i][$labels[10]] = $value['K'];
                            $inserdata[$i][$labels[11]] = $value['L'];
                            $inserdata[$i][$labels[12]] = $value['M'];
                            $inserdata[$i][$labels[13]] = $value['N'];
                            $inserdata[$i][$labels[14]] = $value['O'];
                            $inserdata[$i][$labels[15]] = $value['P'];
                            $inserdata[$i][$labels[16]] = $value['Q'];
                            $inserdata[$i][$labels[17]] = $value['R'];
                            $inserdata[$i][$labels[18]] = $value['S'];
                            $inserdata[$i][$labels[19]] = $value['T'];
                            $inserdata[$i][$labels[20]] = $value['U'];

                            //get just Sarparast member *** organ_user_therapy_kind_id
                            if($command == 'main_members'){
                                //check if this is main member or not "kind_id == 1"
                                if( $value['J'] == 1){
                                    $data_['user_name'] =  $value['A'];;
                                    $data_['user_family'] =  $value['B'];;
                                    $data_['user_national_code'] =  $value['D'];
                                    //find the main_member record by "organ_user_therapy_mobile"
                                    $user = $this->B_user->get_user_by_moblie($value['U']);
                                    if(empty($user)){
                                        //add user because it is not exist
                                        if($this->B_user->create_user($value['U'])){
                                            $user_id = $this->db->insert_id();
                                            $this->B_user->update_user_tb($data_,$user_id);
                                            $new_member_list[] = $value['D'];
                                        }
                                    }else{
                                        //update exist user data
                                        $existed_list[] = $value['D'];
                                        $user_id = $user[0]['user_id'];
                                        $this->B_user->update_user_tb($data_,$user_id);
                                    }
                                    //check member existing in therapy table by $national_code
                                    if(empty($this->B_user->get_organ_user_therapy($value['D']))){
                                        //add main member to organ_user_therapy
                                        $this->B_user->create_organ_user_therapy($inserdata[$i], $user_id);
                                    }else{
                                        $this->B_user->update_organ_user_therapy($user[0]);
                                    }
                                    $data_1 = array('new_member_list'=>$new_member_list , 'existed_list'=>$existed_list);
                                    $message1 = array('result' => "ok", "data" => $data_1, 'desc' => 'اعضای اصلی اضافه یا برروز رسانی شدند');
                                }
                            }elseif($command == "sub_members"){
                                //check if this is main_member or not. "kind_id == 3"
                                if( $value['J'] == 3){
                                    $data_['user_name'] =  $value['A'];;
                                    $data_['user_family'] =  $value['B'];;
                                    $data_['user_national_code'] =  $value['D'];

                                    //find the main_member record by "organ_user_therapy_mobile"
                                    $user = $this->B_user->get_user_by_moblie($value['U']);

                                    if(empty($user)){
                                        //add sub_member to black_list if the main_member didn't exist
                                        $black_list[] = $value['D'];
                                    }else{
                                        //add sub_member to white_list
                                        $white_list[] = $value['D'];
                                        //update exist user data
                                        //check sub_member existing in therapy table by "national_code"
                                        if(empty($this->B_user->get_organ_user_therapy($value['D']))){
                                            //add sub member to organ_user_therapy
                                            $user_id = $user[0]["user_id"];
                                            $this->B_user->create_organ_user_therapy($inserdata[$i], $user_id);
                                        }else{
                                            $this->B_user->update_organ_user_therapy($user[0]);
                                        }
                                    }
                                    $data_1 = array('black_list'=>$black_list , 'white_list'=>$white_list);
                                    $message1 = array('result' => "ok", "data" => $data_1, 'desc' => 'لیست سفید و سیاه');
                                }
                            }
                            $i++;
                        }
                        echo json_encode($message1, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    }else{
                        $message = array('result' => "error", "desc" => "فایل اکسل خالی است و یا ایراد دارد");
                        echo json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                } catch (Exception $e) {
                    $message = array('result' => "error"
                    , "data" => array('Error loading file ' => pathinfo($inputFileName, PATHINFO_BASENAME), 'message'=>$e->getMessage())
                    , 'desc' => 'عملیات با خطا پایان یافت');
                    echo json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            } else {
                $message = array('result' => "error", "desc" => $error['error']);
                echo json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }

    public function add_user_therapy_post()
    {
        $organ_user_therapy_organ_id = $this->post('organ_user_therapy_organ_id');
        $organ_user_therapy_kind_id = $this->post('organ_user_therapy_kind_id');
        $organ_user_therapy_name = $this->post('organ_user_therapy_name');
        $organ_user_therapy_family = $this->post('organ_user_therapy_family');
        $organ_user_therapy_national_code = $this->post('organ_user_therapy_national_code');
        $organ_user_therapy_mobile = $this->post('organ_user_therapy_mobile');
        $organ_user_therapy_gender_id = $this->post('organ_user_therapy_gender_id');
        $organ_user_therapy_year = $this->post('organ_user_therapy_year');
        $organ_user_therapy_month = $this->post('organ_user_therapy_month');
        $organ_user_therapy_day = $this->post('organ_user_therapy_day');
        $organ_user_therapy_fathername = $this->post('organ_user_therapy_fathername');
        $organ_user_therapy_kinship_id = $this->post('organ_user_therapy_kinship_id');
        $organ_user_therapy_organ_therapycontract_id = $this->post('organ_user_therapy_organ_therapycontract_id');
        $organ_user_therapy_basebime_id = $this->post('organ_user_therapy_basebime_id');
        $organ_user_therapy_bank_id = $this->post('organ_user_therapy_bank_id');
        $organ_user_therapy_cardno = $this->post('organ_user_therapy_cardno');
        $organ_user_therapy_accno = $this->post('organ_user_therapy_accno');
        $organ_user_therapy_shebano = $this->post('organ_user_therapy_shebano');
        $organ_user_therapy_bimeno = $this->post('organ_user_therapy_bimeno');
        $organ_user_therapy_idcardno = $this->post('organ_user_therapy_idcardno');
        $organ_user_therapy_main_national_code = $this->post('organ_user_therapy_main_national_code');
        $organ_user_therapy_personal_code = $this->post('organ_user_therapy_personal_code');


            try {
                $data_['organ_user_therapy_organ_id'] =  $organ_user_therapy_organ_id;
                $data_['organ_user_therapy_name'] =  $organ_user_therapy_name;
                $data_['organ_user_therapy_family'] =  $organ_user_therapy_family;
                $data_['organ_user_therapy_national_code'] =  $organ_user_therapy_national_code;
                $data_['organ_user_therapy_kind_id'] =  $organ_user_therapy_kind_id;
                $data_['organ_user_therapy_gender_id'] =  $organ_user_therapy_gender_id;
                $data_['organ_user_therapy_year'] =  $organ_user_therapy_year;
                $data_['organ_user_therapy_month'] =  $organ_user_therapy_month;
                $data_['organ_user_therapy_day'] =  $organ_user_therapy_day;
                $data_['organ_user_therapy_fathername'] =  $organ_user_therapy_fathername;
                $data_['organ_user_therapy_kinship_id'] =  $organ_user_therapy_kinship_id;
                $data_['organ_user_therapy_organ_therapycontract_id'] =  $organ_user_therapy_organ_therapycontract_id;
                $data_['organ_user_therapy_basebime_id'] =  $organ_user_therapy_basebime_id;
                $data_['organ_user_therapy_bank_id'] =  $organ_user_therapy_bank_id;
                $data_['organ_user_therapy_cardno'] =  $organ_user_therapy_cardno;
                $data_['organ_user_therapy_accno'] =  $organ_user_therapy_accno;
                $data_['organ_user_therapy_shebano'] =  $organ_user_therapy_shebano;
                $data_['organ_user_therapy_bimeno'] =  $organ_user_therapy_bimeno;
                $data_['organ_user_therapy_idcardno'] =  $organ_user_therapy_idcardno;
                $data_['organ_user_therapy_main_national_code'] =  $organ_user_therapy_main_national_code;
                $data_['organ_user_therapy_personal_code'] =  $organ_user_therapy_personal_code;


                //find the main_member record by "organ_user_therapy_mobile"
                $user = $this->B_user->get_user_by_moblie($organ_user_therapy_mobile);
                $msg = "";
                if(empty($user)){
                    //add user because it is not exist
                    if( $organ_user_therapy_kind_id == 1){
                        if($this->B_user->create_user($organ_user_therapy_mobile)){
                            $user_id = $this->db->insert_id();
                            $this->B_user->update_user_tb($data_,$user_id);
                            $msg =  'یک عضو سرپرست اضافه شد';
                        }
                    }elseif( $organ_user_therapy_kind_id > 1){
                        $message1 = array('result' => "error", "organ_user_therapy_national_code" => $organ_user_therapy_national_code, 'desc' => 'عضو سرپرست این شخص یافت نشد');
                        echo json_encode($message1, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                }else{
                    $user_id = $user[0]['user_id'];
                    //update exist user data
                    $existed_list[] = $organ_user_therapy_national_code;
                    //$this->B_user->update_user_tb($data_,$user_id);
                    //$msg =  'اطلاعات این سرپرست بروزرسانی گردید';
                }
                //check member existing in therapy table by $national_code
                if(empty($this->B_user->get_organ_user_therapy($organ_user_therapy_national_code))){
                    //add main member to organ_user_therapy
                    $this->B_user->create_organ_user_therapy($data_, $user_id);
                    $msg .= "اطلاعات شخص در بانک درمان اضافه گردید";
                }else{
                    $this->B_user->update_organ_user_therapy($data_,$organ_user_therapy_national_code,$user_id);
                    $msg .=  'اطلاعات شخص در بانک درمان بروزرسانی گردید';
                }
                $message1 = array('result' => "ok",  'desc' => $msg);
                echo json_encode($message1, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (Exception $e) {
            $message = array('result' => "error"
            , "data" => array('message'=>$e->getMessage())
            , 'desc' => 'عملیات با خطا پایان یافت');
            echo json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }
}
