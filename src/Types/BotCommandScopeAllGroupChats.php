<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

/**
 * BotCommandScopeAllGroupChats
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
class BotCommandScopeAllGroupChats extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        'type' => 'string',
    ];
}