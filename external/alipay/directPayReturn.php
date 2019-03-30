<?php
$platform = 'alipay';
$client_id = 'external.alipay.direct.pay.return';

$result = curlPostStream(getHost($platform), json_encode(array(
    'client_id' => $client_id,
    'scope' => 'External.Alipay.directPayCallBack',
    'callback_data' => $_GET,
)));
$resultArr = json_decode($result, true);
switch ($resultArr['code']){
    case 401:
        $return_url = $resultArr['data']['back_url'];
        if($return_url){
            $resultArr['data']['behaviour'] = 'error';
            $resultArr['data']['response'] = $resultArr['response'];
            unset($resultArr['data']['back_url']);
            exit(buildForm('get', $resultArr['data'], $return_url));
        }
        break;
    case 200:
        $return_url = $resultArr['data']['return_url'];
        if($return_url){
            $resultArr['data']['behaviour'] = 'success';
            $resultArr['data']['response'] = $resultArr['response'];
            unset($resultArr['data']['return_url']);
            exit(buildForm('get', $resultArr['data'], $return_url));
        }
        break;
    case 202:
        $return_url = $resultArr['data']['return_url'];
        if($return_url){
            $resultArr['data']['behaviour'] = 'finish';
            $resultArr['data']['response'] = $resultArr['response'];
            unset($resultArr['data']['return_url']);
            exit(buildForm('get', $resultArr['data'], $return_url));
        }
        break;
    case 404:
    default:
        break;
}
exit($result);
