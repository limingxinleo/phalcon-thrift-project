package providers

import "github.com/limingxinleo/di"

type ProviderInterface interface {
	Init()
	Register(DI di.Context)
}
