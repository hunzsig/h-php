<?php
$platform = $_GET['platform'] ?? null;
$client_id = $_GET['client_id'] ?? null;
$external_config = $_GET['external_config'] ?? null;
$return_url = $_GET['return_url'] ?? null;

if (empty($platform)) exit('not p');
if (empty($client_id)) exit('not client');
if (empty($external_config)) exit('not config');
if (empty($return_url)) exit('not return');

$result = curlPostStream(getHost($platform), json_encode(array(
    'client_id' => $client_id,
    'scope' => 'External.Wxmp.getUserInfo',
    'behaviour' => 'login.step1',
    'external_config' => $external_config,
    'return_url' => $return_url,
    'extra' => !empty($_GET['extra']) ? json_decode(urldecode($_GET['extra'])) : null,
)));
$resultArr = json_decode($result, true);
if ($resultArr['code'] == 200 || $resultArr['code'] == 202) {
    if (is_array($resultArr['data'])) {
        exit(buildForm('get', $resultArr['data'], $return_url));
    } elseif (is_string($resultArr['data'])) {
        header('Location:' . $resultArr['data']);
        exit();
    } else {
        exit($result);
    }
} else {
    print_r($resultArr);
    exit();
}
