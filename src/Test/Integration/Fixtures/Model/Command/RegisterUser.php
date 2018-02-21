<?php
declare(strict_types=1);


namespace ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Command;


use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class RegisterUser extends Command implements PayloadConstructable
{
    use PayloadTrait;

    public static function withData(string $userId, string $name, string $email): RegisterUser
    {
        return new self([
            'user_id' => $userId,
            'name' => $name,
            'email' => $email,
        ]);
    }

    public function id(): string
    {
        return $this->payload()['id'];
    }

    public function email(): string
    {
        return $this->payload()['email'];
    }

    public function password(): string
    {
        return $this->payload()['password'];
    }
}