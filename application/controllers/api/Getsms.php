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
class Getsms extends REST_Controller
{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        //1000001439
    }

    public function index_post()
    {
        $stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
        $request = json_decode($stream_clean);
        $destAddrNbr = $request->destAddrNbr;
        $origAddr = $request->origAddr;
        $message = $request->message;
        $binary = $request->binary;

        $remote_pc = $_SERVER['REMOTE_ADDR'];
        $destAddrNbr1 = $this->post("destAddrNbr");
        $origAddr1 = $this->post("origAddr");
        $message1 = $this->post("message");
        $binary1 = $this->post("binary");
        //$message = $remote_pc;

        $this->load->model('B_db');
        $query1="INSERT INTO sms_recive_tb
        (sms_recive_sender_num, sms_recive_reciver_num, sms_recive_text, sms_recive_date, sms_recive_fa)
        VALUES ('$origAddr.$origAddr1' ,'$destAddrNbr.$destAddrNbr1','$message.$message1', now(),'$binary.$binary1') ";
        $result1=$this->B_db->run_query_put($query1);
        echo 'getok';
    }

    public function index_get()
    {
        $stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
        $request = json_decode($stream_clean);
        $destAddrNbr = $request->destAddrNbr;
        $origAddr = $request->origAddr;
        $message = $request->message;
        $binary = $request->binary;

        $destAddrNbr1 = $this->get("destAddrNbr");
        $origAddr1 = $this->get("origAddr");
        $message1 = $this->get("message");
        $binary1 = $this->get("binary");

        $this->load->model('B_db');
        $query1="INSERT INTO sms_recive_tb
        (sms_recive_sender_num, sms_recive_reciver_num, sms_recive_text, sms_recive_date, sms_recive_fa)
        VALUES ('$origAddr.$origAddr1' ,'$destAddrNbr.$destAddrNbr1','$message.$message1', now(),'$binary.$binary1') ";
        $result1=$this->B_db->run_query_put($query1);
        echo 'getok';
    }

    public function read_post()
    {
        //var_dump($_SERVER);
        $this->load->model('B_db');
        $sql = "select * from sms_recive_tb where sms_recive_id>35 order by sms_recive_id DESC ";
        $result=$this->B_db->run_query($sql);
        foreach($result as $row)
            print_r($row);
    }

    function call_me_post(){

        $destAddrNbr = $this->post("destAddrNbr");
        $origAddr = $this->post("origAddr");
        $message = $this->post("message");
        $binary = $this->post("binary");

        $url = 'https://api.aref24.com/api/getsms/';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL,$url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            'Content-Type: application/json'
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $post = [
            "message"=> $message,
            "destAddrNbr"=> $destAddrNbr,
            "origAddr"=> $origAddr,
            "binary"=> $binary
        ];
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $output = curl_exec($curl);
        if($output === false){
            print_r('Curl error: ' . curl_error($curl));
        }
        return $output = json_decode($output, true);
    }
}