<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * B_comission
 *
 * @mixin Eloquent
 */
class B_company extends CI_Model {

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

    function get_company_by($company_name,$company_id){
        $sql="select * from company_tb where company_name='".$company_name."' OR company_id=".$company_id."";
        return $this->db->query($sql)->result_array();
    }

    function add_company($company_id,$company_name,$company_levelof_prosperity,$company_num_branchesdamages , $company_customer_satisfaction ,  $company_timeanswer_complaints , $company_description,$company_logo_url){
        $sql="INSERT INTO company_tb( company_id,  company_name,  company_levelof_prosperity,   company_num_branchesdamages,  company_customer_satisfaction,    company_timeanswer_complaints ,   company_description,   company_logo_url)
	          VALUES ($company_id,'$company_name',$company_levelof_prosperity,$company_num_branchesdamages , $company_customer_satisfaction ,  $company_timeanswer_complaints , '$company_description','$company_logo_url');";
        return $this->db->query($sql);
    }

    function del_company($company_id){
        $sql="DELETE FROM company_tb  where company_id=".$company_id."";
        return $this->db->query($sql);
    }

    function get_fieldcompany_by($fieldcompany_company_id, $fieldcompany_fieldinsurance_id){
        $sql="select * from fieldcompany_tb where fieldcompany_company_id='".$fieldcompany_company_id."' AND fieldcompany_fieldinsurance_id='".$fieldcompany_fieldinsurance_id."'";
        return $this->db->query($sql)->result_array();
    }

    function get_fieldcompany($fieldcompany_fieldinsurance_id){
        $sql="select * from fieldinsurance_tb where fieldinsurance_id='".$fieldcompany_fieldinsurance_id."'";
        return $this->db->query($sql)->result_array();
    }

    function add_fieldcompany($fieldcompany_fieldinsurance_id , $fieldcompany_company_id ,  $fieldcompany_desc , $fieldcompany_link  , $fieldcompany_deactive,$fieldcompany_priority,$fieldcompany_wage,$fieldcompany_genuine_wage){
        $sql="INSERT INTO fieldcompany_tb(      fieldcompany_fieldinsurance_id,  fieldcompany_company_id,    fieldcompany_desc ,   fieldcompany_link,fieldcompany_deactive,fieldcompany_priority,fieldcompany_wage,fieldcompany_genuine_wage)
	         VALUES ($fieldcompany_fieldinsurance_id , $fieldcompany_company_id ,  '$fieldcompany_desc' , '$fieldcompany_link'  , $fieldcompany_deactive,$fieldcompany_priority,$fieldcompany_wage,$fieldcompany_genuine_wage);";
        return $this->db->query($sql);
    }

    function get_company($fieldcompany_company_id){
        $sql="select * from company_tb where company_id='".$fieldcompany_company_id."'";
        return $this->db->query($sql)->result_array();
    }

    function del_fieldcompany($fieldcompany_id){
        $sql = "DELETE FROM fieldcompany_tb  where fieldcompany_id=".$fieldcompany_id;
        return $this->db->query($sql);
    }

    function update_fieldcompany_deactive($fieldcompany_id,$deactive){
        $sql="UPDATE fieldcompany_tb SET fieldcompany_deactive=$deactive where fieldcompany_id=".$fieldcompany_id;
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