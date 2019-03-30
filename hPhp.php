<?php
/**
 * @author hunzsig.org
 * @date 2018/06/27
 */
//TODO 检测PHP环境
if (version_compare(PHP_VERSION, '7.2', '<')) exit('PHP > 7.2 !');

// 全局配置的设定
!defined('CONFIG_PATH') && define('CONFIG_PATH', __DIR__ . '/hPhp.config.php');
$gc = require(CONFIG_PATH);
$isDev = isset($gc['dev']);
if ($isDev) {
    $gc_dev = $gc['dev'] ?? array();
    unset($gc['dev']);
    $gc = array_merge($gc, $gc_dev);
}
define('CONFIG', $gc);
define('IS_DEV', $isDev);
// 版本
define('H_SERVER_VERSION', '1.0.0');
define('IS_WINDOW', strstr(PHP_OS, 'WIN') && PHP_OS !== 'CYGWIN' ? true : false);
// 记录内存
define('MEMORY_LIMIT_ON', function_exists('memory_get_usage'));
// 定义路径
define('PATH_ROOT', CONFIG['path_root']); // 根
define('PATH_H_PHP', __DIR__); // 框架路径
define('PATH_STATIC', realpath(PATH_H_PHP . '/static')); // 静态库路径
define('PATH_ASSETS', realpath(PATH_H_PHP . '/assets')); // 资源路径
define('PATH_PLUGINS', realpath(PATH_H_PHP . '/plugins')); // 拓展路径
define('PATH_RESOURCE', realpath(PATH_H_PHP . '/resource')); // resource路径
// 自动创建必要目录
foreach (['uploads', 'bak'] as $v) {
    if (!is_dir(PATH_H_PHP . '/' . $v)) {
        @mkdir(PATH_H_PHP . '/' . $v);
        @chmod(PATH_H_PHP . '/' . $v, 0777);
    }
}
define('PATH_UPLOAD', realpath(PATH_H_PHP . '/uploads')); // upload路径
define('PATH_BAK', realpath(PATH_H_PHP . '/bak')); // bak路径
define('PATH_APP', realpath(PATH_ROOT . DIRECTORY_SEPARATOR . CONFIG['app_name'])); // app路径
if (!PATH_APP) {
    exit('config app path please');
}
if ($isDev) {
    @file_put_contents(PATH_BAK . DIRECTORY_SEPARATOR . 'hPhp.config.php.' . date('Ymd-H'), file_get_contents("hPhp.config.php"));
}
define('____', 'HStream');
define('_____', null);
define('______', null);
define('_______', null);
define("DEFAULT_TIMEZONE", $gc['timezone'] ?? 'PRC');
define('URL_SEPARATOR', '/');
define('PHP_EXT', '.php');
// 设置时区
date_default_timezone_set(DEFAULT_TIMEZONE);

/**
 * 加载目录
 * @param $dir
 */
function loadDir($dir)
{
    if (!is_dir($dir)) return;
    $files = opendir($dir);
    while ($file = readdir($files)) {
        if ($file != '.' && $file != '..') {
            $realFile = $dir . '/' . $file;
            if (is_dir($realFile)) {
                loadDir($realFile);
            } elseif (strpos($file, PHP_EXT) === false) {
                continue;
            } else {
                require_once($realFile);
            }
        }
    }
    closedir($files);
}

//TODO *加载静态库
loadDir(PATH_STATIC);
//TODO *支持载入
spl_autoload_register('hAutoload');