<?php
/**
 * TODO 自动加载触发式
 * @param $res
 */
function hAutoload($res)
{
    $res = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $res);
    foreach ([PATH_APP, PATH_H_PHP, PATH_PLUGINS] as $path) {
        $file = $path . DIRECTORY_SEPARATOR . $res . PHP_EXT;
        if (is_file($file)) {
            require($file);
            break;
        }
    }
}

/**
 * TODO 获取和设置配置参数 支持批量定义
 * @param string|array $name 配置变量
 * @param mixed $value 配置值
 * @param mixed $default 默认值
 * @return mixed
 */
function C($name = null, $value = null, $default = null)
{
    static $_config = array();
    // 无参数时获取所有
    if (empty($name)) {
        return $_config;
    }
    // 优先执行设置获取或赋值
    if (is_string($name)) {
        if (!strpos($name, '.')) {
            $name = strtoupper($name);
            if (is_null($value))
                return isset($_config[$name]) ? $_config[$name] : $default;
            $_config[$name] = $value;
            return null;
        }
        // 二维数组设置和获取支持
        $name = explode('.', $name);
        $name[0] = strtoupper($name[0]);
        if (is_null($value))
            return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : $default;
        $_config[$name[0]][$name[1]] = $value;
        return null;
    }
    // 批量设置
    if (is_array($name)) {
        $_config = array_merge($_config, array_change_key_case($name, CASE_UPPER));
        return null;
    }
    return null; // 避免非法参数
}

/**
 * TODO 记录和统计时间（微秒）和内存使用情况
 * 使用方法:
 * <code>
 * G('begin'); // 记录开始标记位
 * // ... 区间运行代码
 * G('end'); // 记录结束标签位
 * echo G('begin','end',6); // 统计区间运行时间 精确到小数后6位
 * echo G('begin','end','m'); // 统计区间内存使用情况
 * 如果end标记位没有定义，则会自动以当前作为标记位
 * 其中统计内存使用需要 MEMORY_LIMIT_ON 常量为true才有效
 * </code>
 * @param string $start 开始标签
 * @param string $end 结束标签
 * @param integer|string $dec 小数位或者m
 * @return mixed
 */
function G($start, $end = '', $dec = 4)
{
    static $_info = array();
    static $_mem = array();
    if (is_float($end)) { // 记录时间
        $_info[$start] = $end;
    } elseif (!empty($end)) { // 统计时间和内存使用
        if (!isset($_info[$end])) $_info[$end] = microtime(TRUE);
        if (MEMORY_LIMIT_ON && $dec == 'm') {
            if (!isset($_mem[$end])) $_mem[$end] = memory_get_usage();
            return number_format(($_mem[$end] - $_mem[$start]) / 1024);
        } else {
            return number_format(($_info[$end] - $_info[$start]), $dec);
        }

    } else { // 记录时间和内存使用
        $_info[$start] = microtime(TRUE);
        if (MEMORY_LIMIT_ON) $_mem[$start] = memory_get_usage();
    }
    return null;
}

/**
 * 调试用方法D
 * @param $str
 */
function D($str)
{
    echo("\n<DEBUG>");
    echo("\n >>>>>>>>>> ");
    echo("\n" . $str);
    echo("\n <<<<<<<<<<");
    echo("\n</DEBUG>\n");
}

/**
 * 优化的require_once
 * @param string $filename 文件地址
 * @return boolean
 */
function require_cache($filename)
{
    static $_importFiles = array();
    if (!isset($_importFiles[$filename])) {
        if (file_exists_case($filename)) {
            require $filename;
            $_importFiles[$filename] = true;
        } else {
            $_importFiles[$filename] = false;
        }
    }
    return $_importFiles[$filename];
}

/**
 * 区分大小写的文件存在判断
 * @param string $filename 文件地址
 * @return boolean
 */
function file_exists_case($filename)
{
    if (is_file($filename)) {
        if (IS_WINDOW) {
            if (basename(realpath($filename)) != basename($filename))
                return false;
        }
        return true;
    }
    return false;
}

/**
 * @param $userAgent
 * @return string
 */
function getSourceByUserAgent($userAgent)
{
    $userAgent = strtolower($userAgent);
    if ($userAgent) {
        if (strpos($userAgent, 'iphone') !== false
            || strpos($userAgent, 'ipad') !== false
            || strpos($userAgent, 'iwatch') !== false) {
            return 'apple';
        } elseif (strpos($userAgent, 'android') !== false) {
            return 'android';
        } elseif (strpos($userAgent, 'windows nt') !== false
            || strpos($userAgent, 'msie') !== false) {
            return 'windows';
        }
    }
    return 'other';
}

/**
 * 检查路径目录是否存在，存在则返回真实路径，不存在返回false
 * @param $path
 * @param bool $isBuild 是否自动创建不存在的目录
 * @return bool|string
 */
function dirCheck($path, $isBuild = false)
{
    $temp = str_replace('\\', '/', $path);
    if ($isBuild) {
        $p = explode('/', $temp);
        $tempLen = count($p);
        $temp = '';
        for ($i = 0; $i < $tempLen; $i++) {
            $temp .= $p[$i] . DIRECTORY_SEPARATOR;
            if (!is_dir($temp)) {
                mkdir($temp);
                @chmod($temp, 0777);
            }
        }
    }
    $temp = realpath($temp) . DIRECTORY_SEPARATOR;
    return $temp ? $temp : false;
}

/**
 * 递归删除目录
 * @param $dir
 */
function dirDel($dir)
{
    if (!is_dir($dir)) {
        return;
    }
    $files = opendir($dir);
    while (false !== ($file = readdir($files))) {
        if ($file != '.' && $file != '..') {
            $realDir = realpath($dir);
            $realFile = $realDir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($realFile)) {
                dirDel($realFile);
                @rmdir($realFile);
            } else {
                @unlink($realFile);
            }
        }
    }
    closedir($files);
    @rmdir($dir);
}

/**
 * 获取APP model
 * @param $dir
 * @param array $model
 * @return array
 */
function getAppModel($dir, $model = array()){
    $files = opendir($dir);
    while ($file = readdir($files)) {
        if ($file != '.' && $file != '..') {
            $realFile = $dir . '/' . $file;
            if (is_dir($realFile)) {
                $model = getAppModel($realFile, $model);
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

/**
 * 获取一个path的scope
 * @param $path
 * @return array
 */
function getAppScope($path){
    $model = getAppModel($path);
    $activeDir = str_replace('\\', '/', $path);
    $scope = array();
    foreach ($model as $m) {
        $m = str_replace('\\', '/', $m);
        $modelDir = $m;
        $modelDir = str_replace($activeDir, '', $modelDir);
        $modelDir = str_replace(PHP_EXT, '', $modelDir);
        $modelDir = str_replace('/', '\\', $modelDir);
        $sss = str_replace('Model', '.', $modelDir);
        $sss = str_replace('\\', '', $sss);
        $pClass = get_class_methods(get_parent_class($modelDir));
        $class = get_class_methods($modelDir);
        $class = array_diff($class,$pClass);
        foreach ($class as $c) {
            if (strpos($c, '__') !== false) {
                continue;
            }
            $scope[] = $sss . $c;
        }
    }
    return $scope;
}