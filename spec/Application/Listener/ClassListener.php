<?php

/**
 * This file is part of Event
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Event\Test\Application\Listener;

use Slick\Event\Application\Attribute\AsEventListener;
use Slick\Event\EventListener;

/**
 * ClassListener
 *
 * @package Slick\Event\Test\Application\Listener
 */
#[AsEventListener(event: "test", priority: 10)]
final class ClassListener implements EventListener
{

    /**
     * @inheritDoc
     */
    public function handle(object $event): object
    {
        return $event;
    }
}
