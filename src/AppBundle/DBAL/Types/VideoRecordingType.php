<?php

namespace AppBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * Class VideoRecordingType
 */
class VideoRecordingType extends AbstractEnumType
{
    const CAMERA   = 'camera';
    const RECORDER = 'recorder';
    const UPLOAD   = 'upload';

    /**
     * @var array $choices
     */
    protected static $choices = [
        self::CAMERA   => 'camera',
        self::RECORDER => 'recorder',
        self::UPLOAD   => 'upload',
    ];
}
