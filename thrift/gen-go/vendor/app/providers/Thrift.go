package providers

import (
	"github.com/limingxinleo/di"
	"fmt"
	"thrift"
)

type Server struct {
	Processor        *thrift.TMultiplexedProcessor
	serverTransport  *thrift.TServerSocket
	transportFactory *thrift.TBufferedTransportFactory
	protocolFactory  *thrift.TBinaryProtocolFactory
}

func BuildThriftProvider(builder *di.Builder) {
	// Define an object in the App scope.
	builder.AddDefinition(di.Definition{
		Name: "thrift",
		Scope: di.App,
		Build: func(ctx di.Context) (interface{}, error) {
			server := &Server{}

			config := ctx.Get("config").(*Config)
			key, _ := config.GetKey("application", "network")
			network := key.Value();

			server.transportFactory = thrift.NewTBufferedTransportFactory(1024)
			server.protocolFactory = thrift.NewTBinaryProtocolFactoryDefault()
			server.serverTransport, _ = thrift.NewTServerSocket(network)
			server.Processor = thrift.NewTMultiplexedProcessor()

			//server.Processor.RegisterProcessor("app", service.NewAppProcessor(&impl.App{}))
			//processor.RegisterProcessor("user", service.NewUserProcessor(&impl.User{}));

			fmt.Println("thrift server in", network)

			return server, nil
		},
	})
}

func (this *Server) RegisterProcessor(name string, processor thrift.TProcessor) {
	this.Processor.RegisterProcessor(name, processor)
}

func (this *Server) Serve() error {
	server := thrift.NewTSimpleServer4(this.Processor, this.serverTransport, this.transportFactory, this.protocolFactory)
	return server.Serve();
}
