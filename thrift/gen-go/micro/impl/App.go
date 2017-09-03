package impl

import log "github.com/sirupsen/logrus"

func init() {

}

type App struct {
}

func (this *App) Version() (r string, err error) {
	log.Info("App:version");
	r = "1.10.8"
	return
}
