<?php
$fileLogName = 'alipayDirectPayNotify';

$platform = 'alipay';
$client_id = 'external.alipay.direct.pay.notify';

fileLog($fileLogName, $_POST);
$result = curlPostStream(getHost($platform), json_encode(array(
    'client_id' => $client_id,
    'scope' => 'External.Alipay.directPayCallBack',
    'callback_data' => $_POST,
)));
$resultArr = json_decode($result, true);
switch ($resultArr['code']){
    case 200:
    case 202:
        exit('success');
        break;
    case 401:
    case 404:
    default:
        fileLog($fileLogName, $resultArr);
        exit('fail');
        break;
}
