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
        $this->server = new swoole_http_server("0.0.0.0", $port);

        $this->server->set(array(
            'worker_num' => 10,
            'task_worker_num' => 4, //task数量
        ));

        $this->server->on("start", function () {
            echo "server start" . PHP_EOL;
        });

        $this->server->on("workerStart", function ($worker) {
            echo "worker start" . PHP_EOL;
        });

        $this->server->on("request", function ($request, $response) {
            if (!$request->post['post']) {
                $request->post = $request->rawContent();
            } else {
                $request->post = $request->post['post'];
            }
            $request = get_object_vars($request);
            $this->server->task($request, -1, function ($server, $task_id, $result) use ($response) {
                if ($result !== false) {
                    $response->end($result);
                    return;
                } else {
                    $response->status(404);
                    $response->end();
                }
            });
        });

        $this->server->on('task', function ($server, $task_id, $from_id, $request) {
            $data = $this->io($request);
            $this->server->finish($data);
        });

        $this->server->on('finish', function ($server, $task_id, $data) {
            echo "AsyncTask Finish" . PHP_EOL;
        });

        $this->server->start();
    }
}

$params = getopt('p:c:');
if (!isset($params['p'])) {
    exit('-p set port!');
}
new Main($params['p']);
