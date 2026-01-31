<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * B_car
 *
 * @mixin Eloquent
 */
class B_car extends CI_Model {

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

    function get_carmode_by($carmode_name){
        $sql="select * from carmode_tb where carmode_name='".$carmode_name."'";
        return $this->db->query($sql)->result_array();
    }

    function all_carmode(){
        $sql="select * from carmode_tb where 1 ORDER BY carmode_priority ASC";
        return $this->db->query($sql)->result_array();
    }

    function add_carmode($carmode_name, $carmode_priority){
        $sql="INSERT INTO carmode_tb( carmode_name,  carmode_priority)
	           VALUES ( '$carmode_name', $carmode_priority)";
        return $this->db->query($sql);
    }

    function update_carmode($carmode_logo , $carmode_id){
        $sql="UPDATE carmode_tb SET carmode_logo='$carmode_logo' where carmode_id=".$carmode_id."";
        return $this->db->query($sql);
    }

    function update_carmode_deactive($carmode_id,$from,$to){
        $sql="UPDATE carmode_tb SET carmode_deactive=$from where carmode_deactive=$to AND carmode_id=".$carmode_id."";
        return $this->db->query($sql);
    }

    function get_carcompany_by($carcompany_name){
        $sql="select * from carcompany_tb where carcompany_name='".$carcompany_name."'";
        return $this->db->query($sql)->result_array();
    }

    function add_carcompany($carcompany_name, $carcompany_carmode_id,$carcompany_priority){
        $sql="INSERT INTO carcompany_tb( carcompany_name, carcompany_carmode_id, carcompany_priority)
	                 VALUES ( '$carcompany_name', $carcompany_carmode_id,$carcompany_priority);";
        return $this->db->query($sql);
    }

    function update_carcompany($carcompany_logo, $carcompany_id){
        $sql="UPDATE carcompany_tb SET carcompany_logo='$carcompany_logo' where carcompany_id=".$carcompany_id."";
        return $this->db->query($sql);
    }

    function update_carcompany_deactive($carcompany_id,$from,$to){
        $sql="UPDATE carcompany_tb SET carcompany_deactive=$from where carcompany_deactive=$to AND carcompany_id=".$carcompany_id."";
        return $this->db->query($sql);
    }

    function get_cargroup_by($cargroup_name){
        $sql="select * from cargroup_tb where cargroup_name='".$cargroup_name."'";
        return $this->db->query($sql)->result_array();
    }

    function add_cargroup($cargroup_name, $cargroup_carmode_id,$cargroup_priority){
        $sql="INSERT INTO cargroup_tb( cargroup_name, cargroup_carmode_id, cargroup_priority)
	           VALUES ('$cargroup_name', $cargroup_carmode_id,$cargroup_priority)";
        return $this->db->query($sql);
    }

    function update_cargroup($cargroup_logo, $cargroup_id){
        $sql="UPDATE cargroup_tb SET cargroup_logo='$cargroup_logo' where cargroup_id=".$cargroup_id."";
        return $this->db->query($sql);
    }

    function get_cargroup_byid($carmode_id){
        $sql="select * from cargroup_tb,carmode_tb where cargroup_carmode_id=carmode_id  AND ";
        return $this->db->query($sql)->result_array();
    }

    function update_cargroup_deactive($cargroup_id,$from,$to){
        $sql="UPDATE cargroup_tb SET cargroup_deactive=$from where cargroup_deactive=$to AND cargroup_id=".$cargroup_id."";
        return $this->db->query($sql);
    }

    function get_car($car_name){
        $sql="select * from car_tb where car_name='".$car_name."'";
        return $this->db->query($sql)->result_array();
    }

    function add_car($car_mode_id,$car_company_id, $car_name, $car_passenger,$car_group_id,$car_desc,$car_priority){
        $sql="INSERT INTO car_tb(car_mode_id  ,car_company_id, car_name , car_passenger  ,car_group_id  , car_desc, car_priority)
	                                VALUES ($car_mode_id,$car_company_id, '$car_name', $car_passenger,$car_group_id,'$car_desc',$car_priority);";
        return $this->db->query($sql);
    }

    function update_car($car_image, $car_id){
        $sql="UPDATE car_tb SET car_image='$car_image' where car_id=".$car_id."";
        return $this->db->query($sql);
    }

    function update_car_deactive($car_id,$from,$to){
        $sql="UPDATE car_tb SET car_deactive=$from where car_deactive=$to AND car_id=".$car_id."";
        return $this->db->query($sql);
    }

     function delete_car($car_id){
        $sql="DELETE FROM  car_tb  where car_id=".$car_id."";
        return $this->db->query($sql);
    }

}