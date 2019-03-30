<?php

namespace library;

class Table
{

    private $_transTrace = array();

    public function __construct()
    {
        foreach (CONFIG['db'] as $k => $v) {
            $this->_transTrace[strtoupper($k)] = 0;
        }
    }

    /**
     * 全局数据库
     * @param null $conf
     * @return Mysql | Pgsql | Mssql | null
     * @tips 需要的参数为:
     * host  地址
     * port  端口
     * user  账号
     * pwd   密码
     * name  数据库名
     */
    protected function db($conf = null)
    {
        $conf = $conf ? strtolower($conf) : 'default';
        $link = !empty(CONFIG['db'][$conf]) ? CONFIG['db'][$conf] : null;
        $u = md5(var_export($link, true));
        if (!isset($GLOBALS['db'])) {
            $GLOBALS['db'] = array();
        }
        if (!isset($GLOBALS['db'][$u]) || !$GLOBALS['db'][$u]) {
            $dbType = $link['type'] ? $link['type'] : 'Mysql';
            $dbType = "\\library\\" . ucfirst(strtolower($dbType));
            //D("SQL<{$dbType}> BUILD ({$conf})");
            $GLOBALS['db'][$u] = new $dbType($link['host'], $link['port'], $link['user'], $link['pwd'], $link['name'], $link['charset']);
        }
        return $GLOBALS['db'][$u];
    }

    /**
     * @return Redis
     * @throws \Exception
     */
    protected function redis()
    {
        return $this->db()->redis();
    }

}