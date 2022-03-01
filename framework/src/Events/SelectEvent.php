<?php

namespace Melon\Events;

use Closure;
use Melon\Enums\EventEnum;
use Throwable;

/**
 * 默认 Select 事件
 */
class SelectEvent extends BaseEvent
{
    /**
     * 所有的处理都要记录到这里，因为还要记录回调函数等
     * @var array
     */
    public array $all = [];

    /**
     * read 事件
     * @var array
     */
    public array $read = [];

    /**
     * write 事件
     * @var array
     */
    public array $write = [];

    /**
     * expect 事件
     * @var array
     */
    public array $except = [];

    public function __construct()
    {

    }

    public function add(mixed $stream, EventEnum $eventEnum, Closure $callback = null)
    {
        $fd = intval($stream);

        $data = [
            $callback,
            $stream
        ];

        $this->all[$fd][$eventEnum->name] = $data;

        match ($eventEnum) {
            EventEnum::READ => ($this->read[$fd] = $stream),
            EventEnum::WRITE => ($this->write[$fd] = $stream),
            EventEnum::EXPECT => ($this->except[$fd] = $stream),
        };
    }

    public function remove(mixed $stream, EventEnum $eventEnum)
    {
        $fd = strval($stream);

        unset($this->all[$fd][$eventEnum->name]);

        switch ($eventEnum) {
            case EventEnum::READ:
                unset($this->read[$fd]);
                break;
            case EventEnum::WRITE:
                unset($this->write[$fd]);
                break;
            case EventEnum::EXPECT:
                unset($this->except[$fd]);
        }
    }

    /**
     * 循环
     * @return void
     */
    public function loop()
    {
        $result = false;

        while (1) {
            if ($this->read || $this->write || $this->except) {
                try {
                    $result = stream_select($this->read, $this->write, $this->except, 0, 100000000);
                }catch (Throwable $e) {
                    dd($e);
                }
            } else {
                dump("超时跳出");
                break;
            }

            if (!$result) {
                dd("error");
                continue;
            }

            if ($this->read) {
                foreach ($this->read as $item) {
                    $fd = intval($item);
                    $this->all[$fd][EventEnum::READ->name][0]($item);

                    // dd($this->read);
                }
            }
        }
    }
}
