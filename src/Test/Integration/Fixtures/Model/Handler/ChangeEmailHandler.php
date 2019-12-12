<?php
declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Handler;

use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Infrastructure\UserRepository;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Command\ChangeEmail;

class ChangeEmailHandler
{
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(ChangeEmail $changeEmail): void
    {
        $user = $this->repository->get($changeEmail->id());
        $user->changeEmail($changeEmail->email());
        $this->repository->save($user);
    }
}
