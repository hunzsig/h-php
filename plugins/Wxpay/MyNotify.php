<?php

namespace plugins\Wxpay;

/**
 * 支付回调类
 * @author hunzsig
 */

use plugins\Wxpay\Core\Log;
use plugins\Wxpay\Core\Data\WxPayOrderQuery;
use plugins\Wxpay\Core\WxPayNotify;
use plugins\Wxpay\Core\WxPayApi;


class MyNotify extends WxPayNotify
{

    /**
     * 回调入口
     * @param function $callback
     * @param $needSign
     * @return array 返回数据
     */
    final public function MyHandle($callback, $needSign, $xml)
    {
        $msg = "OK";
        $WxPayApi = (new WxPayApi());
        $WxPayApi->setConfigs($this->getConfigs());
        $result = $WxPayApi->notify(null, $msg, $xml);
        if ($callback) $result = call_user_func($callback, $result);
        else $result = true;
        if ($result == true) {
            $this->SetReturn_code("SUCCESS");
            $this->SetReturn_msg("OK");
        } else {
            $this->SetReturn_code("FAIL");
            $this->SetReturn_msg($msg);
        }

        return $result;
    }

    //查询订单
    public function Queryorder($transaction_id)
    {
        $input = new WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $WxPayApi = (new WxPayApi());
        $WxPayApi->setConfigs($this->getConfigs());
        $result = $WxPayApi->orderQuery($input);
        Log::DEBUG("query:" . json_encode($result));
        if (array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS") {
            return true;
        }
        return false;
    }

}