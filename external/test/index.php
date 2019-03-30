<?php
if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1') {
    exit();
}
function getModel($dir, $model = array())
{
    $files = opendir($dir);
    while ($file = readdir($files)) {
        if ($file != '.' && $file != '..') {
            $realFile = $dir . '/' . $file;
            if (is_dir($realFile)) {
                $model = getModel($realFile, $model);
            } elseif (strpos($file, PHP_EXT) === false) {
                continue;
            } elseif (strpos($realFile, 'Model') !== false
                && strpos($realFile, 'Common') === false
                && strpos($realFile, 'Abstract') === false) {
                $model[] = $realFile;
            }
        }
    }
    closedir($files);
    return $model;
}

$model = getModel(PATH_APP);
$activeDir = str_replace('\\', '/', PATH_APP);
$api = array();
foreach ($model as $m) {
    if (strpos($m, '__') !== false) {
        continue;
    }
    $m = str_replace('\\', '/', $m);
    $mArr = explode('/', $m);
    $modelDir = $m;
    $modelDir = str_replace($activeDir, '', $modelDir);
    $modelDir = str_replace(PHP_EXT, '', $modelDir);
    $beanDir = str_replace('Model', 'Bean', $modelDir);
    $beanDir = str_replace('/', '\\', $beanDir);
    $modelDir = str_replace('/', '\\', $modelDir);
    $tempBean = (new $beanDir());
    $tempModel = (new $modelDir());

    $sss = str_replace('Bean', '.', $beanDir);
    $sss = str_replace('\\', '', $sss);
    $class = get_class_methods($modelDir);
    //print_r($class);

    foreach ($class as $c) {
        if (strpos($c, '__') !== false) {
            continue;
        }
        $s = $sss . $c;
        $p = array_keys($tempBean->toArray());
        $isCommon = false;
        $pCom = $pPri = array();
        foreach ($p as $pp) {
            if ($pp === 'limit') {
                $isCommon = true;
            }
            if ($isCommon) $pCom[] = $pp;
            else $pPri[] = $pp;
        }
        sort($pCom);
        sort($pPri);
        $api[$sss][$c] = array(
            'pCom' => $pCom,
            'pPri' => $pPri,
        );
    }
}
$token = CONFIG['io_secret'];
$clientID = md5($token);
$url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . "/[platform]admin/[token]" . $token;
$apiJson = json_encode($api);
$jqueryJs = file_get_contents(__DIR__ . '/assets/jquery.min.js');
$amazeuiJs = file_get_contents(__DIR__ . '/assets/amazeui.min.js');
$amazeuiCss = file_get_contents(__DIR__ . '/assets/amazeui.min.css');
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, viewport-fit=cover">
    <script>
        <?php echo($jqueryJs);?>
        <?php echo($amazeuiJs);?>
    </script>
    <style>
        .cPri {
            background: #e04240;
            border-color: #e03323;
            color: #ffffff;
        }

        .cCom.authUid, .cCom.page, .cCom.pagePer {
            background: #42ab42;
            border-color: #21ab49;
            color: #ffffff;
        }

        <?php echo($amazeuiCss);?>
    </style>
</head>
<body>

<div class="am-g">
    <div class="am-u-md-12 am-u-lg-2">
        <form id="hFrom" class="am-form" action="<?php echo $url; ?>">
            <fieldset>
                <legend>接口测试</legend>
                <div class="am-g">
                    <div class="am-u-sm-6" style="padding-right: 0">
                        <select id="doc-select-1" name="scope1" onchange="buildScope1(this.value);">
                            <option value="">- Nothing -</option>
                            <?php foreach ($api as $sk => $s): ?>
                                <option value="<?php echo $sk; ?>"><?php echo $sk; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="am-u-sm-6" style="padding-left: 0" id="doc-select-2"></div>
                </div>
                <br/>
                <div class="am-panel am-panel-default" style="padding: 4px">Scope：<label id="scope1"></label><label
                            id="scope2"></label></div>
                <p style="text-align: center;">
                    <button type="submit" class="am-btn am-btn-default">提交</button>
                </p>
            </fieldset>
        </form>
        <div class="am-alert am-alert-secondary" data-am-alert>
            <pre id="request" style="max-height: 500px;overflow: auto;">请求</pre>
        </div>
    </div>
    <form id="hFrom2" class="am-u-md-12 am-u-lg-5">
        <legend>填写接受的参数</legend>
        <div id="paramsBox" style="max-height: 780px;overflow-y: auto;overflow-x: hidden;"></div>
    </form>
    <div class="am-u-md-12 am-u-lg-5">
        <div class="am-alert am-alert-danger" data-am-alert>
            <p id="tips">提交后的错误会显示在这里</p>
        </div>
        <div class="am-alert am-alert-secondary" data-am-alert>
            <pre id="result" style="max-height: 800px;overflow: auto;">结果</pre>
        </div>
    </div>
</div>
<script>
    var cache = {};
    $(function () {
        $('.am-alert').alert();
        $('#hFrom').on('submit', function () {
            try {
                const action = $(this)[0].action;
                let i, params = {};
                let timeStart = (new Date()).getTime();
                const serialize = $('#hFrom,#hFrom2').serializeArray();
                for (i in serialize) {
                    params[serialize[i]['name']] = serialize[i]['value'];
                }
                if (!params['scope1'] && !params['scope2']) { // scope
                    $('.am-alert #tips').html('缺少 scope');
                    $('.am-alert #result').text('错误');
                    return false;
                }
                params['scope'] = params['scope1'] + params['scope2'];
                const request = new XMLHttpRequest();
                request.onreadystatechange = function () {
                    let time = (new Date()).getTime() - timeStart;
                    switch (request.readyState) {
                        case 4:
                            switch (request.status) {
                                case 200:
                                    try {
                                        const res = JSON.parse(request.responseText);
                                        if (res.code === 200) {
                                            $('.am-alert #tips').html('OK （' + "耗时" + time + "毫秒）");
                                            $('.am-alert #result').text(JSON.stringify(res, null, 2));
                                        } else {
                                            $('.am-alert #tips').html(res.response + " （耗时" + time + "毫秒）");
                                            $('.am-alert #result').text(JSON.stringify(res, null, 2));
                                        }
                                    } catch (e) {
                                        $('.am-alert #tips').html('崩啦崩啦～ （' + "耗时" + time + "毫秒）");
                                        $('.am-alert #result').text(request.responseText);
                                    }
                                    break;
                                default:
                                    $('.am-alert #tips').html('FAIL （' + "耗时" + time + "毫秒）");
                                    $('.am-alert #result').text(request.status);
                                    break;
                            }
                            break;
                        case 1:
                            $('.am-alert #tips').html('ING （' + "请求中）");
                            $('.am-alert #result').text('..............');
                            break;
                        default:
                            $('.am-alert #tips').html('FAIL （' + "耗时" + time + "毫秒）");
                            $('.am-alert #result').text(request.readyState);
                            break;
                    }
                };
                let data = JSON.stringify({
                    client_id: '<?php echo $clientID;?>',
                    ...params,
                });
                request.open("POST", action);
                request.send(data);
                delete params['scope1'];
                delete params['scope2'];
                for (const i in params) {
                    if (params[i] === '') {
                        delete params[i];
                    }
                }
                $('.am-alert #request').text(JSON.stringify({
                    client_id: '<?php echo $clientID;?>',
                    ...params
                }, null, 2));
            } catch (e) {
                $('.am-alert #tips').html(e.message);
                $('.am-alert').alert();
            }
            return false;
        });
    });
    let apiJson = eval(<?php echo $apiJson;?>);
    let buildScope1 = function (id) {
        if (id.length <= 0) {
            $('.am-alert #tips').html('Nothing！');
            return;
        }
        $('#scope1').html(id);
        console.log(apiJson[id]);
        let str = "";
        let i;
        str += '<select id="doc-select-2" name="scope2" onchange="buildScope2(this.value);"><option value="">- Nothing -</option>';
        for (i in apiJson[id]) {
            str += '<option value="' + i + '">' + i + '</option>';
        }
        str += '</select>';
        document.getElementById('doc-select-2').innerHTML = str;
    };
    let buildScope2 = function (id) {
        if (id.length <= 0) {
            $('.am-alert #tips').html('Nothing！');
            return;
        }
        $('#scope2').html(id);
        const scope1 = $('#scope1').html();
        let pCom = apiJson[scope1][id].pCom;
        let pPri = apiJson[scope1][id].pPri;
        let str = "";
        let i;
        str += '<div class="hIO am-g">';
        for (i in pPri) {
            str += '<div class="am-input-group am-u-sm-12 am-u-md-12 am-u-lg-6">';
            str += '<span class="am-input-group-label cPri">' + pPri[i] + '</span>';
            str += '<input type="text" class="am-form-field" name="' + pPri[i] + '" value="' + (cache[pPri[i]] !== undefined ? cache[pPri[i]] : '') + '" onchange="cache[\'' + pPri[i] + '\'] = this.value" />';
            str += '</div>';
        }
        for (i in pCom) {
            str += '<div class="am-input-group am-u-sm-12 am-u-md-12 am-u-lg-6">';
            str += '<span class="am-input-group-label cCom ' + pCom[i] + '">' + pCom[i] + '</span>';
            str += '<input type="text" class="am-form-field" name="' + pCom[i] + '" value="' + (cache[pCom[i]] !== undefined ? cache[pCom[i]] : '') + '" onchange="cache[\'' + pCom[i] + '\'] = this.value" />';
            str += '</div>';
        }
        str += '</div>';
        document.getElementById('paramsBox').innerHTML = str;
        $('.am-alert #result').text('点击提交查看结果');
    };
</script>
</body>
</html>