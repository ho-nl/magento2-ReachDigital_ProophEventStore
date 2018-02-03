<?php
declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Api\Model;

interface EntityInterface
{
    public function sameIdentityAs(EntityInterface $other): bool;
}
