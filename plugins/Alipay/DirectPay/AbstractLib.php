<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/10/17
 * Time: 下午 14:43
 */

namespace plugins\Alipay\DirectPay;

use \plugins\Alipay\DirectPay\Funcs\Md5;
use \plugins\Alipay\DirectPay\Funcs\Core;
use \DOMDocument;

abstract class AbstractLib{

    private $Md5 = null;
    private $Core = null;
    private $DOMDocument = null;

    protected function getMd5(){
        if(!$this->Md5) $this->Md5 = new Md5();
        return $this->Md5;
    }

    protected function getCore(){
        if(!$this->Core) $this->Core = new Core();
        return $this->Core;
    }

    protected function getDOMDocument(){
        if(!$this->DOMDocument) $this->DOMDocument = new DOMDocument();
        return $this->DOMDocument;
    }

}