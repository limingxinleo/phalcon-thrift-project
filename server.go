package main

import (
	"fmt"
	"thrift"
	"os"
	"micro/service"
)

const (
	NetworkAddr = "0.0.0.0:10086"
)

type App struct {
}

func (this *App) Version() (r string, err error) {
	r = "1.0.0"
	return
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

	handler := &App{}
	processor := service.NewAppProcessor(handler)

	server := thrift.NewTSimpleServer4(processor, serverTransport, transportFactory, protocolFactory)
	fmt.Println("thrift server in", NetworkAddr)
	server.Serve()
}