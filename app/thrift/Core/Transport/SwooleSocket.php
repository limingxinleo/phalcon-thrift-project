<?php
// +----------------------------------------------------------------------
// | SwooleSocket.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Thrift\Core\Transport;

use Thrift\Exception\TTransportException;
use Thrift\Factory\TStringFuncFactory;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TFramedTransport;

class SwooleSocket extends TBufferedTransport
{
    public $buffer = '';
    public $offset = 0;
    public $server;
    protected $fd;
    protected $read_ = true;
    protected $rBuf_ = '';
    protected $wBuf_ = '';

    public function setHandle($fd)
    {
        $this->fd = $fd;
    }

    public function open()
    {

    }

    public function readAll($len)
    {
        return $this->buffer;
    }

    public function write($buf)
    {
        $this->wBuf_ .= $buf;
    }

    function flush()
    {
        $out = pack('N', strlen($this->wBuf_));
        $out .= $this->wBuf_;
        $this->server->send($this->fd, $out);
        $this->wBuf_ = '';
    }
}
