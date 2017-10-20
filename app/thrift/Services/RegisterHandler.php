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
use Xin\Thrift\Register\RegisterIf;
use Xin\Thrift\Register\ServiceInfo;
use Xin\Thrift\Register\Response;

class RegisterHandler extends Handler implements RegisterIf
{
    public $services = [];

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
        echo 'onWorkerStart' . PHP_EOL;
    }

    public function onWorkerStop()
    {
        echo 'onWorkerStop';
    }
}