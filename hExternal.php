<?php
define('REQUEST_METHOD', $_SERVER['REQUEST_METHOD']);
define('IS_GET', REQUEST_METHOD == 'GET' ? true : false);
define('IS_POST', REQUEST_METHOD == 'POST' ? true : false);
define('IS_PUT', REQUEST_METHOD == 'PUT' ? true : false);
define('IS_DELETE', REQUEST_METHOD == 'DELETE' ? true : false);
define('IS_AJAX', ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || !empty($_POST['ajax']) || !empty($_GET['ajax'])) ? true : false);

/**
 * 加密
 * @param $str
 * @return string
 */
function cryptoEncode($str)
{
    return openssl_encrypt($str, 'des-cbc', CONFIG['crypto_key'], 0, CONFIG['crypto_key']);
}

/**
 * 解密
 * @param $str
 * @return string
 */
function cryptoDecode($str)
{
    return openssl_decrypt($str, 'des-cbc', CONFIG['crypto_key'], 0, CONFIG['crypto_key']);
}

/**
 * 是否https
 * @return bool
 */
function isHttps()
{
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        return true;
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        return true;
    } elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
        return true;
    }
    return false;
}

/**
 * 获取host
 * @param $platform
 * @param bool $withHttp
 * @return string
 */
function getHost($platform, $withHttp = true)
{
    $host = '';
    if ($withHttp) {
        $host .= (isHttps()) ? 'https://' : 'http://';
    }
    $host .= $_SERVER['HTTP_HOST'] . "/[platform]{$platform}/[token]" . CONFIG['io_secret'];
    return $host;
}

/**
 * 建立POST请求，以表单HTML形式构造（默认）
 * @param string $method
 * @param array $params 请求参数数组
 * @param $action
 * @param string $encode
 * @return string 提交表单HTML文本
 */
function buildForm($method, $params, $action, $encode = 'utf-8')
{
    $encode = strtolower($encode);
    header("Content-type:text/html;charset={$encode}");
    //待请求参数数组
    $sHtml = "<form style='display:none;' id='postSubmit' name='postSubmit' action='" . $action . "' method='{$method}'>";
    foreach ($params as $key => $val) {
        $sHtml .= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
    }
    //submit按钮控件请不要含有name属性
    $sHtml = $sHtml . "<input type='submit' value='btn'></form>";
    $sHtml = $sHtml . "<script>document.forms['postSubmit'].submit();</script>";
    return $sHtml;
}

/**
 * 记录日志
 * @param $name
 * @param $data
 */
function fileLog($name, $data)
{
    $data = is_array($data) ? json_encode($data) : (string)$data;
    $data = PHP_EOL . '-----' . date('Y-m-d H:i:s T') . '-----' . PHP_EOL . $data . PHP_EOL;
    $dir = __DIR__ . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . date('Ymd');
    dirCheck($dir, true);
    @file_put_contents($dir . DIRECTORY_SEPARATOR . $name . '.log', $data, FILE_APPEND);
}
