package impl

import (
	"app/container"
	"app/providers"
	"github.com/sirupsen/logrus"
)

func init() {

}

type App struct {
}

func (this *App) Version() (r string, err error) {
	di := container.GetInstance();
	config := di.Get("config").(*providers.Config)
	key, _ := config.GetKey("application", "version");

	logger := di.Get("logger").(*logrus.Logger)
	logger.WithField("version", key.Value()).Infoln("App.Version");
	
	r = key.Value()
	return
}
