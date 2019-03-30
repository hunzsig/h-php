<?php
$platform = 'wxpay';

$client_id = $_GET['client_id'] ?? null;
$external_config = $_GET['external_config'] ?? null;
$order_no = $_GET['order_no'] ?? null;
if (empty($external_config)) exit('not config');
if (empty($order_no)) exit('not order number');

$result = curlPostStream(getHost($platform), json_encode(array(
    'client_id' => $client_id,
    'scope' => 'External.Wxpay.callback',
    'external_config' => $external_config,
    'return_url' => $return_url,
    'order_no' => $order_no,
)));
$resultArr = json_decode($result, true);
if ($resultArr['code'] == 200) {
    exit($resultArr['data']);
} else {
    exit($result);
}





