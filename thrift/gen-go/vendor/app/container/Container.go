package container

import (
	"app/providers"
	"github.com/limingxinleo/di"
)

var DI di.Context

func Init() {
	// Create a Builder with the default scopes.
	builder, _ := di.NewBuilder();

	// Register ServiceProvider
	providers.BuildConfigProvider(builder);
	providers.BuildLoggerProvider(builder);
	providers.BuildDBProvider(builder);
	providers.BuildThriftProvider(builder);
	// Build
	DI = builder.Build();
}

func GetInstance() di.Context {
	return DI
}


