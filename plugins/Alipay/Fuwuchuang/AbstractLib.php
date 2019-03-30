<?php
/**
 * Created by PhpStorm.
 * User: hunzsig
 * Date: 2015/10/30
 * Time: 11:43
 */

namespace Alipay\Lib\Fuwuchuang;

use \Alipay\Lib\Fuwuchuang\Funcs\FunctionInc;
use \Alipay\Lib\Fuwuchuang\Funcs\Md5;
use \Alipay\Lib\Fuwuchuang\Funcs\Core;
use \Alipay\Lib\Fuwuchuang\Funcs\Rsa;
use \DOMDocument;

abstract class AbstractLib{

    protected $config = array();

    private $FunctionInc = null;
    private $Md5 = null;
    private $Core = null;
    private $Rsa = null;
    private $DOMDocument = null;

    public function __construct(){
        $this->config = C(CONFIG_ALIPAY_FUWUCHUANG)[$GLOBALS['THIRD_CONFIG']];
    }

    protected function getConfig($key = null){
        if(!$this->config) E('must set config before get it');
        return ($key) ? $this->config[$key] : $this->config;
    }

    protected function getFunctionInc(){
        if(!$this->FunctionInc) $this->FunctionInc = new FunctionInc();
        return $this->FunctionInc;
    }

    protected function getMd5(){
        if(!$this->Md5) $this->Md5 = new Md5();
        return $this->Md5;
    }

    protected function getCore(){
        if(!$this->Core) $this->Core = new Core();
        return $this->Core;
    }

    protected function getRsa(){
        if(!$this->Rsa) $this->Rsa = new Rsa();
        return $this->Rsa;
    }

    protected function getDOMDocument(){
        if(!$this->DOMDocument) $this->DOMDocument = new DOMDocument();
        return $this->DOMDocument;
    }

}