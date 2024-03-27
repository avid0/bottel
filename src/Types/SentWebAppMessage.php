<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

/**
 * SentWebAppMessage
 *
 * @method string getInlineMessageId()
 *
 * @method bool isInlineMessageId()
 *
 * @method $this setInlineMessageId(string $value)
 *
 * @method $this unsetInlineMessageId()
 *
 * @property string $inline_message_id
 */
class SentWebAppMessage extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        'inline_message_id' => 'string',
    ];
}