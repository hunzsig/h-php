<?php

/**
 * XML编码
 * @param mixed $data 数据
 * @param string $root 根节点名
 * @param string $item 数字索引的子节点名
 * @param string $attr 根节点属性
 * @param string $id 数字索引子节点key转换的属性名
 * @param string $encoding 数据编码
 * @return string
 */
function xml_encode($data, $root = 'hunzsig', $item = 'item', $attr = '', $id = 'id', $encoding = 'utf-8')
{
    if (is_array($attr)) {
        $_attr = array();
        foreach ($attr as $key => $value) {
            $_attr[] = "{$key}=\"{$value}\"";
        }
        $attr = implode(' ', $_attr);
    }
    $attr = trim($attr);
    $attr = empty($attr) ? '' : " {$attr}";
    $xml = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
    $xml .= "<{$root}{$attr}>";
    $xml .= data_to_xml($data, $item, $id);
    $xml .= "</{$root}>";
    return $xml;
}

/**
 * 数据XML编码
 * @param mixed $data 数据
 * @param string $item 数字索引时的节点名称
 * @param string $id 数字索引key转换为的属性名
 * @return string
 */
function data_to_xml($data, $item = 'item', $id = 'id')
{
    $xml = $attr = '';
    foreach ($data as $key => $val) {
        if (is_numeric($key)) {
            $id && $attr = " {$id}=\"{$key}\"";
            $key = $item;
        }
        $xml .= "<{$key}{$attr}>";
        $xml .= (is_array($val) || is_object($val)) ? data_to_xml($val, $item, $id) : $val;
        $xml .= "</{$key}>";
    }
    return $xml;
}

/**
 * 生成N位随机验证码(大小写+数字)
 * @param int $len
 * @return string
 */
function randChar($len = 6)
{
    $codeLib = [
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
    ];
    $codeMax = count($codeLib);
    $code = '';
    for ($i = 0; $i < $len; $i++) {
        $code .= $codeLib[rand(0, $codeMax - 1)];
    }
    return $code;
}

/**
 * 生成N位随机数字
 * @param int $len
 * @return string
 */
function randCharNum($len = 6)
{
    $codeLib = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $codeMax = count($codeLib);
    $code = '';
    for ($i = 0; $i < $len; $i++) {
        $code .= $codeLib[rand(0, $codeMax - 1)];
    }
    return $code;
}

/**
 * 生成N位随机数字
 * @param int $len
 * @param bool $isUpper
 * @return string
 */
function randCharLetter($len = 6, $isUpper = false)
{
    $codeLib = $isUpper
        ? ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z']
        : ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
    $codeMax = count($codeLib);
    $code = '';
    for ($i = 0; $i < $len; $i++) {
        $code .= $codeLib[rand(0, $codeMax - 1)];
    }
    return $code;
}

/**
 * 换行切<br>
 * @param $str
 * @return mixed
 */
function eol2br($str)
{
    return nl2br($str);
}

/**
 * <br>切换行
 * @param $str
 * @return mixed
 */
function br2nl($str)
{
    return str_replace(["<br>", "<br/>"], PHP_EOL, $str);
}

/**
 * 将驼峰转为下划线命名
 * @param $str
 * @return string
 */
function camel2underscore($str)
{
    return strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', $str));
}

/**
 * null to string
 * @param $obj
 * @return array|string
 */
function null2String($obj)
{
    if (is_array($obj)) {
        foreach ($obj as $k => $v) {
            if (is_array($v)) {
                $obj[$k] = null2String($v);
            } elseif (is_null($v)) {
                $obj[$k] = "";
            }
        }
    } elseif (is_null($obj)) {
        $obj = "";
    }
    return $obj;
}

/**
 * 强制类型转换 -> string
 * @desc 兼容一般数据并运用递归处理数组内数据
 * @param $obj
 * @return array|string
 */
function parseString($obj)
{
    if (is_array($obj)) {
        foreach ($obj as $k => $v) {
            if (is_array($v)) {
                $obj[$k] = parseString($v);
            } else {
                $obj[$k] = number_format($v, '', '', '');
            }
        }
    } else {
        $obj = number_format($obj, '', '', '');
    }
    return $obj;
}

/**
 * 强制类型转换 -> int
 * @desc 兼容一般数据并运用递归处理数组内数据
 * @param $obj
 * @param string $type
 * @return array|string
 */
function parseInt($obj, $type = 'round')
{
    if (is_array($obj)) {
        foreach ($obj as $k => $v) {
            if (is_array($v)) {
                $obj[$k] = parseInt($v);
            } elseif (is_numeric($v) || !$v) {
                if ($type == 'round') $obj[$k] = round($v);
                elseif ($type == 'ceil') $obj[$k] = ceil($v);
                elseif ($type == 'floor') $obj[$k] = floor($v);
                elseif ($type == 'int') $obj[$k] = (int)$v;
            }
        }
    } elseif (is_numeric($obj) || !$obj) {
        if ($type == 'round') $obj = round($obj);
        elseif ($type == 'ceil') $obj = ceil($obj);
        elseif ($type == 'floor') $obj = floor($obj);
        elseif ($type == 'int') $obj = (int)$obj;
    }
    return $obj;
}

/**
 * 强制类型转换 -> real
 * @desc 兼容一般数据并运用递归处理数组内数据
 * @param $obj
 * @return array|string
 */
function parseReal($obj)
{
    if (is_array($obj)) {
        foreach ($obj as $k => $v) {
            if (is_array($v)) {
                $obj[$k] = parseReal($v);
            } elseif (is_numeric($v) || !$v) {
                $obj[$k] = round($v, 0, 10);
            }
        }
    } elseif (is_numeric($obj) || !$obj) {
        $obj = round($obj, 0, 10);
    }
    return $obj;
}

/**
 * 科学计数法转回字符串
 * @param $obj
 * @return float|string
 */
function parseTecNum($obj)
{
    if (is_array($obj)) {
        foreach ($obj as $k => $v) {
            if (is_array($v)) {
                $obj[$k] = parseTecNum($v);
            } else {
                if (stripos($v, 'e+') === false) {
                    $obj[$k] = $v;
                } else {
                    $obj[$k] = (int)$v;
                }
            }
        }
    } else {
        if (stripos($obj, 'e+') !== false) {
            $obj = (int)$obj;
        }
    }
    return $obj;
}

/**
 * 字符串命名风格转换
 * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
 * @param string $name 字符串
 * @param integer $type 转换类型
 * @return string
 */
function parse_name($name, $type = 0)
{
    if ($type) {
        return ucfirst(preg_replace_callback('/_([a-zA-Z])/', function ($match) {
            return strtoupper($match[1]);
        }, $name));
    } else {
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }
}

/**
 * 字符串转二进制01
 * @param $str
 * @return string
 */
function str2bin($str)
{
    if (!is_string($str)) return null;
    $value = unpack('H*', $str);
    $value = str_split($value[1], 1);
    $bin = '';
    foreach ($value as $v) {
        $b = str_pad(base_convert($v, 16, 2), 4, '0', STR_PAD_LEFT);
        $bin .= $b;
    }
    return $bin;
}

/**
 * 二进制01字符串转
 * @param $bin
 * @return string
 */
function bin2str($bin)
{
    if (!is_string($bin)) return null;
    $bin = str_split($bin, 4);
    $str = '';
    foreach ($bin as $v) {
        $str .= base_convert($v, 2, 16);
    }
    $str = pack('H*', $str);
    return $str;
}

/**
 * @param $bn
 * @param $sn
 * @return int
 */
function kmod($bn, $sn)
{
    return intval(fmod(floatval($bn), $sn));
}

/**
 * 任意进制转 极限 进制
 * @param $data
 * @param $base_from
 * @return string
 */
function limit_convert($data, $base_from)
{
    $chars_map = [
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
        '_', '~', '!', '@', '$', '[', ']', '-', '·',
    ];
    $dividend = count($chars_map);
    if ($base_from >= $dividend) {
        return null;
    }
    if ($base_from !== 10) {
        $data = base_convert($data, $base_from, 10);
    }
    $base64_chars = [];
    while ($data > $dividend) {
        $r = kmod($data, $dividend);
        $data = ($data - $r) / $dividend;
        $base64_chars[] = $chars_map[$r];
    }
    $r = kmod($data, $dividend);
    $base64_chars[] = $chars_map[$r];
    return join('', array_reverse($base64_chars));
}

/**
 * 解析文件数据，获取标准格式的file
 * @param $fileData
 * @return array
 */
function parseFileData($fileData)
{
    $newFileData = array();
    if (!$fileData) return $newFileData;
    foreach ($fileData as $fd) {
        if (!$fd || !isset($fd['name'])) continue;
        $isMulti = is_array($fd['name']);
        if (false === $isMulti) {
            $newFileData[] = array(
                'name' => $fd['name'],
                'type' => $fd['type'],
                'tmp_name' => $fd['tmp_name'],
                'error' => $fd['error'],
                'size' => $fd['size'],
            );
        } else {
            $qty = count($fd['name']);
            for ($i = 0; $i < $qty; $i += 1) {
                $newFileData[] = array(
                    'name' => $fd['name'][$i],
                    'type' => $fd['type'][$i],
                    'tmp_name' => $fd['tmp_name'][$i],
                    'error' => $fd['error'][$i],
                    'size' => $fd['size'][$i],
                );
            }
        }
    }
    return $newFileData;
}

/**
 * 只替换第一个
 * @param $needle
 * @param $replace
 * @param $haystack
 * @return mixed
 */
function str_replace_once($needle, $replace, $haystack)
{
    $pos = strpos($haystack, $needle);
    return $pos === false ? $haystack : substr_replace($haystack, $replace, $pos, strlen($needle));
}

/**
 * 只替换第一个 适配编码
 * @param $needle
 * @param $replace
 * @param $haystack
 * @param string $encoding
 * @return mixed
 */
function mb_str_replace_once($needle, $replace, $haystack, $encoding = 'utf8')
{
    $pos = mb_strpos($haystack, $needle, 0, $encoding);
    return $pos === false ? $haystack : substr_replace($haystack, $replace, $pos, mb_strlen($needle, $encoding));
}

/**
 * 字符串反转
 * @param $str
 * @return string
 */
function str_reserve($str)
{
    if (!$str) return (string)$str;
    return implode(array_reverse(str_split($str)));
}