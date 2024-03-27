<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

/**
 * PassportData
 *
 * @method EncryptedPassportElement[] getData()
 * @method EncryptedCredentials getCredentials()
 *
 * @method bool isData()
 * @method bool isCredentials()
 *
 * @method $this setData(EncryptedPassportElement[] $value)
 * @method $this setCredentials(EncryptedCredentials $value)
 *
 * @method $this unsetData()
 * @method $this unsetCredentials()
 *
 * @property EncryptedPassportElement[] $data
 * @property EncryptedCredentials $credentials
 */
class PassportData extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        'data' => 'EncryptedPassportElement[]',
        'credentials' => 'EncryptedCredentials',
    ];
}