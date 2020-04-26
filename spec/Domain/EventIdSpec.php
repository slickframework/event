<?php

/**
 * This file is part of slick/event package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\Event\Domain;

use Ramsey\Uuid\Uuid;
use Slick\Event\Domain\EventId;
use PhpSpec\ObjectBehavior;

/**
 * EventIdSpec specs
 *
 * @package spec\Slick\Event\Event
 */
class EventIdSpec extends ObjectBehavior
{

    private $identifier;

    function let()
    {
        $this->identifier = (string)Uuid::uuid4();
        $this->beConstructedWith($this->identifier);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(EventId::class);
    }

    function it_has_an_identifier()
    {
        $this->__toString()->shouldBe($this->identifier);
    }

    function it_throws_an_exception_when_identifier_is_not_a_valid_uuid()
    {
        $this->beConstructedWith('test');
        $this->shouldThrow(\InvalidArgumentException::class)
            ->duringInstantiation();
    }

    function it_generate_an_uuid_internally()
    {
        $regEx = '/\b[0-9a-f]{8}\b-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-\b[0-9a-f]{12}\b/i';
        $this->beConstructedWith(null);
        $this->__toString()->shouldMatch($regEx);
    }
}
