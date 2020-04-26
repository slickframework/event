<?php

/**
 * This file is part of slick/event package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\Event\Application;

use PhpSpec\Exception\Example\FailureException;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use Slick\Event\Application\EventDispatcher;
use PhpSpec\ObjectBehavior;
use Slick\Event\Event;
use Slick\Event\EventDispatcher as SlickEventDispatcher;
use Slick\Event\EventGenerator;
use Slick\Event\EventListener;

/**
 * EventDispatcherSpec specs
 *
 * @package spec\Slick\Event\Application
 */
class EventDispatcherSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(EventDispatcher::class);
    }

    function its_an_event_dispatcher()
    {
        $this->shouldBeAnInstanceOf(SlickEventDispatcher::class);
    }

    function it_can_add_an_event_callable()
    {
        $call = function (object $eventObject) {
            $eventObject->foo = 'baz';
            return $eventObject;
        };

        $this->addListener('*', $call);
        $eventDispatched = (object)['foo' => 'bar'];
        $this->dispatch($eventDispatched)->shouldBe($eventDispatched);
        if ($eventDispatched->foo === 'bar') {
            throw new FailureException(
                "Expected callable to be called, but it wasn't..."
            );
        }
    }

    function it_can_add_event_listeners(EventListener $listener)
    {
        $this->addListener('*', $listener);
        $eventDispatched = (object)['foo' => 'bar'];
        $listener->handle($eventDispatched)
            ->willReturnArgument(0)
            ->shouldBeCalled();
        $this->dispatch($eventDispatched);
    }

    function it_handles_event_priority()
    {
        $event = new PriorityEvent();
        $listener1 = new PriorityListener();
        $listener2 = new PriorityListener();
        $this->addListener('*', $listener1, EventDispatcher::P_LOW);
        $this->addListener('*', $listener2);
        $this->dispatch($event)->called()->shouldBe([$listener2, $listener1]);
    }

    function it_match_events_by_its_names()
    {
        $event = new PriorityEvent();
        $listener1 = new PriorityListener();
        $listener2 = new PriorityListener();
        $this->addListener(\stdClass::class, $listener1, EventDispatcher::P_LOW);
        $this->addListener(PriorityEvent::class, $listener2);
        $this->dispatch($event)->called()->shouldBe([$listener2]);
    }

    function it_match_all_with_asterisk(EventListener $listener1, EventListener $listener2)
    {
        $this->addListener('*', $listener1);
        $this->addListener(PriorityEvent::class, $listener2);

        $event = (object)[];
        $listener1->handle($event)->willReturnArgument(0)->shouldBeCalled();
        $listener2->handle($event)->shouldNotBeCalled();

        $this->dispatch($event);
    }

    function it_can_place_asterisk_in_any_place_in_pattern(EventListener $listener)
    {
        $event = new PriorityEvent();
        $listener->handle($event)->willReturnArgument(0)->shouldBeCalled();

        $this->addListener('*Event', $listener);
        $this->dispatch($event);
    }

    function it_stops_the_dispatch_when_stoppable_event_is_active(
        StoppableEventInterface $event,
        EventListener $listener1,
        EventListener $listener2
    )
    {
        $event->isPropagationStopped()->willReturn(true);
        $listener1->handle($event)->willReturnArgument(0)->shouldBeCalled();
        $listener2->handle($event)->shouldNotBeCalled();

        $this->addListener('*', $listener1);
        $this->addListener('*', $listener2);
        $this->dispatch($event);
    }

    function it_handles_event_listener_providers(
        ListenerProviderInterface $listenerProvider,
        EventListener $listener,
        Event $event
    ) {
        $listenerProvider->getListenersForEvent($event)->willReturn([$listener]);
        $listener->handle($event)->willReturnArgument(0)->shouldBeCalled();
        $this->addListener('*', $listenerProvider);
        $this->dispatch($event);
    }

    function it_can_dispatch_events_form_a_generator(
        EventGenerator $generator,
        Event $event,
        EventListener $listener
    ) {
        $listener->handle($event)->willReturnArgument(0)->shouldBeCalled();
        $generator->releaseEvents()->willReturn([$event]);
        $this->addListener('*', $listener);
        $this->dispatchEventsFrom($generator);
    }

    function it_holds_dispatch_calls_for_after_dispatch_is_over(Event $event1, Event $event2)
    {
        $this->shouldBeAnInstanceOf(EventGenerator::class);
        $dispatcher = $this;
        $calls = [];
        $listener = function (object $event) use ($dispatcher, $event2, &$calls) {
            static $count;
            if (!$count) {
                $count = 0;
            }

            if ($count < 1) {
                $count++;
                $dispatcher->dispatch($event2);
            }

            $calls[] = $event;

            return $event;
        };
        $this->addListener('*', $listener);
        $this->dispatch($event1)->shouldBe($event1);
        $expected = [$event1->getWrappedObject(), $event2->getWrappedObject()];

        if ($calls !== $expected) {
            throw new FailureException(
                "Late dispatch was processed before the origin has finished, it shouldn't..."
            );
        }
    }
}

class PriorityEvent
{

    protected $called = [];

    /**
     * call
     *
     * @param EventListener $listener
     */
    public function call(EventListener $listener): void
    {
        $this->called[] = $listener;
    }

    public function called(): array
    {
        return $this->called;
    }
}

class PriorityListener implements EventListener
{

    /**
     * Handles provided event
     *
     * @param Object|PriorityEvent $event
     */
    public function handle(object $event): object
    {
        $event->call($this);
        return $event;
    }
}
