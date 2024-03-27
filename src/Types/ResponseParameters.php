<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

/**
 * ResponseParameters
 *
 * @method Int getMigrateToChatId()
 * @method Int getRetryAfter()
 *
 * @method bool isMigrateToChatId()
 * @method bool isRetryAfter()
 *
 * @method $this setMigrateToChatId(int $value)
 * @method $this setRetryAfter(int $value)
 *
 * @method $this unsetMigrateToChatId()
 * @method $this unsetRetryAfter()
 *
 * @property Int $migrate_to_chat_id
 * @property Int $retry_after
 */
class ResponseParameters extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        'migrate_to_chat_id' => 'int',
        'retry_after' => 'int',
    ];
}