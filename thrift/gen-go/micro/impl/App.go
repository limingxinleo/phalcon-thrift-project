package impl

type App struct {
}

func (this *App) Version() (r string, err error) {
	r = "1.10.8"
	return
}
