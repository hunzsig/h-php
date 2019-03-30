<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/10/17
 * Time: 下午 14:43
 */

namespace plugins\Alipay\WapV1;

use \plugins\Alipay\WapV1\Funcs\Core;
use \plugins\Alipay\WapV1\Funcs\Rsa;
use \DOMDocument;

abstract class AbstractLib{

    private $Core = null;
    private $Rsa = null;
    private $DOMDocument = null;

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