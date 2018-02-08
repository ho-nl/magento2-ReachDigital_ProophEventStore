<?php
/**
 * Copyright (c) Reach Digital (https://www.reachdigital.nl/)
 * See README.md for license details.
 */

declare(strict_types=1);
namespace ReachDigital\ProophEventStore\Model\Field;

use MabeEnum\EnumSerializableTrait;
use ReachDigital\ProophEventStore\Api\Model\EnumInterface;
use ReachDigital\ProophEventStore\Api\Model\ValueObjectInterface;

abstract class EnumType extends \MabeEnum\Enum implements EnumInterface
{
    use EnumSerializableTrait;

    /**
     * @param string $string
     * @return EnumType
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public static function fromString(string $string): self
    {
        return static::byName($string);
    }

    public function toString(): string
    {
        return $this->__toString();
    }

    public function sameValueAs(ValueObjectInterface $object): bool
    {
        return $this->is($object);
    }
}
