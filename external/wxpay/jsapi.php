<?php
$platform = 'wxpay';

if (!empty($_GET['jsapi_params'])) {
    $jsapiParams = $_GET['jsapi_params'];
    $jsapiParams = str_replace('*', '=', $jsapiParams);
    $jsapiParams = str_replace('-', '+', $jsapiParams);
    $jsapiParams = str_replace('|', '/', $jsapiParams);
    $jsapiParams = base64_decode($jsapiParams);
    $jsapiParams = cryptoDecode($jsapiParams);
    $jsapiParams = unserialize($jsapiParams);
}

$client_id = $jsapiParams['client_id'] ?? $_GET['client_id'] ?? null;
$external_config = $jsapiParams['external_config'] ?? $_GET['external_config'] ?? null;
$return_url = $jsapiParams['return_url'] ?? $_GET['return_url'] ?? null;
$back_url = $jsapiParams['back_url'] ?? $_GET['back_url'] ?? null;
$order_no = $jsapiParams['order_no'] ?? $_GET['order_no'] ?? null;
$code = $_GET['code'] ?? null;

if (empty($client_id)) {
    $error = 'not client_id';
    goto fail;
}
if (empty($external_config)) {
    $error = 'not config';
    goto fail;
}
if (empty($return_url)) {
    $error = 'not return';
    goto fail;
}
if (empty($back_url)) {
    $error = 'not back';
    goto fail;
}
if (empty($order_no)) {
    $error = 'not order';
    goto fail;
}

$result = curlPostStream(getHost($platform), json_encode(array(
    'client_id' => $client_id,
    'scope' => 'External.Wxpay.jsapi',
    'external_config' => $external_config,
    'return_url' => $return_url,
    'back_url' => $back_url,
    'order_no' => $order_no,
    'code' => $code,
)));

$resultArr = json_decode($result, true);
switch ($resultArr['code']) {
    case 202:
        header($resultArr['data']);
        exit();
        break;
    case 200:
        $resultData = $resultArr['data'];
        goto success;
        break;
    case 401:
    default:
        $error = $resultArr['response'] ?? 'unKnow';
        goto fail;
        break;
}
/** - **/
fail:
$jqueryJs = file_get_contents(__DIR__ . '/assets/jquery-3.1.0.min.js');
$wxpayJsapiCss = file_get_contents(__DIR__ . '/assets/wxpayJsapi.css');
$WePayLogoPng = 'data:png;base64,'.base64_encode(file_get_contents(__DIR__ . '/assets/WePayLogo.png'));
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name=viewport content="width=device-width,initial-scale=1,user-scalable=no,maximum-scale=1">
    <title>支付遇到了问题</title>
    <style>
        <?php echo $wxpayJsapiCss;?>
    </style>
    <script>
        <?php echo $jqueryJs;?>
    </script>
    <script type="text/javascript">
        (function (doc, win) {
            var docElScreenWidth = 375;
            var docElScreenWidthMax = 640;
            var docEl = doc.documentElement,
                resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
                recalc = function () {
                    var clientWidth = docEl.clientWidth;
                    if (!clientWidth) return;
                    docEl.style.fontSize = (clientWidth <= 640)
                        ? 100 * (clientWidth / docElScreenWidth) + 'px'
                        : 100 * (clientWidth / docElScreenWidthMax) + 'px';
                };

            if (!doc.addEventListener) return;
            win.addEventListener(resizeEvt, recalc, false);
            doc.addEventListener('DOMContentLoaded', recalc, false);
        })(document, window);
        <?php if(!empty($back_url)):?>
        $(function () {
            function pushHistory() {
                var state = {title: "title", url: "#"};
                window.history.pushState(state, "title", "#");
            }

            pushHistory();
            window.addEventListener("popstate", function (e) {
                location.replace('<?php echo $back_url;?>');
            }, false);
        });
        <?php endif;?>
        function back_url() {
            <?php if(!empty($back_url)):?>
            location.replace('<?php echo $back_url;?>');
            <?php endif;?>
        }
    </script>
</head>
<body>
<div class="wxPayBox t2">
    <div class="top">
        <img title="微信支付LOGO" src="<?php echo $WePayLogoPng;?>">
    </div>
    <div class="middle">
        <p>很抱歉，支付中途遇到了问题</p>
        <?php if ($error) {
            echo "<p>$error</p>";
        } ?>
    </div>
    <div class="bottom">
        <button type="button" onclick="back_url()">我知道了</button>
    </div>
</div>
</body>
</html>
<?php exit(); ?>
<?php
/** - **/
success:
$jqueryJs = file_get_contents(__DIR__ . '/assets/jquery-3.1.0.min.js');
$wxpayJsapiCss = file_get_contents(__DIR__ . '/assets/wxpayJsapi.css');
$WePayLogoPng = 'data:png;base64,'.base64_encode(file_get_contents(__DIR__ . '/assets/WePayLogo.png'));
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name=viewport content="width=device-width,initial-scale=1,user-scalable=no,maximum-scale=1">
    <title> * 请确认交易信息</title>
    <style>
        <?php echo $wxpayJsapiCss;?>
    </style>
    <script>
        <?php echo $jqueryJs;?>
    </script>
    <script type="text/javascript">
        (function (doc, win) {
            var docElScreenWidth = 375;
            var docElScreenWidthMax = 640;
            var docEl = doc.documentElement,
                resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
                recalc = function () {
                    var clientWidth = docEl.clientWidth;
                    if (!clientWidth) return;
                    docEl.style.fontSize = (clientWidth <= 640)
                        ? 100 * (clientWidth / docElScreenWidth) + 'px'
                        : 100 * (clientWidth / docElScreenWidthMax) + 'px';
                };

            if (!doc.addEventListener) return;
            win.addEventListener(resizeEvt, recalc, false);
            doc.addEventListener('DOMContentLoaded', recalc, false);
        })(document, window);
        <?php if(!empty($back_url)):?>
        $(function () {
            function pushHistory() {
                var state = {title: "title", url: "#"};
                window.history.pushState(state, "title", "#");
            }

            pushHistory();
            window.addEventListener("popstate", function (e) {
                location.replace('<?php echo $back_url;?>');
            }, false);
        });
        <?php endif;?>
    </script>
</head>
<body>
<div class="wxPayBox t1">
    <div class="top">
        <img title="微信支付LOGO" src="<?php echo $WePayLogoPng;?>">
    </div>
    <div class="middle">
        <p>温馨提示：请不要在支付途中刷新页面</p>
        <div class="amount">这笔交易要支付，&yen; <span class="money"><?php echo $resultData['total_fee']; ?></span> 元</div>
        <table class="description">
            <tr>
                <td width="20%">交易号</td>
                <td><?php echo $resultData['out_trade_no']; ?></td>
            </tr>
            <tr>
                <td>订单号</td>
                <td><?php echo $resultData['order_no']; ?></td>
            </tr>
            <tr>
                <td>名&emsp;称</td>
                <td><?php echo $resultData['subject']; ?></td>
            </tr>
            <tr>
                <td>描&emsp;述</td>
                <td><?php echo $resultData['body']; ?></td>
            </tr>
        </table>
        <!--<div style="word-break: break-all"><?php // var_dump($resultData['jsapi_params']); ?></div>-->
    </div>
    <div class="bottom">
        <button type="button" onclick="pay()">确认并支付</button>
    </div>
</div>
<script type="text/javascript">

    var return_url = '<?php echo $return_url;?>';

    //调用微信JS api 支付
    function jsApiCall() {
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest',
            <?php echo $resultData['jsapi_params'];?>,
            function (res) {
                WeixinJSBridge.log(res.err_msg);
                alert(res.err_code + res.err_desc + res.err_msg);
            }
        );
    }


    function pay() {
		try{
			if (WeixinJSBridge === undefined) {
				if (document.addEventListener) {
					document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
				} else if (document.attachEvent) {
					document.attachEvent('WeixinJSBridgeReady', jsApiCall);
					document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
				}
			} else {
				jsApiCall();
			}
		} catch(e) {
			alert(e.message);
		}
    }

    function combineUrl(url, params) {
        params = params || null;
        if (typeof url !== 'string' || url.length <= 0) {
            return;
        }
        if (url === 'this') {
            url = location.pathname;
        }
        let paramsStr = '';
        if (params != null) {
            let key;
            for (key in params) {
                const pd = encodeURIComponent(params[key]);
                if (paramsStr === '') {
                    paramsStr += `?${key}=${pd}`;
                } else {
                    paramsStr += `&${key}=${pd}`;
                }
            }
        }
        return url + paramsStr;
    };

    // check is pay
    if (return_url.length > 1) {
        setInterval(function () {
            $.ajax({
                type: "POST",
                async: true,
                url: "/[platform]admin/[token]<?php echo CONFIG['io_secret'];?>",
                data: {
                    post: JSON.stringify({
                        scope: 'External.TradeToken.getInfo',
                        client_id: '<?php echo $client_id;?>',
                        out_trade_no: '<?php echo $resultData['out_trade_no'];?>',
                    })
                },
                success: function (res) {
                    if (res.code === 200 && typeof res.data === "object") {
                        if (res.data.external_trade_token_is_pay === 1 || res.data.external_trade_token_is_pay === '1') {
                            var url = combineUrl(return_url, {
                                out_trade_no: res.data.external_trade_token_out_trade_no,
                                order_no: res.data.external_trade_token_order_no,
                                amount: res.data.external_trade_token_amount,
                                type: res.data.external_trade_token_type,
                            });
                            location.replace(url);
                        }
                    }
                },
                error: function () {
                    console.log('error');
                }
            });
        }, 2500);
    }
</script>
</body>
</html>
<?php exit(); ?>




