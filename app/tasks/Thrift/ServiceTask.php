<?php

namespace App\Tasks\Thrift;

use App\Tasks\System\Socket;
use App\Thrift\Services\AppHandler;
use MicroService\AppProcessor;
use swoole_server;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\TMultiplexedProcessor;
use Thrift\Transport\TMemoryBuffer;

class ServiceTask extends Socket
{
    protected $thrift;

    protected $port = 10086;

    public function onConstruct()
    {
        $this->thrift = di('thrift');
    }

    protected function events()
    {
        return [
            'receive' => [$this, 'receive']
        ];
    }

    public function receive(swoole_server $server, $fd, $reactor_id, $data)
    {
        $processor = new TMultiplexedProcessor();

        $handler = new AppHandler();
        $processor->registerProcessor('app', new AppProcessor($handler));

        $transport = new TMemoryBuffer($data);
        $protocol = new TBinaryProtocol($transport);
        $transport->open();
        $processor->process($protocol, $protocol);
        $server->send($fd, $transport->getBuffer());
        $transport->close();
    }
}

