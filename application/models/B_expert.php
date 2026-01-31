<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * B_expert
 *
 * @mixin Eloquent
 */
class B_expert extends CI_Model {

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

    function get_expert_by($expert_code,$expert_name){
        $sql="select * from expert_tb where expert_code='".$expert_code."' AND expert_name='".$expert_name."'";
        return $this->db->query($sql)->result_array();
    }

    function get_expert_byid($expert_id){
        $sql="select * from expert_status_tb where expert_status_expert_id=".$expert_id." ORDER BY expert_status_id DESC LIMIT 1 ";
        return $this->db->query($sql)->result_array();
    }

    function get_expert_bymobile($expert_mobile){
        $sql="select * from expert_tb where expert_mobile=".$expert_mobile;
        return $this->db->query($sql)->result_array();
    }

    function get_expert_status($expert_id){
        $sql="select * from expert_status_tb where expert_status_expert_id=".$expert_id." ORDER BY expert_status_id DESC LIMIT 1 ";
        return $this->db->query($sql)->result_array();
    }

    function add_expert($expert_code,$expert_name,$expert_family,$expert_gender,$expert_mobile,$expert_pass, $expert_tell,$expert_email,$expert_required_phone,$expert_address, $expert_state_id, $expert_city_id,$expert_sector_name,$expert_long,$expert_lat,$expert_banknum,$expert_bankname,$expert_banksheba,$expert_image_code,$expert_deactive, $expert_evaluatorco_id){
        $sql="INSERT INTO expert_tb(expert_code,   expert_name,  expert_family,   expert_gender,  expert_mobile,    expert_pass ,   expert_tell,   expert_email,   expert_required_phone,   expert_address,   expert_state_id,  expert_city_id,  expert_sector_name,  expert_long  ,  expert_lat  ,  expert_banknum ,  expert_bankname , expert_banksheba ,  expert_image_code,  expert_deactive ,  expert_evaluatorco_id ,expert_register_date)
	           VALUES ('$expert_code','$expert_name','$expert_family','$expert_gender','$expert_mobile','$expert_pass', '$expert_tell','$expert_email','$expert_required_phone','$expert_address', $expert_state_id, $expert_city_id,'$expert_sector_name','$expert_long','$expert_lat','$expert_banknum','$expert_bankname','$expert_banksheba','$expert_image_code',$expert_deactive, $expert_evaluatorco_id,now());";
        return $this->db->query($sql);
    }

    function add_expert_status($expert_id,$expert_status){
        $sql="INSERT INTO expert_status_tb( expert_status_expert_id, expert_status, expert_status_timstamp) VALUES
	          (".$expert_id.", ".$expert_status." ,".time().")";
        return $this->db->query($sql);
    }

    function add_expert_token($expert_token_expert_id ,$expert_token_str, $expert_token_mode, $expert_token_app_version, $expert_token_device_name, $expert_token_device_version, $expert_token_ip,$expert_token_employee_id){
        $sql="INSERT INTO expert_token_tb( expert_token_expert_id , expert_token_str , expert_token_timestamp , expert_token_mode , expert_token_app_version , expert_token_device_name , expert_token_device_version , expert_token_ip,expert_token_employee_id) VALUES
              ( '$expert_token_expert_id'           , '$expert_token_str', now()                  , '$expert_token_mode', '$expert_token_app_version', '$expert_token_device_name', '$expert_token_device_version', '$expert_token_ip',$expert_token_employee_id)";
        return $this->db->query($sql);
    }

    function all_expert($expert_id){
        $sql="select * from expert_tb,state_tb,city_tb,evaluatorco_tb where evaluatorco_id=expert_evaluatorco_id AND city_id=expert_city_id AND state_id=expert_state_id AND expert_id=".$expert_id;
        return $this->db->query($sql)->result_array();
    }

    function del_expert($expert_id){
        $sql="DELETE FROM expert_tb  where expert_id=".$expert_id;
        return $this->db->query($sql);
    }

    function expert_login($expert_mobile, $expert_pass){
        $sql="select * from expert_tb where   expert_mobile='".$expert_mobile."' AND expert_pass='".$expert_pass."'";
        return $this->db->query($sql)->result_array();
    }

    function expert_login_by($expert_id, $expert_pass){
        $sql="select * from expert_tb where   expert_id='".$expert_id."' AND expert_pass='".$expert_pass."'";
        return $this->db->query($sql)->result_array();
    }

    function get_expert_token($expert_token_str){
        $sql="select * from expert_token_tb where expert_token_str='".$expert_token_str."'  AND (expert_token_logout_timestamp IS NULL OR expert_token_logout_timestamp='') ";
        return $this->db->query($sql)->result_array();
    }

    function update_expert_token($expert_token_str){
        $sql="UPDATE expert_token_tb SET expert_token_logout_timestamp=now() WHERE expert_token_str='".$expert_token_str."'";
        return $this->db->query($sql);
    }

    function update_expert_pass($expert_newpass,$expert_id){
        $sql="UPDATE expert_tb SET expert_pass='".$expert_newpass."' where expert_id=".$expert_id;
        return $this->db->query($sql);
    }
}