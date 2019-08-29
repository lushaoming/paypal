<?php
/**
 * Class ${NAME}
 * @author 卢绍明<lusm@sz-bcs.com.cn>
 * @date   2019/8/28
 */
namespace Shannon\PayPal;

use \PayPal\Rest\ApiContext as PayPalContext;

class ApiContext
{
    private static $instance;

    private $clientId;
    private $clientSecret;

    private function __construct()
    {
        $this->clientId = PayPalConfig::getInstance()->get('client_id');
        $this->clientSecret = PayPalConfig::getInstance()->get('client_secret');
    }

    /**
     * Returns the singleton object
     * @param $config
     * @return $this
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function createContext()
    {
        return (new PayPalContext(new \PayPal\Auth\OAuthTokenCredential(
            $this->clientId,
            $this->clientSecret
        )));
    }
}