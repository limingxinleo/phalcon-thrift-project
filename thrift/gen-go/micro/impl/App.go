package impl

import log "github.com/sirupsen/logrus"
import "os"
import "micro/config"

func init() {
	path := config.LOGDIR + "/app.go.log"
	file, _ := os.OpenFile(path, os.O_RDWR | os.O_CREATE | os.O_APPEND, 0666)
	log.SetOutput(file);
}

type App struct {
}

func (this *App) Version() (r string, err error) {
	log.Info("App:version");
	r = "1.10.8"
	return
}
