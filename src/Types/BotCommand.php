<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

/**
 * BotCommand
 *
 * @method string getCommand()
 * @method string getDescription()
 *
 * @method bool isCommand()
 * @method bool isDescription()
 *
 * @method $this setCommand(string $value)
 * @method $this setDescription(string $value)
 *
 * @method $this unsetCommand()
 * @method $this unsetDescription()
 *
 * @property string $command
 * @property string $description
 */
class BotCommand extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        'command' => 'string',
        'description' => 'string',
    ];
}