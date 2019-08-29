<?php
/**
 * Class BillingAddress
 * @author 卢绍明<lusm@sz-bcs.com.cn>
 * @date   2019/8/29
 */
namespace Shannon\PayPal;

class BillingAddress
{
    private $billingAddress;

    public function setBillingAddress($billing)
    {
        $this->billingAddress = new \PayPal\Api\Address();
        $this->billingAddress->setCountryCode($billing['country'])
            ->setState($billing['state'])
            ->setCity($billing['city'])
            ->setPostalCode($billing['postcode'])
            ->setLine1($billing['address_1'])
            ->setLine2($billing['address_2']);
        // 填了billing_phone才传
        if ($billing['phone']) $this->billingAddress->setPhone($billing['phone']);
    }

    public function getBillingAddress()
    {
        return $this->billingAddress;
    }
}