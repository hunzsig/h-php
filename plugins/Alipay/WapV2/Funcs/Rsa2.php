<?php
/* *
 * 支付宝接口RSA函数
 * 详细：RSA签名、验签、解密
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
 */
namespace plugins\Alipay\WapV2\Funcs;


class Rsa2{

	/**
	 * RSA验签
	 * @param $data 待签名数据
	 * @param $ali_public_key 支付宝的公钥字符串
	 * @param $sign 要校对的的签名结果
	 * @return bool
     */
	public function rsaVerify($data, $ali_public_key, $sign)  {
        $res = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($ali_public_key, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";
        ($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');
		$result = (bool)openssl_verify($data, base64_decode($sign), $res , OPENSSL_ALGO_SHA256);
		return $result;
	}

}
?>