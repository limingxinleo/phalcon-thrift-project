<?php

namespace App\Tasks\Thrift;

use App\Core\Cli\Task\Socket;
use App\Thrift\Clients\RegisterClient;
use App\Thrift\Services\AppHandler;
use App\Utils\Redis;
use App\Utils\Register\Sign;
use limx\Support\Str;
use Phalcon\Logger\AdapterInterface;
use Xin\Phalcon\Logger\Sys;
use Xin\Thrift\MicroService\AppProcessor;
use swoole_server;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\TMultiplexedProcessor;
use Thrift\Transport\TMemoryBuffer;
use Xin\Thrift\Register\ServiceInfo;
use swoole_process;

class ServiceTask extends Socket
{

    protected $config = [
        'pid_file' => ROOT_PATH . '/service.pid',
        'daemonize' => false,
        // 'worker_num' => 4, // cpu核数1-4倍比较合理 不写则为cpu核数
        'max_request' => 500, // 每个worker进程最大处理请求次数
    ];

    protected $port = 10086;

    protected $host = '127.0.0.1';

    protected $processor;

    protected function events()
    {
        return [
            'receive' => [$this, 'receive'],
            'WorkerStart' => [$this, 'workerStart'],
        ];
    }

    /**
     * @desc   服务注册
     * @author limx
     * @param swoole_server $server
     * @param               $name
     */
    protected function registryHeartbeat(swoole_server $server, $name)
    {
        $worker = new swoole_process(function (swoole_process $worker) use ($name) {
            $client = RegisterClient::getInstance([
                'host' => env('REGISTER_CENTER_HOST'),
                'port' => env('REGISTER_CENTER_PORT')
            ]);
            /** @var AdapterInterface $logger */
            $logger = di('logger')->getLogger('heart', Sys::LOG_ADAPTER_FILE, ['dir' => 'system']);
            swoole_timer_tick(5000, function () use ($client, $logger, $name) {
                $service = new ServiceInfo();
                $service->name = $name;
                $service->host = $this->host;
                $service->port = $this->port;
                $service->nonce = Str::random(16);
                $service->isService = true;
                $service->sign = Sign::sign(Sign::serviceInfoToArray($service));

                $result = $client->heartbeat($service);

                if ($result->success === false) {
                    $logger->error($result->message);
                } else {
                    foreach ($result->services as $key => $item) {
                        $serviceJson = json_encode(Sign::serviceInfoToArray($item));
                        $logger->info($serviceJson);
                        Redis::hset($name, $key, $serviceJson);
                    }
                }
            });
        });

        $server->addProcess($worker);
    }

    protected function beforeServerStart(swoole_server $server)
    {
        parent::beforeServerStart($server);
        if (env('REGISTER_CENTER_OPEN', false)) {
            $this->registryHeartbeat($server, 'app');
        }
    }


    public function workerStart(swoole_server $serv, $workerId)
    {
        // dump(get_included_files()); // 查看不能被平滑重启的文件

        $this->processor = new TMultiplexedProcessor();
        $handler = new AppHandler();
        $this->processor->registerProcessor('app', new AppProcessor($handler));
    }

    public function receive(swoole_server $server, $fd, $reactor_id, $data)
    {
        $transport = new TMemoryBuffer($data);
        $protocol = new TBinaryProtocol($transport);
        $transport->open();
        $this->processor->process($protocol, $protocol);
        $server->send($fd, $transport->getBuffer());
        $transport->close();
    }
}

