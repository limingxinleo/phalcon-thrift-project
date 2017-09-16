<?php

namespace App\Tasks\Thrift;

use App\Tasks\System\Socket;
use App\Thrift\Services\AppHandler;
use App\Thrift\Core\Transport\SwooleSocket;
use MicroService\AppProcessor;
use swoole_server;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\TMultiplexedProcessor;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TPhpStream;
use Thrift\Transport\TSocket;

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
        // dd(strlen($data));
        // echo $data . PHP_EOL;
        $handler = new AppHandler();
        $processor = new TMultiplexedProcessor();
        $processor->registerProcessor('app', new AppProcessor($handler));

        $transport = new SwooleSocket(null, 1024, 1024);
        $transport->setHandle($fd);
        $transport->buffer = $data;
        $transport->server = $server;


        // $transport = new TBufferedTransport($socket);
        $protocol = new TBinaryProtocol($transport);

        $transport->open();
        $processor->process($protocol, $protocol);
        $transport->close();

    }
}

