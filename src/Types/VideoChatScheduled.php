<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

/**
 * VideoChatScheduled
 *
 * @method Int getStartDate()
 *
 * @method bool isStartDate()
 *
 * @method $this setStartDate(int $value)
 *
 * @method $this unsetStartDate()
 *
 * @property Int $start_date
 */
class VideoChatScheduled extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        'start_date' => 'int',
    ];
}