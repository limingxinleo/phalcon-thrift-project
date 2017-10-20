namespace php Xin.Thrift.Register
namespace go vendor.register

struct Response {
    1: bool                 success,
    2: string               message,
    3: map<string,ServiceInfo> services,
}
struct ServiceInfo {
    1: string   name,
    2: string   host,
    3: i32      port,
    4: string   nonce,
    5: string   sign,
    6: bool     isService
}

service Register {
    // 返回当前注册中心版本
    string version()

    // 服务注册心跳
    Response heartbeat(1: ServiceInfo serviceInfo)
}