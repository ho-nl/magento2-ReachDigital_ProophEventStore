<?php
declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Test\Integration\Fixtures\Api;


interface UserRepositoryInterface
{
    public function save(Data\UserInterface $user): void;
    public function get(string $id): ?Data\UserInterface;
}
