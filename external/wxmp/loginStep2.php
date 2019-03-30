<?php
$platform = $_GET['platform'] ?? null;
$client_id = $_GET['client_id'] ?? null;
$external_config = $_GET['external_config'] ?? null;
$return_url = $_GET['return_url'] ?? null;

//todo 1# 获取ACCESS_TOKEN / OPENID
$code = $_GET['code'] ?? null;
$state = $_GET['state'] ?? null;

if (empty($platform)) exit('errorp');
if (empty($code)) exit('error1');
if (empty($state)) exit('error2');
if (empty($external_config)) exit('error3');
if (empty($client_id)) exit('error4');
if (empty($return_url)) exit('error5');

$result = curlPostStream(getHost($platform), json_encode(array(
    'client_id' => $client_id,
    'scope' => 'External.Wxmp.getUserInfo',
    'behaviour' => 'login.step2',
    'external_config' => $external_config,
    'code' => $code,
    'extra' => !empty($_GET['extra']) ? json_decode(urldecode($_GET['extra'])) : null,
)));
$resultArr = json_decode($result, true);
if ($resultArr['code'] == 200 || $resultArr['code'] == 202) {
    exit(buildForm('get', $resultArr['data'], $return_url));
}
exit($result);