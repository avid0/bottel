<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

/**
 * MessageAutoDeleteTimerChanged
 *
 * @method Int getMessageAutoDeleteTime()
 *
 * @method bool isMessageAutoDeleteTime()
 *
 * @method $this setMessageAutoDeleteTime(int $value)
 *
 * @method $this unsetMessageAutoDeleteTime()
 *
 * @property Int $message_auto_delete_time
 */
class MessageAutoDeleteTimerChanged extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        'message_auto_delete_time' => 'int',
    ];
}