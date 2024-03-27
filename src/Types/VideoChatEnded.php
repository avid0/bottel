<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

/**
 * VideoChatEnded
 *
 * @method Int getDuration()
 *
 * @method bool isDuration()
 *
 * @method $this setDuration(int $value)
 *
 * @method $this unsetDuration()
 *
 * @property Int $duration
 */
class VideoChatEnded extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        'duration' => 'int',
    ];
}