<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * B_employee
 *
 * @mixin Eloquent
 */
class B_employee extends CI_Model {

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

    function get_employee_by_mobile($employee_mobile){
        $sql="select * from employee_tb where  employee_mobile='" .$employee_mobile. "'";
        return $this->db->query($sql)->result_array();
    }

    function all_permision_entity(){
        $sql="select * from thirdparty_discnt_driver_tb where 1 ORDER BY thirdparty_discnt_driver_id ASC";
        return $this->db->query($sql)->result_array();
    }

    function all_permision_mode(){
        $sql="select * from permision_mode_tb where 1 ORDER BY permision_mode_id ASC";
        return $this->db->query($sql)->result_array();
    }

    function add_employee($employee_name,$employee_family,$employee_mobile,$employee_pass,$employee_email,$employee_deactive,$employee_image_code){
         $sql = "INSERT INTO employee_tb( employee_name,  employee_family , employee_mobile, employee_pass , employee_email  ,employee_deactive, employee_image_code ,employee_register_date)
	                    VALUES ('$employee_name','$employee_family','$employee_mobile','$employee_pass'      ,'$employee_email'    ,$employee_deactive  ,'$employee_image_code',now());";
        return $this->db->query($sql);
    }

    function del_employee($employee_id){
        $sql = "DELETE FROM employee_tb  where employee_id=" . $employee_id . "";
        return $this->db->query($sql);
    }

    function get_permision_entity(){
        $sql = "select * from permision_entity_tb where 1 ";
        return $this->db->query($sql)->result_array();
    }

    function get_permision_entity_by($emloyee_permision_emloyee_id,$emloyee_permision_entity,$emloyee_permision_mode){
        $sql = "select * from emloyee_permision_tb where  emloyee_permision_emloyee_id=" . $emloyee_permision_emloyee_id . " AND emloyee_permision_entity='" . $emloyee_permision_entity . "' AND emloyee_permision_mode='" . $emloyee_permision_mode . "'";
        return $this->db->query($sql)->result_array();
    }

    function add_employee_permision($emloyee_permision_emloyee_id,$emloyee_permision_entity,$emloyee_permision_mode){
        $sql = "INSERT INTO emloyee_permision_tb(  emloyee_permision_emloyee_id,  emloyee_permision_entity,   emloyee_permision_mode)
	            VALUES ( $emloyee_permision_emloyee_id,'$emloyee_permision_entity','$emloyee_permision_mode');";
        return $this->db->query($sql);
    }

    function del_employee_permision($emloyee_permision_id){
        $sql = "DELETE FROM emloyee_permision_tb  where emloyee_permision_id=" . $emloyee_permision_id . "";
        return $this->db->query($sql);
    }

    function employee_login_by($employee_mobile,$employee_pass){
        $sql="select * from employee_tb where   employee_mobile='".$employee_mobile."' AND employee_pass='".$employee_pass."'";
        return $this->db->query($sql)->result_array();
    }

    function employee_login($employee_id,$employee_pass){
        $sql="select * from employee_tb where   employee_id=".$employee_id." AND employee_pass='".$employee_pass."'";
        return $this->db->query($sql)->result_array();
    }

    function get_employee($employee_id){
        $sql="select * from employee_tb where employee_id=".$employee_id;
        return $this->db->query($sql)->result_array();
    }

    function add_employee_token($employee_token_employee_id , $employee_token_str,  $employee_token_mode, $employee_token_app_version, $employee_token_device_name, $employee_token_device_version, $employee_token_ip){
        $sql="INSERT INTO employee_token_tb( `employee_token_employee_id` , `employee_token_str` , `employee_token_timestamp` , `employee_token_mode` , `employee_token_app_version` , `employee_token_device_name` , `employee_token_device_version` , `employee_token_ip`) VALUES
                                  ( '$employee_token_employee_id'  , '$employee_token_str', now() , '$employee_token_mode', '$employee_token_app_version', '$employee_token_device_name', '$employee_token_device_version', '$employee_token_ip')";
        return $this->db->query($sql);
    }

    function employee_token($employee_token_str){
        $sql="select * from employee_token_tb where employee_token_str='".$employee_token_str."'  AND (employee_token_logout_timestamp IS NULL OR employee_token_logout_timestamp='') ";
        return $this->db->query($sql)->result_array();
    }

    function update_employee_pass($employeeid ,$employee_newpass){
        $sql="UPDATE employee_tb SET employee_pass='" .$employee_newpass."' where employee_id=".$employeeid;
        return $this->db->query($sql);
    }

    function update_employee_token($employee_token_str){
        $sql="UPDATE employee_token_tb SET employee_token_logout_timestamp=now() WHERE employee_token_str='".$employee_token_str."'";
        return $this->db->query($sql);
    }
}