<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//require APPPATH .'../vendor/autoload.php';
//require FCPATH.'/vendor/autoload.php';

use Aws\S3\S3Client;
class B_db extends CI_Model {

    public $s3Client;
    function __construct()
    {
        parent::__construct();
        // $this->s3Client = new S3Client([
        //     'version' => 'latest',
        //     'region'  => 'eu-east-1',
        //     'endpoint' => ENDPOINT,
        //     'credentials' => [
        //         'key'    => AWS_KEY,
        //         'secret' => AWS_SECRET_KEY
        //     ]
        // ]);
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

    function run_query($sql){
        //echo $sql."###";
        return $this->db->query($sql)->result_array();
    }

    function run_query_put($sql){
        return $this->db->query($sql);
    }

    function get_image($user_national_image_code, $abr_expire_time=3600){
        $sql="SELECT * FROM image_tb WHERE image_code='".$user_national_image_code."'";
        $result =  $this->db->query($sql)->result_array();

        $image=$result[0];
        $image_abr = $image['image_abr'];
        if($image_abr == 0){
            $result[0]['image_url']=IMGADD. $image['image_url'];
            $result[0]['image_tumb_url']=IMGADD.$image['image_tumb_url'];

            return $result;
        }else{
            $image_url = $this->dwnpresigned_image($user_national_image_code, $abr_expire_time);
            $result[0]['image_url']=$image_url;
            $image_tumb_url= $this->dwnpresigned_image('t'.$user_national_image_code, $abr_expire_time);
            $result[0]['image_tumb_url']=$image_tumb_url;
            return $result;
        }
    }

    function get_image_whitoururl($user_national_image_code){
        $sql="SELECT * FROM image_tb WHERE image_code='".$user_national_image_code."'";
        return $this->db->query($sql)->result_array();
    }


    function get_requst_ready_image($request_id){
        $sql31=" SELECT * FROM requst_ready_image_tb,image_tb WHERE image_code=requst_ready_image_code AND requst_ready_request_id=".$request_id;
        return $this->db->query($sql31)->result_array();
    }

    function get_request_img($request_id){
        $sql4=" SELECT * FROM request_img_tb,image_tb WHERE image_id=request_img_image_code AND request_img_request_id=".$request_id;
        return $this->db->query($sql4)->result_array();
    }

    function get_sms_confirm($user_id, $sms_confirm_code){
        $update_query = "Update sms_confirm_tb set counter=counter+1 where sms_confirm_user_id=".$user_id;
        $this->db->query($update_query);
        $sql="select * from sms_confirm_tb where sms_confirm_user_id=".$user_id." and sms_confirm_code=".$sms_confirm_code." and counter<4 and now() <= sms_expire_timestamp";
        return $this->db->query($sql)->result_array();
    }

    function get_login_userpass($user_vip_password, $user_vip_username){
        $sql="select * from user_vip_tb where user_vip_password=".$user_vip_password." and user_vip_username=".$user_vip_username;
        return $this->db->query($sql)->result_array();
    }

    function create_reminder($reminder_mobile,$reminder_reagent_mobile,$reminder_fieldinsurance_id,$reminder_timestamp,$reminder_desc){
        $sql="INSERT INTO reminder_tb( reminder_mobile, reminder_reagent_mobile, reminder_fieldinsurance_id, reminder_timestamp_now, reminder_timestamp, reminder_desc) VALUES
                               ('$reminder_mobile','$reminder_reagent_mobile',$reminder_fieldinsurance_id,       now()    ,'$reminder_timestamp','$reminder_desc')";
        return $this->db->query($sql);
    }

    function add_sms($user_id,$sms_confirm_code){
        $sms_expire_timestamp = date('Y-m-d H:i:s', strtotime("+16320 second"));
        $sql="INSERT INTO sms_confirm_tb(sms_confirm_user_id,sms_confirm_code,sms_confirm_timestamp, sms_expire_timestamp)values(".$user_id.",".$sms_confirm_code.",now(),'".$sms_expire_timestamp."')";
        return $this->db->query($sql);
    }

    function get_last_sms_time($user_id){
        $sql="SELECT TIME_TO_SEC(TIMEDIFF(now(),sms_confirm_timestamp)) as diff
        from sms_confirm_tb
        where sms_confirm_user_id=".$user_id." order by sms_confirm_id desc  LIMIT 1";
        return $this->db->query($sql)->result_array();
    }

    function del_sms_confirm($user_id){
        return $this->db->delete('sms_confirm_tb', array('sms_confirm_user_id' => $user_id));
    }

    function add_token($user_id , $user_token_str ,$user_token_mode , $user_token_app_version, $user_token_device_name, $user_token_device_version, $user_token_ip){
        $this->expire_user_last_token_str($user_id);
        $sql="INSERT INTO user_token_tb( `user_token_user_id` , `user_token_str` , `user_token_timestamp` , `user_token_mode` ,
       `user_token_app_version` ,`user_token_device_name` , `user_token_device_version` , `user_token_ip`) VALUES
       ('$user_id' , '$user_token_str', now() , '$user_token_mode', '$user_token_app_version', '$user_token_device_name', '$user_token_device_version', '$user_token_ip')";
        return $this->db->query($sql);
    }

    function check_user_token($user_token_str , $And = ""){
        //259200 = mean token expires after 3Days
        $sql="select * from user_token_tb where  DATEDIFF(now(), user_token_timestamp)<259200 AND user_token_str='".$user_token_str."' ".$And;
        $result=$this->db->query($sql)->result_array();
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
    }

    function update_user_token_tb($user_token_str){
        $sql="UPDATE user_token_tb SET user_token_logout_timestamp=now() WHERE user_token_str='".$user_token_str."'";
        return $this->db->query($sql);
    }

    function expire_user_last_token_str($user_id){
        $sql="UPDATE user_token_tb SET user_token_logout_timestamp=now() WHERE user_token_logout_timestamp = '' AND user_token_user_id='".$user_id."'";
        return $this->db->query($sql);
    }

    function get_fieldinsurance($condition){
        $sql="select * from fieldinsurance_tb where ".$condition;
        return $this->db->query($sql)->result_array();
    }

    function get_fielddamagefile($condition){
        $sql="select * from fielddamagefile_tb,organ_therapycontract_conditions_covarage_tb where
fielddamagefile_organ_therapycontract_conditions_covarage_id=organ_therapycontract_conditions_covarage_id                                                                                       
              AND
                                       ".$condition;
        return $this->db->query($sql)->result_array();
    }
    function add_sitecontent($sitecontent_page,$sitecontent_place, $sitecontent_inplace,$sitecontent_mode,$sitecontent_title,$sitecontent_text,$sitecontent_image,$sitecontent_image_code,$sitecontent_btntxt,$sitecontent_link){
        $sql="select * from sitecontent_tb where sitecontent_page='".$sitecontent_page."' AND sitecontent_place='".$sitecontent_place."' AND sitecontent_inplace='".$sitecontent_inplace."' ";
        $result =  $this->db->query($sql)->result_array();
        if (empty($result[0])) {
            $sql = "INSERT INTO sitecontent_tb( sitecontent_page,  sitecontent_place,   sitecontent_inplace,  sitecontent_mode,    sitecontent_title ,   sitecontent_text,   sitecontent_image,sitecontent_btntxt,sitecontent_link)
	                           VALUES ('$sitecontent_page','$sitecontent_place','$sitecontent_inplace' , '$sitecontent_mode' ,  '$sitecontent_title' , '$sitecontent_text','$sitecontent_image','$sitecontent_btntxt','$sitecontent_link');";
            $new_sitecontent_id = $this->db->query($sql);
            if($sitecontent_image_code!=""){
                $sql2="select * from image_tb where image_code='".$sitecontent_image_code."'";
                $result2 =  $this->db->query($sql2)->result_array();
                $image=$result2[0];
                $ext = pathinfo($image['image_url'], PATHINFO_EXTENSION);

                $sitecontent_image='filefolder/sitecontent/'.$new_sitecontent_id.'.'.$ext;
                copy($image['image_url'], $sitecontent_image);
                $sql3="UPDATE `sitecontent_tb` SET sitecontent_image='$sitecontent_image' where sitecontent_id=".$new_sitecontent_id."";
                $this->db->query($sql3);
            }
            return array('new_sitecontent_id'=>$new_sitecontent_id);
        }else{
            $result =  $this->db->query($sql)->result_array();
            return array('sitecontent_id'=>$result[0]['sitecontent_id']);
        }
    }

    function del_sitecontent($sitecontent_id){
        $sql="DELETE FROM sitecontent_tb  where sitecontent_id=".$sitecontent_id."";
        return $this->db->query($sql);
    }

    function getmain_page(){
        $sql="select * from fieldinsurance_tb where fieldinsurance_deactive=0  ORDER BY fieldinsurance_id ASC ";
        return $this->db->query($sql)->result_array();
    }

    function peyback_decision($request_id,$amount_pey,$mode='add',$desc,$main='nomain')
    {
        if ($amount_pey > 0) {
            //*****************************************************************************
            if ($main == 'main') {
                $query2 = "SELECT * FROM  user_wallet_tb WHERE user_wallet_id in (
    select peycommision_marketer_user_wallet_id from peycommision_marketer_tb where peycommision_marketer_payback_id=0 AND
    peycommision_marketer_request_id=$request_id) ";
                $result2 = $this->B_db->run_query($query2);
                foreach ($result2 as $row1) {
                    $user_wallet = $row1;

                    $query71 = "INSERT INTO user_wallet_tb( user_wallet_user_id, user_wallet_amount, user_wallet_mode, user_wallet_timestamp, user_wallet_detail, user_wallet_code) VALUES
                  (" . $user_wallet['user_wallet_user_id'] . ",'" . $user_wallet['user_wallet_amount'] . "' , 'get'      ,now()              ,'بازگشت " . $user_wallet['user_wallet_detail'] . "' ,$request_id)      ";
                    $result71 = $this->B_db->run_query_put($query71);
                    $user_wallet_id = $this->db->insert_id();

                    $query72 = "INSERT INTO peybackcommision_marketer_tb
( peybackcommision_marketer_request_id, peybackcommision_marketer_user_wallet_id)
VALUES( $request_id, $user_wallet_id); ";
                    $result72 = $this->B_db->run_query_put($query72);


                }

                $query2 = "SELECT * FROM  user_wallet_tb WHERE user_wallet_id in (
    select peycommision_leader_user_wallet_id from peycommision_leader_tb where peycommision_leader_peyback_id=0 AND
    peycommision_leader_request_id=$request_id) ";
                $result2 = $this->B_db->run_query($query2);
                foreach ($result2 as $row1) {
                    $user_wallet1 = $row1;

                    $query71 = "INSERT INTO user_wallet_tb( user_wallet_user_id, user_wallet_amount, user_wallet_mode, user_wallet_timestamp, user_wallet_detail, user_wallet_code) VALUES
                  (" . $user_wallet1['user_wallet_user_id'] . ",'" . $user_wallet1['user_wallet_amount'] . "' , 'get'      ,now()              ,'بازگشت " . $user_wallet1['user_wallet_detail'] . "' ,$request_id)      ";
                    $result71 = $this->B_db->run_query_put($query71);
                    $user_wallet_id = $this->db->insert_id();

                    $query72 = "INSERT INTO peybackcommision_leader_tb
( peybackcommision_leader_request_id, peybackcommision_leader_user_wallet_id)
VALUES( $request_id, $user_wallet_id); ";
                    $result72 = $this->B_db->run_query_put($query72);

                }
                $query2 = "UPDATE peycommision_marketer_tb
SET  peycommision_marketer_payback_id=1
WHERE   peycommision_marketer_request_id=$request_id ";
                $result2 = $this->db->query($query2);

                $query3 = "UPDATE peycommision_leader_tb
SET peycommision_leader_peyback_id=1
WHERE peycommision_leader_request_id=$request_id ";
                $result3 = $this->db->query($query3);


            }
            //کد های پرداخت کارمزد به بازاریابان
            $query7 = "select * from usermarketer_tb,user_tb,marketer_mode_tb,marketer_commission_defult_tb,fieldinsurance_tb,request_tb 
        where usermarketer_tb.marketer_mode_id=marketer_mode_tb.marketer_mode_id AND marketer_user_id=user_id AND
        marketer_request=0  AND marketer_reject=0 AND 	marketer_commission_defult_mode_id=usermarketer_tb.marketer_mode_id AND
        marketer_commission_defult_fieldinsurance_id=fieldinsurance_id AND
        user_mobile=request_reagent_mobile AND
        request_id = $request_id AND fieldinsurance=request_fieldinsurance";
            $result7 = $this->B_db->run_query($query7);
            $num7 = count($result7[0]);
            if ($num7 > 0) {
                // کاربران بازاریاب**************************************************************************
                $usermarketer = $result7[0];
                $amount = intval($amount_pey * $usermarketer['marketer_commission_defult_percent'] / 1000);
                $amount_pey = intval($amount_pey);
                $user_wallet_detail = '';
                if ($mode == 'add') {
                    $user_wallet_detail = 'پرداخت درصد بازاریابی نوع ' . $usermarketer['marketer_mode_namefa'] . ' در رشته ' . $usermarketer['fieldinsurance_fa'] . ' با درصد  ' . ($usermarketer['marketer_commission_defult_percent'] / 10) . ' برای قسمت ' . $desc . ' سفارش شماره ' . $request_id . ' به مبلغ ' . $amount_pey;
                } else if ($mode == 'get') {
                    $user_wallet_detail = 'بازگشت درصد بازاریابی نوع ' . $usermarketer['marketer_mode_namefa'] . ' در رشته ' . $usermarketer['fieldinsurance_fa'] . ' با درصد  ' . ($usermarketer['marketer_commission_defult_percent'] / 10) . ' برای سفارش شماره ' . $request_id . ' به مبلغ ' . $amount_pey . ' به علت ' . $desc;
                }
                $query71 = "INSERT INTO user_wallet_tb( user_wallet_user_id, user_wallet_amount, user_wallet_mode, user_wallet_timestamp, user_wallet_detail, user_wallet_code) VALUES
                  (" . $usermarketer['user_id'] . ",'" . $amount . "' , '$mode'      ,now()               ,'$user_wallet_detail',$request_id)      ";
                $result71 = $this->B_db->run_query_put($query71);
                $user_wallet_id = $this->db->insert_id();

                if ($mode == 'add') {
                    $query70 = "INSERT INTO peycommision_marketer_tb( peycommision_marketer_request_id, peycommision_marketer_user_wallet_id) VALUES
                                                                   ($request_id,$user_wallet_id)      ";
                    $result70 = $this->B_db->run_query_put($query70);
                } else if ($mode == 'get') {
                    $query70 = "INSERT INTO peybackcommision_marketer_tb( peybackcommision_marketer_request_id, peybackcommision_marketer_user_wallet_id) VALUES
                                                                   ($request_id,$user_wallet_id)      ";
                    $result70 = $this->B_db->run_query_put($query70);
                }

                if ($usermarketer['request_leader_mobile'] != null && $usermarketer['request_leader_mobile'] != "") {
                    // هماهنگ کننده بازاریاب**************************************************************************
                    $query72 = "select * from user_tb where user_mobile=" . $usermarketer['request_leader_mobile'] . "";
                    $result72 = $this->B_db->run_query($query72);
                    $num72 = count($result72[0]);
                    if ($num72 > 0) {
                        $user = $result72[0];
                        $query73 = "select * from request_tb,user_tb ,usermarketer_tb,leader_commission_defult_tb,fieldinsurance_tb ft where request_id=$request_id and request_reagent_mobile=user_mobile 
AND marketer_user_id=user_id AND leader_mode_id=leader_commission_defult_mode_id and leader_commission_defult_fieldinsurance_id=fieldinsurance_id AND request_fieldinsurance=fieldinsurance";
                        $result73 = $this->B_db->run_query($query73);
                        $leader_commission = $result73[0];
                        $amount = intval($amount_pey * $leader_commission['leader_commission_defult_percent'] / 1000);
                        $user_wallet_detail = '';
                        if ($mode == 'add') {
                            $user_wallet_detail = 'پرداخت درصد بازاریابی  هماهنگ کننده همکار فروش در رشته ' . $leader_commission['fieldinsurance_fa'] . ' با درصد  ' . ($leader_commission['leader_commission_defult_percent'] / 10) . ' برای قسمت ' . $desc . ' سفارش شماره ' . $request_id . ' به مبلغ ' . $amount_pey;
                        } else if ($mode == 'get') {
                            $user_wallet_detail = 'بازگشت درصد بازاریابی  هماهنگ کننده همکار فروش در رشته ' . $leader_commission['fieldinsurance_fa'] . ' با درصد  ' . $leader_commission['leader_commission_defult_percent'] . ' برای سفارش شماره ' . $request_id . ' به مبلغ ' . $amount_pey . ' به علت ' . $desc;
                        }
                        $query74 = "INSERT INTO user_wallet_tb( user_wallet_user_id, user_wallet_amount, user_wallet_mode, user_wallet_timestamp, user_wallet_detail, user_wallet_code) VALUES
                          (" . $user['user_id'] . ",'" . $amount . "' , '$mode'      ,now()               ,'$user_wallet_detail',$request_id)      ";
                        $result74 = $this->B_db->run_query_put($query74);
                        $user_wallet_id = $this->db->insert_id();

                        if ($mode == 'add') {
                            $query75 = "INSERT INTO peycommision_leader_tb( peycommision_leader_request_id, peycommision_leader_user_wallet_id) VALUES
                                                                   ($request_id,$user_wallet_id)      ";
                            $result75 = $this->B_db->run_query_put($query75);
                        } else if ($mode == 'get') {
                            $query75 = "INSERT INTO peybackcommision_leader_tb( peybackcommision_leader_request_id, peybackcommision_leader_user_wallet_id) VALUES
                                                                   ($request_id,$user_wallet_id)      ";
                            $result75 = $this->B_db->run_query_put($query75);
                        }


                    }
                    // هماهنگ کننده بازاریاب**************************************************************************
                } else {
                }
                // کاربران بازاریاب**************************************************************************
            } else {
                $query8 = "select * from marketer_mode_tb,marketer_commission_defult_tb,fieldinsurance_tb,request_tb,user_tb 
             where	marketer_commission_defult_mode_id=0 AND
             marketer_commission_defult_fieldinsurance_id=fieldinsurance_id 
             AND marketer_commission_defult_mode_id=marketer_mode_id AND
             user_mobile=request_reagent_mobile
             AND request_reagent_mobile IS NOT NULL AND request_reagent_mobile!='' 
             AND request_id = $request_id AND fieldinsurance=request_fieldinsurance";
                $result8 = $this->B_db->run_query($query8);
                $num8 = count($result8[0]);
                if ($num8 > 0) {
                    // کاربران ثبت نام کرده اند ولی بازاریاب نیستند**************************************************************************
                    $usermarketer = $result8[0];
                    $amount = intval($amount_pey * $usermarketer['marketer_commission_defult_percent'] / 1000);
                    $user_wallet_detail = '';
                    if ($mode == 'add') {
                        $user_wallet_detail = 'پرداخت درصد بازاریابی نوع ' . $usermarketer['marketer_mode_namefa'] . ' در رشته ' . $usermarketer['fieldinsurance_fa'] . ' با درصد ' . ($usermarketer['marketer_commission_defult_percent'] / 10) . '  برای قسمت ' . $desc . ' سفارش شماره ' . $request_id . ' به مبلغ ' . $amount_pey;
                    } else if ($mode == 'get') {
                        $user_wallet_detail = 'بازگشت درصد بازاریابی نوع ' . $usermarketer['marketer_mode_namefa'] . ' در رشته ' . $usermarketer['fieldinsurance_fa'] . ' با درصد ' . ($usermarketer['marketer_commission_defult_percent'] / 10) . ' برای سفارش شماره ' . $request_id . ' به مبلغ ' . $amount_pey . ' به علت ' . $desc;
                    }
                    $query81 = "INSERT INTO user_wallet_tb( user_wallet_user_id, user_wallet_amount, user_wallet_mode, user_wallet_timestamp, user_wallet_detail, user_wallet_code) VALUES
                  (" . $usermarketer['user_id'] . ",'" . $amount . "' , '$mode'      ,now()               ,'$user_wallet_detail',$request_id)      ";
                    $result81 = $this->B_db->run_query_put($query81);
                    $user_wallet_id = $this->db->insert_id();
                    $query82 = "INSERT INTO peycommision_user_tb( peycommision_user_request_id, peycommision_user_wallet_id) VALUES
                                                                   ($request_id,$user_wallet_id)      ";
                    $result82 = $this->B_db->run_query_put($query82);
                    // کاربران ثبت نام کرده اند ولی بازاریاب نیستند**************************************************************************
                } else {
                    $query9 = "select * from marketer_mode_tb,marketer_commission_defult_tb,fieldinsurance_tb,request_tb 
                 where	marketer_commission_defult_mode_id=0 AND
                 marketer_commission_defult_fieldinsurance_id=fieldinsurance_id 
                 AND marketer_commission_defult_mode_id=marketer_mode_id
                 AND request_reagent_mobile IS NOT NULL AND request_reagent_mobile!='' 
                 AND request_id = $request_id AND fieldinsurance=request_fieldinsurance";
                    $result9 = $this->B_db->run_query($query9);
                    $num9 = count($result9[0]);
                    if ($num9 > 0) {
// کاربرانی که ثبت نام نکرده اند**************************************************************************
                        $usermarketer = $result9[0];
                        $amount = intval($amount_pey * $usermarketer['marketer_commission_defult_percent'] / 1000);
                        $user_wallet_detail = '';
                        if ($mode == 'add') {
                            $user_wallet_detail = 'پرداخت درصد بازاریابی به کاربری که هنوز ثبت نام نکرده با نوع ' . $usermarketer['marketer_mode_namefa'] . ' در رشته ' . $usermarketer['fieldinsurance_fa'] . ' با درصد ' . ($usermarketer['marketer_commission_defult_percent'] / 10) . '  برای قسمت ' . $desc . ' سفارش شماره ' . $request_id . ' به مبلغ ' . $amount_pey;
                        } else if ($mode == 'get') {
                            $user_wallet_detail = 'بازگشت درصد بازاریابی به کاربری که هنوز ثبت نام نکرده با نوع ' . $usermarketer['marketer_mode_namefa'] . ' در رشته ' . $usermarketer['fieldinsurance_fa'] . ' با درصد ' . ($usermarketer['marketer_commission_defult_percent'] / 10) . ' برای سفارش شماره ' . $request_id . ' به مبلغ ' . $amount_pey . ' به علت ' . $desc;
                        }

                        $query91 = "INSERT INTO user_wallet_tb( 	user_wallet_user_mobile, user_wallet_amount, user_wallet_mode, user_wallet_timestamp, user_wallet_detail, user_wallet_code) VALUES
                  (" . $usermarketer['request_reagent_mobile'] . ",'" . $amount . "' , '$mode'      ,now()               ,'$user_wallet_detail',$request_id)      ";
                        $result91 = $this->B_db->run_query_put($query91);
                        $user_wallet_id = $this->db->insert_id();
                        $query92 = "INSERT INTO peycommision_user_tb( peycommision_user_request_id, peycommision_user_wallet_id) VALUES
                             ($request_id,$user_wallet_id)      ";
                        $result92 = $this->B_db->run_query_put($query92);
// کاربرانی که ثبت نام نکرده اند**************************************************************************
                    }
                }
            }
        }
    }
}
