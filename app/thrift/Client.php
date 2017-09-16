<?php

namespace App\Thrift;

use Thrift\Protocol\TBinaryProtocol;
use Thrift\Protocol\TMultiplexedProtocol;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TSocket;

abstract class Client implements ClientInterface
{
    public static $_instance = [];

    public static $_protocol = [];

    protected $host;

    protected $port;

    protected $service;

    protected $clientName;

    protected $client;

    protected $persist = false;

    protected $debugHandler = null;

    protected $rBufSize = 512;

    protected $wBufSize = 512;

    private function __construct($className)
    {
        if (!isset($this->host)) {
            throw new ClientException('Thrift Client host is required!');
        }

        if (!isset($this->port)) {
            throw new ClientException('Thrift Client port is required!');
        }

        if (!isset($this->service)) {
            throw new ClientException('Thrift Client service is required!');
        }

        if (!isset($this->clientName)) {
            throw new ClientException('Thrift Client Name is required!');
        }

        $thrift = di('thrift');

        $key = $this->host . ':' . $this->port;
        if (empty(static::$_protocol[$key]) || !(static::$_protocol[$key] instanceof TBinaryProtocol)) {

            $socket = $thrift->socket($this->host, $this->port, $this->persist, $this->debugHandler);
            $transport = new TBufferedTransport($socket, $this->rBufSize, $this->wBufSize);
            $protocol = new TBinaryProtocol($transport);

            static::$_protocol[$key] = $protocol;

            $transport->open();
        }

        $protocol = new TMultiplexedProtocol(static::$_protocol[$key], $this->service);

        $class = $this->clientName;
        $this->client = new $class($protocol);

    }

    public static function getInstance()
    {
        $class = get_called_class();
        if (isset(static::$_instance[$class]) && static::$_instance[$class] instanceof ClientInterface) {
            return static::$_instance[$class];
        }
        return static::$_instance[$class] = new static($class);
    }

    public static function __callStatic($name, $arguments)
    {
        $obj = static::getInstance();
        return $obj->client->$name(...$arguments);
    }

    public function __call($name, $arguments)
    {
        return $this->client->$name(...$arguments);
    }
}

