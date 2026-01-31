<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * B_agent
 *
 * @mixin Eloquent
 */
class B_agent extends CI_Model {

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

    function get_agent_by($agent_code,$agent_name){
        $sql="select * from agent_tb where agent_code='".$agent_code."' AND agent_name='".$agent_name."'";
        return $this->db->query($sql)->result_array();
    }

    function get_agent_byid($agent_id){
        $sql="select * from agent_status_tb where agent_status_agent_id=".$agent_id." ORDER BY agent_status_id DESC LIMIT 1 ";
        return $this->db->query($sql)->result_array();
    }

    function get_agent_bymobile($agent_mobile){
        $sql="select * from agent_tb where agent_mobile=".$agent_mobile;
        return $this->db->query($sql)->result_array();
    }

    function get_agent_status($agent_id){
        $sql="select * from agent_status_tb where agent_status_agent_id=".$agent_id." ORDER BY agent_status_id DESC LIMIT 1 ";
        return $this->db->query($sql)->result_array();
    }

    function add_agent($agent_code,$agent_name,$agent_family,$agent_gender,$agent_mobile,$agent_pass, $agent_tell,$agent_email,$agent_required_phone,$agent_address, $agent_state_id, $agent_city_id,$agent_sector_name,$agent_long,$agent_lat,$agent_banknum,$agent_bankname,$agent_banksheba,$agent_image_code,$agent_deactive, $agent_company_id){
        $sql="INSERT INTO agent_tb(agent_code,   agent_name,  agent_family,   agent_gender,  agent_mobile,    agent_pass ,   agent_tell,   agent_email,   agent_required_phone,   agent_address,   agent_state_id,  agent_city_id,  agent_sector_name,  agent_long  ,  agent_lat  ,  agent_banknum ,  agent_bankname , agent_banksheba ,  agent_image_code,  agent_deactive ,  agent_company_id ,agent_register_date)
	           VALUES ('$agent_code','$agent_name','$agent_family','$agent_gender','$agent_mobile','$agent_pass', '$agent_tell','$agent_email','$agent_required_phone','$agent_address', $agent_state_id, $agent_city_id,'$agent_sector_name','$agent_long','$agent_lat','$agent_banknum','$agent_bankname','$agent_banksheba','$agent_image_code',$agent_deactive, $agent_company_id,now());";
        return $this->db->query($sql);
    }

    function add_agent_status($agent_id,$agent_status){
        $sql="INSERT INTO agent_status_tb( agent_status_agent_id, agent_status, agent_status_timstamp) VALUES
	          (".$agent_id.", ".$agent_status." ,".time().")";
        return $this->db->query($sql);
    }

    function add_agent_token($agent_token_agent_id ,$agent_token_str, $agent_token_mode, $agent_token_app_version, $agent_token_device_name, $agent_token_device_version, $agent_token_ip,$agent_token_employee_id){
        $sql="INSERT INTO agent_token_tb( agent_token_agent_id , agent_token_str , agent_token_timestamp , agent_token_mode , agent_token_app_version , agent_token_device_name , agent_token_device_version , agent_token_ip,agent_token_employee_id) VALUES
              ( '$agent_token_agent_id'           , '$agent_token_str', now()                  , '$agent_token_mode', '$agent_token_app_version', '$agent_token_device_name', '$agent_token_device_version', '$agent_token_ip',$agent_token_employee_id)";
        return $this->db->query($sql);
    }

    function all_agent($agent_id){
        $sql="select * from agent_tb,state_tb,city_tb,company_tb where company_id=agent_company_id AND city_id=agent_city_id AND state_id=agent_state_id AND agent_id=".$agent_id;
        return $this->db->query($sql)->result_array();
    }

    function del_agent($agent_id){
        $sql="DELETE FROM agent_tb  where agent_id=".$agent_id;
        return $this->db->query($sql);
    }

    function agent_login($agent_mobile, $agent_pass){
        $sql="select * from agent_tb where   agent_mobile='".$agent_mobile."' AND agent_pass='".$agent_pass."'";
        return $this->db->query($sql)->result_array();
    }

    function agent_login_by($agent_id, $agent_pass){
        $sql="select * from agent_tb where   agent_id='".$agent_id."' AND agent_pass='".$agent_pass."'";
        return $this->db->query($sql)->result_array();
    }

    function get_agent_token($agent_token_str){
        $sql="select * from agent_token_tb where agent_token_str='".$agent_token_str."'  AND (agent_token_logout_timestamp IS NULL OR agent_token_logout_timestamp='') ";
        return $this->db->query($sql)->result_array();
    }

    function update_agent_token($agent_token_str){
        $sql="UPDATE agent_token_tb SET agent_token_logout_timestamp=now() WHERE agent_token_str='".$agent_token_str."'";
        return $this->db->query($sql);
    }

    function update_agent_pass($agent_newpass,$agent_id){
        $sql="UPDATE agent_tb SET agent_pass='".$agent_newpass."' where agent_id=".$agent_id;
        return $this->db->query($sql);
    }
}