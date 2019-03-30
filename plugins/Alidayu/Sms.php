<?php

namespace plugins\Alidayu;

class Sms
{

    private $error = '验证码发送太快，平台繁忙，请稍后再试';

    private function error($msg)
    {
        $msg && $this->error = $msg;
        return false;
    }

    public function getError()
    {
        return $this->error;
    }

    public function sendSms($ak, $sk, $mobile, $freeSignName, $templateCode, $params)
    {
        include "TopSdk.php";
        date_default_timezone_set('Asia/Shanghai');

        $c = new \TopClient;
        $c->appkey = $ak;
        $c->secretKey = $sk;
        $req = new \AlibabaAliqinFcSmsNumSendRequest;
        $req->setExtend(time());
        $req->setSmsType("normal");
        $req->setSmsFreeSignName($freeSignName);
        $req->setSmsParam($params);    //SmsParam example:"{'code':'98547','product':'alidayu'}"
        $req->setRecNum($mobile);
        $req->setSmsTemplateCode($templateCode);

        $resp = $c->execute($req);
        $result = (array)$resp;
        $error = null;
        if (!empty($result['result'])) {
            $result = (array)$result['result'];
        } else {
            !$error && !empty($result['sub_msg']) && $error = $result['sub_msg'];
            !$error && !empty($result['msg']) && $error = $result['msg'];
        }
        if (isset($result['success'])) {
            return true;
        } else {
            return $this->error($error);
        }
    }

}