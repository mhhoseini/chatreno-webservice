<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//require APPPATH . '/libraries/REST_Controller.php';

// use namespace
require APPPATH .'../vendor/autoload.php';
require FCPATH.'/vendor/autoload.php';
use Aws\S3\S3Client;
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
class Image extends REST_Controller {

    public $s3Client;
    public function __construct()
    {
        parent::__construct();
        $this->s3Client = new S3Client([
            'version' => 'latest',
			'region'  => 'eu-east-1',
			'endpoint' => ENDPOINT,
            'credentials' => [
                'key'    => AWS_KEY,
                'secret' => AWS_SECRET_KEY
            ]
        ]); 
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

    public function index_post(){
        if (isset($this->input->request_headers()['Authorization'])) $user_token_str = $this->input->request_headers()['Authorization'];

        $command = $this->post("command");
        $this->load->model('B_db');
        $this->load->helper('my_helper');
        $this->load->helper('security');
        $timezone=new DateTimeZone("Asia/Tehran");
        $date=new DateTime();
        $date->setTimezone($timezone);
        $year=$date->format("Y");
        $month=$date->format("m");
        $dey=$date->format("d");
        $allowedTypes = [
            'image/png' => 'png',
            'image/jpeg' => 'jpg'
        ];

        if ($command=="uploadpic")
        {
            $image_name=$this->post('image_name') ;
            $image_desc=$this->post('image_desc') ;
            $upload_path = 'filefolder/uploadimg/'.$year.'/'.$month.'/'.$dey.'/';
            $filepath = $_FILES['image']['tmp_name'];
            $check    = getimagesize($filepath);
            $fileSize = filesize($filepath);
            $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
            $filetype = finfo_file($fileinfo, $filepath);
            if($check == false OR $fileSize === 0 OR ($fileSize > 7340032) OR (!in_array($filetype, array_keys($allowedTypes)))) {
                header('Content-Type: application/json'); echo json_encode(array('result'=>"error"
                ,"data"=>""
                ,'desc'=>'نوع فایل ارسالی تصویری نمی باشد یا سایز آن بیشتر از حد استاندارد است'.$check['mime']),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

            } else{

                if ( !file_exists( $upload_path ) ) {
                    @mkdir( $upload_path, 0755, true ) ;
                }

                if(isset($_FILES['image']['name']) and $this->security->xss_clean($_FILES['image'], TRUE)){
                    $query="INSERT INTO image_tb(  image_name, image_desc,image_timestamp) VALUES
                                 ( '".$image_name."','" .$image_desc."', now()) ";
                    $result=$this->B_db->run_query_put($query);
                    $image_id=$this->db->insert_id();

                    $current_timestamp=$date->getTimestamp();
                    $image_code=$current_timestamp. generateToken(8);

                    $fileinfo = pathinfo($_FILES['image']['name']);
                    $extension = $fileinfo['extension'];
                    $file_url = $upload_path .$image_code. '.' . $extension;
                    $file_t_url = $upload_path .'t'.$image_code. '.' . $extension;
                    try{
                        if (move_uploaded_file($_FILES['image']['tmp_name'],$file_url)){
                            if($extension!='png'){
                                copy($file_url, $file_t_url);
                                $this->load->library('image_lib');
                                $config['image_library'] = 'gd2';
                                //$config['create_thumb'] = TRUE;
                                $config['maintain_ratio'] = TRUE;
                                //$config['quality']   = 1000;

                                $config['source_image'] = $file_url;
                                $config['width']     = 1000;
                                $config['height']   = 1000;
                                $this->image_lib->clear();
                                $this->image_lib->initialize($config);
                                $this->image_lib->resize();

                                $config1['image_library'] = 'gd2';
                                //$config1['create_thumb'] = TRUE;
                                $config1['maintain_ratio'] = TRUE;
                                $config1['source_image'] = $file_t_url;
                                $config1['width'] = 200;
                                $config1['height'] = 200;
                                $this->image_lib->clear();
                                $this->image_lib->initialize($config1);
                                $this->image_lib->resize();

                            }else{
                                copy($file_url, $file_t_url);
                            }
                            $query="UPDATE image_tb SET 	image_code='".$image_code."',image_url='".$file_url."' ,image_tumb_url='".$file_t_url."'  where image_id=".$image_id;
                            $result=$this->B_db->run_query_put($query);
                            header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                            ,"data"=>array('image_code'=>$image_code,'file_url'=>IMGADD.$file_url,'file_t_url'=>IMGADD.$file_t_url)
                            ,'desc'=>'بارگزاری موفقیت آمیز بود'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            $query="DELETE FROM `image_tb` WHERE  image_id=".$image_id;

                            $result=$this->B_db->run_query_put($query);
                            header('Content-Type: application/json'); echo json_encode(array('result'=>"error"
                            ,"data"=>""
                            ,'desc'=>'بارگزاری موفقیت آمیز نبود'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                    }catch(Exception $e){

                        $query="DELETE FROM `image_tb` WHERE  image_id=".$image_id;

                        $result=$this->B_db->run_query_put($query);
                        header('Content-Type: application/json'); echo json_encode(array('result'=>"error1"
                        ,"data"=>""
                        ,'desc'=>'خطای ترای کش در بارگزاری'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                    }
                }else{
                    header('Content-Type: application/json'); echo json_encode(array('result'=>"error2"
                    ,"data"=>""
                    ,'desc'=>'فایل موجود نیست'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }
        }
        else if ($command=="uptoabr")
        {
            $image_name=$this->post('image_name') ;
            $image_desc=$this->post('image_desc') ;
            $upload_path = 'filefolder/uploadimg/'.$year.'/'.$month.'/'.$dey.'/';

            $filepath = $_FILES['image']['tmp_name'];
            $check    = getimagesize($filepath);
            $fileSize = filesize($filepath);
            $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
            $filetype = finfo_file($fileinfo, $filepath);

            if($check == false OR $fileSize === 0 OR ($fileSize > 7340032) OR (!in_array($filetype, array_keys($allowedTypes)))) {
                header('Content-Type: application/json'); echo json_encode(array('result'=>"error"
                ,"data"=>""
                ,'desc'=>'نوع فایل ارسالی تصویری نمی باشد یا سایز آن بیشتر از حد استاندارد است'.$check['mime']),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            } else{

                if ( !file_exists( $upload_path ) ) {
                    @mkdir( $upload_path, 0755, true ) ;
                }

                if(isset($_FILES['image']['name'])){

                    $query="INSERT INTO image_tb(  image_name, image_desc,image_timestamp) VALUES
                                 ( '".$image_name."','" .$image_desc."', now()) ";
                    $result=$this->B_db->run_query_put($query);
                    $image_id=$this->db->insert_id();

                    $current_timestamp=$date->getTimestamp();
                    $image_code=$current_timestamp. generateToken(8);

                    $fileinfo = pathinfo($_FILES['image']['name']);
                    $extension = $fileinfo['extension'];
                    $file_url = $upload_path .$image_code. '.' . $extension;
                    $file_t_url = $upload_path .'t'.$image_code. '.' . $extension;
                    $send_to_abr = 0;
                    try{
                        if (move_uploaded_file($_FILES['image']['tmp_name'],$file_url)){
                            if($extension!='png'){
                                copy($file_url, $file_t_url);
                                $this->load->library('image_lib');
                                $config['image_library'] = 'gd2';
                                //$config['create_thumb'] = TRUE;
                                $config['maintain_ratio'] = TRUE;
                                //$config['quality']   = 1000;

                                $config['source_image'] = $file_url;
                                $config['width']     = 1000;
                                $config['height']   = 1000;
                                $this->image_lib->clear();
                                $this->image_lib->initialize($config);
                                $this->image_lib->resize();

                                $config1['image_library'] = 'gd2';
                                //$config1['create_thumb'] = TRUE;
                                $config1['maintain_ratio'] = TRUE;
                                $config1['source_image'] = $file_t_url;
                                $config1['width'] = 200;
                                $config1['height'] = 200;
                                $this->image_lib->clear();
                                $this->image_lib->initialize($config1);
                                $this->image_lib->resize();
                            }else{
                                copy($file_url, $file_t_url);
                            }
                            $upload_abr_result = $this->up_to_abr($image_code, $file_url);
                            $upload_t_abr_result = $this->up_to_abr('t'.$image_code, $file_t_url);
                            if($upload_abr_result and $upload_t_abr_result)
                            {
                                $send_to_abr = 1;
                                gc_collect_cycles();
                                unlink($file_url);
                                unlink($file_t_url);
                                $file_url = '';
                                $file_t_url = '';
                            }
                            $query="UPDATE image_tb SET 	image_code='".$image_code."',image_url='".$file_url."' ,image_tumb_url='".$file_t_url."', image_abr='".$send_to_abr."'  where image_id=".$image_id;
                            $result=$this->B_db->run_query_put($query);
                            header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                            ,"data"=>array('image_code'=>$image_code,'file_url'=>$this->dwnpresigned_image($image_code, 3600),'file_t_url'=>$this->dwnpresigned_image('t'.$image_code, 3600))
                            ,'desc'=>'بارگزاری موفقیت آمیز بود'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            $query="DELETE FROM `image_tb` WHERE  image_id=".$image_id;

                            $result=$this->B_db->run_query_put($query);
                            header('Content-Type: application/json'); echo json_encode(array('result'=>"error"
                            ,"data"=>""
                            ,'desc'=>'بارگزاری موفقیت آمیز نبود'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                    }catch(Exception $e){

                        $query="DELETE FROM `image_tb` WHERE  image_id=".$image_id;

                        $result=$this->B_db->run_query_put($query);
                        header('Content-Type: application/json'); echo json_encode(array('result'=>"error1"
                        ,"data"=>""
                        ,'desc'=>'خطای ترای کش در بارگزاری'.$e),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                    }
                }else{
                    header('Content-Type: application/json'); echo json_encode(array('result'=>"error2"
                    ,"data"=>""
                    ,'desc'=>'فایل موجود نیست'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }
        }
        else if ($command=="uptoabrwhittoken")
        {
            if($user_token_str=="Bearer eyJ4NXQiOiJNell4TW1Ga09HWXdNV0kwWldObU5EY3hOR1l3WW1NNFpUQTNNV0kyTkRBelpHUXpOR00wWkdSbE5qSmtPREZrWkRSaU9URmtNV0ZoTXpVMlpHVmxOZyIsImtpZCI6Ik16WXhNbUZrT0dZd01XSTBaV05tTkRjeE5HWXdZbU00WlRBM01XSTJOREF6WkdRek5HTTBaR1JsTmpKa09ERmtaRFJpT1RGa01XRmhNelUyWkdWbE5nX1JTMjU2IiwiYWxnIjoiUlMyNTYifQ.eyJzdWIiOiJsdXR1c0BjYXJib24uc3VwZXIiLCJhdWQiOiJwRmo0RWtYMEJsRHQ1X01MWkpEWVRQX3dVS2dhIiwibmJmIjoxNjQyNzE5NjYwLCJhenAiOiJwRmo0RWtYMEJsRHQ1X01MWkpEWVRQX3dVS2dhIiwic2NvcGUiOiJhbV9hcHBsaWNhdGlvbl9zY29wZSBkZWZhdWx0IiwiaXNzIjoiaHR0cHM6XC9cL2lkZW50aXR5LmlpeC5jZW50aW5zdXIub3JnOjk0NDJcL29hdXRoMlwvdG9rZW4iLCJleHAiOjE2NDI3MjMyNjAsImlhdCI6MTY0MjcxOTY2MCwianRpIjoiNDYwNDNjM2ItYjVhZS00Yzg3LWE5MjMtMTY1OGU2MDRjZjUyIn0.rYtTsWx_S009bf2Ji9bstG44iBCVAhN8f0nfanFPcVlZL4J9ma7buAEr8_r77opQ4lrM_yqVqiPbvdQShAd-FKuGB6XuqG8ATMpsP-d5kjJyXanNcFXJvGPOUqfp5xlPG0n_SPUJk_G0P2HKR_SUI8PxexLeJdQ5ZlZc6LuptkbTIfRWaVrM2jrur3tnQybWO-kOvaMJnF2KVulCer07wH-v4pJQT3lrtaj51Fb4HalucbwPFGRptvsWzPsKxPu7iYv1qilBTMhU92Tmx3i6Y7hQUbVVeNxKn3pwooaxN1qRAyJWuvXwx2F-ocgfcJZ2T44pwhptTz5ALj83GRyICA") {
                $image_name = $this->post('image_name');
                $image_desc = $this->post('image_desc');
                $upload_path = 'filefolder/uploadimg/' . $year . '/' . $month . '/' . $dey . '/';

                $filepath = $_FILES['image']['tmp_name'];
                $check = getimagesize($filepath);
                $fileSize = filesize($filepath);
                $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
                $filetype = finfo_file($fileinfo, $filepath);

                if ($check == false or $fileSize === 0 or ($fileSize > 7340032) or (!in_array($filetype, array_keys($allowedTypes)))) {
                    header('Content-Type: application/json'); echo json_encode(array('result' => "error"
                    , "data" => ""
                    , 'desc' => 'نوع فایل ارسالی تصویری نمی باشد یا سایز آن بیشتر از حد استاندارد است' . $check['mime']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } else {

                    if (!file_exists($upload_path)) {
                        @mkdir($upload_path, 0755, true);
                    }

                    if (isset($_FILES['image']['name'])) {

                        $query = "INSERT INTO image_tb(  image_name, image_desc,image_timestamp) VALUES
                                 ( '" . $image_name . "','" . $image_desc . "', now()) ";
                        $result = $this->B_db->run_query_put($query);
                        $image_id = $this->db->insert_id();

                        $current_timestamp = $date->getTimestamp();
                        $image_code = $current_timestamp . generateToken(8);

                        $fileinfo = pathinfo($_FILES['image']['name']);
                        $extension = $fileinfo['extension'];
                        $file_url = $upload_path . $image_code . '.' . $extension;
                        $file_t_url = $upload_path . 't' . $image_code . '.' . $extension;
                        $send_to_abr = 0;
                        try {
                            if (move_uploaded_file($_FILES['image']['tmp_name'], $file_url)) {
                                if ($extension != 'png') {
                                    copy($file_url, $file_t_url);
                                    $this->load->library('image_lib');
                                    $config['image_library'] = 'gd2';
                                    //$config['create_thumb'] = TRUE;
                                    $config['maintain_ratio'] = TRUE;
                                    //$config['quality']   = 1000;

                                    $config['source_image'] = $file_url;
                                    $config['width'] = 1000;
                                    $config['height'] = 1000;
                                    $this->image_lib->clear();
                                    $this->image_lib->initialize($config);
                                    $this->image_lib->resize();

                                    $config1['image_library'] = 'gd2';
                                    //$config1['create_thumb'] = TRUE;
                                    $config1['maintain_ratio'] = TRUE;
                                    $config1['source_image'] = $file_t_url;
                                    $config1['width'] = 200;
                                    $config1['height'] = 200;
                                    $this->image_lib->clear();
                                    $this->image_lib->initialize($config1);
                                    $this->image_lib->resize();
                                } else {
                                    copy($file_url, $file_t_url);
                                }
                                $upload_abr_result = $this->up_to_abr($image_code, $file_url);
                                $upload_t_abr_result = $this->up_to_abr('t' . $image_code, $file_t_url);
                                if ($upload_abr_result and $upload_t_abr_result) {
                                    $send_to_abr = 1;
                                    gc_collect_cycles();
                                    unlink($file_url);
                                    unlink($file_t_url);
                                    $file_url = '';
                                    $file_t_url = '';
                                }
                                $query = "UPDATE image_tb SET 	image_code='" . $image_code . "',image_url='" . $file_url . "' ,image_tumb_url='" . $file_t_url . "', image_abr='" . $send_to_abr . "'  where image_id=" . $image_id;
                                $result = $this->B_db->run_query_put($query);
                                header('Content-Type: application/json'); echo json_encode(array('result' => "ok"
                                , "data" => array('image_code' => $image_code, 'file_url' => $this->dwnpresigned_image($image_code, 3600), 'file_t_url' => $this->dwnpresigned_image('t' . $image_code, 3600))
                                , 'desc' => 'بارگزاری موفقیت آمیز بود'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            } else {
                                $query = "DELETE FROM `image_tb` WHERE  image_id=" . $image_id;

                                $result = $this->B_db->run_query_put($query);
                                header('Content-Type: application/json'); echo json_encode(array('result' => "error"
                                , "data" => ""
                                , 'desc' => 'بارگزاری موفقیت آمیز نبود'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }
                        } catch (Exception $e) {

                            $query = "DELETE FROM `image_tb` WHERE  image_id=" . $image_id;

                            $result = $this->B_db->run_query_put($query);
                            header('Content-Type: application/json'); echo json_encode(array('result' => "error1"
                            , "data" => ""
                            , 'desc' => 'خطای ترای کش در بارگزاری' . $e), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                        }
                    } else {
                        header('Content-Type: application/json'); echo json_encode(array('result' => "error2"
                        , "data" => ""
                        , 'desc' => 'فایل موجود نیست'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                }
            } else {
                header('Content-Type: application/json'); echo json_encode(array('result' => "error"
                , 'desc' => 'شما مجوز دسترسی ندارید'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }

        else if ($command=="getimage")
        {
            $image_code=$this->post('image_code') ;
            $expire_time=3600;
            $abr_expire_time=$this->post('abr_expire_time') ;
            if($abr_expire_time!='')
                $expire_time = $abr_expire_time;
            $query="select * from image_tb where image_code='".$image_code."'";
            $result=$this->B_db->run_query($query);
            $num=count($result[0]);
            if ($num!=0)
            {
                $image=$result[0];
                $image_abr = $image['image_abr'];
                if($image_abr == 0){
                    $image_url     =IMGADD.$image['image_url'];
                    $image_tumb_url=IMGADD.$image['image_tumb_url'];
                }else{
                    $image_url     = $this->dwnpresigned_image($image_code, $expire_time);
                    $image_tumb_url= $this->dwnpresigned_image('t'.$image_code, $expire_time);
                }
                header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                ,"data"=>array('image_timestamp'=>$image['image_timestamp'],'image_desc'=>$image['image_desc'],'image_name'=>$image['image_name'],
                        'image_code'=>$image['image_code'],'image_url'=>$image_url,'image_tumb_url'=>$image_tumb_url)
                ,'desc'=>'عکس ارسال شد'),JSON_UNESCAPED_SLASHES);
            }else{
                header('Content-Type: application/json'); echo json_encode(array('result'=>"error3"
                ,"data"=>''
                ,'desc'=>'کد عکس موجود نمیباشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }
        }
    }

    public function dwnpresigned_image($image_code, $expire_time=3600){
        $bucket = 'folderupload';
        $client = $this->s3Client;
        $keyExists = $this->s3Client->doesObjectExist($bucket, $image_code);
        if ($keyExists) {
            $cmd = $client->getCommand('GetObject', [
                'Bucket' => $bucket,
                'Key' => $image_code
            ]);
            $request = $client->createPresignedRequest($cmd, '+'.$expire_time.' seconds');
            return $presignedUrl = (string)$request->getUri();
        }else
            return '';
    }

    public function up_to_abr($filename ,$file_path){
        $bucket = 'folderupload';
        $client = $this->s3Client;
        try {
            $result = $client->putObject([
                'Bucket' => $bucket,
                'Key' => $filename,
                'SourceFile' => $file_path,
            ]);
            return $result;
        } catch (S3Exception $e) {
            echo $e->getMessage() . "\n";
            return false;
        }
    }

    public function uptoabr_post(){
        $command = $this->post("command");
        $this->load->model('B_db');
        $this->load->helper('my_helper');
        $timezone=new DateTimeZone("Asia/Tehran");
        $date=new DateTime();
        $date->setTimezone($timezone);
        $year=$date->format("Y");
        $month=$date->format("m");
        $dey=$date->format("d");
        $allowedTypes = [
            'image/png' => 'png',
            'image/jpeg' => 'jpg'
        ];

        if ($command=="uploadpic")
        {
            $image_name=$this->post('image_name') ;
            $image_desc=$this->post('image_desc') ;
            $upload_path = 'filefolder/uploadimg/'.$year.'/'.$month.'/'.$dey.'/';

            $filepath = $_FILES['image']['tmp_name'];
            $check    = getimagesize($filepath);
            $fileSize = filesize($filepath);
            $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
            $filetype = finfo_file($fileinfo, $filepath);

            if($check == false OR $fileSize === 0 OR ($fileSize > 7340032) OR (!in_array($filetype, array_keys($allowedTypes)))) {
                header('Content-Type: application/json'); echo json_encode(array('result'=>"error"
                ,"data"=>""
                ,'desc'=>'نوع فایل ارسالی تصویری نمی باشد یا سایز آن بیشتر از حد استاندارد است'.$check['mime']),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            } else{

                if ( !file_exists( $upload_path ) ) {
                    @mkdir( $upload_path, 0755, true ) ;
                }

                if(isset($_FILES['image']['name']) and $this->security->xss_clean($_FILES['image'], TRUE)){
                    $query="INSERT INTO image_tb(  image_name, image_desc,image_timestamp) VALUES
                                 ( '".$image_name."','" .$image_desc."', now()) ";
                    $result=$this->B_db->run_query_put($query);
                    $image_id=$this->db->insert_id();

                    $current_timestamp=$date->getTimestamp();
                    $image_code=$current_timestamp. generateToken(8);

                    $fileinfo = pathinfo($_FILES['image']['name']);
                    $extension = $fileinfo['extension'];
                    $file_url = $upload_path .$image_code. '.' . $extension;
                    $file_t_url = $upload_path .'t'.$image_code. '.' . $extension;
                    $send_to_abr = 0;
                    try{
                        if (move_uploaded_file($_FILES['image']['tmp_name'],$file_url)){
                            if($extension!='png'){
                                copy($file_url, $file_t_url);
                                $this->load->library('image_lib');
                                $config['image_library'] = 'gd2';
                                //$config['create_thumb'] = TRUE;
                                $config['maintain_ratio'] = TRUE;
                                //$config['quality']   = 1000;

                                $config['source_image'] = $file_url;
                                $config['width']     = 1000;
                                $config['height']   = 1000;
                                $this->image_lib->clear();
                                $this->image_lib->initialize($config);
                                $this->image_lib->resize();

                                $config1['image_library'] = 'gd2';
                                //$config1['create_thumb'] = TRUE;
                                $config1['maintain_ratio'] = TRUE;
                                $config1['source_image'] = $file_t_url;
                                $config1['width'] = 200;
                                $config1['height'] = 200;
                                $this->image_lib->clear();
                                $this->image_lib->initialize($config1);
                                $this->image_lib->resize();
                            }else{
                                copy($file_url, $file_t_url);
                            }
                            $upload_abr_result = $this->up_to_abr($image_code, $file_url);
                            $upload_t_abr_result = $this->up_to_abr('t'.$image_code, $file_t_url);
                            if($upload_abr_result and $upload_t_abr_result)
                            {
                                $send_to_abr = 1;
                                gc_collect_cycles();
                                unlink($file_url);
                                unlink($file_t_url);
                            }
                            $query="UPDATE image_tb SET 	image_code='".$image_code."',image_url='".$file_url."' ,image_tumb_url='".$file_t_url."', image_abr='".$send_to_abr."'  where image_id=".$image_id;
                            $result=$this->B_db->run_query_put($query);
                            header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                            ,"data"=>array('image_code'=>$image_code,'file_url'=>IMGADD.$file_url,'file_t_url'=>IMGADD.$file_t_url)
                            ,'desc'=>'بارگزاری موفقیت آمیز بود'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            $query="DELETE FROM `image_tb` WHERE  image_id=".$image_id;

                            $result=$this->B_db->run_query_put($query);
                            header('Content-Type: application/json'); echo json_encode(array('result'=>"error"
                            ,"data"=>""
                            ,'desc'=>'بارگزاری موفقیت آمیز نبود'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                    }catch(Exception $e){
                        $query="DELETE FROM `image_tb` WHERE  image_id=".$image_id;
                        $result=$this->B_db->run_query_put($query);
                        header('Content-Type: application/json'); echo json_encode(array('result'=>"error1"
                        ,"data"=>""
                        ,'desc'=>'خطای دوم ترای کش در بارگزاری'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                }else{
                    header('Content-Type: application/json'); echo json_encode(array('result'=>"error2"
                    ,"data"=>""
                    ,'desc'=>'فایل موجود نیست'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }
        }
        else if ($command=="getimage")
        {
            $image_code=$this->post('image_code') ;
            $query="select * from image_tb where image_code='".$image_code."'";
            $result=$this->B_db->run_query($query);
            $num=count($result[0]);
            if ($num!=0)
            {
                $image=$result[0];
                header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                ,"data"=>array('image_timestamp'=>$image['image_timestamp'],'image_desc'=>$image['image_desc'],'image_name'=>$image['image_name'],'image_code'=>$image['image_code'],'image_url'=>IMGADD.$image['image_url'],'image_tumb_url'=>IMGADD.$image['image_tumb_url'])
                ,'desc'=>'عکس ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }else{
                header('Content-Type: application/json'); echo json_encode(array('result'=>"error3"
                ,"data"=>''
                ,'desc'=>'کد عکس موجود نمیباشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }
        }
    }

    public function upfile_toabr_post(){
        $command = $this->post("command");
        $this->load->model('B_db');
        $this->load->helper('my_helper');
        $timezone=new DateTimeZone("Asia/Tehran");
        $date=new DateTime();
        $date->setTimezone($timezone);
        $year=$date->format("Y");
        $month=$date->format("m");
        $dey=$date->format("d");
        $allowedTypes = [
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            'application/pdf' => 'pdf',
        ];

        if ($command=="uploadfile")
        {
            $image_name=$this->post('file_name') ;
            $image_desc=$this->post('file_desc') ;
            $upload_path = 'filefolder/uploadimg/'.$year.'/'.$month.'/'.$dey.'/';

            $filepath = $_FILES['file']['tmp_name'];
            $fileSize = filesize($filepath);
            $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
            $filetype = finfo_file($fileinfo, $filepath);

            if($fileSize === 0 OR ($fileSize > 7340032) OR (!in_array($filetype, array_keys($allowedTypes)))) {
                header('Content-Type: application/json'); echo json_encode(array('result'=>"error"
                ,"data"=>""
                ,'desc'=>'نوع فایل ارسالی صحیح نمی باشد یا سایز آن بیشتر از حد استاندارد است'.$check['mime']),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            } else{

                if ( !file_exists( $upload_path ) ) {
                    @mkdir( $upload_path, 0755, true ) ;
                }

                if(isset($_FILES['file']['name']) and $this->security->xss_clean($_FILES['file'], TRUE)){
                    $query="INSERT INTO image_tb(  image_name, image_desc,image_timestamp) VALUES
                            ('".$image_name."','" .$image_desc."', now()) ";
                    $result=$this->B_db->run_query_put($query);
                    $image_id=$this->db->insert_id();

                    $current_timestamp=$date->getTimestamp();
                    $image_code=$current_timestamp. generateToken(8);

                    $fileinfo = pathinfo($_FILES['file']['name']);
                    $extension = $fileinfo['extension'];
                    $file_url = $upload_path .$image_code. '.' . $extension;
                    $send_to_abr = 0;
                    try{
                        if (move_uploaded_file($_FILES['file']['tmp_name'],$file_url)){
                            $upload_abr_result = $this->up_to_abr($image_code, $file_url);
                            if($upload_abr_result)
                            {
                                $send_to_abr = 1;
                                gc_collect_cycles();
                                unlink($file_url);
                            }
                            $query="UPDATE image_tb SET image_code='".$image_code."', image_abr='".$send_to_abr."'  where image_id=".$image_id;
                            $result=$this->B_db->run_query_put($query);
                            header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                            ,"data"=>array('image_code'=>$image_code,'file_url'=>IMGADD.$file_url)
                            ,'desc'=>'بارگزاری موفقیت آمیز بود'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }else{
                            $query="DELETE FROM `image_tb` WHERE  image_id=".$image_id;
                            $result=$this->B_db->run_query_put($query);
                            header('Content-Type: application/json'); echo json_encode(array('result'=>"error"
                            ,"data"=>""
                            ,'desc'=>'بارگزاری موفقیت آمیز نبود'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                    }catch(Exception $e){
                        $query="DELETE FROM `image_tb` WHERE  image_id=".$image_id;
                        $result=$this->B_db->run_query_put($query);
                        header('Content-Type: application/json'); echo json_encode(array('result'=>"error1"
                        ,"data"=>""
                        ,'desc'=>'خطای ترای کش در بارگزاری'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                    }
                }else{
                    header('Content-Type: application/json'); echo json_encode(array('result'=>"error2"
                    ,"data"=>""
                    ,'desc'=>'فایل موجود نیست'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }
        }
        else if ($command=="getfile")
        {
            $file_code=$this->post('file_code') ;
            $query="select * from image_tb where image_code='".$file_code."'";
            $result=$this->B_db->run_query($query);
            $num=count($result[0]);
            if ($num!=0)
            {
                $image=$result[0];
                header('Content-Type: application/json'); echo json_encode(array('result'=>"ok"
                ,"data"=>array('image_timestamp'=>$image['image_timestamp'],'image_desc'=>$image['image_desc'],'image_name'=>$image['image_name'],'image_code'=>$image['image_code'],'image_url'=>IMGADD.$image['image_url'],'image_tumb_url'=>IMGADD.$image['image_tumb_url'])
                ,'desc'=>'عکس ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); 
            }else{
                header('Content-Type: application/json'); echo json_encode(array('result'=>"error3"
                ,"data"=>''
                ,'desc'=>'کد عکس موجود نمیباشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }
        }
    }

    public function dwnpresigned_post(){
        $bucket = 'folderupload';
        $client = $this->s3Client;
        $expire_time = 3600;
        $_time = $this->post('expire_time');
        if($_time!='')
            $expire_time = $_time;
        $file_name = $this->post('image_key');
        $keyExists = $this->s3Client->doesObjectExist($bucket, $file_name);
        if ($keyExists) {
            $cmd = $client->getCommand('GetObject', [
                'Bucket' => $bucket,
                'Key' => $file_name
            ]);
            $request = $client->createPresignedRequest($cmd, '+'.$expire_time.' seconds');
            $presignedUrl = (string)$request->getUri();
            header('Content-Type: application/json'); echo json_encode(array('result'=>"ok","data"=>array('object_url'=>$presignedUrl)),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }else
            header('Content-Type: application/json'); echo json_encode(array('result'=>"error",'desc'=>'فایل مورد نظر یافت نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    function get_mime($file) {
        if (function_exists("finfo_file")) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file);
            finfo_close($finfo);
            return $mime;
        } else if (function_exists("mime_content_type")) {
            return mime_content_type($file);
        } else if (!stristr(ini_get("disable_functions"), "shell_exec")) {
            $file = escapeshellarg($file);
            $mime = shell_exec("file -bi " . $file);
            return $mime;
        } else {
            return false;
        }
    }

}
