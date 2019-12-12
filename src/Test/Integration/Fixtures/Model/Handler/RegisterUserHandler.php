<?php
declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Handler;

use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Infrastructure\UserRepository;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Command\RegisterUser;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\User;

class RegisterUserHandler
{
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(RegisterUser $registerUser): void
    {
        $user = User::registerWithData($registerUser->id(), $registerUser->email(), $registerUser->password());
        $this->repository->save($user);
    }
}
