package impl

import (
	"app/container"
	"app/providers"
	"github.com/sirupsen/logrus"
	"register"
)

var services = make(map[string](*register.ServiceInfo))

func init() {

}

type Register struct {
}

func (this *Register) Version() (r string, err error) {
	di := container.GetInstance();
	config := di.Get("config").(*providers.Config)
	key, _ := config.GetKey("application", "version");
	logger := di.Get("logger").(*logrus.Logger)
	logger.WithField("version", key.Value()).Infoln("Register.Version");
	r = key.Value()
	return
}

func (this *Register) Heartbeat(serviceInfo *register.ServiceInfo) (r *register.Response, err error) {
	// 获取日志服务
	di := container.GetInstance();
	logger := di.Get("logger").(*logrus.Logger)
	logger.WithField("heartbeat", serviceInfo.GetName()).Infoln("Register.Heartbeat");

	// 载入心跳中的服务
	services[serviceInfo.GetName()] = serviceInfo;
	// 生成新的返回对象
	response := register.NewResponse();
	// 设置返回状态
	response.Success = true;
	// 设置返回信息
	response.Message = "";
	// 设置服务列表
	response.Services = services;
	r = response;
	return
}
