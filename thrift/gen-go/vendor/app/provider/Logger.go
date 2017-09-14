package provider

import (
	"os"
	"time"
	"fmt"
	log "github.com/sirupsen/logrus"
	"app/config"
)

type Logger struct {
}

func (this *Logger) Register() (err error) {
	// 判断日志目录是否存在
	stat, err := os.Stat(config.LOGDIR)
	if err != nil {
		// 新建目录
		os.Mkdir(config.LOGDIR, 0755)
	} else {
		if stat.Mode() != 0755 {
			os.Chmod(config.LOGDIR, 0755)
		}
	}

	year := time.Now().Format("2006-01")
	path := fmt.Sprintf("%s/%s-%s.log", config.LOGDIR, "go.server", year)
	file, _ := os.OpenFile(path, os.O_RDWR | os.O_CREATE | os.O_APPEND, 0666)
	log.SetOutput(file)
	log.Infoln("LoggerProvider Register");
	return
}


