package providers

import (
	"github.com/go-ini/ini"
	"github.com/limingxinleo/di"
	"fmt"
)

type Config struct {
	Cfg *ini.File
}

// Config服务的Build方法
func BuildConfigProvider(builder *di.Builder) {
	// Define an object in the App scope.
	builder.AddDefinition(di.Definition{
		Name: "config",
		Scope: di.App,
		Build: func(ctx di.Context) (interface{}, error) {
			cfg, _ := ini.InsensitiveLoad("config.ini")
			fmt.Println("Build Config Service")
			return &Config{Cfg:cfg}, nil
		},
	})
}

func (this *Config)GetSection(id string) (*ini.Section, error) {
	return this.Cfg.GetSection(id)
}

func (this *Config)GetKey(sec string, key string) (*ini.Key, error) {
	section, _ := this.Cfg.GetSection(sec)
	return section.GetKey(key)
}


