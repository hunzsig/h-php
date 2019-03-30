<?php
require 'hPhp.php';
require 'hColor.php';

G('1');
$now = md5(time());
$Color = (new hColor());
$root = realpath(__DIR__ . '/..');
$isTerminal = (empty($_COOKIE)) ? true : false;
$package_key = CONFIG['package_key'];
$package_iv = CONFIG['package_iv'];
$cext = '.dll';

/* TODO */
if (!$isTerminal) echo '<pre>';
show('Package start', 'yellow');
show('Root:' . $root);
show('Package Key:' . $package_key);
show('Package Iv:' . $package_iv);
show('Build dir dist');
if (!is_dir($root . '/dist')) {
    mkdir($root . '/dist');
} else {
    dirDel(realpath($root . '/dist'));
    mkdir($root . '/dist');
}
$corePath = $root . DIRECTORY_SEPARATOR . 'dist/';

show('Dist built');
G('2');
show("Cost: " . G('1', '2', 10) . "s", 'blue');

show('package dir:' . PATH_APP);
$count = scanner(PATH_APP, true);
show("package success count: {$count} files", 'green');
G('3');
show("Cost: " . G('2', '3', 10) . "s", 'blue');

show('package assets:');
$count = scanner(PATH_H_PHP . DIRECTORY_SEPARATOR . 'assets', false);
show("package success count: {$count} files", 'green');
show('build scope json:');
$scope = getAppScope(PATH_APP);
try {
    if (!is_dir($corePath . '/assets')) {
        mkdir($corePath . '/assets');
    }
    if (!is_dir($corePath . '/assets/json')) {
        mkdir($corePath . '/assets/json');
    }
    file_put_contents($corePath . '/assets/json/scope.json', json_encode($scope));
    show("build success!", 'green');
} catch (\Exception $e) {
    show("build error!" . $e->getMessage(), 'red');
    exit();
}

show('package external:');
$count = scanner(PATH_H_PHP . DIRECTORY_SEPARATOR . 'external', false);
show("package success count: {$count} files", 'green');

show('package plugins:');
$count = scanner(PATH_H_PHP . DIRECTORY_SEPARATOR . 'plugins', false);
show("package success count: {$count} files", 'green');

show('package resource:');
$count = scanner(PATH_H_PHP . DIRECTORY_SEPARATOR . 'resource', false);
show("package success count: {$count} files", 'green');

show('package library:');
$count = scanner(PATH_H_PHP . DIRECTORY_SEPARATOR . 'library', true);
show("package success count: {$count} files", 'green');

show('package core');
$staticCodes = combinePhp(PATH_STATIC);
$staticCodes = '<?php' . str_replace(['<?php', '?>'], '', $staticCodes);

$fileData = "<?php function h{$now}(\$res){hap(\$res);}spl_autoload_register('h{$now}');";
$fileData = openssl_encrypt(base64_encode($fileData), 'aes-256-cfb', $package_key, 0, $package_iv);
file_put_contents($corePath . 'hCatch' . $cext, $fileData);
file_put_contents($corePath . 'hStatic.php', $staticCodes);
$fileData = php_strip_whitespace(__DIR__ . DIRECTORY_SEPARATOR . 'hPhp.production.php');
$fileData = str_replace("define('HL', 0);", "define('____', 'HStream');define('_____', 'httpx');define('______', '{$package_key}');define('_______', '{$package_iv}');", $fileData);
$fileData = str_replace("define('CL', 0);", "include('httpx://' . file_get_contents(PATH_H_PHP . '/hCatch{$cext}'));define('CONFIG', require('httpx://' . file_get_contents(__DIR__ . '/hPhp.config{$cext}')));", $fileData);
file_put_contents($corePath . 'hPhp.php', $fileData);
$fileData = CONFIG;
if (isset($fileData['dev'])) {
    unset($fileData['dev']);
}
$fileData = openssl_encrypt(base64_encode('<?php return ' . var_export($fileData, true) . ';'), 'aes-256-cfb', $package_key, 0, $package_iv);
file_put_contents($corePath . 'hPhp.config' . $cext, $fileData);
file_put_contents($corePath . 'hSwoole.http.php', php_strip_whitespace(__DIR__ . DIRECTORY_SEPARATOR . 'hSwoole.http.php'));
file_put_contents($corePath . 'hSwoole.websocket.php', php_strip_whitespace(__DIR__ . DIRECTORY_SEPARATOR . 'hSwoole.websocket.php'));
file_put_contents($corePath . 'hHttp.php', php_strip_whitespace(__DIR__ . DIRECTORY_SEPARATOR . 'hHttp.php'));
file_put_contents($corePath . 'hExternal.php', php_strip_whitespace(__DIR__ . DIRECTORY_SEPARATOR . 'hExternal.php'));
if (is_file($root . DIRECTORY_SEPARATOR . 'index.php')) {
    $indexData = php_strip_whitespace($root . DIRECTORY_SEPARATOR . 'index.php');
    $diff = str_replace('\\', '/', str_replace(CONFIG['path_root'], '', __DIR__));
    $indexData = str_replace($diff, '', $indexData);
    file_put_contents($corePath . 'index.php', $indexData);
}
show("package core over", 'green');

G('4');
show("Cost: " . G('3', '4', 10) . "s", 'blue');
show("Finish ! Total Cost: " . G('1', '4', 10), 'green');
winSound('打包完成');

if (!$isTerminal) echo '</pre>';

/**
 * @param $dir
 * @param bool $isCrypto 是否加密
 * @param int $count
 * @return int
 */
function scanner($dir, $isCrypto = false, $count = 0)
{
    global $root, $corePath, $isTerminal, $package_key, $package_iv, $cext;
    $target = $root . DIRECTORY_SEPARATOR . 'dist';
    if (!is_dir($dir)) {
        return $count;
    }
    $files = opendir($dir);
    while ($file = readdir($files)) {
        if ($file != '.' && $file != '..') {

            // TODO BAN
            if (strpos($dir, 'svn') !== false) continue;
            if (strpos($dir, 'phpunit') !== false) continue;
            if (strpos($dir, 'test') !== false || strpos($file, 'test') !== false) continue;
            if (strpos($file, 'example') !== false) continue;
            if (strpos($file, '.md') !== false) continue;
            if (strpos($file, 'LICENSE') !== false) continue;
            if (strpos($file, 'README') !== false) continue;
            if (strpos($file, '.log') !== false) continue;
            $realDir = realpath($dir);
            $realFile = $realDir . DIRECTORY_SEPARATOR . $file;
            $fileData = false;
            $action = 'Copy';
            if (is_dir($realFile)) {
                $count = scanner($realFile, $isCrypto, $count);
            } elseif (strpos($file, PHP_EXT) !== false && $isCrypto === true) {
                $fileData = php_strip_whitespace($realFile);
                $fileData = openssl_encrypt(base64_encode($fileData), 'aes-256-cfb', $package_key, 0, $package_iv);
                $action = 'Crypto';
            } elseif (strpos($file, PHP_EXT) !== false) {
                $fileData = php_strip_whitespace($realFile);
                $action = 'Composer';
            } else {
                $fileData = file_get_contents($realFile);
            }
            if ($fileData === false) continue;
            $newDir = null;
            // TODO APP
            if (strpos($realDir, 'h-php') !== false) {
                $newDir = str_replace($root . DIRECTORY_SEPARATOR . 'h-php', $corePath, $realDir);
            } else {
                $newDir = str_replace($root . DIRECTORY_SEPARATOR, $corePath, $realDir);
            }
            $newFile = $newDir . DIRECTORY_SEPARATOR . $file;
            if ($action === 'Crypto') {
                $newFile = str_replace(PHP_EXT, $cext, $newFile);
            }
            if (dirCheck($newDir, true) === false) {
                show("- Fail - DirCheck: {$newDir}", 'red');
                exit();
            }
            if (file_put_contents($newFile, $fileData) === false) {
                show("- Fail - {$action} {$realFile} -> {$newFile}", 'red');
                exit();
            } elseif ($isTerminal) {
                show("- OK - {$action} {$file}", 'grey');
            }
            $count++;
        }
    }
    closedir($files);
    return $count;
}

/**
 * @param $dir
 * @param string $data
 * @return int
 */
function combinePhp($dir, $data = '')
{
    if (!is_dir($dir)) {
        return $data;
    }
    $files = opendir($dir);
    while ($file = readdir($files)) {
        if ($file != '.' && $file != '..') {
            // TODO BAN
            if (strpos($dir, 'svn') !== false) continue;
            if (strpos($dir, 'phpunit') !== false) continue;
            if (strpos($dir, 'test') !== false || strpos($file, 'test') !== false) continue;
            if (strpos($file, 'example') !== false) continue;
            if (strpos($file, '.md') !== false) continue;
            $realDir = realpath($dir);
            $realFile = $realDir . DIRECTORY_SEPARATOR . $file;
            $fileData = false;
            if (is_dir($realFile)) {
                $data = combinePhp($realFile, $data);
            } elseif (strpos($file, PHP_EXT) !== false) {
                $fileData = php_strip_whitespace($realFile);
            }
            if ($fileData === false) continue;
            $data .= $fileData;
            show("- OK - Combine {$file}", 'grey');
        }
    }
    closedir($files);
    return $data;
}

function show($str, $color = null)
{
    global $isTerminal, $Color;
    $str = PHP_EOL . ' -> ' . $str;
    switch ($color) {
        case 'red':
            echo (!$isTerminal) ? "<b style='color: red'>{$str}</b>" : $Color::lightRed($str);
            break;
        case 'yellow':
            echo (!$isTerminal) ? "<b style='color: #ffaf37'>{$str}</b>" : $Color::lightYellow($str);
            break;
        case 'blue':
            echo (!$isTerminal) ? "<b style='color: dodgerblue'>{$str}</b>" : $Color::lightBlue($str);
            break;
        case 'green':
            echo (!$isTerminal) ? "<b style='color: green'>{$str}</b>" : $Color::lightGreen($str);
            break;
        case 'grey':
            echo (!$isTerminal) ? "<b style='color: lightgrey'>{$str}</b>" : $Color::darkGray($str);
            break;
        default:
            echo $str;
            break;
    }
}
