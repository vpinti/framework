<?php

declare(strict_types=1);

namespace Pulsar\Framework\EventDispatcher;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class EventDispatcher implements EventDispatcherInterface
{
    private iterable $listeners = [];

    public function dispatch(object $event): object
    {
        // Loop over the listeners for the event
        foreach ($this->getListenersForEvent($event) as $listener) {
            
            if($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                return $event;
            }

            // Call the listener, passing in the event (each listener will be a callable)
            $listener($event);
        }

        return $event;
    }

    /**
     * @return void
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (\is_string($params)) {
                $callable = class_exists($params) ? new $params : [$subscriber, $params];
                $this->addListener($eventName, $callable);
            } else {
                foreach($params as $listener) {
                    $callable = class_exists($listener[0]) ? new $listener[0] : [$subscriber, $listener[0]];
                    $this->addListener($eventName, $callable);
                }
            }
        }
    }

    // $eventName e.g. Framework\EventDispatcher\ResponseEvent
    public function addListener(string $eventName, callable $listener): self
    {
        $this->listeners[$eventName][] = $listener;

        return $this;
    }

    /**
     * @param object $event
     *   An event for which to return the relevant listeners.
     * @return iterable<callable>
     *   An iterable (array, iterator, or generator) of callables.  Each
     *   callable MUST be type-compatible with $event.
     */
    public function getListenersForEvent(object $event) : iterable
    {
        $eventName = get_class($event);

        if (array_key_exists($eventName, $this->listeners)) {
            return $this->listeners[$eventName];
        }

        return [];
    }
}