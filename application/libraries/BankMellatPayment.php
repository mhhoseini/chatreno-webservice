<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//namespace Restserver\BankMellatPaymentService;

use SoapClient;

/**
 * Class BankMellatPayment
 * @author Abolfazl Ganji
 * @see http://www.aref24.com/
 *
 */
class BankMellatPayment
{
    protected $soapClient;
    protected $wsdl;
    public $config;
    public $callBackURL;

    public function __construct()
    {
        //$this->config = Config('BankMellatPayment');
        $this->callBackURL = $this->config->item('callBackURL');
    }

    /**
     * Payment Request
     * @author Abolfazl Ganji
     * @param $amount - IRR
     * @param $orderId - INT
     * @param string $additionalData
     * @param int $payerId
     * @return array
     * @throws \Exception
     */
    public function paymentRequest($amount, $orderId, $additionalData = '', $payerId = 0)
    {
        //$CI = get_instance();
        $this->soapClient = new SoapClient($this->config->item('wsdl'));

        if($amount && $amount > 100 && $orderId ) {
            $parameters = [
                'terminalId' => $this->config->item('terminalId'),
                'userName' => $this->config->item('userName'),
                'userPassword' => $this->config->item('userPassword'),
                'orderId' => $orderId,
                'amount' => $amount,
                'localDate' => date("Ymd"),
                'localTime' => date("His"),
                'additionalData' => $additionalData,
                'callBackUrl' => $this->callBackURL,
                'payerId' => $payerId
            ];

            try {

                // Call the SOAP method
                $result = $this->soapClient->bpPayRequest($parameters);
                // Display the result
                $res = explode(',', $result->return);
                if ($res[0] == "0") {
                    return [
                        'result' => true,
                        'res_code' => $res[0],
                        'ref_id' => $res[1]
                    ];
                } else {
                    return [
                        'result' => false,
                        'res_code' => $res[0],
                        'ref_id' => isset($res[1]) ? $res[1] : null
                    ];
                }
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
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
                return $this->soapClient->bpVerifyRequest($parameters);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        } else
            return false;
    }
}