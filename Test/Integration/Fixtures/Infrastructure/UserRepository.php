<?php
declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Test\Integration\Fixtures\Infrastructure;

use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Api\UserRepositoryInterface;
use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\EventStore\EventStore;
use Prooph\SnapshotStore\SnapshotStore;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\User;

class UserRepository extends AggregateRepository implements UserRepositoryInterface
{
    public function __construct(EventStore $eventStore, SnapshotStore $snapshotStore)
    {
        parent::__construct(
            $eventStore,
            AggregateType::fromAggregateRootClass(User::class),
            new AggregateTranslator(),
            $snapshotStore,
            null,
            true
        );
    }

    public function save(\ReachDigital\ProophEventStore\Test\Integration\Fixtures\Api\Data\UserInterface $user): void
    {
        $this->saveAggregateRoot($user);
    }

    public function get(string $id) :? \ReachDigital\ProophEventStore\Test\Integration\Fixtures\Api\Data\UserInterface
    {
        return $this->getAggregateRoot($id);
    }
}
