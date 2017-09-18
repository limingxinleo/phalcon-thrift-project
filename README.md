# phalcon-project
[![Total Downloads](https://poser.pugx.org/limingxinleo/phalcon-project/downloads)](https://packagist.org/packages/limingxinleo/phalcon-project)
[![Latest Stable Version](https://poser.pugx.org/limingxinleo/phalcon-project/v/stable)](https://packagist.org/packages/limingxinleo/phalcon-project)
[![Latest Unstable Version](https://poser.pugx.org/limingxinleo/phalcon-project/v/unstable)](https://packagist.org/packages/limingxinleo/phalcon-project)
[![License](https://poser.pugx.org/limingxinleo/phalcon-project/license)](https://packagist.org/packages/limingxinleo/phalcon-project)


[Phalcon 官网](https://docs.phalconphp.com/zh/latest/index.html)

[wiki](https://github.com/limingxinleo/simple-subcontrollers.phalcon/wiki)

## 安装
* 使用Composer安装Thrift扩展后，把go的扩展包拷贝到GOPATH中(或建立软连接)。
~~~
ln -s  /your/path/to/thrift-go-phalcon-project/vendor/apache/thrift/lib/go/thrift thrift
~~~
* 编译Go服务 使用 thrift -r --gen go:thrift_import=thrift App.thrift
* 编译Php服务 使用 thrift -r --gen php:server App.thrift

## Go&Swoole RPC 服务
* Go
thrift/gen-go/main.go
~~~
# RPC服务注册方法
server.RegisterProcessor("app", service.NewAppProcessor(&impl.App{}));
~~~

* Swoole
app/tasks/Thrift/Service.php
~~~php
$handler = new AppHandler();
$processor->registerProcessor('app', new AppProcessor($handler));
~~~



