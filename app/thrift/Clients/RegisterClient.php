<?php

namespace App\Thrift\Clients;

use App\Thrift\Client;
use Xin\Thrift\Register\RegisterClient as RegisterServiceClient;

class RegisterClient extends Client
{
    protected $host = '127.0.0.1';

    protected $port = '11521';

    protected $service = 'register';

    protected $clientName = RegisterServiceClient::class;

    protected $recvTimeoutMilliseconds = 10;

    protected $sendTimeoutMilliseconds;

    /**
     * @desc
     * @author limx
     * @param array $config
     * @return RegisterServiceClient $client
     */
    public static function getInstance($config = [])
    {
        return parent::getInstance($config);
    }


}

