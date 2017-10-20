<?php

namespace App\Tasks\Thrift;

use App\Core\Cli\Task\Socket;
use App\Thrift\Services\AppHandler;
use App\Thrift\Services\RegisterHandler;
use Xin\Thrift\MicroService\AppProcessor;
use swoole_server;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\TMultiplexedProcessor;
use Thrift\Transport\TMemoryBuffer;
use Xin\Thrift\Register\RegisterProcessor;

/**
 * Class RegisterTask
 * @package App\Tasks\Thrift
 * @property RegisterHandler $handle
 */
class RegisterTask extends Socket
{

    protected $config = [
        'pid_file' => ROOT_PATH . '/register.pid',
        'daemonize' => false,
        // 'worker_num' => 4, // cpu核数1-4倍比较合理 不写则为cpu核数
        'max_request' => 5, // 每个worker进程最大处理请求次数
    ];

    protected $port = 11521;

    /** @var RegisterHandler */
    protected $handler;

    protected $processor;

    protected function events()
    {
        return [
            'receive' => [$this, 'receive'],
            'WorkerStart' => [$this, 'onWorkerStart'],
            'WorkerStop' => [$this, 'onWorkerStop'],
        ];
    }

    public function onWorkerStop(swoole_server $serv, $workerId)
    {
        $this->handler->onWorkerStop();
    }

    public function onWorkerStart(swoole_server $serv, $workerId)
    {
        // dump(get_included_files()); // 查看不能被平滑重启的文件

        $this->processor = new TMultiplexedProcessor();
        $this->handler = new RegisterHandler();
        $this->handler->onWorkerStart();
        $this->processor->registerProcessor('register', new RegisterProcessor($this->handler));
    }

    public function receive(swoole_server $server, $fd, $reactor_id, $data)
    {
        $transport = new TMemoryBuffer($data);
        $protocol = new TBinaryProtocol($transport);
        $transport->open();
        $this->processor->process($protocol, $protocol);
        $server->send($fd, $transport->getBuffer());
        $transport->close();
    }
}

