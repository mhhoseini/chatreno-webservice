<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * B_requests
 *
 * @mixin Eloquent
 */

class B_requests extends CI_Model{

    function __construct()
    {
        parent::__construct();
    }

    function get_request_usercancel($request_id){
        $sql="select * from request_usercancel_tb where request_usercancel_request_id=".$request_id;
        return $this->db->query($sql)->result_array();
    }

function get_request_peycash($request_id){
        $sql="select * from user_pey_tb where user_pey_mode='cash' AND user_pey_request_id=".$request_id;
        return $this->db->query($sql)->result_array();
    }

 function get_request($request_id,$user_id){
        $sql="select * from request_tb where request_user_id=".$user_id." AND request_id=".$request_id;
        return $this->db->query($sql)->result_array();
    }

    function get_request_all($user_id){
        $sql="select * from request_tb,company_tb,request_state,fieldinsurance_tb where  fieldinsurance=request_fieldinsurance AND request_state_id=request_last_state_id AND company_id=request_company_id AND request_user_id=".$user_id;
        return $this->db->query($sql)->result_array();
    }
 function getuser_request_detail($user_id,$request_id){
        $sql="select * from request_tb,company_tb,request_state,fieldinsurance_tb where  fieldinsurance=request_fieldinsurance AND request_state_id=request_last_state_id AND company_id=request_company_id AND request_id=".$request_id." AND request_user_id=".$user_id;
        return $this->db->query($sql)->result_array();
    }
 function get_status_date($request_id,$state_id){
        $sql="select * from state_request_tb where  staterequest_request_id=".$request_id." AND staterequest_state_id=".$state_id;
        return $this->db->query($sql)->result_array();
    }

    function get_request_by($request_last_state_id,$request_id){
        /**
         * @should need to provider references from here
         */
        $sql="select * from request_tb where request_last_state_id=$request_last_state_id AND request_id=$request_id";
        return $this->db->query($sql)->result_array();
    }

    function get_address($request_adderss_id){
        $sql0=" SELECT * FROM user_address_tb,state_tb,city_tb WHERE user_address_state_id=state_id AND user_address_city_id=city_id AND user_address_id=".$request_adderss_id;
        return $this->db->query($sql0)->result_array();
    }
    function get_agent($request_agent_id){
        $sql2="select * from agent_tb,state_tb,city_tb where state_id=agent_state_id AND city_id=agent_city_id AND agent_id=".$request_agent_id;
        return $this->db->query($sql2)->result_array();
    }
    function get_request_state($request_id){
        $sql01="select * from state_request_tb,request_state where request_state_id=staterequest_state_id AND staterequest_request_id=".$request_id;
        return $this->db->query($sql01)->result_array();
    }
    function get_state_request($request_id){
        $sql3="select * from request_tb,request_state where request_state_id=request_last_state_id AND request_id=".$request_id;
        return $this->db->query($sql3)->result_array();
    }
    function get_sum_user_pey_amount($request_id){
        $sql1="select sum(user_pey_amount) AS sumpey from user_pey_tb where user_pey_request_id=".$request_id." ORDER BY user_pey_request_id";
        return $this->db->query($sql1)->result_array();
    }
    function get_user_pey($request_id){
        $sql1="select * from user_pey_tb,pey_mod_tb where pey_mod=user_pey_mode AND user_pey_request_id=".$request_id." ORDER BY user_pey_mode";
        return $this->db->query($sql1)->result_array();
    }
    function get_request_ready($request_id){
        $sql3=" SELECT * FROM requst_ready_tb WHERE  requst_ready_request_id=".$request_id;
        return $this->db->query($sql3)->result_array();
    }
    function get_request_delivered($request_id){
        $sql5=" SELECT * FROM request_delivered_tb,state_tb,city_tb WHERE state_id=request_delivered_state_id AND city_id=request_delivered_city_id AND request_delivered_request_id=".$request_id;
        return $this->db->query($sql5)->result_array();
    }
    function request_refuse($request_last_state_id, $request_id){
        $sql1="UPDATE request_tb SET request_last_state_id=$request_last_state_id WHERE request_id=$request_id";
        return $this->db->query($sql1);
    }
    function add_request_usercancel($request_id, $request_usercancel_desc){
        $sql="INSERT INTO request_usercancel_tb( request_usercancel_request_id, request_usercancel_desc, request_usercancel_timstamp) VALUES
                                        ($request_id, '$request_usercancel_desc', now())";
        return $this->db->query($sql);
    }
    function set_request_refuse($request_id,$request_last_state_id,$desc){
        $sql1="INSERT INTO state_request_tb( staterequest_request_id, staterequest_state_id, staterequest_timestamp,staterequest_desc) VALUES
                                        ( $request_id, $request_last_state_id, now(),'$desc')";
        return $this->db->query($sql1);
    }
	
    function del_request_pey($request_id){
        $sql0="DELETE FROM  user_pey_tb WHERE user_pey_request_id=$request_id ";
        return $this->db->query($sql0);
    }
	   function del_request_releations($request_id){
        $sql0="DELETE FROM  instalment_check_tb WHERE instalment_check_request_id=$request_id ";
        $sql1="DELETE FROM  managdiscount_use_tb WHERE managdiscount_request_id=$request_id ";
        $sql2="DELETE FROM  discount_code_use_tb WHERE discount_code_use_request_id=$request_id ";
           $sql3="INSERT INTO user_wallet_tb ( user_wallet_user_id, user_wallet_amount, user_wallet_gift, user_wallet_mode, user_wallet_timestamp, user_wallet_detail, user_wallet_code, user_wallet_user_mobile)
SELECT user_wallet_user_id, user_wallet_amount, user_wallet_gift, 'add', now(), ' بازگشت به کیف پول به علت برگشت درخواست $request_id', user_wallet_code, user_wallet_user_mobile
FROM user_wallet_tb
WHERE user_wallet_code=$request_id AND user_wallet_mode='get' ";

        $this->db->query($sql0);
        $this->db->query($sql1);
        $this->db->query($sql2);
        return $this->db->query($sql3);
    }
    function request_backuser($request_backuser_request_id){
        $sql="select * from request_backuser_tb where  request_backuser_request_id=$request_backuser_request_id ";
        return $this->db->query($sql)->result_array();
    }
    function requst_suspend($requst_suspend_request_id){
        $sql=" SELECT * FROM requst_suspend_tb WHERE requst_suspend_request_id=$requst_suspend_request_id ";
        return $this->db->query($sql)->result_array();
    }

    function requst_difficult($request_difficult_request_id){
        $sql=" SELECT * FROM request_difficult_tb WHERE  request_difficult_request_id=$request_difficult_request_id ";
        return $this->db->query($sql)->result_array();
    }

    function requst_ready_image($requst_ready_request_id){
        $sql=" SELECT * FROM requst_ready_image_tb WHERE  requst_ready_request_id=$requst_ready_request_id ";
        return $this->db->query($sql)->result_array();
    }

    function request_delivered_city($request_delivered_request_id){
        $sql=" SELECT * FROM request_delivered_tb,city_tb,state_tb WHERE request_delivered_city_id=city_id AND request_delivered_state_id=state_id AND  request_delivered_request_id=$request_delivered_request_id ";
        return $this->db->query($sql)->result_array();
    }

    function requst_deficit_pey($user_id){
        $sql=" SELECT * FROM deficit_pey_tb,user_tb,request_tb,company_tb,fieldinsurance_tb WHERE 
 request_company_id=company_id AND request_fieldinsurance=fieldinsurance AND request_user_id = user_id 
 AND deficit_pey_request_id=request_id AND user_id=$user_id ";
        return $this->db->query($sql)->result_array();
    }


    function requst_visit($user_id){
        $sql=" SELECT * FROM request_visit_tb,user_tb,request_tb,company_tb,fieldinsurance_tb WHERE 
 request_company_id=company_id AND request_fieldinsurance=fieldinsurance AND request_user_id = user_id 
 AND request_visit_request_id=request_id AND user_id=$user_id ";
        return $this->db->query($sql)->result_array();
    }

    function usermarketer_byuser($marketer_user_id){
        $sql="select * from usermarketer_tb where marketer_reject=0 AND marketer_user_id=".$marketer_user_id."";
        return $this->db->query($sql)->result_array();
    }

    function del_usermarketer($marketer_user_id){
        $sql="DELETE FROM usermarketer_tb WHERE marketer_user_id=".$marketer_user_id."";
        return $this->db->query($sql);
    }

    function add_usermarketer($marketer_user_id,$marketer_image_code,$marketer_leader_mobile,$marketer_coworker){
        if($marketer_leader_mobile=='') {
            $sql = "INSERT INTO usermarketer_tb ( marketer_user_id , marketer_image_code ,marketer_timestamp , marketer_leader_mobile ,marketer_mode_id,marketer_coworker,marketer_timestamp_change) VALUES
		      ('$marketer_user_id','$marketer_image_code',now(),'$marketer_leader_mobile',1,'$marketer_coworker','');";
        }else{
            $sql = "INSERT INTO usermarketer_tb ( marketer_user_id , marketer_image_code ,marketer_timestamp , marketer_leader_mobile ,marketer_mode_id,marketer_coworker,marketer_timestamp_change) VALUES
		      ('$marketer_user_id','$marketer_image_code',now(),'$marketer_leader_mobile',1,'$marketer_coworker',now());";

        }
        return $this->db->query($sql);
    }

    function update_usermarketer($marketer_user_id,$marketer_leader_mobile){
        $sql="UPDATE usermarketer_tb SET marketer_leader_mobile='".$marketer_leader_mobile."', marketer_timestamp_change=now() where marketer_user_id=".$marketer_user_id;
        return $this->db->query($sql);
    }

    function collect_usermarketer($marketer_user_id){
        $sql="select * from marketer_commission_defult_tb,fieldinsurance_tb,usermarketer_tb where fieldinsurance_id=marketer_commission_defult_fieldinsurance_id AND marketer_mode_id=marketer_commission_defult_mode_id AND marketer_user_id=".$marketer_user_id;
        return $this->db->query($sql)->result_array();
    }

    function get_leader_commission(){
        $sql="select * from leader_commission_defult_tb,fieldinsurance_tb where fieldinsurance_id=leader_commission_defult_fieldinsurance_id ";
        return $this->db->query($sql)->result_array();
    }

    function get_user_company($user_id){
        $sql="select * from request_tb,company_tb,request_state,fieldinsurance_tb,user_tb where  fieldinsurance=request_fieldinsurance AND request_state_id=request_last_state_id AND company_id=request_company_id AND user_mobile=request_reagent_mobile AND user_id=".$user_id;
        return $this->db->query($sql)->result_array();
    }

    function get_user_wallet_peycommision_marketer($request_id){
        $sql="select * from user_wallet_tb,peycommision_marketer_tb where user_wallet_id=peycommision_marketer_user_wallet_id AND peycommision_marketer_request_id=".$request_id;
        return $this->db->query($sql)->result_array();
    }

    function collect_company_usermarketer($mobile){
        $sql="select * from request_tb,company_tb,request_state,fieldinsurance_tb,user_tb,usermarketer_tb where  fieldinsurance=request_fieldinsurance AND request_state_id=request_last_state_id AND company_id=request_company_id AND user_mobile=request_reagent_mobile AND user_id=marketer_user_id AND marketer_leader_mobile= '".$mobile."'";
        return $this->db->query($sql)->result_array();
    }

    function get_user_wallet_peycommision_leader($request_id){
        $sql="select * from user_wallet_tb,peycommision_leader_tb where user_wallet_id=peycommision_leader_user_wallet_id AND peycommision_leader_request_id=".$request_id;
        return $this->db->query($sql)->result_array();
    }

    function get_marketer_mode($marketer_user_id){
        $sql="select * from usermarketer_tb,marketer_mode_tb where marketer_mode_tb.marketer_mode_id=usermarketer_tb.marketer_mode_id AND marketer_user_id=".$marketer_user_id."";
        return $this->db->query($sql)->result_array();
    }
}