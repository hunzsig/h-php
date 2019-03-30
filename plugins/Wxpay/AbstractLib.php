<?php

namespace plugins\Wxpay;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/22
 * Time: 下午 12:52
 */

use plugins\Wxpay\Core\WxPayApi;

abstract class AbstractLib
{

    private $wxpayApi;
    public $wxConfig = array();

    /**
     * @return WxPayApi
     */
    protected function getWxPayApi()
    {
        if (!$this->wxpayApi){
            $this->wxpayApi = (new WxPayApi());
            $this->wxpayApi->setConfigs($this->wxConfig);
        }
        return $this->wxpayApi;
    }

    /**
     * 根据获取配置
     * @param array $configs
     * @return mixed
     */
    public function setConfigs($configs)
    {
        $this->wxConfig = $configs;
    }

    /**
     * 获取微信支付配置
     * @param null $key
     * @return mixed
     */
    protected function getConfigs($key = null)
    {
        return $key ? ($this->wxConfig[$key] ?? null) : $this->wxConfig;
    }

}