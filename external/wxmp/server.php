<?php
$fileLogName = 'wxmpServer';

$client_id = 'external.wxmp.server';
$pathInfo = null;
!$pathInfo && $pathInfo = $_SERVER['REQUEST_URI'] ?? null;
!$pathInfo && $pathInfo = $_SERVER['PHP_SELF'] ?? null;
!$pathInfo && $pathInfo = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
$pathInfo = explode('?', $pathInfo);
$pathInfo = reset($pathInfo);
if (!$pathInfo) exit();
$pathInfo = explode('/', $pathInfo);
if (count($pathInfo) !== 5) exit();
$external_config = array_pop($pathInfo);
if (empty($external_config)) exit('not config');

$signature = $_GET['signature'] ?? null;
$timestamp = $_GET['timestamp'] ?? null;
$nonce = $_GET['nonce'] ?? null;
$msg_signature = $_GET['msg_signature'] ?? null;
$openid = $_GET['openid'] ?? null;
$echostr = $_GET['echostr'] ?? null;
fileLog($fileLogName, $_GET);

if ($echostr) {
    //todo 第一次验证模式
    $result = curlPostStream(getHost('wx'), json_encode(array(
        'client_id' => $client_id,
        'scope' => 'External.Wxmp.server1st',
        'external_config' => $external_config,
        'signature' => $signature,
        'timestamp' => $timestamp,
        'nonce' => $nonce,
        'msg_signature' => $msg_signature,
        'openid' => $openid,
    )));
    $resultArr = json_decode($result, true);
    fileLog($fileLogName, $resultArr);
    if ($resultArr['code'] == 200) {
        exit($echostr);
    } else {
        exit('error:' . $resultArr['response']);
    }
} else {
    //todo 后续的接收模式
    $input = file_get_contents("php://input");
    fileLog($fileLogName, $input);
    $result = curlPostStream(getHost('wx'), json_encode(array(
        'client_id' => $client_id,
        'scope' => 'External.Wxmp.server',
        'external_config' => $external_config,
        'behaviour' => $input,
        'signature' => $signature,
        'timestamp' => $timestamp,
        'nonce' => $nonce,
        'msg_signature' => $msg_signature,
        'openid' => $openid,
    )));
    fileLog($fileLogName, $result);
    $resultArr = json_decode($result, true);
    if ($resultArr['code'] == 200) {
        exit($resultArr['data']);
    } else {
        exit();
    }
}