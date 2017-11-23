<?php

namespace App\Thrift\Clients;

use App\Thrift\Client;
use Xin\Thrift\MicroService\AppClient as AppServiceClient;

class AppClient extends Client
{
    protected $host = '127.0.0.1';

    protected $port = '10086';

    protected $service = 'app';

    protected $clientName = AppServiceClient::class;

    protected $recvTimeoutMilliseconds = 50;

    protected $sendTimeoutMilliseconds;

    /**
     * @desc
     * @author limx
     * @param array $config
     * @return AppServiceClient
     */
    public static function getInstance($config = [])
    {
        return parent::getInstance($config);
    }


}

