<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

/**
 * PassportElementErrorReverseSide
 *
 * @method string getSource()
 * @method string getType()
 * @method string getFileHash()
 * @method string getMessage()
 *
 * @method bool isSource()
 * @method bool isType()
 * @method bool isFileHash()
 * @method bool isMessage()
 *
 * @method $this setSource(string $value)
 * @method $this setType(string $value)
 * @method $this setFileHash(string $value)
 * @method $this setMessage(string $value)
 *
 * @method $this unsetSource()
 * @method $this unsetType()
 * @method $this unsetFileHash()
 * @method $this unsetMessage()
 *
 * @property string $source
 * @property string $type
 * @property string $file_hash
 * @property string $message
 */
class PassportElementErrorReverseSide extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        'source' => 'string',
        'type' => 'string',
        'file_hash' => 'string',
        'message' => 'string',
    ];
}