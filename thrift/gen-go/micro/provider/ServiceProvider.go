package provider

type ServiceProvier interface {
	Register() (err error)
}
