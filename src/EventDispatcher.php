<?php

/**
 * This file is part of slick/event
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Event;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * EventPublisher
 *
 * @package Slick\Event
 */
interface EventDispatcher extends EventDispatcherInterface
{

    public const P_HIGH   = 100;
    public const P_NORMAL = 0;
    public const P_LOW    = -100;

    /**
     * Dispatches events form an event generator
     *
     * @param EventGenerator $generator
     * @return Event[] the list of dispatched events
     */
    public function dispatchEventsFrom(EventGenerator $generator): array;

    /**
     * Adds an event listener
     *
     * @param string                                           $event
     * @param EventListener|Callable|ListenerProviderInterface $listener
     * @param int                                              $priority
     */
    public function addListener(
        string $event,
        $listener,
        int $priority = EventDispatcher::P_NORMAL
    ): void;
}
