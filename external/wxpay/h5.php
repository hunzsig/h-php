<?php
$platform = 'wxpay';

$client_id = $jsapiParams['client_id'] ?? $_GET['client_id'] ?? null;
$external_config = $jsapiParams['external_config'] ?? $_GET['external_config'] ?? null;
$return_url = $jsapiParams['return_url'] ?? $_GET['return_url'] ?? null;
$order_no = $jsapiParams['order_no'] ?? $_GET['order_no'] ?? null;
$code = $_GET['code'] ?? null;

if (empty($client_id)) exit('not client_id');
if (empty($external_config)) exit('not client_id');
if (empty($order_no)) exit('not order');

$result = curlPostStream(getHost($platform), json_encode(array(
    'client_id' => $client_id,
    'scope' => 'External.Wxpay.h5',
    'external_config' => $external_config,
    'return_url' => $return_url,
    'back_url' => $back_url,
    'order_no' => $order_no,
    'code' => $code,
)));
$resultArr = json_decode($result, true);
if($resultArr['code'] == 200){
    header('Location:' . $resultArr['data']);
    exit();
} else {
    exit($result);
}



