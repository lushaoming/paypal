<?php
/**
 * Class ${NAME}
 * @author 卢绍明<lusm@sz-bcs.com.cn>
 * @date   2019/8/28
 */
namespace Shannon\PayPal;

class PayPal
{

    private $config = array();

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function createPayment($order)
    {
        $payment = new Payment();
        $paypalPayment = $payment->init($order);
        var_dump($paypalPayment);
    }


    public function test($name)
    {
//        echo "Hello, {$name}";
        $config = PayPalConfig::getInstance()->getConfigs();
        var_dump($config);
    }
}