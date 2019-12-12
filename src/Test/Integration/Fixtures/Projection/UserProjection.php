<?php
declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Test\Integration\Fixtures\Projection;

use Prooph\EventStore\Projection\ReadModelProjector;
use ReachDigital\ProophEventStore\Api\ProjectionInterface;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Event\EmailChanged;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Event\UserRegistered;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\User;

class UserProjection implements ProjectionInterface
{
    /**
     * @param \Prooph\EventStore\Projection\Projector|ReadModelProjector $projector
     * @return \Prooph\EventStore\Projection\Projector|ReadModelProjector
     */
    public function project($projector)
    {
        $projector->fromCategory(User::class)->when([
            UserRegistered::class => function ($state, UserRegistered $event) {
                /** @var UserReadModel $readModel */
                $readModel = $this->readModel();
                $readModel->stack('insert', [
                    'id' => $event->aggregateId(),
                    'password' => $event->password(),
                    'email' => $event->email(),
                    'name' => 'to be replaced',
                ]);
            },
            EmailChanged::class => function ($state, EmailChanged $event) {
                /** @var UserReadModel $readModel */
                $readModel = $this->readModel();
                $readModel->stack('changeEmail', [
                    'id' => $event->aggregateId(),
                    'email' => $event->email(),
                ]);
            },
        ]);
        return $projector;
    }
}
