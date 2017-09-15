<?php
// +----------------------------------------------------------------------
// | Thrift.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Core\Services;

use Phalcon\Config;
use Phalcon\DI\FactoryDefault;

class Thrift implements ServiceProviderInterface
{
    public function register(FactoryDefault $di, Config $config)
    {
        $di->setShared('thrift', function () use ($config) {
            $namespaces = $config->thrift->namespaces;
            $host = $config->thrift->host;
            $port = $config->thrift->port;
            return new \App\Thrift($namespaces, $host, $port);
        });

    }
}