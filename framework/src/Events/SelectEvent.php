<?php

namespace Melon\Events;

use Melon\Enums\EventEnum;
use Throwable;

/**
 * 默认 Select 事件
 */
class SelectEvent extends BaseEvent
{
    public array $all = [];
    public array $read = [];
    public array $write = [];
    public array $except = [];

    public function __construct()
    {

    }

    public function add(mixed $stream, EventEnum $eventEnum)
    {
        $fd = intval($stream);

        $this->all[$fd][$eventEnum->name] = $stream;

        match ($eventEnum) {
            EventEnum::READ => ($this->read[$fd] = $stream),
            EventEnum::WRITE => ($this->write[$fd] = $stream),
            EventEnum::EXPECT => ($this->except[$fd] = $stream),
        };
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
                    $this->acceptConnection($item);
                }
            }
        }
    }
}
