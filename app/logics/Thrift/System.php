<?php
// +----------------------------------------------------------------------
// | System.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------

namespace App\Logics\Thrift;

use Phalcon\Di\Injectable;

class System extends Injectable implements \ThriftService\SystemIf
{
    public function version()
    {
        return $this->config->version;
    }

    public function test($name)
    {
        return "Hello " . $name;
    }

    public function count($num)
    {
        for ($i = 0; $i < 10000; $i++) {
            for ($j = 0; $j < 10000; $j++) {
                $num++;
            }
        }
        return 'finish';
    }

    public function listOutput(array $data)
    {
        return $data;
    }


}