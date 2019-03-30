<?php
namespace plugins\Hphp;

class Crypto{

    /**
     * 加密参数数组,如
     * array(
        'type' => 'des-cbc',
        'secret' => 'xxxxxxxx',
        'iv' => 'xxxxxxxx',
      )
     * @var
     */
    private $crypto;

    public function __construct($crypto){
        $this->crypto = $crypto;
    }

    public function showAll(){
        print_r(openssl_get_cipher_methods());
    }

    //加密
    public function encrypt($str){
        $type = $this->crypto['type'] ?? null;
        $secret = $this->crypto['secret'] ?? null;
        $iv = $this->crypto['iv'] ?? null;
        $options = $this->crypto['options'] ?? 0;
        if(!$type || !$secret || !$iv){
            return 'crypto encrypt error';
        }
        return openssl_encrypt($str,$type,$secret,$options,$iv);
    }

    //解密
    public function decrypt($str){
        $type = $this->crypto['type'] ?? null;
        $secret = $this->crypto['secret'] ?? null;
        $iv = $this->crypto['iv'] ?? null;
        $options = $this->crypto['options'] ?? 0;
        if(!$type || !$secret || !$iv){
            return 'crypto decrypt error';
        }
        return openssl_decrypt($str,$type,$secret,$options,$iv);
    }

}