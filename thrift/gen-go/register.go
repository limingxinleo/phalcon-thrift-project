package main

import (
	"app/container"
	"app/providers"
	"app/impl"
	"register"
)

func main() {
	container.Init()
	di := container.GetInstance()
	server := di.Get("thrift").(*providers.Server)
	server.RegisterProcessor("register", register.NewRegisterProcessor(&impl.Register{}))
	server.Serve()
}
