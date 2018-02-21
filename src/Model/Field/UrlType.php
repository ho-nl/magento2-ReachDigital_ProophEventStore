<?php
/**
 * Copyright (c) Reach Digital (https://www.reachdigital.nl/)
 * See README.md for license details.
 */

declare(strict_types=1);
namespace ReachDigital\ProophEventStore\Model\Field;


use Prooph\EventStore\Util\Assertion;
use ReachDigital\ProophEventStore\Api\Model\ValueObjectInterface;

abstract class UrlType implements ValueObjectInterface
{
    private $url;

    /**
     * @param string $url
     * @return static
     * @throws \Assert\AssertionFailedException
     */
    public static function fromString(string $url)
    {
        Assertion::url($url);
        return new static($url);
    }

    private function __construct(string $url)
    {
        $this->url = $url;
    }

    public function toString(): string
    {
        return $this->__toString();
    }

    public function __toString(): string
    {
        return $this->url;
    }

    public function sameValueAs(ValueObjectInterface $other): bool
    {
        return \get_class($this) === \get_class($other) && $this->toString() === $other->toString();
    }
}
