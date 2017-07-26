package main

import (
	"fmt"
	"git.apache.org/thrift.git/lib/go/thrift"
	"os"
	"phalcon/thrift/service"
)

const (
	NetworkAddr = "0.0.0.0:10086"
)

type SystemThrift struct {
}

func (this *SystemThrift) Version() (r string, err error) {
	return "1.0.0"
}

func (this *SystemThrift) Test(name string) (r string, err error) {
	return "Hello "
}

func main() {
	transportFactory := thrift.NewTFramedTransportFactory(thrift.NewTTransportFactory())
	protocolFactory := thrift.NewTBinaryProtocolFactoryDefault()
	//protocolFactory := thrift.NewTCompactProtocolFactory()

	serverTransport, err := thrift.NewTServerSocket(NetworkAddr)
	if err != nil {
		fmt.Println("Error!", err)
		os.Exit(1)
	}

	handler := &SystemThrift{}
	processor := service.NewSystemProcessor(handler)

	server := thrift.NewTSimpleServer4(processor, serverTransport, transportFactory, protocolFactory)
	fmt.Println("thrift server in", NetworkAddr)
	server.Serve()
}