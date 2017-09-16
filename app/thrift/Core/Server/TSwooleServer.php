<?php
// +----------------------------------------------------------------------
// | TSwooleServer.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Thrift\Core\Server;

use SebastianBergmann\CodeCoverage\Report\PHP;
use Thrift\Server\TServer;
use swoole_server;

class TSwooleServer extends TServer
{
    public $port;

    public function onWorkerStart(swoole_server $server, $worker_id)
    {
        echo 'Swoole Thrift Service Start' . PHP_EOL;
    }

    public function onReceive(swoole_server $server, $fd, $reactor_id, $data)
    {
        echo 'Receive' . PHP_EOL;
        dump($data);
    }

    public function serve()
    {
        $serv = new swoole_server('0.0.0.0', $this->port);
        $serv->on('workerStart', [$this, 'onWorkerStart']);
        $serv->on('receive', [$this, 'onReceive']);
        $serv->set(array(
            'worker_num' => 1,
            'dispatch_mode' => 1, //1: 轮循, 3: 争抢
            'open_length_check' => true, //打开包长检测
            'package_max_length' => 8192000, //最大的请求包长度,8M
            'package_length_type' => 'N', //长度的类型，参见PHP的pack函数
            'package_length_offset' => 0,   //第N个字节是包长度的值
            'package_body_offset' => 4,   //从第几个字节计算长度
        ));
        $serv->start();
    }

    public function stop()
    {
        // TODO: Implement stop() method.
    }

}