package main

import (
	"fmt"
	"micro/service"
	"micro/impl"
	"micro/config"
	"os"
	"thrift"
)

func init() {
	// 判断日志目录是否存在
	stat, err := os.Stat(config.LOGDIR)
	if err != nil {
		// 新建目录
		os.Mkdir(config.LOGDIR, 0755)
	} else {
		if stat.Mode() != 0755 {
			os.Chmod(config.LOGDIR, 0755)
		}
	}
}

func main() {
	//transportFactory := thrift.NewTFramedTransportFactory(thrift.NewTTransportFactory())
	transportFactory := thrift.NewTBufferedTransportFactory(1024)
	protocolFactory := thrift.NewTBinaryProtocolFactoryDefault()
	//protocolFactory := thrift.NewTCompactProtocolFactory()

	serverTransport, err := thrift.NewTServerSocket(config.NETWORK_ADDR)
	if err != nil {
		fmt.Println("Error!", err)
		os.Exit(1)
	}

	processor := thrift.NewTMultiplexedProcessor();
	processor.RegisterProcessor("app", service.NewAppProcessor(&impl.App{}));
	//processor.RegisterProcessor("user", service.NewUserProcessor(&impl.User{}));
	server := thrift.NewTSimpleServer4(processor, serverTransport, transportFactory, protocolFactory)

	fmt.Println("thrift server in", config.NETWORK_ADDR)
	server.Serve()
}
