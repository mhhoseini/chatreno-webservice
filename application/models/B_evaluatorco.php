<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * B_comission
 *
 * @mixin Eloquent
 */
class B_evaluatorco extends CI_Model {

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

    function get_evaluatorco_by($evaluatorco_name,$evaluatorco_id){
        $sql="select * from evaluatorco_tb where evaluatorco_name='".$evaluatorco_name."' OR evaluatorco_id=".$evaluatorco_id."";
        return $this->db->query($sql)->result_array();
    }

    function add_evaluatorco($evaluatorco_id,$evaluatorco_name,$evaluatorco_levelof_prosperity,$evaluatorco_num_branchesdamages , $evaluatorco_customer_satisfaction ,  $evaluatorco_timeanswer_complaints , $evaluatorco_description,$evaluatorco_logo_url){
        $sql="INSERT INTO evaluatorco_tb( evaluatorco_id,  evaluatorco_name,  evaluatorco_levelof_prosperity,   evaluatorco_num_branchesdamages,  evaluatorco_customer_satisfaction,    evaluatorco_timeanswer_complaints ,   evaluatorco_description,   evaluatorco_logo_url)
	          VALUES ($evaluatorco_id,'$evaluatorco_name',$evaluatorco_levelof_prosperity,$evaluatorco_num_branchesdamages , $evaluatorco_customer_satisfaction ,  $evaluatorco_timeanswer_complaints , '$evaluatorco_description','$evaluatorco_logo_url');";
        return $this->db->query($sql);
    }

    function del_evaluatorco($evaluatorco_id){
        $sql="DELETE FROM evaluatorco_tb  where evaluatorco_id=".$evaluatorco_id."";
        return $this->db->query($sql);
    }

    function get_fieldevaluatorco_by($fieldevaluatorco_evaluatorco_id, $fieldevaluatorco_fieldinsurance_id){
        $sql="select * from fieldevaluatorco_tb where fieldevaluatorco_evaluatorco_id='".$fieldevaluatorco_evaluatorco_id."' AND fieldevaluatorco_fieldinsurance_id='".$fieldevaluatorco_fieldinsurance_id."'";
        return $this->db->query($sql)->result_array();
    }

    function get_fieldevaluatorco($fieldevaluatorco_fieldinsurance_id){
        $sql="select * from fieldinsurance_tb where fieldinsurance_id='".$fieldevaluatorco_fieldinsurance_id."'";
        return $this->db->query($sql)->result_array();
    }

    function add_fieldevaluatorco($fieldevaluatorco_fieldinsurance_id , $fieldevaluatorco_evaluatorco_id ,  $fieldevaluatorco_desc , $fieldevaluatorco_link  , $fieldevaluatorco_deactive,$fieldevaluatorco_priority){
        $sql="INSERT INTO fieldevaluatorco_tb(      fieldevaluatorco_fieldinsurance_id,  fieldevaluatorco_evaluatorco_id,    fieldevaluatorco_desc ,   fieldevaluatorco_link,fieldevaluatorco_deactive,fieldevaluatorco_priority)
	         VALUES ($fieldevaluatorco_fieldinsurance_id , $fieldevaluatorco_evaluatorco_id ,  '$fieldevaluatorco_desc' , '$fieldevaluatorco_link'  , $fieldevaluatorco_deactive,$fieldevaluatorco_priority);";
        return $this->db->query($sql);
    }

    function get_evaluatorco($fieldevaluatorco_evaluatorco_id){
        $sql="select * from evaluatorco_tb where evaluatorco_id='".$fieldevaluatorco_evaluatorco_id."'";
        return $this->db->query($sql)->result_array();
    }

    function del_fieldevaluatorco($fieldevaluatorco_id){
        $sql = "DELETE FROM fieldevaluatorco_tb  where fieldevaluatorco_id=".$fieldevaluatorco_id;
        return $this->db->query($sql);
    }

    function update_fieldevaluatorco_deactive($fieldevaluatorco_id,$deactive){
        $sql="UPDATE fieldevaluatorco_tb SET fieldevaluatorco_deactive=$deactive where fieldevaluatorco_id=".$fieldevaluatorco_id;
        return $this->db->query($sql);
    }

    function get_fieldinsurance_by($fieldinsurance_id,$fieldinsurance, $fieldinsurance_fa ){
        $sql="select * from fieldinsurance_tb where fieldinsurance_id=".$fieldinsurance_id." OR fieldinsurance='".$fieldinsurance."' OR fieldinsurance_fa='".$fieldinsurance_fa."' ";
        return $this->db->query($sql)->result_array();
    }

    function get_fielddamagefile_by($fielddamagefile, $fielddamagefile_fa ){
        $sql="select * from fielddamagefile_tb where  fielddamagefile='".$fielddamagefile."' OR fielddamagefile_fa='".$fielddamagefile_fa."' ";
        return $this->db->query($sql)->result_array();
    }

    function add_fieldinsurance($fieldinsurance_id,$fieldinsurance,$fieldinsurance_fa ,$fieldinsurance_logo_url , $fieldinsurance_desc , $fieldinsurance_link, $fieldinsurance_commission,$fieldinsurance_image_code,$fieldinsurance_deactive,$fieldinsurance_mode){
        $sql="INSERT INTO fieldinsurance_tb(fieldinsurance_id,  fieldinsurance,   fieldinsurance_fa,  fieldinsurance_logo_url,    fieldinsurance_desc ,   fieldinsurance_link,   fieldinsurance_commission,   fieldinsurance_image_code,fieldinsurance_deactive,fieldinsurance_mode)
	             VALUES ('$fieldinsurance_id','$fieldinsurance','$fieldinsurance_fa' , '$fieldinsurance_logo_url' ,  '$fieldinsurance_desc' , '$fieldinsurance_link', '$fieldinsurance_commission','$fieldinsurance_image_code',$fieldinsurance_deactive,$fieldinsurance_mode);";
        return $this->db->query($sql);
    }

    function del_fieldinsurance($fieldinsurance_id){
        $sql="DELETE FROM fieldinsurance_tb  where fieldinsurance_id=".$fieldinsurance_id."";
        return $this->db->query($sql);
    }

    function update_fieldinsurance($fieldinsurance_id, $deactive){
        $sql="UPDATE fieldinsurance_tb SET fieldinsurance_deactive=$deactive where fieldinsurance_id=".$fieldinsurance_id;
        return $this->db->query($sql);
    }


    function add_fielddamagefile($fielddamagefile,$fielddamagefile_fa ,$fielddamagefile_logo_url , $fielddamagefile_desc , $fielddamagefile_link, $fielddamagefile_commission,$fielddamagefile_image_code,$fielddamagefile_deactive,$fielddamagefile_mode,$fielddamagefile_organ_therapycontract_conditions_covarage_id){
        $sql="INSERT INTO fielddamagefile_tb(  fielddamagefile,   fielddamagefile_fa,  fielddamagefile_logo_url,    fielddamagefile_desc ,   fielddamagefile_link,   fielddamagefile_commission,   fielddamagefile_image_code,fielddamagefile_deactive,fielddamagefile_mode,fielddamagefile_organ_therapycontract_conditions_covarage_id)
	             VALUES ('$fielddamagefile','$fielddamagefile_fa' , '$fielddamagefile_logo_url' ,  '$fielddamagefile_desc' , '$fielddamagefile_link', '$fielddamagefile_commission','$fielddamagefile_image_code',$fielddamagefile_deactive,$fielddamagefile_mode,$fielddamagefile_organ_therapycontract_conditions_covarage_id);";
        return $this->db->query($sql);
    }

    function del_fielddamagefile($fielddamagefile_id){
        $sql="DELETE FROM fielddamagefile_tb  where fielddamagefile_id=".$fielddamagefile_id."";
        return $this->db->query($sql);
    }

    function update_fielddamagefile($fielddamagefile_id, $deactive){
        $sql="UPDATE fielddamagefile_tb SET fielddamagefile_deactive=$deactive where fielddamagefile_id=".$fielddamagefile_id;
        return $this->db->query($sql);
    }

}