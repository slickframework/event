<?php

/**
 * This file is part of slick/event package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\Event\Domain;

use Slick\Event\Domain\EventGeneratorMethods;
use PhpSpec\ObjectBehavior;
use Slick\Event\Event;
use Slick\Event\EventGenerator;

/**
 * EventGeneratorMethodsSpec specs
 *
 * @package spec\Slick\Event\Domain
 */
class EventGeneratorMethodsSpec extends ObjectBehavior
{

    function let(Event $event)
    {
        $this->beAnInstanceOf(TestGenerator::class);
        $this->beConstructedWith($event);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TestGenerator::class);
        $this->shouldBeAnInstanceOf(EventGenerator::class);
    }

    function it_holds_a_list_of_events(Event $event)
    {
        $this->releaseEvents()->shouldBe([$event]);
    }

    function it_clears_all_recorded_events_eacht_time_release_is_called(Event $event)
    {
        $this->releaseEvents()->shouldBe([$event]);
        $this->releaseEvents()->shouldBe([]);
    }
}

class TestGenerator implements EventGenerator
{
    use EventGeneratorMethods;

    public function __construct(Event $event)
    {
        $this->recordThat($event);
    }
}