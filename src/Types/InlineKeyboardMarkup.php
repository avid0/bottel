<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

/**
 * InlineKeyboardMarkup
 *
 * @method InlineKeyboardButton[] getInlineKeyboard()
 *
 * @method bool isInlineKeyboard()
 *
 * @method $this setInlineKeyboard(InlineKeyboardButton[] $value)
 *
 * @method $this unsetInlineKeyboard()
 *
 * @property InlineKeyboardButton[] $inline_keyboard
 */
class InlineKeyboardMarkup extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        'inline_keyboard' => 'InlineKeyboardButton[][]',
    ];
}