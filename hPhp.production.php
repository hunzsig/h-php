<?php
/**
 * @author hunzsig.org
 * @date 2019/03/22
 */
// 版本
define('IS_DEV', false);
define('H_SERVER_VERSION', '1.0.0');
define('IS_WINDOW', strstr(PHP_OS, 'WIN') && PHP_OS !== 'CYGWIN' ? true : false);
// 记录内存
define('MEMORY_LIMIT_ON', function_exists('memory_get_usage'));
// 定义路径
define('PATH_ROOT', __DIR__); // 根
define('PATH_H_PHP', PATH_ROOT); // 框架路径
define('PATH_ASSETS', realpath(PATH_H_PHP . '/assets')); // 资源路径
define('PATH_PLUGINS', realpath(PATH_H_PHP . '/plugins')); // 拓展路径
define('PATH_RESOURCE', realpath(PATH_H_PHP . '/resource')); // resource路径
// 自动创建必要目录
foreach (['uploads'] as $v) {
    if (!is_dir(PATH_H_PHP . '/' . $v)) {
        @mkdir(PATH_H_PHP . '/' . $v);
        @chmod(PATH_H_PHP . '/' . $v, 0777);
    }
}
define('PATH_UPLOAD', realpath(PATH_H_PHP . '/uploads')); // upload路径
//
define('HL', 0);
include(PATH_H_PHP . '/hStatic.php');

define('URL_SEPARATOR', '/');
define('PHP_EXT', '.php');
// 设置时区
define('CL', 0);
define("DEFAULT_TIMEZONE", CONFIG['timezone'] ?? 'PRC');
date_default_timezone_set(DEFAULT_TIMEZONE);
define('PATH_APP', realpath(PATH_ROOT . DIRECTORY_SEPARATOR . CONFIG['app_name'])); // app路径
if (!PATH_APP) {
    exit('config app path please');
}