<?php
namespace library;

class Redis
{
    const TYPE_JSON = 'json';
    const TYPE_STR = 'str';
    const TYPE_NUM = 'num';

    const Conf = CONFIG['redis'];

    /**
     * @var \Redis | null
     *
     */
    private $redis = null;

    /**
     * 架构函数 取得模板对象实例
     * @access public
     * @throws \Exception
     * @return self
     */
    public function __construct()
    {
        if (self::Conf['active'] === true && $this->redis == null) {
            if (class_exists('\\Redis')) {
                try {
                    $this->redis = new \Redis();
                    $this->redis->connect(
                        self::Conf['host'],
                        self::Conf['port']
                    );
                } catch (\Exception $e) {
                    $this->redis = null;
                    throw new \Exception('Redis 遇到问题或未安装，请Config处关闭Redis以提高性能');
                }
            }
        }
        return $this;
    }

    private function parse($key)
    {
        return CONFIG['redis']['project_name'] . $key;
    }

    /**
     * 删除kEY
     * @param $key
     */
    public function delete($key)
    {
        if ($this->redis !== null && $key) {
            $this->redis->delete($this->parse($key));
        }
    }

    /**
     * 清空
     * @param bool $sure
     */
    public function flushAll($sure = false)
    {
        if ($this->redis !== null && $sure === true) {
            $this->redis->flushAll();
        }
    }

    /**
     * @param $key
     * @param $value
     * @param int $timeout <= 0 not expire
     * @return void
     */
    public function set($key, $value, $timeout = 0)
    {
        if ($this->redis !== null && $key) {
            $key = $this->parse($key);
            if (is_array($value)) {
                $this->redis->set($key, json_encode($value));
                $this->redis->set($key . '_TO_', self::TYPE_JSON);
            } elseif (is_string($value)) {
                $this->redis->set($key, $value);
                $this->redis->set($key . '_TO_', self::TYPE_STR);
            } elseif (is_numeric($value)) {
                $this->redis->set($key, $value);
                $this->redis->set($key . '_TO_', self::TYPE_NUM);
            } else {
                $this->redis->set($key, $value);
            }
            if ($timeout > 0) {
                $this->redis->expire($key, $timeout);
            }
        }
    }

    /**
     * @param $key
     * @return bool|null|string|array
     */
    public function get($key)
    {
        if ($this->redis === null || !$key) {
            return null;
        } else {
            $key = $this->parse($key);
            $type = $this->redis->get($key . '_TO_');
            switch ($type) {
                case self::TYPE_JSON:
                    $result = json_decode($this->redis->get($key), true);
                    break;
                case self::TYPE_STR:
                    $result = $this->redis->get($key);
                    break;
                case self::TYPE_NUM:
                    $result = round($this->redis->get($key), 10);
                    break;
                default:
                    $result = $this->redis->get($key);
                    break;
            }
            return $result;
        }
    }

    /**
     * @param $table
     * @param $key
     * @param $value
     * @return void
     */
    public function hSet($table, $key, $value)
    {
        if ($this->redis !== null && $table && $key) {
            $table = $this->parse($table);
            if (is_array($value)) {
                $this->redis->hSet($table, $key, json_encode($value));
                $this->redis->hSet($table, $key . '_TO_', self::TYPE_JSON);
            } elseif (is_string($value)) {
                $this->redis->hSet($table, $key, $value);
                $this->redis->hSet($table, $key . '_TO_', self::TYPE_STR);
            } elseif (is_numeric($value)) {
                $this->redis->hSet($table, $key, $value);
                $this->redis->hSet($table, $key . '_TO_', self::TYPE_NUM);
            } else {
                $this->redis->hSet($table, $key, $value);
            }
        }
    }

    /**
     * @param $table
     * @param $key
     * @return bool|null|string|array
     */
    public function hGet($table, $key)
    {
        if ($this->redis === null || !$table || !$key) {
            return null;
        } else {
            $table = $this->parse($table);
            $type = $this->redis->hGet($table, $key . '_TO_');
            switch ($type) {
                case self::TYPE_JSON:
                    $result = json_decode($this->redis->hGet($table, $key), true);
                    break;
                case self::TYPE_STR:
                    $result = (string)$this->redis->hGet($table, $key);
                    break;
                case self::TYPE_NUM:
                    $result = round($this->redis->hGet($table, $key), 10);
                    break;
                default:
                    $result = $this->redis->hGet($table, $key);
                    break;
            }
            return $result;
        }
    }

}