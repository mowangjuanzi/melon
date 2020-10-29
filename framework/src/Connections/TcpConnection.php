<?php


namespace Melon\Connections;


use Melon\Events\Event;
use Melon\Interfaces\EventInterface;

class TcpConnection
{
    /**
     * @var resource
     */
    protected $socket;

    /**
     * 构造函数
     * @param $socket
     * @param $remote_address
     */
    public function __construct($socket, $remote_address)
    {
        $this->socket = $socket;

        $event = Event::getInstance();
        $event->add($socket, EventInterface::EVENT_READ, [$this, "baseRead"]);
    }

    public function baseRead($socket, $eof = true)
    {
        dump($socket, $eof);
    }
}
