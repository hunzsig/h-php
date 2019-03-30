<?php
namespace plugins\Wxpay\Core\Data;

/**
 * 接口调用结果类
 * @author widyhu
 */
use plugins\Wxpay\Core\WxPayException;

class WxPayResults extends WxPayDataBase
{
	/**
	 *
	 * 检测签名
	 */
	public function CheckSign()
	{
		//fix异常
		if(!$this->IsSignSet()){
			throw new WxPayException("签名错误！");
		}

		$sign = $this->MakeSign();
		if($this->GetSign() == $sign){
			return true;
		}
		throw new WxPayException("签名错误！");
	}

	/**
	 *
	 * 使用数组初始化
	 * @param array $array
	 */
	public function FromArray($array)
	{
		$this->values = $array;
	}

	/**
	 *
	 * 使用数组初始化对象
	 * @param array $array
	 * @param bool $noCheckSign 是否检测签名
	 * @return WxPayResults
	 * @throws WxPayException
	 */
	public static function InitFromArray($array, $noCheckSign = false)
	{
		$obj = new self();
		$obj->FromArray($array);
		if($noCheckSign == false){
			$obj->CheckSign();
		}
		return $obj;
	}

	/**
	 *
	 * 设置参数
	 * @param string $key
	 * @param string $value
	 */
	public function SetData($key, $value)
	{
		$this->values[$key] = $value;
	}

    /**
     * 将xml转为array
     * @param string $xml
     * @param $configs
     * @return array
     * @throws WxPayException
     */
	public static function Init($xml, $configs)
	{
		$obj = new self();
        $obj->setConfigs($configs);
		$obj->FromXml($xml);
		//fix bug 2015-06-29
		if($obj->values['return_code'] != 'SUCCESS'){
			return $obj->GetValues();
		}
		$obj->CheckSign();
		return $obj->GetValues();
	}
}