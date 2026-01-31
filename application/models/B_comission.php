<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * B_comission
 *
 * @mixin Eloquent
 */
class B_comission extends CI_Model {

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

    function get_fieldinsurance($marketer_commission_defult_fieldinsurance_id){
        $sql="select * from fieldinsurance_tb where fieldinsurance_id='".$marketer_commission_defult_fieldinsurance_id."'";
        return $this->db->query($sql)->result_array();
    }

    public function add_marketer_commission_defult($marketer_commission_defult_percent,$marketer_commission_defult_wage, $marketer_commission_defult_fieldinsurance_id,$marketer_commission_defult_mode_id){
        $sql="INSERT INTO marketer_commission_defult_tb( marketer_commission_defult_percent,marketer_commission_defult_wage, marketer_commission_defult_fieldinsurance_id,marketer_commission_defult_mode_id)
	                      VALUES ( '$marketer_commission_defult_percent','$marketer_commission_defult_wage', '$marketer_commission_defult_fieldinsurance_id',$marketer_commission_defult_mode_id);";
        return $this->db->query($sql);
    }

    public function get_marketer_commission_defult($marketer_commission_defult_mode_id,$marketer_commission_defult_fieldinsurance_id){
        $sql="select * from marketer_commission_defult_tb where marketer_commission_defult_mode_id=".$marketer_commission_defult_mode_id." marketer_commission_defult_fieldinsurance_id='".$marketer_commission_defult_fieldinsurance_id."'";
        return $this->db->query($sql)->result_array();
    }

    public function get_marketer_commission($marketer_commission_marketer_id,$marketer_commission_fieldinsurance_id){
        $sql="select * from marketer_commission_tb where marketer_commission_marketer_id=$marketer_commission_marketer_id AND marketer_commission_fieldinsurance_id='".$marketer_commission_fieldinsurance_id."'";
        return $this->db->query($sql)->result_array();
    }

    function add_marketer_commission($marketer_commission_marketer_id, $marketer_commission_percent,$marketer_commission_desc, $marketer_commission_fieldinsurance_id){
        $sql="INSERT INTO marketer_commission_tb(marketer_commission_marketer_id, marketer_commission_percent,marketer_commission_desc, marketer_commission_fieldinsurance_id)
	                      VALUES ( $marketer_commission_marketer_id, '$marketer_commission_percent','$marketer_commission_desc', '$marketer_commission_fieldinsurance_id')";
        return $this->db->query($sql);
    }

    function get_leader_commission_defult($leader_commission_defult_fieldinsurance_id){
        $sql="select * from leader_commission_defult_tb where leader_commission_defult_fieldinsurance_id='".$leader_commission_defult_fieldinsurance_id."'";
        return $this->db->query($sql)->result_array();
    }

    function add_leader_commission_defult($leader_commission_defult_percent ,$leader_commission_defult_wage , $leader_commission_defult_fieldinsurance_id){
        $sql="INSERT INTO leader_commission_defult_tb( leader_commission_defult_percent,leader_commission_defult_wage, leader_commission_defult_fieldinsurance_id)
	                 VALUES ( '$leader_commission_defult_percent','$leader_commission_defult_wage', '$leader_commission_defult_fieldinsurance_id');";
        return $this->db->query($sql);
    }

    function tog_leader_commission_defult(){
        $sql="select * from leader_commission_defult_tb,fieldinsurance_tb where leader_commission_defult_fieldinsurance_id=fieldinsurance_id ";
        return $this->db->query($sql)->result_array();
    }

    function get_marketer_mode_by($marketer_mode_name, $marketer_mode_namefa){
        $sql="select * from marketer_mode_tb where marketer_mode_name='".$marketer_mode_name."' marketer_mode_namefa='".$marketer_mode_namefa."'";
        return $this->db->query($sql)->result_array();
    }

    function add_marketer_mode($marketer_mode_id, $marketer_mode_name, $marketer_mode_namefa,$marketer_mode_color){
        $sql="INSERT INTO marketer_mode_tb(marketer_mode_id, marketer_mode_name, marketer_mode_namefa,marketer_mode_color)
	                            VALUES ($marketer_mode_id, '$marketer_mode_name', '$marketer_mode_namefa','$marketer_mode_color');";
        return $this->db->query($sql);
    }

    function all_marketer_mode(){
        $sql="select * from marketer_mode_tb where 1 ";
        return $this->db->query($sql)->result_array();
    }

    function del_marketer_mode($marketer_mode_id){
        $sql="DELETE FROM marketer_mode_tb  where marketer_mode_id=".$marketer_mode_id."";
        return $this->db->query($sql);
    }

}