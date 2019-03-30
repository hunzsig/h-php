<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/10/17
 * Time: 下午 14:43
 */

namespace plugins\Alipay\WapV2;

use \plugins\Alipay\WapV2\Funcs\Core;
use \plugins\Alipay\WapV2\Funcs\Rsa;
use \plugins\Alipay\WapV2\Funcs\Rsa2;
use \DOMDocument;

abstract class AbstractLib{

    private $Core = null;
    private $Rsa = null;
    private $Rsa2 = null;
    private $DOMDocument = null;

    protected function getCore(){
        if(!$this->Core) $this->Core = new Core();
        return $this->Core;
    }

    protected function getRsa(){
        if(!$this->Rsa) $this->Rsa = new Rsa();
        return $this->Rsa;
    }

    protected function getRsa2(){
        if(!$this->Rsa2) $this->Rsa2 = new Rsa2();
        return $this->Rsa2;
    }

    protected function getDOMDocument(){
        if(!$this->DOMDocument) $this->DOMDocument = new DOMDocument();
        return $this->DOMDocument;
    }

}