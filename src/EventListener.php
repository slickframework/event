<?php

/**
 * This file is part of Event
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Event;

/**
 * EventListener
 *
 * @package Slick\Event
 */
interface EventListener
{

    /**
     * Handles provided event
     *
     * @param Object $event
     */
    public function handle(Object $event): Object;
}