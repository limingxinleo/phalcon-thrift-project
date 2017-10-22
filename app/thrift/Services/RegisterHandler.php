<?php
// +----------------------------------------------------------------------
// | AppHandler.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Thrift\Services;

use App\Core\Register\RegisterInput;
use App\Core\Register\Persistent\Redis;
use Xin\Thrift\Register\RegisterIf;
use Xin\Thrift\Register\ServiceInfo;
use Xin\Thrift\Register\Response;

class RegisterHandler extends Handler implements RegisterIf
{
    public $services = [];

    public $persistentKey = 'phalcon:register:service:persistent';

    public function version()
    {
        return $this->config->version;
    }

    /**
     * @desc   服务心跳
     * @author limx
     * @param ServiceInfo $serviceInfo
     * @return Response
     */
    public function heartbeat(ServiceInfo $serviceInfo)
    {
        $success = true;
        $message = '';

        try {
            $service = new RegisterInput($serviceInfo);
            // 把元素加入到services表相应服务首位
            if ($service->input->isService) {
                $key = $service->input->name;
                $this->services[$key] = $service->input;
            }
        } catch (\Exception $ex) {
            $success = false;
            $message = $ex->getMessage();
        }

        $response = new Response();
        $response->success = $success;
        $response->message = $message;
        $response->services = $this->services;

        return $response;
    }


    public function onWorkerStart()
    {
        $isPersistent = di('config')->thrift->register->persistent;
        if ($isPersistent) {
            $client = Redis::getInstance();
            $services = $client->get($this->persistentKey);
            if ($services = unserialize($services)) {
                $this->services = $services;
            }
        }

    }

    public function onWorkerStop()
    {
        $isPersistent = di('config')->thrift->register->persistent;
        if ($isPersistent) {
            $client = Redis::getInstance();
            $client->set($this->persistentKey, serialize($this->services));
        }
    }
}