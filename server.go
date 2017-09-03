package main

import (
	"fmt"
	"micro/service"
	"micro/impl"
	"os"
	"thrift"
)

const (
	NetworkAddr = "0.0.0.0:10086"
)

func init() {
	fmt.Println("INIT")
}

func main() {
	//transportFactory := thrift.NewTFramedTransportFactory(thrift.NewTTransportFactory())
	transportFactory := thrift.NewTBufferedTransportFactory(1024)
	protocolFactory := thrift.NewTBinaryProtocolFactoryDefault()
	//protocolFactory := thrift.NewTCompactProtocolFactory()

	serverTransport, err := thrift.NewTServerSocket(NetworkAddr)
	if err != nil {
		fmt.Println("Error!", err)
		os.Exit(1)
	}

	processor := thrift.NewTMultiplexedProcessor();
	processor.RegisterProcessor("app", service.NewAppProcessor(&impl.App{}));
	//processor.RegisterProcessor("user", service.NewUserProcessor(&impl.User{}));
	server := thrift.NewTSimpleServer4(processor, serverTransport, transportFactory, protocolFactory)

	fmt.Println("thrift server in", NetworkAddr)
	server.Serve()
}
