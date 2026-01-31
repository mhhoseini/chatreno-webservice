<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * B_discount
 *
 * @mixin Eloquent
 */
class B_discount extends CI_Model {

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

    function get_discount_code($discount_code){
        $sql="select * from discount_code_tb where discount_code_deactive=0 AND  discount_code='".$discount_code."'";
        return $this->db->query($sql)->result_array();
    }

    function add_discount_code($discount_code_company_id,$discount_code_fieldinsurance_id,$discount_code , $discount_code_amount ,  $discount_code_number  , $discount_code_desc ,$discount_code_date_start ,$discount_code_date_end){
        $sql="INSERT INTO discount_code_tb(  discount_code_company_id,  discount_code_fieldinsurance_id,   discount_code,  discount_code_amount,    discount_code_number ,   discount_code_desc,   discount_code_date_start,discount_code_date_end)
	            VALUES ('$discount_code_company_id','$discount_code_fieldinsurance_id','$discount_code' , '$discount_code_amount' ,  '$discount_code_number'  , '$discount_code_desc' ,'$discount_code_date_start' ,'$discount_code_date_end');";
        return $this->db->query($sql);
    }

    function discount_code_use_amount($discount_code_id){
        $sql='SELECT SUM(discount_code_use_amount) AS value_sum FROM discount_code_use_tb WHERE discount_code_use_dscntcode_id='.$discount_code_id;
        return $this->db->query($sql)->result_array();
    }

    function discount_code_count($discount_code_id){
        $sql='SELECT COUNT(*) AS value_cnt FROM discount_code_use_tb WHERE discount_code_use_dscntcode_id='.$discount_code_id;
        return $this->db->query($sql)->result_array();
    }

    function get_discount_code_by($discount_code_id){
        $sql="select * from discount_code_use_tb where discount_code_use_dscntcode_id=".$discount_code_id;
        return $this->db->query($sql)->result_array();
    }

    function del_discount_code($discount_code_id){
        $sql="DELETE FROM discount_code_tb  where discount_code_id=".$discount_code_id."";
        return $this->db->query($sql);
    }

    function update_discount_code($discount_code_id,$deactive){
        $sql="UPDATE discount_code_tb SET discount_code_deactive=$deactive where discount_code_id=".$discount_code_id;
        return $this->db->query($sql);
    }

}