<?php
require "hPhp.php";

use library\IO;

/**
 * TODO *启动
 * 服务器挂载
 */
class Main extends IO
{

    private $external = array();

    public function __construct()
    {
        $this->external('alipayDirectPay', __DIR__ . '/external/alipay/directPay.php');
        $this->external('alipayDirectPayNotify', __DIR__ . '/external/alipay/directPayNotify.php');
        $this->external('alipayDirectPayReturn', __DIR__ . '/external/alipay/directPayReturn.php');
        $this->external('alipayWapV1', __DIR__ . '/external/alipay/wapV1.php');
        $this->external('alipayWapV1Notify', __DIR__ . '/external/alipay/wapV1Notify.php');
        $this->external('alipayWapV1Return', __DIR__ . '/external/alipay/wapV1Return.php');
        $this->external('alipayWapV2', __DIR__ . '/external/alipay/wapV2.php');
        $this->external('alipayWapV2Notify', __DIR__ . '/external/alipay/wapV2Notify.php');
        $this->external('alipayWapV2Return', __DIR__ . '/external/alipay/wapV2Return.php');
        $this->external('wxmpLoginStep1', __DIR__ . '/external/wxmp/loginStep1.php');
        $this->external('wxmpLoginStep2', __DIR__ . '/external/wxmp/loginStep2.php');
        $this->external('wxmpServer', __DIR__ . '/external/wxmp/server.php');
        $this->external('wxpayH5', __DIR__ . '/external/wxpay/h5.php');
        $this->external('wxpayJsapi', __DIR__ . '/external/wxpay/jsapi.php');
        $this->external('wxpayNative', __DIR__ . '/external/wxpay/native.php');
        $this->external('wxpayNotify', __DIR__ . '/external/wxpay/notify.php');
        if (IS_DEV) {
            $this->external('test', __DIR__ . '/external/test/index.php');
            $this->external('map', __DIR__ . '/external/test/map.php');
        }
    }

    public function run()
    {
        $pathInfo = null;
        !$pathInfo && $pathInfo = $_SERVER['REQUEST_URI'] ?? null;
        !$pathInfo && $pathInfo = $_SERVER['PHP_SELF'] ?? null;
        !$pathInfo && $pathInfo = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
        $pathInfo = explode('?', $pathInfo);
        $pathInfo = reset($pathInfo);
        if (!$pathInfo) exit();
        $pathInfo = explode('/', $pathInfo);
        $path1 = $pathInfo[1] ?? null;
        $path2 = $pathInfo[2] ?? null;
        // TODO EXTERNAL
        if ($path1 === 'external') {
            if (!empty($this->external[$path2])) {
                @require('hExternal.php');
                foreach ($this->external[$path2] as $e) {
                    @require($e);
                }
                exit();
            }
            exit('not this external');
        }
        // TODO JSON - IO
        $request = array(
            'header' => array(),
            'server' => array(),
            'files' => $_FILES,
            'post' => null,
        );
        if (!$request['post'] && !empty($_POST['post'])) {
            $request['post'] = $_POST['post'];
        }
        if (!$request['post']) {
            $request['post'] = file_get_contents('php://input');
        }
        foreach ($_SERVER as $k => $v) {
            if (strpos($k, 'HTTP_') === 0) {
                $request['header'][strtolower(str_replace('HTTP_', '', $k))] = $v;
            } else {
                $request['server'][strtolower($k)] = $v;
            }
        }
        $data = $this->io($request);
        header('Content-Type:application/json; charset=utf-8');
        exit($data);
    }

    /**
     * 拓展方法
     * @param $name
     * @param $uri
     * @return Main
     */
    public function external($name, $uri)
    {
        if (!$name || !$uri) return $this;
        if (empty($this->external[$name])) {
            $this->external[$name] = array();
        };
        $this->external[$name][] = $uri;
        return $this;
    }
}
