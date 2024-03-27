<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

/**
 * Dice
 *
 * @method string getEmoji()
 * @method Int getValue()
 *
 * @method bool isEmoji()
 * @method bool isValue()
 *
 * @method $this setEmoji(string $value)
 * @method $this setValue(int $value)
 *
 * @method $this unsetEmoji()
 * @method $this unsetValue()
 *
 * @property string $emoji
 * @property Int $value
 */
class Dice extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        'emoji' => 'string',
        'value' => 'int',
        'states' => 'int',
    ];
    
    private static $dice_states = [
        "🎲" => 6,
        "🎯" => 6,
        "🎳" => 6,
        "🏀" => 5,
        "⚽️" => 5,
        "🎰" => 64,
    ];

    public function _init(){
        parent::_init();
        $states = self::$dice_states[$this->emoji] ?? 0;
        $this->_setProperty("states", $states);
    }
}