<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

/**
 * BotCommandScopeAllPrivateChats
 *
 * @method string getType()
 *
 * @method bool isType()
 *
 * @method $this setType(string $value)
 *
 * @method $this unsetType()
 *
 * @property string $type
 */
class BotCommandScopeAllPrivateChats extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        'type' => 'string',
    ];
}