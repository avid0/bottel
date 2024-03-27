<?php
namespace Bottel\Types;

use Bottel\Utils\DelayedJsonMapper\DelayedJsonMapper;

class PassportElementError extends DelayedJsonMapper {
    const JSON_PROPERTY_MAP = [
        PassportElementErrorDataField::class,
        PassportElementErrorFrontSide::class,
        PassportElementErrorReverseSide::class,
        PassportElementErrorSelfie::class,
        PassportElementErrorFile::class,
        PassportElementErrorFiles::class,
        PassportElementErrorTranslationFile::class,
        PassportElementErrorTranslationFiles::class,
        PassportElementErrorUnspecified::class,
    ];
}