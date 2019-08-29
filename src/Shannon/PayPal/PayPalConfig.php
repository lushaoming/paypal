<?php
/**
 * Class PayPalConfig
 * @author 卢绍明<lusm@sz-bcs.com.cn>
 * @date   2019/8/28
 */
namespace Shannon\PayPal;

class PayPalConfig
{
    private $configs = array();

    private static $instance;

    private function __construct()
    {
        if (defined('PAYPAL_CONFIG_PATH')) {
            $configFile = PAYPAL_CONFIG_PATH . '/config.ini';
        } else {
            $configFile = __DIR__ . '/config.ini';
        }

        if (file_exists($configFile)) {
            $this->addConfigFromIni($configFile);
        }
    }

    /**
     * Returns the singleton object
     *
     * @return $this
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Add Configuration from configuration.ini files
     *
     * @param string $fileName
     * @return $this
     */
    public function addConfigFromIni($fileName)
    {
        if ($configs = parse_ini_file($fileName)) {
            $this->addConfigs($configs);
        }
        return $this;
    }

    /**
     * If a configuration exists in both arrays,
     * then the element from the first array will be used and
     * the matching key's element from the second array will be ignored.
     *
     * @param array $configs
     * @return $this
     */
    public function addConfigs($configs = array())
    {
        $this->configs = $configs + $this->configs;
        return $this;
    }

    /**
     * Simple getter for configuration params
     * If an exact match for key is not found,
     * does a "contains" search on the key
     *
     * @param string $searchKey
     * @return array
     */
    public function get($searchKey)
    {

        if (array_key_exists($searchKey, $this->configs)) {
            return $this->configs[$searchKey];
        } else {
            $arr = array();
            foreach ($this->configs as $k => $v) {
                if (strstr($k, $searchKey)) {
                    $arr[$k] = $v;
                }
            }

            return $arr;
        }

    }

    public function getConfigs()
    {
        return $this->configs;
    }


}