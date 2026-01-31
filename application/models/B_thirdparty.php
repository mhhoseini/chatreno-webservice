<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * B_thirdparty
 *
 * @mixin Eloquent
 */
class B_thirdparty extends CI_Model {

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

    function get_image($user_national_image_code){
        $sql="SELECT * FROM image_tb WHERE image_code='".$user_national_image_code."'";
        return $this->db->query($sql)->result_array();
    }

    function get_discount(){
        $sql="select * from thirdparty_discnt_thirdparty_tb where 1 ORDER BY thirdparty_discnt_thirdparty_id ASC";
        return $this->db->query($sql)->result_array();
    }

    function get_discount_by_thirdparty($thirdparty_discnt_thirdparty_name){
        $sql="select * from thirdparty_discnt_thirdparty_tb where thirdparty_discnt_thirdparty_name='".$thirdparty_discnt_thirdparty_name;
        return $this->db->query($sql)->result_array();
    }

    function add_discount($thirdparty_discnt_thirdparty_id,$thirdparty_discnt_thirdparty_name, $thirdparty_discnt_thirdparty_digt){
        $sql="INSERT INTO thirdparty_discnt_thirdparty_tb(thirdparty_discnt_thirdparty_id, thirdparty_discnt_thirdparty_name, thirdparty_discnt_thirdparty_digt)
	                            VALUES( $thirdparty_discnt_thirdparty_id,'$thirdparty_discnt_thirdparty_name', '$thirdparty_discnt_thirdparty_digt')";
        return $this->db->query($sql);
    }

    function del_discount($thirdparty_discnt_thirdparty_id){
        $sql="DELETE FROM thirdparty_discnt_thirdparty_tb  where thirdparty_discnt_thirdparty_id=".$thirdparty_discnt_thirdparty_id."";
        return $this->db->query($sql);
    }

    function all_discnt_driver(){
        $sql="select * from thirdparty_discnt_driver_tb where 1 ORDER BY thirdparty_discnt_driver_id ASC";
        return $this->db->query($sql)->result_array();
    }

    function get_discnt_driver($thirdparty_discnt_driver_name){
        $sql="select * from thirdparty_discnt_driver_tb where thirdparty_discnt_driver_name='".$thirdparty_discnt_driver_name."'";
        return $this->db->query($sql)->result_array();
    }

    function add_discnt_driver($thirdparty_discnt_driver_id,$thirdparty_discnt_driver_name, $thirdparty_discnt_driver_digt){
        $sql="INSERT INTO thirdparty_discnt_driver_tb(thirdparty_discnt_driver_id, thirdparty_discnt_driver_name, thirdparty_discnt_driver_digt)
              VALUES ( $thirdparty_discnt_driver_id,'$thirdparty_discnt_driver_name', '$thirdparty_discnt_driver_digt')";
        return $this->db->query($sql);
    }

    function del_discnt_driver($thirdparty_discnt_driver_id){
        $sql="DELETE FROM thirdparty_discnt_driver_tb  where thirdparty_discnt_driver_id=".$thirdparty_discnt_driver_id."";
        return $this->db->query($sql);
    }
}