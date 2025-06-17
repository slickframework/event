<?php

/**
 * This file is part of Event
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Event\Application;

use Closure;
use Slick\Event\EventListener;

/**
 * CallableEventListener
 *
 * @package Slick\Event\Application
 */
final class CallableEventListener implements EventListener
{

    public function __construct(
        private Closure $listener
    ) {
    }

    /**
     * @inheritDoc
     */
    public function handle(object $event): object
    {
        $result = ($this->listener)($event);
        return is_object($result) ? $result : $event;
    }
}
