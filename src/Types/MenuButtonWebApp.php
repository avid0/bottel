<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

/**
 * MenuButtonWebApp
 *
 * @method string getType()
 * @method string getText()
 * @method WebAppInfo getWebApp()
 *
 * @method bool isType()
 * @method bool isText()
 * @method bool isWebApp()
 *
 * @method $this setType(string $value)
 * @method $this setText(string $value)
 * @method $this setWebApp(WebAppInfo $value)
 *
 * @method $this unsetType()
 * @method $this unsetText()
 * @method $this unsetWebApp()
 *
 * @property string $type
 * @property string $text
 * @property WebAppInfo $web_app
 */
class MenuButtonWebApp extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        'type' => 'string',
        'text' => 'string',
        'web_app' => 'WebAppInfo',
    ];
}