<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * B_bodycar
 *
 * @mixin Eloquent
 */
class B_bodycar extends CI_Model {

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

    function get_bodycar_discnt_by($bodycar_discnt_name, $bodycar_discnt_id){
        $sql="select * from bodycar_discnt_tb where bodycar_discnt_name='".$bodycar_discnt_name."' OR bodycar_discnt_id=".$bodycar_discnt_id."";
        return $this->db->query($sql)->result_array();
    }

    function add_bodycar($bodycar_discnt_id, $bodycar_discnt_name,$bodycar_discnt_digt){
        $sql="INSERT INTO bodycar_discnt_tb(bodycar_discnt_id, bodycar_discnt_name, bodycar_discnt_digt)
	          VALUES ( $bodycar_discnt_id,'$bodycar_discnt_name', '$bodycar_discnt_digt');";
        return $this->db->query($sql);
    }

    function all_bodycar_discnt(){
        $sql="select * from bodycar_discnt_tb where 1 ORDER BY bodycar_discnt_id ASC";
        return $this->db->query($sql)->result_array();
    }

    function del_bodycar($bodycar_discnt_id){
        $sql="DELETE FROM bodycar_discnt_tb  where bodycar_discnt_id=".$bodycar_discnt_id."";
        return $this->db->query($sql);
    }

    function get_bodycar_thirdparty($bodycar_discnt_thirdparty_id, $bodycar_discnt_thirdparty_name){
        $sql="select * from bodycar_discnt_thirdparty_tb where bodycar_discnt_thirdparty_id=$bodycar_discnt_thirdparty_id AND bodycar_discnt_thirdparty_name='".$bodycar_discnt_thirdparty_name."'";
        return $this->db->query($sql)->result_array();
    }

    function add_bodycar_thirdparty($bodycar_discnt_thirdparty_id,$bodycar_discnt_thirdparty_name, $bodycar_discnt_thirdparty_digt){
        $sql="INSERT INTO bodycar_discnt_thirdparty_tb(bodycar_discnt_thirdparty_id, bodycar_discnt_thirdparty_name, bodycar_discnt_thirdparty_digt)
        VALUES ( $bodycar_discnt_thirdparty_id,'$bodycar_discnt_thirdparty_name', '$bodycar_discnt_thirdparty_digt')";
        return $this->db->query($sql);
    }

    function add_bodycar_thirdparty_percent($bodycar_discnt_life_id,$bodycar_discnt_life_company_name, $bodycar_discnt_life_percent){
        $sql="INSERT INTO bodycar_discnt_life_tb(bodycar_discnt_life_id, bodycar_discnt_life_company_name, bodycar_discnt_life_percent)
        VALUES ( $bodycar_discnt_life_id,'$bodycar_discnt_life_company_name', '$bodycar_discnt_life_percent')";
        return $this->db->query($sql);
    }

    function all_bodycar_thirdparty(){
        $sql="select * from bodycar_discnt_thirdparty_tb where 1 ORDER BY bodycar_discnt_thirdparty_id ASC";
        return $this->db->query($sql)->result_array();
    }

    function del_bodycar_thirdparty($bodycar_discnt_thirdparty_id){
        $sql="DELETE FROM bodycar_discnt_thirdparty_tb  where bodycar_discnt_thirdparty_id=".$bodycar_discnt_thirdparty_id."";
        return $this->db->query($sql);
    }

    function get_bodycar_discnt_life_by($bodycar_discnt_life_id , $bodycar_discnt_life_company_name){
        $sql="select * from bodycar_discnt_life_tb where bodycar_discnt_life_id=$bodycar_discnt_life_id AND bodycar_discnt_life_company_name='".$bodycar_discnt_life_company_name."'";
        return $this->db->query($sql)->result_array();
    }

    function all_bodycar_discnt_life(){
        $sql="select * from bodycar_discnt_life_tb where 1 ORDER BY bodycar_discnt_life_id ASC";
        return $this->db->query($sql)->result_array();
    }

    function del_bodycar_discnt_life($bodycar_discnt_life_id){
        $sql="DELETE FROM bodycar_discnt_life_tb  where bodycar_discnt_life_id=".$bodycar_discnt_life_id."";
        return $this->db->query($sql);
    }

    function all_bodycar_discnt_accbank(){
        $sql="select * from bodycar_discnt_accbank_tb where 1 ORDER BY bodycar_discnt_accbank_id ASC";
        return $this->db->query($sql)->result_array();
    }

    function get_bodycar_discnt_accbank_by($bodycar_discnt_accbank_name){
        $sql="select * from bodycar_discnt_accbank_tb where bodycar_discnt_accbank_name='".$bodycar_discnt_accbank_name."'";
        return $this->db->query($sql)->result_array();
    }

    function add_bodycar_discnt_accbank($bodycar_discnt_accbank_id,$bodycar_discnt_accbank_name, $bodycar_discnt_accbank_percent){
        $sql="INSERT INTO bodycar_discnt_accbank_tb(bodycar_discnt_accbank_id, bodycar_discnt_accbank_name, bodycar_discnt_accbank_percent)
        VALUES ( $bodycar_discnt_accbank_id,'$bodycar_discnt_accbank_name', '$bodycar_discnt_accbank_percent');";
        return $this->db->query($sql);
    }

    function del_bodycar_discnt_accbank($bodycar_discnt_accbank_id){
        $sql="DELETE FROM bodycar_discnt_accbank_tb  where bodycar_discnt_accbank_id=".$bodycar_discnt_accbank_id."";
        return $this->db->query($sql);
    }

    function get_bodycar_damage_driver_by($bodycar_damage_driver_name){
        $sql="select * from bodycar_damage_driver_tb where bodycar_damage_driver_name='".$bodycar_damage_driver_name."'";
        return $this->db->query($sql)->result_array();
    }

    function add_bodycar_damage_driver($bodycar_damage_driver_id,$bodycar_damage_driver_name, $bodycar_damage_driver_digit){
        $sql="INSERT INTO bodycar_damage_driver_tb(bodycar_damage_driver_id, bodycar_damage_driver_name, bodycar_damage_driver_digit)
        VALUES ( $bodycar_damage_driver_id,'$bodycar_damage_driver_name', '$bodycar_damage_driver_digit');";
        return $this->db->query($sql);
    }

    function all_bodycar_damage_driver(){
        $sql="select * from bodycar_damage_driver_tb where 1 ORDER BY bodycar_damage_driver_id ASC";
        return $this->db->query($sql)->result_array();
    }

    function del_bodycar_damage_driver($bodycar_damage_driver_id){
        $sql="DELETE FROM bodycar_damage_driver_tb  where bodycar_damage_driver_id=".$bodycar_damage_driver_id."";
        return $this->db->query($sql);
    }

    function get_bodycar_time_by($bodycar_time_desc,$bodycar_time_id){
        $sql="select * from bodycar_time_tb where bodycar_time_desc='".$bodycar_time_desc."' OR bodycar_time_id=".$bodycar_time_id."";
        return $this->db->query($sql)->result_array();
    }

    function add_bodycar_time($bodycar_time_id,$bodycar_time_desc, $bodycar_time_percent){
        $sql="INSERT INTO bodycar_time_tb(bodycar_time_id, bodycar_time_desc, bodycar_time_percent)
        VALUES ( $bodycar_time_id,'$bodycar_time_desc', '$bodycar_time_percent');";
        return $this->db->query($sql);
    }

    function all_bodycar_time(){
        $sql="select * from bodycar_time_tb where 1 ORDER BY bodycar_time_id ASC";
        return $this->db->query($sql)->result_array();
    }

    function del_bodycar_time($bodycar_time_id){
        $sql="DELETE FROM bodycar_time_tb  where bodycar_time_id=".$bodycar_time_id."";
        return $this->db->query($sql);
    }

    function get_bodycar_usefor($bodycar_usefor_carmode_id,$bodycar_usefor_name,$bodycar_usefor_id){
        $sql="select * from bodycar_usefor_tb where bodycar_usefor_carmode_id=$bodycar_usefor_carmode_id AND( bodycar_usefor_name='".$bodycar_usefor_name."' OR bodycar_usefor_id=".$bodycar_usefor_id." )";
        return $this->db->query($sql)->result_array();
    }

    function add_bodycar_usefor($bodycar_usefor_id,$bodycar_usefor_name,$bodycar_usefor_percent,$bodycar_usefor_carmode_id){
        $sql="INSERT INTO bodycar_usefor_tb(bodycar_usefor_id, bodycar_usefor_name, bodycar_usefor_percent,bodycar_usefor_carmode_id)
	                            VALUES ( $bodycar_usefor_id,'$bodycar_usefor_name', '$bodycar_usefor_percent',$bodycar_usefor_carmode_id);";
        return $this->db->query($sql);
    }

    function del_bodycar_usefor($bodycar_usefor_id){
        $sql="DELETE FROM bodycar_usefor_tb  where bodycar_usefor_id=".$bodycar_usefor_id."";
        return $this->db->query($sql);
    }

    function get_bodycar_coverage_by($bodycar_coverage_name,$bodycar_coverage_id){
        $sql="select * from bodycar_coverage_tb where bodycar_coverage_name='".$bodycar_coverage_name."' OR bodycar_coverage_id=".$bodycar_coverage_id."";
        return $this->db->query($sql)->result_array();
    }

    function add_bodycar_coverage($bodycar_coverage_id,$bodycar_coverage_name,$bodycar_coverage_desc, $bodycar_coverage_percent){
        $sql="INSERT INTO bodycar_coverage_tb(bodycar_coverage_id,bodycar_coverage_name, bodycar_coverage_desc, bodycar_coverage_percent)
        VALUES ( $bodycar_coverage_id,'$bodycar_coverage_name','$bodycar_coverage_desc', '$bodycar_coverage_percent');";
        return $this->db->query($sql);

    }

    function all_bodycar_coverage(){
        $sql="select * from bodycar_coverage_tb where 1 ORDER BY bodycar_coverage_id ASC";
        return $this->db->query($sql)->result_array();
    }

    function del_bodycar_coverage($bodycar_coverage_id){
        $sql="DELETE FROM bodycar_coverage_tb  where bodycar_coverage_id=".$bodycar_coverage_id."";
        return $this->db->query($sql);
    }

    function get_bodycar_price_by($bodycar_price_cargroup_id,$bodycar_price_fieldcompany_id,$bodycar_price_car_mode_id){
        $sql="select * from bodycar_price_tb where bodycar_price_cargroup_id=$bodycar_price_cargroup_id AND bodycar_price_fieldcompany_id=".$bodycar_price_fieldcompany_id." AND bodycar_price_car_mode_id=".$bodycar_price_car_mode_id." ";
        return $this->db->query($sql)->result_array();
    }

    function add_bodycar_price($bodycar_price_fieldcompany_id, $bodycar_price_car_mode_id,$bodycar_price_cargroup_id,$bodycar_price_import_year,$bodycar_price_import_percent,$bodycar_price_new_percent,$bodycar_price_min_disc,$bodycar_price_max_disc,$bodycar_price_chash,$bodycar_price_fixed_amount,$bodycar_price_fixed_transportation,$bodycar_price_min_price,$bodycar_price_max_price){
        $sql="INSERT INTO bodycar_price_tb( bodycar_price_fieldcompany_id, bodycar_price_car_mode_id,bodycar_price_cargroup_id,bodycar_price_import_year, bodycar_price_import_percent, bodycar_price_new_percent ,bodycar_price_min_disc ,bodycar_price_max_disc ,bodycar_price_chash,bodycar_price_fixed_amount,bodycar_price_fixed_transportation ,bodycar_price_min_price ,bodycar_price_max_price)
        VALUES ( $bodycar_price_fieldcompany_id, $bodycar_price_car_mode_id,$bodycar_price_cargroup_id,$bodycar_price_import_year,$bodycar_price_import_percent,$bodycar_price_new_percent,$bodycar_price_min_disc,$bodycar_price_max_disc,$bodycar_price_chash,$bodycar_price_fixed_amount,$bodycar_price_fixed_transportation,$bodycar_price_min_price,$bodycar_price_max_price);";
        return $this->db->query($sql);
    }

    function all_bodycar_price(){
        $sql="select * from bodycar_price_tb,fieldcompany_tb,cargroup_tb,company_tb,carmode_tb  where
        bodycar_price_fieldcompany_id=fieldcompany_id
        AND bodycar_price_cargroup_id=cargroup_id
        AND bodycar_price_car_mode_id=carmode_id
        AND fieldcompany_company_id=company_id
        ORDER BY bodycar_price_id ASC";
        return $this->db->query($sql)->result_array();
    }

    function del_bodycar_price($bodycar_price_id){
        $sql="DELETE FROM bodycar_price_tb  where bodycar_price_id=".$bodycar_price_id."";
        return $this->db->query($sql);
    }

    function get_bodycar_slideprice_by($bodycar_slideprice_bodycar_price_id,$bodycar_slideprice_min,$bodycar_slideprice_max){
        $sql="select * from bodycar_slideprice_tb where bodycar_slideprice_bodycar_price_id=".$bodycar_slideprice_bodycar_price_id." AND bodycar_slideprice_min=".$bodycar_slideprice_min." AND bodycar_slideprice_max=".$bodycar_slideprice_max."";
        return $this->db->query($sql)->result_array();
    }

    function get_bodycar_slideprice_by_priceid($bodycar_price_id){
        $sql="select * from bodycar_slideprice_tb where bodycar_slideprice_bodycar_price_id=$bodycar_price_id  ORDER BY bodycar_slideprice_id ASC";
        return $this->db->query($sql)->result_array();
    }

    function add_bodycar_slideprice($bodycar_slideprice_bodycar_price_id,$bodycar_slideprice_min,$bodycar_slideprice_max,$bodycar_slideprice_percent){
        $sql="INSERT INTO bodycar_slideprice_tb(bodycar_slideprice_bodycar_price_id,bodycar_slideprice_min, bodycar_slideprice_max, bodycar_slideprice_percent)
        VALUES ( $bodycar_slideprice_bodycar_price_id,'$bodycar_slideprice_min', '$bodycar_slideprice_max', '$bodycar_slideprice_percent');";
        return $this->db->query($sql);
    }

    function del_bodycar_slideprice($bodycar_slideprice_id){
        $sql="DELETE FROM bodycar_slideprice_tb  where bodycar_slideprice_id=".$bodycar_slideprice_id."";
        return $this->db->query($sql);
    }

    function get_bodycar_extra_slideold_by($bodycar_extra_slideold_bodycar_price_id,$bodycar_extra_slideold_min,$bodycar_extra_slideold_max){
        $sql="select * from bodycar_extra_slideold_tb where bodycar_extra_slideold_bodycar_price_id=".$bodycar_extra_slideold_bodycar_price_id." AND bodycar_extra_slideold_min=".$bodycar_extra_slideold_min." AND bodycar_extra_slideold_max=".$bodycar_extra_slideold_max."";
        return $this->db->query($sql)->result_array();
    }

    function get_bodycar_extra_slideold_by_priceid($bodycar_price_id){
        $sql="select * from bodycar_extra_slideold_tb where bodycar_extra_slideold_bodycar_price_id=$bodycar_price_id  ORDER BY bodycar_extra_slideold_id ASC";
        return $this->db->query($sql)->result_array();
    }

    function add_bodycar_extra_slideold($bodycar_extra_slideold_bodycar_price_id,$bodycar_extra_slideold_min,$bodycar_extra_slideold_max,$bodycar_extra_slideold_percent){
        $sql="INSERT INTO bodycar_extra_slideold_tb(bodycar_extra_slideold_bodycar_price_id,bodycar_extra_slideold_min, bodycar_extra_slideold_max, bodycar_extra_slideold_percent)
        VALUES ( $bodycar_extra_slideold_bodycar_price_id,'$bodycar_extra_slideold_min', '$bodycar_extra_slideold_max', '$bodycar_extra_slideold_percent');";
        return $this->db->query($sql);
    }

    function del_bodycar_extra_slideold($bodycar_extra_slideold_id){
        $sql="DELETE FROM bodycar_extra_slideold_tb  where bodycar_extra_slideold_id=".$bodycar_extra_slideold_id."";
        return $this->db->query($sql);
    }


    function get_bodycar_disc_slideold_by($bodycar_disc_slideold_bodycar_price_id,$bodycar_disc_slideold_min,$bodycar_disc_slideold_max){
        $sql="select * from bodycar_disc_slideold_tb where bodycar_disc_slideold_bodycar_price_id=".$bodycar_disc_slideold_bodycar_price_id." AND bodycar_disc_slideold_min=".$bodycar_disc_slideold_min." AND bodycar_disc_slideold_max=".$bodycar_disc_slideold_max."";
        return $this->db->query($sql)->result_array();
    }

    function get_bodycar_disc_slideold_by_priceid($bodycar_price_id){
        $sql="select * from bodycar_disc_slideold_tb where bodycar_disc_slideold_bodycar_price_id=$bodycar_price_id  ORDER BY bodycar_disc_slideold_id ASC";
        return $this->db->query($sql)->result_array();
    }

    function add_bodycar_disc_slideold($bodycar_disc_slideold_bodycar_price_id,$bodycar_disc_slideold_min,$bodycar_disc_slideold_max,$bodycar_disc_slideold_percent){
        $sql="INSERT INTO bodycar_disc_slideold_tb(bodycar_disc_slideold_bodycar_price_id,bodycar_disc_slideold_min, bodycar_disc_slideold_max, bodycar_disc_slideold_percent)
        VALUES ( $bodycar_disc_slideold_bodycar_price_id,'$bodycar_disc_slideold_min', '$bodycar_disc_slideold_max', '$bodycar_disc_slideold_percent');";
        return $this->db->query($sql);
    }

    function del_bodycar_disc_slideold($bodycar_disc_slideold_id){
        $sql="DELETE FROM bodycar_disc_slideold_tb  where bodycar_disc_slideold_id=".$bodycar_disc_slideold_id."";
        return $this->db->query($sql);
    }

    function get_bodycar_slidedisc_by($bodycar_slidedisc_bodycar_price_id,$bodycar_slidedisc_min,$bodycar_slidedisc_max){
        $sql="select * from bodycar_slidedisc_tb where bodycar_slidedisc_bodycar_price_id=".$bodycar_slidedisc_bodycar_price_id." AND bodycar_slidedisc_min=".$bodycar_slidedisc_min." AND bodycar_slidedisc_max=".$bodycar_slidedisc_max."";
        return $this->db->query($sql)->result_array();
    }

    function get_bodycar_slidedisc_by_priceid($bodycar_price_id){
        $sql="select * from bodycar_slidedisc_tb where bodycar_slidedisc_bodycar_price_id=$bodycar_price_id  ORDER BY bodycar_slidedisc_id ASC";
        return $this->db->query($sql)->result_array();
    }

    function add_bodycar_slidedisc($bodycar_slidedisc_bodycar_price_id,$bodycar_slidedisc_min,$bodycar_slidedisc_max,$bodycar_slidedisc_minpercent,$bodycar_slidedisc_maxpercent,$bodycar_slidedisc_instalment_minpercent,$bodycar_slidedisc_instalment_maxpercent){
        $sql="INSERT INTO bodycar_slidedisc_tb(bodycar_slidedisc_bodycar_price_id,bodycar_slidedisc_min, bodycar_slidedisc_max, bodycar_slidedisc_minpercent, bodycar_slidedisc_maxpercent, bodycar_slidedisc_instalment_minpercent, bodycar_slidedisc_instalment_maxpercent)
        VALUES ( $bodycar_slidedisc_bodycar_price_id,'$bodycar_slidedisc_min', '$bodycar_slidedisc_max', '$bodycar_slidedisc_minpercent', '$bodycar_slidedisc_maxpercent', '$bodycar_slidedisc_instalment_minpercent', '$bodycar_slidedisc_instalment_maxpercent');";
        return $this->db->query($sql);
    }

    function del_bodycar_slidedisc($bodycar_slidedisc_id){
        $sql="DELETE FROM bodycar_slidedisc_tb  where bodycar_slidedisc_id=".$bodycar_slidedisc_id."";
        return $this->db->query($sql);
    }

    function get_bodycar_price_covarage_by($bodycar_price_covarage_bodycoverage_id, $bodycar_price_covarage_bodycar_price_id){
        $sql="select * from bodycar_price_covarage_tb where bodycar_price_covarage_bodycoverage_id=".$bodycar_price_covarage_bodycoverage_id." AND bodycar_price_covarage_bodycar_price_id=".$bodycar_price_covarage_bodycar_price_id."";
        return $this->db->query($sql)->result_array();
    }

    function add_bodycar_price_covarage($bodycar_price_covarage_bodycar_price_id,$bodycar_price_covarage_bodycoverage_id, $bodycar_price_covarage_percent, $bodycar_price_covarage_calmode_id){
        $sql="INSERT INTO bodycar_price_covarage_tb(bodycar_price_covarage_bodycar_price_id,bodycar_price_covarage_bodycoverage_id, bodycar_price_covarage_percent, bodycar_price_covarage_calmode_id)
        VALUES ( $bodycar_price_covarage_bodycar_price_id,$bodycar_price_covarage_bodycoverage_id, '$bodycar_price_covarage_percent', $bodycar_price_covarage_calmode_id);";
        return $this->db->query($sql);
    }

    function all_bodycar_price_covarage_by_priceid($bodycar_price_id){
        $sql="select * from bodycar_price_covarage_tb,bodycar_coverage_tb where bodycar_coverage_id=bodycar_price_covarage_bodycoverage_id AND bodycar_price_covarage_bodycar_price_id=$bodycar_price_id  ORDER BY bodycar_price_covarage_id ASC";
        return $this->db->query($sql)->result_array();
    }

    function del_bodycar_price_covarage($bodycar_price_covarage_id){
        $sql="DELETE FROM bodycar_price_covarage_tb  where bodycar_price_covarage_id=".$bodycar_price_covarage_id."";
        return $this->db->query($sql);
    }

	
	  function get_bodycar_price_usefor_by($bodycar_price_usefor_bodyusefor_id, $bodycar_price_usefor_bodycar_price_id){
        $sql="select * from bodycar_price_usefor_tb where bodycar_price_usefor_bodyusefor_id=".$bodycar_price_usefor_bodyusefor_id." AND bodycar_price_usefor_bodycar_price_id=".$bodycar_price_usefor_bodycar_price_id."";
        return $this->db->query($sql)->result_array();
    }

    function add_bodycar_price_usefor($bodycar_price_usefor_bodycar_price_id,$bodycar_price_usefor_bodyusefor_id, $bodycar_price_usefor_percent, $bodycar_price_usefor_calmode_id){
        $sql="INSERT INTO bodycar_price_usefor_tb(bodycar_price_usefor_bodycar_price_id,bodycar_price_usefor_bodyusefor_id, bodycar_price_usefor_percent, bodycar_price_usefor_calmode_id)
        VALUES ( $bodycar_price_usefor_bodycar_price_id,$bodycar_price_usefor_bodyusefor_id, '$bodycar_price_usefor_percent', $bodycar_price_usefor_calmode_id);";
        return $this->db->query($sql);
    }
	
    function all_bodycar_price_usefor_by_priceid($bodycar_price_id){
        $sql="select * from bodycar_price_usefor_tb,bodycar_usefor_tb where bodycar_usefor_id=bodycar_price_usefor_bodyusefor_id AND bodycar_price_usefor_bodycar_price_id=$bodycar_price_id  ORDER BY bodycar_price_usefor_id ASC";
        return $this->db->query($sql)->result_array();
    }

    function del_bodycar_price_usefor($bodycar_price_usefor_id){
        $sql="DELETE FROM bodycar_price_usefor_tb  where bodycar_price_usefor_id=".$bodycar_price_usefor_id."";
        return $this->db->query($sql);
    }

	
    function get_bodycar_price_discntlife_by($bodycar_price_discntlife_bodycar_discnt_life_id,$bodycar_price_discntlife_bodycar_price_id){
        $sql="select * from bodycar_price_discntlife_tb where bodycar_price_discntlife_bodycar_discnt_life_id=".$bodycar_price_discntlife_bodycar_discnt_life_id." AND bodycar_price_discntlife_bodycar_price_id=".$bodycar_price_discntlife_bodycar_price_id."";
        return $this->db->query($sql)->result_array();
    }

    function add_bodycar_price_discntlife($bodycar_price_discntlife_bodycar_price_id,$bodycar_price_discntlife_bodycar_discnt_life_id,$bodycar_price_discntlife_percent){
        $sql="INSERT INTO bodycar_price_discntlife_tb(bodycar_price_discntlife_bodycar_price_id,bodycar_price_discntlife_bodycar_discnt_life_id, bodycar_price_discntlife_percent)
          VALUES ( $bodycar_price_discntlife_bodycar_price_id,$bodycar_price_discntlife_bodycar_discnt_life_id, '$bodycar_price_discntlife_percent');";
        return $this->db->query($sql);
    }

    function tog_bodycar_price_discntlife($bodycar_price_id){
        $sql="select * from bodycar_price_discntlife_tb,bodycar_discnt_life_tb where bodycar_discnt_life_id=bodycar_price_discntlife_bodycar_discnt_life_id AND bodycar_price_discntlife_bodycar_price_id=$bodycar_price_id  ORDER BY bodycar_price_discntlife_id ASC";
        return $this->db->query($sql)->result_array();
    }

    function del_bodycar_price_discntlife($bodycar_price_discntlife_id){
        $sql="DELETE FROM bodycar_price_discntlife_tb  where bodycar_price_discntlife_id=".$bodycar_price_discntlife_id."";
        return $this->db->query($sql);
    }

    function get_bodycar_price_accbank_by($bodycar_price_accbank_bodycar_discnt_accbank_id,$bodycar_price_accbank_bodycar_price_id){
        $sql="select * from bodycar_price_accbank_tb where bodycar_price_accbank_bodycar_discnt_accbank_id=".$bodycar_price_accbank_bodycar_discnt_accbank_id." AND bodycar_price_accbank_bodycar_price_id=".$bodycar_price_accbank_bodycar_price_id."";
        return $this->db->query($sql)->result_array();
    }

    function add_bodycar_price_accbank($bodycar_price_accbank_bodycar_price_id,$bodycar_price_accbank_bodycar_discnt_accbank_id, $bodycar_price_accbank_percent){
        $sql="INSERT INTO bodycar_price_accbank_tb(bodycar_price_accbank_bodycar_price_id,bodycar_price_accbank_bodycar_discnt_accbank_id, bodycar_price_accbank_percent)
        VALUES ( $bodycar_price_accbank_bodycar_price_id,$bodycar_price_accbank_bodycar_discnt_accbank_id, '$bodycar_price_accbank_percent');";
        return $this->db->query($sql);
    }

    function tog_bodycar_price_accbank($bodycar_price_id){
        $sql="select * from bodycar_price_accbank_tb,bodycar_discnt_accbank_tb where bodycar_discnt_accbank_id=bodycar_price_accbank_bodycar_discnt_accbank_id AND bodycar_price_accbank_bodycar_price_id=$bodycar_price_id  ORDER BY bodycar_price_accbank_id ASC";
        return $this->db->query($sql)->result_array();
    }

    function del_bodycar_price_accbank($bodycar_price_accbank_id){
        $sql="DELETE FROM bodycar_price_accbank_tb  where bodycar_price_accbank_id=".$bodycar_price_accbank_id."";
        return $this->db->query($sql);
    }

    function get_bodycar_price_thirdparty_by($bodycar_price_thirdparty_bodycar_discnt_thirdparty_id,$bodycar_price_thirdparty_bodycar_price_id){
        $sql="select * from bodycar_price_thirdparty_tb where bodycar_price_thirdparty_bodycar_discnt_thirdparty_id=".$bodycar_price_thirdparty_bodycar_discnt_thirdparty_id." AND bodycar_price_thirdparty_bodycar_price_id=".$bodycar_price_thirdparty_bodycar_price_id."";
        return $this->db->query($sql)->result_array();
    }

    function add_bodycar_price_thirdparty($bodycar_price_thirdparty_bodycar_price_id,$bodycar_price_thirdparty_bodycar_discnt_thirdparty_id, $bodycar_price_thirdparty_percent){
        $sql="INSERT INTO bodycar_price_thirdparty_tb(bodycar_price_thirdparty_bodycar_price_id,bodycar_price_thirdparty_bodycar_discnt_thirdparty_id, bodycar_price_thirdparty_percent)
            VALUES ( $bodycar_price_thirdparty_bodycar_price_id,$bodycar_price_thirdparty_bodycar_discnt_thirdparty_id, '$bodycar_price_thirdparty_percent');";
        return $this->db->query($sql);
    }

    function get_bodycar_price_thirdparty($bodycar_price_id){
        $sql="select * from bodycar_price_thirdparty_tb,bodycar_discnt_thirdparty_tb where bodycar_discnt_thirdparty_id=bodycar_price_thirdparty_bodycar_discnt_thirdparty_id AND bodycar_price_thirdparty_bodycar_price_id=$bodycar_price_id  ORDER BY bodycar_price_thirdparty_id ASC";
        return $this->db->query($sql)->result_array();
    }

    function del_bodycar_price_thirdparty($bodycar_price_thirdparty_id){
        $sql="DELETE FROM bodycar_price_thirdparty_tb  where bodycar_price_thirdparty_id=".$bodycar_price_thirdparty_id."";
        return $this->db->query($sql);
    }

    function get_bodycar_price_discbody_by($bodycar_price_discbody_bodycar_discnt_id,$bodycar_price_discbody_bodycar_price_id){
        $sql="select * from bodycar_price_discbody_tb where bodycar_price_discbody_bodycar_discnt_id=".$bodycar_price_discbody_bodycar_discnt_id." AND bodycar_price_discbody_bodycar_price_id=".$bodycar_price_discbody_bodycar_price_id."";
        return $this->db->query($sql)->result_array();
    }

    function add_bodycar_price_discbody($bodycar_price_discbody_bodycar_price_id,$bodycar_price_discbody_bodycar_discnt_id, $bodycar_price_discbody_percent){
        $sql="INSERT INTO bodycar_price_discbody_tb(bodycar_price_discbody_bodycar_price_id,bodycar_price_discbody_bodycar_discnt_id, bodycar_price_discbody_percent)
            VALUES ( $bodycar_price_discbody_bodycar_price_id,$bodycar_price_discbody_bodycar_discnt_id, '$bodycar_price_discbody_percent');";
        return $this->db->query($sql);
    }

    function get_bodycar_price_discbody($bodycar_price_id){
        $sql="select * from bodycar_price_discbody_tb,bodycar_discnt_tb where bodycar_discnt_id=bodycar_price_discbody_bodycar_discnt_id AND bodycar_price_discbody_bodycar_price_id=$bodycar_price_id  ORDER BY bodycar_price_discbody_id ASC";
        return $this->db->query($sql)->result_array();
    }

    function del_bodycar_price_discbody($bodycar_price_discbody_id){
        $sql="DELETE FROM bodycar_price_discbody_tb  where bodycar_price_discbody_id=".$bodycar_price_discbody_id."";
        return $this->db->query($sql);
    }

    function get_bodycar_price_exceptions_by($bodycar_price_exceptions_car_id,$bodycar_price_exceptions_bodycar_price_id){
        $sql="select * from bodycar_price_exceptions_tb where bodycar_price_exceptions_car_id=".$bodycar_price_exceptions_car_id." AND bodycar_price_exceptions_bodycar_price_id=".$bodycar_price_exceptions_bodycar_price_id."";
        return $this->db->query($sql)->result_array();
    }

    function add_bodycar_price_exceptions($bodycar_price_exceptions_bodycar_price_id,$bodycar_price_exceptions_car_id){
        $sql="INSERT INTO bodycar_price_exceptions_tb(bodycar_price_exceptions_bodycar_price_id,bodycar_price_exceptions_car_id)
    VALUES ( $bodycar_price_exceptions_bodycar_price_id,$bodycar_price_exceptions_car_id);";
        return $this->db->query($sql);
    }

    function get_bodycar_price_exceptions($bodycar_price_id){
        $sql="select * from bodycar_price_exceptions_tb,car_tb where car_id=bodycar_price_exceptions_car_id AND bodycar_price_exceptions_bodycar_price_id=$bodycar_price_id  ORDER BY bodycar_price_exceptions_id ASC";
        return $this->db->query($sql)->result_array();
    }

    function del_bodycar_price_exceptions($bodycar_price_exceptions_id){
        $sql="DELETE FROM bodycar_price_exceptions_tb  where bodycar_price_exceptions_id=".$bodycar_price_exceptions_id."";
        return $this->db->query($sql);
    }


    function get_bodycar_price_extracar_by($bodycar_price_extracar_car_id,$bodycar_price_extracar_bodycar_price_id){
        $sql="select * from bodycar_price_extracar_tb where bodycar_price_extracar_car_id=".$bodycar_price_extracar_car_id." AND bodycar_price_extracar_bodycar_price_id=".$bodycar_price_extracar_bodycar_price_id."";
        return $this->db->query($sql)->result_array();
    }

    function add_bodycar_price_extracar($bodycar_price_extracar_bodycar_price_id,$bodycar_price_extracar_car_id){
        $sql="INSERT INTO bodycar_price_extracar_tb(bodycar_price_extracar_bodycar_price_id,bodycar_price_extracar_car_id)
    VALUES ( $bodycar_price_extracar_bodycar_price_id,$bodycar_price_extracar_car_id);";
        return $this->db->query($sql);
    }

    function get_bodycar_price_extracar($bodycar_price_id){
        $sql="select * from bodycar_price_extracar_tb,car_tb where car_id=bodycar_price_extracar_car_id AND bodycar_price_extracar_bodycar_price_id=$bodycar_price_id  ORDER BY bodycar_price_extracar_id ASC";
        return $this->db->query($sql)->result_array();
    }

    function del_bodycar_price_extracar($bodycar_price_extracar_id){
        $sql="DELETE FROM bodycar_price_extracar_tb  where bodycar_price_extracar_id=".$bodycar_price_extracar_id."";
        return $this->db->query($sql);
    }

}