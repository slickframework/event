<?php

/**
 * This file is part of Event
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Event\Infrastructure;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Slick\Event\Application\DispatcherTools;

/**
 * AttributeListenerProvider
 *
 * @package Slick\Event\Infrastructure
 */
final class AttributeListenerProvider implements ListenerProviderInterface
{
    use DispatcherTools;

    public function __construct(private readonly AttributeListenerResolver $resolver)
    {
    }

    /**
     * @inheritDoc
     *
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function getListenersForEvent(object $event): iterable
    {
        $listeners = [];
        foreach ($this->resolver->resolve() as $binding) {
            if ($this->match($binding['event'], $event)) {
                $listeners[] = (object) ['listener' => $binding['listener'], 'priority' => $binding['priority']];
            }
        }

        return array_map(fn ($data) => $data->listener, $this->orderedListeners($listeners));
    }
}
