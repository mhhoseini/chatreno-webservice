<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * B_employeelogin
 *
 * @mixin Eloquent
 */
class B_employeelogin extends CI_Model {

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

    function del_employee_permision($emloyee_permision_id){
        $sql = "DELETE FROM emloyee_permision_tb  where emloyee_permision_id=" . $emloyee_permision_id . "";
        return $this->db->query($sql);
    }
}