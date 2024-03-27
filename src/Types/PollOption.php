<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

/**
 * PollOption
 *
 * @method string getText()
 * @method Int getVoterCount()
 *
 * @method bool isText()
 * @method bool isVoterCount()
 *
 * @method $this setText(string $value)
 * @method $this setVoterCount(int $value)
 *
 * @method $this unsetText()
 * @method $this unsetVoterCount()
 *
 * @property string $text
 * @property Int $voter_count
 */
class PollOption extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        'text' => 'string',
        'voter_count' => 'int',
    ];
}