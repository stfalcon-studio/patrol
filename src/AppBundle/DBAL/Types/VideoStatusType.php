<?php

namespace AppBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * Class VideoStatusType
 */
class VideoStatusType extends AbstractEnumType
{
    const READY      = 'ready';
    const WAITING    = 'waiting';
    const CONVERTING = 'converting';

    /**
     * @var array $choices
     */
    protected static $choices = [
        self::READY      => 'ready',
        self::WAITING    => 'waiting',
        self::CONVERTING => 'converting',
    ];
}
