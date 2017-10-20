<?php
// +----------------------------------------------------------------------
// | AppHandler.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Thrift\Services;

use Xin\Thrift\Register\RegisterIf;

class RegisterHandler extends Handler implements RegisterIf
{
    public $services = [];

    public function version()
    {
        return $this->config->version;
    }

    public function onWorkerStart()
    {
        echo 'onWorkerStart' . PHP_EOL;
    }

    public function onWorkerStop()
    {
        echo 'onWorkerStop';
    }
}