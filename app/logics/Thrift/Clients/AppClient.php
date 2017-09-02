<?php

namespace App\Logics\Thrift\Clients;

use App\Logics\Thrift\Client;

class AppClient extends Client
{
    protected $host = '127.0.0.1';

    protected $port = '10086';

    protected $service = 'app';

    protected $clientName = \MicroService\AppClient::class;

}

