<?php

/**
 * This file is part of slick/event package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\Event\Domain;

use Cassandra\Date;
use DateTimeImmutable;
use Slick\Event\Domain\AbstractEvent;
use PhpSpec\ObjectBehavior;
use Slick\Event\Domain\EventId;
use Slick\Event\Event;

/**
 * AbstractEventSpec specs
 *
 * @package spec\Slick\Event\Domain
 */
class AbstractEventSpec extends ObjectBehavior
{

    function let()
    {
        $this->beAnInstanceOf(TestEvent::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AbstractEvent::class);
    }

    function it_has_an_event_id()
    {
        $this->eventId()->shouldBeAnInstanceOf(EventId::class);
    }

    function it_has_an_occurrence_date_and_time()
    {
        $this->occurredOn()->shouldBeAnInstanceOf(DateTimeImmutable::class);
    }
}

class TestEvent extends AbstractEvent implements Event
{

}