<?php


namespace Melon\Interfaces;


interface EventInterface
{
    const EVENT_READ = 1;

    const EVENT_WRITE = 2;

    const EVENT_EXCEPT = 3;

    const EVENT_SIGNAL = 4;
}
