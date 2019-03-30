<?php
namespace plugins\Wxpay\Core;
/**
 * 微信支付API异常类
 * @author hunzsig
 * @date 2015-11-21
 */
class WxPayException extends \Exception{

	public function errorMessage()
	{
		return $this->getMessage();
	}

}
