<?php
require "hPhp.php";

use library\IO;

/**
 * TODO *启动
 * php ?.php -p [:port]
 */
class Main extends IO
{

    private $server = null;

    public function __construct($port)
    {

        $this->server = new swoole_server("0.0.0.0", $port);

        $this->server->set(array(
            'worker_num' => 10,
            'task_worker_num' => 4, //task数量
            'heartbeat_check_interval' => 10,
            'heartbeat_idle_time' => 180,
        ));

        $this->server->on("start", function () {
            echo "server start" . PHP_EOL;
        });

        $this->server->on("workerStart", function ($worker) {
            echo "worker start" . PHP_EOL;
        });

        $this->server->on('connect', function ($server, $fd) {
            echo "connection open: {$fd}\n";
        });
        $this->server->on('receive', function ($server, $fd, $reactor_id, $data) {
            $request = $data;
            /**
             * 处理数据
             */
            $this->server->task($request, -1, function($server, $task_id, $result) use ($fd) {
                if ($result !== false) {
                    $server->send($fd, $result);
                    return;
                }
            });
        });
        $this->server->on('close', function ($server, $fd) {
            echo "connection close: {$fd}\n";
        });

        $this->server->on('task', function ($server, $task_id, $from_id, $request) {
            $data = $this->io($request);
            $this->server->finish($data);
        });

        $this->server->on('finish', function ($server, $data) {
            echo "AsyncTask Finish" . PHP_EOL;
        });

        $this->server->on('close', function ($server, $fd) {
            echo "connection close: {$fd}\n";
        });

        $this->server->start();
    }
}

$params = getopt('p:');
if (!isset($params['p'])) {
    exit('-p set port!');
}
new Main($params['p']);
