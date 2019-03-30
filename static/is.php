<?php
//---------------------------数据验证--------------------------

/**
 * 验证手机号码
 * @param $mobile
 * @return bool
 */
function isMobile($mobile)
{
    $subMob = substr($mobile, 0, 2);
    $telList = array(
        '00','11','12','13','14','15','16','17','18','19',
    );
    $bool1 = preg_match('/^(\d{11}|\d{8})$/', $mobile) ? true : false;
    $bool2 = (in_array($subMob, $telList)) ? true : false;
    return ($bool1 && $bool2);
}

/**
 * 验证email格式
 * @param $email
 * @return bool
 */
function isEmail($email)
{
    return preg_match('/^[\w\.\-]+@[\w\-]+(\.[a-z]+){1,2}$/i', $email) ? true : false;
}

/**
 * 计算身份证校验码，根据国家标准GB 11643-1999
 * @param $idCardBase
 * @return bool|mixed
 */
function getVerifyBit($idCardBase)
{
    if (strlen($idCardBase) != 17) {
        return false;
    }
    //加权因子
    $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
    //校验码对应值
    $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
    $checksum = 0;
    for ($i = 0; $i < strlen($idCardBase); $i++) {
        $checksum = $checksum + round(substr($idCardBase, $i, 1), 3) * $factor[$i];
    }
    $mod = $checksum % 11;
    $verify_number = $verify_number_list[$mod];
    return $verify_number;
}

/**
 * 验证身份证NO
 * @param $idCardNo
 * @param bool $isStrict
 * @return bool
 */
function isIdentityCardNo($idCardNo, $isStrict = false)
{
    $idCardLength = strlen($idCardNo);
    //长度验证
    if (!preg_match('/^\d{17}(\d|x)$/i', $idCardNo) and !preg_match('/^\d{15}$/i', $idCardNo)) {
        return false;
    }
    if ($isStrict) {
        //地区验证
        $city = array(11 => "北京", 12 => "天津", 13 => "河北", 14 => "山西", 15 => "内蒙古", 21 => "辽宁", 22 => "吉林", 23 => "黑龙江", 31 => "上海", 32 => "江苏", 33 => "浙江", 34 => " 安徽", 35 => "福建", 36 => "江西", 37 => "山东", 41 => "河南", 42 => "湖北", 43 => " 湖南", 44 => "广东", 45 => "广西", 46 => "海南", 50 => "重庆", 51 => "四川", 52 => " 贵州", 53 => "云南", 54 => "西藏", 61 => "陕西", 62 => "甘肃", 63 => "青海", 64 => " 宁夏", 65 => "新疆", 71 => "台湾", 81 => "香港", 82 => "澳门", 91 => "国外");
        if (!array_key_exists(intval(substr($idCardNo, 0, 2)), $city)) {
            return false;
        }
        //15位身份证验证生日，转换为18位
        if ($idCardLength == 15) {
            $sBirthday = '19' . substr($idCardNo, 6, 2) . '-' . substr($idCardNo, 8, 2) . '-' . substr($idCardNo, 10, 2);
            $d = new DateTime($sBirthday);
            $dd = $d->format('Y-m-d');
            if ($sBirthday != $dd) {
                return false;
            }
            $idCardNo = substr($idCardNo, 0, 6) . "19" . substr($idCardNo, 6, 9);//15to18
            $Bit18 = getVerifyBit($idCardNo);//算出第18位校验码
            $idCardNo = $idCardNo . $Bit18;
        }
        //18位身份证处理
        $sBirthday = substr($idCardNo, 6, 4) . '-' . substr($idCardNo, 10, 2) . '-' . substr($idCardNo, 12, 2);
        $d = new DateTime($sBirthday);
        $dd = $d->format('Y-m-d');
        if ($sBirthday != $dd) return false;
        //身份证编码规范验证
        $idCardNo_base = substr($idCardNo, 0, 17);
        if (strtoupper(substr($idCardNo, 17, 1)) != getVerifyBit($idCardNo_base)) {
            return false;
        }
    }
    return true;
}

/**
 * 验证微信OPENID格式
 * @param $open_id
 * @return bool
 */
function isWechatOpenId($open_id)
{
    return (strlen($open_id) > 15) ? true : false;
}

/**
 * 验证IP格式
 * @param $str
 * @return bool|int
 */
function isIp($str)
{
    $ip = explode('.', $str);
    for ($i = 0; $i < count($ip); $i++) {
        if ($ip[$i] > 255) {
            return false;
        }
    }
    return preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $str);
}

/**
 * 是否windows
 * @return bool
 */
function isWindows()
{
    return strstr(PHP_OS, 'WIN') ? true : false;
}

/**
 * 是否CGI
 * @return bool
 */
function isCGI()
{
    return (0 === strpos(PHP_SAPI,'cgi') || false !== strpos(PHP_SAPI,'fcgi')) ? true : false;
}

/**
 * 是否CLI
 * @return bool
 */
function isCLI()
{
    return PHP_SAPI=='cli'? true : false;
}