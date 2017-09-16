<?php
// +----------------------------------------------------------------------
// | System.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------

namespace App\Thrift;

use Phalcon\Di\Injectable;

class App extends Injectable implements \MicroService\AppIf
{
    public function version()
    {
        return $this->config->version;
    }
}