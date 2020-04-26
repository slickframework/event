<?php

/**
 * This file is part of slick/event
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Event;

use DateTimeImmutable;
use Slick\Event\Domain\EventId;

/**
 * Event
 *
 * @package Slick\Event
 */
interface Event
{

    /**
     * Event identifier
     *
     * @return EventId
     */
    public function eventId(): EventId;

    /**
     * The date and time that event has occurred
     *
     * @return DateTimeImmutable
     */
    public function occurredOn(): DateTimeImmutable;
}
