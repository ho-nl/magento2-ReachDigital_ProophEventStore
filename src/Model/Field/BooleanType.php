<?php
/**
 * Copyright (c) Reach Digital (https://www.reachdigital.nl/)
 * See README.md for license details.
 */

declare(strict_types=1);
namespace ReachDigital\ProophEventStore\Model\Field;


use Prooph\EventStore\Util\Assertion;
use ReachDigital\ProophEventStore\Api\Model\ValueObjectInterface;

abstract class BooleanType implements ValueObjectInterface
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $bool
     * @return static
     * @throws \Assert\AssertionFailedException
     */
    public static function fromString(string $bool)
    {
        Assertion::range((int) $bool, 0, 1);
        return new static((bool) $bool);
    }

    private function __construct(bool $bool)
    {
        $this->value = $bool;
    }

    public function toString(): string
    {
        return $this->__toString();
    }

    public function __toString(): string
    {
        return $this->value ? '1' : '0';
    }

    /**
     * @param ValueObjectInterface|StringType $other
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $other): bool
    {
        return \get_class($this) === \get_class($other) && $this->toString() === $other->toString();
    }
}
