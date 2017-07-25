<?php
// +----------------------------------------------------------------------
// | Thrift.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App;

use Thrift\ClassLoader\ThriftClassLoader;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TPhpStream;
use Thrift\Transport\TBufferedTransport;

use Thrift\Transport\TSocket;
use Thrift\Transport\THttpClient;
use Thrift\Exception\TException;

class Thrift
{
    protected $client = [];

    public $host;

    public $port;

    public function __construct($namespaces, $host = '127.0.0.1', $port = '80')
    {
        $gen_dir = ROOT_PATH . '/thrift/gen-php';
        $loader = new ThriftClassLoader();
        foreach ($namespaces as $namespace) {
            $loader->registerDefinition($namespace, $gen_dir);
        }
        $loader->register();

        $this->host = $host;

        $this->port = $port;
    }

    public function handle($handlerClass, $processorClass)
    {
        $processor = new $processorClass(new $handlerClass);
        header('Content-Type', 'application/x-thrift');

        $transport = new TBufferedTransport(new TPhpStream(TPhpStream::MODE_R | TPhpStream::MODE_W));
        $protocol = new TBinaryProtocol($transport, true, true);

        $transport->open();
        $processor->process($protocol, $protocol);
        $transport->close();
        return true;
    }

    public function client($uri = '', $scheme = 'http')
    {
        return new THttpClient($this->host, $this->port, $uri, $scheme);
    }

    public function socket($host = 'localhost', $port = 9090, $persist = false, $debugHandler = null)
    {
        return new TSocket($host, $port, $persist, $debugHandler);
    }
}