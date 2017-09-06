package main

import (
	"fmt"
	"app/config"
	"app/impl"
	"micro/service"
	"app/provider"
	"os"
	"thrift"
)

func init() {
	logger := provider.Logger{}
	logger.Register()
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

	processor := thrift.NewTMultiplexedProcessor()
	processor.RegisterProcessor("app", service.NewAppProcessor(&impl.App{}))
	//processor.RegisterProcessor("user", service.NewUserProcessor(&impl.User{}));
	server := thrift.NewTSimpleServer4(processor, serverTransport, transportFactory, protocolFactory)

	fmt.Println("thrift server in", config.NETWORK_ADDR)
	server.Serve()
}
