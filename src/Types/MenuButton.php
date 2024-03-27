<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

class MenuButton extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        MenuButtonCommands::class,
        MenuButtonDefault::class,
        MenuButtonWebApp::class,
    ];
}