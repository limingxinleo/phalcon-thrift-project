namespace php ThriftService
namespace go phalcon.thrift.service

service System {
    string version()
    string test(1:string name)
    string count(1:i16 num)
    map<i32,map<string,string>> listOutput(1:map<i32,map<string,string>> data)
}