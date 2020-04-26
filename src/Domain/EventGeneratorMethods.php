<?php

/**
 * This file is part of slick/event package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Event\Domain;

use Slick\Event\Event;

/**
 * EventGeneratorMethods
 *
 * @package Slick\Event\Domain
 */
trait EventGeneratorMethods
{

    /**
     * @var Event[]
     */
    protected $events = [];

    /**
     * Record ane event that occurs
     *
     * @param Object $event
     */
    public function recordThat(Object $event): void
    {
        $this->events[] = $event;
    }

    /**
     * Releases all recorded events
     *
     * @return Event[]
     */
    public function releaseEvents(): array
    {
        $event = $this->events;
        $this->events = [];
        return $event;
    }
}
