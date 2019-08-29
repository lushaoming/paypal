<?php
/**
 * Class ShippingAddress
 * @author 卢绍明<lusm@sz-bcs.com.cn>
 * @date   2019/8/29
 */
namespace Shannon\PayPal;

class ShippingAddress
{
    private $shippingAddress;

    public function setShippingAddress($shipping)
    {
        $this->shippingAddress = new \PayPal\Api\ShippingAddress();
        $this->shippingAddress->setRecipientName($shipping['first_name'] . ' ' . $shipping['last_name'])
            ->setPhone($shipping['phone'])
            ->setCountryCode($shipping['country'])
            ->setState($shipping['state'])
            ->setCity($shipping['city'])
            ->setPostalCode($shipping['postal_code'])
            ->setLine1($shipping['address_1'])
            ->setLine2($shipping['address_2'])
            ->setPreferredAddress(empty($shipping['preferred']) ? false : true);
    }

    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }
}