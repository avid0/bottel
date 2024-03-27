<?php
namespace Bottel\Types;
use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

/**
 * MessageId
 *
 * @method Int getMessageId()
 *
 * @method bool isMessageId()
 *
 * @method $this setMessageId(int $value)
 *
 * @method $this unsetMessageId()
 *
 * @property Int $message_id
 */
class MessageId extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        'message_id' => 'int',
    ];
}