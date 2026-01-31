<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * B_user
 *
 * @mixin Eloquent
 */
class B_user extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    function get_user($user_id){
        $sql="select * from user_tb where user_id='".$user_id."'";
        return $this->db->query($sql)->result_array();
    }

    function get_user_vip($user_id){
        $sql="select * from user_tb,user_vip_tb where user_vip_user_id=user_id AND user_id='".$user_id."'";
        return $this->db->query($sql)->result_array();
    }

    function get_token_tb($user_token_str){
        $sql="select * from user_token_tb where user_token_str='".$user_token_str."'";
        return $this->db->query($sql)->result_array();
    }

    function get_user_by_moblie($user_mobile){
        $sql="select * from user_tb where user_mobile='".$user_mobile."'";
        return $this->db->query($sql)->result_array();
    }

    function create_user($user_mobile){
        $sql="INSERT INTO user_tb (user_mobile,user_register_date) VALUES ('".$user_mobile."',now());";
        return $this->db->query($sql);
    }

    function update_user_tb($data,$user_id){
        if(isset($data['user_name'])){
            $this->db->set('user_name', $data['user_name']);
        }
        if(isset($data['user_family'])){
            $this->db->set('user_family', $data['user_family']);
        }
        if(isset($data['user_email'])){
            $this->db->set('user_email', $data['user_email']);
        }
        if(isset($data['user_national_code'])){
            $this->db->set('user_national_code', $data['user_national_code']);
        }
        if(isset($data['user_national_image_code'])){
            $this->db->set('user_national_image_code', $data['user_national_image_code']);
        }
        if(isset($data['user_back_national_image_code'])){
            $this->db->set('user_back_national_image_code', $data['user_back_national_image_code']);
        }
        $this->db->where('user_id', $user_id);
        return $this->db->update('user_tb');
    }


    function add_user_address($user_address_user_id, $user_address_state_id, $user_address_city_id, $user_address_str, $user_address_code, $user_address_name, $user_address_mobile, $user_address_tell){
        $sql="INSERT INTO user_address_tb( user_address_user_id, user_address_state_id, user_address_city_id, user_address_str, user_address_code,
              user_address_name, user_address_mobile, user_address_tell,user_address_timestamp)
	           VALUES ( $user_address_user_id, $user_address_state_id, $user_address_city_id, '$user_address_str', '$user_address_code', '$user_address_name',
	           '$user_address_mobile', '$user_address_tell',now());";
        return $this->db->query($sql);
    }

    function get_user_address($user_address_user_id,$user_address_code,$user_address_name ){
        $sql="select * from user_address_tb where user_address_delete=0 AND user_address_user_id=$user_address_user_id AND (user_address_code='$user_address_code' AND user_address_name='$user_address_name')";
        return $this->db->query($sql)->result_array();
    }

    function get_user_address_by_userid($user_address_user_id){
        $sql="select * from user_address_tb,city_tb,state_tb where state_id=user_address_state_id AND city_id=user_address_city_id AND user_address_delete=0 AND  user_address_user_id=".$user_address_user_id;
        return $this->db->query($sql)->result_array();
    }

    function update_user_address($user_address_id){
        $sql="UPDATE user_address_tb SET user_address_delete=1 where user_address_id=".$user_address_id;
        return $this->db->query($sql);
    }

    function get_statecity($state_id=''){
        if($state_id)
            $sql="select * from city_tb where city_state_id=$state_id";
        else
            $sql="select * from state_tb where 1";
        return $this->db->query($sql)->result_array();
    }

    function checkrequestip($page,$command,$ip, $request_count=10 , $_time=60)
    {
        //More limitation for user sms_sendig in minute with same IP
        if ($command == "register_user") $request_count=3;

        $sql="INSERT INTO requestip_tb( requestip_page, requestip_command, requestip_ip, requestip_timestamp) VALUES('".$page."' , '".$command."' , '".$ip."' ,".time()." )";
         $this->db->query($sql);
        $date=time()-$_time;
        $query="SELECT count(*) AS cnt,max(requestip_timestamp) as max FROM  requestip_tb WHERE requestip_page='".$page."' AND requestip_command='".$command."' AND requestip_ip='".$ip."' AND requestip_timestamp > $date";
        $result =$this->db->query($query)->result_array();
        if($result[0]['cnt']<$request_count){
            return 1;
        }else{
            echo json_encode(array('result'=>"error"
            ,"data"=>''
            ,'desc'=>'به علت درخواست زیاد IP شما بصورت موقت مسدو شد'));
            return 0;
        }
    }

   
    
    function get_userbank($user_id){
        $sql="select * from useracbank_tb where useracbank_delete=0 AND  useracbank_user_id=".$user_id;
        return $this->db->query($sql)->result_array();
    }

    function get_userbank_by($useracbank_sheba){
        $sql="select * from useracbank_tb where useracbank_sheba='".$useracbank_sheba."'";
        return $this->db->query($sql)->result_array();
    }

    public function create_userbank($useracbank_user_id, $useracbank_sheba,$useracbank_bankname){
        $sql="INSERT INTO useracbank_tb( useracbank_user_id, useracbank_sheba, useracbank_bankname,useracbank_timestamp)
	          VALUES ( $useracbank_user_id, '$useracbank_sheba','$useracbank_bankname',now());";
        return $this->db->query($sql);
    }

    function user_wallet($user_id){
        $sql="select * from user_wallet_tb where  user_wallet_user_id=".$user_id;
        return $this->db->query($sql)->result_array();
    }

    function refund_user($user_id){
        $sql="select * from refund_user_tb where  refund_user_user_id=".$user_id;
        return $this->db->query($sql)->result_array();
    }

    function add_refund_user($user_id, $amount ,$useracbank_id){

        $sql ="INSERT INTO refund_user_tb(refund_user_user_id, refund_user_amount, refund_user_useracbank_id,refund_user_date) VALUES
               (".$user_id."  ,$amount , $useracbank_id , now() )";
        return $this->db->query($sql);
    }

    function add_reminder($reminder_mobile,$reminder_reagent_mobile,$reminder_fieldinsurance_id,$reminder_timestamp,$reminder_desc){
        $sql="INSERT INTO reminder_tb( reminder_mobile, reminder_reagent_mobile, reminder_fieldinsurance_id, reminder_timestamp_now, reminder_timestamp, reminder_desc) VALUES
              ('$reminder_mobile','$reminder_reagent_mobile',$reminder_fieldinsurance_id,now(),'$reminder_timestamp','$reminder_desc')";
        return $this->db->query($sql);
    }

    function get_reminder($user_id){
        $sql="select * from reminder_tb,user_tb,fieldinsurance_tb where reminder_fieldinsurance_id=fieldinsurance_id AND user_mobile=reminder_mobile  AND  user_id=".$user_id;
        return $this->db->query($sql)->result_array();
    }

    function get_reminder_reagent($user_id){
        $sql="select * from reminder_tb,user_tb,fieldinsurance_tb where reminder_fieldinsurance_id=fieldinsurance_id AND user_mobile=reminder_reagent_mobile  AND  user_id=".$user_id;
        return $this->db->query($sql)->result_array();
    }

    function get_reminder_leader_mobile($leader_mobile){
        $sql="select * from reminder_tb,user_tb,fieldinsurance_tb,usermarketer_tb where reminder_fieldinsurance_id=fieldinsurance_id AND	marketer_leader_mobile=$leader_mobile AND marketer_user_id=user_id AND user_mobile=reminder_reagent_mobile";
        return $this->db->query($sql)->result_array();
    }

    function modify_reminder($reminder_id , $user_mobile){
        $sql="UPDATE reminder_tb SET reminder_user_deactive=1 WHERE reminder_id=$reminder_id AND reminder_mobile='".$user_mobile."'";
        return $this->db->query($sql);
    }

    function get_user_pey($request_id){
        $sql2="select * from user_pey_tb where user_pey_mode='cash' AND user_pey_request_id=$request_id ";
        return $this->db->query($sql2)->result_array();
    }

    function del_user_pey($request_id){
        $sql2="DELETE FROM  user_pey_tb WHERE user_pey_request_id=$request_id ";
        return $this->db->query($sql2);
    }

    function set_user_wallet($user_id ,$user_pey_amount,$add,$desc ,$user_pey_code){
        $sql3="INSERT INTO user_wallet_tb(user_wallet_user_id,user_wallet_amount,  user_wallet_mode, user_wallet_timestamp, user_wallet_detail, user_wallet_code) VALUES
		                                   ($user_id ,'$user_pey_amount','$add', now(),'$desc','$user_pey_code' )";
        return $this->db->query($sql3);
    }

    function deficit_user_pey($deficit_pey_request_id){
        $sql=" SELECT * FROM deficit_pey_tb LEFT JOIN user_pey_tb ON deficit_pey_user_pey_id = user_pey_id AND deficit_pey_request_id=$deficit_pey_request_id";
        return $this->db->query($sql)->result_array();
    }

    function get_user_back_national_image($user_back_national_image_code){
        $sql="SELECT * FROM image_tb WHERE image_code='".$user_back_national_image_code."'";
        return $this->db->query($sql)->result_array();
    }

    
    function get_main_token(){
        $token = $this->session->userdata('main_token');
        if(!isset($token)){
            $token = generateToken(30);
            $this->session->set_userdata('main_token', $token);
        }else{
            $token = $this->session->userdata('main_token');
        }
        return $token;
    }

    function get_organ_user_therapy($national_code){
        $sql="SELECT * FROM organ_user_therapy_tb WHERE organ_user_therapy_national_code=".$national_code;
        return $this->db->query($sql)->result_array();
    }

    function create_organ_user_therapy($user, $user_id){
        $sql="INSERT INTO organ_user_therapy_tb
        (organ_user_therapy_organ_id, organ_user_therapy_main_user_id, organ_user_therapy_name, organ_user_therapy_family, organ_user_therapy_personal_code, organ_user_therapy_national_code, 
        organ_user_therapy_gender_id, organ_user_therapy_year, organ_user_therapy_month, organ_user_therapy_day, organ_user_therapy_fathername, organ_user_therapy_kind_id, organ_user_therapy_kinship_id, 
        organ_user_therapy_organ_therapycontract_id, organ_user_therapy_basebime_id, organ_user_therapy_bank_id, 
        organ_user_therapy_cardno, organ_user_therapy_accno, organ_user_therapy_shebano, organ_user_therapy_bimeno, organ_user_therapy_idcardno, organ_user_therapy_main_national_code)
        VALUES(".$user['organ_user_therapy_organ_id'].", $user_id, '".$user['organ_user_therapy_name']."', '".$user['organ_user_therapy_family']."','".$user['organ_user_therapy_personal_code']."',
        '".$user['organ_user_therapy_national_code']."', ".$user['organ_user_therapy_gender_id'].",".$user['organ_user_therapy_year'].", ".$user['organ_user_therapy_month'].", ".$user['organ_user_therapy_day'].",
        '".$user['organ_user_therapy_fathername']."', '".$user['organ_user_therapy_kind_id']."', '".$user['organ_user_therapy_kinship_id']."', '".$user['organ_user_therapy_organ_therapycontract_id']."',
        '".$user['organ_user_therapy_basebime_id']."','".$user['organ_user_therapy_bank_id']."', '".$user['organ_user_therapy_cardno']."', '".$user['organ_user_therapy_accno']."', '".$user['organ_user_therapy_shebano']."',
        '".$user['organ_user_therapy_bimeno']."', '".$user['organ_user_therapy_idcardno']."', '".$user['organ_user_therapy_main_national_code']."')";
        return $this->db->query($sql);
    }


    function update_organ_user_therapy($data, $organ_user_therapy_national_code, $user_id){
        if(isset($data['organ_user_therapy_organ_id'])){
            $this->db->set('organ_user_therapy_organ_id', $data['organ_user_therapy_organ_id']);
        }
        if(isset($data['organ_user_therapy_name'])){
            $this->db->set('organ_user_therapy_name', $data['organ_user_therapy_name']);
        }
        if(isset($data['organ_user_therapy_family'])){
            $this->db->set('organ_user_therapy_family', $data['organ_user_therapy_family']);
        }
        if(isset($data['organ_user_therapy_personal_code'])){
            $this->db->set('organ_user_therapy_personal_code', $data['organ_user_therapy_personal_code']);
        }
        if(isset($data['organ_user_therapy_national_code'])){
            $this->db->set('organ_user_therapy_national_code', $data['organ_user_therapy_national_code']);
        }
        if(isset($data['organ_user_therapy_gender_id'])){
            $this->db->set('organ_user_therapy_gender_id', $data['organ_user_therapy_gender_id']);
        }
        if(isset($data['organ_user_therapy_year'])){
            $this->db->set('organ_user_therapy_year', $data['organ_user_therapy_year']);
        }
        if(isset($data['organ_user_therapy_month'])){
            $this->db->set('organ_user_therapy_month', $data['organ_user_therapy_month']);
        }
        if(isset($data['organ_user_therapy_day'])){
            $this->db->set('organ_user_therapy_day', $data['organ_user_therapy_day']);
        }
        if(isset($data['organ_user_therapy_fathername'])){
            $this->db->set('organ_user_therapy_fathername', $data['organ_user_therapy_fathername']);
        }
        if(isset($data['organ_user_therapy_kind_id'])){
            $this->db->set('organ_user_therapy_kind_id', $data['organ_user_therapy_kind_id']);
        }
        if(isset($data['organ_user_therapy_kinship_id'])){
            $this->db->set('organ_user_therapy_kinship_id', $data['organ_user_therapy_kinship_id']);
        }
        if(isset($data['organ_user_therapy_organ_therapycontract_id'])){
            $this->db->set('organ_user_therapy_organ_therapycontract_id', $data['organ_user_therapy_organ_therapycontract_id']);
        }
        if(isset($data['organ_user_therapy_basebime_id'])){
            $this->db->set('organ_user_therapy_basebime_id', $data['organ_user_therapy_basebime_id']);
        }
        if(isset($data['organ_user_therapy_bank_id'])){
            $this->db->set('organ_user_therapy_bank_id', $data['organ_user_therapy_bank_id']);
        }
        if(isset($data['organ_user_therapy_cardno'])){
            $this->db->set('organ_user_therapy_cardno', $data['organ_user_therapy_cardno']);
        }
        if(isset($data['organ_user_therapy_accno'])){
            $this->db->set('organ_user_therapy_accno', $data['organ_user_therapy_accno']);
        }
        if(isset($data['organ_user_therapy_shebano'])){
            $this->db->set('organ_user_therapy_shebano', $data['organ_user_therapy_shebano']);
        }
        if(isset($data['organ_user_therapy_bimeno'])){
            $this->db->set('organ_user_therapy_bimeno', $data['organ_user_therapy_bimeno']);
        }
        if(isset($data['organ_user_therapy_idcardno'])){
            $this->db->set('organ_user_therapy_idcardno', $data['organ_user_therapy_idcardno']);
        }
        if(isset($data['organ_user_therapy_main_national_code'])){
            $this->db->set('organ_user_therapy_main_national_code', $data['organ_user_therapy_main_national_code']);
        }

        $this->db->where('organ_user_therapy_national_code', $organ_user_therapy_national_code);
        return $this->db->update('organ_user_therapy_tb');
    }
}