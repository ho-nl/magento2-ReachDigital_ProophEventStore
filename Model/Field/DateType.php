<?php
/**
 * Copyright (c) Reach Digital (https://www.reachdigital.nl/)
 * See README.md for license details.
 */

declare(strict_types=1);
namespace ReachDigital\ProophEventStore\Model\Field;


use Prooph\EventStore\Util\Assertion;
use ReachDigital\ProophEventStore\Api\Model\ValueObjectInterface;

class DateType implements ValueObjectInterface
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
        Assertion::date($value, \DateTime::ATOM);
        $date = new \DateTime($value, new \DateTimeZone('utc'));
        return new static($date);
    }

    private function __construct(\DateTime $date)
    {
        $this->value = $date;
    }

    public function toString(): string
    {
        return $this->__toString();
    }

    public function __toString(): string
    {
        return $this->value->format(\DateTime::ATOM);
    }

    /**
     * @param ValueObjectInterface|DateType $other
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $other): bool
    {
        return \get_class($this) === \get_class($other) && $this->toString() === $other->toString();
    }
}
