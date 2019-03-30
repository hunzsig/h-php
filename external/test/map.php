<?php
if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1') {
    exit();
}

function getMap($dir, $map = array())
{
    $files = opendir($dir);
    while ($file = readdir($files)) {
        if ($file != '.' && $file != '..') {
            $realFile = $dir . '/' . $file;
            if (strpos($realFile, 'Protected') !== false) {
                continue;
            }
            if (is_dir($realFile)) {
                $map = getMap($realFile, $map);
            } elseif (strpos($file, PHP_EXT) === false) {
                continue;
            } elseif (strpos($realFile, 'Map') !== false
                && strpos($realFile, 'Abstract') === false
                && strpos($realFile, 'Model') === false
                && strpos($realFile, 'Bean') === false) {
                $map[] = $realFile;
            }
        }
    }
    closedir($files);
    return $map;
}

$map = getMap(PATH_APP);
$activeDir = str_replace('\\', '/', PATH_APP);
$maps = array();
foreach ($map as $m) {
    $m = str_replace('\\', '/', $m);
    $mArr = explode('/', $m);
    $mapDir = $m;
    $mapDir = str_replace($activeDir, '', $mapDir);
    $mapDir = str_replace(PHP_EXT, '', $mapDir);
    $mapDir = str_replace('/', '\\', $mapDir);
    $tempMap = (new $mapDir())->getKV();
    $tempMap && $maps[] = array(
        'uri' => $mapDir,
        'map' => $tempMap,
    );
}
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
        <?php echo($amazeuiCss);?>
    </style>
</head>
<body>

<div class="am-g">
    <?php foreach ($maps as $m): ?>
        <div class="am-u-sm-12 am-u-md-6 am-u-lg-3">
            <div class="am-panel am-panel-default">
                <div class="am-panel-hd"><?php echo $m['uri']; ?></div>
                <div class="am-panel-bd">
                    <table style="word-break: break-all;width: 100%;">
                        <?php foreach ($m['map'] as $mmk => $mmv): ?>
                            <tr>
                                <td><?php echo $mmv; ?></td>
                                <td><?php echo $mmk; ?></td>
                            </tr>
                        <?php endforeach;; ?>
                    </table>
                </div>
            </div>
        </div>
    <?php endforeach;; ?>
</div>
</body>
</html>