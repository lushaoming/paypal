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

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * @param $order
     * @return \PayPal\Api\Payment
     * @author 卢绍明<lusm@sz-bcs.com.cn>
     * @date   2019/8/29
     */
    public function createPayment($order)
    {
        $payment = new Payment();
        $paypalPayment = $payment->init($order)
            ->create($this->config);

        return $paypalPayment;
    }

    /**
     * @param $paymentId
     * @param $payerId
     * @return mixed
     * @author 卢绍明<lusm@sz-bcs.com.cn>
     * @date   2019/8/29
     */
    public function receiptPayment($paymentId, $payerId)
    {
        $payment = new Payment();
        return $payment->receiptPayment($paymentId, $payerId);
    }

    /**
     * @param string $transactionId PayPal交易ID
     * @param float  $total 退款金额
     * @param string $currency 货币
     * @param string $reason 原因
     * @return array
     * @throws ShannonPaypalException
     * @author 卢绍明<lusm@sz-bcs.com.cn>
     * @date   2019/8/30
     */
    public function refund($transactionId, $total, $currency = 'USD', $reason = '')
    {
        $refund = new Refund();
        return $refund->execute($transactionId, $total, $currency, $reason);
    }

    public function test()
    {
        $config = PayPalConfig::getInstance()->getConfigs();
        var_dump($config);
    }
}