<?php
declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Command;

use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class ChangeEmail extends Command implements PayloadConstructable
{
    use PayloadTrait;

    public function id(): string
    {
        return $this->payload()['id'];
    }

    public function email(): string
    {
        return $this->payload()['email'];
    }
}
