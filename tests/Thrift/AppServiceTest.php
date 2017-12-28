<?php
// +----------------------------------------------------------------------
// | BaseTest.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace Tests\Thrift;

use App\Thrift\Clients\AppClient;
use Tests\UnitTestCase;

/**
 * Class UnitTest
 */
class AppServiceTest extends UnitTestCase
{
    public function testVersionCase()
    {
        $version = di('config')->version;
        $client = AppClient::getInstance();
        $this->assertEquals($version, $client->version());
    }

    public function testExceptionCase()
    {
        try {
            $client = AppClient::getInstance()->testException();
        } catch (\Exception $ex) {
            $this->assertEquals(400, $ex->getCode());
            $this->assertEquals('异常测试', $ex->getMessage());
        }
    }
}