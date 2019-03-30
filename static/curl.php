<?php

/**
 * 获取当前客户端IP
 * @return  string $ip
 */
function getClientIP()
{
    $ip = null;
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = isIp($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : $ip;
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = isIp($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $ip;
    } else {
        $ip = isIp($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : $ip;
    }
    return $ip;
}

/**
 * 伪造headers
 * @param $data
 * @return array
 */
function curlHeader($data = null)
{
    $clientIP = getClientIP() ?: '127.0.0.1';
    $header = array(
        "CLIENT-IP:{$clientIP}",
        "X-FORWARDED-FOR:{$clientIP}",
    );
    if (is_string($header)) {
        $header[] = 'Content-type:text/plain';
        $header[] = 'Content-Length:' . strlen($data);
    }
    return $header;
}

/**
 * curl - post
 * @param $host
 * @param array $data
 * @param int $timeout
 * @return string
 */
function curlPost($host, $data = array(), $timeout = 10)
{
    if (!$data) $data = array();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $host);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, curlHeader());
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function curlPostSSL($host, $data, $cert, $key, $ca, $timeout = 10)
{
    if (!$data) return false;
    $ch = curl_init();
    $header[] = "Content-type: text/xml";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_URL, $host);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    //因为过程中需要验证服务器和域名，故需要设置下面两行
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 只信任CA颁布的证书
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 检查证书中是否设置域名，并且是否与提供的主机名匹配
    curl_setopt($ch, CURLOPT_SSLCERT, $cert);
    curl_setopt($ch, CURLOPT_SSLKEY, $key);
    curl_setopt($ch, CURLOPT_CAINFO, $ca);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

/**
 * curl - post - stream
 * @param $host
 * @param string $data
 * @param int $timeout
 * @return mixed
 */
function curlPostStream($host, $data = '', $timeout = 10)
{
    if (!$data) $data = '';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $host);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, curlHeader($data));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

/**
 * curl - get
 * @param $src
 * @param $timeout
 * @return mixed
 */
function curlGet($src, $timeout)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $src);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, curlHeader());
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

