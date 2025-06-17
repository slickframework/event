<?php

/**
 * This file is part of Event
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Event\Application\Attribute;

use Attribute;

/**
 * AsEventListener
 *
 * @package Slick\Event\Application\Attribute
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class AsEventListener
{
    public function __construct(
        public string $event = '*',
        public ?string $method = null,
        public int $priority = 0
    ) {
    }
}
