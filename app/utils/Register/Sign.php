<?php
// +----------------------------------------------------------------------
// | Sign.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Utils\Register;

use Xin\Thrift\Register\ServiceInfo;

class Sign
{

    public static function sign($input = [])
    {
        unset($input['sign']);
        ksort($input);
        $data = http_build_query($input);
        $key = env('REGISTER_CENTER_KEY', 'helloworld');

        return md5(md5($data) . $key);
    }

    public static function verify($input, $sign)
    {
        if (env('REGISTER_CENTER_SIGN_VERIFY', false)) {

            unset($input['sign']);
            return static::sign($input) === $sign;
        }

        return true;
    }

    public static function serviceInfoToArray(ServiceInfo $serviceInfo)
    {
        return [
            'name' => $serviceInfo->name,
            'host' => $serviceInfo->host,
            'port' => $serviceInfo->port,
            'nonce' => $serviceInfo->nonce,
            'sign' => $serviceInfo->sign,
            'isService' => $serviceInfo->isService,
        ];
    }
}