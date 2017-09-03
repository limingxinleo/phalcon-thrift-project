package main

import (
	"fmt"
	"micro/service"
	"micro/impl"
	"micro/config"
	"os"
	"thrift"
	"log"
	"time"
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

	year := time.Now().Format("2006-01")
	fmt.Println(year)
	path := fmt.Sprintf("%s/%s-%s.log", config.LOGDIR, "go.server", year)
	file, _ := os.OpenFile(path, os.O_RDWR | os.O_CREATE | os.O_APPEND, 0666)
	log.SetOutput(file);
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
