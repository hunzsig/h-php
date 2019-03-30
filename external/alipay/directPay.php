<?php
$platform = 'alipay';

$client_id = $_GET['client_id'] ?? null;
$external_config = $_GET['external_config'] ?? null;
$return_url = $_GET['return_url'] ?? null;
$back_url = $_GET['back_url'] ?? null;
$order_no = $_GET['order_no'] ?? null;
if (empty($external_config)) exit('not config');
if (empty($return_url)) exit('not return url');
if (empty($back_url)) exit('not back url');
if (empty($order_no)) exit('not order number');

$result = curlPostStream(getHost($platform), json_encode(array(
    'client_id' => $client_id,
    'scope' => 'External.Alipay.directPay',
    'external_config' => $external_config,
    'return_url' => $return_url,
    'back_url' => $back_url,
    'order_no' => $order_no,
)));
$resultArr = json_decode($result, true);
if ($resultArr['code'] == 200) {
    header("Content-type:text/html;charset=utf-8");
    exit($resultArr['data']);
} else {
    exit($result);
}





