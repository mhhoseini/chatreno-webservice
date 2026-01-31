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
class Exami extends CI_Controller {

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


    public function sql_post(){
        $conn = "";

        $query="select * from sarfasl_taffs where RowId =='' or RowId==0 ";
        $result = $this->B_db->run_query($query);
        foreach($result as $row) {
            $sql = "
            INSERT INTO dbo.TVP_SanadArticleS
          (
            [SalMali],
            [Shobe],
            [Code],
            [Tarikh],
            [CodeKol],
            [CodeMoin],
            [CodeTaf_Group],
            [CodeTaf],
            [CodeTaf2_Group],
            [CodeTaf2],
            [Ready],
            [Bed],
            [Bes],
            [Radif],
            Pages,
            ArzValue,
            ArzFee,
            RowDetXML
          )
          values
           (" . $row["SalMali"] . ",1,2312,'1399/12/30',414,1,0,0,0,0,1,1000,0,1,0,0,0,'<Records Tarikh=\"1399/10/10\" Code=\"123456\" Tedad=\"1\" Fee=\"2\" Sharh=\"T1\" />')
            ";
            $result = sqlsrv_query($conn, $sql);
            $RowId = $result->RowId();
            $update_sql = "Update sarfasl_taffs set RowId=" . $RowId . " Where SarfaslTaffs_ID = " . $row["SarfaslTaffs_ID"];
            $res = $this->B_db->run_query_put($update_sql);
        }
    }

    public function test_post(){
        $conn_array = array (
            "UID" => "sa",
            "PWD" => "302050",
            "Database" => "Fasih2",
        );
        $conn = sqlsrv_connect('91.92.127.79\SQLEXPRESS,1433', $conn_array);

        if ($conn){
            $sql = "
            INSERT INTO dbo.TVP_SanadArticleS
          (
            [SalMali],
            [Shobe],
            [Code],
            [Tarikh],
            [CodeKol],
            [CodeMoin],
            [CodeTaf_Group],
            [CodeTaf],
            [CodeTaf2_Group],
            [CodeTaf2],
            [Ready],
            [Bed],
            [Bes],
            [Radif],
            Pages,
            ArzValue,
            ArzFee,
            RowDetXML
          )
          values
           (1399,1,2312,'1399/12/30',414,1,0,0,0,0,1,1000,0,1,0,0,0,'<Records Tarikh=\"1399/10/10\" Code=\"123456\" Tedad=\"1\" Fee=\"2\" Sharh=\"T1\" />')
            ";

            if(($result = sqlsrv_query($conn,$sql)) !== false){
                die(print_r($result));

            }else{
                die(print_r(sqlsrv_errors(), true));
            }
        }
    }

    public function file_acl_post(){
        $real_url = 'https://api.aref24.com/filefolder/uploadimg/2021/08/16/t1629118192QCEYgL5!.png';
        $url = 'http://aref24.com/api/file_acl/t1629118192QCEYgL5!.png';
        //$url = $this->post('url');
        if (isset($this->input->request_headers()['Authorization'])) $user_token_str = $this->input->request_headers()['Authorization'];
        $user_token = $this->B_db->check_user_token($user_token_str);
        if($user_token == "ok"){
            return $translated_url =  $this->translate_url($url);
        }else{
            echo json_encode(array('result'=>"error"
            ,"data"=>''
            ,'desc'=>'شما دسترسی مشاهده این فایل را ندارید'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
    }

    public function translate_url($url){
        return $url;
    }

    public function url_post(){
        //$this->load->library('encrypt');
        $this->load->library('encryption');
        $url = $this->post('url');
        $real_url = 'http://api.aref24.local/filefolder/uploadimg/kamioon.png';
        echo $ciphertext = $this->encryption->encrypt($real_url);
        echo '###';
        echo $this->encryption->decrypt($ciphertext);
        $fp = fopen($real_url,'rb');

        header('Content-Type: image/png');

        //header('Content-length: ' . filesize($real_url));
        //fpassthru($fp);
        echo json_encode(array('result'=>"ok"
        ,"data"=>$fp
        ,'desc'=>'شما دسترسی مشاهده این فایل را ندارید'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        die;


        echo $user_id = strtr($url,array('+' => '.', '=' => '-', '/' => '~'));


        //echo $user_id_link = urlencode($url);
        echo '###';
        //echo $user_id      = urldecode($user_id_link);

        //echo  $url;
    }

    function show_image() {
        $this->load->library('encryption');
        $this->load->model('B_db');

        $real_url = 'http://api.aref24.local/filefolder/uploadimg/kamioon.png';
        $ciphertext = $this->encryption->encrypt($real_url);
        $real_url = $this->encryption->decrypt($ciphertext);

        if (isset($this->input->request_headers()['Authorization'])) $user_token_str = $this->input->request_headers()['Authorization'];
        $user_token = $this->B_db->check_user_token($user_token_str);

        if($user_token == "ok"){
            echo  '<img src="'.$real_url.'" />';
            return $translated_url =  $this->translate_url($real_url);
        }else{
            echo json_encode(array('result'=>"error"
            ,"data"=>''
            ,'desc'=>'شما دسترسی مشاهده این فایل را ندارید'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }

    }
}