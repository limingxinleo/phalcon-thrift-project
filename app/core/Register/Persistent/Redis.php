<?php
// +----------------------------------------------------------------------
// | Redis.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Core\Register\Persistent;

use Xin\Redis as Client;

class Redis
{
    public static function getInstance()
    {
        $config = di('config');

        $host = $config->redis->host;
        $port = $config->redis->port;
        $auth = $config->redis->auth;
        $db = $config->redis->index;

        return Client::getInstance($host, $auth, $db, $port, 'register');
    }

}