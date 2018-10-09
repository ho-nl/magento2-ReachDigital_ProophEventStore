<?php
/**
 * Copyright © Reach Digital (https://www.reachdigital.io/)
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Infrastructure\EventStore;

use Prooph\EventStore\StreamName;
use Prooph\EventStore\Util\Assertion;

class GetSingleStreamStrategyTableName
{
    public function execute(StreamName $streamName) : string
    {
        $tableName = $streamName->toString();
        Assertion::regex($tableName, '/^[a-zA-Z0-9_]+$/');
        Assertion::maxLength($tableName, 64);
        return $tableName;
    }
}
