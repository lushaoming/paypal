<?php
/**
 * Class Refund
 * @author 卢绍明<lusm@sz-bcs.com.cn>
 * @date   2019/8/30
 */
namespace Shannon\PayPal;

use PayPal\Api\Amount;
use PayPal\Api\Sale;

class Refund
{
    /**
     * @param $transactionId
     * @param $total
     * @param string $currency
     * @param string $reason
     * @return array
     * @throws ShannonPaypalException
     * @author 卢绍明<lusm@sz-bcs.com.cn>
     * @date   2019/8/30
     */
    public function execute($transactionId, $total, $currency = 'USD', $reason = '')
    {
        if (empty($total) || !is_numeric($total) || $total < 0) {
            throw new ShannonPaypalException('Refund total is invalid');
        }

        $paypal = ApiContext::getInstance()->createContext();

        $amount = $this->getAmount($total, $currency);
        $refund = $this->getRefund($amount, $reason);

        $sale = new Sale();
        $sale->setId($transactionId);
        $refundSale = $sale->refund($refund, $paypal);
        $refundedSale = $refundSale->toArray();

        return $refundedSale;
    }

    /**
     * @param $total
     * @param $currency
     * @return Amount
     * @author 卢绍明<lusm@sz-bcs.com.cn>
     * @date   2019/8/30
     */
    public function getAmount($total, $currency)
    {
        $amountObj = new Amount();
        $amountObj->setCurrency($currency)->setTotal($total);
        return $amountObj;
    }

    /**
     * getRefund
     * @param  Amount $amountObj
     * @return \PayPal\Api\Refund
     */
    public function getRefund(Amount $amountObj, $reason = '')
    {
        $refund = new \PayPal\Api\Refund();
        $refund->setAmount($amountObj)->setReason($reason);
        return $refund;
    }
}