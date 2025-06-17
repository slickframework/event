<?php

/**
 * This file is part of slick/event package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\Event\Application;

use Prophecy\Exception\Prediction\FailedPredictionException;
use Psr\Container\ContainerInterface;
use Slick\Event\Application\ListenerClassFile;
use PhpSpec\ObjectBehavior;
use Slick\Event\Event;
use Slick\Event\Test\Application\Listener\ClassListener;
use Slick\Event\Test\Application\Listener\InvokerListener;
use Slick\Event\Test\Application\Listener\SimpleMethodListener;

/**
 * ListenerClassFileSpec specs
 *
 * @package spec\Slick\Event\Application
 */
class ListenerClassFileSpec extends ObjectBehavior
{

    public function let(ContainerInterface $container)
    {
        $this->beConstructedWith(__DIR__. '/Listener/ClassListener.php', $container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ListenerClassFile::class);
    }

    public function it_have_a_listener_checker()
    {
        $this->isListener()->shouldBe(true);
    }

    public function is_has_a_class_name()
    {
        $this->className()->shouldBe(ClassListener::class);
    }

    public function it_creates_the_listener_instance(ContainerInterface $container)
    {
        $listener = new ClassListener();
        $container->get(ClassListener::class)->shouldBeCalled()->willReturn($listener);
        $this->resolveListeners()[0]['listener']->shouldBe($listener);
    }

    public function it_can_run_class_with_invoke_method(ContainerInterface $container, Event $event)
    {
        $this->beConstructedWith(__DIR__. '/Listener/InvokerListener.php', $container);
        $container->get(InvokerListener::class)->shouldBeCalled()->willReturn(new InvokerListener());
        $this->resolveListeners()[0]['listener']->handle($event)->shouldBe($event);
        $this->resolveListeners()[1]['listener']->handle($event)->shouldBe($event);
    }

    public function it_can_run_a_method_as_listener(ContainerInterface $container, Event $event)
    {
        $this->beConstructedWith(__DIR__. '/Listener/SimpleMethodListener.php', $container);
        $container->get(SimpleMethodListener::class)->shouldBeCalled()->willReturn(new SimpleMethodListener());
        $this->resolveListeners()[0]['listener']->handle($event)->shouldBe($event);
    }

    public function it_has_bindings_information()
    {
        $this->bindings()->shouldBe([
            [
                'event' => 'test',
                'method' => 'handle',
                'priority' => 10,
                'source' => 'interface'
            ]
        ]);
    }

    public function it_has_a_file_path()
    {
        $this->filePath()->shouldBe(__DIR__. '/Listener/ClassListener.php');
    }

    public function it_can_be_serialized(ContainerInterface $container)
    {
        $listener = new ClassListener();
        $container->get(ClassListener::class)->shouldBeCalled()->willReturn($listener);
        $serData = serialize($this->getWrappedObject());
        /** @var ListenerClassFile $unserialized */
        $unserialized = unserialize($serData);
        $unserialized->withContainer($container->getWrappedObject());
        if (!$unserialized->isListener()) {
            throw new FailedPredictionException("Error unserializing listener");
        }

        $listenerResult = $unserialized->resolveListeners()[0]['listener'];
        if ($listenerResult !== $listener) {
            throw new FailedPredictionException(
                "Error unserializing listener: couldn't retrieve the listener instance."
            );
        }
    }
}
