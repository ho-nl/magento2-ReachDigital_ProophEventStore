<?php
declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Api\Model;


interface ValueObjectInterface
{
    public static function fromString(string $value);
    public function toString() : string;
    public function sameValueAs(ValueObjectInterface $object): bool;
}
