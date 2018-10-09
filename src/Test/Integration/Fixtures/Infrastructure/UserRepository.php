<?php
declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Test\Integration\Fixtures\Infrastructure;

use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\StreamName;
use Prooph\SnapshotStore\SnapshotStore;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\User;

class UserRepository extends AggregateRepository
{
    public function __construct(
        EventStore $eventStore,
        SnapshotStore $snapshotStore
    ) {
        parent::__construct(
            $eventStore,
            AggregateType::fromAggregateRootClass(User::class),
            new AggregateTranslator(),
            $snapshotStore,
            new StreamName('event_stream_prooph_test_user')
        );
    }

    public function save(User $user): void
    {
        $this->saveAggregateRoot($user);
    }

    public function get(string $id) :? User
    {
        return $this->getAggregateRoot($id);
    }
}
