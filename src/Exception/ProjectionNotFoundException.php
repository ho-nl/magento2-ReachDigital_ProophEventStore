<?php
declare(strict_types=1);


namespace ReachDigital\ProophEventStore\Exception;


use Magento\Framework\Exception\LocalizedException;

class ProjectionNotFoundException extends LocalizedException
{
    public static function withName(string $name) : self
    {
        return new self(__('Projection not found %1', $name));
    }
}
