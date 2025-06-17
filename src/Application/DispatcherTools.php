<?php

/**
 * This file is part of Event
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Event\Application;

/**
 * DispatcherTools
 *
 * @package Slick\Event\Application
 */
trait DispatcherTools
{
    /**
     * Matches the listener register pattern with event
     *
     * @param $pattern
     * @param object $event
     * @return bool
     */
    protected function match($pattern, object $event): bool
    {
        $regEx = str_replace(['\\', '*'], ['\\\\', '(.*)'], $pattern);
        $regEx = "/$regEx/i";
        $name = get_class($event);
        return (bool) preg_match($regEx, $name);
    }

    /**
     * orderedListeners
     *
     * @param array $unordered
     * @return array
     */
    protected function orderedListeners(array $unordered): array
    {
        usort($unordered, function ($a, $b) {
            if ($a->priority > $b->priority) {
                return -1;
            }

            if ($a->priority === $b->priority) {
                return 0;
            }

            return 1;
        });
        return $unordered;
    }
}
