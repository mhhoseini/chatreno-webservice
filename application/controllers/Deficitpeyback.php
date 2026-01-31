<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Deficitpeyback extends CI_Controller {

    /**
     * Peyback Page for this controller.
     *
     * So any other public methods not prefixed with an underscore will
     */

    public function index()
    {
        $this->load->helper('my_helper');
        $this->load->model('B_db');
        $this->load->helper('url');
        $this->load->library('user_agent');
        if ($this->agent->is_referral())
        {
            $domain = parse_url($this->agent->referrer(), PHP_URL_HOST);
            if( $domain == 'bpm.shaparak.ir'){
                //DATA IS RETURNED FROM BANK ....
                //'RefId','ResCode','SaleOrderId','SaleReferenceId','CardHolderPAN','CreditCardSaleResponseDetail','FinalAmount';
                $res_code = $this->input->post('ResCode');
                $RefId_from_bank = $this->input->post('RefId');
                $SaleOrderId_from_bank = $this->input->post('SaleOrderId');
                $SaleReferenceId_from_bank = $this->input->post('SaleReferenceId');
                $CardHolderPAN_from_bank = $this->input->post('CardHolderPAN');
                $CreditCardSaleResponseDetail_from_bank = $this->input->post('CreditCardSaleResponseDetail');
                $FinalAmount_from_bank = $this->input->post('FinalAmount');
                $check_existing_refid = $this->check_dificitpay_tb($RefId_from_bank);

                //check local DB for payment record
                if(!empty($check_existing_refid)){
                    $dificitpey_id= $check_existing_refid[0]['pey_id'];
                    $request_id= $check_existing_refid[0]['pey_request_id'];
                    $this->update_dificitpay_tb($dificitpey_id, $RefId_from_bank, $res_code,$SaleReferenceId_from_bank,$CardHolderPAN_from_bank,$CreditCardSaleResponseDetail_from_bank);
                    // check is the transaction the right one
                    $payment_record = $this->get_dificitpay_tb($RefId_from_bank , $dificitpey_id);

                    $amount=$check_existing_refid[0]['pey_amount'];
                    //after success answer from bank
                    if($res_code==0 && !empty($payment_record) && ($amount == $FinalAmount_from_bank)){
                        $verify = $this->verifyPayment($dificitpey_id,$SaleOrderId_from_bank,$SaleReferenceId_from_bank);

                        if($verify['res_code']==0){
                            $settle_result = $this->settlePayment($dificitpey_id,$SaleOrderId_from_bank,$SaleReferenceId_from_bank);

                            if($amount>0)
                            {
                                $query1="INSERT INTO user_pey_tb( user_pey_request_id, user_pey_amount, user_pey_mode, user_pey_code, user_pey_desc,user_pey_timestamp) VALUES
                                    ($request_id ,$amount, 'cash', '$SaleReferenceId_from_bank','$CreditCardSaleResponseDetail_from_bank',now())      ";
                            }
                            $result1=$this->B_db->run_query_put($query1);
                            $user_pey_id=$this->db->insert_id();
                            $query="INSERT INTO user_pey_tb( user_pey_request_id, user_pey_amount , user_pey_mode     , user_pey_code    , user_pey_desc    ,user_pey_image_code     ,user_pey_timestamp)
                                SELECT user_pey_temp_request_id,user_pey_temp_amount,user_pey_temp_mode,user_pey_temp_code,user_pey_temp_desc,user_pey_temp_image_code,user_pey_temp_timestamp
                                FROM user_pey_temp_tb WHERE user_pey_temp_request_id = $request_id     ";
                            $result=$this->B_db->run_query_put($query);
                            //*****************************************************************************
                            $query="select * from user_pey_tb where user_pey_request_id = $request_id     ";
                            $result = $this->B_db->run_query($query);
                            foreach($result as $row)
                            {
                                if($row['user_pey_mode']=='user_wallet'){

                                    $user_wallet_detail= 'پرداخت کسری واریزی سفارش کد'.  $request_id .' توسط  کیف پول ';
                                    $query2="INSERT INTO user_wallet_tb( user_wallet_user_id, user_wallet_amount, user_wallet_mode, user_wallet_timestamp, user_wallet_detail, user_wallet_code) VALUES
                                        (".$row['user_pey_code'].",'".$row['user_pey_amount']."' , 'get'      ,now()               ,'$user_wallet_detail',$request_id)      ";
                                    $result2=$this->B_db->run_query_put($query2);
                                }
                            }
                            $query3="DELETE FROM user_pey_temp_tb WHERE  user_pey_temp_request_id = $request_id";
                            $result3=$this->B_db->run_query_put($query3);
                            //*****************************************************************************
                           
                            $query2="UPDATE request_tb SET request_last_state_id=3  WHERE  request_id = $request_id";
                            $result2=$this->B_db->run_query_put($query2);
							 
							 $deficit_pey_id=$this->get_dificitpay_byid($dificitpey_id);
							 $query2="UPDATE deficit_pey_tb SET deficit_pey_user_pey_id=$user_pey_id,deficit_pey_user_pey_date=now()  WHERE  deficit_pey_id = $deficit_pey_id";
                            $result2=$this->B_db->run_query_put($query2);
							
						
                            //****************************************************************************
                            $query1="INSERT INTO state_request_tb(staterequest_request_id, staterequest_state_id, staterequest_timestamp, staterequest_desc      ) VALUES
                                    (".$request_id."        ,      3               ,  now()                 ,'در حال بررسی توسط نماینده')";
                            $result1=$this->B_db->run_query_put($query1);
                            $staterequest_id=$this->db->insert_id();
                            //*****************************************************************************
                            //   Bazaryab pardakht codes
                            //   $this->B_db->peyback_decision($request_id , $amount);
                            //*****************************************************************************
                        }
                        if($result){
                            echo json_encode(array('result'=>"ok"
                            ,"data"=>""
                            ,'desc'=>'پرداخت با موفقیت ثبت شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        }
                        redirect("https://aref24.com/deficitpayback?id=".$dificitpey_id);
                    }else if($res_code!= 0){
                        //$verify = $this->verifyPayment($pey_id,$SaleOrderId_from_bank,$RefId_from_bank);
                        redirect("https://aref24.com/deficitpayback?id=".$dificitpey_id);
                    }
                }else{
                    echo "تراکنش مورد نظر در بانک اطلاعاتی یافت نشد";
                }

            }else{
                echo "خطای ورود از سایت غیرمجاز";
            }
        }else {
            echo "ورود غیر مجاز شناخته شد و آدرس آی پی ثبت گردید.";
        }
    }


    public function update_dificitpay_tb($pey_id,$pey_refid,$pey_backcode,$SaleReferenceId_from_bank,$CardHolderPAN_from_bank,$CreditCardSaleResponseDetail_from_bank){
        $this->load->model('B_db');
        $sql2 = "UPDATE pey_tb
        SET pey_backdate=now(), pey_backcode='$pey_backcode',pey_refrenceid='$SaleReferenceId_from_bank',pey_cardholder='$CardHolderPAN_from_bank',pey_responsdetail='$CreditCardSaleResponseDetail_from_bank'
        WHERE  pey_id = $pey_id";
        $this->B_db->run_query_put($sql2);
    }

    public function check_dificitpay_tb($pey_refid){
        $this->load->model('B_db');
        $sql1 = "select * from pey_tb where pey_refid = '$pey_refid'";
        return $result=$this->B_db->run_query($sql1);
    }

    public function get_dificitpay_tb($pey_refid, $pey_id){
        $this->load->model('B_db');
        $sql1 = "select * from pey_tb where pey_refid = '$pey_refid' and pey_id = ".$pey_id." and pey_mode='deficitpey'";
        return $result=$this->B_db->run_query($sql1);
    }

	public function get_dificitpay_byid($pey_id){
        $this->load->model('B_db');
        $sql1 = "select pey_deficit_pey_id from pey_tb where pey_id = ".$pey_id." and pey_mode='deficitpey'";
         $result=$this->B_db->run_query($sql1);
		return $result[0]['pey_deficit_pey_id'];
    }
    /**
     * Verify Payment
     * @author Abolfazl Ganji
     * @param $orderId
     * @param $saleOrderId
     * @param $saleReferenceId
     * @return mixed - false for failed
     */
    public function verifyPayment($orderId, $saleOrderId, $saleReferenceId)
    {
        $this->soapClient = new SoapClient($this->config->item('wsdl'));
        if($orderId && $saleOrderId && $saleReferenceId) {

            $parameters = [
                'terminalId' => $this->config->item('terminalId'),
                'userName' => $this->config->item('userName'),
                'userPassword' => $this->config->item('userPassword'),
                'orderId' => $orderId,
                'saleOrderId' => $saleOrderId,
                'saleReferenceId' => $saleReferenceId,
            ];

            try {
                // Call the SOAP method
                $result = $this->soapClient->bpVerifyRequest($parameters);
                // Display the result
                $res = explode(',', $result->return);
                if ($res[0] == "0") {
                    return [
                        'result' => true,
                        'res_code' => $res[0],
                        'RefId' => $res[1]
                    ];
                } else {
                    return [
                        'result' => false,
                        'res_code' => $res[0],
                        'RefId' => isset($res[1]) ? $res[1] : null
                    ];
                }
            } catch (Exception $e) {
                return $e->getMessage();
            }
        } else
            return false;
    }

    function settlePayment($orderId, $saleOrderId, $saleReferenceId){
        $this->soapClient = new SoapClient($this->config->item('wsdl'));
        if($orderId && $saleOrderId && $saleReferenceId) {
            $parameters = [
                'terminalId' => $this->config->item('terminalId'),
                'userName' => $this->config->item('userName'),
                'userPassword' => $this->config->item('userPassword'),
                'orderId' => $orderId,
                'saleOrderId' => $saleOrderId,
                'saleReferenceId' => $saleReferenceId,
            ];
            try {
                // Call the SOAP method
                return $this->soapClient->bpSettleRequest($parameters);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        } else
            return false;
    }

    // Estelam Tarakonesh
    function bpInquiryRequest($orderId, $saleOrderId, $saleReferenceId){
        $this->soapClient = new SoapClient($this->config->item('wsdl'));
        if($orderId && $saleOrderId && $saleReferenceId) {
            $parameters = [
                'terminalId' => $this->config->item('terminalId'),
                'userName' => $this->config->item('userName'),
                'userPassword' => $this->config->item('userPassword'),
                'orderId' => $orderId,
                'saleOrderId' => $saleOrderId,
                'saleReferenceId' => $saleReferenceId,
            ];
            try {
                // Call the SOAP method
                return $this->soapClient->bpInquiryRequest($parameters);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        } else
            return false;
    }

    // Bargashte pule moshtari
    function bpReversalRequest($orderId, $saleOrderId, $saleReferenceId){
        $this->soapClient = new SoapClient($this->config->item('wsdl'));
        if($orderId && $saleOrderId && $saleReferenceId) {
            $parameters = [
                'terminalId' => $this->config->item('terminalId'),
                'userName' => $this->config->item('userName'),
                'userPassword' => $this->config->item('userPassword'),
                'orderId' => $orderId,
                'saleOrderId' => $saleOrderId,
                'saleReferenceId' => $saleReferenceId,
            ];
            try {
                // Call the SOAP method
                return $this->soapClient->bpReversalRequest($parameters);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        } else
            return false;
    }
}
