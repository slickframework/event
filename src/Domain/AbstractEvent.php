<?php

/**
 * This file is part of slick/event package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Event\Domain;

use DateTimeImmutable;
use Slick\Event\Event;

/**
 * AbstractEvent
 *
 * @package Slick\Event\Domain
 */
abstract class AbstractEvent implements Event
{

    /**
     * @var EventId
     */
    protected $eventId;

    /**
     * @var DateTimeImmutable
     */
    protected $occurredOn;

    /**
     * Creates an Abstract Event
     */
    public function __construct()
    {
        $this->eventId = new EventId();
        $this->occurredOn = new DateTimeImmutable();
    }

    /**
     * Event identifier
     *
     * @return EventId
     */
    public function eventId(): EventId
    {
        return $this->eventId;
    }

    /**
     * The date and time that event has occurred
     *
     * @return DateTimeImmutable
     */
    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
