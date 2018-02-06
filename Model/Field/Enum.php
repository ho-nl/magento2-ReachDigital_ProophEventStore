<?php


namespace ReachDigital\ProophEventStore\Model\Field;


use MabeEnum\EnumSerializableTrait;
use ReachDigital\ProophEventStore\Api\Model\EnumInterface;
use ReachDigital\ProophEventStore\Api\Model\ValueObjectInterface;

class Enum extends \MabeEnum\Enum implements EnumInterface
{
    use EnumSerializableTrait;

    public function sameValueAs(ValueObjectInterface $object): bool
    {
        return $this->is($object);
    }

    public static function fromString(string $string): self
    {
        return self::byName($string);
    }

    public function toString(): string
    {
        return $this->getName();
    }
}
