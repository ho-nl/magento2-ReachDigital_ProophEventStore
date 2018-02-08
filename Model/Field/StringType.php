<?php
/**
 * Copyright (c) Reach Digital (https://www.reachdigital.nl/)
 * See README.md for license details.
 */

declare(strict_types=1);
namespace ReachDigital\ProophEventStore\Model\Field;


use ReachDigital\ProophEventStore\Api\Model\ValueObjectInterface;

abstract class StringType implements ValueObjectInterface
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     * @return static
     */
    public static function fromString(string $value): self
    {
        return new static($value);
    }

    private function __construct(string $projectName)
    {
        $this->value = $projectName;
    }

    public function toString(): string
    {
        return $this->__toString();
    }

    public function __toString(): string
    {
        return $this->value;
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
