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
    public function __construct($namespaces)
    {
        $gen_dir = ROOT_PATH . '/thrift/gen-php';
        $loader = new ThriftClassLoader();
        foreach ($namespaces as $namespace) {
            $loader->registerDefinition($namespace, $gen_dir);
        }
        $loader->register();
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

    public function client($host, $port, $uri = '', $scheme = 'http')
    {
        return new THttpClient($host, $port, $uri, $scheme);
    }

    public function socket($host, $port, $persist = false, $debugHandler = null)
    {
        return new TSocket($host, $port, $persist, $debugHandler);
    }
}