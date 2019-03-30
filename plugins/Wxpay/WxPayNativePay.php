<?php

namespace plugins\Wxpay;

use plugins\Wxpay\Core\Data\WxPayBizPayUrl;
use plugins\Wxpay\Core\Data\WxPayUnifiedOrder;

/**
 * 刷码支付实现类
 * @author widyhu
 */
class WxPayNativePay extends AbstractLib{

    /**
     *
     * 生成扫描支付URL,模式一
     * @param $productId
     * @return string
     * @throws Core\WxPayException
     */
	public function GetPrePayUrl($productId)
	{
		$biz = new WxPayBizPayUrl();
		$biz->SetProduct_id($productId);
		$values = $this->getWxPayApi()->bizpayurl($biz);
		$url = "weixin://wxpay/bizpayurl?" . $this->ToUrlParams($values);
		return $url;
	}
	
	/**
	 * 
	 * 参数数组转换为url参数
	 * @param array $urlObj
	 */
	private function ToUrlParams($urlObj)
	{
		$buff = "";
		foreach ($urlObj as $k => $v)
		{
			$buff .= $k . "=" . $v . "&";
		}
		
		$buff = trim($buff, "&");
		return $buff;
	}

    /**
     *
     * 生成直接支付url，支付url有效期为2小时,模式二
     * @param $input
     * @return bool
     * @throws Core\WxPayException
     */
	public function GetPayUrl(WxPayUnifiedOrder $input)
	{
		if($input->GetTrade_type() == "NATIVE")
		{
			$result = $this->getWxPayApi()->unifiedOrder($input);
			return $result;
		}
	}
}