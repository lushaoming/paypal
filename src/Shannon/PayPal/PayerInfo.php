<?php
/**
 * Class PayerInfo
 * @author 卢绍明<lusm@sz-bcs.com.cn>
 * @date   2019/8/29
 */
namespace Shannon\PayPal;

use PayPal\Api\FundingInstrument;
use PayPal\Api\Payer;

class PayerInfo
{
    public function setPayerInfo($payer, $shippingAddress, $billingAddress)
    {
        $payerInfo = new \PayPal\Api\PayerInfo();
        $payerInfo->setEmail($payer['email'])
            ->setBillingAddress($billingAddress)
            ->setShippingAddress($shippingAddress)
            ->setFirstName($payer['first_name'])
            ->setLastName($payer['last_name']);
        return $payerInfo;
    }

    public function setPayer(FundingInstrument $fi = null, \PayPal\Api\PayerInfo $payerInfo = null, string $paymentMethod = 'paypal')
    {
        $payer = new Payer();
        $payer->setPaymentMethod($paymentMethod);
        if ($fi !== null) $payer->setFundingInstruments([$fi]);
        if ($payerInfo !== null) $payer->setPayerInfo($payerInfo);
        return $payer;
    }
}