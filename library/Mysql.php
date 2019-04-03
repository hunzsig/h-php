<?php
/**
 * 数据库连接类，依赖 PDO_MYSQL 扩展
 */

namespace library;

use Exception;
use PDO;
use PDOException;

class Mysql extends DataBase
{

    /**
     * pdo 实例
     *
     * @var PDO
     */
    private $pdo;

    /**
     * pdo sQuery
     *
     * @var \PDOStatement
     */
    private $PDOStatement;

    /**
     * dsn pdo链接设置
     *
     * @var string
     */
    private $dsn;

    /**
     * 数据库用户名密码等配置
     *
     * @var array
     */
    private $settings = array();

    /**
     * sql 的参数
     *
     * @var array
     */
    private $parameters = array();

    /**
     * 最后一条直行的 sql
     *
     * @var string
     */
    private $lastSql = '';

    /**
     * 参数
     *
     * @var array
     */
    private $_options = array();

    /**
     * 错误信息
     *
     * @var
     */
    private $error;

    /**
     * 查询表达式
     *
     * @var string
     */
    private $selectSql = 'SELECT%DISTINCT% %FIELD% FROM %TABLE% %ALIA% %FORCE%%JOIN%%WHERE%%GROUP%%HAVING%%ORDER%%LIMIT% %UNION%%LOCK%%COMMENT%';

    /**
     * 多重嵌套事务处理堆栈
     */
    private $_transTrace = 0;

    /**
     * where 条件类型设置
     */
    const equalTo = 'equalTo';                              //等于
    const notEqualTo = 'notEqualTo';                        //不等于
    const greaterThan = 'greaterThan';                      //大于
    const greaterThanOrEqualTo = 'greaterThanOrEqualTo';    //大于等于
    const lessThan = 'lessThan';                            //小于
    const lessThanOrEqualTo = 'lessThanOrEqualTo';          //小于等于
    const like = 'like';                                    //包含
    const notLike = 'notLike';                              //不包含
    const isNull = 'isNull';                                //为空
    const isNotNull = 'isNotNull';                          //不为空
    const between = 'between';                              //在值之内
    const notBetween = 'notBetween';                        //在值之外
    const in = 'in';                                        //在或集
    const notIn = 'notIn';                                  //不在或集
    const any = 'any';                                      //any
    const contains = 'contains';                            //contains
    const notContains = 'notContains';                      //notContains
    const containsAnd = 'containsAnd';                      //containsAnd
    const notContainsAnd = 'notContainsAnd';                //notContainsAnd

    /**
     * 条件对象，实现无敌闭包
     *
     * @var array
     */
    private $_where = array();
    private $_where_table = '';

    /**
     * 排序类型设置
     */
    const DESC = 'desc';
    const ASC = 'asc';

    /**
     * 临时字段寄存
     */
    private $_currentFieldType = array();
    private $_tempFieldType = array();

    /**
     * 清除所有数据
     */
    protected function resetAll()
    {
        $this->_options = array();
        $this->_where = array();
        $this->_where_table = '';
        $this->_currentFieldType = array();
        $this->_tempFieldType = array();
        $this->parameters = array();
        $this->lastSql = '';
        $this->error = '';
        parent::resetAll();
    }

    /**
     * 构造方法
     *
     * @param string $host
     * @param int $port
     * @param string $user
     * @param string $password
     * @param string $db_name
     * @param string $charset
     */
    public function __construct($host, $port, $user, $password, $db_name, $charset)
    {
        $this->settings = array(
            'host' => $host,
            'port' => $port,
            'user' => $user,
            'password' => $password,
            'dbname' => $db_name,
            'charset' => $charset ?: 'utf8mb4',
        );
        $this->dsn();
    }

    /**
     * 析构方法
     * @access public
     */
    public function __destruct()
    {
        $this->pdoFree();
        $this->pdoClose();
    }

    /**
     * 获取 DSN
     */
    private function dsn()
    {
        if (!$this->dsn) {
            $this->dsn = 'mysql:dbname=' . $this->settings["dbname"] . ';host=' . $this->settings["host"] . ';port=' . $this->settings['port'];
        }
        return $this->dsn;
    }

    /**
     * 获取 PDO
     */
    private function pdo()
    {
        if (!$this->pdo) {
            try {
                $this->pdo = new PDO($this->dsn(), $this->settings["user"], $this->settings["password"],
                    array(
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $this->settings['charset'],
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_STRINGIFY_FETCHES => false,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    )
                );
            } catch (PDOException $e) {
                exit($e->getMessage());
            }
        }
        return $this->pdo;
    }

    /**
     * 关闭 PDOState
     */
    private function pdoFree()
    {
        if (!empty($this->PDOStatement)) {
            $this->PDOStatement = null;
        }
    }

    /**
     * 关闭 PDO连接
     */
    private function pdoClose()
    {
        $this->pdo = null;
    }

    /**
     * 数据库错误信息
     * @param $err
     * @return bool
     */
    private function error($err)
    {
        $this->error = $err;
        return false;
    }

    /**
     * 获取数据库错误信息
     * @return mixed
     */
    public function getError()
    {
        $error = $this->error;
        if (!$error) {
            if ($this->pdo) {
                $errorInfo = $this->pdo->errorInfo();
                $error = $errorInfo[1] . ':' . $errorInfo[2];
            }
            if ('' != $this->lastSql) {
                $error .= "\n [ SQL语句 ] : " . $this->lastSql;
            }
        }
        return $error;
    }

    private function parseKSort(&$val)
    {
        if (is_array($val)) {
            ksort($val);
            foreach ($val as $k => $v) {
                $val[$k] = $this->parseKSort($v);
            }
        }
        return $val;
    }

    private function parseValueByFieldType($val, $ft)
    {
        if (!in_array($ft, ['json', 'jsonb']) && is_array($val)) {
            foreach ($val as $k => $v) {
                $val[$k] = $this->parseValueByFieldType($v, $ft);
            }
            return $val;
        }
        switch ($ft) {
            case 'int':
            case 'bigint':
            case 'smallint':
            case 'tinyint':
                $val = intval($val);
                break;
            case 'boolean':
                $val = boolval($val);
                break;
            case 'json':
                $val = json_encode($val);
                if ($this->isCrypto()) {
                    $json = array('crypto' => $this->enCrypto($val));
                    $val = json_encode($json);
                }
                $val = addslashes($val);
                break;
            case 'date':
                $val = date('Y-m-d', strtotime($val));
                break;
            case 'numeric':
            case 'decimal':
                $val = round($val, 10);
                break;
            case 'char':
            case 'varchar':
            case 'text':
                $val = trim($val);
                if ($this->isCrypto()) {
                    $val = $this->enCrypto($val);
                }
                break;
            default:
                break;
        }
        return $val;
    }

    private function parseWhereByFieldType($val, $ft)
    {
        if (!in_array($ft, ['json', 'jsonb']) && is_array($val)) {
            foreach ($val as $k => $v) {
                $val[$k] = $this->parseWhereByFieldType($v, $ft);
            }
            return $val;
        }
        switch ($ft) {
            case 'int':
            case 'bigint':
            case 'smallint':
            case 'tinyint':
                $val = intval($val);
                break;
            case 'boolean':
                $val = boolval($val);
                break;
            case 'date':
                $val = date('Y-m-d', strtotime($val));
                break;
            case 'numeric':
            case 'decimal':
                $val = round($val, 10);
                break;
            case 'char':
            case 'varchar':
            case 'text':
                $val = trim($val);
                if ($this->isCrypto()) {
                    $val = $this->enCrypto($val);
                }
                break;
            default:
                break;
        }
        return $val;
    }

    /**
     * 数组转 MY 形式数组(实质上是一个逗号序列，运用 not / contains 查询)
     * @param $arr
     * @param $type
     * @return mixed
     */
    public function toMyArray($arr, $type)
    {
        if ($type && is_array($arr)) {
            if ($arr) {
                foreach ($arr as $ak => $a) {
                    $arr[$ak] = $this->parseValueByFieldType($a, $type);
                }
                $arr = ',,,,,' . implode(',', $arr);
            } else {
                $arr = null;
            }
        }
        return $arr;
    }

    /**
     * MY 形式数组 转 数组(实质上是一个逗号序列，运用 not / contains 查询)
     * @param $arr
     * @param $type
     * @return mixed
     */
    public function toCArray($arr, $type)
    {
        if ($type && is_string($arr)) {
            if ($arr) {
                $arr = str_replace(',,,,,', '', $arr);
                $arr = explode(',', $arr);
                if ($this->isCrypto()) {
                    foreach ($arr as $ak => $a) {
                        $arr[$ak] = $this->deCrypto($a);
                    }
                }
            } else {
                $arr = array();
            }
        }
        return $arr;
    }

    /**
     * 递归式格式化数据
     * @param $result
     * @return mixed
     */
    private function fetchFormat($result)
    {
        $ft = $this->getFieldType();
        if ($ft) {
            foreach ($result as $k => $v) {
                if (is_array($v)) {
                    $result[$k] = $this->fetchFormat($v);
                } elseif (isset($ft[$k])) {
                    switch ($ft[$k]) {
                        case 'json':
                            $result[$k] = json_decode($v, true);
                            if ($this->isCrypto()) {
                                $crypto = $result[$k]['crypto'] ?? '';
                                $crypto = $this->deCrypto($crypto);
                                $result[$k] = json_decode($crypto, true);
                            }
                            $result[$k] = $this->parseKSort($result[$k]);
                            break;
                        case 'tinyint':
                        case 'bigint':
                        case 'smallint':
                        case 'int':
                            $result[$k] = intval($v);
                            break;
                        case 'numeric':
                        case 'decimal':
                            $result[$k] = round($v, 10);
                            break;
                        case 'char':
                        case 'varchar':
                        case 'text':
                            if (strpos($v, ',,,,,') === false && $this->isCrypto()) {
                                $result[$k] = $this->deCrypto($v);
                            }
                            break;
                        default:
                            break;
                    }
                    if (strpos($v, ',,,,,') === 0) {
                        $result[$k] = $this->toCArray($v, $ft);
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 执行
     *
     * @param string $query
     * @return bool|\PDOStatement
     * @throws PDOException
     */
    protected function execute($query)
    {
        $this->pdoFree();
        try {
            $PDOStatement = $this->pdo()->prepare($query);
            if (!empty($this->parameters)) {
                foreach ($this->parameters as $param) {
                    $parameters = explode("\x7F", $param);
                    $PDOStatement->bindParam($parameters[0], $parameters[1]);
                }
            }
            $PDOStatement->execute();
        } catch (PDOException $e) {
            // 服务端断开时重连一次
            if ($e->errorInfo[1] == 2006 || $e->errorInfo[1] == 2013) {
                $this->pdoClose();
                try {
                    $PDOStatement = $this->pdo()->prepare($query);
                    if (!empty($this->parameters)) {
                        foreach ($this->parameters as $param) {
                            $parameters = explode("\x7F", $param);
                            $PDOStatement->bindParam($parameters[0], $parameters[1]);
                        }
                    }
                    $PDOStatement->execute();
                } catch (PDOException $ex) {
                    return $this->error($ex);
                }
            } else {
                $msg = $e->getMessage();
                $err_msg = "[" . (int)$e->getCode() . "]SQL:" . $query . " " . $msg;
                return $this->error($err_msg);
            }
        }
        $this->parameters = array();
        return $PDOStatement;
    }

    /**
     * 执行 SQL
     *
     * @param string $query
     * @param int $fetchMode
     * @return mixed
     * @throws Exception
     */
    public function query($query = '', $fetchMode = PDO::FETCH_ASSOC)
    {
        $table = $this->getTable();
        if (!$table) {
            throw new \Exception('lose table');
        }
        $query = trim($query);
        $this->lastSql = $query;

        $rawStatement = explode(" ", $query);
        $statement = strtolower(trim($rawStatement[0]));
        //read model,check cache
        if ($statement === 'select' || $statement === 'show') {
            $result = false;
            if ($this->getRedisType() === 'forever') {
                $result = $this->redis()->hGet($table, $query);
            } elseif (is_numeric($this->getRedisType())) {
                $result = $this->redis()->get($table . $query);
            }
            if ($result) return $result;
        }
        //释放前次的查询结果
        if (!$this->PDOStatement = $this->execute($query)) {
            throw new \Exception($this->getError());
        }

        if ($statement === 'select' || $statement === 'show') {
            $result = $this->PDOStatement->fetchAll($fetchMode);
            $result = $this->fetchFormat($result);
            if ($this->getRedisType() === 'forever') {
                $this->redis()->hSet($table, $query, $result);
            } elseif (is_numeric($this->getRedisType())) {
                $this->redis()->set($table . $query, $result, (int)$this->getRedisType());
            }
            return $result;
        } elseif ($statement === 'update' || $statement === 'delete') {
            if ($this->getRedisType() === 'forever') $this->redis()->delete($table);
            return $this->PDOStatement->rowCount();
        } elseif ($statement === 'insert') {
            if ($this->getRedisType() === 'forever') $this->redis()->delete($table);
            return $this->PDOStatement->rowCount();
        } else {
            return null;
        }
    }

    /**
     * 返回 lastInsertId
     *
     * @return string
     */
    public function lastInsertId()
    {
        return $this->pdo()->lastInsertId();
    }

    /**
     * 返回最后一条执行的 sql
     *
     * @return  string
     */
    public function lastSQL()
    {
        return $this->lastSql;
    }

    /**
     * 分析表达式
     * @access protected
     * @param array $options 表达式参数
     * @return array
     */
    protected function _parseOptions($options = array())
    {
        if (empty($this->_options['field'])) {
            $this->field('*');
        }
        if (is_array($options)) {
            $options = array_merge($this->_options, $options);
        }
        if (!isset($options['table'])) {
            $options['table'] = $this->getTable();
        }
        //别名
        if (!empty($options['alias'])) {
            $options['table'] .= ' ' . $options['alias'];
        }
        return $options;
    }

    /**
     * 字段和表名处理
     * @access protected
     * @param string $key
     * @return string
     */
    protected function parseKey($key)
    {
        $key = trim($key);
        if (!is_numeric($key) && !preg_match('/[,\'\"\*\(\)`.\s]/', $key)) {
            $key = '`' . $key . '`';
        }
        return $key;
    }

    /**
     * value分析
     * @access protected
     * @param mixed $value
     * @return string
     */
    protected function parseValue($value)
    {
        if (is_string($value)) {
            $value = '\'' . $value . '\'';
        } elseif (is_array($value)) {
            $value = array_map(array($this, 'parseValue'), $value);
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
        } elseif (is_null($value)) {
            $value = 'null';
        }
        return $value;
    }

    /**
     * field分析
     * @access private
     * @param mixed $fields
     * @return string
     */
    private function parseField($fields)
    {
        if (is_string($fields) && '' !== $fields) {
            $fields = explode(',', $fields);
        }
        if (is_array($fields)) {
            // 完善数组方式传字段名的支持
            // 支持 'field1'=>'field2' 这样的字段别名定义
            $array = array();
            foreach ($fields as $key => $field) {
                if (!is_numeric($key))
                    $array[] = $this->parseKey($key) . ' AS ' . $this->parseKey($field);
                else
                    $array[] = $this->parseKey($field);
            }
            $fieldsStr = implode(',', $array);
        } else {
            $fieldsStr = '*';
        }
        return $fieldsStr;
    }

    /**
     * table分析
     * @access private
     * @param mixed $tables
     * @return string
     */
    private function parseTable($tables)
    {
        if (is_array($tables)) {// 支持别名定义
            $array = array();
            foreach ($tables as $table => $alias) {
                if (!is_numeric($table))
                    $array[] = $this->parseKey($table) . ' ' . $this->parseKey($alias);
                else
                    $array[] = $this->parseKey($alias);
            }
            $tables = $array;
        } elseif (is_string($tables)) {
            $tables = explode(',', $tables);
            return $this->parseTable($tables);
        }
        return implode(',', $tables);
    }

    /**
     * limit分析
     * @access private
     * @param mixed $limit
     * @return string
     */
    private function parseLimit($limit)
    {
        return !empty($limit) ? ' LIMIT ' . $limit . ' ' : '';
    }

    /**
     * join分析
     * @access private
     * @param mixed $join
     * @return string
     */
    private function parseJoin($join)
    {
        $joinStr = '';
        if (!empty($join)) {
            $joinStr = ' ' . implode(' ', $join) . ' ';
        }
        return $joinStr;
    }

    /**
     * order分析
     * @access private
     * @param mixed $order
     * @return string
     */
    private function parseOrder($order)
    {
        if (is_array($order)) {
            $array = array();
            foreach ($order as $key => $val) {
                if (is_numeric($key)) {
                    $array[] = $this->parseKey($val);
                } else {
                    $array[] = $this->parseKey($key) . ' ' . $val;
                }
            }
            $order = implode(',', $array);
        }
        return !empty($order) ? ' ORDER BY ' . $order : '';
    }

    /**
     * group分析
     * @access private
     * @param mixed $group
     * @return string
     */
    private function parseGroup($group)
    {
        return !empty($group) ? ' GROUP BY ' . $group : '';
    }

    /**
     * having分析
     * @access private
     * @param string $having
     * @return string
     */
    private function parseHaving($having)
    {
        return !empty($having) ? ' HAVING ' . $having : '';
    }

    /**
     * comment分析
     * @access private
     * @param string $comment
     * @return string
     */
    private function parseComment($comment)
    {
        return !empty($comment) ? ' /* ' . $comment . ' */' : '';
    }

    /**
     * distinct分析
     * @access private
     * @param mixed $distinct
     * @return string
     */
    private function parseDistinct($distinct)
    {
        return !empty($distinct) ? ' DISTINCT ' : '';
    }

    /**
     * union分析
     * @access private
     * @param mixed $union
     * @return string
     */
    private function parseUnion($union)
    {
        if (empty($union)) return '';
        if (isset($union['_all'])) {
            $str = 'UNION ALL ';
            unset($union['_all']);
        } else {
            $str = 'UNION ';
        }
        $sql = array();
        foreach ($union as $u) {
            $sql[] = $str . (is_array($u) ? $this->buildSelectSql($u) : $u);
        }
        return implode(' ', $sql);
    }

    /**
     * 设置锁机制
     * @access private
     * @param bool $lock
     * @return string
     */
    private function parseLock($lock = false)
    {
        return $lock ? ' FOR UPDATE ' : '';
    }

    /**
     * index分析，可在操作链中指定需要强制使用的索引
     * @access private
     * @param mixed $index
     * @return string
     */
    private function parseForce($index)
    {
        if (empty($index)) return '';
        if (is_array($index)) $index = join(",", $index);
        return sprintf(" FORCE INDEX ( %s ) ", $index);
    }

    /**
     * where分析
     * @access private
     * @param mixed $where
     * @return string
     */
    private function parseWhere($where)
    {
        $whereStr = '';
        if ($this->_where) {
            //闭包形式
            $whereStr = $this->builtWhereSql($this->_where);
        } elseif ($where) {
            if (is_string($where)) {
                //直接字符串
                $whereStr = $where;
            } elseif (is_array($where)) {
                //数组形式,只支持field=>value形式 AND 逻辑 和 equalTo 条件
                $this->_where = array();
                foreach ($where as $k => $v) {
                    $this->equalTo($k, $v);
                }
                $whereStr = $this->builtWhereSql($this->_where);
            }
        }
        return empty($whereStr) ? '' : ' WHERE ' . $whereStr;
    }

    /**
     * sql过滤
     * @param $sql
     * @return bool
     */
    private function sqlFilter($sql)
    {
        $result = true;
        if ($sql) {
            if (is_array($sql)) {
                foreach ($sql as $v) {
                    if (!$v) continue;
                    if (is_array($v)) {
                        return $this->sqlFilter($v);
                    } else {
                        $preg = preg_match('/(.*?((select)|(from)|(count)|(delete)|(update)|(drop)|(truncate)).*?)+/i', $v);
                        if ($preg) {
                            $result = false;
                            break;
                        }
                    }
                }
            } else {
                if ($sql) {
                    $result = preg_match('/(.*?((select)|(from)|(count)|(delete)|(update)|(drop)|(truncate)).*?)+/i', $sql) ? false : true;
                }
            }
        }
        return $result;
    }

    private function builtWhereSql($closure, $sql = '', $cond = 'and')
    {
        foreach ($closure as $v) {
            $table = isset($v['table']) && $v['table'] ? $v['table'] : $this->getTable();
            if (!$table) {
                return null;
            }
            $ft = $this->getFieldType($table);
            if ($v['operat'] === 'closure') {
                $innerSql = '(' . $this->builtWhereSql($v['closure'], '', $v['cond']) . ')';
                $sql .= $sql ? " {$cond}{$innerSql} " : $innerSql;
            } else {
                $si = strpos($v['field'], '#>>');
                if ($si > 0) {
                    preg_match("/\(?(.*)#>>/", $v['field'], $siField);
                    $ft_type = $ft[$table . '_' . $siField[1]] ?? null;
                } else {
                    $ft_type = $ft[$table . '_' . $v['field']] ?? null;
                }
                if (empty($ft_type)) { // 根据表字段过滤无效field
                    continue;
                }
                if ($this->sqlFilter($v['value'])) {
                    $innerSql = ' ';
                    $field = $this->parseKey($v['field']);
                    if ($si > 0 && strpos($v['field'], '(') === 0) {
                        $innerSql .= '(' . $this->parseKey($table) . '.';
                        $innerSql .= substr($field, 1, strlen($field));
                    } else {
                        $innerSql .= $this->parseKey($table) . '.';
                        $innerSql .= $field;
                    }
                    $isContinue = false;
                    switch ($v['operat']) {
                        case self::equalTo:
                            $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            $value = $this->parseValue($value);
                            $innerSql .= " = {$value}";
                            break;
                        case self::notEqualTo:
                            $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            $value = $this->parseValue($value);
                            $innerSql .= " <> {$value}";
                            break;
                        case self::greaterThan:
                            $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            $value = $this->parseValue($value);
                            $innerSql .= " > {$value}";
                            break;
                        case self::greaterThanOrEqualTo:
                            $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            $value = $this->parseValue($value);
                            $innerSql .= " >= {$value}";
                            break;
                        case self::lessThan:
                            $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            $value = $this->parseValue($value);
                            $innerSql .= " < {$value}";
                            break;
                        case self::lessThanOrEqualTo:
                            $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            $value = $this->parseValue($value);
                            $innerSql .= " <= {$value}";
                            break;
                        case self::like:
                            if ($this->isCrypto()) {
                                $likeO = '';
                                $likeE = '';
                                $vspllit = str_split($v['value']);
                                if ($vspllit[0] === '%') {
                                    $likeO = array_shift($vspllit);
                                }
                                if ($vspllit[count($vspllit) - 1] === '%') {
                                    $likeE = array_pop($vspllit);
                                }
                                $value = $this->parseWhereByFieldType(implode('', $vspllit), $ft_type);
                                $value = $likeO . $value . $likeE;
                            } else {
                                $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            }
                            $value = $this->parseValue($value);
                            $innerSql .= " like {$value}";
                            break;
                        case self::notLike:
                            if ($this->isCrypto()) {
                                $likeO = '';
                                $likeE = '';
                                $vspllit = str_split($v['value']);
                                if ($vspllit[0] === '%') {
                                    $likeO = array_shift($vspllit);
                                }
                                if ($vspllit[count($vspllit) - 1] === '%') {
                                    $likeE = array_pop($vspllit);
                                }
                                $value = $this->parseWhereByFieldType(implode('', $vspllit), $ft_type);
                                $value = $likeO . $value . $likeE;
                            } else {
                                $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            }
                            $value = $this->parseValue($value);
                            $innerSql .= " not like {$value}";
                            break;
                        case self::isNull:
                            $innerSql .= " is null ";
                            break;
                        case self::isNotNull:
                            $innerSql .= " is not null ";
                            break;
                        case self::between:
                            $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            $value = $this->parseValue($value);
                            $innerSql .= " between {$value[0]} and {$value[1]}";
                            break;
                        case self::notBetween:
                            $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            $value = $this->parseValue($value);
                            $innerSql .= " not between {$value[0]} and {$value[1]}";
                            break;
                        case self::in:
                            $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            $value = $this->parseValue($value);
                            $value = implode(',', (array)$value);
                            $innerSql .= " in ({$value})";
                            break;
                        case self::notIn:
                            $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            $value = $this->parseValue($value);
                            $value = implode(',', (array)$value);
                            $innerSql .= " not in ({$value})";
                            break;
                        case self::any: // rename in
                            $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            $value = $this->parseValue($value);
                            $value = implode(',', (array)$value);
                            $innerSql .= " in ({$value})";
                            break;
                        case self::contains: // rename find_in_set
                            if ($v['value']) {
                                $v['value'] = (array)$v['value'];
                                foreach ($v['value'] as $vfisk => $vfis) {
                                    if ($vfis) {
                                        $vfis = $this->parseWhereByFieldType($vfis, $ft_type);
                                        $vfis = $this->parseValue($vfis);
                                        if ($vfisk === 0) {
                                            $innerSql = " (find_in_set({$vfis},{$field})";
                                        } else {
                                            $innerSql .= " or find_in_set({$vfis},{$field})";
                                        }
                                    }
                                }
                                $innerSql .= ")";
                            } else {
                                $isContinue = true;
                            }
                            break;
                        case self::notContains:
                            if ($v['value']) {
                                $v['value'] = (array)$v['value'];
                                foreach ($v['value'] as $vfisk => $vfis) {
                                    if ($vfis) {
                                        $vfis = $this->parseWhereByFieldType($vfis, $ft_type);
                                        $vfis = $this->parseValue($vfis);
                                        if ($vfisk === 0) {
                                            $innerSql = " (not find_in_set({$vfis},{$field})";
                                        } else {
                                            $innerSql .= " or not find_in_set({$vfis},{$field})";
                                        }
                                    }
                                }
                                $innerSql .= ")";
                            } else {
                                $isContinue = true;
                            }
                            break;
                        case self::containsAnd: // rename find_in_set
                            if ($v['value']) {
                                $v['value'] = (array)$v['value'];
                                foreach ($v['value'] as $vfisk => $vfis) {
                                    if ($vfis) {
                                        $vfis = $this->parseWhereByFieldType($vfis, $ft_type);
                                        $vfis = $this->parseValue($vfis);
                                        if ($vfisk === 0) {
                                            $innerSql = " (find_in_set({$vfis},{$field})";
                                        } else {
                                            $innerSql .= " and find_in_set({$vfis},{$field})";
                                        }
                                    }
                                }
                                $innerSql .= ")";
                            } else {
                                $isContinue = true;
                            }
                            break;
                        case self::notContainsAnd:
                            if ($v['value']) {
                                $v['value'] = (array)$v['value'];
                                foreach ($v['value'] as $vfisk => $vfis) {
                                    if ($vfis) {
                                        $vfis = $this->parseWhereByFieldType($vfis, $ft_type);
                                        $vfis = $this->parseValue($vfis);
                                        if ($vfisk === 0) {
                                            $innerSql = " (not find_in_set({$vfis},{$field})";
                                        } else {
                                            $innerSql .= " and not find_in_set({$vfis},{$field})";
                                        }
                                    }
                                }
                                $innerSql .= ")";
                            } else {
                                $isContinue = true;
                            }
                            break;
                        default:
                            $isContinue = true;
                            break;
                    }
                    if ($isContinue) continue;
                    $sql .= $sql ? " {$cond}{$innerSql} " : $innerSql;
                }
            }
        }
        return $sql;
    }

    /**
     * 生成查询SQL
     * @access private
     * @param array $options 表达式
     * @return string
     */
    private function buildSelectSql($options = array())
    {
        if (isset($options['page'])) {
            // 根据页数计算limit
            list($page, $listRows) = $options['page'];
            $page = $page > 0 ? $page : 1;
            $listRows = $listRows > 0 ? $listRows : (is_numeric($options['limit']) ? $options['limit'] : 20);
            $offset = $listRows * ($page - 1);
            $options['limit'] = $listRows . ' OFFSET ' . $offset;
        }
        $sql = $this->parseSql($this->selectSql, $options);
        return $sql;
    }

    /**
     * 替换SQL语句中表达式
     * @access private
     * @param string $sql
     * @param array $options 表达式
     * @return string
     */
    private function parseSql($sql, $options = array())
    {
        $sql = str_replace(
            array('%TABLE%', '%ALIA%', '%DISTINCT%', '%FIELD%', '%JOIN%', '%WHERE%', '%GROUP%', '%HAVING%', '%ORDER%', '%LIMIT%', '%UNION%', '%LOCK%', '%COMMENT%', '%FORCE%'),
            array(
                $this->parseTable(!empty($options['table_origin']) ? $options['table_origin'] : (isset($options['table']) ? $options['table'] : false)),
                !empty($options['table_origin']) ? $this->parseTable(' AS ' . $options['table']) : null,
                $this->parseDistinct(isset($options['distinct']) ? $options['distinct'] : false),
                $this->parseField(!empty($options['field']) ? $options['field'] : '*'),
                $this->parseJoin(!empty($options['join']) ? $options['join'] : ''),
                $this->parseWhere(!empty($options['where']) ? $options['where'] : ''),
                $this->parseGroup(!empty($options['group']) ? $options['group'] : ''),
                $this->parseHaving(!empty($options['having']) ? $options['having'] : ''),
                $this->parseOrder(!empty($options['order']) ? $options['order'] : ''),
                $this->parseLimit(!empty($options['limit']) ? $options['limit'] : ''),
                $this->parseUnion(!empty($options['union']) ? $options['union'] : ''),
                $this->parseLock(isset($options['lock']) ? $options['lock'] : false),
                $this->parseComment(!empty($options['comment']) ? $options['comment'] : ''),
                $this->parseForce(!empty($options['force']) ? $options['force'] : '')
            ), $sql);
        return $sql;
    }

    /**
     * 获取表字段类型
     * @param $table
     * @return mixed|null
     */
    private function getFieldType($table = null)
    {
        if (!$table) return $this->_currentFieldType;
        if (empty($this->_tempFieldType[$table])) {
            $alia = false;
            $originTable = null;
            if (!empty($this->_options['alia'][$table])) {
                $originTable = $table;
                $table = $this->_options['alia'][$table];
                $alia = true;
            }
            $sql = "SELECT COLUMN_NAME AS `field`,DATA_TYPE AS fieldtype FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema ='{$this->settings["dbname"]}' AND table_name = '{$table}';";
            $result = null;
            try {
                $result = $this->redis()->get($sql);
            } catch (\Exception $e) {
            }
            if (!$result) {
                $PDOStatement = $this->execute($sql);
                if ($PDOStatement) {
                    $result = $PDOStatement->fetchAll(PDO::FETCH_ASSOC);
                    try {
                        $this->redis()->set($sql, $result, 600);
                    } catch (\Exception $e) {
                    }
                } else {
                }
            }
            $ft = array();
            foreach ($result as $v) {
                if ($alia && $originTable) {
                    $ft[$originTable . '_' . $v['field']] = $v['fieldtype'];
                } else {
                    $ft[$table . '_' . $v['field']] = $v['fieldtype'];
                }
            }
            $this->_tempFieldType[$table] = $ft;
            $this->_currentFieldType = array_merge($this->_currentFieldType, $ft);
        }
        return $this->_currentFieldType;
    }


    /**
     * 开始事务
     */
    public function beginTrans()
    {
        if ($this->_transTrace <= 0) {
            if ($this->pdo()->inTransaction()) {
                $this->pdo()->commit();
            }
            $this->_transTrace = 1;
        } else {
            $this->_transTrace++;
            return true;
        }
        try {
            return $this->pdo()->beginTransaction();
        } catch (PDOException $e) {
            // 服务端断开时重连一次
            if ($e->errorInfo[1] == 2006 || $e->errorInfo[1] == 2013) {
                $this->pdoClose();
                return $this->pdo()->beginTransaction();
            } else {
                throw $e;
            }
        }
    }

    /**
     * 提交事务
     */
    public function commitTrans()
    {
        $this->_transTrace > 0 && $this->_transTrace--;
        if ($this->_transTrace > 0) {
            return true;
        }
        return $this->pdo()->commit();
    }

    /**
     * 事务回滚
     */
    public function rollBackTrans()
    {
        $this->_transTrace > 0 && $this->_transTrace--;
        if ($this->_transTrace > 0) {
            return true;
        }
        if ($this->pdo()->inTransaction()) {
            return $this->pdo()->rollBack();
        }
    }

    /**
     * 检测是否在一个事务内
     * @return bool
     */
    public function inTransaction()
    {
        return $this->pdo()->inTransaction();
    }

    /**
     * 获取当前table
     * @return string
     */
    protected function getTable()
    {
        return $this->_options['table'] ?? null;
    }

    /**
     * 哪个表
     *
     * @param string $table
     * @return self
     */
    public function table($table)
    {
        $this->resetAll();
        $table = str_replace([' as ', ' AS ', ' As ', ' aS ', ' => '], ' ', trim($table));
        $tableEX = explode(' ', $table);
        if (count($tableEX) === 2) {
            $this->_options['table'] = $tableEX[1];
            $this->_options['table_origin'] = $tableEX[0];
            if (!isset($this->_options['alia'])) {
                $this->_options['alia'] = array();
            }
            $this->_options['alia'][$tableEX[1]] = $tableEX[0];
        } else {
            $this->_options['table'] = $table;
            $this->_options['table_origin'] = null;
        }
        return $this;
    }

    /**
     * USING支持 用于多表删除
     * @access public
     * @param mixed $using
     * @return self
     */
    public function using($using)
    {
        if ($using) {
            $this->_options['using'] = $using;
        }
        return $this;
    }

    /**
     * 查询SQL组装 union
     * @access public
     * @param mixed $union
     * @param boolean $all
     * @return self
     */
    public function union($union, $all = false)
    {
        if (empty($union)) return $this;
        if ($all) {
            $this->_options['union']['_all'] = true;
        }
        if (is_object($union)) {
            $union = get_object_vars($union);
        }
        // 转换union表达式
        $options = null;
        if (is_array($union)) {
            if (isset($union[0])) {
                $this->_options['union'] = array_merge($this->_options['union'], $union);
                return $this;
            } else {
                $this->_options['union'][] = $union;
            }
        } elseif (is_string($options)) {
            $this->_options['union'][] = $options;
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getJoinQty()
    {
        return (int)$this->_options['joinQty'];
    }

    /**
     * 查询SQL组装 join
     * @access public
     * @param mixed $join
     * @param string $type JOIN类型
     * @return self
     */
    private function joinTo($join, $type = 'LEFT')
    {
        if (is_array($join)) {
            foreach ($join as $key => &$_join) {
                $_join = false !== stripos($_join, 'JOIN') ? $_join : $type . ' JOIN ' . $_join;
            }
            $this->_options['join'] = $join;
        } elseif (!empty($join)) {
            $this->_options['join'][] = false !== stripos($join, 'JOIN') ? $join : $type . ' JOIN ' . $join;
        }
        return $this;
    }

    /**
     * @param $target
     * @param $join
     * @param array $req
     * @param string $type INNER | OUTER | LEFT | RIGHT
     * @return self
     */
    public function join($target, $join, $req = array(), $type = 'INNER')
    {
        if ($target && $join) {
            $join = str_replace([' as ', ' AS ', ' As ', ' aS ', ' => '], ' ', trim($join));
            $originJoin = $join = explode(' ', $join);
            $alia = null;
            if (isset($join[1]) && $join[1]) {
                $alia = $this->parseKey($join[1]);
            }
            if (isset($join[0]) && $join[0]) {
                $join = $this->parseKey($join[0]);
            }
            $target = $this->parseKey($target);
            $join = $this->parseKey($join);
            $jsonStr = $join;
            $jsonStr .= $alia ? " AS {$alia}" : "";
            if ($req) {
                $jsonStr .= ' ON ';
                $first = false;
                foreach ($req as $k => $v) {
                    if (!$first) {
                        $first = true;
                        $jsonStr .= $alia ? "{$target}.{$k}={$alia}.{$v}" : "{$target}.{$k}={$join}.{$v}";
                    } else $jsonStr .= " AND " . ($alia ? "{$target}.{$k}={$alia}.{$v}" : '');
                }
            }
            if (!isset($this->_options['joinQty'])) {
                $this->_options['joinQty'] = 0;
            }
            $this->_options['joinQty']++;
            if ($alia) {
                if (!isset($this->_options['alia'])) {
                    $this->_options['alia'] = array();
                }
                $this->_options['alia'][$originJoin[1]] = $originJoin[0];
            }
            $this->joinTo($jsonStr, $type);
        }
        return $this;
    }

    /**
     * @param string $operat see self
     * @param string $field
     * @param null $value
     * @return self
     */
    private function whereOperat($operat, $field, $value = null)
    {
        if ($operat == self::isNull || $operat == self::isNotNull || $value !== null) {//排除空值
            if ($operat != self::like || $operat != self::notLike || ($value != '%' && $value != '%%')) {//排除空like
                $this->_where[] = array(
                    'operat' => $operat,
                    'table' => $this->_where_table,
                    'field' => $field,
                    'value' => $value,
                );
            }
        }
        return $this;
    }

    public function clearWhere()
    {
        $this->_where = array();
        $this->_where_table = '';
        return $this;
    }

    public function whereTable($table)
    {
        $this->_where_table = $table;
        return $this;
    }

    /**
     * 条件闭包
     * @param string $cond 'and' || 'or'
     * @param boolean $isGlobal 'field or total'
     * @return self
     */
    public function closure($cond = 'and', $isGlobal = false)
    {
        if ($this->_where) {
            $o = array();
            $f = array();
            foreach ($this->_where as $v) {
                if ($v['operat'] === 'closure') {
                    $o[] = $v;
                } elseif ($v['field']) {
                    $f[] = $v;
                }
            }
            if ($o && $f) {
                if ($isGlobal === false) {
                    $this->_where = $o;
                    $this->_where[] = array('operat' => 'closure', 'cond' => $cond, 'closure' => $f);
                } else {
                    $this->_where = array(array('operat' => 'closure', 'cond' => $cond, 'closure' => array_merge($o, $f)));
                }
            } elseif ($o && !$f) {
                $this->_where = array(array('operat' => 'closure', 'cond' => $cond, 'closure' => $this->_where));
            } elseif (!$o && $f) {
                $this->_where = array(array('operat' => 'closure', 'cond' => $cond, 'closure' => $f));
            }
        }
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function equalTo($field, $value)
    {
        return $this->whereOperat(self::equalTo, $field, $value);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function notEqualTo($field, $value)
    {
        return $this->whereOperat(self::notEqualTo, $field, $value);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function greaterThan($field, $value)
    {
        return $this->whereOperat(self::greaterThan, $field, $value);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function greaterThanOrEqualTo($field, $value)
    {
        return $this->whereOperat(self::greaterThanOrEqualTo, $field, $value);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function lessThan($field, $value)
    {
        return $this->whereOperat(self::lessThan, $field, $value);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function lessThanOrEqualTo($field, $value)
    {
        return $this->whereOperat(self::lessThanOrEqualTo, $field, $value);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function like($field, $value)
    {
        return $this->whereOperat(self::like, $field, $value);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function notLike($field, $value)
    {
        return $this->whereOperat(self::notLike, $field, $value);
    }

    /**
     * @param $field
     * @return self
     */
    public function isNull($field)
    {
        return $this->whereOperat(self::isNull, $field);
    }

    /**
     * @param $field
     * @return self
     */
    public function isNotNull($field)
    {
        return $this->whereOperat(self::isNotNull, $field);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function between($field, $value)
    {
        if (is_string($value)) $value = explode(',', $value);
        if (!is_array($value)) $value = (array)$value;
        if (count($value) !== 2) return $this;
        if (!$value[0]) return $this;
        if (!$value[1]) return $this;
        return $this->whereOperat(self::between, $field, $value);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function notBetween($field, $value)
    {
        if (is_string($value)) $value = explode(',', $value);
        if (!is_array($value)) $value = (array)$value;
        if (count($value) !== 2) return $this;
        return $this->whereOperat(self::notBetween, $field, $value);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function in($field, $value)
    {
        return $this->whereOperat(self::in, $field, $value);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function notIn($field, $value)
    {
        return $this->whereOperat(self::notIn, $field, $value);
    }

    /**
     * @param array $where
     * @return self
     */
    public function where(array $where)
    {
        if ($where) {
            foreach ($where as $k => $v) {
                $this->equalTo($k, $v);
            }
        }
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function any($field, $value)
    {
        return $this->whereOperat(self::any, $field, $value);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function contains($field, $value)
    {
        return $this->whereOperat(self::contains, $field, $value);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function notContains($field, $value)
    {
        return $this->whereOperat(self::notContains, $field, $value);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function containsAnd($field, $value)
    {
        return $this->whereOperat(self::containsAnd, $field, $value);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function notContainsAnd($field, $value)
    {
        return $this->whereOperat(self::notContainsAnd, $field, $value);
    }

    /**
     * json闭包器
     * @param $string
     * @param null $closure
     * @return array|null
     */
    private function jsonClosure($string = null, $closure = null)
    {
        if ($closure === null) {
            $closure = array();
        }
        if (mb_strlen($string, 'utf-8') > 0) {
            $end = mb_strpos($string, ')', 0, 'utf-8');
            if ($end === false) $end = mb_strlen($string, 'utf-8');
            $start = mb_strripos(mb_substr($string, 0, $end, 'utf-8'), '(', 0, 'utf-8');
            if ($start === false) $start = 0;

            $isOver = false;
            if ($start === 0 && $end === mb_strlen($string, 'utf-8')) {
                $isOver = true;
            }
            $res = mb_substr($string, $isOver ? $start : $start + 1, $isOver ? $end : $end - $start - 1, 'utf-8');
            $and = explode('&&', $res);
            $or = explode('||', $res);
            $an = substr_count($res, '&&');
            $on = substr_count($res, '||');

            $s = str_replace("({$res})", '', $string);
            $sn = substr_count($s, '( &&') + substr_count($s, '&& )') + substr_count($s, '|| )') + substr_count($s, '( ||');

            if ($an < $on) {
                $cond = 'or';
                $conds = $or;
            } else {
                $cond = 'and';
                $conds = $and;
            }
            $cl = array();
            foreach ($conds as $ds) {
                $ds = trim($ds);
                if ($ds) {
                    $cl[] = $ds;
                }
            }
            $closure[] = array(
                'cond' => $cond,
                'fields' => $cl,
            );
            if ($sn === 0) {
                $closure = array(
                    array(
                        'cond' => $cond,
                        'fields' => $closure,
                    )
                );
            }
            //todo
            if ($isOver) {
                return $closure;
            }
            $closure = $this->jsonClosure($s, $closure);
        }
        return $closure;
    }

    /**
     * json选择器
     * @param $field
     * @param $array
     * @return self
     */
    private function jsonWhere($field, $array)
    {
        if (!is_array($array)) return $this;
        foreach ($array as $v) {
            if (!empty($v['fields']) && is_array($v['fields'])) {
                $this->jsonWhere($field, $v['fields']);
                ($v['cond']) && $this->closure($v['cond']);
            } elseif (is_string($v)) {
                preg_match("/{(.*)} (.*) #(.*)/i", $v, $match);
                if (!$match) {
                    continue;
                }
                $tempMatch1 = explode(',', $match[1]);
                $matchField = '$';
                foreach ($tempMatch1 as $tmk => $tm1) {
                    if (is_numeric($tm1)) {
                        $matchField .= "[{$tm1}]";
                    } elseif (is_string($tm1)) {
                        $matchField .= ".{$tm1}";
                    } else {
                        exit('jsonWhere error');
                    }
                }
                $matchField = "\"{$matchField}\"";
                $matchField = $field . '->' . $matchField;
                $matchOperat = $match[2] ?? null;
                $matchValue = $match[3] ?? null;
                if ($matchField === null) continue;
                if ($matchOperat === null) continue;
                switch ($matchOperat) {
                    case 'n':
                        $this->isNull($matchField);
                        break;
                    case '!n':
                        $this->isNotNull($matchField);
                        break;
                    case '=':
                        $this->equalTo($matchField, $matchValue);
                        break;
                    case '<>':
                    case '!=':
                        $this->notEqualTo($matchField, $matchValue);
                        break;
                    case '%':
                        $this->like($matchField, $matchValue);
                        break;
                    case '!%':
                        $this->notLike($matchField, $matchValue);
                        break;
                    case '>':
                        $this->greaterThan($matchField, $matchValue);
                        break;
                    case '>=':
                        $this->greaterThanOrEqualTo($matchField, $matchValue);
                        break;
                    case '<':
                        $this->lessThan($matchField, $matchValue);
                        break;
                    case '<=':
                        $this->lessThanOrEqualTo($matchField, $matchValue);
                        break;
                    case '><':
                        $this->between($matchField, explode(',', $matchValue));
                        break;
                    case '!><':
                        $this->notBetween($matchField, explode(',', $matchValue));
                        break;
                    case '^':
                        $this->in($matchField, explode(',', $matchValue));
                        break;
                    case '!^':
                        $this->notIn($matchField, explode(',', $matchValue));
                        break;
                    case '*':
                        $this->any($matchField, explode(',', $matchValue));
                        break;
                    default:
                        break;
                }
            }
        }
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function json($field, $value)
    {

        if (is_string($value)) {
            $value = $this->jsonClosure($value);
        }
        $this->jsonWhere($field, $value);
        return $this;
    }


    /**
     * 指定查询字段
     * @access protected
     * @param mixed $field
     * @param string | null $table
     * @param null $function
     * @return self
     */
    public function field($field, $table = null, $function = null)
    {
        if ($table === null) {
            $table = $this->getTable();
        }
        $tableLen = mb_strlen($table, 'utf-8');
        if (!$table) {
            return $this;
        }
        if (is_string($field)) {
            $field = explode(',', $field);
        }
        if (is_array($field)) {
            $field = array_filter($field);
            $ft = $this->getFieldType($table);
            $fk = array_keys($ft);
            $parseTable = $this->parseTable($table);
            foreach ($field as $k => $v) {
                $v = trim($v);
                if ($v === '*') {
                    unset($field[$k]);
                    foreach ($fk as $kk) {
                        if ($table === substr($kk, 0, $tableLen)) {
                            $field[] = "{$parseTable}." . mb_str_replace_once("{$table}_", '', $kk) . " as {$kk}";
                        }
                    }
                } else {
                    $from = $v;
                    $to = $v;
                    $v = str_replace([' AS ', ' As ', ' => ', ' as '], ' as ', $v);
                    $aspos = strpos($v, ' as ');
                    if ($aspos > 0) {
                        $as = explode(' as ', $v);
                        $from = $as[0];
                        $to = $as[1];
                        $jsonPos = strpos($from, '#>>');
                        if ($jsonPos > 0) {
                            $jpos = explode('#>>', $v);
                            $ft[$table . '_' . $to] = $ft[$table . '_' . trim($jpos[0])];
                        } elseif (!empty($this->_currentFieldType[$table . '_' . $from])) {
                            $this->_currentFieldType[$table . '_' . $to] = $this->_currentFieldType[$table . '_' . $from];
                            $ft[$table . '_' . $to] = $ft[$table . '_' . $from];
                        }
                    }

                    if (!isset($ft[$table . '_' . $to])) {
                        continue;
                    }
                    // check function
                    $tempParseTableForm = $parseTable . '.' . $from;
                    if ($function) {
                        $tempParseTableForm = str_replace('%' . $k, $tempParseTableForm, $function);
                    }
                    $field[$k] = "{$tempParseTableForm} as {$table}_{$to}";
                }
            }
            if (!isset($this->_options['field'])) {
                $this->_options['field'] = array();
            }
            $this->_options['field'] = array_merge_recursive($this->_options['field'], $field);
        }
        return $this;
    }

    /**
     * group by
     * @access protected
     * @param mixed $groupBy
     * @param string | null $table
     * @return self
     */
    public function groupBy($groupBy, $table = null)
    {
        if (is_array($groupBy)) {
            $groupBy = implode(',', $groupBy);
        }
        if (!is_string($groupBy)) {
            return $this;
        }
        if (!isset($this->_options['group'])) {
            $this->_options['group'] = '';
        }
        if ($this->_options['group'] != '') {
            $this->_options['group'] .= ',';
        }
        if ($table) {
            $this->_options['group'] .= $this->parseTable($table) . '.' . $groupBy;
        } else $this->_options['group'] .= $groupBy;
        return $this;
    }

    /**
     * order by
     * @access protected
     * @param mixed $orderBy 支持格式 'uid asc' | array('uid asc','pid desc')
     * @param string $sort
     * @param string | null $table
     * @return self
     */
    public function orderBy($orderBy, $sort = self::ASC, $table = null)
    {
        if (!$orderBy) {
            return $this;
        }
        if (!isset($this->_options['order'])) {
            $this->_options['order'] = array();
        }
        if ($table) {
            $table = $this->parseTable($table);
        }
        if (is_string($orderBy)) {
            $sort = strtolower($sort);
            if ($table) {
                $this->_options['order'][$table . '.' . $orderBy] = $sort;
            } else {
                $this->_options['order'][$orderBy] = $sort;
            }
        } elseif (is_array($orderBy)) {
            $orderBy = array_filter($orderBy);
            foreach ($orderBy as $v) {
                $orderInfo = explode(' ', $v);
                $orderInfo[1] = strtolower($orderInfo[1]);
                if ($table) {
                    $this->_options['order'][$table . '.' . $orderInfo[0]] = $orderInfo[1];
                } else {
                    $this->_options['order'][$orderInfo[0]] = $orderInfo[1];
                }
                unset($orderInfo);
            }
        }
        return $this;
    }

    /**
     * order by string 支持 field asc | field desc 形式
     * @param $orderBy
     * @param null $table
     * @return self
     */
    public function orderByStr($orderBy, $table = null)
    {
        $orderBy = explode(',', $orderBy);
        foreach ($orderBy as $o) {
            $o = explode(' ', $o);
            if ($table) {
                $this->_options['order'][$table . '.' . $o[0]] = $o[1];
            } else {
                $this->_options['order'][$o[0]] = $o[1];
            }
        }
        return $this;
    }

    /**
     * having
     * @access protected
     * @param mixed $having
     * @param string | null $table
     * @return self
     */
    public function having($having, $table = null)
    {
        if (!is_string($having)) {
            return $this;
        }
        if (!isset($this->_options['having'])) {
            $this->_options['having'] = '';
        }
        if ($this->_options['having'] != '') {
            $this->_options['having'] .= ',';
        }
        if ($table) {
            $this->_options['having'] .= $this->$having($table) . '.' . $having;
        } else {
            $this->_options['having'] .= $having;
        }
        return $this;
    }

    /**
     * 指定查询数量
     * @access protected
     * @param mixed $offset 起始位置
     * @param mixed $length 查询数量
     * @return self
     */
    public function limit($offset, $length = null)
    {
        if (is_null($length) && strpos($offset, ',')) {
            list($offset, $length) = explode(',', $offset);
        }
        $this->_options['limit'] = ($length ? intval($length) . ' OFFSET ' : '') . intval($offset);
        return $this;
    }

    /**
     * 统计设定
     * @param $statTimeRange
     * @param $timeField
     * @param string $groupBy
     * @return self
     */
    public function statRange($statTimeRange, $timeField, $groupBy = null)
    {
        if (!$timeField) {
            return $this;
        }
        if ($groupBy) {
            $groupBy = (array)$groupBy;
            foreach ($groupBy as $k => $v) {
                $this->field($v);
                $this->groupBy($v);
            }
        }
        $this->field("COUNT(0) AS 'qty'");
        switch ($statTimeRange) {
            case 'Y':
                $this->field("SUBSTR({$timeField}, 1, 4) AS 't'");
                $this->groupBy("SUBSTR({$timeField}, 1, 4)");
                break;
            case 'm':
                $this->field("SUBSTR({$timeField}, 1, 7) AS 't'");
                $this->groupBy("SUBSTR({$timeField}, 1, 7)");
                break;
            case 'd':
                $this->field("SUBSTR({$timeField}, 1, 10) AS 't'");
                $this->groupBy("SUBSTR({$timeField}, 1, 10)");
                break;
            case 'H':
                $this->field("SUBSTR({$timeField}, 1, 13) AS 't'");
                $this->groupBy("SUBSTR({$timeField}, 1, 13)");
                break;
            case 'i':
                $this->field("SUBSTR({$timeField}, 1, 16) AS 't'");
                $this->groupBy("SUBSTR({$timeField}, 1, 16)");
                break;
            case 's':
                $this->field("SUBSTR({$timeField}, 1, 19) AS 't'");
                $this->groupBy("SUBSTR({$timeField}, 1, 19)");
                break;
            default:
                break;
        }
        return $this;
    }

    ///TODO 终结操作

    /**
     * 查找记录多条
     * @access public
     * @return mixed
     */
    public function multi()
    {
        $options = $this->_parseOptions();
        $sql = $this->buildSelectSql($options);
        return $this->query($sql);
    }

    /**
     * 查找记录一条
     * @return mixed
     */
    public function one()
    {
        $this->limit(1);
        $result = $this->multi();
        return is_array($result) ? reset($result) : array();
    }

    /**
     * 当前时间（只能用于insert 和 update）
     * @return array
     */
    public function now()
    {
        return array('exp', 'now()');
    }

    /**
     * 统计
     * @param int $field
     * @return int
     */
    public function count($field = 0)
    {
        $this->field("COUNT(" . ($field === 0 ? '0' : $this->parseKey($field)) . ") AS \"hcount\"");
        $result = $this->one();
        return (int)$result['hcount'];
    }

    /**
     * 求和
     * @param string $field
     * @return int
     */
    public function sum($field)
    {
        $this->field("SUM(" . $this->parseKey($field) . ") AS \"hsum\"");
        $result = $this->one();
        return round($result['hsum'], 10);
    }

    /**
     * 求均
     * @param $field
     * @return int
     */
    public function avg($field)
    {
        $this->field("AVG(" . $this->parseKey($field) . ") AS \"havg\"");
        $result = $this->one();
        return round($result['havg'], 10);
    }

    /**
     * 求最小
     * @param $field
     * @return int
     */
    public function min($field)
    {
        $this->field("MIN(" . $this->parseKey($field) . ") AS \"hmin\"");
        $result = $this->one();
        return round($result['hmin'], 10);
    }

    /**
     * 求最大
     * @param $field
     * @return int
     */
    public function max($field)
    {
        $this->field("MAX(" . $this->parseKey($field) . ") AS \"hmax\"");
        $result = $this->one();
        return round($result['hmax'], 10);
    }

    /**
     * 分页查找
     * @param int $current
     * @param int $per
     * @return mixed
     */
    public function page($current = 0, $per = 10)
    {
        $limit = (int)$per;
        $offset = (int)($current) * $limit;
        $this->limit($offset, $limit);

        $options = $this->_parseOptions();
        $sql = $this->buildSelectSql($options);
        $options['order'] = null;
        $options['limit'] = 1;
        if (!empty($options['group'])) {
            $options['field'] = 'count(DISTINCT ' . $options['group'] . ') as "hcount"';
            $options['group'] = null;
        } else {
            $options['field'] = 'count(0) as "hcount"';
        }
        $sqlCount = $this->buildSelectSql($options);
        $data = $this->query($sql);
        $count = $this->query($sqlCount);
        $count = reset($count)['hcount'];
        $count = (int)$count;
        //
        $result = array();
        $per = !$per ? 10 : $per;
        $end = ceil($count / $per);
        $result['data'] = $data;
        $result['page'] = [];
        $result['page']['total'] = $count;
        $result['page']['current'] = (int)$current;
        $result['page']['per'] = $per;
        $result['page']['end'] = (int)$end;
        return $result;
    }

    /**
     * 插入记录
     * @access public
     * @param mixed $data 数据
     * @return integer
     * @throws Exception
     */
    public function insert($data)
    {
        $values = $fields = array();
        $table = $this->getTable();
        $ft = $this->getFieldType($table);
        foreach ($data as $key => $val) {
            if (!empty($ft[$table . '_' . $key])) { // 根据表字段过滤无效key
                if (is_array($val) && isset($val[0]) && 'exp' == $val[0]) {
                    $fields[] = $this->parseKey($key);
                    $values[] = $val[1] ?? null;
                } elseif (is_null($val)) {
                    $fields[] = $this->parseKey($key);
                    $values[] = 'NULL';
                } elseif (is_array($val) || is_scalar($val)) { // 过滤非标量数据
                    //todo 跟据表字段处理数据
                    if (is_array($val) && strpos($ft[$table . '_' . $key], 'char') !== false) { // 字符串型数组
                        $val = $this->toMyArray($val, $ft[$table . '_' . $key]);
                    } else {
                        $val = $this->parseValueByFieldType($val, $ft[$table . '_' . $key]);
                    }
                    if ($val !== null) {
                        $fields[] = $this->parseKey($key);
                        $values[] = $this->parseValue($val);
                    }
                }
            }
        }
        // 兼容数字传入方式
        $sql = 'INSERT INTO ' . $this->parseTable($table) . ' (' . implode(',', $fields) . ') VALUES (' . implode(',', $values) . ')';
        $sql .= $this->parseComment(!empty($options['comment']) ? $options['comment'] : '');
        return $this->query($sql);
    }

    /**
     * 批量插入记录
     * @access public
     * @param mixed $dataSet 数据集
     * @return false | integer
     * @throws Exception
     */
    public function insertAll($dataSet)
    {
        $values = array();
        if (!is_array($dataSet[0])) return false;
        $fields = array_map(array($this, 'parseKey'), array_keys($dataSet[0]));
        $table = $this->getTable();
        $ft = $this->getFieldType($table);
        foreach ($dataSet as $data) {
            $value = array();
            foreach ($data as $key => $val) {
                if (!empty($ft[$table . '_' . $key])) { // 根据表字段过滤无效key
                    if (is_array($val) && isset($val[0]) && 'exp' == $val[0]) {
                        $value[] = $val[1];
                    } elseif (is_null($val)) {
                        $value[] = 'NULL';
                    } elseif (is_array($val) || is_scalar($val)) { // 过滤非标量数据
                        //todo 跟据表字段处理数据
                        if (is_array($val) && strpos($ft[$table . '_' . $key], 'char') !== false) { // 字符串型数组
                            $val = $this->toMyArray($val, $ft[$table . '_' . $key]);
                            if ($val === null) $value[] = 'NULL';
                        } else {
                            $val = $this->parseValueByFieldType($val, $ft[$table . '_' . $key]);
                        }
                        if ($val !== null) {
                            $value[] = $this->parseValue($val);
                        }
                    }
                }
            }
            $values[] = '(' . implode(',', $value) . ')';
        }
        $sql = 'INSERT INTO ' . $this->parseTable($table) . ' (' . implode(',', $fields) . ') VALUES ' . implode(' , ', $values);
        $sql .= $this->parseComment(!empty($options['comment']) ? $options['comment'] : '');
        return $this->query($sql);
    }

    /**
     * 更新记录
     * @access public
     * @param mixed $data 数据
     * @param bool $sure
     * @return false | integer
     * @throws Exception
     */
    public function update($data, $sure = false)
    {
        $table = $this->getTable();
        $sql = 'UPDATE ' . $this->parseTable($table);
        $ft = $this->getFieldType($table);
        $set = array();
        foreach ($data as $key => $val) {
            if (!empty($ft[$table . '_' . $key])) { // 根据表字段过滤无效key
                if (is_array($val) && !empty($val[0]) && 'exp' == $val[0]) {
                    $set[] = $this->parseKey($key) . '=' . $val[1];
                } elseif (is_null($val)) {
                    $set[] = $this->parseKey($key) . '= NULL';
                } elseif (is_array($val) || is_scalar($val)) { // 过滤非标量数据
                    //todo 跟据表字段处理数据
                    if (is_array($val) && strpos($ft[$table . '_' . $key], 'char') !== false) { // 字符串型数组
                        $val = $this->toMyArray($val, $ft[$table . '_' . $key]);
                    } else {
                        $val = $this->parseValueByFieldType($val, $ft[$table . '_' . $key]);
                    }
                    if ($val !== null) {
                        $set[] = $this->parseKey($key) . '=' . $this->parseValue($val);
                    }
                }
            }
        }
        $sql .= ' SET ' . implode(',', $set);
        if (strpos($table, ',')) {// 多表更新支持JOIN操作
            $sql .= $this->parseJoin(!empty($this->_options['join']) ? $this->_options['join'] : '');
        }
        $where = $this->parseWhere(!empty($this->_options['where']) ? $this->_options['where'] : '');
        if (!$where && $sure !== true) {
            throw new \Exception('update must be sure when without where：' . $sql);
        }
        $sql .= $where;
        if (!strpos($table, ',')) {
            //  单表更新支持order和limit
            $sql .= $this->parseOrder(!empty($this->_options['order']) ? $this->_options['order'] : '')
                . $this->parseLimit(!empty($this->_options['limit']) ? $this->_options['limit'] : '');
        }
        $sql .= $this->parseComment(!empty($this->_options['comment']) ? $this->_options['comment'] : '');
        return $this->query($sql);
    }

    /**
     * 删除记录
     * @access public
     * @param bool $sure
     * @return false | integer
     * @throws Exception
     */
    public function delete($sure = false)
    {
        $table = $this->parseTable($this->_options['table']);
        $sql = 'DELETE FROM ' . $table;
        if (strpos($table, ',')) {// 多表删除支持USING和JOIN操作
            if (!empty($this->_options['using'])) {
                $sql .= ' USING ' . $this->parseTable($this->_options['using']) . ' ';
            }
            $sql .= $this->parseJoin(!empty($this->_options['join']) ? $this->_options['join'] : '');
        }
        $where = $this->parseWhere(!empty($this->_options['where']) ? $this->_options['where'] : '');
        if (!$where && $sure !== true) {
            throw new \Exception('delete must be sure when without where');
        }
        $sql .= $where;
        if (!strpos($table, ',')) {
            // 单表删除支持order和limit
            $sql .= $this->parseOrder(!empty($this->_options['order']) ? $this->_options['order'] : '')
                . $this->parseLimit(!empty($this->_options['limit']) ? $this->_options['limit'] : '');
        }
        $sql .= $this->parseComment(!empty($this->_options['comment']) ? $this->_options['comment'] : '');
        return $this->query($sql);
    }

    /**
     * 截断表
     * @alert 必须注意，这个方法一经执行会“清空”原来的“所有数据”及“自增量”
     * @param bool $sure 确认执行，防止误操作
     * @return self
     * @throws Exception
     */
    public function truncate($sure = false)
    {
        if ($this->getTable() && $sure === true) {
            $sqlStr = "TRUNCATE TABLE " . $this->parseTable($this->getTable());
            return $this->query($sqlStr);
        }
        return $this;
    }

    /**
     * 国际化表数据处理
     * @param string $response 字符串
     * @param string $language 语言
     * @return string
     */
    public function responseTranslate($language, $response)
    {
        if (!$response || !$language) return $response;
        try {
            $result = $this->table('system_tips_i18n')->query("SELECT column_name FROM information_schema.COLUMNS WHERE table_schema='{$this->settings['dbname']}' and table_name='system_tips_i18n'");
            if (!$result) return $response;
            $translate = array_column($result, 'column_name');
            if ($translate && in_array($language, $translate)) {
                $model = $this->table('system_tips_i18n');
                $lang = $model->field($language)->equalTo('default', $response)->one();
                if (!$lang) {
                    $model->insert(array('default' => $response));
                    $lang = $model->field($language)->equalTo('default', $response)->one();
                }
                $response = $lang['system_tips_i18n_' . $language] ?? $response;
            }
        } catch (\Exception $e) {
            //exit($e->getMessage());
        }
        return $response;
    }

}
