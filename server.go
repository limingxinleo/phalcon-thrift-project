package main

import (
	"fmt"
	"thrift"
	"os"
	"phalcon/thrift/service"
)

const (
	NetworkAddr = "0.0.0.0:10086"
)

type SystemThrift struct {
}

func (this *SystemThrift) Version() (r string, err error) {
	r = "1.0.0"
	return
}

func (this *SystemThrift) Test(name string) (r string, err error) {
	r = "Hello " + name
	return
}
func (this *SystemThrift) Count(num int16) (r string, err error) {
	for j := 0; j <= 10000; j++ {
		for i := 0; i <= 10000; i++ {
			num++;
		}
	}
	r = "finish"
	return
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