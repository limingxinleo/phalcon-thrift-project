namespace php ThriftService
namespace go phalcon.thrift.service

service System {
    string version()
    string test(1:string name)
    string count(1:i16 num)
}