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

abstract class Enum extends \MabeEnum\Enum implements EnumInterface
{
    use EnumSerializableTrait;

    public function sameValueAs(ValueObjectInterface $object): bool
    {
        return $this->is($object);
    }

    public static function fromString(string $string): self
    {
        return static::byName($string);
    }

    public function toString(): string
    {
        return $this->getName();
    }
}
