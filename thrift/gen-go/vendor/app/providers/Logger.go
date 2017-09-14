package providers

import (
	"fmt"
	"os"
	"time"
	"github.com/sirupsen/logrus"
	"github.com/limingxinleo/di"
)

func BuildLoggerProvider(builder *di.Builder) {
	// Define an object in the App scope.
	builder.AddDefinition(di.Definition{
		Name: "logger",
		Scope: di.App,
		Build: func(ctx di.Context) (interface{}, error) {
			config := ctx.Get("config").(*Config)
			key, _ := config.GetKey("log", "dir")
			dir := key.Value()
			file := getLogFile(dir);
			logrus.SetOutput(file)
			fmt.Println("Build Logger Service")
			return logrus.StandardLogger(), nil
		},
	})
}

func getLogFile(dir string) *os.File {
	// 判断日志目录是否存在
	stat, err := os.Stat(dir)
	if err != nil {
		// 新建目录
		os.Mkdir(dir, 0755)
	} else {
		if stat.Mode() != 0755 {
			os.Chmod(dir, 0755)
		}
	}

	year := time.Now().Format("2006-01")
	path := fmt.Sprintf("%s/%s-%s.log", dir, "go.server", year)
	file, _ := os.OpenFile(path, os.O_RDWR | os.O_CREATE | os.O_APPEND, 0666)
	return file
}