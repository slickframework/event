<?php

/**
 * This file is part of Event
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Event;

/**
 * EventGenerator
 *
 * @package Slick\Event
 */
interface EventGenerator
{

    /**
     * Record ane event that occurs
     *
     * @param Object $event
     */
    public function recordThat(Object $event): void;

    /**
     * Releases all recorded events
     *
     * @return Object[]
     */
    public function releaseEvents(): array;
}
