<?php

/**
 * This file is part of slick/event package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\Event\Application;

use Slick\Event\Application\CallableEventListener;
use PhpSpec\ObjectBehavior;
use Slick\Event\Event;

/**
 * CallableEventListenerSpec specs
 *
 * @package spec\Slick\Event\Application
 */
class CallableEventListenerSpec extends ObjectBehavior
{
    private bool $called = false;
    public function let()
    {
        $callable = function (object $event) {
            $this->called = true;
            return $event;
        };
        $this->beConstructedWith($callable);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CallableEventListener::class);
    }

    function it_call_callable_on_execute(Event $event)
    {
        $this->handle($event)->shouldBe($event);
        if (!$this->called) {
            throw new \Exception("Callable listener not called");
        }
    }
}
