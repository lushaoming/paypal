<?php
/**
 * Class ${NAME}
 * @author 卢绍明<lusm@sz-bcs.com.cn>
 * @date   2019/8/28
 */
require_once 'vendor/autoload.php';
$paypal = new \Shannon\PayPal\PayPal();
$paypal->test('Ming');