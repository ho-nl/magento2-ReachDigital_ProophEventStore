<?php
/**
 * Copyright (c) Reach Digital (https://www.reachdigital.nl/)
 * See README.md for license details.
 */

declare(strict_types=1);
namespace ReachDigital\ProophEventStore\Model\Field;


use Prooph\EventStore\Util\Assertion;
use ReachDigital\ProophEventStore\Api\Model\ValueObjectInterface;

abstract class IntType implements ValueObjectInterface
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $int
     * @return IntType
     * @throws \Assert\AssertionFailedException
     */
    public static function fromString(string $int): self
    {
        Assertion::integerish($int);
        return new static((int) $int);
    }

    private function __construct(int $int)
    {
        $this->value = $int;
    }

    public function toString(): string
    {
        return $this->__toString();
    }

    public function __toString(): string
    {
        return (string) $this->value;
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
