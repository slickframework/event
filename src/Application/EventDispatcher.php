<?php

/**
 * This file is part of slick/event package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Event\Application;

use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use Slick\Event\Domain\EventGeneratorMethods;
use Slick\Event\Event;
use Slick\Event\EventDispatcher as SlickEventDispatcher;
use Slick\Event\EventGenerator;
use Slick\Event\EventListener;

/**
 * EventDispatcher
 *
 * @package Slick\Event\Application
 */
final class EventDispatcher implements SlickEventDispatcher, EventGenerator
{

    use EventGeneratorMethods;

    /**
     * @var bool
     */
    private static $dispatching = false;

    /**
     * @var array
     */
    private $listeners = [];

    /**
     * Dispatches events form an event generator
     *
     * @param EventGenerator $generator
     * @return Event[] the list of dispatched events
     */
    public function dispatchEventsFrom(EventGenerator $generator): array
    {
        $events = [];
        foreach ($generator->releaseEvents() as $event) {
            $events[] = $this->dispatch($event);
        }
        return $events;
    }

    /**
     * Adds an event listener
     *
     * @param string $event
     * @param EventListener|Callable|ListenerProviderInterface $listener
     * @param int $priority
     */
    public function addListener(string $event, $listener, int $priority = SlickEventDispatcher::P_NORMAL): void
    {
        $this->listeners[$event][] = (object) [
            'listener' => $listener,
            'priority' => $priority
        ];
    }

    /**
     * Provide all relevant listeners with an event to process.
     *
     * @param object $event
     *   The object to process.
     *
     * @return object
     *   The Event that was passed, now modified by listeners.
     */
    public function dispatch(object $event)
    {
        if (self::$dispatching) {
            $this->recordThat($event);
            return $event;
        }

        self::$dispatching = true;

        foreach ($this->listenersFor($event) as $listener) {
            $event = $this->invokeListener($listener, $event);
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }
        }

        self::$dispatching = false;
        $this->dispatchEventsFrom($this);
        return $event;
    }

    /**
     * Prioritized list of listeners for provided event
     *
     * @param object $event
     * @return array
     */
    private function listenersFor(object $event): array
    {
        $listeners = [];
        $unordered = $this->orderedListeners(
            $this->matchedListeners($event)
        );
        foreach ($unordered as $data) {
            if ($data->listener instanceof ListenerProviderInterface === false) {
                $listeners[] = $data->listener;
                continue;
            }

            foreach ($data->listener->getListenersForEvent($event) as $listener) {
                $listeners[] = $listener;
            }
        }
        return $listeners;
    }

    /**
     * Invoke listener
     *
     * @param $listener
     * @param Object $event
     * @return Object
     */
    private function invokeListener($listener, Object $event)
    {
        if (is_callable($listener)) {
            return $listener($event);
        }

        if ($listener instanceof  EventListener) {
            return $listener->handle($event);
        }

        return $event;
    }

    /**
     * Matches the listener register pattern with event
     *
     * @param $pattern
     * @param object $event
     * @return bool
     */
    private function match($pattern, object $event): bool
    {
        $regEx = str_replace(['\\', '*'], ['\\\\', '(.*)'], $pattern);
        $regEx = "/$regEx/i";
        $name = get_class($event);
        return (bool) preg_match($regEx, $name);
    }

    /**
     * Filters the matched listeners
     *
     * @param object $event
     * @return array
     */
    private function matchedListeners(object $event): array
    {
        $unordered = [];
        foreach ($this->listeners as $pattern => $data) {
            if (!$this->match($pattern, $event)) {
                continue;
            }

            foreach ($data as $datum) {
                $unordered[] = $datum;
            }
        }
        return $unordered;
    }

    /**
     * orderedListeners
     *
     * @param array $unordered
     * @return array
     */
    private function orderedListeners(array $unordered): array
    {
        usort($unordered, function ($a, $b) {
            if ($a->priority > $b->priority) {
                return -1;
            }

            if ($a->priority === $b->priority) {
                return 0;
            }

            return 1;
        });
        return $unordered;
    }
}
