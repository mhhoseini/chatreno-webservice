<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * B_organ
 *
 * @mixin Eloquent
 */
class B_organ extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    function run_query($sql){
        return $this->db->query($sql)->result_array();
    }

    function run_query_put($sql){
        return $this->db->query($sql);
    }

    function get_organ_by($organ_name){
        $sql="select * from organ_tb where organ_name='".$organ_name."'";
        return $this->db->query($sql)->result_array();
    }

    function get_organ_byid($organ_id){
        $sql="select * from organ_tb where organ_id=".$organ_id;
        return $this->db->query($sql)->result_array();
    }

    function get_organ(){
        $sql="select * from organ_tb where 1";
        return $this->db->query($sql)->result_array();
    }

    function add_organ($organ_name,$organ_username,$organ_agent,$organ_agentmobile,$organ_pass, $organ_tell,$organ_address,$organ_logo,$organ_managemobile){
        $sql ="INSERT INTO organ_tb
        (organ_name, organ_username, organ_pass, organ_tell, organ_agent, organ_agentmobile, organ_address, organ_logo, organ_managemobile)
        VALUES('$organ_name', '$organ_username', '$organ_pass', '$organ_tell', '$organ_agent', '$organ_agentmobile', '$organ_address', '$organ_logo', '$organ_managemobile')";
        return $this->db->query($sql);
    }

    function add_organ_status($organ_id,$organ_status){
        $sql="INSERT INTO organ_status_tb( organ_status_organ_id, organ_status, organ_status_timstamp) VALUES
	          (".$organ_id.", ".$organ_status." ,".time().")";
        return $this->db->query($sql);
    }

    function add_organ_token($organ_token_organ_id ,$organ_token_str, $organ_token_mode, $organ_token_app_version, $organ_token_device_name, $organ_token_device_version, $organ_token_ip){
        $sql="INSERT INTO organ_token_tb( organ_token_organ_id , organ_token_str , organ_token_timestamp , organ_token_mode , organ_token_app_version , organ_token_device_name , organ_token_device_version , organ_token_ip) VALUES
              ( '$organ_token_organ_id'           , '$organ_token_str', now()                  , '$organ_token_mode', '$organ_token_app_version', '$organ_token_device_name', '$organ_token_device_version', '$organ_token_ip')";
        return $this->db->query($sql);
    }

    function all_organ($organ_id){
        $sql="select * from organ_tb where organ_id=".$organ_id;
        return $this->db->query($sql)->result_array();
    }

    function del_organ($organ_id){
        $sql="DELETE FROM organ_tb  where organ_id=".$organ_id;
        return $this->db->query($sql);
    }

    function organ_login($organ_username, $organ_pass){
        $sql="select * from organ_tb where   organ_username='".$organ_username."' AND organ_pass='".$organ_pass."'";
        return $this->db->query($sql)->result_array();
    }

    function organ_login_by($organ_id, $organ_pass){
        $sql="select * from organ_tb where   organ_id='".$organ_id."' AND organ_pass='".$organ_pass."'";
        return $this->db->query($sql)->result_array();
    }

    function get_organ_token($organ_token_str){
        $sql="select * from organ_token_tb where organ_token_str='".$organ_token_str."'  AND (organ_token_logout_timestamp IS NULL OR organ_token_logout_timestamp='') ";
        return $this->db->query($sql)->result_array();
    }

    function update_organ_token($organ_token_str){
        $sql="UPDATE organ_token_tb SET organ_token_logout_timestamp=now() WHERE organ_token_str='".$organ_token_str."'";
        return $this->db->query($sql);
    }

    function update_organ_pass($organ_newpass,$organ_id){
        $sql="UPDATE organ_tb SET organ_pass='".$organ_newpass."' where organ_id=".$organ_id;
        return $this->db->query($sql);
    }

    function get_organ_contract($organ_contract_id){
        $sql = "SELECT * FROM organ_contract_tb where organ_contract_id=".$organ_contract_id;
        return $this->db->query($sql)->result_array();
    }

    function get_organ_confirm_by($organ_confirm_id, $sms_confirm_code){
        $sql = "SELECT * FROM organ_confirm_tb where organ_confirm_id=".$organ_confirm_id." And organ_confirm_sms_code=".$sms_confirm_code;
        return $this->db->query($sql)->result_array();
    }

    function get_organ_contracts($organ_contract_organ_id){
        $sql = "SELECT
        organ_contract_id, organ_contract_fieldinsuranc_id, organ_contract_num,organ_contract_discount_max_amount,
        organ_contract_date, organ_contract_date_start, organ_contract_date_end,
        organ_contract_clearing_day, organ_contract_discount_percent, organ_contract_discount_amount,
        organ_contract_editable, organ_contract_deactive, organ_contract_company_id,
        organ_contract_organ_id, organ_contract_file_id, organ_contract_regdate,organ_contract_employee_id,company_name,fieldinsurance_fa
                FROM organ_contract_tb
                  join company_tb ON company_id=organ_contract_company_id
                  join fieldinsurance_tb ON fieldinsurance_id=organ_contract_fieldinsuranc_id
                where organ_contract_organ_id=".$organ_contract_organ_id;
        return $this->db->query($sql)->result_array();
    }

    function  get_instalmentnopass_permonth($user_id,$organ_id){
        $this->load->helper('time_helper');

        for($i=0;$i<13;$i++){
            $datenow=strtotime(date("d-m-Y",strtotime($i." Months")));
            $Y=jdate('Y',$datenow,"",'','fa');
            $M=jdate('m',$datenow,"",'','fa');
            $datafa=jalali_to_gregorian($Y,$M,1);

            $date = date_create();
            date_date_set($date, $datafa[0], $datafa[1], $datafa[2]);
            $arrdate[]= date_format($date, 'Y-m-d 00:00:00');
        }
        for($i=0;$i<12;$i++) {
            $query67= "SELECT DISTINCT SUM( instalment_check_tb.instalment_check_amount) as sumamount_check FROM instalment_check_tb,instalment_conditions_tb,request_tb,organ_request_tb,organ_contract_tb WHERE
                                 instalment_check_request_id=request_id AND
                                 request_id=organ_request_request_id AND
                                 organ_request_contract_id=organ_contract_id AND
                                 instalment_conditions_mode_id=2 AND
                                instalment_check_pass=0 AND 
                                 instalment_conditions_id=instalment_check_condition_id AND
                                 instalment_check_date>= '".$arrdate[$i]."' AND
                                 instalment_check_date< '".$arrdate[$i+1]."' AND
                                 request_last_state_id>9 AND
                                 request_user_id=".$user_id." AND
                                 organ_contract_organ_id=$organ_id";

            $result67= $this->db->query($query67)->result_array();

            if(!empty($result67)&&count($result67[0])!=0&&$result67[0]['sumamount_check']!=null) {
                $record[]=intval($result67[0]['sumamount_check']);
            }else{
                $record[]=0;
            }
        }
        return $record;
    }
    function get_organ_user($user_id){
        //must be add join table
        $sql = "SELECT * FROM organ_user_tb where organ_user_user_id=".$user_id;
        return $this->db->query($sql)->result_array();
    }

    function get_organ_instalment($instalment_condition_contract_id){
        $sql = "SELECT * FROM instalment_conditions_tb,instalment_mode_tb where instalment_conditions_tb.instalment_conditions_mode_id=instalment_mode_tb.instalment_mode_mode_id AND instalment_condition_contract_id=".$instalment_condition_contract_id;
        return $this->db->query($sql)->result_array();
    }

    function get_organ_therapycontract_conditions_contract($organ_therapycontract_conditions_contract_id){
        $sql = "SELECT * FROM organ_therapycontract_conditions_tb,organ_therapycontract_conditions_covarage_tb where organ_therapycontract_conditions_tb.organ_therapycontract_conditions_tc_c_covarage_id=organ_therapycontract_conditions_covarage_tb.organ_therapycontract_conditions_covarage_id AND organ_therapycontract_conditions_contract_id=".$organ_therapycontract_conditions_contract_id;
        return $this->db->query($sql)->result_array();
    }

    function add_organ_user($organ_user_organ_id,$user_id,$commitment_amount,$commitment_num,$confirm_id,$user_personal_code){
        $sql="INSERT INTO organ_user_tb
        (organ_user_organ_id,organ_user_user_id, organ_user_commitment_amount, organ_user_commitment_num, organ_user_confirm_id,organ_user_personal_code) VALUES($organ_user_organ_id,$user_id,'$commitment_amount',$commitment_num,$confirm_id,'$user_personal_code');";
        return $this->db->query($sql);
    }

    function add_organ_instalment_temp($instalment_check_organ_temp_check_id,$instalment_check_organ_temp_confirm_id){
        $sql="INSERT INTO instalment_check_organ_temp_tb
        (instalment_check_organ_temp_check_id,instalment_check_organ_temp_confirm_id) VALUES($instalment_check_organ_temp_check_id,$instalment_check_organ_temp_confirm_id);";
        return $this->db->query($sql);
    }

    function modify_organ_user($organ_user_organ_id,$user_id,$commitment_amount,$commitment_num,$confirm_id){
        $sql="Update organ_user_tb
        set organ_user_organ_id=$organ_user_organ_id,organ_user_commitment_amount=$commitment_amount , organ_user_commitment_num=$commitment_num, organ_user_confirm_id=$confirm_id where organ_user_user_id=".$user_id;
        return $this->db->query($sql);
    }

    function del_organ_user($user_id,$organ_user_organ_id){
        $sql = "Delete from organ_user_tb where organ_user_user_id=".$user_id." and organ_user_organ_id=".$organ_user_organ_id ;
        return $this->db->query($sql);
    }

    function add_confirm($sms){
        $sql ="INSERT INTO organ_confirm_tb
                  (organ_confitm_sms_send_date, organ_confirm_sms_code)
                  VALUES(now(), $sms)";
        return $this->db->query($sql);
    }

    function check_confirm($sms_confirm_code, $token,$sms_confirm_date_start,$sms_confirm_date_end){
        $sql = "UPDATE organ_confirm_tb
                    SET organ_confirm_sms_recive_date=now(),organ_confirm_token='".$token."',sms_confirm_date_start='".$sms_confirm_date_start."',sms_confirm_date_end='".$sms_confirm_date_end."'
                    WHERE organ_confirm_sms_code =".$sms_confirm_code." And last_insert_id(organ_confirm_id)";
        // AND diff(organ_confitm_sms_send_date,now()<3600)
        return $this->db->query($sql);
    }

    function add_organ_contract($organ_contract_fieldinsuranc_id, $organ_contract_num, $organ_contract_date, $organ_contract_date_start, $organ_contract_date_end, $organ_contract_clearing_day, $organ_contract_discount_percent, $organ_contract_discount_amount, $organ_contract_editable, $organ_contract_discount_max_amount, $organ_contract_deactive, $organ_contract_company_id, $organ_contract_organ_id, $organ_contract_file_id,$organ_contract_employee_id){
        $sql ="INSERT INTO organ_contract_tb
                (organ_contract_fieldinsuranc_id, organ_contract_num, organ_contract_date, organ_contract_date_start, organ_contract_date_end, organ_contract_clearing_day, organ_contract_discount_percent, organ_contract_discount_amount, organ_contract_editable, organ_contract_discount_max_amount, organ_contract_deactive, organ_contract_company_id, organ_contract_organ_id, organ_contract_file_id,organ_contract_regdate,organ_contract_employee_id )
                VALUES($organ_contract_fieldinsuranc_id, '$organ_contract_num',' $organ_contract_date', '$organ_contract_date_start',' $organ_contract_date_end', $organ_contract_clearing_day, $organ_contract_discount_percent, $organ_contract_discount_amount, $organ_contract_editable, $organ_contract_discount_max_amount, $organ_contract_deactive, $organ_contract_company_id, $organ_contract_organ_id, '$organ_contract_file_id',now(),$organ_contract_employee_id)";
        return $this->db->query($sql);
    }

    function add_organ_therapycontract($organ_therapycontract_state_id, $organ_therapycontract_num, $organ_therapycontract_date, $organ_therapycontract_date_start, $organ_therapycontract_date_end, $organ_therapycontract_clearing_day, $organ_therapycontract_discount_percent, $organ_therapycontract_discount_amount, $organ_therapycontract_editable, $organ_therapycontract_discount_max_amount, $organ_therapycontract_deactive, $organ_therapycontract_company_id, $organ_therapycontract_organ_id, $organ_therapycontract_file_id,$organ_therapycontract_employee_id,$organ_therapycontract_city_id){
        $sql ="INSERT INTO organ_therapycontract_tb
                (organ_therapycontract_state_id, organ_therapycontract_num, organ_therapycontract_date, organ_therapycontract_date_start, organ_therapycontract_date_end, organ_therapycontract_clearing_day, organ_therapycontract_discount_percent, organ_therapycontract_discount_amount, organ_therapycontract_editable, organ_therapycontract_discount_max_amount, organ_therapycontract_deactive, organ_therapycontract_company_id, organ_therapycontract_organ_id, organ_therapycontract_file_id,organ_therapycontract_regdate,organ_therapycontract_employee_id   ,    organ_therapycontract_city_id)
                VALUES($organ_therapycontract_state_id, '$organ_therapycontract_num',' $organ_therapycontract_date', '$organ_therapycontract_date_start',' $organ_therapycontract_date_end', $organ_therapycontract_clearing_day, $organ_therapycontract_discount_percent, $organ_therapycontract_discount_amount, $organ_therapycontract_editable, $organ_therapycontract_discount_max_amount, $organ_therapycontract_deactive, $organ_therapycontract_company_id, $organ_therapycontract_organ_id, '$organ_therapycontract_file_id',now(),$organ_therapycontract_employee_id,$organ_therapycontract_city_id)";
        return $this->db->query($sql);
    }

    function add_organ_therapycontract_conditions($organ_therapycontract_conditions_contract_id, $organ_therapycontract_conditions_percent,  $organ_therapycontract_conditions_mode_id,$organ_therapycontract_conditions_employee_id,$organ_therapycontract_conditions_desc){
        $sql ="INSERT INTO organ_therapycontract_conditions_tb
                      (organ_therapycontract_conditions_contract_id,organ_therapycontract_conditions_amount,  organ_therapycontract_conditions_tc_c_covarage_id, organ_therapycontract_conditions_employee_id,organ_therapycontract_conditions_desc)
                VALUES($organ_therapycontract_conditions_contract_id, $organ_therapycontract_conditions_percent, $organ_therapycontract_conditions_mode_id, $organ_therapycontract_conditions_employee_id,'$organ_therapycontract_conditions_desc')";
        return $this->db->query($sql);
    }


    function add_contract_instalment($instalment_condition_contract_id, $instalment_conditions_percent, $instalment_conditions_date, $instalment_conditions_mode_id,$instalment_condition_employee_id,$instalment_conditions_desc){
        $sql ="INSERT INTO instalment_conditions_tb
                      (instalment_condition_contract_id,instalment_conditions_percent, instalment_conditions_date, instalment_conditions_mode_id, instalment_condition_employee_id,instalment_conditions_desc)
                VALUES($instalment_condition_contract_id, $instalment_conditions_percent, $instalment_conditions_date,$instalment_conditions_mode_id, $instalment_condition_employee_id,'$instalment_conditions_desc')";
        return  $this->db->query($sql);
    }

    function get_organs_by_user($user_id){
        $sql = "SELECT DISTINCT organ_tb.* FROM organ_tb
            join organ_user_tb     on     organ_user_organ_id    = organ_id
            where organ_user_user_id=".$user_id;
        return $this->db->query($sql)->result_array();
    }

    function get_therapycontracts(){
        $sql="select * from organ_therapycontract_tb
              join organ_tb ON organ_id=organ_therapycontract_organ_id
             where 1 ";
        return $this->db->query($sql)->result_array();
    }

    function get_contracts(){
        $sql="select * from organ_contract_tb
              join organ_tb ON organ_id=organ_contract_organ_id
              join instalment_round_tb ON instalment_round_id=organ_contract_instalment_round_id
              where 1 ";
        return $this->db->query($sql)->result_array();
    }
    function get_contracts_byorgan($organ_contract_organ_id){
        $sql="select * from organ_contract_tb
              join organ_tb ON organ_id=organ_contract_organ_id
              where organ_contract_organ_id=$organ_contract_organ_id ";
        return $this->db->query($sql)->result_array();
    }

    function get_therapycontract_byorgan($organ_therapycontract_organ_id){
        $sql="select * from organ_therapycontract_tb
              join organ_tb ON organ_id=organ_therapycontract_organ_id
              where organ_therapycontract_organ_id=$organ_therapycontract_organ_id ";
        return $this->db->query($sql)->result_array();
    }

    function get_organ_confirms($organ_id){
        $sql="select * from organ_user_tb
              join organ_confirm_tb    ON organ_user_confirm_id = organ_confirm_id
              Join organ_tb            ON organ_user_organ_id   = organ_id
              where organ_user_organ_id=".$organ_id;
        return $this->db->query($sql)->result_array();
    }

    function get_distinct_organ_confirms($organ_id){
        $sql="select DISTINCT * from organ_confirm_tb WHERE organ_confirm_id in(
              SELECT organ_user_confirm_id FROM organ_user_tb WHERE organ_user_organ_id=".$organ_id.") ORDER BY organ_confirm_id ASC";
        return $this->db->query($sql)->result_array();
    }

function get_clearing_confirm_check($instalment_check_organ_temp_confirm_id){
        $sql="select DISTINCT instalment_check_organ_temp_check_id from instalment_check_organ_temp_tb WHERE instalment_check_organ_temp_confirm_id =".$instalment_check_organ_temp_confirm_id;
        return $this->db->query($sql)->result_array();
    }


    function get_clearing_organ_confirms($organ_id){
        $sql="SELECT DISTINCT organ_confirm_id,organ_confirm_sms_recive_date,sms_confirm_date_start,sms_confirm_date_end 
FROM organ_confirm_tb,instalment_check_organ_temp_tb,instalment_check_tb,instalment_conditions_tb,organ_contract_tb 
WHERE instalment_check_organ_temp_confirm_id=organ_confirm_id AND instalment_check_id=instalment_check_organ_temp_check_id AND instalment_conditions_id=instalment_check_condition_id AND instalment_condition_contract_id=organ_contract_id AND organ_contract_organ_id= ".$organ_id." ORDER BY organ_confirm_id ASC";
        return $this->db->query($sql)->result_array();
    }

	function get_user_organ($organ_id,$organ_confirm_id){
        $sql="
		SELECT a.*,c.*,d.*
FROM organ_user_tb a
INNER JOIN (
   SELECT organ_user_user_id, MAX(organ_user_confirm_id) AS max_confirm_id
FROM organ_user_tb
WHERE organ_user_organ_id=$organ_id and organ_user_confirm_id<=$organ_confirm_id
GROUP by organ_user_user_id
) b ON a.organ_user_confirm_id = b.max_confirm_id AND a.organ_user_user_id = b.organ_user_user_id AND a.organ_user_organ_id=$organ_id
INNER JOIN user_tb c
ON c.user_id=a.organ_user_user_id
INNER JOIN organ_confirm_tb d
ON a.organ_user_confirm_id=d.organ_confirm_id";
        return $this->db->query($sql)->result_array();
    }

    function get_one_user_organ($organ_id,$organ_user_user_id){
        $sql="
		SELECT a.*,c.*,d.*
FROM organ_user_tb a
INNER JOIN (
   SELECT organ_user_user_id, MAX(organ_user_confirm_id) AS max_confirm_id
FROM organ_user_tb
WHERE organ_user_organ_id=$organ_id and organ_user_user_id=$organ_user_user_id and organ_user_confirm_id<=100000000
GROUP by organ_user_user_id
) b ON a.organ_user_confirm_id = b.max_confirm_id AND a.organ_user_user_id = b.organ_user_user_id AND a.organ_user_organ_id=$organ_id
INNER JOIN user_tb c
ON c.user_id=a.organ_user_user_id
INNER JOIN organ_confirm_tb d
ON a.organ_user_confirm_id=d.organ_confirm_id";
        return $this->db->query($sql)->result_array();
    }
    function get_instalment_mode(){
        $sql="select * from instalment_mode_tb
              where 1 ";
        return $this->db->query($sql)->result_array();
    }

    function get_organ_therapycontract_conditions_covarage(){
        $sql="select * from organ_therapycontract_conditions_covarage_tb
              where 1 ";
        return $this->db->query($sql)->result_array();
    }

    function get_fieldinsurance_name($fieldinsurance_id){
        $sql = "SELECT fieldinsurance_fa,fieldinsurance_logo_url
                FROM fieldinsurance_tb where fieldinsurance_id=".$fieldinsurance_id;
        return $this->db->query($sql)->result_array()[0];
    }

    function get_company_name($company_id){
        $sql = "SELECT company_name,company_logo_url
                FROM company_tb where company_id=".$company_id;
        return $this->db->query($sql)->result_array()[0];
    }

    function get_covarage_used($damagefile_user_therapy_id,$organ_therapycontract_conditions_covarage_id){
        $sql = "select SUM(damagefile_ready_expert_estimate) AS sumprice from damagefile_tb,damagefile_ready_tb,fielddamagefile_tb,
organ_therapycontract_conditions_covarage_tb,organ_user_therapy_tb
where damagefile_id=damagefile_ready_damagefile_id
AND damagefile_fielddamagefile_id=fielddamagefile_id
AND fielddamagefile_organ_therapycontract_conditions_covarage_id=organ_therapycontract_conditions_covarage_id
AND damagefile_user_therapy_id=$damagefile_user_therapy_id
AND organ_therapycontract_conditions_covarage_id=$organ_therapycontract_conditions_covarage_id
AND organ_user_therapy_id=damagefile_user_therapy_id
AND organ_user_therapy_organ_therapycontract_id=damagefile_therapycontract_id
GROUP BY organ_therapycontract_conditions_covarage_id";
        return $this->db->query($sql)->result_array()[0];
    }

    function get_confirm_by_token($token){
        $sql = "SELECT organ_confirm_id
                FROM organ_confirm_tb where organ_confirm_token='$token'";
        return $this->db->query($sql)->result_array();
    }

    function check_organ_confirm_exist($user_id,$confirm_id){
        $sql = "SELECT organ_user_confirm_id
                FROM organ_user_tb where organ_user_user_id=$user_id AND organ_user_confirm_id=$confirm_id";
        $result = $this->db->query($sql)->result_array();
        if(!empty($result))
            return true;
        else
            return false;
    }

function check_organ_user_exist($user_id, $organ_user_organ_id){
    $sql = "SELECT organ_user_confirm_id
                FROM organ_user_tb where organ_user_user_id=$user_id AND organ_user_organ_id=$organ_user_organ_id";
    $result = $this->db->query($sql)->result_array();
    if(!empty($result))
        return true;
    else
        return false;
}

}
