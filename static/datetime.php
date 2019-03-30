<?php

/**
 * 获取一个时间戳在该周内的星期一
 * @param $timestamp
 * @return bool|string
 */
function getMonday($timestamp)
{
    $timestamp2 = strtotime(date("Y-m-d", $timestamp) . " last monday");
    if (($timestamp - $timestamp2) / 86400 == 7) {
        return date("Y-m-d", $timestamp);
    } else {
        return date("Y-m-d", $timestamp2);
    }
}

/**
 * 获取一个时间戳在该季度内的首月
 * @param $timestamp
 * @return bool|string
 */
function getQuarterFirstMonth($timestamp)
{
    $m = date('m', $timestamp);
    switch ($m) {
        case 1:
        case 2:
        case 3:
            $m = '01';
            break;
        case 4:
        case 5:
        case 6:
            $m = '04';
            break;
        case 7:
        case 8:
        case 9:
            $m = '07';
            break;
        case 10:
        case 11:
        case 12:
            $m = 10;
            break;
    }
    return $m;
}

/**
 * 获取一个时间戳在该季度内的最后一个月
 * @param $timestamp
 * @return bool|string
 */
function getQuarterLastMonth($timestamp)
{
    $m = date('m', $timestamp);
    switch ($m) {
        case 1:
        case 2:
        case 3:
            $m = '03';
            break;
        case 4:
        case 5:
        case 6:
            $m = '06';
            break;
        case 7:
        case 8:
        case 9:
            $m = '09';
            break;
        case 10:
        case 11:
        case 12:
            $m = 12;
            break;
    }
    return $m;
}

/**
 * @param $str
 * @param bool $isFloat
 * @param int $deep 深度 毫秒 000 微秒 000000
 * @return string | array
 */
function strtomicrotime($str, $isFloat = false, $deep = 6)
{
    $micros = explode('.', $str);
    $micro = str_repeat('0', $deep);
    if (is_array($micros) && count($micros) === 2) {
        $micro = $micros[1];
    }
    if ($isFloat) {
        return floatval(strtotime($str) . '.' . $micro);
    } else {
        $micros[1] = $micro;
        return $micros;
    }
}

/**
 * @param $format
 * @param $val
 * @return string
 */
function datemicro($format, $val)
{
    $micros = strtomicrotime($val);
    return date($format, strtotime($micros[0])) . '.' . $micros[1];
}
