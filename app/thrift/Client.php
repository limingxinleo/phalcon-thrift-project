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

    protected $recvTimeoutMilliseconds;

    protected $sendTimeoutMilliseconds;

    private function __construct($className, $config = [])
    {
        if (isset($config['host'])) {
            $this->host = $config['host'];
        }

        if (isset($config['port'])) {
            $this->port = $config['port'];
        }

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

        $key = $this->host . ':' . $this->port;
        if (empty(static::$_protocol[$key]) || !(static::$_protocol[$key] instanceof TBinaryProtocol)) {

            $socket = new TSocket($this->host, $this->port, $this->persist, $this->debugHandler);

            if (isset($this->recvTimeoutMilliseconds)) {
                $socket->setRecvTimeout($this->recvTimeoutMilliseconds);
            }

            if (isset($this->sendTimeoutMilliseconds)) {
                $socket->setSendTimeout($this->sendTimeoutMilliseconds);
            }

            $transport = new TBufferedTransport($socket, $this->rBufSize, $this->wBufSize);
            $protocol = new TBinaryProtocol($transport);

            static::$_protocol[$key] = $protocol;

            $transport->open();
        }

        $protocol = new TMultiplexedProtocol(static::$_protocol[$key], $this->service);

        $class = $this->clientName;
        $this->client = new $class($protocol);

    }

    public static function getInstance($config = [])
    {
        $class = get_called_class();
        if (isset(static::$_instance[$class]) && static::$_instance[$class] instanceof ClientInterface) {
            return static::$_instance[$class];
        }
        return static::$_instance[$class] = new static($class, $config);
    }

    public function flush()
    {
        $class = get_called_class();
        static::$_instance[$class] = null;
        return true;
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

