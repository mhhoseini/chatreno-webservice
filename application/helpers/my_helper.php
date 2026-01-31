<?php
/**
 * @author   Natan Felles <natanfelles@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');


if ( ! function_exists('get_client_ip'))
{
    /**
     * @param string $trigger_name Trigger name
     *
     * @return string SQL Command
     */
    function get_client_ip() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}


if ( ! function_exists('send_sms')) {
    function send_sms($mobile, $code)
    {
        send_sms_rahyab($mobile," به عارف۲۴ خوش آمدید. کد فعال سازی شما:".$code);
        //     create & initialize a curl session
//        $curl = curl_init();
//        curl_setopt($curl, CURLOPT_URL,"http://ippanel.com:8080/?apikey=wVrk_tSH6Hjc6x2ytUak3yl1kQzTKmtt0U1Vq3F87j4=&pid=h0re8nkomz&fnum=5000125475&tnum=".$mobile."&p1=verification-code&v1=".$code);
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//        $output = curl_exec($curl);
//        curl_close($curl);
    }
}

if ( ! function_exists('send_sms2')) {
    function send_sms2($mobile, $code)
    {
        send_sms_rahyab($mobile," به عارف۲۴ خوش آمدید. کد فعال سازی شما:".$code);
        //     create & initialize a curl session
//        $curl = curl_init();
//        curl_setopt($curl, CURLOPT_URL,"http://ippanel.com:8080/?apikey=wVrk_tSH6Hjc6x2ytUak3yl1kQzTKmtt0U1Vq3F87j4=&pid=h0re8nkomz&fnum=5000125475&tnum=".$mobile."&p1=verification-code&v1=".$code);
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//        $output = curl_exec($curl);
//        curl_close($curl);
    }
}

if ( ! function_exists('send_marketeractive_sms')) {
    function send_marketeractive_sms($user_id, $status)
    {
        $CI = get_instance();
        $CI->load->model('B_db');
        $query = "SELECT user_mobile FROM user_tb where user_id=".$user_id;
        $result=$CI->B_db->run_query($query);
        if(!empty($result))
            $user_mobile = $result[0]['user_mobile'];
        else
            return  array("error",'شماره موبایل کاربر نمی تواند خالی باشد');
        // create & initialize a curl session
        send_sms_rahyab($user_mobile,"با افتخار درخواست همکاری فروش شما $status در عارف۲۴-سامانه آنلاین بیمه تایید شد.");

//        $status = rawurlencode($status);
//        $url_user  = "http://ippanel.com:8080/?apikey=wVrk_tSH6Hjc6x2ytUak3yl1kQzTKmtt0U1Vq3F87j4=&pid=k6r1uwgkv0&fnum=5000125475&tnum=".$user_mobile."&p1=status&v1=".$status;
//        $curl = curl_init();
//        curl_setopt($curl, CURLOPT_URL,$url_user);
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//        $output = curl_exec($curl);
//        curl_close($curl);

    }
}

if ( ! function_exists('send_refund_sms')) {
    function send_refund_sms($user_id, $status)
    {
        $CI = get_instance();
        $CI->load->model('B_db');
        $query = "SELECT user_mobile FROM user_tb where user_id=".$user_id;
        $result=$CI->B_db->run_query($query);
        if(!empty($result))
            $user_mobile = $result[0]['user_mobile'];
        else
            return  array("error",'شماره موبایل کاربر نمی تواند خالی باشد');
        // create & initialize a curl session

        send_sms_rahyab($user_mobile,$status);


//        $status = rawurlencode($status);
//        $url_user  = "http://ippanel.com:8080/?apikey=wVrk_tSH6Hjc6x2ytUak3yl1kQzTKmtt0U1Vq3F87j4=&pid=ubg24nflj6&fnum=5000125475&tnum=".$user_mobile."&p1=status&v1=".$status;
//        $curl = curl_init();
//        curl_setopt($curl, CURLOPT_URL,$url_user);
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//        $output = curl_exec($curl);
//        curl_close($curl);

    }
}

if ( ! function_exists('send_marketerdeactive_sms')) {
    function send_marketerdeactive_sms($user_id, $status)
    {
        $CI = get_instance();
        $CI->load->model('B_db');
        $query = "SELECT user_mobile FROM user_tb where user_id=".$user_id;
        $result=$CI->B_db->run_query($query);
        if(!empty($result))
            $user_mobile = $result[0]['user_mobile'];
        else
            return  array("error",'شماره موبایل کاربر نمی تواند خالی باشد');

        $status ="باسلام. لطفا درخواست همکاری با عارف۲۴ را به علت $status  مجددا ارسال نمایید ";
        send_sms_rahyab($user_mobile,$status);

        // create & initialize a curl session
//        $status = rawurlencode($status);
//        $url_user  = "http://ippanel.com:8080/?apikey=wVrk_tSH6Hjc6x2ytUak3yl1kQzTKmtt0U1Vq3F87j4=&pid=9up92a7478&fnum=5000125475&tnum=".$user_mobile."&p1=status&v1=".$status;
//        $curl = curl_init();
//        curl_setopt($curl, CURLOPT_URL,$url_user);
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//        $output = curl_exec($curl);
//        curl_close($curl);

    }
}
if ( ! function_exists('survey_send_sms')) {
    function survey_send_sms($id)
    {
        $CI = get_instance();
        $CI->load->model('B_db');

        $query = "SELECT user_mobile FROM user_tb,request_tb where user_id=request_user_id AND request_id=".$id;
        $result=$CI->B_db->run_query($query);
        if(!empty($result))
            $user_mobile = $result[0]['user_mobile'];
        else
            return  array("error",'شماره موبایل کاربر نمی تواند خالی باشد');

        $status="با تشکر از حسن انتخاب شما،سپاس‌گزار خواهیم بود با تکمیل فرم نظرسنجی،ما را برای ارایه خدمات بهتر یاری نمایید=aref24.com/survey?id".$id;
        $output=send_sms_rahyab($user_mobile,$status);

//            $text = rawurlencode($text);
//            $url_user  = "http://ippanel.com:8080/?apikey=wVrk_tSH6Hjc6x2ytUak3yl1kQzTKmtt0U1Vq3F87j4=&pid=0dd1s12x08&fnum=5000125475&tnum=".$user_mobile."&p1=request_id&p2=status&v1=".$id."&v2=".$text;
//            $curl = curl_init();
//            curl_setopt($curl, CURLOPT_URL,$url_user);
//            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//            $output = curl_exec($curl);
//            curl_close($curl);
//            return array("error",$output.$url_user);
        return $output;
    }}

if ( ! function_exists('request_send_sms')) {
    function request_send_sms($id, $mode, $text)
    {
        $CI = get_instance();
        $CI->load->model('B_db');
        if($mode == 'user'){
            $query = "SELECT user_mobile FROM user_tb,request_tb where user_id=request_user_id AND request_id=".$id;
            $result=$CI->B_db->run_query($query);
            if(!empty($result))
                $user_mobile = $result[0]['user_mobile'];
            else
                return  array("error",'شماره موبایل کاربر نمی تواند خالی باشد');

            $status="درخواست شماره $id   شما در عارف۲۴ به وضعیت $text تغییر یافت";
            $output=send_sms_rahyab($user_mobile,$status);

//            $text = rawurlencode($text);
//            $url_user  = "http://ippanel.com:8080/?apikey=wVrk_tSH6Hjc6x2ytUak3yl1kQzTKmtt0U1Vq3F87j4=&pid=0dd1s12x08&fnum=5000125475&tnum=".$user_mobile."&p1=request_id&p2=status&v1=".$id."&v2=".$text;
//            $curl = curl_init();
//            curl_setopt($curl, CURLOPT_URL,$url_user);
//            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//            $output = curl_exec($curl);
//            curl_close($curl);
//            return array("error",$output.$url_user);
            return $output;
        }else if($mode == "agent"){
            $query = "SELECT agent_mobile3,agent_mobile1,agent_mobile2,agent_name,agent_family,fieldinsurance_id FROM agent_tb,request_tb,fieldinsurance_tb where fieldinsurance=request_fieldinsurance  AND agent_id=request_agent_id AND request_id=".$id;
            $result=$CI->B_db->run_query($query);
            if(!empty($result))
            {
                $agent_mobile3  = $result[0]['agent_mobile3'];
                $agent_mobile1 = $result[0]['agent_mobile1'];
                $agent_mobile2 = $result[0]['agent_mobile2'];
                $agent_name = $result[0]['agent_name'];
                $agent_family = $result[0]['agent_family'];
                $fieldinsurance_id= $result[0]['fieldinsurance_id'];
            }else
                return  array("error",'شماره موبایل کاربر نمی تواند خالی باشد');
            //    $text = rawurlencode($id.' '.$agent_name.' ' .$agent_family.' '.$fieldinsurance_id);
            $text = $id.' '.$agent_name.' ' .$agent_family.' '.$fieldinsurance_id;

            $status="درخواست شماره  $text در عارف۲۴ به شما ارجاع داده شد";
            send_sms_rahyab($agent_mobile1,$status);
            send_sms_rahyab($agent_mobile2,$status);
            $output=send_sms_rahyab($agent_mobile3,$status);

//            $url_agent = "http://ippanel.com:8080/?apikey=wVrk_tSH6Hjc6x2ytUak3yl1kQzTKmtt0U1Vq3F87j4=&pid=oxzmrlzmp8&fnum=5000125475&tnum=".$agent_mobile3."&p1=request_id&v1=".$text;
//            $curl = curl_init();
//            curl_setopt($curl, CURLOPT_URL,$url_agent);
//            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//            $output = curl_exec($curl);
//            curl_close($curl);
//            //*************************************************
//            $url_agent1 = "http://ippanel.com:8080/?apikey=wVrk_tSH6Hjc6x2ytUak3yl1kQzTKmtt0U1Vq3F87j4=&pid=oxzmrlzmp8&fnum=5000125475&tnum=".$agent_mobile1."&p1=request_id&v1=".$text;
//            $curl1= curl_init();
//            curl_setopt($curl1, CURLOPT_URL,$url_agent1);
//            curl_setopt($curl1, CURLOPT_RETURNTRANSFER, 1);
//            $output1 = curl_exec($curl1);
//            curl_close($curl1);
//            //***************************************
//            $url_agent2 = "http://ippanel.com:8080/?apikey=wVrk_tSH6Hjc6x2ytUak3yl1kQzTKmtt0U1Vq3F87j4=&pid=oxzmrlzmp8&fnum=5000125475&tnum=".$agent_mobile2."&p1=request_id&v1=".$text;
//            $curl2 = curl_init();
//            curl_setopt($curl2, CURLOPT_URL,$url_agent2);
//            curl_setopt($curl2, CURLOPT_RETURNTRANSFER, 1);
//            $output2 = curl_exec($curl2);
//            curl_close($curl2);

            //      return array("error",$output.$url_agent);
            return $output;
        }
    }
}

if ( ! function_exists('damagefile_send_sms')) {
    function damagefile_send_sms($id, $mode, $text)
    {
        $CI = get_instance();
        $CI->load->model('B_db');
        if($mode == 'user'){
            $query = "SELECT user_mobile FROM user_tb,damagefile_tb where user_id=damagefile_user_id AND damagefile_id=".$id;
            $result=$CI->B_db->run_query($query);
            if(!empty($result))
                $user_mobile = $result[0]['user_mobile'];
            else
                return  array("error",'شماره موبایل کاربر نمی تواند خالی باشد');

            $status="درخواست شماره $id   شما در عارف۲۴ به وضعیت $text تغییر یافت";
            $output=send_sms_rahyab($user_mobile,$status);
//
//            $text = rawurlencode($text);
//            $url_user  = "http://ippanel.com:8080/?apikey=wVrk_tSH6Hjc6x2ytUak3yl1kQzTKmtt0U1Vq3F87j4=&pid=0dd1s12x08&fnum=5000125475&tnum=".$user_mobile."&p1=request_id&p2=status&v1=".$id."&v2=".$text;
//            $curl = curl_init();
//            curl_setopt($curl, CURLOPT_URL,$url_user);
//            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//            $output = curl_exec($curl);
//            curl_close($curl);
//            return array("error",$output.$url_user);
            return $output;
        }else if($mode == "agent"){
            $query = "SELECT agent_mobile3,agent_mobile1,agent_mobile2,agent_name,agent_family,fieldinsurance_id FROM agent_tb,request_tb,fieldinsurance_tb where fieldinsurance=request_fieldinsurance  AND agent_id=request_agent_id AND request_id=".$id;
            $result=$CI->B_db->run_query($query);
            if(!empty($result))
            {
                $agent_mobile3  = $result[0]['agent_mobile3'];
                $agent_mobile1 = $result[0]['agent_mobile1'];
                $agent_mobile2 = $result[0]['agent_mobile2'];
                $agent_name = $result[0]['agent_name'];
                $agent_family = $result[0]['agent_family'];
                $fieldinsurance_fa= $result[0]['fieldinsurance_fa'];
            }else
                return  array("error",'شماره موبایل کاربر نمی تواند خالی باشد');

            $text = rawurlencode($id.' '.$agent_name.' ' .$agent_family.' '.$fieldinsurance_fa);

            $status="درخواست شماره  $text در عارف۲۴ به شما ارجاع داده شد";
            send_sms_rahyab($agent_mobile1,$status);
            send_sms_rahyab($agent_mobile2,$status);
            $output=send_sms_rahyab($agent_mobile3,$status);

//            $text = rawurlencode($id.' '.$agent_name.' ' .$agent_family.' '.$fieldinsurance_id);
//            $url_agent = "http://ippanel.com:8080/?apikey=wVrk_tSH6Hjc6x2ytUak3yl1kQzTKmtt0U1Vq3F87j4=&pid=oxzmrlzmp8&fnum=5000125475&tnum=".$agent_mobile3."&p1=request_id&v1=".$text;
//            $curl = curl_init();
//            curl_setopt($curl, CURLOPT_URL,$url_agent);
//            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//            $output = curl_exec($curl);
//            curl_close($curl);
//            //*************************************************
//            $url_agent1 = "http://ippanel.com:8080/?apikey=wVrk_tSH6Hjc6x2ytUak3yl1kQzTKmtt0U1Vq3F87j4=&pid=oxzmrlzmp8&fnum=5000125475&tnum=".$agent_mobile1."&p1=request_id&v1=".$text;
//            $curl1= curl_init();
//            curl_setopt($curl1, CURLOPT_URL,$url_agent1);
//            curl_setopt($curl1, CURLOPT_RETURNTRANSFER, 1);
//            $output1 = curl_exec($curl1);
//            curl_close($curl1);
//            //***************************************
//            $url_agent2 = "http://ippanel.com:8080/?apikey=wVrk_tSH6Hjc6x2ytUak3yl1kQzTKmtt0U1Vq3F87j4=&pid=oxzmrlzmp8&fnum=5000125475&tnum=".$agent_mobile2."&p1=request_id&v1=".$text;
//            $curl2 = curl_init();
//            curl_setopt($curl2, CURLOPT_URL,$url_agent2);
//            curl_setopt($curl2, CURLOPT_RETURNTRANSFER, 1);
//            $output2 = curl_exec($curl2);
//            curl_close($curl2);
//
//            return array("error",$output.$url_agent);
            return $output;
        }
    }
}

if ( ! function_exists('requestoffline_send_sms')) {
    function requestoffline_send_sms($user_mobile,$fieldinsurance_fa,$request_id)
    {
        $CI = get_instance();
        $CI->load->model('B_db');


        $agent_mobile3  ='09122377708';
        $agent_mobile1 = '09193009116';
        $agent_mobile2 = '09124373459';



        $status = $user_mobile.' در رشته '.$fieldinsurance_fa.' با شماره درخواست ' .$request_id;

        $output=send_sms_rahyab($agent_mobile1,"کاربر با شماره همراه  " .$status ." در عارف۲۴ درخواست نرخ دهی افلاین دارد");
        $output=send_sms_rahyab($agent_mobile2,"کاربر با شماره همراه  " .$status ." در عارف۲۴ درخواست نرخ دهی افلاین دارد");
        $output=send_sms_rahyab($agent_mobile3,"کاربر با شماره همراه  " .$status ." در عارف۲۴ درخواست نرخ دهی افلاین دارد");


//        $url_agent = "http://ippanel.com:8080/?apikey=wVrk_tSH6Hjc6x2ytUak3yl1kQzTKmtt0U1Vq3F87j4=&pid=yp429n0wrs&fnum=5000125475&tnum=".$agent_mobile3."&p1=status&v1=".$status;
//        $curl = curl_init();
//        curl_setopt($curl, CURLOPT_URL,$url_agent);
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//        $output = curl_exec($curl);
//        curl_close($curl);
//        //*************************************************
//        $url_agent1 = "http://ippanel.com:8080/?apikey=wVrk_tSH6Hjc6x2ytUak3yl1kQzTKmtt0U1Vq3F87j4=&pid=yp429n0wrs&fnum=5000125475&tnum=".$agent_mobile1."&p1=status&v1=".$status;
//        $curl1= curl_init();
//        curl_setopt($curl1, CURLOPT_URL,$url_agent1);
//        curl_setopt($curl1, CURLOPT_RETURNTRANSFER, 1);
//        $output1 = curl_exec($curl1);
//        curl_close($curl1);
//        //***************************************
//        $url_agent2 = "http://ippanel.com:8080/?apikey=wVrk_tSH6Hjc6x2ytUak3yl1kQzTKmtt0U1Vq3F87j4=&pid=yp429n0wrs&fnum=5000125475&tnum=".$agent_mobile2."&p1=status&v1=".$status;
//        $curl2 = curl_init();
//        curl_setopt($curl2, CURLOPT_URL,$url_agent2);
//        curl_setopt($curl2, CURLOPT_RETURNTRANSFER, 1);
//        $output2 = curl_exec($curl2);
//        curl_close($curl2);

        //  return array("error",$output.$url_agent);
        return $output;


    }
}

if ( ! function_exists('generateToken')){
    function generateToken( $length = 20 )
    {
        $temp = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $generate = "" ;
        for ( $i = 0 ; $i < $length ; $i++ ){
            $begin = 0 ;
            $end = mb_strlen($temp) - 1  ;
            $generate .=  $temp[ random_int( $begin , $end ) ] ;
        }
        return $generate ;
    }
}

if ( ! function_exists('send_email')){
    function send_email($email,$pass,$mobile)
    {

        $to =$email;
        $subject = "ارسال رمز عبور";
        $txt = "سلام کاربر گرامی رمز عبور شما"."\r\n" .$pass . "\r\n"."و شماره همراه ثبت شده". "\r\n" .$mobile. "\r\n". " می باشد" ;
        $headers = "From: webmaster@example.com" . "\r\n" .
            "CC: somebodyelse@example.com";
        mail($to,$subject,$txt,$headers);
    }
}

if ( ! function_exists('checkagenttoken')){
    function checkagenttoken($agent_token_str)
    {
        $CI = get_instance();
        $CI->load->model('B_db');
        $query="select * from agent_token_tb where agent_token_str='".$agent_token_str."'";
        $result=$CI->B_db->run_query($query);
        $num=count($result[0]);
        if ($num!=0)
        {
            $agent_token=$result[0];
            if($agent_token['agent_token_logout_timestamp']==''){
                return  array("ok"
                ,$agent_token['agent_token_agent_id']
                ,'ورود شما مورد تایید است'
                ,$agent_token['agent_token_employee_id']
                );
            }else
            {
                return array("error"
                ,$agent_token['agent_token_agent_id']
                ,'شما از سیستم خارج شده اید . مجددا وارد شوید'
                ,$agent_token['agent_token_employee_id']);
            }
        }else{
            return array("error"
            ,""
            ,'توکن در سیستم موجود نیست',0);
        }
    }
}

if ( ! function_exists('checkexperttoken')){
    function checkexperttoken($expert_token_str)
    {
        $CI = get_instance();
        $CI->load->model('B_db');
        $query="select * from expert_token_tb where expert_token_str='".$expert_token_str."'";
        $result=$CI->B_db->run_query($query);
        $num=count($result[0]);
        if ($num!=0)
        {
            $expert_token=$result[0];
            if($expert_token['expert_token_logout_timestamp']==''){
                return  array("ok"
                ,$expert_token['expert_token_expert_id']
                ,'ورود شما مورد تایید است'
                ,$expert_token['expert_token_employee_id']
                );
            }else
            {
                return array("error"
                ,$expert_token['expert_token_expert_id']
                ,'شما از سیستم خارج شده اید . مجددا وارد شوید'
                ,$expert_token['expert_token_employee_id']);
            }
        }else{
            return array("error"
            ,""
            ,'توکن در سیستم موجود نیست',0);
        }
    }
}


if ( ! function_exists('checkorgantoken')){
    function checkorgantoken($organ_token_str)
    {
        $CI = get_instance();
        $CI->load->model('B_db');
        $query="select * from organ_token_tb where organ_token_str='".$organ_token_str."'";
        $result=$CI->B_db->run_query($query);
        if (!empty($result))
        {
            $organ_token=$result[0];
            if($organ_token['organ_token_logout_timestamp']==''){
                return  array("ok"
                ,$organ_token['organ_token_organ_id']
                ,'ورود شما مورد تایید است');
            }else
            {
                return array("error"
                ,$organ_token['organ_token_organ_id']
                ,'شما از سیستم خارج شده اید . مجددا وارد شوید');
            }
        }else{
            return array("error"
            ,""
            ,'توکن در سیستم موجود نیست');
        }
    }
}

if ( ! function_exists('checkorganconfirmtoken')){
    function checkorganconfirmtoken($organ_confirm_token)
    {
        $CI = get_instance();
        $CI->load->model('B_db');
        $query="select * from organ_confirm_tb where organ_confirm_token='".$organ_confirm_token."'";
        $result=$CI->B_db->run_query($query);
        if (!empty($result))
        {
            $_token=$result[0];
            return  array("ok"
            ,array('organ_confirm_id'=>$_token['organ_confirm_id'])
            ,'ورود شما مورد تایید است');
        }else{
            return array("error"
            ,""
            ,'توکن در سیستم موجود نیست');
        }
    }
}

if ( ! function_exists('gettokenetma')){
    function gettokenetma()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.etmacard.ir/V0/OAuth2/CreateAuthToken',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
"grant_type": "client_credentials",
"nid": "0534629768",
"scopes": "facility:GetCustomerType:get"
}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic MDAwMDAwNTQyNjk3ODEzOjhDRDI1NjdELTlFRjYtNEIxNC04OTU1LTNGRTFBNjkyQTgyNzphUnE4NTlGeUBRd3gyITgwNzM0JT8=',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

    }
}

if ( ! function_exists('checkusertoken')){
    function checkusertoken($user_token_str)
    {
        $CI = get_instance();
        $CI->load->model('B_db');

        /*$pdo = $CI->load->database('second_db', TRUE);
        $query = $this->db->get_where('user_token_tb', array('user_token_str' => $user_token_str));
        //$stmt = $pdo->prepare("select * from user_token_tb where user_token_str=?");
        $stmt->execute([$user_token_str]);
        $result = $stmt->fetchColumn();
        $otherdb = $this->load->database('anotherdb', TRUE);
        var_dump($result);*/
        $sql="select * from user_token_tb where user_token_str='".$user_token_str."'";
        $result=$CI->B_db->run_query($sql);


        if (!empty($result))
        {
            $user_token=$result[0];
            if($user_token['user_token_logout_timestamp']==''){
                return  array("ok"
                ,$user_token['user_token_user_id']
                ,'ورود شما مورد تایید است');
            }else
            {
                return array("error"
                ,$user_token['user_token_user_id']
                ,'شما از سیستم خارج شده اید . مجددا وارد شوید');
            }
        }else{
            return array("error"
            ,""
            ,'توکن در سیستم موجود نیست');
        }
        /*$pdo = $CI->load->database('second_db', TRUE);
        $query = $this->db->get_where('user_token_tb', array('user_token_str' => $user_token_str));
        //$stmt = $pdo->prepare("select * from user_token_tb where user_token_str=?");
        $stmt->execute([$user_token_str]);
        $result = $stmt->fetchColumn();
        var_dump($result);
        */


    }
}

if ( ! function_exists('checkpermissionemployeetoken')){
    function checkpermissionemployeetoken($employee_token_str,$emloyee_permision_mode,$emloyee_permision_entity)
    {
        $CI = get_instance();
        $CI->load->model('B_db');
        $sql="select * from employee_token_tb where employee_token_str='".$employee_token_str."'";
        $result=$CI->B_db->run_query($sql);
        if (!empty($result[0]))
        {
            $employee_token=$result[0];
            if($employee_token['employee_token_logout_timestamp']==''){

                $sql1="select * from emloyee_permision_tb where emloyee_permision_emloyee_id=".$employee_token['employee_token_employee_id']." AND emloyee_permision_entity='".$emloyee_permision_entity."' AND emloyee_permision_mode='".$emloyee_permision_mode."'";
                $result1=$CI->B_db->run_query($sql1);
                if (empty($result1[0]))
                {
                    return  array("error"
                    ,$employee_token['employee_token_employee_id']
                    ,'ورود شما مورد تایید است ولی مجوز دسترسی ندارید');
                }else
                {
                    return  array("ok"
                    ,$employee_token['employee_token_employee_id']
                    ,' ورود شما مورد تایید است و مجوز دسترسی دارید');
                }

            }else
            {
                return array("error"
                ,$employee_token['employee_token_employee_id']
                ,'شما از سیستم خارج شده اید . مجددا وارد شوید');
            }
        }else{
            return array("error"
            ,""
            ,'توکن در سیستم موجود نیست');
        }
    }
}

if ( ! function_exists('checkemployeetoken')){
    function checkemployeetoken($employee_token_str)
    {
        $CI = get_instance();
        $CI->load->model('B_db');
        $query="select * from employee_token_tb where employee_token_str='".$employee_token_str."'";
        $result = $CI->B_db->run_query($query);
        $num=$result[0];
        if ($num!=0)
        {
            $employee_token=$result[0];
            if($employee_token['employee_token_logout_timestamp']==''){
                return  array("ok"
                ,$employee_token['employee_token_employee_id']
                ,'ورود شما مورد تایید است');
            }else
            {
                return array("error"
                ,$employee_token['employee_token_employee_id']
                ,'شما از سیستم خارج شده اید . مجددا وارد شوید');
            }
        }else{
            return array("error"
            ,$query
            ,'توکن در سیستم موجود .نیست');
        }
    }
}

if ( ! function_exists('checkusermarketertoken')){
    function checkusermarketertoken($user_token_str)
    {
        $CI = get_instance();
        $CI->load->model('B_db');
        $query="select * from user_token_tb where user_token_str='".$user_token_str."'";
        $result = $CI->B_db->run_query($query);
        $num=$result[0];
        if ($num!=0)
        {
            $user_token=$result[0];
            if($user_token['user_token_logout_timestamp']==''){
                $marketer_user_id=$user_token['user_token_user_id'];
                $query1="select * from usermarketer_tb,marketer_mode_tb where marketer_mode_tb.marketer_mode_id=usermarketer_tb.marketer_mode_id AND marketer_user_id=".$marketer_user_id."";
                $result1 = $CI->B_db->run_query($query1);
                $num1=$result1[0];
                if ($num1==0)
                {
                    return array('result'=>"error"
                    ,"data"=>array('marketer_user_id'=>$marketer_user_id)
                    ,'desc'=>'شما برای بازاریابی درخواست نداده اید');

                }else{
                    $usermarketer_tb=$result1[0];
                    if($usermarketer_tb['marketer_reject']==0&&$usermarketer_tb['marketer_request']==1){
                        return array('result'=>"wait"
                        ,"data"=>array('marketer_user_id'=>$usermarketer_tb['marketer_user_id'],'marketer_timestamp'=>$usermarketer_tb['marketer_timestamp'])
                        ,'desc'=>'شما برای بازاریابی درخواست  داده اید و منتظر تایید بمانید');

                    }else if($usermarketer_tb['marketer_reject']==1&&$usermarketer_tb['marketer_request']==1){
                        return array('result'=>"reject"
                        ,"data"=>array('marketer_user_id'=>$usermarketer_tb['marketer_user_id'],'marketer_timestamp'=>$usermarketer_tb['marketer_timestamp']
                            ,'marketer_reason'=>$usermarketer_tb['marketer_reason'])
                        ,'desc'=>'شما برای بازاریابی درخواست  داده اید و بدلیل بالا تایید نشد مجدادا  درخواست نمایید');

                    }else if($usermarketer_tb['marketer_reject']==0&&$usermarketer_tb['marketer_request']==0&&$usermarketer_tb['marketer_deactive']==1){
                        return array('result'=>"deactive"
                        ,"data"=>array('marketer_user_id'=>$usermarketer_tb['marketer_user_id'],'marketer_timestamp'=>$usermarketer_tb['marketer_timestamp']
                            ,'marketer_reason'=>$usermarketer_tb['marketer_reason'])
                        ,'desc'=>'شما برای بازاریابی درخواست  داده اید و بدلیل بالا غیر فعال شده اید برای رفع مشکل پیگیری نمایید');
                    }
                    else if($usermarketer_tb['marketer_reject']==0&&$usermarketer_tb['marketer_request']==0&&$usermarketer_tb['marketer_deactive']==0){

//******************************************************************

                        $result2 = $CI->B_db->get_image($usermarketer_tb['marketer_image_code']);
                        $marketer_image = $result2[0];

                        $marketer_image_url=$marketer_image['image_url'];
                        $marketer_image_tumb_url=$marketer_image['image_tumb_url'];

                        $query3=" SELECT * FROM user_tb WHERE user_mobile='".$usermarketer_tb['marketer_leader_mobile']."'";
                        $result3 = $CI->B_db->run_query($query3);
                        $leader=$result3[0];

                        $marketer_leader_name=$leader['user_name'].' '.$leader['user_family'];

                        //*******************************************************************

                        return array('result'=>"ok"
                        ,"data"=>array('marketer_user_id'=>$usermarketer_tb['marketer_user_id']
                            ,'marketer_image_url'=>$marketer_image_url
                            ,'marketer_image_tumb_url'=>$marketer_image_tumb_url
                            ,'marketer_timestamp'=>$usermarketer_tb['marketer_timestamp']
                            ,'marketer_leader_mobile'=>$usermarketer_tb['marketer_leader_mobile']
                            ,'marketer_leader_name'=>$marketer_leader_name
                            ,'marketer_mode_id'=>$usermarketer_tb['marketer_mode_id']
                            ,'marketer_mode_name'=>$usermarketer_tb['marketer_mode_name']
                            ,'marketer_mode_namefa'=>$usermarketer_tb['marketer_mode_namefa']
                            ,'marketer_mode_logourl'=>IMGADD.$usermarketer_tb['marketer_mode_logourl']
                            ,'marketer_mode_color'=>$usermarketer_tb['marketer_mode_color'])
                        ,'desc'=>'درخواست شما مورد تایید است');

                    }
                }
            }else
            {
                return array('result'=>"error"
                ,"data"=>$user_token['user_token_user_id']
                ,'desc'=>'شما از سیستم خارج شده اید . مجددا وارد شوید');
            }
        }else{
            return array('result'=>"error"
            ,"data"=>""
            ,'desc'=>'توکن در سیستم موجود نیست');
        }
    }
}

if ( ! function_exists('get_request')){

    function get_request($request_id)
    {
        $CI = get_instance();
        $CI->load->model('B_db');
        $query="select * from request_tb,company_tb,request_state,fieldinsurance_tb,user_tb where user_id=request_user_id AND fieldinsurance=request_fieldinsurance AND request_state_id=request_last_state_id AND company_id=request_company_id AND request_id=".$request_id;
        $result = $CI->B_db->run_query($query);
        $output =array();
        foreach($result as $row)
        {
            $record=array();
            $record['request_id']=$row['request_id'];
            $request_id=$row['request_id'];
            $record['request_company_id']=$row['request_company_id'];
            $record['company_name']=$row['company_name'];
            $record['company_logo_url']=IMGADD.$row['company_logo_url'];
            //**************************************************************************************
            if($row['request_organ']=='1'){
                $query200="select * from organ_request_tb,organ_contract_tb,organ_tb where organ_contract_organ_id=organ_id  AND organ_contract_id=organ_request_contract_id AND organ_request_request_id=".$request_id;
                $result200 = $CI->B_db->run_query($query200);
                if(!empty($result200)) {
                    $organ = $result200[0];
                    $record['organ_id']=$organ['organ_id'];
                    $record['organ_name']=$organ['organ_name'];
                    $record['organ_contract_num']=$organ['organ_contract_num'];
                    $record['organ_request_confirm_admin_id']=$organ['organ_request_confirm_admin_id'];
                    $record['organ_request_confirm_admin_date']=$organ['organ_request_confirm_admin_date'];

                    $result1 = $CI->B_db->get_image($organ['organ_logo']);
                    $imageurl = "";
                    if (!empty($result1)) {
                        $image = $result1[0];
                        if ($image['image_url']) {
                            $imageurl =  $image['image_url'];
                        }
                    }
                    $record['organ_url']=$imageurl;
                }
            }
            //**************************************************************************************

            if($row['request_agent_id']!=null || $row['request_agent_id']!=""){
                $query2="select * from agent_tb,state_tb,city_tb where state_id=agent_state_id AND city_id=agent_city_id AND agent_id=".$row['request_agent_id'];
                $result2 = $CI->B_db->run_query($query2);
                $agent=$result2[0];
                $record['agent_id']=$agent['agent_id'];
                $record['agent_code']=$agent['agent_code'];
                $record['agent_name']=$agent['agent_name'];
                $record['agent_family']=$agent['agent_family'];
                $record['agent_gender']=$agent['agent_gender'];
                $record['agent_mobile']=$agent['agent_mobile'];
                $record['agent_tell']=$agent['agent_tell'];
                $record['agent_email']=$agent['agent_email'];
                $record['agent_required_phone']=$agent['agent_required_phone'];
                $record['agent_address']=$agent['agent_address'];
                $record['agent_state_id']=$agent['agent_state_id'];
                $record['agent_city_id']=$agent['agent_city_id'];
                $record['agent_state_name']=$agent['state_name'];
                $record['agent_city_name']=$agent['city_name'];
                $record['agent_sector_name']=$agent['agent_sector_name'];
                $record['agent_long']=$agent['agent_long'];
                $record['agent_lat']=$agent['agent_lat'];
                $record['agent_banknum']=$agent['agent_banknum'];
                $record['agent_bankname']=$agent['agent_bankname'];
                $record['agent_banksheba']=$agent['agent_banksheba'];
                $record['agent_image_code']=$agent['agent_image_code'];

                $result112 = $CI->B_db->get_image($agent['agent_image_code']);
                $image = $result112[0];


                $record['agent_image']=$image['image_url'];
                $record['agent_image_tumb']=$image['image_tumb_url'];
                $record['agent_deactive']=$agent['agent_deactive'];
                $record['agent_register_date']=$agent['agent_register_date'];
                $query111="select * from agent_status_tb where agent_status_agent_id=".$agent['agent_id']." ORDER BY agent_status_id DESC LIMIT 1 ";
                $result111=$CI->B_db->run_query($query111);
                if(!empty($result111)){
                    $agent_statuss1=$result111[0];
                    $record['agent_status']=$agent_statuss1['agent_status'];
                }else
                    $record[`agent_status`]=1;
            }
            $record['user_id']=$row['user_id'];
            $record['user_name']=$row['user_name'];
            $record['user_family']=$row['user_family'];
            $record['user_mobile']=$row['user_mobile'];
            $record['fieldinsurance_logo_url']=IMGADD.$row['fieldinsurance_logo_url'];
            $record['fieldinsurance_id']=$row['fieldinsurance_id'];
            $record['request_fieldinsurance_fa']=$row['fieldinsurance_fa'];
            $record['request_description']=$row['request_description'];
            $record['request_last_state_id']=$row['request_last_state_id'];
            $record['request_last_state_name']=$row['request_state_name'];
            $query0=" SELECT * FROM user_address_tb,state_tb,city_tb WHERE user_address_state_id=state_id AND user_address_city_id=city_id AND user_address_id=".$row['request_adderss_id'];
            $result0 = $CI->B_db->run_query($query0);
            $output0 =array();
            foreach($result0 as $row0)
            {
                $record0['user_address_state']=$row0['state_name'];
                $record0['user_address_city']=$row0['city_name'];
                $record0['user_address_state_id']=$row0['state_id'];
                $record0['user_address_city_id']=$row0['city_id'];
                $record0['user_address_str']=$row0['user_address_str'];
                $record0['user_address_code']=$row0['user_address_code'];
                $record0['user_address_name']=$row0['user_address_name'];
                $record0['user_address_mobile']=$row0['user_address_mobile'];
                $record0['user_address_tell']=$row0['user_address_tell'];
                $output0[]=$record0;
            }
            $record['request_adderss']=$output0;
            $query01=" SELECT * FROM user_address_tb,state_tb,city_tb WHERE user_address_state_id=state_id AND user_address_city_id=city_id AND user_address_id=".$row['request_addressofinsured_id'];
            $result01 = $CI->B_db->run_query($query01);
            $output01 =array();
            foreach($result01 as $row01)
            {
                $record01['user_address_state_id']=$row01['state_id'];
                //$record01['user_address_city_id']=$row01['city_name_id'];
                $record01['user_address_state']=$row01['state_name'];
                $record01['user_address_city']=$row01['city_name'];
                $record01['user_address_str']=$row01['user_address_str'];
                $record01['user_address_code']=$row01['user_address_code'];
                $record01['user_address_name']=$row01['user_address_name'];
                $record01['user_address_mobile']=$row01['user_address_mobile'];
                $record01['user_address_tell']=$row01['user_address_tell'];
                $output01[]=$record01;
            }
            $record['request_addressofinsured']=$output01;
            $query0="select sum(user_pey_amount) AS overpayment from user_pey_tb where user_pey_mode='overpayment' AND user_pey_request_id=".$request_id." ORDER BY user_pey_request_id";
            $result0 = $CI->B_db->run_query($query0);
            $user_pey0=$result0[0];
            $overpayment=$user_pey0['overpayment'];
            $query1="select sum(user_pey_amount) AS sumpey from user_pey_tb where not (user_pey_mode='overpayment') AND user_pey_request_id=".$request_id." ORDER BY user_pey_request_id";
            $result1 = $CI->B_db->run_query($query1);
            $user_pey=$result1[0];
            $record['user_pey_amount']=$user_pey['sumpey']-$overpayment;
            $query2="select sum(user_pey_amount) AS sumcash from user_pey_tb where not (user_pey_mode = 'instalment')  AND  not (user_pey_mode='overpayment') AND user_pey_request_id=".$request_id;
            $result2 = $CI->B_db->run_query($query2);
            $user_pey2=$result2[0];
            $record['user_pey_cash']=$user_pey2['sumcash']-$overpayment;
            $query20="select sum(user_pey_amount) AS suminstalment from user_pey_tb where user_pey_mode = 'instalment' AND user_pey_request_id=".$request_id;
            $result20 = $CI->B_db->run_query($query20);
            $user_pey20=$result20[0];
            $record['user_pey_instalment']=$user_pey20['suminstalment'];
            $query1="select * from user_pey_tb,instalment_check_tb where user_pey_mode='instalment' AND instalment_check_user_pey_id=user_pey_id AND user_pey_request_id=".$request_id;
            $result1 = $CI->B_db->run_query($query1);
            $output1 =array();
            foreach($result1 as $row1)
            {
                $record1['user_pey_id']=$row1['user_pey_id'];
                $record1['user_pey_amount']=$row1['user_pey_amount'];
                $record1['instalment_check_num']=$row1['instalment_check_num'];
                $record1['instalment_check_date']=$row1['instalment_check_date'];
                $record1['user_pey_desc']=$row1['user_pey_desc'];
                $record1['user_pey_image_code']=$row1['user_pey_image_code'];

                $result11 = $CI->B_db->get_image($row1['user_pey_image_code']);
                $image = $result11[0];

                if($image['image_tumb_url']==null){ $record1['user_pey_image_turl']=null;}else{ $record1['user_pey_image_turl']=$image['image_tumb_url'];}
                if($image['image_url']==null){ $record1['user_pey_image_url']=null;}else{$record1['user_pey_image_url']=$image['image_url'];}
                $output1[]=$record1;
            }
            $record['user_pey_detail']=$output1;
            $query121="select * from user_pey_tb where not(user_pey_mode='instalment') AND user_pey_request_id=".$request_id;
            $result121 = $CI->B_db->run_query($query121);
            $output121 =array();
            foreach($result121 as $row121)
            {
                $record121['user_pey_amount']=$row121['user_pey_amount'];
                if($row121['user_pey_mode']=='cash'){
                    $record121['user_pey_desc']='پرداخت ار درگاه بانکی';
                    $record121['user_pey_timestamp']=$row121['user_pey_timestamp'];

                }else{
                    $record121['user_pey_desc']=$row121['user_pey_desc'];
                    $record121['user_pey_timestamp']=$row121['user_pey_timestamp'];
                }
                $output121[]=$record121;
            }
            $record['user_pey_detail2']=$output121;
            //***************************************************************************************************************
            $query17="select * from state_request_tb where staterequest_request_id=".$request_id." ORDER BY staterequest_id DESC LIMIT 1 ";
            $result17=$CI->B_db->run_query($query17);
            $state_request17=$result17[0];
            $record['staterequest_last_timestamp']=$state_request17['staterequest_timestamp'];
            //***************************************************************************************************************
            $query7="select * from state_request_tb,request_state where request_state_id=staterequest_state_id AND staterequest_request_id=".$request_id;
            $result7 = $CI->B_db->run_query($query7);
            $output7 =array();
            foreach($result7 as $row7)
            {
                $record7['staterequest_id']=$row7['staterequest_id'];
                $record7['request_state_name']=$row7['request_state_name'];
                $record7['staterequest_timestamp']=$row7['staterequest_timestamp'];
                $record7['staterequest_desc']=$row7['staterequest_desc'];
                if($row7['staterequest_agent_id']){
                    $query71=" SELECT * FROM agent_tb WHERE agent_id =".$row7['staterequest_agent_id'];
                    $result71=$CI->B_db->run_query($query71);
                    $agent=$result71[0];
                    if($agent['agent_code']==null){ $record7['agent_code']=null;}else{ $record7['agent_code']=$agent['agent_code'];}
                    if($agent['agent_name']==null){ $record7['agent_name']=null;}else{$record7['agent_name']=$agent['agent_name'];}
                    if($agent['agent_family']==null){ $record7['agent_family']=null;}else{$record7['agent_family']=$agent['agent_family'];}
                }
                if ($row7['staterequest_employee_id']&&$row7['staterequest_employee_id']!=0) {
                    $query71 = " SELECT * FROM employee_tb WHERE employee_id =" . $row7['staterequest_employee_id'];
                    $result71 = $CI->B_db->run_query($query71);
                    $employee = $result71[0];

                    if ($employee['employee_name'] == null) {
                        $record7['employee_name'] = null;
                    } else {
                        $record7['employee_name'] = $employee['employee_name'];
                    }
                    if ($employee['employee_family'] == null) {
                        $record7['employee_family'] = null;
                    } else {
                        $record7['employee_family'] = $employee['employee_family'];
                    }
                }
                $output7[]=$record7;
            }
            $record['request_stats']=$output7;
            $query6=" SELECT * FROM requst_ready_tb,request_ready_clearing_mode_tb WHERE request_ready_clearing_id=request_ready_clearing_mode_id AND  requst_ready_request_id=".$request_id;
            $result6 = $CI->B_db->run_query($query6);
            $output6 =array();
            foreach($result6 as $row6)
            {
                $record6['requst_ready_start_date'] = $row6['requst_ready_start_date'];
                $record6['requst_ready_end_date'] = $row6['requst_ready_end_date'];
                $record6['requst_ready_end_price'] = $row6['requst_ready_end_price'];
                $record6['requst_ready_num_ins'] = $row6['requst_ready_num_ins'];
                $record6['requst_ready_code_yekta'] = $row6['requst_ready_code_yekta'];
                $record6['requst_ready_code_rayane'] = $row6['requst_ready_code_rayane'];
                $record6['requst_ready_name_insurer'] = $row6['requst_ready_name_insurer'];
                $record6['requst_ready_code_insurer'] = $row6['requst_ready_code_insurer'];
                $record6['requst_ready_code_penalty'] = $row6['requst_ready_code_penalty'];
                $record6['request_ready_clearing_mode_name']=$row6['request_ready_clearing_mode_name'];
                $record6['request_ready_clearing_id']=$row6['request_ready_clearing_id'];
                $record6['requst_ready_employee_id']=$row6['requst_ready_employee_id'];
                //*************************************************************************************************************
                $query61=" SELECT * FROM requst_ready_image_tb,image_tb WHERE image_code=requst_ready_image_code AND requst_ready_request_id=".$request_id;
                $result61 = $CI->B_db->run_query($query61);
                $output61 =array();
                foreach($result61 as $row61)
                {
                    $result11 = $CI->B_db->get_image($row61['requst_ready_image_code']);
                    $image = $result11[0];

                    $record61['image_url']=$image['image_url'];
                    $record61['image_tumb_url']=$image['image_tumb_url'];
                    $record61['image_name']=$row61['image_name'];
                    $record61['image_desc']=$row61['image_desc'];
                    $output61[]=$record61;
                }
                $record6['request_ready_image_tb']=$output61;
                //*************************************************************************************************************
                $query62=" SELECT * FROM request_file_tb WHERE request_file_request_id=".$request_id;
                $result62 = $CI->B_db->run_query($query62);
                $output62 =array();
                foreach($result62 as $row62)
                {
                    $record62['request_file_url']=IMGADD.$row62['request_file_url'];
                    $record62['request_file_desc']=$row62['request_file_desc'];
                    $output62[]=$record62;
                }
                $record6['request_ready_file_tb']=$output62;
                //*************************************************************************************************************
                $output6[]=$record6;
            }
            $record['request_ready']=$output6;
            //***************************************************************************************************************
            $query5=" SELECT * FROM request_delivered_tb,state_tb,city_tb,delivery_mode_tb WHERE delivery_mode_id=request_delivered_mode_id AND state_id=request_delivered_state_id AND city_id=request_delivered_city_id AND request_delivered_request_id=".$row['request_id'];
            $result5 = $CI->B_db->run_query($query5);
            $output5 =array();
            foreach($result5 as $row5)
            {
                $record5['request_delivered_timesatmp']=$row5['request_delivered_timesatmp'];
                $record5['request_delivered_mode']=$row5['delivery_mode_name'];
                //$record5['request_delivered_mode_id']=$row5['delivery_mode_name_id'];
                $record5['request_delivered_dsc']=$row5['request_delivered_dsc'];
                $record5['request_delivered_state']=$row5['state_name'];
                $record5['request_delivered_city']=$row5['city_name'];
                $record5['request_delivered_state_id']=$row5['state_id'];
                $record5['request_delivered_city_id']=$row5['city_id'];

                $result51 = $CI->B_db->get_image($row5['request_delivered_receipt_image_code']);

                if(!empty($result51)){
                    $image=$result51[0];
                    if($image['image_tumb_url']==null){ $record5['user_pey_image_turl']=null;}else{ $record5['user_pey_image_turl']=$image['image_tumb_url'];}
                    if($image['image_url']==null){ $record5['user_pey_image_url']=null;}else{$record5['user_pey_image_url']=$image['image_url'];}
                }
                $output5[]=$record5;
            }
            $record['request_delivered']=$output5;
            //***************************************************************************************************************
            $query4=" SELECT * FROM request_img_tb,image_tb WHERE image_id=request_img_image_code AND request_img_request_id=".$request_id;
            $result4 = $CI->B_db->run_query($query4);
            $output4 =array();
            foreach($result4 as $row4)
            {
                $result51 = $CI->B_db->get_image($row4['image_code']);
                $image=$result51[0];

                $record4['image_url']=$image['image_url'];
                $record4['image_tumb_url']=$image['image_tumb_url'];
                $record4['image_name']=$row4['image_name'];
                $record4['image_desc']=$row4['image_desc'];
                $output4[]=$record4;
            }
            $record['request_image']=$output4;
            //***************************************************************************************************************
            //***************************************************************************************
            $query44=" SELECT * FROM request_visit_tb,user_tb WHERE request_visit_user_id=user_id AND  request_visit_request_id=".$request_id;
            $result44 = $CI->B_db->run_query($query44);
            $output44 =array();
            foreach($result44 as $row44)
            {
                $record44['request_visit_vedio_url']=IMGADD.$row44['request_visit_vedio_url'];
                $record44['request_visit_id']=$row44['request_visit_id'];
                $record44['request_visit_user_id']=$row44['request_visit_user_id'];
                $record44['user_name']=$row44['user_name'];
                $record44['user_family']=$row44['user_family'];
                $record44['user_mobile']=$row44['user_mobile'];
                //**********************************************************
                $query40=" SELECT * FROM request_visit_image_tb,image_tb  WHERE  request_visit_image_code=image_code AND request_visit_image_visit_id=".$row44['request_visit_id'];

                $result40 = $CI->B_db->run_query($query40);
                $output40 =array();
                foreach($result40 as $row40)
                {
                    $result51 = $CI->B_db->get_image($row40['image_code']);
                    $image=$result51[0];

                    $record40['image_url']=$image['image_url'];
                    $record40['image_tumb_url']=$image['image_tumb_url'];
                    $record40['image_name']=$row40['image_name'];
                    $record40['image_desc']=$row40['image_desc'];
                    $output40[]=$record40;

                }
                $record44['images_visit']=$output40;

                //**********************************************************
                $output44[]=$record44;
            }
            $record['visit_image']=$output44;
            //***************************************************************************************************************

            //***************************************************************************************************************

            $output[]=$record;
        }
        return  $output;
    }
}

if ( ! function_exists('get_damagefile_expert')){

    function get_damagefile_expert($damagefile_id)
    {
        $CI = get_instance();
        $CI->load->model('B_db');
        $query = "select * from damagefile_tb,evaluatorco_tb,damagefile_state,fielddamagefile_tb,user_tb where user_id=damagefile_user_id AND fielddamagefile_id=damagefile_fielddamagefile_id AND damagefile_state.damagefile_state_id=damagefile_last_state_id AND evaluatorco_id=damagefile_evaluatorco_id AND damagefile_id=" . $damagefile_id;
        $result = $CI->B_db->run_query($query);
        $output =array();
        foreach ($result as $row) {
            $record = array();
            $record['damagefile_id'] = $row['damagefile_id'];
            $damagefile_id = $row['damagefile_id'];
            $record['user_id'] = $row['user_id'];
            $record['user_name'] = $row['user_name'];
            $record['user_family'] = $row['user_family'];
            $record['user_mobile'] = $row['user_mobile'];
            $record['fielddamagefile_logo_url'] = IMGADD . $row['fielddamagefile_logo_url'];
            $record['fielddamagefile_id'] = $row['fielddamagefile_id'];
            $record['damagefile_fielddamagefile_fa'] = $row['fielddamagefile_fa'];
            $record['damagefile_description'] = $row['damagefile_description'];
            $record['damagefile_price_user'] = $row['damagefile_price_user'];
            $record['damagefile_last_state_id'] = $row['damagefile_last_state_id'];
            $record['damagefile_last_state_name'] = $row['damagefile_state_name'];
            //**************************************************************************************
            //**************************************************************************************

            $query201="SELECT * FROM organ_user_therapy_tb,user_therapy_bank_tb,user_gender_tb,user_therapy_kind_tb,user_therapy_kindship_tb,
user_therapy_baseinsurer_tb
WHERE organ_user_therapy_bank_id=user_therapy_bank_id
AND organ_user_therapy_gender_id=user_gender_id
AND organ_user_therapy_kind_id=user_therapy_kind_id
AND user_therapy_kindship_id=organ_user_therapy_kinship_id
AND organ_user_therapy_basebime_id=user_therapy_baseinsurer_id
AND organ_user_therapy_id=".$row['damagefile_user_therapy_id'];
            $result201 = $CI->B_db->run_query($query201);
            if(!empty($result201)) {
                $record['organ_user_therapy'] = $result201[0];
            }
            //**************************************************************************************

            $record['damagefile_therapycontract_id']=$row['damagefile_therapycontract_id'];
            $query200="select * from organ_therapycontract_tb,organ_tb where  organ_id=organ_therapycontract_organ_id AND organ_therapycontract_id=".$row['damagefile_therapycontract_id'];
            $result200 = $CI->B_db->run_query($query200);
            if(!empty($result200)) {
                $organ = $result200[0];
                $record['organ_id']=$organ['organ_id'];
                $record['organ_name']=$organ['organ_name'];
                $record['organ_therapycontract_num']=$organ['organ_therapycontract_num'];

                $result1 = $CI->B_db->get_image($organ['organ_logo']);
                $imageurl = "";
                if (!empty($result1)) {
                    $image = $result1[0];
                    if ($image['image_url']) {
                        $imageurl =  $image['image_url'];
                    }
                }
                $record['organ_url']=$imageurl;
            }

            //**************************************************************************************

            //*************************************************************************************************************

            //*************************************************************************************************************
            //*************************************************************************************************************





            //***************************************************************************************************************
            $query17 = "select * from state_damagefile_tb where statedamagefile_damagefile_id=" . $damagefile_id . " ORDER BY statedamagefile_id DESC LIMIT 1 ";
            $result17 = $CI->B_db->run_query($query17);
            $state_damagefile17 = $result17[0];
            $record['statedamagefile_last_timestamp'] = $state_damagefile17['statedamagefile_timestamp'];

            //***************************************************************************************************************
            $query7 = "select * from state_damagefile_tb,damagefile_state where damagefile_state_id=statedamagefile_state_id AND statedamagefile_damagefile_id=" . $damagefile_id;
            $result7 = $CI->B_db->run_query($query7);
            $output7 = array();
            foreach ($result7 as $row7) {

                $record7['statedamagefile_id'] = $row7['statedamagefile_id'];
                //  $record7['statedamagefile_state_id']=$row7['statedamagefile_state_id'];
                $record7['damagefile_state_name'] = $row7['damagefile_state_name'];
                $record7['statedamagefile_timestamp'] = $row7['statedamagefile_timestamp'];
                $record7['statedamagefile_desc'] = $row7['statedamagefile_desc'];
                // $record7['statedamagefile_expert_id']=$row7['statedamagefile_expert_id'];

                if ($row7['statedamagefile_expert_id']) {
                    $query71 = " SELECT * FROM expert_tb WHERE expert_id =" . $row7['statedamagefile_expert_id'];
                    $result71 = $CI->B_db->run_query($query71);
                    $expert = $result71[0];
                    if ($expert['expert_code'] == null) {
                        $record7['expert_code'] = null;
                    } else {
                        $record7['expert_code'] = $expert['expert_code'];
                    }
                    if ($expert['expert_name'] == null) {
                        $record7['expert_name'] = null;
                    } else {
                        $record7['expert_name'] = $expert['expert_name'];
                    }
                    if ($expert['expert_family'] == null) {
                        $record7['expert_family'] = null;
                    } else {
                        $record7['expert_family'] = $expert['expert_family'];
                    }
                }

                if ($row7['statedamagefile_employee_id']&&$row7['statedamagefile_employee_id']!=0) {
                    $query71 = " SELECT * FROM employee_tb WHERE employee_id =" . $row7['statedamagefile_employee_id'];
                    $result71 = $CI->B_db->run_query($query71);
                    $expert = $result71[0];

                    if ($expert['employee_name'] == null) {
                        $record7['employee_name'] = null;
                    } else {
                        $record7['employee_name'] = $expert['employee_name'];
                    }
                    if ($expert['employee_family'] == null) {
                        $record7['employee_family'] = null;
                    } else {
                        $record7['employee_family'] = $expert['employee_family'];
                    }
                }else{
                    $record7['employee_name'] = null;
                    $record7['employee_family'] = null;
                }

                $output7[] = $record7;
            }
            $record['damagefile_stats'] = $output7;

            //***************************************************************************************************************
            //***************************************************************************************************************
            $query6=" SELECT * FROM damagefile_ready_tb,damagefile_ready_clearing_mode_tb WHERE damagefile_ready_clearing_id=damagefile_ready_clearing_mode_id AND  damagefile_ready_damagefile_id=".$damagefile_id;
            $result6 = $CI->B_db->run_query($query6);
            $output6 = array();
            foreach ($result6 as $row6) {

                $record6['damagefile_ready_expert_date'] = $row6['damagefile_ready_expert_date'];
                $record6['damagefile_ready_pay_date'] = $row6['damagefile_ready_pay_date'];
                $record6['damagefile_ready_expert_estimate'] = $row6['damagefile_ready_expert_estimate'];
                $record6['damagefile_ready_tracking_code'] = $row6['damagefile_ready_tracking_code'];
                $record6['damagefile_ready_code_yekta'] = $row6['damagefile_ready_code_yekta'];
                $record6['damagefile_ready_code_rayane'] = $row6['damagefile_ready_code_rayane'];
                $record6['damagefile_ready_name_insurer'] = $row6['damagefile_ready_name_insurer'];
                $record6['damagefile_ready_code_insurer'] = $row6['damagefile_ready_code_insurer'];
                $record6['damagefile_ready_code_penalty'] = $row6['damagefile_ready_code_penalty'];
                $record6['damagefile_ready_clearing_mode_name']=$row6['damagefile_ready_clearing_mode_name'];
                $record6['damagefile_ready_clearing_id']=$row6['damagefile_ready_clearing_id'];
                $record6['damagefile_suspend_desc'] = $row6['damagefile_suspend_desc'];

                //*************************************************************************************************************
                $query61 = " SELECT * FROM damagefile_ready_image_tb,image_tb WHERE image_code=damagefile_ready_image_code AND damagefile_ready_damagefile_id=" . $damagefile_id;
                $result61 = $CI->B_db->run_query($query61);
                $output61 = array();
                foreach ($result61 as $row61) {

                    $result51 = $CI->B_db->get_image($row61['image_code']);
                    $image=$result51[0];

                    $record61['image_url'] =  $image['image_url'];
                    $record61['image_tumb_url'] =  $image['image_tumb_url'];
                    $record61['image_name'] = $row61['image_name'];
                    $record61['image_desc'] = $row61['image_desc'];
                    $output61[] = $record61;
                }
                $record6['damagefile_ready_image_tb'] = $output61;

                //*************************************************************************************************************
                $query62 = " SELECT * FROM damagefile_file_tb WHERE damagefile_file_damagefile_id=" . $damagefile_id;
                $result62 = $CI->B_db->run_query($query62);
                $output62 = array();
                foreach ($result62 as $row62) {

                    $record62['damagefile_file_url'] = IMGADD . $row62['damagefile_file_url'];
                    $record62['damagefile_file_desc'] = $row62['damagefile_file_desc'];
                    $output62[] = $record62;
                }
                $record6['damagefile_ready_file_tb'] = $output62;

                //*************************************************************************************************************    $output6[]=$record6;
                $output6[] = $record6;
            }
            $record['damagefile_ready'] = $output6;

            //***************************************************************************************************************

            //***************************************************************************************************************
            $query5 = " SELECT * FROM damagefile_delivered_tb,state_tb,city_tb,delivery_mode_tb WHERE delivery_mode_id=damagefile_delivered_mode_id AND state_id=damagefile_delivered_state_id AND city_id=damagefile_delivered_city_id AND damagefile_delivered_damagefile_id=" . $row['damagefile_id'];
            $result5 = $CI->B_db->run_query($query5);
            $output5 = array();
            foreach ($result5 as $row5) {

                $record5['damagefile_delivered_timesatmp'] = $row5['damagefile_delivered_timesatmp'];
                $record5['damagefile_delivered_mode'] = $row5['delivery_mode_name'];
                $record5['damagefile_delivered_dsc'] = $row5['damagefile_delivered_dsc'];
                $record5['damagefile_delivered_state'] = $row5['state_name'];
                $record5['damagefile_delivered_city'] = $row5['city_name'];

                $result51 = $CI->B_db->get_image($row5['damagefile_delivered_receipt_image_code']);
                $image = $result51[0];

                if ($image['image_tumb_url'] == null) {
                    $record5['user_pey_image_turl'] = null;
                } else {
                    $record5['user_pey_image_turl'] =  $image['image_tumb_url'];
                }
                if ($image['image_url'] == null) {
                    $record5['user_pey_image_url'] = null;
                } else {
                    $record5['user_pey_image_url'] =  $image['image_url'];
                }

                $output5[] = $record5;
            }
            $record['damagefile_delivered'] = $output5;

            //***************************************************************************************************************

            $query4 = " SELECT * FROM damagefile_img_tb,image_tb WHERE image_id=damagefile_img_image_code AND damagefile_img_damagefile_id=" . $damagefile_id;
            $result4 = $CI->B_db->run_query($query4);
            $output4 = array();
            foreach ($result4 as $row4) {
                $result51 = $CI->B_db->get_image($row4['image_code']);
                $image=$result51[0];

                $record4['image_url'] =  $image['image_url'];
                $record4['image_tumb_url'] =  $image['image_tumb_url'];
                $record4['image_name'] = $row4['image_name'];
                $record4['image_desc'] = $row4['image_desc'];
                $output4[] = $record4;
            }
            $record['damagefile_image'] = $output4;




            //***************************************************************************************************************


            $output[] = $record;

        }
        return  $output;
    }
}
if ( ! function_exists('get_sanad_qroup')){

    function get_sanad_qroup()
    {
        $CI = get_instance();
        $CI->load->model('B_db');
        $query = "SELECT  MAX(sanad_qroup)AS id FROM migrate1_sql";
        $result = $CI->B_db->run_query($query)[0];
        return  $result['id'];
    }
}


if ( ! function_exists('get_request_agent')){

    function get_request_agent($request_id)
    {
        $CI = get_instance();
        $CI->load->model('B_db');
        $query = "select * from request_tb,company_tb,request_state,fieldinsurance_tb,user_tb where user_id=request_user_id AND fieldinsurance=request_fieldinsurance AND request_state_id=request_last_state_id AND company_id=request_company_id AND request_id=" . $request_id;
        $result = $CI->B_db->run_query($query);
        $output =array();
        foreach($result as $row)
        {
            $record=array();
            $record['request_id']=$row['request_id'];
            $request_id=$row['request_id'];
            $record['request_company_id']=$row['request_company_id'];
            $record['company_name']=$row['company_name'];
            $record['company_logo_url']=IMGADD.$row['company_logo_url'];
            //**************************************************************************************
            if($row['request_organ']=='1'){
                $query200="select * from organ_request_tb,organ_contract_tb,organ_tb where organ_contract_organ_id=organ_id  AND organ_contract_id=organ_request_contract_id AND organ_request_request_id=".$request_id;
                $result200 = $CI->B_db->run_query($query200);
                if(!empty($result200)) {
                    $organ = $result200[0];
                    $record['organ_id']=$organ['organ_id'];
                    $record['organ_name']=$organ['organ_name'];
                    $record['organ_contract_num']=$organ['organ_contract_num'];
                    $record['organ_request_confirm_admin_id']=$organ['organ_request_confirm_admin_id'];
                    $record['organ_request_confirm_admin_date']=$organ['organ_request_confirm_admin_date'];

                    $result1 = $CI->B_db->get_image($organ['organ_logo']);
                    $imageurl = "";
                    if (!empty($result1)) {
                        $image = $result1[0];
                        if ($image['image_url']) {
                            $imageurl =  $image['image_url'];
                        }
                    }
                    $record['organ_url']=$imageurl;
                }
            }
            //**************************************************************************************

            if($row['request_agent_id']!=null || $row['request_agent_id']!=""){
                $query2="select * from agent_tb,state_tb,city_tb where state_id=agent_state_id AND city_id=agent_city_id AND agent_id=".$row['request_agent_id'];
                $result2 = $CI->B_db->run_query($query2);
                $agent=$result2[0];
                $record['agent_id']=$agent['agent_id'];
                $record['agent_code']=$agent['agent_code'];
                $record['agent_name']=$agent['agent_name'];
                $record['agent_family']=$agent['agent_family'];
                $record['agent_gender']=$agent['agent_gender'];
                $record['agent_mobile']=$agent['agent_mobile'];
                $record['agent_tell']=$agent['agent_tell'];
                $record['agent_email']=$agent['agent_email'];
                $record['agent_required_phone']=$agent['agent_required_phone'];
                $record['agent_address']=$agent['agent_address'];
                $record['agent_state_id']=$agent['agent_state_id'];
                $record['agent_city_id']=$agent['agent_city_id'];
                $record['agent_state_name']=$agent['state_name'];
                $record['agent_city_name']=$agent['city_name'];
                $record['agent_sector_name']=$agent['agent_sector_name'];
                $record['agent_long']=$agent['agent_long'];
                $record['agent_lat']=$agent['agent_lat'];
                $record['agent_banknum']=$agent['agent_banknum'];
                $record['agent_bankname']=$agent['agent_bankname'];
                $record['agent_banksheba']=$agent['agent_banksheba'];
                $record['agent_image_code']=$agent['agent_image_code'];

                $result112 = $CI->B_db->get_image($agent['agent_image_code']);
                $image=$result112[0];

                $record['agent_image']=$image['image_url'];
                $record['agent_image_tumb']=$image['image_tumb_url'];
                $record['agent_deactive']=$agent['agent_deactive'];
                $record['agent_register_date']=$agent['agent_register_date'];
                $query111="select * from agent_status_tb where agent_status_agent_id=".$agent['agent_id']." ORDER BY agent_status_id DESC LIMIT 1 ";
                $result111=$CI->B_db->run_query($query111);
                if(!empty($result111)){
                    $agent_statuss1=$result111[0];
                    $record['agent_status']=$agent_statuss1['agent_status'];
                }else
                    $record[`agent_status`]=1;
            }
            $record['user_id']=$row['user_id'];
            $record['user_name']=$row['user_name'];
            $record['user_family']=$row['user_family'];
            $record['user_mobile']=$row['user_mobile'];
            $record['fieldinsurance_logo_url']=IMGADD.$row['fieldinsurance_logo_url'];
            $record['fieldinsurance_id']=$row['fieldinsurance_id'];
            $record['request_fieldinsurance_fa']=$row['fieldinsurance_fa'];
            $record['request_description']=$row['request_description'];
            $record['request_last_state_id']=$row['request_last_state_id'];
            $record['request_last_state_name']=$row['request_state_name'];
            $query0=" SELECT * FROM user_address_tb,state_tb,city_tb WHERE user_address_state_id=state_id AND user_address_city_id=city_id AND user_address_id=".$row['request_adderss_id'];
            $result0 = $CI->B_db->run_query($query0);
            $output0 =array();
            foreach($result0 as $row0)
            {
                $record0['user_address_state']=$row0['state_name'];
                $record0['user_address_city']=$row0['city_name'];
                $record0['user_address_state_id']=$row0['state_id'];
                $record0['user_address_city_id']=$row0['city_id'];
                $record0['user_address_str']=$row0['user_address_str'];
                $record0['user_address_code']=$row0['user_address_code'];
                $record0['user_address_name']=$row0['user_address_name'];
                $record0['user_address_mobile']=$row0['user_address_mobile'];
                $record0['user_address_tell']=$row0['user_address_tell'];
                $output0[]=$record0;
            }
            $record['request_adderss']=$output0;
            $query01=" SELECT * FROM user_address_tb,state_tb,city_tb WHERE user_address_state_id=state_id AND user_address_city_id=city_id AND user_address_id=".$row['request_addressofinsured_id'];
            $result01 = $CI->B_db->run_query($query01);
            $output01 =array();
            foreach($result01 as $row01)
            {
                $record01['user_address_state_id']=$row01['state_id'];
                //$record01['user_address_city_id']=$row01['city_name_id'];
                $record01['user_address_state']=$row01['state_name'];
                $record01['user_address_city']=$row01['city_name'];
                $record01['user_address_str']=$row01['user_address_str'];
                $record01['user_address_code']=$row01['user_address_code'];
                $record01['user_address_name']=$row01['user_address_name'];
                $record01['user_address_mobile']=$row01['user_address_mobile'];
                $record01['user_address_tell']=$row01['user_address_tell'];
                $output01[]=$record01;
            }
            $record['request_addressofinsured']=$output01;

            $query0="select sum(user_pey_amount) AS overpayment from user_pey_tb where user_pey_mode='overpayment' AND user_pey_request_id=".$request_id;
            $result0 = $CI->B_db->run_query($query0);
            $user_pey0=$result0[0];
            $overpayment=$user_pey0['overpayment'];

            $query1="select sum(user_pey_amount) AS sumpey from user_pey_tb where not (user_pey_mode='overpayment') AND user_pey_request_id=".$request_id." ORDER BY user_pey_request_id";
            $result1 = $CI->B_db->run_query($query1);
            $user_pey=$result1[0];
            $record['user_pey_amount']=$user_pey['sumpey']-$overpayment;

            $query2="select sum(user_pey_amount) AS sumcash from user_pey_tb where not (user_pey_mode = 'instalment')  AND  not (user_pey_mode='overpayment') AND user_pey_request_id=".$request_id." ORDER BY user_pey_request_id";
            $result2 = $CI->B_db->run_query($query2);
            $user_pey2=$result2[0];
            $record['user_pey_cash']=$user_pey2['sumcash']-$overpayment;

            $query20="select sum(user_pey_amount) AS suminstalment from user_pey_tb where user_pey_mode = 'instalment' AND user_pey_request_id=".$request_id." ORDER BY user_pey_request_id";
            $result20 = $CI->B_db->run_query($query20);
            $user_pey20=$result20[0];
            $record['user_pey_instalment']=$user_pey20['suminstalment'];

            $query1="select * from user_pey_tb,instalment_check_tb where user_pey_mode='instalment' AND instalment_check_user_pey_id=user_pey_id AND user_pey_request_id=".$request_id;
            $result1 = $CI->B_db->run_query($query1);
            $output1 =array();
            foreach($result1 as $row1)
            {
                $record1['user_pey_id']=$row1['user_pey_id'];
                $record1['user_pey_amount']=$row1['user_pey_amount'];
                $record1['instalment_check_num']=$row1['instalment_check_num'];
                $record1['instalment_check_date']=$row1['instalment_check_date'];
                $record1['user_pey_desc']=$row1['user_pey_desc'];
                $record1['user_pey_image_code']=$row1['user_pey_image_code'];

                $result11 = $CI->B_db->get_image($row1['user_pey_image_code']);
                $image=$result11[0];


                if($image['image_tumb_url']==null){ $record1['user_pey_image_turl']=null;}else{ $record1['user_pey_image_turl']=$image['image_tumb_url'];}
                if($image['image_url']==null){ $record1['user_pey_image_url']=null;}else{$record1['user_pey_image_url']=$image['image_url'];}
                $output1[]=$record1;
            }
            $record['user_pey_detail']=$output1;
            //***************************************************************************************************************
            $query17="select * from state_request_tb where staterequest_request_id=".$request_id." ORDER BY staterequest_id DESC LIMIT 1 ";
            $result17=$CI->B_db->run_query($query17);
            $state_request17=$result17[0];
            $record['staterequest_last_timestamp']=$state_request17['staterequest_timestamp'];
            //***************************************************************************************************************

            $query7="select * from state_request_tb,request_state where request_state_id=staterequest_state_id AND staterequest_request_id=".$request_id;
            $result7 = $CI->B_db->run_query($query7);
            $output7 =array();
            foreach($result7 as $row7)
            {
                $record7['staterequest_id']=$row7['staterequest_id'];
                $record7['request_state_name']=$row7['request_state_name'];
                $record7['staterequest_timestamp']=$row7['staterequest_timestamp'];
                $record7['staterequest_desc']=$row7['staterequest_desc'];
                if($row7['staterequest_agent_id']){
                    $query71=" SELECT * FROM agent_tb WHERE agent_id =".$row7['staterequest_agent_id'];
                    $result71=$CI->B_db->run_query($query71);
                    $agent=$result71[0];
                    if($agent['agent_code']==null){ $record7['agent_code']=null;}else{ $record7['agent_code']=$agent['agent_code'];}
                    if($agent['agent_name']==null){ $record7['agent_name']=null;}else{$record7['agent_name']=$agent['agent_name'];}
                    if($agent['agent_family']==null){ $record7['agent_family']=null;}else{$record7['agent_family']=$agent['agent_family'];}
                }
                if ($row7['staterequest_employee_id']&&$row7['staterequest_employee_id']!=0) {
                    $query71 = " SELECT * FROM employee_tb WHERE employee_id =" . $row7['staterequest_employee_id'];
                    $result71 = $CI->B_db->run_query($query71);
                    $employee = $result71[0];

                    if ($employee['employee_name'] == null) {
                        $record7['employee_name'] = null;
                    } else {
                        $record7['employee_name'] = $employee['employee_name'];
                    }
                    if ($employee['employee_family'] == null) {
                        $record7['employee_family'] = null;
                    } else {
                        $record7['employee_family'] = $employee['employee_family'];
                    }
                }
                $output7[]=$record7;
            }
            $record['request_stats']=$output7;

            $query6=" SELECT * FROM requst_ready_tb WHERE  requst_ready_request_id=".$request_id;
            $result6 = $CI->B_db->run_query($query6);
            $output6 =array();
            foreach($result6 as $row6)
            {
                $record6['requst_ready_start_date'] = $row6['requst_ready_start_date'];
                $record6['requst_ready_end_date'] = $row6['requst_ready_end_date'];
                $record6['requst_ready_end_price'] = $row6['requst_ready_end_price'];
                $record6['requst_ready_num_ins'] = $row6['requst_ready_num_ins'];
                $record6['requst_ready_code_yekta'] = $row6['requst_ready_code_yekta'];
                $record6['requst_ready_code_rayane'] = $row6['requst_ready_code_rayane'];
                $record6['requst_ready_name_insurer'] = $row6['requst_ready_name_insurer'];
                $record6['requst_ready_code_insurer'] = $row6['requst_ready_code_insurer'];
                $record6['requst_ready_code_penalty'] = $row6['requst_ready_code_penalty'];
                //*************************************************************************************************************
                $query61=" SELECT * FROM requst_ready_image_tb,image_tb WHERE image_code=requst_ready_image_code AND requst_ready_request_id=".$request_id;
                $result61 = $CI->B_db->run_query($query61);
                $output61 =array();
                foreach($result61 as $row61)
                {
                    $result11 = $CI->B_db->get_image($row61['image_code']);
                    $image=$result11[0];

                    $record61['image_url']=$image['image_url'];
                    $record61['image_tumb_url']=$image['image_tumb_url'];
                    $record61['image_name']=$row61['image_name'];
                    $record61['image_desc']=$row61['image_desc'];
                    $output61[]=$record61;
                }
                $record6['request_ready_image_tb']=$output61;
                //*************************************************************************************************************
                $query62=" SELECT * FROM request_file_tb WHERE request_file_request_id=".$request_id;
                $result62 = $CI->B_db->run_query($query62);
                $output62 =array();
                foreach($result62 as $row62)
                {
                    $record62['request_file_url']=IMGADD.$row62['request_file_url'];
                    $record62['request_file_desc']=$row62['request_file_desc'];
                    $output62[]=$record62;
                }
                $record6['request_ready_file_tb']=$output62;
                //*************************************************************************************************************
                $output6[]=$record6;
            }
            $record['request_ready']=$output6;
            //***************************************************************************************************************
            $query5=" SELECT * FROM request_delivered_tb,state_tb,city_tb,delivery_mode_tb WHERE delivery_mode_id=request_delivered_mode_id AND state_id=request_delivered_state_id AND city_id=request_delivered_city_id AND request_delivered_request_id=".$row['request_id'];
            $result5 = $CI->B_db->run_query($query5);
            $output5 =array();
            foreach($result5 as $row5)
            {
                $record5['request_delivered_timesatmp']=$row5['request_delivered_timesatmp'];
                $record5['request_delivered_mode']=$row5['delivery_mode_name'];
                //$record5['request_delivered_mode_id']=$row5['delivery_mode_name_id'];
                $record5['request_delivered_dsc']=$row5['request_delivered_dsc'];
                $record5['request_delivered_state']=$row5['state_name'];
                $record5['request_delivered_city']=$row5['city_name'];
                $record5['request_delivered_state_id']=$row5['state_id'];
                $record5['request_delivered_city_id']=$row5['city_id'];
                $result51 = $CI->B_db->get_image($row5['request_delivered_receipt_image_code']);
                if(!empty($result51)){
                    $image=$result51[0];
                    if($image['image_tumb_url']==null){ $record5['user_pey_image_turl']=null;}else{ $record5['user_pey_image_turl']=$image['image_tumb_url'];}
                    if($image['image_url']==null){ $record5['user_pey_image_url']=null;}else{$record5['user_pey_image_url']=$image['image_url'];}
                }
                $output5[]=$record5;
            }
            $record['request_delivered']=$output5;
            //***************************************************************************************************************
            $query4=" SELECT * FROM request_img_tb,image_tb WHERE image_id=request_img_image_code AND request_img_request_id=".$request_id;
            $result4 = $CI->B_db->run_query($query4);
            $output4 =array();
            foreach($result4 as $row4)
            {
                $result51 = $CI->B_db->get_image($row4['image_code']);
                $image=$result51[0];

                $record4['image_url']=$image['image_url'];
                $record4['image_tumb_url']=$image['image_tumb_url'];
                $record4['image_name']=$row4['image_name'];
                $record4['image_desc']=$row4['image_desc'];
                $output4[]=$record4;
            }
            $record['request_image']=$output4;
            //***************************************************************************************

            $query44=" SELECT * FROM request_visit_tb,user_tb WHERE request_visit_user_id=user_id AND  request_visit_request_id=".$request_id;
            $result44 = $CI->B_db->run_query($query44);
            $output44 =array();
            foreach($result44 as $row44)
            {
                $record44['request_visit_vedio_url']=IMGADD.$row44['request_visit_vedio_url'];
                $record44['request_visit_id']=$row44['request_visit_id'];
                $record44['request_visit_user_id']=$row44['request_visit_user_id'];
                $record44['user_name']=$row44['user_name'];
                $record44['user_family']=$row44['user_family'];
                $record44['user_mobile']=$row44['user_mobile'];
                //**********************************************************
                $query40=" SELECT * FROM request_visit_image_tb,image_tb  WHERE  request_visit_image_code=image_code AND request_visit_image_visit_id=".$row44['request_visit_id'];

                $result40 = $CI->B_db->run_query($query40);
                $output40 =array();
                foreach($result40 as $row40)
                {
                    $result51 = $CI->B_db->get_image($row40['request_visit_image_code']);
                    $image=$result51[0];

                    $record40['image_url']=$image['image_url'];
                    $record40['image_tumb_url']=$image['image_tumb_url'];
                    $record40['image_name']=$row40['image_name'];
                    $record40['image_desc']=$row40['image_desc'];
                    $output40[]=$record40;

                }
                $record44['images_visit']=$output40;

                //**********************************************************
                $output44[]=$record44;
            }
            $record['visit_image']=$output44;
            //***************************************************************************************************************


            $output[]=$record;
        }
        return  $output;
    }

}


if ( ! function_exists('checkusermarketertoken1')){
    function checkusermarketertoken1($user_token_str)
    {
        $CI = get_instance();
        $CI->load->model('B_requests');
        $result=checkusertoken($user_token_str);
        if (!empty($usertoken))
        {
            $user_token=$result[0];
            if($user_token['user_token_logout_timestamp']==''){
                $marketer_user_id=$user_token['user_token_user_id'];
                $result1=$CI->B_requests->get_marketer_mode($marketer_user_id);
                if (empty($result1))
                {
                    return array('result'=>"error"
                    ,"data"=>array('marketer_user_id'=>$marketer_user_id)
                    ,'desc'=>'شما برای بازاریابی درخواست نداده اید');

                }else{
                    $usermarketer_tb=$result1[0];
                    if($usermarketer_tb['marketer_reject']==0&&$usermarketer_tb['marketer_request']==1){
                        return array('result'=>"wait"
                        ,"data"=>array('marketer_user_id'=>$usermarketer_tb['marketer_user_id'],'marketer_timestamp'=>$usermarketer_tb['marketer_timestamp'])
                        ,'desc'=>'شما برای بازاریابی درخواست  داده اید و منتظر تایید بمانید');

                    }else if($usermarketer_tb['marketer_reject']==1&&$usermarketer_tb['marketer_request']==1){
                        return array('result'=>"reject"
                        ,"data"=>array('marketer_user_id'=>$usermarketer_tb['marketer_user_id'],'marketer_timestamp'=>$usermarketer_tb['marketer_timestamp']
                            ,'marketer_reason'=>$usermarketer_tb['marketer_reason'])
                        ,'desc'=>'شما برای بازاریابی درخواست  داده اید و بدلیل بالا تایید نشد مجدادا  درخواست نمایید');

                    }else if($usermarketer_tb['marketer_reject']==0&&$usermarketer_tb['marketer_request']==0&&$usermarketer_tb['marketer_deactive']==1){
                        return array('result'=>"deactive"
                        ,"data"=>array('marketer_user_id'=>$usermarketer_tb['marketer_user_id'],'marketer_timestamp'=>$usermarketer_tb['marketer_timestamp']
                            ,'marketer_reason'=>$usermarketer_tb['marketer_reason'])
                        ,'desc'=>'شما برای بازاریابی درخواست  داده اید و بدلیل بالا غیر فعال شده اید برای رفع مشکل پیگیری نمایید');
                    }
                    else if($usermarketer_tb['marketer_reject']==0&&$usermarketer_tb['marketer_request']==0&&$usermarketer_tb['marketer_deactive']==0){
//******************************************************************

                        $result2 = $CI->B_db->get_image($usermarketer_tb['marketer_image_code']);
                        $marketer_image=$result2[0];

                        $marketer_image_url=$marketer_image['image_url'];
                        $marketer_image_tumb_url=$marketer_image['image_tumb_url'];

                        $query3=" SELECT * FROM user_tb WHERE user_mobile='".$usermarketer_tb['marketer_leader_mobile']."'";
                        $result3 = $CI->B_db->run_query($query3);
                        $leader=$result3[0];

                        $marketer_leader_name=$leader['user_name'].' '.$leader['user_family'];

                        //*******************************************************************
                        return array('result'=>"ok"
                        ,"data"=>array('marketer_user_id'=>$usermarketer_tb['marketer_user_id']
                            ,'marketer_image_code'=>$usermarketer_tb['marketer_image_code']
                            ,'marketer_timestamp'=>$usermarketer_tb['marketer_timestamp']
                            ,'marketer_leader_mobile'=>$usermarketer_tb['marketer_leader_mobile']
                            ,'marketer_leader_name'=>$marketer_leader_name
                            ,'marketer_mode_id'=>$usermarketer_tb['marketer_mode_id']
                            ,'marketer_mode_name'=>$usermarketer_tb['marketer_mode_name']
                            ,'marketer_mode_namefa'=>$usermarketer_tb['marketer_mode_namefa']
                            ,'marketer_mode_logourl'=>IMGADD.$usermarketer_tb['marketer_mode_logourl']
                            ,'marketer_mode_color'=>$usermarketer_tb['marketer_mode_color'])
                        ,'desc'=>'درخواست شما مورد تایید است');
                    }
                }
                //***************************************************************************************

            }else
            {
                return array('result'=>"error"
                ,"data"=>$user_token['user_token_user_id']
                ,'desc'=>'شما از سیستم خارج شده اید . مجددا وارد شوید');
            }
        }else{
            return array('result'=>"error"
            ,"data"=>""
            ,'desc'=>'توکن در سیستم موجود نیست');
        }
    }
}

if ( ! function_exists('show_image'))
{
    function show_image($user_token_str, $img_url) {
        $CI = get_instance();
        $CI->load->helper('download');
        $result=checkusertoken($user_token_str);
        if($result[0] == "ok"){
            // Contents of $url will be automatically read
            force_download($img_url, NULL);
        }else{
            return IMGADD.'filefolder/uploadimg/2021/08/16/t1629118192QCEYgL5!.png';
        }
    }
}

if ( ! function_exists('send_sms_rahyab')) {
    function send_sms_rahyab($mobile, $txt)
    {
        $CI = get_instance();
        $CI->load->model('B_db');
        $url = 'https://api.rahyab.ir/api/v1/SendSMS_Single';
        $token = rahyab_sms_token();
        // create & initialize a curl session
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL,$url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $post = [
            "message"=> $txt,
            "destinationAddress"=> $mobile,
            "number"=> "1000001439",
            "userName"=> "web_parsianlkh",
            "password"=> "73Aqq38ZxrjL",
            "company"=> "PARSIANLKH"
        ];
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $output = curl_exec($curl);
        if($output === false){
            print_r('Curl error: ' . curl_error($curl));
        }
        $output = json_decode($output, true);

        if($output['code'] == 'CHECK_OK')
            $output['code'] = 1;
        else
            $output['code'] = 0;
        $sql  = "INSERT INTO sms_rahyab_tb
                (sms_rahyab_user_id, sms_rahyab_txt, sms_rahyab_timestamp, sms_rahyab_delivery_time, sms_rahyab_status, sms_rahyab_receiver, sms_rahyab_identity, sms_rahyab_errormsg)
                VALUES(1, '$txt', now(), '', '".$output['code']."','".$mobile."', '".$output['identity']."', '".$output['errorMsg']."');";
        $CI->B_db->run_query_put($sql);
        curl_close($curl);
        return $output;
    }
}

//get result and status of sent sms
if ( ! function_exists('rahyab_sms_status')) {
    function rahyab_sms_status($identity)
    {
        $CI = get_instance();
        $CI->load->model('B_db');
        $url = 'https://api.rahyab.ir/api/v1/StatusSMS';
        $token = rahyab_sms_token();
        $post = [
            "userName"=> "web_parsianlkh",
            "password"=> "73Aqq38ZxrjL",
            "company"=> "PARSIANLKH",
            "batchId"=> $identity
        ];
        // create & initialize a curl session
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL,$url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($curl);
        $output = json_decode($output, true);
        $output = $output[0];
        if($output['deliveryStatus'] == 'MT_DELIVERED')
            $output['deliveryStatus'] = 1;
        else
            $output['deliveryStatus'] = 0;
        $sql  = "Update sms_rahyab_tb set sms_rahyab_delivery_time='".$output['time']."' , sms_rahyab_status= '".$output['deliveryStatus']."', 
                 sms_rahyab_errormsg= '".$output['errorMsg']."' WHERE `sms_rahyab_identity` = '".$identity."'";
        $CI->B_db->run_query_put($sql);
        curl_close($curl);
        return $output;
    }
}

//get result and status of sent sms
if ( ! function_exists('rahyab_get_credit')) {
    function rahyab_get_credit()
    {
        $CI = get_instance();
        $CI->load->model('B_db');
        $url = 'https://api.rahyab.ir/api/v1/GetRemainCredit';
        $token = rahyab_sms_token();
        $post = [
            "userName"=> "web_parsianlkh",
            "password"=> "73Aqq38ZxrjL",
            "company"=> "PARSIANLKH",
        ];
        // create & initialize a curl session
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL,$url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($curl);
        $output = json_decode($output, true);
        curl_close($curl);
        return $output;
    }
}

if ( ! function_exists('rahyab_sms_token')) {
    function rahyab_sms_token()
    {
        $url = 'https://api.rahyab.ir/api/Auth/getToken';
        // create & initialize a curl session
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL,$url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            'Content-Type: application/json'
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $post = [
            "userName"=> "web_parsianlkh@PARSIANLKH",
            "password"=> "73Aqq38ZxrjL"
        ];
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}
