<?php
namespace Alipay\Lib\Fuwuchuang;


class Gateway extends AbstractLib{

	public function verifygw($is_sign_success){
		$biz_content = HttpRequest::getRequest("biz_content");
		$as = new AlipaySign();
		$xml = simplexml_load_string($biz_content);
		// print_r($xml);
		$EventType =(string)$xml->EventType;
		// echo $EventType;
		if($EventType == "verifygw"){
			$config = $this->getConfig();
			// global $config;
			// print_r($config);
			if($is_sign_success){
				$response_xml = "<success>true</success><biz_content>" . $as->getPublicKeyStr($config['merchant_public_key_file']). "</biz_content>";
			} else { // echo $response_xml;
				$response_xml = "<success>false</success><error_code>VERIFY_FAILED</error_code><biz_content>" . $as->getPublicKeyStr($config['merchant_public_key_file']). "</biz_content>";
			}
			$return_xml = $as->sign_response($response_xml, $config ['charset'], $config ['merchant_private_key_file']);
			//writeLog("response_xml: " . $return_xml);
			echo $return_xml;
			exit();
		}
	}

}