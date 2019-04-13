<?php

namespace library;

class Model extends Table
{

    private $_now;
    private $_micro_now;
    private $_io;
    private $_header;
    private $_server;
    private $_files;
    private $_scope;
    private $_platform;
    private $_source;
    private $_client_ip;
    private $_client_id;
    private $_router;
    private $_action;
    private $_request;
    private $_x_host;
    private $_download_host;
    private $_host;
    private $_domain;
    private $_is_http;
    private $_is_local;
    private $_bean = null;
    private $_true = '';
    private $_false = '';

    /**
     * 架构函数 取得模板对象实例
     * @access public
     * @param $io
     */
    public function __construct($io = null)
    {
        parent::__construct();
        if ($io) {
            $this->_now = time();
            $this->_micro_now = microtime();
            $this->_io = $io;
            $this->_header = $io['header'];
            $this->_server = $io['server'];
            $this->_files = $io['files'];
            $this->_scope = $io['scope'];
            $this->_platform = $io['platform'];
            $this->_source = $io['source'];
            $this->_router = get_called_class();
            $this->_action = $io['action'];
            $this->_client_ip = $io['client_ip'];
            $this->_client_id = $io['client_id'];
            $this->_request = $io['request'];
            $this->_x_host = $io['x_host'];
            $this->_host = $io['host'];
            $this->_domain = $io['domain'];
            $this->_is_http = $io['is_http'];
            $this->_is_local = $io['is_local'];
        }
        if (method_exists($this, 'init__')) {
            $this->init__();
        }
    }

    /**
     * 通用获取Bean方法
     * 注意*只有Bean类与Model命名空间及名字具有对应关系时才可使用本方法
     * 如    \System\Model\MapModel
     * 对应  \System\Bean\MapBean
     * @return mixed | \Common\Bean\AbstractBean
     */
    protected function getBean()
    {
        $beanRes = $this->getRouter('bean');
        if (!$this->_bean[$beanRes]) {
            $bean = str_replace('/', '\\', $beanRes);
            $urls = array();
            foreach (explode('\\', $bean) as $v) {
                $urls[] = ucfirst($v);
            }
            $bean = implode('\\', $urls);
            $this->_bean[$beanRes] = new $bean();
        }
        $params = $this->getRequest();
        //获取到的变量
        foreach ($params as $pk => $pv) {
            $underLine = explode('_', $pk);
            if (count($underLine) > 1) {
                $newKey = '';
                foreach ($underLine as $ul) {
                    $newKey .= ucfirst($ul);
                }
                $params[$newKey] = $pv;
                unset($params[$pk]);
            } else {
                $params[ucfirst($pk)] = $pv;
                unset($params[$pk]);
            }
        }
        foreach ($params as $pk => $pv) {
            $func = "set{$pk}";
            if (method_exists($this->_bean[$beanRes], $func)) {
                $this->_bean[$beanRes]->$func($pv);
            }
        }
        return $this->_bean[$beanRes];
    }


    /**
     * 国际化
     * @param $response
     * @return string
     */
    protected function responseTranslate($response)
    {
        // 数据处理
        if (strpos($response, 'foreign key constraint') !== false) {
            $response = 'anywhere depend on this record, operation abort';
        }
        if (strpos($response, 'duplicate key') !== false) {
            $response = 'duplicate key';
        }
        $db = $this->db();
        if (method_exists($db, 'responseTranslate')) {
            $response = $this->db()->responseTranslate($this->getBean()->getLanguage(), $response);
        }
        return $response;
    }

    /**
     * _action成功处理 success 方法
     * @param null $data
     * @param string $response
     * @return array
     */
    protected function success($data = null, $response = 'success')
    {
        return IOCode::SUCCESS($data, $this->responseTranslate($response));
    }

    /**
     * _action错误处理 error 方法
     * @param string $response
     * @param null $data
     * @return array
     */
    protected function error($response = 'error', $data = null)
    {
        return IOCode::ERROR($this->responseTranslate($response), $data);
    }

    /**
     * _action广播 broadcast 方法
     * @param $data
     * @param $client
     * @param $response
     * @return array
     */
    protected function broadcast($data, $client, $response = 'broadcast')
    {
        return IOCode::BROADCAST($data, $client, $this->responseTranslate($response));
    }

    /**
     * _action步进 goon 方法
     * @param $data
     * @param $response
     * @return array
     */
    protected function goon($data, $response = 'goon')
    {
        return IOCode::GOON($data, $this->responseTranslate($response));
    }

    /**
     * _action抛出处理 exception 方法
     * @param $response
     * @return array
     */
    protected function exception($response = 'exception')
    {
        return IOCode::EXCEPTION($this->responseTranslate($response));
    }

    /**
     * _action无权限 notPermission 方法
     * @param $response
     * @return array
     */
    protected function notPermission($response = 'not permission')
    {
        return IOCode::NOT_PERMISSION($this->responseTranslate($response));
    }

    /**
     * _action无找到 notFount 方法
     * @param $response
     * @return array
     */
    protected function notFount($response = 'not found')
    {
        return IOCode::NOT_FOUND($this->responseTranslate($response));
    }


    /**
     * 获取io
     * @return null|string
     */
    protected function getIO()
    {
        return $this->_io;
    }

    /**
     * 获取header
     * @return null|string
     */
    protected function getHeader()
    {
        return $this->_header;
    }

    /**
     * 获取server
     * @return null|string
     */
    protected function getServer()
    {
        return $this->_server;
    }

    /**
     * 获取domain
     * @return null|string
     */
    protected function getDomain()
    {
        return $this->_domain;
    }

    /**
     * 获取host
     * @return null|string
     */
    protected function getHost()
    {
        return $this->_host;
    }

    /**
     * 获取X-host
     * @return null|string
     */
    protected function getXHost()
    {
        return $this->_x_host;
    }

    /**
     * 获取download-host
     * @return null|string
     */
    protected function getDownloadHost()
    {
        if ($this->_download_host) {
            return $this->_download_host;
        }
        if (CONFIG['download_host']) {
            $dh = str_split(CONFIG['download_host'], 1);
            $dh = array_pop($dh);
            $this->_download_host = $dh === '/' ? CONFIG['download_host'] : CONFIG['download_host'] . '/';
            return $this->_download_host;
        }
        exit('config has not download_host');
    }

    /**
     * 获取files
     * @return null|array
     */
    protected function getFiles()
    {
        return $this->_files;
    }

    /**
     * 获取scope
     * @return null|string
     */
    protected function getScope()
    {
        return $this->_scope;
    }

    /**
     * 获取platform
     * @return null|string
     */
    protected function getPlatform()
    {
        return $this->_platform;
    }

    /**
     * 获取source
     * @return null|string
     */
    protected function getSource()
    {
        return $this->_source;
    }

    /**
     * 获取router
     * @param $type
     * @return mixed
     */
    protected function getRouter($type = '')
    {
        return str_replace('Model', ucfirst($type), $this->_router);
    }

    /**
     * 获取action
     * @return mixed|string
     */
    protected function getAction()
    {
        return $this->_action;
    }

    /**
     * 获取clientIP
     * @return mixed|string
     */
    protected function getClientIP()
    {
        return $this->_client_ip;
    }

    /**
     * 获取clientID
     * @return mixed|string
     */
    protected function getClientID()
    {
        return $this->_client_id;
    }

    /**
     * 获取request
     * @return mixed|string
     */
    protected function getRequest()
    {
        return $this->_request;
    }

    /**
     * 是否ssl
     * @return null|string
     */
    protected function isSSL()
    {
        return !$this->_is_http;
    }

    /**
     * 是否本地请求
     * @return null|string
     */
    protected function isLocal()
    {
        return $this->_is_local;
    }

    /**
     * 获取now
     * @return null|string
     */
    protected function getNow()
    {
        return $this->_now;
    }

    /**
     * 获取当前日期时间
     * @return false|string
     */
    protected function getNowDateTime()
    {
        return date('Y-m-d H:i:s', $this->getNow());
    }

    /**
     * 获取当前日期
     * @return false|string
     */
    protected function getNowDate()
    {
        return date('Y-m-d', $this->getNow());
    }

    /**
     * 获取当前时间
     * @return false|string
     */
    protected function getNowTime()
    {
        return date('H:i:s', $this->getNow());
    }

    /**
     * 获取 micro now
     * @return null|string
     */
    protected function getMicroNow()
    {
        return $this->_micro_now;
    }


    /**
     * true方法，可以用于代替return true并记录某些信息
     * @param $msg
     * @return bool
     */
    protected function true($msg = null)
    {
        $this->_true = $msg;
        return true;
    }

    /**
     * 获取true设定的信息
     * @return string
     */
    protected function getTrueMsg()
    {
        return $this->_true;
    }

    /**
     * false方法，可以用于代替return false并记录某些信息
     * @param $msg
     * @return bool
     */
    protected function false($msg)
    {
        $this->_false = $msg;
        return false;
    }

    /**
     * 获取false设定的信息
     * @return string
     */
    protected function getFalseMsg()
    {
        return $this->_false;
    }

    /**
     * 获取false设定的信息Protected
     * @return string
     */
    public function getFalseMsg__()
    {
        return $this->getFalseMsg();
    }

    /**
     * 格式化数据
     * @param $data
     * @param $handle
     * @return array
     */
    protected function factoryData($data, $handle)
    {
        if (!$data || !$handle) return $data;
        $tempData = isset($data['page']) ? $data['data'] : $data;
        if (!is_array(reset($tempData))) {
            $tempData && $tempData = array($tempData);
        }
        if ($tempData) {
            $tempData = @$handle($tempData);
        }
        if ($data && !is_array(reset($data))) $tempData = reset($tempData);
        if (isset($data['page'])) {
            $temp = array();
            $temp['data'] = $tempData;
            $temp['page'] = $data['page'];
            $tempData = $temp;
        }
        return $tempData;
    }

}