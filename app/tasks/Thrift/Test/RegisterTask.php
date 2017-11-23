<?php

namespace App\Tasks\Thrift\Test;

use App\Thrift\Clients\RegisterClient;
use App\Utils\Register\Sign;
use Xin\Cli\Color;
use swoole_process;
use Xin\Thrift\Register\ServiceInfo;

class RegisterTask extends \Phalcon\Cli\Task
{
    public function mainAction()
    {
        echo Color::head('Help:') . PHP_EOL;
        echo Color::colorize('  Thrift Register通信测试脚本') . PHP_EOL . PHP_EOL;

        echo Color::head('Usage:') . PHP_EOL;
        echo Color::colorize('  php run test:register@[action]', Color::FG_GREEN) . PHP_EOL . PHP_EOL;

        echo Color::head('Actions:') . PHP_EOL;
        echo Color::colorize('  version                         返回版本号', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  client                          Client单例测试', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  high                            高并发测试', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  heartbeat                       心跳测试', Color::FG_GREEN) . PHP_EOL;

    }

    public function heartbeatAction()
    {
        $client = RegisterClient::getInstance();
        $service = new ServiceInfo();
        $service->name = 'app';
        $service->host = '127.0.0.1';
        $service->port = 10086;
        $service->nonce = 'xxx';
        $service->isService = true;
        $service->sign = Sign::sign(Sign::serviceInfoToArray($service));
        for ($i = 0; $i < 12; $i++) {
            $res = $client->heartbeat($service);
            if ($res->success) {
                if (empty($res->services)) {
                    echo Color::error("服务列表为空") . PHP_EOL;
                }
                foreach ($res->services as $key => $service) {
                    echo Color::colorize("服务{$key}:" . $service->name, Color::FG_GREEN) . PHP_EOL;
                }
            } else {
                echo Color::error($res->message) . PHP_EOL;
            }
        }
    }

    public function highAction($params = [])
    {
        $tasks = 10;
        if (isset($params[0]) && is_numeric($params[0])) {
            $tasks = intval($params[0]);
        }

        $time = microtime(true);
        for ($i = 0; $i < $tasks; $i++) {
            $process = new swoole_process([$this, 'highClient']);
            $pid = $process->start();
            echo Color::colorize("PID=" . $pid, Color::FG_RED) . PHP_EOL;
        }
        swoole_process::wait();
        echo Color::colorize("用时：" . (microtime(true) - $time), Color::FG_GREEN) . PHP_EOL;
    }

    public function highClient()
    {
        $client = RegisterClient::getInstance();
        for ($i = 0; $i < 10000; $i++) {
            $client->version();
            // echo $client->version() . PHP_EOL;
        }
    }

    /**
     * @desc   go服务调用
     * @author limx
     */
    public function versionAction()
    {
        $client = RegisterClient::getInstance();

        dump($client->version());
    }

    /**
     * @desc   单例测试
     * @author limx
     */
    public function clientAction()
    {
        $client = RegisterClient::getInstance();
        $client = RegisterClient::getInstance();

        echo Color::colorize($client->version(), Color::FG_GREEN) . PHP_EOL;

        $client = RegisterClient::getInstance();

        echo Color::colorize($client->version(), Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize("实例个数：" . count(RegisterClient::$_instance), Color::FG_GREEN) . PHP_EOL;
    }

}

