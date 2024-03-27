<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

/**
 * UserProfilePhotos
 *
 * @method Int getTotalCount()
 * @method PhotoSize[] getPhotos()
 *
 * @method bool isTotalCount()
 * @method bool isPhotos()
 *
 * @method $this setTotalCount(int $value)
 * @method $this setPhotos(PhotoSize[] $value)
 *
 * @method $this unsetTotalCount()
 * @method $this unsetPhotos()
 *
 * @property Int $total_count
 * @property PhotoSize[] $photos
 */
class UserProfilePhotos extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        'total_count' => 'int',
        'photos' => 'PhotoSize[][]',
    ];
}