<?php


namespace ReachDigital\ProophEventStore\Model\Field;


use MabeEnum\EnumSerializableTrait;
use ReachDigital\ProophEventStore\Api\Field\EnumInterface;
use ReachDigital\ProophEventStore\Api\Field\ValueObjectInterface;

class Enum extends \MabeEnum\Enum implements EnumInterface
{
    use EnumSerializableTrait;

    public function sameValueAs(ValueObjectInterface $object): bool
    {
        return $this->is($object);
    }

    public function toString(): string
    {
        return $this->getName();
    }
}
