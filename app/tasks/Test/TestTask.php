<?php

namespace App\Tasks\Test;

use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TSocket;
use Thrift\Transport\THttpClient;
use Thrift\Transport\TBufferedTransport;
use Thrift\Exception\TException;

class TestTask extends \Phalcon\Cli\Task
{

    public function mainAction()
    {
        di('thrift');
        $socket = di('thrift')->client('thrift.phalcon.app', 80, '/server');
        // $socket = new TSocket('localhost', 9090);

        $transport = new TBufferedTransport($socket, 1024, 1024);
        $protocol = new TBinaryProtocol($transport);
        $client = new \ThriftService\SystemClient($protocol);

        $transport->open();

        echo $client->test(" World! ");
        echo PHP_EOL;
        echo $client->version();

        $transport->close();
    }

}

