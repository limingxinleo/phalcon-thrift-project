namespace php ThriftService
namespace go phalcon.thrift.service

service System {
    string version()
    string test(1:string name)
}