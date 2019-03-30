<?php
/**
 * 多维数组根据key的值排序
 * @param array $array 二维或者多维数组
 * @param string $key 排序键值
 * @param int $sort 排序方式  1：降序,0:升序
 * @return array 返回数组
 */
function array_multiSortByKey($array, $key, $sort = 1)
{
    if (is_array($array)) {
        $_sort_array = array();
        foreach ($array AS $_key => $_value) {
            $_sort_array[$_key] = (array)$_value[$key];
        }
        array_multisort($_sort_array, $sort ? SORT_DESC : SORT_ASC, $array);
        return $array;
    } else {
        return $array;
    }
}

/**
 * 一维数组根据val的值排序,同值保留键值顺序
 * @param array $array 一维数组
 * @param int $sort 排序方式  1：降序,0:升序
 * @return array 返回数组
 */
function array_sortByVal($array, $sort = 1)
{
    if (is_array($array)) {
        $_temp_array = array();
        foreach ($array as $_key => $_value) {
            $_temp_array[] = array('key' => $_key, 'val' => $_value);
        }
        $_sort_array = array();
        foreach ($_temp_array as $_key => $_value) {
            if (!$_sort_array) {
                $_sort_array[] = array('key' => $_value['key'], 'val' => $_value['val']);
            } else {
                foreach ($_sort_array as $_sort_key => $_sort_value) {
                    if ($sort === 1 && $_sort_value['val'] < $_value['val']) {
                        array_splice($_sort_array, $_sort_key, 0, array(array('key' => $_value['key'], 'val' => $_value['val'])));
                        break;
                    }
                    if ($sort === 0 && $_sort_value['val'] > $_value['val']) {
                        array_splice($_sort_array, $_sort_key, 0, array(array('key' => $_value['key'], 'val' => $_value['val'])));
                        break;
                    }
                }
            }
        }
        $newArray = array();
        foreach ($_sort_array as $v) {
            $newArray[$v['key']] = $v['val'];
        }
        return $newArray;
    } else {
        return $array;
    }
}

/**
 * 递归call
 * @param $filter
 * @param $data
 * @return array
 */
function array_recursiveFilter($filter, $data)
{
    $result = array();
    foreach ($data as $key => $val) {
        $result[$key] = is_array($val)
            ? array_recursiveFilter($filter, $val)
            : call_user_func($filter, $val);
    }
    return $result;
}

/**
 * 判断是否关联数组
 * @param $arr
 * @return bool
 */
function array_is_assoc($arr)
{
    $index = 0;
    foreach (array_keys($arr) as $key) {
        if ($index++ != $key) return false;
    }
    return true;
}

/**
 * 单一维数组排列组合
 * @param $array
 * @param $n
 * @return array
 */
function array_comb($array, $n)
{
    $results = [];
    if ($n == 1) {
        foreach ($array as $key => $value) {
            $results[] = array($key => $value);
        }
        return $results;
    }
    ksort($array);
    $subArray = array_slice($array, 1, null, true);
    $subArray_results = array_comb($subArray, $n - 1);
    foreach ($array as $key => $value) {
        foreach ($subArray_results as $subArray_result) {
            $srs = array_keys($subArray_result);
            if ($key < array_shift($srs)) {
                $results[] = array($key => $value) + $subArray_result;
            }
        }
    }
    return $results;
}

/**
 * 多组数组排列组合
 * @param $arr
 * @return mixed
 */
function array_combs($arr)
{
    function array_combs_recursive($arr)
    {
        if (count($arr) >= 2) {
            $tmpArr = array();
            $arr1 = array_shift($arr);
            $arr2 = array_shift($arr);
            foreach ($arr1 as $k1 => $v1) {
                foreach ($arr2 as $k2 => $v2) {
                    $tmpArr[] = $v1 . '<|>' . $v2;
                }
            }
            array_unshift($arr, $tmpArr);
            $arr = array_combs_recursive($arr);
        } else {
            return $arr;
        }
        return $arr;
    }
    $result = array_combs_recursive($arr);
    $result = reset($result);
    foreach ($result as $k => $v) {
        $result[$k] = explode('<|>', $v);
    }
    return $result;
}
