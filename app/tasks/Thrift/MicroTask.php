<?php

namespace App\Tasks\Thrift;

use App\Tasks\System\Socket;
use swoole_server;

class MicroTask extends Socket
{
    protected $port = 10086;

    protected function events()
    {
        return [
            'receive' => [$this, 'receive']
        ];
    }

    public function receive(swoole_server $server, $fd, $reactor_id, $data)
    {
        echo $data . PHP_EOL;
    }
}

