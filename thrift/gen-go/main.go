package main

import (
	"app/container"
	"app/providers"
	"app/impl"
	"service"
)

func main() {
	container.Init()
	di := container.GetInstance();
	server := di.Get("thrift").(*providers.Server);
	server.RegisterProcessor("app", service.NewAppProcessor(&impl.App{}));
	server.Serve();
}