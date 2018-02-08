<?php
/**
 * Copyright (c) Reach Digital (https://www.reachdigital.nl/)
 * See README.md for license details.
 */

declare(strict_types=1);
namespace ReachDigital\ProophEventStore\Model\Field;


use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use ReachDigital\ProophEventStore\Api\Model\ValueObjectInterface;

abstract class UuidType implements ValueObjectInterface
{
    /**
     * @var UuidInterface
     */
    private $uuid;

    public static function fromString(string $uuid): self
    {
        return new static(Uuid::fromString($uuid));
    }

    public static function generateFakeFromString(string $string): self
    {
        if (self::isValid($string)) {
            return self::fromString($string);
        }
        return self::fromString(md5(static::class . $string));
    }

    public static function generate(): self
    {
        return new static(Uuid::uuid4());
    }

    public static function isValid($uuid): bool
    {
        return Uuid::isValid($uuid);
    }

    protected function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    public function toString(): string
    {
        return $this->__toString();
    }

    public function __toString(): string
    {
       return $this->uuid->toString();
    }

    /**
     * @param ValueObjectInterface|UuidType $other
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $other): bool
    {
        return \get_class($this) === \get_class($other) && $this->uuid->equals($other->uuid);
    }
}
