<?php
declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Api\Field;


interface ValueObjectInterface
{
    public function sameValueAs(ValueObjectInterface $object): bool;
}
