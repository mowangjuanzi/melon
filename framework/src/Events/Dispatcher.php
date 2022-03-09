<?php

namespace Melon\Events;

use Melon\Foundation\Application;

class Dispatcher
{
    /**
     * The queue resolver instance.
     *
     * @var callable
     */
    protected $queueResolver;

    /**
     * Create a new event dispatcher instance.
     * @param Application $application
     */
    public function __construct(protected Application $application)
    {
    }

    /**
     * Set the queue resolver implementation.
     *
     * @param  callable  $resolver
     * @return $this
     */
    public function setQueueResolver(callable $resolver): static
    {
        $this->queueResolver = $resolver;

        return $this;
    }
}
