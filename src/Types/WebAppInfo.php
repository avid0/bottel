<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

/**
 * WebAppInfo
 *
 * @method string getUrl()
 *
 * @method bool isUrl()
 *
 * @method $this setUrl(string $value)
 *
 * @method $this unsetUrl()
 *
 * @property string $url
 */
class WebAppInfo extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        'url' => 'string',
    ];
}