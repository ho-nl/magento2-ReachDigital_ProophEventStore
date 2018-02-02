<?php


namespace ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Handler;


use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Api\UserRepositoryInterface;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Command\RegisterUser;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\User;

class RegisterUserHandler
{
    private $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(RegisterUser $registerUser): void
    {
        $user = User::registerWithData($registerUser->id(), $registerUser->email(), $registerUser->password());
        $this->repository->save($user);
    }
}
