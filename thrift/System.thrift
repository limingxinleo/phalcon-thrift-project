namespace php ThriftService
service System {
    string version()
    string test(1:string name)
}