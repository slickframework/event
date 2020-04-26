<?php

/**
 * This file is part of slick/event package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Event\Domain;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

/**
 * EventId
 *
 * @package Slick\Event\Domain
 */
final class EventId
{
    /**
     * @var string
     */
    private $identifier;


    /**
     * Creates a EventId
     *
     * @param string $identifier
     */
    public function __construct(string $identifier = null)
    {
        $identifier = $identifier ?: Uuid::uuid4()->toString();
        if (!Uuid::isValid($identifier)) {
            throw new InvalidArgumentException(
                "Invalid event identifier."
            );
        }
        $this->identifier = $identifier;
    }

    /**
     * Event identifier as a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->identifier;
    }

}
