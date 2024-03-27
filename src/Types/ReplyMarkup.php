<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

/**
 * @mixin ReplyKeyboardMarkup
 * @mixin KeyboardButton
 * @mixin KeyboardButtonPollType
 * @mixin ReplyKeyboardRemove
 * @mixin InlineKeyboardMarkup
 * @mixin InlineKeyboardButton
 */
class ReplyMarkup extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        ReplyKeyboardMarkup::class,
        KeyboardButton::class,
        KeyboardButtonPollType::class,
        ReplyKeyboardRemove::class,
        InlineKeyboardMarkup::class,
        InlineKeyboardButton::class,
    ];
}