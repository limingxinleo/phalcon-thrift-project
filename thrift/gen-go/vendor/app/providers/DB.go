package providers

import "fmt"
import "database/sql"
import (
	_ "github.com/go-sql-driver/mysql"
	"github.com/limingxinleo/di"
)

type DB struct {
	Client   *sql.DB
	adapter  string
	host     string
	username string
	password string
	dbname   string
	port     string
}

func BuildDBProvider(builder *di.Builder) {
	// Define an object in the App scope.
	builder.AddDefinition(di.Definition{
		Name: "db",
		Scope: di.App,
		Build: func(ctx di.Context) (interface{}, error) {
			config := ctx.Get("config").(*Config)
			db := &DB{};
			db.init(config)
			db.Client, _ = sql.Open(db.adapter, db.getConn())
			fmt.Println("Build DB Service")
			return db, nil
		},
	})
}

func (this *DB) init(config *Config) {
	adapter, _ := config.GetKey("database", "adapter")
	this.adapter = adapter.Value()

	host, _ := config.GetKey("database", "host")
	this.host = host.Value()

	username, _ := config.GetKey("database", "username")
	this.username = username.Value()

	password, _ := config.GetKey("database", "password")
	this.password = password.Value()

	dbname, _ := config.GetKey("database", "dbname")
	this.dbname = dbname.Value()

	port, _ := config.GetKey("database", "port")
	this.port = port.Value()
}

func (this *DB)getConn() string {
	return fmt.Sprintf(
		"%s:%s@tcp(%s:%s)/%s",
		this.username,
		this.password,
		this.host,
		this.port,
		this.dbname,
	)
}


