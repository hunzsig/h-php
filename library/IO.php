<?php

namespace library;

use plugins\Hphp\Crypto;

class IO
{
    private $is_crypto = false;
    private $crypto = null;
    private $stack = null;

    protected function crypto()
    {
        if (!$this->crypto && !empty(CONFIG['crypto'])) {
            $this->crypto = new Crypto(CONFIG['crypto']);
        }
        return $this->crypto;
    }

    /**
     * 返回结果
     * @param $array
     * @return string
     */
    private function result($array)
    {
        $array['stack'] = $this->stack;
        $result = json_encode($array);
        if ($this->is_crypto === true) {
            $result = $this->crypto()->encrypt($result);
        }
        return $result;
    }

    /**
     * io
     * @param $request
     * @return mixed
     */
    protected function io($request)
    {
        $header = $request['header'];
        $server = $request['server'];
        $files = parseFileData($request['files']);
        $this->is_crypto = strpos($request['post'], 'CRYPTO|') === 0;
        if ($this->is_crypto === true) {
            $post = $this->crypto()->decrypt($request['post'] = str_replace_once('CRYPTO|', '', $request['post']));
            $post = json_decode($post, true);
        } else {
            $post = json_decode($request['post'], true);
        }
        $this->stack = $post['stack'] ?? null;

        //todo 如果没有scope，恶意无返回
        $scope = isset($post['scope']) ? $post['scope'] : '';
        if (!$scope) {
            return $this->result(IOCode::NOT_FOUND('illegal scope'));
        }

        $client_id = isset($post['client_id']) ? $post['client_id'] : 0;
        $client_ip = null;
        !$client_ip && $client_ip = $header['x_real_ip'] ?? null;
        !$client_ip && $client_ip = $header['client_ip'] ?? null;
        !$client_ip && $client_ip = $header['x_forwarded_for'] ?? null;
        !$client_ip && $client_ip = $server['remote_addr'] ?? null;
        $_SERVER['HTTP_CLIENT_IP'] = $client_id;

        // todo ip 检验
        if (!$client_ip) {
            return $this->result(IOCode::NOT_FOUND('illegal ip'));
        }

        // todo clientID 检验
        if (!$client_id) {
            return $this->result(IOCode::NOT_FOUND('illegal client'));
        }

        $token = null;
        $platform = null;
        //
        $urlParams = null;
        if (!$urlParams && isset($server['request_uri'])) $urlParams = $server['request_uri'];
        if (!$urlParams && isset($server['path_info'])) $urlParams = $server['path_info'];
        if (!$urlParams) {
            return $this->result(IOCode::NOT_FOUND(false));
        }
        $urlParams = explode('/', $urlParams);
        foreach ($urlParams as $u) {
            if (strpos($u, '[platform]') === 0) {
                $platform = str_replace('[platform]', '', $u);
            }
            if (strpos($u, '[token]') === 0) {
                $token = str_replace('[token]', '', $u);
            }
        }

        // todo token 检验
        if (empty(CONFIG['io_secret']) || $token !== CONFIG['io_secret']) {
            return $this->result(IOCode::NOT_FOUND('illegal secret'));
        }

        // todo platform 检验
        if (!$platform) {
            return $this->result(IOCode::NOT_FOUND('illegal online'));
        }

        $temp = explode('.', $scope);
        $action = $temp ? array_pop($temp) : '';
        if (strpos($action, '__') !== false) {
            return $this->result(IOCode::ERROR('illegal protected'));
        }

        $tempLen = count($temp);
        $model = '';
        for ($i = 0; $i < $tempLen; $i++) {
            if ($i == 0) $model = '\\' . $temp[$i] . '\\Model';
            else $model .= '\\' . $temp[$i];
        }

        //todo 如果没有指向类，恶意无返回
        $className = $model . 'Model';
        if (!class_exists($className)) {
            return $this->result(IOCode::NOT_FOUND('illegal handle.c'));
        }

        //is_http
        $is_http = true;
        if ($is_http === true && isset($server['request_scheme']) && $server['request_scheme'] === 'https') {
            $is_http = false;
        }
        if ($is_http === true && isset($server['server_protocol']) && strpos($server['server_protocol'], 'https') !== false) {
            $is_http = false;
        }

        //host
        $host = null;
        $hostName = $header['host'] ?? null;
        !$hostName && $hostName = $server['server_name'] ?? null;
        if ($hostName) {
            if (strpos($hostName, ':') === false || strpos($hostName, ':') > 6) {
                $host = ($is_http ? 'http' : 'https') . '://' . $hostName;
            } else{
                $host = $hostName;
            }
        }

        //x-host
        $xHost = null;
        if (isset($header['x_host'])) {
            $xHost = ($is_http ? 'http' : 'https') . '://' . $header['x_host'];
        }
        if (!empty($header['origin']) && strpos($header['origin'], ':') !== false) {
            $eho = explode(':', $header['origin']);
            $xHost .= ':' . array_pop($eho);
        }

        //domain
        $domain = str_replace(['http://', 'https://'], '', $host);
        if (!isIp($domain)) {
            $domain = explode('.', $domain);
            $d = array(array_pop($domain), array_pop($domain));
            $d = array_reverse($d);
            $domain = implode('.', $d);
        } else {
            $domain = null;
        }
        //ua
        $user_agent = $header['user_agent'] ?? $header['user-agent'] ?? null;
        //todo 如果没有指向行为，恶意无返回
        $model = new $className(array(
            'header' => $header,
            'server' => $server,
            'files' => $files,
            'scope' => $scope,
            'action' => $action,
            'platform' => $platform,
            'client_ip' => $client_ip,
            'client_id' => $client_id,
            'request' => $post,
            'domain' => $domain,
            'host' => $host,
            'x_host' => $xHost,
            'is_http' => $is_http,
            'is_local' => $client_ip === '127.0.0.1',
            'source' => getSourceByUserAgent($user_agent),
        ));
        if (!method_exists($model, $action)) {
            return $this->result(IOCode::NOT_FOUND('illegal handle.m'));
        }
        // todo 检查权限
        if (method_exists($model, 'checkPermission__') && !$model->checkPermission__()) {
            return $this->result(IOCode::NOT_PERMISSION($model->getFalseMsg__()));
        }
        try {
            return $this->result($model->$action());
        } catch (\Exception $e) {
            return $this->result(IOCode::EXCEPTION($e->getMessage()));
        }
    }

}
