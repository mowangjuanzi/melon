<?php


namespace Melon\Events;


use EventBase;
use Melon\Interfaces\EventInterface;

class Event implements EventInterface
{
    /**
     * @var Event
     */
    private static $instance;

    /**
     * @var EventBase
     */
    protected EventBase $event_base;

    /**
     * 所有的事件
     * @var array
     */
    protected array $all_events = [];

    private function __construct()
    {
        $this->event_base = new EventBase();
    }

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function add($fd, $flag, $func)
    {
        switch ($flag) {
            case self::EVENT_READ:
                $event = new \Event($this->event_base, $fd, \Event::READ | \Event::PERSIST, $func, $fd);
                $event->add();
                $this->all_events[(int)$fd][self::EVENT_READ] = $event;
                return true;
        }
    }

    public function loop()
    {
        $this->event_base->loop();
    }
}
