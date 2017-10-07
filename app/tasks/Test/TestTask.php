<?php

namespace App\Tasks\Test;

use App\Thrift\Clients\AppClient;
use Xin\Cli\Color;
use swoole_process;

class TestTask extends \Phalcon\Cli\Task
{
    public function mainAction()
    {
        echo Color::head('Help:') . PHP_EOL;
        echo Color::colorize('  Thrift 通信测试脚本') . PHP_EOL . PHP_EOL;

        echo Color::head('Usage:') . PHP_EOL;
        echo Color::colorize('  php run test:test@[action]', Color::FG_GREEN) . PHP_EOL . PHP_EOL;

        echo Color::head('Actions:') . PHP_EOL;
        echo Color::colorize('  version                         返回版本号', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  client                          Client单例测试', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  high                            高并发测试', Color::FG_GREEN) . PHP_EOL;
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
        $client = AppClient::getInstance();
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
        $client = AppClient::getInstance();

        dump($client->version());
    }

    /**
     * @desc   单例测试
     * @author limx
     */
    public function clientAction()
    {
        $client = AppClient::getInstance();
        $client = AppClient::getInstance();

        echo Color::colorize($client->version(), Color::FG_GREEN) . PHP_EOL;

        $client = AppClient::getInstance();

        echo Color::colorize($client->version(), Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize("实例个数：" . count(AppClient::$_instance), Color::FG_GREEN) . PHP_EOL;
    }

}

