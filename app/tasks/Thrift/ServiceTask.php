<?php

namespace App\Tasks\Thrift;

use App\Core\Cli\Task\Socket;
use App\Thrift\Clients\RegisterClient;
use App\Thrift\Services\AppHandler;
use App\Utils\Redis;
use App\Utils\Register\Sign;
use Phalcon\Logger\AdapterInterface;
use Phalcon\Text;
use Xin\Phalcon\Cli\Traits\Input;
use Xin\Phalcon\Logger\Sys;
use Xin\Thrift\MicroService\AppProcessor;
use swoole_server;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\TMultiplexedProcessor;
use Thrift\Transport\TMemoryBuffer;
use Xin\Thrift\Register\ServiceInfo;
use swoole_process;

class ServiceTask extends Socket
{
    use Input;

    protected $port = 10086;

    protected $host = '127.0.0.1';

    protected $processor;

    public function onConstruct()
    {
        $this->port = $this->config->thrift->service->port;
    }

    protected function events()
    {
        return [
            'receive' => [$this, 'receive'],
            'WorkerStart' => [$this, 'workerStart'],
        ];
    }

    protected function beforeServerStart(swoole_server $server)
    {
        parent::beforeServerStart($server);
        $config = $this->getConfig();
        if ($this->option('daemonize')) {
            $config['daemonize'] = true;
        }

        // 重置参数
        $server->set($config);
    }


    public function workerStart(swoole_server $serv, $workerId)
    {
        // dump(get_included_files()); // 查看不能被平滑重启的文件

        $this->processor = new TMultiplexedProcessor();
        $handler = new AppHandler();
        $this->processor->registerProcessor('app', new AppProcessor($handler));
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

    protected function getConfig()
    {
        $config = $this->config->thrift->service->config;
        return $config->toArray();
    }
}

