<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

/**
 * ChatLocation
 *
 * @method Location getLocation()
 * @method string getAddress()
 *
 * @method bool isLocation()
 * @method bool isAddress()
 *
 * @method $this setLocation(Location $value)
 * @method $this setAddress(string $value)
 *
 * @method $this unsetLocation()
 * @method $this unsetAddress()
 *
 * @property Location $location
 * @property string $address
 */
class ChatLocation extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        'location' => 'Location',
        'address' => 'string',
    ];
}