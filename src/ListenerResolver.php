<?php

/**
 * This file is part of Event
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Event;

/**
 * ListenerResolver
 *
 * @package Slick\Event
 */
interface ListenerResolver
{
    /**
     * Returns a list of resolved event listeners.
     *
     * Each listener entry is represented as an associative array with:
     *  - 'event' (string): The fully qualified class name of the event.
     *  - 'listener' (EventListener): A callable to be executed when the event is dispatched.
     *  - 'priority' (int): The execution priority (higher runs first).
     *
     * @return array<array{event: string, listener: EventListener, priority: int}>
     */
    public function resolve(): array;
}
