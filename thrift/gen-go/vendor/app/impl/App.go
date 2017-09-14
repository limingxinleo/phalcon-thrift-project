package impl

import (
	"app/container"
	//"app/providers"
	log "github.com/sirupsen/logrus"
	"app/providers"
)

func init() {

}

type App struct {
}

func (this *App) Version() (r string, err error) {
	di := container.GetInstance();
	config := di.Get("config").(*providers.Config)
	key, _ := config.GetKey("application", "version");
	log.Info("App:version");
	r = key.Value()
	return
}
