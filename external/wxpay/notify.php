<?php
$fileLogName = 'wxpayNotify';

$platform = 'wxpay';
$client_id = 'external.wxpay.notify';
$input = file_get_contents('php://input');

fileLog($fileLogName, $input);
$result = curlPostStream(getHost($platform), json_encode(array(
    'client_id' => $client_id,
    'scope' => 'External.Wxpay.callback',
    'callback_data' => $input,
)));
$resultArr = json_decode($result, true);
switch ($resultArr['code']){
    case 200:
    case 202:
        exit('<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>');
        break;
    case 401:
    case 404:
    default:
        fileLog($fileLogName, $resultArr);
        exit();
        break;
}
