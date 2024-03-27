<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

class InputMedia extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        InputMediaAnimation::class,
        InputMediaDocument::class,
        InputMediaAudio::class,
        InputMediaPhoto::class,
        InputMediaVideo::class,
    ];
}